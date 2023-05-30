<?php

defined('ABSPATH') || exit;

class NewsletterUsers extends NewsletterModule {

    static $instance;

    /**
     * @return NewsletterUsers
     */
    static function instance() {
        if (self::$instance == null) {
            self::$instance = new NewsletterUsers();
        }

        return self::$instance;
    }

    function __construct() {
        parent::__construct('users', '1.3.0');
        if (is_admin()) {
            add_action('wp_ajax_newsletter_users_export', array($this, 'hook_wp_ajax_newsletter_users_export'));
        }
    }

    function hook_wp_ajax_newsletter_users_export() {

        $newsletter = Newsletter::instance();
        if ($newsletter->is_allowed()) {
            require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
            $controls = new NewsletterControls();

            if ($controls->is_action('export')) {
                $this->export($controls->data);
            }
        } else {
            die('Not allowed.');
        }
    }

    function upgrade() {
        global $wpdb, $charset_collate;

        parent::upgrade();

        $sql = "CREATE TABLE `" . $wpdb->prefix . "newsletter` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `token` varchar(50) NOT NULL DEFAULT '',
  `language` varchar(10) NOT NULL DEFAULT '',
  `status` varchar(1) NOT NULL DEFAULT 'S',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile` mediumtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` int(11) NOT NULL DEFAULT '0',
  `last_activity` int(11) NOT NULL DEFAULT '0',
  `followup_step` tinyint(4) NOT NULL DEFAULT '0',
  `followup_time` bigint(20) NOT NULL DEFAULT '0',
  `followup` tinyint(4) NOT NULL DEFAULT '0',
  `surname` varchar(100) NOT NULL DEFAULT '',
  `sex` char(1) NOT NULL DEFAULT 'n',
  `feed_time` bigint(20) NOT NULL DEFAULT '0',
  `feed` tinyint(4) NOT NULL DEFAULT '0',
  `referrer` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `wp_user_id` int(11) NOT NULL DEFAULT '0',
  `http_referer` varchar(255) NOT NULL DEFAULT '',
  `geo` tinyint(4) NOT NULL DEFAULT '0',
  `country` varchar(4) NOT NULL DEFAULT '',
  `region` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `bounce_type` varchar(50) NOT NULL DEFAULT '',
  `bounce_time` int(11) NOT NULL DEFAULT '0',
  `unsub_email_id` int(11) NOT NULL DEFAULT '0',  
  `unsub_time` int(11) NOT NULL DEFAULT '0',\n";

        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            $sql .= "`list_$i` tinyint(4) NOT NULL DEFAULT '0',\n";
        }

        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            $sql .= "`profile_$i` varchar(255) NOT NULL DEFAULT '',\n";
        }
        // Leave as last
        $sql .= "`test` tinyint(4) NOT NULL DEFAULT '0',\n";
        $sql .= "PRIMARY KEY (`id`),\nUNIQUE KEY `email` (`email`),\nKEY `wp_user_id` (`wp_user_id`)\n) $charset_collate;";

        dbDelta($sql);

        if ($this->old_version < '1.2.7') {
            $this->query("update " . NEWSLETTER_USERS_TABLE . " set geo=1 where country<>''");
        }
        if ($this->old_version > '1.2.5' && $this->old_version < '1.2.9') {
            $this->upgrade_query("ALTER TABLE " . NEWSLETTER_USERS_TABLE . " DROP COLUMN last_ip;");
        }
    }

    function admin_menu() {
        $this->add_menu_page('index', __('Subscribers', 'newsletter'), 1);
        $this->add_admin_page('new', __('New subscriber', 'newsletter'));
        $this->add_admin_page('edit', __('Subscriber Edit', 'newsletter'));
        $this->add_admin_page('massive', __('Subscribers Maintenance', 'newsletter'));
        $this->add_admin_page('export', __('Export', 'newsletter'));
        $this->add_admin_page('import', __('Import', 'newsletter'));
        $this->add_admin_page('statistics', __('Statistics', 'newsletter'));
    }

    function export($options = null) {
        global $wpdb;

        @setlocale(LC_CTYPE, 'en_US.UTF-8');
        header('Content-Type: application/octet-stream;charset=UTF-8');
        header('Content-Disposition: attachment; filename="newsletter-subscribers.csv"');

        // BOM
        echo "\xEF\xBB\xBF";

        $sep = ';';
        if ($options) {
            $sep = $options['separator'];
        }
        if ($sep == 'tab') {
            $sep = "\t";
        }

        // CSV header
        echo '"Email"' . $sep . '"Name"' . $sep . '"Surname"' . $sep . '"Gender"' . $sep . '"Status"' . $sep . '"Date"' . $sep . '"Token"' . $sep;

        // In table profiles
        for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) {
            echo '"Profile ' . $i . '"' . $sep; // To adjust with field name
        }

        // Lists
        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
            echo '"List ' . $i . '"' . $sep;
        }

        echo '"Feed by mail"' . $sep . '"Follow up"' . $sep;
        echo '"IP"' . $sep . '"Referrer"' . $sep . '"Country"' . $sep . '"Language"';

        echo "\n";

        $page = 0;
        while (true) {
            $query = "select * from " . NEWSLETTER_USERS_TABLE . "";
            $list = (int) $_POST['options']['list'];
            if (!empty($list)) {
                $query .= " where list_" . $list . "=1";
            }
            $recipients = $wpdb->get_results($query . " order by email limit " . $page * 500 . ",500");
            for ($i = 0; $i < count($recipients); $i++) {
                echo '"' . $recipients[$i]->email . '"' . $sep . '"' . $this->sanitize_csv($recipients[$i]->name) .
                '"' . $sep . '"' . $this->sanitize_csv($recipients[$i]->surname) .
                '"' . $sep . '"' . $recipients[$i]->sex .
                '"' . $sep . '"' . $recipients[$i]->status . '"' . $sep . '"' . $recipients[$i]->created . '"' . $sep . '"' . $recipients[$i]->token . '"' . $sep;

                for ($j = 1; $j <= NEWSLETTER_PROFILE_MAX; $j++) {
                    $column = 'profile_' . $j;
                    echo '"' . $this->sanitize_csv($recipients[$i]->$column) . '"' . $sep;
                }

                for ($j = 1; $j <= NEWSLETTER_LIST_MAX; $j++) {
                    $list = 'list_' . $j;
                    echo '"' . $recipients[$i]->$list . '"' . $sep;
                }

                echo '"' . $recipients[$i]->feed . '"' . $sep;
                echo '"' . $recipients[$i]->followup . '"' . $sep;
                echo '"' . $recipients[$i]->ip . '"' . $sep;
                echo '"' . $recipients[$i]->referrer . '"' . $sep;
                echo '"' . $recipients[$i]->country . '"' . $sep;
                echo '"' . $recipients[$i]->language . '"' . $sep;

                echo "\n";
                flush();
            }
            if (count($recipients) < 500) {
                break;
            }
            $page++;
        }
        die();
    }

    function sanitize_csv($text) {
        $text = str_replace('"', "'", $text);
        $text = str_replace("\n", ' ', $text);
        $text = str_replace("\r", ' ', $text);
        $text = str_replace(";", ' ', $text);

        // Do you wonder? Excel...
        $first = substr($text, 0, 1);
        if ($first == '=' || $first == '+' || $first == '-' || $first == '@') {
            $text = "'" . $text;
        }

        return $text;
    }

    /**
     * @param array $args
     * @param string $format
     *
     * @return array|object|null
     */
    function get_users($args, $format = OBJECT) {
        global $wpdb;

        $default_args = array(
            'page' => 1,
            'per_page' => 10
        );

        $args = array_merge($default_args, $args);

        $query = 'SELECT * FROM ' . NEWSLETTER_USERS_TABLE . ' ';
        $query_args = [];

        $query .= ' LIMIT %d OFFSET %d';
        $query_args[] = (int) $args['per_page'];
        $query_args[] = ( (int) $args['page'] - 1 ) * (int) $args['per_page'];

        $records = $wpdb->get_results($wpdb->prepare($query, $query_args), $format);

        if ($wpdb->last_error) {
            $this->logger->error($wpdb->last_error);

            return null;
        }

        return $records;
    }

    /**
     * Check if email exists
     *
     * @param string $email
     *
     * @return bool
     */
    function email_exists($email) {

        $email = parent::normalize_email($email);
        $user = parent::get_user($email);

        return $user ? true : false;
    }
    
    /**
     * 
     * @global type $wpdb
     * @return TNP_Subscribers_Stats
     */
    function get_stats() {
        global $wpdb;
        
        return $wpdb->get_row("select count(*) as total,
count(case when status='C' then 1 else null end) as confirmed,
count(case when status='S' then 1 else null end) as unconfirmed,
count(case when status='B' then 1 else null end) as bounced,
count(case when status='P' then 1 else null end) as complained

from " . NEWSLETTER_USERS_TABLE);
                
    }

}

class TNP_Subscribers_Stats {
    var $total;
    var $confirmed;
    var $unconfirmed;
    var $bounced;
}

NewsletterUsers::instance();
