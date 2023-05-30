<?php
/* @var $this NewsletterSubscription */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$current_language = $this->get_current_language();

$is_all_languages = $this->is_all_languages();

if (!$is_all_languages) {
    $controls->warnings[] = 'You are configuring the language "<strong>' . $current_language . '</strong>". Switch to "all languages" to see every options.';
}

if (!$controls->is_action()) {
    $controls->data = $this->get_options('profile', $current_language);
} else {
    if ($controls->is_action('save')) {
        $this->save_options($controls->data, 'profile', null, $current_language);
        $controls->data = $this->get_options('profile', $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        $controls->data = $this->reset_options('profile');
        $controls->add_message_done();
    }
}

$status = array(0 => __('Private', 'newsletter'), 1 => __('Show on profile page', 'newsletter'), 2 => __('Show on subscription form', 'newsletter'));
$rules = array(0 => __('Optional', 'newsletter'), 1 => __('Required', 'newsletter'));
$extra_type = array('text' => __('Text', 'newsletter'), 'select' => __('List', 'newsletter'));
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscription Form Fields', 'newsletter') ?></h2>

        <p>
        <ul>
            <li>
            <a href="?page=newsletter_subscription_forms"><?php _e('HTML samples and hand coded forms', 'newsletter') ?></a>
            </li>
            <li>
                Customize newsletters with <a href="https://www.thenewsletterplugin.com/documentation/newsletters/newsletter-tags/" target="_blank">subscriber data tags</a>
            </li>
        </p>

    </div>

    <div id="tnp-body">

        <form action="" method="post">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-2"><?php _e('Main profile fields', 'newsletter') ?></a></li>
                    <li><a href="#tabs-3"><?php _e('Extra profile fields', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-2">

                    <p><?php _e('The main subscriber fields. Only the email field is, of course, mandatory.', 'newsletter') ?></p>

                    <table class="form-table">
                        <tr>
                            <th>Email</th>
                            <td>
                                <table class="tnpc-grid">
                                    <tr><th><?php _e('Field label', 'newsletter') ?></th><td><?php $controls->text('email', 50); ?></td></tr>
                                    <tr><th><?php _e('Error message', 'newsletter') ?></th><td><?php $controls->text('email_error', 50); ?></td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table class="form-table">
                        <tr>
                            <th><?php _e('First name', 'newsletter') ?></th>
                            <td>
                                <table class="tnpc-grid">
                                    <tr><th><?php _e('Field label', 'newsletter') ?></th><td><?php $controls->text('name', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                        <tr><th><?php _e('When to show', 'newsletter') ?></th><td><?php $controls->select('name_status', $status); ?></td></tr>
                                        <tr><th><?php _e('Rules', 'newsletter') ?></th><td><?php $controls->select('name_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                </table>
                                <p class="description">
                                    <?php _e('If you want to collect only a generic "name", use only this field and not the last name field.', 'newsletter') ?><br>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Last name', 'newsletter') ?></th>
                            <td>
                                <table class="tnpc-grid">
                                    <tr><th><?php _e('Field label', 'newsletter') ?></th><td><?php $controls->text('surname', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                        <tr><th><?php _e('When to show', 'newsletter') ?></th><td><?php $controls->select('surname_status', $status); ?></td></tr>
                                        <tr><th><?php _e('Rules', 'newsletter') ?></th><td><?php $controls->select('surname_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Gender', 'newsletter') ?></th>
                            <td>
                                <table class="tnpc-grid">
                                    <tr><th><?php _e('Field label', 'newsletter') ?></th><td><?php $controls->text('sex', 50); ?></td></tr>
                                    <?php if ($is_all_languages) { ?>
                                        <tr><th><?php _e('When to show', 'newsletter') ?></th><td><?php $controls->select('sex_status', $status); ?></td></tr>
                                        <tr><th><?php _e('Rules', 'newsletter') ?></th><td><?php $controls->select('sex_rules', $rules); ?></td></tr>
                                    <?php } ?>
                                    <tr>
                                        <th><?php _e('Value labels', 'newsletter') ?></th>
                                        <td>
                                            <?php $controls->text('sex_none', 20, __('not specified', 'newsletter')); ?>
                                            <?php $controls->text('sex_female', 20, __('female', 'newsletter')); ?>
                                            <?php $controls->text('sex_male', 20, __('male', 'newsletter')); ?>
                                        </td>
                                    </tr>
                                </table>
                                
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Salutation', 'newsletter') ?></th>
                            <td>
                                <table class="tnpc-grid">
                                    <tr>
                                        <th><?php _e('Generic', 'newsletter') ?></th>
                                        <td><?php $controls->text('title_none'); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Females', 'newsletter') ?></th>
                                        <td><?php $controls->text('title_female'); ?> (ex. "Mrs")</td>
                                    </tr>
                                    <tr>
                                        <th><?php _e('Males', 'newsletter') ?></th>
                                        <td><?php $controls->text('title_male'); ?> (ex. "Mr")</td>
                                    </tr>
                                </table>
                                <p class="description">
                                    <?php _e('Salutation titles are inserted in emails message when the tag {title} is used. For example "Good morning {title} {surname} {name}".', 'newsletter') ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('"Subscribe" label', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('subscribe', 40); ?>

                                <p class="description">
                                    <?php _e('You can use an image URL', 'newsletter') ?> (http://...).
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Privacy checkbox/notice', 'newsletter') ?></th>
                            <td>
                                <table class="tnpc-grid">
                                    <?php if ($is_all_languages) { ?>
                                        <tr><th><?php _e('Enabled?', 'newsletter') ?></th><td><?php $controls->select('privacy_status', array(0 => __('No', 'newsletter'), 1 => __('Yes', 'newsletter'), 2 => __('Only the notice', 'newsletter'))); ?></td></tr>
                                    <?php } ?>
                                    <tr><th><?php _e('Label', 'newsletter') ?></th><td><?php $controls->text('privacy', 50); ?></td></tr>
                                    <tr>
                                        <th>Privacy URL</th>
                                        <td>
                                            <?php if (!$is_all_languages && !empty($controls->data['privacy_use_wp_url'])) { ?>
                                                <?php _e('The "all language" setting is set to use the WordPress default privacy page. Please translate that page.', 'newsletter') ?>
                                            <?php } else { ?>
                                                <?php if ($is_all_languages) { ?>
                                                    <?php if (function_exists('get_privacy_policy_url') && get_privacy_policy_url()) { ?>
                                                        <?php $controls->checkbox('privacy_use_wp_url', __('Use WordPress privacy URL', 'newsletter')); ?>
                                                        (<a href="<?php echo esc_attr(get_privacy_policy_url()) ?>"><?php echo esc_html(get_privacy_policy_url()) ?></a>)
                                                        <br>OR<br>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if (!$is_all_languages) { ?>
                                                    <?php _e('To use the WordPress privacy page, switch to "all language" and activate it.', 'newsletter') ?><br>
                                                <?php } ?>
                                                <?php $controls->text_url('privacy_url', 50); ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </table>
                                <div class="tnpc-hint">
                                    <?php _e('The privacy acceptance checkbox (required in many Europen countries) forces the subscriber to check it before proceeding. If an URL is specified the label becomes a link.', 'newsletter') ?>
                                </div>
                            </td>
                        </tr>

                    </table>
                </div>


                <div id="tabs-3">
                    <p>
                        <?php _e('Generic textual profile fields that can be collected during the subscription. Field formats can be one line text
                        or selection list. Fields of type "list" must be configured with a set of options, comma separated
                        like: "first option, second option, third option".', 'newsletter') ?>
                    </p>
                    <p>
                        <?php _e('The placeholder works only on HTML 5 compliant browsers.', 'newsletter') ?>
                    </p>

                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Field', 'newsletter') ?></th>
                                <th><?php _e('Name/Label', 'newsletter') ?></th>
                                <th><?php _e('Placeholder', 'newsletter') ?></th>

                                <th><?php _e('When/Where', 'newsletter') ?></th>
                                <th><?php _e('Type', 'newsletter') ?></th>
                                <th><?php _e('Rule', 'newsletter') ?></th>

                                <th><?php _e('List values comma separated', 'newsletter') ?></th>
                            </tr>
                        </thead>
                        <?php for ($i = 1; $i <= NEWSLETTER_PROFILE_MAX; $i++) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php $controls->text('profile_' . $i); ?></td>
                                <td><?php $controls->text('profile_' . $i . '_placeholder'); ?></td>
                                <?php if ($is_all_languages) { ?>
                                    <td><?php $controls->select('profile_' . $i . '_status', $status); ?></td>
                                    <td><?php $controls->select('profile_' . $i . '_type', $extra_type); ?></td>
                                    <td><?php $controls->select('profile_' . $i . '_rules', $rules); ?></td>
                                <?php } else { ?>
                                    <td><?php echo esc_html($status[$controls->get_value('profile_' . $i . '_status')]) ?></td>
                                    <td><?php echo esc_html($extra_type[$controls->get_value('profile_' . $i . '_type')]) ?></td>
                                    <td><?php echo esc_html($rules[$controls->get_value('profile_' . $i . '_rules')]) ?></td>
                                <?php } ?>
                                <td>
                                    <?php $controls->textarea_fixed('profile_' . $i . '_options', '200px', '50px'); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>



            </div>

            <p>
                <?php $controls->button_save(); ?>
                <?php $controls->button_reset(); ?>
            </p>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
