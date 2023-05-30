<?php

if (!class_exists('wpmlHistoriesAttachment')) {
    class wpmlHistoriesAttachment extends wpMailPlugin
    {
	var $model = 'HistoriesAttachment';
	var $controller = 'historiesattachments';
	var $table;
	
	var $errors = array();
	var $data = array();
	
	var $fields = array(
            'id' => "INT(11) NOT NULL AUTO_INCREMENT",
            'title' => "VARCHAR(250) NOT NULL DEFAULT ''",
            'history_id' => "INT(11) NOT NULL DEFAULT '0'",
            'filename' => "TEXT NOT NULL",
            'subdir' => "TEXT NOT NULL",
            'created' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key' => "PRIMARY KEY (`id`), INDEX(`history_id`)",
	);
	
	var $tv_fields = array(
            'id' => array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'title' => array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
            'history_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'filename' => array("TEXT", "NOT NULL"),
            'subdir' => array("TEXT", "NOT NULL"),
            'created' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key' => "PRIMARY KEY (`id`), INDEX(`history_id`)",
	);
	
	var $indexes = array('history_id');
	
        function __construct($data = array())
        {
		parent::__construct();
		
		global $Db;
		
            $this->table = $this->pre . $this->controller;
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
                    $this->{$dkey} = $dval;
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
		
            if (empty($title)) {
                $this->errors['title'] = __('Please fill in an attachment title.', 'wp-mailinglist');
            }
            if (empty($history_id)) {
                $this->errors['history_id'] = __('No history record was specified.', 'wp-mailinglist');
            }
            if (empty($filename)) {
                $this->errors['filename'] = __('No mailing list was specified', 'wp-mailinglist');
            }
		
            return $this->errors;
	}
}
}

?>