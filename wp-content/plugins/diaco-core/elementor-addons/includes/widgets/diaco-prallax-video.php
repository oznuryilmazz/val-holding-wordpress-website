<?php
use Elementor\Utils;

class DiacoParallaxVideo extends \Elementor\Widget_Base {




	public function get_name() {
		return 'DiacoParallaxVideo';
	}

	public function get_title() {
		return esc_html__( 'Diaco Video', 'diaco' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'content', 'diaco' ),
			)
		);

		$this->add_control(
			'video_style',
			array(
				'label'   => __( 'Video Style', 'dico-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'one',
				'options' => array(
					'one' => __( 'Style One', 'dico-core' ),
					'two' => __( 'Style Two', 'dico-core' ),

				),

			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'     => __( 'Sub Title', 'diaco' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => array(
					'video_style' => 'one',
				),

			)
		);
		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,

			)
		);
		$this->add_control(
			'video_url',
			array(
				'label' => __( 'Video Url', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,

			)
		);
		$this->add_control(
			'back_ground_image',
			array(
				'label'   => __( 'Back Ground Image', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->end_controls_section();

	}    protected function render() {
		$settings    = $this->get_settings_for_display();
		$video_style = $settings['video_style'];

		$title                 = $settings['title'];
		$video_url             = $settings['video_url'];
		$back_ground_image_url = ( $settings['back_ground_image']['id'] != '' ) ? wp_get_attachment_url( $settings['back_ground_image']['id'], 'full' ) : $settings['back_ground_image']['url'];
		?>
		<?php if ( $video_style == 'two' ) { ?>
<!-- video-section -->
<section class="video-section centred">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 col-md-12 col-sm-12 offset-lg-1 video-column">
				<div class="video-gallery wow fadeInUp image_background" data-wow-delay="00ms"
					data-wow-duration="1500ms" data-image-src="<?php echo esc_url( $back_ground_image_url ); ?>">
					<a href="<?php echo esc_url( $video_url ); ?>" class="overlay-link lightbox-image"
						data-caption=""><i class="flaticon-arrow"></i></a>
					<?php
					if ( $title ) {
						?>
					<div class="text">
						<h4><?php echo $title; ?></h4>
					</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- video-section end -->
			<?php
		} else {
			$sub_title = $settings['sub_title'];
			?>
<!-- video-section -->
<section class="video-style-two centred image_background"
	data-image-src="<?php echo esc_url( $back_ground_image_url ); ?>">
	<div class="parallax-scene parallax-scene-2 anim-icons">
		<span data-depth="0.40" class="parallax-layer icon icon-1"></span>
		<span data-depth="0.50" class="parallax-layer icon icon-2"></span>
		<span data-depth="0.30" class="parallax-layer icon icon-3"></span>
		<span data-depth="0.40" class="parallax-layer icon icon-4"></span>
		<span data-depth="0.50" class="parallax-layer icon icon-5"></span>
		<span data-depth="0.30" class="parallax-layer icon icon-6"></span>
	</div>
	<div class="container">
		<div class="inner-content">
			<span class="top-text"><?php echo $sub_title; ?></span>
			<?php echo $title; ?>
			<a href="<?php echo esc_url( $video_url ); ?>" class="video-link lightbox-image" data-caption=""><i
					class="flaticon-arrow"></i></a>
		</div>
	</div>
</section>
<!-- video-section end -->
		<?php } ?>

		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoParallaxVideo() );
