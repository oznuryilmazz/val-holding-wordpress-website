<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;

class DiacoCounter extends Widget_Base {

	public function get_name() {
		return 'counter';
	}

	public function get_title() {
		return esc_html__( 'Counter', 'diaco-core' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'diaco' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'counter_section',
			array(
				'label' => esc_html__( 'Counter', 'diaco-core' ),
			)
		);

		$this->add_control(
			'countertex_tabs_tab',
			array(
				'type'      => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default'   => array(
					array( 'tab_title' => esc_html__( 'Counter 1', 'diaco-core' ) ),
					array( 'tab_title' => esc_html__( 'Counter 2', 'diaco-core' ) ),
					array( 'tab_title' => esc_html__( 'Counter 3', 'diaco-core' ) ),
				),
				'fields'    => array(
					array(
						'name'    => 'tab_title',
						'label'   => esc_html__( 'Tab Title', 'solustrid-core' ),
						'type'    => Controls_Manager::TEXT,
						'default' => esc_html__( 'Tab Title', 'solustrid-core' ),
					),
					array(
						'name'    => 'counter_text',
						'label'   => esc_html__( 'Counter Text', 'diaco-core' ),
						'type'    => Controls_Manager::TEXT,
						'default' => '25',
					),
					array(
						'name'  => 'unit_text',
						'label' => esc_html__( 'Unit Text', 'diaco-core' ),
						'type'  => Controls_Manager::TEXT,
					),
					array(
						'name'    => 'counter_title',
						'label'   => esc_html__( 'Counter Title', 'diaco-core' ),
						'type'    => Controls_Manager::TEXT,
						'default' => 'Industries Served',
					),
				),
			)
		);

		$this->add_control(
			'extra_class',
			array(
				'label' => esc_html__( 'Extra Class', 'diaco-core' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings();
		$extra_class = $settings['extra_class'];
		?>
		<!-- fact-counter -->
		<section class="fact-counter centred <?php echo esc_attr( $extra_class ); ?>">
				<div class="container">
					<div class="row">
					<?php foreach ( $settings['countertex_tabs_tab'] as $tab ) { ?>
						<div class="col-lg-3 col-md-6 col-sm-12 counter-column">
							<div class="counter-block-one wow slideInUp" data-wow-delay="400ms" data-wow-duration="1500ms">
								<div class="count-outer count-box">
									<span class="count-text" data-speed="1500"  data-stop="<?php echo esc_attr( $tab['counter_text'] ); ?>
										"></span>
									<?php
									if ( $tab['unit_text'] ) {
										echo '<span>' . wp_kses_post( $tab['unit_text'] ) . '</span>';
									}
									?>
								</div>
								<div class="text"><h3> <?php echo wp_kses_post( $tab['counter_title'] ); ?></h3></div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</section>
			<!-- fact-counter end -->
		<?php
	}

}

Plugin::instance()->widgets_manager->register( new DiacoCounter() );
