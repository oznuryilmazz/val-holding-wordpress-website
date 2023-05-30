<?php

if (!class_exists('wpmlUnsubscribe')) {
    class wpmlUnsubscribe extends wpMailPlugin
    {
	var $model = 'Unsubscribe';
	var $controller = 'unsubscribes';
	var $table;
	
	var $fields = array(
            'id' => "INT(11) NOT NULL AUTO_INCREMENT",
            'email' => "VARCHAR(250) NOT NULL DEFAULT ''",
            'user_id' => "INT(11) NOT NULL DEFAULT '0'",
            'mailinglist_id' => "INT(11) NOT NULL DEFAULT '0'",
            'history_id' => "INT(11) NOT NULL DEFAULT '0'",
            'comments' => "TEXT NOT NULL",
            'created' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key' => "PRIMARY KEY (`id`), INDEX(`email`), INDEX(`user_id`), INDEX(`mailinglist_id`), INDEX(`history_id`)",
	);
	
	var $tv_fields = array(
            'id' => array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'email' => array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
            'user_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'mailinglist_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'history_id' => array("INT(11)", "NOT NULL DEFAULT '0'"),
            'comments' => array("TEXT", "NOT NULL"),
            'created' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified' => array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key' => "PRIMARY KEY (`id`), INDEX(`email`), INDEX(`user_id`), INDEX(`mailinglist_id`), INDEX(`history_id`)",
	);
	
	var $indexes = array('email', 'user_id', 'mailinglist_id', 'history_id');
	
        function __construct($data = array())
        {
		parent::__construct();
		
		global $wpdb, $Db, $Mailinglist;
            $this->table = $this->pre . $this->controller;
		
		if (!empty($data)) {
			foreach ($data as $dkey => $dval) {				
                    $this->{$dkey} = stripslashes_deep($dval);
				
				switch ($dkey) {
                        case 'user_id'                :
						if (!empty($dval)) {
                                $this->userdata = $this->userdata($dval);
						}
						break;
                        case 'mailinglist_id'        :
						if (!empty($dval)) {
                                $Db->model = $Mailinglist->model;
                                $this->mailinglist = $Db->find(array('id' => $dval));
                                $Db->model = $this->model;
						}
						break;
                        case 'history_id'            :
						if (!empty($dval)) {
                                $this->history = $this->History()->find(array('id' => $dval));
                                $Db->model = $this->model;
						}
						break;
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
		);
		
		return $defaults;	
	}
	
        function validate($data = array())
        {
		global $Db;
            $this->errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();
		
            $data = (empty($data[$this->model])) ? $data : $data[$this->model];
		$r = wp_parse_args($data, $defaults);
		extract($r, EXTR_SKIP);
		
		if (!empty($data)) {		
			if (!empty($user_id)) {
				global $Db;
                    $Db->model = $this->model;
                    if ($Db->find(array('user_id' => $user_id, 'history_id' => $history_id))) {
                        $this->errors[] = __('Already exists', 'wp-mailinglist');
				}
			}
		
                if (empty($email)) {
                    $this->errors['email'] = __('No email was specified.', 'wp-mailinglist');
                }
			//if (empty($mailinglist_id)) { $this -> errors['mailinglist_id'] = __('No mailing list was specified.', 'wp-mailinglist'); }
			//if (empty($history_id)) { $this -> errors['history_id'] = __('No history email was specified', 'wp-mailinglist'); }
			
                $Db->model = $this->model;
                if ($current = $Db->find(array('email' => $email, 'mailinglist_id' => $mailinglist_id))) {
                    $this->data->id = $current->id;
			}
		} else {
                $this->errors[] = __('No data was posted', 'wp-mailinglist');
		}
		
            return $this->errors;
	}
}
}

?>