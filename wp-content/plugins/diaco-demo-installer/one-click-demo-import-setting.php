<?php
add_filter(
	'pt-ocdi/replace_url',
	function () {
		return array( 'http://smartdemowp.com/diaco' );
	}
);

add_filter(
	'pt-ocdi/domain_name',
	function () {
		return array( 'smartdemowp.com' );
	}
);

add_filter(
	'pt-ocdi/destination_path',
	function () {
		return '/wp-content/plugins/diaco-demo-installer/images/';
	}
);

add_filter(
	'pt-ocdi/import_files',
	function() {
		return array(
			array(
				'import_file_name'             => esc_html__( 'Diaco', 'diaco_demo_installer' ),
				'local_import_file'            => plugin_dir_path( __FILE__ ) . 'demo-data/demo1/content.xml',
				'local_import_widget_file'     => plugin_dir_path( __FILE__ ) . 'demo-data/demo1/widgets.wie',
				'import_preview_image_url'     => plugin_dir_url( __FILE__ ) . 'demo-data/demo1/screen-image.png',
				'local_import_customizer_file' => plugin_dir_path( __FILE__ ) . 'demo-data/demo1/customize.dat',
				'import_file_name'             => esc_html__( 'Diaco', 'diaco_demo_installer' ),
				'import_notice'                => esc_html__( 'Install and active all required plugins before you click on the "Yes! Important" button.', 'diaco_demo_installer' ),
				'preview_url'                  => 'https://smart.commonsupport.com/diaco/',
				'local_import_redux'           => array(
					array(
						'file_path'   => plugin_dir_path( __FILE__ ) . 'demo-data/demo1/settings.json',
						'option_name' => 'diaco_options',
					),
				),
			),
		);
	},
	15
);


add_action(
	'pt-ocdi/after_import',
	function() {
		$top_menu = get_term_by( 'name', 'Primary', 'nav_menu' );
		if ( isset( $top_menu->term_id ) ) {
			set_theme_mod(
				'nav_menu_locations',
				array(
					'primary' => $top_menu->term_id,
				)
			);
		}
		$home_page = get_page_by_title( 'Home 1' );
		update_option( 'page_on_front', $home_page->ID );
		update_option( 'show_on_front', 'page' );

		$blog_page = get_page_by_title( 'Blog' );
		update_option( 'page_for_posts', $blog_page->ID );
	}
);

$token = get_option( 'envato_theme_license_token' );
if ( $token != '' ) {
	add_filter(
		'pt-ocdi/plugin_page_setup',
		function () {
			return array(
				'parent_slug' => 'envato-theme-license-dashboard',
				'page_title'  => esc_html__( 'One Click Demo Import', 'pt-ocdi' ),
				'menu_title'  => esc_html__( 'Import Demo Data', 'pt-ocdi' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'envato-theme-license-one-click-demo-import',
			);
		}
	);
} else {
	add_filter(
		'pt-ocdi/plugin_page_setup',
		function () {
			return array(
				'parent_slug' => 'themes.php',
				'page_title'  => esc_html__( 'One Click Demo Import', 'pt-ocdi' ),
				'menu_title'  => esc_html__( 'Import Demo Data', 'pt-ocdi' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'one-click-demo-import',
			);
		}
	);
}
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );






add_action( 'pt-ocdi/after_all_import_execution', 'revsliderimportfunc', 10, 3 );

function revsliderimportfunc( $selected_import_files, $import_files, $selected_index ) {

	if ( class_exists( 'RevSlider' ) ) {
		if ( file_exists( plugin_dir_path( __FILE__ ) . 'demo-data/demo-rev-slider/slider1.zip' ) ) {
			$slider = new RevSlider();
			for ( $i = 1;$i < 25;$i++ ) {
				if ( file_exists( plugin_dir_path( __FILE__ ) . 'demo-data/demo-rev-slider/slider' . $i . '.zip' ) ) {
					$slider->importSliderFromPost( true, true, plugin_dir_path( __FILE__ ) . 'demo-data/demo-rev-slider/slider' . $i . '.zip' );
				}
			}
		}
	}
	delete_transient( 'ocdi_importer_data' );
}
