<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoAccordion extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Diaco Accordion';
	}

	public function get_title() {
		return esc_html__( 'Diaco Accordion', 'diaco' );
	}

	public function get_icon() {
		return '';
	}

	public function get_categories() {
		return array( 'diaco' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'accordion',
			array(
				'label' => __( 'Accordion', 'diaco' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'diaco' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'diaco' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'plugin-domain' ),
				'placeholder' => __( 'Type your description here', 'plugin-domain' ),

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

	}    protected function render() {
		$settings = $this->get_settings_for_display();

		?>
		<div class="inner-box">
			<!--Accordion Box-->
			<ul class="accordion-box">
					<!--Block-->
				<?php
				$i = 0;
				foreach ( $settings['items1'] as $item ) {
					$title                = $item['title'];
					$description          = $item['description'];
					$active_class         = '';
					$active_content_class = '';
					if ( $i == 0 ) {
						$active_class         = 'active';
						$active_content_class = 'current';
					} else {
						$active_class         = '';
						$active_content_class = '';
					}
					?>
					<li class="accordion block">
						<div class="acc-btn <?php echo $active_class; ?>">
							<h3><?php echo $title; ?></h3>
						</div>
						<div class="acc-content <?php echo $active_content_class; ?>">
							<div class="content">
							<?php echo $description; ?>
								
							</div>
						</div>
					</li> <?php $i++; } ?>  
				  <!--Block-->
				</ul>
		</div> 
		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoAccordion() );
