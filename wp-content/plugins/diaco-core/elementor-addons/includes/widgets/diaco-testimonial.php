<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoTestimonial extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Testimonial';
	}

	public function get_title() {
		return esc_html__( 'Testimonial', 'diaco-core' );
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
				'label' => __( 'Content', 'diaco-core' ),
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
			'sub_title',
			array(
				'label' => __( 'Sub Title', 'diaco-core' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$this->add_control(
			'add_class',
			array(
				'label'   => __( 'Add Class', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'about-page-1', 'diaco-core' ),
			)
		);

		  $repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'testimonial_author',
			array(
				'label' => __( 'Testimonial Author', 'diaco-core' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$repeater->add_control(
			'designation',
			array(
				'label' => __( 'Designation', 'diaco-core' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$repeater->add_control(
			'testimonial_text',
			array(
				'label' => __( 'Testimonial Content', 'diaco-core' ),
				'type'  => Controls_Manager::WYSIWYG,

			)
		);
		$repeater->add_control(
			'testimonial_image',
			array(
				'label'   => __( 'Author Image', 'diaco-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$this->add_control(
			'items1',
			array(
				'label'   => __( 'Repeater List', 'diaco-core' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'list_title'   => __( 'Title #1', 'diaco-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'diaco-core' ),
					),
					array(
						'list_title'   => __( 'Title #2', 'diaco-core' ),
						'list_content' => __( 'Item content. Click the edit button to change this text.', 'diaco-core' ),
					),
				),
			)
		);
		$this->end_controls_section();

	}    protected function render() {
		$settings = $this->get_settings_for_display();

		$title     = $settings['title'];
		$sub_title = $settings['sub_title'];
		$add_class = $settings['add_class'];

		?> 
	<!-- testimonial-section -->
	<section class="testimonial-section <?php echo esc_attr( $add_class ); ?>">
		<div class="container">
			<div class="sec-title">
				<span class="top-title"> <?php echo esc_html( $sub_title ); ?></span>
			   <?php echo $title; ?>
			</div>
			<div class="two-item-carousel owl-carousel owl-theme">
				<?php
				foreach ( $settings['items1'] as $item ) {
					$testimonial_author = $item['testimonial_author'];
					$designation        = $item['designation'];
					$testimonial_text   = $item['testimonial_text'];

					$testimonial_image = ( $item['testimonial_image']['id'] != '' ) ? wp_get_attachment_url( $item['testimonial_image']['id'], 'full' ) : $item['testimonial_image']['url'];
					?>
					 
					<div class="testimonial-block-one">
						<div class="text">
						<?php echo $testimonial_text; ?>
						</div>
						<div class="author">
							<figure class="author-thumb"><img src="<?php echo $testimonial_image; ?>" alt="testimonial image" ></figure>
							<div class="author-info">
								<h5><?php echo $testimonial_author; ?></h5>
								<span><?php echo $designation; ?></span>
							</div>
						</div>
					</div> 
				<?php } ?>  
			</div>
		</div>
	</section>
	<!-- testimonial-section end -->
		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoTestimonial() );
