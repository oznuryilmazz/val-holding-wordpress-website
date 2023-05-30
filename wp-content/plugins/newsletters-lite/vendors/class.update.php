<?php

class wpmlupdate extends wpMailPlugin {
	
	function __construct() {
		
	}

	function get_changelog(){
	
		$options = array('method' => 'POST', 'timeout' => 120);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => home_url(),
        );
        
		$request_url = NEWSLETTERS_MANAGER_URL . 'changelog/1/' . $this -> get_remote_request_params();
		$raw_response = wp_remote_request($request_url, $options);
		
		if (is_wp_error($raw_response) || 200 != $raw_response['response']['code']) {
            $text = __('Something went wrong!', 'wp-mailinglist'); 
            $text .= __('Please try again or <a href="http://tribulant.com/support/" target="_blank">contact us</a>.', 'wp-mailinglist');
            return $text;
        } else {
            $changelog = $raw_response['body'];
            return wp_unslash($changelog);
        }
    }

	function get_version_info($cache = true) {	
		$raw_response = get_transient('newsletters_update_info');
		
		// Check if cached data should be used
		if (empty($cache)) { 
			$raw_response = false; 
		}
	
		if (empty($raw_response)) {		
			$options = array('method' => 'POST', 'timeout' => 120);
	        $options['headers'] = array(
	            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
	            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
	            'Referer' => home_url()
	        );
	        
			$request_url = NEWSLETTERS_MANAGER_URL . 'updates/1/' . $this -> get_remote_request_params();
			$raw_response = wp_remote_request($request_url, $options);
			
			set_transient('newsletters_update_info', $raw_response, (6 * HOUR_IN_SECONDS));
		}
		
		if (is_wp_error($raw_response) || 200 != $raw_response['response']['code']) {
            return array("is_valid_key" => "1", "version" => "", "url" => "");
        } else {
        	$array = explode("||", $raw_response['body']);
        	$url = $array[2];
        	$info = array("is_valid_key" => $array[0], "version" => $array[1], "url" => $url, "item_id" => $array[4]);
			
			if(count($array) == 4) {
				$info["expiration_time"] = $array[3];
			}
			
			$info['dtype'] = $array[5];

            return $info;
        }
	}
	
	function get_remote_request_params(){
        global $wpdb;
        return sprintf("p:%s/key:%s/v:%s/wp:%s/php:%s/mysql:%s", urlencode('1'), urlencode(apply_filters('newsletters_get_option', $this -> get_option('serialkey'), 'serialkey')), urlencode($this -> version), urlencode(get_bloginfo("version")), urlencode(phpversion()), urlencode($wpdb -> db_version()));
    }
}

?>