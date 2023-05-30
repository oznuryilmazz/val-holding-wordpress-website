<?php
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$current_language = $module->get_current_language();

$is_all_languages = $module->is_all_languages();

$controls->add_language_warning();

$options = $module->get_options('', $current_language);

if ($controls->is_action()) {
    if ($controls->is_action('save')) {

        $defaults = $module->get_default_options();

        // Without the last curly bracket since there can be a form number apended
        if (empty($controls->data['subscription_text'])) {
            $controls->data['subscription_text'] = $defaults['subscription_text'];
        }

        if (empty($controls->data['confirmation_text'])) {
            $controls->data['confirmation_text'] = $defaults['confirmation_text'];
        }

        if (empty($controls->data['confirmation_subject'])) {
            $controls->data['confirmation_subject'] = $defaults['confirmation_subject'];
        }

        if (empty($controls->data['confirmation_message'])) {
            $controls->data['confirmation_message'] = $defaults['confirmation_message'];
        }

        if (empty($controls->data['confirmed_text'])) {
            $controls->data['confirmed_text'] = $defaults['confirmed_text'];
        }

        if (empty($controls->data['confirmed_subject'])) {
            $controls->data['confirmed_subject'] = $defaults['confirmed_subject'];
        }

        if (empty($controls->data['confirmed_message'])) {
            $controls->data['confirmed_message'] = $defaults['confirmed_message'];
        }

        $controls->data['confirmed_message'] = NewsletterModule::clean_url_tags($controls->data['confirmed_message']);
        $controls->data['confirmed_text'] = NewsletterModule::clean_url_tags($controls->data['confirmed_text']);
        $controls->data['confirmation_text'] = NewsletterModule::clean_url_tags($controls->data['confirmation_text']);
        $controls->data['confirmation_message'] = NewsletterModule::clean_url_tags($controls->data['confirmation_message']);

        $controls->data['confirmed_url'] = trim($controls->data['confirmed_url']);
        $controls->data['confirmation_url'] = trim($controls->data['confirmation_url']);

        $module->save_options($controls->data, '', null, $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        $controls->data = $module->reset_options();
    }

    if ($controls->is_action('test-confirmation')) {

        $users = NewsletterUsers::instance()->get_test_users();
        if (count($users) == 0) {
            $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
        } else {
            $addresses = array();
            foreach ($users as &$user) {
                $addresses[] = $user->email;
                $user->language = $current_language;
                $res = $module->send_message('confirmation', $user);
                if (!$res) {
                    $controls->errors = 'The email address ' . $user->email . ' failed.';
                    break;
                }
            }
            $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers: ' .
                    implode(', ', $addresses) . '. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
            $controls->messages .= '<br>If the message is not received, try to change the message text it could trigger some antispam filters.';
        }
    }

    if ($controls->is_action('test-confirmed')) {

        $users = NewsletterUsers::instance()->get_test_users();
        if (count($users) == 0) {
            $controls->errors = 'There are no test subscribers. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
        } else {
            $addresses = array();
            foreach ($users as $user) {
                $addresses[] = $user->email;
                // Force the language to send the message coherently with the current panel view
                $user->language = $current_language;
                $res = $module->send_message('confirmed', $user);
                if (!$res) {
                    $controls->errors = 'The email address ' . $user->email . ' failed.';
                    break;
                }
            }
            $controls->messages .= 'Test emails sent to ' . count($users) . ' test subscribers: ' .
                    implode(', ', $addresses) . '. Read more about test subscribers <a href="https://www.thenewsletterplugin.com/plugins/newsletter/subscribers-module#test" target="_blank">here</a>.';
            $controls->messages .= '<br>If the message is not received, try to change the message text it could trigger some antispam filters.';
        }
    }
} else {
    $controls->data = $module->get_options('', $current_language);
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('/subscription') ?>
        <h2><?php _e('Subscription Configuration', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">


        <form method="post" action="">
            <?php $controls->init(); ?>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-general"><?php _e('General', 'newsletter') ?></a></li>
                    <li><a href="#tabs-2"><?php _e('Subscription', 'newsletter') ?></a></li>
                    <li><a href="#tabs-4"><?php _e('Welcome', 'newsletter') ?></a></li>
                    <li><a href="#tabs-3"><?php _e('Activation', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-general">

                    <?php if ($is_all_languages) { ?>
                        <table class="form-table">

                            <tr>
                                <th><?php $controls->field_label(__('Opt In', 'newsletter'), '/subscription/subscription/') ?></th>
                                <td>
                                    <?php $controls->select('noconfirmation', array(0 => __('Double Opt In', 'newsletter'), 1 => __('Single Opt In', 'newsletter'))); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php $controls->field_label(__('Override Opt In', 'newsletter'), '/subscription/subscription/#advanced') ?></th>
                                <td>
                                    <?php $controls->yesno('optin_override'); ?>
                                </td>
                            </tr>

                            <tr>
                                <th><?php _e('Notifications', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->yesno('notify'); ?>
                                    <?php $controls->text_email('notify_email'); ?>
                                </td>
                            </tr>
                        </table>
                    <?php } else { ?>
                        <?php $controls->switch_to_all_languages_notice(); ?>
                    <?php } ?>

                </div>


                <div id="tabs-2">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Subscription page', 'newsletter') ?><br><?php echo $controls->help('/newsletter-tags') ?></th>
                            <td>
                                <?php $controls->wp_editor('subscription_text'); ?>
                            </td>
                        </tr>

                    </table>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Repeated subscriptions', 'newsletter') ?></th>
                            <td>
                                <?php
                                $controls->select('multiple', ['0' => __('Not allowed', 'newsletter'), '1' => __('Allowed', 'newsletter'),
                                    '1' => __('Allowed force single opt-in', 'newsletter')]);
                                ?> 
                                <br><br>
                                <?php $controls->wp_editor('error_text'); ?>
                                <p class="description">Shown only when "not allowed" is selected<p>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-3">

                    <p><?php _e('Only for double opt-in mode.', 'newsletter') ?></p>


                    <table class="form-table">
                        <tr>
                            <th><?php _e('Activation message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('confirmation_text'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Alternative activation page', 'newsletter'); ?></th>
                            <td>
                                <?php $controls->text('confirmation_url', 70, 'https://...'); ?>
                            </td>
                        </tr>


                        <!-- CONFIRMATION EMAIL -->
                        <tr>
                            <th><?php _e('Activation email', 'newsletter') ?></th>
                            <td>
                                <?php $controls->email('confirmation', 'wordpress'); ?>
                                <br>
                                <?php $controls->btn('test-confirmation', __('Test', 'newsletter'), ['secondary' => true]); ?>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="tabs-4">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Welcome message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('confirmed_text'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Alternative welcome page URL', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('confirmed_url', 70, 'https://...'); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Conversion tracking code', 'newsletter') ?>
                                <?php $controls->help('/subscription#conversion') ?></th>
                            <td>
                                <?php $controls->textarea('confirmed_tracking'); ?>
                            </td>
                        </tr>

                        <!-- WELCOME/CONFIRMED EMAIL -->
                        <tr>
                            <th>
                                <?php _e('Welcome email', 'newsletter') ?>
                            </th>
                            <td>
                                <?php $controls->email('confirmed', 'wordpress', $is_all_languages); ?>
                                <br>
                                <?php $controls->btn('test-confirmed', __('Test', 'newsletter'), ['secondary' => true]); ?>
                            </td>
                        </tr>

                    </table>
                </div>

            </div>

            <p>
                <?php $controls->button_save() ?>
                <?php $controls->button_reset() ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
