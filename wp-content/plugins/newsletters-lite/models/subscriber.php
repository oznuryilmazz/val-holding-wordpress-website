<?php

if (!class_exists('wpmlSubscriber')) {
	class wpmlSubscriber extends wpMailPlugin
    {
		var $model = 'Subscriber';
		var $controller = 'subscribers';
		var $table = '';
		
		var $id;
		var $email;
		var $registered = "N";
		var $user_id = 0;
		var $emailssent = 0;
	    var $bouncecount = 0;
		var $created = '0000-00-00 00:00:00';
		var $modified = '0000-00-00 00:00:00';
		
		var $insertid = '';
		var $recursive = true;
	
		var $error = array();
		var $errors = array();
		var $data = array();
		
		var $table_fields = array(
			'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'email'			=>	"VARCHAR(155) NOT NULL DEFAULT ''",
			'registered'	=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'ip_address'	=>	"VARCHAR(20) NOT NULL DEFAULT ''",
			'country'		=>	"VARCHAR(20) NOT NULL DEFAULT ''",
			'referer'		=>	"VARCHAR(200) NOT NULL DEFAULT ''",
			'user_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'owner_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
			'owner_role'	=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'emailssent'	=>	"INT(11) NOT NULL DEFAULT '0'",
			'format'		=>	"ENUM('html','text') NOT NULL DEFAULT 'html'",
			'cookieauth'	=>	"TEXT NOT NULL",
			'authkey'		=>	"VARCHAR(32) NOT NULL DEFAULT ''",
			'authinprog'	=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
			'password'		=>	"VARCHAR(32) NOT NULL DEFAULT ''",
	        'bouncecount'   =>  "INT(1) NOT NULL DEFAULT '0'",
	        'mandatory'		=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
	        'device'		=>	"VARCHAR(100) NOT NULL DEFAULT ''",
			'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'			=>	"PRIMARY KEY (`id`), UNIQUE KEY `email_unique` (`email`), INDEX(`email`), INDEX(`registered`), INDEX(`ip_address`), INDEX(`user_id`), INDEX(`format`), INDEX(`device`)",
		);
		
		var $tv_fields = array(
			'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'email'			=>	array("VARCHAR(155)", "NOT NULL DEFAULT ''"),
			'registered'	=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'ip_address'	=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'country'		=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
			'referer'		=>	array("VARCHAR(200)", "NOT NULL DEFAULT ''"),
			'user_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'owner_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'owner_role'	=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'emailssent'	=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
			'format'		=>	array("ENUM('html','text')", "NOT NULL DEFAULT 'html'"),
			'cookieauth'	=> 	array("TEXT", "NOT NULL"),
			'authkey'		=>	array("VARCHAR(32)", "NOT NULL DEFAULT ''"),
			'authinprog'	=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
			'password'		=>	array("VARCHAR(32)", "NOT NULL DEFAULT ''"),
	        'bouncecount'   =>  array("INT(1)", "NOT NULL DEFAULT '0'"),
	        'mandatory'		=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
	        'device'		=>	array("VARCHAR(100)", "NOT NULL DEFAULT ''"),
			'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'			=>	"PRIMARY KEY (`id`), UNIQUE KEY `email_unique` (`email`), INDEX(`email`), INDEX(`registered`), INDEX(`ip_address`), INDEX(`user_id`), INDEX(`format`), INDEX(`device`)",					   
		);
		
		var $indexes = array('email', 'registered', 'ip_address', 'user_id', 'format', 'device');
		
		var $name = 'wpmlSubscriber';
		
		function __construct($data = array())
        {
			parent::__construct();
			
			global $Db;
		
			$this -> table = $this -> pre . $this -> controller;
		
			if (!empty($data)) {
				global $wpdb, $Db, $SubscribersList, $Mailinglist;
			
				foreach ($data as $key => $val) {
					$this -> {$key} = stripslashes_deep($val);
				
					if (!empty($data -> recursive) && $data -> recursive == true) {									
						switch ($key) {
							case 'id'		:
								$Db -> model = $SubscribersList -> model;
								if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $val))) {
									foreach ($subscriberslists as $sl) {
										$listquery = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $sl -> list_id . "' LIMIT 1";
										
										$query_hash = md5($listquery);
										if ($ob_list = $this -> get_cache($query_hash)) {
											$list = $ob_list;
										} else {
											$list = $wpdb -> get_row($listquery);
											$this -> set_cache($query_hash, $list);
										}
										
                                        $this -> Mailinglist[] = wp_unslash($list);
										$this -> subscriptions[$sl -> list_id] = $sl;
									}
								}
								break;
							case 'format'	:
								$this -> format = (empty($val)) ? 'html' : $val;
								break;
							case 'ip_address'		:
								if (empty($data -> country)) {
									if (!empty($val)) {
										$ipaddress = esc_html($val);
										if ($ipcountry = $this -> get_country_by_ip($ipaddress)) {
											if (!empty($this -> id)) {
												$this -> save_field('country', $ipcountry, $this -> id);
												$this -> country = $ipcountry;
											}
										}
									}
								}
								break;
						}
						
						if (!empty($val)) {
							if (!in_array($key, $this -> table_fields)) {
								//$val = maybe_unserialize($val);					
								//$_REQUEST[$key] = sanitize_text_field($_POST[$key]) = $val;
							}
						}
					}
				}
			}
			
			$Db -> model = $this -> model;
		}
		
		function admin_subscriber_id($mailinglists = array())
        {
			$adminemail = $this -> get_option('adminemail');
			
			if (strpos($adminemail, ",") !== false) {
				$adminemails = explode(",", $adminemail);
				foreach ($adminemails as $adminemail) {
					$adminemail = trim($adminemail);
					if (!$subscriber_id = $this -> email_exists($adminemail)) {
						$subscriberdata = array(
							'email'				=>	$adminemail,
							'mailinglists'		=>	$mailinglists,
							'registered'		=>	"N",
							'active'			=>	"Y",
						);
						
						$this -> save($subscriberdata, false);
						$subscriber_id = $this -> insertid;
					}
				}
			} else {
				if (!$subscriber_id = $this -> email_exists($adminemail)) {
					$subscriberdata = array(
						'email'					=>	$adminemail,
						'mailinglists'			=>	$mailinglists,
						'registered'			=>	"N",
						'active'				=>	"Y",
					);
					
					$this -> save($subscriberdata, false);
					$subscriber_id = $this -> insertid;
				}
			}
			
			return $subscriber_id;
		}
		
		function mailinglists($subscriber_id = null, $includeonly = null, $exclude = null, $active = "Y")
        {
			global $wpdb, $SubscribersList;
			$mailinglists = false;
		
			if (!empty($subscriber_id)) {
				$query = "SELECT `list_id` FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `subscriber_id` = '" . esc_sql($subscriber_id) . "'";
                if (!empty($active)) {
                    $query .= " AND `active` = '" . esc_sql($active) . "'";
                }
				
				$query_hash = md5($query);
				if ($ob_mailinglists = $this -> get_cache($query_hash)) {
					return $ob_mailinglists;
				}
				
				$listsarray = $wpdb -> get_results($query);
				$mailinglists = array();
				
				if (!empty($listsarray)) {			
					foreach ($listsarray as $larr) {						
						if (empty($includeonly) || (!empty($includeonly) && $includeonly[0] == "all") || (!empty($includeonly) && in_array($larr -> list_id, $includeonly))) {
							if (empty($mailinglists) || (!empty($mailinglists) && !in_array($larr -> list_id, $mailinglists))) {
								if (empty($exclude) || (!empty($exclude) && !in_array($larr -> list_id, $exclude))) {
									$mailinglists[] = $larr -> list_id;
								}
							}
						}
					}
				}
			}
			
			$this -> set_cache($query_hash, $mailinglists);
			return $mailinglists;
		}
		
		function inc_sent($subscriber_id = null)
        {
			global $wpdb;
			
			if (!empty($subscriber_id)) {
				$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `emailssent` = `emailssent` + 1 WHERE `id` = '" . esc_sql($subscriber_id) . "'";
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * Counts all subscriber records.
		 * Can take conditions to apply to the query.
		 * @param ARRAY An array of possible field => value conditions
		 * @return INT The number of subscribers for the given conditions
		 *
		 **/
		function count($condition = array())
        {
			global $wpdb;
			
			$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $this -> table . "`";
			
			if (!empty($condition)) {
				$query .= " WHERE";
				foreach ($condition as $key => $val) {
					$query .= " `" . $key . "` = '" . esc_sql($val) . "'";
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
	
		/**
		 * Counts the subscribers of a specific mailinglist
		 * @param INT The ID of the list to use as a condition
		 * @return INT The number of subscribers in the specified mailing list.
		 *
		 **/
		function count_by_list($list = null)
        {
			global $wpdb;
		
			if (!empty($list)) {
				$where = ($list == "all") ? '' : " WHERE `list_id` = '" . esc_sql($list) . "'";
				
				$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table . "`" . $where . "";
				
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
			}
			
			return 0;
		}
		
		/**
		 * Counts subscribers for a specific day
		 * @param STR The date to use for counting subscribers
		 * @return INT The number of subscribers for the given day
		 *
		 **/
		function count_by_date($date = null)
        {
			global $wpdb;
			
			if (!empty($date)) {
				$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE DATE_FORMAT(`created`, '%Y-%m-%d') = '" . $date . "'";
				
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
			}
			
			return 0;
		}
		
		function check_registration($email = null)
        {
			global $wpdb;
		
			if (!empty($email)) {			
				if ($user_id = email_exists($email)) {
					return $user_id;
				}
			}
			
			return false;
		}
		
		function get($subscriber_id = null, $assign = true)
        {
			global $wpdb, $SubscribersList;
			
			if (!empty($subscriber_id)) {
				$subscriber_id = esc_sql($subscriber_id);
				$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . esc_sql($subscriber_id) . "' LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_subscriber = $this -> get_cache($query_hash)) {
					return $ob_subscriber;
				}
			
				if ($subscriber = $wpdb -> get_row($query)) {			
					$subscriber = $this -> init_class($this -> model, $subscriber);				
					$subscriber -> mailinglists = $this -> mailinglists($subscriber_id);
					
					if ($assign === true) {
						if ($subscriber -> registered == "Y") {
							$user = get_userdata($subscriber -> user_id);						
							$subscriber -> username = $user -> user_login;
						}
						
						$subscriber -> recursive = true;
						$this -> data = (!empty($this -> data)) ? (array) $this -> data : array();
						$newdata = $this -> init_class($this -> model, $subscriber);
						$this -> data = $newdata;
					}
	
					$this -> set_cache($query_hash, $subscriber);
					return $subscriber;
				}
			}
			
			return false;
		}

        function get_segmented_query($fields = null, $scopeall = null, $condquery = null)
        {
			global $Db, $Field, $wpdb, $Subscriber;
			
			$supportedfields = array('text', 'textarea', 'hidden', 'radio', 'checkbox', 'select', 'pre_country', 'pre_gender');
            $fieldsquery = $scope = '';
			if (!empty($fields)) {				
				$f = 1;
				$fieldsquery = " AND";
				
				foreach ($fields as $field_slug => $field_value) {
				    $field_slug = sanitize_text_field($field_slug);
				    $field_value = sanitize_text_field($field_value);

					$Db -> model = $Field -> model;
					$customfield = $Db -> find(array('slug' => $field_slug), array('id', 'slug', 'type'));
					
					if (!empty($field_value) && in_array($customfield -> type, $supportedfields)) {	
						$fieldsquery .= " (";
						
						switch ($customfield -> type) {
							case 'checkbox'						:
								$i = 1;
								foreach ($field_value as $option_value) {
									$condition = $condquery[$field_slug];
									switch ($condition) {
										case 'contains'				:
											$fieldsquery .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $customfield -> id . "' AND `option_id` = '" . $option_value . "')";
											break;
										case 'equals'				:
											$fieldsquery .= " wp_wpmlsubscribers.id IN (SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> SubscribersOption() -> table . " WHERE `field_id` = '" . $customfield -> id . "' AND `option_id` = '" . $option_value . "')";
											break;
									}
	
									if ($i < count($field_value)) {
										switch ($condition) {
											case 'contains'			:
												$fieldsquery .= " OR";
												break;
											case 'equals'			:
												$fieldsquery .= " AND";
												break;
										}
									}
	
									$i++;
								}
								break;
							default 							:
								$condition = $condquery[$field_slug];
								switch ($condition) {
									case 'smaller'			:
										$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " < " . $field_value . "";
										break;
									case 'larger'			:
										$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " > " . $field_value . "";
										break;
									case 'contains'			:
										$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " LIKE '%" . $field_value . "%'";
										break;
									case 'equals'			:
									default  				:
										$fieldsquery .= " " . $wpdb -> prefix . $Subscriber -> table . "." . $customfield -> slug . " = '" . $field_value . "'";
										break;
								}
								break;
						}
						
						$fieldsquery .= ")";
						
						if ($f < count($fields)) {
							$fieldsquery .= ($scopeall) ? " AND" : " OR";
						}
					}
	
					$f++;
				}
			}
			
			return apply_filters('newsletters_get_subscribers_segmented_query', $fieldsquery, $fields, $scope, $condquery);
		}
		
		function get_by_list($list = null)
        {
			global $wpdb;
			
			if (!empty($list)) {
				$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . esc_sql($list) . "'";
				
				$query_hash = md5($query);
				if ($ob_subscribers = $this -> get_cache($query_hash)) {
					return $ob_subscribers;
				}
			
				if ($subscribers = $wpdb -> get_results($query)) {
					if (!empty($subscribers)) {
						$data = array();
					
						foreach ($subscribers as $subscriber) {
							$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
						}
						
						$this -> set_cache($query_hash, $data);
						return $data;
					}
				}
			}
			
			return false;
		}
		
		function select()
        {
			global $wpdb, $Subscriber;
			$select = array();
			
			if ($subscribers = $Subscriber -> get_all()) {
				if (!empty($subscribers)) {
					foreach ($subscribers as $subscriber) {
						$select[$subscriber -> id] = $subscriber -> id . ' - ' . $subscriber -> email;
					}
					
					return $select;
				}
			}
			
			return false;
		}
		
		function get_all()
        {
			global $wpdb;
			
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` ORDER BY `email` ASC";
			
			$query_hash = md5($query);
			if ($ob_subscribers = $this -> get_cache($query_hash)) {
				return $ob_subscribers;
			}
			
			if ($subscribers = $wpdb -> get_results($query)) {
				if (!empty($subscribers)) {
					$data = array();
					
					foreach ($subscribers as $subscriber) {
						$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
					}
					
					$this -> set_cache($query_hash, $data);
					return $data;
				}
			}
			
			return false;
		}
		
		function get_send_subscribers($group = 'all', $lists = null)
        {
			global $wpdb;
			
			$query = "SELECT * FROM `" . $wpdb -> prefix . $this -> table . "` WHERE";
					
			if (!empty($lists)) {
				if (is_array($lists)) {
					$this -> Mailinglist = $this -> init_class('wpmlMailinglist');
					$m = 1;
				
					foreach ($lists as $list_id) {
						$mailinglist = $this -> Mailinglist -> get($list_id);
						$activepaid = ($mailinglist -> paid == "Y") ? "`paid` = 'Y'" : "`active` = 'Y'";
						$query .= " (`list_id` = '" . $list_id . "' AND " . $activepaid . ")";
						
						if ($m < count($lists)) {
							$query .= " OR";
						}
					}
					
					$m++;
				}
			}
			
			if ($subscribers = $wpdb ->	get_results($query)) {		
				if (!empty($subscribers)) {
					$data = array();
	
					if (!empty($subscribers)) {			
						foreach ($subscribers as $subscriber) {
							$data[] = $this -> init_class('wpmlSubscriber', $subscriber);
						}
						
						return $data;
					}
				}
			}
			
			return false;
		}
		
		function email_exists($email = null, $list_id = null)
        {
			global $wpdb;
		
			if (!empty($email)) {
				$query = "SELECT `id` FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . esc_sql($email) . "'";
				
				if (!empty($list_id)) {
					$query .= " AND `list_id` = '" . esc_sql($list_id) . "'";
				}
			
				if ($subscriber = $wpdb -> get_row($query)) {			
					return $subscriber -> id;
				}
			}
			
			return false;
		}
		
		function email_validate($email = null)
        {
			$valid = false;
			
			$email = strtolower(trim($email));
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$valid = true;
				
				// Should extended validation be done?
				$emailvalidationextended = $this -> get_option('emailvalidationextended');
				if (!empty($emailvalidationextended)) {
					/* require($this -> plugin_base() . DS . 'vendors' . DS . 'class.verifyemail.php');
					$mail = new VerifyEmail();
					$mail -> setStreamTimeoutWait(1);
					$mail -> Debug = false; 
					$mail -> Debugoutput = 'html'; 
					$mail -> setEmailFrom(esc_html($this -> get_option('smtpfrom')));
					
					// Check if email is valid and exist
					if ($mail -> check($email)) { 
					    $valid = true;
					} elseif (verifyEmail::validate($email)) { 
					    $valid = false; 
					} else{ 
					    $valid = false;
					} */

                    require_once($this -> plugin_base() . DS . 'vendors' . DS . 'EmailVerify.class.php');
					$verify = new EmailVerify();

					if ( FALSE === $verify->verify_formatting($email) ) {
						$valid = false;
					} 
					
					if ( FALSE === $verify->verify_domain($email) ) {
						$valid = false;
					}
				}
				
				// Should API email validation be used?
				$mailapi = $this -> get_option('mailapi');
				switch ($mailapi) {
					case 'mailgun'						:					
						$mailapi_mailgun_emailvalidation = $this -> get_option('mailapi_mailgun_emailvalidation');
						if (!empty($mailapi_mailgun_emailvalidation)) {
							$mailgun_apikey = $this -> get_option('mailapi_mailgun_apikey');
							$mailgun_pubapikey = $this -> get_option('mailapi_mailgun_pubapikey');
							$mailgun_domain = $this -> get_option('mailapi_mailgun_domain');
							$mailgun_region = $this -> get_option('mailapi_mailgun_region');
							$region = (empty($mailgun_region) || $mailgun_region == "US") ? 'https://api.mailgun.net' : 'https://api.eu.mailgun.net';
							
							require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
							//$mailgun = new Mailgun\Mailgun($mailgun_pubapikey);
							$mg = Mailgun\Mailgun::create($mailgun_pubapikey, $region);
							$result = $mg -> emailValidation() -> validate($email);
							//get('address/validate', array('address' => $email));
							$isValid = $result -> isValid();
							
							if (!empty($isValid)) {
								$valid = true;
							} else {
								$valid = false;
							}
						}
						break;
				}
			}
			
			return apply_filters('newsletters_email_validation', $valid, $email);
		}
		
		function search($data = array())
        {
			global $wpdb;
			
			if (!empty($data)) {
                if (empty($data['searchterm'])) {
                    $this -> errors['searchterm'] = __('Please fill in a searchterm', 'wp-mailinglist');
                }
                if (empty($data['searchtype'])) {
                    $this -> errors['searchtype'] = __('Please select a search type', 'wp-mailinglist');
                }
				
				if (empty($this -> errors)) {
					if ($data['searchtype'] == "listtitle") {
						$listsquery = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> Mailinglist -> table_name . "` WHERE `title` LIKE '%" . strtolower($data['searchterm']) . "%'";
						$lists = $wpdb -> query($listsquery);
						
						if (!empty($lists)) {
							$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . esc_sql($lists[0] -> id) . "'";
							
							for ($l = 1; $l < count($lists); $l++) {
								$query .= " OR `list_id` = '" . $lists[$l] -> id . "'";
							}
						} else {
							$this -> errors['mailinglists'] = __('No mailing lists matched your title', 'wp-mailinglist');
							return false;
						}
					} else {
						$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `" . $data['searchtype'] . "` LIKE '%" . esc_sql($data['searchterm']) . "%'";
						$subscribers = $wpdb -> get_results($query);
						
						if (!empty($subscribers)) {
							$data = array();
						
							foreach ($subscribers as $subscriber) {
								$data[] = $this -> init_class($this -> plugin_name, $subscriber);
							}
							
							return $data;
						}
					}
				}
			}
			
			return false;
		}
		
		function optin($data = array(), $validate = true, $checkexists = true, $confirm = true, $skipsubscriberupdate = false, $wperror = false)
        {
			//global Wordpress variables
			
			$data = (array) $data;
			
			global $wpdb, $Db, $Field, $Authnews, $Html, $SubscribersList, $Mailinglist;
			$this -> errors = array();
            $number = (!empty($_REQUEST['uninumber'])) ? esc_html($_REQUEST['uninumber']) : false;
			$emailfield = $Field -> email_field();
			$postedlists = (empty($data['mailinglists'])) ? false : $data['mailinglists'];
		
			//ensure that the data is not empty
             if (!empty($data) ) {
                $data['list_id'] = array_filter( (!empty($data['list_id']) ? $data['list_id'] : array()));		
				$options = $this -> get_option('widget');
				
				if (!empty($data['list_id']) && is_array($data['list_id'])) {
					foreach ($data['list_id'] as $list_id) {
						if (empty($data['mailinglists']) || (!empty($data['mailinglists']) && !in_array($list_id, $data['mailinglists']))) {
							$data['mailinglists'][] = $list_id;
						}
					}
				}
				
				// The email address should always be validated, we don't want broken addresses
                if (empty($data['email'])) {
                    $this -> errors['email'] = __($emailfield -> errormessage);
                } elseif (!$this -> email_validate($data['email'])) {
                    $this -> errors['email'] = __($emailfield -> errormessage);
                }
				
				// Should everything be validated?
				if ($validate == true) {				
					$data = $Field -> validate_optin($data);										
					if (!empty($Field -> errors)) {
						$this -> errors = array_merge($this -> errors, $Field -> errors);
					}
	
					if (!empty($data['captcha_prefix']) || isset($data['g-recaptcha-response'])) {
						$cap = 'Y';
					} else {
						$cap = 'N';
					}
					
					if ($captcha_type = $this -> use_captcha($cap)) {						
						if ($captcha_type == "rsc") {						
							$captcha = new ReallySimpleCaptcha();										
                            if (empty($data['captcha_code'])) {
                                $this -> errors['captcha_code'] = __('Please fill in the code in the image.', 'wp-mailinglist');
                            } elseif (!$captcha -> check($data['captcha_prefix'], $data['captcha_code'])) {
                                $this -> errors['captcha_code'] = __('Your code does not match the code in the image.', 'wp-mailinglist');
                            }
							$captcha -> remove($data['captcha_prefix']);
						} elseif ($captcha_type == "recaptcha") {											
							$secret = $this -> get_option('recaptcha_privatekey');
							require_once($this -> plugin_base() . DS . 'vendors' . DS . 'recaptcha' . DS . 'ReCaptcha.php');
							
							if ($ReCaptcha = new ReCaptcha($secret)) {
								if (!$ReCaptcha -> verify($data['g-recaptcha-response'], $this -> get_ip_address())) {
									$this -> errors['captcha_code'] = $ReCaptcha -> errors[0];
								}
							}
						}
					}
					
					//Honeypot spam prevention
					if (!empty($data['newslettername'])) {
						$this -> errors['newslettername'] = __('Validation error occurred, this looks like spam', 'wp-mailinglist');
					}
				}
				
				if (empty($this -> errors)) {									
					if ($data['id'] = $this -> email_exists($data['email'])) {												
						$lists = $this -> mailinglists($data['id'], $data['mailinglists'], false, false);					
						if (!empty($checkexists) && $checkexists == true && !empty($lists)) {
							if ($this -> get_option('subscriberexistsredirect') == "management") {
								//$redirecturl = $Html -> retainquery('email=' . $data['email'], $this -> get_managementpost(true));
								$redirecturl = $this -> get_managementpost(true);
							} elseif ($this -> get_option('subscriberexistsredirect') == "custom") {
								//$redirecturl = $Html -> retainquery('email=' . $data['email'], $this -> get_option('subscriberexistsredirecturl'));	
								$redirecturl = $this -> get_option('subscriberexistsredirecturl');
							} else {
								//do nothing...	
								$redirecturl = false;
							}
							
							if (!empty($redirecturl)) {
                                $this -> render('error', array('errors' => array('email' => __($this -> get_option('subscriberexistsmessage')))), true, 'default');
								$this -> redirect($redirecturl, false, false, true);
                                exit();
                                die();
							}
						}
					}
					
					// All lists?
					if ($data['mailinglists'] == "all" || $data['mailinglists'][0] == "all") {
						$data['mailinglists'] = array();
					
						$Db -> model = $Mailinglist -> model;
						if ($lists = $Db -> find_all()) {
							foreach ($lists as $list) {
								$data['mailinglists'][] = $list -> id;
							}
						}
						
						$data['list_id'] = $data['mailinglists'];
					}
					
					// is an "active" parameter already passed through?
					if (empty($data['active'])) {
						$data['active'] = ($this -> get_option('requireactivate') == "Y") ? 'N' : 'Y';
					}
					
					if ($userid = $this -> check_registration($data['email'])) {
						$data['registered'] = "Y";
						$data['user_id'] = $userid;
					} else {
						$data['registered'] = "N";
						$data['user_id'] = 0;
					}
					
					// Go head, try tosave the subscriber
					if ($this -> save($data, false, false, $skipsubscriberupdate)) {																	
						$subscriber = $this -> get($this -> insertid, false);
						$subscriberauth = $Authnews -> gen_subscriberauth();
						$subscriberauth = $this -> gen_auth($subscriber -> id);
						
						if (!is_admin()) {
							$Authnews -> set_emailcookie($subscriber -> email);
						}
						
						/* Management Auth */
						if (empty($data['cookieauth'])) {							
							$Db -> model = $this -> model;
							$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
						}
						
						$subscriber -> mailinglists = $data['mailinglists'];
						
						$this -> delete_all_cache();
						
                        if ($confirm) {
                            $this -> subscription_confirm($subscriber);
                        }
						
						$subscriber -> mailinglists = (empty($data['list_id'])) ? $data['mailinglists'] : $data['list_id'];
						$this -> admin_subscription_notification($subscriber);					
						
						return $subscriber -> id;
					}
				} else {
					$_POST[$this -> pre . 'errors'] = $this -> errors;
				}
			} else {
				$this -> errors['data'] = __('No data was posted', 'wp-mailinglist');
			}
			
			$this -> data = $data;
			
			if (!empty($wperror)) {				
				if (!empty($this -> errors)) {
					$errors = new WP_Error();
					
					foreach ($this -> errors as $ekey => $error) {
						$errors -> add($ekey, $error);
					}
					
					return $errors;
				}
			}
			
			return false;
		}
		
		function save($data = array(), $validate = true, $return_query = false, $skipsubscriberupdate = false, $emptyfields = false)
        {
			global $wpdb, $Html, $Db, $FieldsList, $Mailinglist, $SubscribersList, 
			$Bounce, $Unsubscribe, $Field;
			
			$this -> errors = false;
			
			$owner_id = 0;
			$owner_role = 0;
			include_once(ABSPATH . 'wp-includes/pluggable.php');
			if ($current_user = wp_get_current_user()) {
				$owner_id = $current_user -> ID;
				$owner_role = $current_user -> roles[0];
			}
			
			$saveipaddress = $this -> get_option('saveipaddress');
			$ipaddress = (is_admin() && !defined('DOING_AJAX')) ? false : $this -> get_ip_address();
			
			$defaults = array(
				'ip_address'		=>	((!empty($saveipaddress)) ? $ipaddress : false),
				'country'			=>	((!empty($saveipaddress)) ? $this -> get_country_by_ip($ipaddress) : false),
				'referer'			=>	wp_get_referer(),
				'cookieauth'		=>	"",
				'emailssent'		=>	0,
				'format'			=>	"html",
				'authkey'			=>	"",
				'authinprog'		=>	"N",
				'registered' 		=> 	"N", 
				'username'			=>	"",
				'password' 			=> 	substr(md5(uniqid(microtime())), 0, 6), 
				'active' 			=>	"N",
	            'bouncecount'       =>  0,
				'user_id'			=>	0,
				'owner_id'			=>	$owner_id,
				'owner_role'		=>	$owner_role,
				'device'			=>	$this -> get_device(),
				'created' 			=> 	$Html -> gen_date(), 
                'modified' 			=> 	$Html -> gen_date(),
                'consent'           =>  "N",
                'list'              =>  ''
			);
			
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			//$this -> data = array($this -> model => (object) $r);
			$this -> data = (object) $r;
			extract($r, EXTR_SKIP);
			$emailfield = $Field -> email_field();
			
			if (!empty($id)) {
				if ($subscriber = $this -> get($id, false)) {								
					if ($subscriber -> registered == "Y") {
						if (!empty($subscriber -> user_id)) {
							$user = get_userdata($subscriber -> user_id);
						}
					}
				}
			}
			
			if ($validate == true) {			
				//was the email address left empty?
                if (empty($email)) {
                    $this -> errors['email'] = __($emailfield -> errormessage);
                }
				//does a subscriber with this email address already exist?
				elseif ($curr_id = $this -> email_exists($email)) { 					
					if (empty($subscriber) || (!empty($subscriber) && $email != $subscriber -> email)) {																						
						$id = $curr_id;
						$this -> id = $curr_id;
						$this -> data -> id = $curr_id;
						
						$cur_lists = $this -> mailinglists($curr_id);
						$sel_lists = $mailinglists;
						
						if (is_array($cur_lists) && is_array($sel_lists)) {
							$new_lists = array_merge($cur_lists, $sel_lists);
						} else {
							if (is_array($cur_lists)) {
								$new_lists = $cur_lists;
							} elseif (is_array($sel_lists)) {
								$new_lists = $sel_lists;
							}
						}
						
						if (empty($justsubscribe)) {
							$_POST['subscriber_id'] = $curr_id;
							$this -> errors['email'] = __('Email exists, therefore the appropriate lists have been checked below. Please submit again', 'wp-mailinglist');
							
							$this -> data = $this -> get($curr_id);
						}
						
						// Assign current and new lists
						$this -> data -> mailinglists = $new_lists;
					}					
                } elseif (!$this -> email_validate($email)) {
                    $this -> errors['email'] = __('Please fill in a valid email address', 'wp-mailinglist');
                }
				
				if (!is_admin() && empty($mailinglists)) { 
					if ($default_list = $Mailinglist -> get_default(true)) {
						$mailinglists = array($default_list);	
					} else {
						$this -> errors['mailinglists'] = __('Please select mailing list/s', 'wp-mailinglist'); 
					}
				}
				
                if (empty($registered)) {
                    $registered = "N"; /*$this -> errors['registered'] = __('Please select a registered status', 'wp-mailinglist');*/
                } elseif ($registered == "Y") {
					if (!$userid = $this -> check_registration($data['email'])) {
                        if (empty($username)) {
                            $this -> errors['username'] = __('Please fill in a username', 'wp-mailinglist');
                        } else {
							if (empty($fromregistration)) {					
								if (!empty($user)) {					
									if (username_exists($username) && $username !== $user -> user_login) {
										$this -> errors['username'] = __('Username is already in use', 'wp-mailinglist');
									} else {
										if ($username !== $user -> user_login) {
											if (!empty($email)) {										
												if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
													$wpuser = new WP_User($user_id);
													$wpuser -> set_role("subscriber");
													wp_new_user_notification($user_id, $password);
												}
											} else {
												$this -> errors['username'] = __('Email required for registration', 'wp-mailinglist');
											}
										} else {
											$user_id = $user -> ID;
										}
									}
								} else {											
									if (username_exists($username)) {
										$this -> errors['username'] = __('Username is already in use', 'wp-mailinglist');
									} else {													
										if (!empty($username) && !empty($email)) {									
											if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
												$wpuser = new WP_User($user_id);
												$wpuser -> set_role("subscriber");
												wp_new_user_notification($user_id, $password);
											}
										} else {
											$this -> errors['username'] = __('Username and email address required for registration', 'wp-mailinglist');
										}
									}
								}
							}
						}
					} else {
						$userdata = $this -> userdata($userid);
						$data['username'] = $username = $userdata -> data -> user_login;
						$data['registered'] = $registered = "Y";
					}
				}
				
                if (empty($active)) {
                    $this -> errors['active'] = __('Please select an active status', 'wp-mailinglist');
                }
			} else {
                if (empty($email)) {
                    $this -> errors['email'] = __($emailfield -> errormessage);
                }
			}
			
			$this -> errors = apply_filters('newsletters_subscriber_validation', $this -> errors, $this -> data);
			$this -> errors = apply_filters($this -> pre . '_subscriber_validation', $this -> errors, $this -> data);
			
			if (empty($this -> errors)) {
				$email = $data['email'] = trim(strtolower($data['email']));
				
				if ($userid = $this -> check_registration($data['email'])) {
					$data['registered'] = $registered = "Y";
					$data['user_id'] = $user_id = $userid;
				} else {
					$data['registered'] = $registered = "N";
				}

				if (!empty($saveipaddress)) {				
					if (!empty($data['ip_address'])) {
						$data['country'] = $this -> get_country_by_ip($data['ip_address']);
					}
				}
				
				$fieldsconditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
				$Db -> model = $Field -> model;
				$fields = $Db -> find_all($fieldsconditions);
					
				if (!empty($id)) {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET";
					unset($this -> table_fields['key']);
					unset($this -> table_fields['created']);
					$c = 1;
							
					/* Custom Fields */	
					$usedfields = array();
					
					if (!empty($fields)) {				
						foreach ($fields as $field) {
							if ((empty($usedfields)) || (!empty($usedfields) && !in_array($field -> slug, $usedfields))) {						
                                if ((isset($data[$field -> slug])) && (!empty($data[$field -> slug]) || $data[$field -> slug] == "0" || $field -> type == "file")) {
									if ($field -> type == "file") {	
                                        $_FILES[$field -> slug] = map_deep($_FILES[$field -> slug], 'sanitize_text_field');
										if (!empty($_FILES[$field -> slug]['name'])) {							
                                            if (!function_exists('wp_handle_upload')) {
										        require_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'file.php');
										    }
										    
										    $upload_overrides = array('test_form' => false);									
                                            $uploadedfile = map_deep($_FILES[$field -> slug], 'sanitize_text_field');
																			
											$file_info = wp_handle_upload($uploadedfile, $upload_overrides);
											
											if ($file_info && empty($file_info['error'])) {
												$data[$field -> slug] = $file_info['url'];
											} else {
												$this -> errors[$field -> slug] = $file_info['error'];
											}
										} elseif (!empty($_POST['oldfiles'][$field -> slug])) {
											$data[$field -> slug] = sanitize_text_field(wp_unslash($_POST['oldfiles'][$field -> slug]));
										}
                                    }
                                    if (!empty($field -> type) && ($field -> type == "radio" || $field -> type == "select")) {
										$fieldoptions = $field -> newfieldoptions;
										$fieldoptions = array_map('__', $fieldoptions);
										$fieldoptions_lower = array_map('strtolower', array_change_key_case($fieldoptions, CASE_LOWER));
										
										if (defined('NEWSLETTERS_IMPORTING')) {
											if (array_key_exists(strtolower($data[$field -> slug]), $fieldoptions_lower)) {
												//do nothing, it is okay?
											} elseif ($key = array_search(strtolower($data[$field -> slug]), $fieldoptions_lower)) {
												$data[$field -> slug] = $key;
											} else {
												// Should the field option be created?
												$import_createfieldoptions = $this -> get_option('import_createfieldoptions');
												if (!empty($import_createfieldoptions)) {
													$optiondata = array(
														'value'				=>	$data[$field -> slug],
														'field_id'			=>	$field -> id,
													);
													
													$this -> Option() -> save($optiondata);
													$option_id = $this -> Option() -> insertid;
													$fieldoptions[$option_id] = $aval;
													$data[$field -> slug] = $option_id;
												}
											}
										} else {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}
									} elseif ($field -> type == "checkbox") {
										$fieldoptions = $field -> newfieldoptions;
										$fieldoptions = array_map('__', $fieldoptions);
										$fieldoptions_lower = array_map('strtolower', array_change_key_case($fieldoptions, CASE_LOWER));
										
										if (defined('NEWSLETTERS_IMPORTING')) {			
											$data[$field -> slug] = maybe_unserialize($data[$field -> slug]);																		
											if (!empty($data[$field -> slug]) || $data[$field -> slug] == "0") {
												$array = $data[$field -> slug];
												if (!is_array($data[$field -> slug]) && strpos($data[$field -> slug], ",") !== false) {
													$array = explode(",", $data[$field -> slug]);
												} elseif (!is_array($data[$field -> slug])) {
													$array = array($data[$field -> slug]);
												}
												
												$newarray = array();
												foreach ($array as $akey => $aval) {										
													if (!empty($aval) || $aval == "0") {																																
														if (array_key_exists(strtolower($aval), $fieldoptions_lower)) {												
															$newarray[] = $aval;
														} elseif ($key = array_search(trim(str_replace("\n", "", strtolower($aval))), $fieldoptions_lower)) {												
															$newarray[] = $key;
														} else {
															$import_createfieldoptions = $this -> get_option('import_createfieldoptions');
															if (!empty($import_createfieldoptions)) {																
																$optiondata = array(
																	'value'				=>	$aval,
																	'field_id'			=>	$field -> id,
																);
																
																$this -> Option() -> save($optiondata);
																$option_id = $this -> Option() -> insertid;
																$fieldoptions[$option_id] = $aval;
																$newarray[] = $option_id;
															}
														}
													}
												}
												
												$data[$field -> slug] = maybe_serialize($newarray);
											}
										} else {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}
									} elseif ($field -> type == "pre_gender") {
										if (!empty($data[$field -> slug])) {
											$data[$field -> slug] = strtolower($data[$field -> slug]);
										}
									} elseif ($field -> type == "pre_date") {
										if (!empty($data[$field -> slug])) {
											$data[$field -> slug] = date_i18n("Y-m-d", strtotime($data[$field -> slug]));
										}
									} elseif ($field -> type == "pre_country") {
										if (!is_numeric($data[$field -> slug])) {
											$countryquery = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> Country() -> table . "` WHERE `value` = '" . esc_sql($data[$field -> slug]) . "'";
											if ($country_id = $wpdb -> get_var($countryquery)) {										
												$data[$field -> slug] = $country_id;
											}
										}
									} else {																		
										if (is_array($data[$field -> slug])) {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}
									}
								
									$query .= " `" . $field -> slug . "` = '" . (esc_sql($data[$field -> slug])) . "', ";
								} else {
									if (!empty($emptyfields)) {
										$query .= " `" . $field -> slug . "` = '', ";
									}
								}
							
								$usedfields[] = $field -> slug;
							}
						}
					}
					
					foreach (array_keys($this -> table_fields) as $field) {
						if (!empty(${$field}) || ${$field} == "0") {
							$query .= " `" . $field . "` = '" . esc_sql(${$field}) . "'";
							
							if ($c < count($this -> table_fields)) {
								$query .= ", ";
							}
						}
						
						$c++;
					}
					
					$query .= " WHERE `id` = '" . $id . "';";
					$this -> table_fields['created'] = true;
				} else { 
					$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table . "` (";
					$query2 = "";
					
					unset($this -> table_fields['id']);
					unset($this -> table_fields['key']);
					$c = 1;
					
					/* Custom Fields */
					$usedfields = array();				
					
					if (!empty($fields)) {
						foreach ($fields as $field) {
							if (empty($usedfields) || (!empty($usedfields) && !in_array($field -> slug, $usedfields))) {
                                if (isset($data[$field -> slug]) && (!empty($data[$field -> slug]) || $data[$field -> slug] == "0" || $field -> type == "file")) {
									if ($field -> type == "file") {	
                                        $_FILES[$field -> slug] = map_deep($_FILES[$field -> slug], 'sanitize_text_field');
										if (!empty($_FILES[$field -> slug]['name'])) {							
                                            if (!function_exists('wp_handle_upload')) {
										        require_once(ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'file.php');
										    }
										    
										    $upload_overrides = array('test_form' => false);									
                                            $uploadedfile = map_deep($_FILES[$field -> slug], 'sanitize_text_field');
											$file_info = wp_handle_upload($uploadedfile, $upload_overrides);
											
											if ($file_info && empty($file_info['error'])) {
												$data[$field -> slug] = $file_info['url'];
											} else {
												$this -> errors[$field -> slug] = $file_info['error'];
											}
										} elseif (!empty($_POST['oldfiles'][$field -> slug])) {
											$data[$field -> slug] = sanitize_text_field(wp_unslash($_POST['oldfiles'][$field -> slug]));
										}
                                    }
                                    if (!empty($field -> type) && ($field -> type == "radio" || $field -> type == "select")) {
										$fieldoptions = $field -> newfieldoptions;
										$fieldoptions = array_map('__', $fieldoptions);
										$fieldoptions_lower = array_map('strtolower', array_change_key_case($fieldoptions, CASE_LOWER));
										
										if (defined('NEWSLETTERS_IMPORTING')) {
											if (array_key_exists(strtolower($data[$field -> slug]), $fieldoptions_lower)) {
												//do nothing, it is okay?
											} elseif ($key = array_search(strtolower($data[$field -> slug]), $fieldoptions_lower)) {
												$data[$field -> slug] = $key;
											} else {
												// Should the field option be created?
												$import_createfieldoptions = $this -> get_option('import_createfieldoptions');
												if (!empty($import_createfieldoptions)) {
													$optiondata = array(
														'value'				=>	$data[$field -> slug],
														'field_id'			=>	$field -> id,
													);
													
													$this -> Option() -> save($optiondata);
													$option_id = $this -> Option() -> insertid;
													$fieldoptions[$option_id] = $aval;
													$data[$field -> slug] = $option_id;
												}
											}
										} else {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}
									} elseif ($field -> type == "checkbox") {
										$fieldoptions = $field -> newfieldoptions;
										$fieldoptions = array_map('__', $fieldoptions);
										$fieldoptions_lower = array_map('strtolower', array_change_key_case($fieldoptions, CASE_LOWER));
										
										if (defined('NEWSLETTERS_IMPORTING')) {			
											$data[$field -> slug] = maybe_unserialize($data[$field -> slug]);																		
											if (!empty($data[$field -> slug]) || $data[$field -> slug] == "0") {
												$array = $data[$field -> slug];
												if (!is_array($data[$field -> slug]) && strpos($data[$field -> slug], ",") !== false) {
													$array = explode(",", $data[$field -> slug]);
												} elseif (!is_array($data[$field -> slug])) {
													$array = array($data[$field -> slug]);
												}
												
												$newarray = array();
												foreach ($array as $akey => $aval) {										
													if (!empty($aval) || $aval == "0") {																																
														if (array_key_exists(strtolower($aval), $fieldoptions_lower)) {												
															$newarray[] = $aval;
														} elseif ($key = array_search(trim(str_replace("\n", "", strtolower($aval))), $fieldoptions_lower)) {												
															$newarray[] = $key;
														} else {
															$import_createfieldoptions = $this -> get_option('import_createfieldoptions');
															if (!empty($import_createfieldoptions)) {																
																$optiondata = array(
																	'value'				=>	$aval,
																	'field_id'			=>	$field -> id,
																);
																
																$this -> Option() -> save($optiondata);
																$option_id = $this -> Option() -> insertid;
																$fieldoptions[$option_id] = $aval;
																$newarray[] = $option_id;
															}
														}
													}
												}
												
												$data[$field -> slug] = maybe_serialize($newarray);
											}
										} else {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}										
									} elseif ($field -> type == "pre_gender") {
										if (!empty($data[$field -> slug])) {
											$data[$field -> slug] = strtolower($data[$field -> slug]);
										}
									} elseif ($field -> type == "pre_date") {
										if (!empty($data[$field -> slug])) {
											$data[$field -> slug] = date_i18n("Y-m-d", strtotime($data[$field -> slug]));
										}
									} elseif ($field -> type == "pre_country") {
										if (!is_numeric($data[$field -> slug])) {
											$countryquery = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> Country() -> table . "` WHERE `value` = '" . esc_sql($data[$field -> slug]) . "'";
											if ($country_id = $wpdb -> get_var($countryquery)) {										
												$data[$field -> slug] = $country_id;
											}
										}
									} else {
										if (is_array($data[$field -> slug])) {
											$data[$field -> slug] = maybe_serialize($data[$field -> slug]);
										}
									}
									
									$query1 .= "`" . $field -> slug . "`, ";
									$query2 .= "'" . esc_sql($data[$field -> slug]) . "', ";
								} else {
									$query1 .= "`" . $field -> slug . "`, ";
									$query2 .= "'', ";
								}
							
								$usedfields[] = $field -> slug;
							}
						}
					}
					
					foreach (array_keys($this -> table_fields) as $field) {
						$value = (!empty(${$field}) || ${$field} == "0") ? esc_sql(${$field}) : '';
						
						$query1 .= "`" . $field . "`";
						$query2 .= "'" . $value . "'";
						
						if ($c < count($this -> table_fields)) {
							$query1 .= ", ";
							$query2 .= ", ";
						}
						
						$c++;
					}
					
					$query1 .= ") VALUES (";
					$query = $query1 . $query2 . ");";
				}
				
				if (empty($return_query) || $return_query == false) {			
					if (empty($skipsubscriberupdate) || (!empty($skipsubscriberupdate) && empty($id))) {
						$result = $wpdb -> query($query);
					} else {
						// Don't update the subscriber record itself
						return true;	
					}
													
					if ($result !== false && $result >= 0) {											
						$this -> insertid = $subscriber_id = (empty($id)) ? $wpdb -> insert_id : $id;
						$insertid = $this -> insertid;
						
						$unsubscribe_delete_query = "DELETE FROM " . $wpdb -> prefix . $Unsubscribe -> table . " WHERE `email` = '" . esc_sql($data['email']) . "'";
						$wpdb -> query($unsubscribe_delete_query);
						$bounce_delete_query = "DELETE FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE `email` = '" . esc_sql($data['email']) . "'";
						$wpdb -> query($bounce_delete_query);
						
						/* Mailing list associations */
						if (!empty($mailinglists)) {														
							// Save the subscriptions
							$oldactive = $active;														
							foreach ($mailinglists as $key => $list_id) {					
								$mailinglist = $Mailinglist -> get($list_id);
								$active = (!empty($mailinglist -> doubleopt) && $mailinglist -> doubleopt == "N") ? "Y" : $oldactive;							
								$paid = ($mailinglist -> paid == "Y") ? 'Y' : 'N';
								$paid_date = false;
								
								if (!empty($listexpirations[$list_id])) {									
									$paid_stamp = $Mailinglist -> paid_stamp($mailinglist -> interval, strtotime($listexpirations[$list_id]), true);
									$paid_date = $Html -> gen_date("Y-m-d", $paid_stamp);
								}
                                /* else {
									$paid_date = $Html->gen_date("Y-m-d");
                                } */
								
								//if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") { $active = "N"; }
                                $sl_data = array('SubscribersList' => array('subscriber_id' => $insertid, 'list_id' => $list_id, 'form_id' => isset($data['form_id']) ? $data['form_id'] : '', 'active' => $active, 'paid' => $paid, 'paid_date' => $paid_date));
								$sl_data = apply_filters('newsletters_subscriberslist_save_data', $sl_data);
								$SubscribersList -> save($sl_data);							
								$active = $oldactive;
								$SubscribersList -> errors = false;
							}
						}
						
						/* Subscriber Options */
						$fieldsconditions['1'] = "1 AND (`type` = 'radio' OR `type` = 'select' OR `type` = 'checkbox') AND `slug` != 'email' AND `slug` != 'list'";
						$Db -> model = $Field -> model;
						if ($fields = $Db -> find_all($fieldsconditions)) {
							foreach ($fields as $field) {					
								$this -> SubscribersOption() -> delete_all(array('subscriber_id' => $insertid, 'field_id' => $field -> id));
										
								if (!empty($data[$field -> slug])) {
									$subscriber_fieldoptions = maybe_unserialize($data[$field -> slug]);
																	
									if (!empty($subscriber_fieldoptions)) {																		
										if (is_array($subscriber_fieldoptions)) {															
											foreach ($subscriber_fieldoptions as $subscriber_fieldoption) {																
												$option_id = $subscriber_fieldoption;
												
												if (!empty($option_id)) {
													$subscribers_option_data = array(
														'subscriber_id'					=>	$insertid,
														'field_id'						=>	$field -> id,
														'option_id'						=>	$option_id,
													);
												
													$this -> SubscribersOption() -> save($subscribers_option_data);	
												}
											}
										} else {	
											$option_id = $subscriber_fieldoptions;
												
											if (!empty($option_id)) {													
												$subscribers_option_data = array(
													'subscriber_id'					=>	$insertid,
													'field_id'						=>	$field -> id,
													'option_id'						=>	$option_id,
												);
											
												$this -> SubscribersOption() -> save($subscribers_option_data);	
											}
										}
									}
								}
							}
						}
						
						/* Subscriber register? */
						if ($this -> get_option('subscriberegister') == "Y") {
							$username = $email;
							$password = wp_generate_password(12);
							
							if ($user_id = username_exists($username)) {
								//do nothing, we have the user ID
							} elseif ($user_id = email_exists($email)) {
								//do nothing, we have the user ID
							} else {
								if ($user_id = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email))) {
									$wpuser = new WP_User($user_id);
									$wpuser -> set_role("subscriber");
									wp_new_user_notification($user_id, $password);
								}
							}
							
							$subscriberquery = "UPDATE `" . $wpdb -> prefix . $this -> table . "` SET `registered` = 'Y', `user_id` = '" . esc_sql($user_id) . "' WHERE `id` = '" . esc_sql($subscriber_id) . "'";
							$wpdb -> query($subscriberquery);
						}
						
						if (empty($preventautoresponders)) {							
							// Send autoresponders linked to form
							if (!empty($data['form_id'])) {
								if ($form = $this -> Subscribeform() -> find(array('id' => $data['form_id']))) {
									$subscriber = $this -> get($subscriber_id, false);
									$this -> autoresponders_form_send($subscriber, $form);
								}
							}
										
							// Send autoresponders linked to mailing lists			
							if (!empty($mailinglists)) {															
								foreach ($mailinglists as $mkey => $mval) {								
									$subscriber = $this -> get($subscriber_id, false);
									$this -> gen_auth($subscriber -> id);
									$mailinglist = $Mailinglist -> get($mval, false);
									$this -> autoresponders_send($subscriber, $mailinglist);
								}
							}
						}
						
						do_action($this -> pre . '_subscriber_saved', $insertid, $data);
						do_action('newsletters_subscriber_saved', $insertid, $data);
					}
					
					return true;
				} else {
					return $query;	
				}
			}
			
			return false;
		}
		
		/**
		 * Saves the value of the single field in the "subscribers" table.
		 * @param STR The name of the field to save to.
		 * @param STR The value to write to the field mentioned above
		 * @param INT The ID of the record to update.
		 * @return BOOL Returns true if the procedure was successful
		 *
		 **/
		function save_field($field = null, $value = null, $id = null)
        {
			global $wpdb;
			
			$subscriber_id = (empty($id)) ? $this -> id : $id;
		
			if (!empty($field) && !empty($value)) {
				if ($wpdb -> query("UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `" . $field . "` = '" . esc_sql($value) . "' WHERE `id` = '" . esc_sql($subscriber_id) . "'")) {
					return true;
				}
			}
			
			return false;
		}
		
		function find($conditions = array(), $fields = false)
        {
			global $wpdb;
			
			if (!empty($fields)) {
				$f = 1;
				$newfields = "";
			
				foreach ($fields as $field) {
					$newfields .= " `" . $field . "`";
					
					if ($f < count($fields)) {
						$newfields .= ", ";
					}
					
					$f++;
				}
			} else {
				$newfields = "*";
			}
			
			$query = "SELECT " . $newfields . " FROM `" . $wpdb -> prefix . "" . $this -> table . "`";
			
			if (!empty($conditions)) {
				$c = 1;
				$query .= " WHERE";
				
				foreach ($conditions as $ckey => $cval) {
					$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
				
					if ($c < count($conditions)) {
						$query .= " AND";
					}
					
					$c++;
				}
			}
			
			if ($subscriber = $wpdb -> get_row($query)) {
				if (!empty($subscriber)) {
					$subscriber = $this -> init_class($this -> model, $subscriber);
					
					return $subscriber;
				}
			}
			
			return false;
		}
		
		function get_by_email($email = null)
        {
			global $wpdb;
			
			if (!empty($email)) {
			    $email = sanitize_email($email);
				if ($subscriber = $wpdb -> get_row("SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . esc_sql($email) . "'")) {
					if (!empty($subscriber)) {						
						$data = $this -> init_class($this -> model, $subscriber);
						return $data;
					}
				}
			}
			
			return false;
		}
		
		/**
		 * Fetches a Wordpress user by email address
		 * @param STRING. The email to execute the query with.
		 * @return BOOLEAN/OBJ
		 *
		 */
		function get_user_by_email($email = null)
        {
			if ($user_id = $this -> check_registration($email)) {
				if ($userdata = $this -> userdata($user_id)) {
					return $userdata;
				}
			}
			
			return false;
		}
	
		
		/**
		 * Activates a subscriber by ID and email address
		 * Simply updates the `active` field and sets it to "Y"
		 * @param INT. The ID of the subscriber
		 * @param STRING. The email address of the subscriber
		 * @return BOOLEAN
		 *
		 */
		function activate($id = null, $email = null)
        {
			global $wpdb;
			
			if (!empty($id) && !empty($email)) {
				if ($subscriber = $this -> get($id)) {
					if ($subscriber -> active == "N") {
						if ($wpdb -> query("UPDATE `" . $wpdb -> prefix . "" . $this -> table . "` SET `active` = 'Y' WHERE `id` = '" . $id . "' AND `email` = '" . $email . "' LIMIT 1")) {
							return true;
						}
					}
				}
			}
			
			return false;
		}
		
		/**
		 * Deletes a single subscriber record from the database
		 * @param INT. The ID of the subscriber
		 * @return BOOLEAN
		 *
		 */
		function delete($subscriber_id = null)
        {
			global $wpdb, $SubscribersList;
			
			if (!empty($subscriber_id)) {
				$wp_user_id = (int) $wpdb->get_var("SELECT user_id FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $subscriber_id . "'");
				if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $subscriber_id . "' LIMIT 1")) {
					$this -> Order() -> delete_all(array('subscriber_id' => $subscriber_id));
					$SubscribersList -> delete_all(array('subscriber_id' => $subscriber_id));
					$this -> SubscribersOption() -> delete_all(array('subscriber_id' => $subscriber_id));
					
					$wpdb -> query("DELETE FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " WHERE `subscriber_id` = '" . $subscriber_id . "'");

					if ($this->get_option('unsubscribewpuserdelete') == 'Y' && is_numeric($wp_user_id) && $wp_user_id > 0) {
						wp_delete_user($wp_user_id);
					}

					return true;
				}
			}
			
			return false;
		}
		
		function delete_by_list($list_id = null)
        {
			global $wpdb;
		
			if (!empty($list_id)) {
				$query = "DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `list_id` = '" . esc_sql($list_id) . "'";
				
				if ($wpdb -> query($query)) {
					return true;
				}
			}
			
			return false;
		}
		
		function delete_by_email($email = null)
        {
			global $wpdb;
			
			if (!empty($email)) {
				if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `email` = '" . esc_sql($email) . "'")) {
					return true;
				}
			}
			
			return false;
		}
		
		function unsubscribe($id = null, $email = null)
        {
			global $wpdb;
			
			if ($wpdb -> query("DELETE FROM `" . $wpdb -> prefix . "" . $this -> table . "` WHERE `id` = '" . $id . "' AND `email` = '" . esc_sql($email) . "' LIMIT 1")) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * Removes several subscriber records by an array of IDs
		 * @param ARRAY. An array of subscriber IDs
		 * @return BOOLEAN.
		 *
		 */
		function delete_array($subscribers = array())
        {
			global $wpdb;
			
			if (!empty($subscribers)) {
				foreach ($subscribers as $subscriber) {
					$this -> delete($subscriber);
				}
				
				return true;
			}
			
			return false;
		}
	}
}

include_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'newsletter.php');

?>
