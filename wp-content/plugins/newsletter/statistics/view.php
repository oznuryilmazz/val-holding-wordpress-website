<?php
/* @var $this NewsletterStatistics */

defined('ABSPATH') || exit;

wp_enqueue_script('tnp-chart');

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$email = $this->get_email($_GET['id']);
$report = $this->get_statistics($email);

if ($email->status == 'new') {
    $controls->warnings[] = __('Draft newsletter, no data available', 'newsletter');
} else if ($email->status == 'sending') {
    $controls->warnings[] = __('Newsletter still sending', 'newsletter');
}

if (empty($email->track)) {
    $controls->warnings[] = __('This newsletter has the tracking disabled. No statistics will be available.', 'newsletter');
}

?>

<div class="wrap tnp-statistics tnp-statistics-view" id="tnp-wrap">
    <?php include NEWSLETTER_DIR . '/tnp-header.php' ?>
    <div id="tnp-heading">
        <h2><?php _e('Statistics of', 'newsletter') ?> "<?php echo htmlspecialchars($email->subject); ?>"</h2>
        <p>Retargeting and subscriber detailed list are available with <a href="https://www.thenewsletterplugin.com/reports" target="_blank">Reports Addon</a>.</p>
    </div>

    <div id="tnp-body" style="min-width: 500px">

        <div class="row">
            <div class="col-md-3">
                <div class="tnp-widget tnp-number">
                    <h3><?php _e('Reach', 'newsletter') ?></h3>
                    <div class="tnp-icon"><i class="fas fa-users"></i></div>
                    <div class="tnp-value"><?php echo number_format_i18n($report->total, 0) ?></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="tnp-widget tnp-number">
                    <h3><?php _e('Opens', 'newsletter') ?></h3>
                    <div class="tnp-icon tnp-blue"><i class="fas fa-envelope-open"></i></div>
                    <div class="tnp-value"><?php echo $report->open_rate; ?>%</div>
                    <div class="tnp-value-2">(<?php echo $report->open_count; ?>)</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="tnp-widget tnp-number">
                    <h3><?php _e('Clicks', 'newsletter') ?></h3>
                    <div class="tnp-icon tnp-orange"><i class="fas fa-mouse-pointer"></i></div>
                    <div class="tnp-value"><?php echo $report->click_rate; ?>%</div>
                    <div class="tnp-value-2">(<?php echo $report->click_count; ?>)</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="tnp-widget tnp-number tnp-inactive">
                    <h3>Reactivity</h3>
                    <div class="tnp-icon tnp-gray"><i class="fas fa-star"></i></div>
                    <div class="tnp-value">-%</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="tnp-widget tnp-inactive">
                    <h3>Clicked URLs</h3>
                    <div class="tnp-placeholder">
                        <a href="https://www.thenewsletterplugin.com/premium?utm_source=reports&utm_medium=urls&utm_campaign=plugin" target="_blank">
                            <img src="<?php echo plugins_url('newsletter') ?>/statistics/images/clicked-urls@2x.png">
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="tnp-widget tnp-inactive">
                    <h3>World Map</h3>
                    <div class="tnp-placeholder">
                        <a href="https://www.thenewsletterplugin.com/premium?utm_source=reports&utm_medium=map&utm_campaign=plugin" target="_blank">
                            <img src="<?php echo plugins_url('newsletter') ?>/statistics/images/world-map@2x.png">
                        </a>
                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="tnp-widget tnp-inactive">
                    <h3>Interactions</h3>
                    <div class="tnp-placeholder">
                        <a href="https://www.thenewsletterplugin.com/premium?utm_source=reports&utm_medium=interactions&utm_campaign=plugin" target="_blank">
                            <img src="<?php echo plugins_url('newsletter') ?>/statistics/images/interactions@2x.png">
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <?php include NEWSLETTER_DIR . '/tnp-footer.php' ?>
</div>
