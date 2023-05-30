<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoServiceGrid extends \Elementor\Widget_Base {


	public function get_name() {
		return 'DiacoServiceGrid';
	}

	public function get_title() {
		return esc_html__( 'Diaco Service Grid', 'diaco' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco' );
	}

	protected function register_controls() {
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
				'label' => __( 'Service Icon', 'diaco' ),
				'type'  => Controls_Manager::ICON,

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
				'default' => __( 'Read More', 'diaco-core' ),
			)
		);

		$this->end_controls_section();

	}
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$read_more = $settings['read_more'];

		?>

<section class="service-style-two alternate-2">
	<div class="container">
		<div class="inner-content">
			<div class="row">
				<?php
						$num = 1;
				foreach ( $settings['items1'] as $item ) {
					$service_title   = $item['service_title'];
					$serviceicon     = $item['serviceicon'];
					$service_content = $item['service_content'];
					$service_link    = $item['service_link']['url'];

					$service_image = ( $item['service_image']['id'] != '' ) ? wp_get_attachment_url( $item['service_image']['id'], 'full' ) : $item['service_image']['url'];

					?>

				<div class="col-lg-4 col-md-6 col-sm-12 service-block wow fadeInLeft" data-wow-delay="00ms"
					data-wow-duration="1500ms">
					<div class="service-block-one">
						<div class="inner-box">
							<figure class="image-box">
								<img src="<?php echo esc_url( $service_image ); ?>" alt="service image">
							</figure>
							<div class="caption-box">
								<div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
								<h4><?php echo esc_html( $service_title ); ?></h4>
								<a href="<?php echo esc_url( $service_link ); ?>"><?php echo esc_html( $read_more ); ?></a>
							</div>
							<div class="overlay-box">
								<div class="icon-box"><i class="<?php echo esc_attr( $serviceicon ); ?>"></i></div>
								<h4><a
										href="<?php echo esc_url( $service_link ); ?>"><?php echo esc_html( $service_title ); ?></a>
								</h4>
								<div class="text"><?php echo wp_kses_post( $service_content ); ?></div>
							</div>
						</div>
					</div>
				</div>
					<?php
					$num++;
				}
				?>

			</div>
		</div>
	</div>
</section>
<!-- service-style-two end -->

		<?php
	}
	protected function content_template() {    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new \DiacoServiceGrid() );
