<?php
defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = NewsletterEmails::instance();


if ($controls->is_action('theme')) {
    $controls->merge($module->themes->get_options($controls->data['theme']));
    $module->save_options($controls->data);
}


if ($controls->data == null) {
    $controls->data = $module->get_options();
}

function newsletter_emails_update_options($options)
{
    add_option('newsletter_emails', '', null, 'no');
    update_option('newsletter_emails', $options);
}

function newsletter_emails_update_theme_options($theme, $options)
{
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    add_option('newsletter_emails_' . $theme, '', null, 'no');
    update_option('newsletter_emails_' . $theme, $options);
}

function newsletter_emails_get_options()
{
    $options = get_option('newsletter_emails', array());
    return $options;
}

function newsletter_emails_get_theme_options($theme)
{
    $x = strrpos($theme, '/');
    if ($x !== false) {
        $theme = substr($theme, $x + 1);
    }
    $options = get_option('newsletter_emails_' . $theme, array());
    return $options;
}

$themes = $module->themes->get_all_with_data();
?>
<script>
    function tnp_select_theme(id) {
        var f = document.getElementById('newsletter-form');
        f.act.value = 'theme';
        f.elements['options[theme]'].value = id;
        f.submit();
    }
</script>
<div class="wrap tnp-emails tnp-emails-theme" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Legacy themes', 'newsletter') ?></h2>

        <?php echo $controls->page_help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-themes') ?>

    </div>
    <div id="tnp-body">

        <form method="post" id="newsletter-form" action="<?php echo $module->get_admin_page_url('new'); ?>">
            <?php $controls->init(); ?>
            <?php $controls->hidden('theme'); ?>

            <?php foreach ($themes as $id => $data) { ?>
                <div class="tnp-theme-preview">
                    <p><?php echo esc_html($data['name']) ?></p>
                    <a href="<?php echo wp_nonce_url('admin.php?page=newsletter_emails_new&id=' . urlencode($id), 'newsletter-new') ?>"
                       style="margin-right: 20px; margin-bottom: 20px">
                        <img src="<?php echo esc_attr($data['screenshot']) ?>">
                    </a>
                </div>
            <?php } ?>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
