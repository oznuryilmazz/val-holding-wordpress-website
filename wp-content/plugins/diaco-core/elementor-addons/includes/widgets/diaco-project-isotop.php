<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoProjectIsotop extends \Elementor\Widget_Base {

	public function get_name() {
		return 'DiacoProjectIsotop';
	}

	public function get_title() {
		return esc_html__( 'Diaco Project Isotop', 'diaco' );
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
				'label' => __( 'Title', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::WYSIWYG,
			)
		);
		$this->add_control(
			'select_style',
			array(
				'label'   => esc_html__( 'Select Style', 'plugin-name' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'2' => esc_html__( 'Grid', 'plugin-name' ),
					'3' => esc_html__( 'Masonry', 'plugin-name' ),

				),
				'default' => esc_html__( '2', 'plugin-name' ),
			)
		);

		$this->add_control(
			'column_select',
			array(
				'label'     => esc_html__( 'Column Select', 'plugin-name' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'2' => esc_html__( 'Two', 'plugin-name' ),
					'3' => esc_html__( 'Three', 'plugin-name' ),
					'4' => esc_html__( 'Four', 'plugin-name' ),

				),
				'default'   => esc_html__( '2', 'plugin-name' ),
				'condition' => array(
					'select_style' => '2',
				),
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
			'project_cat',
			array(
				'label'   => __( 'Project Category', 'diaco-core' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Architecture', 'diaco' ),

			)
		);
		$repeater->add_control(
			'project_category',
			array(
				'label' => __( 'Category Isotop', 'diaco-core' ),
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
			'masnry_column_select',
			array(
				'label'   => esc_html__( 'Masonry Column Select', 'plugin-name' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'2' => esc_html__( 'Big Column', 'plugin-name' ),
					'3' => esc_html__( 'Small Column', 'plugin-name' ),

				),
				'default' => esc_html__( '2', 'plugin-name' ),
			)
		);

		$repeater->add_control(
			'pro_url',
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

		$settings      = $this->get_settings_for_display();
		$items1        = $settings['items1'];
		$column_select = $settings['column_select'];
		$select_style  = $settings['select_style'];
		$title         = $settings['title'];

		?>  



   <!-- project-section -->

		<?php if ( $select_style == '3' ) { ?>
   <section class="project-section project-page project-page-10">
		<div class="project-title wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms"><?php esc_html_e( 'Project', 'diaco-core' ); ?></div>
  <?php } else { ?>
	<section class="project-section project-page project-page-03">
  <?php } ?>

		  

		<div class="container">
			<?php echo wp_kses_post( $title ); ?>
			<div class="sortable-masonry">
				<!--Filter-->

				<?php
				$category_arr       = array();
				$category_arr_class = array();
				foreach ( $items1 as $key => $item ) {
					$cat                        = $item['project_category'];
					$child_categories_ex        = explode( ',', $cat );
					$child_categories           = str_replace( ',', ' ', $cat );
					$category_arr_class[ $key ] = strtolower( $child_categories );
					foreach ( $child_categories_ex as $child_category ) {
						$category_arr[] = strtolower( $child_category );
					}
				}

				if ( $title != '' ) {
					$class = '';
				} else {
					$class = 'centred';
				}

				?>
				<div class="filters">
					<ul class="filter-tabs filter-btns <?php echo esc_attr( $class_ ); ?> clearfix">
						<li class="active filter" data-role="button" data-filter=".all"><?php esc_html_e( 'All', 'diaco-core' ); ?></li>
						<?php
						$category_arr = array_unique( $category_arr );
						foreach ( $category_arr as $category ) {
							$category_slug = str_replace( ' ', '_', $category );
							echo '<li class="filter" data-role="button" data-filter=".' . $category_slug . '">' . $category . '</li>';
						}
						?>
					</ul>
				</div>
				<div class="items-container row clearfix">

				<?php

				foreach ( $items1 as $key => $item ) {
					  $project_title = $item['project_title'];

					  $project_image_url = ( $item['project_image']['id'] != '' ) ? wp_get_attachment_url( $item['project_image']['id'], 'full' ) : $item['project_image']['url'];

					  $project_url          = $item['pro_url']['url'];
					  $project_cat          = $item['project_cat'];
					  $masnry_column_select = $item['masnry_column_select'];
					  $project_category     = $item['project_category'];
					  $project_cat2         = str_replace( ' ', '_', $project_category );
					  $project_cat2         = str_replace( ',', ' ', $project_cat2 );
					  $project_cat2         = strtolower( $project_cat2 );
					?>
					<?php

					if ( $masnry_column_select == '3' ) {
						$masnry_column = 'small-column';
					} else {
						$masnry_column = 'big-column';
					}

					if ( $select_style == '3' ) {
						?>
					  <div class="masonry-item <?php echo esc_attr( $masnry_column ); ?> all <?php echo esc_attr( $project_cat2 ); ?>">
						<div class="project-block-one">
							<div class="inner-box">
								<figure class="image-box"><a href="<?php echo esc_url( $project_url ); ?>">
									<img src="<?php echo esc_url( $project_image_url ); ?>" alt="project image"></a>
								</figure>
								<div class="caption-box"><h4><?php echo esc_html( $project_title ); ?></h4></div>
								<div class="text"><?php echo esc_html( $project_cat ); ?></div>
								<div class="icon-box">
								  <a href="<?php echo esc_url( $project_image_url ); ?>" class="lightbox-image" data-fancybox="gallery">
									<i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i>
								  </a>
								</div>
							</div>
						</div>
					</div>
						<?php
					} else {
						if ( $column_select == '3' ) {
							?>
							  <div class="col-lg-4 col-md-6 col-sm-12 masonry-item small-column all <?php echo esc_attr( $project_cat2 ); ?>">
							<?php } elseif ( $column_select == '4' ) { ?>
							  <div class="col-lg-3 col-md-6 col-sm-12 masonry-item small-column all <?php echo esc_attr( $project_cat2 ); ?>">
							<?php } else { ?>
							  <div class="col-lg-6 col-md-6 col-sm-12 masonry-item small-column all <?php echo esc_attr( $project_cat2 ); ?>">
								<?php
							}

							?>
						<div class="project-block-one line-overlay">
							<div class="inner-box">
								<div class="image-content">
									<figure class="image-box">
										<span class="line"></span>
										<span class="line line-bottom"></span><a href="<?php echo esc_url( $project_url ); ?>">
										<img src="<?php echo esc_url( $project_image_url ); ?>" alt="project image"></a>
									</figure>
									<div class="icon-box"><a href="<?php echo esc_url( $project_image_url ); ?>" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-expanding-two-opposite-arrows-diagonal-symbol-of-interface"></i></a></div>
								</div>
								<div class="lower-content">
									<h4><a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_title ); ?></a></h4>
									<span><?php echo esc_html( $project_cat ); ?></span>
								</div>
							</div>
						</div>
					</div>
							  <?php
					}
				}

				?>
					 
	  
		
		
				</div>
			</div>
		</div>
	</section>
	<!-- project-section end -->

		<?php

	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoProjectIsotop() );
