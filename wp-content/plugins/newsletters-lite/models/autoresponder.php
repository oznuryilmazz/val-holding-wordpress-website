<?php

if (!class_exists('wpmlAutoresponder')) {
	class wpmlAutoresponder extends wpmlDbHelper
    {
		var $model = 'Autoresponder';
		var $controller = 'autoresponders';
		var $table = '';
		
		var $fields = array(
			'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'title'				=>	"VARCHAR(250) NOT NULL DEFAULT ''",
			'history_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'status'			=>	"ENUM('active','inactive') NOT NULL DEFAULT 'active'",
			'sendauto'			=>	"INT(1) NOT NULL DEFAULT '1'",
			'delay'				=>	"INT(11) NOT NULL DEFAULT '0'",
			'delayinterval'		=>	"VARCHAR(50) NOT NULL DEFAULT 'days'",
			'applyexisting'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'alwayssend'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`history_id`), INDEX(`status`)",
		);
		
		var $tv_fields = array(
			'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'				=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
			'history_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'status'			=>	array("ENUM('active','inactive')", "NOT NULL DEFAULT 'active'"),
			'sendauto'			=>	array("INT(1)", "NOT NULL DEFAULT '1'"),
			'delay'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'delayinterval'		=>	array("VARCHAR(50)", "NOT NULL DEFAULT 'days'"),
			'applyexisting'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'alwayssend'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`history_id`), INDEX(`status`)",					   
		);
		
		var $indexes = array('history_id', 'status', 'delay', 'alwayssend');
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $wpdb, $Mailinglist, $Db;
		
			$this -> table = $this -> pre . $this -> controller;
			
			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {				
					$this -> {$dkey} = stripslashes_deep($dval);
				}
				
				$this -> mailinglists = array();
				if ($autoresponderslists = $this -> AutorespondersList() -> find_all(array('autoresponder_id' => $this -> id))) {				
					foreach ($autoresponderslists as $autoresponderslist) {
						$Db -> model = $Mailinglist -> model;
						$this -> lists[] = $autoresponderslist -> list_id;
						$this -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
					}
				}
				
				$this -> forms = array();
				if ($autorespondersforms = $this -> AutorespondersForm() -> find_all(array('autoresponder_id' => $this -> id))) {
					foreach ($autorespondersforms as $autorespondersform) {
						$this -> forms[] = $autorespondersform -> form_id;
					}
				}
				
				$this -> pending = $this -> Autoresponderemail() -> count(array('autoresponder_id' => $this -> id, 'status' => "unsent"));
			}
			
            if (!empty($this -> model) && !empty($Db -> model)) {
                $Db -> model = $this -> model;
            }
		}
		
		function defaults()
        {
			global $Html;
			
			$defaults = array(
				'history_id'		=>	1,
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
                if (empty($title)) {
                    $this -> errors['title'] = __('Please fill in a title.', 'wp-mailinglist');
                }
                if (empty($lists) && empty($forms)) {
                    $this -> errors['lists'] = __('Please select subscribe form/s OR mailing list/s.', 'wp-mailinglist');
                }
				
				if (empty($newsletter)) {
					$this -> errors['newsletter'] = __('Please choose a newsletter type.', 'wp-mailinglist');	
				} else {
					if ($newsletter == "new") {
                        if (empty($nnewsletter['subject'])) {
                            $this -> errors['nnewsletter_subject'] = __('Please fill in a subject.', 'wp-mailinglist');
                        }
                        if (empty($_POST['content'])) {
                            $this -> errors['nnewsletter_content'] = __('Please fill in content for this newsletter.', 'wp-mailinglist');
                        }
					} else {
                        if (empty($history_id)) {
                            $this -> errors['history_id'] = __('Please select a history email.', 'wp-mailinglist');
                        }
					}
				}
				
				if (!empty($sendauto)) {
                    if (empty($delay) && $delay != "0") {
                        $this -> errors['delay'] = __('Please fill in a send delay.', 'wp-mailinglist');
                    }
				} else {
					$this -> data -> sendauto = 0;
				}
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}
			
			$this -> errors = apply_filters('newsletters_autoresponder_validation', $this -> errors, $data);
			
			return $this -> errors;
		}
		
		function select()
        {
			global $Db;		
			$select = array();		
			$Db -> model = $this -> model;
			
			if ($autoresponders = $Db -> find_all(false, array('id', 'title'), array('title', "ASC"))) {		
				foreach ($autoresponders as $autoresponder) {
					$select[$autoresponder -> id] = $autoresponder -> title;
				}
			}
			
			return $select;
		}
		
		function save($data = array(), $validate = true)
        {
			return parent::save($data, $validate);
		}
	}
}

include_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'newsletter.php');

?>