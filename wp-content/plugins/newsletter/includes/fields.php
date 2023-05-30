<?php

class NewsletterFields {
    /* @var NewsletterControls */

    var $controls;

    public function __construct(NewsletterControls $controls) {
        $this->controls = $controls;
    }

    public function _open($subclass = '') {
        echo '<div class="tnpf-field ', $subclass, '">';
    }

    public function _close() {
        echo '</div>';
    }

    public function _label($text, $for = '') {
        if (empty($text)) {
            return;
        }
        // Do not escape, HTML allowed
        echo '<label class="tnpf-label">', $text, '</label>';
    }

    public function _description($attrs) {
        if (empty($attrs['description'])) {
            return;
        }
        // Do not escape, HTML allowed
        echo '<div class="tnpf-description">', $attrs['description'], '</div>';
    }

    public function _id($name) {
        return 'options-' . esc_attr($name);
    }

    public function _name($name) {
        return 'options[' . esc_attr($name) . ']';
    }

    /**
     * Adds some empty basic atributes to avoid the isset() checking.
     *
     * @param array $attrs
     * @return array
     */
    public function _merge_base_attrs($attrs) {
        return array_merge(['description' => '', 'label' => '', 'help_url' => ''], $attrs);
    }

    /** Adds some basic attributes and the provided default ones.
     *
     * @param array $attrs
     * @param array $defaults
     * @return array
     */
    public function _merge_attrs($attrs, $defaults = []) {
        return array_merge(['description' => '', 'label' => '', 'help_url' => ''], $defaults, $attrs);
    }

    /**
     * A form section title.
     *
     * @param string $title
     */
    public function section($title = '') {
        // Do not escape, HTML allowed
        echo '<div class="tnpf-section">', $title, '</div>';
    }

    public function separator() {
        echo '<div class="tnpf-separator"></div>';
    }

    public function checkbox($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_base_attrs($attrs);
        $this->_open('tnpf-checkbox');
        $this->controls->checkbox($name, $label);
        $this->_description($attrs);
        $this->_close();
    }

    /** General Input field with default type = text
     *
     * Attributes:
     * - label_after (default: none): small text ti be displayed after the text field
     * - min (default: none): minimum number of characters
     * - max (default: none): maximum number of characters
     * - size (default: none): size in pixels
     */
    public function input($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['placeholder' => '', 'size' => 0, 'label_after' => '', 'type' => 'text']);
        $this->_open();
        $this->_label($label);
        $value = $this->controls->get_value($name);

        echo '<input id="', $this->_id($name), '" placeholder="', esc_attr($attrs['placeholder']), '" name="', $this->_name($name), '" type="', esc_attr($attrs['type']), '"';

        if (!empty($attrs['size'])) {
            echo ' style="width: ', $attrs['size'], 'px"';
        }

        if (isset($attrs['min'])) {
            echo ' min="' . (int) $attrs['min'] . '"';
        }

        if (isset($attrs['max'])) {
            echo ' max="' . (int) $attrs['max'] . '"';
        }

        echo ' value="', esc_attr($value), '">';

        if (!empty($attrs['label_after'])) {
            echo $attrs['label_after'];
        }

        $this->_description($attrs);
        $this->_close();
    }

    public function text($name, $label = '', $attrs = []) {
        $attrs['type'] = 'text';
        $this->input($name, $label, $attrs);
    }

    public function text_on_off($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['placeholder' => '', 'size' => 0, 'label_after' => '', 'type' => 'text']);
        $this->_open();
        $this->_label($label);
        $value = $this->controls->get_value($name);

        echo '<input type="hidden" name="tnp_fields[' . esc_attr($name . '_enabled') . ']" value="checkbox">';
        echo '<input id="', $this->_id($name . '_enabled'), '" name="', $this->_name($name . '_enabled'), '" type="checkbox" value="1"';
        if (!empty($this->controls->get_value($name . '_enabled'))) {
            echo ' checked';
        }
        echo '>&nbsp;';
        
        echo '<input id="', $this->_id($name), '" placeholder="', esc_attr($attrs['placeholder']), '" name="', $this->_name($name), '" type="text"';

        echo ' style="width: 90%;"';

        if (isset($attrs['min'])) {
            echo ' min="' . (int) $attrs['min'] . '"';
        }

        if (isset($attrs['max'])) {
            echo ' max="' . (int) $attrs['max'] . '"';
        }

        echo ' value="', esc_attr($value), '">';

        if (!empty($attrs['label_after'])) {
            echo $attrs['label_after'];
        }

        $this->_description($attrs);
        $this->_close();
    }

    public function number($name, $label = '', $attrs = []) {
        $attrs = array_merge(['type' => 'number'], $attrs);
        $this->input($name, $label, $attrs);
    }

    /**
     * A set of text fields, named $name_1, $name_2, ...
     *
     * Attributes:
     * - label_after: a label to show after the field column
     *
     * @param type $name
     * @param type $label
     * @param type $count
     * @param type $attrs
     */
    public function multitext($name, $label = '', $count = 10, $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['description' => '', 'placeholder' => '', 'size' => 0, 'label_after' => '']);
        $this->_open();
        $this->_label($label);

        for ($i = 1; $i <= $count; $i++) {
            $value = $this->controls->get_value($name . '_' . $i);
            echo '<input id="', $this->_id($name . '_' . $i), '" placeholder="', esc_attr($attrs['placeholder']), '" name="options[', $name, '_', $i, ']" type="text"';
            if (!empty($attrs['size'])) {
                echo ' style="width: ', $attrs['size'], 'px"';
            }
            echo ' value="', esc_attr($value), '">';
        }
        if (!empty($attrs['label_after'])) {
            echo $attrs['label_after'];
        }
        $this->_description($attrs);
        $this->_close();
    }

    public function textarea($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['width' => '100%', 'height' => '150']);
        $this->_open();
        $this->_label($label);
        $this->controls->textarea_fixed($name, $attrs['width'], $attrs['height']);
        $this->_description($attrs);
        $this->_close();
    }

    public function wp_editor($name, $label = '', $attrs = []) {
        global $wp_version;

        $attrs = $this->_merge_attrs($attrs);
        $this->_open();
        $this->_label($label);
        $value = $this->controls->get_value($name);
        $name = esc_attr($name);

        // Uhm...
        if (is_array($value)) {
            $value = implode("\n", $value);
        }
        if (version_compare($wp_version, '4.8', '<')) {
            echo '<p><strong>Rich editor available only with WP 4.8+</strong></p>';
        }
        echo '<textarea class="tnpf-wp-editor" id="options-', $name, '" name="options[', $name, ']" style="width: 100%;height:250px">';
        echo esc_html($value);
        echo '</textarea>';

        if (version_compare($wp_version, '4.8', '>=')) {

            $paragraph_style = " p { font-family: {$attrs['text_font_family']}; font-size: {$attrs['text_font_size']}px; font-weight: 0{$attrs['text_font_weight']}; color: {$attrs['text_font_color']}; line-height: 1.5em; }";
            $content_style = $paragraph_style;

            echo '<script>';
            echo 'wp.editor.remove("options-', $name, '");';
            echo 'wp.editor.initialize("options-', $name, '", { tinymce: {'
                    //. 'font_formats: "Default=; Andale Monox=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Oswald=oswald; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",'
                    . 'content_style: "' . $content_style . '", toolbar1: "undo redo | formatselect fontselect fontsizeselect | bold italic forecolor backcolor | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | wp_add_media | charmap | rtl ltr", fontsize_formats: "11px 12px 14px 16px 18px 24px 36px 48px", plugins: "link textcolor colorpicker lists wordpress charmap directionality", default_link_target: "_blank", relative_urls : false, convert_urls: false, keep_styles: true }});';
            echo '</script>';
        }
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Attributes:
     * - realod: when true is forces a submit of the form (used to change the form fields or values for example when changing layout or color scheme)
     *
     * @param type $name
     * @param type $label
     * @param type $options
     * @param type $attrs
     */
    public function select($name, $label = '', $options = [], $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['reload' => false, 'after-rendering' => '', 'class' => '']);
        $this->_open();
        $this->_label($label);
        $value = $this->controls->get_value($name);

        echo '<select id="', $this->_id($name), '" name="', $this->_name($name), '"';
        if ($attrs['class']) {
            echo ' class="', esc_attr($attrs['class']), '"';
        }
        if ($attrs['reload']) {
            echo ' onchange="tnpc_reload_options(event)"';
        }
        if (!empty($attrs['after-rendering'])) {
            echo ' data-after-rendering="', $attrs['after-rendering'], '"';
        }
        echo '>';
//        if (!empty($first)) {
//            echo '<option value="">', esc_html($first), '</option>';
//        }

        foreach ($options as $key => $text) {
            echo '<option value="', esc_attr($key), '"';
            if ($value == $key) {
                echo ' selected';
            }
            echo '>', esc_html($text), '</option>';
        }
        echo '</select>';

        $this->_description($attrs);
        $this->_close();
    }

    public function align($name = 'align') {
        $this->select($name,
                __('Align', 'newsletter'),
                ['center' => __('Center', 'newsletter'), 'left' => __('Left', 'newsletter'), 'right' => __('Right')]
        );
    }

    public function yesno($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs);
        $this->_open();
        $this->_label($label);
        
        $value = isset($this->controls->data[$name]) ? (int) $this->controls->data[$name] : 0;

        echo '<select style="width: 60px" name="options[', esc_attr($name), ']">';
        echo '<option value="0"';
        if ($value == 0) {
            echo ' selected';
        }
        echo '>', __('No', 'newsletter'), '</option>';
        echo '<option value="1"';
        if ($value == 1) {
            echo ' selected';
        }
        echo '>', __('Yes', 'newsletter'), '</option>';
        echo '</select>';
        
        $this->_description($attrs);
        $this->_close();
    }

    public function select_number($name, $label = '', $min = 0, $max = 10, $attrs = []) {
        $attrs = $this->_merge_attrs($attrs);
        $this->_open();
        $this->_label($label);
        $this->controls->select_number($name, $min, $max);
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * General field to collect an element dimension in pixels
     *
     * Attributes:
     * - size: field width in pixels
     */
    public function size($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['description' => '', 'placeholder' => '', 'size' => 0, 'label_after' => 'px']);
        $this->_open('tnpf-size');
        $this->_label($label);
        $value = $this->controls->get_value($name);
        echo '<input id="', $this->_id($name), '" placeholder="', esc_attr($attrs['placeholder']), '" name="', $this->_name($name), '" type="text"';
        if (!empty($attrs['size'])) {
            echo ' style="width: ', $attrs['size'], 'px"';
        }
        echo ' value="', esc_attr($value), '">', $attrs['label_after'];
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Collects a color in HEX format with a picker.
     */
    public function color($name, $label, $attrs = []) {
        $this->_open('tnp-color');
        $this->_label($label);
        $this->controls->color($name);
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Configuration for a simple button with label and color
     *
     * Attributes:
     * - weight: if true (default) shows the font weight selector
     * - url_paceholder: the placeholder for the URL field
     * - url: if true (default) shows the URL field (sometime the URL is produced elsewhere, for example on post list)
     */
    public function button($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs,
                [
                    'placeholder' => 'Label...',
                    'url_placeholder' => 'https://...',
                    'url' => true,
                    'weight' => true,
                    'family_default' => false,
                    'size_default' => false,
                    'weight_default' => false,
        ]);

        $this->_open('tnpf-button');
        $this->_label($label);
        $value = $this->controls->get_value($name . '_label');
        $name_esc = esc_attr($name);
        echo '<div class="tnp-field-row">';
        echo '<div class="tnp-field-col-2">';
        echo '<input id="', $this->_id($name . '_label'), '" placeholder="', esc_attr($attrs['placeholder']), '" name="options[', $name_esc, '_label]" type="text"';
        echo ' style="width: 100%"';
        echo ' value="', esc_attr($value), '">';
        echo '</div>';

        if ($attrs['url']) {
            $value = $this->controls->get_value($name . '_url');
            echo '<div class="tnp-field-col-2">';
            $width = isset($attrs['media']) ? '90%' : '100%';
            echo '<input id="', $this->_id($name . '_url'), '" placeholder="', esc_attr($attrs['url_placeholder']), '" name="options[',
            $name_esc, '_url]" type="url" style="width: ', $width, '" value="', esc_attr($value), '">';
            if (isset($attrs['media'])) {
                echo '&nbsp;<i class="far fa-folder-open" data-field="', $this->_id($name . '_url'), '" onclick="tnp_fields_url_select(this)"></i>';
            }
            echo '</div>';
        }
        echo '<div style="clear: both"></div>';
        echo '</div>';
        $this->controls->css_font($name . '_font', [
            'weight' => $attrs['weight'],
            'family_default' => $attrs['family_default'],
            'size_default' => $attrs['size_default'],
            'weight_default' => $attrs['weight_default']
        ]);
        $this->controls->color($name . '_background');
        $this->_close();
    }

    public function button_style($name, $label = '') {

        $this->_open('tnp-font');
        $this->_label($label);
        $this->controls->css_font($name . '_font');
        $this->controls->color($name . '_background_color');
        $this->_close();
    }

    /**
     * URL input field
     *
     * @param string $name
     * @param string $label
     * @param array $attrs
     */
    public function url($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['placeholder' => 'https://...']);
        $this->_open('tnp-url');
        $this->_label($label);
        $this->controls->text_url($name);
        if (isset($attrs['media'])) {
            echo '<i class="far fa-folder-open" onclick="tnp_fields_url_select(\'options_', $name, '\')"></i>';
        }
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Provides a list of custom post types.
     *
     * @param string $name
     * @param string $label
     * @param array $attrs
     */
    public function post_type($name = 'post_type', $label = '', $attrs = []) {

        $post_types = get_post_types(['public' => true], 'objects', 'and');

        $attrs = array_merge(['description' => ''], $attrs);
        $this->_open('tnp-post-type');
        $this->_label($label);

        $options = ['post' => 'Standard posts', 'page' => 'Pages'];

        foreach ($post_types as $post_type) {
            if ($post_type->name == 'post' || $post_type->name == 'page' || $post_type->name == 'attachment') {
                continue;
            }
            $options[$post_type->name] = $post_type->labels->name;
        }

        $value = $this->controls->get_value($name);

        echo '<select id="', $this->_id($name), '" name="options[' . esc_attr($name) . ']" onchange="tnpc_reload_options(event); return false;">';
//        if (!empty($first)) {
//            echo '<option value="">' . esc_html($first) . '</option>';
//        }
        $label = esc_html($label);
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"';
            if ($value == $key)
                echo ' selected';
            echo '>', $label, '</option>';
        }
        echo '</select>';

        $this->_description($attrs);
        $this->_close();
    }

    function posts($name, $label, $count = 20, $args = []) {
        $value = $this->controls->get_value($name, 0);
        
        // Post select options
        $options = [];
        
        // Retrieve the selected post and add as first element since it could not be part of the 
        // latest list anymore
        if (!empty($value)) {
            $post = get_post($value);
            if ($post) {
                $options['' . $post->ID] = $post->post_title;
            }
        }
        
        $args = array_merge(array('filters' => array(
                'posts_per_page' => 5,
                'offset' => 0,
                'category' => '',
                'category_name' => '',
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => '',
                'exclude' => '',
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'post',
                'post_mime_type' => '',
                'post_parent' => '',
                'author' => '',
                'author_name' => '',
                'post_status' => 'publish',
                'suppress_filters' => true),
            'last_post_option' => false
                ), $args);
        $args['filters']['posts_per_page'] = $count;

        $posts = get_posts($args['filters']);
        
        if ($args['last_post_option']) {
            $options['last'] = 'Most recent post';
        }
        foreach ($posts as $post) {
            $options['' . $post->ID] = $post->post_title;
        }

        $this->select($name, $label, $options);
    }

    function lists($name, $label, $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['empty_label' => null]);
        $this->_open();
        $this->_label($label);
        $lists = $this->controls->get_list_options($attrs['empty_label']);
        $this->controls->select($name, $lists);
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Media selector using the WP media library (for images and files.
     * The field to use it the {$name}_id which contains the media id.
     *
     * Attributes:
     * - alt: if true shows the alternate text field for the "alt" attribute
     * - layout: if set to "mini" the controls is shown as a mini selector, no labels
     *
     * @param string $name
     * @param string $label
     * @param array $attrs
     */
    public function media($name, $label = '', $attrs = []) {
        $attrs = $this->_merge_attrs($attrs, ['alt' => false, 'layout' => '']);

        if (empty($attrs['layout'])) {
            $this->_open('tnp-media');
            $this->_label($label);
            $this->controls->media($name);
            if ($attrs['alt']) {
                $this->controls->text($name . '_alt', 20, 'Alternative text');
            }
            $this->_description($attrs);
            $this->_close();
        } else {
            if (isset($this->controls->data[$name]['id'])) {
                $media_id = (int) $this->controls->data[$name]['id'];
                $media = wp_get_attachment_image_src($media_id, 'thumbnail');
            } else {
                $media = false;
                $media_id = 0;
            }
            echo '<div class="tnpf-media-mini-select" data-name="' . esc_attr($name) . '" style="width: 100px; height: 100px; overflow: hidden; border: 1px dashed #999; position: relative" onclick="tnp_fields_media_mini_select(this)">';
            echo '<a style="position: absolute; top: 5px; right: 5px; background-color: #000; color: #fff; padding: 0px 5px 6px 5px; font-size: 24px; display: block; text-decoration: none" href="#" onclick="tnp_fields_media_mini_remove(\'' . esc_attr($name) . '\'); return false;">&times;</a>';
            if ($media) {
                echo '<img style="max-width: 100%; height: auto; display: block" id="' . esc_attr($name) . '_img" src="' . esc_attr($media[0]) . '">';
            } else {
                echo '<img style="max-width: 100%; height: auto; display: block" id="' . esc_attr($name) . '_img" src="">';
            }

            echo '</div>';
            echo '<input type="hidden" id="' . esc_attr($name) . '_id" name="options[' . esc_attr($name) . '][id]" value="' . esc_attr($media_id) . '">';
        }
    }

    public function categories($name = 'categories', $label = '', $attrs = []) {
        if (empty($label)) {
            $label = __('Categories', 'newsletter');
        }
        $attrs = $this->_merge_attrs($attrs);
        $this->_open('tnp-categories');
        $this->_label($label);
        $this->controls->categories_group($name);
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * The field name is preset to tax_$taxonomy. A different name can be specified
     * with the attribute 'name'.
     * @param type $taxonomy
     * @param type $label
     * @param type $attrs
     */
    public function terms($taxonomy, $label = '', $attrs = []) {
        if (isset($attrs['name'])) {
            $name = $attrs['name'];
        } else {
            $name = 'tax_' . $taxonomy;
        }
        if (empty($label)) {
            $label = __('Terms', 'newsletter');
        }
        $attrs = $this->_merge_attrs($attrs);
        $this->_open('tnp-categories');
        $this->_label($label);
        $terms = get_terms($taxonomy);

        if (empty($terms)) {
            echo 'No terms in use';
        } else {

            echo '<div class="newsletter-checkboxes-group">';
            foreach ($terms as $term) {
                /* @var $term WP_Term */
                echo '<div class="newsletter-checkboxes-item">';
                $this->controls->checkbox_group($name, $term->term_id, esc_html($term->name));
                echo '</div>';
            }
            echo '<div style="clear: both"></div>';
            echo '</div>';
        }

        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Shows a language selector only if the blog is multilanguage.
     *
     * @param string $name
     * @param string $label
     * @param array $attrs
     */
    public function language($name = 'language', $label = '', $attrs = []) {
        if (!Newsletter::instance()->is_multilanguage()) {
            return;
        }
        if (empty($label)) {
            $label = __('Language', 'newsletter');
        }
        $attrs = $this->_merge_attrs($attrs);
        $this->_open('tnp-language');
        $this->_label($label);
        $this->controls->language($name);
        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Collects font details for a text: family, color, size and weight to be used
     * directly on CSS rules. Size is a pure number.
     *
     * Attributes:
     * - family: true|false enable or not the font family field
     * - family_default: true|false enables the default entry with an empty key value
     * - color: true|false enable or not the color field
     * - weight: true|false enable or not the weight field
     * - size: true|false enable or not the size selection
     *
     * @param type $name
     * @param type $label
     * @param array $attrs
     */
    public function font($name = 'font', $label = 'Font', $attrs = []) {
        $attrs = $this->_merge_base_attrs($attrs);
        $attrs = array_merge([
            'hide_family' => false,
            'family' => true,
            'color' => true,
            'size' => true,
            'weight' => true,
            'family_default' => false,
            'size_default' => false,
            'weight_default' => false,
                ], $attrs);

        $this->_open('tnp-font');
        $this->_label($label);

        $this->controls->css_font_family($name . '_family', !empty($attrs['family_default']));

        if ($attrs['size']) {
            $this->controls->css_font_size($name . '_size', !empty($attrs['size_default']));
        }
        if ($attrs['weight']) {
            $this->controls->css_font_weight($name . '_weight', !empty($attrs['weight_default']));
        }
        if ($attrs['color']) {
            $this->controls->color($name . '_color');
        }

        $this->_description($attrs);
        $this->_close();
    }

    /**
     * Collects fout number values representing the padding of a box. The values can
     * be found as {$name}_top, {$name}_bottom, {$name}_left, {$name}_right.
     *
     * @param type $name
     * @param type $label
     * @param type $attrs
     */
    public function padding($name = 'block_padding', $label = 'Padding', $attrs = []) {
        $attrs = $this->_merge_base_attrs($attrs);
        $attrs = array_merge(['padding_top' => 0, 'padding_left' => 0, 'padding_right' => 0, 'padding_bottom' => 0], $attrs);
        $field_only = !empty($attrs['field_only']);

        if (!$field_only) {
            $this->_open('tnp-padding');
            $this->_label($label);
        }
        echo '<div class="tnp-padding-fields">';
        echo '&larr;';
        $this->controls->text($name . '_left', 5);
        echo '&nbsp;&nbsp;&nbsp;';
        echo '&uarr;';
        $this->controls->text($name . '_top', 5);
        echo '&nbsp;&nbsp;&nbsp;';

        $this->controls->text($name . '_bottom', 5);
        echo '&darr;';
        echo '&nbsp;&nbsp;&nbsp;';
        $this->controls->text($name . '_right', 5);
        echo '&rarr;';
        echo '</div>';
        if (!$field_only) {
            $this->_description($attrs);
            $this->_close();
        }
    }

    /**
     * Background color selector for a block.
     */
    public function block_background() {
        $this->color('block_background', __('Block Background', 'newsletter'));
    }

    /**
     * Padding selector for a block.
     */
    public function block_padding() {
        $this->padding('block_padding', __('Padding', 'newsletter'));
    }

    public function block_commons() {

        $this->_open('tnp-block-commons');
        $this->_label('Padding and background');
        $this->controls->color('block_background');

        echo '&nbsp;&rarr;&nbsp;';
        $this->controls->checkbox('block_background_gradient');
        $this->controls->color('block_background_2');

        echo '&nbsp;&nbsp;&nbsp;';
        $this->padding('block_padding', '', ['field_only' => true]);
        echo '<div class="tnp-description">Gradients are displayed only by few clients</div>';
        $this->_close();
    }

}
