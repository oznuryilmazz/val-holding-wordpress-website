<?php get_header();
global $diaco_options;
$diaco_header_image_link = isset( $diaco_options['diaco_blog_header_image']['url'] ) ? $diaco_options['diaco_blog_header_image']['url'] : '';
$diaco_rotate_text       = isset( $diaco_options['diaco_blog_rotate_text'] ) ? $diaco_options['diaco_blog_rotate_text'] : '';
$diaco_related_post      = isset( $diaco_options['diaco_related_post'] ) ? $diaco_options['diaco_related_post'] : 0;
$diaco_single_post_nav   = isset( $diaco_options['diaco_single_post_nav'] ) ? $diaco_options['diaco_single_post_nav'] : 0;

$diaco_page_spacing = isset( $diaco_options['diaco_page_spacing'] ) ? $diaco_options['diaco_page_spacing'] : 1;
if ( $diaco_page_spacing == 1 ) {
	$diaco_page_spacing = 'sidebar-page-container blog-single post-content-unit';
} else {
	$diaco_page_spacing = 'sidebar-page-container post-content blog-single';
}
?>
	<section class="page-title centred image_background" data-image-src="<?php echo esc_url( $diaco_header_image_link ); ?>">    
		<?php
		if ( ! empty( $diaco_rotate_text ) ) {
			echo '<div class="rotate-text">' . esc_html( $diaco_rotate_text ) . '</div>'; }
		?>
				
		<div class="container">
			<div class="content-box">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	</section>
	<!-- blog-classic -->
	<section class="<?php echo esc_attr( $diaco_page_spacing ); ?>">
		<div class="container">
			<div class="row">
				<?php if ( is_active_sidebar( 'left_sideber' ) ) { ?>
				<div class="col-lg-8 col-md-12 col-sm-12 content-side">
				<?php } else { ?>
					<div class="col-lg-12 col-md-12 col-sm-12 content-side">
				<?php } ?>
					<div class="blog-single-content">
						<div class="inner-box">
					  <?php
						while ( have_posts() ) :
							the_post();
							?>
							<div class="content-style-one">
								<div class="news-block-one">
									<?php if ( has_post_thumbnail() ) { ?>
									<figure class="image-box">
										<?php echo get_the_post_thumbnail(); ?>
									</figure>
									<?php } ?>
									<div class="lower-content">
										<ul class="post-info">
											<li><?php diaco_posted_on(); ?></li>
											<?php diaco_entry_footer(); ?>
										</ul>
										<div class="text">
											<?php the_content(); ?>
											<?php
											wp_link_pages(
												array(
													'before' => '<div class="page-links">',
													'after' => '</div>',
												)
											);
											?>
																					   
										</div>
									</div>
								</div>
							  
							</div>
							  <?php diaco_post_tags( get_the_ID() ); ?>
							  <?php if ( $diaco_single_post_nav == 1 ) { ?> 
							<div class="post-controls">
								<div class="inner centred">
									<?php
									the_post_navigation(
										array(
											'prev_text' => '<div class="prev-post">' . esc_html__( 'Prev Article', 'diaco' ) . '</div>',
											'next_text' => '<div class="next-post">' . esc_html__( 'Next Article', 'diaco' ) . '</div>',
											'screen_reader_text' => esc_html__( '&nbsp;', 'diaco' ),
										)
									);
									?>
									<div class="scroll-btn centred scroll-to-target" data-target=".blog-single"><?php esc_html_e( 'Back To Top', 'diaco' ); ?> <i class="far fa-arrow-alt-circle-up"></i></div>
								</div>
							</div>
							<?php } ?>
							  <?php get_template_part( 'template-parts/single/author', 'box' ); ?>
							  <?php
								if ( $diaco_related_post == '1' ) {
									diaco_related_post();
								}
								?>
							  <?php
								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								  endif;
								?>
							  <?php endwhile; ?>
						</div>
					</div>
				</div>
				<?php if ( is_active_sidebar( 'left_sideber' ) ) { ?>
				<div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
					<?php get_sidebar(); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<!-- blog-classic end -->
<?php get_footer(); ?>
