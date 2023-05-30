<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoPriceBox extends \Elementor\Widget_Base {

	public function get_name() {
		return 'DiacoPriceBox';
	}

	public function get_title() {
		return esc_html__( 'Diaco Price Box', 'diaco' );
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
			'title',
			array(
				'label'   => __( 'Title', 'diaco-core' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( '<h1>servicing<br><span>Cost</span></h1>', 'diaco-core' ),
			)
		);
		$this->add_control(
			'title_background',
			array(
				'label'   => __( 'Title Background', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$this->add_control(
			'add_class',
			array(
				'label'   => __( 'Add Class', 'diaco-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'alternate-2', 'diaco-core' ),

			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'price_box',
			array(
				'label' => __( 'Price Box', 'diaco' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'price_title',
			array(
				'label'   => __( 'Price Title', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Starter', 'diaco' ),

			)
		);
		$repeater->add_control(
			'price',
			array(
				'label'   => __( 'Price', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'rows'    => 0,
				'default' => __( '$35.99', 'plugin-domain' ),

			)
		);
		$repeater->add_control(
			'duration',
			array(
				'label'   => __( 'Duration', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'rows'    => 0,
				'default' => __( 'Monthly', 'plugin-domain' ),

			)
		);
		$repeater->add_control(
			'price_list',
			array(
				'label'       => __( 'Price List', 'diaco-core' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'         => __( 'Link', 'diaco' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'plugin-domain' ),
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
				'type'    => \Elementor\Controls_Manager::REPEATER,
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
		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		  $add_class      = $settings['add_class'];
		  $title          = $settings['title'];
		$title_background = ( $settings['title_background']['id'] != '' ) ? wp_get_attachment_url( $settings['title_background']['id'], 'full' ) : $settings['title_background']['url'];

		?>


	<!-- pricing-section -->
	<section class="pricing-section <?php echo esc_attr( $add_class ); ?>">
		<div class="top-content" style="background-image: url(<?php echo esc_url( $title_background ); ?>);">
			<div class="parallax-scene parallax-scene-2 anim-icons">
				<span data-depth="0.40" class="parallax-layer icon icon-1"></span>
				<span data-depth="0.50" class="parallax-layer icon icon-2"></span>
				<span data-depth="0.30" class="parallax-layer icon icon-3"></span>
				<span data-depth="0.40" class="parallax-layer icon icon-4"></span>
				<span data-depth="0.50" class="parallax-layer icon icon-5"></span>
				<span data-depth="0.30" class="parallax-layer icon icon-6"></span>
			</div>
			<div class="container">
				<div class="title-box"><?php echo wp_kses_post( $title ); ?></div>
			</div>
		</div>
		<div class="lower-content">
			<div class="container">
				<div class="inner-content">
					<div class="row">
					<?php
					foreach ( $settings['items1'] as $item ) {
						$price_title = $item['price_title'];
						$price       = $item['price'];
						$duration    = $item['duration'];
						$price_list  = $item['price_list'];
						$link        = $item['link']['url'];
						?>
						  
						<div class="col-lg-4 col-md-6 col-sm-12 pricing-column wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
							<div class="pricing-table">
								<div class="table-header">
									<h3><?php echo esc_html( $price_title ); ?></h3>
									<div class="price"><?php echo esc_html( $price ); ?> / <span><?php echo esc_html( $duration ); ?></span></div>
								</div>
								<div class="table-content">
									<?php echo wp_kses_post( $price_list ); ?>
								</div>
								<div class="table-footer">
									<a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Learn More', 'diaco-core' ); ?></a>
								</div>
							</div>
						</div>
					  <?php } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- pricing-section end -->

		<?php
	}
	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoPriceBox() );
