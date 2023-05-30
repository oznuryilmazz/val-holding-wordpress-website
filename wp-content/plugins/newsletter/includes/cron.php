<?php
/**
 * Set up and log the cron system. To be loaded at the plugin start.
 */
defined('ABSPATH') || exit;

// Can be redefined in wp-config.php (not recommended)
if (!defined('NEWSLETTER_CRON_INTERVAL')) {
    define('NEWSLETTER_CRON_INTERVAL', 300);
}

// Logging of the cron calls to debug the so many situations where the cron is not triggered at all (grrr...).
if (defined('DOING_CRON') && DOING_CRON) {
    $calls = get_option('newsletter_diagnostic_cron_calls', []);
    // Protection against scrambled options or bad written database caching plugin (yes, it happened, grrr...).
    if (!is_array($calls)) {
        $calls = [];
    }
    $calls[] = time();
    // TODO: create a constant for samples limit
    if (count($calls) > 100) {
        // TODO: optimize using array_slice() and call every ten records
        array_shift($calls);
    }
    update_option('newsletter_diagnostic_cron_calls', $calls, false);
}

// As soon as possible but with low priority so it is ecxecutes as last filter to avoid bad witten
// filters which remove other's schedules (yes, it happened, grrr...).
add_filter('cron_schedules', function ($schedules) {
    $schedules['newsletter'] = [
        'interval' => NEWSLETTER_CRON_INTERVAL,
        'display' => 'Every ' . NEWSLETTER_CRON_INTERVAL . ' seconds'
    ];
    return $schedules;
}, 1000);
