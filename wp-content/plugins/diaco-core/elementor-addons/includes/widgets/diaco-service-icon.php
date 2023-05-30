<?php
use Elementor\Utils;

class DiacoServiceIcon extends \Elementor\Widget_Base {

	public function get_name() {
		return 'DiacoServiceIcon';
	}

	public function get_title() {
		return esc_html__( 'Service Icon', 'diaco-core' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco-core' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content',
			array(
				'label' => __( 'Content', 'diaco-core' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'service_content',
			array(
				'label' => __( 'Title', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::ICON,
			)
		);

		$this->add_control(
			'service_link',
			array(
				'label'         => __( 'Service Link', 'diaco-core' ),
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

		$this->end_controls_section();

	}    protected function render() {
		$settings = $this->get_settings_for_display();

		$title           = $settings['title'];
		$service_content = $settings['service_content'];
		$icon            = $settings['icon'];
		$url             = $settings['service_link']['url'];

		?>
		 <div class="service-block-two">
			  <div class="icon-box"><i class="<?php echo esc_attr( $icon ); ?>"></i></div>
			  <h5><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h5>
			  <div class="text"><?php echo wp_kses_post( $service_content ); ?></div>
		  </div>
		 <?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoServiceIcon() );
