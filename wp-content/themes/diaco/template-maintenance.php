<?php 
/*
 * Template Name: Comming Soon
 */
get_header('maintenance'); 

$featured_img_url =  get_the_post_thumbnail_url(get_the_ID(),'full'); 

global $diaco_options;
$logo_coming_soon_link = isset($diaco_options['diaco_comingsoon_header_image']['url']) ? $diaco_options['diaco_comingsoon_header_image']['url'] : '';

$comming_soon_top_title = isset($diaco_options['diaco_comingsoon_subtitle']) ? $diaco_options['diaco_comingsoon_subtitle'] : '';
$comming_soon_content = isset($diaco_options['diaco_comingsoon_desc']) ? $diaco_options['diaco_comingsoon_desc'] : '';
$comming_count_down_time = isset($diaco_options['diaco_comingsoon_time']) ? $diaco_options['diaco_comingsoon_time'] : '';
?>
 <!-- .preloader -->
 <div class="preloader"></div>
    <!-- /.preloader -->
    <!-- coming-soon -->
    <section class="coming-soon centred image_background" data-image-src="<?php echo esc_url($featured_img_url);?>">
        <div class="container">
            <div class="inner-content">
                <figure class="logo"><a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url($logo_coming_soon_link);?>" alt="<?php esc_attr_e('logo','diaco')?>"></a></figure>
                <div class="top-text"><?php echo esc_html($comming_soon_top_title);?></div>
                <div class="title-text"><h1><?php echo esc_html(the_title());?></h1></div>
                <div class="text"><?php echo wp_kses_post($comming_soon_content);?></div>   
                <!-- countdown -->
                <div class="timer">
                    <div class="cs-countdown" data-countdown="<?php echo esc_attr( $comming_count_down_time );?>"></div>            
                </div>
            </div>
        </div>
    </section>
    <!-- coming-soon end -->
<?php
get_footer('maintenance'); ?>