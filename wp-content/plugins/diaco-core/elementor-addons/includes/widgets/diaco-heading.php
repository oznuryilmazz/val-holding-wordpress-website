<?php
use Elementor\Utils;

class DiacoHeading extends \Elementor\Widget_Base {

	public function get_name() {
		return 'DiacoHeading';
	}

	public function get_title() {
		return esc_html__( 'Diaco Heading', 'diaco' );
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
			'heading',
			array(
				'label' => __( 'Heading', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'sub_heading',
			array(
				'label' => __( 'Sub Heading', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,

			)
		);
		$this->end_controls_section();

	}    protected function render() {
		$settings = $this->get_settings_for_display();

		$heading     = $settings['heading'];
		$sub_heading = $settings['sub_heading'];

		?> <div class="content-box">
	  <div class="title"><h1><?php echo $heading; ?><span><?php echo $sub_heading; ?></span></h1></div>
	  </div>
		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoHeading() );
