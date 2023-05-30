<?php

defined('ABSPATH') || exit;

class NewsletterUnsubscription extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterUnsubscription
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterUnsubscription();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('unsubscription', '1.0.4');

        add_filter('newsletter_replace', array($this, 'hook_newsletter_replace'), 10, 4);
        add_filter('newsletter_page_text', array($this, 'hook_newsletter_page_text'), 10, 3);
        add_filter('newsletter_message_headers', array($this, 'hook_add_unsubscribe_headers_to_email'), 10, 3);

        add_action('newsletter_action', array($this, 'hook_newsletter_action'), 11, 3);
    }

    function upgrade() {
        parent::upgrade();

        if (!empty($this->options['notify_admin_on_unsubscription'])) {
            unset($this->options['notify_admin_on_unsubscription']);
            $this->options['notify'] = '1';
            $this->options['notify_email'] = get_option('admin_email');
            $this->save_options($this->options);
        }
    }

    function hook_newsletter_action($action, $user, $email) {

        if (in_array($action, ['u', 'uc', 'lu', 'reactivate'])) {
            if (!$user) {
                $this->dienow(__('Subscriber not found', 'newsletter'), 'Already deleted or using the wrong subscriber key in the URL', 404);
            }
        }

//        if ($action === 'u' && empty($this->options['optout'])) {
//            $action = 'uc';
//        }

        switch ($action) {
            case 'u':
                $url = $this->build_message_url(null, 'unsubscribe', $user, $email);
                wp_redirect($url);
                die();
                break;

            case 'lu': //Left for backwards compatibility, could be removed after some time
            case 'uc':
                if (isset($_POST['List-Unsubscribe']) && 'One-Click' === $_POST['List-Unsubscribe']) {
                    $this->unsubscribe($user, $email);
                } else if ($this->antibot_form_check()) {
                    $this->unsubscribe($user, $email);
                    $url = $this->build_message_url(null, 'unsubscribed', $user, $email);
                    wp_redirect($url);
                } else {
                    $this->request_to_antibot_form('Unsubscribe');
                }
                die();
                break;

            case 'reactivate':
                if ($this->antibot_form_check()) {
                    $this->reactivate($user);
                    $url = $this->build_message_url(null, 'reactivated', $user);
                    setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
                    wp_redirect($url);
                } else {
                    $this->request_to_antibot_form('Reactivate');
                }
                die();
                break;
        }
    }

    /**
     * Unsubscribes the subscriber from the request. Die on subscriber extraction failure.
     *
     * @return TNP_User
     */
    function unsubscribe($user, $email = null) {
        global $wpdb;

        if ($user->status == TNP_User::STATUS_UNSUBSCRIBED) {
            return $user;
        }

        $this->refresh_user_token($user);
        $this->set_user_status($user, TNP_User::STATUS_UNSUBSCRIBED);

        $this->add_user_log($user, 'unsubscribe');

        do_action('newsletter_user_unsubscribed', $user);

        if ($email) {
            $wpdb->update(NEWSLETTER_USERS_TABLE, array('unsub_email_id' => (int) $email->id, 'unsub_time' => time()), array('id' => $user->id));
        }

        $this->send_unsubscribed_email($user);

        $this->notify_admin($user);

        return $user;
    }

    function send_unsubscribed_email($user, $force = false) {
        $options = $this->get_options('', $this->get_user_language($user));
        if (!$force && !empty($options['unsubscribed_disabled'])) {
            return true;
        }

        $message = do_shortcode($options['unsubscribed_message']);
        $subject = $options['unsubscribed_subject'];

        return NewsletterSubscription::instance()->mail($user, $subject, $message);
    }

    function notify_admin($user) {

        if (empty($this->options['notify'])) {
            return;
        }

        $message = $this->generate_admin_notification_message($user);
        $email = trim($this->options['notify_email']);
        $subject = $this->generate_admin_notification_subject('New cancellation');

        Newsletter::instance()->mail($email, $subject, ['html' => $message]);
    }

    /**
     * Reactivate the subscriber extracted from the request setting his status
     * to confirmed and logging. No email are sent. Dies on subscriber extraction failure.
     *
     * @return TNP_User
     */
    function reactivate($user = null) {
        // For compatibility, to be removed
        if (!$user) {
            $user = $this->get_user_from_request(true);
        }
        $this->set_user_status($user, TNP_User::STATUS_CONFIRMED);
        $this->add_user_log($user, 'reactivate');
        do_action('newsletter_user_reactivated', $user);
    }

    function hook_newsletter_replace($text, $user, $email, $html = true) {

        if ($user) {
            $text = $this->replace_url($text, 'unsubscription_confirm_url', $this->build_action_url('uc', $user, $email));
            $text = $this->replace_url($text, 'unsubscription_url', $this->build_action_url('u', $user, $email));
            $text = $this->replace_url($text, 'reactivate_url', $this->build_action_url('reactivate', $user, $email));
        } else {
            $text = $this->replace_url($text, 'unsubscription_confirm_url', $this->build_action_url('nul'));
            $text = $this->replace_url($text, 'unsubscription_url', $this->build_action_url('nul'));
        }

        return $text;
    }

    function hook_newsletter_page_text($text, $key, $user = null) {

        $options = $this->get_options('', $this->get_current_language($user));
        if ($key == 'unsubscribe') {
            if (!$user) {
                return 'Subscriber not found.';
            }
            return $options['unsubscribe_text'];
        }
        if ($key == 'unsubscribed') {
            if (!$user) {
                return $options['error_text'];
            }
            return $options['unsubscribed_text'];
        }
        if ($key == 'reactivated') {
            if (!$user) {
                return $options['error_text'];
            }
            return $options['reactivated_text'];
        }
        if ($key == 'unsubscription_error') {
            return $options['error_text'];
        }
        return $text;
    }

    function admin_menu() {
        $this->add_admin_page('index', 'Unsubscribe');
        if (!$this->is_multilanguage()) {
            $this->add_admin_page('indexnew', 'Unsubscribe');
        }
    }

    /**
     * @param array $headers
     * @param TNP_Email $email
     * @param TNP_User $user
     *
     * @return array
     */
    function hook_add_unsubscribe_headers_to_email($headers, $email, $user) {

        if (!empty($this->options['disable_unsubscribe_headers'])) {
            return $headers;
        }

        $list_unsubscribe_values = [];
        if (!empty($this->options['list_unsubscribe_mailto_header'])) {
            $unsubscribe_address = $this->options['list_unsubscribe_mailto_header'];
            $list_unsubscribe_values[] = "<mailto:$unsubscribe_address?subject=unsubscribe>";
        }

        $unsubscribe_action_url = $this->build_action_url('uc', $user, $email);
        $list_unsubscribe_values[] = "<$unsubscribe_action_url>";

        $headers['List-Unsubscribe'] = implode(', ', $list_unsubscribe_values);
        $headers['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';

        return $headers;
    }

}

NewsletterUnsubscription::instance();
