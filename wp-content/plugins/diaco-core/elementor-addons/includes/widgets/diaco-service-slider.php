<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoServiceSlider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'diacoserviceslider';
	}

	public function get_title() {
		return esc_html__( 'Diaco Service Slider', 'diaco' );
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
				'label' => __( 'Content', 'diaco' ),
			)
		);
		$this->add_control(
			'slider_style',
			array(
				'label'   => __( 'Slider Style', 'dico-core' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'two',
				'options' => array(
					'one' => __( 'Style One', 'dico-core' ),
					'two' => __( 'Style Two', 'dico-core' ),

				),

			)
		);
		$this->add_control(
			'show_content',
			array(
				'label'        => __( 'Show Description', 'diaco-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'slider_style' => 'two',
				),
			)
		);
		$this->add_control(
			'show_slider',
			array(
				'label'        => __( 'Show Slider', 'diaco-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',

			)
		);
		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'diaco-core' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'service_description',
			array(
				'label'       => __( 'Description', 'diaco-core' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),
				'condition'   => array(
					'show_content' => 'yes',
					'slider_style' => 'two',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'service',
			array(
				'label' => __( 'Service', 'diaco' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'service_title',
			array(
				'label' => __( 'Service Title', 'diaco' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'serviceicon',
			array(
				'label'   => __( 'Service Icon', 'diaco' ),
				'type'    => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'service_image',
			array(
				'label'   => __( 'Service Image', 'diaco' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$repeater->add_control(
			'service_link_title',
			array(
				'label'       => __( 'Button Name', 'diaco' ),
				'type'        => Controls_Manager::TEXT,
				'rows'        => 0,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'service_content',
			array(
				'label'       => __( 'Description', 'diaco-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'service_link',
			array(
				'label'         => __( 'Service Link', 'diaco' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'diaco-core' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),

			)
		);
		$this->add_control(
			'items1',
			array(
				'label'   => __( 'Repeater List', 'diaco' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Title #1', 'diaco' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'diaco' ),
					),
					array(
						'list_title'   => __( 'Title #2', 'diaco' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'diaco' ),
					),
				),
			)
		);

		$this->add_control(
			'read_more',
			array(
				'label'   => __( 'Read More', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Read More',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'Button',
			array(
				'label'     => __( 'Button', 'diaco' ),
				'condition' => array(
					'show_content' => 'yes',
					'slider_style' => 'two',
				),
			)
		);
		$this->add_control(
			'all_service_button',
			array(
				'label' => __( 'All Services', 'diaco' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'all_service_button_link',
			array(
				'label'         => __( 'All Service Link', 'diaco' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'diaco-core' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),

			)
		);

		$this->end_controls_section();
	}    protected function render() {
		$settings  = $this->get_settings_for_display();
		$title     = $settings['title'];
		$read_more = $settings['read_more'];

		$slider_style = $settings['slider_style'];

		if ( $slider_style == 'one' ) {

			?>
<!-- service-section -->
<section class="service-section">
	<div class="top-content">
		<div class="parallax-scene parallax-scene-2 anim-icons">
			<span data-depth="0.40" class="parallax-layer icon icon-1"></span>
			<span data-depth="0.50" class="parallax-layer icon icon-2"></span>
			<span data-depth="0.30" class="parallax-layer icon icon-3"></span>
			<span data-depth="0.40" class="parallax-layer icon icon-4"></span>
			<span data-depth="0.50" class="parallax-layer icon icon-5"></span>
			<span data-depth="0.30" class="parallax-layer icon icon-6"></span>
		</div>
		<div class="container">
			<div class="title-box">
				<?php echo wp_kses_post( $title ); ?>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="inner-content">
			<div class="four-item-carousel owl-theme owl-carousel nav-style-one">

				<?php
				  $num = 1;
				foreach ( $settings['items1'] as $item ) {
					$service_title   = $item['service_title'];
					//$serviceicon     = $item['serviceicon'];
					$service_content = $item['service_content'];
					$service_link    = $item['service_link']['url'];
					
					$serviceicon = ( $item['serviceicon']['id'] != '' ) ? wp_get_attachment_url( $item['serviceicon']['id'], 'full' ) : $item['serviceicon']['url'];
					$service_image = ( $item['service_image']['id'] != '' ) ? wp_get_attachment_url( $item['service_image']['id'], 'full' ) : $item['service_image']['url'];

					?>

				<div class="service-block-one">
					<div class="inner-box">
						<figure class="image-box">
							<img src="<?php echo esc_url( $service_image ); ?>" alt="service image">
							<div class="icon-box">
							<?php
							if (!empty($serviceicon)){
								echo '<img src="' . $serviceicon . '">';
							  }
							?>
							</div>
						</figure>
						<div class="caption-box">
							<div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
							<h4><?php echo esc_html( $service_title ); ?></h4>
							<a href="<?php echo esc_url( $service_link ); ?>"><?php echo esc_html( $read_more ); ?></a>
						</div>
						<div class="overlay-box">
							<div class="icon-box">
							<?php
							if (!empty($serviceicon)){
								echo '<img src="' . $serviceicon . '">';
							  }
							?>
							</div>


							<h4>
								<?php
								if ( ! empty( $service_link ) ) {
									?>
								<a href="<?php echo esc_url( $service_link ); ?>"> <?php } ?>
									<?php echo esc_html( $service_title ); ?>
									<?php
									if ( $service_link ) {
										?>
								</a><?php } ?>
							</h4>


							<div class="text"><?php echo wp_kses_post( $service_content ); ?></div>
						</div>
					</div>
				</div>
				<?php $num++; } ?>
			</div>
		</div>
	</div>
</section>
<!-- service-section end -->
			<?php
		} else {
			$service_description     = $settings['service_description'];
			$show_slider             = $settings['show_slider'];
			$show_content            = $settings['show_content'];
			$all_service_button_link = $settings['all_service_button_link']['url'];
			$all_service_button      = $settings['all_service_button'];
			?>
<!-- service-style-two -->
			<?php if ( 'yes' == $show_content ) { ?>
<section class="service-style-two gray-bg">
				<?php } else { ?>
	<section class="service-style-two service-style-three">
		<?php } ?>
			<?php
			if ( 'no' == $show_content ) {
				?>
		<div class="icon-layer wow zoomIn animated"></div><?php } ?>
		<div class="container">
			<?php if ( 'yes' == $show_content ) { ?>
			<div class="row">
				<?php } else { ?>
				<div class="inner-content">
					<?php } ?>
					<?php if ( 'yes' == $show_content ) { ?>
					<div class="col-xl-4 col-lg-12 col-md-12 inner-column">
						<div class="inner-content">
							<div class="sec-title">
								<?php echo wp_kses_post( $title ); ?>
							</div>
							<div class="text"><?php echo wp_kses_post( $service_description ); ?></div>
							<?php if ( $all_service_button ) { ?>
							<div class="link"><a
									href="<?php echo esc_url( $all_service_button_link ); ?>"><?php echo esc_html( $all_service_button ); ?></a>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					<?php if ( 'yes' == $show_content ) { ?>
					<div class="col-xl-8 col-lg-12 col-md-12 carousel-column">
						<div class="carousel-content">
							<div class="three-item-carousel owl-theme owl-carousel nav-style-one">
								<?php } else { ?>
								<div class="three-item-carousel owl-theme owl-carousel nav-style-one">
									<?php } ?>

									<?php
									$num = 1;
									foreach ( $settings['items1'] as $item ) {
										$service_title   = $item['service_title'];
										$serviceicon = ( $item['serviceicon']['id'] != '' ) ? wp_get_attachment_url( $item['serviceicon']['id'], 'full' ) : $item['serviceicon']['url'];
										$service_content = $item['service_content'];
										$service_link    = $item['service_link']['url'];

										$service_image = ( $item['service_image']['id'] != '' ) ? wp_get_attachment_url( $item['service_image']['id'], 'full' ) : $item['service_image']['url'];

										?>

													<?php
													if ( $show_slider != 'yes' ) {
														?>
									<div class="col-lg-4 col-md-6 col-sm-12 service-block wow fadeInLeft"
										data-wow-delay="00ms" data-wow-duration="1500ms"> <?php } ?>
										<div class="service-block-one">
											<div class="inner-box">
												<figure class="image-box">
													<img src="<?php echo esc_url( $service_image ); ?>"
														alt="service image">
												</figure>
												<div class="caption-box">
													<div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
													<h4><?php echo esc_html( $service_title ); ?></h4>
													<a
														href="<?php echo esc_url( $service_link ); ?>"><?php echo esc_html( $read_more ); ?></a>
												</div>
												<div class="overlay-box">
													<div class="icon-box">
													<?php
														if (!empty($serviceicon)){
															echo '<img src="' . $serviceicon . '">';
														}
													?>	
													</div>
													<h4><a
															href="<?php echo esc_url( $service_link ); ?>"><?php echo esc_html( $service_title ); ?></a>
													</h4>
													<div class="text"><?php echo wp_kses_post( $service_content ); ?>
													</div>
												</div>
											</div>
										</div>
										<?php
										if ( $show_slider != 'yes' ) {
											?>
									</div> <?php } ?>
													<?php
														 $num++;

									}
									?>

									<?php if ( 'yes' == $show_content ) { ?>
								</div>
							</div>
						</div>
						<?php } else { ?>
					</div>
					<?php } ?>


				</div>
			</div>
	</section>
	<!-- service-style-two end -->

			<?php
		}
	}
	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoServiceSlider() );
