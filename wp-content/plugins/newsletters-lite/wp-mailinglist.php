<?php

/*
Plugin Name: Newsletters
Plugin URI: https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin
Version: 4.8.8
Description: This newsletter software by Tribulant allows users to subscribe to multiple mailing lists on your WordPress website. Send newsletters manually or from posts, manage newsletter templates, view a complete history with tracking, import/export subscribers, accept paid subscriptions and much more. Upgrade to the premium version to remove all limitations.
Author: Tribulant
Author URI: https://tribulant.com
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-mailinglist 
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!defined('DS')) { define("DS", DIRECTORY_SEPARATOR); }
if (!defined('WP_MEMORY_LIMIT')) { define('WP_MEMORY_LIMIT', "2048M"); }
if (!defined('W3TC_DYNAMIC_SECURITY')) { define('W3TC_DYNAMIC_SECURITY', md5(rand(0, 999))); }
if (!defined('NEWSLETTERS_NAME')) { define('NEWSLETTERS_NAME', basename(dirname(__FILE__))); }
if (!defined('NEWSLETTERS_DIR')) { define('NEWSLETTERS_DIR', dirname(__FILE__)); }

//include the wpMailPlugin class file
require_once(NEWSLETTERS_DIR . DS . 'includes' . DS . 'checkinit.php');
require_once(NEWSLETTERS_DIR . DS . 'includes' . DS . 'constants.php');
require_once(NEWSLETTERS_DIR . DS . 'wp-mailinglist-plugin.php');

if (!class_exists('wpMail')) {
    class wpMail extends wpMailPlugin {
        var $url;
        var $plugin_file;
        var $replace_user;
        var $replace_subscriber;
        function tinymce() {
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

            // Add TinyMCE buttons when using rich editor
            if (get_user_option('rich_editing') == 'true' && $this -> get_option('tinymcebtn') == "Y") {
                add_filter('mce_buttons', array($this, 'mcebutton'));
                add_filter('mce_buttons_3', array($this, 'mcebutton3'));
                add_filter('mce_external_plugins', array($this, 'mceplugin'));
                add_filter('mce_external_languages', array($this, 'mcelanguage'));
                add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'), 1);
            }
        }

        function mcebutton($buttons) {
            array_push($buttons, 'Newsletters');
            $page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");

            if (!empty($page) && in_array($page, (array) $this -> sections)) {
                // Fusion page builder breaking editor
                if(($key = array_search('fusion_button', $buttons)) !== false) {
                    unset($buttons[$key]);
                }
            }

            return $buttons;
        }

        function mcebutton3($buttons = array()) {
            //Viper's Video Quicktags compatibility
            $page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");
            if (!empty($page) && ($page == $this -> sections -> send || $page == $this -> sections -> templates_save)) {
                if (!empty($buttons)) {
                    foreach ($buttons as $bkey => $bval) {
                        if (preg_match("/\v\v\q(.*)?/si", $bval, $match)) {
                            unset($buttons[$bkey]);
                        }
                    }
                }
            }

            return $buttons;
        }

        function mceplugin($plugins = array()) {
            if (version_compare(get_bloginfo('version'), "3.8") >= 0) {
                $url = $this -> url() . '/js/tinymce/editor_plugin.js';
            } else {
                $url = $this -> url() . '/js/tinymce/editor_plugin_old.js';
            }

            $plugins['Newsletters'] = $url;
            $page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");
            if (!empty($page) && in_array($page, (array) $this -> sections)) {

                // Fusion page builder
                if (!empty($plugins['fusion_button'])) {
                    unset($plugins['fusion_button']);
                }

                // Viper's video quicktags
                if (isset($plugins['vipersvideoquicktags'])) {
                    unset($plugins['vipersvideoquicktags']);
                }
            }

            return $plugins;
        }

        function mcelanguage($locales = null) {
            return $locales;
        }

        function block_editor_settings($editor_settings = array(), $post = false) {

            //Override block editor settings for newsletter
            $custompostslug = $this -> get_option('custompostslug');
            if (!empty($post -> post_type) && $post -> post_type == $custompostslug) {

            $editor_settings['titlePlaceholder'] = __('Enter email subject', 'wp-mailinglist');
            $editor_settings['bodyPlaceholder'] = __('Start typing your newsletter or add a block', 'wp-mailinglist');
            }

            return $editor_settings;
        }

        function tiny_mce_before_init($init_array = array()) {
            global $wpdb, $Db, $Html, $Field, $post, $Mailinglist;

            $init_array['content_css'] .= "," . $this -> render_url('css/editor-style.css', 'admin', false);

            $snippets = array();
            $templatesquery = "SELECT * FROM " . $wpdb -> prefix . $this -> Template() -> table . " ORDER BY title ASC";
            $templates = $wpdb -> get_results($templatesquery);

            foreach ($templates as $template) {
                $snippets[] = array('text' => esc_html($template -> title), 'value' => $template -> id);
            }

            $snippets = wp_json_encode($snippets);
            $init_array['newsletters_snippet_list'] = $snippets;

            $mailinglists = array();
            $Db -> model = $Mailinglist -> model;
            //[{text:'MULTI - All (No Choice)', value:'all'}, {text:'MULTI - Select Drop Down', value:'select'}, {text:'MULTI - Checkboxes List', value:'checkboxes'}]
            $mailinglists[] = array('text' => __('Subscribe Form', 'wp-mailinglist'), 'value' => "form");
            $mailinglists[] = array('text' => __('All Lists (no choice)', 'wp-mailinglist'), 'value' => "all");
            $mailinglists[] = array('text' => __('Select Drop Down', 'wp-mailinglist'), 'value' => "select");
            $mailinglists[] = array('text' => __('Checkboxes List', 'wp-mailinglist'), 'value' => "checkboxes");

            if ($lists = $Db -> find_all(false, false, array('title', "ASC"))) {
                foreach ($lists as $list) {
                    $mailinglists[] = array('text' => $list -> id . ' - ' . esc_html($list -> title), 'value' => $list -> id);
                }
            }
            $mailinglists = wp_json_encode($mailinglists);
            $init_array['newsletters_mailinglists_list'] = $mailinglists;

            $subscribeforms = array();
            if ($forms = $this -> Subscribeform() -> find_all()) {
                foreach ($forms as $form) {
                    $subscribeforms[] = array(
                        'text'					=>	esc_html($form -> title),
                        'value'					=>	$form -> id,
                    );
                }
            }
            $subscribeforms = wp_json_encode($subscribeforms);
            $init_array['newsletters_subscribeforms'] = $subscribeforms;

            $post_id = isset($post -> ID) ? $post -> ID : 0;
            $init_array['newsletters_post_id'] = $post_id;

            $init_array['newsletters_language_do'] = $this -> language_do();
            $init_array['newsletters_languages'] = false;
            if ($this -> language_do()) {
                $newsletters_languages = array();
                $languages = $this -> language_getlanguages();
                foreach ($languages as $language) {
                    $newsletters_languages[] = array('text' => $this -> language_name($language), 'value' => $language);
                }
                $newsletters_languages = wp_json_encode($newsletters_languages);
                $init_array['newsletters_languages'] = $newsletters_languages;
            }

            $categories_args = array('hide_empty' => 0, 'show_count' => 1);
            if ($categories = get_categories($categories_args)) {
                $newsletters_categories = array();
                $newsletters_posts_categories = array();
                $newsletters_categories[]= array('text' => __('- Select -', 'wp-mailinglist'), 'value' => false);
                $newsletters_posts_categories[] = array('text' => __('All Categories', 'wp-mailinglist'), 'value' => false);
                foreach ($categories as $category) {
                    $newsletters_categories[] = array('text' => esc_html($category -> name), 'value' => $category -> cat_ID);
                    $newsletters_posts_categories[] = array('text' => esc_html($category -> name), 'value' => $category -> cat_ID);
                }
                $newsletters_categories = wp_json_encode($newsletters_categories);
                $newsletters_posts_categories = wp_json_encode($newsletters_posts_categories);
                $init_array['newsletters_post_categories'] = $newsletters_categories;
                $init_array['newsletters_posts_categories'] = $newsletters_posts_categories;
            }

            $init_array['newsletters_loading_image'] = $this -> url() . '/images/loading.gif';

            if ($post_types = $this -> get_custom_post_types()) {
                $newsletters_post_types = array();
                $newsletters_post_types[] = array('text' => __('- Select -', 'wp-mailinglist'), 'value' => false);
                foreach ($post_types as $ptype_key => $ptype) {
                    $newsletters_post_types[] = array('text' => $ptype -> labels -> name, 'value' => $ptype_key);
                }

                $newsletters_post_types = wp_json_encode($newsletters_post_types);
                $init_array['newsletters_post_types'] = $newsletters_post_types;
            } else {
                $init_array['newsletters_post_types'] = "{}";
            }

            //tinymce.settings.newsletters_thumbnail_sizes
            if ($image_sizes = $Html -> get_image_sizes()) {
                $newsletters_thumbnail_sizes = array();
                foreach ($image_sizes as $size_key => $size) {
                    //$newsletters_thumbnail_sizes[] = array('text' => $size, 'value' => $size);
                    $newsletters_thumbnail_sizes[] = array(
                        'text'				=>	$size['title'],
                        'value'				=>	$size_key,
                    );
                }

                $init_array['newsletters_thumbnail_sizes'] = wp_json_encode($newsletters_thumbnail_sizes);
            } else {
                $init_array['newsletters_thumbnail_sizes'] = "{}";
            }

            //tinymce.settings.newsletters_thumbnail_align
            $newsletters_thumbnail_align = array(
                array('text' => __('Left', 'wp-mailinglist'), 'value' => "left"),
                array('text' => __('Right', 'wp-mailinglist'), 'value' => "right"),
                array('text' => __('None', 'wp-mailinglist'), 'value' => "none"),
            );
            $init_array['newsletters_thumbnail_align'] = wp_json_encode($newsletters_thumbnail_align);

            // tinymce.settings.newsletters_posts_orderby_values
            $newsletters_posts_orderby_values = array(
                array('text' => __('Date', 'wp-mailinglist'), 'value' => "post_date"),
                array('text' => __('Author', 'wp-mailinglist'), 'value' => "author"),
                array('text' => __('Category', 'wp-mailinglist'), 'value' => "category"),
                array('text' => __('Post Content', 'wp-mailinglist'), 'value' => "content"),
                array('text' => __('Post ID', 'wp-mailinglist'), 'value' => "ID"),
                array('text' => __('Menu Order', 'wp-mailinglist'), 'value' => "menu_order"),
                array('text' => __('Title', 'wp-mailinglist'), 'value' => "title"),
                array('text' => __('Random Order', 'wp-mailinglist'), 'value' => "rand"),
            );
            $newsletters_posts_orderby_values = apply_filters('newsletters_posts_orderby_values', $newsletters_posts_orderby_values, 'tinymce');
            $init_array['newsletters_posts_orderby_values'] = wp_json_encode($newsletters_posts_orderby_values);

            $init_array['newsletters_anchor_link_menu'] = __("Anchor Link", 'wp-mailinglist');
            $init_array['newsletters_anchor_link_title'] = __('Insert Email Anchor', 'wp-mailinglist');
            $init_array['newsletters_anchor_link_label'] = __('Anchor Name', 'wp-mailinglist');
            $init_array['newsletters_anchor_link_tooltip'] = esc_js(__('Inserts an anchor link with this value as the name attribute eg. mynameattribute. You can then link to the anchor with hash eg. #mynameattribute', 'wp-mailinglist'));
            $init_array['newsletters_anchor_link_error'] = esc_js(__('Fill in a name attribute to use', 'wp-mailinglist'));

            $init_array['newsletters_snippet_title'] = __('Insert Email Snippet', 'wp-mailinglist');
            $init_array['newsletters_snippet_tooltip'] = __('Choose the snippet to insert', 'wp-mailinglist');

            $init_array['newsletters_woocommerce_products_do'] = false;
            if (class_exists('WooCommerce')) {
                $init_array['newsletters_woocommerce_products_do'] = true;

                $init_array['newsletters_woocommerce_products'] = array(

                );
            }

            return $init_array;
        }

        function my_change_mce_settings($init_array = array()) {
            $init_array['disk_cache'] = false; // disable caching
            $init_array['compress'] = false; // disable gzip compression
            $init_array['old_cache_max'] = 3; // keep 3 different TinyMCE configurations cached (when switching between several configurations regularly)

            return $init_array;
        }

        function mceupdate($ver) {
            $ver += 3;
            return $ver;
        }

        function admin_init() {

            // Flush the W3 Total Cache object cache in admin if it is enabled, it causes problems
            if (function_exists('w3tc_objectcache_flush')) {
                w3tc_objectcache_flush();
            }
        }

        function phpmailer_init($phpmailer = null) {
            global $phpmailer, $fromwpml, $newsletters_plaintext;

            if (!empty($fromwpml) && $fromwpml == true) {
                global $orig_message, $wpml_message, $wpml_textmessage, $wpmlhistory_id;
                global $wpdb, $Subscriber;

                if (!empty($wpmlhistory_id)) {
                    $query = "SELECT `from`, `fromname`, `text` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . esc_sql($wpmlhistory_id) . "'";
                    $his = $wpdb -> get_row($query);
                    $history = stripslashes_deep($his);
                }

                $multimime = $this -> get_option('multimime');
                if ($multimime == "Y" && empty($newsletters_plaintext)) {
                    if (!empty($wpml_textmessage)) {
                        if (!empty($history -> text)) {
                            $altbody = $wpml_textmessage;
                        }

                        if (version_compare(PHP_VERSION, '5.3.2') >= 0 && class_exists('DOMDocument')) {
                            require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
                            $html2text = new Html2Text();
                            $altbody = $html2text -> convert($wpml_textmessage);
                        } else {
                            $altbody = "";
                        }

                        $phpmailer -> AltBody = $altbody;
                    }
                }

                $smtpfrom = (empty($history -> from)) ? esc_html($this -> get_option('smtpfrom')) : $history -> from;
                $smtpfromname = (empty($history -> fromname)) ? esc_html($this -> get_option('smtpfromname')) : $history -> fromname;

                if (!empty($newsletters_plaintext)) {
                    $phpmailer -> ContentType = "text/plain";
                    $phpmailer -> IsHTML(false);
                    $phpmailer -> Body = strip_tags($phpmailer -> Body);
                    $phpmailer -> AltBody = false;
                } else {
                    $phpmailer -> ContentType = "text/html";
                    $phpmailer -> IsHTML(true);
                }

                $phpmailer -> Body = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($phpmailer -> Body), $phpmailer, $wpmlhistory_id));
                $phpmailer -> Sender = $this -> get_option('bounceemail');
                $phpmailer -> SetFrom($smtpfrom, $smtpfromname);

                // Should the Reply-To header be different?
                $replytodifferent = $this -> get_option('replytodifferent');
                if (!empty($replytodifferent)) {
                    $smtpreply = $this -> get_option('smtpreply');
                    $phpmailer -> AddReplyTo($smtpreply, $smtpfromname);
                }

                $bccemails = $this -> get_option('bccemails');
                if (!empty($bccemails)) {
                    $bccemails_address = $this -> get_option('bccemails_address');
                    if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
                        $phpmailer -> addBCC($bccemails_address);
                    }
                }

                $phpmailer -> CharSet = get_bloginfo('charset');
                $phpmailer -> Encoding = $this -> get_option('emailencoding');
                $phpmailer -> WordWrap = 0;
                $phpmailer -> Priority = $this -> get_option('mailpriority');
                $phpmailer -> MessageID = $this -> phpmailer_messageid();
                $phpmailer -> SMTPKeepAlive = true;

                if ($this -> debugging) {
                    $phpmailer -> SMTPDebug = 1;
                    $phpmailer -> Debugoutput = 'html';
                }

                $phpmailer -> AddCustomHeader('Precedence', "bulk");
                $phpmailer -> AddCustomHeader('List-Unsubscribe', $this -> get_managementpost(true));

                global $Subscriber, $newsletters_presend, $newsletters_emailraw;
                if (!empty($newsletters_presend) && $newsletters_presend == true) {
                    $subscriber_id = $Subscriber -> admin_subscriber_id();
                    $subscriber = $Subscriber -> get($subscriber_id, false);
                    $phpmailer -> PreSend();
                    $header = $phpmailer -> CreateHeader();
                    $header .= "To: " . $subscriber -> email . "\r\n";
                    $header .= "Subject: " . $phpmailer -> Subject . "\r\n";
                    $body = $phpmailer -> CreateBody();
                    $emailraw = $header . $body;
                    $newsletters_emailraw = $emailraw;

                    $phpmailer -> AddCustomHeader('Received', "from [127.0.0.1] (EHLO yourMailExchangerName.domain.com) ([127.0.0.1]) by receiverMailExchagerName.domain2.com with SMTP ; 06 Feb 2019 09:41:41 +0000 (UTC) ");

                    $phpmailer = new fakemailer();
                }
            } else {
                // Template system emails?
                $wpmailconf = $this -> get_option('wpmailconf');
                if (!empty($wpmailconf)) {
                    $wpmailconf_template = $this -> get_option('wpmailconf_template');

                    $subject = wp_unslash($phpmailer -> Subject);
                    $body = preg_replace("/<http(.*)>/m", "http$1", $phpmailer -> Body);

                    // Text part of email
                    if (version_compare(PHP_VERSION, '5.3.2') >= 0 && class_exists('DOMDocument')) {
                        require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
                        $html2text = new Html2Text();
                        $altbody = $html2text -> convert($body);
                        $phpmailer -> AltBody = $altbody;
                    } else {
                        $phpmailer -> AltBody = "";
                    }

                    // Html part of email
                    $body = $this -> render_email(false, array('message' => $body), false, true, true, $wpmailconf_template, true, $body);
                    $body = str_replace("[wpmlsubject]", wp_unslash($subject), $body);
                    $body = str_replace("[newsletters_subject]", wp_unslash($subject), $body);
                    $body = $this -> process_set_variables(false, false, $body, false, false, false);
                    $body = $this -> strip_set_variables($body);
                    $phpmailer -> Body = $body;
                }
            }

            $phpmailer -> SMTPAutoTLS = false;

            if (empty($phpmailer -> Sender)) {
                $phpmailer -> Sender = $phpmailer -> From;
            }

            return apply_filters('newsletters_phpmailer_init', $phpmailer);
        }

        //update existing subscriber's email
        function profile_update($user_id = null) {
            global $wpdb, $Db, $Subscriber;

            if (!empty($user_id)) {
                if ($newuserdata = $this -> userdata($user_id)) {
                    $Db -> model = $Subscriber -> model;

                    if ($subscriber = $Db -> find(array('user_id' => $user_id))) {
                        $Db -> model = $Subscriber -> model;
                        $Db -> save_field('email', $newuserdata -> user_email, array('id' => $subscriber -> id));
                    }
                }
            }

            return true;
        }

        function register_form() {
            if ($this -> get_option('registercheckbox') == "Y") :
                ?>

                <p class="newsletter">
                    <label><input tabindex="21" <?php echo $check = ($this -> get_option('checkboxon') == "Y" || $_POST[$this -> pre . 'subscribe'] == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="<?php echo esc_html($this -> pre); ?>subscribe" value="Y" /> <?php echo esc_html($this -> get_option('registerformlabel')); ?></label>
                </p>

            <?php
            endif;
        }

        function comment_form($post_id = null) {
            if ($this -> get_option('commentformcheckbox') == "Y") {
                ?>

                <p class="newsletter">
                    <label><input style="width:auto;" <?php echo ($this -> get_option('commentformautocheck') == "Y") ? 'checked="checked"' : ''; ?> id="newsletter<?php echo esc_html( $post_id); ?>" type="checkbox" name="newsletter" value="1" /> <?php echo esc_html($this -> get_option('commentformlabel')); ?></label>
                </p>

                <?php
            }
        }

        function comment_post($comment_id = null, $comment = null) {

            if ($status = wp_get_comment_status($comment_id)) {
                if ($status == false || $status == "spam") {
                    return;
                }
            }

            if ($this -> get_option('commentformcheckbox') == "Y") {
                if (!empty($_POST['newsletter']) && $_POST['newsletter'] == 1) {
                    if (!empty($comment_id)) {
                        if ($comment = get_comment($comment_id)) {
                            global $Mailinglist, $Subscriber, $SubscribersList;

                            $data = array(
                                'email' 			=> 	$comment -> comment_author_email,
                                'mailinglists'		=>	array($this -> get_option('commentformlist')),
                                'fromregistration'	=>	false,
                                'justsubscribe'		=>	true,
                                'active'			=>	(($this -> get_option('requireactivate') == "Y") ? "N" : "Y"),
                            );

                            if ($Subscriber -> save($data, true)) {
                                $subscriber = $Subscriber -> get($Subscriber -> insertid, false);
                                $this -> subscription_confirm($subscriber);
                                $this -> admin_subscription_notification($subscriber);
                            }
                        }
                    }
                }
            }
        }

        function ratereview_hook($days = 30) {
            $this -> update_option('showmessage_ratereview', $days);
            $this -> delete_option('hidemessage_ratereview');
            $this -> delete_option('dismissed-ratereview');

            return true;
        }

        function upgrade_hook($days = 7) {
            $this -> update_option('showmessage_upgrade', $days);
            $this -> delete_option('dismissed-upgrade');
        }

        function countries_hook() {
            global $wpdb, $Subscriber;
            $countriessaved = 0;
            $query = "SELECT `id`, `ip_address`, `country` FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `country` = '' AND `ip_address` != '' LIMIT 1000;";
            if ($subscribers = $wpdb -> get_results($query)) {
                foreach ($subscribers as $subscriber) {
                    if ($ipcountry = $this -> get_country_by_ip($subscriber -> ip_address)) {
                        if ($Subscriber -> save_field('country', $ipcountry, $subscriber -> id)) {
                            $countriessaved++;
                        }
                    }
                }
            }

            echo wp_kses_post(sprintf(__('%s countries saved to subscribers from their IP address.', 'wp-mailinglist'), $countriessaved));
            return true;
        }

        function optimize_hook() {
            global $wpdb;
            $this -> check_tables();

            if (!empty($this -> tablenames)) {
                $query = "OPTIMIZE TABLE `" . implode("`, `", $this -> tablenames) . "`";
                $wpdb -> query($query);

                $query = "REPAIR TABLE `" . implode("`, `", $this -> tablenames) . "`";
                $wpdb -> query($query);
            }

            $this -> scheduling();

            return true;
        }

        function emailarchive_hook() {
            $emailarchive = $this -> get_option('emailarchive');
            if (!empty($emailarchive)) {
                global $wpdb, $Html, $Email;
                $emailarchive_olderthan = $this -> get_option('emailarchive_olderthan');
                $interval = (empty($emailarchive_olderthan)) ? 90 : $emailarchive_olderthan;
                $condition = " WHERE DATE_SUB(NOW(), INTERVAL " . esc_sql($interval) . " DAY) > created";

                $outfile_file = 'emailarchive.txt';
                $outfile_path = $Html -> uploads_path() . DS . $this -> plugin_name . DS  . 'export' . DS;
                $outfile_full = $outfile_path . $outfile_file;

                $fh = fopen($outfile_full, "w");
                fclose($fh);
                @chmod($outfile_full, 0755);

                if (file_exists($outfile_full) && is_writable($outfile_full)) {
                    $query = "SELECT * FROM " . $wpdb -> prefix . $Email -> table . $condition;
                    $command = 'mysql -h ' . DB_HOST . ' -u ' . DB_USER . ' -p' . DB_PASSWORD . ' ' . DB_NAME . ' -N -B -e "' . $query . '" | sed "s/\t/,/g" >> ' . $outfile_full;
                    $exec = exec($command, $output);

                    $query = "DELETE FROM " . $wpdb -> prefix . $Email -> table . $condition;
                    $wpdb -> query($query);
                }
            }

            return true;
        }

        function admin_notices() {
            global $Html;
            $screen = get_current_screen();
            $pagenow = $screen -> id;
            $page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");

            if (is_admin() && !defined('DOING_AJAX')) {
                $this -> check_uploaddir();
                $this -> get_managementpost();

                global $Html;
                $screen = get_current_screen();
                $pagenow = $screen -> id;
                $page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");

                //Open the menu accordingly
                if ((!empty($page) && in_array($page, (array) $this -> sections)) || (!empty($pagenow) && $pagenow == "newsletter")) {
                    $Html -> wp_has_current_submenu($page, $this -> menus, $this -> submenus);
                }

                // Rate & Review
                $showmessage_ratereview = $this -> get_option('showmessage_ratereview');
                if (!empty($showmessage_ratereview)) {
                    $rate_url = "https://wordpress.org/support/plugin/newsletters-lite/reviews/?rate=5#new-post";
                    $works_url = "https://wordpress.org/plugins/newsletters-lite/?compatibility[version]=" . get_bloginfo("version") . "&compatibility[topic_version]=" . $this -> version . "&compatibility[compatible]=1";
                    $message = sprintf(__('You have been using %s for %s days or more. Please consider to %s on %s. We appreciate it very much!', 'wp-mailinglist'), '<a href="https://wordpress.org/plugins/newsletters-lite/" target="_blank">' . __('Tribulant Newsletters', 'wp-mailinglist') . '</a>', $showmessage_ratereview, '<a href="' . $rate_url . '" target="_blank" class="button"><i class="fa fa-star"></i> ' . __('leave your rating', 'wp-mailinglist') . '</a>', '<a href="https://wordpress.org/plugins/newsletters-lite/" target="_blank">WordPress.org</a>');
                    $this -> render_message($message, false, true, 'ratereview');
                }

                // Upgrade


                // Submit Serial
                if (!$this -> ci_serial_valid() && (empty($page) || $page != $this -> sections -> submitserial)) {
                    $message = sprintf(__('To activate Newsletters PRO, please submit a serial key, else %s', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=submitserial') . '">' . __('continue using Newsletters LITE', 'wp-mailinglist') . '</a>');
                    $message .= ' <a class="button button-primary" id="' . $this -> pre . 'submitseriallink" href="' . admin_url('admin.php') . '?page=' . $this -> sections -> submitserial . '">' . __('Submit Serial Key', 'wp-mailinglist') . '</a>';
                    $message .= ' <a class="button button-secondary" href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">' . __('Upgrade to PRO', 'wp-mailinglist') . '</a>';
                    $message .= ' <a style="text-decoration:none;" href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=submitserial') . '" class=""><i class="fa fa-times"></i></a>';
                    $this -> render_message($message, false, true, 'submitserial');

                    ?>

                    <script type="text/javascript">
                        jQuery(document).ready(function(e) {
                            jQuery('#<?php echo esc_html($this -> pre); ?>submitseriallink').click(function() {
                                jQuery.colorbox({href:newsletters_ajaxurl + "action=<?php echo esc_html($this -> pre); ?>serialkey&security=<?php echo esc_html( wp_create_nonce('serialkey')); ?>"});
                                return false;
                            });
                        });
                    </script>

                    <?php
                }

                // Is DISABLE_WP_CRON defined?
                if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) {
                    $hidemessage_disablewpcron = $this -> get_option('hidemessage_disablewpcron');
                    if (empty($hidemessage_disablewpcron)) {
                        $message = '<div id="error" class="updated notice is-dismissible error">';
                        $message .= '<h3><i class="fa fa-envelope"></i> ' . __('Your WordPress cron is turned off!', 'wp-mailinglist') . '</h3>';
                        $message .= '<p>';
                        $message .= __('You have <code>DISABLED_WP_CRON</code> in your <code>wp-config.php</code> file, <i>are you aware?</i>', 'wp-mailinglist');
                        $message .= '<br/>';
                        $message .= __('Some features may not work as expected if the WordPress cron is not working.', 'wp-mailinglist');
                        $message .= '</p>';

                        if (apply_filters('newsletters_whitelabel', true)) {
                            $message .= '<p>';
                            $message .= '<a href="' . $Html -> retainquery(array('newsletters_method' => "hidemessage", 'message' => "disablewpcron")) . '" class="button button-default"><i class="fa fa-check"></i> ' . __('Yes I know, hide this message', 'wp-mailinglist') . '</a> ';
                            $message .= '<a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/11164" target="_blank" class="button button-primary"><i class="fa fa-question-circle"></i> ' . __('No, how can I fix it?', 'wp-mailinglist') . '</a>';
                            $message .= '</p>';
                        }

                        $message .= '</div>';
                        echo wp_kses_post($message);
                    }
                }

                // Database update required?
                $hidedbupdate = $this -> get_option('hidedbupdate');
                $showmessage_dbupdate = $this -> get_option('showmessage_dbupdate');
                if (!empty($showmessage_dbupdate)) {
                    if (empty($hidedbupdate) || (!empty($hidedbupdate) && version_compare($this -> dbversion, $hidedbupdate, '>'))) {
                        $message = sprintf(__('Newsletters requires a database update to continue %s. Make a database backup before running this.', 'wp-mailinglist'), '<a class="button" href="' . $Html -> retainquery('newsletters_method=dbupdate') . '">' . __('do it now', 'wp-mailinglist') . '</a>');
                        $message .= $Html -> help(__('Depending on your current database, this may take some time. If it times out for any reason, please refresh.', 'wp-mailinglist'));
                        $message .= ' <a class="button button-secondary" href="' . $Html -> retainquery('newsletters_method=hidemessage&message=dbupdate&version=' . $this -> dbversion) . '"><i class="fa fa-times"></i> ' . __('Hide', 'wp-mailinglist') . '</a>';
                        $this -> render_error($message, false, true, 'dbupdate');
                    }
                }

                global $queue_count, $queue_status;

                if (!empty($_GET[$this -> pre . 'updated'])) {
                    $this -> render_message(sanitize_text_field(urldecode(wp_unslash($_GET[$this -> pre . 'message']))));
                }

                if (!empty($_GET[$this -> pre . 'error'])) {
                    $this -> render_error(sanitize_text_field(urldecode(wp_unslash($_GET[$this -> pre . 'message']))));
                }

                if (!empty($_GET['newsletters_exportlink'])) {
                    $message = sprintf(__('Your export is ready. %s', 'wp-mailinglist'), '<a class="button button-secondary" href="' . $Html -> retainquery('wpmlmethod=exportdownload&file=' .  sanitize_text_field(wp_unslash($_GET['newsletters_exportlink'])), $this -> url) . '"><i class="fa fa-download fa-fw"></i>' . __('Download', 'wp-mailinglist') . '</a>');
                    $this -> render_message($message);
                }

                if (current_user_can('edit_plugins')) {
                    $folder = $Html -> uploads_path();
                    if (file_exists($folder)) {
                        if (is_writable($folder)) {
                            //all good
                        } else {
                            $this -> render_error(sprintf(__('Folder named "%s" is not writable', 'wp-mailinglist'), $folder));
                        }
                    } else {
                        $this -> render_error(sprintf(__('Folder named "%s" does not exist', 'wp-mailinglist'), $folder));
                    }
                }

                if (current_user_can('newsletters_queue')) {
                    /* Inside the plugin sections only */
                    if (!empty($page) && in_array($page, (array) $this -> sections)) {
                        global $wpdb;

                        $hidemessage_queue_status = $this -> get_option('hidemessage_queue_status');
                        if (empty($hidemessage_queue_status) && !empty($queue_count)) {
                            if (!empty($queue_status) && $queue_status == "pause") {
                                $message = sprintf(__('The %s is currently paused, please unpause it to send out emails.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> queue) . '">' . __('email queue', 'wp-mailinglist') . '</a>');
                                $message .= ' <a class="button button-secondary" href="' . admin_url('admin.php?page=' . $this -> sections -> welcome . '&newsletters_method=hidemessage&message=queue_status') . '" class=""><i class="fa fa-times fa-fw"></i>' . __('Hide', 'wp-mailinglist') . '</a>';
                                $this -> render_error($message, false, true, 'queue_status');
                            }
                        }
                    }
                }

                // GDPR
                $message = sprintf(__('Newsletters: Are you GDPR compliant? Check the %s to make sure you comply.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> gdpr) . '" class="button button-primary">' . __('GDPR Requirements', 'wp-mailinglist') . '</a>');
                $this -> render_info($message, false, true, "gdpr");

                // Is an Update Available?
                /*if (!empty($page) && in_array($page, (array) $this -> sections)) {
					if (apply_filters('newsletters_updates', true)) {
						if (current_user_can('edit_plugins') && $this -> has_update() && (empty($page) || (!empty($page) && $page != $this -> sections -> settings_updates))) {
							$hideupdate = $this -> get_option('hideupdate');
							if (empty($hideupdate) || (!empty($hideupdate) && version_compare($this -> version, $hideupdate, '>'))) {
								$update = $this -> vendor('update');
								$update_info = $update -> get_version_info();
								$this -> render('update', array('update_info' => $update_info), true, 'admin');
							}
						}
					}
				}*/
            }
        }

        function feed_newsletters() {
            $this -> debugging(false);
            global $Db;
            header("Content-Type: application/xml");
            $data = '<?xml version="1.0" encoding="UTF-8"?>';
            $emails = $this -> History() -> find_all(array('sent' => "> 0"), false, array('modified', "DESC"));
            $data .= $this -> render('feed-newsletters', array('emails' => $emails), false, 'default');
            echo wp_kses_post($data);
        }

        function end_session() {
            $managementauthtype = $this -> get_option('managementauthtype');
            if (!empty($managementauthtype) && ($managementauthtype == 2 || $managementauthtype == 3)) {
                session_destroy();
            }
        }

        function custom_post_types() {
            $custompostslug = $this -> get_option('custompostslug');
            $custompostslug = (empty($custompostslug)) ? 'newsletter' : $custompostslug;

            $custompostarchive = $this -> get_option('custompostarchive');
            $public = ((!empty($custompostarchive)) ? true : false);

            $newsletter_args = array(
                'label'					=>	esc_html($this -> name, 'wp-mailinglist'),
                'labels'				=>	array(
                    'name' 					=> 	esc_html($this -> name, 'wp-mailinglist'),
                    'singular_name' 		=> 	__('Newsletter', 'wp-mailinglist'),
                    'menu_name'				=>	__('Manage Newsletters', 'wp-mailinglist'),
                    'add_new'				=>	__('Add Newsletter', 'wp-mailinglist'),
                    'add_new_item'			=>	__('Add New Newsletter', 'wp-mailinglist'),
                    'edit_item'				=>	__('Edit Newsletter', 'wp-mailinglist'),
                    'new_item'				=>	__('New Newsletter', 'wp-mailinglist'),
                    /*'view_item' - Default is View Post/View Page.
					'view_items' - Label for viewing post type archives. Default is 'View Posts' / 'View Pages'.
					'search_items' - Default is Search Posts/Search Pages.
					'not_found' - Default is No posts found/No pages found.
					'not_found_in_trash' - Default is No posts found in Trash/No pages found in Trash.
					'parent_item_colon' - This string isn't used on non-hierarchical types. In hierarchical ones the default is 'Parent Page:'.
					'all_items' - String for the submenu. Default is All Posts/All Pages.
					'archives' - String for use with archives in nav menus. Default is Post Archives/Page Archives.
					'attributes' - Label for the attributes meta box. Default is 'Post Attributes' / 'Page Attributes'.
					'insert_into_item' - String for the media frame button. Default is Insert into post/Insert into page.
					'uploaded_to_this_item' - String for the media frame filter. Default is Uploaded to this post/Uploaded to this page.
					'featured_image' - Default is Featured Image.
					'set_featured_image' - Default is Set featured image.
					'remove_featured_image' - Default is Remove featured image.
					'use_featured_image' - Default is Use as featured image.
					'menu_name' - Default is the same as `name`.
					'filter_items_list' - String for the table views hidden heading.
					'items_list_navigation' - String for the table pagination hidden heading.
					'items_list' - String for the table hidden heading.
					'name_admin_bar' - String for use in New in Admin menu bar. Default is the same as `singular_name`.*/
                ),
                'description'			=>	__('Emails/newsletters', 'wp-mailinglist'),
                'public'				=>	$public,
                'show_ui'				=>	true,
                'show_in_menu'			=>	false,
                //'show_in_menu'			=>	$this -> sections -> welcome,
                //'menu_position'			=>	100,
                'show_in_rest'			=>	true,
                'hierarchical'			=>	false,
                'has_archive'			=>	$public,
                'rewrite'				=>	array('slug' => $custompostslug, 'with_front' => false),
                'supports'				=>	array(
                    'title',
                    'editor',
                    //'author',
                    'revisions',
                    //'excerpt',
                    //'custom-fields',
                    //'thumbnail',
                    //'page-attributes'
                ),
            );

            register_post_type($custompostslug, $newsletter_args);
        }

        function init_early() {
            $managementauthtype = $this -> get_option('managementauthtype');
            if (!empty($managementauthtype) && ($managementauthtype == 2 || $managementauthtype == 3)) {
                if (!session_id() && !headers_sent()) {
                    session_start(array('read_and_close' => true));
                }
            }

            $wpmlmethod = (empty($_POST[$this -> pre . 'method'])) ? null : sanitize_text_field(wp_unslash($_POST[$this -> pre . 'method']));
            $method = (empty($_GET[$this -> pre . 'method'])) ? $wpmlmethod : sanitize_text_field(wp_unslash($_GET[$this -> pre . 'method']));

            switch ($method) {
                case 'track'			:
                    global $Html, $Db, $Email;
                    $id = sanitize_text_field(wp_unslash($_GET['id']));

                    if (!empty($id)) {
                        $Db -> model = $Email -> model;
                        $Db -> save_field('read', "Y", array('eunique' => $id));
                        $Db -> save_field('status', "sent", array('eunique' => $id));
                        $Db -> save_field('device', $this -> get_device(), array('eunique' => $id));
                    }

                    $tracking = $this -> get_option('tracking');
                    $tracking_image = $this -> get_option('tracking_image');
                    $tracking_image_file = $this -> get_option('tracking_image_file');

                    if (!empty($tracking_image) && $tracking_image == "custom") {
                        $tracking_image_full = $Html -> uploads_path() . DS . $this -> plugin_name . DS . $tracking_image_file;
                        $imginfo = getimagesize($tracking_image_full);
                        header("Content-type: " . $imginfo['mime']);
                        readfile($tracking_image_full);
                    } else {
                        header("Content-Type: image/jpeg");
                        $image = imagecreate(1, 1);
                        imagejpeg($image);
                        imagedestroy($image);
                    }

                    exit();

                    break;
            }
        }

        function init() {
            global $Db, $Email, $Html, $Mailinglist, $Subscriber, $SubscribersList;

            $this -> clear_memcached();

            $wpmlmethod = (empty($_POST[$this -> pre . 'method'])) ? null :  sanitize_text_field(wp_unslash($_POST[$this -> pre . 'method']));
            $method = (empty($_GET[$this -> pre . 'method'])) ? $wpmlmethod :  sanitize_text_field(wp_unslash($_GET[$this -> pre . 'method']));

            if (!empty($_GET[$this -> pre . 'link']) || !empty($_GET['newsletters_link'])) {
                $link_hash = (empty($_GET[$this -> pre . 'link'])) ? sanitize_text_field(wp_unslash($_GET['newsletters_link'])) : sanitize_text_field(wp_unslash($_GET[$this -> pre . 'link']));

                if ($link = $this -> Link() -> find(array('hash' => $link_hash))) {

                    $email_conditions = array('history_id' => sanitize_text_field(isset($_GET['history_id']) ? $_GET['history_id'] : 0));
                    if (!empty($_GET['subscriber_id'])) { $email_conditions['subscriber_id'] = sanitize_text_field(isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : 0); }
                    if (!empty($_GET['user_id'])) { $email_conditions['user_id'] = sanitize_text_field(isset($_GET['user_id']) ? $_GET['user_id'] : 0); }

                    if (!empty($email_conditions['history_id']) && (!empty($email_conditions['subscriber_id']) || !empty($email_conditions['user_id']))) {
                        $Db -> model = $Email -> model;
                        $Db -> save_field('read', "Y", $email_conditions);
                        $Db -> model = $Email -> model;
                        $Db -> save_field('status', "sent", $email_conditions);
                    }

                    $click_data = array(
                        'link_id'			=>	$link -> id,
                        'history_id'		=>	sanitize_text_field(isset($_GET['history_id']) ? $_GET['history_id'] : 0),
                        'user_id'			=>	sanitize_text_field(isset($_GET['user_id']) ? $_GET['user_id'] : 0),
                        'subscriber_id'		=>	sanitize_text_field(isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : 0),
                        'device'			=>	$this -> get_device()
                    );

                    $link -> link = html_entity_decode($link -> link);

                    if ($this -> Click() -> save($click_data, true)) {

                        do_action('newsletters_click', $click_data, $link);

                        header("Location: " . $link -> link);
                        exit();
                    }
                }
            }

            $newsletters_method = sanitize_text_field(isset($_GET['newsletters_method']) ? $_GET['newsletters_method'] : "");
            if (!empty($newsletters_method)) {
                switch ($newsletters_method) {
                    case 'newsletter'					:
                        global $Db, $Subscriber;
                        header('Content-type: text/html; charset=utf-8');

                        $id = sanitize_text_field(isset($_GET['id']) ? $_GET['id']  : 0);
                        if (!empty($id)) {
                            if ($email = $this -> History() -> find(array('id' => $id))) {
                                $Db -> model = $Subscriber -> model;
                                $subscriber = $Subscriber -> get(sanitize_text_field(isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : 0), false);

                                $clicktrack = $this -> get_option('clicktrack');
                                if (!empty($clicktrack) && $clicktrack == "Y") {
                                    $click_data = array(
                                        'referer'			=>	"online",
                                        'history_id'		=>	esc_html($email -> id),
                                        'user_id'			=>	sanitize_text_field(isset($_GET['user_id']) ? $_GET['user_id'] : 0 ),
                                        'subscriber_id'		=>	sanitize_text_field(isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : 0),
                                        'device'			=>	$this -> get_device()
                                    );

                                    $this -> Click() -> save($click_data, true);
                                }
                                if(isset($subscriber) && !empty($subscriber)) {
                                    $subscriber->mailinglist_id = sanitize_text_field(isset($_GET['mailinglist_id']) ? $_GET['mailinglist_id'] : 0);
                                }
                                $authkey = sanitize_text_field(isset($_GET['authkey']) ? $_GET['authkey'] : '');

                                // Check if the subscriber's authkey and the authkey matches
                                if (isset($subscriber -> authkey ) && $subscriber -> authkey != $authkey) {
                                    $subscriber = false;
                                }

                                $message = $email -> message;
                                $content = $this -> render_email('send', array('print' => sanitize_text_field(isset($_GET['print']) ? $_GET['print'] : ''), 'message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $id), false, true, true, $email -> theme_id);
                                $output = "";
                                ob_start();
                                $thecontent = do_shortcode(wp_unslash($content));
                                // phpcs:ignore
                                echo apply_filters('wpml_online_newsletter', $thecontent, $subscriber);
                                $output = ob_get_clean();
                                $user_redefined = isset($user) ? $user : null;
                                echo $this -> inlinestyles($this -> process_set_variables($subscriber, $user_redefined, $output, esc_html($email -> id)));
                                exit();
                            } else {
                                $message = wp_kses_post(__('Newsletter cannot be read', 'wp-mailinglist'));
                            }
                        } else {
                            $message = wp_kses_post(__('No newsletter was specified', 'wp-mailinglist'));
                        }

                        if (!empty($message)) {
                            ?>

                            <script type="text/javascript">
                                alert('<?php echo wp_kses_post(addslashes($message)); ?>');
                            </script>

                            <?php
                        }
                        break;
                    case 'dbupdate'						:
                        $this -> remove_server_limits();
                        $cur_version = $this -> get_option('dbversion');
                        $new_version = $cur_version;
                        $this -> delete_option('showmessage_dbupdate');

                        if (version_compare($cur_version, '1.2') < 0) {	// 1.0
                            global $wpdb, $Db, $Field, $Mailinglist, $Subscriber;

                            $this -> update_options();

                            $query = "TRUNCATE `" . $wpdb -> prefix . $this -> Option() -> table . "`";
                            $wpdb -> query($query);
                            $query = "TRUNCATE `" . $wpdb -> prefix . $this -> SubscribersOption() -> table . "`";
                            $wpdb -> query($query);

                            $query = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE `type` = 'radio' OR `type` = 'select' OR `type` = 'checkbox'";
                            if ($fields = $wpdb -> get_results($query)) {
                                foreach ($fields as $field) {
                                    if (!empty($field -> fieldoptions)) {
                                        $fieldoptions = maybe_unserialize($field -> fieldoptions);

                                        if (!empty($fieldoptions) && is_array($fieldoptions)) {
                                            $o = 1;

                                            foreach ($fieldoptions as $fieldoption) {
                                                $option_data = array(
                                                    'id'					=>	false,
                                                    'order'					=>	$o,
                                                    'value'					=>	$fieldoption,
                                                    'field_id'				=>	$field -> id,
                                                );

                                                $this -> Option() -> save($option_data);
                                                $this -> Option() -> id = $this -> Option() -> data = false;
                                                $o++;
                                            }

                                            // Subscriber stuff
                                            $newfieldoptions = $this -> Option() -> find_all(array('field_id' => $field -> id), false, array('order', "ASC"));
                                            $newfieldoptionsarray = array();
                                            foreach ($newfieldoptions as $newfieldoption) {
                                                $newfieldoptionsarray[$newfieldoption -> id] = $newfieldoption -> value;
                                            }

                                            $query = "SELECT `id`, `" . $field -> slug . "` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE `" . esc_sql($field -> slug) . "` != ''";
                                            if ($subscriber_fields = $wpdb -> get_results($query)) {
                                                foreach ($subscriber_fields as $subscriber_field) {
                                                    $subscriber_fieldoptions = maybe_unserialize($subscriber_field -> {$field -> slug});

                                                    if (!empty($subscriber_fieldoptions)) {
                                                        $new_subscriber_fieldoptions = array();

                                                        if (is_array($subscriber_fieldoptions)) {
                                                            foreach ($subscriber_fieldoptions as $subscriber_fieldoption) {
                                                                $option_id = array_search($fieldoptions[$subscriber_fieldoption], $newfieldoptionsarray);

                                                                $subscribers_option_data = array(
                                                                    'subscriber_id'					=>	$subscriber_field -> id,
                                                                    'field_id'						=>	$field -> id,
                                                                    'option_id'						=>	$option_id,
                                                                );

                                                                $this -> SubscribersOption() -> save($subscribers_option_data);
                                                                $new_subscriber_fieldoptions[] = $option_id;
                                                            }

                                                            $new_subscriber_fieldoptions = maybe_serialize($new_subscriber_fieldoptions);
                                                        } else {
                                                            $option_id = array_search($fieldoptions[$subscriber_fieldoptions], $newfieldoptionsarray);

                                                            $subscribers_option_data = array(
                                                                'subscriber_id'					=>	$subscriber_field -> id,
                                                                'field_id'						=>	$field -> id,
                                                                'option_id'						=>	$option_id,
                                                            );

                                                            $this -> SubscribersOption() -> save($subscribers_option_data);
                                                            $new_subscriber_fieldoptions = $option_id;
                                                        }

                                                        if (!empty($newfieldoptionsarray[$new_subscriber_fieldoptions])) {
                                                            $Db -> model = $Subscriber -> model;
                                                            $Db -> save_field($field -> slug, $new_subscriber_fieldoptions, array('id' => $subscriber_field -> id));
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // Increase custom field title length
                            $query = "ALTER TABLE " . $wpdb -> prefix . $Field -> table . " CHANGE `title` `title` VARCHAR(255) NOT NULL DEFAULT ''";
                            $wpdb -> query($query);
                            // Increase mailing list title length
                            $query = "ALTER TABLE " . $wpdb -> prefix . $Mailinglist -> table . " CHANGE `title` `title` VARCHAR(255) NOT NULL DEFAULT ''";
                            $wpdb -> query($query);
                            // Increase group title length
                            $query = "ALTER TABLE " . $wpdb -> prefix . $this -> Group() -> table . " CHANGE `title` `title` VARCHAR(255) NOT NULL DEFAULT ''";
                            $wpdb -> query($query);
                            // Increase history subject length
                            $query = "ALTER TABLE " . $wpdb -> prefix . $this -> History() -> table . " CHANGE `subject` `subject` VARCHAR(255) NOT NULL DEFAULT ''";
                            $wpdb -> query($query);

                            $new_version = '1.2';
                        }

                        if (version_compare($cur_version, '1.2.1') < 0) {
                            global $wpdb, $Db, $Field, $Subscriber;

                            $Db -> model = $Field -> model;
                            if ($fields = $Db -> find_all(array('type' => "'pre_date'"))) {
                                foreach ($fields as $field) {
                                    $query = "SELECT `id`, `" . $field -> slug . "` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE `" . $field -> slug . "` != '' AND `" . $field -> slug . "` != '0000-00-00'";
                                    if ($subscribers = $wpdb -> get_results($query)) {
                                        foreach ($subscribers as $subscriber) {
                                            $new_date = date_i18n("Y-m-d", strtotime($subscriber -> {$field -> slug}));
                                            $query = "UPDATE `" . $wpdb -> prefix . $Subscriber -> table . "` SET `" . $field -> slug . "` = '" . $new_date . "' WHERE `id` = '" . $subscriber -> id . "'";
                                            $wpdb -> query($query);
                                        }
                                    }
                                }

                                $this -> change_field($Subscriber -> table, $field -> slug, $field -> slug, "DATE NOT NULL DEFAULT '0000-00-00'");
                            }

                            $new_version = '1.2.1';
                        }

                        if (version_compare($cur_version, '1.2.2') < 0) {
                            global $wpdb, $Db, $Field, $Mailinglist, $Subscriber;

                            $this -> update_options();

                            // truncate the SubscribersOption database table.
                            $query = "TRUNCATE `" . $wpdb -> prefix . $this -> SubscribersOption() -> table . "`";
                            $wpdb -> query($query);

                            $query = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE `type` = 'radio' OR `type` = 'select' OR `type` = 'checkbox'";
                            if ($fields = $wpdb -> get_results($query)) {
                                foreach ($fields as $field) {

                                    $newfieldoptions = $this -> Option() -> find_all(array('field_id' => $field -> id), false, array('order', "ASC"));
                                    $newfieldoptionsarray = array();
                                    foreach ($newfieldoptions as $newfieldoption) {
                                        $newfieldoptionsarray[$newfieldoption -> id] = $newfieldoption -> value;
                                    }

                                    $query = "SELECT `id`, `" . $field -> slug . "` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE `" . $field -> slug . "` != ''";
                                    if ($subscriber_fields = $wpdb -> get_results($query)) {
                                        foreach ($subscriber_fields as $subscriber_field) {
                                            $subscriber_fieldoptions = maybe_unserialize($subscriber_field -> {$field -> slug});

                                            if (!empty($subscriber_fieldoptions)) {
                                                $new_subscriber_fieldoptions = array();

                                                if (is_array($subscriber_fieldoptions)) {
                                                    foreach ($subscriber_fieldoptions as $subscriber_fieldoption) {
                                                        $option_id = $subscriber_fieldoption;

                                                        $subscribers_option_data = array(
                                                            'subscriber_id'					=>	$subscriber_field -> id,
                                                            'field_id'						=>	$field -> id,
                                                            'option_id'						=>	$option_id,
                                                        );

                                                        $this -> SubscribersOption() -> save($subscribers_option_data);
                                                    }
                                                } else {
                                                    $option_id = $subscriber_fieldoption;

                                                    $subscribers_option_data = array(
                                                        'subscriber_id'					=>	$subscriber_field -> id,
                                                        'field_id'						=>	$field -> id,
                                                        'option_id'						=>	$option_id,
                                                    );

                                                    $this -> SubscribersOption() -> save($subscribers_option_data);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $new_version = '1.2.2';
                        }

                        // 1.2.3
                        if (version_compare($cur_version, '1.2.3') < 0) {
                            global $wpdb, $Db, $Field, $Mailinglist, $Subscriber;

                            $this -> update_options();
                            $this -> initialize_classes();

                            // truncate the SubscribersOption database table.
                            $query = "TRUNCATE `" . $wpdb -> prefix . $this -> SubscribersOption() -> table . "`";
                            $wpdb -> query($query);

                            $query = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE (`type` = 'radio' OR `type` = 'select' OR `type` = 'checkbox') AND `slug` != 'email' AND `slug` != 'list'";
                            if ($fields = $wpdb -> get_results($query)) {
                                foreach ($fields as $field) {
                                    $query = "SELECT `id`, `" . $field -> slug . "` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE `" . $field -> slug . "` != ''";
                                    if ($subscriber_fields = $wpdb -> get_results($query)) {
                                        foreach ($subscriber_fields as $subscriber_field) {

                                            $subscriber_fieldoptions = maybe_unserialize($subscriber_field -> {$field -> slug});

                                            if (!empty($subscriber_fieldoptions)) {
                                                if (is_array($subscriber_fieldoptions)) {
                                                    foreach ($subscriber_fieldoptions as $subscriber_fieldoption) {
                                                        $option_id = $subscriber_fieldoption;

                                                        if (!empty($option_id)) {
                                                            $subscribers_option_data = array(
                                                                'subscriber_id'					=>	$subscriber_field -> id,
                                                                'field_id'						=>	$field -> id,
                                                                'option_id'						=>	$option_id,
                                                            );

                                                            $this -> SubscribersOption() -> save($subscribers_option_data);
                                                        }
                                                    }
                                                } else {
                                                    $option_id = $subscriber_fieldoptions;

                                                    if (!empty($option_id)) {
                                                        $subscribers_option_data = array(
                                                            'subscriber_id'					=>	$subscriber_field -> id,
                                                            'field_id'						=>	$field -> id,
                                                            'option_id'						=>	$option_id,
                                                        );

                                                        $this -> SubscribersOption() -> save($subscribers_option_data);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $new_version = '1.2.3';
                        }

                        $this -> update_option('dbversion', $new_version);
                        $this -> render_message(7);

                        break;
                    case 'set_user_option'				:
                        if (!empty($_GET['option']) && !empty($_GET['value'])) {
                            $this -> update_user_option(get_current_user_id(), sanitize_text_field(wp_unslash($_GET['option'])), sanitize_text_field(wp_unslash($_GET['value'])));
                        }

                        $this -> redirect(esc_url_raw($Html -> retainquery(sanitize_text_field(wp_unslash($_GET['option'])) . '=' . urlencode(sanitize_text_field(wp_unslash($_GET['value']))), $this -> referer)));
                        break;
                    case 'management_loginp'			:
                        global $newsletters_errors, $Subscriber, $Authnews, $Db;

                        $management_password = $this -> get_option('management_password');
                        if (!empty($management_password)) {
                            if (!empty($_POST)) {
                                $email = sanitize_text_field(wp_unslash($_POST['email']));
                                $password = sanitize_text_field(wp_unslash($_POST['password']));

                                if (empty($email)) { $newsletters_errors['emailp'] = __('Fill in your email address', 'wp-mailinglist'); }
                                elseif (!$subscriber = $Subscriber -> get_by_email($email)) { $newsletters_errors['emailp'] = __('Email address not found', 'wp-mailinglist'); }
                                else {
                                    if (empty($password)) { $newsletters_errors['password'] = __('Fill in your password', 'wp-mailinglist'); }
                                    elseif (md5($password) != $subscriber -> password) { $newsletters_errors['password'] = __('Password is incorrect', 'wp-mailinglist'); }
                                    else {

                                        $subscriberauth = $this -> gen_auth($subscriber -> id);
                                        $Authnews -> set_cookie($subscriberauth);
                                        $Authnews -> set_emailcookie($email);
                                        $Db -> model = $Subscriber -> model;
                                        $Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));

                                        $this -> redirect($this -> get_managementpost(true));
                                    }
                                }
                            } else {
                                $newsletters_errors[] = __('No data was posted', 'wp-mailinglist');
                            }
                        } else {
                            $newsletters_errors[] = __('This login is currently disabled', 'wp-mailinglist');
                        }

                        break;
                    case 'management_login'				:
                        global $Subscriber, $Field, $Authnews, $Html, $newsletters_errors;
                        $emailfield = $Field -> email_field();
                        $newsletters_errors = array();

                        if (!empty($_POST)) {
                            if (!empty($_POST['email'])) {
                                if ($Subscriber -> email_validate(sanitize_text_field(wp_unslash($_POST['email'])))) {
                                    $Db -> model = $Subscriber -> model;

                                    if ($subscriber = $Db -> find(array('email' => sanitize_text_field(wp_unslash($_POST['email']))))) {
                                        if ($subscriberauth = $this -> gen_auth($subscriber -> id)) {
                                            $Authnews -> set_emailcookie(sanitize_text_field(wp_unslash($_POST['email'])));

                                            $Db -> model = $Subscriber -> model;
                                            $Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));

                                            $subject = wp_unslash(esc_html($this -> et_subject('authenticate', $subscriber)));
                                            $fullbody = $this -> et_message('authenticate', $subscriber);
                                            $message = $this -> render_email(false, array('subscriber' => $subscriber), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('authenticate'), false, $fullbody);
                                            $eunique = $Html -> eunique($subscriber, false, "authentication");

                                            if ($this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "authentication")) {
                                                $_REQUEST['updated'] = true;
                                                $_REQUEST['success'] = __('Authentication email has been sent, please check your inbox.', 'wp-mailinglist');
                                            } else {
                                                $newsletters_errors[] = __('Authentication email could not be sent.', 'wp-mailinglist');
                                            }
                                        } else {
                                            $newsletters_errors[] = __('Authentication string could not be created.', 'wp-mailinglist');
                                        }
                                    } else {
                                        $newsletters_errors['email'] = __('Email address not found', 'wp-mailinglist');
                                    }
                                } else {
                                    $newsletters_errors['email'] = __('Please fill in a valid email address.', 'wp-mailinglist');
                                }
                            } else {
                                $newsletters_errors['email'] = esc_html($emailfield -> error);
                            }
                        } else {
                            $newsletters_errors[] = __('No data was posted.', 'wp-mailinglist');
                        }
                        break;
                    case 'delete_transient'				:
                        if (!empty($_GET['transient'])) {
                            delete_transient(sanitize_text_field(wp_unslash($_GET['transient'])));
                            $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> queue));
                        }
                        break;
                    case 'hidemessage'					:
                        if (!empty($_GET['message'])) {
                            $message = sanitize_text_field(wp_unslash($_GET['message']));

                            switch ($_GET['message']) {
                                case 'submitserial'				:
                                    $this -> update_option('hidemessage_submitserial', true);
                                    break;
                                case 'ratereview'				:
                                    $this -> update_option('hidemessage_ratereview', true);
                                    break;
                                case 'dbupdate'					:
                                    $this -> delete_option('showmessage_dbupdate');
                                    $this -> update_option('hidedbupdate', sanitize_text_field(wp_unslash($_GET['version'])));
                                    break;
                                case 'queue_status'				:
                                    $this -> update_option('hidemessage_queue_status', true);
                                    break;
                                case 'disablewpcron'		:
                                    $this -> update_option('hidemessage_disablewpcron', 1);
                                    break;
                            }
                        }

                        do_action('newsletters_hidemessage', $message);

                        $this -> redirect($this -> referer);
                        break;
                    case 'hideupdate'					:
                        if (!empty($_GET['version'])) {
                            $this -> update_option('hideupdate', sanitize_text_field(wp_unslash($_GET['version'])));
                            $this -> redirect($this -> referer);
                        }
                        break;
                    case 'webhook'						:
                        $type = sanitize_text_field(wp_unslash($_GET['type']));

                        do_action('newsletters_webhook', $type);

                        switch ($type) {
                            case 'sparkpost'	:

                                $json = json_decode(file_get_contents("php://input"));

                                if (!empty($json)) {
                                    foreach ($json as $j) {
                                        if (!empty($j -> msys)) {
                                            foreach ($j -> msys as $event => $event_data) {
                                                $messageid = $event_data -> rcpt_meta -> messageid;

                                                if (!empty($messageid)) {
                                                    $Db -> model = $Email -> model;
                                                    if ($email = $Db -> find(array('messageid' => $messageid))) {
                                                        // found the original email
                                                    }

                                                    if (!empty($event_data -> rcpt_to)) {
                                                        $subscriber = $Subscriber -> get_by_email($event_data -> rcpt_to);
                                                    }

                                                    switch ($event) {
                                                        case 'message_event'			:
                                                            if (!empty($event_data -> type)) {
                                                                switch ($event_data -> type) {
                                                                    case 'bounce'					:
                                                                        $this -> bounce($subscriber -> email, "sparkpost", $event_data -> reason, $messageid);
                                                                        break;
                                                                    case 'spam_complaint'			:
                                                                        $this -> bounce($subscriber -> email, "sparkpost", $event_data -> report_by, $messageid);
                                                                        break;
                                                                    case 'delivery'					:
                                                                        // we can make sure the email is updated to Sent = Yes
                                                                        if (!empty($email)) {
                                                                            $Db -> model = $Email -> model;
                                                                            $Db -> save_field('status', "sent", array('id' => esc_html($email -> id)));
                                                                        }
                                                                        break;
                                                                }
                                                            }
                                                            break;
                                                        case 'track_event'				:
                                                            if (!empty($event_data -> type)) {
                                                                switch ($event_data -> type) {
                                                                    case 'click'					:
                                                                        // do nothing for now, click tracking can track this
                                                                        break;
                                                                    case 'open'						:
                                                                        // it's been opened, update the read status
                                                                        if (!empty($email)) {
                                                                            $Db -> model = $Email -> model;
                                                                            $Db -> save_field('read', "Y", array('id' => esc_html($email -> id)));
                                                                        }
                                                                        break;
                                                                }
                                                            }
                                                            break;
                                                        case 'unsubscribe_event'		:
                                                            if (!empty($event_data -> type)) {
                                                                switch ($event_data -> type) {
                                                                    case 'list_unsubscribe'			:
                                                                    case 'link_unsubscribe'			:
                                                                        // subscriber has unsubscribed
                                                                        if (!empty($subscriber)) {
                                                                            $this -> process_unsubscribe($subscriber, false, $email -> history_id);
                                                                        }
                                                                        break;
                                                                }
                                                            }
                                                            break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            case 'mailgun'		:
                                if (!empty($_POST)) {
                                    if (!empty($_POST['event'])) {
                                        $customDataMessageId = 0;

                                        if (!empty($_POST['my-custom-data'])) {
                                            // phpcs:ignore
                                            $custom_data = json_decode(wp_unslash($_POST['my-custom-data']));

                                            $customDataMessageId = sanitize_text_field($custom_data->MessageID);

                                            if (!empty($custom_data -> MessageID)) {
                                                $Db -> model = $Email -> model;
                                                if ($email = $Db -> find(array('messageid' => $customDataMessageId))) {
                                                    // found the original email
                                                }
                                            }
                                        }

                                        if (!empty($_POST['recipient'])) {
                                            $subscriber = $Subscriber -> get_by_email(sanitize_text_field(wp_unslash($_POST['recipient'])));
                                        }

                                        switch ($_POST['event']) {
                                            case 'opened'				:
                                                // it's been opened, update the read status
                                                if (!empty($email)) {
                                                    $Db -> model = $Email -> model;
                                                    $Db -> save_field('read', "Y", array('id' => $email -> id));
                                                }
                                            case 'clicked'				:
                                                //do nothing for now
                                                break;
                                            case 'complained'			:
                                                $this -> bounce(sanitize_text_field(wp_unslash($_POST['recipient'])), "mailgun", __('Spam complaint', 'wp-mailinglist'), $customDataMessageId);
                                                break;
                                            case 'bounced'				:
                                                $this -> bounce(sanitize_text_field(wp_unslash($_POST['recipient'])), "mailgun", sanitize_text_field(wp_unslash($_POST['code'])) . ' - ' . sanitize_text_field(wp_unslash($_POST['error'])), $customDataMessageId);
                                                break;
                                            case 'dropped'				:
                                                $this -> bounce(sanitize_text_field(wp_unslash($_POST['recipient'])), "mailgun", sanitize_text_field(wp_unslash($_POST['code'])) . ' - ' . sanitize_text_field(wp_unslash($_POST['description'])), $customDataMessageId);
                                                break;
                                            case 'unsubscribed'			:
                                                // subscriber has unsubscribed
                                                if (!empty($subscriber)) {
                                                    $this -> process_unsubscribe($subscriber, false, $email -> history_id);
                                                }
                                                break;
                                            case 'delivered'			:
                                                // we can make sure the email is updated to Sent = Yes
                                                if (!empty($email)) {
                                                    $Db -> model = $Email -> model;
                                                    $Db -> save_field('status', "sent", array('id' => $email -> id));
                                                }
                                                break;
                                        }
                                    }
                                }
                                break;
                            case 'sendgrid'		:
                                $json = json_decode(file_get_contents("php://input"));

                                if (!empty($json)) {
                                    foreach ($json as $event) {
                                        if (!empty($event)) {
                                            if (!empty($event -> event)) {
                                                if (!empty($event -> MessageID)) {
                                                    $Db -> model = $Email -> model;
                                                    if ($email = $Db -> find(array('messageid' => $event -> MessageID))) {
                                                        // found the original email
                                                    }
                                                }

                                                if (!empty($event -> email)) {
                                                    $subscriber = $Subscriber -> get_by_email($event -> email);
                                                }

                                                switch ($event -> event) {
                                                    case 'bounce'					:
                                                    case 'spamreport'				:
                                                    case 'dropped'					:
                                                        $this -> bounce($event -> email, "sendgrid", $event -> status . ' - ' . $event -> reason, $event -> MessageID);
                                                        break;
                                                    case 'click'					:
                                                        // a click will redirect and log click tracking
                                                        break;
                                                    case 'deferred'					:
                                                    case 'processed'				:
                                                        // do nothing for now
                                                        break;
                                                    case 'delivered'				:
                                                        // we can make sure the email is updated to Sent = Yes
                                                        if (!empty($email)) {
                                                            $Db -> model = $Email -> model;
                                                            $Db -> save_field('status', "sent", array('id' => $email -> id));
                                                        }
                                                        break;
                                                    case 'open'						:
                                                        // it's been opened, update the read status
                                                        if (!empty($email)) {
                                                            $Db -> model = $Email -> model;
                                                            $Db -> save_field('read', "Y", array('id' => $email -> id));
                                                        }
                                                        break;
                                                    case 'unsubscribe'				:
                                                        // subscriber has unsubscribed
                                                        if (!empty($subscriber)) {
                                                            $this -> process_unsubscribe($subscriber, false, $email -> history_id);
                                                        }
                                                        break;
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                        }

                        // done with webhooks
                        exit();
                        break;
                }
            }

            if (!empty($method)) {
                switch ($method) {
                    case 'exportdownload'					:
                        if (current_user_can('newsletters_welcome')) {
                            $file = sanitize_text_field(wp_unslash($_GET['file']));
                            if (!empty($file)) {
                                $filename = urldecode($file);
                                $filepath = $Html -> uploads_path() . '/' . $this -> plugin_name . '/export/';
                                $filefull = $filepath . $filename;

                                if (file_exists($filefull)) {
                                    if(ini_get('zlib.output_compression')) {
                                        ini_set('zlib.output_compression', 'Off');
                                    }

                                    $contenttype = (function_exists('mime_content_type')) ? mime_content_type($filefull) : "text/csv";
                                    header("Pragma: public");
                                    header("Expires: 0");
                                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                                    header("Cache-Control: public", false);
                                    header("Content-Description: File Transfer");
                                    header("Content-Type: text/csv");
                                    header("Accept-Ranges: bytes");
                                    header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
                                    header("Content-Transfer-Encoding: binary");
                                    header("Content-Length: " . filesize($filefull));

                                    if ($fh = fopen($filefull, 'rb')){
                                        while (!feof($fh) && connection_status() == 0) {
                                            @set_time_limit(0);
                                            //phpcs:ignore
                                            print(fread($fh, (1024 * 8)));
                                        }

                                        fclose($fh);
                                        exit();
                                        die();
                                    }
                                } else {
                                    $error = __('Export file could not be created', 'wp-mailinglist');
                                }
                            } else {
                                $error = __('No export file was specified', 'wp-mailinglist');
                            }
                        } else {
                            $error = __('You do not have permission to access exports', 'wp-mailinglist');
                        }

                        if (!empty($error)) {
                            // phpcs:ignore
                            wp_die($error);
                        }
                        break;
                    case 'docron'			:
                        if (!empty($_GET['auth'])) {
                            if ($this -> get_option('servercronstring') == $_GET['auth']) {
                                $this -> cron_hook();
                            } else {
                                // phpcs:ignore
                                _e('Authentication string does not match.', 'wp-mailinglist');
                            }
                        } else {
                            // phpcs:ignore
                            _e('No authentication string was specified.', 'wp-mailinglist');
                        }

                        exit();
                        break;
                    case 'defaultthemes'	:
                        if (current_user_can('edit_plugins') || is_super_admin()) {
                            $this -> initialize_default_themes();
                            esc_html_e('Stock templates have been added', 'wp-mailinglist');
                        } else {
                            esc_html_e('Please login as administrator for stock templates to be loaded', 'wp-mailinglist');
                        }

                        exit();
                        break;
                    case 'themebyname'		:
                        if (!empty($_GET['name'])) {
                            ob_start();
                            include $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS . sanitize_text_field(wp_unslash($_GET['name'])) . DS . 'index.html';
                            $content = ob_get_clean();
                            echo wp_kses_post($content);
                            exit();
                        }
                        break;
                    case 'themepreview'		:
                        $id = sanitize_text_field(wp_unslash($_GET['id']));
                        if (!empty($id)) {
                            global $Db, $Theme;
                            $Db -> model = $Theme -> model;
                            $subject = __('Newsletter Template Preview', 'wp-mailinglist');
                            $history_id = "123";

                            if ($theme = $Db -> find(array('id' => $id))) {
                                header('Content-Type: text/html');
                                echo do_shortcode(wp_unslash($theme -> content));
                            }
                        }

                        exit();
                        break;
                    case 'preview'			:
                        $this -> render_email('preview', false, true, $this -> htmltf($subscriber -> format), true, $this -> default_theme_id('sending'));
                        exit();
                        break;
                    case 'track'			:
                        global $Html;
                        $id = sanitize_text_field(wp_unslash($_GET['id']));
                        if (!empty($id)) {
                            $Db -> model = $Email -> model;
                            $Db -> save_field('read', "Y", array('eunique' => $id));
                            $Db -> save_field('status', "sent", array('eunique' => $id));
                            $Db -> save_field('device', $this -> get_device(), array('eunique' => $id));
                        }

                        $tracking = $this -> get_option('tracking');
                        $tracking_image = $this -> get_option('tracking_image');
                        $tracking_image_file = $this -> get_option('tracking_image_file');

                        if (!empty($tracking_image) && $tracking_image == "custom") {
                            $tracking_image_full = $Html -> uploads_path() . DS . $this -> plugin_name . DS . $tracking_image_file;
                            $imginfo = getimagesize($tracking_image_full);
                            header("Content-type: " . $imginfo['mime']);
                            readfile($tracking_image_full);
                        } else {
                            header("Content-Type: image/jpeg");
                            $image = imagecreate(1, 1);
                            imagejpeg($image);
                            imagedestroy($image);
                        }

                        exit();

                        break;
                    case 'offsite'			:
                        global $Html, $Subscriber, $Mailinglist;

                        $form_id = (empty($_GET['form'])) ? false :  sanitize_text_field(wp_unslash($_GET['form']));
                        $list = (empty($_GET['list'])) ? false :  sanitize_text_field(wp_unslash($_GET['list']));

                        if (!empty($_POST)) {
                            $_POST['form_id'] = $form_id;
                            if ($subscriber_id = $Subscriber -> optin($_POST)) {
                                $subscriber = $Subscriber -> get($subscriber_id);

                                if (!empty($form_id) && $form = $this -> Subscribeform() -> find(array('id' => $form_id))) {
                                    if (empty($form -> confirmationtype) || $form -> confirmationtype == "message") {
                                        if (!empty($form -> confirmation_message)) {
                                            echo '<div class="newsletters-acknowledgement">' . wp_kses_post(wpautop(($form -> confirmation_message))) . '</div>';

                                            exit();
                                        }
                                    } elseif ($form -> confirmationtype == "redirect") {
                                        if (!empty($form -> confirmation_redirect)) {
                                            $subscriberedirecturl = do_shortcode(wp_unslash(esc_html($form -> confirmation_redirect)));
                                            $subscriberedirecturl = $this -> process_set_variables($subscriber, false, $subscriberedirecturl);

                                            ?>

                                            <script type="text/javascript">
                                                if (window.opener && window.opener !== window) {
                                                    window.opener.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                                    window.close();
                                                } else if (window.top != window.self) {
                                                    window.top.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                                } else {
                                                    window.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                                }
                                            </script>

                                            <?php

                                            exit();
                                        }
                                    }
                                } elseif (!empty($list)) {
                                    $subscriberedirect = $this -> get_option('subscriberedirect');
                                    $subscribelist = $Mailinglist -> get($list);

                                    if (!empty($subscribelist -> subredirect)) {
                                        $subscriberedirecturl = esc_html($subscribelist -> subredirect);
                                    } elseif (!empty($subscriberedirect) && $subscriberedirect == "Y") {
                                        $subscriberedirecturl = $this -> get_option('subscriberedirecturl');
                                    }

                                    if (!empty($subscriberedirecturl)) {
                                        ?>

                                        <script type="text/javascript">
                                            if (window.opener && window.opener !== window) {
                                                window.opener.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                                window.close();
                                            } else if (window.top != window.self) {
                                                window.top.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                            } else {
                                                window.location = "<?php echo esc_js($subscriberedirecturl); ?>";
                                            }
                                        </script>

                                        <?php

                                        exit();
                                    }

                                    echo '<div class="newsletters-acknowledgement">' . wp_kses_post(wpautop($instance['acknowledgement'])) . '</div>';

                                    if (empty($_GET['iframe'])) {
                                        echo '<p><a href="" class="button" onclick="window.close();">' . esc_html_e('Close this window', 'wp-mailinglist') . '</a></p>';
                                    }
                                }
                            }
                        }

                        if (!empty($form_id) && $form = $this -> Subscribeform() -> find(array('id' => $form_id))) {
                            $form -> ajax = false;
                            $form -> offsite = true;
                            $this -> render('offsite', array('form' => $form, 'title' => esc_html($form -> title)), true, 'default');

                        } elseif (!empty($list)) {
                            $atts['list'] = sanitize_text_field(wp_unslash($_GET['list']));
                            $number = 'embed' . rand(999, 9999);
                            $widget_id = 'newsletters-' . $number;
                            $instance = $this -> widget_instance($number, $atts);
                            $instance['ajax'] = "N";
                            $instance['offsite'] = true;
                            $success = false;

                            $defaults = array(
                                'list' 				=> 	$list_id,
                                'id' 				=> 	false,
                                'lists'				=>	false,
                                'ajax'				=>	$instance['ajax'],
                                'button'			=>	$instance['button'],
                                'captcha'			=>	$instance['captcha'],
                                'acknowledgement'	=>	$instance['acknowledgement'],
                            );

                            $r = shortcode_atts($defaults, $atts);
                            extract($r);

                            $this -> render('offsite', array('instance' => $r, 'title' => esc_html(urldecode(sanitize_text_field(wp_unslash($_GET['title']))))), true, 'default');
                        }

                        exit();
                        break;
                    case 'optin'			:
                        global $Subscriber, $Html, $Mailinglist;
                        if (!empty($_POST)) {
                            if ($subscriber_id = $Subscriber -> optin($_POST)) {
                                $subscriber = $Subscriber -> get($subscriber_id);

                                if ($paidlist_id = $Mailinglist -> has_paid_list(sanitize_text_field(wp_unslash($_POST['list_id'])))) {
                                    $paidlist = $Mailinglist -> get($paidlist_id, false);
                                    $this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $paidlist -> id . '&extend=0', $this -> get_managementpost(true)));
                                }

                                if (empty($_POST['form_id'])) {
                                    if ($this -> get_option('subscriberedirect') == "Y") {
                                        $subscriberedirecturl = $this -> get_option('subscriberedirecturl');

                                        if (!empty($_POST['list_id']) && (!is_array($_POST['list_id']) || count($_POST['list_id']) == 1)) {
                                            if ($subscribelist = $Mailinglist -> get(sanitize_text_field(wp_unslash($_POST['list_id'][0])))) {
                                                if (!empty($subscribelist -> subredirect)) {
                                                    $subscriberedirecturl = esc_html($subscribelist -> subredirect);
                                                }
                                            }
                                        }

                                        $this -> redirect($subscriberedirecturl, false, false, true);
                                    } else {
                                        $url = $Html -> retainquery($this -> pre . 'method=optin&success=1', wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))));
                                        $this -> redirect($url);
                                    }
                                }  elseif (!empty($_POST['form_id'])) {
                                    if ($form = $this -> Subscribeform() -> find(array('id' => sanitize_text_field(wp_unslash($_POST['form_id']))))) {
                                        if (empty($form -> confirmationtype) || $form -> confirmationtype == "message") {
                                            if (!empty($form -> confirmation_message)) {
                                                global ${'newsletters_form' . $form -> id . '_success'};
                                                ${'newsletters_form' . $form -> id . '_success'} = true;
                                            }
                                        } elseif ($form -> confirmationtype == "redirect") {
                                            if (!empty($form -> confirmation_redirect)) {
                                                $subscriberedirecturl = do_shortcode(wp_unslash(esc_html($form -> confirmation_redirect)));
                                                $subscriberedirecturl = $this -> process_set_variables($subscriber, false, $subscriberedirecturl);
                                                $this -> redirect($subscriberedirecturl, false, false, true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 'unsubscribe'		:
                        global $Html;

                        $querystring = 'method=unsubscribe&' . $this -> pre . 'subscriber_id=' . sanitize_text_field(wp_unslash($_GET[$this -> pre . 'subscriber_id'])) . '&' . $this -> pre . 'mailinglist_id=' . sanitize_text_field(wp_unslash($_GET[$this -> pre . 'mailinglist_id'])) . '&authkey=' . sanitize_text_field(wp_unslash($_GET['authkey']));
                        $url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
                        $this -> redirect($url);
                        exit();
                        break;
                    case 'manage'			:
                        //redirect to the new management section
                        $this -> redirect($this -> get_managementpost(true));

                        exit();
                        die();
                        break;
                    case 'activate'			:
                        global $wpdb, $Authnews, $Mailinglist, $Html, $Db, $HistoriesAttachment, $Email, $Subscriber;
                        if (!empty($_GET[$this -> pre . 'subscriber_id']) && !empty($_GET['authkey'])) {
                            $subscriber_id = intval($_GET[$this -> pre . 'subscriber_id']);
                            $authkey = sanitize_text_field(wp_unslash($_GET['authkey']));

                            if (!empty($_GET[$this -> pre . 'mailinglist_id'])) {
                                $mailinglists = @explode(",", sanitize_text_field(wp_unslash($_GET[$this -> pre . 'mailinglist_id'])));
                                $mailinglistsstring = sanitize_text_field(wp_unslash($_GET[$this -> pre . 'mailinglist_id']));
                            }

                            $subscriber = $Subscriber -> get($subscriber_id, false);

                            if ($authkey != $subscriber -> authkey) {
                                $subscriberedirecturl = $this -> get_option('subscriberedirect');
                                $msgtype = "error";
                                $message = "Invalid key, please check that you have the correct url to activate this mailing list.";
                                $this -> redirect($this -> get_managementpost(true), $msgtype, $message);
                            }

                            $Authnews -> set_emailcookie($subscriber -> email, "+30 days");

                            if (empty($subscriber -> cookieauth)) {
                                $subscriberauth = $Authnews -> gen_subscriberauth();
                                $Db -> model = $Subscriber -> model;
                                $Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
                            } else {
                                $subscriberauth = $subscriber -> cookieauth;
                            }

                            $Authnews -> set_cookie($subscriberauth, "+30 days", true);
                            $paidlists = false;

                            if (!empty($mailinglists)) {
                                foreach ($mailinglists as $list_id) {
                                    if ($mailinglist = $Mailinglist -> get($list_id, false)) {
                                        if ($mailinglist -> paid == "N" || empty($mailinglist -> paid)) {
                                            if ($SubscribersList -> save_field('active', "Y", array('subscriber_id' => $subscriber_id, 'list_id' => $list_id))) {
                                                $msgtype = "success";
                                                $message = __('Subscription has been activated', 'wp-mailinglist');
                                                $subscriber = $Subscriber -> get($subscriber_id, false);
                                                $subscriber -> mailinglist_id = $mailinglist -> id;

                                                $saveipaddress = $this -> get_option('saveipaddress');
                                                if (!empty($saveipaddress)) {
                                                    $Db -> model = $Subscriber -> model;
                                                    $Db -> save_field('ip_address', $this -> get_ip_address(), array('id' => $subscriber -> id));
                                                }

                                                $this -> autoresponders_send($subscriber, $mailinglist);
                                                do_action($this -> pre . '_subscriber_activated', $subscriber);
                                            }
                                        } else {
                                            $paidlists[] = $list_id;
                                        }

                                        if ($subscriberslists = $this -> SubscribersList() -> find_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id))) {
                                            foreach ($subscriberslists as $sl) {
                                                if (!empty($sl -> form_id)) {
                                                    // Send autoresponders linked to form
                                                    if ($form = $this -> Subscribeform() -> find(array('id' => $sl -> form_id))) {
                                                        $this -> autoresponders_form_send($subscriber, $form);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $msgtype = "error";
                            $message = __('Subscription is invalid', 'wp-mailinglist');
                        }

                        do_action('newsletters_subscriber_activate', $subscriber, $mailinglists);

                        if (!empty($mailinglists) && count($mailinglists) == 1 && !empty($mailinglist -> redirect)) {
                            $activateredirecturl = esc_html($mailinglist -> redirect);
                        } else {
                            if ($this -> get_option('customactivateredirect') == "Y") {
                                $activateredirecturl = $this -> get_option('activateredirecturl');
                            } else {
                                $activateredirecturl = $Html -> retainquery('updated=1&success=' . __('Thank you for confirming your subscription.', 'wp-mailinglist'), $this -> get_managementpost(true));
                            }
                        }

                        //If there are paid lists... we need to provide a payment form.
                        if (!empty($paidlists)) {
                            if ($this -> get_option('activationemails') == "single" && count($paidlists) > 1) {
                                $message = sprintf(__('Thank you for confirming your subscription.<br/><br/>Since you subscribed to %s paid list, please click the "Pay Now" button below to make a payment and your subscription will then be active.', 'wp-mailinglist'), count($paidlists));
                                $this -> redirect($this -> get_managementpost(true), "success", $message);
                            } else {
                                $subscriber = $Subscriber -> get($subscriber_id, false);
                                $mailinglist = $Mailinglist -> get($paidlists[0], false);
                                $this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $mailinglist -> id . '&extend=0', $this -> get_managementpost(true)));
                            }
                        } else {
                            $this -> redirect($activateredirecturl, $msgtype, $message);
                        }
                        break;
                    case 'paypal'			:
                        global $Html, $SubscribersList;

                        $raw_post_data = file_get_contents('php://input');
                        $raw_post_array = explode('&', $raw_post_data);
                        $myPost = array();

                        foreach ($raw_post_array as $keyval) {
                            $keyval = explode ('=', $keyval);
                            if (count($keyval) == 2) {
                                $myPost[$keyval[0]] = urldecode($keyval[1]);
                            }
                        }

                        $this -> log_error($myPost);

                        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
                        $req = 'cmd=_notify-validate';

                        if (function_exists('get_magic_quotes_gpc')) {
                            $get_magic_quotes_exists = true;
                        }

                        foreach ($myPost as $key => $value) {
                            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                                $value = urlencode(wp_unslash($value));
                            } else {
                                $value = urlencode($value);
                            }

                            $req .= "&" . $key . "=" . $value;
                        }

                        $custom = json_decode(urldecode($myPost['custom']));
                        $item_name = esc_html($myPost['item_name']);
                        $item_number = esc_html($myPost['item_number']);
                        $payment_status = esc_html($myPost['payment_status']);
                        $payment_amount = esc_html($myPost['mc_gross']);
                        $payment_currency = esc_html($myPost['mc_currency']);
                        $txn_id = esc_html($myPost['txn_id']);
                        $txn_type = esc_html($myPost['txn_type']);
                        $subref = esc_html($myPost['subscr_id']);
                        $receiver_email = esc_html($myPost['receiver_email']);
                        $payer_email = esc_html($myPost['payer_email']);

                        $this -> log_error($custom);

                        $paypalsandbox = $this -> get_option('paypalsandbox');
                        $ppurl = (!empty($paypalsandbox) && $paypalsandbox == "Y") ? 'ipnpb.sandbox.paypal.com' : 'ipnpb.paypal.com';

                        $ppport = 443;
                        $header = '';
                        $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
                        $header .= "Host: " . $ppurl . "\r\n";
                        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
                        $fhost = 'tls://' . $ppurl;

                        $fp = fsockopen($fhost, $ppport, $errno, $errstr, 30);

                        $verified = false;

                        if (!$fp) {
                            $message = __('An HTTP error has occurred. PayPal cannot be contacted.', 'wp-mailinglist');
                            $this -> log_error(sprintf(__('PayPal IPN: %s - %s', 'wp-mailinglist'), $errno, $errstr));
                        } else {
                            fputs($fp, $header . $req);

                            while (!feof($fp)) {
                                //$res = fgets($fp, 1024);
                                $this->log_error($res);
                                if (trim($res) == 'VERIFIED') {
                                    $verified = true;
                                    $this -> log_error('PayPal IPN: Verified');
                                }
                            }

                            fclose($fp);
                        }

                        if ($verified) {
                            // The IPN is verified, process it
                            $this -> log_error('This PayPal transaction is verified');
                        } else {
                            // IPN invalid, log for manual investigation
                            $this -> log_error('PayPal transaction is not verified');
                        }

                        /*$verified = false;
                        // inspect IPN validation result and act accordingly
                        if (strcmp ($res, "VERIFIED") == 0) {
                            // The IPN is verified, process it
                            $this -> log_error('This PayPal transaction is verified');
                            $verified = true;
                        } else if (strcmp ($res, "INVALID") == 0) {
                            // IPN invalid, log for manual investigation
                            $this -> log_error('PayPal transaction is not verified');
                            $verified = false;
                        }		*/

                        $doupdate = false;
                        if (!empty($verified) && $verified == true) {

                            $this -> log_error('Payment Status: ' . $payment_status);
                            $this -> log_error('Transaction Type: ' . $txn_type);

                            switch ($payment_status) {
                                case 'Failed'				:
                                    $message = __('The payment has failed. Please try again', 'wp-mailinglist');
                                    break;
                                case 'Denied'				:
                                    $message = __('The payment has been denied. This payment could already be pending', 'wp-mailinglist');
                                    break;
                                default						:
                                    if (!empty($custom -> subscriber_id) && !empty($custom -> mailinglist_id)) {
                                        switch ($txn_type) {
                                            case 'subscr_payment'		:
                                                if ($payment_status == "Pending") {
                                                    $message = __('Thank you for your PayPal subscription. Your payment is currently pending. Please wait for the merchant to accept it', 'wp-mailinglist');
                                                } elseif ($payment_status == "Completed") {
                                                    $doupdate = true;
                                                }
                                                break;
                                            case 'subscr_cancel'		:
                                                $mailinglists = explode(",", $custom -> mailinglist_id);
                                                foreach ($mailinglists as $list_id) {
                                                    $sl_conditions = array('subscriber_id' => $custom -> subscriber_id, 'list_id' => $list_id);
                                                    $SubscribersList -> save_field('active', "N", $sl_conditions);
                                                }
                                                $message = __('PayPal subscription has been cancelled', 'wp-mailinglist');
                                                break;
                                            default						:
                                                if ($payment_status == "Completed" || ($myPost['test_ipn'] && $payment_status == "Pending")) {
                                                    $doupdate = true;
                                                }
                                                break;
                                        }
                                    } else {
                                        $message = __('Subscriber or list ID empty', 'wp-mailinglist');
                                    }
                                    break;
                            }
                        } else {
                            //why on earth?
                            $message = __('PayPal has marked the transaction as invalid', 'wp-mailinglist');
                        }

                        $this -> log_error($message);
                        $this -> log_error($doupdate);

                        //everything is fine, lets continue
                        if ($doupdate == true) {

                            $this -> log_error('do update');

                            $subscriber = $Subscriber -> get($custom -> subscriber_id, false);
                            $list_id = $custom -> mailinglist_id;
                            $mailinglist = $Mailinglist -> get($list_id, false);
                            $subscriber -> mailinglist_id = $mailinglist -> id;

                            if ($payment_amount == $mailinglist -> price) {
                                $sl_conditions = array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id);
                                $subscriberslist = $SubscribersList -> find($sl_conditions);

                                if (!empty($subscriberslist) && !empty($custom -> subscription_extend)) {
                                    $paid_now = strtotime($subscriberslist -> paid_date);
                                    if (strtotime($subscriberslist -> paid_date) < current_time('timestamp')) {
                                        $paid_now = current_time('timestamp');
                                    }

                                    $paid_stamp = $Mailinglist -> paid_stamp($mailinglist -> interval, $paid_now);
                                    $paid_date = $Html -> gen_date("Y-m-d", $paid_stamp);
                                } else {
                                    $paid_date = $Html -> gen_date();
                                }

                                $SubscribersList -> save_field('active', "Y", $sl_conditions);
                                $SubscribersList -> save_field('paid', "Y", $sl_conditions);
                                $SubscribersList -> save_field('paid_date', $paid_date, $sl_conditions);
                                $SubscribersList -> save_field('paid_sent', "0", $sl_conditions);
                                $SubscribersList -> save_field('modified', $Html -> gen_date(), $sl_conditions);

                                if ($this -> get_option('paypalsubscriptions') == "Y") {
                                    $SubscribersList -> save_field('ppsubscription', "Y", $sl_conditions);
                                }

                                $this -> autoresponders_send($subscriber, $mailinglist);

                                $orderdata = array(
                                    'list_id'				=>	$list_id,
                                    'subscriber_id'			=>	$custom -> subscriber_id,
                                    'completed'				=>	'Y',
                                    'amount'				=>	$mailinglist -> price,
                                    'product_id'			=>	$subscriberslist -> rel_id,
                                    'order_number'			=>	$subscriberslist -> rel_id,
                                    'reference'				=>	$txn_id,
                                    'subref'				=>	$subref,
                                    'pmethod'				=>	'pp',
                                );

                                if ($this -> Order() -> save($orderdata, true)) {
                                    //success

                                    if (empty($subscriberslist -> order_id)) {
                                        $SubscribersList -> save_field('order_id', $this -> Order() -> insertid, $sl_conditions);
                                    }
                                }

                                $message = __('Payment received and subscription activated', 'wp-mailinglist');

                                if ($this -> get_option('adminordernotify') == "Y") {
                                    $emailsused = array();
                                    $subscriber -> mailinglists = array($list_id);
                                    $to = new stdClass();
                                    $adminemail = $this -> get_option('adminemail');
                                    $subject = wp_unslash($this -> et_subject('order', $subscriber));
                                    $fullbody = $this -> et_message('order', $subscriber);
                                    $message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('order'), false, $fullbody);

                                    if (strpos($adminemail, ",") !== false) {
                                        $adminemails = explode(",", $adminemail);
                                        foreach ($adminemails as $adminemail) {
                                            if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
                                                $to -> email = $adminemail;
                                                $eunique = $Html -> eunique($to, false, "order");
                                                $this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "order");
                                                $emailsused[] = $adminemail;
                                            }
                                        }
                                    } else {
                                        $to -> email = $adminemail;
                                        $eunique = $Html -> eunique($to, false, "order");
                                        $this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "order");
                                    }
                                }
                            } else {
                                $this -> log_error('PayPal amount does not match mailing list price');
                            }
                        } else {
                            //Send a message to the administrator?
                            $this -> log_error(sprintf(__('PayPal IPN: %s', 'wp-mailinglist'), $message));
                        }

                        if (!empty($message)) {
                            ?>

                            <script type="text/javascript">
                                alert('<?php echo wp_kses_post(addslashes($message)); ?>');
                                window.location = '<?php echo esc_url_raw($this -> get_managementpost(true)); ?>';
                            </script>

                            <?php

                            exit();
                        }
                        break;
                    case 'twocheckout'		:
                        if (!empty($_POST['order_number'])) {
                            if ($_POST['credit_card_processed'] == "Y") {
                                $vendorid = $this -> get_option('tcovendorid');
                                $secret = $this -> get_option('tcosecret');
                                $total = sanitize_text_field(wp_unslash($_POST['total']));

                                if ($_POST['demo'] == "Y" && $this -> get_option('tcodemo') == "Y") {
                                    $ordernumber = 1;
                                } else {
                                    $ordernumber = sanitize_text_field(wp_unslash($_POST['order_number']));
                                }

                                $mykey = $secret . $vendorid . $ordernumber . $total;
                                $mykey = strtoupper(md5($mykey));

                                if ($mykey === $_POST['key']) {
                                    $subscriberid = sanitize_text_field(wp_unslash($_POST['subscriber_id']));
                                    $subscriber = $Subscriber -> get($subscriberid, false);
                                    $mailinglist_id = sanitize_text_field(wp_unslash($_POST['mailinglist_id']));
                                    $mailinglist = $Mailinglist -> get($mailinglist_id, false);
                                    $subscriber -> mailinglist_id = $mailinglist -> id;
                                    $this -> autoresponders_send($subscriber, $mailinglist);
                                    $sl_conditions = array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id);
                                    $SubscribersList -> save_field('active', "Y", $sl_conditions);
                                    $SubscribersList -> save_field('paid', "Y", $sl_conditions);
                                    $SubscribersList -> save_field('paid_date', $this -> gen_date(), $sl_conditions);
                                    $SubscribersList -> save_field('paid_sent', "0", $sl_conditions);

                                    $orderdata = array(
                                        'list_id'				=>	$mailinglist_id,
                                        'subscriber_id'			=>	$subscriberid,
                                        'completed'				=>	'Y',
                                        'amount'				=>	sanitize_text_field(wp_unslash($_POST['total'])),
                                        'product_id'			=>	1,
                                        'order_number'			=>	sanitize_text_field(wp_unslash($_POST['order_number'])),
                                        'reference'				=>	$ordernumber,
                                        'pmethod'				=>	'2co',
                                    );

                                    if ($this -> Order() -> save($orderdata, true)) {
                                        //success
                                    }

                                    if ($this -> get_option('adminordernotify') == "Y") {
                                        $emailsused = array();
                                        $adminemail = $this -> get_option('adminemail');
                                        $subject = wp_unslash($this -> et_subject('order', $subscriber));
                                        $fullbody = $this -> et_message('order', $subscriber);
                                        $message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('order'), false, $fullbody);

                                        if (strpos($adminemail, ",") !== false) {
                                            $adminemails = explode(",", $adminemail);
                                            foreach ($adminemails as $adminemail) {
                                                if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
                                                    $to -> email = $adminemail;
                                                    $eunique = $Html -> eunique($to, false, "order");
                                                    $this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "order");
                                                }
                                            }
                                        } else {
                                            $to -> email = $adminemail;
                                            $eunique = $Html -> eunique($to, false, "order");
                                            $this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "order");
                                        }
                                    }

                                    $msgtype = 'success';
                                    $message = __('Payment received and subscription activated', 'wp-mailinglist');
                                } else {
                                    $msgtype = 'error';
                                    $message = __('Hash encryption failed! Please contact us', 'wp-mailinglist');
                                }
                            } else {
                                $msgtype = 'error';
                                $message = __('Credit card could not be processed, please try again.', 'wp-mailinglist');
                            }
                        }

                        ?>

                        <?php if (!empty($message)) : ?>
                        <?php $this -> redirect($this -> get_managementpost(true), $msgtype, $message); ?>
                    <?php endif; ?>

                        <?php
                        break;
                    case 'bounce'			:
                        switch ($_GET['type']) {
                            case 'mandrill'			:
                                if (isset($_POST['mandrill_events'])) {
                                    $events = json_decode(wp_unslash(sanitize_text_field(wp_unslash($_POST['mandrill_events']))));
                                    foreach ($events as $event) {
                                        if ($event -> event === 'soft_bounce' || $event -> event === "deferral") {
                                            $this -> log_error(sprintf(__('Mandrill bounce: %s', 'wp-mailinglist'), $event -> msg -> email));
                                            $result = $this -> bounce($event -> msg -> email, "mandrill-bounce", $event -> msg -> bounce_description);
                                        } else {
                                            $this -> log_error(sprintf(__('Mandrill hard bounce, rejection, or spam: %s', $this->plugin_name), $event -> msg -> email));
                                            $result = $this -> bounce($event -> msg -> email, "mandrill-delete", $event -> msg -> bounce_description);
                                        }
                                    }
                                }
                                break;
                            case 'sns'			:
                                $json = json_decode(file_get_contents("php://input"));
                                if (!empty($json)) {
                                    $json_message = json_decode($json -> Message);

                                    if ($json -> Type == "SubscriptionConfirmation") {
                                        $subscribe_url = $json -> SubscribeURL;

                                        $this -> log_error(sprintf(__('Amazon SNS subscription confirm: %s', 'wp-mailinglist'), $subscribe_url));
                                        $raw_response = wp_remote_request($subscribe_url);
                                    } elseif ($json -> Type == "Notification") {
                                        if ($json_message -> notificationType == "Bounce") {
                                            if (!empty($json_message -> bounce -> bounceType) && $json_message -> bounce -> bounceType == "Permanent") {
                                                if (!empty($json_message -> bounce -> bouncedRecipients)) {
                                                    foreach ($json_message -> bounce -> bouncedRecipients as $recipient) {
                                                        if ($recipient -> action == "failed") {
                                                            $messageid = $json_message -> mail -> messageId;
                                                            $this -> log_error(sprintf(__('Amazon SNS bounce: %s', 'wp-mailinglist'), $recipient -> emailaddress));
                                                            $result = $this -> bounce($recipient -> emailAddress, "sns", $json_message -> bounce -> bouncedRecipients[0] -> status . ' - ' . $json_message -> bounce -> bouncedRecipients[0] -> diagnosticCode, $messageid);
                                                        }
                                                    }
                                                }
                                            }
                                        } elseif ($json_message -> notificationType == "Complaint") {
                                            if (!empty($json_message -> complaint -> complainedRecipients)) {
                                                foreach ($json_message -> complaint -> complainedRecipients as $recipient) {
                                                    $messageid = $json_message -> mail -> messageId;
                                                    $this -> log_error(sprintf(__('Amazon SNS complaint: %s', 'wp-mailinglist'), $recipient -> emailaddress));
                                                    $result = $this -> bounce($recipient -> emailAddress, "sns", $json_message -> bounce -> bouncedRecipients[0] -> status . ' - ' . $json_message -> bounce -> bouncedRecipients[0] -> diagnosticCode, $messageid);
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            default				:
                                $this -> bounce(sanitize_text_field(wp_unslash($_GET['em'])));
                                break;
                        }
                        break;
                    case 'newsletter'		:
                        global $Db, $Subscriber;
                        header('Content-type: text/html; charset=utf-8');

                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                        if (!empty($id)) {
                            if ($email = $this -> History() -> find(array('id' => $id))) {
                                $Db -> model = $Subscriber -> model;
                                $subscriber = $Subscriber -> get(sanitize_text_field(wp_unslash($_GET['subscriber_id'])), false);

                                $clicktrack = $this -> get_option('clicktrack');
                                if (!empty($clicktrack) && $clicktrack == "Y") {
                                    $click_data = array(
                                        //'link_id'			=>	$link -> id,
                                        'referer'			=>	"online",
                                        'history_id'		=>	esc_html($email -> id),
                                        'user_id'			=>	esc_html($_GET['user_id']),
                                        'subscriber_id'		=>	esc_html($_GET['subscriber_id']),
                                        'device'			=>	$this -> get_device()
                                    );

                                    $this -> Click() -> save($click_data, true);
                                }

                                $message = $email -> message;
                                $content = $this -> render_email('send', array('print' => sanitize_text_field(wp_unslash($_GET['print'])), 'message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $id), false, true, true, $email -> theme_id);
                                $output = "";
                                ob_start();
                                $thecontent = do_shortcode(wp_unslash($content));
                                echo wp_kses_post(apply_filters('wpml_online_newsletter', $thecontent, $subscriber));
                                $output = ob_get_clean();
                                // phpcs:ignore
                                echo $this -> process_set_variables($subscriber, $user, $output, $email -> id);
                                exit();
                            } else {
                                $message = __('Newsletter cannot be read', 'wp-mailinglist');
                            }
                        } else {
                            $message = __('No newsletter was specified', 'wp-mailinglist');
                        }

                        if (!empty($message)) {
                            ?>

                            <script type="text/javascript">
                                alert('<?php echo wp_kses_post(addslashes($message)); ?>');
                            </script>

                            <?php
                        }
                        break;
                }
            }
        }

        function wp_head() {
            $this -> render('head');

            global $wpmljavascript;
            if (!empty($wpmljavascript)) {
                // phpcs:ignore
                echo $wpmljavascript;
            }
        }

        function wp_footer() {
            $this -> render('footer');

            if (wpml_is_management()) {
                $this -> render('js' . DS . 'management', false, true, 'default');
            }
        }

        function delete_user($user_id = null) {
            global $Db, $Subscriber;

            $unsubscribeondelete = $this -> get_option('unsubscribeondelete');
            if (!empty($unsubscribeondelete) && $unsubscribeondelete == "Y") {
                if (!empty($user_id)) {
                    $Db -> model = $Subscriber -> model;
                    if ($subscriber = $Db -> find(array('user_id' => $user_id))) {
                        if (!empty($subscriber)) {
                            $Subscriber -> delete($subscriber -> id);
                        }
                    }

                    $this -> Click() -> delete_all(array('user_id' => $user_id));
                }
            }
        }

        function user_register($user_id = null) {
            global $Db, $Mailinglist, $Subscriber, $SubscribersList;

            if (!empty($user_id)) {
                if ($userdata = $this -> userdata($user_id)) {
                    // Check if subscriber exists
                    if ($subscriber = $Subscriber -> get_by_email($userdata -> user_email)) {
                        // Update the registered status of the subscriber
                        $Db -> model = $Subscriber -> model;
                        $Db -> save_field('registered', "Y", array('id' => $subscriber -> id));
                    }

                    // Did the user tick the subscribe checkbox?
                    if (!empty($_POST[$this -> pre . 'subscribe']) && $_POST[$this -> pre . 'subscribe'] == "Y") {
                        $autosubscribelist = $this -> get_option('autosubscribelist');
                        if (!empty($autosubscribelist)) {
                            $data = array(
                                'email' 			=> 	$userdata -> user_email,
                                'registered'		=>	'Y',
                                'username'			=>	$userdata -> user_login,
                                'mailinglists'		=>	$autosubscribelist,
                                'fromregistration'	=>	true,
                                'justsubscribe'		=>	true,
                                'user_id'			=>	$user_id,
                                'active'			=>	(($this -> get_option('requireactivate') == "Y") ? "N" : "Y"),
                            );

                            if ($Subscriber -> save($data, true)) {
                                $subscriber = $Subscriber -> get($Subscriber -> insertid, false);
                                $this -> subscription_confirm($subscriber);
                                $this -> admin_subscription_notification($subscriber);
                            }
                        }
                    }
                }
            }

            return true;
        }

        function dashboard_setup() {
            if (current_user_can('newsletters_welcome')) {
                wp_add_dashboard_widget($this -> plugin_name, '<i class="fa fa-envelope fa-fw"></i> ' . esc_html($this -> name, 'wp-mailinglist'), array($this, 'dashboard_widget'));
                global $wp_meta_boxes;
                $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
                $example_widget_backup = array($this -> plugin_name => $normal_dashboard[$this -> plugin_name]);
                unset($normal_dashboard[$this -> plugin_name]);
                $sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);
                $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
            }
        }

        function dashboard_widget() {
            $this -> render('dashboard', false, true, 'admin');
        }

        function do_meta_boxes($type = 'post') {
            global $Metabox, $Html;

            $post_types = $this -> get_custom_post_types();
            $custompostslug = $this -> get_option('custompostslug');

            if (!empty($type) && $type != $custompostslug) {
                if ($type == "post" || $type == "page" || (!empty($post_types) && array_key_exists($type, $post_types))) {
                    if (current_user_can('newsletters_send') && $this -> get_option('sendasnewsletterbox') == "Y") {
                        add_meta_box('newsletters',   __('Send as Newsletter', 'wp-mailinglist'),  array($Metabox, 'write_advanced'), $type, 'normal', 'high');
                    }
                }
            }

            return;
        }

        function activateaction_hook() {
            global $wpdb, $SubscribersList, $Subscriber, $Db;
            $this -> activateaction_scheduling();
            $activateaction = $this -> get_option('activateaction');

            if (!empty($activateaction)) {
                switch ($activateaction) {
                    case 'remind'				:
                        $activatereminder = $this -> get_option('activatereminder');
                        if (!empty($activatereminder)) {
                            $query = "SELECT * FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `active` = 'N' AND `reminded` = '0' AND `created` <= DATE_SUB(CURDATE(), INTERVAL " . $activatereminder . " DAY)";

                            if ($subscriptions = $wpdb -> get_results($query)) {
                                foreach ($subscriptions as $subscription) {
                                    $subscriber = $Subscriber -> get($subscription -> subscriber_id);
                                    $subscriber -> mailinglists = array($subscription -> list_id);
                                    $this -> subscription_confirm($subscriber);

                                    $Db -> model = $SubscribersList -> model;
                                    $Db -> save_field('reminded', "1", array('rel_id' => $subscription -> rel_id));
                                }
                            }
                        }
                        break;
                    case 'delete'				:
                        $activatedelete = $this -> get_option('activatedelete');
                        if (!empty($activatedelete)) {
                            $query = "SELECT * FROM `" . $wpdb -> prefix . $SubscribersList -> table . "` WHERE `active` = 'N' AND `created` <= DATE_SUB(CURDATE(), INTERVAL " . $activatedelete . " DAY)";
                            if ($subscriptions = $wpdb -> get_results($query)) {
                                foreach ($subscriptions as $subscription) {
                                    $Db -> model = $SubscribersList -> model;
                                    $Db -> delete_all(array('rel_id' => $subscription -> rel_id));
                                    $subscriber_id = $subscription -> subscriber_id;
                                }

                                if (!empty($subscriber_id)) {
                                    if ($this -> get_option('unsubscribedelete') == "Y") {
                                        $subscribedlists = $Subscriber -> mailinglists($subscriber_id);	//all subscribed mailing lists
                                        if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {
                                            $Db -> model = $Subscriber -> model;
                                            $Db -> delete($subscriber -> id);
                                            $deleted = true;
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case 'none'					:
                    default						:
                        //do nothing...
                        break;
                }
            }
        }

        function latestposts_hook($id = null, $preview = false) {
            global $wpdb, $post, $Db, $Html, $Mailinglist, $Subscriber, $SubscribersList;
            $sentmailscount = 0;

            // Check for expired paid subscriptions
            if ($this -> get_option('subscriptions') == "Y") {
                $SubscribersList -> check_expirations();
            }

            $this -> qp_reset_data();
            $this -> log_error('latest posts ' . $id . ' is firing now');

            if (!empty($id) && $latestpostssubscription = $this -> Latestpostssubscription() -> find(array('id' => $id))) {
                if (!empty($preview) || empty($latestpostssubscription -> status) || (!empty($latestpostssubscription -> status) && $latestpostssubscription -> status == "active")) {
                    if (!empty($latestpostssubscription -> language)) {
                        $this -> language_set($latestpostssubscription -> language);
                    }

                    $post_criteria = $this -> get_latestposts($latestpostssubscription);

                    if (!empty($latestpostssubscription -> groupbycategory) && $latestpostssubscription -> groupbycategory == "Y") {
                        $categories_args = array(
                            'type'						=>	'post',
                            'child_of'					=>	false,
                            'parent'					=>	false,
                            'orderby'					=>	"name",
                            'order'						=>	"asc",
                            'hide_empty'				=>	true,
                            'hierarchical'				=>	true,
                            'exclude'					=>	false,
                            'include'					=>	((!empty($post_criteria['category'])) ? $post_criteria['category'] : false),
                        );

                        $categories_args = apply_filters('newsletters_latest_posts_categories_args', $categories_args);

                        if ($categories = get_categories($categories_args)) {
                            global $shortcode_categories;
                            $c = 0;

                            foreach ($categories as $category) {
                                $post_criteria['category'] = $category -> cat_ID;
                                $posts = get_posts($post_criteria);

                                if (!empty($posts)) {
                                    $shortcode_categories[$c]['category'] = $category;
                                    $shortcode_categories[$c]['posts'] = $posts;
                                }

                                $c++;
                            }
                        }
                    }

                    $minnumber = $latestpostssubscription -> minnumber;

                    if (!empty($shortcode_categories) || $posts = get_posts($post_criteria)) {

                        $allpostscount = 0;
                        if (!empty($shortcode_categories)) {
                            foreach ($shortcode_categories as $c => $shortcode_category) {
                                $allpostscount += count($shortcode_category['posts']);
                            }
                        } elseif (!empty($posts)) {
                            $allpostscount = count($posts);
                        }

                        if (empty($minnumber) || (!empty($minnumber) && $allpostscount >= $minnumber)) {
                            if (!empty($posts) || !empty($shortcode_categories)) {
                                if ($this -> language_do()) {
                                    foreach ($posts as $pkey => $post) {
                                        $posts[$pkey] = $this -> language_use($latestpostssubscription -> language, $post, false);
                                    }
                                }

                                global $shortcode_posts, $shortcode_post, $shortcode_post_language, $shortcode_categories;
                                $shortcode_posts = $posts;
                                $shortcode_post = $posts[0];
                                $shortcode_post_language = $latestpostssubscription -> language;
                                $subject = do_shortcode(wp_unslash($latestpostssubscription -> subject));
                                $content = do_shortcode($this -> et_message('latestposts', false, $latestpostssubscription -> language));
                                $attachment = false;
                                $post_id = false;

                                if (!empty($preview) && $preview == true) {
                                    $subscriber_id = $Subscriber -> admin_subscriber_id();
                                    $subscriber = $Subscriber -> get($subscriber_id);
                                    $subscriber -> mailinglists = $email -> mailinglists;
                                    $eunique = $Html -> eunique($subscriber, $history_id);
                                    $message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $latestpostssubscription -> theme_id);

                                    $output = "";
                                    ob_start();
                                    // phpcs:ignore
                                    echo do_shortcode(wp_unslash($message));
                                    $output = ob_get_clean();
                                    ob_start();
                                    // phpcs:ignore
                                    echo $this -> process_set_variables($subscriber, $user, $output, $history_id);
                                    $output = ob_get_clean();

                                    return $output;
                                } else {
                                    $history_data = array(
                                        'subject'			=>	$subject,
                                        'message'			=>	$content,
                                        'language'			=>	$latestpostssubscription -> language,
                                        'theme_id'			=>	$latestpostssubscription -> theme_id,
                                        'mailinglists'		=>	$latestpostssubscription -> lists,
                                        'attachment'		=>	"N",
                                        'attachmentfile'	=>	false,
                                    );

                                    $history_data['sent'] = 1;
                                    $this -> History() -> save($history_data, false);
                                    $history_id = $this -> History() -> insertid;

                                    $this -> Latestpostssubscription() -> save_field('history_id', $history_id, array('id' => $latestpostssubscription -> id));

                                    if (!empty($shortcode_categories)) {
                                        foreach ($shortcode_categories as $shortcode_category) {
                                            if (!empty($shortcode_category['posts'])) {
                                                foreach ($shortcode_category['posts'] as $post) {
                                                    $this -> Latestpost() -> save(array('post_id' => $post -> ID, 'lps_id' => $latestpostssubscription -> id), true);
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($shortcode_posts as $post) {
                                            $this -> Latestpost() -> save(array('post_id' => $post -> ID, 'lps_id' => $latestpostssubscription -> id), true);
                                        }
                                    }

                                    if ($mailinglists = maybe_unserialize($latestpostssubscription -> lists)) {
                                        $mailinglistscondition = "(";
                                        $m = 1;

                                        foreach ($mailinglists as $mailinglist_id) {
                                            $mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";
                                            if ($m < count($mailinglists)) { $mailinglistscondition .= " OR "; }
                                            $m++;
                                        }

                                        $query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
                                            . $wpdb -> prefix . $Subscriber -> table . ".email FROM "
                                            . $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
                                            . $wpdb -> prefix . $SubscribersList -> table . " ON "
                                            . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id "
                                            . "LEFT JOIN " . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = " . $wpdb -> prefix . $Mailinglist -> table . ".id WHERE "
                                            . $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";

                                        $query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval
										OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')";

                                        $subscribers = $wpdb -> get_results($query);

                                        if (!empty($subscribers)) {

                                            $queue_process = 1;
                                            $queue_process_counter_1 = 0;
                                            $queue_process_counter_2 = 0;
                                            $queue_process_counter_3 = 0;

                                            $sentmailscount = 0;
                                            $this -> qp_reset_data();

                                            $subscriberids = array();

                                            foreach ($subscribers as $subscriber) {
                                                $this -> remove_server_limits();

                                                if (empty($subscriberids) || !in_array($subscriber -> id, $subscriberids)) {
                                                    $queue_process_data = array(
                                                        'subscriber_id'				=>	$subscriber -> id,
                                                        'subject'					=>	$subject,
                                                        'message'					=>	$content,
                                                        'attachments'				=>	false,
                                                        'post_id'					=>	false,
                                                        'history_id'				=>	$history_id,
                                                        'theme_id'					=>	$latestpostssubscription -> theme_id,
                                                        'senddate'					=>	false,
                                                    );

                                                    $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);
                                                    $sentmailscount++;

                                                    ${'queue_process_counter_' . $queue_process}++;
                                                    $this -> {'queue_process_' . $queue_process} -> counter_reset = 10;

                                                    if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                                        $this -> {'queue_process_' . $queue_process} -> save();
                                                        $this -> {'queue_process_' . $queue_process} -> reset_data();
                                                        ${'queue_process_counter_' . $queue_process} = 0;
                                                    }

                                                    $queue_process++;
                                                    if ($queue_process > 3) {
                                                        $queue_process = 1;
                                                    }

                                                    $subscriberids[] = $subscriber -> id;
                                                }
                                            }

                                            $this -> qp_save();
                                            $this -> qp_dispatch();
                                        }
                                    }
                                }
                            }
                        } else {
                            echo wp_kses_post(sprintf(__('Minimum number of %s posts required to send this latest posts subscription newsletter.', 'wp-mailinglist'), $minnumber));
                        }
                    } else {
                        esc_html_e('No posts with the specified criteria could be found. Are there new posts available to be sent?', 'wp-mailinglist');
                    }
                } else {
                    esc_html_e('Latest posts subscription is currently paused.', 'wp-mailinglist');
                }
            } else {
                // Cannot be found, delete the schedule
                if (!empty($id)) {
                    wp_clear_scheduled_hook('newsletters_latestposts', array((int) $id));
                }

                esc_html_e('No latest posts subscription was specified', 'wp-mailinglist');
            }

            echo esc_html( $sentmailscount . ' ' . __('emails were sent/queued.', 'wp-mailinglist'));

            // All done, we need to exit this to prevent a loop
            exit(); die();
        }

        function importusers_hook() {
            global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList, $Unsubscribe, $Bounce, $Field;
            $Db -> model = $Mailinglist -> model;
            $importcount = 0;
            $importuserslists = $this -> get_option('importuserslists');
            $importusers_updateall = $this -> get_option('importusers_updateall');

            if (!empty($importuserslists)) {
                foreach ($importuserslists as $role => $lists) {
                    $users_arguments = array(
                        'blog_id'				=>	$GLOBALS['blog_id'],
                        'fields'				=>	array('ID', 'user_email', 'user_login'),
                        'role__in'					=>	[$role],
                    );

                    if (empty($importusers_updateall)) {

                        $subscribers_table = $wpdb -> prefix . $Subscriber -> table;
                        $subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;

                        $userslistquery = "SELECT GROUP_CONCAT(DISTINCT(s.user_id)) FROM `" . $subscribers_table . "` AS s
						LEFT JOIN `" . $subscriberslists_table . "` AS sl ON s.id = sl.subscriber_id
						WHERE sl.list_id IN (" . implode(",", $lists) . ") AND s.user_id != '0'";

                        $userslist = $wpdb -> get_var($userslistquery);
                        $users_arguments['exclude'] = $userslist;
                    }

                    if ($users = get_users($users_arguments)) {
                        $importusersrequireactivate = $this -> get_option('importusersrequireactivate');
                        foreach ($users as $user) {
                            if (!user_can($user -> ID, 'pending')) {
                                // check unsubscribe
                                $Db -> model = $Unsubscribe -> model;
                                if (!$Db -> find(array('email' => $user -> user_email))) {
                                    // check bounce
                                    $Db -> model = $Bounce -> model;
                                    if (!$Db -> find(array('email' => $user -> user_email))) {
                                        $Db -> model = $Subscriber -> model;
                                        $user_role = $this -> user_role($user -> ID);

                                        if (!empty($importuserslists[$user_role])) {
                                            $subscriber = array(
                                                'id'				=>	false,
                                                'email'				=>	$user -> user_email,
                                                'mailinglists'		=>	$importuserslists[$user_role],
                                                'registered'		=>	"Y",
                                                'username'			=>	$user -> user_login,
                                                'fromregistration'	=>	true,
                                                'justsubscribe'		=>	true,
                                                'active'			=>	((empty($importusersrequireactivate) || $importusersrequireactivate == "Y") ? "N" : "Y"),
                                                'user_id'			=>	$user -> ID,
                                            );

                                            $fieldsquery = "SELECT `id`, `slug` FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list'";
                                            $fields = $wpdb -> get_results($fieldsquery);

                                            if (!empty($fields)) {
                                                $importusersfields = $this -> get_option('importusersfields');
                                                $importusersfieldspre = $this -> get_option('importusersfieldspre');

                                                foreach ($fields as $field) {
                                                    if (!empty($importusersfieldspre[$field -> id]) && $usermeta = get_user_option($importusersfieldspre[$field -> id], $user -> ID)) {
                                                        $subscriber[$field -> slug] = $usermeta;
                                                    } elseif (!empty($importusersfields[$field -> id]) && $usermeta = get_user_option($importusersfields[$field -> id], $user -> ID)) {
                                                        $subscriber[$field -> slug] = $usermeta;
                                                    }
                                                }
                                            }

                                            // Don't send autoresponders again if the subscriber is updated
                                            if ($Subscriber -> email_exists($user -> user_email)) {
                                                $subscriber['preventautoresponders'] = true;
                                            }

                                            if ($Subscriber -> save($subscriber, true)) {
                                                $importcount++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            echo wp_kses_post($importcount . ' ' . __('users were imported as subscribers.', 'wp-mailinglist'));
        }

        function captchacleanup_hook() {
            if ($this -> is_plugin_active('captcha')) {
                if (class_exists('ReallySimpleCaptcha')) {
                    if ($captcha = new ReallySimpleCaptcha()) {
                        $captcha -> cleanup(60);
                    }
                }
            }
        }

        function autoresponders_hook() {
            $addedtoqueue = 0;

            do_action('newsletters_autoresponders_hook_start');

            /* Do the Autoresponders */
            global $wpdb, $Db, $Html, $HistoriesAttachment, $Subscriber, $SubscribersList;

            $query = "SELECT * FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " ae WHERE (ae.status = 'unsent' OR ae.status != 'sent') AND ae.senddate <= '" . date_i18n("Y-m-d H:i:s") . "';";

            $autoresponderemails = $wpdb -> get_results($query);

            if (!empty($autoresponderemails)) {
                foreach ($autoresponderemails as $ae) {

                    if (!empty($ae -> list_id)) {
                        if ($sl = $this -> Autoresponderemail() -> find(array('list_id' => $ae -> list_id, 'subscriber_id' => $ae -> subscriber_id))) {
                            if (!empty($sl -> active) && $sl -> active == "N") {
                                //don't do this autoresponder, the subscription is inactive
                                continue;
                            }
                        }
                    }

                    /* The History Email */
                    $query = "SELECT " . $wpdb -> prefix . $this -> History() -> table . ".id, "
                        . $wpdb -> prefix . $this -> History() -> table . ".subject, "
                        . $wpdb -> prefix . $this -> History() -> table . ".message, "
                        . $wpdb -> prefix . $this -> History() -> table . ".theme_id FROM "
                        . $wpdb -> prefix . $this -> History() -> table . " LEFT JOIN "
                        . $wpdb -> prefix . $this -> Autoresponder() -> table . " ON " . $wpdb -> prefix . $this -> History() -> table . ".id = " . $wpdb -> prefix . $this -> Autoresponder() -> table . ".history_id WHERE "
                        . $wpdb -> prefix . $this -> Autoresponder() -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";

                    $history = $wpdb -> get_row($query);
                    $history = stripslashes_deep($history);
                    $history -> attachments = array();

                    /* Attachments */
                    $attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";

                    if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
                        foreach ($attachments as $attachment) {
                            $history -> attachments[] = array(
                                'id'					=>	$attachment -> id,
                                'title'					=>	$attachment -> title,
                                'filename'				=>	$attachment -> filename,
                            );
                        }
                    }

                    /* The Subscriber */
                    $Db -> model = $Subscriber -> model;
                    $subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
                    $subscriber -> mailinglist_id = $ae -> list_id;

                    /* The Message */
                    $eunique = $Html -> eunique($subscriber, $history -> id);

                    /* Send the email */
                    $Db -> model = $Email -> model;
                    $message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);

                    if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {
                        $this -> Autoresponderemail() -> save_field('status', "sent", array('id' => $ae -> id));
                        $addedtoqueue++;
                        $this -> History() -> save_field('sent', 1, array('id' => $history -> id));
                    }
                }
            }

            //update scheduling
            $this -> autoresponder_scheduling();

            do_action('newsletters_autoresponders_hook_end');

            echo wp_kses_post($addedtoqueue . ' ' . __('autoresponder emails have been sent out.', 'wp-mailinglist') . '<br/>');

            return;
        }

        function pop_hook() {
            //update scheduling
            $this -> pop_scheduling();

            if ($this -> get_option('bouncemethod') == "pop") {
                $this -> bounce(false, "pop");
            }

            return;
        }

        function send_queued_email($email = null) {
            global $wpdb, $Html, $Db, $Email, $Subscriber, $SubscribersList;

            if (!empty($email)) {
                if (!empty($email['history_id'])) {
                    if ($history = $this -> History() -> find(array('id' => $email['history_id']))) {
                        // set the message from the history email
                        $message = $history -> message;
                        if($history -> using_grapeJS)
                        {
                            $message = $history -> grapejs_content;
                        }

                        // set the global $post and $shortcode post variables
                        if ($getpost = get_post($history -> post_id)) {
                            global $post, $shortcode_post;
                            $post = $getpost;
                            $shortcode_post = $getpost;
                        }
                    } else {
                        // history email does not exist
                        return true;
                    }
                } elseif (!empty($email['message'])) {
                    // the message is static
                    $message = $email['message'];
                } else {
                    // no history email or message
                    return true;
                }

                // Email to subscriber
                if (!empty($email['subscriber_id'])) {
                    if ($subscriber = $Subscriber -> get($email['subscriber_id'], false)) {
                        $eunique = $Html -> eunique($subscriber, $email['history_id']);
                        $message = $this -> render_email('send', array('message' => $message, 'subject' => $email['subject'], 'subscriber' => $subscriber, 'history_id' => $email['history_id'], 'post_id' => $email['post_id'], 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $email['theme_id']);

                        // Check if mailing lists are required
                        if (empty($subscriber -> mailinglists)) {
                            $subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, false, false, false);
                        }

                        if ($this -> execute_mail($subscriber, false, $email['subject'], $message, $email['attachments'], $email['history_id'], $eunique, true, "newsletter")) {
                            return true;
                        } else {
                            global $mailerrors;
                            $this -> log_error(sprintf(__('Email could not be sent from the queue: %s', 'wp-mailinglist'), $mailerrors));
                        }
                    } else {
                        //subscriber doesn't exist
                        return true;
                    }
                    // Email to user
                } elseif (!empty($email['user_id'])) {
                    if ($user = $this -> userdata($email['user_id'])) {
                        $eunique = $Html -> eunique($user, $email['history_id']);
                        $message = $this -> render_email('send', array('message' => $message, 'subject' => $email['subject'], 'subscriber' => false, 'user' => $user, 'history_id' => $email['history_id'], 'post_id' => $email['post_id']), false, 'html', true, $email['theme_id']);

                        if ($this -> execute_mail(false, $user, $email['subject'], $message, $email['attachments'], $email['history_id'], $eunique, true, "newsletter")) {
                            return true;
                        } else {
                            global $mailerrors;
                            $this -> log_error(sprintf(__('Email could not be sent from the queue: %s', 'wp-mailinglist'), $mailerrors));
                        }
                    } else {
                        //user doesn't exist
                        return true;
                    }
                } else {
                    // no user or subscriber
                    return true;
                }
            } else {
                // email array is completely empty
                return true;
            }

            return false;
        }

        function cron_hook() {
            global $wpdb, $Html, $Db, $Email, $Subscriber, $SubscribersList;
            do_action('newsletters_cron_fired');

            $this -> scheduling();

            $this -> History() -> queue_scheduled();
            $this -> History() -> queue_recurring();
            $this -> autoresponders_hook();

            $this -> qp_do_crons();

            // All done with this
            return true;
        }

        function upload_mimes($mimes = null) {

            $mimes['vcf'] = "text/x-vcard";
            $mimes['csv'] = "text/csv";

            return $mimes;
        }

        function wp_check_filetype_and_ext($data = array(), $file = null, $filename = null, $mimes = null) {
            $wp_filetype = wp_check_filetype( $filename, $mimes );

            $ext = $wp_filetype['ext'];
            $type = $wp_filetype['type'];
            $proper_filename = $data['proper_filename'];

            return compact('ext', 'type', 'proper_filename');
        }

        function the_editor($html = null) {
            /* Check multilingual Support */
            if (is_admin() && !defined('DOING_AJAX')) {
                if ($this -> language_do()) {
                    if ($this -> is_plugin_screen('send') || $this -> is_plugin_screen('settings_templates')) {
                        global $newsletters_languageplugin;

                        switch ($newsletters_languageplugin) {
                            case 'qtranslate'					:
                                remove_filter('the_editor', 'qtrans_modifyRichEditor');
                                remove_action('wp_tiny_mce_init', 'qtrans_TinyMCE_init');
                                break;
                            case 'qtranslate-x'					:
                                remove_filter('the_editor', 'qtranxf_modifyRichEditor');
                                remove_action('wp_tiny_mce_init', 'qtranxf_TinyMCE_init');
                                break;
                            default 							:
                                //do nothing...
                                break;
                        }
                    }
                }
            }

            return $html;
        }

        function cron_schedules($schedules = array()) {
            $schedules['1minutes']		= array('interval' => 60, 'display' => __('Every Minute', 'wp-mailinglist'));
            $schedules['2minutes']		= array('interval' => 120, 'display' => __('Every 2 Minutes', 'wp-mailinglist'));
            $schedules['5minutes']		= array('interval' => 300, 'display' => __('Every 5 Minutes', 'wp-mailinglist'));
            $schedules['10minutes']		= array('interval' => 600, 'display' => __('Every 10 Minutes', 'wp-mailinglist'));
            $schedules['20minutes'] 	= array('interval' => 1200, 'display' => __('Every 20 Minutes', 'wp-mailinglist'));
            $schedules['30minutes'] 	= array('interval' => 1800, 'display' => __('Every 30 Minutes', 'wp-mailinglist'));
            $schedules['40minutes'] 	= array('interval' => 2400, 'display' => __('Every 40 Minutes', 'wp-mailinglist'));
            $schedules['50minutes'] 	= array('interval' => 3000, 'display' => __('Every 50 minutes', 'wp-mailinglist'));
            $schedules['weekly']		= array('interval' => 604800, 'display' => __('Once Weekly', 'wp-mailinglist'));
            $schedules['monthly']		= array('interval' => 2664000, 'display' => __('Once Monthly', 'wp-mailinglist'));

            return $schedules;
        }

        function screen_settings($current, $screen) {
            // Screen Options for various sections

            $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
            if (!empty($page)) {
                // Newsletters > Subscribers
                if ($page == $this -> sections -> subscribers) {

                    if (!empty($_POST['screenoptions'])) {
                        if (!empty($_POST['fields']) && is_array($_POST['fields'])) {
                            $this -> update_option('screenoptions_subscribers_fields', map_deep(wp_unslash($_POST['fields']), 'sanitize_text_field'));
                        } else { delete_option($this -> pre . 'screenoptions_subscribers_fields'); }

                        if (!empty($_POST['custom']) && is_array($_POST['custom'])) {
                            $this -> update_option('screenoptions_subscribers_custom', map_deep(wp_unslash($_POST['custom']), 'sanitize_text_field'));
                        } else { delete_option($this -> pre . 'screenoptions_subscribers_custom'); }
                    }

                    global $Db, $Field;
                    $Db -> model = $Field -> model;
                    $conditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
                    $fields = $Db -> find_all($conditions, false, array('order', "ASC"));

                    $current .= $this -> render('subscribers' . DS . 'screen-options', array('fields' => $fields), false, 'admin');
                }

                // Newsletters > Sent & Draft Emails
                if ($page == $this -> sections -> history) {

                }
            }

            return $current;
        }

        function plugin_action_links($actions = null, $plugin_file = null, $plugin_data = null, $context = null) {
            $this_plugin = plugin_basename(__FILE__);

            if (!empty($plugin_file) && $plugin_file == $this_plugin) {
                $actions[] = '<a href="" onclick="jQuery.colorbox({href:newsletters_ajaxurl + \'action=' . $this -> pre . 'serialkey&security=' . wp_create_nonce('serialkey') . '\'}); return false;" id="' . $this -> pre . 'submitseriallink" title="' . __('Serial Key', 'wp-mailinglist') . '"><i class="fa fa-key fa-fw"></i>' . __('Serial Key', 'wp-mailinglist') . '</a>';
                $actions[] = '<a href="' . admin_url('admin.php?page=' . $this -> sections -> settings) . '"><i class="fa fa-cog fa-fw"></i>' . __('Settings', 'wp-mailinglist') . '</a>';

                if ($update = $this -> vendor('update')) {
                    $version_info = $update -> get_version_info();
                    if (!empty($version_info['dtype']) && $version_info['dtype'] == "single") {
                        $actions[] = '<a href="https://tribulant.com/items/upgrade/' . $version_info['item_id'] . '" target="_blank">' . __('Upgrade', 'wp-mailinglist') . '</a>';
                    }
                }
            }

            return $actions;
        }

        function plugin_row_meta($links = null, $plugin_file = null) {
            $this_plugin = plugin_basename(__FILE__);
            if (!empty($plugin_file) && $plugin_file == $this_plugin) {
                if (!empty($links)) {
                    foreach ($links as $lkey => $link) {
                        $links[$lkey] = str_replace("wp-mailinglist", "newsletters-lite", $link);
                    }
                }
            }

            return $links;
        }

        function init_textdomain() {
            global $Html, $newsletters_language_loaded;
            $newsletters_language_loaded = false;

            $locale = get_locale();

            if (!empty($locale)) {
                if ($locale == "ja" || $locale == "ja_JP") { setlocale(LC_ALL, "ja_JP.UTF8"); }
            } else {
                setlocale(LC_ALL, apply_filters('newsletters_setlocale', $locale));
            }

            if (function_exists('load_plugin_textdomain')) {
                $language_path = dirname($Html -> get_language_location());

                //if (load_plugin_textdomain($this -> plugin_name, false, $language_path)) {
                if (load_plugin_textdomain('wp-mailinglist', false, $language_path)) {
                    $newsletters_language_loaded = true;
                    return true;
                }
            }

            return false;
        }

        function save_newsletter($post_id = null, $post = null, $onlyupdatehistory = false) {
            global $wpdb, $Unsubscribe, $Db, $Html, $HistoriesAttachment, $Mailinglist, $Subscriber, $Field, $SubscribersList;

            //debug()
            return;

            // Check that the post_id and post is not empty
            if (!empty($post_id) && !empty($post)) {
                $custompostslug = $this -> get_option('custompostslug');
                // Only save the 'newsletter' custom post type
                if (!empty($post -> post_type) && $post -> post_type == $custompostslug) {

                    // Delete existing post meta
                    $query = "DELETE FROM `" . $wpdb -> postmeta . "` WHERE `post_id` = '" . $post_id . "' AND `meta_key` LIKE '_newsletters_%'";
                    $wpdb -> query($query);

                    foreach ($_POST as $pkey => $pval) {
                        if (!empty($pkey) && preg_match('/(newsletters\_*)/si', $pkey)) {
                            update_post_meta($post_id, '_' . $pkey, sanitize_text_field($pval));
                        }
                    }
                } else {
                    // This is not a newsletter custom post type, don't continue
                    return;
                }
            }

            $subject = wp_kses_post(wp_unslash($_POST['post_title']));
            $content = wp_kses_post(wp_unslash($_POST['content']));
            
            // Save the history email and get the preview
            $history_data = array(
                'from'				=>	sanitize_text_field(wp_unslash($_POST['newsletters_from'])),
                'fromname'			=>	sanitize_text_field(wp_unslash($_POST['newsletters_fromname'])),
                'post_id'			=>	false,
                'p_id'				=>	$post_id,
                'subject'			=>	$subject,
                'message'			=>	$content,
                'text'				=>	((!empty($_POST['newsletters_customtexton']) && !empty($_POST['newsletters_customtext'])) ? sanitize_textarea_field(wp_unslash($_POST['newsletters_customtext'])) : false),
                'theme_id'			=>	sanitize_text_field(wp_unslash($_POST['newsletters_theme_id'])),
                'condquery'			=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_condquery']), 'sanitize_text_field')),
                'conditions'		=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_fields']), 'sanitize_text_field')),
                'conditionsscope'	=>	sanitize_text_field(wp_unslash($_POST['newsletters_fieldsconditionsscope'])),
                'daterange'			=>	sanitize_text_field(wp_unslash($_POST['newsletters_daterange'])),
                'daterangefrom'		=>	sanitize_text_field(wp_unslash($_POST['newsletters_daterangefrom'])),
                'daterangeto'		=>	sanitize_text_field(wp_unslash($_POST['newsletters_daterangeto'])),
                'countries'			=>	array_map('sanitize_text_field', $_POST['newsletters_countries']),
                'selectedcountries'	=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_selectedcountries']), 'sanitize_text_field')),
                'mailinglists'		=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_mailinglists']), 'sanitize_text_field')),
                'groups'			=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_groups']), 'sanitize_text_field')),
                'roles'				=>	maybe_serialize(map_deep(wp_unslash($_POST['newsletters_roles']), 'sanitize_text_field')),
                'senddate'			=>	sanitize_text_field(wp_unslash($_POST['senddate'])),
                //'scheduled'			=>	$_POST['scheduled'],
                'scheduled'			=>	"N",
                'format'			=>	sanitize_text_field(wp_unslash($_POST['newsletters_format'])),
                'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                'using_grapeJS'     =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''

            );

            if (!empty($_POST['newsletters_history_id'])) {
                $history_data['id'] = sanitize_text_field(wp_unslash($_POST['newsletters_history_id']));
                $history_curr = $this -> History() -> find(array('id' => $history_data['id']));
                $history_data['sent'] = $history_curr -> sent;
            }

            if (!empty($_POST['newsletters_sendrecurring'])) {
                if (!empty($_POST['newsletters_sendrecurringvalue']) && !empty($_POST['newsletters_sendrecurringinterval']) && !empty($_POST['newsletters_sendrecurringdate'])) {
                    $history_data['recurring'] = "Y";
                    $history_data['recurringvalue'] = sanitize_text_field(wp_unslash($_POST['newsletters_sendrecurringvalue']));
                    $history_data['recurringinterval'] = sanitize_text_field(wp_unslash($_POST['newsletters_sendrecurringinterval']));
                    $history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['newsletters_sendrecurringdate']));
                    $history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['newsletters_sendrecurringlimit']));
                }
            }

            if ($this -> History() -> save($history_data, false, false)) {
                $history_id = $this -> History() -> insertid;
                $this -> History() -> save_field('p_id', $post_id, array('id' => $history_id));
                update_post_meta($post_id, '_newsletters_history_id', $history_id);

                if (!empty($_POST['contentarea'])) {
                    //phpcs:ignore
                    foreach (map_deep(wp_unslash($_POST['contentarea']), 'sanitize_text_field') as $number => $content) {
                        $content_data = array(
                            'number'			=>	$number,
                            'history_id'		=>	$history_id,
                            'content'			=>	$content,
                        );

                        $this -> Content() -> save($content_data, true);
                    }
                }
            }

            // Do not continue after this point
            if (!empty($onlyupdatehistory) || !empty($_POST['savedraft'])) {
                return true;
            }

            $post_status = $post -> post_status;
            switch ($post_status) {
                case 'draft'					:
                case 'future'					:
                    // don't do anything...
                    break;
                case 'publish'					:
                    $newsletters_mailinglists = array_map('sanitize_text_field', $_POST['newsletters_mailinglists']);
                    $newsletters_roles = array_map('sanitize_text_field', $_POST['newsletters_roles']);

                    if (!empty($_POST['groups'])) {
                        global $Db, $Mailinglist;

                        foreach (map_deep(wp_unslash($_POST['groups']), 'sanitize_text_field') as $group_id) {
                            $Db -> model = $Mailinglist -> model;
                            if ($mailinglists = $Db -> find_all(array('group_id' => sanitize_text_field($group_id)), array('id'))) {
                                foreach ($mailinglists as $mailinglist) {
                                    $newsletters_mailinglists[] = $mailinglist -> id;
                                }
                            }
                        }
                    }

                    global $errors;
                    $errors = array();

                    if (empty($subject)) { $errors['subject'] = __('Please fill in an email subject', 'wp-mailinglist'); }
                    if (empty($content)) { $errors['content'] = __('Please fill in a newsletter message', 'wp-mailinglist'); }
                    if ((empty($newsletters_mailinglists) || !is_array($newsletters_mailinglists)) && empty($newsletters_roles)) { $errors['mailinglists'] = __('Please select mailing list/s', 'wp-mailinglist'); }

                    //unset the fields if the "dofieldsconditions" was unchecked
                    if (empty($_POST['newsletters_dofieldsconditions'])) {
                        unset($_POST['newsletters_fields']);
                    }

                    if (empty($errors)) {

                        $defaulttexton = sanitize_text_field(wp_unslash($_POST['newsletters_defaulttexton']));
                        if (!empty($defaulttexton) && !empty($_POST['newsletters_customtext'])) {
                            $this -> update_option('defaulttexton', true);
                            $this -> update_option('defaulttextversion', sanitize_textarea_field(wp_unslash($_POST['newsletters_customtext'])));
                        } else {
                            $this -> delete_option('defaulttexton');
                            $this -> delete_option('defaulttextversion');
                        }

                        if (!empty($errors)) {
                            $this -> render_error(__('Newsletter could not be scheduled/qeueued', 'wp-mailinglist'));
                        } else {

                            global $Db, $Field;

                            if ($this -> get_option('subscriptions') == "Y") {
                                $SubscribersList -> check_expirations();
                            }

                            $subscriberids = array();
                            $subscriberemails = array();

                            if (!empty($newsletters_mailinglists) || !empty($newsletters_roles)) {
                                $mailinglistscondition = false;
                                if (!empty($newsletters_mailinglists)) {
                                    $mailinglistscondition = "(";
                                    $m = 1;

                                    foreach ($newsletters_mailinglists as $mailinglist_id) {
                                        $mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($mailinglist_id) . "'";
                                        if ($m < count($newsletters_mailinglists)) { $mailinglistscondition .= " OR "; }
                                        $m++;
                                    }

                                    // Fields conditions
                                    if (!empty($_POST['newsletters_dofieldsconditions'])) {
                                        $fields = array_filter(map_deep(wp_unslash($_POST['newsletters_fields']), 'sanitize_text_field'));
                                        $scopeall = (empty($_POST['newsletters_fieldsconditionsscope']) || $_POST['newsletters_fieldsconditionsscope'] == "all") ? true : false;
                                        $condquery = sanitize_text_field(wp_unslash($_POST['newsletters_condquery']));
                                        $fieldsquery = $Subscriber -> get_segmented_query($fields, $scopeall, $condquery);
                                    }

                                    // Date range
                                    if (!empty($_POST['newsletters_daterange']) && $_POST['newsletters_daterange'] == "Y") {
                                        if (!empty($_POST['newsletters_daterangefrom']) && !empty($_POST['newsletters_daterangeto'])) {
                                            $daterangefrom = date_i18n("Y-m-d", strtotime(sanitize_text_field(wp_unslash($_POST['newsletters_daterangefrom']))));
                                            $daterangeto = date_i18n("Y-m-d", strtotime(sanitize_text_field(wp_unslash($_POST['newsletters_daterangeto']))));
                                            $fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . esc_sql($daterangefrom) . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . esc_sql($daterangeto) . "')";
                                        }
                                    }

                                    // Countries
                                    if (!empty($_POST['newsletters_countries'])) {
                                        if (!empty($_POST['newsletters_selectedcountries']) && is_array($_POST['newsletters_selectedcountries'])) {
                                            $countries = implode("', '", array_map('sanitize_text_field', map_deep(wp_unslash($_POST['newsletters_selectedcountries']), 'sanitize_text_field')));
                                            $fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".country IN ('" . esc_sql($countries) . "'))";
                                        }
                                    }
                                }

                                /* Attachments */
                                $history = $this -> History() -> find(array('id' => $history_id));

                                $query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
                                    . $wpdb -> prefix . $Subscriber -> table . ".email FROM "
                                    . $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
                                    . $wpdb -> prefix . $SubscribersList -> table . " ON "
                                    . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id
									LEFT JOIN " . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id =
									" . $wpdb -> prefix . $Mailinglist -> table . ".id";

                                if (!empty($mailinglistscondition)) {
                                    $query .= " WHERE " . $mailinglistscondition . ")";
                                }

                                if (empty($_POST['status']) || $_POST['status'] == "active") {
                                    $query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
                                } elseif ($_POST['status'] == "inactive") {
                                    $query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'N'";
                                } elseif ($_POST['status'] == "all") {
                                    $query .= "";
                                }

                                $query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval
									OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')"
                                    . str_replace(" AND ()", "", $fieldsquery);

                                $sentmailscount = 0;
                                $sendingprogress_option = $this -> get_option('sendingprogress');
                                $sendingprogress = (!empty($_POST['newsletters_sendingprogress'])) ? "Y" : "N";
                                $datasets = array();
                                $d = 0;

                                $queue_process_counter_1 = 0;
                                $queue_process_counter_2 = 0;
                                $queue_process_counter_3 = 0;

                                $this -> qp_reset_data();
                                $queue_process = 1;

                                // Users by roles
                                if (!empty($_POST['newsletters_roles'])) {
                                    $users = array();
                                    $exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
                                    $exclude_users = $wpdb -> get_var($exclude_users_query);

                                    foreach (map_deep(wp_unslash($_POST['roles']), 'sanitize_text_field') as $role_key) {
                                        $users_arguments = array(
                                            'blog_id'				=>	$GLOBALS['blog_id'],
                                            'role'					=>	sanitize_text_field($role_key),
                                            'exclude'				=>	$exclude_users,
                                            'fields'				=>	array('ID', 'user_email', 'user_login'),
                                        );

                                        $role_users = get_users($users_arguments);
                                        $users = array_merge($users, $role_users);
                                    }

                                    if (!empty($users)) {
                                        foreach ($users as $user) {
                                            $this -> remove_server_limits();

                                            if ($sendingprogress == "N") {
                                                $queue_process_data = array(
                                                    'user_id'					=>	$user -> ID,
                                                    'subject'					=>	$subject,
                                                    'attachments'				=>	$newattachments,
                                                    'post_id'					=>	$post_id,
                                                    'history_id'				=>	$history_id,
                                                    'theme_id'					=>	sanitize_text_field(wp_unslash($_POST['newsletters_theme_id'])),
                                                    'senddate'					=>	sanitize_text_field(wp_unslash($_POST['newsletters_senddate'])),
                                                );

                                                $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);

                                                ${'queue_process_counter_' . $queue_process}++;
                                                if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                                    $this -> {'queue_process_' . $queue_process} -> save();
                                                    $this -> {'queue_process_' . $queue_process} -> reset_data();
                                                    ${'queue_process_counter_' . $queue_process} = 0;
                                                }

                                                $queue_process++;
                                                if ($queue_process > 3) {
                                                    $queue_process = 1;
                                                }
                                            } else {
                                                $dataset = array(
                                                    'id'				=>	false,
                                                    'user_id'			=>	$user -> ID,
                                                    'email'				=>	$user -> user_email,
                                                    'mailinglist_id'	=>	false,
                                                    'mailinglists'		=>	false,
                                                    'format'			=> 	'html',
                                                );

                                                $datasets[$d] = $dataset;
                                                $d++;
                                            }

                                            continue;
                                        }
                                    }
                                }

                                // Subscribers by lists
                                if (!empty($newsletters_mailinglists)) {
                                    $subscribers = $wpdb -> get_results($query);

                                    if (!empty($subscribers)) {
                                        foreach ($subscribers as $subscriber) {
                                            $this -> remove_server_limits();

                                            if ($sendingprogress == "N") {
                                                $queue_process_data = array(
                                                    'subscriber_id'				=>	$subscriber -> id,
                                                    'subject'					=>	$subject,
                                                    'attachments'				=>	$newattachments,
                                                    'post_id'					=>	$post_id,
                                                    'history_id'				=>	$history_id,
                                                    'theme_id'					=>	sanitize_text_field(wp_unslash($_POST['newsletters_theme_id'])),
                                                    'senddate'					=>	sanitize_text_field(wp_unslash($_POST['newsletters_senddate'])),
                                                );

                                                $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);

                                                ${'queue_process_counter_' . $queue_process}++;
                                                if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                                    $this -> {'queue_process_' . $queue_process} -> save();
                                                    $this -> {'queue_process_' . $queue_process} -> reset_data();
                                                    ${'queue_process_counter_' . $queue_process} = 0;
                                                }

                                                $queue_process++;
                                                if ($queue_process > 3) {
                                                    $queue_process = 1;
                                                }
                                            } else {
                                                $dataset = array(
                                                    'id'				=>	$subscriber -> id,
                                                    'email'				=>	$subscriber -> email,
                                                    'mailinglist_id'	=>	$subscriber -> mailinglist_id,
                                                    'mailinglists'		=>	$subscriber -> mailinglists,
                                                    'format'			=> 	(empty($subscriber -> format) ? 'html' : $subscriber -> format),
                                                );

                                                $datasets[$d] = $dataset;
                                                $d++;
                                            }

                                            continue;
                                        }
                                    }
                                }

                                if ($sendingprogress == "Y") {
                                    $subject = wp_kses_post(wp_unslash($_POST['subject']));
                                    $content = wp_kses_post(wp_unslash($_POST['content']));
                                    $this -> render('send-post', array('subscribers' => $datasets, 'subject' => $subject, 'content' => $content, 'attachments' => $newattachments, 'post_id' => $post_id, 'history_id' => $history_id, 'theme_id' => sanitize_text_field($_POST['theme_id'])), true, 'admin');
                                    $dontrendersend = true;
                                } else {
                                    $this -> qp_save();
                                    $this -> qp_dispatch();
                                    delete_transient('newsletters_queue_count');
                                    do_action($this -> pre . '_admin_emailsqueued', (count($subscribers) + count($users)));

                                    $message = (count($subscribers) + count($users)) . ' ' . __('emails have been queued.', 'wp-mailinglist');
                                    $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> queue), 'message', $message);
                                }
                            } else {
                                $message = __('No mailing lists or roles have been selected', 'wp-mailinglist');
                                $this -> render_error($message);
                            }
                        }

                        if (!empty($_POST['inctemplate'])) {
                            $this -> Template() -> inc_sent(sanitize_text_field($_POST['inctemplate']));
                        }
                    }

                    exit();
                    break;
            }

            return $post_id;
        }

        function save_post($post_id = null, $post = null) {
            global $wpdb, $post, $Db, $Html, $Shortcode, $Mailinglist, $Subscriber, $SubscribersList;
            $custompostslug = $this -> get_option('custompostslug');

            // Get the $post by ID
            $post = get_post($post_id);
            $post_status = $post -> post_status;

            // Don't do anything if it's a revision or 'newsletter' post type
            if (wp_is_post_revision($post_id) || $post -> post_type == $custompostslug) {
                return $post_id;
            }

            global $newsletters_queued_post;
            if (!empty($newsletters_queued_post) && $newsletters_queued_post == $post -> ID) {
                // this post was already queued in this process it seems
                // It is possible that the save_post hook is being fired again
                return true;
            }

            if (!empty($post_id) && !empty($post)) {
                $newsletters_subject = isset($_POST['newsletters_subject']) ? $_POST['newsletters_subject'] : '';

                switch ($post_status) {
                    // Saving a draft
                    case 'draft'					:
                    case 'future'					:
                        if (!empty($_POST['newsletters_sendasnewsletter'])) {
                            update_post_meta($post_id, '_newsletters_sendasnewsletter', 1);
                            update_post_meta($post_id, '_newsletters_subject', $newsletters_subject);
                            update_post_meta($post_id, '_newsletters_mailinglists', map_deep(wp_unslash($_POST['newsletters_mailinglists']), 'sanitize_text_field'));
                            update_post_meta($post_id, '_newsletters_showdate', sanitize_text_field(wp_unslash($_POST['newsletters_showdate'])));
                            update_post_meta($post_id, '_newsletters_theme_id', sanitize_text_field(wp_unslash($_POST['newsletters_theme_id'])));
                            update_post_meta($post_id, '_newsletters_language', sanitize_text_field(wp_unslash($_POST['newsletters_language'])));
                            update_post_meta($post_id, '_newsletters_sendonpublishef', sanitize_text_field(wp_unslash($_POST['newsletters_sendonpublishef'])));

                            if ($post_status == "future") {
                                update_post_meta($post_id, '_newsletters_scheduled', 1);
                            }
                        } else {
                            delete_post_meta($post_id, '_newsletters_sendasnewsletter');
                            delete_post_meta($post_id, '_newsletters_subject');
                            delete_post_meta($post_id, '_newsletters_mailinglists');
                            delete_post_meta($post_id, '_newsletters_showdate');
                            delete_post_meta($post_id, '_newsletters_theme_id');
                            delete_post_meta($post_id, '_newsletters_language');
                            delete_post_meta($post_id, '_newsletters_sendonpublishef');
                            delete_post_meta($post_id, '_newsletters_scheduled');
                        }
                        break;
                    case 'publish'					:
                        global $shortcode_post, $shortcode_post_language, $wpml_target;

                        if (!empty($_POST['newsletters_sendasnewsletter'])) {
                            $newsletters_mailinglists = map_deep(wp_unslash($_POST['newsletters_mailinglists']), 'sanitize_text_field');
                            $newsletters_showdate = sanitize_text_field(wp_unslash($_POST['newsletters_showdate']));
                            $newsletters_theme_id = sanitize_text_field(wp_unslash($_POST['newsletters_theme_id']));
                            $newsletters_language = sanitize_text_field(wp_unslash($_POST['newsletters_language']));
                            $newsletters_sendonpublishef = sanitize_text_field(wp_unslash($_POST['newsletters_sendonpublishef']));
                        } else {
                            $newsletters_sendasnewsletter = get_post_meta($post_id, '_newsletters_sendasnewsletter', true);

                            if (!empty($newsletters_sendasnewsletter)) {
                                $newsletters_subject = get_post_meta($post_id, '_newsletters_subject', true);
                                $newsletters_mailinglists = get_post_meta($post_id, '_newsletters_mailinglists', true);
                                $newsletters_showdate = get_post_meta($post_id, '_newsletters_showdate', true);
                                $newsletters_theme_id = get_post_meta($post_id, '_newsletters_theme_id', true);
                                $newsletters_language = get_post_meta($post_id, '_newsletters_language', true);
                                $newsletters_sendonpublishef = get_post_meta($post_id, '_newsletters_sendonpublishef', true);
                            } else {
                                $sendas_defaults_postbyemail = $this -> get_option('sendas_defaults_postbyemail');
                                if (!empty($sendas_defaults_postbyemail)) {
                                    $send_lists = array();
                                    $sendas_defaults = maybe_unserialize($this -> get_option('sendas_defaults'));
                                    if (!empty($sendas_defaults)) {
                                        if ($post_categories = wp_get_post_categories($post_id)) {
                                            foreach ($post_categories as $post_category) {
                                                $category = get_category($post_category);
                                                foreach ($sendas_defaults as $sendas_default) {
                                                    if ($category -> cat_ID == $sendas_default['category']) {
                                                        $send_lists = array_merge($send_lists, $sendas_default['lists']);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    // Finally set the lists for queuing
                                    $newsletters_mailinglists = $send_lists;
                                    $newsletters_theme_id = $this -> default_theme_id('sending');

                                    // Full post or excerpt
                                    $sendas_defaults_postbyemailoutput = $this -> get_option('sendas_defaults_postbyemailoutput');
                                    $newsletters_sendonpublishef = (empty($sendas_defaults_postbyemailoutput) || $sendas_defaults_postbyemailoutput == "full") ? 'fp' : 'excerpt';
                                }
                            }
                        }

                        $shortcode_post = $post;
                        $shortcode_post_language = $newsletters_language;

                        add_filter('excerpt_length', array($Shortcode, 'excerpt_length'));
                        add_filter('excerpt_more', array($Shortcode, 'excerpt_more'));
                        add_filter('post_password_required', array($Shortcode, 'post_password_required'), 10, 2);
                        add_filter('the_content_more_link', array($Shortcode, 'excerpt_more'));

                        if (!empty($newsletters_mailinglists)) {
                            // Setup the global post data
                            setup_postdata($post);

                            // Do language related things
                            if ($this -> language_do()) {
                                if ($languages = $this -> language_getlanguages()) {
                                    if (!empty($newsletters_language)) {
                                        $titles = $this -> language_split($post -> post_title);
                                        $contents = $this -> language_split($post -> post_content);

                                        if (!empty($titles[$newsletters_language])) { $post -> post_title = $titles[$newsletters_language]; }
                                        if (!empty($contents[$newsletters_language])) { $post -> post_content = $contents[$newsletters_language]; }
                                    }
                                }
                            }

                            $message = '';
                            $message .= '[newsletters_sendas post_id="' . $post -> ID . '"';
                            $message .= ' showdate="' . ((!empty($newsletters_showdate)) ? 'Y' : 'N') . '"';
                            $message .= ((!empty($newsletters_language)) ? ' language="' . $newsletters_language . '"' : '');
                            $message .= ' eftype="' . ((!empty($newsletters_sendonpublishef) && $newsletters_sendonpublishef == "fp") ? 'full' : 'excerpt') . '"';
                            $message .= ']';

                            if ($this -> get_option('subscriptions') == "Y") {
                                $SubscribersList -> check_expirations();
                            }

                            // Make sure the subject is not empty
                            if (!empty($newsletters_subject)) {
                                $subject = wp_unslash($newsletters_subject);
                            } else {
                                $subject = wp_unslash($post -> post_title);
                            }

                            //save the History record
                            $history_data = array(
                                'subject'			=>	$subject,
                                'message'			=>	$message,
                                'theme_id'			=>	$newsletters_theme_id,
                                'mailinglists'		=>	maybe_serialize($newsletters_mailinglists),
                                'attachment'		=>	"N",
                                'sent'				=>	1,
                                'post_id'			=>	$post_id,
                                'attachmentfile'	=>	false
                            );

                            $this -> History() -> save($history_data, false, false);
                            $history_id = $this -> History() -> insertid;

                            $mailinglistscondition = "(";
                            $m = 1;

                            foreach ($newsletters_mailinglists as $mailinglist_id) {
                                $mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . $mailinglist_id . "'";
                                if ($m < count($newsletters_mailinglists)) { $mailinglistscondition .= " OR "; }
                                $m++;
                            }

                            $query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
                                . $wpdb -> prefix . $Subscriber -> table . ".email FROM "
                                . $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
                                . $wpdb -> prefix . $SubscribersList -> table . " ON "
                                . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id WHERE "
                                . $mailinglistscondition . ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";

                            if ($subscribers = $wpdb -> get_results($query)) {
                                $queue_process_counter_1 = 0;
                                $queue_process_counter_2 = 0;
                                $queue_process_counter_3 = 0;
                                $queue_process = 1;
                                $this -> qp_reset_data();

                                foreach ($subscribers as $subscriber) {
                                    $this -> remove_server_limits();

                                    $queue_process_data = array(
                                        'subscriber_id'				=>	$subscriber -> id,
                                        'subject'					=>	$subject,
                                        'attachments'				=>	false,
                                        'post_id'					=>	$post_id,
                                        'history_id'				=>	$history_id,
                                        'theme_id'					=>	$newsletters_theme_id,
                                        'senddate'					=>	false,
                                    );

                                    $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);

                                    ${'queue_process_counter_' . $queue_process}++;
                                    if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                        $this -> {'queue_process_' . $queue_process} -> save();
                                        $this -> {'queue_process_' . $queue_process} -> reset_data();
                                        ${'queue_process_counter_' . $queue_process} = 0;
                                    }

                                    $queue_process++;
                                    if ($queue_process > 3) {
                                        $queue_process = 1;
                                    }
                                }

                                $this -> qp_save();
                                $this -> qp_dispatch();

                                $newsletters_queued_post = $post -> ID;

                                if (!$this -> Post() -> get_by_post_id($post -> ID)) {
                                    $post_data = array('post_id' => $post -> ID, 'sent' => "Y");
                                    $this -> Post() -> save($post_data, false);
                                    update_post_meta($post -> ID, '_newsletters_sent', true);
                                    update_post_meta($post -> ID, '_newsletters_history_id', $history_id);
                                }
                            }

                            $_POST['newsletters_sendasnewsletter'] = false;

                            delete_post_meta($post_id, '_newsletters_sendasnewsletter');
                            delete_post_meta($post_id, '_newsletters_subject');
                            delete_post_meta($post_id, '_newsletters_mailinglists');
                            delete_post_meta($post_id, '_newsletters_showdate');
                            delete_post_meta($post_id, '_newsletters_theme_id');
                            delete_post_meta($post_id, '_newsletters_language');
                            delete_post_meta($post_id, '_newsletters_sendonpublishef');
                            delete_post_meta($post_id, '_newsletters_scheduled');
                        }
                        break;
                }
            }
        }

        function delete_post($post_id = null) {
            global $Db;

            if (!empty($post_id)) {
                $this -> Post() -> delete_all(array('post_id' => $post_id));
                $this -> Latestpost() -> delete_all(array('post_id' => $post_id));
                $this -> History() -> save_field('post_id', "0", array('post_id' => $post_id));
            }
        }

        function admin() {
            $this -> admin_index();
        }

        function set_screen_option($status = null, $option = null, $value = null) {
            return $value;
        }

        function screen_options_history() {
            $screen = get_current_screen();

            // get out of here if we are not on our settings page
            if (!is_object($screen) || $screen -> id != $this -> menus['newsletters-history']) {
                return;
            }

            $args = array(
                'label' 	=> 	__('Newsletters per page', 'wp-mailinglist'),
                'default' 	=> 	15,
                'option' 	=> 	'newsletters_history_perpage'
            );

            add_screen_option('per_page', $args);

            require_once $this -> plugin_base() . DS . 'vendors' . DS . 'wp_list_table' . DS . 'newsletter.php';
            $Newsletter_List_Table = new Newsletter_List_Table;
        }

        function default_hidden_columns($hidden = null, $screen = null) {
            if ($current_screen = get_current_screen()) {
                if ($current_screen -> id == $screen -> id) {
                    switch ($screen -> id) {
                        case 'newsletters_page_' . $this -> sections -> history  				:
                            $hidden = array(
                                'recurring',
                                'post_id',
                                'user_id',
                                'attachments',
                            );
                            break;
                    }
                }
            }

            return $hidden;
        }

        function add_dashboard() {
            add_dashboard_page(sprintf('%s %s', $this -> name, $this -> version), sprintf('%s %s', $this -> name, $this -> version), 'read', 'newsletters-about', array($this, 'newsletters_about'));
            remove_submenu_page('index.php', 'newsletters-about');
        }

        function newsletters_about() {
            $this -> delete_option('activation_redirect');
            $this -> render('about', false, true, 'admin');
        }

        function dashboard_columns() {
            add_screen_option(
                'layout_columns',
                array(
                    'max'     	=> 	4,
                    'default' 	=> 	2
                )
            );

            ?>

            <div id="newsletters-postbox-container-css">
                <style>
                    .postbox-container {
                        min-width: 49.5% !important;
                    }
                    .meta-box-sortables.ui-sortable.empty-container {
                        display: none;
                    }
                </style>
            </div>

            <script type="text/javascript">
                jQuery(document).ready(function() {
                    postbox_container_set_width(jQuery('.columns-prefs input[name="screen_columns"]:checked').val());

                    jQuery('.columns-prefs input[name="screen_columns"]').on('click', function(element) {
                        var columns = jQuery(this).val();
                        postbox_container_set_width(columns);
                    });
                });

                var postbox_container_set_width = function(columns) {
                    if (columns == 1) {
                        jQuery('#newsletters-postbox-container-css').html('<style>.postbox-container { min-width:100% !important; }</style>');
                    } else if (columns == 2) {
                        jQuery('#newsletters-postbox-container-css').html('<style>.postbox-container { min-width:49.5% !important; }</style>');
                    } else if (columns == 3) {
                        jQuery('#newsletters-postbox-container-css').html('<style>.postbox-container { min-width:33% !important; }</style>');
                    } else if (columns == 4) {
                        jQuery('#newsletters-postbox-container-css').html('<style>.postbox-container { min-width:25% !important; width:25% !important; }</style>');
                    }

                    var append = '.postbox-container { float:left !important; min-height:100px !important; } .postbox-container .empty-container { min-height:100px !important; }';

                    jQuery('#newsletters-postbox-container-css style').append(append);
                }
            </script>

            <?php
        }

        function admin_menu() {
            global $queue_count, $queue_status;
            $queue_count = $this -> qp_get_queued_count();
            $queue_status = $this -> get_option('queue_status');
            $queue_count_icon = ' <span class="update-plugins count-1"><span class="update-count" id="newsletters-menu-queue-count">' . $queue_count . '</span></span>';
            //$update_icon = ($this -> has_update()) ? ' <span class="update-plugins count-1"><span class="update-count">1</span></span>' : '';
            $update_icon = '';
            $menunames = $this -> get_menu_names();

            add_menu_page(esc_html($this -> name, 'wp-mailinglist'), esc_html($this -> name, 'wp-mailinglist') . $update_icon, 'newsletters_welcome', $this -> sections -> welcome, array($this, 'admin'), false, "26.11");

            if (false && !$this -> ci_serial_valid()) {
                $this -> menus['newsletters-submitserial'] = add_submenu_page($this -> sections -> welcome, __('Submit Serial', 'wp-mailinglist'), __('Submit Serial', 'wp-mailinglist'), 'newsletters_welcome', $this -> sections -> submitserial, array($this, 'admin_submitserial'));
            } else {
                $this -> menus['newsletters'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters']), esc_html($menunames['newsletters']), 'newsletters_welcome', $this -> sections -> welcome, array($this, 'admin'));

                $this -> menus['newsletters-settings'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-settings']), esc_html($menunames['newsletters-settings']), 'newsletters_settings', $this -> sections -> settings, array($this, 'admin_config'));
                $this -> menus['newsletters-settings-subscribers'] = add_submenu_page($this -> menus['newsletters-settings'], __('Subscribers Configuration', 'wp-mailinglist'), __('Subscribers', 'wp-mailinglist'), 'newsletters_settings_subscribers', $this -> sections -> settings_subscribers, array($this, 'admin_settings_subscribers'));
                $this -> menus['newsletters-settings-templates'] = add_submenu_page($this -> menus['newsletters-settings'], __('System Emails Configuration', 'wp-mailinglist'), __('System Emails', 'wp-mailinglist'), 'newsletters_settings_templates', $this -> sections -> settings_templates, array($this, 'admin_settings_templates'));
                $this -> menus['newsletters-settings-system'] = add_submenu_page($this -> menus['newsletters-settings'], __('System Configuration', 'wp-mailinglist'), __('System', 'wp-mailinglist'), 'newsletters_settings_system', $this -> sections -> settings_system, array($this, 'admin_settings_system'));
                $this -> menus['newsletters-settings-tasks'] = add_submenu_page($this -> menus['newsletters-settings'], __('Scheduled Tasks', 'wp-mailinglist'), __('Scheduled Tasks', 'wp-mailinglist'), 'newsletters_settings_tasks', $this -> sections -> settings_tasks, array($this, 'admin_settings_tasks'));
                $this -> menus['newsletters-settings-api'] = add_submenu_page($this -> menus['newsletters-settings'], __('API', 'wp-mailinglist'), __('API', 'wp-mailinglist'), 'newsletters_settings_api', $this -> sections -> settings_api, array($this, 'admin_settings_api'));
                $this -> menus['newsletters-view-logs'] = add_submenu_page($this -> menus['newsletters'], __('View Logs', 'wp-mailinglist'), __('View Logs', 'wp-mailinglist'), 'newsletters_settings_api', $this -> sections -> view_logs, array($this, 'admin_view_logs'));
                $this -> menus['newsletters-settings-updates'] = add_submenu_page($this -> menus['newsletters-settings'], __('Updates', 'wp-mailinglist'), __('Updates', 'wp-mailinglist') . $update_icon, 'newsletters_settings_updates', $this -> sections -> settings_updates, array($this, 'admin_settings_updates'));
                $this -> menus['newsletters-forms'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-forms']), esc_html($menunames['newsletters-forms']), 'newsletters_forms', $this -> sections -> forms, array($this, 'admin_forms'));
                $this -> menus['newsletters-send'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-send']), esc_html($menunames['newsletters-send']), 'newsletters_send', $this -> sections -> send, array($this, 'admin_send'));

                $this -> menus['newsletters-history'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-history']), esc_html($menunames['newsletters-history']), 'newsletters_history', $this -> sections -> history, array($this, 'admin_history'));
                add_action("load-" . $this -> menus['newsletters-history'], array($this, 'screen_options_history'));

                $this -> menus['newsletters-emails'] = add_submenu_page($this -> menus['newsletters-history'], __('All Emails', 'wp-mailinglist'), __('All Emails', 'wp-mailinglist'), 'newsletters_emails', $this -> sections -> emails, array($this, 'admin_emails'));

                // New custom post type screens
                //$this -> menus['newsletters-edit'] = add_submenu_page($this -> sections -> welcome, 'Create Newsletter', 'Create Newsletter (New)', "newsletters_send", 'post-new.php?post_type=newsletter', '', '');
                //$this -> menus['newsletters-post-new'] = $val = add_submenu_page($this -> sections -> welcome, 'Manage Newsletters', 'Manage Newsletters (New)', "newsletters_history", 'edit.php?post_type=newsletter', '', '');

                if ($this -> get_option('clicktrack') == "Y") {
                    $this -> menus['newsletters-links'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-links']), esc_html($menunames['newsletters-links']), 'newsletters_links', $this -> sections -> links, array($this, 'admin_links'));
                    $this -> menus['newsletters-links-clicks'] = add_submenu_page($this -> menus['newsletters-links'], __('Clicks', 'wp-mailinglist'), __('Clicks', 'wp-mailinglist'), 'newsletters_clicks', $this -> sections -> clicks, array($this, 'admin_clicks'));
                }

                $this -> menus['newsletters-autoresponders'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-autoresponders']), esc_html($menunames['newsletters-autoresponders']), 'newsletters_autoresponders', $this -> sections -> autoresponders, array($this, 'admin_autoresponders'));
                $this -> menus['newsletters-autoresponderemails'] = add_submenu_page($this -> menus['newsletters-autoresponders'], __('Autoresponder Emails', 'wp-mailinglist'), __('Autoresponder Emails', 'wp-mailinglist'), 'newsletters_autoresponderemails', $this -> sections -> autoresponderemails, array($this, 'admin_autoresponderemails'));
                $this -> menus['newsletters-lists'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-lists']), esc_html($menunames['newsletters-lists']), 'newsletters_lists', $this -> sections -> lists, array($this, 'admin_mailinglists'));
                $this -> menus['newsletters-groups'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-groups']), esc_html($menunames['newsletters-groups']), 'newsletters_groups', $this -> sections -> groups, array($this, 'admin_groups'));

                $this -> menus['newsletters-subscribers'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-subscribers']), esc_html($menunames['newsletters-subscribers']), 'newsletters_subscribers', $this -> sections -> subscribers, array($this, 'admin_subscribers'));
                $this -> menus['newsletters-import'] = add_submenu_page($this -> menus['newsletters-subscribers'], esc_html($menunames['newsletters-import']), esc_html($menunames['newsletters-import']), 'newsletters_importexport', $this -> sections -> importexport, array($this, 'admin_importexport'));

                $this -> menus['newsletters-fields'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-fields']), esc_html($menunames['newsletters-fields']), 'newsletters_fields', $this -> sections -> fields, array($this, 'admin_fields'));
                $this -> menus['newsletters-themes'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-themes']), esc_html($menunames['newsletters-themes']), 'newsletters_themes', $this -> sections -> themes, array($this, 'admin_themes'));
                $this -> menus['newsletters-templates'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-templates']), esc_html($menunames['newsletters-templates']), 'newsletters_templates', $this -> sections -> templates, array($this, 'admin_templates'));
                $this -> menus['newsletters-templates-save'] = add_submenu_page($this -> menus['newsletters-templates'], __('Save an Email Snippet', 'wp-mailinglist'), __('Save an Email Snippet', 'wp-mailinglist'), 'newsletters_templates_save', $this -> sections -> templates_save, array($this, 'admin_templates'));
                $this -> menus['newsletters-queue'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-queue']), esc_html($menunames['newsletters-queue']) . ((!empty($queue_count)) ? $queue_count_icon : ''), 'newsletters_queue', $this -> sections -> queue, array($this, 'admin_mailqueue'));
                $this -> menus['newsletters-orders'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-orders']), esc_html($menunames['newsletters-orders']), 'newsletters_orders', $this -> sections -> orders, array($this, 'admin_orders'));
                $this -> menus['newsletters-extensions'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-extensions']), esc_html($menunames['newsletters-extensions']), 'newsletters_extensions', $this -> sections -> extensions, array($this, 'admin_extensions'));
                $this -> menus['newsletters-extensions-settings'] = add_submenu_page($this -> menus['newsletters-extensions'], __('Extensions Settings', 'wp-mailinglist'), __('Extensions Settings', 'wp-mailinglist'), 'newsletters_extensions_settings', $this -> sections -> extensions_settings, array($this, 'admin_extensions_settings'));

                /*if ($this -> has_update()) {
					$this -> menus['newsletters-updates'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-updates']), esc_html($menunames['newsletters-updates']) . $update_icon, 'newsletters_settings_updates', $this -> sections -> settings_updates, array($this, 'admin_settings_updates'));
				}*/

                if (WPML_SHOW_SUPPORT) {
                    $this -> menus['newsletters-support'] = add_submenu_page($this -> menus['newsletters-settings'], esc_html($menunames['newsletters-support']), esc_html($menunames['newsletters-support']), 'newsletters_support', $this -> sections -> support, array($this, 'admin_help'));
                }
            }

            if (!$this -> ci_serial_valid()) {
                $this -> menus['newsletters-submitserial'] = add_submenu_page($this -> sections -> welcome, esc_html($menunames['newsletters-submitserial']), esc_html($menunames['newsletters-submitserial']), 'newsletters_welcome', $this -> sections -> submitserial, array($this, 'admin_submitserial'));
            }

            $this -> menus['newsletters-gdpr'] = add_submenu_page($this -> menus['newsletters-settings'], esc_html($menunames['newsletters-gdpr']), esc_html($menunames['newsletters-gdpr']), 'newsletters_gdpr', $this -> sections -> gdpr, array($this, 'admin_gdpr'));

            do_action('newsletters_admin_menu', $this -> menus);

            add_action('admin_head-' . $this -> menus['newsletters'], array($this, 'admin_head_welcome'));
            add_action('admin_head-' . $this -> menus['newsletters-send'], array($this, 'admin_head_send'));
            add_action('admin_head-' . $this -> menus['newsletters-forms'], array($this, 'admin_head_forms'));
            add_action('admin_head-' . $this -> menus['newsletters-templates-save'], array($this, 'admin_head_templates_save'));
            add_action('admin_head-' . $this -> menus['newsletters-themes'], array($this, 'admin_head_themes_save'));
            add_action('admin_head-' . $this -> menus['newsletters-settings'], array($this, 'admin_head_settings'));
            add_action('admin_head-' . $this -> menus['newsletters-settings-system'], array($this, 'admin_head_settings_system'));
            add_action('admin_head-' . $this -> menus['newsletters-settings-templates'], array($this, 'admin_head_settings_templates'));
            add_action('admin_head-' . $this -> menus['newsletters-settings-subscribers'], array($this, 'admin_head_settings_subscribers'));
            add_action('admin_head-' . $this -> menus['newsletters-extensions-settings'], array($this, 'admin_head_settings_extensions_settings'));

            add_action('admin_head-edit.php', array($this, 'admin_head_newsletters_edit'));
            add_action('admin_head-post-new.php', array($this, 'admin_head_newsletters_post_new'));
            add_action('admin_head-post.php', array($this, 'admin_head_newsletters_post_new'));
        }

        function admin_head_newsletter() {
            $post_type = get_post_type();
            $custompostslug = $this -> get_option('custompostslug');
            if (!empty($post_type) && $post_type == $custompostslug) {
                $this -> render('head-newsletter', false, true, 'admin');
            }
        }

        function admin_foot_newsletter() {
            $post_type = get_post_type();
            $custompostslug = $this -> get_option('custompostslug');
            if (!empty($post_type) && $post_type == $custompostslug) {
                $this -> render('foot-newsletter', false, true, 'admin');
            }
        }

        function admin_head() {
            $this -> render('head', false, true, 'admin');
        }

        function admin_footer() {
            //do nothing...
        }

        function admin_head_welcome() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('quicksearchdiv', __('Quick Search', 'wp-mailinglist') . $Html -> help(__('Quick search', 'wp-mailinglist')), array($Metabox, 'welcome_quicksearch'), $page, 'side', 'core');
            add_meta_box('subscribersdiv', __('Total Subscribers', 'wp-mailinglist') . $Html -> help(__('This is the total number of subscribers in the database. In other words, email addresses. Each subscriber could have multiple subscriptions to different lists or no subscriptions at all for that matter.', 'wp-mailinglist')), array($Metabox, 'welcome_subscribers'), $page, 'side', 'core');
            add_meta_box('listsdiv', __('Total Mailing Lists', 'wp-mailinglist') . $Html -> help(__('The total mailing lists that you have in use. Each list can have a purpose of its own, make use of lists to organize and power your subscribers.', 'wp-mailinglist')), array($Metabox, 'welcome_lists'), $page, 'side', 'core');
            add_meta_box('emailsdiv', __('Total Emails', 'wp-mailinglist') . $Html -> help(__('The total number of emails sent to date since the plugin was installed until now.', 'wp-mailinglist')), array($Metabox, 'welcome_emails'), $page, 'side', 'core');
            add_meta_box('bouncesdiv', __('Bounced Emails', 'wp-mailinglist') . $Html -> help(__('The total number of bounces to date.', 'wp-mailinglist')), array($Metabox, 'welcome_bounces'), $page, 'side', 'core');
            add_meta_box('unsubscribesdiv', __('Unsubscribes', 'wp-mailinglist') . $Html -> help(__('Total unsubscribes to date.', 'wp-mailinglist')), array($Metabox, 'welcome_unsubscribes'), $page, 'side', 'core');
            add_meta_box('statsdiv', __('Statistics Overview', 'wp-mailinglist') . $Html -> help(__('This chart shows an overview of subscribers, emails sent, unsubscribes, bounces, etc in a visual manner.', 'wp-mailinglist')), array($Metabox, 'welcome_stats'), $page, 'normal', 'core');
            add_meta_box('historydiv', __('Recent Emails', 'wp-mailinglist') . $Html -> help(__('This is a quick overview of your 5 latest newsletters.', 'wp-mailinglist')), array($Metabox, 'welcome_history'), $page, 'normal', 'core');

            do_action($this -> pre . '_metaboxes_overview', $page, "normal", $post);

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_forms() {
            global $Metabox, $Html;

            $screen = get_current_screen();
            $page = $screen -> id;
            if(!isset($post))
            {
                $post = array();
            }
            add_meta_box('submitdiv', __('Save Form', 'wp-mailinglist'), array($Metabox, 'forms_submit'), $page, 'side', 'core');
            add_meta_box('fieldsdiv', __('Available Fields', 'wp-mailinglist') . $Html -> help(sprintf(__('Custom fields from %s > Custom Fields. You can drag/drop fields into the form or click on the field to add it.', 'wp-mailinglist'), $this -> name)), array($Metabox, 'forms_fields'), $page, 'side', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_newsletters_edit() {

        }

        function meta_box_order_newsletter($metaboxes = null) {

            if (!empty($metaboxes)) {
                if (!empty($metaboxes['side'])) {
                    $side = explode(",", $metaboxes['side']);
                    if (!empty($side) && is_array($side)) {
                        $submitkey = array_search('submitdiv', $metaboxes['side']);
                        $metaboxes['side'] = array_merge(array('submitdiv'), $metaboxes['side']);
                    }
                }
            }

            return $metaboxes;
        }

        function post_updated_messages($messages = null) {

            global $post, $post_type, $post_type_object;

            $permalink = get_permalink($post -> ID);
            if (empty($permalink)) {
                $permalink = '';
            }

            $messages = array();

            $preview_post_link_html = $scheduled_post_link_html = $view_post_link_html = '';
            $preview_page_link_html = $scheduled_page_link_html = $view_page_link_html = '';

            $preview_url = get_preview_post_link( $post );
            $viewable = is_post_type_viewable( $post_type_object );

            if ( $viewable ) {

                // Preview post link.
                $preview_post_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
                    esc_url( $preview_url ),
                    __( 'Preview newsletter', 'wp-mailinglist')
                );

                // Scheduled post preview link.
                $scheduled_post_link_html = sprintf( ' <a target="_blank" href="%1$s">%2$s</a>',
                    esc_url( $permalink ),
                    __( 'Preview newsletter', 'wp-mailinglist')
                );

                // View post link.
                $view_post_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
                    esc_url( $permalink ),
                    __( 'View newsletter', 'wp-mailinglist')
                );
            }

            /* translators: Publish box date format, see https://secure.php.net/date */
            $scheduled_date = date_i18n(__('M j, Y @ H:i'), strtotime($post -> post_date));

            $messages['newsletter'] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => __( 'Newsletter updated.', 'wp-mailinglist') . $view_post_link_html,
                2 => __( 'Custom field updated.', 'wp-mailinglist'),
                3 => __( 'Custom field deleted.', 'wp-mailinglist'),
                4 => __( 'Newsletter updated.', 'wp-mailinglist'),
                /* translators: %s: date and time of the revision */
                5 => isset($_GET['revision']) ? sprintf( __( 'Newsletter restored to revision from %s.', 'wp-mailinglist'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6 => __( 'Newsletter published.', 'wp-mailinglist') . $view_post_link_html,
                7 => __( 'Newsletter saved.', 'wp-mailinglist'),
                8 => __( 'Newsletter submitted.', 'wp-mailinglist') . $preview_post_link_html,
                9 => sprintf( __( 'Newsletter scheduled for: %s.', 'wp-mailinglist'), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
                10 => __( 'Newsletter draft updated.', 'wp-mailinglist') . $preview_post_link_html,
            );

            return $messages;
        }

        function admin_head_newsletters_post_new() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;
            $custompostslug = $this -> get_option('custompostslug');

            // Remove metaboxes
            remove_meta_box('slugdiv', $custompostslug, 'normal');
            remove_meta_box('submitdiv', $custompostslug, 'side');

            $publish_callback_args = array( '__back_compat_meta_box' => true );
            add_meta_box('newsletters_submit', '<i class="fa fa-paper-plane fa-fw"></i> ' . __('Send Newsletter', 'wp-mailinglist'), array($Metabox, 'newsletters_submit'), $custompostslug, 'side', 'core');

            $createspamscore = $this -> get_option('createspamscore');
            if (!empty($createspamscore) && $createspamscore == "Y") {
                add_meta_box('newsletters_spamscore', '<i class="fa fa-meh-o fa-fw"></i> ' . __('Spam Score', 'wp-mailinglist'), array($Metabox, 'newsletters_spamscore'), $custompostslug, 'side', 'core');
            }

            add_meta_box('newsletters_mailinglists', '<i class="fa fa-users fa-fw"></i> ' . __('Subscribers', 'wp-mailinglist') . $Html ->  help(__('Tick/check the group(s) or list(s) that you want to send/queue this newsletter to. The newsletter will only be sent to active subscriptions in the chosen list(s).', 'wp-mailinglist')), array($Metabox, 'newsletters_mailinglists'), $custompostslug, 'side', 'core');

            if (!$this -> is_block_editor()) {
                add_meta_box('newsletters_insert', '<i class="fa fa-level-down fa-fw"></i> ' . __('Insert into Newsletter', 'wp-mailinglist') . $Html -> help(__('Use this box to insert various things into your newsletter such as posts, snippets, custom fields and post thumbnails.', 'wp-mailinglist')), array($Metabox, 'newsletters_insert'), $custompostslug, 'side', 'core');
            }

            add_meta_box('newsletters_themes', '<i class="fa fa-paint-brush fa-fw"></i> ' . __('Template', 'wp-mailinglist') . $Html -> help(__('Choose the template that you want to use for this newsletter. The content filled into the TinyMCE editor to the left will be inserted into the template where it has the [newsletters_main_content] tag inside it.', 'wp-mailinglist')), array($Metabox, 'newsletters_theme'), $custompostslug, 'side', 'core');

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $history_id = (int) sanitize_text_field(wp_unslash($_GET['id']));
            if (!empty($method) && $method == "history" && !empty($history_id)) {
                if ($contentareas = $this -> Content() -> find_all(array('history_id' => $history_id), false, array('number', "ASC"))) {
                    foreach ($contentareas as $contentarea) {
                        add_meta_box('contentareabox' . $contentarea -> number, '<i class="fa fa-file fa-fw"></i> ' . sprintf(__('Content Area %s', 'wp-mailinglist'), $contentarea -> number), array($Metabox, 'send_contentarea'), $custompostslug, 'normal', 'high', array('contentarea' => $contentarea));
                    }
                }
            }

            $multimime = $this -> get_option('multimime');
            if (!empty($multimime) && $multimime == "Y") {
                add_meta_box('newsletters_text', '<i class="fa fa-font fa-fw"></i> ' . __('TEXT Version', 'wp-mailinglist') . $Html -> help(__('Specify the TEXT version of multipart emails which will be seen by users who prefer text or have HTML turned off.', 'wp-mailinglist')), array($Metabox, 'newsletters_text'), $custompostslug, 'normal', 'core');
            }

            $createpreview = $this -> get_option('createpreview');
            if (!empty($createpreview) && $createpreview == "Y") {
                add_meta_box('newsletters_preview', '<i class="fa fa-eye fa-fw"></i> ' . __('Live Preview', 'wp-mailinglist') . $Html -> help(__('The preview section below shows a preview of what the newsletter will look like with the template, content and other elements. It updates automatically every few seconds or you can click the "Update Preview" button to manually update it. Please note that this is a browser preview and some email/webmail clients render emails differently than browsers.', 'wp-mailinglist')), array($Metabox, 'newsletters_preview'), $custompostslug, 'normal', 'core');
            }

            if (apply_filters('newsletters_admin_createnewsletter_emailattachments_show', true)) { add_meta_box('newsletters_attachment', '<i class="fa fa-paperclip fa-fw"></i> ' . __('Email Attachment', 'wp-mailinglist') . $Html -> help(__('Attach files to your newsletter. It is possible to attach multiple files of any filetype and size to newsletters which will be sent to the subscribers. Try to keep attachments small to prevent emails from becoming too large.', 'wp-mailinglist')), array($Metabox, 'newsletters_attachment'), $custompostslug, 'normal', 'core'); }
            if (apply_filters('newsletters_admin_createnewsletter_variables_show', true)) { add_meta_box('newsletters_variables', '<i class="fa fa-terminal fa-fw"></i> ' . __('Variables & Custom Fields', 'wp-mailinglist') . $Html -> help(__('These are shortcodes which can be used inside of the newsletter template or content where needed and as many of them as needed. The shortcodes will be replaced with their respective values for each subscriber individually. You can use this to personalize your newsletters to your subscribers easily.', 'wp-mailinglist')), array($Metabox, 'send_setvariables'), $custompostslug, 'normal', 'core'); }

            //if (apply_filters('newsletters_admin_createnewsletter_publishpost_show', true)) { add_meta_box('publishdiv', '<i class="fa fa-file fa-fw"></i> ' . __('Publish as Post', 'wp-mailinglist') . $Html -> help(__('When you queue/send this newsletter you can publish it as a post on your website. Configure these settings to publish this newsletter as a post according to your needs.', 'wp-mailinglist')), array($Metabox, 'send_publish'), $custompostslug, 'normal', 'core'); }

            do_action('newsletters_admin_createnewsletter_metaboxes', $custompostslug);

            do_action('do_meta_boxes', $custompostslug, 'side', $post);
            do_action('do_meta_boxes', $custompostslug, 'normal', $post);
            do_action('do_meta_boxes', $custompostslug, 'advanced', $post);
        }

        function admin_head_send() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            $createspamscore = $this -> get_option('createspamscore');
            if (!empty($createspamscore) && $createspamscore == "Y") {
                add_meta_box('spamscorediv', '<i class="fa fa-meh-o fa-fw"></i> ' . __('Spam Score', 'wp-mailinglist'), array($Metabox, 'send_spamscore'), $page, 'side', 'core');
            }

            add_meta_box('mailinglistsdiv', '<i class="fa fa-users fa-fw"></i> ' . __('Subscribers', 'wp-mailinglist') . $Html ->  help(__('Tick/check the group(s) or list(s) that you want to send/queue this newsletter to. The newsletter will only be sent to active subscriptions in the chosen list(s).', 'wp-mailinglist')), array($Metabox, 'send_mailinglists'), $page, 'side', 'core');
            add_meta_box('insertdiv', '<i class="fa fa-level-down fa-fw"></i> ' . __('Insert into Newsletter', 'wp-mailinglist') . $Html -> help(__('Use this box to insert various things into your newsletter such as posts, snippets, custom fields and post thumbnails.', 'wp-mailinglist')), array($Metabox, 'send_insert'), $page, 'side', 'core');
            add_meta_box('themesdiv', '<i class="fa fa-paint-brush fa-fw"></i> ' . __('Template', 'wp-mailinglist') . $Html -> help(__('Choose the template that you want to use for this newsletter. The content filled into the TinyMCE editor to the left will be inserted into the template where it has the [newsletters_main_content] tag inside it.', 'wp-mailinglist')), array($Metabox, 'send_theme'), $page, 'side', 'core');
            add_meta_box('authordiv', '<i class="fa fa-user fa-fw"></i> ' . __('Author/User', 'wp-mailinglist'), array($Metabox, 'send_author'), $page, 'side', 'core');
            add_meta_box('submitdiv', '<i class="fa fa-paper-plane fa-fw"></i> ' . __('Send Newsletter', 'wp-mailinglist'), array($Metabox, 'send_submit'), $page, 'side', 'core');

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $history_id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
            if (!empty($method) && $method == "history" && !empty($history_id)) {
                if ($contentareas = $this -> Content() -> find_all(array('history_id' => $history_id), false, array('number', "ASC"))) {
                    foreach ($contentareas as $contentarea) {
                        add_meta_box('contentareabox' . $contentarea -> number, '<i class="fa fa-file fa-fw"></i> ' . sprintf(__('Content Area %s', 'wp-mailinglist'), $contentarea -> number), array($Metabox, 'send_contentarea'), $page, 'normal', 'high', array('contentarea' => $contentarea));
                    }
                }
            }

            $multimime = $this -> get_option('multimime');
            if (!empty($multimime) && $multimime == "Y") {
                add_meta_box('multimimediv', '<i class="fa fa-font fa-fw"></i> ' . __('TEXT Version', 'wp-mailinglist') . $Html -> help(__('Specify the TEXT version of multipart emails which will be seen by users who prefer text or have HTML turned off.', 'wp-mailinglist')), array($Metabox, 'send_multimime'), $page, 'normal', 'core');
            }

            $createpreview = $this -> get_option('createpreview');
            if (!empty($createpreview) && $createpreview == "Y") {
                add_meta_box('previewdiv', '<i class="fa fa-eye fa-fw"></i> ' . __('Live Preview', 'wp-mailinglist') . $Html -> help(__('The preview section below shows a preview of what the newsletter will look like with the template, content and other elements. It updates automatically every few seconds or you can click the "Update Preview" button to manually update it. Please note that this is a browser preview and some email/webmail clients render emails differently than browsers.', 'wp-mailinglist')), array($Metabox, 'send_preview'), $page, 'normal', 'core');
            }

            if (apply_filters('newsletters_admin_createnewsletter_variables_show', true)) { add_meta_box('setvariablesdiv', '<i class="fa fa-terminal fa-fw"></i> ' . __('Variables & Custom Fields', 'wp-mailinglist') . $Html -> help(__('These are shortcodes which can be used inside of the newsletter template or content where needed and as many of them as needed. The shortcodes will be replaced with their respective values for each subscriber individually. You can use this to personalize your newsletters to your subscribers easily.', 'wp-mailinglist')), array($Metabox, 'send_setvariables'), $page, 'normal', 'core'); }
            if (apply_filters('newsletters_admin_createnewsletter_emailattachments_show', true)) { add_meta_box('attachmentdiv', '<i class="fa fa-paperclip fa-fw"></i> ' . __('Email Attachment', 'wp-mailinglist') . $Html -> help(__('Attach files to your newsletter. It is possible to attach multiple files of any filetype and size to newsletters which will be sent to the subscribers. Try to keep attachments small to prevent emails from becoming too large.', 'wp-mailinglist')), array($Metabox, 'send_attachment'), $page, 'normal', 'core'); }
            if (apply_filters('newsletters_admin_createnewsletter_publishpost_show', true)) { add_meta_box('publishdiv', '<i class="fa fa-file fa-fw"></i> ' . __('Publish as Post', 'wp-mailinglist') . $Html -> help(__('When you queue/send this newsletter you can publish it as a post on your website. Configure these settings to publish this newsletter as a post according to your needs.', 'wp-mailinglist')), array($Metabox, 'send_publish'), $page, 'normal', 'core'); }

            do_action('newsletters_admin_createnewsletter_metaboxes', $page);

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_templates_save() {
            global $Metabox, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Save Snippet', 'wp-mailinglist'), array($Metabox, 'templates_submit'), $page, 'side', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_themes_save() {
            global $Metabox, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Save Template', 'wp-mailinglist'), array($Metabox, 'themes_submit'), $page, 'side', 'core');
            add_meta_box('generaldiv', __('General Settings', 'wp-mailinglist'), array($Metabox, 'themes_general'), $page, 'normal', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_settings() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Configuration Settings', 'wp-mailinglist'), array($Metabox, 'settings_submit'), $page, 'side', 'core');
            add_meta_box('generaldiv', __('General Mail Settings', 'wp-mailinglist') . $Html -> help(__('These are general settings related to the sending of emails such as your email server. You can also turn on/off other features here such as read tracking, click tracking, and more.', 'wp-mailinglist')), array($Metabox, 'settings_general'), $page, 'normal', 'core');
            add_meta_box('sendingdiv', __('Sending Settings', 'wp-mailinglist'), array($Metabox, 'settings_sending'), $page, 'normal', 'core');
            add_meta_box('optindiv', __('Default Subscription Form Settings', 'wp-mailinglist') . $Html -> help(__('Global subscribe form settings for hardcoded and shortcode (post/page) subscribe forms.', 'wp-mailinglist')), array($Metabox, 'settings_optin'), $page, 'normal', 'core');
            add_meta_box('subscriptionsdiv', __('Paid Subscriptions', 'wp-mailinglist'), array($Metabox, 'settings_subscriptions'), $page, 'normal', 'core');
            add_meta_box('ppdiv', __('PayPal Configuration', 'wp-mailinglist') . $Html -> help(__('If you are using PayPal as your payment method for paid subscriptions you can configure it here.', 'wp-mailinglist')), array($Metabox, 'settings_pp'), $page, 'normal', 'core');
            add_meta_box('tcdiv', __('2Checkout Configuration', 'wp-mailinglist') . $Html -> help(__('Configure 2Checkout (2CO) here if you are using it as your payment method for paid subscriptions.', 'wp-mailinglist')), array($Metabox, 'settings_tc'), $page, 'normal', 'core');
            add_meta_box('publishingdiv', __('Posts Configuration', 'wp-mailinglist') . $Html -> help(__('These are settings related to posts in general. For publishing newsletters as posts and also inserting posts into newsletters.', 'wp-mailinglist')), array($Metabox, 'settings_publishing'), $page, 'normal', 'core');
            add_meta_box('schedulingdiv', __('Email Scheduling', 'wp-mailinglist') . $Html -> help(__('The purpose of email scheduling is to allow you to send thousands of emails in a load distributed way. Please take note that you cannot expect your server/hosting to send hundreds/thousands of emails all simultaneously, so this is where email scheduling helps you.', 'wp-mailinglist')), array($Metabox, 'settings_scheduling'), $page, 'normal', 'core');
            add_meta_box('bouncediv', __('Bounce Configuration', 'wp-mailinglist'), array($Metabox, 'settings_bounce'), $page, 'normal', 'core');
            add_meta_box('emailsdiv', __('History & Emails Configuration', 'wp-mailinglist'), array($Metabox, 'settings_emails'), $page, 'normal', 'core');
            add_meta_box('latestposts', __('Latest Posts Subscriptions', 'wp-mailinglist'), array($Metabox, 'settings_latestposts'), $page, 'normal', 'core');
            add_meta_box('customcss', __('Theme, Scripts & Custom CSS', 'wp-mailinglist'), array($Metabox, 'settings_customcss'), $page, 'normal', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        // Newsletters > Configuration > System Emails
        function admin_head_settings_templates() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Configuration Settings', 'wp-mailinglist'), array($Metabox, 'settings_submit'), $page, 'side', 'core');
            add_meta_box('sendasdiv', __('Send as Newsletter', 'wp-mailinglist') . $Html -> help(__('The posts template used when sending a post/page as a newsletter.', 'wp-mailinglist')), array($Metabox, 'settings_templates_sendas'), $page, 'normal', 'core');
            add_meta_box('postsdiv', __('Posts', 'wp-mailinglist') . $Html -> help(__('The posts template used when using the [newsletters_post...] or [newsletters_posts...] shorcodes in your newsletters.', 'wp-mailinglist')), array($Metabox, 'settings_templates_posts'), $page, 'normal', 'core');
            add_meta_box('latestpostsdiv', __('Latest Posts', 'wp-mailinglist') . $Html -> help(__('The posts template used for the "Latest Posts Subscriptions" feature which automatically sends out new posts.', 'wp-mailinglist')), array($Metabox, 'settings_templates_latestposts'), $page, 'normal', 'core');
            add_meta_box('confirmdiv', __('Confirmation Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to new subscribers to confirm their subscription.', 'wp-mailinglist')), array($Metabox, 'settings_templates_confirm'), $page, 'normal', 'core');
            add_meta_box('bouncediv', __('Bounce Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the administrator when an email to a subscriber bounces.', 'wp-mailinglist')), array($Metabox, 'settings_templates_bounce'), $page, 'normal', 'core');
            add_meta_box('unsubscribediv', __('Unsubscribe Admin Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the administrator when a subscriber unsubscribes.', 'wp-mailinglist')), array($Metabox, 'settings_templates_unsubscribe'), $page, 'normal', 'core');
            add_meta_box('unsubscribeuserdiv', __('Unsubscribe User Email', 'wp-mailinglist') . $Html -> help(__('Email message to the subscriber to confirm their unsubscribe.', 'wp-mailinglist')), array($Metabox, 'settings_templates_unsubscribeuser'), $page, 'normal', 'core');
            add_meta_box('expirediv', __('Expiration Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the subscriber when a paid subscription expires.', 'wp-mailinglist')), array($Metabox, 'settings_templates_expire'), $page, 'normal', 'core');
            add_meta_box('orderdiv', __('Paid Subscription Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the administrator for a new paid subscription order payment.', 'wp-mailinglist')), array($Metabox, 'settings_templates_order'), $page, 'normal', 'core');
            add_meta_box('schedulediv', __('Cron Schedule Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the administrator when the email cron fires.', 'wp-mailinglist')), array($Metabox, 'settings_templates_schedule'), $page, 'normal', 'core');
            add_meta_box('subscribediv', __('New Subscription Email', 'wp-mailinglist') . $Html -> help(__('Email message sent to the administrator when a new user subscribes.', 'wp-mailinglist')), array($Metabox, 'settings_templates_subscribe'), $page, 'normal', 'core');
            add_meta_box('authenticatediv', __('Authentication Email', 'wp-mailinglist'), array($Metabox, 'settings_templates_authenticate'), $page, 'normal', 'core');

            do_action('newsletters_admin_settingstemplates_metaboxes', $page);

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_settings_subscribers() {
            global $Html, $Metabox, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Configuration Settings', 'wp-mailinglist'), array($Metabox, 'settings_submit'), $page, 'side', 'core');
            add_meta_box('importdiv', __('Import Settings', 'wp-mailinglist'), array($Metabox, 'settings_import'), $page, 'normal', 'core');
            add_meta_box('managementdiv', __('Subscriber Management Section', 'wp-mailinglist') . $Html -> help(__('This section lets you control the way the subscriber management section behaves. It is the "Manage Subscriptions" page which is provided to subscribers where they unsubscribe, manage current subscriptions, update their profile, etc.', 'wp-mailinglist')), array($Metabox, 'settings_management'), $page, 'normal', 'core');
            add_meta_box('subscribersdiv', __('Subscription Behaviour', 'wp-mailinglist') . $Html -> help(__('Control the way the plugin behaves when someone subscribes to your site. Certain things can happen upon subscription based on these settings.', 'wp-mailinglist')), array($Metabox, 'settings_subscribers'), $page, 'normal', 'core');
            add_meta_box('unsubscribediv', __('Unsubscribe Behaviour', 'wp-mailinglist') . $Html -> help(__('Control the unsubscribe procedure. Certain things can happen when a subscriber unsubscribes from your site based on these settings.', 'wp-mailinglist')), array($Metabox, 'settings_unsubscribe'), $page, 'normal', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_settings_extensions_settings() {
            global $Metabox, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Extensions Settings', 'wp-mailinglist'), array($Metabox, 'extensions_settings_submit'), $page, 'side', 'core');
            do_action($this -> pre . '_metaboxes_extensions_settings', $page);
            do_action('newsletters_metaboxes_extensions_settings', $page);

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function admin_head_settings_system() {
            global $Metabox, $Html, $post;

            $screen = get_current_screen();
            $page = $screen -> id;

            add_meta_box('submitdiv', __('Configuration Settings', 'wp-mailinglist'), array($Metabox, 'settings_submit'), $page, 'side', 'core');
            add_meta_box('settingsdiv', __('General', 'wp-mailinglist'), array($Metabox, 'settings_system_general'), $page, 'normal', 'core');
            add_meta_box('captchadiv', __('Captcha Settings', 'wp-mailinglist') . $Html -> help(__('Use these settings for the captcha security image used in the subscribe forms.', 'wp-mailinglist')), array($Metabox, 'settings_system_captcha'), $page, 'normal', 'core');
            add_meta_box('wprelateddiv', __('WordPress Related', 'wp-mailinglist') . $Html -> help(__('These are settings related to WordPress directly and how the plugin interacts with it.', 'wp-mailinglist')), array($Metabox, 'settings_wprelated'), $page, 'normal', 'core');
            add_meta_box('permissionsdiv', __('Permissions', 'wp-mailinglist'), array($Metabox, 'settings_permissions'), $page, 'normal', 'core');
            add_meta_box('autoimportusersdiv', __('Auto Import Users', 'wp-mailinglist') . $Html -> help(__('Use these settings to configure the way that WordPress users are automatically imported as subscribers into the system.', 'wp-mailinglist')), array($Metabox, 'settings_importusers'), $page, 'normal', 'core');
            add_meta_box('commentform', __('WordPress Comment- and Registration Form', 'wp-mailinglist') . $Html -> help(__('Put a subscribe checkbox on your WordPress registration and/or comment forms to capture subscribers.', 'wp-mailinglist')), array($Metabox, 'settings_commentform'), $page, 'normal', 'core');

            do_action('do_meta_boxes', $page, 'side', $post);
            do_action('do_meta_boxes', $page, 'normal', $post);
            do_action('do_meta_boxes', $page, 'advanced', $post);
        }

        function hardcoded($list_id = "select", $lists = null, $atts = array(), $form_id = null) {
            global $Html, $Subscriber;

            if (is_feed()) return;

            if (!empty($form_id)) {
                if ($form = $this -> Subscribeform() -> find(array('id' => $form_id))) {
                    $output = $this -> render('subscribe', array('form' => $form, 'errors' => $Subscriber -> errors), false, 'default');
                }
            } else {
                $atts['list'] = $list_id;

                if ($rand_transient = get_transient('newsletters_shortcode_subscribe_rand_' . $post_id)) {
                    $rand = $rand_transient;
                } else {
                    $rand = rand(999, 9999);
                    set_transient('newsletters_shortcode_subscribe_rand_' . $post_id, $rand, HOUR_IN_SECONDS);
                }

                $number = 'embed' . $rand;
                $widget_id = 'newsletters-' . $number;
                $instance = $this -> widget_instance($number, $atts);

                $defaults = array(
                    'list' 				=> 	$list_id,
                    'id' 				=> 	false,
                    'lists'				=>	false,
                    'ajax'				=>	$instance['ajax'],
                    'button'			=>	$instance['button'],
                    'captcha'			=>	$instance['captcha'],
                    'acknowledgement'	=>	$instance['acknowledgement'],
                );

                $r = shortcode_atts($defaults, $atts);
                extract($r);

                $action = ($this -> language_do()) ? $this -> language_converturl(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $instance['language']) : sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
                $action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;
                $errors = $Subscriber -> errors;

                $output = "";
                $output .= '<div id="' . $widget_id . '" class="newsletters ' . $this -> pre . ' widget_newsletters">';
                $output .= '<div id="' . $widget_id . '-wrapper">';
                $output .= $this -> render('widget', array('action' => $action, 'errors' => $errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), false, 'default');
                $output .= '</div>';
                $output .= '</div>';
            }

            // phpcs:ignore
            echo $output;
        }

        function widget_register() {
            register_widget('Newsletters_Widget');
        }

        function admin_submitserial() {
            $success = false;

            if (!empty($_POST)) {
                check_admin_referer($this -> sections -> submitserial);

                if (empty($_REQUEST['serial'])) { $errors[] = __('Please fill in a serial key.', 'wp-mailinglist'); }
                else {
                    $this -> update_option('serialkey', sanitize_text_field(wp_unslash($_REQUEST['serial'])));	//update the DB option
                    $this -> delete_all_cache('all');

                    if (!$this -> ci_serial_valid()) { $errors[] = __('Serial key is invalid, please try again.', 'wp-mailinglist'); }
                    else {
                        delete_transient('newsletters_update_info');
                        $success = true;
                        $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> welcome));
                    }
                }
            }

            $this -> render('settings-submitserial', array('success' => $success, 'errors' => $errors), true, 'admin');
        }

        function admin_gdpr() {
            $this -> render('gdpr', false, true, 'admin');
        }

        function admin_index() {
            $this -> render('index', false, true, 'admin');
        }

        
        function admin_forms() {
            global $wpdb, $Db, $Subscriber, $SubscribersList;
            $errors = array();
            $form_id = (!empty($_GET['id'])) ? esc_html($_GET['id']) : false;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'save'								:

                    if (!empty($_POST)) {

                        check_admin_referer($this -> sections -> forms . '_save');
                        if ($this -> Subscribeform() -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                            $message = __('Form has been saved', 'wp-mailinglist');
                            if (!empty($_POST['continueediting'])) {
                                if(!empty($this->Subscribeform()->insertid)) {
                                    $this->redirect(admin_url('admin.php?page=' . $this->sections->forms . '&method=save&id=' . $this->Subscribeform()->insertid . '&continueediting=1'), 'message', $message);
                                }
                                else if (!empty($_POST['_wp_http_referer'])) {
                                    $this->redirect($_POST['_wp_http_referer'] . '&continueediting=1', 'message', $message);
                                }
                                else {
                                    $this->redirect(admin_url('admin.php?page=' . $this->sections->forms ), 'message', $message);

                                }
                            } else {
                                $this -> redirect($this -> url, 'message', $message);
                            }
                        } else {
                            $errors = $this -> Subscribeform() -> errors;
                            $form = $this -> init_class($this -> Subscribeform() -> model, $_POST);
                            $this -> render_error(__('Form could not be saved', 'wp-mailinglist'));
                        }
                    } else {
                        if (!empty($form_id)) {
                            $form = $this -> Subscribeform() -> find(array('id' => $form_id));
                        }
                    }

                    $this -> render('forms' . DS . 'save', array('form' => (isset($form) ? $form : array()), 'errors' => $errors), true, 'admin');
                    break;
                case 'delete'							:
                    if (!empty($form_id)) {
                        if ($this -> Subscribeform() -> delete($form_id)) {
                            $msg_type = 'message';
                            $message = __('Form was deleted', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Form could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No form was specified');
                    }

                    $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> forms), $msg_type, $message);
                    break;
                case 'settings'							:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> forms . '_settings');
                        if ($this -> Subscribeform() -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                            $message = __('Form has been saved', 'wp-mailinglist');
                            $this -> render_message($message);
                        } else {
                            $errors = $this -> Subscribeform() -> errors;
                            $this -> render_error(__('Form could not be saved', 'wp-mailinglist'));
                        }

                        $form = $this -> Subscribeform() -> data;
                    } else {
                        if (!empty($form_id)) {
                            $form = $this -> Subscribeform() -> find(array('id' => $form_id));
                        }
                    }

                    $this -> render('forms' . DS . 'settings', array('form' => $form), true, 'admin');

                    break;
                case 'preview'							:

                    $form = $this -> Subscribeform() -> find(array('id' => $form_id));
                    $this -> render('forms' . DS . 'preview', array('form' => $form), true, 'admin');

                    break;
                case 'codes'							:

                    $form = $this -> Subscribeform() -> find(array('id' => $form_id));
                    $this -> render('forms' . DS . 'codes', array('form' => $form), true, 'admin');

                    break;
                case 'subscriptions'					:
                    $perpage = (!empty($_COOKIE[$this -> pre . 'subscribersperpage'])) ? (int) $_COOKIE[$this -> pre . 'subscribersperpage'] : 15;
                    $sub = $this -> sections -> forms . '&method=subscriptions&id=' . $form_id;
                    $subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
                    $conditions = array($subscriberslists_table . '.form_id' => $form_id);
                    $searchterm = false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);
                    $data = $this -> paginate($SubscribersList -> model, false, $sub, $conditions, $searchterm, $perpage, $order);
                    $subscribers = $data[$SubscribersList -> model];
                    $form = $this -> Subscribeform() -> find(array('id' => $form_id));
                    $this -> render('forms' . DS . 'subscriptions', array('form' => $form, 'subscribers' => $subscribers, 'paginate' => $data['Paginate']), true, 'admin');
                    break;
                case 'mass'								:
                    check_admin_referer($this -> sections -> forms . '_mass');
                    if (!empty($_POST['forms'])) {
                        if (!empty($_POST['action'])) {
                            $forms = array_map('sanitize_text_field', $_POST['forms']);

                            switch ($_POST['action']) {
                                case 'delete'			:
                                    foreach ($forms as $form_id) {
                                        $this -> Subscribeform() -> delete($form_id);
                                    }

                                    $msg_type = "message";
                                    $message = 18;
                                    break;
                            }
                        } else {
                            $msg_type = "error";
                            $message = 17;
                        }
                    } else {
                        $msg_type = "error";
                        $message = 16;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                default 								:
                    $perpage = (isset($_COOKIE[$this -> pre . 'formsperpage'])) ? sanitize_text_field(wp_unslash($_COOKIE[$this -> pre . 'formsperpage'])) : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> forms . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    if (!empty($_GET['showall'])) {
                        $forms = $this -> Subscribeform() -> find_all($conditions, "*", $order, false, true);
                        $data[$this -> Subscribeform() -> model] = $forms;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Subscribeform() -> model, false, $this -> sections -> forms, $conditions, $searchterm, $perpage, $order);
                    }
                    if(!empty($data)) {
                    $this -> render('forms' . DS . 'index', array('forms' => $data[$this -> Subscribeform() -> model], 'paginate' => $data['Paginate']), true, 'admin');

                    }
                    else {
                        $this -> render('forms' . DS . 'index', array('forms' => array(), 'paginate' => array()), true, 'admin');

                    }
                    break;
            }
        }


        function admin_send() {
            global $wpdb, $Unsubscribe, $Db, $Html, $Theme, $HistoriesAttachment, $Mailinglist, $Subscriber, $Field, $SubscribersList;
            $user_id = get_current_user_id();
            $post_id = false;
            $this -> remove_server_limits();
            $sentmailscount = 0;

            /* Themes */
            $Db -> model = $Theme -> model;
            $themes = $Db -> find_all(false, false, array('title', "ASC"));

            // Do the post publishing
            if (!empty($_POST['post_id'])) {
                $post_id = sanitize_text_field(wp_unslash($_POST['post_id']));
            } elseif (!empty($_GET['id'])) {
                $history_id = sanitize_text_field(wp_unslash($_GET['id']));
                if ($history_post_id = $this -> History() -> field('post_id', array('id' => $history_id))) {
                    $post_id = $history_post_id;
                }
            }

            if (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y") {
                $status = (!empty($_POST['post_status'])) ? sanitize_text_field(wp_unslash($_POST['post_status'])) : 'draft';
                $slug = (!empty($_POST['post_slug'])) ? sanitize_text_field(wp_unslash($_POST['post_slug'])) : $Html -> sanitize(sanitize_text_field(wp_unslash($_POST['subject'])), '-');
                    
                $post = array(
                    'ID'					=>	$post_id,
                    'post_title'			=>	wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject']))),
                    'post_content'			=>	$this -> strip_set_variables(wp_kses_post($_POST['content'])),
                    'post_status'			=>	$status,
                    'post_name'				=>	$slug,
                    'post_category'			=>	map_deep(wp_unslash($_POST['cat']), 'sanitize_text_field'),
                    'post_type'				=>	((empty($_POST['newsletters_post_type'])) ? 'post' : sanitize_text_field(wp_unslash($_POST['newsletters_post_type']))),
                    'post_author'			=>	(empty($_POST['post_author'])) ? $user_id : sanitize_text_field(wp_unslash($_POST['post_author'])),
                );

                stripslashes_deep($post);

                $currstatus = $this -> get_option('sendonpublish');
                $this -> update_option('sendonpublish', 'N');
                $_POST['sendtolist'] = "N";
                $post_id = wp_insert_post($post);
                $this -> update_option('sendonpublish', $currstatus);
            }

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'snippet'		:
                case 'template'		:
                    $mailinglists = $Mailinglist -> get_all('*', true);
                    $templates = $this -> Template() -> get_all();

                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if ($template = $this -> Template() -> get($id)) {
                        $this -> render_message(__('Email template has been loaded into the subject field and editor below.', 'wp-mailinglist'));
                        $_POST = array('subject' => $template -> title, 'inctemplate' => $template -> id, 'content' => $template -> content);
                        $this -> render('send', array('mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates), true, 'admin');
                    } else {
                        $message = __('Email template could not be loaded, please try again.', 'wp-mailinglist');
                        $this -> redirect($this -> referer, "error", $message);
                    }
                    break;
                case 'history'		:
                    $mailinglists = $Mailinglist -> get_all('*', true);
                    $templates = $this -> Template() -> get_all();
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));

                    if ($history = $this -> History() -> get($id)) {
                        $_POST = array(
                            'ishistory'			=>	$history -> id,
                            'p_id'				=>	$history -> p_id,
                            'user_id'			=>	$history -> user_id,
                            'from'				=>	$history -> from,
                            'fromname'			=>	$history -> fromname,
                            'subject'			=>	$history -> subject,
                            'content'			=>	$history -> message,
                            'groups'			=>	$history -> groups,
                            'roles'				=>	maybe_unserialize($history -> roles),
                            'mailinglists'		=>	$history -> mailinglists,
                            'theme_id'			=>	$history -> theme_id,
                            'post_id'			=>	$post_id,
                            'condquery'			=>	maybe_unserialize($history -> condquery),
                            'conditions'		=>	maybe_unserialize($history -> conditions),
                            'conditionsscope'	=>	$history -> conditionsscope,
                            'daterange'			=>	$history -> daterange,
                            'countries'			=>	$history -> countries,
                            'selectedcountries'	=>	maybe_unserialize($history -> selectedcountries),
                            'daterangefrom'		=>	$history -> daterangefrom,
                            'daterangeto'		=>	$history -> daterangeto,
                            'fields'			=>	maybe_unserialize($history -> conditions),
                            'attachments'		=>	$history -> attachments,
                            'senddate'			=>	$history -> senddate,
                            'customtexton'		=>	((!empty($history -> text)) ? true : false),
                            'customtext'		=>	$history -> text,
                            'spamscore'			=>	$history -> spamscore,
                            'format'			=>	$history -> format,
                            'status'			=>	$history -> status,
                            'builderon'         =>  $history -> builderon

                        );

                        if (!empty($_POST['condquery']) && !empty($_POST['conditions']) && !empty($_POST['conditionsscope'])) {
                            $_POST['dofieldsconditions'] = 1;
                        }

                        if (!empty($history -> recurring) && $history -> recurring == "Y") {
                            $_POST['sendrecurring'] = "Y";
                            $_POST['sendrecurringvalue'] = $history -> recurringvalue;
                            $_POST['sendrecurringinterval'] = $history -> recurringinterval;
                            $_POST['sendrecurringdate'] = $history -> recurringdate;
                            $_POST['sendrecurringlimit'] = $history -> recurringlimit;
                            $_POST['sendrecurringsent'] = $history -> recurringsent;
                        }

                        if (!empty($post_id)) {
                            if ($post = get_post($post_id)) {
                                $_POST['cat'] = wp_get_post_categories($post_id);
                                $_POST['post_status'] = $post -> post_status;
                                $_POST['newsletters_post_type'] = $post -> post_type;
                                $_POST['post_slug'] = $post -> post_name;
                            }
                        }

                        $this -> render('send', array('history' => $history, 'mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates), true, 'admin');
                    } else {
                        $message = __('Sent/draft email could not be loaded, please try again.', 'wp-mailinglist');
                        $this -> redirect('?page=' . $this -> sections -> history, "error", $message);
                    }
                    break;
                default				:
                    global $errors;
                    $errors = array();
                    if (!empty($_POST['newsletter'])) {
                        if (!empty($_POST['groups'])) {
                            global $Db, $Mailinglist;

                            foreach (map_deep(wp_unslash($_POST['groups']), 'sanitize_text_field') as $group_id) {
                                $Db -> model = $Mailinglist -> model;

                                if ($mailinglists = $Db -> find_all(array('group_id' => sanitize_text_field($group_id)), array('id'))) {
                                    foreach ($mailinglists as $mailinglist) {
                                        $_POST['mailinglists'][] = $mailinglist -> id;
                                    }
                                }
                            }
                        }

                        $mailinglists = false;
                        $mailinglist = false;

                        global $errors;
                        $errors = array();

                        if (empty($_POST['subject'])) { $errors['subject'] = __('Please fill in an email subject', 'wp-mailinglist'); }
                        //if (empty($_POST['content'])) { $errors['content'] = __('Please fill in a newsletter message', 'wp-mailinglist'); }

                        if (empty($_POST['preview']) && empty($_POST['draft'])) {
                            if ((empty($_POST['mailinglists']) || !is_array($_POST['mailinglists'])) && empty($_POST['roles'])) {
                                $errors['mailinglists'] = __('Please select mailing list/s', 'wp-mailinglist');
                            }
                        }
                        $newattachments = array();
                        if (!empty($_POST['sendattachment']) && $_POST['sendattachment'] == "1") {
                            $newattachments = array();

                            if (!empty($_POST['ishistory'])) {
                                if ($history = $this -> History() -> find(array('id' => sanitize_text_field(wp_unslash($_POST['ishistory']))))) {
                                    $newattachments = $history -> attachments;
                                }
                            }

                            $newfiles = map_deep(wp_unslash($_FILES['attachments']), 'sanitize_text_field');

                            if (!empty($newfiles)) {
                                $_FILES = array();

                                foreach ($newfiles['name'] as $fkey => $fval) {
                                    // phpcs:ignore
                                    $_FILES['attachments'][$fkey] = array(
                                        'name'						=>	$fval,
                                        'type'						=>	$newfiles['type'][$fkey],
                                        'tmp_name'					=>	$newfiles['tmp_name'][$fkey],
                                        'error'						=>	$newfiles['error'][$fkey],
                                        'size'						=>	$newfiles['size'][$fkey],
                                    );

                                    if (!function_exists('wp_handle_upload')) {
                                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                                    }

                                    // phpcs:ignore
                                    $uploadedfile = $_FILES['attachments'][$fkey];
                                    $upload_overrides = array('test_form' => false);

                                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                                    if ($movefile && !isset($movefile['error'])) {
                                        $newattachments[] = array(
                                            'title'						=>	$fval,
                                            'filename'					=>	$movefile['file'],
                                            'subdir'					=>	$Html -> uploads_subdir(),
                                        );
                                    } else {
                                        //$movefile['error']
                                        $this -> render_error($movefile['error']);
                                    }
                                }
                            }
                        }

                        $_POST['attachments'] = apply_filters('newsletters_send_attachments', $newattachments);
                        $_POST['subject'] = wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject'])));
                        //$_POST['content'] = wp_kses_post(sanitize_text_field(wp_unslash($_POST['content'])));
                        $_POST['content'] = wp_kses_post($_POST['content']);
                  
                        //unset the fields if the "dofieldsconditions" was unchecked
                        if (empty($_POST['dofieldsconditions'])) {
                            unset($_POST['fields']);
                        }

                        if (empty($errors)) {

                            $defaulttexton = sanitize_text_field(isset($_POST['defaulttexton']) ? $_POST['defaulttexton'] : '');
                            if (!empty($defaulttexton) && !empty($_POST['customtext'])) {
                                $this -> update_option('defaulttexton', true);
                                $this -> update_option('defaulttextversion', sanitize_textarea_field(isset($_POST['customtext']) ? $_POST['customtext'] : ''));
                            } else {
                                $this -> delete_option('defaulttexton');
                                $this -> delete_option('defaulttextversion');
                            }

                            // Not a preview or a draft but actually sending/queuing
                            if (empty($_POST['preview']) && empty($_POST['draft'])) {
                                if (!empty($_POST)) {
                                    if (!empty($errors)) {
                                        $this -> render_error(__('Newsletter could not be scheduled/qeueued', 'wp-mailinglist'));
                                    } else {
                                        $history_data = array(
                                            'from'				=>	sanitize_text_field(isset($_POST['from']) ? $_POST['from'] : ''),
                                            'fromname'			=>	sanitize_text_field(isset($_POST['fromname']) ? $_POST['fromname'] : ''),
                                            'subject'			=>	wp_kses_post(wp_unslash(isset($_POST['subject']) ? $_POST['subject'] : '')),
                                            'message'			=>	wp_kses_post(wp_unslash(isset($_POST['content']) ? $_POST['content'] : '')),
                                            'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? sanitize_textarea_field($_POST['customtext']) : false),
                                            'theme_id'			=>	sanitize_text_field(isset($_POST['theme_id']) ? $_POST['theme_id'] : ''),
                                            'condquery'			=>	maybe_serialize(map_deep(isset($_POST['condquery']) ? $_POST['condquery'] : '', 'sanitize_text_field')),
                                            'conditions'		=>	maybe_serialize(map_deep(isset($_POST['fields']) ? $_POST['fields'] : '', 'sanitize_text_field')),
                                            'conditionsscope'	=>	sanitize_text_field(isset($_POST['fieldsconditionsscope']) ? $_POST['fieldsconditionsscope'] : ''),
                                            'daterange'			=>	sanitize_text_field(isset($_POST['daterange']) ? $_POST['daterange'] : ''),
                                            'daterangefrom'		=>	sanitize_text_field(isset($_POST['daterangefrom']) ? $_POST['daterangefrom'] : ''),
                                            'daterangeto'		=>	sanitize_text_field(isset($_POST['daterangeto']) ? $_POST['daterangeto'] : ''),
                                            'countries'			=> 	isset($_POST['countries']) ? (is_array($_POST['countries']) ? array_map('sanitize_text_field', wp_unslash($_POST['countries'])) : (!empty($_POST['countries']) ? [sanitize_text_field(wp_unslash($_POST['countries']))] : []) ) : '',
                                            'selectedcountries'	=>	maybe_serialize(map_deep(isset($_POST['selectedcountries']) ? $_POST['selectedcountries'] : '', 'sanitize_text_field')),
                                            'mailinglists'		=>	maybe_serialize(map_deep(isset($_POST['mailinglists']) ? $_POST['mailinglists'] : '', 'sanitize_text_field')),
                                            'groups'			=>	maybe_serialize(map_deep(isset($_POST['groups']) ? $_POST['groups'] : '', 'sanitize_text_field')),
                                            'roles'				=>	maybe_serialize(map_deep(isset($_POST['roles']) ? $_POST['roles'] : '', 'sanitize_text_field')),
                                            'post_id'			=>	$post_id,
                                            'user_id'			=>	sanitize_text_field(isset($_POST['user_id']) ? $_POST['user_id'] : ''),
                                            'newattachments'	=>	$newattachments,
                                            'senddate'			=>	sanitize_text_field(isset($_POST['senddate']) ? $_POST['senddate'] : ''),
                                            'scheduled'			=>	sanitize_text_field(isset($_POST['scheduled']) ? $_POST['scheduled'] : ''),
                                            'format'			=>	sanitize_text_field(isset($_POST['format']) ? $_POST['format'] : ''),
                                            'status'			=>	sanitize_text_field(isset($_POST['status']) ? $_POST['status'] : ''),
                                            'state'				=>	"sent",
                                            'builderon'         =>  $_POST['builderon'],
                                            'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                                            'using_grapeJS'     =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''

                                        );

                                        //is this a recurring newsletter?
                                        if (!empty($_POST['sendrecurring'])) {
                                            if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
                                                $history_data['recurring'] = "Y";
                                                $history_data['recurringvalue'] = sanitize_text_field($_POST['sendrecurringvalue']);
                                                $history_data['recurringinterval'] = sanitize_text_field($_POST['sendrecurringinterval']);
                                                $history_data['recurringsent'] = sanitize_text_field($_POST['recurringsent']);

                                                /*if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
													$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
												} else {*/
                                                $history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['sendrecurringdate']));
                                                //}

                                                $history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']));
                                            }
                                        }

                                        $increment = (!empty($_POST['sendtype']) && $_POST['sendtype'] == "schedule") ? 0 : 1;

                                        //is this an existing newsletter?
                                        if (!empty($_POST['ishistory'])) {
                                            $history_data['id'] = sanitize_text_field(wp_unslash($_POST['ishistory']));
                                            if ($history_curr = $this -> History() -> find(array('id' => $history_data['id']))) {
                                                $history_data['sent'] = ($history_curr -> sent + $increment);
                                            } else {
                                                $history_data['sent'] = $increment;
                                            }
                                        } else {
                                            $history_data['sent'] = $increment;
                                        }

                                        $this -> History() -> save($history_data, false);
                                        $history_id = $this -> History() -> insertid;
                                        if (!empty($_POST['contentarea'])) {

                                            foreach (map_deep(wp_unslash($_POST['contentarea']), 'sanitize_text_field') as $number => $content) {
                                                $content_data = array(
                                                    'number'			=>	$number,
                                                    'history_id'		=>	$history_id,
                                                    'content'			=>	$content,
                                                );

                                                $this -> Content() -> save($content_data, true);
                                            }
                                        }

                                        global $Db, $Field;

                                        if (empty($_POST['sendtype']) || $_POST['sendtype'] == "queue" || $_POST['sendtype'] == "send") {
                                            if ($this -> get_option('subscriptions') == "Y") {
                                                $SubscribersList -> check_expirations();
                                            }

                                            $subscriberids = array();
                                            $subscriberemails = array();

                                            if (!empty($_POST['mailinglists']) || !empty($_POST['roles'])) {
                                                $mailinglistscondition = false;
                                                if (!empty($_POST['mailinglists'])) {
                                                    $mailinglistscondition = "(";
                                                    $m = 1;

                                                    foreach (map_deep(wp_unslash($_POST['mailinglists']), 'sanitize_text_field') as $mailinglist_id) {
                                                        $mailinglistscondition .= $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($mailinglist_id) . "'";
                                                        if ($m < count($_POST['mailinglists'])) { $mailinglistscondition .= " OR "; }
                                                        $m++;
                                                    }

                                                    if (!empty($_POST['dofieldsconditions'])) {
                                                        $fields = array_filter(map_deep(wp_unslash($_POST['fields']), 'sanitize_text_field'));
                                                        $scopeall = (empty($_POST['fieldsconditionsscope']) || $_POST['fieldsconditionsscope'] == "all") ? true : false;
                                                        $condquery = sanitize_text_field(wp_unslash($_POST['condquery']));
                                                        $fieldsquery = $Subscriber -> get_segmented_query($fields, $scopeall, $condquery);
                                                    }

                                                    if (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") {
                                                        if (!empty($_POST['daterangefrom']) && !empty($_POST['daterangeto'])) {
                                                            $daterangefrom = date_i18n("Y-m-d", strtotime(sanitize_text_field(wp_unslash($_POST['daterangefrom']))));
                                                            $daterangeto = date_i18n("Y-m-d", strtotime(sanitize_text_field(wp_unslash($_POST['daterangeto']))));
                                                            $fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . esc_sql($daterangefrom) . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . esc_sql($daterangeto) . "')";
                                                        }
                                                    }

                                                    // Countries
                                                    if (!empty($_POST['countries'])) {
                                                        if (!empty($_POST['selectedcountries']) && is_array($_POST['selectedcountries'])) {
                                                            $countries = implode("', '", array_map('sanitize_text_field', $_POST['selectedcountries']));
                                                            $fieldsquery .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".country IN ('" . esc_sql($countries) . "'))";
                                                        }
                                                    }
                                                }

                                                /* Attachments */
                                                $history = $this -> History() -> find(array('id' => $history_id));

                                                $query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id, "
                                                    . $wpdb -> prefix . $Subscriber -> table . ".email FROM "
                                                    . $wpdb -> prefix . $Subscriber -> table . " LEFT JOIN "
                                                    . $wpdb -> prefix . $SubscribersList -> table . " ON "
                                                    . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id
												LEFT JOIN " . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id =
												" . $wpdb -> prefix . $Mailinglist -> table . ".id";

                                                if (!empty($mailinglistscondition)) {
                                                    $query .= " WHERE " . $mailinglistscondition . ")";
                                                }

                                                if (empty($_POST['status']) || $_POST['status'] == "active") {
                                                    $query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
                                                } elseif ($_POST['status'] == "inactive") {
                                                    $query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'N'";
                                                } elseif ($_POST['status'] == "all") {
                                                    $query .= "";
                                                }

                                                $query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval
												OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')"
                                                    . str_replace(" AND ()", "", $fieldsquery);

                                                $sentmailscount = 0;
                                                $sendingprogress_option = $this -> get_option('sendingprogress');
                                                $sendingprogress = (!empty($_POST['sendingprogress'])) ? "Y" : "N";
                                                $datasets = array();
                                                $d = 0;

                                                $queue_process_counter_1 = 0;
                                                $queue_process_counter_2 = 0;
                                                $queue_process_counter_3 = 0;

                                                $this -> qp_reset_data();
                                                $queue_process = 1;
                                               

                                                $roles_involved = !empty($_POST['roles']);
                                                $mailing_list_check = !empty($_POST['mailinglists']);
                                                $users = array();
                                                $subscribers = array();
                                                if ($roles_involved) {
                                                   
                                                    $exclude_users_query = "SELECT GROUP_CONCAT(`user_id`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `user_id` != '0'";
                                                    $exclude_users = $wpdb -> get_var($exclude_users_query);

                                                    foreach (map_deep(wp_unslash($_POST['roles']), 'sanitize_text_field') as $role_key) {
                                                        $users_arguments = array(
                                                            'blog_id'				=>	$GLOBALS['blog_id'],
                                                            'role'					=>	sanitize_text_field($role_key),
                                                            'exclude'				=>	$exclude_users,
                                                            'fields'				=>	array('ID', 'user_email', 'user_login'),
                                                        );

                                                        $role_users = get_users($users_arguments);
                                                        $users = array_merge($users, $role_users);
                                                    }

                                                    if (!empty($users)) {

                                                        $users = array_map("unserialize", array_unique(array_map("serialize", $users)));

                                                        foreach ($users as $user) {
                                                            $this -> remove_server_limits();

                                                            if ($sendingprogress == "N") {
                                                                $queue_process_data = array(
                                                                    'user_id'					=>	$user -> ID,
                                                                    'subject'					=>	wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject']))),
                                                                    'attachments'				=>	$newattachments,
                                                                    'post_id'					=>	$post_id,
                                                                    'history_id'				=>	$history_id,
                                                                    'theme_id'					=>	sanitize_text_field(wp_unslash($_POST['theme_id'])),
                                                                    'senddate'					=>	sanitize_text_field(wp_unslash($_POST['senddate']))
                                                                );

                                                                $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);

                                                                ${'queue_process_counter_' . $queue_process}++;
                                                                if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                                                    $this -> {'queue_process_' . $queue_process} -> save();
                                                                    $this -> {'queue_process_' . $queue_process} -> reset_data();
                                                                    ${'queue_process_counter_' . $queue_process} = 0;
                                                                }

                                                                $queue_process++;
                                                                if ($queue_process > 3) {
                                                                    $queue_process = 1;
                                                                }
                                                            } else {
                                                                $dataset = array(
                                                                    'id'				=>	false,
                                                                    'user_id'			=>	$user -> ID,
                                                                    'email'				=>	$user -> user_email,
                                                                    'mailinglist_id'	=>	false,
                                                                    'mailinglists'		=>	false,
                                                                    'format'			=> 	'html',
                                                                );

                                                                $datasets[$d] = $dataset;
                                                                $d++;
                                                            }

                                                            continue;
                                                        }
                                                    }
                                                }
                                                 elseif ($mailing_list_check ) {
                                                        $subscribers = $wpdb -> get_results($query);
                                                        //$subscribers = array_map("unserialize", array_unique(array_map("serialize", $subscribers)));
                                                        
                                                        if (!empty($subscribers)) {
                                                            foreach ($subscribers as $subscriber) {
                                                                $this -> remove_server_limits();

                                                                if ($sendingprogress == "N") {
                                                                    $queue_process_data = array(
                                                                        'subscriber_id'				=>	$subscriber -> id,
                                                                        'subject'					=>	wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject']))),
                                                                        'attachments'				=>	$newattachments,
                                                                        'post_id'					=>	$post_id,
                                                                        'history_id'				=>	$history_id,
                                                                        'theme_id'					=>	sanitize_text_field(wp_unslash($_POST['theme_id'])),
                                                                        'senddate'					=>	sanitize_text_field(wp_unslash($_POST['senddate'])),
                                                                    );

                                                                    $this -> {'queue_process_' . $queue_process} -> push_to_queue($queue_process_data);

                                                                    ${'queue_process_counter_' . $queue_process}++;
                                                                    if (${'queue_process_counter_' . $queue_process} >= $this -> {'queue_process_' . $queue_process} -> counter_reset) {
                                                                        $this -> {'queue_process_' . $queue_process} -> save();
                                                                        $this -> {'queue_process_' . $queue_process} -> reset_data();
                                                                        ${'queue_process_counter_' . $queue_process} = 0;
                                                                    }

                                                                    $queue_process++;
                                                                    if ($queue_process > 3) {
                                                                        $queue_process = 1;
                                                                    }
                                                                } else {
                                                                    $dataset = array(
                                                                        'id'				=>	$subscriber -> id,
                                                                        'email'				=>	$subscriber -> email,
                                                                        'mailinglist_id'	=>	$subscriber -> mailinglist_id,
                                                                        'mailinglists'		=>	$subscriber -> mailinglists,
                                                                        'format'			=> 	(empty($subscriber -> format) ? 'html' : $subscriber -> format),
                                                                    );

                                                                    $datasets[$d] = $dataset;
                                                                    $d++;
                                                                }

                                                                continue;
                                                            }
                                                        }

                                                    }




                                                if ($sendingprogress == "Y") {
                                                    $subject = wp_kses_post(wp_unslash($_POST['subject']));
                                                    $content = wp_kses_post(wp_unslash($_POST['content']));
                                                    $this -> render('send-post', array('subscribers' => $datasets, 'subject' => $subject, 'content' => $content, 'attachments' => $newattachments, 'post_id' => $post_id, 'history_id' => $history_id, 'theme_id' => sanitize_text_field($_POST['theme_id'])), true, 'admin');
                                                    $dontrendersend = true;
                                                } else {
                                                    $this -> qp_save();
                                                    $this -> qp_dispatch();
                                                    delete_transient('newsletters_queue_count');
                                                    $count_subscribers = 0;
                                                    $count_users = 0;

                                                    if (!empty($subscribers)) {
                                                        $count_subscribers = is_array($subscribers) ? count($subscribers) : 0;
                                                    }
                                                    if (!empty($users)) {
                                                        $count_users = is_array($users) ? count($users) : 0;
                                                    }
                                                    do_action($this->pre . '_admin_emailsqueued', ($count_subscribers + $count_users));
                                                    $message = ($count_subscribers+ $count_users) . ' ' . __('emails have been queued.', 'wp-mailinglist');

                                                    $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> queue), 'message', $message);
                                                }
                                            } else {
                                                $message = __('No mailing lists or roles have been selected', 'wp-mailinglist');
                                                $this -> render_error($message);
                                            }
                                        } else {
                                            $message = sprintf(__('Newsletter has been scheduled for %s', 'wp-mailinglist'), sanitize_text_field(wp_unslash($_POST['senddate'])));
                                            $this -> redirect('?page=' . $this -> sections -> history, 'message', $message);
                                        }

                                        if (!empty($_POST['inctemplate'])) {
                                            $this -> Template() -> inc_sent(sanitize_text_field(wp_unslash($_POST['inctemplate'])));
                                        }
                                    }
                                }
                                /* Save Draft */
                            } elseif (!empty($_POST['draft'])) {
                                $history_data = array(
                                    'from'				=>	sanitize_text_field(wp_unslash($_POST['from'])),
                                    'fromname'			=>	sanitize_text_field(wp_unslash($_POST['fromname'])),
                                    'subject'			=>	wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject']))),
                                    'message'			=>	wp_kses_post($_POST['content']),
                                    'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? sanitize_textarea_field(wp_unslash($_POST['customtext'])) : false),
                                    'theme_id'			=>	sanitize_text_field(wp_unslash($_POST['theme_id'])),
                                    'condquery'			=>	maybe_serialize(map_deep(wp_unslash($_POST['condquery']), 'sanitize_text_field')),
                                    'conditions'		=>	maybe_serialize(map_deep(wp_unslash($_POST['fields']), 'sanitize_text_field')),
                                    'conditionsscope'	=>	sanitize_text_field(wp_unslash($_POST['fieldsconditionsscope'])),
                                    'daterange'			=>	sanitize_text_field(wp_unslash($_POST['daterange'])),
                                    'daterangefrom'		=>	sanitize_text_field(wp_unslash($_POST['daterangefrom'])),
                                    'daterangeto'		=>	sanitize_text_field(wp_unslash($_POST['daterangeto'])),
                                    'countries'			=>	is_array($_POST['countries']) ? array_map('sanitize_text_field', wp_unslash($_POST['countries'])) : (!empty($_POST['countries']) ? [sanitize_text_field(wp_unslash($_POST['countries']))] : []),
                                    'selectedcountries'	=>	maybe_serialize(map_deep(wp_unslash($_POST['selectedcountries']), 'sanitize_text_field')),
                                    'post_id'			=>	$post_id,
                                    'user_id'			=>	sanitize_text_field((int)$_POST['user_id']),
                                    'mailinglists'		=>	maybe_serialize(map_deep(wp_unslash($_POST['mailinglists']), 'sanitize_text_field')),
                                    'groups'			=>	maybe_serialize(map_deep(wp_unslash($_POST['groups']), 'sanitize_text_field')),
                                    'roles'				=>	maybe_serialize(map_deep(wp_unslash($_POST['roles']), 'sanitize_text_field')),
                                    'newattachments'	=>	$newattachments,
                                    'recurring'			=>	"N",
                                    'senddate'			=>	sanitize_text_field(wp_unslash($_POST['senddate'])),
                                    //'scheduled'			=>	$_POST['scheduled'],
                                    'scheduled'			=>	"N",
                                    'format'			=>	sanitize_text_field(wp_unslash($_POST['format'])),
                                    'status'			=>	sanitize_text_field(wp_unslash($_POST['status'])),
                                    'state'				=>	"draft",
                                    'builderon'         =>  $_POST['builderon'],
                                    'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                                    'using_grapeJS'   =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''

                                );

                                if (!empty($_POST['ishistory'])) {
                                    $history_data['id'] = sanitize_text_field(wp_unslash($_POST['ishistory']));
                                    if ($history_curr = $this -> History() -> find(array('id' => $history_data['id']), array('id', 'sent', 'recurringdate'))) {
                                        $history_data['sent'] = $history_curr -> sent;
                                    }
                                }

                                if (!empty($_POST['sendrecurring'])) {
                                    if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
                                        $history_data['recurring'] = "Y";
                                        $history_data['recurringvalue'] = sanitize_text_field(wp_unslash($_POST['sendrecurringvalue']));
                                        $history_data['recurringinterval'] = sanitize_text_field(wp_unslash($_POST['sendrecurringinterval']));
                                        $history_data['recurringsent'] = sanitize_text_field(wp_unslash($_POST['recurringsent']));

                                        /*if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
											$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
										} else {*/
                                        $history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['sendrecurringdate']));
                                        //}

                                        $history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']));
                                    }
                                }

                                // Can the content areas be saved now?
                                $contentareas_saved = false;
                                if (!empty($history_data['id'])) {
                                    if (!empty($_POST['contentarea'])) {
                                        foreach (map_deep(wp_unslash($_POST['contentarea']), 'sanitize_text_field') as $number => $content) {
                                            $content_data = array(
                                                'number'			=>	$number,
                                                'history_id'		=>	$history_data['id'],
                                                'content'			=>	$content,
                                            );

                                            $this -> Content() -> save($content_data, true);
                                        }

                                        $contentareas_saved = true;
                                    }
                                }
                                

                                if ($this -> History() -> save($history_data, false)) {
                                    $history_id = $this -> History() -> insertid;

                                    // Should the content areas be saved now?
                                    if (empty($contentareas_saved)) {
                                        if (!empty($_POST['contentarea'])) {
                                            foreach (map_deep(wp_unslash($_POST['contentarea']), 'sanitize_text_field') as $number => $content) {
                                                $content_data = array(
                                                    'number'			=>	$number,
                                                    'history_id'		=>	$history_id,
                                                    'content'			=>	$content,
                                                );

                                                $this -> Content() -> save($content_data, true);
                                            }
                                        }
                                    }

                                    $redirect_url = admin_url('admin.php?page=' . $this -> sections -> send . '&method=history&id=' . $history_id);
                                    $this -> redirect($redirect_url, 'message', 4);
                                } else {
                                    $this -> render_error(5);
                                }
                                /* Send a preview email */
                            } else {
                                $history_data = array(
                                    'from'				=>	sanitize_text_field(isset($_POST['from']) ? $_POST['from'] : ''),
                                    'fromname'			=>	sanitize_text_field(isset($_POST['fromname']) ? $_POST['fromname'] : ''),
                                    'subject'			=>	wp_kses_post(wp_unslash(isset($_POST['subject']) ? $_POST['subject'] : '')),
                                    'message'			=>	wp_kses_post(wp_unslash(isset($_POST['content']) ? $_POST['content'] : '')),
                                    'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? sanitize_textarea_field($_POST['customtext']) : false),
                                    'language'          =>  sanitize_text_field(isset($_POST['language']) ? $_POST['language'] : ''),
                                    'preheader'          =>  sanitize_text_field(isset($_POST['preheader']) ? $_POST['preheader'] : ''),
                                    'spamscore'          =>  sanitize_text_field(isset($_POST['spamscore']) ? $_POST['spamscore'] : ''),
                                    'theme_id'			=>	sanitize_text_field(isset($_POST['theme_id']) ? $_POST['theme_id'] : ''),
                                    'condquery'			=>	maybe_serialize(map_deep(isset($_POST['condquery']) ? $_POST['condquery'] : '', 'sanitize_text_field')),
                                    'conditions'		=>	maybe_serialize(map_deep(isset($_POST['fields']) ? $_POST['fields'] : '', 'sanitize_text_field')),
                                    'conditionsscope'	=>	sanitize_text_field(isset($_POST['fieldsconditionsscope']) ? $_POST['fieldsconditionsscope'] : ''),
                                    'daterange'			=>	sanitize_text_field(isset($_POST['daterange']) ? $_POST['daterange'] : ''),
                                    'daterangefrom'		=>	sanitize_text_field(isset($_POST['daterangefrom']) ? $_POST['daterangefrom'] : ''),
                                    'daterangeto'		=>	sanitize_text_field(isset($_POST['daterangeto']) ? $_POST['daterangeto'] : ''),
                                    'countries'			=>	isset($_POST['countries']) ? (is_array($_POST['countries']) ? array_map('sanitize_text_field', $_POST['countries']) : (!empty($_POST['countries']) ? [sanitize_text_field($_POST['countries'])] : [])) : [],
                                    'selectedcountries'	=>	maybe_serialize(map_deep(isset($_POST['selectedcountries']) ? $_POST['selectedcountries'] : '', 'sanitize_text_field')),
                                    'mailinglists'		=>	maybe_serialize(map_deep(isset($_POST['mailinglists']) ? $_POST['mailinglists'] : '', 'sanitize_text_field')),
                                    'groups'			=>	maybe_serialize(map_deep(isset($_POST['groups']) ? $_POST['groups'] : '', 'sanitize_text_field')),
                                    'roles'				=>	maybe_serialize(map_deep(isset( $_POST['roles']) ? $_POST['roles'] : '', 'sanitize_text_field')),
                                    'post_id'			=>	$post_id,
                                    'user_id'			=>	sanitize_text_field(isset($_POST['user_id']) ? $_POST['user_id'] : ''),
                                    'newattachments'	=>	$newattachments,
                                    'recurring'			=>	"N",
                                    'senddate'			=>	sanitize_text_field(isset($_POST['senddate']) ? $_POST['senddate'] : ''),
                                    //'scheduled'			=>	$_POST['scheduled'],
                                    'scheduled'			=>	"N",
                                    'format'			=>	sanitize_text_field(isset($_POST['format']) ? $_POST['format'] : ''),
                                    'status'			=>	sanitize_text_field(isset($_POST['status']) ? $_POST['status'] : ''),
                                    'state'				=>	"draft",
                                    'builderon'         =>  $_POST['builderon'],
                                    'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                                    'using_grapeJS'   =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''

                                );

                                if (!empty($_POST['ishistory'])) {
                                    $history_data['id'] = sanitize_text_field(wp_unslash($_POST['ishistory']));
                                    if ($history_curr = $this -> History() -> find(array('id' => $history_data['id']))) {
                                        $history_data['sent'] = $history_curr -> sent;
                                    }
                                }

                                if (!empty($_POST['sendrecurring'])) {
                                    if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
                                        $history_data['recurring'] = "Y";
                                        $history_data['recurringvalue'] = sanitize_text_field(wp_unslash($_POST['sendrecurringvalue']));
                                        $history_data['recurringinterval'] = sanitize_text_field(wp_unslash($_POST['sendrecurringinterval']));
                                        $history_data['recurringsent'] = sanitize_text_field(wp_unslash($_POST['recurringsent']));

                                        /*if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
											$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
										} else {*/
                                        $history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['sendrecurringdate']));
                                        //}

                                        $history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']));
                                    }
                                }
                                if(!isset($_POST['sendrecurring']) || empty($_POST['sendrecurring']))
                                {
                                    $history_data['recurringdate'] = '';
                                    $history_data['recurring'] = '';
                                    $history_data['recurringvalue'] = '';
                                    $history_data['recurringinterval'] = '';
                                    $history_data['recurringlimit'] = '';
                                    $history_data['recurringlimit'] = '';
                                    $history_data['recurringsent'] = '';
                                }

                                $this -> History() -> save($history_data, false);
                                $history_id = $this -> History() -> insertid;

                                if (!empty($_POST['contentarea'])) {
                                    foreach ($_POST['contentarea'] as $number => $content) {
                                        $content_data = array(
                                            'number'			=>	$number,
                                            'history_id'		=>	$history_id,
                                            'content'			=>	$content,
                                        );

                                        $this -> Content() -> save($content_data, true);
                                    }
                                }

                                $history = $this -> History() -> find(array('id' => $history_id));

                                $subscriber_id = $Subscriber -> admin_subscriber_id();
                                if (!empty($_POST['previewemail'])) {
                                    $emails = explode(",", sanitize_text_field($_POST['previewemail']));

                                    foreach ($emails as $email) {
                                        $email = trim($email);

                                        if (is_email($email)) {
                                            if (!$subscriber_id = $Subscriber -> email_exists($email)) {
                                                $subscriber_data = array('email' => $email);
                                                $Subscriber -> save($subscriber_data, false);
                                                $subscriber_id = $Subscriber -> insertid;
                                            }

                                            $subscriber = $Subscriber -> get($subscriber_id, false);
                                            $subject = wp_kses_post(wp_unslash($_POST['subject']));
                                            $content = wp_kses_post(wp_unslash($_POST['content']));
                                            $message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, isset($_POST['theme_id']) ? $_POST['theme_id'] : '');
                                            $eunique = $Html -> eunique($subscriber, $history -> id);

                                            if (!$this -> execute_mail($subscriber, false, $subject, $message, $newattachments, $history_id, $eunique, false, "preview")) {
                                                global $mailerrors;

                                                if (is_array($mailerrors)) {
                                                    $mailerrors = implode(";", $mailerrors);
                                                }

                                                $this -> render_error(2, array($email, $mailerrors));
                                            } else {
                                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> send . '&method=history&id=' . $history_id . '&previewemail=' . sanitize_text_field(wp_unslash($_POST['previewemail'])), 'message', 1, false));
											}
                                        } else {
                                            $this -> render_error(3, array($email));
                                        }
                                    }
                                }

                                $newpost = wp_parse_args(array(
                                    'ishistory'			=>	$history -> id,
                                    'p_id'				=>	$history -> p_id,
                                    'user_id'			=>	$history -> user_id,
                                    'from'				=>	$history -> from,
                                    'fromname'			=>	$history -> fromname,
                                    'subject'			=>	$history -> subject,
                                    'content'			=>	$history -> message,
                                    'groups'			=>	$history -> groups,
                                    'roles'				=>	maybe_unserialize($history -> roles),
                                    'mailinglists'		=>	$history -> mailinglists,
                                    'theme_id'			=>	$history -> theme_id,
                                    'condquery'			=>	maybe_unserialize($history -> condquery),
                                    'conditions'		=>	maybe_unserialize($history -> conditions),
                                    'conditionsscope'	=>	$history -> conditionsscope,
                                    'daterange'			=>	$history -> daterange,
                                    'daterangefrom'		=>	$history -> daterangefrom,
                                    'daterangeto'		=>	$history -> daterangeto,
                                    'countries'			=>	$history -> countries,
                                    'selectedcountries'	=>	maybe_unserialize($history -> selectedcountries),
                                    'fields'			=>	maybe_unserialize($history -> conditions),
                                    'attachments'		=>	$newattachments,
                                    'customtexton'		=>	((!empty($history -> text)) ? true : false),
                                    'customtext'		=>	$history -> text,
                                    'format'			=>	$history -> format,
                                    'status'			=>	$history -> status,
                                    'builderon'         =>  $history -> builderon

                                ), $_POST);

                                $_POST = $newpost;
                            }
                        } else {
                            if (!empty($_POST['preview'])) {
                                $message = __('Preview could not be sent', 'wp-mailinglist');
                            } else {
                                if (!empty($_POST['sendtype']) && $_POST['sendtype'] == "queue") {
                                    $message = __('Newsletter could not be scheduled/queued', 'wp-mailinglist');
                                } else {
                                    $message = __('Newsletter could not be sent', 'wp-mailinglist');
                                }
                            }

                            $this -> render_error($message);
                        }
                    }

                    if (empty($dontrendersend) || $dontrendersend == false) {
                        $mailinglists = $Mailinglist -> get_all('*', true);
                        $templates = $this -> Template() -> get_all();

                        $this -> render('send', array('mailinglists' => $mailinglists, 'themes' => $themes, 'templates' => $templates, 'errors' => $errors), true, 'admin');
                    }
                    break;
            }
        }

        function admin_autoresponders() {
            global $wpdb, $Db;
            $Db -> model = $this -> Autoresponder() -> model;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");

            switch ($method) {
                case 'save'					:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> autoresponders . '_save');
                        if ($this -> Autoresponder() -> save($_POST)) {
                            $message = __('Autoresponder has been saved.', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&method=save&id=' . $this -> Autoresponder() -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect("?page=" . $this -> sections -> autoresponders, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Autoresponder could not be saved, please try again.', 'wp-mailinglist'));
                            $this -> render('autoresponders' . DS . 'save', false, true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                        $autoresponder = array();
                        if (!empty($id)) {
                            $Db -> model = $this -> Autoresponder() -> model;
                            $autoresponder = $Db -> find(array('id' => $id));
                        }

                        $this -> render('autoresponders' . DS . 'save', array('autoresponder' => $autoresponder), true, 'admin');
                    }
                    break;
                case 'delete'				:
                    $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                    if (!empty($id)) {
                        if ($Db -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Autoresponder has been deleted.', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Autoresponder cannot be deleted, please try again.', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No autoresponder was specified.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'mass'					:
                    check_admin_referer($this -> sections -> autoresponders . '_mass');

                    if (!empty($_POST['autoresponderslist'])) {
                        if (!empty($_POST['action'])) {
                            $autoresponders = array_map('sanitize_text_field', $_POST['autoresponderslist']);

                            switch ($_POST['action']) {
                                case 'delete'				:
                                    foreach ($autoresponders as $autoresponder_id) {
                                        //remove the autoresponder
                                        $Db -> model = $this -> Autoresponder() -> model;
                                        $Db -> delete($autoresponder_id);
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                                case 'activate'				:
                                    foreach ($autoresponders as $autoresponder_id) {
                                        $Db -> model = $this -> Autoresponder() -> model;
                                        $Db -> save_field('status', "active", array('id' => $autoresponder_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected autoresponders have been activated and messages will be scheduled for new subscriptions.', 'wp-mailinglist');
                                    break;
                                case 'deactivate'			:
                                    foreach ($autoresponders as $autoresponder_id) {
                                        $Db -> model = $this -> Autoresponder() -> model;
                                        $Db -> save_field('status', "inactive", array('id' => $autoresponder_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected autoresponders have been deactivated and no more messages will be scheduled for them.', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 17;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 16;
                    }

                    $this -> redirect("?page=" . $this -> sections -> autoresponders, $msg_type, $message);
                    break;
                case 'autoresponderscheduling'			:
                    check_admin_referer($this -> sections -> autoresponders . '_scheduling');

                    if (!empty($_POST['autoresponderscheduling'])) {
                        $this -> update_option('autoresponderscheduling', sanitize_text_field(wp_unslash($_POST['autoresponderscheduling'])));
                        wp_clear_scheduled_hook($this -> pre . '_autoresponders');
                        $this -> autoresponder_scheduling();

                        $msg_type = 'message';
                        $message = __('Autoresponders schedule interval has been updated.', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No schedule interval was chosen.', 'wp-mailinglist');
                    }

                    $this -> redirect('?page=' . $this -> sections -> autoresponders, $msg_type, $message);
                    break;
                default						:
                    $dojoin = false;
                    $conditions_and = array();
                    $autoresponders_table = $wpdb -> prefix . $this -> Autoresponder() -> table;
                    $autoresponderslist_table = $wpdb -> prefix . $this -> AutorespondersList() -> table;

                    $perpage = (isset($_COOKIE[$this -> pre . 'autorespondersperpage'])) ? $_COOKIE[$this -> pre . 'autorespondersperpage'] : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field($_GET[$this -> pre . 'searchterm']) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field($_POST['searchterm']) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> autoresponders . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' : esc_html($_GET['orderby']);
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(esc_html($_GET['order']));
                    $order = array($orderfield, $orderdirection);

                    $sections = $this -> sections -> autoresponders;

                    if (!empty($_GET['filter'])) {
                        check_admin_referer($this -> sections -> autoresponders . '_filter');
                        $sections .= '&filter=1';

                        if (!empty($_GET['list'])) {
                            switch ($_GET['list']) {
                                case 'all'					:
                                    $dojoin = false;
                                    break;
                                case 'none'					:
                                    $dojoin = false;
                                    $conditions_and[$autoresponders_table . '.id'] = "NOT IN (SELECT autoresponder_id FROM " . $autoresponderslist_table . ")";
                                    break;
                                default						:
                                    $dojoin = true;
                                    $conditions_and[$autoresponderslist_table . '.list_id'] = esc_html($_GET['list']);
                                    break;
                            }
                        }

                        if (!empty($_GET['status'])) {
                            switch ($_GET['status']) {
                                case 'active'				:
                                    $conditions_and[$autoresponders_table . '.status'] = 'active';
                                    break;
                                case 'inactive'				:
                                    $conditions_and[$autoresponders_table . '.status'] = 'inactive';
                                    break;
                                default 					:
                                    //do nothing, all statuses
                                    break;
                            }
                        }
                    }

                    $data = array();
                    if (!empty($_GET['showall'])) {
                        $Db -> model = $this -> Autoresponder() -> model;
                        $autoresponders = $Db -> find_all(false, "*", $order);
                        $data[$this -> Autoresponder() -> model] = $autoresponders;
                        $data['Paginate'] = false;
                    } else {
                        if ($dojoin) {
                            $data = $this -> paginate($this -> AutorespondersList() -> model, false, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                            $autoresponders = $data[$this -> AutorespondersList() -> model];
                        } else {
                            $data = $this -> paginate($this -> Autoresponder() -> model, false, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                            if(!empty($data)) {
                               $autoresponders = $data[$this->Autoresponder()->model];
                            }
                            else {
                                $autoresponders = $data;
                            }
                        }
                    }

                    $this -> render_message(__('Please note that autoresponder emails are only sent to Active subscriptions. Once a subscription is Active, the autoresponder email will queue.', 'wp-mailinglist'));
                    $this -> render('autoresponders' . DS . 'index', array('autoresponders' => $autoresponders, 'paginate' => isset($data['Paginate']) ? $data['Paginate'] : array()), true, 'admin');
                    break;
            }
        }

        function admin_autoresponderemails() {
            global $wpdb, $Db, $Subscriber, $SubscribersList, $Html,
                   $HistoriesAttachment;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'send'					:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $query = "SELECT " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".id, "
                            . $wpdb -> prefix . $SubscribersList -> table . ".list_id, "
                            . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".subscriber_id, "
                            . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".autoresponder_id FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " LEFT JOIN "
                            . $wpdb -> prefix . $SubscribersList -> table . " ON " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".subscriber_id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id
						WHERE " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".id = '" . esc_sql($id) . "' LIMIT 1";

                        $ae = $wpdb -> get_row($query);

                        if (!empty($ae)) {
                            $query = "SELECT " . $wpdb -> prefix . $this -> History() -> table . ".id, "
                                . $wpdb -> prefix . $this -> History() -> table . ".subject, "
                                . $wpdb -> prefix . $this -> History() -> table . ".message, "
                                . $wpdb -> prefix . $this -> History() -> table . ".theme_id FROM "
                                . $wpdb -> prefix . $this -> History() -> table . " LEFT JOIN "
                                . $wpdb -> prefix . $this -> Autoresponder() -> table . " ON " . $wpdb -> prefix . $this -> History() -> table . ".id = " . $wpdb -> prefix . $this -> Autoresponder() -> table . ".history_id WHERE "
                                . $wpdb -> prefix . $this -> Autoresponder() -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";

                            $history = $wpdb -> get_row($query);

                            // Get the attachments of the newsletter
                            $history -> attachments = array();
                            $attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";

                            if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
                                foreach ($attachments as $attachment) {
                                    $history -> attachments[] = array(
                                        'id'					=>	$attachment -> id,
                                        'title'					=>	$attachment -> title,
                                        'filename'				=>	$attachment -> filename,
                                    );
                                }
                            }

                            /* The Subscriber */
                            $Db -> model = $Subscriber -> model;
                            $subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
                            $subscriber -> mailinglist_id = $ae -> list_id;

                            /* The Message */
                            $eunique = $Html -> eunique($subscriber, $history -> id);

                            /* Send the email */
                            $Db -> model = $Email -> model;
                            $message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);

                            if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {
                                $Db -> model = $this -> Autoresponderemail() -> model;
                                $Db -> save_field('status', "sent", array('id' => $ae -> id));
                                $addedtoqueue++;
                                $msg_type = 'message';
                                $message = __('Autoresponder email has been sent.', 'wp-mailinglist');
                            } else {
                                $msg_type = 'error';
                                $message = __('Autoresponder email could not be sent, please check your email settings.', 'wp-mailinglist');
                            }
                        } else {
                            $msg_type = 'error';
                            $message = __('Autoresponder email cannot be read.', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No autoresponder email was specified.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'delete'				:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $Db -> model = $this -> Autoresponderemail() -> model;

                        if ($Db -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Autoresponder email has been removed.', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Autoresponder email cannot be deleted.', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No autoresponder email has been specified.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'mass'					:

                    check_admin_referer($this -> sections -> autoresponderemails . '_mass');

                    if (!empty($_POST['autoresponderemailslist'])) {
                        if (!empty($_POST['action'])) {
                            $autoresponderemails = array_map('sanitize_text_field', $_POST['autoresponderemailslist']);

                            switch ($_POST['action']) {
                                case 'delete'				:
                                    foreach ($autoresponderemails as $ae_id) {
                                        //remove the autoresponder
                                        $Db -> model = $this -> Autoresponderemail() -> model;
                                        $Db -> delete(esc_sql($ae_id));
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                                case 'send'				:
                                    foreach ($autoresponderemails as $ae_id) {
                                        $query = "SELECT " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".id, "
                                            . $wpdb -> prefix . $SubscribersList -> table . ".list_id, "
                                            . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".subscriber_id, "
                                            . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".autoresponder_id FROM " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . " LEFT JOIN "
                                            . $wpdb -> prefix . $SubscribersList -> table . " ON " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".subscriber_id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id
										WHERE " . $wpdb -> prefix . $this -> Autoresponderemail() -> table . ".id = '" . $ae_id . "' LIMIT 1";

                                        if ($ae = $wpdb -> get_row($query)) {
                                            $query = "SELECT " . $wpdb -> prefix . $this -> History() -> table . ".id, "
                                                . $wpdb -> prefix . $this -> History() -> table . ".subject, "
                                                . $wpdb -> prefix . $this -> History() -> table . ".message, "
                                                . $wpdb -> prefix . $this -> History() -> table . ".theme_id FROM "
                                                . $wpdb -> prefix . $this -> History() -> table . " LEFT JOIN "
                                                . $wpdb -> prefix . $this -> Autoresponder() -> table . " ON " . $wpdb -> prefix . $this -> History() -> table . ".id = " . $wpdb -> prefix . $this -> Autoresponder() -> table . ".history_id WHERE "
                                                . $wpdb -> prefix . $this -> Autoresponder() -> table . ".id = '" . $ae -> autoresponder_id . "' LIMIT 1;";

                                            $history = $wpdb -> get_row($query);

                                            /* The Subscriber */
                                            $Db -> model = $Subscriber -> model;
                                            $subscriber = $Db -> find(array('id' => $ae -> subscriber_id), false, false, true, false);
                                            $subscriber -> mailinglist_id = $ae -> list_id;

                                            /* The Message */
                                            $eunique = $Html -> eunique($subscriber, $history -> id);

                                            /* Send the email */
                                            $Db -> model = $Email -> model;
                                            $message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);

                                            if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {
                                                $Db -> model = $this -> Autoresponderemail() -> model;
                                                $Db -> save_field('status', "sent", array('id' => $ae -> id));
                                                $addedtoqueue++;
                                            }
                                        }
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected autoresponder emails were sent.', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 17;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 16;
                    }

                    $this -> redirect("?page=" . $this -> sections -> autoresponderemails, $msg_type, $message);
                    break;
                default						:
                    $perpage = (isset($_COOKIE[$this -> pre . 'autoresponderemailsperpage'])) ? $_COOKIE[$this -> pre . 'autoresponderemailsperpage'] : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field($_GET[$this -> pre . 'searchterm']) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field($_POST['searchterm']) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;

                    if (!empty($_GET['status'])) {
                        $_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'] = esc_html($_GET['status']);
                    }

                    if (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status'])) {
                        switch($_COOKIE[$this -> pre . 'autoresponderemailsfilter_status']) {
                            case 'all'		:
                                //do nothing...
                                break;
                            case 'sent'		:
                                $conditions['status'] = "sent";
                                break;
                            case 'unsent'	:
                            default			:
                                $conditions['status'] = "unsent";
                                break;
                        }
                    }

                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] = $id;
                    }

                    if (isset($_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'])) {
                        if (empty($conditions['status'])) {
                            $conditions['autoresponder_id'] = $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'];
                        } else {
                            $conditions['status'] .= "' AND autoresponder_id = '" . $_COOKIE[$this -> pre . 'autoresponderemailsfilter_autoresponder_id'] . "";
                        }
                    }

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' : esc_html($_GET['orderby']);
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(esc_html($_GET['order']));
                    $order = array($orderfield, $orderdirection);

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $this -> Autoresponderemail() -> model;
                        $autoresponderemails = $Db -> find_all($conditions, "*", $order, false, true);
                        $data[$this -> Autoresponderemail() -> model] = $autoresponderemails;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Autoresponderemail() -> model, false, $this -> sections -> autoresponderemails, $conditions, $searchterm, $perpage, $order);
                    }

                    $this -> render_message(__('Please note that autoresponder emails are only sent to Active subscriptions. Once a subscription is Active, the autoresponder email will queue.', 'wp-mailinglist'));
                    $this -> render('autoresponderemails' . DS . 'index', array('autoresponderemails' => $data[$this -> Autoresponderemail() -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        function admin_mailinglists() {
            global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList;
            $Db -> model = $Mailinglist -> model;
            $msg_type = '';
            $method = isset($_GET['method']) ? sanitize_text_field($_GET['method']) : '';
            switch ($method) {
                case 'save'			:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> lists . '_save');
                        if ($Mailinglist -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                            $message = __('Mailing list has been saved', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> lists . '&method=save&id=' . $Mailinglist -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect($this -> url, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Mailing list could not be saved', 'wp-mailinglist'));
                            $mailinglist = $this -> init_class('wpmlMailinglist', $_POST);
                            $this -> render('mailinglists' . DS . 'save', array('mailinglist' => $mailinglist, 'errors' => $this -> Mailinglist -> errors), true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : '0');
                        if (!empty($id)) {
                            $mailinglist = $Mailinglist -> get($id);
                        }

                        if (!empty($_GET['group_id']) && !empty ($Mailinglist -> data)) { $Mailinglist -> data -> group_id = esc_html($_GET['group_id']); }
                        $this -> render('mailinglists' . DS . 'save', array('mailinglist' => (!empty($mailinglist) ? $mailinglist : array()) ), true, 'admin');
                    }
                    break;
                case 'cleardefault'		:
                    $query = "UPDATE `" . $wpdb -> prefix . $Mailinglist -> table . "` SET `default` = '0'";
                    $wpdb -> query($query);

                    $this -> redirect($this -> url, 'message', __('Default list removed', 'wp-mailinglist'));
                    break;
                case 'default'		:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $query = "UPDATE `" . $wpdb -> prefix . $Mailinglist -> table . "` SET `default` = (`id` = '" . esc_sql($id) . "')";
                        if ($wpdb -> query($query)) {
                            $message = __('List set as default list', 'wp-mailinglist');
                            $msg_type = 'message';
                        } else {
                            $message = __('List could not be set as default', 'wp-mailinglist');
                            $msg_type = 'error';
                        }
                    } else {
                        $message = __('No list was specified', 'wp-mailinglist');
                        $msg_type = 'error';
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                case 'view'			:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($mailinglist = $Mailinglist -> get($id)) {
                            $perpage = (!empty($_COOKIE[$this -> pre . 'subscribersperpage'])) ? $_COOKIE[$this -> pre . 'subscribersperpage'] : 15;
                            $sub = $this -> sections -> lists . '&method=view&id=' . $id;
                            $subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
                            $conditions = array($subscriberslists_table . '.list_id' => $id);
                            $searchterm = false;
                            $orderfield = (empty($_GET['orderby'])) ? 'modified' : esc_html($_GET['orderby']);
                            $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(esc_html($_GET['order']));
                            $order = array($orderfield, $orderdirection);
                            $data = $this -> paginate($SubscribersList -> model, false, $sub, $conditions, $searchterm, $perpage, $order);
                            $subscribers = $data[$SubscribersList -> model];

                            $this -> render('mailinglists' . DS . 'view', array('mailinglist' => $mailinglist, 'subscribers' => $subscribers, 'paginate' => (isset($data['Paginate']) ? $data['Paginate'] : array())), true, 'admin');
                        } else {
                            $this -> render_error(__('Mailing list could not be read', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No mailing list was specified', 'wp-mailinglist'));
                    }
                    break;
                case 'delete'		:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($Mailinglist -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Mailing list has been removed', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Mailing list cannot be removed', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No mailing list was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'deletesubscribers'		:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($Mailinglist -> delete_subscribers($id)) {
                            $msg_type = 'message';
                            $message = __('Subscribers in mailing list deleted', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Subscribers could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No mailing list was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'mass'			:
                    check_admin_referer($this -> sections -> lists . '_mass');

                    if (!empty($_POST['mailinglistslist'])) {
                        if (!empty($_POST['action'])) {
                            $lists = array_map('sanitize_text_field', $_POST['mailinglistslist']);

                            switch ($_POST['action']) {
                                case 'singleopt'		:
                                    foreach ($lists as $list_id) {
                                        $Db -> model = $Mailinglist -> model;
                                        $Db -> save_field('doubleopt', "N", array('id' => $list_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected lists set as single opt-in', 'wp-mailinglist');
                                    break;
                                case 'doubleopt'		:
                                    foreach ($lists as $list_id) {
                                        $Db -> model = $Mailinglist -> model;
                                        $Db -> save_field('doubleopt', "Y", array('id' => $list_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected lists set as doublt opt-in', 'wp-mailinglist');
                                    break;
                                case 'merge'			:
                                    global $Db, $Mailinglist, $SubscribersList, $FieldsList, $HistoriesList;

                                    if (!empty($_POST['list_title'])) {
                                        if (count($lists) > 1) {
                                            $list_data = array(
                                                'title'					=>	sanitize_text_field(wp_unslash($_POST['list_title'])),
                                                'privatelist'			=>	"N",
                                                'paid'					=> 	"N",
                                            );

                                            if ($Mailinglist -> save($list_data)) {
                                                $new_list_id = $Mailinglist -> insertid;

                                                foreach ($lists as $list_id) {
                                                    $Db -> model = $SubscribersList -> model;
                                                    $Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));
                                                    $Db -> model = $FieldsList -> model;
                                                    $Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));
                                                    $Db -> model = $HistoriesList -> model;
                                                    $Db -> save_field('list_id', $new_list_id, array('list_id' => $list_id));
                                                    $Mailinglist -> delete($list_id);
                                                }

                                                $msg_type = 'message';
                                                $message = __('Selected lists have been merged', 'wp-mailinglist');
                                            } else {
                                                $msg_type = 'error';
                                                $message = __('Merge list could not be created', 'wp-mailinglist');
                                            }
                                        } else {
                                            $msg_type = 'error';
                                            $message = __('Select more than one list in order to merge', 'wp-mailinglist');
                                        }
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('Fill in a list title for the new list', 'wp-mailinglist');
                                    }

                                    break;
                                case 'setgroup'			:
                                    if (!empty($_POST['setgroup_id'])) {
                                        foreach ($lists as $list_id) {
                                            $Mailinglist -> save_field('group_id', sanitize_text_field(wp_unslash($_POST['setgroup_id']), $list_id));
                                        }

                                        $msg_type = "message";
                                        $message = __('Selected mailing lists assigned to the chosen group.', 'wp-mailinglist');
                                    } else {
                                        $msg_type = "message";
                                        $message = __('No group was selected.', 'wp-mailinglist');
                                    }
                                    break;
                                case 'delete'			:
                                    $Mailinglist -> delete_array($lists);
                                    $msg_type = "message";
                                    $message = 18;
                                    break;
                                case 'private'			:
                                    foreach ($lists as $id) {
                                        $Mailinglist -> save_field('privatelist', 'Y', $id);
                                    }

                                    $msg_type = "message";
                                    $message = __('Selected mailing lists have been set as private', 'wp-mailinglist');
                                    break;
                                case 'notprivate'		:
                                    foreach ($lists as $id) {
                                        $Mailinglist -> save_field('privatelist', 'N', $id);
                                    }

                                    $msg_type = "message";
                                    $message = __('Selected mailing lists have been set as not private', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = "error";
                            $message = 17;
                        }
                    } else {
                        $msg_type = "error";
                        $message = 16;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'offsite'			:
                    if (!empty($_GET['listid'])) {
                        $this -> update_option('offsitelist', sanitize_text_field(wp_unslash($_GET['listid'])));
                    } else {
                        $msg_type = 'error';
                        $message = __('No mailing list was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url . '&method=offsitewizard&listid=' . sanitize_text_field(wp_unslash($_GET['listid'])), $msg_type, $message);
					break;
                case 'offsitewizard'	:
                    global $Html, $FieldsList;

                    $code = false;
                    $listid = (!empty($_GET['listid'])) ? sanitize_text_field(wp_unslash($_GET['listid'])) : sanitize_text_field(wp_unslash($_POST['list']));

					if (!empty($_POST)) {
                        $opts = array('title', 'formtype', 'subscribe', 'form', 'list', 'width', 'height', 'button', 'stylesheet', 'fields');

                        foreach ($_POST as $pkey => $pval) {
                            if (in_array($pkey, $opts)) {
                                if (!empty($pval)) {
                                    $this -> update_option('offsite' . $pkey, wp_unslash($pval));
                                }
                            }
                        }

                        if (!empty($_POST['subscribe']) && $_POST['subscribe'] == "list") {
                            if (empty($listid)) { $this -> render_error(__('Please select mailing list/s', 'wp-mailinglist')); }
                        } elseif (!empty($_POST['subscribe']) && $_POST['subscribe'] == "form") {
                            if (empty($_POST['form'])) { $this -> render_error(__('Please select a subscribe form', 'wp-mailinglist')); }
                        }
                    } else {
                        $this -> render_message(__('Offsite code is specifically used for non-WordPress and other remote websites, not for the current one.', 'wp-mailinglist'));
                    }

					if (!empty($_POST['subscribe']) && $_POST['subscribe'] == "list" && !empty($listid)) {
                        $options = $this -> get_option('offsite');

                        $options['title'] = (empty($_POST['title'])) ? get_option('blogname') :  sanitize_text_field(wp_unslash($_POST['title']));
                        $options['list'] = esc_html($listid);
                        $options['button'] = (empty($_POST['button'])) ? __('Subscribe', 'wp-mailinglist') :  sanitize_text_field(wp_unslash($_POST['button']));
                        $options['ajax'] = "Y";
                        $options['stylesheet'] = (empty($_POST['stylesheet'])) ? "Y" :  sanitize_text_field(wp_unslash($_POST['stylesheet']));
                        $wpoptinid = current_time('timestamp');
                        $options['wpoptinid'] = $wpoptinid;

                        $fields = false;
                        if (!empty($_POST['formtype']) && $_POST['formtype'] == "popup") {
                            if (empty($_POST['fields']) || (!empty($_POST['fields']) && $_POST['fields'] == "Y")) {
                                $fields = $FieldsList -> fields_by_list($listid);
                            }
                        } elseif ($_POST['formtype'] == "html") {
                            if (empty($_POST['html_fields']) || (!empty($_POST['html_fields']) && $_POST['html_fields'] == "Y")) {
                                $fields = $FieldsList -> fields_by_list($listid);
                            }
                        }

                        ob_start();
                        switch ($_POST['formtype']) {
                            case 'iframe'				:
                                wp_enqueue_script('jquery-autoheight', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.autoheight.js', array('jquery'), false, true);

                                $this -> render('offsite-iframe', array('options' => $options, 'fields' => $fields), true, 'admin');
                                break;
                            case 'html'					:
                                $this -> render('offsite-html', array('options' => $options, 'fields' => $fields), true, 'admin');
                                break;
                            case 'popup'				:
                            default						:
                                $this -> render('offsite-form', array('options' => $options, 'fields' => $fields), true, 'admin');
                                break;
                        }

                        $code = ob_get_clean();

                        $offsiteurl = home_url('?' . $this -> pre . 'method=offsite&list=' . $listid);
                    } elseif (!empty($_POST['subscribe']) && $_POST['subscribe'] == "form" && !empty($_POST['form'])) {

                    }

					$this -> render('offsite-wizard', array('code' => $code, 'offsiteurl' => $offsiteurl, 'listid' => $listid), true, 'admin');
					break;
                default				:
                    $perpage = (isset($_COOKIE[$this -> pre . 'listsperpage'])) ? (int) $_COOKIE[$this -> pre . 'listsperpage'] : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> lists . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
                    $conditions_and = false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    $conditions = apply_filters($this -> pre . '_admin_mailinglists_conditions', $conditions);
                    $conditions_and = apply_filters('newsletters_admin_mailinglists_conditions_and', $conditions_and);

                    $data = array();
                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Mailinglist -> model;
                        $lists = $Db -> find_all(false, "*", $order);
                        $data[$Mailinglist -> model] = $lists;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($Mailinglist -> model, null, $this -> sections -> lists, $conditions, $searchterm, $perpage, $order, $conditions_and);
                    }

                    $this -> render('mailinglists' . DS . 'index', array('mailinglists' => $data[$Mailinglist -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        function admin_groups() {
            global $wpdb, $Db, $Mailinglist;
            $Db -> model = $this -> Group() -> model;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'save'						:
                    $Db -> model = $this -> Group() -> model;

                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> groups . '_save');
                        if ($this -> Group() -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                            $message = __('Group has been saved.', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> groups . '&method=save&id=' . $this -> Group() -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect($this -> url, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Group could not be saved.', 'wp-mailinglist'));
                            $group = $this -> init_class($this -> Group() -> model, $_POST);
                            $this -> render('groups' . DS . 'save', array('group' => $group, 'errors' => $this -> Group() -> errors), true, 'admin');
                        }
                    } else {
                        $id = isset($_GET['id']) ? (int) sanitize_text_field($_GET['id']) : 0;
                        if (!empty($id)) {
                            $group = $this -> Group() -> find(array('id' => $id));
                        }

                        if(isset($groups) && !empty($group)) {
                        $this -> render('groups' . DS . 'save', array('group' => $group), true, 'admin');
                    }
                        else {
                            $this -> render('groups' . DS . 'save', array(), true, 'admin');
                        }
                    }
                    break;
                case 'view'						:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($group = $Db -> find(array('id' => $id))) {
                            $perpage = (!empty($_COOKIE[$this -> pre . 'listsperpage'])) ? (int) $_COOKIE[$this -> pre . 'listsperpage'] : 15;
                            $data = $Mailinglist -> get_all_paginated(array('group_id' => $id), false, $this -> sections -> groups . '&method=view&id=' . $id, $perpage);
                            if (isset($data['Mailinglist']) && !empty($data)) {
                            $this -> render('groups' . DS . 'view', array('group' => $group, 'mailinglists' => $data['Mailinglist'], 'paginate' => $data['Pagination']), true, 'admin');

                            }
                            else {
                                $this -> render('groups' . DS . 'view', array('group' => $group, 'mailinglists' => array(), 'paginate' => array()), true, 'admin');

                            }

                        } else {
                            $this -> render_error(__('Group could not be read', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No group was specified', 'wp-mailinglist'));
                    }
                    break;
                case 'delete'					:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($Db -> delete($id)) {
                            $Db -> model = $Mailinglist -> model;
                            $Db -> save_field('group_id', "0", array('group_id' => $id));

                            $msg_type = 'message';
                            $message = __('Group has been removed', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Group cannot be removed', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No group was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'mass'						:
                    check_admin_referer($this -> sections -> groups . '_mass');
                    if (!empty($_POST['groupslist'])) {
                        if (!empty($_POST['action'])) {
                            $groups = array_map('sanitize_text_field', $_POST['groupslist']);

                            switch ($_POST['action']) {
                                case 'delete'			:

                                    foreach ($groups as $group_id) {
                                        $this -> Group() -> delete($group_id);
                                    }

                                    $msg_type = "message";
                                    $message = 18;
                                    break;
                            }
                        } else {
                            $msg_type = "error";
                            $message = 17;
                        }
                    } else {
                        $msg_type = "error";
                        $message = 16;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                default							:
                    $perpage = (isset($_COOKIE[$this -> pre . 'groupsperpage'])) ? (int) $_COOKIE[$this -> pre . 'groupsperpage'] : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> groups . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    $data = array();
                    if (!empty($_GET['showall'])) {
                        $groups = $this -> Group() -> find_all(false, "*", $order);
                        $data[$this -> Group() -> model] = $groups;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Group() -> model, null, $this -> sections -> groups, $conditions, $searchterm, $perpage, $order);
                    }
                    if(!empty($data)) {
                    $this -> render('groups' . DS . 'index', array('groups' => $data[$this -> Group() -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    }
                    else {
                        $this -> render('groups' . DS . 'index', array( ), true, 'admin');
                    }

                    break;
            }
        }

        function admin_subscribers() {
            global $wpdb, $Html, $Db, $Email, $Field, $Subscriber, $Unsubscribe, $Bounce, $SubscribersList, $Mailinglist;
            $Db -> model = $Subscriber -> model;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'save'			:
                    if (!empty($_POST)) {
                        // Remove unchecked subscriptions
                        $subscriber_id = (int) sanitize_text_field(wp_unslash($_POST['Subscriber']['id']));
                        if (!empty($subscriber_id)) {
                            $subscriber = $Subscriber -> get($subscriber_id);
                            $mailinglists = array_map('sanitize_text_field', $_POST['Subscriber']['mailinglists']);

                            if (!empty($subscriber -> mailinglists)) {
                                foreach ($subscriber -> mailinglists as $mid) {
                                    if (empty($mailinglists) || !in_array($mid, $mailinglists)) {
                                        if (!empty($mid)) {
                                            $SubscribersList -> delete_all(array('subscriber_id' => $subscriber_id, 'list_id' => $mid));
                                        }
                                    }
                                }
                            }
                        }

                        $Db -> model = $Field -> model;
                        $conditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
                        if ($fields = $Db -> find_all($conditions)) {
                            foreach ($fields as $field) {
                                if (!empty($_POST[$field -> slug])) {
                                    if (is_array($_POST[$field -> slug])) {
                                        foreach (map_deep(wp_unslash($_POST[$field -> slug]), 'sanitize_text_field') as $fkey => $fval) {
                                            $_POST['Subscriber'][$field -> slug][$fkey] = $fval;
                                        }
                                    } else {
                                        $_POST['Subscriber'][$field -> slug] = sanitize_text_field(wp_unslash($_POST[$field -> slug]));
                                    }
                                }
                            }
                        }

                        if ($Subscriber -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'), true, false, false, true)) {
                            $message = 8;

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=save&id=' . $Subscriber -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect($this -> url, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Subscriber could not be saved', 'wp-mailinglist'));
                            $this -> render('subscribers' . DS . 'save', array('subscriber' => $this -> init_class('wpmlSubscriber', $this -> Subscriber -> data), 'errors' => $this -> Subscriber -> error), true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                        $Subscriber -> get($id);
                        if (!empty($_GET['mailinglist_id'])) {
                            if (empty($Subscriber -> data)) {
                                $Subscriber -> data = new stdClass();
                            }

                            $Subscriber -> data -> mailinglists = array(esc_html($_GET['mailinglist_id']));
                        }

                        $this -> render('subscribers' . DS . 'save', false, true, 'admin');
                    }
                    break;
                case 'view'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($subscriber = $Subscriber -> get($id)) {
                            $Db -> model = $this -> Order() -> model;
                            $orders = $Db -> find_all(array('subscriber_id' => $subscriber -> id));
                            $conditions[$wpdb -> prefix . $Email -> table . '.subscriber_id'] = $subscriber -> id;
                            $order = array($wpdb -> prefix . $Email -> table . ".modified", "DESC");
                            $data = $this -> paginate($Email -> model, false, $this -> sections -> subscribers . '&method=view&id=' . $subscriber -> id, $conditions, false, 15, $order);
                            if(!empty($data)) {
                            $this -> render('subscribers' . DS . 'view', array('subscriber' => $subscriber, 'orders' => $orders, 'emails' => $data[$Email -> model], 'paginate' => $data['Paginate']), true, 'admin');
                            }
                            else{
                                $this -> render('subscribers' . DS . 'view', array('subscriber' => $subscriber, 'orders' => $orders, 'emails' => array(), 'paginate' => array()), true, 'admin');
                            }
                        } else {
                            $message = __('Subscriber cannot be read', 'wp-mailinglist');
                            $this -> redirect($this -> url, 'error', $message);
                        }
                    } else {
                        $message = __('No subscriber was specified', 'wp-mailinglist');
                        $this -> redirect($this -> url, 'error', $message);
                    }
                    break;
                case 'unsubscribe'	:
                    $subscriber_id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($subscriber_id) && $subscriber = $Subscriber -> get($subscriber_id)) {
                        if ($this -> process_unsubscribe($subscriber, false, false)) {
                            $message_type = 'message';
                            $message = __('Unsubscribe successful', 'wp-mailinglist');
                        } else {
                            $message_type = 'error';
                            $message = __('Unsubscribe could not be processed', 'wp-mailinglist');
                        }
                    } else {
                        $message_type = 'error';
                        $message = __('No subscriber was specified', 'wp-mailinglist');
                    }

                    $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> subscribers), $message_type, $message);
                    break;
                case 'delete'		:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Subscriber -> model;

                        if ($Db -> delete($id)) {
                            $message_type = 'message';
                            $message = __('Subscriber has been removed', 'wp-mailinglist');
                        } else {
                            $message_type = 'error';
                            $message = __('Subscriber cannot be removed', 'wp-mailinglist');
                        }
                    } else {
                        $message_type = 'error';
                        $message = __('No subscriber was specified', 'wp-mailinglist');
                    }

                    $this -> redirect('?page=' . $this -> sections -> subscribers, $message_type, $message);
                    break;
                case 'mass'			:
                    if (!empty($_POST['subscriberslist'])) {
                        if (!empty($_POST['action'])) {
                            $subscribers = array_map('sanitize_text_field', $_POST['subscriberslist']);

                            switch ($_POST['action']) {
                                case 'assignlists'		:
                                    if (!empty($_POST['lists'])) {
                                        foreach ($subscribers as $subscriber_id) {
                                            foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                $Db -> model = $Mailinglist -> model;

                                                if ($mailinglist = $Db -> find(array('id' => $list_id))) {
                                                    $sl = array('subscriber_id' => $subscriber_id, 'list_id' => $list_id, 'active' => "Y");

                                                    if ($mailinglist -> paid == "Y") {
                                                        $sl['paid'] = "Y";
                                                    }

                                                    $SubscribersList -> save($sl, true);
                                                }
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected mailing lists have been appended to the subscribers', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No mailing lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'setlists'			:
                                    if (!empty($_POST['lists'])) {
                                        foreach ($subscribers as $subscriber_id) {
                                            $SubscribersList -> delete_all(array('subscriber_id' => $subscriber_id));

                                            foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                $Db -> model = $Mailinglist -> model;

                                                if ($mailinglist = $Db -> find(array('id' => $list_id))) {
                                                    $sl = array('subscriber_id' => $subscriber_id, 'list_id' => sanitize_text_field($list_id), 'active' => "Y");

                                                    if ($mailinglist -> paid == "Y") {
                                                        $sl['paid'] = "Y";
                                                    }

                                                    $SubscribersList -> save($sl, true);
                                                }
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected mailing lists have been assigned to the subscribers', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No mailing lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'dellists'			:
                                    if (!empty($_POST['lists'])) {
                                        foreach ($subscribers as $subscriber_id) {
                                            foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                $SubscribersList -> delete_all(array('subscriber_id' => (int) $subscriber_id, 'list_id' => (int) sanitize_text_field($list_id)));
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected lists removed from subscriber', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'getcountry'		:
                                    foreach ($subscribers as $subscriber_id) {
                                        if ($subscriber = $Subscriber -> get($subscriber_id)) {
                                            if (!empty($subscriber -> ip_address) && empty($subscriber -> country)) {
                                                if ($country = $this -> get_country_by_ip($subscriber -> ip_address)) {
                                                    $Subscriber -> save_field('country', $country, $subscriber_id);
                                                }
                                            }
                                        }
                                    }

                                    $msg_type = 'message';
                                    $message = __('Countries fetched by IP addresses', 'wp-mailinglist');
                                    break;
                                case 'delete'			:
                                    foreach ($subscribers as $subscriber_id) {
                                        $Db -> model = $Subscriber -> model;
                                        $Db -> delete($subscriber_id);
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                                case 'unsubscribe'		:
                                    foreach ($subscribers as $subscriber_id) {
                                        if ($subscriber = $Subscriber -> get($subscriber_id)) {
                                            $this -> process_unsubscribe($subscriber, false, false);
                                        }
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected subscribers unsubscribed.', 'wp-mailinglist');
                                    break;
                                case 'mandatory'		:
                                case 'notmandatory'		:
                                    $mandatory = ($_POST['action'] == "mandatory") ? "Y" : "N";
                                    foreach ($subscribers as $subscriber_id) {
                                        $Db -> model = $Subscriber -> model;
                                        $Db -> save_field('mandatory', $mandatory, array('id' => $subscriber_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Mandatory status has been changed', 'wp-mailinglist');
                                    break;
                                case 'html'				:
                                case 'text'				:

                                    $format = ($_POST['action'] == "html") ? 'html' : 'text';

                                    foreach ($subscribers as $subscriber_id) {
                                        $Db -> model = $Subscriber -> model;
                                        $Db -> save_field('format', $format, array('id' => $subscriber_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Format has been set', 'wp-mailinglist');
                                    break;
                                case 'active'			:
                                    if (!empty($subscribers)) {
                                        foreach ($subscribers as $subscriber_id) {
                                            $Db -> model = $SubscribersList -> model;
                                            $Db -> save_field('active', "Y", array('subscriber_id' => $subscriber_id));
                                        }
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected subscribers set as active', 'wp-mailinglist');
                                    break;
                                case 'inactive'			:
                                    foreach ($subscribers as $subscriber_id) {
                                        $Db -> model = $SubscribersList -> model;
                                        $Db -> save_field('active', "N", array('subscriber_id' => $subscriber_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected subscribers deactivated', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 17;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 16;
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'check-bounced'    :
                    $bounce_results = $this -> bounce(false, "pop");
                    $bounce_message = "";
                    $bounce_message .= $bounce_results[0] . " ";
                    $bounce_message .= __('subscribers', 'wp-mailinglist') . ' ';
                    $bounce_message .= __('and', 'wp-mailinglist') . ' ';
                    $bounce_message .= $bounce_results[1] . " ";
                    $bounce_message .= __('bounced emails were removed.', 'wp-mailinglist');
                    $this -> redirect($this -> referer, 'message', $bounce_message);
                    break;
                case 'check-expired'	:
                    global $SubscribersList;
                    $subscriber_id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($subscriber_id) && !empty($_GET['list_id'])) {
                        $list_id = sanitize_text_field(wp_unslash($_GET['list_id']));

                        $updated = $SubscribersList -> check_expirations(false, false, true, $subscriber_id, $list_id, true);
                        $message = __('Expiration email has been sent to subscriber.', 'wp-mailinglist');
                    } else {
                        $updated = $SubscribersList -> check_expirations();
                        $message = sprintf(__('%s subscriptions have been deactivated due to expiration or max emails sent.', 'wp-mailinglist'), $updated);
                    }
                    $this -> redirect("?page=" . $this -> sections -> subscribers, 'message', $message);
                    break;
                case 'bouncemass'				:
                    global $wpdb, $Db, $Subscriber, $Bounce;

                    if (!empty($_POST['action'])) {
                        if (!empty($_POST['bounces'])) {
                            switch ($_POST['action']) {
                                case 'delete'					:
                                    foreach (map_deep(wp_unslash($_POST['bounces']), 'sanitize_text_field') as $bounce_id) {
                                        $Db -> model = $Bounce -> model;
                                        $Db -> delete($bounce_id);
                                    }

                                    $msgtype = 'message';
                                    $message = 13;
                                    break;
                                case 'deletesubscribers'		:
                                    foreach (map_deep(wp_unslash($_POST['bounces']), 'sanitize_text_field') as $bounce_id) {
                                        $query = "SELECT `email` FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `id` = '" . esc_sql($bounce_id) . "'";
                                        if ($email = $wpdb -> get_var($query)) {
                                            $Db -> model = $Subscriber -> model;
                                            if ($subscriber_id = $Db -> field('id', array('email' => $email))) {
                                                $Db -> model = $Subscriber -> model;
                                                $Db -> delete($subscriber_id);
                                            }
                                        }
                                    }

                                    $msgtype = 'message';
                                    $message = 14;
                                    break;
                            }
                        } else {
                            $msgtype = 'error';
                            $message = 12;
                        }
                    } else {
                        $msgtype = 'error';
                        $message = 11;
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);

                    break;
                case 'bouncedelete'				:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Bounce -> model;
                        if ($Db -> delete($id)) {
                            $msgtype = 'message';
                            $message = __('Bounce was deleted', 'wp-mailinglist');
                        } else {
                            $msgtype = 'error';
                            $message = __('Bounce cannot be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No bounce was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);

                    break;
                case 'bounces'					:
                    $bounces_table = $wpdb -> prefix . $Bounce -> table;
                    $sections = $this -> sections -> subscribers . '&method=bounces';
                    $conditions = false;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;
                    $perpage = (isset($_COOKIE[$this -> pre . 'bouncesperpage'])) ? (int) $_COOKIE[$this -> pre . 'bouncesperpage'] : 15;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    $conditions = (!empty($searchterm)) ? array($bounces_table . '.email' => "LIKE '%" . esc_sql($searchterm) . "%'", $bounces_table . '.status' => "LIKE '%" . esc_sql($searchterm) . "%'") : false;

                    $conditions_and = false;

                    if (!empty($_GET['filter'])) {
                        if (!empty($_GET['history_id'])) {
                            $conditions_and[$bounces_table . '.history_id'] = "IN (" . implode(",", (int) $_GET['history_id']) . ")";
                        }
                    } else {
                        if (!empty($_GET['history_id'])) {
                            $conditions[$bounces_table . '.history_id'] = sanitize_text_field(wp_unslash($_GET['history_id']));
                        }
                    }

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Bounce -> model;
                        $bounces = $Db -> find_all(false, "*", $order);
                        $data[$Bounce -> model] = $bounces;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($Bounce -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                        $bounces = $data[$Bounce -> model];
                    }

                    $this -> render('subscribers' . DS . 'bounces', array('bounces' => $bounces, 'paginate' => $data['Paginate']), true, 'admin');
                    break;
                case 'unsubscribes'				:
                    $unsubscribes_table = $wpdb -> prefix . $Unsubscribe -> table;
                    $sections = $this -> sections -> subscribers . '&method=unsubscribes';
                    $conditions = false;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;
                    $perpage = (isset($_COOKIE[$this -> pre . 'unsubscribesperpage'])) ? (int) ($_COOKIE[$this -> pre . 'unsubscribesperpage']) : 15;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    $conditions = (!empty($searchterm)) ? array($unsubscribes_table . '.email' => "LIKE '%" . esc_sql($searchterm) . "%'") : false;

                    if (!empty($_GET['history_id'])) {
                        $conditions[$unsubscribes_table . '.history_id'] = sanitize_text_field(wp_unslash($_GET['history_id']));
                    }

                    $conditions_and = false;

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Unsubscribe -> model;
                        $unsubscribes = $Db -> find_all(false, "*", $order);
                        $data[$Unsubscribe -> model] = $unsubscribes;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($Unsubscribe -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                        $unsubscribes = $data[$Unsubscribe -> model];
                    }

                    $this -> render('subscribers' . DS . 'unsubscribes', array('unsubscribes' => $unsubscribes, 'paginate' => $data['Paginate']), true, 'admin');
                    break;
                case 'unsubscribedelete'		:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Unsubscribe -> model;

                        if ($Db -> delete($id)) {
                            $msgtype = 'message';
                            $message = __('Unsubscribe has been deleted', 'wp-mailinglist');
                        } else {
                            $msgtype = 'error';
                            $message = __('Unsubscribe could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No unsubscribe was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);
                    break;
                case 'unsubscribemass'			:
                    if (!empty($_POST['action'])) {
                        $action = sanitize_text_field(wp_unslash($_POST['action']));
                        $unsubscribes = array_map('sanitize_text_field', $_POST['unsubscribes']);

                        if (!empty($unsubscribes)) {
                            switch ($action) {
                                case 'delete'				:
                                    foreach ($unsubscribes as $unsubscribe_id) {
                                        $Db -> model = $Unsubscribe -> model;
                                        $Db -> delete($unsubscribe_id);
                                    }

                                    $msgtype = 'message';
                                    $message = __('Selected unsubscribes deleted', 'wp-mailinglist');
                                    break;
                                case 'deletesubscribers'	:
                                    foreach ($unsubscribes as $unsubscribe_id) {
                                        $Db -> model = $Unsubscribe -> model;
                                        $subscriber_id = $Db -> field('subscriber_id', array('id' => $unsubscribe_id));

                                        if (!empty($subscriber_id)) {
                                            $Db -> model = $Subscriber -> model;
                                            $Db -> delete($subscriber_id);
                                        }
                                    }

                                    $msgtype = 'message';
                                    $message = __('Subscribers of the selected unsubscribes have been deleted', 'wp-mailinglist');
                                    break;
                                case 'deleteusers'			:
                                    foreach ($unsubscribes as $unsubscribe_id) {
                                        $Db -> model = $Unsubscribe -> model;
                                        $user_id = $Db -> field('user_id', array('id' => $unsubscribe_id));

                                        if (!empty($user_id)) {
                                            wp_delete_user($user_id);
                                        }
                                    }

                                    $msgtype = 'message';
                                    $message = __('Users of the selected unsubscribes have been deleted', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msgtype = 'error';
                            $message = __('No unsubscribes were selected', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No action was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);
                    break;
                case 'deleteuser'				:
                    if (!empty($_GET['user_id'])) {
                        if (wp_delete_user((int) $_GET['user_id'])) {
                            $msgtype = 'message';
                            $message = __('User has been deleted', 'wp-mailinglist');
                        } else {
                            $msgtype = 'error';
                            $message = __('User could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No user was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);
                    break;
                default			:
                    $oldperpage = 15;

                    // screen options changes?
                    if (!empty($_POST['screenoptions'])) {
                        if (!empty($_POST['fields']) && is_array($_POST['fields'])) {
                            $this -> update_option('screenoptions_subscribers_fields', map_deep(wp_unslash($_POST['fields']), 'sanitize_text_field'));
                        } else { delete_option($this -> pre . 'screenoptions_subscribers_fields'); }

                        if (!empty($_POST['custom']) && is_array($_POST['custom'])) {
                            $this -> update_option('screenoptions_subscribers_custom', map_deep(wp_unslash($_POST['custom']), 'sanitize_text_field'));
                        } else { delete_option($this -> pre . 'screenoptions_subscribers_custom'); }

                        if (!empty($_POST['perpage'])) {
                            $oldperpage = sanitize_text_field(wp_unslash($_POST['perpage']));
                        }
                    }

                    $perpage = (isset($_COOKIE[$this -> pre . 'subscribersperpage'])) ? (int) $_COOKIE[$this -> pre . 'subscribersperpage'] : $oldperpage;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> subscribers . '_search');
                        $searchurl = $Html -> retainquery($this -> pre . 'page=1&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                        $this -> redirect($searchurl);
                    } elseif (isset($_POST['searchterm'])) {
                        $this -> redirect($Html -> retainquery($this -> pre . 'page=1&' . $this -> pre . 'searchterm='));
                    }

                    $subscribers_table = $wpdb -> prefix . $Subscriber -> table;
                    $subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;

                    $conditions = (!empty($searchterm)) ? array($subscribers_table . '.email' => "LIKE '%" . esc_sql($searchterm) . "%'") : false;

                    if (!empty($searchterm)) {
                        $Db -> model = $Field -> model;
                        $fieldsconditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
                        if ($fields = $Db -> find_all($fieldsconditions)) {
                            if (empty($conditions) || !is_array($conditions)) { $conditions = array(); }
                            foreach ($fields as $field) {
                                $conditions[$subscribers_table . "." . $field -> slug] = "LIKE '%" . esc_sql($searchterm) . "%'";
                            }
                        }
                    }

                    $dojoin = false;
                    $sections = $this -> sections -> subscribers;
                    $conditions_and = array();

                    $newsletters_filter_subscribers = (!empty($_GET['filter']) || (!empty($_COOKIE['newsletters_filter_subscribers']))) ? true : false;

                    if (!empty($newsletters_filter_subscribers)) {
                        $sections .= '&filter=1';

                        //** list filter
                        $newsletters_filter_subscribers_list = (!empty($_GET['list'])) ? sanitize_text_field(wp_unslash($_GET['list'])) : false;
                        $newsletters_filter_subscribers_list = (!empty($_COOKIE['newsletters_filter_subscribers_list'])) ? sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_list'])) : $newsletters_filter_subscribers_list;

                        if (!empty($newsletters_filter_subscribers_list)) {
                            switch ($newsletters_filter_subscribers_list) {
                                case 'all'				:
                                    $dojoin = false;
                                    break;
                                case 'none'				:
                                    $dojoin = false;
                                    $conditions_and[$subscribers_table . '.id'] = "NOT IN (SELECT subscriber_id FROM " . $subscriberslists_table . ")";
                                    break;
                                default					:
                                    $dojoin = true;
                                    $conditions_and[$subscriberslists_table . '.list_id'] = $newsletters_filter_subscribers_list;
                                    break;
                            }

                            $sections .= '&list=' . $newsletters_filter_subscribers_list;
                        }

                        //** expired filter
                        $newsletters_filter_subscribers_expired = (!empty($_COOKIE['newsletters_filter_subscribers_expired'])) ? sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_expired'])) : false;
                        $newsletters_filter_subscribers_expired = (!empty($_GET['expired'])) ? sanitize_text_field(wp_unslash($_GET['expired'])) : $newsletters_filter_subscribers_expired;

                        if (!empty($newsletters_filter_subscribers_expired)) {
                            if ($newsletters_filter_subscribers_expired != "all") {
                                $expired = ($newsletters_filter_subscribers_expired == "expired") ? "SE " . $Html -> gen_date("Y-m-d") : "LE " . $Html -> gen_date("Y-m-d");
                                $conditions_and[$subscriberslists_table . '.expiry_date'] = $expired;
                                $dojoin = true;
                            }

                            $sections .= '&expired=' . $newsletters_filter_subscribers_expired;
                        }

                        //** status filter (active/inactive)
                        $newsletters_filter_subscribers_status = (!empty($_COOKIE['newsletters_filter_subscribers_status'])) ? sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_status'])) : false;
                        $newsletters_filter_subscribers_status = (!empty($_GET['status'])) ? sanitize_text_field(wp_unslash($_GET['status'])) : $newsletters_filter_subscribers_status;

                        if (!empty($newsletters_filter_subscribers_status)) {
                            if ($newsletters_filter_subscribers_status != "all") {
                                $status = ($newsletters_filter_subscribers_status == "active") ? "Y" : "N";
                                $conditions_and[$subscriberslists_table . '.active'] = $status;
                                $dojoin = true;
                            }

                            $sections .= '&status=' . $newsletters_filter_subscribers_status;
                        }

                        //** registered filter
                        $newsletters_filter_subscribers_registered = (empty($_GET['registered'])) ? (isset($_COOKIE['newsletters_filter_subscribers_registered']) ?  $_COOKIE['newsletters_filter_subscribers_registered'] : '' ) : esc_html($_GET['registered']);

                        if (!empty($newsletters_filter_subscribers_registered) && $newsletters_filter_subscribers_registered != "all") {
                            $conditions_and[$subscribers_table . '.registered'] = $newsletters_filter_subscribers_registered;

                            $sections .= '&registered=' . $newsletters_filter_subscribers_registered;
                        }

                        //** country filter
                        $newsletters_filter_subscribers_country = (empty($_GET['country'])) ? (isset($_COOKIE['newsletters_filter_subscribers_country']) ? $_COOKIE['newsletters_filter_subscribers_country'] : '') : esc_html($_GET['country']);
                        $newsletters_filter_subscribers_country = (empty($newsletters_filter_subscribers_country)) ? 'all' : $newsletters_filter_subscribers_country;

                        if ($newsletters_filter_subscribers_country == "none") {
                            $newsletters_filter_subscribers_country = '';
                        }

                        if ($newsletters_filter_subscribers_country != "all") {
                            $conditions_and[$subscribers_table . '.country'] = $newsletters_filter_subscribers_country;
                            $sections .= '&country=' . $newsletters_filter_subscribers_country;
                        }
                    }

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    $data = array();
                    $Subscriber -> recursive = true;

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Subscriber -> model;
                        $subscribers = $Db -> find_all(false, "*", $order);
                        $data[$Subscriber -> model] = $subscribers;
                        $data['Paginate'] = false;
                    } else {
                        if ($dojoin) {
                            $data = $this -> paginate($SubscribersList -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                            $subscribers = $data[$SubscribersList -> model];
                        } else {
                            $data = $this -> paginate($Subscriber -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
                            if(!empty($data)) {$subscribers = $data[$Subscriber -> model];}
                            else {$subscribers = array();}
                        }
                    }

                    $this -> render('subscribers' . DS . 'index', array('subscribers' => $subscribers, 'paginate' =>  ( !empty($data) ? $data['Paginate'] :  array())), true, 'admin');
                    break;
            }
        }

        function admin_importexport() {
            global $wpdb, $Db, $Html, $Field, $Subscriber, $Unsubscribe, $Bounce, $SubscribersList, $Mailinglist;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");

            $mailinglist = array();
            $mailinglists = array();

            if (!empty($_POST)) {
                $this -> remove_server_limits();
                define('NEWSLETTERS_IMPORTING', true);

                switch ($method) {
                    case 'delete'			:
                        $uploadedfile = map_deep(wp_unslash($_FILES['file']), 'sanitize_text_field');

                        $upload_overrides = array(
                            'test_form' 	=> 	false,
                            'test_type'		=>	false,
                            'ext'			=>	'csv',
                            'type'			=>	'text/csv',
                        );

                        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                        if ($movefile && !isset($movefile['error'])) {
                            $csvtypes = array('text/comma-separated-values', 'data/csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/octet-stream', 'application/vnd.msexcel', 'text/anytext', 'text/plain');
                            $filetype = wp_check_filetype($movefile['file']);
                            $delimiter = $Html -> detectDelimiter($movefile['file']);

                            if (!empty($filetype['type']) && in_array($filetype['type'], $csvtypes)) {
                                if (($fh = fopen($movefile['file'], "r")) !== false) {
                                    $deleted = 0;
                                    while (($row = fgetcsv($fh, 1000, $delimiter)) !== false && $done <= $rows) {
                                        $email = $row[0];
                                        if (!empty($email) && $Subscriber -> email_validate($email)) {
                                            if ($subscriber = $Subscriber -> get_by_email($email)) {
                                                $Db -> model = $Subscriber -> model;
                                                $Db -> delete($subscriber -> id);
                                                $deleted++;
                                            }
                                        }
                                    }

                                    $message = sprintf(__('%s subscribers deleted', 'wp-mailinglist'), $deleted);
                                    $this -> render_message($message);
                                } else {
                                    $this -> render_error(__('Filel cannot be read', 'wp-mailinglist'));
                                }
                            } else {
                                $this -> render_error(__('File type is not allowed', 'wp-mailinglist'));
                            }
                        } else {
                            $this -> render_error($movefile['error']);
                        }

                        $this -> render('import-export', array('mailinglists' => $mailinglists), true, 'admin');

                        break;
                    case 'import'			:
                        check_admin_referer($this -> sections -> importexport . '_import');

                        if (empty($_POST['uploadedfile'])) { $error['file'] = __('No file selected for uploading', 'wp-mailinglist'); }
                        if (empty($_POST['filetype'])) { $error['filetype'] = __('No file type has been selected', 'wp-mailinglist'); }

                        if (empty($error)) {
                            $numberimported = 0;
                            $numberupdated = 0;
                            $numbernotimported = 0;
                            $errors = array();
                            $datasets = array();

                            $fileFull = sanitize_text_field(wp_unslash($_POST['uploadedfile']));

                            if (!empty($_POST['columns'])) {
                                foreach (map_deep(wp_unslash($_POST['columns']), 'sanitize_text_field') as $column => $field) {
                                    $structure[$field] = (!empty($field)) ? (int) ($column - 1) : false;
                                }
                            }

                            if ($fh = fopen($fileFull, "r")) {
                                $csvdelimiter = $this -> get_option('csvdelimiter');
                                $delimiter = (empty($_POST['delimiter'])) ? $csvdelimiter :  sanitize_text_field(wp_unslash($_POST['delimiter']));
                                $d = 0;
                                $i_queries = array();
                                $import_progress = (empty($_POST['import_progress']) || $_POST['import_progress'] == "N") ? false : true;
                                $import_preventbu = (empty($_POST['import_preventbu'])) ? false : true;
                                $import_overwrite = (empty($_POST['import_overwrite'])) ? false : true;

                                if ((!empty($_POST['activation']) && $_POST['activation'] == "Y")) {
                                    $confirmation_subject = (empty($_POST['confirmation_subject'])) ? $this -> get_option('etsubject_confirm') : wp_kses_post(wp_unslash($_POST['confirmation_subject']));
                                    $confirmation_email = (empty($_POST['confirmation_email'])) ? $this -> get_option('etmessage_confirm') : wp_kses_post(wp_unslash($_POST['confirmation_email']));
                                }

                                $afterlists = array();
                                if (!empty($_POST['importlists'])) {
                                    foreach (map_deep(wp_unslash($_POST['importlists']), 'sanitize_text_field') as $importlist_id) {
                                        $query = "SELECT `id`, `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . esc_sql($importlist_id) . "'";

                                        $query_hash = md5($query);
                                        if ($ob_mailinglist = $this -> get_cache($query_hash)) {
                                            $mailinglist = $ob_mailinglist;
                                        } else {
                                            $mailinglist = $wpdb -> get_row($query);
                                            $this -> set_cache($query_hash, $mailinglist);
                                        }

                                        if (!empty($mailinglist)) {
                                            $paid = (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? "N" : "Y";
                                            $active = (!empty($_POST['activation']) && $_POST['activation'] == "Y") ? "N" : "Y";
                                            $afterlists[] = array('id' => $importlist_id, 'paid' => $paid, 'active' => $active);
                                        }
                                    }
                                }

                                $skipsubscriberupdate = false;

                                $import_process_counter = 0;

                                while (($row = fgetcsv($fh, "1000", $delimiter)) !== false) {
                                    $this -> remove_server_limits();
                                    $datasets[$d] = array();

                                    if (!empty($row)) {
                                        $addlists = array();	//additional lists specified in the CSV
                                        $thisafterlists = $afterlists;
                                        $mailinglists = map_deep(wp_unslash($_POST['importlists']), 'sanitize_text_field');
                                        $email = $row[$structure['email']];
                                        $current_id = false;

                                        if (empty($import_progress)) {
                                            if ($current_id = $Subscriber -> email_exists($email)) {
                                                if (empty($import_overwrite) || $import_overwrite == false) {
                                                    $skipsubscriberupdate = true;
                                                } else {
                                                    $datasets[$d] = (array) $Subscriber -> get($current_id);
                                                    $skipsubscriberupdate = false;
                                                }
                                            } else {
                                                $skipsubscriberupdate = false;
                                            }
                                        }

                                        if ($_POST['filetype'] == "mac") {
                                            foreach ($structure as $skey => $sval) {
                                                if ($skey != "email") {
                                                    $_POST['fields'][$skey] = $sval;
                                                    $_POST[$skey . 'column'] = ($sval + 1);
                                                }
                                            }
                                        }

                                        $Db -> model = $Unsubscribe -> model;
                                        if ($import_progress == true || empty($import_preventbu) || ($import_preventbu == true && !$Db -> find(array('email' => $email)))) {
                                            $Db -> model = $Bounce -> model;
                                            if ($import_progress == true || empty($import_preventbu) || ($import_preventbu == true && !$Db -> find(array('email' => $email)))) {
                                                if (!empty($email)) {
                                                    if (!empty($_POST['fields']['mailinglists']) &&
                                                        $_POST['fields']['mailinglists'] == "Y" &&
                                                        !empty($_POST['mailinglistscolumn'])) {

                                                        // phpcs:ignore
                                                        $caddlists = $row[(int)($_POST['mailinglistscolumn'] - 1)];
                                                        if (($addlistsarr = explode(",", $caddlists)) !== false) {
                                                            foreach ($addlistsarr as $addlisttitle) {
                                                                $newaddlisttitle = trim($addlisttitle);
                                                                $addlists[] = $newaddlisttitle;

                                                                $slug = $Html -> sanitize($newaddlisttitle);
                                                                $checkquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `slug` = '" . $slug . "'";

                                                                if ($mailinglist_id = $wpdb -> get_var($checkquery)) {
                                                                    //do nothing, this list exists
                                                                } else {
                                                                    //we'll create the mailinglist
                                                                    if (!empty($_POST['autocreatemailinglists']) && $_POST['autocreatemailinglists'] == "Y") {
                                                                        $mailinglistdata = array(
                                                                            'title'					=>	$newaddlisttitle,
                                                                            'privatelist'			=>	"N",
                                                                            'group_id'				=>	0,
                                                                            'paid'					=>	"N",
                                                                        );

                                                                        if ($Mailinglist -> save($mailinglistdata, true)) {
                                                                            $mailinglist_id = $Mailinglist -> insertid;
                                                                        }
                                                                    }
                                                                }

                                                                if (!empty($mailinglist_id)) {
                                                                    if (empty($mailinglists) || (!empty($mailinglists) && !in_array($mailinglist_id, $mailinglists))) {
                                                                        $mailinglists[] = $mailinglist_id;

                                                                        $query = "SELECT `id`, `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . esc_sql($mailinglist_id) . "'";

                                                                        $query_hash = md5($query);
                                                                        if ($ob_mailinglist = $this -> get_cache($query_hash)) {
                                                                            $mailinglist = $ob_mailinglist;
                                                                        } else {
                                                                            $mailinglist = $wpdb -> get_row($query);
                                                                            $this -> set_cache($query_hash, $mailinglist);
                                                                        }

                                                                        if (!empty($mailinglist)) {
                                                                            $paid = (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? "N" : "Y";
                                                                            $active = (!empty($_POST['activation']) && $_POST['activation'] == "Y") ? "N" : "Y";
                                                                            $thisafterlists[] = array('id' => $mailinglist_id, 'paid' => $paid, 'active' => $active);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $datasets[$d]['id'] = ((empty($current_id)) ? false : $current_id);
                                                    $datasets[$d]['email'] = $email;
                                                    $datasets[$d]['active'] = ((empty($_POST['activation']) || (!empty($_POST['activation']) && $_POST['activation'] == "Y")) ? "N" : "Y");
                                                    $datasets[$d]['registered'] = $registered;
                                                    $datasets[$d]['user_id'] = $user_id;
                                                    $datasets[$d]['mailinglists'] = $mailinglists;
                                                    $datasets[$d]['afterlists'] = $thisafterlists;

                                                    if (!empty($_POST['preventautoresponders'])) {
                                                        $datasets[$d]['preventautoresponders'] = true;
                                                    }

                                                    if (!empty($_POST['columns'])) {
                                                        foreach (map_deep(wp_unslash($_POST['columns']), 'sanitize_text_field') as $column => $field) {
                                                            if (!empty($field)) {
                                                                if (!empty($import_overwrite) || empty($datasets[$d][$field])) {
                                                                    $datasets[$d][$field] = ($row[($column - 1)]);

                                                                    if (function_exists('mb_detect_encoding')) {
                                                                        $encoding = mb_detect_encoding($datasets[$d][$field], mb_detect_order(), true);
                                                                        if (($encoding == "ISO-8859-1" || $encoding == "ISO-8859-15") && function_exists('utf8_encode')) {
                                                                            $datasets[$d][$field] = utf8_encode($datasets[$d][$field]);
                                                                        } elseif ($encoding == "ASCII") {
                                                                            $datasets[$d][$field] = mb_convert_encoding($datasets[$d][$field], "UTF-8", "ASCII");
                                                                        } elseif (empty($encoding) || $encoding != "UTF-8") {
                                                                            $datasets[$d][$field] = utf8_encode($datasets[$d][$field]);
                                                                        }
                                                                    }

                                                                    switch ($field) {
                                                                        case 'created'					:
                                                                            $datasets[$d]['created'] = date_i18n("Y-m-d H:i:s", strtotime($datasets[$d]['created']));
                                                                            break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (empty($import_progress) || $import_progress == false) {
                                                        $datasets[$d]['justsubscribe'] = true;
                                                        $datasets[$d]['fromregistration'] = true;
                                                        $datasets[$d]['username'] = $email;

                                                        if (empty($skipsubscriberupdate) || (!empty($skipsubscriberupdate) && empty($id))) {
                                                            $item = array(
                                                                'subscriber'			=>	$datasets[$d],
                                                                'options'				=>	array(
                                                                    'skipsubscriberupdate'		=>	$skipsubscriberupdate,
                                                                    'confirmation'				=>	((!empty($_POST['activation']) && $_POST['activation'] == "Y") ? array(
                                                                        'confirmation_subject'		=>	$confirmation_subject,
                                                                        'confirmation_email'		=>	$confirmation_email,
                                                                    ) : false)
                                                                ),
                                                            );

                                                            $this -> import_process -> push_to_queue($item);

                                                            if (empty($datasets[$d]['id'])) {
                                                                $numberimported++;
                                                            } else {
                                                                if (empty($skipsubscriberupdate)) {
                                                                    $numberupdated++;
                                                                }
                                                            }

                                                            $import_process_counter++;
                                                            if ($import_process_counter >= $this -> import_process -> counter_reset) {
                                                                $this -> import_process -> save();
                                                                $this -> import_process -> reset_data();
                                                                $import_process_counter = 0;
                                                            }
                                                        }
                                                    }

                                                    $d++;
                                                }
                                            }
                                        }
                                    }
                                }

                                // Remove the uploaded file
                                $uploadedfile = sanitize_text_field(wp_unslash($_POST['uploadedfile']));
                                if (!unlink($uploadedfile)) {
                                    $this -> log_error(sprintf(__('Uploaded file could not be removed %s', 'wp-mailinglist'), $uploadedfile));
                                }

                                if (empty($import_progress) || $import_progress == false) {
                                    if (!empty($numberimported) || !empty($numberupdated) || !empty($numbernotimported)) {
                                        $this -> import_process -> save() -> dispatch();
                                        $this -> render_message($numberimported . ' ' . sprintf(__('subscribers successfully imported, %s updated and %s not imported.', 'wp-mailinglist'), $numberupdated, $numbernotimported));
                                    } else {
                                        $this -> render_error(__('No subscribers were imported, broken file? Change delimiter setting?', 'wp-mailinglist'));
                                    }

                                    $this -> render('import-export', array('mailinglists' => $mailinglists), true, 'admin');
                                } else {
                                    $this -> render('import-post', array('subscribers' => $datasets, 'confirmation_subject' => esc_html($confirmation_subject), 'confirmation_email' => $confirmation_email), true, 'admin');
                                }
                            } else {
                                /* CSV could not be read */
                                $this -> render_error(__('CSV file cannot be read', 'wp-mailinglist'));
                                $this -> render('import-export', array('mailinglists' => $mailinglists), true, 'admin');
                            }
                        } else {
                            $this -> render_error(9);
                            $this -> render('import-export', array('mailinglists' => $mailinglists, 'importerrors' => $error), true, 'admin');
                        }

                        break;
                    case 'export'				:
                        global $wpdb, $Db, $Html, $Subscriber, $SubscribersList, $Mailinglist, $Field;
                        $errors = false;

                        if (empty($_POST['export_lists']) || !is_array($_POST['export_lists'])) { $errors[] = __('Please select export list(s)', 'wp-mailinglist'); }
                        if (empty($_POST['export_filetype'])) { $errors[] = __('Please select an export filetype', 'wp-mailinglist'); }

                        $exportfilename = 'tribulant-'.'subscribers-export' . date_i18n("Y-m-d-H-i-s") . '.csv';
                        $exportfilepath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
                        $exportfilefull = $exportfilepath . $exportfilename;
                        @unlink($exportfilefull);

                        if (!$fh = fopen($exportfilefull, "w")) { $errors[] = sprintf(__('Export file could not be created, please check permissions on <b>%s</b> to make sure it is writable.', 'wp-mailinglist'), $Html -> uploads_path() . "/" . $this -> plugin_name . "/export/"); }
                        else { fclose($fh); }

                        @chmod($exportfilefull, 0755);

                        if (empty($errors)) {
                            $query = "";
                            $query .= "SELECT *, COUNT(" . $wpdb -> prefix . $Subscriber -> table . ".email) FROM `" . $wpdb -> prefix . $Subscriber -> table . "`";
                            $query .= " LEFT JOIN `" . $wpdb -> prefix . $SubscribersList -> table . "` ON";
                            $query .= " " . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id";

                            // phpcs:ignore
                            if (!empty($_POST['export_lists'])) {
                                $query .= " WHERE (";
                                $e = 1;

                                foreach (map_deep(wp_unslash($_POST['export_lists']), 'sanitize_text_field') as $list_id) {
                                    $query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($list_id) . "'";

                                    if ($e < count($_POST['export_lists'])) {
                                        $query .= " OR ";
                                    }

                                    $e++;
                                }

                                $query .= ")";
                            }

                            if (!empty($_POST['export_status']) && $_POST['export_status'] != "all") {
                                $active = ($_POST['export_status'] == "active") ? "Y" : "N";
                                $query .= " AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = '" . esc_sql($active) . "'";
                            }

                            $query .= " GROUP BY " . $wpdb -> prefix . $Subscriber -> table . ".email";
                            $subscribers = $wpdb -> get_results($query);

                            $datasets = array();
                            if (!empty($subscribers)) {
                                $d = 0;

                                $Db -> model = $Field -> model;
                                $fieldsconditions['1'] = "1 AND `slug` != 'email' AND `slug` != 'list'";
                                $fields = $Db -> find_all($fieldsconditions);

                                $csvdelimiter = $this -> get_option('csvdelimiter');
                                $delimiter = (!empty($_POST['export_delimiter'])) ? sanitize_text_field(wp_unslash($_POST['export_delimiter'])) : $csvdelimiter;

                                $headings = array();
                                $headings['id'] = __('Subscriber ID', 'wp-mailinglist');
                                $headings['email'] = __('Email Address', 'wp-mailinglist');
                                $headings['mailinglists'] = __('Mailing List/s', 'wp-mailinglist');

                                if (!empty($fields)) {
                                    foreach ($fields as $field) {
                                        $headings[$field -> slug] = esc_html($field -> title);
                                    }
                                }

                                $headings['ip_address'] = __('IP Address', 'wp-mailinglist');
                                $headings['referer'] = __('Referrer', 'wp-mailinglist');
                                $headings['created'] = __('Created', 'wp-mailinglist');
                                $headings['modified'] = __('Modified', 'wp-mailinglist');

                                foreach ($subscribers as $subscriber) {
                                    $datasets[$d] = array(
                                        'id'					=>	$subscriber -> id,
                                        'email'					=>	$subscriber -> email,
                                    );

                                    $mailinglists = '';
                                    if ($lists = $Subscriber -> mailinglists($subscriber -> id)) {
                                        $m = 1;
                                        foreach ($lists as $list_id) {
                                            $mailinglists .= $Mailinglist -> get_title_by_id($list_id);

                                            if ($m < count($lists)) {
                                                $mailinglists .= ',';
                                            }

                                            $m++;
                                        }
                                    }

                                    $datasets[$d]['mailinglists'] = $mailinglists;

                                    if (!empty($fields)) {
                                        foreach ($fields as $field) {
                                            if (!empty($field -> fieldoptions)) {
                                                $fieldoptions = $field -> newfieldoptions;
                                            }

                                            switch ($_POST['export_purpose']) {
                                                case 'other'				:
                                                    switch ($field -> type) {
                                                        case 'select'				:
                                                        case 'radio'				:
                                                            $datasets[$d][$field -> slug] = esc_html($fieldoptions[$subscriber -> {$field -> slug}]);
                                                            break;
                                                        case 'checkbox'				:
                                                            $checkboxes = array();
                                                            $supoptions = maybe_unserialize($subscriber -> {$field -> slug});
                                                            if (!empty($supoptions) && is_array($supoptions)) {
                                                                foreach ($supoptions as $subopt) {
                                                                    $checkboxes[] = esc_html($fieldoptions[$subopt]);
                                                                }
                                                            }

                                                            $datasets[$d][$field -> slug] = (!empty($checkboxes) && is_array($checkboxes)) ? implode(",", $checkboxes) : '';
                                                            break;
                                                        case 'pre_country'			:
                                                            $query = "SELECT `value` FROM " . $wpdb -> prefix . $this -> Country() -> table . " WHERE `id` = '" . $subscriber -> {$field -> slug} . "'";
                                                            $country = $wpdb -> get_var($query);

                                                            $datasets[$d][$field -> slug] = (!empty($country)) ? $country : '';
                                                            break;
                                                        case 'pre_date'				:
                                                            if (is_serialized($subscriber -> {$field -> slug})) {
                                                                $date = maybe_unserialize($subscriber -> {$field -> slug});
                                                                $datasets[$d][$field -> slug] = $date['y'] . '-' . $date['m'] . '-' . $date['d'];
                                                            } else {
                                                                $datasets[$d][$field -> slug] = date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug}));
                                                            }
                                                            break;
                                                        case 'pre_gender'			:
                                                            $datasets[$d][$field -> slug] = $Html -> gender($subscriber -> {$field -> slug});
                                                            break;
                                                        default						:
                                                            $datasets[$d][$field -> slug] = $subscriber -> {$field -> slug};
                                                            break;
                                                    }
                                                    break;
                                                case 'newsletters'			:
                                                default						:
                                                    switch ($field -> type) {
                                                        case 'select'				:
                                                        case 'radio'				:
                                                            if (!empty($subscriber -> {$field -> slug})) {
                                                                $datasets[$d][$field -> slug] = __($fieldoptions[$subscriber -> {$field -> slug}]);
                                                            }
                                                           break;
                                                        default						:
                                                            $datasets[$d][$field -> slug] = $subscriber -> {$field -> slug};
                                                            break;
                                                    }
                                                    break;
                                            }
                                        }
                                    }

                                    $datasets[$d]['ip_address'] = $subscriber -> ip_address;
                                    $datasets[$d]['referer'] = $subscriber -> referer;
                                    $datasets[$d]['created'] = $subscriber -> created;
                                    $datasets[$d]['modified'] = $subscriber -> modified;

                                    $d++;
                                }
                            }

                            if (!empty($_POST['export_progress']) && $_POST['export_progress'] == "Y") {
                                $fh = fopen($exportfilefull, "w");
                                fputcsv($fh, $headings, $delimiter, '"');
                                fclose($fh);

                                $this -> render('export-post', array('subscribers' => $datasets, 'headings' => $headings, 'exportfile' => $exportfilename, 'delimiter' => $delimiter), true, 'admin');
                            } else {
                                $fh = fopen($exportfilefull, "w");

                                $headings_keys = array();
                                foreach ($headings as $hkey => $hval) {
                                    $headings_keys[$hkey] = '';
                                }

                                $headings = apply_filters('newsletters_admin_subscribers_export_headings', $headings, $data);

                                fputcsv($fh, $headings, $delimiter, '"');

                                if (!empty($datasets)) {
                                    $datasets = apply_filters('newsletters_admin_subscribers_export_data', $datasets, $headings);

                                    foreach ($datasets as $dkey => $dval) {
                                        $datasets[$dkey] = array_merge($headings_keys, $datasets[$dkey]);
                                        fputcsv($fh, $datasets[$dkey], $delimiter, '"');
                                    }
                                }

                                fclose($fh);
                                @chmod($exportfull, 0755);

                                $this -> render('import-export', array('exportfile' => $exportfilename), true, 'admin');
                            }
                        } else {
                            $this -> render('import-export', array('exporterrors' => $errors), true, 'admin');
                        }
                        break;
                }
            } elseif ($method == "clear") {
                $this -> import_process -> cancel_all_processes();
                $this -> render_message(__('Import has been cancelled and stopped.', 'wp-mailinglist'));
                $this -> render('import-export', array('mailinglists' => $mailinglists), true, 'admin');
            } else {
                $this -> render('import-export', array('mailinglists' => $mailinglists), true, 'admin');
            }
        }

        function admin_themes() {
            global $wpdb, $Db, $Theme, $Html;
            $Db -> model = $Theme -> model;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");

            if ($this -> is_php_module('mod_security')) {
                $error = __('Please note that Apache mod_security is turned on. Saving a template may not be allowed due to the raw HTML. Please ask your hosting provider.', 'wp-mailinglist');
                $this -> render_error($error);
            }

            switch ($method) {
                case 'defaulttemplate'		:
                    if (empty($_POST['defaulttemplate'])) {
                        $this -> update_option('defaulttemplate', false);
                        $this -> redirect($this -> referer, 'message', __('Default template turned off', 'wp-mailinglist'));
                    } else {
                        $this -> update_option('defaulttemplate', true);
                        $this -> redirect($this -> referer, 'message', __('Default template turned on', 'wp-mailinglist'));
                    }
                    break;
                case 'save'			:
                    if (!empty($_POST)) {
                        //if ($Db -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                        //if ($Db -> save(map_deep($_POST, 'wp_kses_post'))) {
                        if ($Db -> save($_POST)) {
                            $message = __('Template has been saved', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> themes . '&method=save&id=' . $Theme -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> themes, 'message', $message));
                            }
                        } else {
                            $this -> render_error(__('Template could not be saved', 'wp-mailinglist'));
                            $this -> render('themes' . DS . 'save', false, true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                        $Db -> find(array('id' => $id));
                        if (!empty($Theme -> data -> content)) {
                            $Theme -> data -> paste = $Theme -> data -> content;
                        }
                        $this -> render('themes' . DS . 'save', false, true, 'admin');
                    }
                    break;
                case 'duplicate'	:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $query = "SHOW TABLE STATUS LIKE '" . $wpdb -> prefix . $Theme -> table . "'";
                        $tablestatus = $wpdb -> get_row($query);
                        $nextid = $tablestatus -> Auto_increment;
                        $query = "CREATE TEMPORARY TABLE `themetmp` SELECT * FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . esc_sql($id) . "'";
                        $wpdb -> query($query);
                        $query = "UPDATE `themetmp` SET `id` = '" . $nextid . "', `title` = CONCAT(title, ' " . __('Copy', 'wp-mailinglist') . "'), `def` = 'N', `defsystem` = 'N', `created` = '" . $Html -> gen_date() . "', `modified` = '" . $Html -> gen_date() . "' WHERE `id` = '" . esc_sql($id) . "'";
                        $wpdb -> query($query);
                        $query = "INSERT INTO `" . $wpdb -> prefix . $Theme -> table . "` SELECT * FROM `themetmp` WHERE `id` = '" . $nextid . "'";
                        $wpdb -> query($query);
                        $query = "DROP TEMPORARY TABLE `themetmp`;";
                        $wpdb -> query($query);

                        $msgtype = 'message';
                        $message = __('Template has been duplicated', 'wp-mailinglist');
                    } else {
                        $msgtype = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msgtype, $message);
                    break;
                case 'delete'		:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($Db -> delete($id)) {
                            $msgtype = 'message';
                            $message = __('Template has been removed', 'wp-mailinglist');
                        } else {
                            $msgtype = 'error';
                            $message = __('Template could not be removed', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect('?page=' . $this -> sections -> themes, $msgtype, $message);
                    break;
                case 'remove_default'					:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Theme -> model;
                        $Db -> save_field('def', "N", array('id' => $id));

                        $msg_type = 'message';
                        $message = __('Selected template removed as sending default', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
                    break;
                case 'remove_defaultsystem'				:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $Db -> model = $Theme -> model;
                        $Db -> save_field('defsystem', "N", array('id' => $id));

                        $msg_type = 'message';
                        $message = __('Selected template removed as system default', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
                    break;
                case 'default'		:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Theme -> model;
                        $Db -> save_field('def', "N");

                        $Db -> model = $Theme -> model;
                        $Db -> save_field('def', "Y", array('id' => $id));

                        $msg_type = 'message';
                        $message = __('Selected template has been set as the sending default', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
                    break;
                case 'defaultsystem'	:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $Theme -> model;
                        $Db -> save_field('defsystem', "N");

                        $Db -> model = $Theme -> model;
                        $Db -> save_field('defsystem', "Y", array('id' => $id));

                        $msg_type = 'message';
                        $message = __('Selected template has been set as the system default', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No template was specified', 'wp-mailinglist');
                    }

                    $this -> redirect("?page=" . $this -> sections -> themes, $msg_type, $message);
                    break;
                case 'mass'			:
                    if (!empty($_POST['action'])) {
                        $themes = array_map('sanitize_text_field', $_POST['themeslist']);

                        if (!empty($themes)) {
                            switch ($_POST['action']) {
                                case 'delete'				:
                                    foreach ($themes as $theme_id) {
                                        $Db -> model = $Theme -> model;
                                        $Db -> delete($theme_id);
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 17;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 16;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                default				:
                    $perpage = (empty($_COOKIE[$this -> pre . 'themesperpage'])) ? 15 : $_COOKIE[$this -> pre . 'themesperpage'];
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field($_GET[$this -> pre . 'searchterm']) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field($_POST['searchterm']) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' : esc_html($_GET['orderby']);
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(esc_html($_GET['order']));
                    $order = array($orderfield, $orderdirection);

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Theme -> model;
                        $themes = $Db -> find_all(false, "*", $order);
                        $data[$Theme -> model] = $themes;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($Theme -> model, null, $this -> sections -> themes, $conditions, $searchterm, $perpage, $order);
                    }

                    $this -> render('themes' . DS . 'index', array('themes' => $data[$Theme -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        function admin_templates() {
            global $wpdb, $Db;
            $Db -> model = $this -> Template() -> model;

            //get the page method
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $method = (empty($method)) ? preg_replace("/newsletters\-templates\-/si", "", esc_html($_GET['page'])) : $method;

            switch ($method) {
                case 'save'			:
                    $this -> render_message(__('Email Snippets are meant for content only, use the Templates for newsletter layouts.', 'wp-mailinglist'));

                    if (!empty($_POST)) {
                        if ($this -> Template() -> save($_POST)) {
                            $message = 10;

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> templates_save . '&continueediting=1&id=' . $this -> Template() -> insertid), 'message', $message);
                            } else {
                                $this -> redirect('?page=' . $this -> sections -> templates, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Snippet could not be saved', 'wp-mailinglist'));
                            $this -> render('templates' . DS . 'save', false, true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
                        $this -> Template() -> find(array('id' => $id));
                        $_POST['content'] = isset($this -> Template() -> data -> content) ? $this -> Template() -> data -> content : '';
                        $this -> render('templates' . DS . 'save', false, true, 'admin');
                    }
                    break;
                case 'delete'					:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($this -> Template() -> delete($id)) {
                            $message = __('Snippet has been removed', 'wp-mailinglist');
                            $this -> redirect($this -> url, 'message', $message);
                        }
                    } else {
                        $message = __('No snippet was specified', 'wp-mailinglist');
                        $this -> redirect($this -> url, 'error', $message);
                    }
                    break;
                case 'view'						:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        if ($template = $this -> Template() -> find(array('id' => $id))) {
                            $this -> render('templates' . DS . 'view', array('template' => $template), true, 'admin');
                        } else {
                            $message = __('Snippet cannot be read', 'wp-mailinglist');
                            $this -> redirect($this -> url, 'error', $message);
                        }
                    } else {
                        $message = __('No snippet was specified', 'wp-mailinglist');
                        $this -> redirect($this -> url, 'error', $message);
                    }
                    break;
                case 'mass'						:
                    if (!empty($_POST['action'])) {
                        if (!empty($_POST['templateslist'])) {
                            if ($this -> Template() -> delete_array(map_deep($_POST['templateslist'], 'sanitize_text_field'))) {
                                $msg_type = 'message';
                                $message = 18;
                            } else {
                                $msg_type = 'error';
                                $message = __('Snippets could not be removed', 'wp-mailinglist');
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 16;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 17;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                default 						:
                    $perpage = (empty($_COOKIE[$this -> pre . 'templatesperpage'])) ? 15 : $_COOKIE[$this -> pre . 'templatesperpage'];
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field($_GET[$this -> pre . 'searchterm']) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field($_POST['searchterm']) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('title' => "LIKE '%" . $searchterm . "%'") : false;
                    $conditions_and = false;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' : esc_html($_GET['orderby']);
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field($_GET['order']));
                    $order = array($orderfield, $orderdirection);

                    if (!empty($_GET['showall'])) {
                        $templates = $this -> Template() -> find_all(false, "*", $order);
                        $data[$this -> Template() -> model] = $templates;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Template() -> model, null, $this -> sections -> templates, $conditions, $searchterm, $perpage, $order, $conditions_and);
                    }

                    if(!empty($data)) {
                        $this->render('templates' . DS . 'index', array('templates' => $data[$this->Template()->model], 'paginate' => $data['Paginate']), true, 'admin');
                    }
                    else {
                        $this->render('templates' . DS . 'index', array('templates' => array(), 'paginate' => array()), true, 'admin');

                    }

                    break;
            }
        }

        function admin_mailqueue() {
            global $wpdb, $Html, $Db, $Email, $Subscriber;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'batchbulk'			:
                    $action = sanitize_text_field(wp_unslash($_POST['action']));
                    $batch = sanitize_text_field(wp_unslash($_POST['batch']));
                    $emails = map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field');

                    if (!empty($action)) {
                        if (!empty($emails)) {
                            switch ($action) {
                                case 'delete'				:
                                    if ($batch = $this -> qp_get_specific_batch($batch)) {
                                        foreach ($batch -> data as $key => $data) {
                                            if (in_array($key, $emails)) {
                                                unset($batch -> data[$key]);
                                            }
                                        }

                                        $this -> qp_update($batch -> key, $batch -> data);
                                        $this -> render_message(__('Emails were deleted', 'wp-mailinglist'));
                                    } else {
                                        $this -> render_error(__('Batch cannot be read', 'wp-mailinglist'));
                                    }
                                    break;
                            }
                        } else {
                            $this -> render_error(__('No emails were selected', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No action was specified', 'wp-mailinglist'));
                    }

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
                case 'deleteemail'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    $batch = array_map('sanitize_text_field', $_GET['batch']);

                    if (!empty($id) || $id == 0) {
                        if (!empty($batch)) {
                            if ($batch = $this -> qp_get_specific_batch($batch)) {
                                unset($batch -> data[$id]);

                                if (!empty($batch -> data)) {
                                    $this -> qp_update($batch -> key, $batch -> data);
                                } else {
                                    $this -> qp_delete($batch -> key);
                                }

                                $this -> render_message(__('Email was deleted', 'wp-mailinglist'));
                            } else {
                                $this -> render_error(__('Batch cannot be read', 'wp-mailinglist'));
                            }
                        } else {
                            $this -> render_error(__('No batch was specified', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No email was specified', 'wp-mailinglist'));
                    }

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
                case 'sendemail'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    $batch = sanitize_text_field( $_GET['batch']);

                    if (!empty($id) || $id == 0) {
                        if (!empty($batch)) {
                            if ($batch = $this -> qp_get_specific_batch($batch)) {
                                if ($this -> send_queued_email($batch -> data[$id])) {
                                    unset($batch -> data[$id]);

                                    if (!empty($batch -> data)) {
                                        $this -> qp_update($batch -> key, $batch -> data);
                                    } else {
                                        $this -> qp_delete($batch -> key);
                                    }

                                    $this -> render_message(__('Email was sent', 'wp-mailinglist'));
                                } else {
                                    global $mailerrors;
                                    $this -> render_error(sprintf(__('Email could not be sent: %s', 'wp-mailinglist'), strip_tags($mailerrors)));
                                }
                            } else {
                                $this -> render_error(__('Batch cannot be read', 'wp-mailinglist'));
                            }
                        } else {
                            $this -> render_error(__('No batch was specified', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No email was specified', 'wp-mailinglist'));
                    }

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
                case 'deletebatch'			:
                    $batch = esc_html($_GET['batch']);
                    if (!empty($batch)) {
                        $query = "DELETE FROM `" . $wpdb -> options . "` WHERE `option_name` = '" . esc_sql($batch) . "'";
                        $wpdb -> query($query);
                        $message = __('Batch has been deleted', 'wp-mailinglist');
                        $this -> log_error($message);
                        $this -> render_message($message);
                    } else {
                        $this -> render_error(__('No batch was specified', 'wp-mailinglist'));
                    }

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
                case 'sendbatch'			:
                    $batchkey = esc_html($_GET['batch']);
                    if (!empty($batchkey)) {
                        $batch = $this -> qp_get_specific_batch($batchkey);

                        if (!empty($batch -> data)) {
                            $successful = 0;

                            foreach ($batch -> data as $key => $value) {
                                $task = $this -> qp_do_specific_item($value, true);

                                if (false !== $task) {
                                    $batch -> data[$key] = $task;
                                } else {
                                    unset($batch -> data[$key]);
                                    $successful++;
                                }

                                $this -> qp_update($batch -> key, $batch -> data);
                            }

                            if (empty($batch -> data)) {
                                $this -> qp_delete($batchkey);
                            }

                            $message = sprintf(__('%s emails have been sent', 'wp-mailinglist'), $successful);
                            $this -> log_error($message);
                            $this -> render_message($message);
                        } else {
                            $this -> qp_delete($batchkey);
                            $this -> render_error(__('Batch has no data/emails', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No batch was specified', 'wp-mailinglist'));
                    }

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
                case 'unlock'				:

                    $this -> qp_unlock();
                    $message = __('The queue process was unlocked', 'wp-mailinglist');
                    $this -> redirect($this -> url, 'message', $message);

                    break;
                case 'clear'				:
                    $this -> qp_cancel_all_processes();
                    $message = __('The queue has been cleared', 'wp-mailinglist');
                    $this -> log_error($message);
                    $this -> redirect($this -> url, 'message', $message);
                    break;
                case 'errors'				:
                    $errors = $this -> qp_get_batches(false, true);
                    $this -> render('queues' . DS . 'errors', array('errors' => $errors), true, 'admin');
                    break;
                default						:

                    $this -> render('queues' . DS . 'index', false, true, 'admin');
                    break;
            }
        }

        function admin_emails() {
            // Coming soon...
        }

        function admin_history() {
            global $wpdb, $Db, $Html, $HistoriesList, $Email, $Mailinglist, $Subscriber, $SubscribersList;

            $emails_table = $wpdb -> prefix . $Email -> table;
            $subscribers_table = $wpdb -> prefix . $Subscriber -> table;
            $histories_table = $wpdb -> prefix . $this -> History() -> table;
            $clicks_table = $wpdb -> prefix . $this -> Click() -> table;

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $method = (empty($method)) ? false : $method;

            switch ($method) {
                case 'view'				:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($history = $this -> History() -> get($id)) {
                            $sections = $this -> sections -> history . '&method=view&id=' . $history -> id;

                            $conditions = array($wpdb -> prefix . $Email -> table . '.history_id' => $id);
                            $perpage = (isset($_COOKIE[$this -> pre . 'emailsperpage'])) ? (int) ($_COOKIE[$this -> pre . 'emailsperpage']) : 20;

                            $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                            $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));

                            switch ($orderfield) {
                                case 'clicked'						:
                                    $orderfield = "clicked";
                                    break;
                                case 'subscriber_id'				:
                                    $orderfield = $subscribers_table . ".email";
                                    break;
                                default 							:
                                    $orderfield = $emails_table . "." . $orderfield;
                                    break;
                            }

                            $order = array($orderfield, $orderdirection);

                            $conditions_and = array();
                            $dojoin = false;

                            if (!empty($_GET['filter'])) {
                                check_admin_referer($this -> sections -> history . '_filter');
                                $sections .= '&filter=1';

                                // status
                                if (!empty($_GET['status'])) {
                                    switch ($_GET['status']) {
                                        case 'all'				:
                                            $dojoin = false;
                                            break;
                                        case 'sent'				:
                                            $dojoin = false;
                                            $conditions_and[$emails_table . '.status'] = "sent";
                                            break;
                                        case 'unsent'			:
                                            $dojoin = false;
                                            $conditions_and[$emails_table . '.status'] = "unsent";
                                            break;
                                    }
                                }

                                // read
                                if (!empty($_GET['read'])) {
                                    switch ($_GET['read']) {
                                        case 'Y'			:
                                            $dojoin = false;
                                            $conditions_and[$emails_table . '.read'] = "Y";
                                            break;
                                        case 'N'			:
                                            $dojoin = false;
                                            $conditions_and[$emails_table . '.read'] = "N";
                                            break;
                                        case 'all'			:
                                        default 			:
                                            $dojoin = false;
                                            break;
                                    }
                                }

                                // clicked
                                if (!empty($_GET['clicked'])) {
                                    switch ($_GET['clicked']) {
                                        case 'Y'			:
                                            $conditions_and['clicked'] = "Y";
                                            break;
                                        case 'N'			:
                                            $conditions_and['clicked'] = "N";
                                            break;
                                        case 'all'			:
                                        default 			:
                                            //do nothing...
                                            break;
                                    }
                                }

                                if (!empty($_GET['bounced'])) {
                                    switch ($_GET['bounced']) {
                                        case 'Y'			:
                                            $conditions_and['bounced'] = "Y";
                                            $conditions_and[$emails_table . '.bounced'] = "Y";
                                            break;
                                        case 'N'			:
                                            $conditions_and['bounced'] = "N";
                                            $conditions_and[$emails_table . '.bounced'] = "N";
                                            break;
                                        case 'all'			:
                                        default 			:
                                            //do nothing...
                                            break;
                                    }
                                }
                            }

                            $data = $this -> paginate($Email -> model, $emails_table . ".*", $sections, $conditions, '', $perpage, $order, $conditions_and);
                            $this -> render('history' . DS . 'view', array('history' => $history, 'emails' => (isset($data[$Email -> model]) ?  $data[$Email -> model] : array()), 'paginate' => (isset($data['Paginate']) ? $data['Paginate'] : array())), true, 'admin');
                        } else {
                            $message = __('History email cannot be read', 'wp-mailinglist');
                            $this -> redirect($this -> url, 'error', $message);
                        }
                    } else {
                        $message = __('No history email was specified', 'wp-mailinglist');
                        $this -> redirect($this -> url, 'error', $message);
                    }
                    break;
                case 'archive'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> History() -> save_field('state', "archived", array('id' => $id))) {
                            $msgtype = 'message';
                            $message = __('Newsletter archived', 'wp-mailinglist');
                        } else {
                            $msgtype = 'error';
                            $message = __('Newsletter could not be archived', 'wp-mailinglist');
                        }
                    } else {
                        $msgtype = 'error';
                        $message = __('No newsletter was specified', 'wp-mailinglist');
                    }

                    $this -> redirect(false, $msgtype, $message);
                    break;
                case 'delete'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> History() -> delete($id)) {
                            $message = __('History email has been removed', 'wp-mailinglist');
                        } else {
                            $message = __('History email could not be removed', 'wp-mailinglist');
                        }
                    } else {
                        $message = __('No history email was specified', 'wp-mailinglist');
                    }

                    $this -> redirect(false, 'message', $message);
                    break;
                case 'duplicate'		:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {

                        $query = "SHOW TABLE STATUS LIKE '" . $wpdb -> prefix . $this -> History() -> table . "'";
                        $tablestatus = $wpdb -> get_row($query);
                        $nextid = $tablestatus -> Auto_increment;
                        $query = "CREATE TEMPORARY TABLE `historytmp` SELECT * FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . esc_sql($id) . "'";
                        $wpdb -> query($query);
                        $query = "UPDATE `historytmp` SET `id` = '" . $nextid . "', `spamscore` = '', `post_id` = '0', `scheduled` = 'N', `recurring` = 'N', `sent` = '0', `created` = '" . $Html -> gen_date() . "', `modified` = '" . $Html -> gen_date() . "' WHERE `id` = '" . esc_sql($id) . "'";
                        $wpdb -> query($query);
                        $query = "INSERT INTO `" . $wpdb -> prefix . $this -> History() -> table . "` SELECT * FROM `historytmp` WHERE `id` = '" . $nextid . "'";
                        $wpdb -> query($query);
                        $query = "UPDATE `" . $wpdb -> prefix . $this -> History() -> table . "` SET `subject` = concat(`subject`, ' " . esc_sql(__('Copy', 'wp-mailinglist')) . "') WHERE `id` = '" . $nextid . "'";
                        $wpdb -> query($query);
                        $query = "DROP TEMPORARY TABLE `historytmp`;";
                        $wpdb -> query($query);

                        // Are there content areas that need to be copied as well?
                        if ($contents = $this -> Content() -> find_all(array('history_id' => $id))) {
                            foreach ($contents as $content) {
                                $new_content = array(
                                    'history_id'			=>	$nextid,
                                    'content'				=>	$content -> content,
                                    'number'				=>	$content -> number
                                );

                                $this -> Content() -> data -> id = false;
                                $this -> Content() -> save($new_content);
                            }
                        }

                        $msgtype = 'message';
                        $message = __('History email has been duplicated', 'wp-mailinglist');
                    } else {
                        $msgtype = 'error';
                        $message = __('No history email was specified', 'wp-mailinglist');
                    }

                    $this -> redirect(false, $msgtype, $message);
                    break;
                case 'emails-mass'		:
                    check_admin_referer($this -> sections -> history . '_emails-mass');

                    if (!empty($_REQUEST['action'])) {
                        if (!empty($_REQUEST['emails'])) {
                            switch ($_REQUEST['action']) {
                                case 'subscribers_delete'		:
                                    foreach (map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field') as $email_id) {
                                        $Db -> model = $Email -> model;
                                        if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
                                            if (!empty($email -> subscriber_id)) {
                                                $Db -> model = $Subscriber -> model;
                                                $Db -> delete($email -> subscriber_id);
                                            }
                                        }
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected subscribers deleted', 'wp-mailinglist');
                                    break;
                                case 'subscribers_addlists'		:
                                    if (!empty($_POST['lists'])) {
                                        foreach (map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field') as $email_id) {
                                            $email_id = sanitize_text_field($email_id);
                                            $Db -> model = $Email -> model;
                                            if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
                                                foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                    $list_id = sanitize_text_field($list_id);
                                                    $sl_data = array(
                                                        'subscriber_id'			=>	(int) $email -> subscriber_id,
                                                        'list_id'				=>	(int) $list_id,
                                                        'active'				=>	"Y",
                                                    );

                                                    $SubscribersList -> save($sl_data, true);
                                                }
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected lists added to subscribers', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'subscribers_setlists'		:
                                    if (!empty($_POST['lists'])) {
                                        foreach (map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field') as $email_id) {
                                            $email_id = sanitize_text_field($email_id);
                                            $Db -> model = $Email -> model;
                                            if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
                                                if (!empty($email -> subscriber_id)) {
                                                    $SubscribersList -> delete_all(array('subscriber_id' => $email -> subscriber_id));

                                                    foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                        $list_id = sanitize_text_field($list_id);
                                                        $sl_data = array(
                                                            'subscriber_id'					=>	(int) $email -> subscriber_id,
                                                            'list_id'						=>	(int) $list_id,
                                                            'active'						=>	"Y",
                                                        );

                                                        $SubscribersList -> save($sl_data, true);
                                                    }
                                                }
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected lists set to subscribers', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'subscribers_dellists'		:
                                    if (!empty($_POST['lists'])) {
                                        foreach (map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field') as $email_id) {
                                            $email_id = sanitize_text_field($email_id);
                                            $Db -> model = $Email -> model;
                                            if ($email = $Db -> find(array('id' => $email_id), array('subscriber_id'))) {
                                                if (!empty($email -> subscriber_id)) {
                                                    foreach (map_deep(wp_unslash($_POST['lists']), 'sanitize_text_field') as $list_id) {
                                                        $list_id = sanitize_text_field($list_id);
                                                        $SubscribersList -> delete_all(array('subscriber_id' => (int) $email -> subscriber_id, 'list_id' => (int) $list_id));
                                                    }
                                                }
                                            }
                                        }

                                        $msg_type = 'message';
                                        $message = __('Selected lists removed from subscriber', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'delete'					:
                                    foreach (map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field') as $email_id) {
                                        $Db -> model = $Email -> model;
                                        $Db -> delete(sanitize_text_field($email_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected emails have been deleted', 'wp-mailinglist');
                                    break;
                                case 'export'					:
                                case 'exportall'				:
                                    $history_id = (empty($_POST['id'])) ? sanitize_text_field(wp_unslash($_REQUEST['history_id'])) : sanitize_text_field(wp_unslash($_POST['id']));
                                    $history_id = sanitize_text_field($history_id);

                                    if (!empty($_REQUEST['action']) && $_REQUEST['action'] == "export") {
                                        $email_ids = implode(",", map_deep(wp_unslash($_POST['emails']), 'sanitize_text_field'));
                                        $emailsquery = "SELECT * FROM " . $wpdb -> prefix . $Email -> table . " WHERE id IN (" . esc_sql($email_ids) . ")";
                                    } else {
                                        $emailsquery = "SELECT * FROM " . $wpdb -> prefix . $Email -> table . " WHERE history_id = '" . esc_sql($history_id) . "'";
                                    }

                                    if ($emails = $wpdb -> get_results($emailsquery)) {
                                        $exportfile = 'history' . $history_id . '-emails-' . date_i18n("Ymd") . '.csv';
                                        $exportpath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
                                        $exportfull = $exportpath . $exportfile;

                                        if ($fh = fopen($exportfull, "w")) {
                                            $headings = array(
                                                'email'						=>	__('Email Address', 'wp-mailinglist'),
                                                'mailinglists'				=>	__('Mailing List/s', 'wp-mailinglist'),
                                                'status'					=>	__('Sent/Unsent', 'wp-mailinglist'),
                                                'read'						=>	__('Read/Opened', 'wp-mailinglist'),
                                                'unsubscribed'				=>	__('Unsubscribed', 'wp-mailinglist'),
                                                'clicked'					=>	__('Clicked', 'wp-mailinglist'),
                                                'history_id'				=>	__('Newsletter', 'wp-mailinglist'),
                                                'device'					=>	__('Device', 'wp-mailinglist'),
                                                'bounced'					=>	__('Bounced', 'wp-mailinglist'),
                                                'modified'					=>	__('Sent Date', 'wp-mailinglist'),
                                            );

                                            $d = 0;
                                            $data = array();

                                            foreach ($emails as $email) {
                                                if (!empty($email -> subscriber_id)) {
                                                    $Db -> model = $Subscriber -> model;
                                                    $subscriber = $Db -> find(array('id' => $email -> subscriber_id));
                                                    $emailaddress = $subscriber -> email;

                                                    $mailinglists = array();
                                                    if (!empty($email -> mailinglists)) {
                                                        if ($lists = maybe_unserialize($email -> mailinglists)) {
                                                            foreach ($lists as $list_id) {
                                                                $list = $Mailinglist -> get($list_id, false);
                                                                $mailinglists[] = esc_html($list -> title);
                                                            }

                                                            $mailinglists = implode(", ", $mailinglists);
                                                        }
                                                    }
                                                } elseif (!empty($email -> user_id)) {
                                                    $user = $this -> userdata($email -> user_id);
                                                    $emailaddress = $user -> user_email;
                                                }

                                                $clicked = 0;
                                                if (!empty($email -> subscriber_id)) {
                                                    $clicked = $this -> Click() -> count(array('history_id' => $email -> history_id, 'subscriber_id' => $email -> subscriber_id));
                                                } elseif (!empty($user)) {
                                                    $clicked = $this -> Click() -> count(array('history_id' => $email -> history_id, 'user_id' => $email -> user_id));
                                                }

                                                global $Unsubscribe;
                                                $unsubscribed = false;
                                                $Db -> model = $Unsubscribe -> model;
                                                if (!empty($email -> subscriber_id)) {
                                                    $unsubscribed = $Db -> find(array('history_id' => $email -> history_id, 'email' => $subscriber -> email));
                                                } elseif (!empty($user)) {
                                                    $unsubscribed = $Db -> find(array('history_id' => $email -> history_id, 'user_id' => $email -> user_id));
                                                }

                                                $history = $this -> History() -> find(array('id' => $email -> history_id));
                                                $newsletter = esc_html($history -> subject);

                                                $data[$d] = array(
                                                    'email'						=>	$emailaddress,
                                                    'mailinglists'				=>	$mailinglists,
                                                    'status'					=>	$email -> status,
                                                    'read'						=>	((!empty($email -> read) && $email -> read == "Y") ? __('Yes', 'wp-mailinglist') : __('No', 'wp-mailinglist')),
                                                    'unsubscribed'				=>	((!empty($unsubscribed)) ? __('Yes', 'wp-mailinglist') : __('No', 'wp-mailinglist')),
                                                    'clicked'					=>	$clicked,
                                                    'history_id'				=>	$newsletter,
                                                    'device'					=>	$email -> device,
                                                    'bounced'					=>	((!empty($email -> bounced) && $email -> bounced == "Y") ? __('Yes', 'wp-mailinglist') : __('No', 'wp-mailinglist')),
                                                    'modified'					=>	$Html -> gen_date("Y-m-d H:i:s", strtotime($email -> modified)),
                                                );

                                                $d++;
                                            }

                                            $headings_keys = array();
                                            foreach ($headings as $hkey => $hval) {
                                                $headings_keys[$hkey] = '';
                                            }

                                            $headings = apply_filters('newsletters_admin_history_emails_export_headings', $headings, $data);

                                            $csvdelimiter = $this -> get_option('csvdelimiter');

                                            fputcsv($fh, $headings, $csvdelimiter, '"');

                                            if (!empty($data)) {
                                                $data = apply_filters('newsletters_admin_history_emails_export_data', $data, $headings);

                                                foreach ($data as $dkey => $dval) {
                                                    $data[$dkey] = array_merge($headings_keys, $data[$dkey]);
                                                    fputcsv($fh, $data[$dkey], $csvdelimiter, '"');
                                                }
                                            }

                                            fclose($fh);
                                            @chmod($exportfull, 0755);

                                            $exportfileabs = $Html -> uploads_url() . '/' . $exportfile;
                                            $msg_type = 'message';
                                            $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history_id . '&newsletters_exportlink=' . $exportfile));
                                        } else {
                                            $msg_type = 'error';
                                            $message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', 'wp-mailinglist'), $exportpath);
                                        }
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No history/draft emails are available to export!', 'wp-mailinglist');
                                    }
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = __('No emails were selected', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No action was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'mass'				:
                    check_admin_referer($this -> sections -> history);
                    if (!empty($_POST['action'])) {
                        if (!empty($_POST['historylist'])) {
                            $histories = array_map('sanitize_text_field', $_POST['historylist']);

                            switch ($_POST['action']) {
                                case 'delete'				:
                                    foreach ($histories as $history_id) {
                                        $this -> History() -> delete((int) $history_id);
                                    }

                                    $msg_type = 'message';
                                    $message = count($histories) . ' ' . __('history record(s) have been removed', 'wp-mailinglist');
                                    break;
                                case 'export'				:
                                    global $Db, $Html, $Email, $Mailinglist, $Theme;
                                    $csvdelimiter = $this -> get_option('csvdelimiter');

                                    if ($emails = $this -> History() -> find_all(false, false, array('modified', "DESC"))) {
                                        $data = "";
                                        $data .= '"' . __('Id', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Subject', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Lists', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Template', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Author', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Read %', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Emails Sent', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Emails Read', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Created', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= '"' . __('Modified', 'wp-mailinglist') . '"' . $csvdelimiter;
                                        $data .= "\r\n";

                                        foreach ($emails as $email) {
                                            $this -> remove_server_limits();			//remove the server resource limits

                                            $data .= '"' . $email -> id . '"' . $csvdelimiter;
                                            $data .= '"' . $email -> subject . '"' . $csvdelimiter;

                                            /* Mailing lists */
                                            if (!empty($email -> mailinglists)) {
                                                $data .= '"';
                                                $m = 1;

                                                foreach ($email -> mailinglists as $mailinglist_id) {
                                                    $mailinglist = $Mailinglist -> get($mailinglist_id);
                                                    $data .= esc_html($mailinglist -> title);

                                                    if ($m < count($email -> mailinglists)) {
                                                        $data .= $csvdelimiter . ' ';
                                                    }

                                                    $m++;
                                                }

                                                $data .= '"' . $csvdelimiter;
                                            } else {
                                                $data .= '""' . $csvdelimiter;
                                            }

                                            /* Theme */
                                            if (!empty($email -> theme_id)) {
                                                $Db -> model = $Theme -> model;

                                                if ($theme = $Db -> find(array('id' => $email -> theme_id))) {
                                                    $data .= '"' . $theme -> title . '"' . $csvdelimiter;
                                                } else {
                                                    $data .= '""' . $csvdelimiter;
                                                }
                                            } else {
                                                $data .= '""' . $csvdelimiter;
                                            }

                                            /* Author */
                                            if (!empty($email -> user_id)) {
                                                if ($user = get_userdata($email -> user_id)) {
                                                    $data .= '"' . $user -> display_name . '"' . $csvdelimiter;
                                                } else {
                                                    $data .= '""' . $csvdelimiter;
                                                }
                                            } else {
                                                $data .= '""' . $csvdelimiter;
                                            }

                                            /* read % */
                                            $Db -> model = $Email -> model;
                                            $etotal = $Db -> count(array('history_id' => $email -> id));
                                            $eread = $Db -> count(array('history_id' => $email -> id, 'read' => "Y"));
                                            $eperc = (!empty($etotal)) ? (($eread / $etotal) * 100) : 0;
                                            $data .= '"' . number_format($eperc, 2, '.', '') . '% ' . __('read', 'wp-mailinglist') . '"' . $csvdelimiter;

                                            $data .= '"' . $etotal . '"' . $csvdelimiter; 					// emails sent
                                            $data .= '"' . $eread . '"' . $csvdelimiter;					// emails read
                                            $data .= '"' . $email -> created . '"' . $csvdelimiter;		// created date
                                            $data .= '"' . $email -> modified . '"' . $csvdelimiter;		// modified date

                                            $data .= "\r\n";
                                        }

                                        if (!empty($data)) {
                                            $filename = "history-" . date_i18n("Ymd") . ".csv";
                                            $filepath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
                                            $filefull = $filepath . $filename;

                                            if ($fh = fopen($filefull, "w")) {
                                                fwrite($fh, $data);
                                                fclose($fh);
                                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&newsletters_exportlink=' . $filename));
                                            } else {
                                                $message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', 'wp-mailinglist'), $filepath);
                                                $this -> redirect($this -> url, "error", $message);
                                            }
                                        } else {
                                            $message = __('CSV data could not be formulated, no emails maybe? Please try again', 'wp-mailinglist');
                                            $this -> redirect($this -> url, "error", $message);
                                        }
                                    } else {
                                        $message = __('No history/draft emails are available to export!', 'wp-mailinglist');
                                        $this -> redirect($this -> url, "error", $message);
                                    }
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 16;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 17;
                    }

                    $this -> redirect(false, $msg_type, $message);
                    break;
                case 'clear'			:
                    if ($this -> History() -> truncate()) {
                        $msg_type = 'message';
                        $message = __('History list has been purged', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('History items cannot be removed', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'unlinkpost'		:
                    global $Db;
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> History() -> save_field('post_id', 0, array('id' => $id))) {
                            $msg_type = 'message';
                            $message = __('Post has been unlinked', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Post could not be unlinked', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No history email was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'removeattachment'	:
                    global $Db, $HistoriesAttachment;
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $Db -> model = $HistoriesAttachment -> model;

                        if ($attachment = $Db -> find(array('id' => $id))) {
                            if (!empty($attachment -> filename) && file_exists($attachment -> filename)) {
                                @unlink($attachment -> filename);
                            }

                            $Db -> model = $HistoriesAttachment -> model;
                            $Db -> delete($attachment -> id);

                            $msg_type = 'message';
                            $message = __('Attachment file has been removed.', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Attachment could not be read.', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No attachment was specified.', 'wp-mailinglist');
                    }

                    $this -> redirect(false, $msg_type, $message);
                    break;
                case 'exportsent'		:
                    global $wpdb, $Html, $Db, $Subscriber, $Mailinglist, $Email;

                    if (!empty($_GET['history_id'])) {
                        $Db -> model = $Email -> model;
                        $csvdelimiter = $this -> get_option('csvdelimiter');

                        if ($emails = $Db -> find_all(array('history_id' => esc_sql((int) $_GET['history_id'])), false, array('modified', "DESC"))) {
                            /* CSV Headings */
                            $data = "";
                            $data .= '"' . __('Email Address', 'wp-mailinglist') . '"' . $csvdelimiter;
                            $data .= '"' . __('Mailing List', 'wp-mailinglist') . '"' . $csvdelimiter;
                            $data .= '"' . __('Read/Opened', 'wp-mailinglist') . '"' . $csvdelimiter;
                            $data .= '"' . __('Sent Date', 'wp-mailinglist') . '"' . $csvdelimiter;
                            $data .= "\r\n";

                            foreach ($emails as $email) {
                                $this -> remove_server_limits();

                                if (!empty($email -> subscriber_id)) {
                                    $Db -> model = $Subscriber -> model;
                                    $subscriber = $Db -> find(array('id' => $email -> subscriber_id));
                                    /* Subscriber */
                                    $Db -> model = $Subscriber -> model;
                                    $subscriber = $Db -> find(array('id' => $email -> subscriber_id));
                                    $data .= '"' . $subscriber -> email . '"' . $csvdelimiter;

                                    /* Mailing List */
                                    $Db -> model = $Mailinglist -> model;
                                    $mailinglist = $Db -> find(array('id' => $email -> mailinglist_id));
                                    $data .= '"' . esc_html($mailinglist -> title) . '"' . $csvdelimiter;
                                } elseif (!empty($email -> user_id)) {
                                    $user = $this -> userdata($email -> user_id);
                                    $data .= '"' . $user -> user_email . '"' . $csvdelimiter;
                                    $data .= '"' . '' . '"' . $csvdelimiter;
                                }

                                /* Read/Opened Status */
                                $data .= '"' . ((!empty($email -> read) && $email -> read == "Y") ? __('Yes', 'wp-mailinglist') : __('No', 'wp-mailinglist')) . '"' . $csvdelimiter;
                                $data .= '"' . (date_i18n("Y-m-d H:i:s", strtotime($email -> modified))) . '"' . $csvdelimiter;
                                $data .= "\r\n";
                            }

                            if (!empty($data)) {
                                $exportfile = 'history' . sanitize_text_field(wp_unslash($_GET['history_id'])) . '-emails-' . date_i18n("Ymd") . '.csv';
                                $exportpath = $Html -> uploads_path() . DS . $this -> plugin_name . DS . 'export' . DS;
                                $exportfull = $exportpath . $exportfile;

                                if ($fh = fopen($exportfull, "w")) {
                                    fwrite($fh, $data);
                                    fclose($fh);
                                    @chmod($exportfull, 0755);

                                    $exportfileabs = $Html -> uploads_url() . '/' . $exportfile;
                                    $msg_type = 'message';
                                    $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . esc_html($_GET['history_id']) . '&newsletters_exportlink=' . $exportfile));
                                } else {
                                    $msg_type = 'error';
                                    $message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', 'wp-mailinglist'), $exportpath);
                                }
                            } else {
                                $msg_type = 'error';
                                $message = __('CSV data could not be formulated, no emails maybe? Please try again', 'wp-mailinglist');
                            }
                        } else {
                            $msg_type = 'error';
                            $message = __('No history/draft emails are available to export!', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No history email was specified, please try again.', 'wp-mailinglist');
                    }

                    $this -> redirect(admin_url("admin.php?page=" . $this -> sections -> history . "&method=view&id=" . esc_html($_GET['history_id'])), $msg_type, $message);
					break;
                default					:

                    $screen = get_current_screen($this -> menus['newsletters-history']);
					$screen_perpage = $screen -> get_option('per_page', 'option');
					$user_id = get_current_user_id();

					$user_perpage = get_user_meta($user_id, $screen_perpage, true);
					if (!empty($user_perpage)) {
						$perpage = $user_perpage;
					} else {
						$perpage = $screen -> get_option('per_page', 'default');
					}

					$perpage = 15;

					$sections = $this -> sections -> history;
					$history_table = $wpdb -> prefix . $this -> History() -> table;
					$historieslist_table = $wpdb -> prefix . $HistoriesList -> table;
					$conditions_and = array();
					//$perpage = (isset($_COOKIE[$this -> pre . 'historiesperpage'])) ? $_COOKIE[$this -> pre . 'historiesperpage'] : $screen_perpage;
					$searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? esc_html($_GET[$this -> pre . 'searchterm']) : false;
					$searchterm = (!empty($_POST['searchterm'])) ? esc_html($_POST['searchterm']) : $searchterm;

					if (!empty($_POST['searchterm'])) {
						check_admin_referer($this -> sections -> history);
						$this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . urlencode($searchterm));
					}

					$conditions = (!empty($searchterm)) ? array('subject' => "LIKE '%" . $searchterm . "%'") : false;

					$ofield = (isset($_COOKIE[$this -> pre . 'historysorting'])) ? sanitize_text_field($_COOKIE[$this -> pre . 'historysorting']) : "modified";
					$odir = (isset($_COOKIE[$this -> pre . 'history' . $ofield . 'dir'])) ? sanitize_text_field($_COOKIE[$this -> pre . 'history' . $ofield . 'dir']) : "DESC";
					$order = array($ofield, $odir);

					$orderfield = (empty($_GET['orderby'])) ? 'created' :  sanitize_text_field(wp_unslash($_GET['orderby']));
					$orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
					$order = array($orderfield, $orderdirection);

					$dojoin = false;

					if (!empty($_GET['filter'])) {
						//check_admin_referer($this -> sections -> history);
						$sections .= '&filter=1';

						if (!empty($_GET['list'])) {
							switch ($_GET['list']) {
								case 'all'				:
									$dojoin = false;
									break;
								case 'none'				:
									$dojoin = false;
									$conditions_and[$history_table . '.id'] = "NOT IN (SELECT history_id FROM " . $historieslist_table . ")";
									break;
								default 				:
									$dojoin = true;
									$conditions_and[$historieslist_table . '.list_id'] = sanitize_text_field(wp_unslash($_GET['list']));
									break;
							}
						}

						if (!empty($_GET['sent'])) {
							switch ($_GET['sent']) {
								case 'all'				:

									break;
								case 'draft'			:
									$conditions_and[$history_table . '.sent'] = '0';
									break;
								case 'sent'				:
									$conditions_and[$history_table . '.sent'] = 'LE 1';
									break;
							}
						}

						if (!empty($_GET['theme_id'])) {
							if ($_GET['theme_id'] != "all") {
								$conditions_and[$history_table . '.theme_id'] = sanitize_text_field(wp_unslash($_GET['theme_id']));
							}
						}
					}

					$conditions = apply_filters($this -> pre . '_admin_history_conditions', $conditions);
					$conditions_and = apply_filters('newsletters_admin_history_conditions_and', $conditions_and);

					if (!empty($_GET['showall'])) {
						$histories = $this -> History() -> find_all(false, "*", $order);
						$data[$this -> History() -> model] = $histories;
						$data['Paginate'] = false;
					} else {
						if ($dojoin) {
							$data = $this -> paginate($HistoriesList -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$histories = $data[$HistoriesList -> model];
						} else {
							$data = $this -> paginate($this -> History() -> model, null, $sections, $conditions, $searchterm, $perpage, $order, $conditions_and);
							$histories = $data[$this -> History() -> model];
						}
					}

                    $this -> render('history' . DS . 'index', array('histories' => $histories, 'paginate' => $data['Paginate'], 'perpage' => $perpage), true, 'admin');
                    break;
            }
        }

        function admin_links() {
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'delete'					:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> Link() -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Link has been deleted', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Link could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No link was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'mass'						:
                    check_admin_referer($this -> sections -> links . '_mass');
                    if (!empty($_POST['action'])) {
                        if (!empty($_POST['links'])) {
                            $links = array_map('sanitize_text_field', $_POST['links']);

                            switch ($_POST['action']) {
                                case 'delete'				:
                                    foreach ($links as $link_id) {
                                        $this -> Link() -> delete($link_id);
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                                case 'reset'				:
                                    foreach ($links as $link_id) {
                                        $this -> Click() -> delete_all(array('link_id' => $link_id));
                                    }

                                    $msg_type = 'message';
                                    $message = __('Selected links have been reset', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 16;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 17;
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                default							:
                    $perpage = (isset($_COOKIE[$this -> pre . 'linksperpage'])) ? (int) ($_COOKIE[$this -> pre . 'linksperpage']) : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> links . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('link' => "LIKE '%" . $searchterm . "%'") : false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);
                    $sub = $this -> sections -> links;

                    if (!empty($_GET['showall'])) {
                        $links = $this -> Link() -> find_all(false, "*", $order);
                        $data[$this -> Link() -> model] = $links;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Link() -> model, "*", $sub, $conditions, $searchterm, $perpage, $order);
                    }
                    $this -> render('links' . DS . 'index', array('links' => $data[$this -> Link() -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        function admin_clicks() {

            global $wpdb;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'delete'					:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> Click() -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Click has been deleted', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Click could not be deleted', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No click was specified', 'wp-mailinglist');
                    }

                    $this -> redirect('?page=' . $this -> sections -> clicks, $msg_type, $message);
                    break;
                case 'mass'						:
                    check_admin_referer($this -> sections -> clicks . '_mass');
                    if (!empty($_POST['action'])) {
                        $action = sanitize_text_field(wp_unslash($_POST['action']));
                        $clicks = array_map('sanitize_text_field', $_POST['clicks']);

                        if (!empty($clicks)) {
                            switch ($action) {
                                case 'delete'				:
                                    foreach ($clicks as $click_id) {
                                        $this -> Click() -> delete($click_id);
                                    }

                                    $msg_type = 'message';
                                    $message = 18;
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 16;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 17;
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                default							:
                    $perpage = (isset($_COOKIE[$this -> pre . 'clicksperpage'])) ? (int) ($_COOKIE[$this -> pre . 'clicksperpage']) : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> clicks . '_search');
                        $searchterm = esc_html($searchterm);
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array($wpdb -> prefix . $this -> Link() -> table . '.link' => "LIKE '%" . esc_sql($searchterm) . "%'") : false;
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);
                    $sub = $this -> sections -> clicks;

                    $conditions_and = array();

                    if (!empty($_GET['subscriber_id'])) {
                        $conditions_and['subscriber_id'] = (int) $_GET['subscriber_id'];
                    }

                    if (!empty($_GET['link_id'])) {
                        $conditions_and['link_id'] = (int) $_GET['link_id'];
                    }

                    if (!empty($_GET['history_id'])) {
                        $conditions_and['history_id'] = (int) $_GET['history_id'];
                    }

                    if (!empty($_GET['showall'])) {
                        $clicks = $this -> Click() -> find_all(false, "*", $order);
                        $data[$this -> Click() -> model] = $clicks;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Click() -> model, "*", $sub, $conditions, $searchterm, $perpage, $order, $conditions_and);
                        $clicks = $data[$this -> Click() -> model];
                    }

                    $this -> render('clicks' . DS . 'index', array('clicks' => $clicks, 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        function admin_orders() {
            global $wpdb, $Db, $Subscriber, $Mailinglist;
            $Db -> model = $this -> Order() -> model;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'view'			:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($order = $this -> Order() -> get($id)) {
                            $subscriber = $Subscriber -> get($order -> subscriber_id, false);
                            $mailinglist = $Mailinglist -> get($order -> list_id, false);
                            $this -> render('orders' . DS . 'view', array('order' => $order, 'subscriber' => $subscriber, 'mailinglist' => $mailinglist), true, 'admin');
                        } else {
                            $this -> render_error(__('Order could not be retrieved', 'wp-mailinglist'));
                        }
                    } else {
                        $this -> render_error(__('No order ID was specified', 'wp-mailinglist'));
                    }
                    break;
                case 'save'			:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> orders . '_save');
                        $_POST['completed'] = "Y";

                        if ($this -> Order() -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'), true)) {
                            $message = __('Order has been saved', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> orders . '&method=save&id=' . $this -> Order() -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> render_message($message);
                                $data = $this -> Order() -> get_all_paginated();
                                $this -> render('orders' . DS . 'index', array('orders' => $data[$this -> Order() -> model], 'paginate' => $data['Pagination']), true, 'admin');
                            }
                        } else {
                            $this -> render_error(__('Order could not be saved', 'wp-mailinglist'));
                            $this -> render('orders' . DS . 'save', array('order' => new wpmlOrder($_POST), 'errors' => $this -> Order() -> errors), true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                        if (!empty($id)) {
                            if ($order = $this -> Order() -> get($id)) {
                                $this -> render('orders' . DS . 'save', array('order' => $order), true, 'admin');
                            } else {
                                $this -> render_error(__('Order could not be read', 'wp-mailinglist'));
                            }
                        } else {
                            $this -> render_error(__('No order ID was specified', 'wp-mailinglist'));
                        }
                    }
                    break;
                case 'delete'		:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        if ($this -> Order() -> delete($id)) {
                            $msg_type = 'message';
                            $message = __('Order successfully removed', 'wp-mailinglist');
                        } else {
                            $msg_type = 'error';
                            $message = __('Order could not be removed', 'wp-mailinglist');
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No order ID was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'mass'			:
                    check_admin_referer($this -> sections -> orders . '_mass');
                    if (!empty($_POST)) {
                        if (!empty($_POST['orderslist'])) {
                            if (!empty($_POST['action'])) {
                                $orders = array_map('sanitize_text_field', $_POST['orderslist']);

                                switch ($_POST['action']) {
                                    case 'delete'		:
                                        foreach ($orders as $order_id) {
                                            $this -> Order() -> delete($order_id);
                                        }

                                        $msg_type = 'message';
                                        $message = 18;
                                        break;
                                }
                            } else {
                                $msg_type = 'error';
                                $message = 17;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 16;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = __('No data was posted', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                default				:
                    $perpage = (isset($_COOKIE[$this -> pre . 'ordersperpage'])) ? (int) $_COOKIE[$this -> pre . 'ordersperpage'] : 15;
                    $searchterm = (!empty($_GET[$this -> pre . 'searchterm'])) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : false;
                    $searchterm = (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : $searchterm;

                    if (!empty($_POST['searchterm'])) {
                        check_admin_referer($this -> sections -> orders . '_search');
                        $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                    }

                    $conditions = (!empty($searchterm)) ? array('subscriber_id' => "LIKE '%" . esc_sql($searchterm) . "%'") : false;

                    $orderfield = (empty($_GET['orderby'])) ? 'modified' :  sanitize_text_field(wp_unslash($_GET['orderby']));
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field(wp_unslash($_GET['order'])));
                    $order = array($orderfield, $orderdirection);

                    if (!empty($_GET['showall'])) {
                        $Db -> model = $this -> Order() -> model;
                        $orders = $Db -> find_all(false, "*", $order);
                        $data[$this -> Order() -> model] = $orders;
                        $data['Paginate'] = false;
                    } else {
                        $data = $this -> paginate($this -> Order() -> model, null, $this -> sections -> orders, $conditions, $searchterm, $perpage, $order);
                    }
                    if(!empty($data)) {
                    $this -> render('orders' . DS . 'index', array('orders' => $data[$this -> Order() -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    }
                    else{
                        $this -> render('orders' . DS . 'index', array('orders' => array(), 'paginate' => array()), true, 'admin');

                    }

                    break;
            }
        }

        function admin_fields() {
            global $wpdb, $Db, $Field, $FieldsList;
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'save'				:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> fields . '_save');
                        if ($Field -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
                            $message = __('Custom field has been saved', 'wp-mailinglist');

                            if (!empty($_POST['continueediting'])) {
                                $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> fields . '&method=save&id=' . $Field -> insertid . '&continueediting=1'), 'message', $message);
                            } else {
                                $this -> redirect('?page=' . $this -> sections -> fields, 'message', $message);
                            }
                        } else {
                            $this -> render_error(__('Custom field could not be saved', 'wp-mailinglist'));
                            $this -> render('fields' . DS . 'save', false, true, 'admin');
                        }
                    } else {
                        $id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0 );
                        $field = $Field -> get($id);
                        if ($Field -> data -> slug == "email" || $Field -> data -> slug == "list") {
                            $this -> render_message(__('This is a fixed field and can be edited but not deleted.', 'wp-mailinglist'));
                        }

                        $this -> render('fields' . DS . 'save', false, true, 'admin');
                    }
                    break;
                case 'delete'			:
                    $id = (int) sanitize_text_field($_GET['id']);
                    if (!empty($id)) {
                        $fieldquery = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE id = '" . $id . "'";
                        if ($field = $wpdb -> get_row($fieldquery)) {
                            if ($field -> slug != "email" && $field -> slug != "list") {
                                if ($Field -> delete($id)) {
                                    $message_type = 'message';
                                    $message = __('Field has been removed', 'wp-mailinglist');
                                } else {
                                    $message_type = 'error';
                                    $message = __('Field cannot be removed', 'wp-mailinglist');
                                }
                            } else {
                                $message_type = 'error';
                                $message = __('This field may not be deleted.', 'wp-mailinglist');
                            }
                        } else {
                            $message_type = 'error';
                            $message = __('Field cannot be read.', 'wp-mailinglist');
                        }
                    } else {
                        $message_type = 'error';
                        $message = __('No field was specified', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> url, $message_type, $message);
                    break;
                case 'mass'				:
                    check_admin_referer($this -> sections -> fields . '_mass');

                    if (!empty($_POST['fieldslist'])) {
                        if (!empty($_POST['action'])) {
                            $fields = map_deep($_POST['fieldslist'], 'sanitize_text_field');
                            $mailinglists = map_deep($_POST['mailinglists'], 'sanitize_text_field');
                            $msg_type = 'message';

                            switch ($_POST['action']) {
                                case 'delete'		:
                                    $Field -> delete_array($fields);
                                    $message = 18;
                                    break;
                                case 'lists'		:
                                    if (!empty($mailinglists)) {
                                        foreach ($fields as $field_id) {
                                            $Db -> model = $Field -> model;
                                            $Db -> save_field('display', "specific", array('id' => $field_id));
                                            $Db -> model = $FieldsList -> model;
                                            $Db -> delete_all(array('field_id' => $field_id));
                                            foreach ($mailinglists as $list_id) {
                                                $fl_data = array('field_id' => $field_id, 'list_id' => $list_id);
                                                $FieldsList -> save($fl_data, false);
                                            }
                                        }

                                        $message = __('Selected fields assigned to specified lists', 'wp-mailinglist');
                                    } else {
                                        $msg_type = 'error';
                                        $message = __('No mailing lists were selected', 'wp-mailinglist');
                                    }
                                    break;
                                case 'alllists'		:
                                    foreach ($fields as $field_id) {
                                        $Db -> model = $Field -> model;
                                        $Db -> save_field('display', "always", array('id' => $field_id));
                                        $Db -> model = $FieldsList -> model;
                                        $Db -> delete_all(array('field_id' => $field_id));
                                        $fl_data = array('field_id' => $field_id, 'list_id' => "0");
                                        $FieldsList -> save($fl_data, false);
                                    }

                                    $message = __('Selected fields assigned to always show', 'wp-mailinglist');
                                    break;
                                case 'required'		:
                                    foreach ($fields as $field_id) {
                                        $Db -> model = $Field -> model;
                                        $Db -> save_field('required', "Y", array('id' => $field_id));
                                    }

                                    $message = __('Selected custom fields have been set as required', 'wp-mailinglist');
                                    break;
                                case 'notrequired'	:
                                    foreach ($fields as $field_id) {
                                        $fieldquery = "SELECT * FROM " . $wpdb -> prefix . $Field -> table . " WHERE id = '" . $field_id . "'";
                                        if ($field = $wpdb -> get_row($fieldquery)) {
                                            if ($field -> slug != "email") {
                                                $Db -> model = $Field -> model;
                                                $Db -> save_field('required', "N", array('id' => $field_id));
                                            }
                                        }
                                    }

                                    $message = __('Selected custom fields have been set as NOT required', 'wp-mailinglist');
                                    break;
                            }
                        } else {
                            $msg_type = 'error';
                            $message = 17;
                        }
                    } else {
                        $msg_type = 'error';
                        $message = 16;
                    }

                    $this -> redirect($this -> url, $msg_type, $message);
                    break;
                case 'order'			:
                    $Db -> model = $Field -> model;
                    $fields = $Db -> find_all(false, false, array('order', "ASC"));
                    $this -> render('fields' . DS . 'order', array('fields' => $fields), true, 'admin');
                    break;
                case 'loaddefaults'		:
                    $Field -> check_default_fields();
                    $this -> redirect($this -> referer, 'message', 15);
                    break;
                default					:
                    $orderfield = (empty($_GET['orderby'])) ? 'modified' : sanitize_text_field($_GET['orderby']);
                    $orderdirection = (empty($_GET['order'])) ? 'DESC' : strtoupper(sanitize_text_field($_GET['order']));
                    $order = array($orderfield, $orderdirection);
                    $conditions = array();
                    $data = array();
                    if (!empty($_GET['showall'])) {
                        $Db -> model = $Field -> model;
                        $data[$Field -> model] = $Db -> find_all($conditions, "*", $order);
                        $data['Paginate'] = false;
                    } else {
                        $perpage = (!empty($_COOKIE[$this -> pre . 'fieldsperpage'])) ? $_COOKIE[$this -> pre . 'fieldsperpage'] : 15;
                        $searchterm = (empty($_GET[$this -> pre . 'searchterm'])) ? '' : sanitize_text_field($_GET[$this -> pre . 'searchterm']);
                        $searchterm = (empty($_POST['searchterm'])) ? $searchterm : sanitize_text_field($_POST['searchterm']);

                        if (!empty($_POST['searchterm'])) {
                            check_admin_referer($this -> sections -> fields . '_search');
                            $this -> redirect($this -> url . '&' . $this -> pre . 'searchterm=' . esc_html($searchterm));
                        }

                        if (!empty($searchterm)) {
                            $conditions['title'] = "LIKE '%" . esc_sql($searchterm) . "%'";
                            $conditions['slug'] = "LIKE '%" . esc_sql($searchterm) . "%'";
                        }

                        $data = $this -> paginate($Field -> model, null, $this -> sections -> fields, $conditions, $searchterm, $perpage, $order);
                    }

                    $this -> render('fields' . DS . 'index', array('fields' => $data[$Field -> model], 'paginate' => $data['Paginate']), true, 'admin');
                    break;
            }
        }

        /**
         * Administration configuration area
         * Outputs the config form and receives posted option keys and values
         *
         **/
        function admin_config() {
            global $wpdb, $Html, $Db, $Subscriber, $SubscribersList, $Mailinglist;

            do_action('newsletters_admin_settings');

            if (!empty($_GET['reset']) && $_GET['reset'] == 1) {
                $this -> update_options();
                $this -> redirect($this -> url);
            }

            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $method = (empty($method)) ? false : $method;

            switch ($method) {
                case 'managementpost'	:

                    check_admin_referer($this -> sections -> settings . '_managementpost');

                    $this -> get_managementpost(false, true);
                    $msg_type = 'message';
                    $message = __('Manage subscriptions post/page has been created', 'wp-mailinglist');
                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'clearlog'			:

                    check_admin_referer($this -> sections -> settings . '_clearlog');

                    @unlink(NEWSLETTERS_LOG_FILE);

                    $fh = fopen(NEWSLETTERS_LOG_FILE, "w");
                    fwrite($fh, "*** Newsletters Log File *** \r\n\r\n");
                    fclose($fh);
                    chmod(NEWSLETTERS_LOG_FILE, 0777);

                    $msgtype = 'message';
                    $message = __('Log file has been cleared', 'wp-mailinglist');
                    $this -> redirect($this -> referer, $msgtype, $message);
                case 'checkdb'			:
                    $this -> check_roles();
                    $this -> check_tables();

                    if (!empty($this -> tablenames)) {
                        foreach ($this -> tablenames as $table) {
                            $query = "OPTIMIZE TABLE `" . $table . "`";
                            $wpdb -> query($query);
                        }
                    }

                    $this -> delete_option('hidedbupdate');

                    flush_rewrite_rules();

                    $msg_type = 'message';
                    $message = __('All database tables have been checked and optimized.', 'wp-mailinglist');
                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'clearlpshistory'	:
                    $id = (int) sanitize_text_field(wp_unslash($_GET['id']));
                    if (!empty($id)) {
                        $clearquery = "DELETE FROM " . $wpdb -> prefix . $this -> Latestpost() -> table . " WHERE `lps_id` = '" . $id . "'";
                    } else {
                        $clearquery = "TRUNCATE TABLE " . $wpdb -> prefix . $this -> Latestpost() -> table . "";
                    }

                    if ($wpdb -> query($clearquery)) {
                        $msg_type = 'message';
                        $message = __('Latest Posts Subscription history has been cleared.', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('Latest Posts Subscription history could not be cleared, please try again.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'reset'			:

                    check_admin_referer($this -> sections -> settings . '_reset');

                    $query = "TRUNCATE TABLE `" . $wpdb -> prefix . "" . $this -> Country() -> table . "`";
                    $wpdb -> query($query);

                    $query = "DELETE FROM `" . $wpdb -> prefix . "options` WHERE `option_name` LIKE '" . $this -> pre . "%';";

                    if ($wpdb -> query($query)) {
                        $msg_type = 'message';
                        $message = __('All configuration settings have been reset', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('Configuration settings cannot be reset', 'wp-mailinglist');
                    }

                    $this -> redirect($Html -> retainquery('reset=1', $this -> url), $msg_type, $message);
                    break;
                default					:
                    //make sure that data has been posted
                    if (!empty($_POST)) {

                        check_admin_referer($this -> sections -> settings);

                        //unset values that are not required
                        unset($_POST['save']);
                        delete_option('tridebugging');
                        $this -> update_option('inlinestyles', 0);
                        $this -> update_option('themeintextversion', 0);
                        $this -> update_option('emailarchive', 0);
                        $this -> update_option('showpostattachments', 0);
                        $this -> update_option('disable_drag_drop_builder', 0);
                        $this -> update_option('excerpt_settings', 0);
                        $this -> update_option('postswpautop', 0);
                        $this -> update_option('defaulttemplate', 0);
                        $this -> update_option('videoembed', 0);
                        $this -> update_option('loadstyles', 0);
                        $this -> update_option('loadscripts', 0);
                        $this -> update_option('remove_width_height_attr', 0);
                        $this -> update_option('replytodifferent', 0);
                        $this -> update_option('paymentmethod', 0);
                        $this -> update_option('notifyqueuecomplete', 0);
                        $this -> update_option('bccemails', 0);
                        $this -> update_option('mailapi_mailgun_emailvalidation', 0);

                        if (!empty($_FILES)) {
                            $_FILES = map_deep(wp_unslash($_FILES), 'sanitize_text_field');
                            foreach ($_FILES as $fkey => $fval) {
                                switch ($fkey) {
                                    case 'tracking_image_file'			:
                                        $tracking_image_file = $this -> get_option('tracking_image_file');

                                        if (!empty($_POST['tracking']) && $_POST['tracking'] == "Y" && !empty($_POST['tracking_image']) && $_POST['tracking_image'] == "custom") {
                                            if (!empty($_FILES['tracking_image_file']['name'])) {

                                                $_FILES['tracking_image_file'] = map_deep(wp_unslash($_FILES['tracking_image_file']), 'sanitize_text_field');

                                                $tracking_image_file = sanitize_text_field(wp_unslash($_FILES['tracking_image_file']['name']));
                                                $tracking_image_path = $Html -> uploads_path() . DS . $this -> plugin_name . DS;
                                                $tracking_image_full = $tracking_image_path . $tracking_image_file;

                                                // phpcs:ignore
                                                if (move_uploaded_file($_FILES['tracking_image_file']['tmp_name'], $tracking_image_full)) {
                                                    $this -> update_option('tracking_image_file', $tracking_image_file);
                                                } else {
                                                    $this -> render_error(__('Tracking image file could not be moved from /tmp', 'wp-mailinglist'));
                                                }
                                            } else {
                                                if (empty($tracking_image_file)) {
                                                    $this -> render_error(__('No image was specified', 'wp-mailinglist'));
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                        }

                        foreach ($_POST as $key => $val) {
                            $this -> update_option($key, $val);

                            switch ($key) {
                                case 'scheduleinterval'		:
                                    $schedules = wp_get_schedules();

                                    if (!empty($schedules[$val])) {
                                        $this -> update_option('scheduleintervalseconds', $schedules[$val]['interval']);
                                    }

                                    $this -> qp_scheduling();
                                    break;
                                case 'defaulttemplate'		:
                                    if (!empty($val)) {
                                        $this -> update_option('defaulttemplate', true);
                                    }
                                    break;
                                case 'videoembed'			:
                                    if (!empty($val)) {
                                        $this -> update_option('videoembed', true);
                                    }
                                    break;
                                case 'debugging'			:
                                    if (!empty($val)) {
                                        update_option('tridebugging', 1);
                                    }
                                    break;
                                case 'embed'				:
                                    if ($this -> language_do()) {
                                        if (!empty($val) && is_array($val)) {
                                            foreach ($val as $vkey => $vval) {
                                                $val[$vkey] = $this -> language_join($vval);
                                            }
                                        }
                                    }
                                    $this -> update_option('embed', $val);
                                    break;
                                case 'smtpfromname'			:
                                case 'smtpfrom'				:
                                case 'excerpt_more'			:
                                    if ($this -> language_do()) {
                                        $this -> update_option($key, $this -> language_join($val));
                                    } else {
                                        $this -> update_option($key, $val);
                                    }
                                    break;
                                case 'customcsscode'		:
                                    if (!empty($_POST['customcss']) && $_POST['customcss'] == "Y") {
                                        $this -> update_option('customcss', "Y");
                                        $this -> update_option('customcsscode', sanitize_textarea_field(wp_unslash($_POST['customcsscode'])));
                                    } else {
                                        $this -> update_option('customcss', "N");
                                    }
                                    break;
                                case 'emailarchive'			:
                                    if (!empty($val)) {
                                        $this -> emailarchive_scheduling();
                                    }
                                    break;
                            }
                        }

                        //update scheduling
                        $this -> scheduling();
                        $this -> pop_scheduling();
                        $this -> optimize_scheduling();

                        $message = 6;
                        $this -> redirect(admin_url('admin.php?page=' . $this -> sections -> settings), 'message', $message);
                    }

                    $mailinglists = $Mailinglist -> get_all('*', true);
                    $this -> delete_all_cache('all');
                    $this -> render('settings', array('mailinglists' => $mailinglists), true, 'admin');
                    break;
            }
        }

        function admin_settings_subscribers() {
            if (!empty($_POST)) {
                check_admin_referer($this -> sections -> settings_subscribers);

                $this -> update_option('import_notification', 0);
                $this -> update_option('import_createfieldoptions', 0);
                $this -> update_option('unsubscribe_usernotification', 0);
                $this -> update_option('currentusersubscribed', 0);
                $this -> update_option('managementdelete', 0);
                $this -> update_option('managementshowprivate', 0);
                delete_option('tridebugging');
                $this -> update_option('saveipaddress', 0);
                $this -> update_option('resubscribe', 0);
                $this -> update_option('management_password', 0);
                $this -> update_option('unsubscribe_redirect', 0);
                $this -> update_option('emailvalidationextended', 0);

                foreach ($_POST as $key => $val) {
                    switch ($key) {
                        // Actions for multilingual strings
                        case 'managelinktext'				:
                        case 'managementpost'				:
                        case 'managementloginsubject'		:
                        case 'subscriberexistsmessage'		:
                        case 'onlinelinktext'				:
                        case 'printlinktext'				:
                        case 'activationlinktext'			:
                        case 'authenticatelinktext'			:
                        case 'unsubscribetext'				:
                        case 'unsubscribealltext'			:
                        case 'resubscribetext'				:
                            if ($this -> language_do()) {
                                $this -> update_option($key, $this -> language_join($val));
                            } else {
                                $this -> update_option($key, $val);
                            }
                            break;
                        case 'debugging'			:
                            if (!empty($val)) {
                                update_option('tridebugging', 1);
                            }
                            break;
                        case 'activateaction'				:
                            $this -> update_option($key, $val);
                            $this -> activateaction_scheduling();
                            break;
                        default								:
                            $this -> update_option($key, $val);
                            break;
                    }
                }

                $this -> render_message(__('Subscribers configuration settings have been saved.', 'wp-mailinglist'));
            }

            $this -> delete_all_cache('all');
            $this -> render('settings-subscribers', false, true, 'admin');
        }

        function admin_settings_templates() {

            if (!empty($_POST)) {
                check_admin_referer($this -> sections -> settings_templates);
                delete_option('tridebugging');
                $this -> update_option('sendas_defaults_postbyemail', 0);

                foreach ($_POST as $key => $val) {
                    switch ($key) {
                        case 'sendas_defaults'		:
                            $sendas_defaults = array();
                            foreach ($val as $sendas_default) {
                                if (!empty($sendas_default['category'])) {
                                    $sendas_defaults[] = array(
                                        'category'				=>	$sendas_default['category'],
                                        'lists'					=>	$sendas_default['lists'],
                                    );
                                }
                            }

                            $sendas_defaults = maybe_serialize($sendas_defaults);
                            $this -> update_option('sendas_defaults', $sendas_defaults);
                            break;
                        // Options without language
                        case 'sendas_defaults_postbyemail'			:
                        case 'sendas_defaults_postbyemailoutput'	:
                            if (!empty($val)) {
                                $this -> update_option($key, $val);
                            }
                            break;
                        default 					:
                            if ($this -> language_do()) {
                                $this -> update_option($key, $this -> language_join($val));
                            } else {
                                $this -> update_option($key, $val);
                            }

                            if (!empty($key) && $key == "debugging") {
                                update_option('tridebugging', 1);
                            }
                            break;
                    }
                }

                $this -> render_message(__('Email template configuration settings have been saved.', 'wp-mailinglist'));
            }

            $this -> delete_all_cache('all');
            $this -> render('settings-templates', false, true, 'admin');
        }

        function admin_settings_system() {
            if (!empty($_POST)) {
                check_admin_referer($this -> sections -> settings_system);

                delete_option('tridebugging');
                $this -> update_option('wpmailconf', 0);
                $this -> update_option('custompostarchive', 0);
                $this -> update_option('importusers_updateall', 0);
                $this -> update_option('timezone_set', 0);

                foreach ($_POST as $key => $val) {
                    $this -> update_option($key, $val);

                    switch ($key) {
                        case 'custompostarchive'	:
                        case 'custompostslug'		:
                            $this -> custom_post_types();
                            flush_rewrite_rules();
                            break;
                        case 'debugging'			:
                            if (!empty($val)) {
                                update_option('tridebugging', 1);
                            }
                            break;
                        case 'commentformlabel'		:
                        case 'registerformlabel'	:
                            if ($this -> language_do()) {
                                $this -> update_option($key, $this -> language_join($val));
                            }
                            break;
                        case 'captchainterval'		:
                            $this -> captchacleanup_scheduling();
                            break;
                        case 'permissions'		:
                            global $wp_roles;
                            $role_names = $wp_roles -> get_names();

                            if (!empty($_POST['permissions'])) {
                                $permissions = map_deep(wp_unslash($_POST['permissions']), 'sanitize_text_field');

                                foreach ($this -> sections as $section_key => $section_menu) {
                                    foreach ($role_names as $role_key => $role_name) {
                                        $wp_roles -> remove_cap($role_key, 'newsletters_' . $section_key);
                                    }

                                    if (!empty($permissions[$section_key])) {
                                        foreach ($permissions[$section_key] as $role) {
                                            $wp_roles -> add_cap($role, 'newsletters_' . $section_key);
                                        }
                                    } else {
                                        /* No roles were selected for this capability, at least add 'administrator' */
                                        $wp_roles -> add_cap('administrator', 'newsletters_' . $section_key);
                                        $permissions[$section_key][] = 'administrator';
                                    }
                                }

                                foreach ($this -> blocks as $block) {
                                    if (!empty($permissions[$block])) {
                                        foreach ($permissions[$block] as $role) {
                                            $wp_roles -> add_cap($role, $block);
                                        }
                                    } else {
                                        $wp_roles -> add_cap('administrator', $block);
                                        $permissions[$block][] = 'administrator';
                                    }
                                }
                            }

                            $this -> update_option('permissions', $permissions);
                            break;
                        case 'importusers'		:
                            $this -> importusers_scheduling();
                            break;
                    }
                }

                $this -> render_message(__('System configuration settings have been saved.', 'wp-mailinglist'));
            }

            $this -> delete_all_cache('all');
            $this -> render('settings-system', false, true, 'admin');
        }

        function admin_settings_tasks() {
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'runschedule'		:
                    if (!empty($_GET['hook'])) {
                        $id = (int) sanitize_text_field(isset($_GET['id'])? $_GET['id'] : 0);
                        $arg = (empty($id)) ? false : $id;
                        $hook = $_GET['hook'];

                        switch ($hook) {
                            case 'wp_queue_process_cron'					:
                                $this -> qp_do_crons();
                                break;
                            case 'wp_import_process_cron'					:
                                do_action($hook);
                                break;
                            default 				:
                                // phpcs:ignore
                                if (preg_match("/(newsletters)/si", $_GET['hook'])) {
                                    $hook = sanitize_text_field(wp_unslash($_GET['hook']));
                                } else {
                                    $hook = $this -> pre . '_' .  sanitize_text_field(wp_unslash($_GET['hook']));
                                }

                                do_action($hook, $arg);
                                break;
                        }

                        $msg_type = 'message';
                        $message = __('Task has been executed successfully!', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No task was specified, please try again.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'reschedule'		:
                    if (!empty($_GET['hook'])) {
                        switch ($_GET['hook']) {
                            case 'wp_queue_process_cron'					:
                                $this -> qp_scheduling();
                                break;
                            case 'wp_import_process_cron'					:
                                $this -> import_process -> scheduling();
                                break;
                            case 'newsletters_countrieshook'				:
                                wp_clear_scheduled_hook('newsletters_countrieshook');
                                $this -> countries_scheduling();
                                break;
                            case 'newsletters_optimizehook'					:
                                $this -> optimize_scheduling();
                                break;
                            case 'cronhook'			:
                                $this -> scheduling();
                                break;
                            case 'pophook'			:
                                $this -> pop_scheduling();
                                break;
                            case 'autoresponders'	:
                                wp_clear_scheduled_hook($this -> pre . '_autoresponders');
                                $this -> autoresponder_scheduling();
                                break;
                            case 'captchacleanup'	:
                                $this -> captchacleanup_scheduling();
                                break;
                            case 'importusers'		:
                                $this -> importusers_scheduling();
                                break;
                        }

                        $msg_type = 'message';
                        $message = __('Task has been rescheduled successfully!', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No task was specified, please try again.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                case 'clearschedule'	:
                    if (!empty($_GET['hook'])) {
                        $hook = sanitize_text_field(wp_unslash($_GET['hook']));
                        switch ($hook) {
                            case 'wp_queue_process_cron'				:
                                wp_clear_scheduled_hook('wp_queue_process_cron');
                                wp_clear_scheduled_hook('wp_queue_process_2_cron');
                                wp_clear_scheduled_hook('wp_queue_process_3_cron');
                                break;
                            case 'wp_import_process_cron'				:
                                wp_clear_scheduled_hook('wp_import_process_cron');
                                break;
                            default 									:
                                // phpcs:ignore
                                if (preg_match("/(newsletters)/si", $_GET['hook'])) {
                                    $hook = sanitize_text_field(wp_unslash($_GET['hook']));
                                } else {
                                    $hook = $this -> pre . '_' . sanitize_text_field(wp_unslash($_GET['hook']));
                                }

                                wp_clear_scheduled_hook($hook);
                                break;
                        }

                        $msg_type = 'message';
                        $message = __('Task has been unscheduled, remember to reschedule as needed.', 'wp-mailinglist');
                    } else {
                        $msg_type = 'error';
                        $message = __('No task was specified, please try again.', 'wp-mailinglist');
                    }

                    $this -> redirect($this -> referer, $msg_type, $message);
                    break;
                default					:
                    $this -> render('settings-cronschedules', false, true, 'admin');
                    break;
            }
        }

        function admin_settings_api() {

            if (!empty($_POST)) {
                $this -> update_option('api_enable', 0);
                $this -> update_option('api_hosts', 0);

                foreach ($_POST as $pkey => $pval) {
                    $this -> update_option($pkey, $pval);
                }

                $message = __('API settings have been saved', 'wp-mailinglist');
                $this -> render_message($message);
            }

            $this -> render('settings' . DS . 'api', false, true, 'admin');
        }


        function admin_view_logs() {
            $this -> render('settings' . DS . 'view_logs', false, true, 'admin');
        }

        function admin_settings_updates() {
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'check'				:
                    delete_transient('newsletters_update_info');
                    $this -> redirect($this -> referer);
                    break;
            }

            $this -> render('settings-updates', false, true, 'admin');
        }

        /* Plugin Extensions Section */
        function admin_extensions() {
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            switch ($method) {
                case 'activate'				:
                    check_admin_referer('newsletters_extension_activate_' . sanitize_text_field(wp_unslash($_GET['plugin'])));
                    activate_plugin(plugin_basename(sanitize_text_field(wp_unslash($_GET['plugin']))));
                    $this -> redirect($this -> url, 'message', __('Extension has been activated.', 'wp-mailinglist'));
                    break;
                case 'deactivate'			:
                    check_admin_referer('newsletters_extension_deactivate_' . sanitize_text_field(wp_unslash($_GET['plugin'])));
                    deactivate_plugins(array(plugin_basename(sanitize_text_field(wp_unslash($_GET['plugin'])))));
                    $this -> redirect($this -> url, 'error', __('Extension has been deactivated.', 'wp-mailinglist'));
                    break;
                default						:
                    $this -> render('extensions' . DS . 'index', false, true, 'admin');
                    break;
            }
        }

        function admin_extensions_settings() {
            $method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
            $method = (!empty($method)) ? $method : false;

            switch ($method) {
                default						:
                    if (!empty($_POST)) {
                        check_admin_referer($this -> sections -> extensions_settings);

                        foreach ($_POST as $pkey => $pval) {
                            $this -> update_option($pkey, $pval);
                        }

                        do_action($this -> pre . '_extensions_settings_saved', $_POST);
                        do_action('newsletters_extensions_settings_saved', $_POST);
                        $this -> render_message(__('Extensions settings have been saved.', 'wp-mailinglist'));
                    }

                    $this -> delete_all_cache('all');
                    $this -> render('extensions' . DS . 'settings', false, true, 'admin');
                    break;
            }
        }

        function admin_help() {
            $this -> render('help', false, true, 'admin');
        }

        function update_plugin_complete_actions($upgrade_actions = null, $plugin = null) {
            $this_plugin = plugin_basename(__FILE__);

            if (!empty($plugin) && $plugin == $this_plugin) {
                $this -> add_option('activation_redirect', true);

                ?>

                esc_html_e('Reactivating and redirecting to about page, please wait...', 'wp-mailinglist'); ?>

                <script>
                    window.onload = function() {
                        window.top.location.href = '<?php echo esc_url_raw( admin_url('index.php?page=newsletters-about')) ?>';
                    }
                </script>

                <?php

                exit();
            }

            return $upgrade_actions;
        }

        function upgrader_process_complete($plugin_upgrader = null, $details = null) {
            if (!empty($details['plugins']) && is_array($details['plugins'])) {
                $this_plugin = plugin_basename(__FILE__);
                $plugins = $details['plugins'];

                foreach ($plugins as $plugin) {
                    if (!empty($plugin) && $plugin == $this_plugin) {
                        $this -> add_option('activation_redirect', true);
                    }
                }
            }
        }

        function activation_hook() {

            // Check the PHP version, we need 5.4+
            if (version_compare(PHP_VERSION, '5.4.0') <= 0) {
                // phpcs:ignore
                _e('PHP version 5.4.0 or higher is required to run this plugin, please upgrade PHP.', 'wp-mailinglist');
                exit(); die();
            }

            $this -> ci_initialization();
            $this -> add_option('activation_redirect', true);

            //$this -> check_plugin_folder();
        }

        function custom_redirect() {
            //$this -> check_plugin_folder();

            $activation_redirect = $this -> get_option('activation_redirect');
            if (is_admin() && !defined('DOING_AJAX') && !empty($activation_redirect)) {
                $this -> delete_option('activation_redirect');
                wp_cache_flush();
                // phpcs:ignore
                exit(wp_redirect(admin_url('index.php') . "?page=newsletters-about"));
            }
        }

        function check_plugin_folder() {
            // Let's see if the plugin needs to be renamed
            $plugin_dir = dirname(plugin_basename(__FILE__));
            if (!empty($plugin_dir) && $plugin_dir != "wp-mailinglist") {
                $source = WP_PLUGIN_DIR . DS . $plugin_dir;
                $destination = WP_PLUGIN_DIR . DS . 'wp-mailinglist';

                if (file_exists($source)) {
                    global $wp_filesystem;

                    if (empty($wp_filesystem)) {
                        require_once (ABSPATH . '/wp-admin/includes/file.php');
                        WP_Filesystem();
                    }

                    if ($wp_filesystem -> move($source, $destination, true)) {
                        $plugin_path = 'wp-mailinglist/wp-mailinglist.php';
                        header("Location: " . html_entity_decode(wp_nonce_url('plugins.php?action=activate&plugin=' . urlencode($plugin_path), 'activate-plugin_' . $plugin_path)));
                        exit();
                    }
                }
            }
        }

        function __construct($data = array()) {
            parent::__construct();

            $url = explode("&", sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])));
            $this -> fullurl = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
            $this -> url = $url[0];

            if (!empty($_SERVER['HTTP_REFERER'])) {
                $this -> referer = sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER']));
            }

            $this -> plugin_file = plugin_basename(__FILE__);
            $base = basename(NEWSLETTERS_DIR);
            $this -> register_plugin($base, __FILE__);
            $url = explode("&", sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])));
            $this -> url = $url[0];
        }

        function activated_plugin($plugin = null, $network = null) {

            return true;
        }
    }
}

/* Include the necessary class files */
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'mailinglist.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'subscriber.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'bounce.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'unsubscribe.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'latestpost.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'history.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'histories_list.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'histories_attachment.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'email.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'queue.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'theme.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'template.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'post.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'order.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'field.php');
require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'fields_list.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'subscribers_list.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'country.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'autoresponder.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'autoresponders_list.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'autoresponderemail.php');
//require_once(NEWSLETTERS_DIR . DS . 'models' . DS . 'group.php');
require_once(NEWSLETTERS_DIR . DS . 'vendors' . DS . 'class.pagination.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'db.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'html.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'form.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'metabox.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'shortcode.php');
require_once(NEWSLETTERS_DIR . DS . 'helpers' . DS . 'auth.php');

// Async and Background Processes
require_once(NEWSLETTERS_DIR . DS . 'vendors' . DS . 'processing' . DS . 'wp-async-request.php');
require_once(NEWSLETTERS_DIR . DS . 'vendors' . DS . 'processing' . DS . 'wp-background-process.php');

//initialize the wpMail class
global $wpMail;
if (empty($wpMail)) {
    $wpMail = new wpMail();
}

if (!function_exists('WPMAIL')) {
    function WPMAIL($params = null) {
        //return new wpMail($params);
        global $wpMail;
        return $wpMail;
    }
}

require_once(NEWSLETTERS_DIR . DS . 'wp-mailinglist-api.php');
require_once(NEWSLETTERS_DIR . DS . 'wp-mailinglist-functions.php');
require_once(NEWSLETTERS_DIR . DS . 'wp-mailinglist-widget.php');

register_activation_hook(plugin_basename(__FILE__), array($wpMail, 'activation_hook'));
add_filter('update_plugin_complete_actions', array($wpMail, 'update_plugin_complete_actions'), 10, 2);
add_action('upgrader_process_complete', array($wpMail, 'upgrader_process_complete'), 10, 2);
register_activation_hook(plugin_basename(__FILE__), array($wpMail, 'update_options'));
