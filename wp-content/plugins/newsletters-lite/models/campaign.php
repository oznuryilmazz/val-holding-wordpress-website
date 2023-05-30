<?php

if (!class_exists('wpmlCampaign')) {
    class wpmlCampaign extends wpMailPlugin
    {
	var $model = 'Campaign';
	var $controller = 'campaigns';
	var $table = '';
	
	var $fields = array(
            'id' => "INT(11) NOT NULL AUTO_INCREMENT",
            'title' => "VARCHAR(250) NOT NULL DEFAULT ''",
            'created' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key' => "PRIMARY KEY (`id`)",
	);
	
	var $tv_fields = array(
            'id' => array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'title' => array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
            'created' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key' => "PRIMARY KEY (`id`)",
	);
	
        function __construct($data = array())
        {
		parent::__construct();
		
		global $wpdb, $Db;
	
            $this->table = $this->pre . $this->controller;
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {
                    $this->{$dkey} = $dval;
			}
		}
		
            $Db->model = $this->model;
	}
}
}

?>