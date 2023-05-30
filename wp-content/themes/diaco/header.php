<?php 
	global $diaco_options; 
	$diaco_preloader   = $diaco_options['diaco_preloader'];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php
		if ( function_exists( 'has_site_icon' ) && has_site_icon() ) { // since 4.3.0
			wp_site_icon();
		}
		?>

<?php wp_head(); ?>
</head>

<!-- page wrapper -->
<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}
?>
<?php if ( $diaco_preloader == 1 ) { ?> 
<!-- .preloader -->

<div class="preloader"></div>
<!-- /.preloader -->
<?php } ?>

<?php

global $diaco_options;
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
$header_style = get_query_var( 'header_type' );

if ( ! $header_style ) {
		$header_style = $diaco_options['diaco_header_style'];
}

if ( ! $header_style ) {
		$header_style = 1;
}

get_template_part( 'template-parts/header/header', $header_style );
}
?>