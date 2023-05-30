<?php
global $current_user, $wpdb;

defined('ABSPATH') || exit;

$dismissed = get_option('newsletter_dismissed', []);

$user_count = Newsletter::instance()->get_user_count();

$is_administrator = current_user_can('administrator');

function newsletter_print_entries($group) {
    $entries = apply_filters('newsletter_menu_' . $group, array());
    if (!$entries) {
        return;
    }

    foreach ($entries as &$entry) {
        echo '<li><a href="', $entry['url'], '">', $entry['label'];
        if (!empty($entry['description'])) {
            //echo '<small>', $entry['description'], '</small>';
        }
        echo '</a></li>';
    }
}

// Check the status to show a warning if needed
$status_options = Newsletter::instance()->get_options('status');
$warning = false;

//$warning |= empty($status_options['mail']);

$current_user_email = ''; //$current_user->user_email;
//if (strpos($current_user_email, 'admin@') === 0) {
//    $current_user_email = '';
//}

$system_warnings = NewsletterSystem::instance()->get_warnings_count();
?>

<div id="tnp-menu">
    <ul>
        <li>
            <a href="?page=newsletter_main_index"><img src="<?php echo plugins_url('newsletter'); ?>/admin/images/logo.png" class="tnp-header-logo" style="vertical-align: bottom;"></a>

        </li>
        <li><a href="#"><i class="fas fa-users"></i> <?php _e('Subscribers', 'newsletter') ?></a>
            <ul>
                <li>
                    <a href="?page=newsletter_users_index"><?php _e('All subscribers', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_profile_index"><?php _e('Profile page', 'newsletter') ?></a>
                </li>

                <?php if (!class_exists('NewsletterImport')) { ?>
                    <li>
                        <a href="?page=newsletter_users_import"><?php _e('Import', 'newsletter') ?></a>
                    </li>
                <?php } ?>

                <li>
                    <a href="?page=newsletter_users_export"><?php _e('Export', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_users_massive"><?php _e('Maintenance', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_users_statistics"><?php _e('Statistics', 'newsletter') ?></a>
                </li>

                <?php newsletter_print_entries('subscribers') ?>
            </ul>
        </li>
        <li><a href="#"><i class="fas fa-list"></i> <?php _e('List Building', 'newsletter') ?></a>
            <ul>

                <li>
                    <?php $url = Newsletter::instance()->is_multilanguage() ? '?page=newsletter_subscription_options' : '?page=newsletter_subscription_options'; ?>
                    <a href="<?php echo $url ?>"><?php _e('Subscription', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_profile"><?php _e('Subscription Form Fields, Buttons, Labels', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_lists"><?php _e('Lists', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_antibot"><?php _e('Antispam', 'newsletter') ?></a>
                </li>

                <li>
                    <?php if (true || $this->is_multilanguage()) { ?>
                        <a href="?page=newsletter_unsubscription_index"><?php _e('Unsubscribe', 'newsletter') ?></a>
                    <?php } else { ?>
                        <a href="?page=newsletter_unsubscription_index"><?php _e('Unsubscribe', 'newsletter') ?></a>
                    <?php } ?>
                </li>

                <?php
                newsletter_print_entries('subscription');
                ?>
            </ul>
        </li>

        <li>
            <a href="#"><i class="fas fa-newspaper"></i> <?php _e('Newsletters', 'newsletter') ?></a>
            <ul>
                <li>
                    <a href="?page=newsletter_emails_index"><?php _e('All newsletters', 'newsletter') ?></a>
                </li>
                
                <li>
                    <a href="?page=newsletter_emails_composer"><?php _e('Create newsletter', 'newsletter') ?></a>
                </li>

                

                <li>
                    <a href="<?php echo NewsletterStatistics::instance()->get_index_url() ?>"><?php _e('Statistics', 'newsletter') ?></a>
                </li>
                <?php
                newsletter_print_entries('newsletters');
                ?>
            </ul>
        </li>

        <li>
            <a href="#"><i class="fas fa-cog"></i> <?php _e('Settings', 'newsletter') ?></a>
            <ul>
                <?php if ($is_administrator) { ?>
                    <li>
                        <a href="?page=newsletter_main_main"><?php _e('General Settings', 'newsletter') ?></a>
                    </li>
                    <?php if (!class_exists('NewsletterSmtp')) { ?>
                        <li>
                            <a href="?page=newsletter_main_smtp"><?php _e('SMTP', 'newsletter') ?></a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <li>
                    <a href="?page=newsletter_main_info"><?php _e('Company Info', 'newsletter') ?></a>
                </li>

                <li>
                    <a href="?page=newsletter_subscription_template"><?php _e('Messages Template', 'newsletter') ?></a>
                </li>

                <?php
                newsletter_print_entries('settings');
                ?>
            </ul>
        </li>

        <?php if ($is_administrator) { ?>
            <li>
                <a href="#"> 
                    <?php if ($system_warnings['total']) { ?>
                        <i class="fas fa-exclamation-triangle" style="color: red;"></i>
                    <?php } else { ?>
                        <i class="fas fa-thermometer"></i>
                    <?php } ?>
                    <?php _e('System', 'newsletter') ?>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo admin_url('site-health.php') ?> "><?php _e('WP Site Health') ?></a>
                    </li>
                    <li>
                        <a href="?page=newsletter_system_delivery"><?php _e('Delivery Diagnostic', 'newsletter') ?></a>
                    </li>
                    <li>
                        <a href="?page=newsletter_system_scheduler"><?php _e('Scheduler', 'newsletter') ?>
                            <?php if ($system_warnings['scheduler']) { ?>
                                <i class="fas fa-exclamation-triangle tnp-menu-warning" style="color: red;"></i>
                            <?php } ?>
                        </a>
                    </li>
                    <li>
                        <a href="?page=newsletter_system_status"><?php _e('Status', 'newsletter') ?>
                            <?php if ($system_warnings['status']) { ?>
                                <i class="fas fa-exclamation-triangle tnp-menu-warning" style="color: red;"></i>
                            <?php } ?>
                        </a>
                    </li>

                    <li>
                        <a href="?page=newsletter_system_logs"><?php _e('Logs', 'newsletter') ?></a>

                    </li>
                    <li>
                        <a href="https://www.thenewsletterplugin.com/documentation/developers/backup-recovery/" target="_blank"><?php _e('Backup', 'newsletter') ?></a>
                    </li>
                </ul>
            </li>
        <?php } ?>

        <?php
        $license_data = Newsletter::instance()->get_license_data();
        $premium_url = 'https://www.thenewsletterplugin.com/premium?utm_source=header&utm_medium=link&utm_campaign=plugin&utm_content=' . urlencode($_GET['page']);
        ?>

        <?php if (empty($license_data)) { ?>

            <li class="tnp-licence-button"><a href="<?php echo $premium_url ?>" target="_blank">
                    <i class="fas fa-trophy"></i> <?php _e('Get Professional Addons', 'newsletter') ?></a>
            </li>

        <?php } elseif (is_wp_error($license_data)) { ?>

            <li class="tnp-licence-button-red">
                <a href="?page=newsletter_main_main"><i class="fas fa-hand-paper" style="color: white"></i> <?php _e('Unable to check', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire == 0) { ?>

            <li class="tnp-licence-button">
                <a href="<?php echo $premium_url ?>" target="_blank"><i class="fas fa-trophy"></i> <?php _e('Get Professional Addons', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire < time()) { ?>

            <li class="tnp-licence-button-red">
                <a href="?page=newsletter_main_main"><i class="fas fa-hand-paper" style="color: white"></i> <?php _e('License expired', 'newsletter') ?></a>
            </li>

        <?php } elseif ($license_data->expire >= time()) { ?>

            <?php $p = class_exists('NewsletterExtensions') ? 'newsletter_extensions_index' : 'newsletter_main_extensions'; ?>
            <li class="tnp-licence-button">
                <a href="?page=<?php echo $p ?>"><i class="fas fa-check-square"></i> <?php _e('License active', 'newsletter') ?></a>
            </li>

        <?php } ?>
    </ul>
</div>

<?php
$news = Newsletter::instance()->get_news();
?>

<?php foreach ($news as $n) { ?>
    <div class="tnp-news">
        <a href="<?php echo esc_attr($_SERVER['REQUEST_URI'] . '&news=' . urlencode($n['id'])) ?>" class="tnp-news-dismiss">&times;</a>
        <div class="tnp-news-message"><?php echo esc_html($n['message']) ?></div>
        <div class="tnp-news-cta">
            <a class="tnp-news-link" href="<?php echo esc_attr($n['url']) ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($n['label']) ?></a>
        </div>
    </div>
    <?php break; ?>
<?php } ?>

<?php
if (!empty(Newsletter::instance()->options['page'])) {
    $tnp_page_id = Newsletter::instance()->options['page'];
    if (get_post_status($tnp_page_id) !== 'publish') {
        echo '<div class="tnp-notice tnp-notice-warning">The Newsletter dedicated page is not published. <a href="', site_url('/wp-admin/post.php') . '?post=', $tnp_page_id, '&action=edit"><strong>Edit the page</strong></a> or <a href="admin.php?page=newsletter_main_main"><strong>review the main settings</strong></a>.</div>';
    } else if (!isset($dismissed['newsletter-shortcode'])) {
        $content = get_post_field('post_content', $tnp_page_id);
        // With and without attributes
        if (strpos($content, '[newsletter]') === false && strpos($content, '[newsletter ') === false) {
            ?>
            <div class="tnp-notice tnp-notice-warning">
                <a href="<?php echo esc_attr($_SERVER['REQUEST_URI']) . '&dismiss=newsletter-shortcode' ?>" class="tnp-dismiss">&times;</a>
                The Newsletter dedicated page does not contain the <code>[newsletter]</code> shortcode. If you're using a visual composer it could be ok.
                <a href="<?php echo site_url('/wp-admin/post.php') ?>?post=<?php echo esc_attr($tnp_page_id) ?>&action=edit"><strong>Edit the page</strong></a>.
            </div>
            <?php
        }
    }
} else {
    /*
      <div class="tnp-notice">
      <a href="<?php echo esc_attr($_SERVER['REQUEST_URI']) . '&noheader=1&dismiss=newsletter-page' ?>" class="tnp-dismiss">&times;</a>

      You should create a blog page to show the subscription form and the subscription messages. Go to the
      <a href="?page=newsletter_main_main">general settings panel</a> to configure it.

      </div>
     */
}
?>

<?php if (isset($_GET['debug']) || !isset($dismissed['rate']) && $user_count > 300) { ?>
    <div class="tnp-notice">
        <a href="<?php echo esc_attr($_SERVER['REQUEST_URI']) . '&noheader=1&dismiss=rate' ?>" class="tnp-dismiss">&times;</a>

        We never asked before and we're curious: <a href="http://wordpress.org/plugins/newsletter/" target="_blank">would you rate this plugin</a>?
        (few seconds required - account on WordPress.org required, every blog owner should have one...). <strong>Really appreciated, The Newsletter Team</strong>.

    </div>
<?php } ?>

<?php if (isset($_GET['debug']) || !isset($dismissed['newsletter-subscribe']) && get_option('newsletter_install_time') && get_option('newsletter_install_time') < time() - 86400 * 15) { ?>
    <div class="tnp-notice">
        <a href="<?php echo esc_attr($_SERVER['REQUEST_URI']) . '&noheader=1&dismiss=newsletter-subscribe' ?>" class="tnp-dismiss">&times;</a>
        Subscribe to our news, promotions and getting started lessons!
        Proceeding you agree to the <a href="https://www.thenewsletterplugin.com/privacy" target="_blank">privacy policy</a>.
        <br><br>
        <form action="https://www.thenewsletterplugin.com/?na=s" target="_blank" method="post">
            <input type="hidden" value="plugin-header" name="nr">
            <input type="hidden" value="3" name="nl[]">
            <input type="hidden" value="1" name="nl[]">
            <input type="hidden" value="double" name="optin">
            <input type="email" name="ne" value="<?php echo esc_attr($current_user_email) ?>">
            <input type="submit" class="button-primary" value="<?php esc_attr_e('Subscribe', 'newsletter') ?>">
        </form>
    </div>
<?php } ?>

<?php
if (!defined('NEWSLETTER_CRON_WARNINGS') || NEWSLETTER_CRON_WARNINGS) {
    $x = NewsletterSystem::instance()->get_job_status();
    if ($x !== NewsletterSystem::JOB_OK) {
        echo '<div class="tnp-notice tnp-notice-warning">There are issues with the delivery engine. Please <a href="?page=newsletter_system_scheduler">check them here</a>.</div>';
    }
}
?>

<?php
if ($_GET['page'] !== 'newsletter_emails_edit') {

    $last_failed_newsletters = Newsletter::instance()->get_emails_by_status(TNP_Email::STATUS_ERROR);
    if (false && $last_failed_newsletters) {
        $c = new NewsletterControls();
        foreach ($last_failed_newsletters as $n) {
            echo '<div class="tnp-notice tnp-notice-warning">';
            printf(__('Newsletter "%s" stopped by fatal error.', 'newsletter'), esc_html($n->subject));
            echo '&nbsp;';

            $c->btn_link('?page=newsletter_emails_edit&id=' . $n->id, __('Check', 'newsletter'));
            echo '</div>';
        }
    }
}
?>

<div id="tnp-notification">
    <?php
    if (isset($controls)) {
        $controls->show();
        $controls->messages = '';
        $controls->errors = '';
    }
    ?>
</div>


