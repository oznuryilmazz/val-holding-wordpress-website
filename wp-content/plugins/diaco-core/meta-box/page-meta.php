<?php
add_filter( 'rwmb_meta_boxes', 'diaco_register_framework_post_meta_box' );

/**
 * Register meta boxes
 *
 * Remember to change "your_prefix" to actual prefix in your project
 *
 * @return void
 */
function diaco_register_framework_post_meta_box( $meta_boxes ) {

	global $wp_registered_sidebars;

	$sidebars = array(
		'0' => esc_html__( 'Default widget', 'diaco-core' ),
	);

	foreach ( $wp_registered_sidebars as $key => $value ) {
		$sidebars[ $key ] = $value['name'];
	}

	/**
	 * prefix of meta keys (optional)
	 * Use underscore (_) at the beginning to make keys hidden
	 * Alt.: You also can make prefix empty to disable it
	 */
	// Better has an underscore as last sign
	$prefix = 'diaco';

	$meta_boxes[] = array(
		'id'        => 'framework-post-meta-image',
		'title'     => esc_html__( 'Design Settings', 'diaco-core' ),
		'pages'     => array(
			'post',
		),
		'context'   => 'normal',
		'priority'  => 'high',
		'tab_style' => 'left',
		'fields'    => array(
			array(
				'name'             => esc_html__( 'Post Meta Image', 'diaco-core' ),
				'id'               => "{$prefix}_post_meta_image",
				'type'             => 'image',
				'max_file_uploads' => 1,
			),
		),
	);

	$posts_page = get_option( 'page_for_posts' );

	if ( ! isset( $_GET['post'] ) || intval( $_GET['post'] ) != $posts_page ) {

		$meta_boxes[] = array(
			'id'       => $prefix . '_page_meta_box',
			'title'    => esc_html__( 'Page Design Settings', 'diaco-core' ),
			'pages'    => array(
				'page',
			),
			'context'  => 'normal',
			'priority' => 'core',
			'fields'   => array(
				array(
					'name' => esc_html__( 'Header Slider Shortcode', 'diaco-core' ),
					'id'   => "{$prefix}_slider_shortcode",
					'desc' => '',
					'type' => 'text',
				),
				array(
					'id'      => "{$prefix}_show_page_title",
					'name'    => esc_html__( 'Show Page Titlebar', 'diaco-core' ),
					'desc'    => '',
					'type'    => 'radio',
					'std'     => 'on',
					'options' => array(
						'on'  => 'Yes',
						'off' => 'No',
					),
				),
				array(
					'id'      => "{$prefix}_show_breadcrumb",
					'name'    => esc_html__( 'Show Breadcrumb', 'diaco-core' ),
					'desc'    => '',
					'type'    => 'radio',
					'std'     => 'on',
					'options' => array(
						'on'  => 'Yes',
						'off' => 'No',
					),
				),
				array(
					'name' => esc_html__( 'Left Side Rotate Text', 'diaco-core' ),
					'id'   => "{$prefix}_rotate_text",
					'desc' => '',
					'type' => 'text',
				),
				array(
					'id'          => "{$prefix}_page_style",
					'name'        => esc_html__( 'Page Style', 'diaco-core' ),
					'desc'        => '',
					'type'        => 'image_select',
					'std'         => 'full',
					'options'     => array(
						'left_side'  => DIACO_CORE_PLUGIN_URI . '/assets/images/admin/left.jpg',
						'full'       => DIACO_CORE_PLUGIN_URI . '/assets/images/admin/no.jpg',
						'right_side' => DIACO_CORE_PLUGIN_URI . '/assets/images/admin/right.jpg',
					),
					'allowClear'  => true,
					'placeholder' => esc_html__( 'Select', 'diaco-core' ),
				),
				array(
					'id'          => "{$prefix}_page_sidebar",
					'name'        => esc_html__( 'Page Sidebar', 'diaco-core' ),
					'desc'        => '',
					'type'        => 'select_advanced',
					'std'         => '',
					'options'     => $sidebars,
					'allowClear'  => true,
					'placeholder' => esc_html__( 'Select', 'diaco-core' ),
				),
			),
		);
	}

	$meta_boxes[] = array(
		'id'       => 'framework-meta-work',
		'title'    => esc_html__( 'Work Meta Fields', 'diaco-core' ),
		'pages'    => array(
			'work',
		),
		'context'  => 'advanced',
		'priority' => 'default',
		'autosave' => 'false',
		'fields'   => array(
			array(
				'name'    => esc_html__( 'Work Icon', 'diaco-core' ),
				'desc'    => esc_html__( 'Work Icon', 'diaco-core' ),
				'id'      => "{$prefix}_work_icon",
				'type'    => 'text',
				'default' => 'flaticon-interior',
			),
			array(
				'name'    => esc_html__( 'Work Link', 'diaco-core' ),
				'desc'    => esc_html__( 'Work Link', 'diaco-core' ),
				'id'      => "{$prefix}_work_link",
				'type'    => 'text',
				'default' => '#',
			),

		),
	);

	return $meta_boxes;
}
