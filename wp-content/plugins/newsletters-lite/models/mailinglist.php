<?php

if (!class_exists('wpmlMailinglist')) {
	class wpmlMailinglist extends wpMailPlugin
    {
		var $plugin_name;
		var $name = 'mailinglist';
		var $controller = 'mailinglists';
		var $model = 'Mailinglist';	
		var $table_name = 'wpmlmailinglists';
		
		var $fields = array(
			'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'title'				=>	"VARCHAR(255) NOT NULL DEFAULT ''",
			'slug'				=>	"VARCHAR(255) NOT NULL DEFAULT ''",
			'default'			=>	"INT(1) NOT NULL DEFAULT '0'",
			'privatelist'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'paid'				=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'price'				=>	"FLOAT NOT NULL DEFAULT '0.00'",
			'tcoproduct'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'interval'			=>	"ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once') NOT NULL DEFAULT 'monthly'",
			'maxperinterval'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'group_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'doubleopt'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'Y'",
			'adminemail'		=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'subredirect'		=>	"TEXT NOT NULL",
			'redirect'			=>	"TEXT NOT NULL",
			'owner_id'			=>	"INT(11) NOT NULL DEFAULT '0'",
			'owner_role'		=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`paid`), INDEX(`group_id`)",
		);
		
		var $tv_fields = array(
			'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'slug'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'default'			=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
			'privatelist'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'paid'				=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'price'				=>	array("FLOAT", "NOT NULL DEFAULT '0.00'"),
			'tcoproduct'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'interval'			=>	array("ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once')", "NOT NULL DEFAULT 'monthly'"),
			'maxperinterval'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'group_id'			=> 	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'doubleopt'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'Y'"),
			'adminemail'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'subredirect'		=>	array("TEXT", "NOT NULL"),
			'redirect'			=>	array("TEXT", "NOT NULL"),
			'owner_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'owner_role'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY (`id`), INDEX(`paid`), INDEX(`group_id`)",					   
		);
		
		var $indexes = array('paid', 'group_id');
		
		var $data = array();
		var $errors = array();
		
		var $id = 0;
		var $title;
		var $privatelist = "N";
		var $paid = "N";
		var $price = "0.00";
		var $tcoproduct = 0;
		var $interval = "daily";
		var $created = "0000-00-00 00:00:00";
		var $modified = "0000-00-00 00:00:00";
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $wpdb, $Db, $FieldsList, $SubscribersList;
		
			$this -> table = $this -> pre . $this -> controller;	
		
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					$this -> {$key} = stripslashes_deep($val);
					
					switch ($key) {
						case 'id'				:
							//$Db -> model = $SubscribersList -> model;
							//$this -> subscriberscount = $Db -> count(array('list_id' => $this -> id));
							$this -> subscriberscount = $this -> SubscribersList() -> count(array('list_id' => $this -> id));
							break;
						case 'group_id'			:
							if (!empty($val)) {						
								$this -> group = $this -> Group() -> find(array('id' => $val));	
							}
							break;	
					}
				}
	
	            $this -> cfields = array();
				if ($fieldslists = $FieldsList -> find_all(array('list_id' => $this -> id))) {
	                $f = 0;
	
					foreach ($fieldslists as $fl) {
					    $this -> fields[$f] = $fl -> field_id;
	                    $f++;
					}
				}
			}
			
			if ($this -> get_option('defaultlistcreated') == "N") {
				$list_data = array('title' => __('Default List', 'wp-mailinglist'), 'default' => "1", 'privatelist' => "N");
				
				$query = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `title` = 'Default List'";
				
				if (!$wpdb -> get_var($query)) {
					if ($this -> save($list_data)) {
						$this -> update_option('defaultlistcreated', "Y");
					}
				}
			}
			
			$Db -> model = $this -> model;
			return;
		}
		
		function has_paid_list($lists = array())
        {
			global $Db;

                if (!empty($lists) && is_array($lists)) {
                    foreach ($lists as $list_id) {
					$Db -> model = $this -> model;
					$list = $Db -> find(array('id' => $list_id));
					
					if (!empty($list -> paid) && $list -> paid == "Y") {
						return $list -> id;
					}
				}
			}
			
			return false;
		}
		
		/**
		 * Counts all the mailinglist records.
		 * @return INT the number of mailing list records.
		 *
		 */
		function count($conditions = array())
        {
			global $wpdb;
			$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table_name . "`";
			
			if (!empty($conditions)) {
				$query .= " WHERE";
				$c = 1;
				
				foreach ($conditions as $ckey => $cval) {
					$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
					
					if (count($conditions) > $c) {
						$query .= " AND";
					}
					
					$c++;
				}
			}
			
			$query_hash = md5($query);
			if ($ob_count = $this -> get_cache($query_hash)) {
				$count = $ob_count;
			} else {
				$count = $wpdb -> get_var($query);
				$this -> set_cache($query_hash, $count);
			}
			
			if (!empty($count)) {
				return $count;
			}
			
			return 0;
		}
		
		function get_default($idonly = true)
        {
			global $wpdb;
			
			$query = "SELECT " . ((!empty($idonly)) ? '`id`' : '*') . " FROM `" . $wpdb -> prefix . $this -> table . "` WHERE `default` = '1'";
			
			$query_hash = md5($query);
			if ($ob_list = $this -> get_cache($query_hash)) {
				return $ob_list;
			}
			
			if ($list = $wpdb -> get_row($query)) {
				$data = $this -> init_class($this -> model, $list);
				
				if (!empty($idonly)) {
					$data = $data -> id;	
				}
				
				$this -> set_cache($query_hash, $data);
				return $data;	
			}
			
			return false;
		}
		
		function get($mailinglist_id = null, $assign = true)
        {
			global $wpdb;
			
			if (!empty($mailinglist_id)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . $this -> table_name . "` WHERE `id` = '" . esc_sql($mailinglist_id) . "' LIMIT 1";
				$query_hash = md5($query);
				if ($ob_list = $this -> get_cache($query_hash, 'query')) {					
					return $ob_list;
				}
			
				if ($list = $wpdb -> get_row($query)) {
					$data = $this -> init_class($this -> model, $list);
					
					if ($assign == true) {
						//$this -> data = array($this -> model => $data);				
						$this -> data = $data;
					}
					
					$this -> set_cache($query_hash, $data);
					return $data;
				}
			}
			
			return false;
		}
		
		function get_by_subscriber_id($subscriber_id = null)
        {
			global $wpdb;
			
			if (!empty($subscriber_id)) {
				if ($subscriber = $this -> Subscriber -> get($subscriber_id)) {
					if ($mailinglist = $this -> get($subscriber -> list_id)) {
						return $this -> init_class('wpmlMailinglist', $mailinglist);
					}
				}
			}
			
			return false;
		}
		
		function select($privatelists = false, $ids = null)
        {
			global $wpdb, $Html;
			
			$privatecond = ($privatelists == true) ? "WHERE 1 = 1" : "WHERE `privatelist` = 'N'";
			
			if (!empty($ids) && is_array($ids)) {
				$p = 1;
				$privatecond .= " AND (";
			
				foreach ($ids as $id) {
					$privatecond .= "id = '" . $id . "'";
                    if ($p < count($ids)) {
                        $privatecond .= " OR ";
                    }
					$p++;
				}
				
				$privatecond .= ")";
			}
			
	        $query = "SELECT `id`, `title`, `paid`, `price`, `interval` FROM `" . $wpdb -> prefix . $this -> table_name . "` " . $privatecond . " ORDER BY `title` ASC";
	        
	        $query_hash = md5($query);
	        if ($ob_lists = $this -> get_cache($query_hash)) {
		        $lists = $ob_lists;
	        } else {
		        $lists = $wpdb -> get_results($query);
		        $this -> set_cache($query_hash, $lists);
	        }
	
			if (!empty($lists)) {			
				$listselect = array();
				$this -> intervals = $this -> get_option('intervals');
				
				foreach ($lists as $list) {
					$paid = ($list -> paid == "Y") ? ' <span class="wpmlsmall">(' . __('Paid', 'wp-mailinglist') . ': ' . $Html -> currency() . '' . number_format($list -> price, 2, '.', '') . ' ' . $this -> intervals[$list -> interval] . ')</span>' : '';
					$listselect[$list -> id] = esc_html($list -> title) . $paid;
				}
				
				// sort the mailing lists alphabetically after applying gettext
				asort($listselect);
				
				return apply_filters($this -> pre . '_mailinglists_select', $listselect);
			}
			
			return false;
		}
		
		/**
		 * Checks whether or not a list exists.
		 * Simply executes a query and checks for an ID value.
		 * @param INT the ID of the mailing list record to check for.
		 * @return BOOLEAN either true or false is returned
		 *
		 */
		function list_exists($list_id = null)
        {
			global $wpdb;
		
			if (!empty($list_id)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . esc_sql($list_id) . "'";
				
				$query_hash = md5($query);
				if ($ob_list = $this -> get_cache($query_hash)) {
					$list = $ob_list;
				} else {
					$list = $wpdb -> get_row($query);
					$this -> set_cache($query_hash, $list);
				}
			
				if (!empty($list)) {
					new Mailinglist($list);
					return true;
				}
			}
			
			return false;
		}
		
		function get_title_by_id($id = null)
        {
			global $wpdb;
		
			if (!empty($id)) {
				$query = "SELECT `title` FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . esc_sql($id) . "' LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_title = $this -> get_cache($query_hash)) {
					$title = $ob_title;
				} else {
					$title = $wpdb -> get_var($query);
					$this -> set_cache($query_hash, $title);
				}
			
				if (!empty($title)) {
					return esc_html($title);
				}
			}
			
			return false;
		}
		
		function get_all($fields = '*', $privatelists = false)
        {
			global $wpdb;
			
			$privatecond = ($privatelists == true) ? "" : "WHERE `privatelist` = 'N'";
			
			$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` " . $privatecond . " ORDER BY `title` ASC";
			
			$query_hash = md5($query);
			if ($ob_lists = $this -> get_cache($query_hash)) {
				return $ob_lists;
			}
			
			if ($lists = $wpdb -> get_results($query)) {
				$data = array();
			
				foreach ($lists as $list) {
					$data[] = $this -> init_class('wpmlMailinglist', $list);
				}
				
				$this -> set_cache($query_hash, $data);
				return $data;
			}
			
			return false;
		}
		
		function get_all_paginated($conditions = array(), $searchterm = null, $sub = 'newsletters-lists', $perpage = 15, $order = array('modified', "DESC"))
        {
			global $wpdb;
			
			$paginate = new wpMailPaginate($wpdb -> prefix . "" . $this -> table_name, "*", $sub);
			$paginate -> perpage = $perpage;
			$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
			$paginate -> where = (empty($conditions)) ? false : $conditions;
			$paginate -> order = $order;
            $lists = $paginate -> start_paging(isset($_GET[$this -> pre . 'page']) ? $_GET[$this -> pre . 'page'] : '');
			
			$data = array();
			$data['Pagination'] = $paginate;
			
			if (!empty($lists)) {
				foreach ($lists as $list) {
					$data['Mailinglist'][] = $this -> init_class('wpmlMailinglist', $list);
				}
			}
			
			return $data;
		}
		
		function save($data = array(), $validate = true)
        {
			global $wpdb, $FieldsList, $Html;
			
			$owner_id = 0;
			$owner_role = '';
			include_once(ABSPATH . 'wp-includes/pluggable.php');
			if ($current_user = wp_get_current_user()) {
				$owner_id = $current_user -> ID;
				$owner_role = $current_user -> roles[0];
			}
			
			$defaults = array(
				'group_id'			=>	0,
				'paid' 				=>	"N",
				'owner_id'			=>	$owner_id,
				'owner_role'		=>	$owner_role,
				'doubleopt'			=>	"N",
				'subredirect'		=>	false,
				'redirect'			=>	false,
				'adminemail'		=>	false,
				'tcoproduct'		=>	false,
				'price'				=>	false,
				'interval'			=>	false,
				'maxperinterval'	=> 	false,
			);
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			$this -> data = array();
			$this -> data = (object) $r;
			extract($r, EXTR_SKIP);
		
			if (!empty($data)) {			
				if ($validate == true) {				
                    if (empty($title)) {
                        $this -> errors['title'] = __('Please fill in a title', 'wp-mailinglist');
                    } elseif (is_array($title) && !array_filter($title)) {
						$this -> errors['title'] = __('Please fill in a title', 'wp-mailinglist'); 	
					}				
					
                    if (empty($privatelist)) {
                        $this -> errors['privatelist'] = __('Please select private status', 'wp-mailinglist');
                    }
					
					if (empty($paid)) {
						$this -> errors['paid'] = __('Please select a paid status', 'wp-mailinglist');
					} else {
						if ($paid == "Y") {
							if ($this -> get_option('paymentmethod') == "2co") {
                                if (empty($tcoproduct)) {
                                    $this -> errors['tcoproduct'] = __('Please fill in a valid 2Checkout product ID', 'wp-mailinglist');
                                }
							}
							
                            if (empty($interval)) {
                                $this -> errors['interval'] = __('Please select a subscription interval', 'wp-mailinglist');
                            }
                            if (empty($price)) {
                                $this -> errors['price'] = __('Please fill in a subscription price', 'wp-mailinglist');
                            }
						}
					}
				}
				
				$this -> errors = apply_filters('newsletters_mailinglist_validation', $this -> errors, $this -> data);
				$this -> errors = apply_filters($this -> pre . '_mailinglist_validation', $this -> errors, $this -> data);
				
				if (empty($this -> errors)) {
					$created = $modified = $this -> gen_date();
					
					if ($this -> language_do()) {
						$title = $this -> language_join($title);
						$subredirect = $this -> language_join($subredirect);
						$redirect = $this -> language_join($redirect);
					}
					
					$slug = $Html -> sanitize(esc_html($title));
				
					$query = (!empty($id)) ?
					"UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `title` = '" . esc_sql($title) . "', `slug` = '" . esc_sql($slug) . "', `group_id` = '" . esc_sql($group_id) . "', `doubleopt` = '" . esc_sql($doubleopt) . "', `subredirect` = '" . esc_sql($subredirect) . "', `redirect` = '" . esc_sql($redirect) . "', `owner_id` = '" . esc_sql($owner_id) . "', `owner_role` = '" . esc_sql($owner_role) . "', `adminemail` = '" . esc_sql($adminemail) . "', `privatelist` = '" . esc_sql($privatelist) . "', `paid` = '" . esc_sql($paid) . "', `tcoproduct` = '" . esc_sql($tcoproduct) . "', `price` = '" . esc_sql($price) . "', `interval` = '" . esc_sql($interval) . "', `maxperinterval` = '" . esc_sql($maxperinterval) . "', `modified` = '" . $modified . "' WHERE `id` = '" . esc_sql($id) . "' LIMIT 1" :
					"INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`title`, `slug`, `group_id`, `doubleopt`, `subredirect`, `redirect`, `owner_id`, `owner_role`, `adminemail`, `privatelist`, `paid`, `tcoproduct`, `price`, `interval`, `maxperinterval`, `created`, `modified`) VALUES ('" . esc_sql($title) . "', '" . esc_sql($slug) . "', '" . esc_sql($group_id) . "', '" . esc_sql($doubleopt) . "', '" . esc_sql($subredirect) . "', '" . esc_sql($redirect) . "', '" . esc_sql($owner_id) . "', '" . esc_sql($owner_role) . "', '" . esc_sql($adminemail) . "', '" . esc_sql($privatelist) . "', '" . esc_sql($paid) . "', '" . esc_sql($tcoproduct) . "', '" . esc_sql($price) . "', '" . esc_sql($interval) . "', '" . esc_sql($maxperinterval) . "', '" . $created . "', '" . $modified . "');";
					
					if ($wpdb -> query($query)) {
						$this -> insertid = (empty($id)) ? $wpdb -> insert_id : $id;
						do_action($this -> pre . '_admin_mailinglist_saved', $this -> insertid, $this -> data);
						
						if (!empty($fields)) {
							$FieldsList -> delete_all(array('list_id' => $this -> insertid));
						
							foreach ($fields as $field_id) {
								$fl_data = array('field_id' => $field_id, 'list_id' => $this -> insertid);						
								$FieldsList -> save($fl_data, true);
							}
						}
						
						return true;
					}
				}
			}
			
			return false;
		}
		
		function paid_stamp($interval = null, $now = null, $fromexpiration = false)
        {
			if (empty($now)) {
				$now = current_time('timestamp');
			}
			
			$operator = (!empty($fromexpiration)) ? '-' : '+';
			
			if (!empty($interval)) {
				switch ($interval) {
					case 'daily'					:
						$stamp = strtotime($operator . "1 day", $now);
						break;
					case 'weekly'					:
						$stamp = strtotime($operator . "1 week", $now);
						break;
					case 'monthly'					:
						$stamp = strtotime($operator . "1 month", $now);
						break;
					case '2months'					:
						$stamp = strtotime($operator . "2 months", $now);
						break;
					case '3months'					:
						$stamp = strtotime($operator . "3 months", $now);
						break;
					case 'biannually'				:
						$stamp = strtotime($operator . "6 months", $now);
						break;
					case '9months'					:
						$stamp = strtotime($operator . "9 months", $now);
						break;
					case 'yearly'					:
						$stamp = strtotime($operator . "1 year", $now);
						break;
					case 'once'						:
					default							:
						$stamp = strtotime($operator . "99 years", $now);
						break;
				}
				
				return $stamp;
			}
			
			return false;
		}
		
		function has_expired($subscriber_id = null, $list_id = null)
        {
			global $wpdb, $Subscriber, $SubscribersList;
			
			if (!empty($subscriber_id) && !empty($list_id)) {			
				if ($subscriberslist = $SubscribersList -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $list_id))) {								
					if ($mailinglist = $this -> get($list_id, false)) {					
						if ($subscriberslist -> paid == "Y" || !empty($subscriberslist -> paid_date)) {
							switch ($mailinglist -> interval) {
								case 'daily'					:
									$intervalstring = "-1 day";
									break;
								case 'weekly'					:
									$intervalstring = "-1 week";
									break;
								case 'monthly'					:
									$intervalstring = "-1 month";
									break;
								case '2months'					:
									$intervalstring = "-2 months";
									break;
								case '3months'					:
									$intervalstring = "-3 months";
									break;
								case 'biannually'				:
									$intervalstring = "-6 months";
									break;
								case '9months'					:
									$intervalstring = "-9 months";
									break;
								case 'yearly'					:
									$intervalstring = "-1 year";
									break;
								case 'once'						:
								default							:
									$intervalstring = "-99 years";
									break;
							}
						
							$paiddate = strtotime($subscriberslist -> paid_date);
							$expiry = current_time('timestamp') - strtotime($intervalstring);
							$expiration = $paiddate + $expiry;
							
							if ($expiration <= current_time('timestamp')) {
								return true;
							}
						}
					}
				}
			}
			
			return false;
		}
		
		function gen_expiration_date($subscriber_id = null, $list_id = null)
        {
			global $wpdb, $Db, $Subscriber, $SubscribersList;
			
			if (!empty($subscriber_id) && !empty($list_id)) {			
				if ($subscriberslist = $SubscribersList -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $list_id))) {								
					if ($mailinglist = $this -> get($list_id, false)) {					
						if ($subscriberslist -> paid == "Y" || !empty($subscriberslist -> paid_date)) {
							switch ($mailinglist -> interval) {
								case 'daily'					:
									$intervalstring = "-1 day";
									break;
								case 'weekly'					:
									$intervalstring = "-1 week";
									break;
								case 'monthly'					:
									$intervalstring = "-1 month";
									break;
								case '2months'					:
									$intervalstring = "-2 months";
									break;
								case '3months'					:
									$intervalstring = "-3 months";
									break;
								case 'biannually'				:
									$intervalstring = "-6 months";
									break;
								case '9months'					:
									$intervalstring = "-9 months";
									break;
								case 'yearly'					:
									$intervalstring = "-1 year";
									break;
								case 'once'						:
								default							:
									$intervalstring = "-99 years";
									break;
							}
						
							$paiddate = strtotime($subscriberslist -> paid_date);
							$expiry = current_time('timestamp') - strtotime($intervalstring);
							$expiration = $paiddate + $expiry;
							$expiration = $this -> gen_date("Y-m-d", $expiration);
							
							$Db -> model = $SubscribersList -> model;
							$Db -> save_field('expiry_date', $expiration, array('rel_id' => $subscriberslist -> rel_id));
							
							return $expiration;
						}
					}
				}
			}
			
			return false;
		}
		
		function save_field($field = null, $value = null, $id = null)
        {
			global $wpdb;
		
			if (!empty($field) && !empty($value)) {
				$list_id = (empty($id)) ? $this -> id : $id;
				$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `" . $field . "` = '" . esc_sql($value) . "' WHERE `id` = '" . esc_sql($list_id) . "'";
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
			
			return false;
		}
		
		function delete_subscribers($mailinglist_id = null)
        {
			global $wpdb, $Subscriber, $Mailinglist, $Email;
			
			$subquery = "SELECT `subscriber_id` FROM " . $wpdb -> prefix . $this -> SubscribersList() -> table . " WHERE `list_id` = '" . $mailinglist_id . "'";
			
			$query = "DELETE FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			$query = "DELETE FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `subscriber_id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			$query = "DELETE FROM " . $wpdb -> prefix . $this -> SubscriberMeta() -> table . " WHERE `subscriber_id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			$query = "DELETE FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " WHERE `subscriber_id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			$query = "DELETE FROM " . $wpdb -> prefix . $this -> Click() -> table . " WHERE `subscriber_id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			$query = "DELETE FROM " . $wpdb -> prefix . $Email -> table . " WHERE `subscriber_id` IN (" . $subquery . ")";
			$wpdb -> query($query);
			
			//last query
			$query = "DELETE FROM " . $wpdb -> prefix . $this -> SubscribersList() -> table . " WHERE `list_id` = '" . $mailinglist_id . "'";
			$wpdb -> query($query);
			
			return true;
		}
		
		function delete($mailinglist_id = null)
        {
			global $wpdb, $Db, $SubscribersList, $FieldsList, $HistoriesList;
		
			if (!empty($mailinglist_id)) {
				$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `id` = '" . esc_sql($mailinglist_id) . "' LIMIT 1";				
				if ($wpdb -> query($query)) {
					$SubscribersList -> delete_all(array('list_id' => $mailinglist_id));				
					$FieldsList -> delete_all(array('list_id' => $mailinglist_id));	
					
					$Db -> model = $HistoriesList -> model;
					$Db -> delete_all(array('list_id' => $mailinglist_id));
					
					return true;
				}
			}
			
			return false;
		}
		
		function delete_array($lists = array())
        {
			global $wpdb, $SubscribersList;
			
			if (!empty($lists)) {		
				foreach ($lists as $list) {
					$this -> delete($list);
				}
				
				return true;
			}
			
			return false;
		}
	}
}

include_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'newsletter.php');

?>