<?php
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

class DiacoPartnersLogo extends Widget_Base {

	public function get_name() {
		return 'diacopartnerslogo';
	}

	public function get_title() {
		return esc_html__( 'Diaco Partners Logo', 'diaco' );
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
			'show_slider',
			array(
				'label'        => __( 'Show Slider', 'plugin-domain' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'     => __( 'Sub Title', 'diaco' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'show_slider' => 'no',
				),

			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'diaco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 0,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),
				'condition'   => array(
					'show_slider' => 'no',
				),
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

		$this->start_controls_section(
			'logo_list',
			array(
				'label' => __( 'Logo List', 'diaco' ),
			)
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'logo_image',
			array(
				'label'   => __( 'Logo', 'diaco' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$repeater->add_control(
			'logo_url',
			array(
				'label'         => __( 'Logo Url', 'diaco' ),
				'type'          => Controls_Manager::URL,
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
		$this->end_controls_section();

	}
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$sub_title   = $settings['sub_title'];
		$extra_class = $settings['extra_class'];
		$title       = $settings['title'];

		if ( 'no' === $settings['show_slider'] ) {
			?>     <!-- client-style-two -->
	<section class="client-style-two <?php echo esc_attr( $extra_class ); ?>">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-12 title-column">
					<div class="sec-title">
						<span class="top-title"><?php echo $sub_title; ?></span>
						<?php echo $title; ?>
					</div>
				</div>
				<div class="col-lg-8 col-md-12 col-sm-12 inner-column">
					<div class="inner-content centred">
						<ul class="clients-logo clearfix">
							<?php
							foreach ( $settings['items1'] as $item ) {
								  $logo_image_url = ( $item['logo_image']['id'] != '' ) ? wp_get_attachment_url( $item['logo_image']['id'], 'full' ) : $item['logo_image']['url'];
								 $logo_url        = $item['logo_url']['url'];
								?>
							  
							<li class="logo"><figure><a href="<?php echo esc_url( $logo_url ); ?>"><img src="<?php echo $logo_image_url; ?>" alt="client image" ></a></figure></li>
						 <?php } ?>  
						 </ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- client-style-two end -->
			<?php

		} else {
			?>
  <!-- clients-section -->
  <section class="clients-section <?php echo esc_attr( $extra_class ); ?>">
		<div class="container">
			<?php if ( ! empty( $sub_title ) || ! empty( $title ) ) { ?> 
			  <div class="sec-title centred">
				<?php if ( ! empty( $sub_title ) ) { ?>
				  <span class="top-title"><?php echo $sub_title; ?></span>
					<?php
				}
				?>
				<?php if ( ! empty( $title ) ) { ?>
					<?php echo $title; ?>
				  <?php } ?>
				</div>
		  <?php } ?>
			<div class="clients-outer">
				<ul class="clients-carousel owl-theme owl-carousel">
				<?php
				foreach ( $settings['items1'] as $item ) {

					$logo_image_url = ( $item['logo_image']['id'] != '' ) ? wp_get_attachment_url( $item['logo_image']['id'], 'full' ) : $item['logo_image']['url'];

					$logo_url = $item['logo_url']['url'];
					?>
						
					  <li class="logo"><figure><a href="<?php echo esc_url( $logo_url ); ?>"><img src="<?php echo $logo_image_url; ?>" alt="client image" ></a></figure></li>
				<?php } ?>  
				</ul>
			</div>
		</div>
	</section>
	<!-- clients-section end -->

			<?php

		}
	}

	protected function content_template() {

	}
}

Plugin::instance()->widgets_manager->register( new \DiacoPartnersLogo() );
