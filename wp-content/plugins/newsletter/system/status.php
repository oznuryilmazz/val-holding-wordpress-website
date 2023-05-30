<?php
/* @var $this NewsletterSystem */
/* @var $wpdb wpdb */

defined('ABSPATH') || exit;

wp_enqueue_script('tnp-chart');

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$newsletter = Newsletter::instance();
$mailer = $newsletter->get_mailer();

if ($controls->is_action('conversion')) {
    $this->logger->info('Maybe convert to utf8mb4');
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    if (function_exists('maybe_convert_table_to_utf8mb4')) {
        $r = maybe_convert_table_to_utf8mb4(NEWSLETTER_EMAILS_TABLE);
        if (!$r) {
            $controls->errors .= 'It was not possible to run the conversion for the table ' . NEWSLETTER_EMAILS_TABLE . ' - ';
            $controls->errors .= $wpdb->last_error . '<br>';
        }
        $r = maybe_convert_table_to_utf8mb4(NEWSLETTER_USERS_TABLE);
        if (!$r) {
            $controls->errors .= 'It was not possible to run the conversion for the table ' . NEWSLETTER_EMAILS_TABLE . ' - ';
            $controls->errors .= $wpdb->last_error . '<br>';
        }
        $controls->messages .= 'Done.';
    } else {
        $controls->errors = 'Table conversion function not available';
    }
}

if ($controls->is_action('reset_dismissed')) {
    update_option('newsletter_dismissed', [], false);
    $controls->add_message_done();
}

if ($controls->is_action('reset_send_stats')) {
    $this->reset_send_stats();
    $controls->add_message_done();
}

if ($controls->is_action('reset_warnings')) {
    $this->reset_warnings();
    $controls->add_message_done();
}

if ($controls->is_action('stats_email_column_upgrade')) {
    $this->query("alter table " . NEWSLETTER_STATS_TABLE . " drop index email_id");
    $this->query("alter table " . NEWSLETTER_STATS_TABLE . " drop index user_id");
    $this->query("alter table `" . NEWSLETTER_STATS_TABLE . "` modify column `email_id` int(11) not null default 0");
    $this->query("create index email_id on " . NEWSLETTER_STATS_TABLE . " (email_id)");
    $this->query("create index user_id on " . NEWSLETTER_STATS_TABLE . " (user_id)");
    $controls->add_message_done();
    update_option('newsletter_stats_email_column_upgraded', true);
}

// Compute the number of newsletters ongoing and other stats
$emails = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where status='sending' and send_on<" . time() . " order by id asc");
$total = 0;
$queued = 0;
foreach ($emails as $email) {
    $total += $email->total;
    $queued += $email->total - $email->sent;
}
$speed = $newsletter->get_send_speed();

// Trick to access the private function (!)
class TNP_WPDB extends wpdb {

    public function get_table_charset($table) {
        return parent::get_table_charset($table);
    }

}

$tnp_wpdb = new TNP_WPDB(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

function tnp_describe_table($table) {
    global $wpdb;
    $rs = $wpdb->get_results("show full columns from " . esc_sql($table));
    ?>
    <table class="tnp-db-table">
        <thead>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Collation</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rs as $r) { ?>
                <tr>
                    <td><?php echo esc_html($r->Field) ?></td>
                    <td><?php echo esc_html($r->Type) ?></td>
                    <td><?php echo esc_html($r->Collation) ?></td>
                    <td><?php echo esc_html($r->Null) ?></td>
                    <td><?php echo esc_html($r->Key) ?></td>
                    <td><?php echo esc_html($r->Default) ?></td>
                    <td><?php echo esc_html($r->Extra) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}
?>

<style>
<?php include __DIR__ . '/css/system.css' ?>
</style>

<div class="wrap tnp-system tnp-system-status" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('System Status', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <p>
                <?php $controls->btn('reset_warnings', __('Reset warnings', 'newsletter')) ?>
            </p>
            <h3>Delivery</h3>
            <table class="widefat" id="tnp-status-table">

                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Note</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Delivering</td>
                        <td class="status">
                            &nbsp;
                        </td>
                        <td>
                            <?php if (count($emails)) { ?>
                                Delivering <?php echo count($emails) ?> newsletters to about <?php echo $queued ?> recipients.
                                At speed of <?php echo $speed ?> emails per hour it will take <?php printf('%.1f', $queued / $speed) ?> hours to finish.

                            <?php } else { ?>
                                Nothing delivering right now
                            <?php } ?>
                        </td>

                    </tr>
                    <tr>
                        <td>Mailer</td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            <?php echo esc_html($mailer->get_description()) ?>
                        </td>
                    </tr>
                    <?php
                    $stats = $this->get_send_stats();

                    if ($stats) {
                        $condition = $stats->mean > 5 ? 2 : 1;
                        ?>
                        <tr>
                            <td id="tnp-speed">
                                Send details
                            </td>
                            <td class="status">
                                <?php $this->condition_flag($condition) ?>

                            </td>
                            <td>
                                <?php if ($condition == 2) { ?>
                                    <strong>Sending an email is taking more than 5 seconds (by mean), rather slow.</strong>
                                    <a href="https://www.thenewsletterplugin.com/documentation/installation/status-panel/#email-speed" target="_blank">Read more</a>.
                                    <br>
                                <?php } ?>
                                Average time to send an email: <?php echo $stats->mean ?> seconds<br>
                                <?php if ($stats->mean > 0) { ?>
                                    Max speed: <?php echo sprintf("%.2f", 1.0 / $stats->mean * 3600) ?> emails per hour<br>
                                <?php } ?>

                                Max mean time measured: <?php echo $stats->max ?> seconds<br>
                                Min mean time measured: <?php echo $stats->min ?> seconds<br>
                                Total emails in the sample: <?php echo $stats->total_emails ?><br>
                                Total sending time: <?php echo $stats->total_time ?> seconds<br>
                                Runs in the sample: <?php echo $stats->total_runs ?><br>
                                Runs prematurely interrupted: <?php echo $stats->interrupted ?><br>


                                <canvas id="tnp-send-chart" style="width: 550px; height: 150px"></canvas>
                                <script>
                                    jQuery(function () {
                                        var sendChartData = {
                                            labels: <?php echo json_encode(range(1, count($stats->means))) ?>,
                                            datasets: [
                                                {
                                                    label: "Batch Average Time",
                                                    data: <?php echo json_encode($stats->means) ?>,
                                                    borderColor: '#2980b9',
                                                    fill: false
                                                }/*,
                                                 {
                                                 label: "Batch Average Time",
                                                 data: <?php echo json_encode($stats->sizes) ?>,
                                                 borderColor: '#b98028',
                                                 fill: false      
                                                 }*/]
                                        };
                                        var sendChartConfig = {
                                            type: "line",
                                            data: sendChartData,
                                            options: {
                                                responsive: false,
                                                maintainAspectRatio: false
                                            }
                                        };
                                        new Chart('tnp-send-chart', sendChartConfig);
                                    });
                                </script>
                                <br>
                                <?php $controls->button_reset('reset_send_stats') ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td>
                                Sending statistics
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                Not enough data available.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>General checks</h3>
            <table class="widefat" id="tnp-status-table">

                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Note</th>
                    </tr>

                </thead>

                <tbody>
                    <tr>
                        <td>
                            General notices and warnings
                        </td>
                        <td>

                        </td>
                        <td>
                            <?php $controls->btn('reset_dismissed', 'Restore', ['secondary' => true]) ?>
                        </td>
                    </tr>

                    <tr>
                        <?php
                        $tnp_page_id = $newsletter->get_newsletter_page_id();
                        $page = $newsletter->get_newsletter_page();
                        $condition = 1;
                        if ($tnp_page_id) {
                            if (!$page || $page->post_status !== 'publish') {
                                $condition = 0;
                            }
                        } else {
                            $condition = 2;
                        }
                        ?>
                        <td>
                            Dedicated page<br>
                            <small>The blog page Newsletter uses for messages</small>
                        </td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($condition == 2) { ?>
                                Newsletter is using a neutral page to show messages, if you want to use a dedicated page, configure it on
                                <a href="?page=newsletter_main_main">main settings</a>.
                            <?php } else if ($condition == 0) { ?>
                                A dedicated page is set but it is no more available or no more published. Review the dedicated page on
                                <a href="?page=newsletter_main_main">main settings</a>.
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <?php
                        $page_id = $newsletter->get_newsletter_page_id();
                        $page = $newsletter->get_newsletter_page();
                        $condition = 1;
                        if ($page_id) {
                            if (!$page) {
                                $condition = 0;
                            } else {
                                $content = $page->post_content;
                                if (strpos($content, '[newsletter]') === false && strpos($content, '[newsletter ') === false) {
                                    $condition = 2;
                                }
                            }
                        }
                        ?>
                        <td>
                            Dedicated page content<br>
                        </td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($condition == 2) { ?>
                                The page seems to not contain the <code>[newsletter]</code>, but sometime it cannot be detected if you use
                                a visual composer. <a href="post.php?post=<?php echo $page->ID ?>&action=edit" target="_blank">Please, check the page</a>.
                            <?php } else if ($condition == 0) { ?>
                                The dedicated page seems to not be available.
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $method = '';
                    if (function_exists('get_filesystem_method')) {
                        $method = get_filesystem_method(array(), WP_PLUGIN_DIR);
                    }
                    if (empty($method))
                        $condition = 2;
                    else if ($method == 'direct')
                        $condition = 1;
                    else
                        $condition = 0;
                    ?>
                    <tr>
                        <td>Add-ons installable</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($condition == 2) { ?>
                                No able to check, just try the add-ons manager one click install
                            <?php } else if ($condition == 1) { ?>
                                The add-ons manager can install our add-ons
                            <?php } else { ?>
                                The plugins dir could be read-only, you can install add-ons uploading the package from the
                                plugins panel (or uploading them directly via FTP). This is unusual you should ask te provider
                                about file and folder permissions.
                            <?php } ?>
                        </td>

                    </tr>


                    <?php
                    $return_path = $newsletter->options['return_path'];
                    if (!empty($return_path)) {
                        list($return_path_local, $return_path_domain) = explode('@', $return_path);
                    }
                    $sender = $newsletter->options['sender_email'];
                    if (!empty($sender)) {
                        list($sender_local, $sender_domain) = explode('@', $sender);
                    }
                    ?>
                    <tr>
                        <td>Return path</td>
                        <td>
                            <?php if (empty($return_path)) { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } else { ?>
                                <?php if ($sender_domain != $return_path_domain) { ?>
                                    <span class="tnp-maybe">MAYBE</span>
                                <?php } else { ?>
                                    <span class="tnp-ok">OK</span>
                                <?php } ?>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (!empty($return_path)) { ?>
                                Some providers require the return path domain <code><?php echo esc_html($return_path_domain) ?></code> to be identical
                                to the sender domain <code><?php echo esc_html($sender_domain) ?></code>. See the main settings.
                            <?php } else { ?>
                            <?php } ?>
                        </td>

                    </tr>





                    <tr>
                        <?php
                        $condition = NEWSLETTER_EXTENSION_UPDATE ? 1 : 0;
                        ?>
                        <td>Addons update</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($condition == 0) { ?>
                                Newsletter Addons update is disabled (probably in your <code>wp-config.php</code> file the constant
                                <code>NEWSLETTER_EXTENSION_UPDATE</code> is set to <code>true</code>)
                            <?php } else { ?>
                                Newsletter Addons can be updated
                            <?php } ?>
                        </td>

                    </tr>

                    <tr>
                        <?php
                        $res = true;
                        $response = wp_remote_get('http://www.thenewsletterplugin.com/wp-content/extensions.json');
                        $condition = 1;
                        if (is_wp_error($response)) {
                            $res = false;
                            $condition = 0;
                            $message = $response->get_error_message();
                        } else {
                            if (wp_remote_retrieve_response_code($response) != 200) {
                                $res = false;
                                $condition = 0;
                                $message = wp_remote_retrieve_response_message($response);
                            }
                        }
                        ?>

                        <td>
                            Addons version check<br>
                            <small>Your blog can check the professional addon updates?</small>
                        </td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($condition == 0) { ?>
                                The blog cannot contact www.thenewsletterplugin.com to check the license or the extension versions.<br>
                                Error: <?php echo esc_html($message) ?><br>
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>









                    <?php /*
                      $memory = intval(WP_MEMORY_LIMIT);
                      if (false !== strpos(WP_MEMORY_LIMIT, 'G'))
                      $memory *= 1024;
                      ?>
                      <tr>
                      <td>
                      PHP memory limit
                      </td>
                      <td>
                      <?php if ($memory < 64) { ?>
                      <span class="tnp-ko">MAYBE</span>
                      <?php } else if ($memory < 128) { ?>
                      <span class="tnp-maybe">MAYBE</span>
                      <?php } else { ?>
                      <span class="tnp-ok">OK</span>
                      <?php } ?>
                      </td>
                      <td>
                      WordPress WP_MEMORY_LIMIT is set to <?php echo $memory ?> megabyte but your PHP setting could allow more than that.
                      Anyway we suggest to set the value to at least 64M.
                      <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                      <?php if ($memory < 64) { ?>
                      This value is too low you should increase it adding <code>define('WP_MEMORY_LIMIT', '64M');</code> to your <code>wp-config.php</code>.
                      <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                      <?php } else if ($memory < 128) { ?>
                      The value should be fine, it depends on how many plugins you're running and how many resource are required by your theme.
                      Blank pages may happen with low memory problems. Eventually increase it adding <code>define('WP_MEMORY_LIMIT', '128M');</code>
                      to your <code>wp-config.php</code>.
                      <a href="https://www.thenewsletterplugin.com/documentation/status-panel#status-memory" target="_blank">Read more</a>.
                      <?php } else { ?>

                      <?php } ?>

                      </td>
                      </tr>
                     */ ?>

                    <?php
                    $ip = gethostbyname($_SERVER['HTTP_HOST']);
                    $name = gethostbyaddr($ip);
                    $res = true;
                    if (strpos($name, '.secureserver.net') !== false) {
                        //$smtp = get_option('newsletter_main_smtp');
                        //if (!empty($smtp['enabled']))
                        $res = false;
                        $message = 'If you\'re hosted with GoDaddy, be sure to set their SMTP (relay-hosting.secureserver.net, without username and password) to send emails
                                    on Newsletter SMTP panel.
                                    Remember they limits you to 250 emails per day. Open them a ticket for more details.';
                    }
                    if (strpos($name, '.aruba.it') !== false) {
                        $res = false;
                        $message = 'If you\'re hosted with Aruba consider to use an external SMTP (Sendgrid, Mailjet, Mailgun, Amazon SES, Elasticemail, Sparkpost, ...)
                                    since their mail service is not good. If you have your personal email with them, you can try to use the SMTP of your
                                    pesonal account. Ask the support for the SMTP parameters and configure them on Newsletter SMTP panel.';
                    }
                    ?>
                    <tr>
                        <td>Your Server</td>
                        <td>
                            <?php if ($res === false) { ?>
                                <span class="tnp-maybe">MAYBE</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>


                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                <?php echo $message ?>
                            <?php } else { ?>

                            <?php } ?>
                            IP: <?php echo $ip ?><br>
                            Name: <?php echo $name ?><br>
                        </td>
                    </tr>

                    <?php
                    wp_mkdir_p(NEWSLETTER_LOG_DIR);
                    $condition = is_dir(NEWSLETTER_LOG_DIR) && is_writable(NEWSLETTER_LOG_DIR) ? 1 : 0;
                    if ($condition) {
                        @file_put_contents(NEWSLETTER_LOG_DIR . '/test.txt', "");
                        $condition = is_file(NEWSLETTER_LOG_DIR . '/test.txt') ? 1 : 0;
                        if ($condition) {
                            @unlink(NEWSLETTER_LOG_DIR . '/test.txt');
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            Log folder
                        </td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            The log folder is <?php echo esc_html(NEWSLETTER_LOG_DIR) ?><br>
                            <?php if (!$res) { ?>
                                Cannot create the folder or it is not writable.
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3>Filters</h3>
            <table class="widefat" id="tnp-status-table">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>newsletter_message_headers</code></td>
                        <td>
                            <?php echo has_filter('newsletter_message_headers') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_message_headers') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_message_html</code></td>
                        <td>
                            <?php echo has_filter('newsletter_message_html') ? '' : '-' ?><br>
                            <?php echo $this->get_hook_functions('newsletter_message_html') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_message_text</code></td>
                        <td>
                            <?php echo has_filter('newsletter_message_text') ? '' : '-' ?><br>
                            <?php echo $this->get_hook_functions('newsletter_message_text') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_message_subject</code></td>
                        <td>
                            <?php echo has_filter('newsletter_message_subject') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_message_subject') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_message</code></td>
                        <td>
                            <?php echo has_filter('newsletter_message') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_message') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_replace</code></td>
                        <td>
                            <?php echo has_filter('newsletter_replace') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_replace') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_replace_name</code></td>
                        <td>
                            <?php echo has_filter('newsletter_replace_name') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_replace_name') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_current_language</code></td>
                        <td>
                            <?php echo has_filter('newsletter_current_language') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_current_language') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_languages</code></td>
                        <td>
                            <?php echo has_filter('newsletter_languages') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_languages') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_languages</code></td>
                        <td>
                            <?php echo has_filter('newsletter_is_multilanguage') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_is_multilanguage') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>newsletter_send_user</code></td>
                        <td>
                            <?php echo has_filter('newsletter_send_user') ? '' : '-' ?>
                            <?php echo $this->get_hook_functions('newsletter_send_user') ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <h3>3rd party plugins</h3>
            <table class="widefat" id="tnp-status-table">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th></th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_plugin_active('plugin-load-filter/plugin-load-filter.php')) { ?>
                        <tr>
                            <td><a href="https://wordpress.org/plugins/plugin-load-filter/" target="_blank">Plugin load filter</a></td>
                            <td>
                                <span class="tnp-maybe">MAY BE</span>
                            </td>
                            <td>
                                Be sure Newsletter is set as active on EVERY context.
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if (is_plugin_active('wp-asset-clean-up/wpacu.php')) { ?>
                        <tr>
                            <td><a href="https://wordpress.org/plugins/wp-asset-clean-up/" target="_blank">WP Asset Clean Up</a></td>
                            <td>
                                <span class="tnp-maybe">MAY BE</span>
                            </td>
                            <td>
                                Be sure Newsletter is set as active on EVERY context.
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if (is_plugin_active('freesoul-deactivate-plugins/freesoul-deactivate-plugins.php')) { ?>
                        <tr>
                            <td><a href="https://wordpress.org/plugins/freesoul-deactivate-plugins/" target="_blank">Freesoul Deactivate Plugins</a></td>
                            <td>
                                <span class="tnp-maybe">MAY BE</span>
                            </td>
                            <td>
                                Be sure Newsletter is set as active on EVERY context.
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>


            <h3>WordPress</h3>

            <table class="widefat" id="tnp-status-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <?php
                        $condition = (defined('WP_DEBUG') && WP_DEBUG) ? 2 : 1;
                        ?>
                        <td>
                            WordPress debug mode
                        </td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if (defined('WP_DEBUG') && WP_DEBUG) { ?>
                                WordPress is in debug mode it is not recommended on a production system. See the constant <code>WP_DEBUG</code> inside the <code>wp-config.php</code>.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>



                    <tr>
                        <?php
                        $charset = get_option('blog_charset');
                        $condition = $charset === 'UTF-8' ? 1 : 0;
                        ?>
                        <td>Blog Charset</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            Charset: <?php echo esc_html($charset) ?>
                            <br>

                            <?php if ($condition == 1) { ?>

                            <?php } else { ?>
                                It is recommended to use
                                the <code>UTF-8</code> charset but the <a href="https://codex.wordpress.org/Converting_Database_Character_Sets" target="_blank">conversion</a>
                                could be tricky. If you're not experiencing problem, leave things as is.
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <?php
                        $condition = (strpos(home_url('/'), 'http') !== 0) ? 0 : 1;
                        ?>
                        <td>Home URL</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            Value: <?php echo home_url('/'); ?>
                            <br>
                            <?php if ($condition == 0) { ?>
                                Your home URL is not absolute, emails require absolute URLs.
                                Probably you have a protocol agnostic plugin installed to manage both HTTPS and HTTP in your
                                blog.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <?php
                        $condition = (strpos(WP_CONTENT_URL, 'http') !== 0) ? 0 : 1;
                        ?>
                        <td>WP_CONTENT_URL</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            Value: <?php echo esc_html(WP_CONTENT_URL); ?>
                            <br>
                            <?php if ($condition == 0) { ?>
                                Your content URL is not absolute, emails require absolute URLs when they have images inside.
                                Newsletter tries to deal with this problem but when a problem with images persists, you should try to remove
                                from your <code>wp-config.php</code> the <code>WP_CONTENT_URL</code> define and check again.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <?php
                        $attachments = get_posts([
                            'post_type' => 'attachment',
                            'post_mime_type' => 'image',
                            'numberposts' => 1,
                            'post_status' => null,
                            'post_parent' => null
                        ]);
                        $condition = 1;
                        $src = 'No media found to make a test';
                        if ($attachments) {
                            $src = wp_get_attachment_image_src($attachments[0]->ID);
                            $src = $src[0];
                            $condition = (strpos($src, 'http') !== 0) ? 0 : 1;
                        }
                        ?>
                        <td>Images URL</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            Example: <?php echo esc_html($src); ?>

                            <?php if ($condition == 0) { ?>
                                <br><br>
                                Your uploaded images seems to be returned with a relative URL: they won't work in your newsletter. Check the <code>WP_CONTENT_URL</code>
                                above and fix it if is showing a warning. If not, probably a plugin or some custom code is forcing relative URLs for your
                                images. Check that with your site developer.
                            <?php } else { ?>

                            <?php } ?>
                        </td>
                    </tr>



                    <tr>
                        <?php
                        $uploads = wp_upload_dir();
                        ?>
                        <td>Uploads dir and url</td>
                        <td>

                        </td>
                        <td>
                            <table class="widefat" style="width: auto">
                                <thead>
                                    <tr><th>Key</th><th>Value</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($uploads as $k => $v) { ?>
                                        <tr><td><?php echo esc_html($k) ?></td><td><?php echo esc_html($v) ?></td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <?php
                        set_transient('newsletter_transient_test', 1, 300);
                        delete_transient('newsletter_transient_test');
                        $res = get_transient('newsletter_transient_test');
                        $condition = ($res !== false) ? 0 : 1;
                        ?>
                        <td>WordPress transients</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($res !== false) { ?>
                                Transients cannot be deleted. This can block the delivery engine. Usually it is due to a not well coded plugin installed.
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>



            <h3>PHP</h3>
            <table class="widefat" id="tnp-status-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PHP version</td>
                        <td>
                            <?php if (version_compare(phpversion(), '5.6', '<')) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Your PHP version is <?php echo phpversion() ?><br>
                            <?php if (version_compare(phpversion(), '5.3', '<')) { ?>
                                Newsletter plugin works correctly with PHP version 5.6 or greater. Ask your provider to upgrade your PHP. Your version is
                                unsupported even by the PHP community.
                            <?php } ?>
                        </td>

                    </tr>

                    <tr>
                        <?php
                        $value = (int) ini_get('max_execution_time');
                        $res = true;
                        $condition = 1;
                        if ($value != 0 && $value < NEWSLETTER_CRON_INTERVAL) {
                            $res = set_time_limit(NEWSLETTER_CRON_INTERVAL);
                            if ($res)
                                $condition = 1;
                            else
                                $condition = 0;
                        }
                        ?>
                        <td>PHP execution time limit</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if (!$res) { ?>
                                Your PHP execution time limit is <?php echo $value ?> seconds. It cannot be changed and it is too lower to grant the maximum delivery rate of Newsletter.
                            <?php } else { ?>
                                Your PHP execution time limit is <?php echo $value ?> seconds and can be changed by the Newsletter plugin.<br>
                            <?php } ?>

                        </td>

                    </tr>


                    <tr>
                        <?php
                        $condition = function_exists('curl_version');
                        ?>
                        <td>Curl version</td>
                        <td>
                            <?php if (!$condition) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            <?php
                            if (!$condition) {
                                echo 'cUrl is not available, ask the provider to install it and activate the PHP cUrl library';
                            } else {
                                $version = curl_version();
                                echo 'Version: ' . $version['version'] . '<br>';
                                echo 'SSL Version: ' . $version['ssl_version'] . '<br>';
                            }
                            ?>
                        </td>

                    </tr>
                    <?php if (ini_get('opcache.validate_timestamps') === '0') { ?>
                        <tr>
                            <td>
                                Opcache
                            </td>

                            <td>
                                <span class="tnp-ko">KO</span>
                            </td>

                            <td>
                                You have the PHP opcache active with file validation disable so every blog plugins update needs a webserver restart!
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3>Database</h3>
            <table class="widefat" id="tnp-status-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Database Charset</td>
                        <td>
                            <?php if ($wpdb->charset != 'utf8mb4') { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            Charset: <?php echo $wpdb->charset; ?>
                            <br>
                            <?php if ($wpdb->charset != 'utf8mb4') { ?>
                                The recommended charset for your database is <code>utf8mb4</code> to avoid possible saving errors when you use emoji.
                                Read the WordPress Codex <a href="https://codex.wordpress.org/Converting_Database_Character_Sets" target="_blank">conversion
                                    instructions</a> (skilled technicia required).
                            <?php } else { ?>
                                If you experience newsletter saving database error
                                <?php $controls->button('conversion', 'Try tables upgrade') ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>get_table_charset()</td>
                        <td>

                        </td>
                        <td>
                            <?php echo esc_html(NEWSLETTER_USERS_TABLE), ': ', esc_html($tnp_wpdb->get_table_charset(NEWSLETTER_USERS_TABLE)) ?>
                        </td>
                    </tr>




                    <?php
                    $wait_timeout = $wpdb->get_var("select @@wait_timeout");
                    $condition = ($wait_timeout < 30) ? 0 : 1;
                    ?>
                    <tr>
                        <td>Database wait timeout</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            Your database wait timeout is <?php echo $wait_timeout; ?> seconds<br>
                            <?php if ($wait_timeout < 30) { ?>
                                That value is low and could produce database connection errors while sending emails or during long import
                                sessions. Ask the provider to raise it at least to 60 seconds.
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $res = $wpdb->query("drop table if exists {$wpdb->prefix}newsletter_test");
                    $res = $wpdb->query("create table if not exists {$wpdb->prefix}newsletter_test (id int(20))");
                    $condition = $res === false ? 0 : 1;
                    ?>
                    <tr>
                        <td>Database table creation</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                Check the privileges of the user you use to connect to the database, it seems it cannot create tables.<br>
                                (<?php echo esc_html($wpdb->last_error) ?>)
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    $res = $wpdb->query("alter table {$wpdb->prefix}newsletter_test add column id1 int(20)");
                    $condition = $res === false ? 0 : 1;
                    ?>
                    <tr>
                        <td>Database table change</td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php if ($res === false) { ?>
                                Check the privileges of the user you use to connect to the database, it seems it cannot change the tables. It's require to update the
                                plugin.<br>
                                (<?php echo esc_html($wpdb->last_error) ?>)
                            <?php } else { ?>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    // Clean up
                    $res = $wpdb->query("drop table if exists {$wpdb->prefix}newsletter_test");
                    ?>

                    <?php if (!get_option('newsletter_stats_email_column_upgraded', false)) { ?>
                        <?php
                        $data_type = $wpdb->get_var(
                                $wpdb->prepare('SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s',
                                        DB_NAME, NEWSLETTER_STATS_TABLE, 'email_id'));
                        $to_upgrade = strtoupper($data_type) == 'INT' ? false : true;
                        ?>
                        <?php if ($to_upgrade) { ?>
                            <tr>
                                <td>Database stats table upgrade</td>
                                <td><?php $this->condition_flag(0) ?></td>
                                <td><?php $controls->button('stats_email_column_upgrade', 'Stats table upgrade') ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                </tbody>
            </table>


            <h3>Tables</h3>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Table</th>
                        <th></th>
                        <th>Database check result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $r = $wpdb->get_row("check table " . NEWSLETTER_USERS_TABLE);
                    $condition = $r->Msg_text == 'OK' ? 1 : 0;
                    ?>
                    <tr>
                        <td><code><?php echo NEWSLETTER_USERS_TABLE ?></code></td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php print_r($r) ?>
                        </td>
                    </tr>
                    <?php
                    $r = $wpdb->get_row("check table " . NEWSLETTER_EMAILS_TABLE);
                    $condition = $r->Msg_text == 'OK' ? 1 : 0;
                    ?>
                    <tr>
                        <td><code><?php echo NEWSLETTER_EMAILS_TABLE ?></code></td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php print_r($r) ?>
                        </td>
                    </tr>
                    <?php
                    $r = $wpdb->get_row("check table " . NEWSLETTER_SENT_TABLE);
                    $condition = $r->Msg_text == 'OK' ? 1 : 0;
                    ?>
                    <tr>
                        <td><code><?php echo NEWSLETTER_SENT_TABLE ?></code></td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php print_r($r) ?>
                        </td>
                    </tr>
                    <?php
                    $r = $wpdb->get_row("check table " . NEWSLETTER_STATS_TABLE);
                    $condition = $r->Msg_text == 'OK' ? 1 : 0;
                    ?>
                    <tr>
                        <td><code><?php echo NEWSLETTER_STATS_TABLE ?></code></td>
                        <td>
                            <?php $this->condition_flag($condition) ?>
                        </td>
                        <td>
                            <?php print_r($r) ?>
                        </td>
                    </tr>
                </tbody>
            </table>



            <h3>General parameters</h3>
            <table class="widefat" id="tnp-parameters-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>Newsletter version</td>
                        <td>
                            <?php echo NEWSLETTER_VERSION ?>
                        </td>
                    </tr>

                    <tr>
                        <td>NEWSLETTER_MAX_EXECUTION_TIME</td>
                        <td>
                            <?php
                            if (defined('NEWSLETTER_MAX_EXECUTION_TIME')) {
                                echo NEWSLETTER_MAX_EXECUTION_TIME . ' (seconds)';
                            } else {
                                echo 'Not set';
                            }
                            ?>
                        </td>
                    </tr>




                    <?php /*
                      <tr>
                      <td>WordPress plugin url</td>
                      <td>
                      <?php echo WP_PLUGIN_URL; ?>
                      <br>
                      Filters:

                      <?php
                      if (isset($wp_filter))
                      $filters = $wp_filter['plugins_url'];
                      if (!isset($filters) || !is_array($filters))
                      echo 'no filters attached to "plugin_urls"';
                      else {
                      echo '<ul>';
                      foreach ($filters as &$filter) {
                      foreach ($filter as &$entry) {
                      echo '<li>';
                      if (is_array($entry['function']))
                      echo esc_html(get_class($entry['function'][0]) . '->' . $entry['function'][1]);
                      else
                      echo esc_html($entry['function']);
                      echo '</li>';
                      }
                      }
                      echo '</ul>';
                      }
                      ?>
                      <p class="description">
                      This value should contains the full URL to your plugin folder. If there are filters
                      attached, the value can be different from the original generated by WordPress and sometime worng.
                      </p>
                      </td>
                      </tr>
                     */ ?>

                    <tr>
                        <td>Absolute path</td>
                        <td>
                            <?php echo esc_html(ABSPATH); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tables Prefix</td>
                        <td>
                            <?php echo $wpdb->prefix; ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <?php if (isset($_GET['advanced'])) { ?>

                <h3>Database tables' status</h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th></th>
                            <th>Database check result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $r = $wpdb->get_row("check table " . NEWSLETTER_USERS_TABLE);
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo NEWSLETTER_USERS_TABLE ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php
                        $r = $wpdb->get_row("check table " . NEWSLETTER_EMAILS_TABLE);
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo NEWSLETTER_EMAILS_TABLE ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php
                        $r = $wpdb->get_row("check table " . NEWSLETTER_SENT_TABLE);
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo NEWSLETTER_SENT_TABLE ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php
                        $r = $wpdb->get_row("check table " . NEWSLETTER_STATS_TABLE);
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo NEWSLETTER_STATS_TABLE ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        
                        <?php if (class_exists('NewsletterAutomated')) { ?>
                        <?php
                        $r = $wpdb->get_row("check table " . $wpdb->prefix . 'newsletter_automated');
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo $wpdb->prefix . 'newsletter_automated' ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if (class_exists('NewsletterAutoresponder')) { ?>
                        <?php
                        $r = $wpdb->get_row("check table " . $wpdb->prefix . 'newsletter_autoresponder');
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo $wpdb->prefix . 'newsletter_autoresponder' ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php
                        $r = $wpdb->get_row("check table " . $wpdb->prefix . 'newsletter_autoresponder_steps');
                        $condition = $r->Msg_text == 'OK' ? 1 : 0;
                        ?>
                        <tr>
                            <td><code><?php echo $wpdb->prefix . 'newsletter_autoresponder_steps' ?></code></td>
                            <td>
                                <?php $this->condition_flag($condition) ?>
                            </td>
                            <td>
                                <?php print_r($r) ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <h3>Database tables' structure</h3>
                <h3>Database tables' status</h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Structure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code><?php echo NEWSLETTER_USERS_TABLE ?></code></td>
                            <td>
                                <?php tnp_describe_table(NEWSLETTER_USERS_TABLE) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><code><?php echo NEWSLETTER_EMAILS_TABLE ?></code></td>
                            <td>
                                <?php tnp_describe_table(NEWSLETTER_EMAILS_TABLE) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h3>Update plugins data</h3>
                <pre style="font-size: 11px; font-family: monospace; background-color: #efefef; color: #444"><?php echo esc_html(print_r(get_site_transient('update_plugins'), true)); ?></pre>

            <?php } else { ?>

                <p>
                    <a href="<?php echo add_query_arg('advanced', '1', $_SERVER['REQUEST_URI']) ?>">Show advanced parameters</a>
                </p>    
            <?php } ?>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
