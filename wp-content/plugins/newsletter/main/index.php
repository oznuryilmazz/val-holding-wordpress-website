<?php
/* @var $this Newsletter */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

wp_enqueue_script('tnp-chart');

if ($controls->is_action('feed_enable')) {
    delete_option('newsletter_feed_demo_disable');
    $controls->messages = 'Feed by Mail demo panels enabled. On next page reload it will show up.';
}

if ($controls->is_action('feed_disable')) {
    update_option('newsletter_feed_demo_disable', 1);
    $controls->messages = 'Feed by Mail demo panel disabled. On next page reload it will disappear.';
}

$emails_module = NewsletterEmails::instance();
$statistics_module = NewsletterStatistics::instance();
$emails = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where type='message' order by id desc limit 5");

$users_module = NewsletterUsers::instance();
$query = "select * from " . NEWSLETTER_USERS_TABLE . " order by id desc limit 5";
$subscribers = $wpdb->get_results($query);

// Retrieves the last standard newsletter
$last_email = $wpdb->get_row(
        $wpdb->prepare("select * from " . NEWSLETTER_EMAILS_TABLE . " where type='message' and status in ('sent', 'sending') and send_on<%d order by id desc limit 1", time()));

if ($last_email) {
    $report = $statistics_module->get_statistics($last_email);
    $last_email_sent = $report->total;
    $last_email_opened = $report->open_count;
    $last_email_notopened = $last_email_sent - $last_email_opened;
    $last_email_clicked = $report->click_count;
    $last_email_opened -= $last_email_clicked;

    $overall_sent = $wpdb->get_var("select sum(sent) from " . NEWSLETTER_EMAILS_TABLE . " where type='message' and status in ('sent', 'sending')");

    $overall_opened = $wpdb->get_var("select count(distinct user_id,email_id) from " . NEWSLETTER_STATS_TABLE);
    $overall_notopened = $overall_sent - $overall_opened;
    $overall_clicked = $wpdb->get_var("select count(distinct user_id,email_id) from " . NEWSLETTER_STATS_TABLE . " where url<>''");
    $overall_opened -= $overall_clicked;
} else {
    $last_email_opened = 500;
    $last_email_notopened = 400;
    $last_email_clicked = 200;

    $overall_opened = 500;
    $overall_notopened = 400;
    $overall_clicked = 200;
}

$months = $wpdb->get_results("select count(*) as c, concat(year(created), '-', date_format(created, '%m')) as d "
        . "from " . NEWSLETTER_USERS_TABLE . " where status='C' "
        . "group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 12");
$values = array();
$labels = array();
foreach ($months as $month) {
    $values[] = (int) $month->c;
    $labels[] = date("M y", date_create_from_format("Y-m", $month->d)->getTimestamp());
}
$values = array_reverse($values);
$labels = array_reverse($labels);

$lists = $this->get_lists();
?>

<style>
    <?php include __DIR__ . '/css/dashboard.css' ?>
</style>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-body" class="tnp-main-index">
        <div class="tnp-dashboard">
            <div class="tnp-cards-container">
                <div class="tnp-card tnp-mimosa">
                    <div class="tnp-card-title"><?php esc_html_e('Forms', 'newsletter')?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Setup the form fields and labels.', 'newsletter')?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_subscription_profile"><?php esc_html_e('Edit forms', 'newsletter')?></a>
                    </div>
                </div>
                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Lists', 'newsletter')?></div>
                    <div class="tnp-card-description">You have <?php echo count($lists) ?> lists.</div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_subscription_lists"><?php esc_html_e('Manage', 'newsletter')?></a>
                    </div>
                </div>
                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Delivery', 'newsletter')?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Change the delivery speed, sender name and return path.', 'newsletter')?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_main_main"><?php esc_html_e('Change the delivery settings', 'newsletter')?></a>
                    </div>
                </div>
                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Company Info', 'newsletter')?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Set your company name, address, socials.', 'newsletter')?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_main_info"><?php esc_html_e('Edit your info', 'newsletter')?></a>
                    </div>
                </div>
            </div>
            <div class="tnp-cards-container">
                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Newsletters', 'newsletter')?></div>
                    <div class="tnp-card-upper-buttons"><a href="?page=newsletter_emails_composer"><?php _e('New', 'newsletter') ?></a></div>
                    <div class="tnp-card-upper-buttons"><a href="?page=newsletter_emails_index"><?php _e('List', 'newsletter') ?></a></div>
                    <div class="tnp-card-content">
                        <?php foreach ($emails as $email) { ?>
                            <div class="tnp-card-newsletter-list">
                                <?php
                                $subject = $email->subject ? $email->subject : "Newsletter #" . $email->id;
                                ?>
                                <div class="tnp-card-newsletters-subject">
                                    <?php echo esc_html($subject) ?>
                                </div>
                                <div class="tnp-card-newsletters-status">
                                    <?php $emails_module->show_email_status_label($email) ?>
                                </div>
                                <div class="tnp-card-newsletters-progress">
                                    <?php $emails_module->show_email_progress_bar($email, array('scheduled' => true)) ?>
                                </div>
                                <div class="tnp-card-newsletters-action">
                                    <?php
                                    if ($email->status === TNP_Email::STATUS_SENT || $email->status === TNP_Email::STATUS_SENDING) {
                                        echo '<a class="button-primary" href="' . $statistics_module->get_statistics_url($email->id) . '"><i class="fas fa-chart-bar"></i></a>';
                                    } else {
                                        echo $emails_module->get_edit_button($email, true);
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-title">Last Subscribers</div>
                    <div class="tnp-card-upper-buttons"><a href="<?php echo $users_module->get_admin_page_url('new'); ?>"><?php _e('New', 'newsletter') ?></a></div>
                    <div class="tnp-card-upper-buttons"><a href="<?php echo $users_module->get_admin_page_url('index'); ?>"><?php _e('List', 'newsletter') ?></a></div>
                    <div class="tnp-card-content">

                        <?php foreach ($subscribers as $s) { ?>
                            <div class="tnp-card-newsletter-list">
                                <div class="tnp-card-newsletters-subscriber-email">
                                    <?php echo esc_html($s->email) ?>
                                </div>

                                <div class="tnp-card-newsletters-subscriber-name">
                                    <?php echo esc_html($s->name) ?> <?php echo esc_html($s->surname) ?>
                                </div>
                                <div class="tnp-card-newsletters-subscriber-status">
                                    <?php echo $emails_module->get_user_status_label($s, true) ?>
                                </div>
                                <div class="tnp-card-newsletters-action">
                                        <a class="button-primary"
                                           title="<?php _e('Edit', 'newsletter') ?>"
                                           href="<?php echo $users_module->get_admin_page_url('edit'); ?>&amp;id=<?php echo $s->id; ?>"><i
                                                    class="fas fa-edit"></i></a>
                                        <!--
                                            <a title="<?php _e('Profile', 'newsletter') ?>"
                                               href="<?php echo home_url('/') ?>?na=p&nk=<?php echo $s->id . '-' . $s->token; ?>"
                                               class="button-primary" target="_blank"><i
                                                    class="fas fa-user"></i></a>-->
                                </div>
                            </div>
                        <?php } ?>



                </div>
            </div>
        </div>
        <div class="tnp-cards-container">
            <div class="tnp-card">
                <div class="tnp-card-title"><?php _e('Subscriptions', 'newsletter') ?></div>
                <div class="tnp-canvas">
                    <canvas id="tnp-events-chart-canvas" height="300"></canvas>
                </div>

                <script type="text/javascript">
                    var events_data = {
                        labels: <?php echo json_encode($labels) ?>,
                        datasets: [
                            {
                                label: "<?php _e('Subscriptions', 'newsletter') ?>",
                                fill: true,
                                strokeColor: "#27AE60",
                                backgroundColor: "#eee",
                                borderColor: "#27AE60",
                                pointBorderColor: "#27AE60",
                                pointBackgroundColor: "#ECF0F1",
                                data: <?php echo json_encode($values) ?>
                            }
                        ]
                    };

                    jQuery(document).ready(function ($) {
                        ctxe = $('#tnp-events-chart-canvas').get(0).getContext("2d");
                        eventsLineChart = new Chart(ctxe, {
                            type: 'line', data: events_data,
                            options: {
                                maintainAspectRatio: false,
                                xresponsive: true,
                                scales: {
                                    xAxes: [{
                                        type: "category",
                                        "id": "x-axis-1",
                                        gridLines: {display: false},
                                        ticks: {fontFamily: "soleil"}
                                    }],
                                    yAxes: [
                                        {
                                            type: "linear",
                                            "id": "y-axis-1",
                                            gridLines: {display: false},
                                            ticks: {fontFamily: "soleil"}
                                        },
                                    ]
                                },
                            }
                        });
                    });
                </script>
            </div>
            <div class="tnp-card">
                <div class="tnp-card-title"><?php _e('Documentation', 'newsletter') ?></div>
                <div class="break"></div>
                <a href="https://www.thenewsletterplugin.com/documentation/installation/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Installation
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/subscription/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Subscription
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/category/tips" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Tips & Tricks
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/subscribers-and-management/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Subscribers and management
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/newsletters/newsletters-module/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Creating Newsletters
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/addons/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Premium Addons
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/customization/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Customization
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/delivery-and-spam/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Delivery and spam
                    </div>
                </a>
                <a href="https://www.thenewsletterplugin.com/documentation/developers/" target="_blank">
                    <div class="tnp-card-documentation-index">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                        Developers & Advanced Topics
                    </div>
                </a>
            </div>
        </div>
        <div class="tnp-cards-container">
            <div class="tnp-card" style="align-self: flex-start">
                <div class="tnp-card-title"><?php _e('Developers', 'newsletter') ?></div>
                <div class="tnp-card-description">Extending Newsletter by yourself? There is something for you as well!</div>
                <div class="tnp-card-button-container">
                    <a href="https://www.thenewsletterplugin.com/documentation/developers/" target="_blank">Developer's love 💛</a>
                </div>
            </div>
            <div class="tnp-card">
                <div class="tnp-card-title"><?php _e('Video Tutorials', 'newsletter') ?></div>
                <div class="tnp-card-description">We have some videos to help gest the most from Newsletter.</div>
                <div class="tnp-card-video">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/zmVmW84Bw9A" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="tnp-card-button-container">
                    <a href="https://www.thenewsletterplugin.com/video-tutorials" target="_blank">See the videos</a>
                </div>
            </div>
        </div>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
