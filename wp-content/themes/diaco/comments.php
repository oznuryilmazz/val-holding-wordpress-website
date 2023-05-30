<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Diaco
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
// You can start editing here -- including this comment!
if (have_comments()) :
    ?>
    <!--Comments Area-->
    <div class="comments-area">
        <div class="group-title"><i class="fas fa-comment-dots"></i>
                <?php
				$diaco_comment_count = get_comments_number();
				if ('1' === $diaco_comment_count) {
					printf(
							/* translators: 1: title. */
							esc_html__('One Comment', 'diaco')
					);
				} else {
					printf(// WPCS: XSS OK.
							/* translators: 1: comment count number, 2: title. */
							esc_html(_nx('%1$s Comment', '%1$s Comments ', $diaco_comment_count, 'comments title', 'diaco'), 'diaco'), number_format_i18n($diaco_comment_count)
					);
				}
				?>
        </div>

        <?php
        // You can start editing here -- including this comment!
        if (have_comments()) :
            ?>
            <!-- .comments-title -->
            <?php the_comments_navigation(); ?>
            <div class="comment-list content-inner">
                <?php
                wp_list_comments(array(
                    'style' => 'div',
                    'callback' => 'diaco_comments',
                    'short_ping' => true,
                ));
                ?>
            </div><!-- .comment-list -->
            <?php
            the_comments_navigation();
            // If comments are closed and there are comments, let's leave a little note, shall we?
            if (!comments_open()) :
                ?>
                <p class="no-comments"><?php esc_html_e('Comments are closed.', 'diaco'); ?></p>
                <?php
            endif;
        endif; // Check for have_comments().
        ?>
    </div>
    <?php
//You can start editing here -- including this comment!
endif;

 if (have_comments()) { ?>
    <div id="comments" class="comments-form-area">
    <?php } else { ?>
        <div id="comments" class="comments-form-area custom-comment-margin">
            <?php
        }
        $user = wp_get_current_user();
        $diaco_user_identity = $user->display_name;
        $req = get_option('require_name_email');
        $aria_req = $req ? " aria-required='true'" : '';

    if(is_user_logged_in()){

        $formargs = array(
            'id_form' => 'commentform',
            'id_submit' => 'submit',
            'class_form' => 'form-default',
            'title_reply' => '<span class="group-title"><i class="fas fa-comment-dots"></i>' . esc_html__('Leave a Reply', 'diaco') . '</span>',
            'title_reply_to' => esc_html__('Leave a Reply to %s', 'diaco'),
            'cancel_reply_link' => esc_html__('Cancel Reply', 'diaco'),
            'label_submit' => esc_html__('Post Comment', 'diaco'),
            'submit_button' => '<div class="message-btn message-btn-position"><button type="submit" name="%1$s" id="%2$s" class="%3$s"><i class="fa fa-paper-plane"></i></button></div>',
            'comment_field' => '<div class="row clearfix"><div class="col-lg-12 col-md-12 col-sm-12 form-group"><textarea placeholder="' . esc_attr__('Your Comment..', 'diaco') . '"  class="form-control commentfield-big" id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
            '</textarea></div></div>',
            'must_log_in' => '<div>' .
            sprintf(
                    wp_kses(__('You must be <a href="%s">logged in</a> to post a comment.', 'diaco'), array('a' => array('href' => array()))), wp_login_url(apply_filters('the_permalink', esc_url(get_permalink())))
            ) . '</div>',
            'logged_in_as' => '<div class="logged-in-as">' .
            sprintf(
                    wp_kses(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="%4$s">Log out?</a>', 'diaco'), array('a' => array('href' => array()))), esc_url(admin_url('profile.php')), $diaco_user_identity, wp_logout_url(apply_filters('the_permalink', esc_url(get_permalink()))), esc_attr__('Log out of this account', 'diaco')
            ) . '</div>',
            'comment_notes_before' => '<p>' .
            esc_html__('Your email address will not be published.', 'diaco') . ( $req ? '<span class="required">*</span>' : '' ) .
            '</p>',
            'comment_notes_after' => '',
            'fields' => apply_filters('comment_form_default_fields', array(
                'author' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fa fa-user"></i>'
                . '<input id="author"  class="form-control" name="author" placeholder="' . esc_attr__('Name', 'diaco') . '" type="text" value="' . esc_attr($commenter['comment_author']) .
                '" size="30"' . $aria_req . ' /></div>',
                'email' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fa fa-envelope"></i>'
                . '<input id="email" name="email"  class="form-control" type="text"  placeholder="' . esc_attr__('Enter your valid email', 'diaco') . '" value="' . esc_attr($commenter['comment_author_email']) .
                '" size="30"' . $aria_req . ' /></div>',
                'url' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fas fa-globe-asia"></i>'
                . '<input id="url"  class="form-control" name="url" placeholder="' . esc_attr__('Website', 'diaco') . '" type="text" value="' . esc_attr($commenter['comment_author_url']) .
                '" size="30"' . $aria_req . ' /></div>'
                    )
            )
        );

    }else{
        $formargs = array(
            'id_form' => 'commentform',
            'id_submit' => 'submit',
            'class_form' => 'form-default',
            'title_reply' => '<span class="group-title"><i class="fas fa-comment-dots"></i>' . esc_html__('Leave a Reply', 'diaco') . '</span>',
            'title_reply_to' => esc_html__('Leave a Reply to %s', 'diaco'),
            'cancel_reply_link' => esc_html__('Cancel Reply', 'diaco'),
            'label_submit' => esc_html__('Post Comment', 'diaco'),
            'submit_button' => '<div class="message-btn"><button type="submit" name="%1$s" id="%2$s" class="%3$s"><i class="fa fa-paper-plane"></i></button></div>',
            'comment_field' => '<div class="row clearfix"><div class="col-lg-12 col-md-12 col-sm-12 form-group"><textarea placeholder="' . esc_attr__('Your Comment..', 'diaco') . '"  class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
            '</textarea></div>',
            'must_log_in' => '<div>' .
            sprintf(
                    wp_kses(__('You must be <a href="%s">logged in</a> to post a comment.', 'diaco'), array('a' => array('href' => array()))), wp_login_url(apply_filters('the_permalink', esc_url(get_permalink())))
            ) . '</div>',
            'logged_in_as' => '<div class="logged-in-as">' .
            sprintf(
                    wp_kses(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="%4$s">Log out?</a>', 'diaco'), array('a' => array('href' => array()))), esc_url(admin_url('profile.php')), $diaco_user_identity, wp_logout_url(apply_filters('the_permalink', esc_url(get_permalink()))), esc_attr__('Log out of this account', 'diaco')
            ) . '</div>',
            'comment_notes_before' => '<p>' .
            esc_html__('Your email address will not be published.', 'diaco') . ( $req ? '<span class="required">*</span>' : '' ) .
            '</p>',
            'comment_notes_after' => '',
            'fields' => apply_filters('comment_form_default_fields', array(
                'author' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fa fa-user"></i>'
                . '<input id="author"  class="form-control" name="author" placeholder="' . esc_attr__('Name', 'diaco') . '" type="text" value="' . esc_attr($commenter['comment_author']) .
                '" size="30"' . $aria_req . ' /></div>',
                'email' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fa fa-envelope"></i>'
                . '<input id="email" name="email"  class="form-control" type="text"  placeholder="' . esc_attr__('Enter your valid email', 'diaco') . '" value="' . esc_attr($commenter['comment_author_email']) .
                '" size="30"' . $aria_req . ' /></div>',
                'url' =>
                '<div class="col-lg-4 col-md-6 col-sm-12 form-group"><i class="fas fa-globe-asia"></i>'
                . '<input id="url"  class="form-control" name="url" placeholder="' . esc_attr__('Website', 'diaco') . '" type="text" value="' . esc_attr($commenter['comment_author_url']) .
                '" size="30"' . $aria_req . ' /></div></div>'
                    )
            )
        );
    }

        ?>
        <div class="comment-form">
        <?php
        comment_form($formargs);
        ?>
        </div>
    </div><!-- #comments -->