<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class WorkBlock extends \Elementor\Widget_Base {

	public function get_name() {
		return 'WorkBlock';
	}

	public function get_title() {
		return esc_html__( 'Work Block', 'diaco' );
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
			'readmore',
			array(
				'label' => __( 'Read more text', 'diaco' ),
				'type'  => Controls_Manager::TEXT,
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
			'work_content',
			array(
				'label'       => __( 'Work Content', 'diaco' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);

			$repeater->add_control(
				'action_link',
				array(
					'label'         => __( 'Url', 'diaco' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => __( 'https://your-link.com', 'diaco-core' ),
					'show_external' => true,
					'default' => array(
						'url'         => 'http://',
						'is_external' => '',
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

	}    protected function render() {
		$settings = $this->get_settings_for_display();

		?>   <div class="lower-content">
				<div class="row">
				
		<?php
		$num = 1;
		foreach ( $settings['items1'] as $item ) {
			$readmore        = $settings['readmore'];
			$title        = $item['title'];
			//$count        = $item['count'];
			$work_content = $item['work_content'];
			$link     	  = $item['action_link'];	
			$url      	  = $link['url'];
			$target   	  = $link['is_external'] ? 'target="_blank"' : '';
			?>
	  <div class="col-lg-4 col-md-6 col-sm-12 work-column wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
		  <div class="work-block-one">
			  <h4><?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) :
									?>
					target="<?php echo $target; ?>" 
									<?php
								endif;
								?>
								><?php } ?> <?php echo $title; ?></a></h4>
			  <div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
			  <div class="text"><?php echo $work_content; ?></div>
			  <div class="link">
				  <?php
							if ( $url ) {
								?>
							<a href="<?php echo $url; ?>"
								<?php
								if ( ! ( empty( $target ) ) ) :
									?>
					target="<?php echo $target; ?>" 
									<?php
								endif;
								?>
								><?php esc_html_e( 'Read More', 'diaco-core' ); ?></a></div>
							<?php } ?>
		  </div>
	  </div> 
		 <?php $num++; } ?>  
				</div>
  </div>
		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \WorkBlock() );
