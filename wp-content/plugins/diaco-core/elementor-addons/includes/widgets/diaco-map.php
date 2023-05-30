<?php
/** 
 * Elementor Map area one
 * @since 1.0.0
*/
class Google_Map extends \Elementor\Widget_Base {
    public function get_name() {
        return 'Google_Map';
    }
    public function get_title(){
        return __( 'Map', 'diaco' );
    }
    public function get_icon(){
        return 'fa fa-object-ungroup';
    }
    public function get_categories(){
        return [ 'diaco' ];
    }
    protected function register_controls() {

        $this->start_controls_section(
            'map_contyent_canv',[
                'label' => __( 'Map canvace info', 'diaco' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'map_iframe',[
				'label' => __( 'Map Iframe', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'If you put map iframe here then below settings will not work', 'diaco' ),
			]
        );
        $this->add_control(
            'data_zoom',[
				'label' => __( 'data-zoom', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '12' ),
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_lat',[
				'label' => __( 'data-lat', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '-37.817085' ),
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_lng',[
				'label' => __( 'data-lng', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '144.955631' ),
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_type',[
				'label' => __( 'data-type', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'roadmap' ),
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_hue',[
				'label' => __( 'data-hue', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '#ffc400' ),
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_title',[
				'label' => __( 'data-title', 'diaco' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'conten here', 'diaco' ),
			]
        );
        $this->add_control(
            'data_icon_path',[
				'label' => __( 'data-icon-path', 'diaco' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display(); 
            $map_iframe= $settings['map_iframe'];
            $data_zoom= $settings['data_zoom'];
            $data_lat= $settings['data_lat'];
            $data_lng= $settings['data_lng'];
            $data_title= $settings['data_title'];
            $data_icon_path= $settings['data_icon_path']['url'];
        ?>

            <!-- google-map-section -->
            <section class="google-map-section">
                <div class="google-map-area">
                <?php if($map_iframe != ""){ ?>
                    <div  id="contact-google-map">
                        <?php echo $map_iframe;?>
                    </div>
                <?php }else{?>
                    <div 
                        class="google-map" 
                        id="contact-google-map" 
                        data-map-lat="<?php echo $data_lat; ?>" 
                        data-map-lng="<?php echo $data_lng; ?>" 
                        data-icon-path="<?php echo $data_icon_path; ?>"  
                        data-map-title="<?php echo $data_title; ?>" 
                        data-map-zoom="<?php echo $data_zoom; ?>" 
                        data-markers='{
                            "marker-1": [40.712776, -74.005974, "<h4>Branch Office</h4><p>77/99 New York</p>","images/icons/map-marker.png"]
                        }'>

                    </div>
                <?php } ?>
                </div>
            </section>
            <!-- google-map-section end -->


        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register( new \Google_Map() );