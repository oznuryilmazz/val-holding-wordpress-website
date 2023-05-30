<?php

if (!class_exists('wpmlAutorespondersForm')) {
	class wpmlAutorespondersForm extends wpmlDbHelper
    {
		var $model = "AutorespondersForm";
		var $controller = "autorespondersforms";
		var $table;
		
		var $fields = array(
			'id'					=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'autoresponder_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'form_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'created'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`autoresponder_id`), INDEX(`form_id`)",
		);
		
		var $tv_fields = array(
			'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'autoresponder_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'form_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`autoresponder_id`), INDEX(`form_id`)",				
		);
		
		var $indexes = array('autoresponder_id', 'form_id');
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $Db;
			
			$this -> table = $this -> pre . $this -> controller;	
			
			if (!empty($data)) {		
				foreach ($data as $dkey => $dval) {				
					$this -> {$dkey} = stripslashes_deep($dval);
				}
			}
			
			$Db -> model = $this -> model;
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
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();

			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($autoresponder_id)) {
                    $this -> errors['autoresponder_id'] = __('No autoresponder was specified.', 'wp-mailinglist');
                }
                if (empty($form_id)) {
                    $this -> errors['form_id'] = __('No form was specified.', 'wp-mailinglist');
                }
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}
			
			return $this -> errors;
		}
	}
}

?>