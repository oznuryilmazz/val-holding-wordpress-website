<?php
$text_style = TNP_Composer::get_text_style($options, '', $composer);
if ($media) {
    $image_width = (int) (600 - $options['block_padding_left'] - $options['block_padding_right']) / 2;
    if ($options['logo_width']) {
        $image_width = min($options['logo_width'], $image_width);
    }
    $media->set_width($image_width);
}
?>
<style>
    .text {
        <?php $text_style->echo_css(0.9) ?>
        text-decoration: none;
        line-height: normal;
        padding: 10px;
    }

    .title {
        <?php $text_style->echo_css(1.2) ?>
        text-decoration: none;
        line-height: normal;
    }

    .logo {
        <?php $text_style->echo_css() ?>
        line-height: normal !important;
    }
</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0; border-collapse: collapse;">
    <tr>
        <td align="center" width="50%" inline-class="logo">
            <?php if ($media) { ?>
                <?php echo TNP_Composer::image($media) ?>
            <?php } else { ?>
                [logo]
            <?php } ?>
        </td>
        <td width="50%" align="center" style="padding: 10px">
            <a href="<?php echo home_url() ?>" target="_blank" inline-class="title">
                <?php echo esc_attr($info['header_title']) ?>
            </a>
            <div inline-class="text"><?php echo esc_attr($info['header_sub']) ?></div>
        </td>
    </tr>
</table>
