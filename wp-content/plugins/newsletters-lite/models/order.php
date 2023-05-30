<?php

if (!class_exists('wpmlOrder')) {
	class wpmlOrder extends wpmlDbHelper
    {
		
		/*var $name = 'wpmlorder';
		var $controller = 'orders';
		var $model = 'wpmlOrder';
		var $table = '';
		var $table_name = 'wpmlorders';*/
		
		var $model = 'Order';
		var $controller = 'orders';
	
		/**
		 * The ID of the order record
		 *
		 **/
		var $id = '';
		
		/**
		 * The ID of the subscriber whom submitted the order
		 *
		 **/
		var $subscriber_id = '';
		
		/**
		 * Total amount for the order
		 *
		 **/
		var $amount = 0;
		
		/**
		 * Indicates paid status.
		 * "Y" represents that the order has been paid
		 *
		 **/
		var $completed = 'N';
		var $product_id = 0;
		var $order_number = 0;
		var $created = '0000-00-00 00:00:00';
		var $modified = '0000-00-00 00:00:00';
		var $errors = array();
		var $data = array();
		var $insert_id = 0;
		
		var $table_fields = array(
			'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'subscriber_id'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'list_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'completed'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'amount'		=>	"FLOAT NOT NULL DEFAULT '0.00'",
			'product_id'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'order_number'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'pmethod'		=>	"ENUM('pp','2co') NOT NULL DEFAULT 'pp'",
			'reference'		=>	"VARCHAR(250) NOT NULL DEFAULT ''",
			'subref'		=>	"VARCHAR(250) NOT NULL DEFAULT ''",
			'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'			=>	"PRIMARY KEY (`id`), INDEX(`subscriber_id`), INDEX(`list_id`)"
		);
		
		var $tv_fields = array(
			'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'subscriber_id'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'list_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'completed'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'amount'		=>	array("FLOAT", "NOT NULL DEFAULT '0.00'"),
			'product_id'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'order_number'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'pmethod'		=>	array("ENUM('pp','2co')", "NOT NULL DEFAULT 'pp'"),
			'reference'		=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
			'subref'		=>	array("VARCHAR(250)", "NOT NULL DEFAULT ''"),
			'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'			=>	"PRIMARY KEY (`id`), INDEX(`subscriber_id`), INDEX(`list_id`)"					   
		);
		
		var $indexes = array('subscriber_id', 'list_id', 'product_id', 'order_number');
	
		function __construct($data = array())
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
		
		/**
		 * Retrieves a single Order record with an ID condition
		 * @param INT The ID of the Order record to get.
		 * @return OBJ An Order object with the values of the order.
		 *
		 **/
		function get($order_id = null)
        {
			global $wpdb;
			
			//make sure an order ID is available
			if (!empty($order_id)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . "wpmlorders` WHERE `id` = '" . esc_sql($order_id) . "' LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_order = $this -> get_cache($query_hash)) {
					return $ob_order;
				}
			
				if ($order = $wpdb -> get_row($query)) {
					$order = $this -> init_class($this -> model, $order);
					$this -> set_cache($query_hash, $order);
					return $order;
				}
			}
			
			return false;
		}
		
		/**
		 * Retrieve all orders in a paginated fashion
		 * @param $conditions ARRAY conditions passed on to the pagination class.
		 * @return $data ARRAY an array of order objects retrieved from the database
		 *
		 **/
		function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-orders', $perpage = 15, $order = array('modified', "DESC"))
        {
			global $wpdb;
			
			$paginate = new wpMailPaginate($wpdb -> prefix . $this -> table_name, '*', $sub, $sub);
			$paginate -> where = (empty($conditions)) ? false : $conditions;
			$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
			$paginate -> perpage = $perpage;
			$paginate -> order = $order;
			$orders = $paginate -> start_paging(sanitize_text_field(wp_unslash($_GET['wpmlpage'])));
			
			$data = array();
			$data['Pagination'] = $paginate;
			
			if (!empty($orders)) {
				foreach ($orders as $order) {
					$data[$this -> model][] = $this -> init_class($this -> model, $order);
				}
			}
			
			return $data;
		}
		
		function delete_by_subscriber($subscriber_id = null)
        {
			global $wpdb;
			
			if (!empty($subscriber_id)) {
				if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "wpmlorders` WHERE `subscriber_id` = '" . $subscriber_id . "'")) {
					return true;
				} else {
					$this -> errors[] = __('No order records were removed', 'wp-mailinglist');
					return false;
				}
			} else {
				$this -> errors[] = __('No subscriber ID was specified for deleting orders', 'wp-mailinglist');
				return false;
			}
		}
	}
}

?>