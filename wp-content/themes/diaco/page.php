<?php
get_header();

global $diaco_options;
$diaco_page_spacing = isset($diaco_options['diaco_page_spacing']) ? $diaco_options['diaco_page_spacing'] : 1;
$diaco_rotate_text =  get_post_meta(get_the_id(), 'diaco_rotate_text', true); 

$diaco_header_image_link =  get_the_post_thumbnail_url(get_the_ID(),'full'); 
$layout_settings = get_post_meta(get_the_id(), 'diaco_page_style', true);
$layout_sidebar = get_post_meta(get_the_id(), 'diaco_page_sidebar', true);
$show_page_breadcumb = get_post_meta(get_the_id(), 'diaco_show_breadcrumb', true);
$show_show_page_title = get_post_meta(get_the_id(), 'diaco_show_page_title', true);

$layout = ($layout_settings) ? $layout_settings : 'full';
$sidebar = ($layout_sidebar) ? $layout_sidebar : 'left_sideber';

if (is_active_sidebar($sidebar)) {
    if ($layout == 'full') {
        $content_class = 'col-lg-12 col-md-12 col-sm-12';
    } else {
        $content_class = 'content-side col-lg-8 col-md-12 col-sm-12';
    }
} else {
    $content_class = 'col-lg-12 col-md-12 col-sm-12';
}
?>
<?php if(isset($show_show_page_title) && $show_show_page_title != "off"){?>
<section class="page-title centred image_background" data-image-src="<?php echo esc_url($diaco_header_image_link);?>">
<?php if(!empty($diaco_rotate_text)){ echo '<div class="rotate-text">'.esc_html($diaco_rotate_text).'</div>'; } ?>  
    <div class="container">
        <div class="content-box">
            <h1><?php the_title();?></h1>
        </div>
    </div>
</section>
<?php } ?>

<?php if($diaco_page_spacing != '0'){?>
<div class="sidebar-page-container page-content">
<?php } ?>
    <div class="container">
        <div class="row">
            <?php
            if (is_active_sidebar($sidebar)) {
                if ($layout == 'left_side') {
                    ?>
                    <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                        <?php
                        if (is_active_sidebar($sidebar)) {
                            dynamic_sidebar($sidebar);
                        }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="<?php echo esc_attr($content_class); ?>">
        <?php
                    while ( have_posts() ) : the_post();

                    ?>
                    <div  id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <?php
                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'diaco' ),
                            'after'  => '</div>',
                        ) );
                        ?>

                    </div>
                <?php

                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;

                    endwhile; // End of the loop.
                    ?>
                    </div>
                    <?php
            if (is_active_sidebar($sidebar)) {
                if ($layout == 'right_side') {
                    ?>
                    <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                        <?php
                        if (is_active_sidebar($sidebar)) {
                            dynamic_sidebar($sidebar);
                        }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
<?php if($diaco_page_spacing != '0'){?>
</div>
<?php } ?>
<?php get_footer();?>