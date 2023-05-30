<?php

/*
 * Name: Header
 * Section: header
 * Description: Default header with company info
 */

$default_options = array(
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'font_weight' => '',
    'logo_width' => 120,
    'block_padding_top' => 15,
    'block_padding_bottom' => 15,
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_background' => '',
    'layout' => ''
);
$options = array_merge($default_options, $options);

if (empty($info['header_logo']['id'])) {
    $media = false;
} else {
    $media = tnp_get_media($info['header_logo']['id'], 'large');
    if ($media) {
        $media->alt = $info['header_title'];
        $media->link = home_url();
    }
}

$empty = !$media && empty($info['header_sub']) && empty($info['header_title']);

if ($empty) {
    echo '<p>Please, set your company info.</p>';
} elseif ($options['layout'] === 'logo') {
    include __DIR__ . '/layout-logo.php';
    return;
} elseif ($options['layout'] === 'titlemotto') {
    include __DIR__ . '/layout-titlemotto.php';
    return;
} else {
    include __DIR__ . '/layout-default.php';
    return;
}
?>

