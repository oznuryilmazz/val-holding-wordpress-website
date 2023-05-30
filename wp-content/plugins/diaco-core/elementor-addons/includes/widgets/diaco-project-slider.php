<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoProjectSlider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'diacoprojectslider';
	}

	public function get_title() {
		return esc_html__( 'Diaco Project Slider', 'diaco' );
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
			'project_style',
			array(
				'label'   => __( 'Project Style', 'dico-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'one',
				'options' => array(
					'one' => __( 'Style One', 'dico-core' ),
					'two' => __( 'Style Two', 'dico-core' ),

				),

			)
		);
		$this->add_control(
			'animate_title',
			array(
				'label'   => __( 'Animate Title', 'diaco-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Project', 'diaco-core' ),
			)
		);
		$this->add_control(
			'show_content',
			array(
				'label'        => __( 'Show Description', 'plugin-domain' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'project_style' => 'two',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'     => __( 'Title', 'diaco-core' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'condition' => array(
					'show_content'  => 'yes',
					'project_style' => 'two',
				),
			)
		);
		$this->add_control(
			'project_description',
			array(
				'label'       => __( 'Description', 'diaco-core' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),
				'condition'   => array(
					'show_content'  => 'yes',
					'project_style' => 'two',
				),
			)
		);
		$this->add_control(
			'add_class',
			array(
				'label' => __( 'Add Class', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::TEXT,

			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'project_type',
			array(
				'label' => __( 'Project Type', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,

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
			'project_image_column',
			array(
				'label'   => esc_html__( 'Number of Column', 'diaco-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'3'  => esc_html__( '3', 'diaco-core' ),
					'4'  => esc_html__( '4', 'diaco-core' ),
					'5'  => esc_html__( '5', 'diaco-core' ),
					'6'  => esc_html__( '6', 'diaco-core' ),
					'12' => esc_html__( '12', 'diaco-core' ),
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
		$this->end_controls_section();

		$this->start_controls_section(
			'project_list',
			array(
				'label' => __( 'Project List', 'diaco' ),
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

		$this->start_controls_section(
			'button',
			array(
				'label' => __( 'button', 'diaco' ),
			)
		);
		$this->add_control(
			'button_title',
			array(
				'label'   => __( 'Title', 'diaco' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Load More', 'diaco' ),

			)
		);

		$this->add_control(
			'more_button',
			array(
				'label'         => __( 'More Button', 'diaco' ),
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
		$this->end_controls_section();
	}
	protected function render() {

		$settings          = $this->get_settings_for_display();
		$more_button       = $settings['more_button']['url'];
		$more_button_title = $settings['button_title'];
		$project_style     = $settings['project_style'];
		$add_class         = $settings['add_class'];
		$animate_title     = $settings['animate_title'];

		if ( 'one' == $project_style ) {
			?>   <!-- project-section -->
	<section class="project-section <?php echo esc_attr( $add_class ); ?>">
			<?php if ( $animate_title ) { ?>
		<div class="project-title wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms"><?php echo esc_html( $animate_title ); ?></div>
		<?php } ?>
		<div class="container-fluid">
			<div class="project-carousel owl-theme owl-carousel">
			<?php
			foreach ( $settings['items1'] as $item ) {
				   $title = $item['title'];

					 $project_type = $item['project_type'];

					 $project_image = ( $item['project_image']['id'] != '' ) ? wp_get_attachment_url( $item['project_image']['id'], 'full' ) : $item['project_image']['url'];

					  $url = $item['url']['url'];
				?>
					  
			 <div class="project-block-one">
					<div class="inner-box">
						<figure class="image-box"><a href="<?php echo esc_url( $url ); ?>"><img src="<?php echo esc_url( $project_image ); ?>" alt="project image"></a></figure>
						<div class="caption-box"><h4><?php echo esc_html( $title ); ?></h4></div>
						<div class="text"><?php echo esc_html( $project_type ); ?></div>
						<div class="icon-box"><a href="<?php echo esc_url( $project_image ); ?>" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i></a></div>
					</div>
				</div> <?php } ?>  

			</div>
			 
			<?php if ( ! empty( $more_button ) && ( ! empty( $more_button_title ) ) ) { ?>
			<div class="load-btns centred"><a href="<?php echo esc_url( $more_button ); ?>"><?php echo esc_html( $more_button_title ); ?></a></div>
			<?php } ?>
		</div>
	</section>
	<!-- project-section end -->
			<?php
		} else {

			$title               = $settings['title'];
			$project_description = $settings['project_description'];
			?>
	<!-- project-section -->
<section class="recent-project <?php echo esc_attr( $add_class ); ?>">
			<?php if ( $animate_title ) { ?>
		<div class="project-title wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms"><?php echo esc_html( $animate_title ); ?></div>
		<?php } ?>
		<div class="container">
			<?php if ( $title || $project_description ) { ?>
			<div class="sec-title">
				<span class="top-title"><?php echo esc_html( $title ); ?></span>
				<?php echo wp_kses_post( $project_description ); ?>
			</div>
		  <?php } ?>
	<div class="row">
			<?php
			foreach ( $settings['items1'] as $item ) {

				$columnClass          = '';
				$project_image_column = $item['project_image_column'];

				switch ( $project_image_column ) {
					case '3':
						$columnClass = 'col-lg-3 col-md-6 col-sm-12 ';
						break;
					case '4':
						$columnClass = 'col-lg-4 col-md-12 col-sm-12 ';
						break;
					case '5':
						$columnClass = 'col-lg-5 col-md-6 col-sm-12  ';
						break;
					case '6':
						$columnClass = 'col-lg-6 col-md-12 col-sm-12 ';
						break;
					case '12':
						$columnClass = 'col-lg-12 col-md-12 col-sm-12 ';
						break;
					default:
						$columnClass = 'col-lg-3 col-md-6 col-sm-12 ';
				}

				$title = $item['title'];

				$project_type = $item['project_type'];

				$project_image = ( $item['project_image']['id'] != '' ) ? wp_get_attachment_url( $item['project_image']['id'], 'full' ) : $item['project_image']['url'];

				$url = $item['url']['url'];
				?>
			  
			   <div class="<?php echo esc_attr( $columnClass ); ?> project-block">
		
			 <div class="project-block-one">
					<div class="inner-box">
						<figure class="image-box"><a href="<?php echo esc_url( $url ); ?>"><img src="<?php echo esc_url( $project_image ); ?>" alt="project image"></a></figure>
						<div class="caption-box"><h4><?php echo esc_html( $title ); ?></h4></div>
						<div class="text"><?php echo esc_html( $project_type ); ?></div>
						<div class="icon-box"><a href="<?php echo esc_url( $project_image ); ?>" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i></a></div>
					</div>
				</div>
				</div>
				<?php } ?>  

			</div>
			 
			<?php if ( ! empty( $more_button ) && ( ! empty( $more_button_title ) ) ) { ?>
			<div class="load-more centred"><a href="<?php echo esc_url( $more_button ); ?>"><?php echo esc_html( $more_button_title ); ?></a></div>
			<?php } ?>
		</div>
	</section>
	<!-- project-section end -->
			<?php
		}
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoProjectSlider() );
