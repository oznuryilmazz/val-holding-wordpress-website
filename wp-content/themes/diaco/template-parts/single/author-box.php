<?php
global $post;
$display_name = get_the_author_meta('display_name', $post->post_author);
$user_description = get_the_author_meta('user_description', $post->post_author);
$user_avatar = get_avatar($post->post_author, 175);
if (isset($user_description) && !empty($user_description)) {
    ?>
    <div class="author-box">
        <div class="group-title"><i class="fas fa-pencil-alt"></i><?php esc_html_e('Author','diaco');?></div>
        <div class="author-inner">
            <figure class="author-thumb"><?php echo wp_kses_post($user_avatar); ?></figure>
            <div class="author-content">
                <div class="info-box clearfix">
                    <h6><?php echo wp_kses_post(ucfirst($display_name)); ?></h6>
                    <a href="<?php  esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) )) ;?>"><?php esc_html_e('More articles by this authors','diaco');?> <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
                <div class="text">
                <?php echo wp_kses_post($user_description); ?>
                </div>
            </div>
        </div>
    </div>
<?php 
}