<?php

if (!class_exists('wpmlQueue')) {
	class wpmlQueue extends wpmlDbHelper
    {
		var $id;
		var $subject;
		var $message;
		var $list_id;
		var $theme_id = 0;
		var $group;
		var $attachment;
		var $attachmentfile;
		var $error = array();
		var $model = 'Queue';
		var $controller = 'queue';
		var $table_name = 'wpmlqueue';
		
		var $table_fields = array(
			
		);
		
		var $tv_fields = array(
							   
		);
		
		var $indexes = array();
		
		function __construct($data = array())
        {
			parent::__construct();
		
			$this -> table = $this -> pre . $this -> controller;
		
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					$this -> {$key} = stripslashes_deep($val);
					
					switch ($key) {
						case 'groups'			:
							$this -> groups = maybe_unserialize($val);
							break;	
					}
				}
			}
		}
	}
}

?>