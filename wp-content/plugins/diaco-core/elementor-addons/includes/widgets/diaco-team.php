<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoTeam extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Team';
	}

	public function get_title() {
		return esc_html__( 'Team', 'diaco-core' );
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
				'type'  => Controls_Manager::TEXT,
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
			'team_content',
			array(
				'label'       => __( 'Content', 'diaco-core' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),

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
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'extra_class',
				array(
					'label'   => __( 'Extra Class', 'diaco' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'about-page-1', 'diaco-core' ),
				)
			);
		  $repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'author_name',
			array(
				'label' => __( 'Name', 'diaco-core' ),
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
			'testimonial_image',
			array(
				'label'   => __( 'Author Image', 'diaco-core' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),

			)
		);
		$repeater->add_control(
			'url',
			array(
				'label'         => __( 'Url', 'diaco' ),
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
		$this->end_controls_section();

		$this->start_controls_section(
			'team_list',
			array(
				'label' => __( 'Team List', 'diaco' ),
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

	}
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$title        = $settings['title'];
		$sub_title    = $settings['sub_title'];
		$team_content = $settings['team_content'];
		$show_slider  = $settings['show_slider'];
		$extra_class  = $settings['extra_class'];

		$slider_class = '';

		$slider_parent_class = '';

		if ( 'yes' === $show_slider ) {
			$slider_parent_class = 'team-carousel owl-theme owl-carousel nav-style-one';
		} else {
			$slider_class        = 'col-lg-3 col-md-6 col-sm-12 team-block wow fadeInUp ';
			$slider_parent_class = 'row';
		}

		?>  <!-- team-section -->
	<section class="team-section <?php echo esc_attr( $extra_class ); ?>">
		<div class="container">
			<div class="sec-title">
				<span class="top-title"><?php echo $sub_title; ?></span>
				<?php echo $title; ?>
				<?php echo $team_content; ?>
			</div>
			<div class="inner-content">
				<div class="<?php echo $slider_parent_class; ?>">
		   
		<?php
		foreach ( $settings['items1'] as $item ) {
			$author_name       = $item['author_name'];
			$designation       = $item['designation'];
			$testimonial_image = ( $item['testimonial_image']['id'] != '' ) ? wp_get_attachment_url( $item['testimonial_image']['id'], 'full' ) : $item['testimonial_image']['url'];

			$url = $item['url']['url'];
			?>
			<div class="<?php echo esc_attr( $slider_class ); ?> team-block-one line-overlay">
						<div class="image-box">
							<span class="line"></span>
							<span class="line line-bottom"></span>
							<figure class="image"><img src="<?php echo esc_url( $testimonial_image ); ?>" alt="team image"></figure>
						</div>
						<div class="lower-content">
							<h3><a href="<?php echo esc_url( $url ); ?>"><?php echo $author_name; ?></a></h3>
							<span class="designation"><?php echo $designation; ?></span>
						</div>
					</div> 
			<?php } ?>  
				</div> <!-- nav style -->
			</div>
		</div>
	</section>
	<!-- team-section end -->

		<?php
	}

	protected function content_template() {

	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new \DiacoTeam() );
