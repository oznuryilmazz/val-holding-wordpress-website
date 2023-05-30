<?php

if (!class_exists('wpmlHistoriesList')) {
    class wpmlHistoriesList extends wpMailPlugin
    {
	var $model = 'HistoriesList';
	var $controller = 'historieslists';
	var $table = '';
	
	var $errors = array();
	var $data = array();
	
	var $fields = array(
            'id' => "INT(11) NOT NULL AUTO_INCREMENT",
            'history_id' => "INT(11) NOT NULL DEFAULT '0'",
            'list_id' => "INT(11) NOT NULL DEFAULT '0'",
            'created' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key' => "PRIMARY KEY (`id`), INDEX(`history_id`), INDEX(`list_id`)",
	);
	
	var $tv_fields = array(
            'id' => array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'history_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'list_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'created' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key' => "PRIMARY KEY (`id`), INDEX(`history_id`), INDEX(`list_id`)"
	);
	
	var $indexes = array('history_id', 'list_id');
	
        function __construct($data = array())
        {
		parent::__construct();
		
		global $Db;
	
            $this->table = $this->pre . $this->controller;
	
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
                    $this->{$dkey} = stripslashes_deep($dval);
			}
		}
		
            $Db->model = $this->model;
	}
	
        function defaults()
        {
		global $Html;
	
		$defaults = array(
                'created' => $Html->gen_date(),
                'modified' => $Html->gen_date(),
		);
		
		return $defaults;
	}
	
        function validate($data = array())
        {
            $this->errors = array();
		
            $data = (empty($data[$this->model])) ? $data : $data[$this->model];
		extract($data, EXTR_SKIP);
		
            if (empty($history_id)) {
                $this->errors['history_id'] = __('No history record was specified', 'wp-mailinglist');
            }
            if (empty($list_id)) {
                $this->errors['list_id'] = __('No mailing list was specified', 'wp-mailinglist');
            }
		
            return $this->errors;
	}
}
}

?>