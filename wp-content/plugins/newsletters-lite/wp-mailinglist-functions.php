<?php
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!function_exists('newsletters_has_emails_in_queue')) {
	function newsletters_has_emails_in_queue() {
		if (class_exists('wpMail')) {
			if ($wpMail = new wpMail()) {
				$count = $wpMail -> queue_process -> get_queued_count();
				
				if (!empty($count)) {
					return $count;
				}
			}
		}
		
		return false;
	}
}

/**
 *	Check if the current post/page is the Manage Subscriptions page.	
 *
 *
 */
if (!function_exists('wpml_is_management')) {
	function wpml_is_management() {		
		if (function_exists('WPMAIL')) {			
			if (defined('NEWSLETTERS_IS_MANAGEMENT')) {
				return true;	
			}
			
			if ($post_id = WPMAIL() -> get_managementpost(false)) {				
				if (is_page($post_id) || is_single($post_id)) {
					return true;
				}
			}
			
			if (WPMAIL() -> is_plugin_active('wpml')) {
				if ($otherpost = icl_object_id($post_id, 'page', false)) {
					if (is_page($otherpost) || is_single($otherpost)) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
}

if (!function_exists('newsletters_hardcode')) {
	function newsletters_hardcode($list = "select", $lists = null, $atts = array(), $form_id = null) {
		if (class_exists('wpMail')) {
			$wpMail = new wpMail();
			$wpMail -> hardcoded($list, $lists, $atts, $form_id);
		}
	}
}

if (!function_exists('wpml_get_mailinglists')) {
	function wpml_get_mailinglists($args = array()) {
		global $wpdb, $Mailinglist, $wpMail;
		
		$defaults = array('conditions' => null);
		$r = wp_parse_args($args, $defaults);
		extract($r, EXTR_SKIP);
		
		$lists = array();
		
		$query = "SELECT * FROM `" . $wpdb -> prefix . $Mailinglist -> table . "`";
		
		if (!empty($conditions)) {
			$query .= " WHERE";
			$c = 1; 
			
			foreach ($conditions as $ckey => $cval) {
				$query .= " " . $ckey . " = '" . esc_sql($cval) . "'";
				if ($c < count($conditions)) { $query .= " AND"; }
				$c++;
			}
		}
		
		$query .= " ORDER BY `title` ASC";
		
		if ($mailinglists = $wpdb -> get_results($query)) {
			foreach ($mailinglists as $mailinglist) {
				$lists[] = $wpMail -> init_class($Mailinglist -> model, $mailinglist);
			}
		}
		
		return $lists;
	}
}

if (!function_exists('newsletters_get_snippets')) {
	function newsletters_get_snippets() {
		global $wpdb, $Db;
			
		if (class_exists('wpMail')) {	
			$wpMail = new wpMail();
			
			if ($snippets = $wpMail -> Template() -> find_all()) {
				return $snippets;
			}
		}
		
		return false;
	}
}

if (!function_exists('newsletters_get_templates')) {
	function newsletters_get_templates($conditions = null, $fields = null, $order = null, $limit = null) {
		return wpml_get_themes($conditions, $fields, $order, $limit);
	}
}

if (!function_exists('wpml_get_themes')) {
	function wpml_get_themes($conditions = null, $fields = null, $order = null, $limit = null) {
		$themes = array();
		
		global $wpdb, $Db, $Theme;
		$Db -> model = $Theme -> model;
		
		if ($themes = $Db -> find_all($conditions, $fields, $order, $limit)) {
			//do nothing...
		}
		
		return $themes;
	}
}

if (!function_exists('newsletters_get_fields')) {
	function newsletters_get_fields($args = array()) {
		return wpml_get_fields($args);
	}
}

if (!function_exists('wpml_get_fields')) {
	function wpml_get_fields($args = array()) {
		global $wpdb, $Field, $wpMail;
		
		$defaults = array('conditions' => null, 'conditions_join' => "AND");
		$r = wp_parse_args($args, $defaults);
		extract($r, EXTR_SKIP);
	
		$query = "SELECT * FROM `" . $wpdb -> prefix . $Field -> table . "`";
		
		if (!empty($conditions)) {
			$query .= " WHERE";
			$c = 1;
		
			foreach ($conditions as $ckey => $cval) {
				$query .= " " . $ckey . " = '" . esc_sql($cval) . "'";
				if ($c < count($conditions)) { $query .= " " . $conditions_join; }
				
				$c++;
			}
		}
		
		$query .= " ORDER BY `order` ASC";
		
		if ($customfields = $wpdb -> get_results($query)) {
			$fields = $customfields;
		}
		
		return $fields;
	}
}

if (!function_exists('wpml_get_subscriber')) {
	function wpml_get_subscriber($subscriber_id = null) {
		if (!empty($subscriber_id)) {
			global $wpdb, $Db, $Subscriber;
			
			$Db -> model = $Subscriber -> model;
			
			if ($subscriber = $Db -> find(array('id' => $subscriber_id))) {
				return $subscriber;
			}
		}
		
		return false;
	}
}

?>