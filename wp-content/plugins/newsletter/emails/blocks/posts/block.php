<?php

/*
 * Name: Last posts
 * Section: content
 * Description: Last posts list with different layouts
 */

/* @var $options array */
/* @var $wpdb wpdb */

$defaults = array(
    'title' => 'Last news',
    'color' => '#999999',
    'font_family' => '',
    'font_size' => '',
    'font_color' => '',
    'title_weight' => '',
    'title_font_family' => '',
    'title_font_size' => '',
    'title_font_color' => '',
    'title_font_weight' => '',
    'max' => 4,
    'categories' => '',
    'tags' => '',
    'layout' => 'one',
    'language' => '',
    'button_label' => __('Read more...', 'newsletter'),
    'button_background' => '',
    'button_font_color' => '',
    'button_font_family' => '',
    'button_font_size' => '',
    'button_font_weight' => '',
    'block_padding_left' => 15,
    'block_padding_right' => 15,
    'block_padding_top' => 15,
    'block_padding_bottom' => 15,
    'block_background' => '',
    'excerpt_length' => 30,
    'excerpt_length_type' => '',
    'post_offset' => 0,
    'automated_include' => 'new',
    'inline_edits' => [],
    'automated_no_contents' => 'No new posts by now!',
    'automated' => '1',
    'show_read_more_button' => true,
);

// Backward compatibility
if (isset($options['automated_required'])) {
    $defaults['automated'] = '1';
}

$options = array_merge($defaults, $options);

$filters = array();

$options['max'] = (int) $options['max'];
if ($options['layout'] == 'two') {
    $options['max'] = (int) floor($options['max'] / 2) * 2;
}

$filters['posts_per_page'] = $options['max'];
$filters['offset'] = max((int) $options['post_offset'], 0);

if (!empty($options['categories'])) {
    $filters['category__in'] = $options['categories'];
}

if (!empty($options['tags'])) {
    $tags = explode(',', $options['tags']);
    // It's ok even as array
    $filters['tag'] = array_unique(array_map('sanitize_title', $tags));
}

if ($context['type'] != 'automated') {
    $posts = Newsletter::instance()->get_posts($filters, $options['language']);
} else {

    if (!empty($options['automated_disabled'])) {
        $posts = Newsletter::instance()->get_posts($filters, $options['language']);
    } else {
        // Can be empty when composing...
        if (!empty($context['last_run'])) {
            $filters['date_query'] = array(
                'after' => gmdate('c', $context['last_run'])
            );
        }

        $posts = Newsletter::instance()->get_posts($filters, $options['language']);
        if (empty($posts)) {
            if ($options['automated'] == '1') {
                $out['stop'] = true;
                return;
            } else if ($options['automated'] == '2') {
                $out['skip'] = true;
                return;
            } else {
                echo '<div inline-class="nocontents">', $options['automated_no_contents'], '</div>';
                return;
            }
        } else {
            if ($options['automated_include'] == 'max') {
                unset($filters['date_query']);
                $posts = Newsletter::instance()->get_posts($filters, $options['language']);
            }
        }
    }
}

if ($posts) {
    $out['subject'] = $posts[0]->post_title;
}

$current_language = Newsletter::instance()->get_current_language();
Newsletter::instance()->switch_language($current_language);

remove_all_filters('excerpt_more');

$image_placeholder_url = plugins_url('newsletter') . '/emails/blocks/posts/images/blank-240x160.png';

$excerpt_length = $options['excerpt_length'];
$excerpt_length_in_chars = $options['excerpt_length_type'] == 'chars';


$show_image = !empty($options['show_image']);
$show_date = !empty($options['show_date']);
$show_author = !empty($options['show_author']);

$title_font_family = empty($options['title_font_family']) ? $global_title_font_family : $options['title_font_family'];
$title_font_size = empty($options['title_font_size']) ? round($global_title_font_size * 0.8) : $options['title_font_size'];
$title_font_color = empty($options['title_font_color']) ? $global_title_font_color : $options['title_font_color'];
$title_font_weight = empty($options['title_font_weight']) ? $global_title_font_weight : $options['title_font_weight'];

$text_font_family = empty($options['font_family']) ? $global_text_font_family : $options['font_family'];
$text_font_size = empty($options['font_size']) ? round($global_text_font_size * 0.9) : $options['font_size'];
$text_font_color = empty($options['font_color']) ? $global_text_font_color : $options['font_color'];
$text_font_weight = empty($options['font_weight']) ? $global_text_font_weight : $options['font_weight'];

$meta_style = TNP_Composer::get_style($options, '', $composer, 'text', ['scale' => 0.9]);

$button_options = $options;
$button_options['button_font_family'] = empty($options['button_font_family']) ? $global_button_font_family : $options['button_font_family'];
$button_options['button_font_size'] = empty($options['button_font_size']) ? $global_button_font_size : $options['button_font_size'];
$button_options['button_font_color'] = empty($options['button_font_color']) ? $global_button_font_color : $options['button_font_color'];
$button_options['button_font_weight'] = empty($options['button_font_weight']) ? $global_button_font_weight : $options['button_font_weight'];
$button_options['button_background'] = empty($options['button_background']) ? $global_button_background_color : $options['button_background'];

$show_read_more_button = (bool) $options['show_read_more_button'];

Newsletter::instance()->switch_language($options['language']);

if ($options['layout'] == 'one') {
    include __DIR__ . '/layout-one.php';
} else if ($options['layout'] == 'one-2') {
    include __DIR__ . '/layout-one-2.php';
} else if ($options['layout'] == 'two') {
    include __DIR__ . '/layout-two.php';
} else if ($options['layout'] == 'full-post') {
    include __DIR__ . '/layout-full-post.php';
} else {
    include __DIR__ . '/layout-big-image.php';
}


