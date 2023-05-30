<?php
/*
 * Name: Footer
 * Section: footer
 * Description: View online ad profile links
 */

$default_options = array(
    'view' => __('View online', 'newsletter'),
    'view_enabled' => 1,
    'profile' => __('Manage your subscription', 'newsletter'),
    'profile_enabled' => 1,
    'unsubscribe' => __('Unsubscribe', 'newsletter'),
    'unsubscribe_enabled' => 1,
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'font_weight' => '',
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_padding_bottom' => 15,
    'block_padding_top' => 15,
    'block_background' => '',
    'url' => 'profile'
);

// Migration code
if (!isset($options['profile_enabled']) && isset($options['url'])) {
    if ($options['url'] === 'profile') {
        $options['profile_enabled'] = 1;
        $options['unsubscribe_enabled'] = 0;
    } else {
        $options['profile_enabled'] = 1;
        $options['unsubscribe_enabled'] = 0;
    }
}

$options = array_merge($default_options, $options);

$text_style = TNP_Composer::get_text_style($options, '', $composer, ['scale'=>0.8]);

$links = [];
if ($options['unsubscribe_enabled']) {
    $links[] = '<a inline-class="text" href="{unsubscription_url}" target="_blank">' . esc_html($options['unsubscribe']) . '</a>';
}
if ($options['profile_enabled']) {
    $links[] = '<a inline-class="text" href="{profile_url}" target="_blank">' . esc_html($options['profile']) . '</a>';
}
if ($options['view_enabled']) {
    $links[] = '<a inline-class="text" href="{email_url}" target="_blank">' . esc_html($options['view']) . '</a>';
}

?>
<style>
    .text {
        <?php $text_style->echo_css()?>
        text-decoration: none;
        line-height: normal;
    }
</style>

<?php echo implode('<span inline-class="text">&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</span>', $links) ?>

