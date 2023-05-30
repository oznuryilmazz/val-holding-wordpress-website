<?php

if (!class_exists('wpmlAuthHelper')) {
class wpmlAuthnewsHelper extends wpMailPlugin {

	var $name = 'Authnews';
	var $cookiename = 'subscriberauth';
	var $emailcookiename = 'subscriberemailauth';

	function logged_in($subscriber_id = null) {
		global $wpdb, $Db, $Subscriber;
		
		$user_id = false;	
		if (is_user_logged_in()) {
			$user_id = get_current_user_id();
		}
		
		$subscriberauth = $this -> read_cookie();
		$Db -> model = $Subscriber -> model;
		
		if (!empty($subscriber_id) && $subscriber = $Db -> find(array('id' => $subscriber_id), false, false, true, true, false)) {
			return $subscriber;
		} elseif (!empty($subscriberauth) && $subscriber = $Db -> find(array('cookieauth' => $subscriberauth), false, false, true, true, false)) {			
			return $subscriber;
		} elseif (!empty($user_id) && $subscriber = $Db -> find(array('user_id' => $user_id))) {
			return $subscriber;
		}

		return false;
	}

	function read_cookie($create = false) {
		$managementauthtype = $this -> get_option('managementauthtype');

		switch ($managementauthtype) {
			case 1			:
				if (isset($_COOKIE[$this -> cookiename])) {
					return sanitize_text_field(wp_unslash($_COOKIE[$this -> cookiename]));
				}
				break;
			case 2			:
				if (isset($_SESSION[$this -> cookiename])) {
					return sanitize_text_field(wp_unslash($_SESSION[$this -> cookiename]));
				}
				break;
			case 3			:
			default 		:
				if (isset($_COOKIE[$this -> cookiename])) {
					return sanitize_text_field(wp_unslash($_COOKIE[$this -> cookiename]));
				} elseif (isset($_SESSION[$this -> cookiename])) {
					return sanitize_text_field(wp_unslash($_SESSION[$this -> cookiename]));
				}
				break;
		}

		return false;
	}

	function read_emailcookie() {
		$managementauthtype = $this -> get_option('managementauthtype');

		switch ($managementauthtype) {
			case 1					:
				if (isset($_COOKIE[$this -> emailcookiename])) {
					return sanitize_text_field(wp_unslash($_COOKIE[$this -> emailcookiename]));
				}
				break;
			case 2					:
				if (isset($_SESSION[$this -> emailcookiename])) {
					return sanitize_text_field(wp_unslash($_SESSION[$this -> emailcookiename]));
				}
				break;
			case 3					:
			default 				:
				if (isset($_COOKIE[$this -> emailcookiename])) {
					return sanitize_text_field(wp_unslash($_COOKIE[$this -> emailcookiename]));
				} elseif (isset($_SESSION[$this -> emailcookiename])) {
					return sanitize_text_field(wp_unslash($_SESSION[$this -> emailcookiename]));
				}
				break;
		}

		return false;
	}

	function write_db() {

	}

	function set_emailcookie($email = null, $days = "+30 days") {
		if (is_feed()) {
			return false;
		}

		$managementauthtype = $this -> get_option('managementauthtype');

		if (!empty($email)) {
			switch ($managementauthtype) {
				case 1					:
                    //phpcs:ignore
					if (!empty($_COOKIE[$this -> emailcookiename]) && $_COOKIE[$this -> emailcookiename]) {
						return true;
					}

					if (!headers_sent()) {
						setcookie($this -> emailcookiename, $email, strtotime($days), '/');
					} else {
						$this -> javascript_cookie($this -> emailcookiename, $email);
					}

					$_COOKIE[$this -> emailcookiename] = $email;
					break;
				case 2					:
					$_SESSION[$this -> emailcookiename] = $email;
					break;
				case 3					:
				default 				:
                    //phpcs:ignore
					if (!empty($_COOKIE[$this -> emailcookiename]) && $_COOKIE[$this -> emailcookiename]) {
						return true;
					}

					if (!headers_sent()) {
						setcookie($this -> emailcookiename, $email, strtotime($days), '/');
					} else {
						$this -> javascript_cookie($this -> emailcookiename, $email);
					}

					$_COOKIE[$this -> emailcookiename] = $email;
					$_SESSION[$this -> emailcookiename] = $email;
					break;
			}
		}

		return false;
	}

	function set_cookie($value = null, $days = "+30 days") {
		if (is_feed()) {
			return false;
		}

		$managementauthtype = $this -> get_option('managementauthtype');

		if (!empty($value)) {
			switch ($managementauthtype) {
				case 1						:
					if (!empty($_COOKIE[$this -> cookiename]) && $_COOKIE[$this -> cookiename] == $value) {
						return true;
					}

					if (!headers_sent()) {
						setcookie($this -> cookiename, $value, strtotime($days), '/');
					} else {
						$this -> javascript_cookie($this -> cookiename, $value);
					}

					$_COOKIE[$this -> cookiename] = $value;
					break;
				case 2						:
					$_SESSION[$this -> cookiename] = $value;
					break;
				case 3						:
				default 					:
					if (!empty($_COOKIE[$this -> cookiename]) && $_COOKIE[$this -> cookiename] == $value) {
						return true;
					}

					if (!headers_sent()) {
						setcookie($this -> cookiename, $value, strtotime($days), '/');
					} else {
						$this -> javascript_cookie($this -> cookiename, $value);
					}

					$_COOKIE[$this -> cookiename] = $value;
					$_SESSION[$this -> cookiename] = $value;
					break;
			}
		}

		return true;
	}

	function delete_cookie($cookiename = null, $cookievalue = null) {		
		if (!headers_sent() ) {
			unset($_COOKIE[$cookiename]);
			//setcookie($cookiename, $cookievalue, current_time('timestamp') - 3600, '/');
			setcookie($cookiename, null, -1, '/');
		} else {
			$this -> javascript_cookie($cookiename, $cookievalue, true);
		}
	}

	function javascript_cookie($cookiename = null, $value = null, $delete = false) {
		if (!empty($cookiename) && !empty($value)) {
			global $wpmljavascript;
			ob_start();

			?>

			<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php if (!empty($delete)) : ?>
					datum = new Date();
					datum.setTime(datum.getTime() - 7 *24*60*60*1000);
					document.cookie = "<?php echo esc_html( $cookiename); ?>=<?php echo esc_html( $value); ?>; expires="  + datum.toUTCString();
				<?php else : ?>
					datum = new Date();
					datum.setTime(datum.getTime() + 7 *24*60*60*1000);
					document.cookie = "<?php echo esc_html( $cookiename); ?>=<?php echo esc_html( $value); ?>; expires=" + datum.toUTCString();
				<?php endif; ?>
			});
			</script>

			<?php

			$newjavascript = ob_get_clean();
			$wpmljavascript .= $newjavascript;
			return $wpmljavascript;
		}

		return false;
	}

	function gen_subscriberauth() {
		$subscriberauth = md5(microtime());
		return $subscriberauth;
	}
}
}

?>