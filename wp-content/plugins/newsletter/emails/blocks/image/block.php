<?php
/*
 * Name: Single image
 * Section: content
 * Description: A single image with link
 */

/* @var $options array */
/* @var $wpdb wpdb */

$defaults = array(
    'image' => '',
    'image-alt' => '',
    'url' => '',
    'width' => 0,
    'align' => 'center',
    'block_background' => '',
    'block_padding_left' => 0,
    'block_padding_right' => 0,
    'block_padding_bottom' => 15,
    'block_padding_top' => 15
);

$options = array_merge($defaults, $options);

if (empty($options['image']['id'])) {
    if (!empty($options['image-url'])) {
        $media = new TNP_Media();
        $media->url = $options['image-url'];
    } else {
        $media = new TNP_Media();
        // A placeholder can be set by a preset and it is kept indefinitely
        if (!empty($options['placeholder'])) {
            $media->url = $options['placeholder'];
            $media->width = $composer['width'];
            $media->height = 250;
        } else {
            $media->url = 'https://source.unsplash.com/1200x500/daily';
            $media->width = $composer['width'];
            $media->height = 250;
        }
    }
} else {
    $media = tnp_resize_2x($options['image']['id'], [$composer['width'], 0]);
    // Should never happen but... it happens
    if (!$media) {
        echo 'The selected media file cannot be processed';
        return;
    }
}

if (!empty($options['width'])) {
    $media->set_width($options['width']);
}
$media->link = $options['url'];
$media->alt = $options['image-alt'];

echo '<table width="100%"><tr><td align="', esc_attr($options['align']), '">';

if ($media->link) {
     echo '<a href="', esc_attr($media->link), '" target="_blank" rel="noopener nofollow" style="display: block; font-size: 0; text-decoration: none; line-height: normal!important">';
} else {
}


echo '<img src="', esc_attr($media->url), '" width="', esc_attr($media->width), '"';
if ($media->height) {
    echo ' height="', esc_attr($media->height), '"';
}
echo ' alt="', esc_attr($media->alt), '"';
// The font size is important for the alt text
echo ' border="0" style="display: block; height: auto; max-width: ', esc_attr($media->width), 'px !important; width: 100%; padding: 0; border: 0; font-size: 12px"';
echo '>';

if ($media->link) {
    echo '</a>';
} else {
}

echo '</td></tr></table>';
?>

