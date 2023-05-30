<?php

if (!class_exists('wpmlDbHelper')) {
	class wpmlDbHelper extends wpMailPlugin {
	
		var $name = "Db";	
		var $model = '';
		var $errors = array();
		var $data;
		
		function __construct()
        {
			parent::__construct();			
		}
	
		function getobject($model = null)
        {
			if (!empty($model)) {							
				if (in_array($model, (array) $this -> models)) {					
					$object = $this -> {$model}();	
				} else {
					global ${$model};
					$object = (!is_object(${$model})) ? $this -> {$model}() : ${$model};
				}
				
				return $object;
			}
			
			return false;
		}
		
		function save($data = array(), $validate = true)
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
	
				if (!empty($data)) {
					$object -> errors = false;
					$data = (empty($data[$object -> model])) ? $data : $data[$object -> model];
					
					// Load default field values
					$defaults = array();
					if (empty($data['id'])) {
						if (method_exists($object, 'defaults')) {
							$defaults = $object -> defaults();
						}
					}
					
					$r = wp_parse_args($data, $defaults);
					$object -> data = (object) $r;
					
					if ($validate == true) {					
						if (method_exists($object, 'validate')) {						
							$object -> errors = $object -> validate($r);
						}
					}
					
					extract($r, EXTR_SKIP);
					
					switch ($object -> model) {
                        case 'Autoresponder':
                            $object -> data -> nnewsletter['content'] = wp_kses_post(wp_unslash($_POST['content']));
							
							break;
						case 'Theme'						:
							if (empty($object -> data -> themestylesheet)) {
								$object -> data -> themestylesheet = 0;
							}
							break;
					}
					
					$object -> errors = apply_filters('newsletters_' . strtolower(str_replace("wpml", "", $object -> model)) . '_before_save', $object -> errors, $object -> data, $object);
                   // error_log(json_encode(($object -> errors)));
					if (empty($object -> errors)) {						
						$query = (empty($id)) ? $this -> iquery($object -> model) : $this -> uquery($object -> model);												
						$result = $wpdb -> query($query);
						
						if ($result !== false && $result >= 0) {
							$object -> insertid = $insertid = (empty($id)) ? $wpdb -> insert_id : $id;

                            if (!isset($this -> {$object -> model})) {
                                $this -> {$object -> model}
                                = $object;
                            }

                            $this -> {$object -> model}
                            -> insertid = $insertid;
							global $Db, $Email;

                            if (isset($Db->{$object->model}) && !is_object($Db->{$object->model})) {
                                $Db->{$object->model} = $object;
                            }
							
                            if (isset($Db -> {$object -> model})) {
                                $Db -> {$object -> model}
                                -> insertid = $insertid;
                            }

							$oldmodel = $object -> model;
							
							switch ($object -> model) {
								case 'Click'					:
									
									if (!empty($object -> data -> history_id) && (!empty($object -> data -> subscriber_id) || !empty($object -> data -> user_id))) {
										$email_conditions = array();
										$email_conditions['history_id'] = $object -> data -> history_id;
                                        if (!empty($object -> data -> subscriber_id)) {
                                            $email_conditions['subscriber_id'] = $object -> data -> subscriber_id;
                                        }
                                        if (!empty($object -> data -> user_id)) {
                                            $email_conditions['user_id'] = $object -> data -> user_id;
                                        }
										
										$Db -> model = $Email -> model;
										$Db -> save_field('read', "Y", $email_conditions);
										$Db -> model = $Email -> model;
										$Db -> save_field('status', "sent", $email_conditions);
									}
									
									break;
								case 'Subscribeform'			:																
									if (!empty($object -> data -> form_fields)) {
										$order = 1;
										
										foreach ($object -> data -> form_fields as $field_id => $form_field) {																						
											$fieldform_data = array(
												'id'						=>	$form_field['id'],
												'form_id'					=>	$insertid,
												'field_id'					=>	$field_id,
												'order'						=>	$order,
												'label'						=>	$form_field['label'],
												'caption'					=>	$form_field['caption'],
												'placeholder'				=>	$form_field['placeholder'],
												'required'					=>	$form_field['required'],
												'errormessage'				=>	$form_field['errormessage'],
                                                'settings'					=>	maybe_serialize(isset($form_field['settings']) ? $form_field['settings'] : ''),
											);
											
											$this -> FieldsForm() -> save($fieldform_data);
											$order++;
										}
									}
																		
									$emailfields = array('etsubject_confirm', 'etmessage_confirm');
									foreach ($object -> data as $okey => $oval) {										
										if (!empty($okey) && in_array($okey, $emailfields)) {											
											$this -> update_option($okey . '_form_' . $insertid, $oval);
										}
									}															
									break;
								case 'Latestpostssubscription'	:	
									if (empty($object -> data -> updateinterval) || $object -> data -> updateinterval == "Y") {
										$this -> latestposts_scheduling($object -> data -> interval, $object -> data -> startdate, array((int) $object -> insertid));																																			
									}
									break;
								case 'Autoresponder'			:															
									global $Html, $Subscriber, $SubscribersList;
                                    //error_log('test1111');

									/* Create the History email if needed */
									if (!empty($object -> data -> newsletter) && $object -> data -> newsletter == "new") {									
										$history_data = array(
											'subject'			=>	wp_unslash($object -> data -> nnewsletter['subject']),
											'message'			=>	$object -> data -> nnewsletter['content'],
											'theme_id'			=>	$object -> data -> nnewsletter['theme_id'],
											'mailinglists'		=>	$object -> data -> lists,
										);
										
										if ($this -> History() -> save($history_data, true)) {
											$history_id = $this -> History() -> insertid;
											
											$this -> model = $this -> Autoresponder() -> model;
											$this -> save_field('history_id', $history_id, array('id' => $this -> Autoresponder() -> insertid));
										}
									}
									
									/* Do the Autoresponder > List associations */
									if (!empty($this -> Autoresponder() -> insertid)) {
										$this -> AutorespondersList() -> delete_all(array('autoresponder_id' => $this -> Autoresponder() -> insertid));
										$listsquery = "";
										$l = 1;
										
										foreach ($this -> Autoresponder() -> data -> lists as $list_id) {
											$listsquery .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($list_id) . "'";
                                            if (count($this -> Autoresponder() -> data -> lists) > $l) {
                                                $listsquery .= " OR ";
                                            }
											
											$autoresponderslist_data = array(
												'autoresponder_id'	=>	$this -> Autoresponder() -> insertid,
												'list_id'			=>	$list_id,
											);
											
											$this -> AutorespondersList() -> save($autoresponderslist_data, true);	
											$l++;
										}
										
										$this -> AutorespondersForm() -> delete_all(array('autoresponder_id' => $this -> Autoresponder() -> insertid));
										foreach ($this -> Autoresponder() -> data -> forms as $form_id) {
											$autorespondersform_data = array(
												'autoresponder_id'	=>	$this -> Autoresponder() -> insertid,
												'form_id'			=>	$form_id,
											);
											
											$this -> AutorespondersForm() -> save($autorespondersform_data, true);
										}
									}
									if ($this -> Autoresponder() -> data -> applyexisting == "Y" && $this -> Autoresponder() -> data -> status == "active") {
										$senddate = $Html -> gen_date("Y-m-d H:i:s", strtotime("+ " . $this -> Autoresponder() -> data -> delay . " " . $this -> Autoresponder() -> data -> delayinterval));
										
										$query1 = "SELECT DISTINCT " 
										. $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id as sid, "
										. $wpdb -> prefix . $this -> AutorespondersList() -> table . ".list_id, "
										. $wpdb -> prefix . $this -> AutorespondersList() -> table . ".autoresponder_id, "
										. "'" . $Html -> gen_date() . "', '" . $Html -> gen_date() . "', '" . $senddate . "' FROM " 
										. $wpdb -> prefix . $SubscribersList -> table . " LEFT JOIN "
										. $wpdb -> prefix . $this -> AutorespondersList() -> table . " ON ("
										. $wpdb -> prefix . $SubscribersList -> table . ".list_id = "
										. $wpdb -> prefix . $this -> AutorespondersList() -> table . ".list_id)"
										. " WHERE (" . $listsquery . ") AND "
										. $wpdb -> prefix . $this -> AutorespondersList() -> table . ".autoresponder_id = '" . $this -> Autoresponder() -> insertid . "'";
										
										if (empty($this -> Autoresponder() -> data -> alwayssend) || $this -> Autoresponder() -> data -> alwayssend == "N") {
											$query1 .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id NOT IN 
											(SELECT subscriber_id FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " WHERE autoresponder_id = '" . $this -> Autoresponder() -> insertid . "')";
										}
										
										$query1 .= " GROUP BY " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id";
										
										$query2 = "INSERT INTO " . $wpdb -> prefix . $this -> Autoresponderemail() -> table 
										. " (subscriber_id, list_id, autoresponder_id, created, modified, senddate) (" . $query1 . ")";
										
										$wpdb -> query($query2);
									}
									break;	
							}
							
							$this -> model = $oldmodel;
							
							// eg. newsletters_group_saved
							do_action('newsletters_' . strtolower(str_replace("wpml", "", $oldmodel)) . '_saved', $object -> data);
							
							return true;
						}
					} else {
                        $oldmodel = $object -> model  ;
						$this -> model = $oldmodel;
					}
				}
			}
			
			return false;
		}
		
		function save_field($field = null, $value = null, $conditions = array())
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model}, $Db, $Field, $Email, $Html;
				
				$object = $this -> getobject($this -> model);
				$field = esc_sql($field);
				$value = esc_sql($value);
	
				if (!empty($field)) {			
					$query = "UPDATE `" . $wpdb -> prefix . "" . $object -> table . "` SET `" . $field . "` = '" . esc_sql($value) . "'";
					
					$conditions = apply_filters('newsletters_db_savefield_conditions', $conditions, $object -> model);
					
					if (!empty($conditions) && is_array($conditions)) {
						$query .= " WHERE";
						$c = 1;
						
						foreach ($conditions as $ckey => $cval) {
							$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
							
							if ($c < count($conditions)) {
								$query .= " AND";
							}
							
							$c++;
						}
					}
					
					$result = $wpdb -> query($query);
					
					if ($result !== false && $result >= 0) {	
                        do_action('newsletters_' . strtolower(str_replace("wpml", "", $object -> model)) . '_field_saved', isset($object -> data) ? $object -> data : array());
										
						switch ($this -> model) {
							case $Email -> model 					:							
								if (!empty($field) && $field == "read") {
									$this -> model = $Email -> model;
									$this -> save_field('read_date', $Html -> gen_date(), $conditions);
								}
								break;
							case 'Subscriber'						:
								$Db -> model = $Field -> model;
								if ($customfield = $Db -> find(array('slug' => $field))) {
									$subscriber_id = $conditions['id'];
									
									if (!empty($subscriber_id)) {
										$this -> SubscribersOption() -> delete_all(array('subscriber_id' => $subscriber_id, 'field_id' => $customfield -> id));
										
										if ($customfield -> type == "radio" || $customfield -> type == "checkbox" || $customfield -> type == "select") {
											$subscriber_fieldoptions = maybe_unserialize($value);								
											
											
											if (!empty($subscriber_fieldoptions)) {									
												$new_subscriber_fieldoptions = array();
												
												if (is_array($subscriber_fieldoptions)) {															
													foreach ($subscriber_fieldoptions as $subscriber_fieldoption) {																
														$option_id = $subscriber_fieldoption;
														
														$subscribers_option_data = array(
															'subscriber_id'					=>	$subscriber_id,
															'field_id'						=>	$customfield -> id,
															'option_id'						=>	$option_id,
														);
														
														$this -> SubscribersOption() -> save($subscribers_option_data);
													}
												} else {	
													$option_id = $subscriber_fieldoption;
																											
													$subscribers_option_data = array(
														'subscriber_id'					=>	$subscriber_id,
														'field_id'						=>	$customfield -> id,
														'option_id'						=>	$option_id,
													);
													
													$this -> SubscribersOption() -> save($subscribers_option_data);
												}
											}
										}
									}
								}
								
								$this -> model = 'Subscriber';
								
								break;
						}
						
						return true;
					}
				}
			}
			
			return false;
		}
		
		function iquery($model = null)
        {
			if (!empty($model)) {
				global $wpdb, ${$model};
				
				$object = $this -> getobject($model);
				
				if (!empty($object -> data)) {				
					$data = $object -> data;
				
					if (empty($data -> id)) {					
						if (!empty($object -> fields)) {						
							$query1 = "INSERT INTO `" . $wpdb -> prefix . "" . $object -> table . "` (";
							$query2 = "";
							$c = 1;
							
							unset($object -> fields['key']);
							unset($object -> fields['id']);
							
							$object -> fields = apply_filters('newsletters_db_insert_fields', $object -> fields, $object -> model);
							
							foreach (array_keys($object -> fields) as $field) {
								if (isset($data -> {$field}) && (!empty($data -> {$field}) || $data -> {$field} == 0)) {
									$query1 .= "`" . $field . "`";
									
									if (is_array($data -> {$field}) || is_object($data -> {$field})) {
										$data -> {$field} = maybe_serialize($data -> {$field});
									}
									
									$query2 .= "'" . esc_sql($data -> {$field}) . "'";
									
									if ($c < count($object -> fields)) {
										$query1 .= ", ";
										$query2 .= ", ";
									}
								}
								
								$c++;
							}
							
							$query1 = rtrim($query1, ", ");
							$query2 = rtrim($query2, ", ");
							
							$query1 .= ") VALUES (";
							$query = $query1 . $query2 . ");";
							
							return $query;
						}
					} else {
						$query = $this -> uquery($model);
						return $query;
					}
				}
			}
			
			return false;
		}
		
		function uquery($model = null)
        {
			global $wpdb, ${$model};
			
			$object = $this -> getobject($model);
			
			if (!empty($model)) {
				$data = $object -> data;
				
				if (!empty($data -> id)) {
					if (!empty($object -> fields)) {					
						$query = "UPDATE `" . $wpdb -> prefix . "" . $object -> table . "` SET ";
						
						$c = 1;
						unset($object -> fields['key']);
						unset($object -> fields['created']);
						
						$object -> fields = apply_filters('newsletters_db_update_fields', $object -> fields, $object -> model);
						
						foreach (array_keys($object -> fields) as $field) {												
                            if ($model == "Subscribeform")
                            {
                                if (isset($data)) {

                                    if (is_array($data -> {$field}) || is_object($data -> {$field})) {
                                        $data -> {$field} = maybe_serialize($data -> {$field});
                                    }

                                    $query .= "`" . $field . "` = '" . $data -> {$field} . "'";

                                    if ($c < count($object -> fields)) {
                                        $query .= ", ";
                                    }
                                }

                                $c++;
                            }
							else if ($model == "FieldsForm")
                            {
                                if (isset($data)) {

                                    if (is_array($data -> {$field}) || is_object($data -> {$field})) {
                                        $data -> {$field} = maybe_serialize($data -> {$field});
                                    }

                                    $query .= "`" . $field . "` = '" . $data -> {$field} . "'";

                                    if ($c < count($object -> fields)) {
                                        $query .= ", ";
                                    }
                                }

                                $c++;
                            }
                            else {
                                if ((isset($data) && isset($data -> {$field})) && (!empty($data -> {$field}) || $data -> {$field} == "0")) {

                                    if (is_array($data -> {$field}) || is_object($data -> {$field})) {
                                        $data -> {$field} = maybe_serialize($data -> {$field});
                                    }

                                    $query .= "`" . $field . "` = '" . esc_sql($data -> {$field}) . "'";

                                    if ($c < count($object -> fields)) {
                                        $query .= ", ";
                                    }
                                }

                                $c++;
                            }
						}
						
						$query = rtrim($query, ", ");
						$query .= " WHERE `id` = '" . $data -> id . "';";
						return $query;
					}
				} else {
					$query = $this -> iquery($model);
					return $query;
				}
			}
			
			return false;
		}
		
		function field($field = null, $conditions = array())
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				
				if (!empty($conditions) && is_array($conditions)) {
					$query = "SELECT `" . $field . "` FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE";
					$c = 1;
					
					foreach ($conditions as $ckey => $cval) {
						$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
						
						if ($c < count($conditions)) {
							$query .= " AND";
						}
						
						$c++;
					}
					
					$query_hash = md5($query);
					if ($ob_value = $this -> get_cache($query_hash)) {
						return $ob_value;
					} else {
						$value = $wpdb -> get_var($query);
						$this -> set_cache($query_hash, $value);
						return $value;
					}
				}
			}
			
			return false;
		}
		
		function delete($record_id = null)
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				$record_id = esc_sql($record_id);
			
				if (!empty($record_id)) {
					switch ($this -> model) {
						case 'History'					:
							$p_id = $this -> History() -> field('p_id', array('id' => $record_id));
							break;	
					}
					
					$query = "DELETE FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE `id` = '" . esc_sql($record_id) . "' LIMIT 1";
					

					if ($this->model == 'Subscriber') {
						$wp_user_id = (int) $wpdb->get_var("SELECT user_id FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE `id` = '" . $record_id . "'");
					}

					if ($wpdb -> query($query)) {
						do_action('newsletters_' . strtolower(str_replace("wpml", "", $object -> model)) . '_deleted', $record_id);
						
						switch ($this -> model) {
							case 'Subscribeform'			:
								$this -> FieldsForm() -> delete_all(array('form_id' => $record_id));
								break;
							case 'Latestpostssubscription'	:
								wp_clear_scheduled_hook('newsletters_latestposts', array((int) $record_id));
								$this -> Latestpost() -> delete_all(array('lps_id' => $record_id));
								break;
							case 'Link'					:
								$this -> Click() -> delete_all(array('link_id' => $record_id));
								break;
							case 'Subscriber'			:
								//global variables
								global $Email, $SubscribersList;
							
								//remove all Orders
								$this -> model = $this -> Order() -> model;
								$this -> delete_all(array('subscriber_id' => $record_id));
								
								//remove all List associations
								$this -> model = $SubscribersList -> model;
								$this -> delete_all(array('subscriber_id' => $record_id));
	
	                            //remove all emails
	                            $this -> model = $Email -> model;
	                            $this -> delete_all(array('subscriber_id' => $record_id));
	                            
	                            //remove all autoresponder emails
	                            $this -> model = $this -> Autoresponderemail() -> model;
	                            $this -> delete_all(array('subscriber_id' => $record_id));
	                            
	                            $this -> model = $this -> Click() -> model;
	                            $this -> delete_all(array('subscriber_id' => $record_id));
	                            
	                            $this -> model = $this -> SubscribersOption() -> model;
	                            $this -> delete_all(array('subscriber_id' => $record_id));

								// maybe delete WP User as well
								if ($this->get_option('unsubscribewpuserdelete') == 'Y' 
									&& is_numeric($wp_user_id) 
									&& $wp_user_id > 0) {
									wp_delete_user($wp_user_id);
								}

								break;
							case 'Mailinglist'			:						
								$this -> model = 'HistoriesList';
								$this -> delete_all(array('list_id' => $record_id));
								
								/* Remove the Autoresponder/List associations */
								$this -> AutorespondersList() -> delete_all(array('list_id' => $record_id));
								
								$this -> model = $this -> Autoresponderemail() -> model;
								$this -> delete_all(array('list_id' => $record_id));
								break;
							case 'History'				:
								global $Email, $Bounce, $HistoriesAttachment, $HistoriesList, $Unsubscribe;
							
								$this -> model = 'HistoriesList';
								$this -> delete_all(array('history_id' => $record_id));
								
								$this -> Autoresponder() -> delete_all(array('history_id' => $record_id));
								
								if (!empty($p_id)) {
									$this -> model = $object -> model;
									wp_delete_post($p_id, true);
								}
	
	                            //remove all emails
	                            $this -> model = $Email -> model;
	                            $this -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> Click() -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> model = $Bounce -> model;
	                            $this -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> Content() -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> model = $HistoriesAttachment -> model;
	                            $this -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> model = $HistoriesList -> model;
	                            $this -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> Latestpostssubscription() -> delete_all(array('history_id' => $record_id));
	                            
	                            $this -> model = $Unsubscribe -> model;
	                            $this -> delete_all(array('history_id' => $record_id));
								break;
							case 'Autoresponder'		:								
								$this -> AutorespondersList() -> delete_all(array('autoresponder_id' => $record_id));
								
								//remove the Autoresponderemail records
								$this -> model = $this -> Autoresponderemail() -> model;
								$this -> delete_all(array('autoresponder_id' => $record_id));
								break;
						}
						
						$this -> model = $object -> model;
					
						return true;
					}
				}
			}
			
			return false;
		}
		
		function delete_all($conditions = array())
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model}, $SubscribersList;
				
				$object = $this -> getobject($this -> model);
				
				if (!empty($conditions) && is_array($conditions)) {
					$query = "DELETE FROM `" . $wpdb -> prefix . "" . $object -> table . "` WHERE";
					$c = 1;
					
					foreach ($conditions as $ckey => $cval) {
						$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
						
						if ($c < count($conditions)) {
							$query .= " AND";
						}
						
						$c++;
					}
					
					if ($wpdb -> query($query)) {
						switch ($this -> model) {
							case $SubscribersList -> model				:
								if (!empty($conditions['subscriber_id'])) {
									$this -> Autoresponderemail() -> delete_all(array('subscriber_id' => $conditions['subscriber_id']));
								}
								break;
						}
												
						return true;
					}
				}
			}
			
			return false;
		}
		
		function count($conditions = array(), $sum = false, $sumcol = null)
        {
			$count = 0;
		
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				
				if (!empty($sum) && !empty($sumcol)) {
					$query = "SELECT SUM(`" . $sumcol . "`) FROM `" . $wpdb -> prefix . $object -> table . "`";
				} else {
					$query = "SELECT COUNT(*) FROM `" . $wpdb -> prefix . $object -> table . "`";
				}
				
				$conditions = apply_filters('newsletters_db_count_conditions', $conditions, $object -> model);
				
				if (!empty($conditions) && is_array($conditions)) {
					$query .= " WHERE";
					$c = 1;
					
					foreach ($conditions as $ckey => $cval) {				
						if (preg_match("/[>]\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[1]) || $cmatches[1] == "0") {					
								$query .= " `" . $ckey . "` > " . esc_sql($cmatches[1]) . "";
							}
						} elseif (preg_match("/[<]\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[1]) || $cmatches[1] == "0") {
								$query .= " `" . $ckey . "` < " . esc_sql($cmatches[1]) . "";	
							}
						} elseif (preg_match("/^(!=)\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[2]) || $cmatches[2] == 0) {
								$query .= " `" . $ckey . "` != " . esc_sql($cmatches[2]) . "";
							}
						} elseif (preg_match("/(NOT IN)/si", $cval)) {
							$query .= " " . $ckey . " " . ($cval) . "";
							$countquery .= " " . $ckey . " " . esc_sql($cval) . "";
						} elseif (preg_match("/(IN \()/si", $cval)) {
							$query .= " " . $ckey . " " . ($cval) . "";
							$countquery .= " " . $ckey . " " . esc_sql($cval);
						} elseif (preg_match("/(LIKE)/", $cval, $cmatches)) {
							$query .= " `" . $ckey . "` " . ($cval);
						} else {											
							$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
						}
						
						if ($c < count($conditions)) {
							$query .= " AND";
						}
						
						$c++;
					}
				}
				
				$query_hash = md5($query);
				if ($ob_count = $this -> get_cache($query_hash)) {
					return $ob_count;
				} else {
					$count = $wpdb -> get_var($query);
					$this -> set_cache($query_hash, $count);
					return $count;
				}
			}
			
			return $count;
		}
		
		function select()
        {
			$select = false;
			
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				
				if (empty($object -> table)) {
					return false;
				}
				
				
				$fields = "id, title";
				$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
				
				$query_hash = md5($query);
				if ($ob_records = $this -> get_cache($query_hash)) {
					return $ob_records;
				}
				
				if ($records = $wpdb -> get_results($query)) {
					foreach ($records as $record) {
						$record = $this -> init_class($object -> model, $record);
						$select[$record -> id] = $record -> title;
					}
					
					$this -> set_cache($query_hash, $select);
				}
			}
			
			return $select;
		}
		
		function find($conditions = array(), $fields = false, $order = array('modified', "DESC"), $assign = true, $recursive = true, $cache = true)
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				
				if (empty($object -> table)) {
					return false;
				}
				
				$fields = (empty($fields)) ? "*" : implode(", ", $fields);
				$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
				
				$conditions = apply_filters('newsletters_db_find_conditions', $conditions);
				
				if (!empty($conditions) && is_array($conditions)) {
					$query .= " WHERE";
					$c = 1;
					
					foreach ($conditions as $ckey => $cval) {
						$query .= " `" . $ckey . "` = '" . esc_sql($cval) . "'";
						
						if ($c < count($conditions)) {
							$query .= " AND";
						}
						
						$c++;
					}
				}
				
				$order = (empty($order)) ? array('modified', "DESC") : $order;
				list($ofield, $odir) = $order;
				$query .= " ORDER BY `" . $ofield . "` " . $odir . "";
				$query .= " LIMIT 1";
				
				$query_hash = md5($query);
				if ($ob_record = $this -> get_cache($query_hash)) {
					return $ob_record;
				}
				
				if ($record = $wpdb -> get_row($query)) {
					if (!empty($record)) {				
						$record -> recursive = ((!empty($recursive) && $recursive == true) ? 1 : 0);
						$data = $this -> init_class($object -> model, $record);
						
						if ($assign == true) {
							$object -> data = $data;
							
							global $Db;
							if ($Db -> {$object -> model}()) {
								$Db -> {$object -> model}() -> data = $data;
							}
						}
						
						$this -> set_cache($query_hash, $data);
						return $data;
					}
				}
			}
			
			return false;
		}
		
		function find_all($conditions = array(), $fields = false, $order = array('modified', "DESC"), $limit = false, $recursive = false)
        {
			if (!empty($this -> model)) {
				global $wpdb, ${$this -> model};
				
				$object = $this -> getobject($this -> model);
				
				$fields = (empty($fields) || !is_array($fields)) ? "*" : implode(", ", $fields);
				$query = "SELECT " . $fields . " FROM `" . $wpdb -> prefix . "" . $object -> table . "`";
				
				$conditions = apply_filters('newsletters_db_findall_conditions', $conditions, $object -> model);
				
				if (!empty($conditions) && is_array($conditions)) {
					$query .= " WHERE";
					$c = 1;
					
					foreach ($conditions as $ckey => $cval) {				
						if (preg_match("/[>]\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[1]) || $cmatches[1] == "0") {					
								$query .= " `" . $ckey . "` > " . esc_sql($cmatches[1]) . "";
							}
						} elseif (preg_match("/[<]\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[1]) || $cmatches[1] == "0") {
								$query .= " `" . $ckey . "` < " . esc_sql($cmatches[1]) . "";	
							}
						} elseif (preg_match("/^(!=)\s?(.*)?/si", $cval, $cmatches)) {
							if (!empty($cmatches[2]) || $cmatches[2] == 0) {
								$query .= " `" . $ckey . "` != " . esc_sql($cmatches[2]) . "";
							}
						} elseif (preg_match("/(IN \()/si", $cval, $matches)) {
							$query .= " " . $ckey . " " . ($cval) . "";
							$countquery .= " " . $ckey . " " . $cval;
						} elseif (preg_match("/(LIKE)/", $cval, $cmatches)) {
							$query .= " `" . $ckey . "` " . esc_sql($cval);
						} else {											
							$query .= " " . $ckey . " = " . ($cval) . "";
						}
						
						if ($c < count($conditions)) {
							$query .= " AND";
						}
						
						$c++;
					}
				}
				
				$order = (empty($order)) ? array('modified', "DESC") : $order;
				list($ofield, $odir) = $order;
				$query .= " ORDER BY `" . $ofield . "` " . $odir . "";
				$query .= (empty($limit)) ? '' : " LIMIT " . $limit . "";
				
				$query_hash = md5($query);
				if ($ob_records = $this -> get_cache($query_hash)) {
					return $ob_records;
				}
				
				if ($records = $wpdb -> get_results($query)) {
					if (!empty($records)) {
						$data = array();
						
						foreach ($records as $record) {
                            if ((!empty($recursive) && $recursive == true) || (!empty($object -> recursive) && $object -> recursive == true)) {
                                $record -> recursive = true;
                            }
							$data[] = $this -> init_class($object -> model, $record);
						}
						
						$this -> set_cache($query_hash, $data);
						return $data;
					}
				}
			}
			
			return false;
		}
	}
}

?>
