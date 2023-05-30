<?php
/*
 * Name: Separator
 * Section: content
 * Description: Separator
 *
 */

/* @var $options array */

$default_options = array(
    'color'=>'#dddddd',
    'height'=>1,
    'block_padding_top'=>20,
    'block_padding_bottom'=>20,
    'block_padding_right'=>20,
    'block_padding_left'=>20,
    'block_background'=>''

);

$options = array_merge($default_options, $options);

?>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="border-bottom: <?php echo $options['height'] ?>px solid <?php echo $options['color'] ?>;"></td>
    </tr>
</table>
