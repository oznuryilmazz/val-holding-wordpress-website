<?php
if ( ! defined( 'DIACO_THEME_URI' ) ) {
	define( 'DIACO_THEME_URI', get_template_directory_uri() );
}
define( 'DIACO_THEME_DIR', get_template_directory() );
define( 'DIACO_CSS_URL', get_template_directory_uri() . '/css' );
define( 'DIACO_JS_URL', get_template_directory_uri() . '/js' );
define( 'DIACO_FONTS_URL', get_template_directory_uri() . '/fonts/font-awesome/css' );
define( 'DIACO_IMG_URL', DIACO_THEME_URI . '/images/' );
define( 'DIACO_FREAMWORK_DIRECTORY', DIACO_THEME_DIR . '/framework/' );
define( 'DIACO_INC_DIRECTORY', DIACO_THEME_DIR . '/inc/' );
define( 'DIACO_VC_MAP', DIACO_THEME_DIR . '/vc_element/' );


/**
 * Redux framework configuration
*/
require_once DIACO_FREAMWORK_DIRECTORY . 'redux.config.php';

// require_once(DIACO_FREAMWORK_DIRECTORY . "google-fonts.php");

/**
 * Enable support TGM features.
*/

require_once DIACO_FREAMWORK_DIRECTORY . 'plugin-list.php';
require_once DIACO_FREAMWORK_DIRECTORY . 'class-tgm-plugin-activation.php';
require_once DIACO_FREAMWORK_DIRECTORY . 'config-tgm.php';
require_once DIACO_FREAMWORK_DIRECTORY . '/dashboard/class-dashboard.php';

require_once DIACO_INC_DIRECTORY . 'template-tags.php';
require_once DIACO_FREAMWORK_DIRECTORY . 'class-wp-diaco-navwalker.php';
/**
 * Theme option compatibility.
 */



if ( ! function_exists( 'diaco_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function diaco_setup() {
		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Pool Services, use a find and replace
		* to change 'diaco' to the name of your theme in all the template files.
		*/
		load_theme_textdomain( 'diaco', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'diaco' ),
			)
		);
		/*
		* Enable support for custom-background.
		*/
		$defaults = array(
			'default-image'          => '',
			'width'                  => 0,
			'height'                 => 0,
			'flex-height'            => false,
			'flex-width'             => false,
			'uploads'                => true,
			'random-default'         => false,
			'header-text'            => true,
			'default-text-color'     => '',
			'wp-head-callback'       => '',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		);

		add_theme_support( 'custom-header', $defaults );

		$defaults = array(
			'default-color'          => '',
			'default-image'          => '',
			'default-repeat'         => '',
			'default-position-x'     => '',
			'default-attachment'     => '',
			'wp-head-callback'       => '_custom_background_cb',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		);
		add_theme_support( 'custom-background', $defaults );

		/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );

		// Add custom thumb size
		set_post_thumbnail_size( 870, 491, false );
		add_image_size( 'diaco-thumbnail', 770, 350, true );
		add_image_size( 'diaco-blog-home', 570, 350, true );
		add_image_size( 'diaco-thumbnail-grid', 370, 350, true );
		add_image_size( 'diaco-coupon', 570, 310, true );
		add_image_size( 'diaco-gallery-thumbnail', 370, 370, true );
		add_image_size( 'diaco-blog-sidebar', 120, 80, true );
		add_image_size( 'diaco-testimonial', 653, 235, true );
	}
endif;
	add_action( 'after_setup_theme', 'diaco_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function diaco_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'diaco_content_width', 640 );
}

add_action( 'after_setup_theme', 'diaco_content_width', 0 );


/**
 * diacoue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', 'diaco_scripts', 1000 );

function diaco_scripts() {
	wp_enqueue_style( 'bootstrap', DIACO_CSS_URL . '/bootstrap.css', '', null );
	wp_enqueue_style( 'flaticon', DIACO_CSS_URL . '/flaticon.css', '', null );
	wp_enqueue_style( 'owl', DIACO_CSS_URL . '/owl.css', '', null );
	wp_enqueue_style( 'font-awesome-all', DIACO_CSS_URL . '/font-awesome-all.css', '', null );
	wp_enqueue_style( 'fancybox-style', DIACO_CSS_URL . '/jquery.fancybox.min.css', '', null );
	wp_enqueue_style( 'animate', DIACO_CSS_URL . '/animate.css', '', null );
	wp_enqueue_style( 'diaco-style', get_stylesheet_uri() );
	wp_enqueue_style( 'diaco-responsive-style', DIACO_CSS_URL . '/responsive.css', '', null );

	wp_enqueue_script( 'popper', DIACO_JS_URL . '/popper.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'bootstrap', DIACO_JS_URL . '/bootstrap.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'owl-carousel', DIACO_JS_URL . '/owl.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'wow', DIACO_JS_URL . '/wow.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-fancybox', DIACO_JS_URL . '/jquery.fancybox.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'appear', DIACO_JS_URL . '/appear.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'isotope', DIACO_JS_URL . '/isotope.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'parallax', DIACO_JS_URL . '/parallax.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-countTo', DIACO_JS_URL . '/jquery.countTo.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'countdown', DIACO_JS_URL . '/countdown.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'jquery-mCustomScrollbar', DIACO_JS_URL . '/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'diaco-custom', DIACO_JS_URL . '/script.js', array( 'jquery' ), time(), true );
	wp_localize_script( 'diaco-custom', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	if ( ! is_admin() ) {
		global $diaco_options;
		if ( isset( $diaco_options['diaco_maps_key'] ) && ! empty( $diaco_options['diaco_maps_key'] ) ) {
			wp_enqueue_script( 'diaco-google-map', '//maps.googleapis.com/maps/api/js?key=' . $diaco_options['diaco_maps_key'], array(), '', true );
			wp_enqueue_script( 'diaco-map-script', DIACO_JS_URL . '/gmaps.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'diaco-map-helper', DIACO_JS_URL . '/map-helper.js', array( 'jquery' ), '', true );
		}
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
require_once DIACO_THEME_DIR . '/css/custom-style.php';



add_action( 'wp_enqueue_scripts', 'diaco_add_google_font' );

function diaco_add_google_font() {
	$protocol   = is_ssl() ? 'https' : 'http';
	$subsets    = 'latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek,vietnamese';
	$variants   = ':100,100i,200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i';
	$query_args = array(
		'family' => 'Oswald' . $variants . '%7COpen+Sans' . $variants,
		'subset' => $subsets,
	);
	$font_url   = add_query_arg( $query_args, $protocol . '://fonts.googleapis.com/css' );
	wp_enqueue_style( 'diaco-google-fonts', $font_url, array(), null );
}

// end add by tanvir
add_action( 'widgets_init', 'diaco_widgets_init' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function diaco_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog Sidebar', 'diaco' ),
			'id'            => 'left_sideber',
			'description'   => esc_html__( 'Blog sidebar area', 'diaco' ),
			'before_widget' => '<div class="%2$s widget-content widget" id="%1$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="sidebar-title">',
			'after_title'   => '</h3>',
		)
	);
	global $diaco_options;
	$diaco_footer_widget = isset( $diaco_options['diaco_footer_widget'] ) ? $diaco_options['diaco_footer_widget'] : 0;
	if ( $diaco_footer_widget == 1 ) {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Sidebar', 'diaco' ),
				'id'            => 'footer_sideber',
				'description'   => esc_html__( 'Footer sidebar area', 'diaco' ),
				'before_widget' => '<div class="%2$s col-lg-3 col-md-6 col-sm-12 footer-column" id="%1$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

}

function diaco_header_add_query_vars_filter( $vars ) {
	$vars[] = 'header_type';
	return $vars;
}

add_filter( 'query_vars', 'diaco_header_add_query_vars_filter' );

function diaco_add_query_vars_filter( $vars ) {
	 $vars[] = 'blog_type';
	return $vars;
}

add_filter( 'query_vars', 'diaco_add_query_vars_filter' );

// diaco_comments
function diaco_comments( $comment, $args, $depth ) {
	if ( 'div' === $args['style'] ) {
		$tag       = 'div';
		$add_below = 'comment';
	} else {
		$tag       = 'li';
		$add_below = 'div-comment';
	}
	?>
	<<?php echo esc_html( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? 'comment-box' : 'parent comment-box' ); ?> id="comment-<?php comment_ID(); ?>"><?php if ( 'div' != $args['style'] ) { ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
												 <?php
	 }
		?>
	<?php if ( $comment->comment_type != 'trackback' && $comment->comment_type != 'pingback' ) { ?>
		<div class="comment">
	<?php } else { ?>
			<div class="comment yes-ping">
	<?php } ?>
	<?php if ( $comment->comment_type != 'trackback' && $comment->comment_type != 'pingback' ) { ?>
			<div class="author-thumb">
		<?php print get_avatar( $comment, 70, null, null, array( 'class' => array() ) ); ?>
			</div>
	<?php } ?>
			<div class="comment-info clearfix"><strong> <?php echo get_comment_author_link(); ?> </strong>
				<div class="comment-time"><?php comment_time( get_option( 'date_format' ) ); ?></div>
			</div>
			<div class="text">
				<?php comment_text(); ?>
			</div>
			<div class="reply-outer">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'reply_text' => esc_html__( 'Reply ', 'diaco' ) . '<span class="fas fa-angle-right"></span>',
							'depth'      => $depth,
							'max_depth'  => $args['max_depth'],
						)
					)
				);
				?>
			</div>
		</div>
	<?php
}

if ( ! function_exists( 'diaco_body_classes' ) ) {

	function diaco_body_classes( $classes ) {
		$classes[] = 'boxed_wrapper';
		return $classes;
	}
}
add_filter( 'body_class', 'diaco_body_classes' );

function diaco_custom_css() {
	$diaco_custom_inline_style = '';

	if ( function_exists( 'diaco_get_custom_styles' ) ) {
		$diaco_custom_inline_style = diaco_get_custom_styles();
	}

	wp_add_inline_style( 'diaco-style', $diaco_custom_inline_style );
}
add_action( 'wp_enqueue_scripts', 'diaco_custom_css', 1000 );

if ( ! function_exists( 'diaco_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */

	function diaco_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'diaco_theme_register_elementor_locations', array( true ), '2.0', 'diaco_register_elementor_locations' );
		if ( apply_filters( 'diaco_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}

add_action( 'elementor/theme/register_locations', 'diaco_register_elementor_locations' );


