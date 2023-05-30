<?php
/*
 * Name: Preheader
 * Section: header
 * Description: Preheader
 *
 */

/* @var $options array */
/* @var $wpdb wpdb */

$defaults = array(
    'view' => 'View online',
    'text' => 'Few words summary',
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'font_weight' => '',
    'block_padding_left'=>15,
    'block_padding_right'=>15,
    'block_padding_bottom'=>15,
    'block_padding_top'=>15,
    'block_background' => '',
);

$options = array_merge($defaults, $options);

$text_font_family = empty( $options['font_family'] ) ? $global_text_font_family : $options['font_family'];
$text_font_size   = empty( $options['font_size'] ) ? round($global_text_font_size*0.9) : $options['font_size'];
$text_font_color  = empty( $options['font_color'] ) ? $global_text_font_color : $options['font_color'];
$text_font_weight = empty( $options['font_weight'] ) ? $global_text_font_weight : $options['font_weight'];

?>
<style>
    .td {
        font-family: <?php echo $text_font_family ?>;
        font-size: <?php echo $text_font_size ?>px;
        font-weight: <?php echo $text_font_weight ?>;
        color: <?php echo $text_font_color ?>;
        line-height: normal !important;
    }
    .link {
        font-family: <?php echo $text_font_family ?>;
        font-size: <?php echo $text_font_size ?>px;
        font-weight: <?php echo $text_font_weight ?>;
        color: <?php echo $text_font_color ?>;
        text-decoration: none;
    }
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="responsive">
    <tr>
        <td inline-class="td" width="50%" valign="top" align="left">
            <?php echo $options['text'] ?>
        </td>
        <td inline-class="td" width="50%" valign="top" align="right">
            <a href="{email_url}" target="_blank" rel="noopener" inline-class="link"><?php echo $options['view'] ?></a>
        </td>
    </tr>
</table>

