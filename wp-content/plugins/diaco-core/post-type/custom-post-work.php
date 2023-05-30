<?php
// Register Custom Post Type
function diaco_work_post_type() {

    $labels = array(
        'name' => _x('Work', 'Post Type General Name', 'diaco-core'),
        'singular_name' => _x('Work', 'Post Type Singular Name', 'diaco-core'),
        'menu_name' => __('Work', 'diaco-core'),
        'name_admin_bar' => __('Work', 'diaco-core'),
        'archives' => __('Item Work', 'diaco-core'),
        'parent_item_colon' => __('Parent Item:', 'diaco-core'),
        'all_items' => __('All Work', 'diaco-core'),
        'add_new_item' => __('Add New Work', 'diaco-core'),
        'add_new' => __('Add New Work', 'diaco-core'),
        'new_item' => __('New Work Item', 'diaco-core'),
        'edit_item' => __('Edit Work Item', 'diaco-core'),
        'update_item' => __('Update Work Item', 'diaco-core'),
        'view_item' => __('View Work Item', 'diaco-core'),
        'search_items' => __('Search Item', 'diaco-core'),
        'not_found' => __('Not found', 'diaco-core'),
        'not_found_in_trash' => __('Not found in Trash', 'diaco-core'),
        'featured_image' => __('Featured Image', 'diaco-core'),
        'set_featured_image' => __('Set featured image', 'diaco-core'),
        'remove_featured_image' => __('Remove featured image', 'diaco-core'),
        'use_featured_image' => __('Use as featured image', 'diaco-core'),
        'insert_into_item' => __('Insert into item', 'diaco-core'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'diaco-core'),
        'items_list' => __('Items list', 'diaco-core'),
        'items_list_navigation' => __('Items list navigation', 'diaco-core'),
        'filter_items_list' => __('Filter items list', 'diaco-core'),
    );



    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'diaco-core'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'work'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'thumbnail'),
    );
    register_post_type('work', $args);
}

add_action('init', 'diaco_work_post_type', 0);

function add_custom_taxonomies() {

    register_taxonomy('work-category', 'work', array(
        // Hierarchical taxonomy (like categories)
        'hierarchical' => true,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => _x('Work Category', 'diaco-core'),
            'singular_name' => _x('Work Category', 'diaco-core'),
            'search_items' => __('Search Work Category'),
            'all_items' => __('All Work Category'),
            'parent_item' => __('Parent Category'),
            'parent_item_colon' => __('Parent Work Category:'),
            'edit_item' => __('Edit Work Category'),
            'update_item' => __('Update Work Category'),
            'add_new_item' => __('Add New Work Category'),
            'new_item_name' => __('New Work Category Name'),
            'menu_name' => __('Work Category'),
        ),
        'rewrite' => array(
            'slug' => 'work-category',
            'with_front' => false,
            'hierarchical' => true
        )
    ));
}

add_action('init', 'add_custom_taxonomies', 0);

