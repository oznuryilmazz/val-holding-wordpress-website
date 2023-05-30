<?php
// FRONTEND // After Elementor registers all styles.
add_action( 'elementor/frontend/after_register_styles', 'icons_enqueue_after_frontend' );

function icons_enqueue_after_frontend() {
	wp_enqueue_style( 'icomoon', plugin_dir_url( __FILE__ ) . 'style.css', array(), '' );
}

// EDITOR // Before the editor scripts enqueuing.
add_action( 'elementor/editor/before_enqueue_scripts', 'icons_enqueue_before_editor' );

function icons_enqueue_before_editor() {
	wp_enqueue_style( 'icomoon', plugin_dir_url( __FILE__ ) . 'style.css', array(), '' );
}

function solustrid_modify_controls( $controls_registry ) {
	// Get existing icons
	$icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
	// Append new icons
	$new_icons = array_merge(
		array(
			'flaticon-magnifying-glass' => 'flaticon-magnifying-glass',
			'flaticon-mail'             => 'flaticon-mail',
			'flaticon-phone-call'       => 'flaticon-phone-call',
			'flaticon-add'              => 'flaticon-add',
			'flaticon-menu'             => 'flaticon-menu',
			'flaticon-mouse'            => 'flaticon-mouse',
			'flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface' => 'flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface',
			'flaticon-arrow'            => 'flaticon-arrow',
			'flaticon-interior'         => 'flaticon-interior',
			'flaticon-slim-right'       => 'flaticon-slim-right',
			'flaticon-slim-left'        => 'flaticon-slim-left',
			'flaticon-plant'            => 'flaticon-plant',
			'flaticon-lamp'             => 'flaticon-lamp',
			'flaticon-cactus'           => 'flaticon-cactus',
			'flaticon-plant-1'          => 'flaticon-plant-1',
			'flaticon-couch'            => 'flaticon-couch',
			'flaticon-sofa'             => 'flaticon-sofa',
			'flaticon-drawing'          => 'flaticon-drawing',
			'flaticon-check-box'        => 'flaticon-check-box',
			'flaticon-bookmark'         => 'flaticon-bookmark',
			'flaticon-right-quote'      => 'flaticon-right-quote',
			'flaticon-house'            => 'flaticon-house',
			'flaticon-phone-call-1'     => 'flaticon-phone-call-1',
			'flaticon-envelope'         => 'flaticon-envelope',
			'flaticon-email'            => 'flaticon-email',
		),
		$icons
	);
	// Then we set a new list of icons as the options of the icon control
	$controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
}

add_action( 'elementor/controls/controls_registered', 'solustrid_modify_controls', 10, 1 );

