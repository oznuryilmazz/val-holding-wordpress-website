<?php

/*
 * TNP classes for internal API
 *
 * Error reference
 * 404	Object not found
 * 403	Not allowed (when the API key is missing or wrong)
 * 400	Bad request, when the parameters are not correct or required parameters are missing
 *
 */

/**
 * Main API functions
 *
 * @author roby
 */
class TNP {
    /*
     * The full process of subscription
     */

    public static function subscribe($params) {
        
        if ($params instanceof TNP_Subscription) {
            return NewsletterSubscription::instance()->subscribe2($params);
        }

        $logger = new NewsletterLogger('phpapi');
        $logger->debug($params);

        apply_filters('newsletter_api_subscribe', $params);
        
        $newsletter = Newsletter::instance();
        
        $subscription = NewsletterSubscription::instance()->get_default_subscription();
        $subscription->spamcheck = isset($params['spamcheck']);
        $data = $subscription->data;
        
        $subscription->send_emails = !empty($params['send_emails']);

        // Form field configuration
        $options_profile = get_option('newsletter_profile', array());
        
        $data->email = $params['email'];

        if (isset($params['name'])) {
            $data->name = $params['name'];
        }

        if (isset($params['surname'])) {
            $data->surname = $params['surname'];
        }
        
        // Lists
        if (isset($params['lists']) && is_array($params['lists'])) {
            $public_lists = array_keys($newsletter->get_lists_public());
            $list_ids = array_intersect($public_lists, $params['lists']);
            
            foreach ($list_ids as $list_id) {
                $data->lists['' . $list_id] = 1;
            }
        } 
        
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // If the profile cannot be set by  subscriber, skip it.
            if ($options_profile['profile_' . $i . '_status'] == 0) {
                continue;
            }
            if (isset($params['profile_' . $i])) {
                $data->profiles['' . $i] = stripslashes($params['profile_' . $i]);
            }
        }

        $data->ip = $newsletter->get_remote_ip();

        $user = NewsletterSubscription::instance()->subscribe2($subscription);

        return $user;
    }

    /*
     * The UNsubscription
     */

    public static function unsubscribe($params) {

        $newsletter = Newsletter::instance();
        $user = $newsletter->get_user($params['email']);

        //        $newsletter->logger->debug($params);

        if (!$user) {
            return new WP_Error('-1', 'Email address not found', array('status' => 404));
        }

        if ($user->status == TNP_User::STATUS_UNSUBSCRIBED) {
            return;
        }

        $user = $newsletter->set_user_status($user, 'U');
        $newsletter->add_user_log($user, 'unsubscribe');

        NewsletterUnsubscription::instance()->send_unsubscribed_email($user);

	    NewsletterUnsubscription::instance()->notify_admin($user);

        do_action('newsletter_unsubscribed', $user);

        return;
    }

    /*
     * Adds a subscriber if not already in
     */

    public static function add_subscriber($params) {

        $newsletter = Newsletter::instance();
        $subscription = NewsletterSubscription::instance();

        $email = $newsletter->normalize_email(stripslashes($params['email']));

        if (!$email) {
            return new WP_Error('-1', 'Email address not valid', array('status' => 400));
        }

        $user = $newsletter->get_user($email);

        if ($user) {
            return new WP_Error('-1', 'Email address already exists', array('status' => 400));
        }

        $user = array('email' => $email);

        if (isset($params['name'])) {
            $user['name'] = $newsletter->normalize_name(stripslashes($params['name']));
        }

        if (isset($params['surname'])) {
            $user['surname'] = $newsletter->normalize_name(stripslashes($params['surname']));
        }

        if (!empty($params['gender'])) {
            $user['sex'] = $newsletter->normalize_sex($params['gender']);
        }

	    if (!empty($params['country'])) {
		    $user['country'] = sanitize_text_field($params['country']);
	    }

	    if (!empty($params['region'])) {
		    $user['region'] = sanitize_text_field($params['region']);
	    }

	    if (!empty($params['city'])) {
		    $user['city'] = sanitize_text_field($params['city']);
	    }

        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i ++) {
            if (isset($params['profile_' . $i])) {
                $user['profile_' . $i] = trim(stripslashes($params['profile_' . $i]));
            }
        }

        // Lists (an array under the key "lists")
        //(field names are nl[] and values the list number so special forms with radio button can work)
        if (isset($params['lists']) && is_array($params['lists'])) {
            foreach ($params['lists'] as $list_id) {
                $user['list_' . ( (int) $list_id )] = 1;
            }
        }


        if (!empty($params['status'])) {
            $user['status'] = $params['status'];
        } else {
            $user['status'] = 'C';
        }

        if (!empty($params['language'])) {
            $user['language'] = $params['language'];
        }

        $user['token'] = $newsletter->get_token();
        $user['updated'] = time();

        $user['ip'] = Newsletter::get_remote_ip();

        $user = $newsletter->save_user($user);

        return $user;
    }

    /*
     * Subscribers list
     */

    public static function subscribers($params) {

        global $wpdb;
        $newsletter = Newsletter::instance();

        $items_per_page = 20;
        $where = "";

        $query = "select name, email from " . NEWSLETTER_USERS_TABLE . ' ' . $where . " order by id desc";
        $query .= " limit 0," . $items_per_page;
        $list = $wpdb->get_results($query);

        return $list;
    }

    /*
     * Deletes a subscriber
     */

    public static function delete_subscriber($params) {

        global $wpdb;
        $newsletter = Newsletter::instance();

        $user = $newsletter->get_user($params['email']);

        if (!$user) {
            return new WP_Error('-1', 'Email address not found', array('status' => 404));
        }

        if ($wpdb->query($wpdb->prepare("delete from " . NEWSLETTER_USERS_TABLE . " where id=%d", (int) $user->id))) {
            return "OK";
        } else {
            $newsletter->logger->debug($wpdb->last_query);

            return new WP_Error('-1', $wpdb->last_error, array('status' => 400));
        }
    }

    /*
     * Newsletters list
     */

    public static function newsletters($params) {

        global $wpdb;
        $newsletter = Newsletter::instance();

        $list = $wpdb->get_results("SELECT id, subject, created, status, total, sent, send_on FROM " . NEWSLETTER_EMAILS_TABLE . " ORDER BY id DESC LIMIT 10", OBJECT);

        if ($wpdb->last_error) {
            $newsletter->logger->error($wpdb->last_error);

            return false;
        }

        if (empty($list)) {
            return array();
        }

        return $list;
    }

}
