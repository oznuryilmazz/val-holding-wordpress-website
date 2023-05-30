<?php
/* @var $this NewsletterUnsubscription */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$current_language = $this->get_current_language();

$is_all_languages = $this->is_all_languages();

$controls->add_language_warning();

if (!$controls->is_action()) {
    $controls->data = $this->get_options('', $current_language);
} else {
    if ($controls->is_action('save')) {
        $this->save_options($controls->data, '', null, $current_language);
        $controls->data = $this->get_options('', $current_language);
        $controls->add_message_saved();
    }

    if ($controls->is_action('reset')) {
        // On reset we ignore the current language
        $controls->data = $this->reset_options();
    }
}
?>
<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('/cancellation') ?>
        <h2><?php _e('Unsubscribe', 'newsletter') ?></h2>
    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div class="tnp-tabs">

                <ul>
                    <li><a href="#tabs-cancellation"><?php _e('Unsubscribe', 'newsletter') ?></a></li>
                    <li><a href="#tabs-goodbye"><?php _e('Goodbye', 'newsletter') ?></a></li>
                    <li><a href="#tabs-reactivation"><?php _e('Reactivation', 'newsletter') ?></a></li>
                    <li><a href="#tabs-advanced"><?php _e('Advanced', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-cancellation">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Opt-out message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('unsubscribe_text', array('editor_height' => 250)); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('On error', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('error_text', array('editor_height' => 150)); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tabs-goodbye">
                    <table class="form-table">


                        <tr>
                            <th><?php _e('Goodbye message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('unsubscribed_text', array('editor_height' => 250)); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Goodbye email', 'newsletter') ?></th>
                            <td>
                                <?php $controls->email('unsubscribed', 'wordpress', $is_all_languages, array('editor_height' => 250)); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tabs-reactivation">
                    <table class="form-table">
                        <tr>
                            <th><?php _e('Reactivated message', 'newsletter') ?></th>
                            <td>
                                <?php $controls->wp_editor('reactivated_text', array('editor_height' => 250)); ?>
                                <p class="description">
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tabs-advanced">
                    <?php if ($is_all_languages) { ?>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Notifications', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->yesno('notify'); ?>
                                    <?php $controls->text_email('notify_email'); ?>
                                </td>
                            </tr>
                        </table>
                        <h3>List-Unsubscribe header</h3>
                        <table class="form-table">
                            <tr>
                                <th>
                                    <?php _e('Disable unsubscribe headers', 'newsletter') ?>
                                    <?php $controls->field_help('/subscribers-and-management/cancellation/#list-unsubscribe') ?>
                                </th>
                                <td>
                                    <?php $controls->yesno('disable_unsubscribe_headers'); ?>

                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Cancellation requests via email', 'newsletter') ?>
                                    <?php $controls->field_help('/subscribers-and-management/cancellation/#list-unsubscribe') ?>
                                </th>
                                <td>
                                    <?php $controls->text_email('list_unsubscribe_mailto_header'); ?>
                                    <span class="description">
                                        <i class="fas fa-exclamation-triangle"></i> Please, read carefully the documentation page
                                    </span>
                                </td>
                            </tr>
                            
                        </table>
                    <?php } else { ?>

                        <?php $controls->switch_to_all_languages_notice(); ?>

                    <?php } ?>
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
