<?php
/*
 * Name: Call To Action
 * Section: content
 * Description: Call to action button
 */

$default_options = array(
    'button_label' => 'Call to action',
    'button_url' => home_url(),
    'button_font_family' => '',
    'button_font_size' => '',
    'button_font_weight' => '',
    'button_font_color' => '',
    'button_background' => '',
    'align' => 'center',
    'block_background' => '',
    'button_width' => '200',
    'button_align' => 'center',
    'block_padding_top' => 20,
    'block_padding_bottom' => 20,
    'schema' => ''
);

// Migration from old option names
if (!empty($options['font_color']))
    $options['button_font_color'] = $options['font_color'];
if (!empty($options['url']))
    $options['button_url'] = $options['url'];
if (!empty($options['font_family']))
    $options['button_font_family'] = $options['font_family'];
if (!empty($options['font_size']))
    $options['button_font_size'] = $options['font_size'];
if (!empty($options['font_weight']))
    $options['button_font_weight'] = $options['font_weight'];
if (!empty($options['background']))
    $options['button_background'] = $options['background'];
if (!empty($options['text']))
    $options['button_label'] = $options['text'];
if (!empty($options['width']))
    $options['button_width'] = $options['width'];

unset($options['font_color']);
unset($options['url']);
unset($options['font_family']);
unset($options['font_size']);
unset($options['font_weight']);
unset($options['background']);
unset($options['text']);
unset($options['width']);

$options = array_merge($default_options, $options);

if (!empty($options['schema'])) {
    if ($options['schema'] === 'dark') {
        $options['block_background'] = '#000000';
        $options['button_font_color'] = '#ffffff';
        $options['button_background'] = '#96969C';
    }

    if ($options['schema'] === 'bright') {
        $options['block_background'] = '#ffffff';
        $options['button_font_color'] = '#ffffff';
        $options['button_background'] = '#256F9C';
    }
}

// Cloned since we need to set the general options
$button_options = $options;

$button_options['button_font_family'] = empty($options['button_font_family']) ? $global_button_font_family : $options['button_font_family'];
$button_options['button_font_size'] = empty($options['button_font_size']) ? $global_button_font_size : $options['button_font_size'];
$button_options['button_font_color'] = empty($options['button_font_color']) ? $global_button_font_color : $options['button_font_color'];
$button_options['button_font_weight'] = empty($options['button_font_weight']) ? $global_button_font_weight : $options['button_font_weight'];
$button_options['button_background'] = empty($options['button_background']) ? $global_button_background_color : $options['button_background'];
?>


<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0; border-collapse: collapse;">
    <tr>
        <td align="<?php echo esc_attr($options['align'])?>">
            <?php echo TNP_Composer::button($button_options); ?>
        </td>
    </tr>
</table>

<div itemscope="" itemtype="http://schema.org/EmailMessage">
    <div itemprop="potentialAction" itemscope="" itemtype="http://schema.org/ViewAction">
        <meta itemprop="url" content="<?php echo esc_attr($options['button_url']) ?>" />
        <meta itemprop="name" content="<?php echo esc_attr($options['button_label']) ?>" />
    </div>
    <meta itemprop="description" content="<?php echo esc_attr($options['button_label']) ?>" />
</div>
