<?php

if (!class_exists('wpmlTheme')) {
	class wpmlTheme extends wpMailPlugin
    {
		var $name = 'wpmlTheme';
		var $model = 'Theme';
		var $controller = 'themes';
		var $table_name = 'wpmlthemes';
		var $recursive = true;
		
		var $fields = array(
			'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'title'				=>	"VARCHAR(150) NOT NULL DEFAULT ''",
			'name'				=>	"VARCHAR(50) NOT NULL DEFAULT ''",
			'premade'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'type'				=>	"ENUM('upload','paste','builder') NOT NULL DEFAULT 'paste'",
			'content'			=>	"LONGTEXT NOT NULL",
			'def'				=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'defsystem'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'acolor'			=>	"VARCHAR(20) NOT NULL DEFAULT '#333333'",
			'themestylesheet'	=>	"INT(1) NOT NULL DEFAULT '0'",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`def`), INDEX(`defsystem`)"
		);
		
		var $tv_fields = array(
			'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'				=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
			'name'				=>	array("VARCHAR(50)", "NOT NULL DEFAULT ''"),
			'premade'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'type'				=>	array("ENUM('upload','paste','builder')", "NOT NULL DEFAULT 'paste'"),
			'content'			=>	array("LONGTEXT", "NOT NULL"),
			'def'				=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'defsystem'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'acolor'			=>	array("VARCHAR(20)", "NOT NULL DEFAULT '#333333'"),
			'themestylesheet'	=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`def`), INDEX(`defsystem`)"					   
		);
		
		var $indexes = array('def', 'defsystem');
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $Db;
		
			$this -> table = $this -> pre . $this -> controller;
		
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					$this -> {$key} = stripslashes_deep($val);
				}
			}
			
			$Db -> model = $this -> model;
			return;
		}
		
		function defaults()
        {
			global $Html;
			
			$defaults = array(
				'themestylesheet'	=>	0,
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);
			
			return $defaults;
		}
		
		function validate($data = array())
        {
			global $Html;
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();

			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($title)) {
                    $this -> errors['title'] = __('Please fill in a title', 'wp-mailinglist');
                } else {
					if (!empty($id) && empty($name)) {
						$this -> data -> name = $Html -> sanitize($title, '');
					}
				}
				
                if (empty($type)) {
                    $this -> errors['type'] = __('Please choose a submission type', 'wp-mailinglist');
                } else {
					switch ($type) {
                        case 'upload':
                            $_FILES['upload'] = map_deep($_FILES['upload'], 'sanitize_text_field');
							if ($_FILES['upload']['error'] > 0) {
                                $this -> errors['upload'] = $Html -> file_upload_error($_FILES['upload']['error']);
                            } else {
                                if (empty($_FILES['upload']['name'])) {
                                    $this -> errors['upload'] = __('Please choose an HTML file for uploading', 'wp-mailinglist');
                                } elseif (!is_uploaded_file($_FILES['upload']['tmp_name'])) {
                                    $this -> errors['upload'] = __('HTML file could not be uploaded', 'wp-mailinglist');
                                } elseif ($_FILES['upload']['type'] != "text/html") {
                                    $this -> errors['upload'] = __('This is not a valid HTML file. Ensure that it has a .html extension', 'wp-mailinglist');
							} else {							
                                    @chmod($_FILES['upload']['tmp_name'], 0755);
									
                                    if ($fh = fopen($_FILES['upload']['tmp_name'], "r")) {
										$html = "";
										
										while (!feof($fh)) {
											$html .= fread($fh, 1024);
										}
										
										fclose($fh);
										$this -> data -> content = $this -> data -> paste = $html;
										$this -> data -> type = "paste";
									} else {
										$this -> errors['upload'] = __('HTML file could not be opened for reading. Please check its permissions', 'wp-mailinglist');	
									}
								}
							}
							break;
                        case 'builder':
							$this -> data -> content = wp_unslash($this -> data -> builder);
							break;
                        default:
                            if (empty($paste)) {
                                $this -> errors['paste'] = __('Please paste HTML code for your template', 'wp-mailinglist');
                            } else {
								$this -> data -> content = wp_unslash($paste);	
							}
							break;
					}
				}
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}
			
			if (empty($this -> errors)) {
				if (!empty($this -> data -> inlinestyles) && $this -> data -> inlinestyles == "Y") {	
					$this -> data -> content = $this -> inlinestyles($this -> data -> content);
				}
				
				if (!empty($this -> data -> imgprependurl)) {
					if (preg_match_all('/<img.*?>/', $this -> data -> content, $matches)) {
						if (!empty($matches[0])) {
							foreach ($matches[0] as $img) {
						        if (preg_match('/src="(.*?)"/', $img, $m)) {
							    	if (!empty($m)) {
							    		$this -> data -> content = str_replace($m[0], 'src="' . rtrim($this -> data -> imgprependurl, '/') . '/' . $m[1] . '"', $this -> data -> content); 
							    	}
						        }
						    }
						}
					}
				}
			}
			
			return $this -> errors;
		}
		
		function select()
        {
			global $Db;
			$Db -> model = $this -> model;
			$themeselect = array();
			
			if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) {
				foreach ($themes as $theme) {
					$themeselect[$theme -> id] = esc_html($theme -> title);	
				}
			}
			
			return apply_filters($this -> pre . '_themes_select', $themeselect);
		}
		
		function save($data = array(), $validate = true)
        {
			if (empty($data['themestylesheet'])) {
				$data['themestylesheet'] = 0;
			}
			
			return parent::save($data);
		}
	}
}

?>