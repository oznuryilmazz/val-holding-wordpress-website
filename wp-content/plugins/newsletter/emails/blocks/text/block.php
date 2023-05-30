<?php
/*
 * Name: Text
 * Section: content
 * Description: Free text block
 *
 */

/* @var $options array */

$default_options = array(
    'html'=>'<p style="text-align: left; margin: 0; font-family: ' . $composer['text_font_family'] . '; font-size: ' . $composer['text_font_size'] . '">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vitae sodales nulla, nec blandit velit. Morbi feugiat imperdiet augue, vel mattis augue sagittis rutrum. Sed.</p>',
    'font_family'=>'',
    'font_size'=>'',
    'font_color'=>'',
    'block_padding_left'=>15,
    'block_padding_right'=>15,
    'block_padding_top' => 20,
    'block_padding_bottom' => 20,
    'block_background'=>''
);

$options = array_merge($default_options, $options);

$text_style = TNP_Composer::get_style($options, '', $composer, 'text');

?>
<style>
    .text {
        <?php echo $text_style->echo_css() ?>
        line-height: 1.5;
    }
</style>
<table width="100%" style="width: 100%!important" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="100%" valign="top" align="left" class="text" inline-class="text">
            <?php echo $options['html'] ?>
        </td>
    </tr>
</table>

