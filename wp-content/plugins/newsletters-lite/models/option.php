<?php

if (!class_exists('wpmlOption')) {
	class wpmlOption extends wpmlDbHelper
    {
		var $model = 'Option';
		var $controller = 'options';
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'value'					=>	array("TEXT", "NOT NULL"),
			'field_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'order'					=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`field_id`)"
		);
		
		var $indexes = array('field_id');
		
		function __construct($data = null)
        {
			parent::__construct();
			
			global $Db;
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
			
			//$Db -> model = $this -> model;
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
			global $Html;
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($value)) {
                    $this -> errors['value'] = __('Please specify a value', 'wp-mailinglist');
                }
                if (empty($field_id)) {
                    $this -> errors['field_id'] = __('Please select a field', 'wp-mailinglist');
                }
				
				if ($cur_option = $this -> find(array('field_id' => $field_id, 'value' => $value), false, false, false)) {
					$this -> data -> id = $cur_option -> id;
				}
			} else {
				$this -> errors[] = __('No data was provided', 'wp-mailinglist');
			}
			
			return $this -> errors;
		}
		
		function save($data = array(), $validate = true)
        {
			if (!empty($data)) {
				$data['value'] = trim($data['value']);
			}
			
			return parent::save($data);
		}
	}
}

?>