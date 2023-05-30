<?php

if (!class_exists('wpmlSubscribersOption')) {
	class wpmlSubscribersOption extends wpmlDbHelper
    {
		var $model = 'SubscribersOption';
		var $controller = 'subscribersoptions';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'subscriber_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'field_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'option_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`field_id`)"
		);
		
		var $indexes = array('subscriber_id', 'field_id', 'option_id');
		var $errors = array();
		
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
				}
			}
			
			return;
		}
		
		function defaults()
        {
			global $Html;
			
			$defaults = array(
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);
			
			return $defaults;
		}
		
		function validate($data = array())
        {
			global $Html, $Db;
			$this -> errors = array();
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
            $defaults = array();
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($subscriber_id)) {
                    $this -> errors['subscriber_id'] = 'empty';
                }
                if (empty($field_id)) {
                    $this -> errors['field_id'] = 'empty';
                }
                if (empty($option_id)) {
                    $this -> errors['option_id'] = 'empty';
                }
			} else {
				$this -> errors[] = __('No data was provided', 'wp-mailinglist');
			}
			
			if (empty($this -> errors)) {				
				if ($record = $this -> find(array('field_id' => $field_id, 'subscriber_id' => $subscriber_id, 'option_id' => $option_id))) {
					$this -> data -> id = $record -> id;
					$this -> data -> modified = $Html -> gen_date();
				}
			}
			
			return $this -> errors;
		}
	}
}

?>