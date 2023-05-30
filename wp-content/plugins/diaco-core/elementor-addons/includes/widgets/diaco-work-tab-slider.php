<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class WorkTabSlider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'WorkTabSlider';
	}

	public function get_title() {
		return esc_html__( 'Work Tab Slider', 'diaco' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'Work Tab Slider', 'diaco' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label' => esc_html__( 'Heading', 'diaco' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'sub_heading',
			array(
				'label' => esc_html__( 'Sub Heading', 'diaco' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'content',
			array(
				'label' => esc_html__( 'Content', 'diaco' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings = $this->get_settings_for_display();

		$heading     = $settings['heading'];
		$sub_heading = $settings['sub_heading'];
		$content     = $settings['content'];

		?>   



	<!-- work-section -->
	<section class="work-section work-tab">
		<div class="container">
			<div class="sec-title centred">
				<span class="top-title"><?php echo $sub_heading; ?></span>
				<h1><?php echo $heading; ?></h1>
				<p><?php echo $content; ?></p>
			</div>
			<div class="content-box">
				<div class="row">
					<div class="col-lg-10 col-md-12 col-sm-12 offset-lg-1 inner-column">
						<div class="inner-content">
							<div class="tab-btns centred">
							<?php

							$terms = get_terms(
								array(
									'taxonomy'         => 'work-category',
									'orderby'          => 'title',
									'order'            => 'ASC',
									'suppress_filters' => true,
									'hide_empty'       => false,
								)
							);

							?>
								<ul class="product-tab-btns project-btn post-filter">
								  <?php
									  $i = 0;
									foreach ( $terms as $term ) {
										$i++;
										if ( $i == 1 ) {
											echo '<li data-tab="#' . str_replace( ' ', '', $term->slug ) . '" class="p-tab-btn active-btn">' . $term->name . '</li>';
										} else {
											echo '<li data-tab="#' . str_replace( ' ', '', $term->slug ) . '" class="p-tab-btn">' . $term->name . '</li>';
										}
									}
									?>
								</ul>
							</div>
							<div class="p-tabs-content">

							<?php
							$i = 0;
							foreach ( $terms as $term ) {
								$i++;
								?>

								<div class="p-tab 
								<?php
								if ( $i == 1 ) {
									echo 'active-tab';}
								?>
								" id="<?php echo str_replace( ' ', '', $term->slug ); ?>">
									<div class="work-slider owl-theme owl-carousel nav-style-one">
									
								<?php
								$cat   = explode( ':', $term->name );
								$args  = array(
									'post_type' => 'work',
									'tax_query' => array(
										array(
											'taxonomy' => 'work-category',
											'field'    => 'slug',
											'terms'    => $cat[0],
										),
									),
								);
								$query = new WP_Query( $args );
								?>
										<?php
										$work_no = 1;
										while ( $query->have_posts() ) :
											$query->the_post();
											$work_icon = get_post_meta( get_the_ID(), 'diaco_work_icon', true );
											$work_link = get_post_meta( get_the_ID(), 'diaco_work_link', true );
											?>
										<div class="single-item">
											<div class="row">
												<div class="col-lg-6 col-md-6 col-sm-12 content-column">
													<div class="tab-content">
														<div class="top-content">
															<div class="count-text"><?php echo sprintf( '%02d', $work_no ); ?></div>
															<div class="icon-box"><i class="<?php echo $work_icon; ?>"></i></div>
														</div>
														<div class="lower-content">
															<h2><a href="<?php echo esc_url( $work_link ); ?>"><?php echo get_the_title(); ?></a></h2>
															<div class="text"><?php echo get_the_content(); ?></div>
														</div>
													</div>
												</div>
												<div class="col-lg-6 col-md-6 col-sm-12 image-column">
													<figure class="image-box line-overlay">
														<span class="line"></span>
														<span class="line line-bottom"></span>
														<?php echo the_post_thumbnail( 'full' ); ?>
													</figure>
												</div>
											</div> 
										</div> 
											<?php
											$work_no++;
									endwhile;
										?>

									</div>
								</div>
								
	  
								<?php } ?>

							  <?php
								wp_reset_postdata();
								?>
																
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- work-section end -->


		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \WorkTabSlider() );
