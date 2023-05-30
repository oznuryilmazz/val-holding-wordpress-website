<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Diaco
 * @since 1.0.0
 */

get_header();
global $diaco_options;
$diaco_header_image_link = isset($diaco_options['diaco_404_header_image']['url']) ? $diaco_options['diaco_404_header_image']['url'] : '';
$diaco_rotate_text = isset($diaco_options['diaco_404_rotate_text']) ? $diaco_options['diaco_404_rotate_text'] : 0;

?>
<section class="page-title centred image_background" data-image-src="<?php echo esc_url($diaco_header_image_link);?>">
<?php if(!empty($diaco_rotate_text)){ echo '<div class="rotate-text">'.esc_html($diaco_rotate_text).'</div>'; } ?>  
    <div class="container">
        <div class="content-box">
            <h1><?php esc_html_e('Error Page','diaco');?></h1>
        </div>
    </div>
</section>
<section class="error-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 offset-lg-3 error-column">
                <div class="content-box">
                    <h1><?php echo esc_html__( '404' , 'diaco'); ?></h1>
                    <h2><?php echo esc_html__( 'Page Not Found', 'diaco' ); ?></h2>
                    <p><?php echo esc_html__( 'The page you were looking for could not be found. ','diaco');?>
                        <a href="<?php echo esc_url(home_url('/')); ?>"> <?php echo esc_html__('Go to Home', 'diaco'); ?></a>
                    </p>
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
