<?php
/*
 * Name: Heading
 * Section: content
 * Description: Section title
 */

$default_options = array(
    'text' => 'An Awesome Title',
    'align' => 'center',
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'font_weight' => '',
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_padding_bottom' => 15,
    'block_padding_top' => 15,
    'block_background' => ''
);
$options = array_merge($default_options, $options);

$title_style = TNP_Composer::get_title_style($options, '', $composer);

?>

<style>
    .title {
        <?php $title_style->echo_css()?>
        padding: 0;
        line-height: normal !important;
        letter-spacing: normal;
    }
</style>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($options['align']) ?>" valign="middle" inline-class="title">
            <?php echo $options['text'] ?>
        </td>
    </tr>
</table>