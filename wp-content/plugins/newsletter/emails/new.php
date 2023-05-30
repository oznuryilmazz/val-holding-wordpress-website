<?php
/* @var $this NewsletterEmails */
require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

$theme_id = $_GET['id'];

if ($theme_id === 'rawhtml' && check_admin_referer('newsletter-new')) {
    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter');
    $email['track'] = Newsletter::instance()->options['track'];
    $email['token'] = $this->get_token();
    $email['type'] = 'message';
    $email['send_on'] = time();
    $email['editor'] = NewsletterEmails::EDITOR_HTML;
    $email['message'] = "<!DOCTYPE html>\n<html>\n<head>\n<title>Your email title</title>\n</head>\n<body>\n</body>\n</html>";
    $email = Newsletter::instance()->save_email($email);

    $controls->js_redirect($this->get_editor_url($email->id, $email->editor));
    return;
}


$theme = $this->themes->get_theme($theme_id);

// Should never happen
if (!$theme) {
    die('Invalid theme');
}

if (!file_exists($theme['dir'] . '/theme-options.php') && check_admin_referer('newsletter-new')) {
    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter');
    $email['track'] = Newsletter::instance()->options['track'];
    $email['token'] = $this->get_token();
    $email['type'] = 'message';
    $email['send_on'] = time();
    $email['editor'] = NewsletterEmails::EDITOR_TINYMCE;

    $theme_options = $this->themes->get_options($controls->data['theme']);
    $theme_url = $theme['url'];
    $theme_subject = '';

    ob_start();
    include $theme['dir'] . '/theme.php';
    $email['message'] = ob_get_clean();

    if (!empty($theme_subject)) {
        $email['subject'] = $theme_subject;
    }

    if (file_exists($theme['dir'] . '/theme-text.php')) {
        ob_start();
        include $theme['dir'] . '/theme-text.php';
        $email['message_text'] = ob_get_clean();
    } else {
        $email['message_text'] = 'You need a modern email client to read this email. Read it online: {email_url}.';
    }
    $email = Newsletter::instance()->save_email($email);

    $controls->js_redirect($this->get_editor_url($email->id, $email->editor));
    return;
}

if ($controls->is_action('refresh')) {
    $this->themes->save_options($theme_id, $controls->data);
}

if ($controls->is_action('create')) {

    $this->themes->save_options($theme_id, $controls->data);

    $email = array();
    $email['status'] = 'new';
    $email['subject'] = __('Here the email subject', 'newsletter');
    $email['track'] = Newsletter::instance()->options['track'];
    $email['message_text'] = '';
    $email['type'] = 'message';
    $email['send_on'] = time();
    $email['editor'] = NewsletterEmails::EDITOR_TINYMCE;

    $theme_options = $this->themes->get_options($theme_id);

    $theme_url = $theme['url'];
    $theme_subject = '';

    ob_start();
    include $theme['dir'] . '/theme.php';
    $email['message'] = ob_get_clean();

    if (!empty($theme_subject)) {
        $email['subject'] = $theme_subject;
    }

    if (is_file($theme['dir'] . '/theme-text.php')) {
        ob_start();
        include $theme['dir'] . '/theme-text.php';
        $email['message_text'] = ob_get_clean();
    }

    $email = $this->save_email($email);
    $controls->js_redirect($this->get_editor_url($email->id, $email->editor));
    return;
} else {
    $controls->data = $this->themes->get_options($theme_id);
    $controls->data['id'] = $theme_id;
}
?>
<style>
#tnp-body .tnp-emails-theme-options {
    background-color: #fff;
    padding: 10px;
    margin-top: 14px;
}

#tnp-body .tnp-emails-theme-options table.form-table {
    margin: 0;
}

#tnp-body .tnp-emails-theme-options h3 {
    color: #000;
}
</style>

<div class="wrap tnp-emails tnp-emails-new" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Create a newsletter', 'newsletter') ?>
            <a class="tnp-btn-h1" href="<?php echo NewsletterEmails::instance()->get_admin_page_url('theme'); ?>"><?php _e('Back to newsletter themes', 'newsletter') ?></a>
        </h2>
        <br>
        <p>Theme options are saved for next time you'll use this theme.</p>

    </div>

    <div id="tnp-body" class="tnp-body-lite"> 

        <form method="post" action="">
            <?php $controls->init(); ?>
            <?php $controls->hidden('id'); ?>
            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                        <div style="float: right; margin-left: 15px;"><?php $controls->button_primary('refresh', __('Refresh the preview', 'newsletter')); ?></div>

                    </td>
                    <td style="text-align: left; vertical-align: top; border-bottom: 1px solid #ddd; padding-bottom: 10px">
                        <div style="float: right"><?php $controls->button_primary('create', 'Proceed to edit &raquo;', 'this.form.action=\'' . home_url('/', is_ssl() ? 'https' : 'http') . '?na=emails-create\';this.form.submit()'); ?></div>
                        <img style="position: relative; left: 5px; top: 10px;"src="<?php echo plugins_url('newsletter') ?>/emails/images/arrow.png" height="35">
                    </td>
                </tr>
                <tr>
                    <td style="width: 500px; vertical-align: top;">
                        <div class="tnp-emails-theme-options">
                            <?php @include $theme['dir'] . '/theme-options.php'; ?>
                        </div>
                    </td>
                    <td style="vertical-align: top; padding-top: 15px; padding-left: 15px">
                        <iframe src="<?php echo wp_nonce_url(home_url('/', is_ssl() ? 'https' : 'http') . '?na=emails-preview&id=' . urlencode($theme_id) . '&ts=' . time(), 'view'); ?>" height="700" style="width: 100%; border: 1px solid #ccc"></iframe>
                    </td>
                </tr>
            </table>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>