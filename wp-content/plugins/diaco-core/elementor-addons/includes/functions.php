<?php
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'elementor/init',
	function() {
			\Elementor\Plugin::$instance->elements_manager->add_category(
				'diaco',
				array(
					'title' => __( 'Diaco', 'diaco-core' ),
					'icon'  => 'fa fa-plug',
				),
				1
			);
	}
);
