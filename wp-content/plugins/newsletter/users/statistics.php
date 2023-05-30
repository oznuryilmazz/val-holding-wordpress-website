<?php
/* @var $this NewsletterUsers */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

wp_enqueue_script('tnp-chart');

$all_count = $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE);
$options_profile = get_option('newsletter_profile');
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

    google.charts.load("current", {packages: ['corechart', 'geochart', 'geomap']});

</script>

<div class="wrap" id="tnp-wrap">
    
    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('/subscribers-and-management/') ?>
        <h2><?php _e('Subscriber statistics', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body" class="tnp-users-statistics">

        <?php $controls->init(); ?>

        <div id="tabs">

            <ul>
                <li><a href="#tabs-overview">By Status</a></li>
                <li><a href="#tabs-lists">By Lists</a></li>
                <li><a href="#tabs-language">By Language</a></li>
                <li><a href="#tabs-countries">World Map</a></li>
                <li><a href="#tabs-referrers">By Referrer</a></li>
                <li><a href="#tabs-sources">By URL</a></li>
                <li><a href="#tabs-gender">By Gender</a></li>
                <li><a href="#tabs-time">By Time</a></li>
            </ul>

            <div id="tabs-overview">
                <?php
                $list = $wpdb->get_row("select count(*) as total, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE);
                ?>

                <div class="row">
                    <div class="col-md-6">
                        <table class="widefat" style="width: 250px">
                            <thead>
                                <tr>
                                    <th><?php _e('Status', 'newsletter') ?></th>
                                    <th><?php _e('Total', 'newsletter') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php _e('Any', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->total; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Confirmed', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->confirmed; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Not confirmed', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->unconfirmed; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Unsubscribed', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->unsubscribed; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Bounced', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->bounced; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e('Complained', 'newsletter') ?></td>
                                    <td>
                                        <?php echo $list->complained; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <div style="height: 250px;">
                            <canvas id="tnp-users-chart-status"></canvas>
                        </div>
                        <script>
                            const dataStatus = {
                                labels: ['Confirmed', 'Unconfirmed', 'Unsubscribed', 'Bounced', 'Complained'],
                                datasets: [{
                                        label: 'Status',
                                        backgroundColor: ["#0074D9", "#FF4136", "#2ECC40", "#FF851B", "#7FDBFF", "#B10DC9", "#FFDC00", "#001f3f", "#39CCCC", "#01FF70", "#85144b", "#F012BE", "#3D9970", "#111111", "#AAAAAA"],
//                                        borderWidth: 1,
                                        data: <?php echo json_encode([(int) $list->confirmed, (int) $list->unconfirmed, (int) $list->unsubscribed, (int) $list->bounced, (int) $list->complained]) ?>,
                                    }]
                            };

                            jQuery(function () {
                                const myChartx = new Chart(
                                        document.getElementById('tnp-users-chart-status'),
                                        {
                                            type: 'doughnut',
                                            data: dataStatus,
                                            options: {
                                                maintainAspectRatio: false,
                                                legend: {
                                                    position: 'right'
                                                }

                                            }
                                        });
                            });
                        </script>
                    </div>
                </div>

            </div>


            <div id="tabs-lists">

                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php _e('List', 'newsletter') ?></th>
                            <th style="text-align: right"><?php _e('Total', 'newsletter') ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('C', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('S', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('U', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('B', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('P', true) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $lists = $this->get_lists(); ?>
                        <?php foreach ($lists as $list) { ?>
                            <?php
                            $row = $wpdb->get_row("select count(*) as total, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " where list_" . $list->id . "=1");
                            ?>
                            <tr>
                                <td><?php echo $list->id ?></td>
                                <td><?php echo esc_html($list->name) ?></td>

                                <td style="text-align: right"><?php echo (int) $row->total; ?></td>
                                <td style="text-align: right"><?php echo (int) $row->confirmed; ?></td>
                                <td style="text-align: right"><?php echo (int) $row->unconfirmed; ?></td>
                                <td style="text-align: right"><?php echo (int) $row->unsubscribed; ?></td>
                                <td style="text-align: right"><?php echo (int) $row->bounced; ?></td>
                                <td style="text-align: right"><?php echo (int) $row->complained; ?></td>
                            </tr>


                        <?php } ?>

                        <?php
                        $where = ' 1=1';

                        for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
                            $where .= ' and list_' . $i . '=0';
                        }
                        $row = $wpdb->get_row("select count(*) as total, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " where " . $where);
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>IN NO LIST</td>
                            <td style="text-align: right"><?php echo $row->total; ?></td>
                            <td style="text-align: right"><?php echo $row->confirmed; ?></td>
                            <td style="text-align: right"><?php echo $row->unconfirmed; ?></td>
                            <td style="text-align: right"><?php echo $row->unsubscribed; ?></td>
                            <td style="text-align: right"><?php echo $row->bounced; ?></td>
                            <td style="text-align: right"><?php echo $row->complained; ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <div id="tabs-language">
                <?php if ($this->is_multilanguage()) { ?>
                    <h3>By language</h3>
                    <?php $languages = $this->get_languages(); ?>

                    <table class="widefat" style="width: auto">
                        <thead>
                            <tr>
                                <th><?php _e('Status', 'newsletter') ?></th>
                                <th><?php _e('Total', 'newsletter') ?></th>
                            </tr>
                        <tbody>
                            <?php foreach ($languages as $code => $label) { ?>
                                <tr>
                                    <td><?php echo esc_html($label) ?></td>
                                    <td>
                                        <?php echo $wpdb->get_var($wpdb->prepare("select count(*) from " . NEWSLETTER_USERS_TABLE . " where language=%s", $code)); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><?php _e('Without language', 'newsletter') ?></td>
                                <td>
                                    <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where language=''"); ?>
                                </td>
                            </tr>
                            </thead>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>
                        This panel is active when a <a href="https://www.thenewsletterplugin.com/documentation/newsletters/multilanguage/" target="_blank">supported multilanguage plugin</a> is installed.
                    </p>
                <?php } ?>

            </div>

            <div id="tabs-countries">
                <?php
                if (!has_action('newsletter_users_statistics_countries')) {
                    include __DIR__ . '/statistics-countries.php';
                } else {
                    do_action('newsletter_users_statistics_countries', $controls);
                }
                ?>
            </div>


            <div id="tabs-referrers">
                <p>
                    <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscribers-statistics#referrer') ?>
                </p>
                <?php
                $list = $wpdb->get_results("select referrer, count(*) as total, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " group by referrer order by confirmed desc");
                ?>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th><?php _e('Referrer', 'newsletter') ?></th>
                            <th style="text-align: right"><?php _e('Total', 'newsletter') ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('C', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('S', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('U', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('B', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('P', true) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?php echo empty($row->referrer) ? '[not set]' : esc_html($row->referrer) ?></td>
                                <td style="text-align: right"><?php echo $row->total; ?></td>
                                <td style="text-align: right"><?php echo $row->confirmed; ?></td>
                                <td style="text-align: right"><?php echo $row->unconfirmed; ?></td>
                                <td style="text-align: right"><?php echo $row->unsubscribed; ?></td>
                                <td style="text-align: right"><?php echo $row->bounced; ?></td>
                                <td style="text-align: right"><?php echo $row->complained; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>


            <div id="tabs-sources">
                <p>
                    <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscribers-statistics#source') ?>
                </p>
                <?php
                $list = $wpdb->get_results("select http_referer, count(*) as total, SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " group by http_referer order by count(*) desc limit 100");
                ?>
                <table class="widefat" style="width: auto">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th style="text-align: right"><?php _e('Total', 'newsletter') ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('C', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('S', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('U', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('B', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('P', true) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row) { ?>
                            <tr>
                                <td><?php echo empty($row->http_referer) ? '[not set]' : $controls->print_truncated($row->http_referer, 120) ?></td>
                                <td style="text-align: right"><?php echo $row->total; ?></td>
                                <td style="text-align: right"><?php echo $row->confirmed; ?></td>
                                <td style="text-align: right"><?php echo $row->unconfirmed; ?></td>
                                <td style="text-align: right"><?php echo $row->unsubscribed; ?></td>
                                <td style="text-align: right"><?php echo $row->bounced; ?></td>
                                <td style="text-align: right"><?php echo $row->complained; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>


            <div id="tabs-gender">


                <?php
                $male_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " where sex='m'");
                $female_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " where sex='f'");
                $none_count = $wpdb->get_row("select SUM(if(status='C', 1, 0)) as confirmed, SUM(if(status='S', 1, 0)) as unconfirmed, SUM(if(status='B', 1, 0)) as bounced, SUM(if(status='U', 1, 0)) as unsubscribed, SUM(if(status='P', 1, 0)) as complained from " . NEWSLETTER_USERS_TABLE . " where sex='n'");
                ?>

                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Gender', 'newsletter') ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('C', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('S', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('U', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('B', true) ?></th>
                            <th style="text-align: right"><?php echo $this->get_user_status_label('P', true) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Female', 'newsletter') ?></td>
                            <td style="text-align: right"><?php echo $female_count->confirmed; ?></td>
                            <td style="text-align: right"><?php echo $female_count->unconfirmed; ?></td>
                            <td style="text-align: right"><?php echo $female_count->unsubscribed; ?></td>
                            <td style="text-align: right"><?php echo $female_count->bounced; ?></td>
                            <td style="text-align: right"><?php echo $female_count->complained; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Male', 'newsletter') ?></td>
                            <td style="text-align: right"><?php echo $male_count->confirmed; ?></td>
                            <td style="text-align: right"><?php echo $male_count->unconfirmed; ?></td>
                            <td style="text-align: right"><?php echo $male_count->unsubscribed; ?></td>
                            <td style="text-align: right"><?php echo $male_count->bounced; ?></td>
                            <td style="text-align: right"><?php echo $male_count->complained; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e('Not specified', 'newsletter') ?></td>
                            <td style="text-align: right"><?php echo $none_count->confirmed; ?></td>
                            <td style="text-align: right"><?php echo $none_count->unconfirmed; ?></td>
                            <td style="text-align: right"><?php echo $none_count->unsubscribed; ?></td>
                            <td style="text-align: right"><?php echo $none_count->bounced; ?></td>
                            <td style="text-align: right"><?php echo $none_count->complained; ?></td>
                        </tr>
                    </tbody>
                </table>


            </div>


            <div id="tabs-time">

                <?php
                if (!has_action('newsletter_users_statistics_time')) {
                    include __DIR__ . '/statistics-time.php';
                } else {
                    do_action('newsletter_users_statistics_time', $controls);
                }
                ?>

            </div>

            <?php
            if (isset($panels['user_statistics'])) {
                foreach ($panels['user_statistics'] as $panel) {
                    call_user_func($panel['callback'], $id, $controls);
                }
            }
            ?>
        </div>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>



