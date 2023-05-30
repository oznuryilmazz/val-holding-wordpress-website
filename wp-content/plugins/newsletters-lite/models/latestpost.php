<?php

if (!class_exists('wpmlLatestpost')) {
	class wpmlLatestpost extends wpmlDbHelper
    {
		var $model = 'Latestpost';
		var $controller = 'latestposts';
		var $table_name = 'wpmllatestposts';
		
		var $fields = array(
			'id'					=> 	"INT(11) NOT NULL AUTO_INCREMENT",
			'post_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'lps_id'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'created'				=> 	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'				=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`post_id`), INDEX(`lps_id`)",
		);
		
		var $tv_fields = array(
			'id'					=> 	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'post_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'lps_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'created'				=> 	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'					=>	"PRIMARY KEY (`id`), INDEX(`post_id`), INDEX(`lps_id`)",					   
		);
		
		var $indexes = array('post_id', 'lps_id');
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $Db, $wpdb;
			
			$this -> table = $this -> pre . $this -> controller;
		
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {
					$this -> {$dkey} = stripslashes_deep($dval);	
				}
			}
			
			$Db -> model = $this -> model;
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
			$this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();

			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);
			
			if (!empty($data)) {
                if (empty($post_id)) {
                    $this -> errors['post_id'] = __('No post ID was specified', 'wp-mailinglist');
                }
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');	
			}
			
			return $this -> errors;
		}
	}
}

?>