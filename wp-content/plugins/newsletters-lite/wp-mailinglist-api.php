<?php
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('wpMailAPI')) {
class wpMailAPI extends wpMail {

	var $api_methods = array(
		'subscriber_add',
		'subscriber_delete',
		'send_email',
		'send_newsletter',
	);
	
	var $api_method;
	
	function __construct() {
		return;
	}
	
	function api_init() {
		
		// check if the API is enabled
		$api_enable = $this -> get_option('api_enable');
		if (empty($api_enable)) {
			$error = sprintf(__('The API is disabled, please turn it on under %s > Configuration > API.', 'wp-mailinglist'), $this -> name);
			$this -> api_error($error);
			
			exit();
			die();
		}		
		
		// check the host 
		$api_hosts = $this -> get_option('api_hosts');
		if (!empty($api_hosts)) {
			$remote_addr = $this -> get_ip_address();
			if (empty($remote_addr) || !in_array($remote_addr, $api_hosts)) {
				$error = sprintf(__('The IP address %s is not allowed by this API.', 'wp-mailinglist'), $remote_addr);
				$this -> api_error($error);
				
				exit();
				die();
			}
		}
		
		global $wpdb, $Db, $Html, $Subscriber, 
		$Field, $Mailinglist, $Unsubscribe, $Bounce, $SubscribersList;;
		
		$api_key = $this -> get_option('api_key');
		$input = file_get_contents('php://input');
		
		if ($Html -> is_json($input)) {
			$data = json_decode($input, false);
		} elseif (!empty($_REQUEST)) {
			$data = (object) $_REQUEST;
		}
		
		if (!empty($data)) {
			if (!empty($data -> api_key) && $data -> api_key == $api_key) {
				if (!empty($data -> api_method) && in_array($data -> api_method, $this -> api_methods)) {
					$this -> api_method = $data -> api_method;
				
					switch ($data -> api_method) {
						case 'subscriber_add'			:
							$subscriber_data = $data -> api_data;						
							if ($subscriber_id = $Subscriber -> optin((array) $subscriber_data, false)) {
								$result = array('id' => $subscriber_id);
								$this -> api_success($result);
							} else {
								$error = (object) $Subscriber -> errors;
								$this -> api_error($error);	
							}
							break;
						case 'subscriber_delete'		:
							$Db -> model = $Subscriber -> model;
							if ($Db -> delete($data -> api_data -> id)) {
								$result = sprintf(__('Subscriber %s has been deleted', 'wp-mailinglist'), $data -> api_data -> id);
								$this -> api_success($result);
							} else {
								$error = __('Subscriber could not be deleted', 'wp-mailinglist');
								$this -> api_error($error);
							}
							break;
						case 'send_newsletter'			:						
							if (!empty($data -> api_data -> history_id)) {
								$history_id = $data -> api_data -> history_id;
								if ($history = $this -> History() -> find(array('id' => $history_id))) {									
									$this -> remove_server_limits();
									$mailinglists = maybe_unserialize($history -> mailinglists);
									$roles = maybe_unserialize($history -> roles);
									$fieldsconditions = maybe_unserialize($history -> conditions);
									$condquery = maybe_unserialize($history -> condquery);
									$subscriberids = array();
									$subscriberemails = array();
									
									if (!empty($mailinglists) || !empty($roles)) {
										$mailinglistscondition = "(";
										$m = 1;
										
										foreach ($mailinglists as $mailinglist_id) {
											$mailinglistscondition .= "list_id = '" . esc_sql($mailinglist_id) . "'";
											if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
											$m++;	
										}
										
										$fields = array_filter($fieldsconditions);
										$scopeall = (empty($history -> conditionsscope) || $history -> conditionsscope == "all") ? true : false;
										$fieldsquery = $Subscriber -> get_segmented_query($fields, $scopeall, $condquery);
										
										if (!empty($history -> daterange) && $history -> daterange == "Y") {							
											if (!empty($history -> daterangefrom) && !empty($history -> daterangeto)) {								
												$daterangefrom = date_i18n("Y-m-d H:i:s", strtotime($history -> daterangefrom));
												$daterangeto = date_i18n("Y-m-d H:i:s", strtotime($history -> daterangeto));
												$fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
											}
										}
										
										// Countries
										if (!empty($history -> countries)) {
											if (!empty($history -> selectedcountries) && is_array($history -> selectedcountries)) {
												$countries = implode("', '", $history -> selectedcountries);
												$fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".country IN ('" . $countries . "'))";
											}
										}
										
										/* Attachments */
										$history = $this -> History() -> find(array('id' => $history -> id));
										
										$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
										. $wpdb -> prefix . $Subscriber -> table . ".email FROM " 
										. $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
										. $wpdb -> prefix . $SubscribersList -> table . " ON "
										. $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id WHERE "
										. $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'"
										. str_replace(" AND ()", "", $fieldsquery);
										
										$sent = ($history -> sent + 1);
										$Db -> model = $this -> model;
										$Db -> save_field('sent', $sent, array('id' => $history -> id));
										
										$queue_process_counter_1 = 0;
										$queue_process_counter_2 = 0;
										$queue_process_counter_3 = 0;
										$queue_process = 1;
										
										$emails_queued = 0;
										WPMAIL() -> qp_reset_data();
										
										// Send to user roles?
										if (!empty($roles)) {						
											$users = array();
											$exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
											$exclude_users = $wpdb -> get_var($exclude_users_query);
											
											foreach ($roles as $role_key) {
												$users_arguments = array(
													'blog_id'				=>	$GLOBALS['blog_id'],
													'role'					=>	$role_key,
													'exclude'				=>	$exclude_users,
													'fields'				=>	array('ID', 'user_email', 'user_login'),
												);
												
												$role_users = get_users($users_arguments);
												$users = array_merge($users, $role_users);
											}
											
											if (!empty($users)) {
												foreach ($users as $user) {
													$this -> remove_server_limits();
													
													$queue_process_data = array(
														'user_id'					=>	$user -> ID,
														'subject'					=>	$history -> subject,
														'attachments'				=>	$history -> attachments,
														'post_id'					=>	$history -> post_id,
														'history_id'				=>	$history -> id,
														'theme_id'					=>	$history -> theme_id,
														'senddate'					=>	$history -> senddate,
													);
													
													WPMAIL() -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);
													$emails_queued++;
													
													${'queue_process_counter_' . $queue_process}++;
													if (${'queue_process_counter_' . $queue_process} >= WPMAIL() -> {'queue_process_' . $queue_process} -> counter_reset) {															
														WPMAIL() -> {'queue_process_' . $queue_process} -> save();
														WPMAIL() -> {'queue_process_' . $queue_process} -> reset_data();
														${'queue_process_counter_' . $queue_process} = 0;
													}
													
													$queue_process++;
													if ($queue_process > 3) {
														$queue_process = 1;
													}
													
													continue;
												}
											}
										}
										
										if (!empty($mailinglists)) {
											$query_hash = md5($query);
											if ($ob_subscribers = $this -> get_cache($query_hash)) {
												$subscribers = $ob_subscribers;
											} else {
												$subscribers = $wpdb -> get_results($query);
												$this -> set_cache($query_hash, $subscribers);
											}
																		
											if (!empty($subscribers)) {							
												foreach ($subscribers as $subscriber) {
													$this -> remove_server_limits();											
													
													$queue_process_data = array(
														'subscriber_id'				=>	$subscriber -> id,
														'subject'					=>	$history -> subject,
														'attachments'				=>	$history -> attachments,
														'post_id'					=>	$history -> post_id,
														'history_id'				=>	$history -> id,
														'theme_id'					=>	$history -> theme_id,
														'senddate'					=>	$history -> senddate,
													);
													
													WPMAIL() -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);
													$emails_queued++;
													
													${'queue_process_counter_' . $queue_process}++;
													if (${'queue_process_counter_' . $queue_process} >= WPMAIL() -> {'queue_process_' . $queue_process} -> counter_reset) {															
														WPMAIL() -> {'queue_process_' . $queue_process} -> save();
														WPMAIL() -> {'queue_process_' . $queue_process} -> reset_data();
														${'queue_process_counter_' . $queue_process} = 0;
													}
													
													$queue_process++;
													if ($queue_process > 3) {
														$queue_process = 1;
													}
													
													continue;
												}
											}
										}
											
										WPMAIL() -> qp_save();	
										WPMAIL() -> qp_dispatch();
										
										$this -> api_success(sprintf(__('%s emails have been queued successfully', 'wp-mailinglist'), $emails_queued));
									} else {
										$this -> api_error(__('No list/s or role/s are saved to this newsletter.', 'wp-mailinglist'));
									}
								} else {
									$this -> api_error(sprintf(__('Newsletter with ID %s cannot be found', 'wp-mailinglist'), $history_id));
								}
							} else {
								$this -> api_error(__('No newsletter was specified', 'wp-mailinglist'));
							}							
							break;
						case 'send_email'				:
							if (!empty($data -> api_data -> email)) {
								$Db -> model = $Subscriber -> model;
								if ($subscriber = $Db -> find(array('email' => $data -> api_data -> email))) {
									if ($this -> execute_mail($subscriber, false, $data -> api_data -> subject, $data -> api_data -> message)) {
										$this -> api_success(__('Email has been sent', 'wp-mailinglist'));
									} else {
										global $mailerrors;
										
										if (is_array($mailerrors)) {
											$mailerrors = implode("; ", $mailerrors);
										}
										
										$this -> api_error($mailerrors);
									}
								} else {
									$this -> api_error(sprintf(__('Subscriber not found by email, first add with %s', 'wp-mailinglist'), '<code>subscriber_add</code>'));
								}
							} else {
								$this -> api_error(__('No email was specified', 'wp-mailinglist'));
							}
							break;
					}
				} else {
					$error = sprintf(__('%s is not a valid API method', 'wp-mailinglist'), $data -> api_method);
					$this -> api_error($error);
				}
			} else {
				$error = __('API key is invalid, please check', 'wp-mailinglist');
				$this -> api_error($error);
			}
		} else {
			$error = __('No data was posted to the API, check the code', 'wp-mailinglist');
			$this -> api_error($error);
		}
		
		exit();
		die();
	}
	
	function api_output($data = null) {
		header("Content-Type: application/json");
		$data['method'] = $this -> api_method;
		echo wp_json_encode($data);
	}
	
	function api_success($result = null) {
		$data = array(
			'success'			=>	true,
			'result'			=>	$result,
		);
		
		$this -> api_output($data);
	}
	
	function api_error($error = null) {
		$data = array(
			'success'			=>	false,
			'errormessage'		=>	$error,
		);
		
		$this -> api_output($data);
	}
}

$wpMailAPI = new wpMailAPI();
add_action('wp_ajax_newsletters_api', array($wpMailAPI, 'api_init'));
add_action('wp_ajax_nopriv_newsletters_api', array($wpMailAPI, 'api_init'));
}

?>
