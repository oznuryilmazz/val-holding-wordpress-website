<?php
	
if (!class_exists('ReCaptcha')) {
	class ReCaptcha extends wpMailPlugin {
		
		var $api_url = 'https://www.google.com/recaptcha/api/siteverify';
		var $secret = '';
		var $errors = array();
		
		function __construct($secret = null) {
			$this -> secret = $secret;
		}
		
		function verify($response = null, $ip_address = null) {
			global $Html;
			
			$ip_address = (empty($ip_address)) ? $_SERVER['REMOTE_ADDR'] : $ip_address;
			
			$args = array(
				'secret'			=>	$this -> secret,
				'response'			=>	$response,
				'remoteip'			=>	$ip_address,
			);
			
			$response = wp_remote_post($this -> api_url, array('body' => $args, 'timeout' => 120));
			
			if (!is_wp_error($response)) {
				if (!empty($response['body'])) {
					$body = json_decode($response['body']);
					
					if (!empty($body -> success) && $body -> success == true) {
						return true;
					} else {
						if (!empty($body -> {'error-codes'})) {
							foreach ($body -> {'error-codes'} as $error_code) {
								$this -> errors[] = $Html -> reCaptchaErrorMessage($error_code);
							}
						} else {
							$this -> errors[] = __('Captcha failed, try again', 'wp-mailinglist');
						}
					}
				} else {
					$this -> errors[] = __('Captcha response was empty', 'wp-mailinglist');
				}
			} else {
				$this -> errors[] = $response -> get_error_message();
			}
			
			return false;
		}
	}
}	
	
?>