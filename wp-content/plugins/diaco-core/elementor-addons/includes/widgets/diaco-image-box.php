<?php
use Elementor\Utils;

class DiacoImageBox extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Diaco Image Box';
	}

	public function get_title() {
		return esc_html__( 'Diaco Image Box', 'diaco' );
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
			'image_url',
			array(
				'label'   => __( 'Image Url', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$this->end_controls_section();

	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$image_url = ( $settings['image_url']['id'] != '' ) ? wp_get_attachment_url( $settings['image_url']['id'], 'full' ) : $settings['image_url']['url'];
		?>   
	  <div class="image-box">
	   <figure class="image"><a href="<?php echo $image_url; ?>" class="lightbox-image" data-fancybox="gallery"><img src="<?php echo $image_url; ?>" alt="image box"></a></figure>
	  </div>
		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoImageBox() );
