<?php

defined('ABSPATH') || exit;

class NewsletterSubscription extends NewsletterModule {

    const MESSAGE_CONFIRMED = 'confirmed';
    const OPTIN_DOUBLE = 0;
    const OPTIN_SINGLE = 1;

    static $instance;

    /**
     * @var array
     */
    var $options_profile;

    /**
     * Contains the options for the current language to build a subscription form. Must be initialized with
     * setup_form_options() before use.
     *
     * @var array
     */
    var $form_options = null;

    /**
     * Contains the antibot/antispam options. Must be initialized with
     * setup_antibot_options() before use.
     *
     * @var array
     */
    var $antibot_options = null;

    /**
     * @var array
     */
    var $options_lists;

    /**
     * @return NewsletterSubscription
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterSubscription();
        }
        return self::$instance;
    }

    static $sources = [];

    static function register_source($source) {
        self::$sources[] = $source;
    }

    function __construct() {

        parent::__construct('subscription', '2.2.7', null, array('lists', 'template', 'profile', 'antibot'));
        $this->options_profile = $this->get_options('profile');
        $this->options_lists = $this->get_options('lists');

        // Must be called after the Newsletter::hook_init, since some constants are defined
        // there.
        add_action('init', array($this, 'hook_init'), 90);
    }

    function hook_init() {
        add_action('newsletter_action', array($this, 'hook_newsletter_action'), 10, 3);
        if (is_admin()) {
            add_action('admin_init', array($this, 'hook_admin_init'));
        } else {
            // Shortcode for the Newsletter page
            add_shortcode('newsletter', array($this, 'shortcode_newsletter'));
            add_shortcode('newsletter_form', array($this, 'shortcode_newsletter_form'));
            add_shortcode('newsletter_field', array($this, 'shortcode_newsletter_field'));
        }
    }

    function hook_admin_init() {
        // So the user can add JS and other code
        if (isset($_GET['page']) && $_GET['page'] === 'newsletter_subscription_forms') {
            header('X-XSS-Protection: 0');
        }

        if (function_exists('register_block_type')) {
            // Add custom blocks to Gutenberg
            wp_register_script('tnp-blocks', plugins_url('newsletter') . '/includes/tnp-blocks.js', array('wp-block-editor', 'wp-blocks', 'wp-element', 'wp-components'), NEWSLETTER_VERSION);
            register_block_type('tnp/minimal', array('editor_script' => 'tnp-blocks'));
        }
    }

    /**
     *
     * @global wpdb $wpdb
     * @return mixed
     */
    function hook_newsletter_action($action, $user, $email) {
        global $wpdb;

        switch ($action) {
            case 'profile-change':
                if ($this->antibot_form_check()) {

                    if (!$user || $user->status != TNP_user::STATUS_CONFIRMED) {
                        $this->dienow('Subscriber not found or not confirmed.', 'Even the wrong subscriber token can lead to this error.', 404);
                    }

                    if (!$email) {
                        $this->dienow('Newsletter not found', 'The newsletter containing the link has been deleted.', 404);
                    }

                    if (isset($_REQUEST['list'])) {
                        $list_id = (int) $_REQUEST['list'];

                        // Check if the list is public
                        $list = $this->get_list($list_id);
                        if (!$list || $list->status == TNP_List::STATUS_PRIVATE) {
                            $this->dienow('List change not allowed.', 'Please check if the list is marked as "private".', 400);
                        }

                        if (empty($_REQUEST['redirect'])) {
                            $url = home_url();
                        } else {
                            $url = esc_url_raw($_REQUEST['redirect']);
                        }
                        $this->set_user_list($user, $list_id, $_REQUEST['value']);

                        $user = $this->get_user($user->id);
                        $this->add_user_log($user, 'cta');
                        NewsletterStatistics::instance()->add_click($url, $user->id, $email->id);
                        wp_redirect($url);
                        die();
                    }
                } else {
                    $this->request_to_antibot_form('Continue');
                }

                die();

            case 'm':
            case 'message':
                include dirname(__FILE__) . '/page.php';
                die();

            // normal subscription
            case 's':
            case 'subscribe':

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $this->dienow('Invalid request', 'The subscription request was not made with a HTTP POST', 400);
                }

                $options_antibot = $this->get_options('antibot');

                $captcha = !empty($options_antibot['captcha']);

                if (!empty($_GET['_wp_amp_action_xhr_converted']) || !empty($options_antibot['disabled']) || $this->antibot_form_check($captcha)) {

                    $subscription = $this->build_subscription();

                    $user = $this->subscribe2($subscription);

                    if (is_wp_error($user)) {
                        if ($user->get_error_code() === 'exists') {
                            $language = isset($_REQUEST['nlang']) ? $_REQUEST['nlang'] : '';
                            $options = $this->get_options('', $language);
                            $this->dienow($options['error_text'], $user->get_error_message(), 200);
                        }
                        $this->dienow('Registration failed.', $user->get_error_message(), 400);
                    }

                    if ($user->status == TNP_User::STATUS_CONFIRMED) {
                        $this->show_message('confirmed', $user);
                    }
                    if ($user->status == TNP_User::STATUS_NOT_CONFIRMED) {
                        $this->show_message('confirmation', $user);
                    }
                } else {
                    $language = isset($_REQUEST['nlang']) ? $_REQUEST['nlang'] : '';
                    $options = $this->get_form_options($language);
                    $this->request_to_antibot_form($options['subscribe'], $captcha);
                }
                die();

            // AJAX subscription
            case 'ajaxsub':

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $this->dienow('Invalid request');
                }

                $subscription = $this->build_subscription();

                $user = $this->subscribe2($subscription);

                if (is_wp_error($user)) {
                    if ($user->get_error_code() === 'exists') {
                        echo $this->options['error_text'];
                        die();
                    } else {
                        $this->dienow('Registration failed.', $user->get_error_message(), 400);
                    }
                } else {
                    if ($user->status == TNP_User::STATUS_CONFIRMED) {
                        $key = 'confirmed';
                    }

                    if ($user->status == TNP_User::STATUS_NOT_CONFIRMED) {
                        $key = 'confirmation';
                    }
                }

                $message = $this->replace($this->options[$key . '_text'], $user);
                if (isset($this->options[$key . '_tracking'])) {
                    $message .= $this->options[$key . '_tracking'];
                }
                echo $message;
                die();

            case 'c':
            case 'confirm':
                if (!$user) {
                    $this->dienow(__('Subscriber not found.', 'newsletter'), 'Or it is not present or the secret key does not match.', 404);
                }

                if ($this->antibot_form_check()) {
                    $user = $this->confirm($user);
                    $this->set_user_cookie($user);
                    $this->show_message('confirmed', $user);
                } else {
                    $this->request_to_antibot_form('Confirm');
                }
                die();
                break;

            default:
                return;
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $newsletter = Newsletter::instance();
        $lists_options = $this->get_options('lists');
        $profile_options = $this->get_options('profile');

        if (empty($lists_options)) {
            foreach ($profile_options as $key => $value) {
                if (strpos($key, 'list_') === 0) {
                    $lists_options[$key] = $value;
                }
            }
        }

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            // Options migration to the new set
            if (!empty($profile_options['list_' . $i]) && empty($lists_options['list_' . $i])) {
                $lists_options['list_' . $i] = $profile_options['list_' . $i];
                $lists_options['list_' . $i . '_checked'] = $profile_options['list_' . $i . '_checked'];
                $lists_options['list_' . $i . '_forced'] = $profile_options['list_' . $i . '_forced'];
            }

            if (!isset($profile_options['list_' . $i . '_forced'])) {
                $profile_options['list_' . $i . '_forced'] = empty($this->options['preferences_' . $i]) ? 0 : 1;
                $lists_options['list_' . $i . '_forced'] = empty($this->options['preferences_' . $i]) ? 0 : 1;
            }
        }

        $this->save_options($profile_options, 'profile');
        $this->save_options($lists_options, 'lists');

        $default_options = $this->get_default_options();

        if (empty($this->options['error_text'])) {
            $this->options['error_text'] = $default_options['error_text'];
            $this->save_options($this->options);
        }

        $this->init_options('template', false);

        global $wpdb, $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter_user_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL DEFAULT 0,
            `ip` varchar(50) NOT NULL DEFAULT '',
            `source` varchar(50) NOT NULL DEFAULT '',
            `data` longtext,
            `created` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
          ) $charset_collate;";

        dbDelta($sql);

        return true;
    }

    function first_install() {
        
    }

    function admin_menu() {
        $this->add_menu_page('lists', __('Lists', 'newsletter'), 1);
        $this->add_menu_page('profile', __('Subscription Form', 'newsletter'), 1);
        if ($this->is_multilanguage()) {
            $this->add_menu_page('options', __('Subscription', 'newsletter'), 1);
        } else {
            $this->add_admin_page('options', __('Subscription', 'newsletter'), 1);
            //$this->add_menu_page('optionsnew', __('Subscription Flow', 'newsletter'), 1);
        }
        $this->add_admin_page('antibot', __('Security', 'newsletter'));
        $this->add_admin_page('forms', __('Forms', 'newsletter'));
        $this->add_admin_page('template', __('Template', 'newsletter'));
    }

    /**
     * This method has been redefined for compatibility with the old options naming. It would
     * be better to change them instead. The subscription options should be named
     * "newsletter_subscription" while the form field options, actually named
     * "newsletter_profile", should be renamed "newsletter_subscription_profile" (since
     * they are retrived with get_options('profile')) or "newsletter_subscription_fields" or
     * "newsletter_subscription_form".
     *
     * @param array $options
     * @param string $sub
     */
    function save_options($options, $sub = '', $autoload = null, $language = '') {
        if (empty($sub) && empty($language)) {
            // For compatibility the options are wrongly named
            return update_option('newsletter', $options, $autoload);
        }

        if (empty($sub) && !empty($language)) {
            return update_option('newsletter_' . $language, $options, $autoload);
        }

        if ($sub == 'profile') {
            if (empty($language)) {
                $this->options_profile = $options;
                return update_option('newsletter_profile', $options, $autoload);
            } else {
                return update_option('newsletter_profile_' . $language, $options, $autoload);
            }
            // For compatibility the options are wrongly named
        }

        if ($sub == 'forms') {
            // For compatibility the options are wrongly named
            return update_option('newsletter_forms', $options, $autoload);
        }

        if ($sub == 'lists') {
            $this->options_lists = $options;
        }
        return parent::save_options($options, $sub, $autoload, $language);
    }

    function get_raw_options($sub = '', $language = '') {
        if ($sub == '') {
            $key = 'newsletter';
            // For compatibility the options are wrongly named
            if ($language) {
                $key = '_' . $language;
            }

            $options = get_option($key, []);

            if (!is_array($options)) {
                $options = [];
            }

            return $options;
        }

        if ($sub == 'profile') {
            $key = 'newsletter_profile';
            if ($language) {
                $key = '_' . $language;
            }

            $options = get_option($key, []);

            if (!is_array($options)) {
                $options = [];
            }

            return array_filter($options);
        }

        if ($sub == 'forms') {
            // For compatibility the options are wrongly named
            return get_option('newsletter_forms', []);
        }
        return parent::get_options($sub, $language);
    }

    function get_options($sub = '', $language = '') {
        if ($sub == '') {
            // For compatibility the options are wrongly named. The is_array() check prevents problem
            // with object caching plugins.
            if ($language) {
                $options = $this->get_option_array('newsletter_' . $language);
                $opt2 = $this->get_option_array('newsletter');
                $options = array_merge($opt2, $options);
            } else {
                $options = $this->get_option_array('newsletter');
            }

            return $options;
        }
        if ($sub == 'profile') {
            if ($language) {
                // All that because for unknown reasome, sometime the options are returned as string, maybe a WPML
                // interference...
                $i18n_options = $this->get_option_array('newsletter_profile_' . $language);
                $options = $this->get_option_array('newsletter_profile');
                $options = array_merge($options, array_filter($i18n_options));
            } else {
                $options = $this->get_option_array('newsletter_profile');
            }
            // For compatibility the options are wrongly named
            return $options;
        }
        if ($sub == 'forms') {
            // For compatibility the options are wrongly named
            return $this->get_option_array('newsletter_forms');
        }
        return parent::get_options($sub, $language);
    }

    /**
     * Prepares the options used to build a subscription form for the current language
     * in internal variable $form_options (for optimization). Can be called many times.
     */
    function setup_form_options() {
        if (empty($this->form_options)) {
            $this->form_options = $this->get_options('profile', $this->get_current_language());
        }
    }

    function get_form_options($language = '') {
        return $this->get_options('profile', $language);
    }

    function set_updated($user, $time = 0, $ip = '') {
        global $wpdb;
        if (!$time) {
            $time = time();
        }

        if (!$ip) {
            $ip = $this->get_remote_ip();
        }
        $ip = $this->process_ip($ip);

        if (is_object($user)) {
            $id = $user->id;
        } else if (is_array($user)) {
            $id = $user['id'];
        }

        $id = (int) $id;

        $wpdb->update(NEWSLETTER_USERS_TABLE, array('updated' => $time, 'ip' => $ip, 'geo' => 0), array('id' => $id));
    }

    /**
     * Sanitize the subscription data collected before process them. Cleanup the lists, the optin mode, email, first name,
     * last name, adds mandatory lists, get (if not provided) and process the IP.
     *
     * @param TNP_Subscription_Data $data
     */
    private function sanitize($data) {
        $data->email = $this->normalize_email($data->email);
        if (!empty($data->name)) {
            $data->name = $this->normalize_name($data->name);
        }
        if (!empty($data->surname)) {
            $data->surname = $this->normalize_name($data->surname);
        }

        if (empty($data->ip)) {
            $data->ip = $this->get_remote_ip();
        }
        $data->ip = $this->process_ip($data->ip);

        if (isset($data->http_referer)) {
            $data->http_referer = mb_substr(strip_tags($data->http_referer), 0, 200);
        }

        if (isset($data->sex)) {
            $data->sex = $this->normalize_sex($data->sex);
        }

        if (!isset($data->language)) {
            $data->language = $this->get_current_language();
        } else {
            $data->language = strtolower(strip_tags($data->language));
        }

        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            $key = 'profile_' . $i;
            if (isset($data->$key)) {
                $data->$key = trim($data->$key);
            }
        }
    }

    /**
     * Builds a default subscription object to be used to collect data and subscription options.
     *
     * @return TNP_Subscription
     */
    function get_default_subscription($language = null) {
        $subscription = new TNP_Subscription();

        $language = is_null($language) ? $this->get_current_language() : $language;

        $subscription->data->language = $language;
        $subscription->optin = $this->is_double_optin() ? 'double' : 'single';

        if (empty($this->options['multiple'])) {
            $subscription->if_exists = TNP_Subscription::EXISTING_ERROR;
        } else if ($this->options['multiple'] == 1) {
            $subscription->if_exists = TNP_Subscription::EXISTING_MERGE;
        } else {
            $subscription->if_exists = TNP_Subscription::EXISTING_SINGLE_OPTIN;
        }

        $lists = $this->get_lists();
        foreach ($lists as $list) {
            if ($list->forced) {
                $subscription->data->lists['' . $list->id] = 1;
                continue;
            }
            // Enforced by language
            if ($language && in_array($language, $list->languages)) {
                $subscription->data->lists['' . $list->id] = 1;
            }
        }

        return $subscription;
    }

    /**
     *
     * @param TNP_Subscription $subscription
     *
     * @return TNP_User|WP_Error
     */
    function subscribe2(TNP_Subscription $subscription) {

        $this->logger->debug($subscription);

        $this->sanitize($subscription->data);

        if (empty($subscription->data->email)) {
            return new WP_Error('email', 'Wrong email address');
        }

        if (!empty($subscription->data->country) && strlen($subscription->data->country) != 2) {
            return new WP_Error('country', 'Country code length error. ISO 3166-1 alpha-2 format (2 letters)');
        }

        // Here we should have a clean subscription data
        // Filter?

        if ($subscription->spamcheck) {
            // TODO: Use autoload
            require_once NEWSLETTER_INCLUDES_DIR . '/antispam.php';
            $antispam = NewsletterAntispam::instance();
            if ($antispam->is_spam($subscription)) {
                return new WP_Error('spam', 'This looks like a spam subscription');
            }
        }

        // Exists?
        $user = $this->get_user_by_email($subscription->data->email);

        $subscription = apply_filters('newsletter_subscription', $subscription, $user);

        // Do we accept repeated subscriptions?
        if ($user != null && $subscription->if_exists === TNP_Subscription::EXISTING_ERROR) {
            //$this->show_message('error', $user);
            return new WP_Error('exists', 'Email address already registered and Newsletter sets to block repeated registrations. You can change this behavior or the user message above on subscription configuration panel.');
        }


        if ($user != null) {

            $this->logger->info('Subscription of an address with status ' . $user->status);

            // We cannot communicate with bounced addresses, there is no reason to proceed
            // TODO: Evaluate if the bounce status is very old, possible reset it
            if ($user->status == TNP_User::STATUS_BOUNCED || $user->status == TNP_User::STATUS_COMPLAINED) {
                return new WP_Error('bounced', 'Subscriber present and blocked');
            }

            if ($user->status == TNP_User::STATUS_UNSUBSCRIBED) {
                // Special behavior?
            }

            if ($subscription->optin == 'single') {
                $user->status = TNP_User::STATUS_CONFIRMED;
            } else {
                if ($user->status == TNP_User::STATUS_CONFIRMED) {

                    set_transient('newsletter_subscription_' . $user->id, $subscription->data, 3600 * 48);

                    // This status is *not* stored it indicate a temporary status to show the correct messages
                    $user->status = TNP_User::STATUS_NOT_CONFIRMED;

                    $this->send_message('confirmation', $user);

                    return $user;
                } else {
                    $user->status = TNP_User::STATUS_NOT_CONFIRMED;
                }
            }

            // Can be updated on the fly?
            $subscription->data->merge_in($user);
        } else {
            $this->logger->info('New subscriber');

            $user = new TNP_User();
            $subscription->data->merge_in($user);

            $user->token = $this->get_token();

            $user->status = $subscription->optin == 'single' ? TNP_User::STATUS_CONFIRMED : TNP_User::STATUS_NOT_CONFIRMED;
            $user->updated = time();
        }

        $user->ip = $this->process_ip($user->ip);

        $user = apply_filters('newsletter_user_subscribe', $user);

        $user = $this->save_user($user);

        $this->add_user_log($user, 'subscribe');

        // Notification to admin (only for new confirmed subscriptions)
        if ($user->status == TNP_User::STATUS_CONFIRMED) {
            do_action('newsletter_user_confirmed', $user);
            $this->notify_admin_on_subscription($user);
            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
        }

        if ($subscription->send_emails) {
            $this->send_message(($user->status == TNP_User::STATUS_CONFIRMED) ? 'confirmed' : 'confirmation', $user);
        }

        // Used by Autoresponder (probably)
        do_action('newsletter_user_post_subscribe', $user);

        return $user;
    }

    /**
     * Create a subscription using the $_REQUEST data. Does security checks.
     *
     * @deprecated since version 6.9.0
     * @param string $status The status to use for this subscription (confirmed, not confirmed, ...)
     * @param bool $emails If the confirmation/welcome email should be sent or the subscription should be silent
     * @return TNP_User
     */
    function subscribe($status = null, $emails = true) {

        $this->logger->debug('Subscription start');

        // Validation
        $ip = $this->get_remote_ip();
        $email = $this->normalize_email(stripslashes($_REQUEST['ne']));
        $first_name = '';
        if (isset($_REQUEST['nn'])) {
            $first_name = $this->normalize_name(stripslashes($_REQUEST['nn']));
        }

        $last_name = '';
        if (isset($_REQUEST['ns'])) {
            $last_name = $this->normalize_name(stripslashes($_REQUEST['ns']));
        }

        $opt_in = (int) $this->options['noconfirmation']; // 0 - double, 1 - single
        if (!empty($this->options['optin_override']) && isset($_REQUEST['optin'])) {
            switch ($_REQUEST['optin']) {
                case 'single': $opt_in = self::OPTIN_SINGLE;
                    break;
                case 'double': $opt_in = self::OPTIN_DOUBLE;
                    break;
            }
        }

        if ($status != null) {
            // If a status is forced and it is requested to be "confirmed" is like a single opt in
            // $status here can only be confirmed or not confirmed
            // TODO: Add a check on status values
            if ($status == TNP_User::STATUS_CONFIRMED) {
                $opt_in = self::OPTIN_SINGLE;
            } else {
                $opt_in = self::OPTIN_DOUBLE;
            }
        }

        $user = $this->get_user($email);

        if ($user != null) {
            // Email already registered in our database
            $this->logger->info('Subscription of an address with status ' . $user->status);

            // Bounced
            // TODO: Manage other cases when added
            if ($user->status == 'B') {
                // Non persistent status to decide which message to show (error)
                $user->status = 'E';
                return $user;
            }

            // Is there any relevant data change? If so we can proceed otherwise if repeated subscriptions are disabled
            // show an already subscribed message

            if (empty($this->options['multiple'])) {
                $user->status = 'E';
                return $user;
            }

            // If the subscriber is confirmed, we cannot change his data in double opt in mode, we need to
            // temporary store and wait for activation
            if ($user->status == TNP_User::STATUS_CONFIRMED && $opt_in == self::OPTIN_DOUBLE) {

                set_transient($this->get_user_key($user), $_REQUEST, 3600 * 48);

                // This status is *not* stored it indicate a temporary status to show the correct messages
                $user->status = 'S';

                $this->send_message('confirmation', $user);

                return $user;
            }
        }

        // Here we have a new subscription or we can process the subscription even with a pre-existant user for example
        // because it is not confirmed
        if ($user != null) {
            $this->logger->info("Email address subscribed but not confirmed");
            $user = array('id' => $user->id);
        } else {
            $this->logger->info("New email address");
            $user = array('email' => $email);
        }

        $user = $this->update_user_from_request($user);

        $user['token'] = $this->get_token();
        $ip = $this->process_ip($ip);
        $user['ip'] = $ip;
        $user['geo'] = 0;
        $user['status'] = $opt_in == self::OPTIN_SINGLE ? TNP_User::STATUS_CONFIRMED : TNP_User::STATUS_NOT_CONFIRMED;

        $user['updated'] = time();

        $user = apply_filters('newsletter_user_subscribe', $user);

        $user = $this->save_user($user);

        $this->add_user_log($user, 'subscribe');

        // Notification to admin (only for new confirmed subscriptions)
        if ($user->status == TNP_User::STATUS_CONFIRMED) {
            do_action('newsletter_user_confirmed', $user);
            $this->notify_admin_on_subscription($user);
            setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');
        }

        if ($emails) {
            $this->send_message(($user->status == TNP_User::STATUS_CONFIRMED) ? 'confirmed' : 'confirmation', $user);
        }

        $user = apply_filters('newsletter_user_post_subscribe', $user);

        return $user;
    }

    function add_microdata($message) {
        return $message . '<span itemscope itemtype="http://schema.org/EmailMessage"><span itemprop="description" content="Email address confirmation"></span><span itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction"><meta itemprop="name" content="Confirm Subscription"><span itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler"><meta itemprop="url" content="{subscription_confirm_url}"><link itemprop="method" href="http://schema.org/HttpRequestMethod/POST"></span></span></span>';
    }

    /**
     * Builds a subscription object starting from values in the $_REQUEST
     * global variable. It DOES NOT sanitizie or formally check the values.
     * Usually data comes from a form submission.
     * https://www.thenewsletterplugin.com/documentation/subscription/newsletter-forms/
     *
     * @return TNP_Subscription
     */
    function build_subscription() {

        $language = '';
        if (!empty($_REQUEST['nlang'])) {
            $language = $_REQUEST['nlang'];
        } else {
            $language = $this->get_current_language();
        }

        $subscription = $this->get_default_subscription($language);
        $data = $subscription->data;

        $data->email = $_REQUEST['ne'];

        if (isset($_REQUEST['nn'])) {
            $data->name = stripslashes($_REQUEST['nn']);
        }

        if (isset($_REQUEST['ns'])) {
            $data->surname = stripslashes($_REQUEST['ns']);
        }

        if (!empty($_REQUEST['nx'])) {
            $data->sex = $_REQUEST['nx'][0];
        }

        if (isset($_REQUEST['nr'])) {
            $data->referrer = $_REQUEST['nr'];
        }

        // From the antibot form
        if (isset($_REQUEST['nhr'])) {
            $data->http_referer = stripslashes($_REQUEST['nhr']);
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $data->http_referer = $_SERVER['HTTP_REFERER'];
        }

        // New profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // If the profile cannot be set by  subscriber, skip it.
            if ($this->options_profile['profile_' . $i . '_status'] == 0) {
                continue;
            }
            if (isset($_REQUEST['np' . $i])) {
                $data->profiles['' . $i] = stripslashes($_REQUEST['np' . $i]);
            }
        }

        // Lists (field name is nl[] and values the list number so special forms with radio button can work)
        if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
            $this->logger->debug($_REQUEST['nl']);
            foreach ($_REQUEST['nl'] as $list_id) {
                $list = $this->get_list($list_id);
                if (!$list || $list->is_private()) {
                    // To administrator show an error to make him aware of the wrong form configuration
                    if (current_user_can('administrator')) {
                        $this->dienow('Invalid list', 'List ' . $list_id . ' has been submitted but it is set as private. Please fix the subscription form.');
                    }
                    // Ignore this list
                    continue;
                }
                $data->lists['' . $list_id] = 1;
            }
        } else {
            $this->logger->debug('No lists received');
        }

        // Opt-in mode
        if (!empty($this->options['optin_override']) && isset($_REQUEST['optin'])) {
            switch ($_REQUEST['optin']) {
                case 'single': $subscription->optin = 'single';
                    break;
                case 'double': $subscription->optin = 'double';
                    break;
            }
        }

        return $subscription;
    }

    /**
     * Processes the request and fill in the *array* representing a subscriber with submitted values
     * (filtering when necessary).
     *
     * @deprecated since version 6.9.0
     * @param array $user An array partially filled with subscriber data
     * @return array The filled array representing a subscriber
     */
    function update_user_from_request($user) {

        if (isset($_REQUEST['nn'])) {
            $user['name'] = $this->normalize_name(stripslashes($_REQUEST['nn']));
        }
        // TODO: required checking

        if (isset($_REQUEST['ns'])) {
            $user['surname'] = $this->normalize_name(stripslashes($_REQUEST['ns']));
        }
        // TODO: required checking

        if (!empty($_REQUEST['nx'])) {
            $user['sex'] = $this->normalize_sex($_REQUEST['nx'][0]);
        }
        // TODO: valid values check

        if (isset($_REQUEST['nr'])) {
            $user['referrer'] = strip_tags(trim($_REQUEST['nr']));
        }

        $language = '';
        if (!empty($_REQUEST['nlang'])) {
            $language = strtolower(strip_tags($_REQUEST['nlang']));
            // TODO: Check if it's an allowed language code
            $user['language'] = $language;
        } else {
            $language = $this->get_current_language();
            $user['language'] = $language;
        }

        // From the antibot form
        if (isset($_REQUEST['nhr'])) {
            $user['http_referer'] = strip_tags(trim($_REQUEST['nhr']));
        } else if (isset($_SERVER['HTTP_REFERER'])) {
            $user['http_referer'] = strip_tags(trim($_SERVER['HTTP_REFERER']));
        }

        if (strlen($user['http_referer']) > 200) {
            $user['http_referer'] = mb_substr($user['http_referer'], 0, 200);
        }

        // New profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // If the profile cannot be set by  subscriber, skip it.
            if ($this->options_profile['profile_' . $i . '_status'] == 0) {
                continue;
            }
            if (isset($_REQUEST['np' . $i])) {
                $user['profile_' . $i] = trim(stripslashes($_REQUEST['np' . $i]));
            }
        }

        // Extra validation to explain the administrator while the submitted data could
        // be interpreted only partially
        if (current_user_can('administrator')) {
            if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
                foreach ($_REQUEST['nl'] as $list_id) {
                    $list = $this->get_list($list_id);
                    if ($list && $list->status == TNP_List::STATUS_PRIVATE) {
                        $this->dienow('Invalid list', '[old] List ' . $list_id . ' has been submitted but it is set as private. Please fix the subscription form.');
                    }
                }
            }
        }
        // Preferences (field names are nl[] and values the list number so special forms with radio button can work)
        // Permetto l'aggiunta solo delle liste pubbliche
        if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
            $lists = $this->get_lists_public();
            //$this->logger->debug($_REQUEST['nl']);
            foreach ($lists as $list) {
                if (in_array('' . $list->id, $_REQUEST['nl'])) {
                    $user['list_' . $list->id] = 1;
                }
            }
        } else {
            $this->logger->debug('No lists received');
        }

        // Forced lists (general or by language)
        // Forzo l'aggiunta delle liste forzate
        $lists = $this->get_lists();
        foreach ($lists as $list) {
            if ($list->forced) {
                $user['list_' . $list->id] = 1;
            }
            if (in_array($language, $list->languages)) {
                $user['list_' . $list->id] = 1;
            }
        }

        // TODO: should be removed!!!
        if (defined('NEWSLETTER_FEED_VERSION')) {
            $options_feed = get_option('newsletter_feed', array());
            if ($options_feed['add_new'] == 1) {
                $user['feed'] = 1;
            }
        }
        return $user;
    }

    /**
     * Sends a service message applying the template to the HTML part
     *
     * @param TNP_User $user
     * @param string $subject
     * @param string|array $message If string it is considered HTML, if array it should contains the key "html" and "text"
     * @return type
     */
    function mail($user, $subject, $message) {
        $language = $this->get_user_language($user);

        $options_template = $this->get_options('template', $language);

        $template = trim($options_template['template']);
        if (empty($template) || strpos($template, '{message}') === false) {
            $template = '{message}';
        }

        if (is_array($message)) {
            $message['html'] = str_replace('{message}', $message['html'], $template);
            $message['html'] = $this->replace($message['html'], $user);
            $message['text'] = $this->replace($message['text'], $user);
        } else {
            $message = str_replace('{message}', $message, $template);
            $message = $this->replace($message, $user);
        }

        $headers = [];

        // Replaces tags from the template

        $subject = $this->replace($subject, $user);

        return Newsletter::instance()->mail($user->email, $subject, $message, $headers);
    }

    /**
     * Confirms a subscription changing the user status and, possibly, merging the
     * temporary data if present.
     *
     * @param TNP_User $user Optionally it can be null (user search from requests paramaters, but deprecated, or a user id)
     * @return TNP_User
     */
    function confirm($user = null, $emails = true) {

        // Compatibility with WP Registration Addon
        if (!$user) {
            $user = $this->get_user_from_request(true);
        } else if (is_numeric($user)) {
            $user = $this->get_user($user);
        }

        if (!$user) {
            $this->dienow('Subscriber not found', '', 404);
        }
        // End compatibility
        // Should be merged?
        $data = get_transient('newsletter_subscription_' . $user->id);
        if ($data !== false) {
            $data->merge_in($user);
            //$this->merge($user, $data);
            $user = $this->save_user($user);
            $user->status = TNP_User::STATUS_NOT_CONFIRMED;
            delete_transient('newsletter_subscription_' . $user->id);
        } else {
            $new_email = get_transient('newsletter_user_' . $user->id . '_email');
            if ($new_email) {
                $data = ['id' => $user->id, 'email' => $new_email];
                $this->save_user($data);
                delete_transient('newsletter_user_' . $user->id . '_email');
            }
        }


        $this->update_user_last_activity($user);

        setcookie('newsletter', $user->id . '-' . $user->token, time() + 60 * 60 * 24 * 365, '/');

        if ($user->status == TNP_User::STATUS_CONFIRMED) {
            $this->add_user_log($user, 'activate');
            do_action('newsletter_user_confirmed', $user);
            return $user;
        }

        $this->set_user_status($user, TNP_User::STATUS_CONFIRMED);

        $user = $this->get_user($user);

        $this->add_user_log($user, 'activate');

        do_action('newsletter_user_confirmed', $user);
        $this->notify_admin_on_subscription($user);

        if ($emails) {
            $this->send_message('confirmed', $user);
        }

        return $user;
    }

    /**
     * Sends a message (activation, welcome, cancellation, ...) with the correct template
     * and checking if the message itself is disabled
     *
     * @param string $type
     * @param TNP_User $user
     */
    function send_message($type, $user, $force = false) {
        if (!$force && !empty($this->options[$type . '_disabled'])) {
            return true;
        }

        $language = $this->get_user_language($user);

        $options = $this->get_options('', $language);
        $message = [];
        $message['html'] = do_shortcode($options[$type . '_message']);
        $message['text'] = $this->get_text_message($type);
        if ($user->status == TNP_User::STATUS_NOT_CONFIRMED) {
            $message['html'] = $this->add_microdata($message['html']);
        }
        $subject = $options[$type . '_subject'];

        return $this->mail($user, $subject, $message);
    }

    function get_text_message($type) {
        switch ($type) {
            case 'confirmation':
                return __('To confirm your subscription follow the link below.', 'newsletter') . "\n\n{subscription_confirm_url}";
            case 'confirmed':
                return __('Your subscription has been confirmed.', 'newsletter');
        }
        return '';
    }

    function is_double_optin() {
        return $this->options['noconfirmation'] == 0;
    }

    /**
     * Sends the activation email without conditions.
     *
     * @param stdClass $user
     * @return bool
     */
    function send_activation_email($user) {
        return $this->send_message('confirmation', $user, true);
    }

    /**
     * Finds the right way to show the message identified by $key (welcome, unsubscription, ...) redirecting the user to the
     * WordPress page or loading the configured url or activating the standard page.
     */
    function show_message($key, $user, $alert = '', $email = null) {
        $url = '';

        if (isset($_REQUEST['ncu'])) {
            // Custom URL from the form
            $url = $_REQUEST['ncu'];
        } else {
            // Per message custom URL from configuration (language variants could not be supported)
            $options = $this->get_options('', $this->get_user_language($user));
            if (!empty($options[$key . '_url'])) {
                $url = $options[$key . '_url'];
            }
        }

        $url = Newsletter::instance()->build_message_url($url, $key, $user, $email, $alert);
        wp_redirect($url);

        die();
    }

    function get_message_key_from_request() {
        if (empty($_GET['nm'])) {
            return 'subscription';
        }
        $key = $_GET['nm'];
        switch ($key) {
            case 's': return 'confirmation';
            case 'c': return 'confirmed';
            case 'u': return 'unsubscription';
            case 'uc': return 'unsubscribed';
            case 'p':
            case 'pe':
                return 'profile';
            default: return $key;
        }
    }

    var $privacy_url = false;

    /**
     * Generates the privacy URL and cache it.
     *
     * @return string
     */
    function get_privacy_url() {
        if ($this->privacy_url === false) {
            $this->setup_form_options();
            if (!empty($this->form_options['privacy_use_wp_url']) && function_exists('get_privacy_policy_url')) {
                $this->privacy_url = get_privacy_policy_url();
            } else {
                $this->privacy_url = $this->form_options['privacy_url'];
            }
        }
        return $this->privacy_url;
    }

    function get_form_javascript() {
        
    }

    /**
     * Manages the custom forms made with [newsletter_form] and internal [newsletter_field] shortcodes.
     *
     * @param array $attrs
     * @param string $content
     * @return string
     */
    function get_subscription_form_custom($attrs = [], $content = '') {
        if (!is_array($attrs)) {
            $attrs = [];
        }

        $this->setup_form_options();

        $attrs = array_merge(['class' => 'tnp-subscription', 'style' => '', 'id' => ''], $attrs);

        $action = esc_attr($this->build_action_url('s'));
        $class = esc_attr($attrs['class']);
        $style = esc_attr($attrs['style']);

        $buffer = '<form method="post" action="' . $action . '" class="' . $class . '" style="' . $style . '"';
        if (!empty($attrs['id'])) {
            $buffer .= ' id="' . esc_attr($attrs['id']) . '"';
        }
        $buffer .= '>' . "\n";

        $language = $this->get_current_language();

        $buffer .= $this->get_form_hidden_fields($attrs);

        $buffer .= do_shortcode($content);

        if (isset($attrs['button_label'])) {
            $label = $attrs['button_label'];
        } else {
            $label = $this->form_options['subscribe'];
        }

        if (!empty($label)) {
            $buffer .= '<div class="tnp-field tnp-field-button">';
            if (strpos($label, 'http') === 0) {
                $buffer .= '<input class="tnp-button-image" type="image" src="' . $label . '">';
            } else {
                $buffer .= '<input class="tnp-button" type="submit" value="' . $label . '">';
            }
            $buffer .= '</div>';
        }

        $buffer .= '</form>';

        return $buffer;
    }

    /** Generates the hidden field for lists which should be implicitely set with a subscription form.
     *
     * @param string $lists Comma separated directly from the shortcode "lists" attribute
     * @param string $language ???
     * @return string
     */
    function get_form_implicit_lists($lists, $language = '') {
        $buffer = '';

        if (is_array($lists)) {
            $arr = $lists;
        } else {
            $arr = explode(',', $lists);
        }

        foreach ($arr as $a) {
            $a = trim($a);
            if (empty($a))
                continue;

            $list = $this->get_list($a);
            if (!$list) {
                $buffer .= $this->build_field_admin_notice('List "' . $a . '" added to the form is not configured, skipped.');
                continue;
            }

            if ($list->is_private()) {
                $buffer .= $this->build_field_admin_notice('List ' . $a . ' is private cannot be used in a public form.');
                continue;
            }

            if ($list->forced) {
                $buffer .= $this->build_field_admin_notice('List ' . $a . ' is already enforced on every subscription there is no need to specify it.');
                continue;
            }

            $buffer .= "<input type='hidden' name='nl[]' value='" . esc_attr($a) . "'>\n";
        }
        return $buffer;
    }

    /**
     * Builds all the hidden fields of a subscription form. Implicit lists, confirmation url,
     * referrer, language, ...
     *
     * @param array $attrs Attributes of form shortcode
     * @return string HTML with the hidden fields
     */
    function get_form_hidden_fields($attrs) {
        $b = '';

        // Compatibility
        if (isset($attrs['list'])) {
            $attrs['lists'] = $attrs['list'];
        }
        if (isset($attrs['lists'])) {
            $b .= $this->get_form_implicit_lists($attrs['lists']);
        }

        if (isset($attrs['referrer'])) {
            $b .= '<input type="hidden" name="nr" value="' . esc_attr($attrs['referrer']) . '">';
        }

        if (isset($attrs['confirmation_url'])) {
            if ($attrs['confirmation_url'] === '#') {
                $attrs['confirmation_url'] = esc_url_raw($_SERVER['REQUEST_URI']);
            }

            $b .= '<input type="hidden" name="ncu" value="' . esc_attr($attrs['confirmation_url']) . '">';
        }

        if (isset($attrs['optin'])) {
            $optin = trim(strtolower($attrs['optin']));
            if ($optin !== 'double' && $optin !== 'single') {
                $b .= $this->build_field_admin_notice('The optin is set to an invalid value.');
            } else {
                if ($optin !== 'double' && $this->is_double_optin() && empty($this->options['optin_override'])) {
                    $b .= $this->build_field_admin_notice('The optin is specified but cannot be overridden (see the subscription configiraton page).');
                } else {
                    $b .= '<input type="hidden" name="optin" value="' . esc_attr($optin) . '">';
                }
            }
        }

        $language = $this->get_current_language();
        $b .= '<input type="hidden" name="nlang" value="' . esc_attr($language) . '">';

        return $b;
    }

    /**
     * Internal use only
     *
     * @param type $name
     * @param type $attrs
     * @param type $suffix
     * @return string
     */
    private function _shortcode_label($name, $attrs, $suffix = null) {

        if (!$suffix) {
            $suffix = $name;
        }
        $buffer = '<label for="' . esc_attr($attrs['id']) . '">';
        if (isset($attrs['label'])) {
            if (empty($attrs['label'])) {
                return;
            } else {
                $buffer .= esc_html($attrs['label']);
            }
        } else {
            if (isset($this->form_options[$name])) {
                $buffer .= esc_html($this->form_options[$name]);
            }
        }
        $buffer .= "</label>\n";
        return $buffer;
    }

    /**
     * Creates a notices to be displayed near a subscription form field to inform of worng configurations.
     * It is created only if the current user looking at the form is the administrator.
     *
     * @param string $message
     * @return string
     */
    function build_field_admin_notice($message) {
        if (!current_user_can('administrator')) {
            return '';
        }
        return '<p style="background-color: #eee; color: #000; padding: 10px; margin: 10px 0">' . $message . ' <strong>This notice is shown only to administrators to help with configuration.</strong></p>';
    }

    function shortcode_newsletter_field($attrs, $content = '') {
        // Counter to create unique ID for checkbox and labels
        static $idx = 0;

        $idx++;
        $attrs['id'] = 'tnp-' . $idx;

        $this->setup_form_options();
        $language = $this->get_current_language();

        $name = $attrs['name'];

        $buffer = '';

        if ($name == 'email') {
            $buffer .= '<div class="tnp-field tnp-field-email">';

            $buffer .= $this->_shortcode_label('email', $attrs);

            $buffer .= '<input class="tnp-email" type="email" name="ne" id="' . esc_attr($attrs['id']) . '" value=""';
            if (isset($attrs['placeholder'])) {
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            }
            $buffer .= ' required>';
            if (isset($attrs['button_label'])) {
                $label = $attrs['button_label'];
                if (strpos($label, 'http') === 0) {
                    $buffer .= ' <input class="tnp-submit-image" type="image" src="' . esc_attr(esc_url_raw($label)) . '">';
                } else {
                    $buffer .= ' <input class="tnp-submit" type="submit" value="' . esc_attr($label) . '" style="width: 29%">';
                }
            }
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'first_name' || $name == 'name') {
            $buffer .= '<div class="tnp-field tnp-field-firstname">';
            $buffer .= $this->_shortcode_label('name', $attrs);

            $buffer .= '<input class="tnp-name" type="text" name="nn" id="' . esc_attr($attrs['id']) . '" value=""';
            if (isset($attrs['placeholder'])) {
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            }
            if ($this->form_options['name_rules'] == 1) {
                $buffer .= ' required';
            }
            $buffer .= '>';
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'last_name' || $name == 'surname') {
            $buffer .= '<div class="tnp-field tnp-field-surname">';
            $buffer .= $this->_shortcode_label('surname', $attrs);

            $buffer .= '<input class="tnp-surname" type="text" name="ns" id="' . esc_attr($attrs['id']) . '" value=""';
            if (isset($attrs['placeholder'])) {
                $buffer .= ' placeholder="' . esc_attr($attrs['placeholder']) . '"';
            }
            if ($this->form_options['surname_rules'] == 1) {
                $buffer .= ' required';
            }
            $buffer .= '>';
            $buffer .= '</div>';
            return $buffer;
        }

        // Single list
        if ($name == 'preference' || $name == 'list') {
            if (!isset($attrs['number'])) {
                return $this->build_field_admin_notice('List number not specified.');
            }
            $number = (int) $attrs['number'];
            $list = $this->get_list($number, $language);
            if (!$list) {
                return $this->build_field_admin_notice('List ' . $number . ' is not configured, cannot be shown.');
            }

            if ($list->status == 0 || $list->forced) {
                return $this->build_field_admin_notice('List ' . $number . ' is private or enforced cannot be shown.');
            }

            if (isset($attrs['hidden'])) {
                return '<input type="hidden" name="nl[]" value="' . esc_attr($list->id) . '">';
            }

            $idx++;
            $buffer .= '<div class="tnp-field tnp-field-checkbox tnp-field-list"><label for="tnp-' . $idx . '">';
            $buffer .= '<input type="checkbox" id="tnp-' . $idx . '" name="nl[]" value="' . esc_attr($list->id) . '"';
            if (isset($attrs['checked'])) {
                $buffer .= ' checked';
            }
            $buffer .= '>';
            if (isset($attrs['label'])) {
                if ($attrs['label'] != '') {
                    $buffer .= '&nbsp;' . esc_html($attrs['label']) . '</label>';
                }
            } else {
                $buffer .= '&nbsp;' . esc_html($list->name) . '</label>';
            }
            $buffer .= "</div>\n";

            return $buffer;
        }

        // All lists
        if ($name == 'lists' || $name == 'preferences') {
            $lists = $this->get_lists_for_subscription($language);
            if (!empty($lists) && isset($attrs['layout']) && $attrs['layout'] === 'dropdown') {

                $buffer .= '<div class="tnp-field tnp-lists">';
                // There is not a default "label" for the block of lists, so it can only be specified in the shortcode attrs as "label"
                $buffer .= $this->_shortcode_label('lists', $attrs);
                $buffer .= '<select class="tnp-lists" name="nl[]" required>';

                if (!empty($attrs['first_option_label'])) {
                    $buffer .= '<option value="" selected="true" disabled="disabled">' . esc_html($attrs['first_option_label']) . '</option>';
                }

                foreach ($lists as $list) {
                    $buffer .= '<option value="' . $list->id . '">' . esc_html($list->name) . '</option>';
                }
                $buffer .= '</select>';
                $buffer .= '</div>';
            } else {

                foreach ($lists as $list) {
                    $idx++;
                    $buffer .= '<div class="tnp-field tnp-field-checkbox tnp-field-list"><label for="nl' . $idx . '">';
                    $buffer .= '<input type="checkbox" id="nl' . $idx . '" name="nl[]" value="' . $list->id . '"';
                    if ($list->checked) {
                        $buffer .= ' checked';
                    }
                    $buffer .= '>&nbsp;' . esc_html($list->name) . '</label>';
                    $buffer .= "</div>\n";
                }
            }
            return $buffer;
        }

        if ($name == 'sex' || $name == 'gender') {
            $buffer .= '<div class="tnp-field tnp-field-gender">';
            $buffer .= $this->_shortcode_label('sex', $attrs);

            $buffer .= '<select name="nx" class="tnp-gender" id="tnp-gender"';
            if ($this->form_options['sex_rules']) {
                $buffer .= ' required ';
            }
            $buffer .= '>';
            if ($this->form_options['sex_rules']) {
                $buffer .= '<option value=""></option>';
            }
            $buffer .= '<option value="n">' . esc_html($this->form_options['sex_none']) . '</option>';
            $buffer .= '<option value="f">' . esc_html($this->form_options['sex_female']) . '</option>';
            $buffer .= '<option value="m">' . esc_html($this->form_options['sex_male']) . '</option>';
            $buffer .= '</select>';
            $buffer .= "</div>\n";
            return $buffer;
        }

        if ($name == 'profile') {
            if (!isset($attrs['number'])) {
                return $this->build_field_admin_notice('Extra profile number not specified.');
            }

            $number = (int) $attrs['number'];

            $profile = TNP_Profile_Service::get_profile_by_id($number, $language);

            if (!$profile) {
                return $this->build_field_admin_notice('Extra profile ' . $number . ' is not configured, cannot be shown.');
            }

            if ($profile->status == 0) {
                return $this->build_field_admin_notice('Extra profile ' . $number . ' is private, cannot be shown.');
            }

            $size = isset($attrs['size']) ? $attrs['size'] : '';
            $buffer .= '<div class="tnp-field tnp-field-profile">';
            $buffer .= $this->_shortcode_label('profile_' . $profile->id, $attrs);

            $placeholder = isset($attrs['placeholder']) ? $attrs['placeholder'] : $profile->placeholder;

            // Text field
            if ($profile->type == TNP_Profile::TYPE_TEXT) {
                $buffer .= '<input class="tnp-profile tnp-profile-' . $number . '" id="tnp-profile_' . $number . '" type="text" size="' . esc_attr($size) . '" name="np' . $number . '" placeholder="' . esc_attr($placeholder) . '"';
                if ($profile->is_required()) {
                    $buffer .= ' required';
                }
                $buffer .= '>';
            }

            // Select field
            if ($profile->type == TNP_Profile::TYPE_SELECT) {
                $buffer .= '<select class="tnp-profile tnp-profile-' . $number . '" id="tnp-profile_' . $number . '" name="np' . $number . '"';
                if ($profile->is_required()) {
                    $buffer .= ' required';
                }
                $buffer .= '>';
                if (!empty($placeholder)) {
                    $buffer .= '<option value="" selected disabled>' . esc_html($placeholder) . '</option>';
                }
                foreach ($profile->options as $option) {
                    $buffer .= '<option>' . esc_html(trim($option)) . '</option>';
                }
                $buffer .= "</select>\n";
            }

            $buffer .= "</div>\n";

            return $buffer;
        }

        if (strpos($name, 'privacy') === 0) {
            if (!isset($attrs['url'])) {
                $attrs['url'] = $this->get_privacy_url();
            }

            if (!isset($attrs['label'])) {
                $attrs['label'] = $this->form_options['privacy'];
            }

            $buffer .= '<div class="tnp-field tnp-field-checkbox tnp-field-privacy">';

            $idx++;
            $buffer .= '<input type="checkbox" name="ny" required class="tnp-privacy" id="tnp-' . $idx . '"> ';
            $buffer .= '<label for="tnp-' . $idx . '">';
            if (!empty($attrs['url'])) {
                $buffer .= '<a target="_blank" href="' . esc_attr($attrs['url']) . '">';
            }
            $buffer .= $attrs['label'];
            if (!empty($attrs['url'])) {
                $buffer .= '</a>';
            }
            $buffer .= '</label>';
            $buffer .= '</div>';

            return $buffer;
        }
    }

    /**
     * Builds the privacy field only for completely generated forms.
     *
     * @return string Empty id the privacy filed is not configured
     */
    function get_privacy_field($pre_html = '', $post_html = '') {
        $this->setup_form_options();
        $privacy_status = (int) $this->form_options['privacy_status'];
        if (empty($privacy_status)) {
            return '';
        }

        $buffer = '<label>';
        if ($privacy_status === 1) {
            $buffer .= '<input type="checkbox" name="ny" required class="tnp-privacy">&nbsp;';
        }
        $url = $this->get_privacy_url();
        if (!empty($url)) {
            $buffer .= '<a target="_blank" href="' . esc_attr($url) . '">';
            $buffer .= esc_attr($this->form_options['privacy']) . '</a>';
        } else {
            $buffer .= esc_html($this->form_options['privacy']);
        }

        $buffer .= "</label>";

        return $pre_html . $buffer . $post_html;
    }

    /**
     * The new standard form.
     *
     * @param string $referrer Deprecated since 6.9.1, use the "referrer" key on $attrs
     * @param string $action
     * @param string $attrs
     * @return string The full HTML form
     */
    function get_subscription_form($referrer = '', $action = null, $attrs = []) {
        $language = $this->get_current_language();
        $options_profile = $this->get_options('profile', $language);

        if (!is_array($attrs)) {
            $attrs = [];
        }

        // Possible alternative form actions (used by...?)
        if (isset($attrs['action'])) {
            $action = $attrs['action'];
        }

        // The referrer parameter is deprecated
        if (!empty($referrer)) {
            $attrs['referrer'] = $referrer;
        }

        $buffer = '';

        if (empty($action)) {
            $action = $this->build_action_url('s');
        }

        if (isset($attrs['before'])) {
            $buffer .= $attrs['before'];
        } else {
            if (isset($attrs['class'])) {
                $buffer .= '<div class="tnp tnp-subscription ' . $attrs['class'] . '">' . "\n";
            } else {
                $buffer .= '<div class="tnp tnp-subscription">' . "\n";
            }
        }

        $buffer .= '<form method="post" action="' . esc_attr($action) . '"';

        if (!empty($attrs['id'])) {
            $buffer .= ' id="' . esc_attr($attrs['id']) . '"';
        }

        $buffer .= '>' . "\n\n";

        $buffer .= $this->get_form_hidden_fields($attrs);

        if ($options_profile['name_status'] == 2) {
            $buffer .= $this->shortcode_newsletter_field(['name' => 'first_name']);
        }

        if ($options_profile['surname_status'] == 2) {
            $buffer .= $this->shortcode_newsletter_field(['name' => 'last_name']);
        }

        $buffer .= $this->shortcode_newsletter_field(['name' => 'email']);

        if (isset($options_profile['sex_status']) && $options_profile['sex_status'] == 2) {
            $buffer .= $this->shortcode_newsletter_field(['name' => 'gender']);
        }

        // Extra profile fields
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            // Not for subscription form
            if ($options_profile['profile_' . $i . '_status'] != 2) {
                continue;
            }

            $buffer .= $this->shortcode_newsletter_field(['name' => 'profile', 'number' => $i]);
        }

        $tmp = '';
        $lists = $this->get_lists_for_subscription($language);
        if (!empty($attrs['lists_field_layout']) && $attrs['lists_field_layout'] == 'dropdown') {
            if (empty($attrs['lists_field_empty_label'])) {
                $attrs['lists_field_empty_label'] = '';
            }
            if (empty($attrs['lists_field_label'])) {
                $attrs['lists_field_label'] = '';
            }
            $buffer .= $this->shortcode_newsletter_field(['name' => 'lists', 'layout' => 'dropdown', 'first_option_label' => $attrs['lists_field_empty_label'], 'label' => $attrs['lists_field_label']]);
        } else {
            $buffer .= $this->shortcode_newsletter_field(['name' => 'lists']);
        }



        // Deprecated
        $extra = apply_filters('newsletter_subscription_extra', array());
        foreach ($extra as $x) {
            $label = $x['label'];
            if (empty($label)) {
                $label = '&nbsp;';
            }
            $name = '';
            if (!empty($x['name'])) {
                $name = $x['name'];
            }
            $buffer .= '<div class="tnp-field tnp-field-' . $name . '"><label>' . $label . "</label>";
            $buffer .= $x['field'] . "</div>\n";
        }

        $buffer .= $this->get_privacy_field('<div class="tnp-field tnp-privacy-field">', '</div>');

        $buffer .= '<div class="tnp-field tnp-field-button">';

        $button_style = '';
        if (!empty($attrs['button_color'])) {
            $button_style = 'style="background-color:' . esc_attr($attrs['button_color']) . '"';
        }

        if (strpos($options_profile['subscribe'], 'http') === 0) {
            $buffer .= '<input class="tnp-submit-image" type="image" src="' . esc_attr($options_profile['subscribe']) . '">' . "\n";
        } else {
            $buffer .= '<input class="tnp-submit" type="submit" value="' . esc_attr($options_profile['subscribe']) . '" ' . $button_style . '>' . "\n";
        }

        $buffer .= "</div>\n</form>\n";

        if (isset($attrs['after'])) {
            $buffer .= $attrs['after'];
        } else {
            $buffer .= "</div>\n";
        }

        return $buffer;
    }

    function get_form($number) {
        $options = get_option('newsletter_forms');

        $form = $options['form_' . $number];

        $form = do_shortcode($form);

        $action = $this->build_action_url('s');

        if (stripos($form, '<form') === false) {
            $form = '<form method="post" action="' . $action . '">' . $form . '</form>';
        }

        // For compatibility
        $form = str_replace('{newsletter_url}', $action, $form);

        $form = $this->replace_lists($form);

        return $form;
    }

    /** Replaces on passed text the special tag {lists} that can be used to show the preferences as a list of checkbox.
     * They are called lists but on configuration panel they are named preferences!
     *
     * @param string $buffer
     * @return string
     */
    function replace_lists($buffer) {
        $checkboxes = '';
        $lists = $this->get_lists_for_subscription($this->get_current_language());
        foreach ($lists as $list) {
            $checkboxes .= '<input type="checkbox" name="nl[]" value="' . $list->id . '"/>&nbsp;' . $list->name . '<br />';
        }
        $buffer = str_replace('{lists}', $checkboxes, $buffer);
        $buffer = str_replace('{preferences}', $checkboxes, $buffer);
        return $buffer;
    }

    function notify_admin_on_subscription($user) {

        if (empty($this->options['notify'])) {
            return;
        }

        $message = $this->generate_admin_notification_message($user);
        $email = trim($this->options['notify_email']);
        $subject = $this->generate_admin_notification_subject('New subscription');

        Newsletter::instance()->mail($email, $subject, array('html' => $message));
    }

    /**
     * Builds the minimal subscription form, with only the email field and inline
     * submit button. If enabled the privacy checkbox is added.
     *
     * @param type $attrs
     * @return string
     */
    function get_subscription_form_minimal($attrs) {

        $this->setup_form_options();

        if (!is_array($attrs)) {
            $attrs = [];
        }

        $attrs = array_merge(array('class' => '', 'referrer' => 'minimal',
            'button' => $this->form_options['subscribe'], 'button_color' => '',
            'button_radius' => '', 'placeholder' => $this->form_options['email']), $attrs);

        $form = '';

        $form .= '<div class="tnp tnp-subscription-minimal ' . $attrs['class'] . '">';
        $form .= '<form action="' . esc_attr($this->build_action_url('s')) . '" method="post"';
        if (!empty($attrs['id'])) {
            $form .= ' id="' . esc_attr($attrs['id']) . '"';
        }
        $form .= '>';

        $form .= $this->get_form_hidden_fields($attrs);

        $form .= '<input class="tnp-email" type="email" required name="ne" value="" placeholder="' . esc_attr($attrs['placeholder']) . '">';

        if (isset($attrs['button_label'])) {
            $label = $attrs['button_label'];
        } else if (isset($attrs['button'])) { // Backward compatibility
            $label = $attrs['button'];
        } else {
            $label = $this->form_options['subscribe'];
        }

        $form .= '<input class="tnp-submit" type="submit" value="' . esc_attr($attrs['button']) . '"'
                . ' style="background-color:' . esc_attr($attrs['button_color']) . '">';

        $form .= $this->get_privacy_field('<div class="tnp-field tnp-privacy-field">', '</div>');

        $form .= "</form></div>\n";

        return $form;
    }

    function shortcode_newsletter_form($attrs, $content) {

        if (isset($attrs['type']) && $attrs['type'] === 'minimal') {
            return $this->get_subscription_form_minimal($attrs);
        }

        // Custom form using the [newsletter_field] shortcodes
        if (!empty($content)) {
            return $this->get_subscription_form_custom($attrs, $content);
        }

        // Custom form hand coded and saved in the custom forms option
        if (isset($attrs['form'])) {
            return $this->get_form((int) $attrs['form']);
        }

        // Custom hand coded form (as above, new syntax)
        if (isset($attrs['number'])) {
            return $this->get_form((int) $attrs['number']);
        }

        return $this->get_subscription_form(null, null, $attrs);
    }

    /**
     *
     * @global wpdb $wpdb
     * @param array $attrs
     * @param string $content
     * @return string
     */
    function shortcode_newsletter($attrs, $content) {
        global $wpdb;

        $message_key = $this->get_message_key_from_request();
        if ($message_key == 'confirmation') {
            $user = $this->get_user_from_request(false, 'preconfirm');
        } else {
            $user = $this->get_user_from_request();
        }

        $message = apply_filters('newsletter_page_text', '', $message_key, $user);

        $options = $this->get_options('', $this->get_current_language($user));

        if (empty($message)) {
            $message = $options[$message_key . '_text'];

            // TODO: the if can be removed
            if ($message_key == 'confirmed') {
                $message .= $options[$message_key . '_tracking'];
            }
        }

        // Now check what form must be added
        if ($message_key == 'subscription') {
            if (isset($attrs['show_form']) && $attrs['show_form'] === 'false') {
                //return $this->build_field_admin_notice('The [newsletter] shortcode is configured to not show the subscription form.');
                return;
            }

            // Compatibility check
            if (stripos($message, '<form') !== false) {
                $message = str_ireplace('<form', '<form method="post" action="' . esc_attr($this->get_subscribe_url()) . '"', $message);
            } else {

                if (strpos($message, '{subscription_form') === false) {
                    $message .= '{subscription_form}';
                }

                if (isset($attrs['form'])) {
                    $message = str_replace('{subscription_form}', $this->get_form($attrs['form']), $message);
                } else {
                    $message = str_replace('{subscription_form}', $this->get_subscription_form('page', null, $attrs), $message);
                }
            }
        }

        $email = $this->get_email_from_request();

        $message = $this->replace($message, $user, $email, 'page');

        $message = do_shortcode($message);

        if (isset($_REQUEST['alert'])) {
            // slashes are already added by wordpress!
            $message .= '<script>alert("' . esc_js(strip_tags($_REQUEST['alert'])) . '");</script>';
        }

        return $message;
    }

}

NewsletterSubscription::instance();

// Compatibility code

/**
 * @deprecated
 * @param int $number
 */
function newsletter_form($number = null) {
    if ($number != null) {
        echo NewsletterSubscription::instance()->get_form($number);
    } else {
        echo NewsletterSubscription::instance()->get_subscription_form();
    }
}
