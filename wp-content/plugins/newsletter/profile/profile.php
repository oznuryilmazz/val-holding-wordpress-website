<?php

defined('ABSPATH') || exit;

class NewsletterProfile extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterProfile
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterProfile();
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct('profile', '1.1.0');
        add_shortcode('newsletter_profile', [$this, 'shortcode_newsletter_profile']);
        add_filter('newsletter_replace', [$this, 'hook_newsletter_replace'], 10, 4);
        add_filter('newsletter_page_text', [$this, 'hook_newsletter_page_text'], 10, 3);
        add_action('newsletter_action', [$this, 'hook_newsletter_action'], 12, 3);
    }

    function hook_newsletter_action($action, $user, $email) {

        if (in_array($action, ['p', 'profile', 'pe', 'profile-save', 'profile_export', 'ps'])) {
            if (!$user || $user->status != TNP_User::STATUS_CONFIRMED) {
                $this->dienow(__('The subscriber was not found or is not confirmed.', 'newsletter'), '', 404);
            }
        }

        switch ($action) {
            case 'profile':
            case 'p':
            case 'pe':

                $profile_url = $this->build_message_url($this->options['url'], 'profile', $user, $email);
                $profile_url = apply_filters('newsletter_profile_url', $profile_url, $user);

                wp_redirect($profile_url);
                die();

                break;

            case 'profile-save':
            case 'ps':
                $res = $this->save_profile($user);
                if (is_wp_error($res)) {
                    wp_redirect($this->build_message_url($this->options['url'], 'profile', $user, $email, $res->get_error_message()));
                    die();
                }
                
                wp_redirect($this->build_message_url($this->options['url'], 'profile', $user, $email, $res));
                die();
                break;

            case 'profile_export':
                header('Content-Type: application/json;charset=UTF-8');
                echo $this->to_json($user);
                die();
        }
    }

    /**
     *
     * @param stdClass $user
     */
    function get_profile_export_url($user) {
        return $this->build_action_url('profile_export', $user);
    }

    /**
     * URL to the subscriber profile edit action. This URL MUST NEVER be changed by
     * 3rd party plugins. Plugins can change the final URL after the action has been executed using the
     * <code>newsletter_profile_url</code> filter.
     *
     * @param stdClass $user
     */
    function get_profile_url($user, $email = null) {
        return $this->build_action_url('profile', $user, $email);
    }

    function hook_newsletter_replace($text, $user, $email, $html = true) {
        if (!$user) {
            $text = $this->replace_url($text, 'PROFILE_URL', $this->build_action_url('nul'));
            return $text;
        }

        // Profile edit page URL and link
        $url = $this->get_profile_url($user, $email);
        $text = $this->replace_url($text, 'profile_url', $url);
        // Profile export URL and link
        $url = $this->get_profile_export_url($user);
        $text = $this->replace_url($text, 'profile_export_url', $url);

        if (strpos($text, '{profile_form}') !== false) {
            $text = str_replace('{profile_form}', $this->get_profile_form($user), $text);
        }
        return $text;
    }

    /**
     *
     * @param type $text
     * @param type $key
     * @param TNP_User $user
     * @return string
     */
    function hook_newsletter_page_text($text, $key, $user) {
        if ($key == 'profile') {
            if (!$user || $user->status == TNP_User::STATUS_UNSUBSCRIBED) {
                return 'Subscriber not found.';
            }
            $options = $this->get_options('main', $this->get_current_language($user));
            return $options['text'];
        }
        return $text;
    }

    function shortcode_newsletter_profile($attrs, $content) {
        $user = $this->check_user();

        if (empty($user)) {
            if (empty($content)) {
                return __('Subscriber not found.', 'newsletter');
            } else {
                return $content;
            }
        }

        return $this->get_profile_form($user);
    }

    function to_json($user) {
        global $wpdb;


        $fields = array('name', 'surname', 'sex', 'created', 'ip', 'email');
        $data = array(
            'email' => $user->email,
            'name' => $user->name,
            'last_name' => $user->surname,
            'gender' => $user->sex,
            'created' => $user->created,
            'ip' => $user->ip,
        );

        // Lists
        $data['lists'] = array();

        $lists = $this->get_lists_public();
        foreach ($lists as $list) {
            $field = 'list_' . $list->id;
            if ($user->$field == 1) {
                $data['lists'][] = $list->name;
            }
        }

        // Profile
        $options_profile = get_option('newsletter_profile', array());
        $data['profiles'] = array();
        for ($i = 1; $i < NEWSLETTER_PROFILE_MAX; $i++) {
            $field = 'profile_' . $i;
            if ($options_profile[$field . '_status'] != 1 && $options_profile[$field . '_status'] != 2) {
                continue;
            }
            $data['profiles'][] = array('name' => $options_profile[$field], 'value' => $user->$field);
        }

        // Newsletters
        if ($this->options['export_newsletters']) {
            $sent = $wpdb->get_results($wpdb->prepare("select * from {$wpdb->prefix}newsletter_sent where user_id=%d order by email_id asc", $user->id));
            $newsletters = array();
            foreach ($sent as $item) {
                $action = 'none';
                if ($item->open == 1) {
                    $action = 'read';
                } else if ($item->open == 2) {
                    $action = 'click';
                }

                $email = $this->get_email($item->email_id);
                if (!$email) {
                    continue;
                }
                // 'id'=>$item->email_id,
                $newsletters[] = array('subject' => $email->subject, 'action' => $action, 'sent' => date('Y-m-d h:i:s', $email->send_on));
            }

            $data['newsletters'] = $newsletters;
        }

        $extra = apply_filters('newsletter_profile_export_extra', array());

        $data = array_merge($extra, $data);

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Build the profile editing form for the specified subscriber.
     *
     * @param TNP_User $user
     * @return string
     */
    function get_profile_form($user) {
        // Do not pay attention to option name here, it's a compatibility problem

        $language = $this->get_user_language($user);
        $options = NewsletterSubscription::instance()->get_options('profile', $language);

        $buffer = '';

        $buffer .= '<div class="tnp tnp-profile">';
        $buffer .= '<form action="' . $this->build_action_url('ps') . '" method="post">';
        $buffer .= '<input type="hidden" name="nk" value="' . esc_attr($user->id . '-' . $user->token) . '">';

        $buffer .= '<div class="tnp-field tnp-field-email">';
        $buffer .= '<label>' . esc_html($options['email']) . '</label>';
        $buffer .= '<input class="tnp-email" type="text" name="ne" required value="' . esc_attr($user->email) . '">';
        $buffer .= "</div>\n";


        if ($options['name_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-firstname">';
            $buffer .= '<label>' . esc_html($options['name']) . '</label>';
            $buffer .= '<input class="tnp-firstname" type="text" name="nn" value="' . esc_attr($user->name) . '"' . ($options['name_rules'] == 1 ? ' required' : '') . '>';
            $buffer .= "</div>\n";
        }

        if ($options['surname_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-lastname">';
            $buffer .= '<label>' . esc_html($options['surname']) . '</label>';
            $buffer .= '<input class="tnp-lastname" type="text" name="ns" value="' . esc_attr($user->surname) . '"' . ($options['surname_rules'] == 1 ? ' required' : '') . '>';
            $buffer .= "</div>\n";
        }

        if ($options['sex_status'] >= 1) {
            $buffer .= '<div class="tnp-field tnp-field-gender">';
            $buffer .= '<label>' . esc_html($options['sex']) . '</label>';
            $buffer .= '<select name="nx" class="tnp-gender"';
            if ($options['sex_rules']) {
                $buffer .= ' required ';
            }
            $buffer .= '>';
            if ($options['sex_rules']) {
                $buffer .= '<option value=""></option>';
            }
            $buffer .= '<option value="n"' . ($user->sex == 'n' ? ' selected' : '') . '>' . esc_html($options['sex_none']) . '</option>';
            $buffer .= '<option value="f"' . ($user->sex == 'f' ? ' selected' : '') . '>' . esc_html($options['sex_female']) . '</option>';
            $buffer .= '<option value="m"' . ($user->sex == 'm' ? ' selected' : '') . '>' . esc_html($options['sex_male']) . '</option>';
            $buffer .= '</select>';
            $buffer .= "</div>\n";
        }

        if ($this->is_multilanguage()) {

            $languages = $this->get_languages();

            $buffer .= '<div class="tnp-field tnp-field-language">';
            $buffer .= '<label>' . __('Language', 'newsletter') . '</label>';
            $buffer .= '<select name="nlng" class="tnp-language">';

            $buffer .= '<option value="" disabled ' . ( empty($user->language) ? ' selected' : '' ) . '>' . __('Select language', 'newsletter') . '</option>';
            foreach ($languages as $key => $l) {
                $buffer .= '<option value="' . $key . '"' . ( $user->language == $key ? ' selected' : '' ) . '>' . esc_html($l) . '</option>';
            }

            $buffer .= '</select>';
            $buffer .= "</div>\n";
        }

        // Profile
        $profiles = NewsletterSubscription::instance()->get_profiles_for_profile($user->language);
        foreach ($profiles as $profile) {
            $i = $profile->id; // I'm lazy

            $buffer .= '<div class="tnp-field tnp-field-profile">';
            $buffer .= '<label>' . esc_html($profile->name) . '</label>';

            $field = 'profile_' . $i;

            if ($profile->is_text()) {
                $buffer .= '<input class="tnp-profile tnp-profile-' . $i . '" type="text" name="np' . $i . '" value="' . esc_attr($user->$field) . '"' .
                        ($profile->is_required() ? ' required' : '') . '>';
            }

            if ($profile->is_select()) {
                $buffer .= '<select class="tnp-profile tnp-profile-' . $i . '" name="np' . $i . '"' . ($profile->is_required() ? ' required' : '') . '>';
                foreach ($profile->options as $option) {
                    $buffer .= '<option';
                    if ($option == $user->$field) {
                        $buffer .= ' selected';
                    }
                    $buffer .= '>' . esc_html($option) . '</option>';
                }
                $buffer .= '</select>';
            }

            $buffer .= "</div>\n";
        }

        // Lists
        $lists = $this->get_lists_for_profile($language);
        $tmp = '';
        foreach ($lists as $list) {

            $tmp .= '<div class="tnp-field tnp-field-list">';
            $tmp .= '<label><input class="tnp-list tnp-list-' . $list->id . '" type="checkbox" name="nl[]" value="' . $list->id . '"';
            $field = 'list_' . $list->id;
            if ($user->$field == 1) {
                $tmp .= ' checked';
            }
            $tmp .= '><span class="tnp-list-label">' . esc_html($list->name) . '</span></label>';
            $tmp .= "</div>\n";
        }

        if (!empty($tmp)) {
            $buffer .= '<div class="tnp-lists">' . "\n" . $tmp . "\n" . '</div>';
        }

        // Obsolete
        $extra = apply_filters('newsletter_profile_extra', array(), $user);
        foreach ($extra as $x) {
            $buffer .= '<div class="tnp-field">';
            $buffer .= '<label>' . $x['label'] . "</label>";
            $buffer .= $x['field'];
            $buffer .= "</div>\n";
        }

        $local_options = $this->get_options('', $language);

        // Privacy
        $privacy_url = NewsletterSubscription::instance()->get_privacy_url();
        if (!empty($local_options['privacy_label']) && !empty($privacy_url)) {
            $buffer .= '<div class="tnp-field tnp-field-privacy">';
            if ($privacy_url) {
                $buffer .= '<a href="' . $privacy_url . '" target="_blank">';
            }

            $buffer .= $local_options['privacy_label'];

            if ($privacy_url) {
                $buffer .= '</a>';
            }
            $buffer .= "</div>\n";
        }

        $buffer .= '<div class="tnp-field tnp-field-button">';
        $buffer .= '<input class="tnp-submit" type="submit" value="' . esc_attr($local_options['save_label']) . '">';
        $buffer .= "</div>\n";

        $buffer .= "</form>\n</div>\n";

        return $buffer;
    }

    /**
     * Saves the subscriber data extracting them from the $_REQUEST and for the
     * subscriber identified by the <code>$user</code> object.
     *
     * @return string|WP_Error If not an error the string represent the message to show
     */
    function save_profile($user) {
        global $wpdb;

        // Conatains the cleaned up user data to be saved
        $data = array();
        $data['id'] = $user->id;

        $options = $this->get_options('', $this->get_current_language($user));
        $options_profile = get_option('newsletter_profile', array());
        $options_main = get_option('newsletter_main', array());

        // Not an elegant interaction between modules but...
        $subscription_module = NewsletterSubscription::instance();
        
        require_once NEWSLETTER_INCLUDES_DIR . '/antispam.php';
        
        $antispam = NewsletterAntispam::instance();
        
        $email = $this->normalize_email(stripslashes($_REQUEST['ne']));

        if ($antispam->is_address_blacklisted($email)) {
            return new WP_Error('spam', 'That email address is not accepted');
        }
        
        if (!$email) {
            return new WP_Error('email', $options['error']);
        }
        
        $email_changed = ($email != $user->email);

        // If the email has been changed, check if it is available
        if ($email_changed) {
            $tmp = $this->get_user($email);
            if ($tmp != null && $tmp->id != $user->id) {
                return new WP_Error('inuse', $options['error']);
            }
        }
        
        if ($email_changed && $subscription_module->is_double_optin()) {
            set_transient('newsletter_user_' . $user->id . '_email', $email, DAY_IN_SECONDS);
        } else {
            $data['email'] = $email;
        }
        
        if (isset($_REQUEST['nn'])) {
            $data['name'] = $this->normalize_name(stripslashes($_REQUEST['nn']));
            if ($antispam->is_spam_text($data['name'])) {
                return new WP_Error('spam', 'That name/surname');
            }
        }
        if (isset($_REQUEST['ns'])) {
            $data['surname'] = $this->normalize_name(stripslashes($_REQUEST['ns']));
            if ($antispam->is_spam_text($data['surname'])) {
                return new WP_Error('spam', 'That name/surname');
            }
        }
        if ($options_profile['sex_status'] >= 1) {
            $data['sex'] = $_REQUEST['nx'][0];
            // Wrong data injection check
            if ($data['sex'] != 'm' && $data['sex'] != 'f' && $data['sex'] != 'n') {
                die('Wrong sex field');
            }
        }
        if (isset($_REQUEST['nlng'])) {
            $languages = $this->get_languages();
            if (isset($languages[$_REQUEST['nlng']])) {
                $data['language'] = $_REQUEST['nlng'];
            }
        }

        // Lists. If not list is present or there is no list to choose or all are unchecked.
        $nl = array();
        if (isset($_REQUEST['nl']) && is_array($_REQUEST['nl'])) {
            $nl = $_REQUEST['nl'];
        }

        // Every possible list shown in the profile must be processed
        $lists = $this->get_lists_for_profile();
        foreach ($lists as $list) {
            $field_name = 'list_' . $list->id;
            $data[$field_name] = in_array($list->id, $nl) ? 1 : 0;
        }

        // Profile
        $profiles = $this->get_profiles_public();
        foreach ($profiles as $profile) {
            if (isset($_REQUEST['np' . $profile->id])) {
                $data['profile_' . $profile->id] = stripslashes($_REQUEST['np' . $profile->id]);
            }
        }

        // Feed by Mail service is saved here
        $data = apply_filters('newsletter_profile_save', $data);

        if ($user->status == TNP_User::STATUS_NOT_CONFIRMED) {
            $data['status'] = TNP_User::STATUS_CONFIRMED;
        }

        $user = $this->save_user($data);
        $this->add_user_log($user, 'profile');

        // Send the activation again only if we use double opt-in, otherwise it has no meaning
        if ($email_changed && $subscription_module->is_double_optin()) {
            $user->email = $email;
            $subscription_module->send_activation_email($user);
            return $options['email_changed'];
        }
        
        return $options['saved'];
    }

    function admin_menu() {
        $this->add_admin_page('index', 'Profile');
    }

    // Patch to avoid conflicts with the "newsletter_profile" option of the subscription module
    // TODO: Fix it
    public function get_prefix($sub = '', $language = '') {
        if (empty($sub)) {
            $sub = 'main';
        }
        return parent::get_prefix($sub, $language);
    }

}

NewsletterProfile::instance();
