<?php
$diaco_slider_shortcode = get_post_meta( get_the_id(), 'diaco_slider_shortcode', true );
global $diaco_options;

$header_style = get_query_var( 'header_type' );

if ( ! $header_style ) {
		$header_style = $diaco_options['diaco_header_style'];
}
$header_style_class = '';

if ( $header_style == '2' || $header_style == '3' ) {
	$header_style_class = 'style-two';
} elseif ( $header_style == '4' || $header_style == '5' ) {
	$header_style_class = 'style-four';
} elseif ( $header_style == '6' ) {
	$header_style_class = 'style-six';
} else {
	$header_style_class = '';
}
?>

<?php if ( ! is_search() ) { ?>
	<!--Main Slider-->

	<section class="main-slider <?php echo esc_attr( $header_style_class ); ?>">
		<?php echo do_shortcode( $diaco_slider_shortcode ); ?>
		<!--Scroll Dwwn Btn-->
	  <?php if ( $header_style == '2' ) { ?> 
		<div class="mouse-btn-down scroll-to-target" data-target=".about-style-two">
	  <?php } elseif ( $header_style == '3' ) { ?> 
		<div class="mouse-btn-down scroll-to-target" data-target=".service-style-three">
	  <?php } elseif ( $header_style == '4' || $header_style == '5' ) { ?> 
		<div class="mouse-btn-down scroll-to-target" data-target=".about-style-three">
	  <?php } elseif ( $header_style == '6' ) { ?> 
		<div class="mouse-btn-down scroll-to-target" data-target=".project-masonary">
	  <?php } else { ?>
		<div class="mouse-btn-down scroll-to-target" data-target=".about-section">
	 <?php } ?>
			<div class="scroll-arrow-box">
				<span class="scroll-arrow"></span>
			</div>
			<div class="scroll-btn-flip-box">
				<span class="scroll-btn-flip" data-text="Scroll"><?php esc_html_e( 'Scroll', 'diaco' ); ?></span>
			</div>
		</div>
	</section>
	<!--End Main Slider-->
<?php } ?>
