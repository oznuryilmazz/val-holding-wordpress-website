<?php
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class DiacAboutAgent extends Widget_Base {

	public function get_name() {
		return 'Diac About Agent';
	}

	public function get_title() {
		return esc_html__( 'About Agent', 'diaco' );
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
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->add_control(
			'agent_name',
			array(
				'label'       => __( 'Agent Name', 'diaco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 0,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),

			)
		);
		$this->add_control(
			'agent_image',
			array(
				'label'   => __( 'Agent Image', 'diaco' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$this->add_control(
			'agent_bio',
			array(
				'label'       => __( 'Agent Bio', 'diaco' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),

			)
		);

			$this->add_control(
				'extra_class',
				array(
					'label' => __( 'Extra Class', 'diaco' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

		$this->end_controls_section();

	}
	protected function render() {
		   $settings  = $this->get_settings_for_display();
		 $heading     = $settings['heading'];
		 $agent_name  = $settings['agent_name'];
		 $agent_image = ( $settings['agent_image']['id'] != '' ) ? wp_get_attachment_url( $settings['agent_image']['id'], 'full' ) : $settings['agent_image']['url'];

		 $agent_bio   = $settings['agent_bio'];
		 $extra_class = $settings['extra_class'];
		?>
		  <!-- about-style-two -->
	<section class="about-style-two <?php echo esc_attr( $extra_class ); ?>">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12 col-sm-12 image-column">
					<div class="image-box line-overlay wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
						<figure class="image">
							<span class="line active"></span>
							<span class="line line-bottom active"></span>
							<img src="<?php echo $agent_image; ?>" alt="about agent image" >
							<?php echo $agent_name; ?> 
						</figure>
						<div class="rotate-text"><?php echo $heading; ?></div>
					</div>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12 content-column">
				<div class="content-box wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
					 <?php echo $agent_bio; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- about-style-two end -->

		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacAboutAgent() );
