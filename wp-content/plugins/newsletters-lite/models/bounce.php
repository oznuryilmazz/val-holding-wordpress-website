<?php

if (!class_exists('wpmlBounce')) {
	class wpmlBounce extends wpMailPlugin
    {
		var $model = 'Bounce';
		var $controller = 'bounces';
		var $table;
		
		var $fields = array(
			'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'email'				=>	"VARCHAR(250) NOT NULL DEFAULT ''",
			'status'			=>	"TEXT NOT NULL",
			'count'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'history_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`email`), INDEX(`history_id`)",
		);
		
		var $tv_fields = array(
			'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'email'				=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
			'status'			=>	array("TEXT", "NOT NULL"),
			'count'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'history_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`email`), INDEX(`history_id`)",					   
		);
		
		var $indexes = array('email', 'history_id');
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $wpdb, $Db;
			$this -> table = $this -> pre . $this -> controller;
			
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {				
					$this -> {$dkey} = stripslashes_deep($dval);
					
					switch ($dkey) {
						case 'history_id'			:
							$this -> history = $this -> History() -> find(array('id' => $dval));
							break;
					}
				}
			}
			
			$Db -> model = $this -> model;
		}
		
		function defaults()
        {
			global $Html;
			
			$defaults = array(
				'count'				=>	0,
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);
			
			return $defaults;	
		}
		
		function validate($data = array())
        {
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();

			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($email)) {
                    $this -> errors['email'] = __('No email was specified.', 'wp-mailinglist');
                } else {
				}
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}
			
			return $this -> errors;
		}
		
		function alltotal()
        {
			global $wpdb;
			$total = 0;
			
			$alltotalquery = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $this -> table . "`";
			$alltotalquery = apply_filters('newsletters_bounces_alltotalquery', $alltotalquery);
			
			$query_hash = md5($alltotalquery);
			if ($ob_alltotal = $this -> get_cache($query_hash)) {
				$alltotal = $ob_alltotal;
			} else {
				$alltotal = $wpdb -> get_var($alltotalquery);
				$this -> set_cache($query_hash, $alltotal);
			}
			
			if (!empty($alltotal)) {
				$total = $alltotal;
			}
			
			return $total;
		}
		
		function save($data = null, $validate = true)
        {
			global $wpdb;
		
			if (!empty($data)) {
				$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
				$defaults = $this -> defaults();
				$r = wp_parse_args($data, $defaults);
				extract($r, EXTR_SKIP);
				
				if ($validate == true) {
					$this -> validate($data);
				}
				
				if (empty($this -> errors)) {
					$bouncequery = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `email` = '" . $email . "' AND `history_id` = '" . $history_id . "'";
					
					if ($bounce = $wpdb -> get_row($bouncequery)) {
						$query = "UPDATE `" . $wpdb -> prefix . $this -> table . "` "
						. " SET `count` = '" . ((int) $bounce -> count + 1) . "', `status` = '" . esc_sql($status) . "', `modified` = '" . esc_sql($modified) . "' WHERE `id` = '" . $bounce -> id . "' LIMIT 1";
					} else {
						$query = "INSERT INTO `" . $wpdb -> prefix . $this -> table . "` "
						. " (`id`, `email`, `count`, `status`, `history_id`, `created`, `modified`) "
						. " VALUES ('', '" . esc_sql($email) . "', '1', '" . esc_sql($status) . "', '" . esc_sql($history_id) . "', '" . $created . "', '" . $modified . "')";
					}
					
					if ($wpdb -> query($query)) {
						return true;
					}
				}
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}
			
			return false;
		}
	}
}

?>