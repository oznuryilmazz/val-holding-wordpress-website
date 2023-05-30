<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoTimeline extends \Elementor\Widget_Base {

	public function get_name() {
		return 'diaco_timeline';
	}

	public function get_title() {
		return esc_html__( 'Diaco Timeline', 'diaco-core' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco-core' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'genaral',
			array(
				'label' => __( 'Genaral', 'diaco-core' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Educa<span>tion.</span>', 'diaco-core' ),

			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'experience', 'diaco-core' ),

			)
		);
		$this->add_control(
			'desc',
			array(
				'label'       => __( 'Desc', 'diaco-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),

			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'date',
			array(
				'label'   => __( 'Date', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '2014-2016', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'univer_name',
			array(
				'label'   => __( 'University name', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'BSS University, USA', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'designation',
			array(
				'label'   => __( 'Designation', 'diaco-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Interior Designer', 'diaco-core' ),

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
		  $settings = $this->get_settings_for_display();
		  $title    = $settings['title'];
		  $subtitle = $settings['subtitle'];
		  $desc     = $settings['desc'];

		?> <section class="education-section">
		<div class="container">
			<div class="sec-title">
				<span class="top-title"><?php echo esc_html( $subtitle ); ?></span>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<p><?php echo wp_kses_post( $desc ); ?></p>
			</div>
			<div class="row">
				<div class="col-xl-5 col-lg-12 col-md-12 offset-lg-4 inner-column">
					<div class="inner-content">
					 <?php
						foreach ( $settings['items1'] as $item ) {
							$date        = $item['date'];
							$univer_name = $item['univer_name'];
							$designation = $item['designation'];
							?>
					   <div class="single-item">
							<div class="date"><?php echo esc_html( $date ); ?></div>
							<div class="content-box">
								<div class="text"><?php echo esc_html( $univer_name ); ?></div>
								<span><?php echo esc_html( $designation ); ?></span>
							</div>
						</div> 
						<?php } ?>  
					   
					</div>
				</div>
			</div>
		</div>
	</section> 
		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoTimeline() );

