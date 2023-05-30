<?php
/*
 * Name: Company Info
 * Section: footer
 * Description: Company Info for Can-Spam act requirements
 */

$default_options = array(
	'font_family'          => '',
	'font_size'            => 14,
	'font_color'           => '',
	'font_weight'          => '',
	'block_padding_top'    => 15,
	'block_padding_bottom' => 15,
	'block_padding_left'   => 15,
	'block_padding_right'  => 15,
	'block_background'     => '',
	'title'                => $info['footer_title'],
	'address'              => $info['footer_contact'],
	'copyright'            => $info['footer_legal'],
);

$options = array_merge($default_options, $options);

$text_font_family = empty( $options['font_family'] ) ? $global_text_font_family : $options['font_family'];
$text_font_size   = empty( $options['font_size'] ) ? $global_text_font_size : $options['font_size'];
$text_font_color  = empty( $options['font_color'] ) ? $global_text_font_color : $options['font_color'];
$text_font_weight = empty( $options['font_weight'] ) ? $global_text_font_weight : $options['font_weight'];

?>

<style>
    .canspam-text {
        padding: 10px;
        text-align: center;
        font-size: <?php echo $text_font_size ?>px;
        font-family: <?php echo $text_font_family ?>;
        font-weight: <?php echo $text_font_weight ?>;
        color: <?php echo $text_font_color?>;
    }
</style>

<div inline-class="canspam-text">
    <strong><?php echo esc_html($options['title']) ?></strong>
    <br>
    <?php echo esc_html($options['address']) ?>
    <br>
    <em><?php echo esc_html($options['copyright']) ?></em>
</div>
