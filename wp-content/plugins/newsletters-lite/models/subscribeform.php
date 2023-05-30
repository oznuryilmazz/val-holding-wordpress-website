<?php

if (!class_exists('wpmlSubscribeform')) {
	class wpmlSubscribeform extends wpmlDbHelper
    {
		var $model = 'Subscribeform';
		var $controller = 'forms';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'					=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'ajax'					=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'scroll'				=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'buttontext'			=>	array("TEXT", "NOT NULL"),
			'confirmationtype'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT 'message'"),
			'confirmation_message'	=>	array("TEXT", "NOT NULL"),
			'confirmation_redirect'	=>	array("TEXT", "NOT NULL"),
			'styling'				=>	array("TEXT", "NOT NULL"),
			'styling_beforeform'	=>	array("TEXT", "NOT NULL"),
			'styling_afterform'		=>	array("TEXT", "NOT NULL"),
			'styling_customcss'		=>	array("TEXT", "NOT NULL"),
			'captcha'				=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`title`)"
		);
		
		var $indexes = array('title');
		
		function __construct($data = null)
        {
			parent::__construct();
			
			$this -> table = $this -> pre . $this -> controller;
			
			foreach ($this -> tv_fields as $field => $attributes) {
				if (is_array($attributes)) {
					$this -> fields[$field] = implode(" ", $attributes);
				} else {
					$this -> fields[$field] = $attributes;
				}
			}
			
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {
					$this -> {$dkey} = stripslashes_deep($dval);
					
					switch ($dkey) {
						case 'buttontext'					:
							if (empty($dval)) {
								$this -> {$dkey} = __('Subscribe', 'wp-mailinglist');
							}
							break;
					}
				}
				
				if (!empty($this -> id)) {
					$this -> form_fields = $this -> FieldsForm() -> find_all(array('form_id' => $this -> id), false, array('order', "ASC"));
				}
			}
			
			return $this;
		}
		
		function defaults()
        {
			global $Html;
			
			$defaults = array(
				'ajax'					=>	1,
				'scroll'				=>	0,
				'captcha'				=>	0,
				'buttontext'			=>	"Subscribe",
				'created'				=>	$Html -> gen_date(),
				'modified'				=>	$Html -> gen_date(),
				'confirmationtype'		=>	"message",
				'confirmation_message'	=>	"Thank you for subscribing",
				'styling'				=>	array(
					'formlayout'				=>	"normal",
					'formpadding'				=>	0,
					'formtextcolor'				=>	'',
					'background'				=>	'',
					'loadingindicator'			=>	1,
					'loadingicon'				=>	"refresh",
					'loadingcolor'				=>	"#ffffff",
					'fieldcolor'				=>	'#ffffff',
					'fieldtextcolor'			=>	'#555555',
					'fieldborderradius'			=>	'4',
					'fieldshowlabel'			=>	1,
					'fieldcaptions'				=>	1,
					'fielderrors'				=>	1,
					'fielderrorcolor'			=>	'',
					'buttoncolor'				=>	'#337ab7',
					'buttontextcolor'			=>	'#ffffff',
					'buttonbordersize'			=>	'1',
					'buttonborderradius'		=>	'4',
					'buttonbordercolor'			=>	'#2e6da4',
					'buttonhovercolor'			=>	'#286090',
					'buttonhoverbordercolor'	=>	'#204d74',
				),
			);
			
			return $defaults;
		}
		
		function validate($data = array())
        {
			global $Html, $Field;
			$this -> errors = array();
            $defaults = $this -> defaults();

			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
				$method = sanitize_text_field(wp_unslash($_GET['method']));
				switch ($method) {
                    case 'ajax':
                        if (empty($title)) {
                            $this -> errors['title'] = __('Please fill in a title', 'wp-mailinglist');
                        }
						break;
					case 'save'						:
						// Check for empty or invalid values
                        if (empty($title)) {
                            $this -> errors['title'] = __('Please fill in a title', 'wp-mailinglist');
                        }
						
						$email_field = false;
						$email_field_id = $Field -> email_field_id();
						if (!empty($data['form_fields'])) {
							foreach ($data['form_fields'] as $field_id => $field_values) {
								if ($field_id == $email_field_id) {
									$email_field = true;
									break;
								}
							}
						}
						
						if (empty($email_field) || $email_field == false) {
							$this -> errors[] = __('An email address field is mandatory.', 'wp-mailinglist');
						}	
						
						$list_field = false;
						$list_field_id = $Field -> list_field_id();
						if (!empty($data['form_fields'])) {
							foreach ($data['form_fields'] as $field_id => $field_values) {
								if ($field_id == $list_field_id) {
									$list_field = true;
									break;
								}
							}
						}
						
						if (empty($list_field) || $list_field == false) {
							$this -> errors[] = __('A mailing list field is mandatory', 'wp-mailinglist');
						}
						break;
                    case 'settings':
                        if (empty($ajax)) {
                            $this -> data -> ajax = 0;
                        }
                        if (empty($scroll)) {
                            $this -> data -> scroll = 0;
                        }
                        if (empty($captcha)) {
                            $this -> data -> captcha = 0;
                        }
						
						// Styling stuff
						if (!empty($styling)) {
							$defaults = $this -> defaults();						
							if (!empty($defaults['styling'])) {
								foreach ($defaults['styling'] as $style => $defaultvalue) {																		
									if (empty($styling[$style])) {
										$styling[$style] = 0;
									}
								}
							}
						}											
						break;
				}
				
				// Some Arrays/Objects may need to be serialized
				$this -> data -> styling = maybe_serialize($styling);
			} else {
				$this -> errors[] = __('No data was provided', 'wp-mailinglist');
			}
			
			return $this -> errors;
		}
		
		function customcss($form = null)
        {
			$customcss = false;
			
			if (!empty($form)) {
				if (is_numeric($form)) {
					$form_id = $form;
					$form = $this -> find(array('id' => $form_id));
				} else {
					$form_id = $form -> id;
				}
				
				try {
					require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
					$scss = new Leafo\ScssPhp\Compiler();
					
					$css = '#newsletters-' . $form -> id . '-form-wrapper {';
					$css .= wp_unslash($form -> styling_customcss);
					$css .= '}';
					
					$compiled = $scss -> compile($css);
					
					return apply_filters('newsletters_subscribeform_customcss', wp_unslash($compiled), $form);
				} catch (Exception $e) {
					$error = $e -> getMessage();
					$this -> log_error($error);
					
					return wp_unslash($form -> styling_customcss);
				}
			}
			
			return false;
		}
		
		function save($data = array(), $validate = true)
        {
			global $Html;

			if ($this -> language_do()) {
				$languagefields = array('title', 'buttontext', 'confirmation_message', 'confirmation_redirect', 'styling_beforeform', 'styling_afterform', 'etsubject_confirm', 'etmessage_confirm');
				
				foreach ($data as $key => $value) {					
					// Language fields
					if (!empty($key) && in_array($key, $languagefields)) {
						switch ($key) {
							default 					:
								if(!is_array($value))
								{
									$my_array = [];
									$my_array[] = $value;
									$value = $my_array;
								}
								if (array_filter($value)) {
									$value = $data[$key] = $this -> language_join($value);
								} else {
									$value = $data[$key] = false;
								}
								break;
						}
					}
				}
			}
			$data['modified'] = $Html -> gen_date();
			return parent::save($data, $validate);
		}
	}
}

?>