<?php 
get_header();

global $diaco_options;
$diaco_header_image_link = isset($diaco_options['diaco_blog_header_image']['url']) ? $diaco_options['diaco_blog_header_image']['url'] : '';
$diaco_rotate_text = isset($diaco_options['diaco_blog_rotate_text']) ? $diaco_options['diaco_blog_rotate_text'] : 0;

$diaco_page_spacing = isset($diaco_options['diaco_page_spacing']) ? $diaco_options['diaco_page_spacing'] : 1;

if($diaco_page_spacing == 1){
    $diaco_page_spacing = 'sidebar-page-container post-content-unit';
}else{
    $diaco_page_spacing = 'sidebar-page-container post-content';
}
$diaco_blog_style = get_query_var('blog_type');

if (!$diaco_blog_style) {
    $diaco_blog_style = $diaco_options['diaco_blog_style'];
}
?>

    <!-- page-title -->
    <section class="page-title centred image_background" data-image-src="<?php echo esc_url($diaco_header_image_link);?>">
    <?php if(!empty($diaco_rotate_text)){ echo '<div class="rotate-text">'.esc_html($diaco_rotate_text).'</div>'; } ?>  
        <div class="container">
            <div class="content-box">
            <?php if ( have_posts() ) : ?>
                <h1><?php printf( esc_html__( 'Search Results for: %s', 'diaco' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
                <?php else : ?>
                <h1><?php esc_html_e( 'Nothing Found', 'diaco' ); ?></h1>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <!-- page-title end -->
        
  <!-- blog-classic -->
  <section class="<?php echo esc_attr($diaco_page_spacing);?>">
        <div class="container">
            <div class="row">
            <?php if($diaco_blog_style !='3'){ ?>
                <?php if (is_active_sidebar('left_sideber')) { ?>
                <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <?php }else{ ?>
                    <div class="col-lg-12 col-md-12 col-sm-12 content-side">
               <?php } ?>
                    <div class="blog-classic-content">
                <?php }   ?>
 
                <?php if ( have_posts() ) : ?>
                    <?php 
                        /* Start the Loop */
                     while ( have_posts() ) : the_post();
                    ?>
                        <?php if($diaco_blog_style =='3'){ ?>
                            <div class="col-lg-4 col-md-6 col-sm-12 news-block wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
                                <div class="news-block-one">
                                    <?php diaco_post_thumbnail(); ?>
                                    <div class="lower-content">
                                
                                        <ul class="post-info">
                                            <li><?php diaco_posted_on();?></li>
                                            <?php diaco_entry_footer();?>
                                        </ul>
                                        <h4 class="post-title"><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h4>
                                    </div>
                                </div>
                            </div> 
                            <?php }else{ ?>
                                        <div class="news-block-one">
                                            <?php if($diaco_blog_style != '2'){
                                                 diaco_post_thumbnail();
                                                }
                                            ?>
                                            <div class="lower-content">
                                            <?php if (is_sticky()) { ?>
                                                <div class="sticky_post_icon" title="<?php esc_attr_e('Sticky Post', 'diaco') ?>"><span class="fas fa-thumbtack"></span></div>
                                            <?php } ?>
                                                <ul class="post-info">
                                                    <li><?php diaco_posted_on();?></li>
                                                    <?php diaco_entry_footer();?>
                                                </ul>
                                                <h4 class="post-title"><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h4>
                                                <div class="text">
                                                <?php 
                                                    the_excerpt();
                                                    wp_link_pages(
                                                        array(
                                                            'before' => '<div class="page-links post-links">',
                                                            'after' => '</div>',
                                                        )
                                                    );
                                                ?>    
                                                </div>
                                                <div class="link"><a href="<?php esc_url(the_permalink()); ?>"><?php esc_html_e('Read More','diaco');?></a></div>
                                            </div>
                                        </div>
                            <?php } ?>
                        <?php  
                                endwhile; 

                            else :

                                get_template_part( 'template-parts/content', 'none' );

                            endif; 
                           
                            the_posts_pagination(array(
                                'mid_size' => 2,
                                'prev_text' => '<span class="fa fa-angle-left"></span>',
                                'next_text' => '<span class="fa fa-angle-right"></span>'
                            ));
                                                   
                    ?>
                <?php if($diaco_blog_style !='3'){ ?>
                    </div>
                </div>
                <?php } ?>
                <?php 
                if($diaco_blog_style != '3'){ 
                   if (is_active_sidebar('left_sideber')) {
                ?>
                <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                    <?php get_sidebar();?>
                </div>
                <?php 
                   }
                }  
                ?>
            </div>
        </div>
    </section>
    <!-- blog-classic end -->
<?php 
get_footer();
?>