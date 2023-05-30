<?php
add_action('widgets_init', 'diaco_sidebar_recent_post');

function diaco_sidebar_recent_post() {
    register_widget('diacoRecentPost');
}

class DiacoRecentPost extends WP_Widget {

    private $defaults = array();

    function __construct() {
        $this->defaults = array(
            'title' => esc_html__('Recent Post', 'diaco-core'),
            'number' => 4,
        );
        parent::__construct(
            'posts-widget', esc_html__('Diaco Recent Posts', 'diaco-core'),
            array( 
                'description' => __( 'Widget for displaying Post', 'diaco-core' ), 
                'classname' => 'sidebar-post ',
              ) 
        );
    }

    function update($new_instance, $old_instance) {
        $defaults = $this->defaults;
        $instance = $old_instance;
        $instance['title'] = esc_attr($new_instance['title']);
        $instance['number'] = intval($new_instance['number']);
        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'diaco-core'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('title')); ?>"  value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" />
        </p>
        <p>
            <label for="<?php print esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of posts:', 'diaco-core'); ?>
                <input class="widefat" id="<?php print esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo isset($instance['number']) ? esc_attr($instance['number']) : ''; ?>" />
            </label>
        </p>
        <?php
    }

    function widget($args, $instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        extract($args);
        $number = isset($instance['number']) ? $instance['number'] : 2;
        $title = $instance['title'];
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
        ?>
            <?php
            $query_args = array(
                'posts_per_page' => $number,
                'no_found_rows' => true,
                'post_status' => 'publish',
                'ignore_sticky_posts' => true
            );
            $query = new WP_Query($query_args);
            if ($query->have_posts()) {
                while ($query->have_posts()) :
                    $query->the_post();
                    ?>
                    <div class="post">
                                    <figure class="thumb"><a href="<?php esc_url(the_permalink()); ?>"><?php echo the_post_thumbnail('diaco-blog-sidebar'); ?></a></figure>
                                    <span class="date"><?php echo get_the_date(); ?></span>
                                    <h5><a href="<?php esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h5>
                      </div>
                
                    <?php
                endwhile;
                wp_reset_query();
                echo $args['after_widget'];
            }
        }

    }
    ?>