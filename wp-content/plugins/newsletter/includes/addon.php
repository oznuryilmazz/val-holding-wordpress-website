<?php

/**
 * User by add-ons as base-class.
 */
class NewsletterAddon {

    var $logger;
    var $admin_logger;
    var $name;
    var $options;
    var $version;
    var $labels;

    public function __construct($name, $version = '0.0.0') {
        $this->name = $name;
        $this->version = $version;
        if (is_admin()) {
            $old_version = get_option('newsletter_' . $name . '_version');
            if ($version !== $old_version) {
                $this->upgrade($old_version === false);
                update_option('newsletter_' . $name . '_version', $version, false);
            }
        }
        add_action('newsletter_init', array($this, 'init'));
        //Load translations from specific addon /languages/ directory
        load_plugin_textdomain('newsletter-' . $this->name, false, 'newsletter-' . $this->name . '/languages/');
    }

    /**
     * Method to be overridden and invoked on version change or on first install.
     *
     * @param bool $first_install
     */
    function upgrade($first_install = false) {
        
    }

    /**
     * Method to be overridden to initialize the add-on. It is invoked when Newsletter
     * fires the <code>newsletter_init</code> event.
     */
    function init() {
        
    }

    function get_current_language() {
        return Newsletter::instance()->get_current_language();
    }

    function is_all_languages() {
        return Newsletter::instance()->is_all_languages();
    }

    function is_allowed() {
        return Newsletter::instance()->is_allowed();
    }

    function get_languages() {
        return Newsletter::instance()->get_languages();
    }

    function is_multilanguage() {
        return Newsletter::instance()->is_multilanguage();
    }

    /**
     * General logger for this add-on.
     *
     * @return NewsletterLogger
     */
    function get_logger() {
        if (!$this->logger) {
            $this->logger = new NewsletterLogger($this->name);
        }
        return $this->logger;
    }

    /**
     * Specific logger for administrator actions.
     *
     * @return NewsletterLogger
     */
    function get_admin_logger() {
        if (!$this->admin_logger) {
            $this->admin_logger = new NewsletterLogger($this->name . '-admin');
        }
        return $this->admin_logger;
    }

    /**
     * Loads and prepares the options. It can be used to late initialize the options to save some resources on
     * add-ons which do not need to do something on each page load.
     */
    function setup_options() {
        if ($this->options) {
            return;
        }
        $this->options = get_option('newsletter_' . $this->name, []);
    }

    /**
     * Retrieve the stored options, merged with the specified language set.
     * 
     * @param string $language
     * @return array
     */
    function get_options($language = '') {
        if ($language) {
            return array_merge(get_option('newsletter_' . $this->name, []), get_option('newsletter_' . $this->name . '_' . $language, []));
        } else {
            return get_option('newsletter_' . $this->name, []);
        }
    }

    /**
     * Saved the options under the correct keys and update the internal $options
     * property.
     * @param array $options
     */
    function save_options($options, $language = '') {
        if ($language) {
            update_option('newsletter_' . $this->name . '_' . $language, $options);
        } else {
            update_option('newsletter_' . $this->name, $options);
            $this->options = $options;
        }
    }

    function merge_defaults($defaults) {
        $options = get_option('newsletter_' . $this->name, []);
        $options = array_merge($defaults, $options);
        $this->save_options($options);
    }

    /**
     * 
     */
    function setup_labels() {
        if (!$this->labels) {
            $labels = [];
        }
    }

    function get_label($key) {
        if (!$this->options)
            $this->setup_options();

        if (!empty($this->options[$key])) {
            return $this->options[$key];
        }

        if (!$this->labels)
            $this->setup_labels();

        // We assume the required key is defined. If not there is an error elsewhere.
        return $this->labels[$key];
    }

    /**
     * Equivalent to $wpdb->query() but logs the event in case of error.
     *
     * @global wpdb $wpdb
     * @param string $query
     */
    function query($query) {
        global $wpdb;

        $r = $wpdb->query($query);
        if ($r === false) {
            $logger = $this->get_logger();
            $logger->fatal($query);
            $logger->fatal($wpdb->last_error);
        }
        return $r;
    }

}

/**
 * Used by mailer add-ons as base-class. Some specific options collected by the mailer
 * are interpreted automatically.
 *
 * They are:
 *
 * `enabled` if not empty it means the mailer is active and should be registered
 *
 * The options are set up in the constructor, there is no need to setup them later.
 */
class NewsletterMailerAddon extends NewsletterAddon {

    var $enabled = false;
    var $menu_title = null;
    var $menu_description = null;
    var $dir = '';

    function __construct($name, $version = '0.0.0', $dir = '') {
        parent::__construct($name, $version);
        $this->dir = $dir;
        $this->setup_options();
        $this->enabled = !empty($this->options['enabled']);
    }

    /**
     * This method must be called as `parent::init()` is overridden.
     */
    function init() {
        parent::init();
        add_action('newsletter_register_mailer', function () {
            if ($this->enabled) {
                Newsletter::instance()->register_mailer($this->get_mailer());
            }
        });

        if (is_admin() && !empty($this->menu_title) && !empty($this->dir) && current_user_can('administrator')) {
            add_action('admin_menu', [$this, 'hook_admin_menu'], 101);
            add_filter('newsletter_menu_settings', [$this, 'hook_newsletter_menu_settings']);
        }
    }

    function hook_newsletter_menu_settings($entries) {
        $entries[] = array('label' => '<i class="fas fa-envelope"></i> ' . $this->menu_title, 'url' => '?page=newsletter_' . $this->name . '_index', 'description' => $this->menu_description);
        return $entries;
    }

    function hook_admin_menu() {
        add_submenu_page('newsletter_main_index', $this->menu_title, '<span class="tnp-side-menu">' . $this->menu_title . '</span>', 'manage_options', 'newsletter_' . $this->name . '_index',
                function () {
                    require $this->dir . '/index.php';
                }
        );
    }

    /**
     * Must return an implementation of NewsletterMailer.
     * @return NewsletterMailer
     */
    function get_mailer() {
        return null;
    }

    function get_last_run() {
        return get_option('newsletter_' . $this->name . '_last_run', 0);
    }

    function save_last_run($time) {
        update_option('newsletter_' . $this->name . '_last_run', $time);
    }

    function save_options($options, $language = '') {
        parent::save_options($options, $language);
        $this->enabled = !empty($options['enabled']);
    }

    /**
     * Returns a TNP_Mailer_Message built to send a test message to the <code>$to</code>
     * email address.
     *
     * @param string $to
     * @param string $subject
     * @return TNP_Mailer_Message
     */
    static function get_test_message($to, $subject = '', $type = '') {
        $message = new TNP_Mailer_Message();
        $message->to = $to;
        $message->to_name = '';
        if (empty($type) || $type == 'html') {
            $message->body = file_get_contents(NEWSLETTER_DIR . '/includes/test-message.html');
            $message->body = str_replace('{plugin_url}', Newsletter::instance()->plugin_url, $message->body);
        }

        if (empty($type) || $type == 'text') {
            $message->body_text = 'This is the TEXT version of a test message. You should see this message only if you email client does not support the rich text (HTML) version.';
        }

        $message->headers['X-Newsletter-Email-Id'] = '0';

        if (empty($subject)) {
            $message->subject = '[' . get_option('blogname') . '] Test message from Newsletter (' . date(DATE_ISO8601) . ')';
        } else {
            $message->subject = $subject;
        }

        if ($type) {
            $message->subject .= ' - ' . $type . ' only';
        }

        $message->from = Newsletter::instance()->options['sender_email'];
        $message->from_name = Newsletter::instance()->options['sender_name'];
        return $message;
    }

    /**
     * Returns a set of test messages to be sent to the specified email address. Used for
     * turbo mode tests. Each message has a different generated subject.
     *
     * @param string $to The destination mailbox
     * @param int $count Number of message objects to create
     * @return TNP_Mailer_Message[]
     */
    function get_test_messages($to, $count, $type = '') {
        $messages = array();
        for ($i = 0; $i < $count; $i++) {
            $messages[] = self::get_test_message($to, '[' . get_option('blogname') . '] Test message ' . ($i + 1) . ' from Newsletter (' . date(DATE_ISO8601) . ')', $type);
        }
        return $messages;
    }

}

class NewsletterFormManagerAddon extends NewsletterAddon {

    var $menu_title = null;
    var $menu_description = null;
    var $dir = '';

    function __construct($name, $version, $dir) {
        parent::__construct($name, $version);
        $this->dir = $dir;
        $this->setup_options();
    }

    function init() {
        parent::init();

        if (is_admin() && !empty($this->menu_title) && !empty($this->dir) && Newsletter::instance()->is_allowed()) {
            add_action('admin_menu', [$this, 'hook_admin_menu'], 101);
            add_filter('newsletter_menu_subscription', [$this, 'hook_newsletter_menu_subscription']);
        }
    }

    function hook_newsletter_menu_subscription($entries) {
        $entries[] = ['label' => $this->menu_title, 'url' => '?page=newsletter_' . $this->name . '_index'];
        return $entries;
    }

    function hook_admin_menu() {
        add_submenu_page('newsletter_main_index', $this->menu_title, '<span class="tnp-side-menu">' . $this->menu_title . '</span>', 'exist', 'newsletter_' . $this->name . '_index',
                function () {
                    require $this->dir . '/admin/index.php';
                }
        );
    }

    /**
     * Returns a lists of representations of forms available in the plugin subject of integration. 
     * Usually the $fields is not set up on returned objects.
     * Must be implemented.
     *  
     * @return TNP_FormManager_Form[] List of forms by 3rd party plugin
     */
    function get_forms() {
        return [];
    }

    /**
     * Build a form general representation of a real form from a form manager plugin extracting
     * only the data required to integrate. The form id is domain of the form manager plugin, so it can be
     * anything.
     * Must be implemented.
     * 
     * @param mixed $form_id
     * @return TNP_FormManager_Form
     */
    function get_form($form_id) {
        return null;
    }

    /**
     * Saves the form mapping and integration settings.
     * @param mixed $form_id
     * @param array $data
     */
    public function save_form_options($form_id, $data) {
        update_option('newsletter_' . $this->name . '_' . $form_id, $data, false);
    }

    /**
     * Gets the form mapping and integration settings. Returns an empty array if the dataset is missing.
     * @param mixed $form_id
     * @return array
     */
    public function get_form_options($form_id) {
        return get_option('newsletter_' . $this->name . '_' . $form_id, []);
    }

}

class TNP_FormManager_Form {

    var $id = null;
    var $title = '';
    var $fields = [];
    var $connected = false;

}
