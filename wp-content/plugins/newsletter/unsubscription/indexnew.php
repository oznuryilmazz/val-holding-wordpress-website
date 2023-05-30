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
</script>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Opt-out', 'newsletter') ?> <?php $controls->title_help('https://www.thenewsletterplugin.com/documentation/cancellation') ?></h2>

        <p>
            How the subscriber can opt-out and how you interact with him/her.
        </p>
    </div>

    <div id="tnp-body">


        <form method="post" action="">
            <?php $controls->init(); ?>
            <?php $controls->select('optout', ['' => 'Single step (recommended)', '1' => 'Double step']) ?>
            <?php $controls->button_save() ?>

            <br><br>

            <div class="tnp-flow tnp-flow-row">

                <div class="tnp-flow-item wide" data-fieldset-id="cancellationlink">
                    Cancellation link
                </div>

                <div class="tnp-flow-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>

                <?php if (empty($this->options['optout']) || $this->options['optout'] === 'single') { ?>
                <?php } else { ?>
                    <div class="tnp-flow-item wide" data-fieldset-id="cancellationconfirm">
                        Confirm message
                    </div>
                    <div class="tnp-flow-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                <?php } ?>
                <div style="flex-direction: column">
                    <div class="tnp-flow-item wide" data-fieldset-id="goodbyemessage">
                        Goodbye message
                    </div>
                    <div class="tnp-flow-item wide" data-fieldset-id="goodbyeemail">
                        Goodbye email
                    </div>
                    <div class="tnp-flow-item wide" data-fieldset-id="adminnotification">
                        Admin Notification
                    </div>
                </div>
                <div class="tnp-flow-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="tnp-flow-item wide" data-fieldset-id="reactivation">
                    Reactivated message
                </div>
            </div>

            <div class="tnp-flow tnp-flow-row">

                <div class="tnp-flow-item wide" data-fieldset-id="cancellationreader">
                    Cancellation from mail reader
                </div>
                <div class="tnp-flow-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="tnp-flow-item wide" data-fieldset-id="cancellationmailbox">
                    Options
                </div>
            </div>


            <div class="tnp-flow-fieldset" id="cancellationlink">
                <div>
                    <section>
                        <p>
                            Every newsletter contains a cancellation link provided by the {unsubscription_url} tag. It starts
                            the cancellation process that unsubscribe the contact.
                        </p>

                    </section>

                    <footer>
                        <?php //$controls->button_save() ?>
                    </footer>
                </div>
            </div>


            <div class="tnp-flow-fieldset" id="cancellationconfirm">
                <div>
                    <section>

                        <table class="form-table">
                            <tr>
                                <th><?php _e('Cancellation message', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->wp_editor('unsubscribe_text', array('editor_height' => 250)); ?>
                                </td>
                            </tr>

                        </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="goodbyemessage">
                <div>
                    <section>

                        <table class="form-table">
                            <table class="form-table">


                                <tr>
                                    <th><?php _e('Goodbye message', 'newsletter') ?></th>
                                    <td>
                                        <?php $controls->wp_editor('unsubscribed_text', array('editor_height' => 250)); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th><?php _e('On error', 'newsletter') ?></th>
                                    <td>
                                        <?php $controls->wp_editor('error_text', array('editor_height' => 150)); ?>
                                    </td>
                                </tr>
                            </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>


            <div class="tnp-flow-fieldset" id="goodbyeemail">
                <div>
                    <section>

                        <table class="form-table">
                            <table class="form-table">




                                <tr>
                                    <th><?php _e('Goodbye email', 'newsletter') ?></th>
                                    <td>
                                        <?php $controls->email('unsubscribed', 'wordpress', $is_all_languages, array('editor_height' => 250)); ?>
                                    </td>
                                </tr>


                            </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="adminnotification">
                <div>
                    <section>
                        <table class="form-table">       
                            <tr>
                                <th><?php _e('Notify admin on cancellation', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->yesno('notify_admin_on_unsubscription'); ?>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="reactivation">
                <div>
                    <section>
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
                    </section>
                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>


            <div class="tnp-flow-fieldset" id="cancellationreader">
                <div>
                    <section>
                        Some email reader (eg. Gmail) can show a button to cancel the subscription when a newsletter is received.
                        The email reader can interact directly with Newsletter or send a request by email or simply ignore this option.
                        <br>
                        <a href="https://www.thenewsletterplugin.com/documentation/subscribers-and-management/cancellation/#list-unsubscribe" target="_blank">Read more</a>.
                    </section>
                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>

            <div class="tnp-flow-fieldset" id="cancellationmailbox">
                <div>
                    <section>
                        <table class="form-table">
                            <tr>
                                <th>
                                    <?php $controls->label(__('Disable', 'newsletter'), '/subscribers-and-management/cancellation/#list-unsubscribe') ?>
                                </th>
                                <td>
                                    <?php $controls->yesno('disable_unsubscribe_headers'); ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Cancellation requests via email', 'newsletter') ?></th>
                                <td>
                                    <?php $controls->text_email('list_unsubscribe_mailto_header'); ?>
                                    <p class="description">
                                        <i class="fas fa-exclamation-triangle"></i> <a href="https://www.thenewsletterplugin.com/documentation/subscribers-and-management/cancellation/#list-unsubscribe" target="_blank"><?php _e('Read more', 'newsletter') ?></a>
                                    </p>
                                </td>
                            </tr>


                        </table>
                    </section>
                    <footer>
                        <?php $controls->button_save() ?>
                    </footer>
                </div>
            </div>



        </form>
        <p>
            <a href="?page=newsletter_unsubscription_index">Switch to the previous interface</a></p>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
