<?php
// scripts
class diaco_Google_Fonts {
	/*
	  default config variable
	 */

	public $fonts_areas = array(
		'diaco_body_typography',
		'diaco_page_title_typography',
		'diaco_section_title_typography',
	);

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'diaco_google_script' ) );
	}

	public function diaco_google_script() {

		global $diaco_options;

		// Load styles
		if ( ! class_exists( 'ReduxFrameworkPlugin' ) ) {
			$protocol   = is_ssl() ? 'https' : 'http';
			$subsets    = 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese';
			$variants   = ':100,100i,200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i';
			$query_args = array(
				'family' => 'Oswald|Open+Sans' . $variants,
				'subset' => $subsets,
			);
			$font_url   = add_query_arg( $query_args, $protocol . '://fonts.googleapis.com/css' );
			wp_enqueue_style( 'diaco-google-fonts', $font_url, array(), null );
		}

		foreach ( $this->fonts_areas as $option ) {
			if ( isset( $diaco_options[ $option ]['font-family'] ) && $diaco_options[ $option ]['font-family'] ) {
				$font_families[] = $diaco_options[ $option ]['font-family'];
				if ( isset( $diaco_options[ $option ]['subsets'] ) && $diaco_options[ $option ]['subsets'] != '' ) {
					$font_subsets[] = $diaco_options[ $option ]['subsets'];
				}
				if ( isset( $diaco_options[ $option ]['font-weight'] ) && $diaco_options[ $option ]['font-weight'] != '' ) {
					$font_variants[ $diaco_options[ $option ]['font-family'] ][] = $diaco_options[ $option ]['font-weight'];
				}
			}
		}

		if ( isset( $font_families ) && ! empty( $font_families ) ) {
			$font_families = array_unique( $font_families );
			$fontfamily    = array();
			$protocol      = is_ssl() ? 'https' : 'http';
			foreach ( $font_families as $value ) {
				if ( isset( $font_variants[ $value ] ) ) {
					$font_variant = array_unique( $font_variants[ $value ] );
					if ( ! empty( $font_variant ) ) {
						$fontfamily[] = $value . ':' . implode( ',', $font_variant );
					} else {
						$fontfamily[] = $value;
					}
				}
			}
			if ( ! empty( $font_subsets ) ) {
				$subsets = implode( ',', $font_subsets );
			} else {
				$subsets = '';
			}
			if ( ! empty( $fontfamily ) ) {
				$fontfamily = implode( '|', $fontfamily );
			}
			if ( $subsets != '' ) {
				$query_args = array(
					'family' => urlencode( $fontfamily ),
					'subset' => urlencode( $subsets ),
				);
			} else {
				$query_args = array(
					'family' => urlencode( $fontfamily ),
				);
			}
			$protocol = is_ssl() ? 'https' : 'http';
			$font_url = add_query_arg( $query_args, $protocol . '://fonts.googleapis.com/css' );
			wp_enqueue_style( 'diaco-google-fonts', $font_url, array(), null );
		}
	}

}

$diaco_google_fonts = new diaco_Google_Fonts();
