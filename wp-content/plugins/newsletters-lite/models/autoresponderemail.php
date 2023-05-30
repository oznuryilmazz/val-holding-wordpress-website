<?php

if (!class_exists('wpmlAutoresponderemail')) {
    class wpmlAutoresponderemail extends wpmlDbHelper
    {
	var $model = "Autoresponderemail";
	var $controller = "autoresponderemails";
	var $table;
	
	var $fields = array(
            'id' => "INT(11) NOT NULL AUTO_INCREMENT",
            'autoresponder_id' => "INT(11) NOT NULL DEFAULT '0'",
            'list_id' => "INT(11) NOT NULL DEFAULT '0'",
            'form_id' => "INT(11) NOT NULL DEFAULT '0'",
            'subscriber_id' => "INT(11) NOT NULL DEFAULT '0'",
            'senddate' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'status' => "ENUM('sent','unsent') NOT NULL DEFAULT 'unsent'",
            'created' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key' => "PRIMARY KEY (`id`), INDEX(`autoresponder_id`), INDEX(`list_id`), INDEX(`subscriber_id`), INDEX(`status`)",
	);
	
	var $tv_fields = array(
            'id' => array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'autoresponder_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'list_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'form_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'subscriber_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'senddate' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'status' => array("ENUM('sent','unsent')", "NOT NULL DEFAULT 'unsent'"),
            'created' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key' => "PRIMARY KEY (`id`), INDEX(`autoresponder_id`), INDEX(`list_id`), INDEX(`subscriber_id`), INDEX(`status`)",
	);
	
	var $indexes = array('autoresponder_id', 'list_id', 'subscriber_id', 'status');
	
        function __construct($data = array())
        {
		parent::__construct();
		
		global $wpdb, $Db, $Subscriber, $SubscribersList;
		
            $this->table = $this->pre . $this->controller;
		
		if (!empty($data)) {		
			foreach ($data as $dkey => $dval) {				
                    $this->{$dkey} = stripslashes_deep($dval);
				
                    if (!empty($data->recursive) && $data->recursive == true) {
					switch ($dkey) {
                            case 'subscriber_id'            :
                                $Db->model = $Subscriber->model;
                                $this->subscriber = $Db->find(array('id' => $dval));
							break;
                            case 'autoresponder_id'            :
                                $Db->model = $this->Autoresponder()->model;
                                $this->autoresponder = $Db->find(array('id' => $dval));
							break;	
					}
				}
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
                'status' => "unsent",
		);
		
		return $defaults;	
	}
	
        function validate($data = array())
        {
            $this->errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();
		
            $data = (empty($data[$this->model])) ? $data : $data[$this->model];
		$r = wp_parse_args($data, $defaults);
		extract($r, EXTR_SKIP);
		
		if (!empty($data)) {
                if (empty($autoresponder_id)) {
                    $this->errors['autoresponder_id'] = __('No autoresponder was specified.', 'wp-mailinglist');
                }
                if (empty($subscriber_id)) {
                    $this->errors['subscriber_id'] = __('No subscriber was specified.', 'wp-mailinglist');
                }
		} else {
                $this->errors[] = __('No data was posted', 'wp-mailinglist');
		}
		
		/* Check if the record exists */
            if (empty($this->errors)) {
			global $Db;
                $Db->model = $this->model;
			
                if ($Db->find(array('autoresponder_id' => $autoresponder_id, 'subscriber_id' => $subscriber_id))) {
                    $this->errors[] = __('Autoresponder email already exists.', 'wp-mailinglist');
			}
		}
		
            return $this->errors;
	}
}
}

?>