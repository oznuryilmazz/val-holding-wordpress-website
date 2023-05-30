<?php
/* @var $this NewsletterSystem */
/* @var $wpdb wpdb */

defined('ABSPATH') || exit;

wp_enqueue_script('tnp-chart');

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

if ($controls->is_action('reset')) {
    $this->reset_cron_stats();
    $controls->add_message_done();
}

if ($controls->is_action('reschedule')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + 30, 'newsletter', 'newsletter');
    $controls->add_message_done();
}

if ($controls->is_action('trigger')) {
    wp_clear_scheduled_hook('newsletter');
    wp_schedule_event(time() + NEWSLETTER_CRON_INTERVAL, 'newsletter', 'newsletter');
    Newsletter::instance()->hook_newsletter();
    $controls->add_message_done();
}

if ($controls->is_action('test')) {
    $response = wp_remote_get(site_url('/wp-cron.php') . '?' . time());
    if (is_wp_error($response)) {
        $controls->errors = 'Test failed: ' . esc_html($response->get_error_message());
    } else if (wp_remote_retrieve_response_code($response) != 200) {
        $controls->errors = 'Test failed: ' . esc_html(wp_remote_retrieve_response_message($response));
    } else {
        $controls->add_message('Test ok');
    }

    if ($controls->errors) {
        $controls->errors .= '<br>Report this error to your provider saying the site cannot make an HTTP call to its wp-cron.php file and copying the error message above.';
    }
}

?>

<style>
   <?php include __DIR__ . '/css/system.css' ?>
</style>

<div class="wrap tnp-system tnp-system-scheduler" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('WP Scheduler and Delivery Engine', 'newsletter') ?></h2>
        <p>
            The scheduler is a WordPress component that executes <strong>background tasks</strong> 
            (publish future post, run backups, send newsletters, ...). 
            <br>
            Here some steps you can consider if the scheduler has issues.
        </p>
        <ul>
            <li>Check the <a href="<?php echo admin_url('/site-health.php') ?>">site health panel</a> and try to solve the issues identified by WordPress in your site</li>
            <li>Install the <a href="https://wordpress.org/plugins/wp-crontrol/" target="_blank">WP Crontrol</a> plugin which shows all the scheduled jobs and delays</li>
            <li>Configure an <a href="https://www.thenewsletterplugin.com/documentation/delivery-and-spam/newsletter-delivery-engine/" target="_blank">external cron service</a>
                (if you have a license you can use our <a href="https://www.thenewsletterplugin.com/account/cron/" target="_blank">cron service</a>)</li>
        </ul>
    </div>

    <div id="tnp-body">


        <form method="post" action="">
            <?php $controls->init(); ?>

            <table class="widefat">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th></th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <?php
                $status = $this->get_job_status();
                $condition = $status == NewsletterSystem::JOB_OK ? 1 : 0;
                ?>
                <tr>
                    <td>Delivery background job</td>
                    <td class="status">
                        <?php $this->condition_flag($condition, 'https://www.thenewsletterplugin.com/documentation/delivery-and-spam/newsletter-delivery-engine/') ?>
                    </td>
                    <td>
                        <?php
                        switch ($status) {
                            case NewsletterSystem::JOB_MISSING:
                                echo 'The engine schedule is missing. Try to deactivate and reactivate the Newsletter plugin.';
                                break;
                            case NewsletterSystem::JOB_LATE:
                                echo 'The engine schedule is late. You probably need and external scheduler trigger.';
                                break;
                            case NewsletterSystem::JOB_SKIPPED:
                                echo 'The engine schedule has been skipped. The scheduler is overloaded or a job has fatal error and blocks the scheduler.';
                                break;
                            case NewsletterSystem::JOB_OK:
                                echo 'Everything seems fine!';
                                break;
                        }
                        ?>
                        <br><br>
                        Next run: <?php echo $controls->print_date($this->get_job_schedule(), false, true) ?>
                        <br><br>
                        <?php
                        if ($status == NewsletterSystem::JOB_LATE) {
                            $controls->button('trigger', 'Run manually');
                        }
                        ?>
                    </td>
                </tr>


                <tr>
                    <td>Last cron call</td>
                    <td class="status">&nbsp;</td>
                    <td>
                        <?php echo $controls->print_date($this->get_last_cron_call()) ?>
                    </td>
                </tr>                


                <tr>
                    <?php
                    $stats = NewsletterSystem::instance()->get_cron_stats();
                    ?>
                    <td>
                        Cron call stats
                    </td>
                    <?php if ($stats == null) { ?>
                        <td class="status">
                            &nbsp;
                        </td>
                        <td>
                            Not enough data, some hours are still required.
                        </td>

                    <?php } else { ?>

                        <?php
                        $condition = $stats->good ? 1 : 0;
                        ?>

                        <td class="status">
                            <?php $this->condition_flag($condition, 'https://www.thenewsletterplugin.com/documentation/delivery-and-spam/newsletter-delivery-engine/') ?>
                        </td>
                        <td>
                            <?php if ($condition == 0) { ?>
                                The blog cron system is NOT triggered enough often.<br>
                            <?php } ?>

                            Samples <?php echo count($stats->deltas) ?>, average <?php echo $stats->avg ?>&nbsp;s, max <?php echo $stats->max ?>&nbsp;s, min <?php echo $stats->min ?>&nbsp;s

                            <canvas id="tnp-cron-chart" style="width: 700px; height: 300px"></canvas>
                            <script>
                                jQuery(function () {
                                    var cronChartData = {
                                        //labels: <?php echo json_encode(range(1, count($stats->deltas))) ?>,
                                        labels: <?php echo json_encode($stats->deltas_ts) ?>,
                                        datasets: [
                                            {
                                                label: "Batch Average Time",
                                                data: <?php echo json_encode(array_map(function ($v) {return $v/1000;}, $stats->deltas)) ?>,
                                                borderColor: '#2980b9',
                                                fill: false
                                            }]
                                    };
                                    var cronChartConfig = {
                                        type: "line",
                                        data: cronChartData,
                                        options: {
                                            responsive: false,
                                            maintainAspectRatio: false,
                                            scales: {
                                                x: {
                                                    type: 'linear'
                                                }
                                            }
                                        }
                                    };
                                    new Chart('tnp-cron-chart', cronChartConfig);
                                });
                            </script>

                            <?php $controls->button_reset() ?>

                        </td>
                    <?php } ?>
                </tr>          

                <?php
                $condition = $this->has_newsletter_schedule() ? 1 : 0;
                $schedules = wp_get_schedules();
                ?>
                <tr>
                    <td>
                        Newsletter engine schedule
                    </td>
                    <td class="status"><?php $this->condition_flag($condition) ?></td>
                    <td>
                        <?php if (!$condition) { ?>
                            The Newsletter schedule is not present probably another plugin is interfering with the starndard WordPress scheuling system.<br>
                            You can reactivate it, but is the problem persist
                            <?php $controls->button('reschedule', 'Reactivate') ?>
                        <?php } ?>

                        Registered recurring schedules:<br>
                        <ul style="margin-left: 0em;">
                            <?php
                            if (!empty($schedules)) {
                                foreach ($schedules as $key => $data) {
                                    if ($key == 'newsletter') {
                                        echo '<li style="padding: 0; margin: 0; font-weight: bold">', esc_html($key . ' - ' . $data['interval']), ' seconds</li>';
                                    } else {
                                        echo '<li style="padding: 0; margin: 0;">', esc_html($key . ' - ' . $data['interval']), ' seconds</li>';
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </td>
                </tr>


                <tr>
                    <td>Cron URL</td>
                    <td class="status">&nbsp;</td>
                    <td>
                        <strong><?php echo esc_html(site_url('/wp-cron.php')) ?></strong>
                        <br><br>
                        Can be used to trigger the WordPress scheduler from an external cron service.
                    </td>
                </tr>                      




                <tr>
                    <td>
                        WordPress scheduler auto trigger
                    </td>
                    <td class="status">
                        <?php //$this->condition_flag($condition)  ?>
                    </td>
                    <td>
                        <?php $controls->button_test() ?>
                    </td>
                </tr>       

                <?php
                $condition = (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? 2 : 1;
                ?>
                <tr>
                    <td>
                        <code>DISABLE_WP_CRON</code>
                    </td>
                    <td class="status">
                        <?php $this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php if ($condition == 2) { ?>
                            The constant <code>DISABLE_WP_CRON</code> is set to <code>true</code> (probably in <code>wp-config.php</code>). That disables the scheduler auto triggering and it's
                            good ONLY if you setup an external trigger.
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <code>ALTERNATE_WP_CRON</code>
                    </td>
                    <td class="status">
                        &nbsp;
                    </td>
                    <td>
                        <?php if (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON) { ?>
                            Using the alternate cron trigger. Rare configuration but should not be a problem.
                        <?php } else { ?>
                            Option not active, it's ok.
                        <?php } ?>
                    </td>
                </tr>  

                <?php
                $condition = NEWSLETTER_CRON_INTERVAL == 300 ? 1 : 2;
                ?>
                <tr>
                    <td><code>NEWSLETTER_CRON_INTERVAL</code></td>
                    <td class="status">
                        <?php $this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php echo NEWSLETTER_CRON_INTERVAL, ' seconds'; ?>
                        <br><br>
                        How often the Newsletter engine should be activated. Default 300 seconds. Different value can be set on your <code>wp-config.php</code>
                        (not recommended).
                    </td>
                </tr>


                <?php
                $condition = WP_CRON_LOCK_TIMEOUT != MINUTE_IN_SECONDS ? 2 : 1;
                ?>
                <tr>
                    <td><code>WP_CRON_LOCK_TIMEOUT</code></td>
                    <td class="status">
                        <?php $this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php echo WP_CRON_LOCK_TIMEOUT, ' seconds'; ?>

                        <?php if ($condition == 2) { ?>
                            <br>
                            A non standard (<?php echo MINUTE_IN_SECONDS ?> seconds) value is specified probably in your <code>wp-config.php</code>.
                        <?php } ?>
                    </td>
                </tr>                


                <?php
                $condition = (defined('NEWSLETTER_CRON_WARNINGS') && !NEWSLETTER_CRON_WARNINGS) ? 2 : 1;
                ?>
                <tr>

                    <td>
                        <code>NEWSLETTER_CRON_WARNINGS</code>
                    </td>
                    <td class="status">
                        <?php $this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php if ($condition == 2) { ?>
                            Scheduler warnings are disabled in your <code>wp-config.php</code> with the constant <code>NEWSLETTER_CRON_WARNINGS</code> set to true.
                        <?php } else { ?>
                            Scheduler warnings are enabled
                        <?php } ?>
                    </td>
                </tr>

                <?php
                $condition = has_filter('pre_reschedule_event') ? 2 : 1;
                $functions = $this->get_hook_functions('pre_reschedule_event');
                ?>
                <tr>
                    <td><code>pre_reschedule_event</code></td>
                    <td class="status">
                        <?php $this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php if ($condition == 2) { ?>
                            One or more plugin are filtering the jobs rescheduling. If a recurrent job (like the newsletter generation with Automated) disappers
                            this is a good starting point.<br><br>
                        <?php } ?>
                        Attached functions:<br>
                        <?php echo $functions?$functions:'[none]' ?>
                    </td>
                </tr>    

                <?php
                $transient = get_transient('doing_cron');
                ?>
                <tr>
                    <td>Transient <code>doing_cron</code></td>
                    <td class="status">
                        <?php //$this->condition_flag($condition) ?>
                    </td>
                    <td>
                        <?php if ($transient) { ?>
                            <?php
                            echo esc_html($transient);
                            if (is_numeric($transient)) {
                                echo ' (', $controls->print_date((int) $transient), ')';
                            }
                            ?>
                        <?php } else { ?>
                            [unset]
                        <?php } ?>
                        <br><br>
                        When set it means the scheduler is executing background jobs. Install the WP Crontol plugin to have more information about
                        your site background jobs.
                    </td>
                </tr>    
            </table>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
