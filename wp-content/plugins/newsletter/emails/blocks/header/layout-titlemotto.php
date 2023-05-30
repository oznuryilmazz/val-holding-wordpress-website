<?php
$title_style = TNP_Composer::get_title_style($options, '-', $composer);
$text_style = TNP_Composer::get_text_style($options, '', $composer);
?>
<style>
    .text {
        <?php $text_style->echo_css() ?>
        text-decoration: none;
        line-height: normal;
        padding: 10px;
    }

    .title {
        <?php $title_style->echo_css(0.9) ?>
        text-decoration: none;
        line-height: normal;
    }

</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0; border-collapse: collapse;">
    <tr>
        <td align="center" width="100%">
            <a href="<?php echo home_url() ?>" target="_blank" inline-class="title">
                <?php echo esc_attr($info['header_title']) ?>
            </a>
        </td>
    </tr>
    <tr>
        <td width="100%" align="center" inline-class="text">
            <?php echo esc_html($info['header_sub']) ?>
        </td>
    </tr>
</table>
