<?php

class NewsletterAntispam {

    var $options;
    var $logger;

    public static function instance() {
        static $instance;
        if (!$instance) {
            $instance = new NewsletterAntispam();
        }
        return $instance;
    }

    public function __construct() {
        $this->options = NewsletterSubscription::instance()->get_options('antibot');
        $this->logger = new NewsletterLogger('antispam');
    }

    /**
     * $email must be cleaned using the is_email() function.
     *
     * @param TNP_Subscription $subscription
     */
    function is_spam($subscription) {

        $email = $subscription->data->email;
        $ip = $subscription->data->ip;


        $full_name = $subscription->data->name . ' ' . $subscription->data->surname;
        if ($this->is_spam_text($full_name)) {
            $this->logger->fatal($email . ' - ' . $ip . ' - Name with http: ' . $full_name);
            return true;
        }

        if ($this->is_ip_blacklisted($ip)) {
            $this->logger->fatal($email . ' - ' . $ip . ' - IP blacklisted');
            return true;
        }

        if ($this->is_address_blacklisted($email)) {
            $this->logger->fatal($email . ' - ' . $ip . ' - Address blacklisted');
            return true;
        }

        // Akismet check
	    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	    $referrer   = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
	    if ( $this->is_spam_by_akismet( $email, $full_name, $ip, $user_agent, $referrer ) ) {
		    $this->logger->fatal( $email . ' - ' . $ip . ' - Akismet blocked' );

		    return true;
	    }

        // Flood check
        if ($this->is_flood($email, $ip)) {
            $this->logger->fatal($email . ' - ' . $ip . ' - Antiflood triggered');
            return true;
        }

//        if ($this->is_missing_domain_mx($email)) {
//            $this->logger->fatal($email . ' - ' . $ip . ' - MX check failed');
//            header("HTTP/1.0 404 Not Found");
//            return true;
//        }

        return false;
    }

    function is_address_blacklisted($email) {

        if (empty($this->options['address_blacklist'])) {
            return false;
        }

        $this->logger->debug('Address blacklist check');
        $rev_email = strrev($email);
        foreach ($this->options['address_blacklist'] as $item) {
            if (strpos($rev_email, strrev($item)) === 0) {
                return true;
            }
        }
        return false;
    }

    function is_ip_blacklisted($ip) {
        
        if ($ip === '::1' || $ip === '127.0.0.1') {
            return false;
        }

        if (empty($this->options['ip_blacklist'])) {
            return false;
        }
        $this->logger->debug('IP blacklist check');
        foreach ($this->options['ip_blacklist'] as $item) {
            if (substr($item, 0, 1) === '#') {
                continue;
            }
            if ($this->ip_match($ip, $item)) {
                return true;
            }
        }
        return false;
    }

    function is_missing_domain_mx($email) {
        // Actually not fully implemented
        return false;

        if (empty($this->options['domain_check'])) {
            return false;
        }

        $this->logger->debug('Domain MX check');
        list($local, $domain) = explode('@', $email);

        $hosts = array();
        if (!getmxrr($domain, $hosts)) {
            return true;
        }
        return false;
    }

    function is_flood($email, $ip) {
        global $wpdb;

        if (empty($this->options['antiflood'])) {
            return false;
        }

        $this->logger->debug('Antiflood check');

        $updated = $wpdb->get_var($wpdb->prepare("select updated from " . NEWSLETTER_USERS_TABLE . " where ip=%s or email=%s order by updated desc limit 1", $ip, $email));

        if ($updated && time() - $updated < $this->options['antiflood']) {
            return true;
        }

        return false;
    }

    function is_spam_text($text) {
        if (stripos($text, 'http:') !== false || stripos($text, 'https:') !== false) {
            return true;
        }
        if (stripos($text, 'www.') !== false) {
            return true;
        }
        if (preg_match('|[^\s\.]+\.[^\s\.]+\.[^\s\.]{2,}|', $text)) {
            return true;
        }

        return false;
    }

    function is_spam_by_akismet($email, $name, $ip, $agent, $referrer) {

        if (!class_exists('Akismet')) {
            return false;
        }

        if (empty($this->options['akismet'])) {
            return false;
        }

        $this->logger->debug('Akismet check');
        $request = 'blog=' . urlencode(home_url()) . '&referrer=' . urlencode($referrer) .
                '&user_agent=' . urlencode($agent) .
                '&comment_type=signup' .
                '&comment_author_email=' . urlencode($email) .
                '&user_ip=' . urlencode($ip);
        if (!empty($name)) {
            $request .= '&comment_author=' . urlencode($name);
        }

        $response = Akismet::http_post($request, 'comment-check');

        if ($response && $response[1] == 'true') {
            return true;
        }
        return false;
    }

    function ip_match($ip, $range) {
        if (empty($ip))
            return false;
        if (strpos($range, '/')) {
            list ($subnet, $bits) = explode('/', $range);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
            return ($ip & $mask) == $subnet;
        } else {
            return strpos($range, $ip) === 0;
        }
    }

}
