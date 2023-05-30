<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package diaco
 */
if (!function_exists('diaco_posted_on')) :

    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function diaco_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf($time_string, esc_attr(get_the_date(DATE_W3C)), esc_html(get_the_date()), esc_attr(get_the_modified_date(DATE_W3C)), esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
                /* translators: %s: post date. */
                esc_html('%s'), '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . get_the_date() . '</a>'
        );

        echo '<i class="far fa-calendar-alt"></i>' . $posted_on; // WPCS: XSS OK.
    }

endif;

if (!function_exists('diaco_posted_by')) :

    /**
     * Prints HTML with meta information for the current author.
     */
    function diaco_posted_by() {
        $byline = sprintf(
                /* translators: %s: post author. */
                esc_html_x('by %s', 'post author', 'diaco'), '<a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(ucfirst(get_the_author())) . '</a>'
        );

        echo '<span class="fa fa-pencil-alt"></span>' . $byline; // WPCS: XSS OK.
    }

endif;
if (!function_exists('diaco_entry_footer')) :

    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function diaco_entry_footer() {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'diaco'));
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<li><i class="fa fa-tag"></i>' . esc_html('%1$s') . '</li>', $categories_list); // WPCS: XSS OK.
            }
        }
    }

endif;

if (!function_exists('diaco_post_thumbnail')) :

    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function diaco_post_thumbnail($type=null) {

        global $diaco_options;
        $diaco_blog_style = get_query_var('blog_type');
        if (!$diaco_blog_style) {
                $diaco_blog_style = $diaco_options['diaco_blog_style'];
            }

        if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
            return;
        }
        if (is_singular()) :
            ?>
             <figure class="image-box">
                <?php the_post_thumbnail('full'); ?>
                </figure><!-- .post-thumbnail -->
        <?php else : ?>
        <figure class="image-box">

                <a class="post-thumbnail" href="<?php esc_url(the_permalink()); ?>">
                <?php if($diaco_blog_style == '3'){
                    ?>
                    <?php
                   
                      the_post_thumbnail('diaco-thumbnail-grid', array(
                          'alt' => the_title_attribute(array(
                              'echo' => false,
                          )),
                      ));
               
                }else{

                ?>
                    <?php
                    the_post_thumbnail('diaco-thumbnail', array(
                        'alt' => the_title_attribute(array(
                            'echo' => false,
                        )),
                    ));
                    ?>
                <?php  } ?>
                </a>
            </figure>
        <?php
        endif; // End is_singular().
    }

endif;

if (!function_exists('diaco_comments_count')) :

    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function diaco_comments_count() {

        if (get_comments_number(get_the_ID()) > 1) {
            $comments_count = sprintf(
                    /* translators: %s: post date. */
                    esc_html('%s'), '<a href="' . esc_url(get_permalink()) . '" >' . get_comments_number(get_the_ID()) . " comments" . '</a>'
            );
        } else {
            $comments_count = sprintf(
                    /* translators: %s: post date. */
                    esc_html('%s'), '<a href="' . esc_url(get_permalink()) . '#comments" >' . get_comments_number(get_the_ID()) . " comment" . '</a>'
            );
        }


        echo '<span class="fa fa-comments"></span>' . $comments_count;
    }

endif;

if (!function_exists('diaco_post_meta')) :

    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function diaco_post_meta() {
        ?>
        <ul class="post-info">
            <li><?php diaco_posted_by(); ?></li>
            <li><?php diaco_posted_on(); ?></li>
            <li><?php diaco_comments_count(); ?></li>
        </ul>
        <?php
    }

endif;

if (!function_exists('diaco_post_tags')) :

    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    function diaco_post_tags($postid) {
        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list('<ul class="tags"><li class="title"><i class="fas fa-tags"></i>' . esc_html__("Tags:", "diaco") . '</li> <li> ','</li> <li>', '</li></ul>');
        if ($tags_list) {
        ?>
        <div class="post-share-option clearfix">
            <div class="pull-left">
                <div class="float-left">
                    <?php
                        printf('' . esc_html('%1$s') . '', $tags_list); // WPCS: XSS OK.
                    ?>
                </div>
            </div>
        </div>
        <?php
         }
    }

endif;

if (!function_exists('diaco_comments')) {

    function diaco_comments($comment, $args, $depth) {
        extract($args, EXTR_SKIP);
        $args['reply_text'] = esc_html__('Reply', 'diaco');
        $class = '';
        if ($depth > 1) {
            $class = '';
        }
        if ($depth == 1) {
            $child_html_el = '<ul><li>';
            $child_html_end_el = '</li></ul>';
        }

        if ($depth >= 2) {
            $child_html_el = '<li>';
            $child_html_end_el = '</li>';
        }
        ?>
        <div class="comment-box" id="comment-<?php comment_ID(); ?>">
            <?php if ($comment->comment_type != 'trackback' && $comment->comment_type != 'pingback') { ?>
                <div class="comment ">
                <?php } else { ?>
                    <div class="comment yes-ping">
                    <?php } ?>
                    <?php if($comment->comment_type!='trackback' && $comment->comment_type!='pingback' ){ ?>
                    <div class="author-thumb">
                        <?php print get_avatar($comment, 110, null, null, array('class' => array())); ?>
                    </div>
                     <?php } ?>
                    <div class="comment-info clearfix"><strong> <?php echo get_comment_author_link(); ?> </strong>
                        <div class="comment-time"><?php comment_time(get_option('date_format')); ?> </div>
                    </div>
                    <div class="text">
                        <?php comment_text(); ?>
                    </div>
                    <div class="reply-outer">
                        <?php
                        comment_reply_link(array_merge($args, array(
                            'reply_text' => esc_html__('Reply', 'diaco'),
                            'depth' => $depth,
                            'max_depth' => $args['max_depth']
                                        )
                        ));
                        ?>
                    </div>
                </div>
        </div>
        <?php
    }
}

function diaco_related_post(){
    $post_categories = wp_get_post_terms( get_the_ID(), 'category' );
	$post_tags = wp_get_post_terms( get_the_ID(), 'post_tag' );


	$all_related_posts = array();

	$not___in = array();
	$not___in[] = get_the_ID();

	foreach ($post_categories as $post_categorie) {

		$rp_args = array(
			'posts_per_page' => 4,
			'exclude' => get_the_ID(),
			'cat' => $post_categorie->term_id,
			'post__not_in' => $not___in
		);

		$related_posts_temp = new WP_Query( $rp_args );

		if($related_posts_temp->have_posts()){
			$all_related_posts[] = $related_posts_temp;
		}
	}

	foreach ($post_tags as $post_tag) {
		$rp_args = array(
			'posts_per_page' => 4,
			'exclude' => get_the_ID(),
			'tag_id' => $post_tag->term_id,
			'post__not_in' => $not___in
		);

		$related_posts_temp = new WP_Query( $rp_args );

		if($related_posts_temp->have_posts()){
			$all_related_posts[] = $related_posts_temp;
		}
		
	}

	$already_in = array();
	
	if(count($all_related_posts)):
        $pcount = 0;
        ?>
        
        <div class="related-post">
            <div class="group-title"><i class="far fa-file-alt"></i><?php esc_html_e('RELATED ARTICLES','diaco');?></div>
            <div class="post-inner">
                <div class="row">
                <?php foreach($all_related_posts as $related_posts): ?>
    				<?php if ($related_posts->have_posts()): ?>
    				<?php 
                        $i = 0;
                    while ( $related_posts->have_posts() && $i <= 3) : 
                        $i++;
                        $related_posts->the_post(); 
                        ?>
                        <div class="col-lg-6 col-md-6 col-sm-12 news-block">
                            <div class="news-block-one">
                                <figure class="image-box">
                                    <a href="<?php esc_url(the_permalink());?>">
                                    <?php echo get_the_post_thumbnail($related_posts->ID,'diaco-thumbnail-grid'); ?>
                                    </a>
                                </figure>
                                <div class="lower-content">
                                    <ul class="post-info">
                                        <li><?php diaco_posted_on();?></li>
                                        <?php diaco_entry_footer();?>
                                    </ul>
                                    <h4><a href="<?php esc_url(the_permalink());?>"><?php the_title();?></a></h4>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
    			<?php endforeach; ?>
                </div>
            </div>
        </div>
<?php wp_reset_postdata(); ?>

<?php
endif;
    }