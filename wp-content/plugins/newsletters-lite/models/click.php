<?php

if (!class_exists('wpmlClick')) {
	class wpmlClick extends wpmlDbHelper
    {
		var $model = 'Click';
		var $controller = 'clicks';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'link_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'referer'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'history_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'user_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'subscriber_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'device'				=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`link_id`), INDEX(`history_id`), INDEX(`user_id`), INDEX(`subscriber_id`)"
		);
		
		var $indexes = array('link_id', 'history_id', 'user_id', 'subscriber_id');
		
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
		
		function referer_name($referer = null)
        {
			$referer_name = __('Unknown', 'wp-mailinglist');
			
			switch ($referer) {
				case 'unsubscribe'				:
					$referer_name = __('Unsubscription', 'wp-mailinglist');
					break;
				case 'online'					:
					$referer_name = __('View Online', 'wp-mailinglist');
					break;
				case 'manage'					:
					$referer_name = __('Management', 'wp-mailinglist');
					break;
				default 						:
					$referer_name = __('Unknown', 'wp-mailinglist');
					break;
			}
			
			return $referer_name;
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
			global $Html;
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($link_id) && empty($referer)) {
                    $this -> errors['link_id'] = __('Please specify a link', 'wp-mailinglist');
                }
			} else {
				$this -> errors[] = __('No data was provided', 'wp-mailinglist');
			}
			
			return $this -> errors;
		}
	}
}

?>