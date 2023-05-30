<?php
/* @var $this NewsletterSystem */
/* @var $wpdb wpdb */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$newsletter = Newsletter::instance();

if ($controls->is_action('test')) {

    if (!NewsletterModule::is_email($controls->data['test_email'])) {
        $controls->errors = 'The test email address is not set or is not correct.';
    }

    if (empty($controls->errors)) {

        $options = $controls->data;

        if ($controls->data['test_email'] == $newsletter->get_sender_email()) {
            $controls->messages .= '<strong>Warning:</strong> you are using as test email the same address configured as sender in main configuration. Test can fail because of that.<br>';
        }

        $message = NewsletterMailerAddon::get_test_message($controls->data['test_email'], 'Newsletter test email at ' . date(DATE_ISO8601));

        $r = $newsletter->deliver($message);

        if (!is_wp_error($r)) {
            $options['mail'] = 1;
            $controls->messages .= '<strong>SUCCESS</strong><br>';
            $controls->messages .= 'Anyway if the message does not appear the mailbox (check even the spam folder) you can ';
            $controls->messages .= '<a href="https://www.thenewsletterplugin.com/documentation/?p=15170" target="_blank"><strong>read more here</strong></a>.';
        } else {
            $options['mail'] = 0;
            $options['mail_error'] = $r->get_error_message();

            $controls->errors .= '<strong>FAILED</strong> (' . esc_html($r->get_error_message()) . ')<br>';

            if (!empty($newsletter->options['return_path'])) {
                $controls->errors .= '- Try to remove the return path on main settings.<br>';
            }

            $controls->errors .= '<a href="https://www.thenewsletterplugin.com/documentation/?p=15170" target="_blank"><strong>' . __('Read more', 'newsletter') . '</strong></a>.';

            $parts = explode('@', $newsletter->get_sender_email());
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            if (strtolower($sitename) != strtolower($parts[1])) {
                $controls->errors .= '- Try to set on main setting a sender address with the same domain of your blog: ' . esc_html($sitename) . ' (you are using ' . esc_html($newsletter->get_sender_email()) . ')<br>';
            }
        }
        $this->save_options($options, 'status');
    }
}

$options = $this->get_options('status');

$mailer = Newsletter::instance()->get_mailer();
$functions = $this->get_hook_functions('phpmailer_init');
$icon = 'fas fa-plug';
if ($mailer instanceof NewsletterDefaultMailer) {
    $mailer_name = 'Wordpress';
    $service_name = 'Hosting Provider';
    if (!empty($functions)) {
        $mailer_name .= '<br>(see below)';
        $service_name .= '<br>(see below)';
    }
    $icon = 'fab fa-wordpress';
} else if ($mailer instanceof NewsletterDefaultSMTPMailer) {
    $mailer_name = 'Internal SMTP';
    $service_name = 'SMTP Provider';
} else {
    $mailer_name = 'Unknown';
    $service_name = 'Unknown';
    if (is_object($mailer)) {
        if (method_exists($mailer, 'get_description')) {
            $mailer_name = esc_html($mailer->get_description());
            $service_name = esc_html(ucfirst($mailer->get_name()) . ' Service');
        } else {
            $mailer_name = esc_html(get_class($mailer));
            $service_name = $mailer_name;
        }
    }
}

$speed = Newsletter::instance()->get_send_speed();
?>

<style>
   <?php include __DIR__ . '/css/system.css' ?>
</style>

<div class="wrap tnp-system tnp-system-delivery" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Email Delivery', 'newsletter') ?></h2>
        <p>
            Test here the email delivery and the path it runs across to your subscribers.
        </p>
    </div>

    <div id="tnp-body">
        <form method="post" action="">
            <?php $controls->init(); ?>
            <h3>Mailing test</h3>

            <p>
                <?php $controls->text_email('test_email', ['required' => true]) ?> 
                <?php $controls->button('test', __('Send a test message')) ?>
                <?php if (empty($options['mail'])) { ?>
                    <span class="tnp-ko">KO</span>
                <?php } else { ?>
                    <span class="tnp-ok">OK</span>
                <?php } ?>
            </p>

            <p>
                <?php if (empty($options['mail'])) { ?>
                    <?php if (empty($options['mail_error'])) { ?>
                    <p>A test has never run.</p>
                <?php } else { ?>
                    <p>Last test failed with error "<?php echo esc_html($options['mail_error']) ?>".</p>

                <?php } ?>
            <?php } else { ?>
                <p>Last test was successful. If you didn't receive the test email:</p>
                <ol>
                    <li>If you're using an third party SMTP plugin, do a test from that plugin configuration panel</li>
                    <li>If you're using a Newsletter Delivery Addon, do a test from that addon configuration panel</li>
                    <li>If previous points do not apply to you, ask for support to your provider reporting the emails from your blog are not delivered</li>
                </ol>
            <?php } ?>
            <p><a href="https://www.thenewsletterplugin.com/documentation/email-sending-issues" target="_blank">Read more to solve your issues, if any</a></p>


        </form>

        <h3>
            How are messages delivered by Newsletter to your subscribers?
        </h3>

        <div class="tnp-flow tnp-flow-row">
            <div class="tnp-mail"><i class="fas fa-envelope"></i><br><br>Messages<br>
                (max: <?php echo esc_html($speed) ?> emails per hour)
            </div>
            <div class="tnp-arrow">&rightarrow;</div>
            <div class="tnp-addon"><i class="<?php echo $icon ?>"></i><br><br><?php echo $mailer_name ?></div>
            <div class="tnp-arrow">&rightarrow;</div>
            <div class="tnp-service"><i class="fas fa-cog"></i><br><br>
                <?php echo $service_name ?>
            </div>
            <div class="tnp-arrow">&rightarrow;</div>
            <div class="tnp-user"><i class="fas fa-user"></i><br><br>Subscriber</div>
        </div>


        <?php if (!empty($functions)) { ?>
            <br><br>
            <h3>Functions that are changing the default WordPress delivery system</h3>
            <p><?php echo $functions ?></p>
        <?php } ?>


    </div>
    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>
</div>
