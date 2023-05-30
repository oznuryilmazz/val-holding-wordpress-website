<?php
/* @var $this NewsletterSubscription */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterSubscription::instance();

$options = $module->get_options();
$languages = $this->get_languages();

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

        // Unpack the options (OMG...)
        foreach ($languages as $id => $language) {
            $opts = [];
            foreach ($controls->data as $key => $value) {

                // Maybe even strip_tags to avoid empty content made of <p></p>?
                $value = trim($value);
                if (empty($value)) {
                    continue;
                }

                if (substr($key, 0, 2) != $id) {
                    continue;
                }

                $opts[substr($key, 3)] = $value;
            }

            $this->save_options($opts, '', null, $id);
        }
        $module->save_options($controls->data, '', null);

        //$controls->add_message_saved();
        //$controls->add_toast('Saved');
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
                //$user->language = $current_language;
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
    $controls->data = $module->get_options();
    // Merge multilanguage options (and hope for the best)
    foreach ($languages as $id => $language) {
        $opts = $module->get_raw_options('', $language);
        foreach ($opts as $key => $value) {
            $controls->data[$id . '_' . $key] = $value;
        }
    }
}

do_action('newsletter_register_subscription_sources');

$stats = NewsletterUsers::instance()->get_stats();
?>



<script>
    jQuery(function () {
        jQuery('.tnp-flow-fieldset>div').prepend('<i class="fas fa-times"></i>');
        jQuery('.tnp-flow-fieldset i.fas.fa-times').click(function () {
            jQuery('.tnp-flow-fieldset').css('display', 'none');
        });
        jQuery('.tnp-flow-item').on('click', function () {
            console.log(this.dataset);
            if (this.dataset.fieldsetId) {
                document.getElementById(this.dataset.fieldsetId).style.display = 'flex';
            }
        });
    });
    function tnp_save() {
        tinyMCE.triggerSave();
        document.getElementById('tnp-form').submit();
    }

</script>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscription Flow', 'newsletter') ?> <?php $controls->title_help('https://www.thenewsletterplugin.com/documentation/subscription/subscription/') ?></h2>

        <p>
            Welcome to this new <strong>experimental</strong> interface! It is still a bit raw but aimed to make more clear
            the subscriber journey from subscription to opt-out.<br>
            Let us know <a href="mailto:support@thenewsletterplugin.com?subject=Feedback%20new%20interface">your impressions</a> or switch back to the <a href="?page=newsletter_subscription_options">old interface</a>.
        </p>
    </div>

    <div id="tnp-body">

        <div class="tnp-flow">

            <div>
                <a class="tnp-flow-item wide" target="_blank" href="?page=newsletter_subscription_profile">Subscription form</a>
            </div>
            <!--
            <div class="tnp-flow-arrow">
                <i class="fas fa-link"></i>
            </div>
            -->


            <div id="sources-switch" style="margin-bottom: 10px;">
                <a href="#" onclick="document.getElementById('sourcesss').style.display = 'flex'; document.getElementById('sources-switch').style.display = 'none'; return false;">Subscription starting points</a>
            </div>


            <div class="sources" id="sourcesss" style="display: none; width: 100%">
                <div class="tnp-flow-item wide" data-fieldset-id="subscriptionpage">
                    Subscription page
                </div>

                <a class="tnp-flow-item compact" href="widgets.php" target="_blank">
                    Widgets
                </a>
                
                <a class="tnp-flow-item compact inactive" href="https://www.thenewsletterplugin.com/form-builders" target="_blank">
                    3rd Party Forms
                </a>
                
                <?php foreach (NewsletterSubscription::$sources as $source) { ?>
                    <a class="tnp-flow-item compact" target="_blank" href="<?php echo $source['url'] ?>">
                        <?php echo $source['title'] ?> <i class="fas fa-external-link-alt"></i>
                    </a>
                <?php } ?>

                <?php if (class_exists('NewsletterAPI')) { ?>
                    <a class="tnp-flow-item compact" target="_blank" href="?page=newsletter_api_index">
                        Newsletter API
                    </a>
                <?php } else { ?>
                    <a class="tnp-flow-item compact inactive" href="https://www.thenewsletterplugin.com/documentation/developers/newsletter-api-2/" target="_blank">
                        Newsletter API
                    </a>
                <?php } ?>

                <?php if (class_exists('NewsletterWpUsers')) { ?>
                    <a class="tnp-flow-item compact" target="_blank" href="?page=newsletter_wpusers_index">
                        WP Registration
                    </a>
                <?php } else { ?>
                    <a class="tnp-flow-item compact inactive" target="_blank" href="https://www.thenewsletterplugin.com/documentation/addons/extended-features/wpusers-extension/">
                        WP Registration
                    </a>
                <?php } ?>
                
                <?php if (class_exists('NewsletterWoocommerce')) { ?>
                    <a class="tnp-flow-item compact" target="_blank" href="?page=newsletter_woocommerce_index">
                        Woocommerce
                    </a>
                <?php } else { ?>
                    <a class="tnp-flow-item compact inactive" target="_blank" href="https://www.thenewsletterplugin.com/woocommerce">
                        Woocommerce
                    </a>
                <?php } ?>

            </div>

            <div class="tnp-flow-arrow">
                <i class="fas fa-arrow-down"></i>
            </div>

            <div>
                <a class="tnp-flow-item" target="_blank" href="?page=newsletter_subscription_antibot">
                    <i class="fas fa-filter"></i>
                    Antispam
                </a>
                <!--
                <div class="tnp-flow-item" onclick="document.getElementById('antispam').style.display = 'flex'">
                    Antispam filter
                </div>
                -->
            </div>
            <div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('validation').style.display = 'flex'">
                    <i class="fas fa-database"></i>
                    Data collection and validation
                </div>
            </div>
            <div>
                <div class="tnp-flow-item" onclick="document.getElementById('optin').style.display = 'flex'">
                    <i class="fas fa-check-double"></i>
                    Opt-in mode
                    <!--(<?php echo empty($controls->data['noconfirmation']) ? 'double' : 'single' ?>)-->
                </div>
            </div>
            <div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('activation').style.display = 'flex'">
                    <i class="fas fa-comment"></i>
                    Activation message
                </div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('activation-email').style.display = 'flex'">
                    <i class="fas fa-envelope"></i>
                    Activation email
                </div>
            </div>
            <div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('welcome').style.display = 'flex'">
                    <i class="fas fa-comment"></i>
                    Welcome message
                </div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('welcome-email').style.display = 'flex'">
                    <i class="fas fa-envelope"></i>
                    Welcome email
                </div>
                <div class="tnp-flow-item wide" onclick="document.getElementById('notification').style.display = 'flex'">
                    <i class="fas fa-envelope"></i>
                    Admin notification
                </div>
            </div>
            <div>
                <div class="tnp-flow-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
            <div>


                <?php if (class_exists('NewsletterAutomated')) { ?>
                    <a href="?page=newsletter_automated_index" target="_blank" class="tnp-flow-item wide">
                        <i class="fas fa-mail-bulk"></i>
                        Automated newsletters
                    </a>
                <?php } else { ?>
                    <a href="https://www.thenewsletterplugin.com/automated" target="_blank" class="tnp-flow-item inactive wide">
                        <i class="fas fa-mail-bulk"></i>
                        Automated newsletters
                    </a>
                <?php } ?>

                <a href="?page=newsletter_emails_index" target="_blank" class="tnp-flow-item wide">
                    <i class="fas fa-mail-bulk"></i>
                    Newsletters
                </a>

                <?php if (class_exists('NewsletterAutoresponder')) { ?>
                    <a href="?page=newsletter_autoresponder_index" target="_blank" class="tnp-flow-item wide">
                        <i class="fas fa-mail-bulk"></i>
                        Autoresponder & Followup
                    </a>
                <?php } else { ?>
                    <a href="https://www.thenewsletterplugin.com/autoresponder" target="_blank" class="tnp-flow-item inactive wide">
                        <i class="fas fa-mail-bulk"></i>
                        Autoresponder & Followup
                    </a>
                <?php } ?>

            </div>
            <div>
                <div class="tnp-flow-arrow">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
            <div>
                <a href="?page=newsletter_profile_index" target="_blank" class="tnp-flow-item wide">
                    <i class="fas fa-user"></i>
                    Subscriber profile
                </a>
                <a href="?page=newsletter_unsubscription_indexnew" target="_blank" class="tnp-flow-item wide">
                    <i class="fas fa-trash"></i>
                    Opt-out
                </a>
            </div>
        </div>




        <form method="post" action="" id="tnp-form">
            <?php $controls->init(); ?>

            <div class="tnp-flow-fieldset" id="subscriptionpage">
                <div>
                    <section>

                        <p><a href="<?php echo Newsletter::instance()->get_newsletter_page_url() ?>">Open your current subscription page</a>.
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Subscription page', 'newsletter') ?><br><?php echo $controls->help('https://www.thenewsletterplugin.com/documentation/subscription/subscription/') ?></th>
                                <td>
                                    <?php $controls->wp_editor('subscription_text'); ?>
                                </td>
                            </tr>

                        </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="tnp-wpusers">
                <div>
                    <section>
                        <p>
                            WP stadard signup can be connected to the Newsletter 
                            plugin using <a href="https://www.thenewsletterplugin.com/documentation/addons/extended-features/wpusers-extension/" target="_blank">this addon</a>.    
                        </p>
                    </section>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="antispam">
                <div>
                    <section>
                        <p><a href="?page=newsletter_subscription_antibot">Configure the antispam options</a>.</p>
                    </section>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="optin">
                <div>
                    <section>
                        <p>Subscribers should confirm their email address receiving an activation message or not?</p>
                        <table class="form-table">

                            <tr>
                                <th><?php $controls->field_label(__('Opt In', 'newsletter'), '/documentation/subscription/subscription/') ?></th>
                                <td>
                                    <?php $controls->select('noconfirmation', array(0 => __('Double Opt In', 'newsletter'), 1 => __('Single Opt In', 'newsletter'))); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php $controls->field_label(__('Override Opt In', 'newsletter'), '/documentation/subscription/subscription/#advanced') ?></th>
                                <td>
                                    <?php $controls->yesno('optin_override'); ?>
                                    <p class="description">Let subscription sources to ovverride the opt-in.</p>
                                </td>
                            </tr>
                        </table>
                    </section>
                    <footer>

                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div id="welcome" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <p>
                            <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscription#welcome') ?>
                        </p>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Welcome message', 'newsletter') ?></th>
                                <td>
                                    <?php if ($languages) { ?>

                                        <div class="tnp-tabs">
                                            <ul>
                                                <li><a href="#tabs-a">Default</a></li>
                                                <?php foreach ($languages as $key => $value) { ?>
                                                    <li><a href="#tabs-a-<?php echo $key ?>"><?php echo esc_html($value) ?></a></li>
                                                <?php } ?>
                                            </ul>

                                            <div id="tabs-a">
                                                <?php $controls->wp_editor('confirmed_text'); ?>
                                            </div>
                                            <?php foreach ($languages as $key => $value) { ?>
                                                <div id="tabs-a-<?php echo $key ?>">
                                                    <?php $controls->wp_editor($key . '_confirmed_text'); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <?php $controls->wp_editor('confirmed_text'); ?>
                                    <?php } ?>


                                </td>
                            </tr>

                            <tr>
                                <th><?php _e('Custom URL', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->text('confirmed_url', 70, 'https://...'); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>
                                    <?php $controls->label(__('Conversion tracking code', 'newsletter'), 'https://www.thenewsletterplugin.com/documentation/subscription#conversion') ?>
                                </th>
                                <td>
                                    <?php $controls->textarea('confirmed_tracking'); ?>
                                </td>
                            </tr>



                        </table>
                    </section>
                    <footer>

                        <?php $controls->button_save() ?>

                    </footer>
                </div>
            </div>

            <div id="welcome-email" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <table class="form-table">
                            <!-- WELCOME/CONFIRMED EMAIL -->
                            <tr>
                                <th>
                                    <?php _e('Welcome email', 'newsletter') ?>
                                </th>
                                <td>
                                    <?php if ($languages) { ?>

                                        <div class="tnp-tabs">
                                            <ul>
                                                <li><a href="#tabs-b">Default</a></li>
                                                <?php foreach ($languages as $key => $value) { ?>
                                                    <li><a href="#tabs-b-<?php echo $key ?>"><?php echo esc_html($value) ?></a></li>
                                                <?php } ?>
                                            </ul>

                                            <div id="tabs-b">
                                                <?php $controls->email('confirmed', 'wordpress', true); ?>
                                            </div>
                                            <?php foreach ($languages as $key => $value) { ?>
                                                <div id="tabs-b-<?php echo $key ?>">
                                                    <?php $controls->email($key . '_confirmed', 'wordpress', false); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <?php $controls->email('confirmed', 'wordpress'); ?>
                                    <?php } ?>

                                    <br>

                                </td>
                            </tr>

                        </table>
                    </section>
                    <footer>
                        <?php $controls->button('test-confirmed', 'Send a test'); ?>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>
            <div id="validation" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Repeated subscriptions', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->select('multiple', array('0' => __('Not allowed', 'newsletter'), '1' => __('Allowed', 'newsletter'))); ?> 
                                </td>
                            </tr>
                        </table>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Error message', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->wp_editor('error_text'); ?>
                                </td>
                            </tr>
                        </table>
                    </section>
                    <footer>

                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>


            <div id="notification" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <table class="form-table">



                            <tr>
                                <th><?php _e('Notifications', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->yesno('notify'); ?>
                                    <?php $controls->text_email('notify_email'); ?>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>


            <div id="activation" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <p><?php _e('Only for double opt-in mode.', 'newsletter') ?></p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/documentation/subscription#activation') ?>

                        <table class="form-table">
                            <tr>
                                <th><?php _e('Activation message', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->wp_editor_multilanguage('confirmation_text', null, $languages); ?>

                                </td>
                            </tr>

                            <tr>
                                <th><?php _e('Alternative activation page', 'newsletter'); ?></th>
                                <td>
                                    <?php $controls->text('confirmation_url', 70, 'https://...'); ?>
                                </td>
                            </tr>



                        </table>
                    </section>
                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div id="activation-email" class="tnp-flow-fieldset">
                <div>
                    <section>
                        <table class="form-table">
                            <!-- CONFIRMATION EMAIL -->
                            <tr>
<!--                                <th><?php _e('Activation email', 'newsletter') ?></th>-->
                                <td>
                                    <?php $controls->email('confirmation', 'wordpress'); ?>
                                </td>
                            </tr>
                        </table>
                    </section>
                    <footer>
                        <?php $controls->button('test-confirmation', 'Send a test'); ?>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>



        </form>
        <!--
        <p><a href="?page=newsletter_subscription_options">Use the classic configuration panel</a></p>
        -->

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
