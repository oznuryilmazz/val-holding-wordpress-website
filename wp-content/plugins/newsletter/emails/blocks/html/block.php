<?php
/*
 * Name: Html
 * Section: content
 * Description: Free HTML block
 *
 */

/* @var $options array */
/* @var $wpdb wpdb */

$default_options = array(
    'html'=>'<p style="font-size: 16px; font-family: Helvetica, Arial, sans-serif">This is a piece of nice html code. You can use any tag, but be aware that email readers do not render everything.<p>',
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_padding_top' => 20,
    'block_padding_bottom' => 20,
    'block_background' => '',
    'font_family' => 'Helvetica, Arial, sans-serif',
    'font_size' => 16,
    'font_color' => '#000'
);

$options = array_merge($default_options, $options);

?>
<style>
    .html-td {
        font-family: <?php echo $options['font_family']?>;
        font-size: <?php echo $options['font_size']?>px;
        color: <?php echo $options['font_color']?>;
    }
</style>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="center" inline-class="html-td" class="html-td-global">
            <?php echo $options['html'] ?>
        </td>
    </tr>
</table>

