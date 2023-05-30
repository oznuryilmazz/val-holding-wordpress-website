<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoProjectMasonary extends \Elementor\Widget_Base {

	public function get_name() {
		return 'diacoprojectmasonary';
	}

	public function get_title() {
		return esc_html__( 'Diaco Project Masonary', 'diaco' );
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
				'label' => __( 'Title', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'Project',
			array(
				'label' => __( 'Project', 'diaco' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'project_title',
			array(
				'label' => __( 'Title', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'project_description',
			array(
				'label'       => __( 'Description', 'diaco-core' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'project_image',
			array(
				'label'   => __( 'Project Image', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'         => __( 'Url', 'diaco' ),
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

	}   protected function render() {

		$settings = $this->get_settings_for_display();
		$title    = $settings['title'];

		$odd_item  = array();
		$even_item = array();
		$i         = 1;
		foreach ( $settings['items1'] as $item ) {
			if ( $i % 2 == 0 ) {
				$even_item[] = $item;
			} else {
				$odd_item[] = $item;
			}
			$i++;

		}
		?>     
	<!-- project-masonary -->
	<section class="project-masonary">
		<div class="lower-text">
			<div class="container">
				<div class="text"><?php echo esc_html( $title ); ?></div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-md-12 col-sm-12 offset-lg-1 big-column">
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12 inner-column small-column">
							<div class="inner-content">
							<?php
							foreach ( $odd_item as $odd_item ) {

								 $odd_project_title = $odd_item['project_title'];

								 $odd_project_image_url = ( $odd_item['project_image']['id'] != '' ) ? wp_get_attachment_url( $odd_item['project_image']['id'], 'full' ) : $odd_item['project_image']['url'];

								 $odd_project_url          = $odd_item['url']['url'];
								 $odd_project_url_target   = $odd_item['url']['is_external'] ? 'target="_blank"' : '';
								 $odd_project_url_nofollow = $odd_item['url']['nofollow'] ? 'rel="nofollow"' : '';

								 $odd_project_description = $odd_item['project_description'];
								?>
								<div class="project-block-one line-overlay wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
									<div class="inner-box">
										<figure class="image-box">
											<span class="line"></span>
											<span class="line line-bottom"></span>
											<img src="<?php echo esc_url( $odd_project_image_url ); ?>" alt="project image">
										</figure>
										<div class="icon-box"><a href="<?php echo esc_url( $odd_project_image_url ); ?>" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i></a></div>
									</div>
									<div class="lower-content">
										<h4><a href="<?php echo esc_url( $odd_project_url ); ?>"><?php echo esc_html( $odd_project_title ); ?></a></h4>
										<div class="text"><?php echo wp_kses_post( $odd_project_description ); ?></div>
									</div>
								</div>
							<?php } ?>  
							</div>
						</div>
						<div class="col-lg-6 col-md-12 col-sm-12 inner-column small-column">
							<div class="inner-content">
							<?php
							foreach ( $even_item as $evn_item ) {
								$evn_project_title = $evn_item['project_title'];

								$evn_project_image_url = ( $evn_item['project_image']['id'] != '' ) ? wp_get_attachment_url( $evn_item['project_image']['id'], 'full' ) : $evn_item['project_image']['url'];

								$evn_project_url          = $evn_item['url']['url'];
								$evn_project_url_target   = $evn_item['url']['is_external'] ? 'target="_blank"' : '';
								$evn_project_url_nofollow = $evn_item['url']['nofollow'] ? 'rel="nofollow"' : '';

								$evn_project_description = $evn_item['project_description'];
								?>
								<div class="project-block-one line-overlay wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
									<div class="inner-box">
										<figure class="image-box">
											<span class="line"></span>
											<span class="line line-bottom"></span>
											<img src="<?php echo esc_url( $evn_project_image_url ); ?>" alt="project image">
										</figure>
										<div class="icon-box"><a href="<?php echo esc_url( $evn_project_image_url ); ?>" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i></a></div>
									</div>
									<div class="lower-content">
										<h4><a href="<?php echo esc_url( $evn_project_url ); ?>"><?php echo esc_html( $evn_project_title ); ?></a></h4>
										<div class="text"><?php echo wp_kses_post( $evn_project_description ); ?></div>
									</div>
								</div>
								<?php } ?>  
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- project-masonary end -->

		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoProjectMasonary() );
