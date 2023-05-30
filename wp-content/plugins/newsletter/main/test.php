<?php
/* @var $this Newsletter */
/* @var $wpdb wpdb */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();


if ($controls->is_action('test')) {

    if (!NewsletterModule::is_email($controls->data['test_email'])) {
        $controls->errors = 'The test email address is not set or is not correct.';
    }

    if (empty($controls->errors)) {

        $options = $controls->data;

        if ($controls->data['test_email'] == $this->options['sender_email']) {
            $controls->messages .= '<strong>Warning:</strong> you are using as test email the same address configured as sender in main configuration. Test can fail because of that.<br>';
        }

        $message = new TNP_Mailer_Message();
        $message->body = '<p>This is an <b>HTML</b> test email sent using the sender data set on Newsletter main setting. <a href="https://www.thenewsletterplugin.com">This is a link to an external site</a>.</p>';
        $message->body_text = 'This is a textual test email part sent using the sender data set on Newsletter main setting.';
        $message->to = $controls->data['test_email'];
        $message->subject = 'Newsletter test email at ' . date(DATE_ISO8601);
        $message->from = $this->options['sender_email'];
        $message->from_name = $this->options['sender_name'];

        $r = $this->deliver($message);

        if (!is_wp_error($r)) {
            $options['mail'] = 1;
            $controls->messages .= '<strong>SUCCESS</strong><br>';
            $controls->messages .= 'Anyway if the message does not appear the mailbox (check even the spam folder) you can ';
            $controls->messages .= '<a href="https://www.thenewsletterplugin.com/documentation/?p=15170" target="_blank"><strong>read more here</strong></a>.';
        } else {
            $options['mail'] = 0;
            $options['mail_error'] = $r->get_error_message();

            $controls->errors .= '<strong>FAILED</strong> (' . $r->get_error_message() . ')<br>';

            if (!empty($this->options['return_path'])) {
                $controls->errors .= '- Try to remove the return path on main settings.<br>';
            }

            $controls->errors .= '<a href="https://www.thenewsletterplugin.com/documentation/?p=15170" target="_blank"><strong>' . __('Read more', 'newsletter') . '</strong></a>.';

            $parts = explode('@', $this->options['sender_email']);
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            if (strtolower($sitename) != strtolower($parts[1])) {
                $controls->errors .= '- Try to set on main setting a sender address with the same domain of your blog: ' . $sitename . ' (you are using ' . $this->options['sender_email'] . ')<br>';
            }
        }
        $this->save_options($options, 'status');
    }
}

if ($controls->is_action('optimize-stats')) {
    $this->logger->info('Stats table otpimization');
    $this->query("alter table " . NEWSLETTER_STATS_TABLE . " drop index email_id");
    
    $this->query("alter table " . NEWSLETTER_STATS_TABLE . " drop index user_id");
    $this->query("alter table `" . NEWSLETTER_STATS_TABLE . "` modify column `email_id` int(11) not null default 0");
    $this->query("create index email_id on " . NEWSLETTER_STATS_TABLE . " (email_id)");
    $this->query("create index user_id on " . NEWSLETTER_STATS_TABLE . " (user_id)");
    $controls->add_message_done();
}

$options = $this->get_options('status');
?>

<div class="wrap tnp-main-status" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Mailing test', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <h3>Mailing test</h3>
            <table class="widefat" id="tnp-status-table">

                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th><?php _e('Status', 'newsletter') ?></th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Mailing</td>
                        <td>
                            <?php if (empty($options['mail'])) { ?>
                                <span class="tnp-ko">KO</span>
                            <?php } else { ?>
                                <span class="tnp-ok">OK</span>
                            <?php } ?>

                        </td>
                        <td>
                            <?php if (empty($options['mail'])) { ?>
                                <?php if (empty($options['mail_error'])) { ?>
                                    A test has never run.
                                <?php } else { ?>
                                    Last test failed with error "<?php echo esc_html($options['mail_error']) ?>".

                                <?php } ?>
                            <?php } else { ?>
                                Last test was successful. If you didn't receive the test email:
                                <ol>
                                    <li>If you set the Newsletter SMTP, do a test from that panel</li>
                                    <li>If you're using a integration extension do a test from its configuration panel</li>
                                    <li>If previous points do not apply to you, ask for support to your provider reporting the emails from your blog are not delivered</li>
                                </ol>
                            <?php } ?>
                            <br>
                            <a href="https://www.thenewsletterplugin.com/documentation/email-sending-issues" target="_blank">Read more to solve your issues, if any</a>.    
                            <br>
                            Email: <?php $controls->text_email('test_email') ?> <?php $controls->button('test', __('Send a test message')) ?>
                        </td>

                    </tr>

                    <tr>
                        <td>Mailer</td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            <?php
                            $mailer = Newsletter::instance()->get_mailer();
                            $name = 'Unknown';
                            if (is_object($mailer)) {
                                if (method_exists($mailer, 'get_description')) {
                                    $name = $mailer->get_description();
                                } else {
                                    $name = get_class($mailer);
                                }
                            }
                            ?>

                            <?php echo esc_html($name) ?>
                        </td>
                    </tr>
                    
                    <?php
                    $res = $wpdb->get_var("select count(*) from {$wpdb->prefix}newsletter_stats`");
                    ?>
                    
                    <?php if ($res > 500) { ?>
                    <tr>
                        <td>Statistics table optimization</td>
                        <td>
                            
                        </td>
                        <td>
                            The auto-optimization has not been started because your table contains an big number of rows. Run it manually.
                            <?php $controls->button('optimize-stats', __('Run', 'newsletter')); ?>
                        </td>
                    </tr> 
                    <?php } ?>

                </tbody>
            </table>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
