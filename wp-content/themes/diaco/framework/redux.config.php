<?php
/**
 * ReduxFramework Barebones Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */
if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name   = 'diaco_options';
$opt_prefix = 'diaco';
/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */
$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
	// TYPICAL -> Change these values as you need/desire
	'opt_name'             => $opt_name,
	// This is where your data is stored in the database and also becomes your global variable name.
	'display_name'         => $theme->get( 'Name' ),
	// Name that appears at the top of your panel
	'display_version'      => $theme->get( 'Version' ),
	// Version that appears at the top of your panel
	'menu_type'            => 'menu',
	// Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
	'allow_sub_menu'       => true,
	// Show the sections below the admin menu item or not
	'menu_title'           => esc_html__( 'Diaco Options', 'diaco' ),
	'page_title'           => esc_html__( 'Diaco Options', 'diaco' ),
	// You will need to generate a Google API key to use this feature.
	// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
	'google_api_key'       => '',
	// Set it you want google fonts to update weekly. A google_api_key value is required.
	'google_update_weekly' => false,
	// Must be defined to add google fonts to the typography module
	'async_typography'     => true,
	// Use a asynchronous font on the front end or font string
	// 'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
	'admin_bar'            => true,
	// Show the panel pages on the admin bar
	'admin_bar_icon'       => 'dashicons-portfolio',
	// Choose an icon for the admin bar menu
	'admin_bar_priority'   => 50,
	// Choose an priority for the admin bar menu
	'global_variable'      => '',
	// Set a different name for your global variable other than the opt_name
	'dev_mode'             => false,
	// Show the time the page took to load, etc
	'update_notice'        => true,
	// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
	'customizer'           => true,
	// Enable basic customizer support
	// 'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
	// 'disable_save_warn' => true,                    // Disable the save warning when a user changes a field
	// OPTIONAL -> Give you extra features
	'page_priority'        => null,
	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	'page_parent'          => 'themes.php',
	// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	'page_permissions'     => 'manage_options',
	// Permissions needed to access the options panel.
	'menu_icon'            => '',
	// Specify a custom URL to an icon
	'last_tab'             => '',
	// Force your panel to always open to a specific tab (by id)
	'page_icon'            => 'icon-themes',
	// Icon displayed in the admin panel next to your menu_title
	'page_slug'            => '_options',
	// Page slug used to denote the panel
	'save_defaults'        => true,
	// On load save the defaults to DB before user clicks save or not
	'default_show'         => false,
	// If true, shows the default value next to each field that is not the default value.
	'default_mark'         => '',
	// What to print by the field's title if the value shown is default. Suggested: *
	'show_import_export'   => true,
	// Shows the Import/Export panel when not used as a field.
	// CAREFUL -> These options are for advanced use only
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	'output_tag'           => true,
	// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
	// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	'database'             => '',
	// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	'use_cdn'              => true,
	// If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
	// 'compiler'             => true,
	// HINTS
	'hints'                => array(
		'icon'          => 'el el-question-sign',
		'icon_position' => 'right',
		'icon_color'    => 'lightgray',
		'icon_size'     => 'normal',
		'tip_style'     => array(
			'color'   => 'light',
			'shadow'  => true,
			'rounded' => false,
			'style'   => '',
		),
		'tip_position'  => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect'    => array(
			'show' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'mouseover',
			),
			'hide' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'click mouseleave',
			),
		),
	),
);


Redux::setArgs( $opt_name, $args );

Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Header Option', 'diaco' ),
		'id'     => 'diaco_header_area',
		'desc'   => esc_html__( 'Chnage header option here', 'diaco' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'      => $opt_prefix . '_header_style',
				'type'    => 'select',
				'title'   => esc_html__( 'Select Header', 'diaco' ),
				'options' => array(
					'1' => esc_html__( 'Header One', 'diaco' ),
					'2' => esc_html__( 'Header Two', 'diaco' ),
					'3' => esc_html__( 'Header Three', 'diaco' ),
					'4' => esc_html__( 'Header Four', 'diaco' ),
					'5' => esc_html__( 'Header Five', 'diaco' ),
					'6' => esc_html__( 'Header Six', 'diaco' ),
				),
				'default' => '1',
			),
			array(
				'id'    => $opt_prefix . '_header_social_link',
				'type'  => 'editor',
				'title' => esc_html__( 'Header Social Link', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_header_sticky_logo',
				'type'     => 'media',
				'url'      => true,
				'compiler' => 'true',
				'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'diaco' ),
				'subtitle' => esc_html__( 'Add/Upload Header Image using the WordPress native uploader', 'diaco' ),
				'title'    => esc_html__( 'Header Sticky Logo', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_preloader',
				'type'     => 'switch',
				'title'    => __( 'Preloader On/Off', 'diaco' ),
				'subtitle' => __( 'Look, it\'s on!', 'diaco' ),
				'default'  => true,
				'1'        => esc_html__( 'Enable', 'diaco' ),
				'0'        => esc_html__( 'Disable', 'diaco' ),
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Blog Settings', 'diaco' ),
		'id'               => 'blog_settings',
		'desc'             => esc_html__( 'These are really basic fields!', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'    => $opt_prefix . '_blog_title',
				'type'  => 'text',
				'title' => esc_html__( 'Blog Title', 'diaco' ),
			),
			array(
				'id'    => $opt_prefix . '_blog_rotate_text',
				'type'  => 'text',
				'title' => esc_html__( 'Rotate Text', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_blog_header_image',
				'type'     => 'media',
				'url'      => true,
				'compiler' => 'true',
				'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'diaco' ),
				'subtitle' => esc_html__( 'Add/Upload Header Image using the WordPress native uploader', 'diaco' ),
				'title'    => esc_html__( 'Header Image', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_blog_style',
				'type'     => 'select',
				'title'    => esc_html__( 'Post Style', 'diaco' ),
				'subtitle' => esc_html__( 'Blog style List, Masonry or Grid', 'diaco' ),
				'default'  => '1',
				'options'  => array(
					'1' => esc_html__( 'List One', 'diaco' ),
					'2' => esc_html__( 'List Two', 'diaco' ),
					'3' => esc_html__( 'Grid', 'diaco' ),
				),
			),
			array(
				'id'       => $opt_prefix . '_related_post',
				'type'     => 'switch',
				'title'    => esc_html__( 'Related Post', 'diaco' ),
				'subtitle' => esc_html__( 'Enable or Disable', 'diaco' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'diaco' ),
				'off'      => esc_html__( 'Disable', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_single_post_nav',
				'type'     => 'switch',
				'title'    => esc_html__( 'Single Post Nav', 'diaco' ),
				'subtitle' => esc_html__( 'Enable or Disable', 'diaco' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'diaco' ),
				'off'      => esc_html__( 'Disable', 'diaco' ),
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Page Settings', 'diaco' ),
		'id'               => 'page_settings',
		'desc'             => esc_html__( 'These are really basic fields!', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'       => $opt_prefix . '_page_spacing',
				'type'     => 'switch',
				'title'    => esc_html__( 'Page Spacing', 'diaco' ),
				'subtitle' => esc_html__( 'Enable or Disable', 'diaco' ),
				'default'  => true,
				'on'       => esc_html__( 'Enable', 'diaco' ),
				'off'      => esc_html__( 'Disable', 'diaco' ),
			),
		),
	)
);


Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Typography', 'diaco' ),
		'id'               => 'typography',
		'desc'             => esc_html__( 'Theme all font options', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-font',
		'fields'           => array(
			array(
				'id'         => $opt_prefix . 'body_typography',
				'type'       => 'typography',
				'title'      => esc_html__( 'Body Typography', 'diaco' ),
				'subtitle'   => esc_html__( 'Select body font family, size, line height, color and weight.', 'diaco' ),
				'text-align' => false,
				'subsets'    => false,
				'default'    => array(
					'color'       => '#222222',
					'font-weight' => '400',
					'font-family' => 'Open Sans',
					'google'      => true,
					'font-size'   => '14px',
				),
			),
			array(
				'id'          => $opt_prefix . 'page_title_typography',
				'type'        => 'typography',
				'title'       => esc_html__( 'Page Title', 'diaco' ),
				'subtitle'    => esc_html__( 'Page title Typography Settings', 'diaco' ),
				'text-align'  => false,
				'line-height' => false,
				'subsets'     => false,
				'color'       => false,
				'font-size'   => false,
				'output'      => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Google Maps', 'diaco' ),
		'id'               => 'gmaps_settings',
		'desc'             => esc_html__( 'Google Maps Settings', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-website',
		'fields'           => array(
			array(
				'id'      => $opt_prefix . '_maps_key',
				'type'    => 'text',
				'title'   => esc_html__( 'Key', 'diaco' ),
				'default' => '',
			),
		),
	)
);

Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( '404 Page Settings', 'diaco' ),
		'id'               => '404_settings',
		'desc'             => esc_html__( 'These are really basic fields!', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'    => $opt_prefix . '_404_rotate_text',
				'type'  => 'text',
				'title' => esc_html__( 'Rotate Text', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_404_header_image',
				'type'     => 'media',
				'url'      => true,
				'compiler' => 'true',
				'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'diaco' ),
				'subtitle' => esc_html__( 'Add/Upload Header Image using the WordPress native uploader', 'diaco' ),
				'title'    => esc_html__( 'Header Image', 'diaco' ),
			),
		),
	)
);


Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Coming Soon Settings', 'diaco' ),
		'id'               => 'comingsoon_settings',
		'desc'             => esc_html__( 'These are really basic fields!', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'      => $opt_prefix . '_comingsoon_subtitle',
				'type'    => 'text',
				'title'   => esc_html__( 'Comingsoon Sub Title', 'diaco' ),
				'default' => esc_html__( 'The site is under construction', 'diaco' ),
			),
			array(
				'id'      => $opt_prefix . '_comingsoon_desc',
				'type'    => 'textarea',
				'title'   => esc_html__( 'Comingsoon Desc', 'diaco' ),
				'default' => wp_kses_post( 'If you have any questions please contact us by e-mail: <a href="#">info@yourmail.com</a>', 'diaco' ),
			),
			array(
				'id'      => $opt_prefix . '_comingsoon_time',
				'type'    => 'text',
				'title'   => esc_html__( 'Comingsoon Time', 'diaco' ),
				'default' => esc_html__( '9/24/2019 05:06:59', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_comingsoon_header_image',
				'type'     => 'media',
				'url'      => true,
				'compiler' => 'true',
				'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'diaco' ),
				'subtitle' => esc_html__( 'Add/Upload Header Image using the WordPress native uploader', 'diaco' ),
				'title'    => esc_html__( 'Logo Image', 'diaco' ),
			),
		),
	)
);


Redux::setSection(
	$opt_name,
	array(
		'title'            => esc_html__( 'Footer Settings', 'diaco' ),
		'id'               => 'footer_settings',
		'desc'             => esc_html__( 'These are really basic fields!', 'diaco' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-th-large',
		'fields'           => array(
			array(
				'id'       => $opt_prefix . '_footer_widget',
				'type'     => 'switch',
				'title'    => esc_html__( 'Footer Widget', 'diaco' ),
				'subtitle' => esc_html__( 'Enable or Disable', 'diaco' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'diaco' ),
				'off'      => esc_html__( 'Disable', 'diaco' ),
			),
			array(
				'id'      => $opt_prefix . '_copyright_text',
				'type'    => 'textarea',
				'title'   => esc_html__( 'Copyright Text', 'diaco' ),
				'default' => wp_kses_post( '© <a href="#">diaco</a> 2019. All Rights Reserved.', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_footer_image',
				'type'     => 'media',
				'url'      => true,
				'compiler' => 'true',
				'desc'     => esc_html__( 'Basic media uploader with disabled URL input field.', 'diaco' ),
				'subtitle' => esc_html__( 'Add/Upload Header Image using the WordPress native uploader', 'diaco' ),
				'title'    => esc_html__( 'Header Image', 'diaco' ),
			),
			array(
				'id'       => $opt_prefix . '_back_to_top',
				'type'     => 'switch',
				'title'    => esc_html__( 'Back To Top', 'diaco' ),
				'subtitle' => esc_html__( 'Enable or Disable', 'diaco' ),
				'default'  => false,
				'on'       => esc_html__( 'Enable', 'diaco' ),
				'off'      => esc_html__( 'Disable', 'diaco' ),
			),
		),
	)
);
Redux::setSection(
	$opt_name,
	array(
		'title'  => esc_html__( 'Color option', 'diaco' ),
		'id'     => 'color_area',
		'desc'   => esc_html__( 'Chnage Color option here', 'diaco' ),
		'icon'   => 'el el-home',
		'fields' => array(
			array(
				'id'          => $opt_prefix . '_main_color',
				'type'        => 'color',
				'title'       => __( 'Primary Color', 'diaco' ),
				'subtitle'    => __( 'Pick a color for the theme (default: #f5a64a).', 'diaco' ),
				'validate'    => 'color',
				'transparent' => false,
			),
		),
	)
);
