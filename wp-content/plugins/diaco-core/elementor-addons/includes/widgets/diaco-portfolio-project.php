<?php
use Elementor\Utils;
use Elementor\Controls_Manager;

class DiacoPortfolioProject extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Portfolio Project';
	}

	public function get_title() {
		return esc_html__( 'Diaco Portfolio Project', 'diaco' );
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
				'label' => __( 'content', 'diaco' ),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'diaco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 0,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$this->add_control(
			'sub_title',
			array(
				'label' => __( 'Sub Title', 'diaco' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$this->add_control(
			'show_bg',
			array(
				'label'        => __( 'Back Backgroud', 'diaco-core' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'your-plugin' ),
				'label_off'    => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->add_control(
			'project_link_text',
			array(
				'label' => __( 'project text', 'diaco' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$this->add_control(
			'project_link',
			array(
				'label'         => __( 'More Link', 'diaco-core' ),
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
			'portfolios',
			array(
				'label' => __( 'Portfolios', 'diaco' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'diaco' ),
				'type'  => Controls_Manager::ICON,
			)
		);

		$repeater->add_control(
			'project_name',
			array(
				'label' => __( 'Project Name', 'diaco' ),
				'type'  => Controls_Manager::TEXT,

			)
		);
		$repeater->add_control(
			'project_details',
			array(
				'label'       => __( 'Project Details', 'diaco' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'Default description', 'diaco-core' ),
				'placeholder' => __( 'Type your description here', 'diaco-core' ),

			)
		);
		$repeater->add_control(
			'image',
			array(
				'label'   => __( 'Image', 'diaco' ),
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
		$settings              = $this->get_settings_for_display();
		$title                 = $settings['title'];
		$show_bg               = $settings['show_bg'];
		$sub_title             = $settings['sub_title'];
		$project_link_text     = $settings['project_link_text'];
		$project_link          = $settings['project_link']['url'];
		$project_link_target   = $settings['project_link']['is_external'] ? 'target="_blank"' : '';
		$project_link_nofollow = $settings['project_link']['nofollow'] ? 'rel="nofollow"' : '';

		$bg_class = '';
		if ( $show_bg == 'yes' ) {
			$bg_class = 'black-bg';
		} else {
			$bg_class = 'single-team-page';
		}
		?>  
	   <section class="project-style-two  <?php echo esc_attr( $bg_class ); ?>">
		<div class="project-title wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms"><?php echo $sub_title; ?></div>
		<div class="container">
			<div class="sec-title">
				<span class="top-title"><?php echo $sub_title; ?></span>
				<?php echo $title; ?>
			</div>

		<?php
		$num = 1;
		$i   = 0;
		foreach ( $settings['items1'] as $item ) {
			$i++;
			$icon            = $item['icon'];
			$project_name    = $item['project_name'];
			$project_details = $item['project_details'];
			 $image_url      = ( $item['image']['id'] != '' ) ? wp_get_attachment_url( $item['image']['id'], 'full' ) : $item['image']['url'];
			   $url          = $item['url']['url'];
				$target      = $item['url']['is_external'] ? 'target="_blank"' : '';
				$nofollow    = $item['url']['nofollow'] ? 'rel="nofollow"' : '';
			?>
		   
		 <div class="project-block-two line-overlay wow fadeInLeft" data-wow-delay="00ms" data-wow-duration="1500ms">
				<div class="row">
					   <?php if ( $i % 2 == 0 ) { ?> 
				  <div class="col-lg-5 col-md-12 col-sm-12 content-column">
						<div class="content-box">
							<div class="top-content">
								<div class="icon-box"><i class="<?php echo $icon; ?>"></i></div>
								<div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
								<h2><a href="<?php echo esc_url( $url ); ?>"><?php echo $project_name; ?></a></h2>
							</div>
							<div class="text"><?php echo $project_details; ?></div>
							<div class="link"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html_e( 'View Project', 'diaco-core' ); ?></a></div>
						</div>
					</div>
					<div class="col-lg-7 col-md-12 col-sm-12 image-column">
						<figure class="image-box">
							<span class="line"></span>
							<span class="line line-bottom"></span>
							<img src="<?php echo $image_url; ?>" alt="portfolio image">
						</figure>
					</div>
				<?php } else { ?>
				  <div class="col-lg-7 col-md-12 col-sm-12 image-column">
						<figure class="image-box">
							<span class="line"></span>
							<span class="line line-bottom"></span>
							<img src="<?php echo $image_url; ?>" alt="portfolio image">
						</figure>
					</div>
					<div class="col-lg-5 col-md-12 col-sm-12 content-column">
						<div class="content-box">
							<div class="top-content">
								<div class="icon-box"><i class="<?php echo $icon; ?>"></i></div>
								<div class="count-text"><?php echo sprintf( '%02d', $num ); ?></div>
								<h2><a href="<?php echo esc_url( $url ); ?>"><?php echo $project_name; ?></a></h2>
							</div>
							<div class="text"><?php echo $project_details; ?></div>
							<div class="link"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html_e( 'View Project', 'diaco-core' ); ?></a></div>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
			<?php
			$num++; }

		if ( ! empty( $project_link ) ) {
			?>
	  
	<div class="view-btn centred"><a href="<?php echo esc_url( $project_link ); ?>">
			<?php
			if ( $project_link_text != '' ) :
				echo $project_link_text;
			else :
				echo esc_html__( 'View All Projects', 'diaco-core' );
			endif;
			?>
	</a></div>
	  <?php } ?>

  
</div>

</section>
		<?php
	}

	protected function content_template() {

	}
}

  \Elementor\Plugin::instance()->widgets_manager->register( new \DiacoPortfolioProject() );
