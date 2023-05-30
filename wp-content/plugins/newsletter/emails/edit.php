<?php
/* @var $this NewsletterEmails */
defined('ABSPATH') || exit;

/* @var $wpdb wpdb */
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();

function tnp_prepare_controls($email, $controls) {
    $controls->data = $email;

    foreach ($email['options'] as $name => $value) {
        $controls->data['options_' . $name] = $value;
    }
}

// Always required
$email = $this->get_email($_GET['id'], ARRAY_A);

if (empty($email)) {
    echo 'Wrong email identifier';
    return;
}

$email_id = $email['id'];

/* Satus changes which require a reload */
if ($controls->is_action('pause')) {
    $this->admin_logger->info('Newsletter ' . $email_id . ' paused');
    $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => 'paused'), array('id' => $email_id));
    $email = $this->get_email($_GET['id'], ARRAY_A);
    tnp_prepare_controls($email, $controls);
}

if ($controls->is_action('continue')) {
    $this->admin_logger->info('Newsletter ' . $email_id . ' restarted');
    $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => 'sending'), array('id' => $email_id));
    $email = $this->get_email($_GET['id'], ARRAY_A);
    tnp_prepare_controls($email, $controls);
}

if ($controls->is_action('abort')) {
    $this->admin_logger->info('Newsletter ' . $email_id . ' aborted');
    $wpdb->query("update " . NEWSLETTER_EMAILS_TABLE . " set last_id=0, sent=0, status='new' where id=" . $email_id);
    $email = $this->get_email($_GET['id'], ARRAY_A);
    tnp_prepare_controls($email, $controls);
    $controls->messages = __('Delivery definitively cancelled', 'newsletter');
}

if ($controls->is_action('change-private')) {
    $data = [];
    $data['private'] = $controls->data['private'];
    $data['id'] = $email['id'];
    $email = $this->save_email($data, ARRAY_A);
    $controls->add_message_saved();

    tnp_prepare_controls($email, $controls);
}


$editor_type = $this->get_editor_type($email);

// Backward compatibility: preferences conversion
if (!$controls->is_action()) {
    if (!isset($email['options']['lists'])) {

        $options_profile = get_option('newsletter_profile');

        if (empty($controls->data['preferences_status_operator'])) {
            $email['options']['lists_operator'] = 'or';
        } else {
            $email['options']['lists_operator'] = 'and';
        }
        $controls->data['options_lists'] = array();
        $controls->data['options_lists_exclude'] = array();

        if (!empty($email['preferences'])) {
            $preferences = explode(',', $email['preferences']);
            $value = empty($email['options']['preferences_status']) ? 'on' : 'off';

            foreach ($preferences as $x) {
                if ($value == 'on') {
                    $controls->data['options_lists'][] = $x;
                } else {
                    $controls->data['options_lists_exclude'][] = $x;
                }
            }
        }
    }
}
// End backward compatibility

if (!$controls->is_action()) {
    tnp_prepare_controls($email, $controls);
}

if ($controls->is_action('html')) {

    $this->admin_logger->info('Newsletter ' . $email_id . ' converted to HTML');

    $data = [];
    $data['editor'] = NewsletterEmails::EDITOR_HTML;
    $data['id'] = $email_id;

    // Backward compatibility: clean up the composer flag
    $data['options'] = $email['options'];
    unset($data['options']['composer']);
    // End backward compatibility

    $email = $this->save_email($data, ARRAY_A);
    $controls->messages = 'You can now edit the newsletter as pure HTML';

    tnp_prepare_controls($email, $controls);

    $editor_type = NewsletterEmails::EDITOR_HTML;
}



if ($controls->is_action('test') || $controls->is_action('save') || $controls->is_action('send') || $controls->is_action('schedule')) {

    if ($email['updated'] != $controls->data['updated']) {
        $controls->errors = 'This newsletter has been modified by someone else. Cannot save.';
    } else {
        $email['updated'] = time();
        if ($controls->is_action('save')) {
            $this->admin_logger->info('Saving newsletter: ' . $email_id);
        } else if ($controls->is_action('send')) {
            $this->admin_logger->info('Sending newsletter: ' . $email_id);
        } else if ($controls->is_action('schedule')) {
            $this->admin_logger->info('Scheduling newsletter: ' . $email_id);
        }

        $email['subject'] = $controls->data['subject'];
        $email['track'] = $controls->data['track'];
        $email['editor'] = $editor_type;
        $email['private'] = $controls->data['private'];
        $email['message_text'] = $controls->data['message_text'];
        if ($controls->is_action('send')) {
            $email['send_on'] = time();
        } else {
            // Patch, empty on continuation
            if (!empty($controls->data['send_on'])) {
                $email['send_on'] = $controls->data['send_on'];
            }
        }

        // Reset and refill the options
        // Try without the reset and let's see where the problems are
        //$email['options'] = array();
        // Reset only specific keys
        unset($email['options']['lists']);
        unset($email['options']['lists_operator']);
        unset($email['options']['lists_exclude']);
        unset($email['options']['sex']);
        for ($i = 1; $i <= 20; $i++) {
            unset($email['options']["profile_$i"]);
        }

        // Patch for Geo addon to be solved with a filter
        unset($email['options']['countries']);
        unset($email['options']['regions']);
        unset($email['options']['cities']);

        foreach ($controls->data as $name => $value) {
            if (strpos($name, 'options_') === 0) {
                $email['options'][substr($name, 8)] = $value;
            }
        }

        // Before send, we build the query to extract subscriber, so the delivery engine does not
        // have to worry about the email parameters
        if ($email['options']['status'] == 'S') {
            $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='S'";
        } else {
            $query = "select * from " . NEWSLETTER_USERS_TABLE . " where status='C'";
        }

        if ($email['options']['wp_users'] == '1') {
            $query .= " and wp_user_id<>0";
        }

        if (!empty($email['options']['language'])) {
            $query .= " and language='" . esc_sql((string) $email['options']['language']) . "'";
        }


        $list_where = array();
        if (isset($email['options']['lists']) && count($email['options']['lists'])) {
            foreach ($email['options']['lists'] as $list) {
                $list = (int) $list;
                $list_where[] = 'list_' . $list . '=1';
            }
        }

        if (!empty($list_where)) {
            if (isset($email['options']['lists_operator']) && $email['options']['lists_operator'] == 'and') {
                $query .= ' and (' . implode(' and ', $list_where) . ')';
            } else {
                $query .= ' and (' . implode(' or ', $list_where) . ')';
            }
        }

        // Excluded lists
        $list_where = array();
        if (isset($email['options']['lists_exclude']) && count($email['options']['lists_exclude'])) {
            foreach ($email['options']['lists_exclude'] as $list) {
                $list = (int) $list;
                $list_where[] = 'list_' . $list . '=0';
            }
        }
        if (!empty($list_where)) {
            // Must not be in one of the excluded lists
            $query .= ' and (' . implode(' and ', $list_where) . ')';
        }

        // Gender
        if (isset($email['options']['sex'])) {
            $sex = $email['options']['sex'];
            if (is_array($sex) && count($sex)) {
                $query .= " and sex in (";
                foreach ($sex as $x) {
                    $query .= "'" . esc_sql((string) $x) . "', ";
                }
                $query = substr($query, 0, -2);
                $query .= ")";
            }
        }

        // Profile fields filter
        $profile_clause = array();
        for ($i = 1; $i <= 20; $i++) {
            if (isset($email["options"]["profile_$i"]) && count($email["options"]["profile_$i"])) {
                $profile_clause[] = 'profile_' . $i . " IN ('" . implode("','", esc_sql($email["options"]["profile_$i"])) . "') ";
            }
        }

        if (!empty($profile_clause)) {
            $query .= ' and (' . implode(' and ', $profile_clause) . ')';
        }

        // Temporary save to have an object and call the query filter
        $e = Newsletter::instance()->save_email($email);
        $query = apply_filters('newsletter_emails_email_query', $query, $e);

        $email['query'] = $query;
        if ($email['status'] == 'sent') {
            $email['total'] = $email['sent'];
        } else {
            $email['total'] = $wpdb->get_var(str_replace('*', 'count(*)', $query));
        }

        if ($controls->is_action('send') && $controls->data['send_on'] < time()) {
            $controls->data['send_on'] = time();
        }

        $email = Newsletter::instance()->save_email($email, ARRAY_A);

        tnp_prepare_controls($email, $controls);

        if ($email === false) {
            $controls->errors = 'Unable to save. Try to deactivate and reactivate the plugin may be the database is out of sync.';
        }

        $controls->add_message_saved();
    }
}

if (empty($controls->errors) && ($controls->is_action('send') || $controls->is_action('schedule'))) {

    NewsletterStatistics::instance()->reset_stats($email);

    if ($email['subject'] == '') {
        $controls->errors = __('A subject is required to send', 'newsletter');
    } else {
        $wpdb->update(NEWSLETTER_EMAILS_TABLE, array('status' => TNP_Email::STATUS_SENDING), array('id' => $email_id));
        $email['status'] = TNP_Email::STATUS_SENDING;
        if ($controls->is_action('send')) {
            $controls->messages = __('Now sending.', 'newsletter');
        } else {
            $controls->messages = __('Scheduled.', 'newsletter');
        }
    }
}

if (isset($email['options']['status']) && $email['options']['status'] == 'S') {
    $controls->warnings[] = __('This newsletter will be sent to not confirmed subscribers.', 'newsletter');
}

if (strpos($email['message'], '{profile_url}') === false && strpos($email['message'], '{unsubscription_url}') === false && strpos($email['message'], '{unsubscription_confirm_url}') === false) {
    $controls->warnings[] = __('The message is missing the subscriber profile or cancellation link.', 'newsletter');
}

if (TNP_Email::STATUS_ERROR === $email['status'] && isset($email['options']['error_message'])) {
    $controls->errors .= sprintf(__('Stopped by fatal error: %s', 'newsletter'), esc_html($email['options']['error_message']));
}


if ($email['status'] != 'sent') {
    $subscriber_count = $wpdb->get_var(str_replace('*', 'count(*)', $email['query']));
} else {
    $subscriber_count = $email['sent'];
}
?>
<style>
<?php readfile(__DIR__ . '/assets/edit.css') ?>
</style>

<div class="wrap tnp-emails tnp-emails-edit" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('/newsletter-targeting') ?>

        <h2><?php _e('Edit Newsletter', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">
        <form method="post" action="" id="newsletter-form">
            <?php $controls->init(['cookie_name' => 'newsletter_emails_edit_tab']); ?>
            <?php $controls->hidden('updated') ?>

            <div class="tnp-submit">

                <?php if ($email['status'] == 'sending' || $email['status'] == 'sent') { ?>

                    <?php $controls->button_back('?page=newsletter_emails_index') ?>

                <?php } else { ?>

                    <a class="button-primary" href="<?php echo $module->get_editor_url($email_id, $editor_type) ?>">
                        <i class="fas fa-edit"></i> <?php _e('Edit', 'newsletter') ?>
                    </a>

                <?php } ?>

                <?php if ($email['status'] != 'sending' && $email['status'] != 'sent') $controls->button_save(); ?>
                <?php if ($email['status'] == 'new') $controls->button_confirm('send', __('Send now', 'newsletter'), __('Start real delivery?', 'newsletter')); ?>
                <?php if ($email['status'] == 'sending') $controls->button_confirm('pause', __('Pause', 'newsletter'), __('Pause the delivery?', 'newsletter')); ?>
                <?php if ($email['status'] == 'paused' || $email['status'] == 'error') $controls->button_confirm('continue', __('Continue', 'newsletter'), 'Continue the delivery?'); ?>
                <?php if ($email['status'] == 'paused') $controls->button_confirm('abort', __('Stop', 'newsletter'), __('This totally stop the delivery, ok?', 'newsletter')); ?>
                <?php if ($email['status'] == 'new' || ( $email['status'] == 'paused' && $email['send_on'] > time() )) { ?>
                    <a id="tnp-schedule-button" class="button-secondary" href="javascript:tnp_toggle_schedule()"><i class="far fa-clock"></i> <?php _e("Schedule") ?></a>
                    <span id="tnp-schedule" style="display: none;">
                        <?php $controls->datetime('send_on') ?>
                        <?php $controls->button_confirm('schedule', __('Schedule', 'newsletter'), __('Schedule delivery?', 'newsletter')); ?>
                        <a class="button-secondary tnp-button-cancel" href="javascript:tnp_toggle_schedule()"><?php _e("Cancel") ?></a>
                    </span>
                <?php } ?>
            </div>

            <div class="tnp-emails-header">

                <div class="tnp-emails-subject">



                    <?php $controls->text('subject', null, 'Subject'); ?>
                    &nbsp;&nbsp;&nbsp;
                    <i class="far fa-lightbulb" data-tnp-modal-target="#subject-ideas-modal" style="font-size: 24px"></i>
                </div>

                <div class="tnp-emails-status">

                    <div style="display: flex; justify-content: space-between">
                        <div style="flex-grow: 1">
                            <?php $module->show_email_status_label($email) ?>
                        </div>

                        <div style="flex-grow: 1">
                            <?php
                            if ($email['status'] == 'sending' && $email['send_on'] > time() || $email['status'] == 'sent') {
                                echo $module->format_date($email['send_on']);
                            } else {
                                $module->show_email_progress_bar($email);
                            }
                            ?>

                        </div>

                        <div style="flex-grow: 1; text-align: right">
                            <?php if ($email['status'] == 'new') { ?>
                                <i class="fas fa-users"></i> <?php echo $subscriber_count ?>
                            <?php } else { ?>
                                <i class="fas fa-users"></i> <?php $this->show_email_progress_numbers($email) ?>
                            <?php } ?>
                        </div>

                    </div>

                </div>
            </div>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-options"><?php _e('Targeting', 'newsletter') ?></a></li>
                    <li><a href="#tabs-advanced"><?php _e('Advanced', 'newsletter') ?></a></li>
                    <li><a href="#tabs-preview"><?php _e('Preview', 'newsletter') ?></a></li>
                </ul>


                <div id="tabs-options" class="tnp-list-conditions">

                    <p>
                        <?php _e('Leaving all multichoice options unselected is like to select all them', 'newsletter'); ?>
                    </p>
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Lists', 'newsletter') ?></th>
                            <td>
                                <?php
                                $lists = $controls->get_list_options();
                                ?>
                                <?php $controls->select('options_lists_operator', array('or' => __('Match at least one of', 'newsletter'), 'and' => __('Match all of', 'newsletter'))); ?>

                                <?php $controls->select2('options_lists', $lists, null, true, null, __('All', 'newsletter')); ?>

                                <br>
                                <?php _e('must not in one of', 'newsletter') ?>

                                <?php $controls->select2('options_lists_exclude', $lists, null, true, null, __('None', 'newsletter')); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Language', 'newsletter') ?></th>
                            <td>
                                <?php $controls->language('options_language'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Gender', 'newsletter') ?></th>
                            <td>
                                <?php $controls->checkboxes_group('options_sex', array('f' => 'Women', 'm' => 'Men', 'n' => 'Not specified')); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Status', 'newsletter') ?></th>
                            <td>
                                <?php $controls->select('options_status', array('C' => __('Confirmed', 'newsletter'), 'S' => __('Not confirmed', 'newsletter'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Only to subscribers linked to WP users', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('options_wp_users'); ?>
                            </td>
                        </tr>
                        <?php
                        $fields = TNP_Profile_Service::get_profiles('', TNP_Profile::TYPE_SELECT);
                        ?>
                        <?php if (!empty($fields)) { ?>
                            <tr>
                                <th><?php _e('Profile fields', 'newsletter') ?></th>
                                <td>
                                    <?php foreach ($fields as $profile) { ?>
                                        <?php echo esc_html($profile->name), ' ', __('is one of:', 'newsletter') ?>
                                        <?php $controls->select2("options_profile_$profile->id", $profile->options, null, true, null, __('Do not filter by this field', 'newsletter')); ?>
                                        <br>
                                    <?php } ?>
                                    <p class="description">

                                    </p>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                    <?php do_action('newsletter_emails_edit_target', $module->get_email($email_id), $controls) ?>

                </div>


                <div id="tabs-advanced">

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Keep private', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('private'); ?>
                                <?php if ($email['status'] == 'sent') { ?>
                                    <?php $controls->button('change-private', __('Save')) ?>
                                <?php } ?>
                                <span class="description">
                                    <?php _e('Hide/show from public sent newsletter list.', 'newsletter') ?>
                                    <?php _e('Used by', 'newsletter') ?>: <a href="" target="_blank">Archive Addon</a>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Track clicks and message opening', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('track'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Sender email address', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text_email('options_sender_email', 40); ?>
                                <p class="description">
                                    Original: <?php echo esc_html(Newsletter::instance()->get_sender_email()) ?>.<br>
                                    If you use a delivery service, be sure to use a validated email address.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Sender name', 'newsletter') ?>
                            </th>
                            <td>
                                <?php $controls->text('options_sender_name', 40); ?>
                                <p class="description">
                                    Original: <?php echo esc_html(Newsletter::instance()->get_sender_name()) ?>
                                </p> 
                            </td>
                        </tr>
                    </table>

                    <?php do_action('newsletter_emails_edit_other', $module->get_email($email_id), $controls) ?>

                    <table class="form-table">
                        <tr>
                            <th>Query (tech)</th>
                            <td><?php echo esc_html($email['query']); ?></td>
                        </tr>
                        <tr>
                            <th>Token (tech)</th>
                            <td><?php echo esc_html($email['token']); ?></td>
                        </tr>
                        <tr>
                            <th style="vertical-align: top">
                                This is the textual version of your newsletter. 
                                If you empty it, only an HTML version will be sent but is an anti-spam best practice to include a text only version.
                            </th>
                            <td>
                                <?php if ($editor_type == NewsletterEmails::EDITOR_COMPOSER) { ?>
                                    <?php $controls->select('options_text_message_mode', ['' => __('Autogenerate', 'newsletter'), '1' => __('Hand edited', 'newsletter')]) ?>
                                    <p class="description"></p>
                                <?php } ?>

                                <?php $controls->textarea_fixed('message_text', '100%', '500'); ?>
                                <!--
                                <p class="tnp-tab-warning">
                                    See <a href="https://wordpress.org/plugins/plaintext-newsletter/" target="_blank">this plugin</a> for automatic plaintext generation.
                                </p>
                                -->
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-preview">

                    <div class="tnpc-preview">
                        <!-- Flat Laptop Browser -->
                        <div class="fake-browser-ui">
                            <div class="frame">
                                <span class="bt-1"></span>
                                <span class="bt-2"></span>
                                <span class="bt-3"></span>
                            </div>
                            <iframe id="tnpc-preview-desktop" src="" width="700" height="520" alt="" frameborder="0"></iframe>
                        </div>

                        <!-- Flat Mobile Browser -->
                        <div class="fake-mobile-browser-ui">
                            <iframe id="tnpc-preview-mobile" src="" width="320" height="445" alt="" frameborder="0"></iframe>
                            <div class="frame">
                                <span class="bt-4"></span>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript">
                        preview_url = ajaxurl + "?action=tnpc_preview&id=<?php echo $email_id ?>";
                        jQuery('#tnpc-preview-desktop, #tnpc-preview-mobile').attr("src", preview_url);
                        setTimeout(function () {
                            jQuery('#tnpc-preview-desktop, #tnpc-preview-mobile').contents().find("a").click(function (e) {
                                e.preventDefault();
                            })
                        }, 500);
                    </script>

                    <p>
                        <?php if ($editor_type != NewsletterEmails::EDITOR_HTML && $email['status'] != 'sending' && $email['status'] != 'sent') $controls->button_confirm('html', __('Convert to HTML newsletter', 'newsletter'), 'Attention: no way back!'); ?>
                    </p>
                </div>

            </div>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/emails/subjects.php'; ?>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
