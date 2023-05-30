<?php
global $diaco_options;
$diaco_footer_image_link = isset( $diaco_options['diaco_footer_image']['url'] ) ? $diaco_options['diaco_footer_image']['url'] : '';
$diaco_footer_widget     = isset( $diaco_options['diaco_footer_widget'] ) ? $diaco_options['diaco_footer_widget'] : 0;
$diaco_show_top          = isset( $diaco_options['diaco_back_to_top'] ) ? $diaco_options['diaco_back_to_top'] : 0;
$diaco_copyright_text    = isset( $diaco_options['diaco_copyright_text'] ) ? $diaco_options['diaco_copyright_text'] : '© <a href="#">diaco</a> 2019. All Rights Reserved.';
?>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) { ?>
<!-- main-footer -->
<footer class="main-footer image_background" data-image-src="<?php echo esc_url( $diaco_footer_image_link ); ?>">
<!--footer upper-->
	<div class="container">
		<?php if ( $diaco_footer_widget == 1 ) { ?>
			<?php get_sidebar( 'footer' ); ?>
		<?php } ?>
		<div class="footer-bottom centred">
			<div class="copyright"><?php echo wp_kses_post( $diaco_copyright_text ); ?></div>
		</div>
	</div>
</footer>
<!-- main-footer end -->

	<?php if ( $diaco_show_top == 1 ) : ?>
	<!--Scroll to top-->
	<button class="scroll-top scroll-to-target" data-target="html">
		<span class="fa fa-arrow-up"></span>
	</button>
<?php endif; ?>
<?php } ?>

<?php wp_footer(); ?>
</body><!-- End of .page_wrapper -->
</html>
