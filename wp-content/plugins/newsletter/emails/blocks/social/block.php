<?php
/*
 * Name: Social links
 * Section: footer
 * Description: Link with icons to social profiles
 *
 */

/* @var $options array */

$defaults = array(
    'type' => 1,
    'width' => 32,
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_padding_bottom' => 15,
    'block_padding_top' => 15,
    'block_background' => ''
);
$options = array_merge($defaults, $options);

$type = (int) $options['type'];
$width = (int) $options['width'];
$social_icon_url = plugins_url('newsletter') . '/images/social-' . $type;

$socials = ['facebook', 'twitter', 'pinterest', 'linkedin', 'tumblr', 'youtube', 'soundcloud', 'instagram', 'vimeo', 'telegram', 'vk', 'discord', 'tiktok', 'twitch'];

$valid_socials = [];
foreach ($socials as &$social) {
    if (!empty($block_options[$social . '_url'])) {
        $valid_socials[] = $social;
    }
}

if (!$valid_socials) {
    echo '<p>Configure your social links in the <a href="?page=newsletter_main_info" target="_blank">social configuration section</a></p>';
    return;
}
?>
<style>
    .link {
        line-height: normal;
        text-decoration: none;
    }
</style>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="responsive">
    <tr>
        <td align="center" valign="middle">
            <?php foreach ($valid_socials as &$social) { ?>
                <a href="<?php echo esc_url($block_options[$social . '_url']) ?>" inline-class="link"><img src="<?php echo $social_icon_url ?>/<?php echo $social ?>.png" width="<?php echo $width?>" height="<?php echo $width?>" alt="<?php echo $social ?>"></a>&nbsp;
            <?php } ?>
        </td>
    </tr>
</table>


