<?php
use Elementor\Utils;

class DiacAboutUs extends \Elementor\Widget_Base {

	public function get_name() {
		return 'diac_about_us';
	}

	public function get_title() {
		return esc_html__( 'Our Store', 'diaco' );
	}

	public function get_icon() {
		return 'eicon-post';
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
			'title',
			array(
				'label' => __( 'Title', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label'       => __( 'Sub Title', 'diaco' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'rows'        => 0,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),
			)
		);
		$this->add_control(
			'count',
			array(
				'label'       => __( 'Count Text', 'diaco' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'rows'        => 0,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),
			)
		);

		$this->add_control(
			'about_content',
			array(
				'label'       => __( 'Story Content', 'diaco' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),
			)
		);
		$this->add_control(
			'url_text',
			array(
				'label'   => __( 'Button Text ', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Discover more about our story', 'diaco' ),
			)
		);
		$this->add_control(
			'url',
			array(
				'label'         => __( 'Button Url', 'diaco' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'plugin-domain' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$this->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$title         = $settings['title'];
		$sub_title     = $settings['sub_title'];
		$count         = $settings['count'];
		$about_content = $settings['about_content'];
		$url_text      = $settings['url_text'];
		$url           = $settings['url']['url'];
		$image_url     = ( $settings['image']['id'] != '' ) ? wp_get_attachment_url( $settings['image']['id'], 'full' ) : $settings['image']['url'];
		?>
		<div class="top-content">
			<div class="row">
				<div class="col-lg-4 col-md-12 col-sm-12 title-column">
					<div class="title-box sec-title wow slideInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
						<span class="top-title"><?php echo $sub_title; ?></span>
						<?php echo $title; ?>
					</div>
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12 content-column">
					<div class="content-box wow slideInUp" data-wow-delay="300ms" data-wow-duration="1500ms">
						<div class="count-text"><?php echo $count; ?></div>
						<div class="text"> <?php echo $about_content; ?></div>
						<a href="<?php echo esc_url( $url ); ?>"><?php echo $url_text; ?></a>
					</div>
				</div>
				<div class="col-lg-4 col-md-12 col-sm-12 image-column">
					<figure class="image-box wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="about image"  >
					</figure>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template() {

	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \DiacAboutUs() );
