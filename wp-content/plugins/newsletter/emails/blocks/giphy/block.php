<?php
/*
 * Name: Giphy
 * Section: content
 * Description: Add a Giphy image
 *
 */

/* @var $options array */
/* @var $wpdb wpdb */

$defaults = array(
    'block_padding_top'=>15,
    'block_padding_bottom'=>15,
    'block_padding_left'=>0,
    'block_padding_right'=>0,
    'block_background'=>'',
    'giphy_url' => ''
);

$options = array_merge($defaults, $options);

?>
<style>
    .image {
        display: inline-block; 
        max-width: 100%!important; 
        height: auto!important;
        font-size: 0;
    }
</style>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="responsive">
    <tr>
        <td align="center">
            <img src="<?php echo $options['giphy_url'] ?>" inline-class="image">
        </td>
    </tr>
</table>

