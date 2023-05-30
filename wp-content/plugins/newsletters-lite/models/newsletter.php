<?php

if (!class_exists('newsletters_lite')) {
	class newsletters_lite extends wpMailPlugin
    {
		function __construct()
        {
			parent::__construct();
				
			if (!is_multisite() || (is_multisite() && $this -> is_plugin_active($this -> plugin_file))) {	
				if (!$this -> ci_serial_valid()) {				
					$this -> add_filter('newsletters_sections', 'lite_sections', 10, 1);
					$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);		
					$this -> add_action('newsletters_admin_menu', 'lite_admin_menu', 10, 1);
					$this -> add_action('admin_bar_menu', 'lite_admin_bar_menu', 999, 1);
					$this -> add_filter('wpml_mailinglist_validation', 'lite_mailinglist_validation', 10, 2);
					$this -> add_filter('wpml_sendmail_validation', 'lite_sendmail_validation', 10, 2); 
					$this -> add_filter('wpml_subscriber_validation', 'lite_subscriber_validation', 10, 2);
					$this -> add_filter('newsletters_field_validation', 'lite_field_validation', 10, 2);
				}
			}
		}
		
		function lite_sections($sections = null)
        {
			$sections -> lite_upgrade = "newsletters-lite-upgrade";
			return $sections;
		}
		
		function lite_admin_menu($menus = null)
        {
			add_submenu_page($this -> sections -> welcome, __('Upgrade to PRO', 'wp-mailinglist'), __('Upgrade to PRO', 'wp-mailinglist'), 'newsletters_welcome', $this -> sections -> lite_upgrade, array($this, 'lite_upgrade'));
		}
		
		function lite_upgrade()
        {
			$this -> render('lite-upgrade', false, true, 'admin');
		}
		
		function lite_admin_bar_menu($wp_admin_bar = null)
        {
			global $wp_admin_bar, $blog_id;

			if (is_multisite()) {				
				if (is_network_admin()) {
					return;
				}
			}
			
			if (!current_user_can('newsletters_welcome')) {
				return;
			}
		
			$args = array(
				'id'		=>	'newsletterslite',
				'title'		=>	sprintf(__('%s LITE', 'wp-mailinglist'), $this -> name),
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'newsletters-lite'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			global $Db, $Mailinglist;
			$Db -> model = $Mailinglist -> model;
			$list_count = $Db -> count();
			$lists = $list_count;
			$lists_percentage = (($lists / 1) * 100);
			$listlimit_title = sprintf(__('%s of 1 (%s&#37;) mailing lists used', 'wp-mailinglist'), $lists, $lists_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_listlimit',
				'title'		=>	$listlimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-listlimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			global $Db, $Subscriber;
			$Db -> model = $Subscriber -> model;
			$subscriber_count = $Db -> count();
			$subscribers = $subscriber_count;
			$subscribers_percentage = (($subscribers / 500) * 100);
			$subscriberlimit_title = sprintf(__('%s of 500 (%s&#37;) subscribers used', 'wp-mailinglist'), $subscribers, $subscribers_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_subscriberlimit',
				'title'		=>	$subscriberlimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-subscriberlimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$emails = $this -> lite_current_emails_all(1000, 'monthly');
			$emails_percentage = (($emails / 1000) * 100);
			$emaillimit_title = sprintf(__('%s of 1000 (%s&#37;) emails used (resets on 1st of month)', 'wp-mailinglist'), $emails, $emails_percentage);
			
			$args = array(
				'id'		=>	'newsletterslite_emaillimit',
				'title'		=>	$emaillimit_title,
				'parent'	=>	'newsletterslite',
				'href'		=>	false,
				'meta'		=>	array('class' => 'newsletters-lite-emaillimit'),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'newsletterslite_submitserial',
				'title'		=>	__('Submit Serial Key', 'wp-mailinglist'),
				'parent'	=>	'newsletterslite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> submitserial),
				'meta'		=>	array('class' => 'newsletters-lite-submitserial', 'onclick' => "jQuery.colorbox({href:newsletters_ajaxurl + \"action=" . $this -> pre . "serialkey&security=" . wp_create_nonce('serialkey') . "\"}); return false;"),
			);
			
			$wp_admin_bar -> add_node($args);
			
			$args = array(
				'id'		=>	'newsletterslite_upgrade',
				'title'		=>	__('Upgrade to PRO now!', 'wp-mailinglist'),
				'parent'	=>	'newsletterslite',
				'href'		=>	admin_url('admin.php?page=' . $this -> sections -> lite_upgrade),
				'meta'		=>	array('class' => 'newsletters-lite-upgrade'),
			);
			
			$wp_admin_bar -> add_node($args);
		}
		
		function lite_prevtime($time = null, $interval = null, $value = null)
        {
			global $Html;
			
			$time = (empty($time)) ? false : strtotime($time);
		
			switch ($interval) {
				case 'hourly'			:
					$offset = ($time % 3600);
					$prev = $time - $offset;
					$seconds = ($value * 60);																						
					$newtime = ($offset >= $seconds) ? ($prev + $seconds) : ($prev - 3600 + $seconds);
					break;
                case 'daily':
                    if ($Html -> gen_date("H", $time) < $value) {
                        $time = strtotime("-1 days", $time);
                    }
					$y = $Html -> gen_date("Y", $time);
					$m = $Html -> gen_date("m", $time);
					$d = $Html -> gen_date("d", $time);
					$h = $Html -> gen_date("H", strtotime($value . ':00'));
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' ' . $h . ':00');
					break;
				case 'weekly'			:
					$diff = $value - $Html -> gen_date("w"); 
					$timestamp = strtotime("+" . $diff . " days");
					$timestamp = strtotime("-7 days", $timestamp);
					$y = $Html -> gen_date("Y", $timestamp);
					$m = $Html -> gen_date("m", $timestamp);
					$d = $Html -> gen_date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' 00:00:00');
					break;
				case 'monthly'			:
					$diff = $value - $Html -> gen_date("d"); 
					$timestamp = strtotime("+" . $diff . " days");
                    if ($Html -> gen_date("d", $time) < $value) {
                        $timestamp = strtotime("-1 months", $timestamp);
                    }
					$y = $Html -> gen_date("Y", $timestamp);
					$m = $Html -> gen_date("m", $timestamp);
					$d = $Html -> gen_date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-' . $d . ' 00:00:00');
					break;
				case 'yearly'			:
					$diff = $value - $Html -> gen_date("m"); 
					$timestamp = strtotime("+" . $diff . " months");
                    if ($Html -> gen_date("m", $time) < $value) {
                        $timestamp = strtotime("-1 years", $timestamp);
                    }
					$y = $Html -> gen_date("Y", $timestamp);
					$m = $Html -> gen_date("m", $timestamp);
					$d = $Html -> gen_date("d", $timestamp);
					$newtime = strtotime($y . '-' . $m . '-01 00:00:00');
					break;
			}									
			
			return $newtime;
		}
		
        public function lite_current_emails_all($sendlimit = null, $sendlimitinterval = null, $sendlimitstart = null)
        {
			global $Email, $wpdb, $Html;
			$emailscount = false;
			
			$prevtime = $Html -> gen_date("Y-m-d H:i:s", $this -> lite_prevtime(false, $sendlimitinterval, $sendlimitstart));
			$email_table = $wpdb -> prefix . $Email -> table;
			$history_table = $wpdb -> prefix . $this -> History() -> table;
			
			$emailsquery = "SELECT COUNT(" . $email_table . ".id) FROM `" . $email_table . "` LEFT JOIN `" . $history_table . "` 
			ON " . $email_table . ".history_id = " . $history_table . ".id WHERE " . $email_table . ".created > '" . $prevtime . "'";
			
			$emailscount = $wpdb -> get_var($emailsquery);				
			return $emailscount;
		}
		
		function lite_mailinglist_validation($errors = null, $data = null)
        {
			$newsletters_lite_listlimit = 1;
			if (!empty($newsletters_lite_listlimit) && $newsletters_lite_listlimit > 0) {
				global $Db, $Mailinglist;
				$Db -> model = $Mailinglist -> model;
				$list_count = $Db -> count();
				
				if (empty($data -> id) && $list_count >= $newsletters_lite_listlimit) {
					$error = sprintf(__('Only %s mailing list allowed in the free version. %s to create unlimited mailing lists.', 'wp-mailinglist'), $newsletters_lite_listlimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					
					if (!defined('DOING_AJAX')) {
						$this -> render_error($error);
					}
				}
			}
			
			return $errors;
		}
		
		function lite_sendmail_validation($errors = null, $data = null)
        {
			global $Db, $wpdb;
		
			if (!empty($data['history_id'])) {
				$history_id = $data['history_id'];
				if ($history = $this -> History() -> find(array('id' => $history_id))) {
					$newsletters_lite_emaillimit = 1000;
					$newsletters_lite_emaillimitinterval = 'monthly';
					$newsletters_lite_emaillimitstart = 1;
					$newsletters_current_emails = $this -> lite_current_emails_all($newsletters_lite_emaillimit, $newsletters_lite_emaillimitinterval, $newsletters_lite_emaillimitstart);
					
					if (!empty($newsletters_lite_emaillimit)) {
						if ($newsletters_current_emails >= $newsletters_lite_emaillimit) {
							$error = sprintf(__('Email limit of %s emails per month has been reached, you can %s for unlimited.', 'wp-mailinglist'), $newsletters_lite_emaillimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
							global $mailerrors;
							$mailerrors = $error;
							$errors['limit'] = $error;
						}
					}
				}
			}
			
			return $errors;
		}
		
		function lite_subscriber_validation($errors = null, $data = null)
        {
			$newsletters_lite_subscriberlimit = 500;
			if (!empty($newsletters_lite_subscriberlimit) && $newsletters_lite_subscriberlimit > 0) {
				global $Db, $Subscriber;
				$Db -> model = $Subscriber -> model;
				$subscriber_count = $Db -> count();
				
				if ($subscriber_count >= $newsletters_lite_subscriberlimit) {
					$error = sprintf(__('Subscriber limit of %s has been reached, you can %s for unlimited.', 'wp-mailinglist'), $newsletters_lite_subscriberlimit, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
					$errors['limit'] = $error;
					
					if (!defined('DOING_AJAX')) {
						$this -> render_error($error);
					}
				}
			}
			
			return $errors;
		}
		
		function lite_field_validation($errors = null, $data = null)
        {
			global $Db, $Field;
			$Db -> model = $Field -> model;
			$field_count = $Db -> count();
		
			if ($field_count >= 3 && empty($data -> id)) {
				$error = sprintf(__('Additional custom fields are only available in the PRO version, you can %s for unlimited.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">Upgrade to PRO</a>');
				$errors['limit'] = $error;
				
				if (!defined('DOING_AJAX')) {
					$this -> render_error($error);
				}
			}
			
			return $errors;
		}
	}
	
	add_action('plugins_loaded', 'load_newsletters_lite');
	
	function load_newsletters_lite()
    {
		$newsletters_lite = new newsletters_lite();
		return $newsletters_lite;
	}
}

?>