<?php
namespace Diaco;

class Element {

	public function __construct() {

		add_action( 'elementor/widgets/register', array( $this, 'widgets_registered' ) );
	}

	public function widgets_registered() {

		if ( defined( 'DIACO_ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-testimonial.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-team.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-work-block.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-work-tab-slider.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-about-us.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaca-about-agent.php';

			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-partners-logo.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-prallax-video.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-heading.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-image-box.php';

			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-accordion.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-portfolio-project.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-blogs.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-counter.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-project-slider.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-service-slider.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-service-grid.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-price-box.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-service-icon.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-project-masonary.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-project-isotop.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-project-isotop-two.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-map.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-form-on-map.php';
			require_once DIACO_ELEMENTOR_INCLUDES . '/widgets/diaco-timeline.php';

		}
	}

}
