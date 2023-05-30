<?php
use Elementor\Utils;

class DiacoProjectIsotopTwo extends \Elementor\Widget_Base {

	public function get_name() {
		return 'DiacoProjectIsotopTwo';
	}

	public function get_title() {
		return esc_html__( 'Diaco Project Isotop Two', 'diaco' );
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
			'add_class',
			array(
				'label' => esc_html__( 'Add Class', 'plugin-name' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
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
			'project_desc',
			array(
				'label' => __( 'Desc', 'diaco-core' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);
		$repeater->add_control(
			'project_number',
			array(
				'label' => __( 'Project Number', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'projecticon',
			array(
				'label' => __( 'Project Icon', 'diaco' ),
				'type'  => \Elementor\Controls_Manager::ICON,
			)
		);
		$repeater->add_control(
			'project_category',
			array(
				'label' => __( 'Category', 'diaco-core' ),
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

	}
	protected function render() {

		$settings = $this->get_settings_for_display();
		$items1   = $settings['items1'];

		?>  



   <!-- project-section -->
   <section class="project-section project-page project-page-06">
		<div class="container">
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
				?>
				<div class="filters">
					<ul class="filter-tabs filter-btns centred clearfix">
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
				$i = 0;
				foreach ( $items1 as $key => $item ) {
					  $i++;
					  $project_title     = $item['project_title'];
					  $project_image_url = ( $item['project_image']['id'] != '' ) ? wp_get_attachment_url( $item['project_image']['id'], 'full' ) : $item['project_image']['url'];
					  $project_url       = $item['url']['url'];
					  $project_category  = $item['project_category'];
					  $project_desc      = $item['project_desc'];
					  $project_number    = $item['project_number'];
					  $projecticon       = $item['projecticon'];
					  $project_category  = str_replace( ' ', '_', $project_category );
					  $project_category  = str_replace( ',', ' ', $project_category );
					  $project_category  = strtolower( $project_category );
						// $category_arr_class[ $key ]
					?>
  
					<?php if ( $i % 2 == 0 ) { ?>
					<div class="col-lg-12 col-md-12 col-sm-12 masonry-item small-column all <?php echo esc_attr( $project_category ); ?>">
						<div class="project-block-two line-overlay">
							<div class="row">
							<div class="col-lg-5 col-md-12 col-sm-12 content-column">
									<div class="content-box">
										<div class="top-content">
										<?php if ( $projecticon ) { ?>
											<div class="icon-box"><i class="<?php echo esc_attr( $projecticon ); ?>"></i></div>
											<?php } ?>
											<?php if ( $project_number != '' ) { ?>
											<div class="count-text"><?php echo esc_html( $project_number ); ?></div>
											<?php } ?>
											<h2><a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_title ); ?></a></h2>
										</div>
										<div class="text"><?php echo esc_html( $project_desc ); ?></div>
										<div class="link"><a href="<?php echo esc_url( $project_url ); ?>"><?php esc_html_e( 'View Project', 'diaco-core' ); ?></a></div>
									</div>
								</div>
								<div class="col-lg-7 col-md-12 col-sm-12 image-column">
									<figure class="image-box">
										<span class="line"></span>
										<span class="line line-bottom"></span>
										<img src="<?php echo esc_url( $project_image_url ); ?>" alt="project image">
									</figure>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
				  <div class="col-lg-12 col-md-12 col-sm-12 masonry-item small-column all <?php echo esc_attr( $project_category ); ?>">
						<div class="project-block-two line-overlay">
							<div class="row">
								<div class="col-lg-7 col-md-12 col-sm-12 image-column">
									<figure class="image-box">
										<span class="line"></span>
										<span class="line line-bottom"></span>
										<img src="<?php echo esc_url( $project_image_url ); ?>" alt="project image">
									</figure>
								</div>
								<div class="col-lg-5 col-md-12 col-sm-12 content-column">
									<div class="content-box">
										<div class="top-content">
											<?php if ( $projecticon ) { ?>
											<div class="icon-box"><i class="<?php echo esc_attr( $projecticon ); ?>"></i></div>
											<?php } ?>
											<?php if ( $project_number != '' ) { ?>
											<div class="count-text"><?php echo esc_html( $project_number ); ?></div>
											<?php } ?>
											<h2><a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_title ); ?></a></h2>
										</div>
										<div class="text"><?php echo esc_html( $project_desc ); ?></div>
										<div class="link"><a href="<?php echo esc_url( $project_url ); ?>"><?php esc_html_e( 'View Project', 'diaco-core' ); ?></a></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
					<?php } ?> 
	  
		
		
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

\Elementor\Plugin::instance()->widgets_manager->register( new \DiacoProjectIsotopTwo() );
