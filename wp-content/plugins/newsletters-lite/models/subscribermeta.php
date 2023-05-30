<?php

if (!class_exists('wpmlSubscriberMeta')) {
	class wpmlSubscriberMeta extends wpmlDbHelper {
		var $model = 'SubscriberMeta';
		var $controller = 'subscribermetas';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'subscriber_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'meta_key'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'meta_value'			=>	array("TEXT", "NOT NULL"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`subscriber_id`), INDEX(`meta_key`)"
		);
		
		var $indexes = array('subscriber_id', 'meta_key');
		
		function __construct($data = null) {
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
		
		function defaults() {
			global $Html;
			
			$defaults = array(
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);
			
			return $defaults;
		}
		
		function validate($data = array()) {
			global $Html;
			$this -> errors = array();
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
				if (empty($subscriber_id)) { $this -> errors['subscriber_id'] = __('No subscriber was specified', 'wp-mailinglist'); }
				if (empty($meta_key)) { $this -> errors['meta_key'] = __('No meta key was specified', 'wp-mailinglist'); }
				if (empty($meta_value)) { $this -> errors['meta_value'] = __('No meta value was specified', 'wp-mailinglist'); }
			} else {
				$this -> errors[] = __('No data was provided', 'wp-mailinglist');
			}
			
			if (empty($this -> errors)) {
				if ($cur = $this -> SubscriberMeta() -> find(array('subscriber_id' => $subscriber_id, 'meta_key' => $meta_key))) {
					$this -> data -> id = $cur -> id;
				}
			}
			
			return $this -> errors;
		}
	}
}

?>