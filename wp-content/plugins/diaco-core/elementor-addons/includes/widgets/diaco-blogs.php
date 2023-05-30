<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;

class Blogs extends Widget_Base {

    public function get_name() {
        return 'blogs';
    }

    public function get_title() {
        return esc_html__('Blog', 'diaco-core');
    }

    public function get_icon() {
        return 'eicon-post';
    }

    public function get_categories() {
        return ['diaco'];
    }

    private function get_blog_categories() {
        $options = array();
        $taxonomy = 'category';
        if (!empty($taxonomy)) {
            $terms = get_terms(
                    array(
                        'parent' => 0,
                        'taxonomy' => $taxonomy,
                        'hide_empty' => false,
                    )
            );
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (isset($term)) {
                        $options[''] = 'Select';
                        if (isset($term->slug) && isset($term->name)) {
                            $options[$term->slug] = $term->name;
                        }
                    }
                }
            }
        }
        return $options;
    }

    protected function register_controls() {

        $this->start_controls_section(
                'section_blogs', [
            'label' => esc_html__('Blogs', 'diaco-core'),
                ]
        );

        $this->add_control(
                'title_1', [
            'label' => esc_html__('Title 1', 'diaco-core'),
            'type' => Controls_Manager::TEXT,
            'default' => 'News & Articles'
                ]
        );

        $this->add_control(
                'title_2', [
            'label' => esc_html__('Title 2', 'diaco-core'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Recent Articles'
                ]
        );

        $this->add_control(
                'category_id', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Category', 'diaco-core'),
            'options' => $this->get_blog_categories()
                ]
        );



        $this->add_control(
                'delay_time', [
            'label' => esc_html__('Delay Time', 'diaco-core'),
            'type' => Controls_Manager::TEXT,
            'default' => '400'
                ]
        );

        $this->add_control(
                'number_of_column', [
            'label' => esc_html__('Number of Column', 'diaco-core'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '3' => esc_html__('3', 'diaco-core'),
                '2' => esc_html__('2', 'diaco-core')
            ]
                ]
        );



        $this->add_control(
                'number', [
            'label' => esc_html__('Number of Post', 'diaco-core'),
            'type' => Controls_Manager::TEXT,
            'default' => 2
                ]
        );

        $this->add_control(
                'order_by', [
            'label' => esc_html__('Order By', 'diaco-core'),
            'type' => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => esc_html__('Date', 'diaco-core'),
                'ID' => esc_html__('ID', 'diaco-core'),
                'author' => esc_html__('Author', 'diaco-core'),
                'title' => esc_html__('Title', 'diaco-core'),
                'modified' => esc_html__('Modified', 'diaco-core'),
                'rand' => esc_html__('Random', 'diaco-core'),
                'comment_count' => esc_html__('Comment count', 'diaco-core'),
                'menu_order' => esc_html__('Menu order', 'diaco-core')
            ]
                ]
        );

        $this->add_control(
                'order', [
            'label' => esc_html__('Order', 'diaco-core'),
            'type' => Controls_Manager::SELECT,
            'default' => 'desc',
            'options' => [
                'desc' => esc_html__('DESC', 'diaco-core'),
                'asc' => esc_html__('ASC', 'diaco-core')
            ]
                ]
        );

        $this->add_control(
                'extra_class', [
            'label' => esc_html__('Extra Class', 'diaco-core'),
            'type' => Controls_Manager::TEXT
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings();
        $extra_class = $settings['extra_class'];
        $posts_per_page = $settings['number'];
        $delay_time = $settings['delay_time'];
        $number_of_column = $settings['number_of_column'];
        $order_by = $settings['order_by'];
        $order = $settings['order'];
        $pg_num = get_query_var('paged') ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => array('post'),
            'post_status' => array('publish'),
            'nopaging' => false,
            'paged' => $pg_num,
            'posts_per_page' => $posts_per_page,
            'category_name' => $settings['category_id'],
            'orderby' => $order_by,
            'order' => $order,
        );
        $query = new WP_Query($args);


        $columnClasss = 'col-lg-4 col-md-6 ';
        $newsBlog = 'news-block-two';
        if ('2' == $number_of_column) {
            $columnClasss = 'col-lg-6 col-md-12';
            $newsBlog = 'news-block';
        }
        ?>

  <!-- news-section -->
  <section class="news-section <?php echo esc_attr($extra_class); ?>">
        <div class="container">
            <div class="sec-title centred">
                <span class="top-title"><?php  echo wp_kses_post($settings['title_1']);  ?></span>
                <?php echo wp_kses_post($settings['title_2']);  ?>
            </div>
            <div class="row">
            <?php
                    $i = 0;
                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            $dataDelay = ((int) $delay_time ) * $i;
                            ?>
                <div class="<?php echo esc_attr($columnClasss); ?> col-sm-12 news-block wow" data-wow-delay="00ms" data-wow-duration="1500ms">
                    <div class="news-block-one">
                        <figure class="image-box">
                        <a href="<?php the_permalink(); ?>">
                        <?php
                          if ('3' == $number_of_column) {
                          the_post_thumbnail('diaco-thumbnail-grid', array(
                              'alt' => the_title_attribute(array(
                                  'echo' => false,
                              )),
                          ));
                        }else{
                            the_post_thumbnail('diaco-blog-home', array(
                                'alt' => the_title_attribute(array(
                                    'echo' => false,
                                )),
                            ));
                        }
 
                           ?>
                         </a></figure>
                        <div class="lower-content">
                            <ul class="post-info">
                                <li><?php diaco_posted_on(); ?></li>
                                <?php diaco_entry_footer();?>
                            </ul>
                            <h4 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        </div>
                    </div>
                </div>
                <?php
                            $i++;
                        }
                        wp_reset_postdata();
                    }
                    ?>
            </div>
        </div>
    </section>
    <!-- news-section end -->
        <?php
    }

    protected function content_template() {
        
    }

}

Plugin::instance()->widgets_manager->register(new Blogs());
