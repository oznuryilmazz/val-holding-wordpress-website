<?php

/** For old style coders */
function tnp_register_block($dir) {
    return TNP_Composer::register_block($dir);
}

/**
 * Generates and HTML button for email using the values found on $options and
 * prefixed by $prefix, with the standard syntax of NewsletterFields::button().
 *
 * @param array $options
 * @param string $prefix
 * @return string
 */
function tnpc_button($options, $prefix = 'button') {
    return TNP_Composer::button($options, $prefix);
}

class TNP_Composer {

    static $block_dirs = array();

    static function register_block($dir) {
        // Checks
        $dir = realpath($dir);
        if (!$dir) {
            $error = new WP_Error('1', 'Seems not a valid path: ' . $dir);
            NewsletterEmails::instance()->logger->error($error);
            return $error;
        }

        $dir = wp_normalize_path($dir);

        if (!file_exists($dir . '/block.php')) {
            $error = new WP_Error('1', 'block.php missing on folder ' . $dir);
            NewsletterEmails::instance()->logger->error($error);
            return $error;
        }

        self::$block_dirs[] = $dir;
        return true;
    }

    /**
     * @param string $open
     * @param string $inner
     * @param string $close
     * @param string[] $markers
     *
     * @return string
     */
    static function wrap_html_element($open, $inner, $close, $markers = array('<!-- tnp -->', '<!-- /tnp -->')) {

        return $open . $markers[0] . $inner . $markers[1] . $close;
    }

    /**
     * @param string $block
     * @param string[] $markers
     *
     * @return string
     */
    static function unwrap_html_element($block, $markers = array('<!-- tnp -->', '<!-- /tnp -->')) {
        if (self::_has_markers($block, $markers)) {
            self::_escape_markers($markers);
            $pattern = sprintf('/%s(.*?)%s/s', $markers[0], $markers[1]);

            $matches = array();
            preg_match($pattern, $block, $matches);

            return $matches[1];
        }

        return $block;
    }

    /**
     * @param string $block
     * @param string[] $markers
     *
     * @return bool
     */
    private static function _has_markers($block, $markers = array('<!-- tnp -->', '<!-- /tnp -->')) {

        self::_escape_markers($markers);

        $pattern = sprintf('/%s(.*?)%s/s', $markers[0], $markers[1]);

        return preg_match($pattern, $block);
    }

    /**
     * Sources:
     * - https://webdesign.tutsplus.com/tutorials/creating-a-future-proof-responsive-email-without-media-queries--cms-23919
     *
     * @param type $email
     * @return type
     */
    static function get_html_open($email) {
        $open = '<!DOCTYPE html>' . "\n";
        $open .= '<html xmlns="https://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">' . "\n";
        $open .= '<head>' . "\n";
        $open .= '<title>{email_subject}</title>' . "\n";
        $open .= '<meta charset="utf-8">' . "\n";
        $open .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        $open .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
        $open .= '<meta name="format-detection" content="address=no">' . "\n";
        $open .= '<meta name="format-detection" content="telephone=no">' . "\n";
        $open .= '<meta name="format-detection" content="email=no">' . "\n";
        $open .= '<meta name="x-apple-disable-message-reformatting">' . "\n";

//        $open .= '<!--[if !mso]><!-->' . "\n";
//        $open .= '<meta http-equiv="X-UA-Compatible" content="IE=edge" />' . "\n";
//        $open .= '<!--<![endif]-->' . "\n";
//        $open .= '<!--[if mso]>' . "\n";

        $open .= '<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->' . "\n";

//        $open .= '<style type="text/css">';
//        $open .= 'table {border-collapse:collapse;border-spacing:0;margin:0;}';
//        $open .= 'div, td {padding:0;}';
//        $open .= 'div {margin:0 !important;}';
//        $open .= '</style>';
//        $open .= "\n";
//        $open .= '<noscript>';
//        $open .= '<xml>';
//        $open .= '<o:OfficeDocumentSettings>';
//        $open .= '<o:PixelsPerInch>96</o:PixelsPerInch>';
//        $open .= '</o:OfficeDocumentSettings>';
//        $open .= '</xml>';
//        $open .= '</noscript>';
//        $open .= "\n";
//        $open .= '<![endif]-->';
//        $open .= "\n";
        $open .= '<style type="text/css">' . "\n";
        $open .= NewsletterEmails::instance()->get_composer_css();
        $open .= "\n</style>\n";
        $open .= "</head>\n";
        $open .= '<body style="margin: 0; padding: 0; line-height: normal; word-spacing: normal;" dir="' . (is_rtl() ? 'rtl' : 'ltr') . '">';
        $open .= "\n";
        $open .= self::get_html_preheader($email);

        return $open;
    }

    static private function get_html_preheader($email) {

        if (empty($email->options['preheader'])) {
            return "";
        }

        $preheader_text = esc_html($email->options['preheader']);
        $html = "<div style=\"display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;\">$preheader_text</div>";
        $html .= "\n";

        return $html;
    }

    static function get_html_close($email) {
        return "</body>\n</html>";
    }

    /**
     *
     * @param TNP_Email $email
     * @return string
     */
    static function get_main_wrapper_open($email) {
        if (!isset($email->options['composer_background']) || $email->options['composer_background'] == 'inherit') {
            $bgcolor = '';
        } else {
            $bgcolor = $email->options['composer_background'];
        }

        return "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n" .
                "<tr>\n" .
                "<td bgcolor='$bgcolor' valign='top'><!-- tnp -->";
    }

    /**
     *
     * @param TNP_Email $email
     * @return string
     */
    static function get_main_wrapper_close($email) {
        return "\n<!-- /tnp -->\n" .
                "</td>\n" .
                "</tr>\n" .
                "</table>\n\n";
    }

    /**
     * Remove <doctype>, <body> and unnecessary envelopes for editing with composer
     *
     * @param string $html_email
     *
     * @return string
     */
    static function unwrap_email($html_email) {

        if (self::_has_markers($html_email)) {
            $html_email = self::unwrap_html_element($html_email);
        } else {
            //KEEP FOR OLD EMAIL COMPATIBILITY
            // Extracts only the body part
            $x = strpos($html_email, '<body');
            if ($x) {
                $x = strpos($html_email, '>', $x);
                $y = strpos($html_email, '</body>');
                $html_email = substr($html_email, $x + 1, $y - $x - 1);
            }

            /* Cleans up uncorrectly stored newsletter bodies */
            $html_email = preg_replace('/<style\s+.*?>.*?<\\/style>/is', '', $html_email);
            $html_email = preg_replace('/<meta.*?>/', '', $html_email);
            $html_email = preg_replace('/<title\s+.*?>.*?<\\/title>/i', '', $html_email);
            $html_email = trim($html_email);
        }

        // Required since esc_html DOES NOT escape the HTML entities (apparently)
        $html_email = str_replace('&', '&amp;', $html_email);
        $html_email = str_replace('"', '&quot;', $html_email);
        $html_email = str_replace('<', '&lt;', $html_email);
        $html_email = str_replace('>', '&gt;', $html_email);

        return $html_email;
    }

    private static function _escape_markers(&$markers) {
        $markers[0] = str_replace('/', '\/', $markers[0]);
        $markers[1] = str_replace('/', '\/', $markers[1]);
    }

    /**
     * Using the data collected inside $controls (and submitted by a form containing the
     * composer fields), updates the email. The message body is completed with doctype,
     * head, style and the main wrapper.
     *
     * @param TNP_Email $email
     * @param NewsletterControls $controls
     */
    static function update_email($email, $controls) {
        if (isset($controls->data['subject'])) {
            $email->subject = $controls->data['subject'];
        }

        // They should be only composer options
        foreach ($controls->data as $name => $value) {
            if (strpos($name, 'options_') === 0) {
                $email->options[substr($name, 8)] = $value;
            }
        }

        //if (isset($controls->data['preheader'])) {
        //    $email->options['preheader'] = $controls->data['preheader'];
        //}

        $email->editor = NewsletterEmails::EDITOR_COMPOSER;

        $email->message = self::get_html_open($email) . self::get_main_wrapper_open($email) .
                $controls->data['message'] . self::get_main_wrapper_close($email) . self::get_html_close($email);
    }

    /**
     * Prepares a controls object injecting the relevant fields from an email
     * which cannot be directly used by controls. If $email is null or missing,
     * $controls is prepared with default values.
     *
     * @param NewsletterControls $controls
     * @param TNP_Email $email
     */
    static function prepare_controls($controls, $email = null) {

        // Controls for a new email (which actually does not exist yet
        if (!empty($email)) {

            foreach ($email->options as $name => $value) {
                $controls->data['options_' . $name] = $value;
            }

            $controls->data['message'] = TNP_Composer::unwrap_email($email->message);
            $controls->data['subject'] = $email->subject;
            $controls->data['updated'] = $email->updated;
        }

        if (!empty($email->options['sender_email'])) {
            $controls->data['sender_email'] = $email->options['sender_email'];
        } else {
            $controls->data['sender_email'] = Newsletter::instance()->options['sender_email'];
        }

        if (!empty($email->options['sender_name'])) {
            $controls->data['sender_name'] = $email->options['sender_name'];
        } else {
            $controls->data['sender_name'] = Newsletter::instance()->options['sender_name'];
        }

        $controls->data = array_merge(TNP_Composer::get_global_style_defaults(), $controls->data);
    }

    /**
     * Extract inline edited post field from inline_edit_list[]
     *
     * @param array $inline_edit_list
     * @param string $field_type
     * @param int $post_id
     *
     * @return string
     */
    static function get_edited_inline_post_field($inline_edit_list, $field_type, $post_id) {

        foreach ($inline_edit_list as $edit) {
            if ($edit['type'] == $field_type && $edit['post_id'] == $post_id) {
                return $edit['content'];
            }
        }

        return '';
    }

    /**
     * Check if inline_edit_list[] have inline edit field for specific post
     *
     * @param array $inline_edit_list
     * @param string $field_type
     * @param int $post_id
     *
     * @return bool
     */
    static function is_post_field_edited_inline($inline_edit_list, $field_type, $post_id) {
        if (empty($inline_edit_list) || !is_array($inline_edit_list)) {
            return false;
        }
        foreach ($inline_edit_list as $edit) {
            if ($edit['type'] == $field_type && $edit['post_id'] == $post_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Creates the HTML for a button extrating from the options, with the provided prefix, the button attributes:
     *
     * - [prefix]_url The button URL
     * - [prefix]_font_family
     * - [prefix]_font_size
     * - [prefix]_font_weight
     * - [prefix]_label
     * - [prefix]_font_color The label color
     * - [prefix]_background The button color
     *
     * TODO: Add radius and possiblt the alignment
     *
     * @param array $options
     * @param string $prefix
     * @return string
     */
    static function button($options, $prefix = 'button') {

        if (empty($options[$prefix . '_label'])) {
            return;
        }
        $defaults = [
            $prefix . '_url' => '#',
            $prefix . '_font_family' => 'Helvetica, Arial, sans-serif',
            $prefix . '_font_color' => '#ffffff',
            $prefix . '_font_weight' => 'bold',
            $prefix . '_font_size' => 20,
            $prefix . '_background' => '#256F9C',
            $prefix . '_align' => 'center'
        ];

        $options = array_merge($defaults, array_filter($options));

        $b = '<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto"';
        if (!empty($options[$prefix . '_align'])) {
            $b .= ' align="' . esc_attr($options[$prefix . '_align']) . '"';
        }
        if (!empty($options[$prefix . '_width'])) {
            $b .= ' width="' . esc_attr($options[$prefix . '_width']) . '"';
        }
        $b .= '>';
        $b .= '<tr>';
        $b .= '<td align="center" bgcolor="' . $options[$prefix . '_background'] . '" role="presentation" style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px;background:' . $options[$prefix . '_background'] . '" valign="middle">';
        $b .= '<a href="' . $options[$prefix . '_url'] . '"';
        $b .= ' style="display:inline-block;background:' . $options[$prefix . '_background'] . ';color:' . $options[$prefix . '_font_color'] . ';font-family:' . $options[$prefix . '_font_family'] . ';font-size:' . $options[$prefix . '_font_size'] . 'px;font-weight:' . $options[$prefix . '_font_weight'] . ';line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:3px;"';
        $b .= ' target="_blank">';
        $b .= $options[$prefix . '_label'];
        $b .= '</a>';
        $b .= '</td></tr></table>';
        return $b;
    }

    /**
     * Generates an IMG tag, linked if the media has an URL.
     *
     * @param TNP_Media $media
     * @param string $style
     * @return string
     */
    static function image($media, $attr = []) {

        $default_attrs = [
            'style' => 'max-width: 100%; height: auto; display: inline-block',
            'class' => '',
            'link-style' => 'text-decoration: none; display: inline-block',
            'link-class' => null,
        ];

        $attr = array_merge($default_attrs, $attr);

        //Class and style attribute are mutually exclusive.
        //Class take priority to style because classes will transform to inline style inside block rendering operation
        if (!empty($attr['inline-class'])) {
            $styling = ' inline-class="' . esc_attr($attr['inline-class']) . '" ';
        } else {
            $styling = ' style="' . esc_attr($attr['style']) . '" ';
        }

        if (!empty($attr['class'])) {
            $styling .= ' class="' . esc_attr($attr['class']) . '" ';
        }

        //Class and style attribute are mutually exclusive.
        //Class take priority to style because classes will transform to inline style inside block rendering operation
        if (!empty($attr['link-class'])) {
            $link_styling = ' inline-class="' . esc_attr($attr['link-class']) . '" ';
        } else {
            $link_styling = ' style="' . esc_attr($attr['link-style']) . '" ';
        }

        $b = '';
        if ($media->link) {
            $b .= '<a href="' . esc_attr($media->link) . '" target="_blank" rel="noopener nofollow" style="display: inline-block; font-size: 0; text-decoration: none; line-height: normal!important">';
        } else {
            // The span grants images are not upscaled when fluid (two columns posts block)
            $b .= '<span style="display: inline-block; font-size: 0; text-decoration: none; line-height: normal!important">';
        }
        if ($media) {
            $b .= '<img src="' . esc_attr($media->url) . '" width="' . esc_attr($media->width) . '"';
            if ($media->height) {
                $b .= ' height="' . esc_attr($media->height) . '"';
            }
            $b .= ' alt="' . esc_attr($media->alt) . '"'
                    . ' border="0"'
                    . ' style="display: inline-block; max-width: 100%!important; padding: 0; border: 0; font-size: 12px"'
                    . ' class="' . esc_attr($attr['class']) . '" '
                    . '>';
        }

        if ($media->link) {
            $b .= '</a>';
        } else {
            $b .= '</span>';
        }

        return $b;
    }

    /**
     * Returns a WP media ID for the specified post (or false if nothing can be found)
     * looking for the featured image or, if missing, taking the first media in the gallery and
     * if again missing, searching the first reference to a media in the post content.
     *
     * The media ID is not checked for real existance of the associated attachment.
     *
     * @param int $post_id
     * @return int
     */
    static function get_post_thumbnail_id($post_id) {
        if (is_object($post_id)) {
            $post_id = $post_id->ID;
        }

        // Find a media id to be used as featured image
        $media_id = get_post_thumbnail_id($post_id);
        if (!empty($media_id)) {
            return $media_id;
        }

        $attachments = get_children(array('numberpost' => 1, 'post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order'));
        if (!empty($attachments)) {
            foreach ($attachments as $id => &$attachment) {
                return $id;
            }
        }

        $post = get_post($post_id);

        $r = preg_match('/wp-image-(\d+)/', $post->post_content, $matches);
        if ($matches) {
            return (int) $matches[1];
        }

        return false;
    }

    /**
     * Builds a TNP_Media object to be used in newsletters from a WP media/attachement ID. The returned
     * media has a size which best match the one requested (this is the standard WP behavior, plugins
     * could change it).
     *
     * @param int $media_id
     * @param array $size
     * @return \TNP_Media
     */
    function get_media($media_id, $size) {
        $src = wp_get_attachment_image_src($media_id, $size);
        if (!$src) {
            return null;
        }
        $media = new TNP_Media();
        $media->id = $media_id;
        $media->url = $src[0];
        $media->width = $src[1];
        $media->height = $src[2];
        return $media;
    }

    static function post_content($post) {
        $content = $post->post_content;
        
        if (!has_block($post)) {
            $content = wpautop($content);
        }
        
        if (true || $options['enable shortcodes']) {
            remove_shortcode('gallery');
            add_shortcode('gallery', 'tnp_gallery_shortcode');
            $content = do_shortcode($content);
        }
        $content = str_replace('<p>', '<p inline-class="p">', $content);
        $content = str_replace('<li>', '<li inline-class="li">', $content);

        $selected_images = array();
        if (preg_match_all('/<img [^>]+>/', $content, $matches)) {
            foreach ($matches[0] as $image) {
                if (preg_match('/wp-image-([0-9]+)/i', $image, $class_id) && ( $attachment_id = absint($class_id[1]) )) {
                    $selected_images[$image] = $attachment_id;
                }
            }
        }

        foreach ($selected_images as $image => $attachment_id) {
            $src = tnp_media_resize($attachment_id, array(600, 0));
            if (is_wp_error($src)) {
                continue;
            }
            $content = str_replace($image, '<img src="' . $src . '" width="600" style="max-width: 100%">', $content);
        }

        return $content;
    }

    static function get_global_style_defaults() {
        return [
            'options_composer_title_font_family' => 'Verdana, Geneva, sans-serif',
            'options_composer_title_font_size' => 32,
            'options_composer_title_font_weight' => 'normal',
            'options_composer_title_font_color' => '#222222',
            'options_composer_text_font_family' => 'Verdana, Geneva, sans-serif',
            'options_composer_text_font_size' => 16,
            'options_composer_text_font_weight' => 'normal',
            'options_composer_text_font_color' => '#222222',
            'options_composer_button_font_family' => 'Verdana, Geneva, sans-serif',
            'options_composer_button_font_size' => 16,
            'options_composer_button_font_weight' => 'normal',
            'options_composer_button_font_color' => '#FFFFFF',
            'options_composer_button_background_color' => '#256F9C',
            'options_composer_background' => '#FFFFFF',
            'options_composer_block_background' => '#FFFFFF',
            'options_composer_width' => '600'
        ];
    }

    /**
     * Inspired by: https://webdesign.tutsplus.com/tutorials/creating-a-future-proof-responsive-email-without-media-queries--cms-23919
     * 
     * Attributes:
     * - columns: number of columns [2]
     * - padding: cells padding [10]
     * - responsive: il on mobile the cell should stack up [true]
     * - width: the whole row width, it should reduced by the external row padding [600]
     * 
     * @param string[] $items
     * @param array $attrs
     * @return string
     */
    static function grid($items = [], $attrs = []) {
        $attrs = wp_parse_args($attrs, ['width' => 600, 'columns' => 2, 'padding' => 10, 'responsive' => true]);
        $width = (int) $attrs['width'];
        $columns = (int) $attrs['columns'];
        $padding = (int) $attrs['padding'];
        $column_width = $width / $columns;
        $td_width = 100 / $columns;
        $chunks = array_chunk($items, $columns);

        if ($attrs['responsive']) {

            $e = '';
            foreach ($chunks as &$chunk) {
                $e .= '<div style="text-align:center;font-size:0;">';
                $e .= '<!--[if mso]><table role="presentation" width="100%"><tr><![endif]-->';
                foreach ($chunk as &$item) {
                    $e .= '<!--[if mso]><td width="' . $td_width . '%" style="width:' . $td_width . '%;padding:' . $padding . 'px" valign="top"><![endif]-->';

                    $e .= '<div class="max-width-100" style="width:100%;max-width:' . $column_width . 'px;display:inline-block;vertical-align: top;box-sizing: border-box;">';

                    // This element to add padding without deal with border-box not well supported
                    $e .= '<div style="padding:' . $padding . 'px;">';
                    $e .= $item;
                    $e .= '</div>';
                    $e .= '</div>';

                    $e .= '<!--[if mso]></td><![endif]-->';
                }
                $e .= '<!--[if mso]></tr></table><![endif]-->';
                $e .= '</div>';
            }

            return $e;
        } else {
            $e = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 100%!important">';
            foreach ($chunks as &$chunk) {
                $e .= '<tr>';
                foreach ($chunk as &$item) {
                    $e .= '<td width="' . $td_width . '%" style="width:' . $td_width . '%; padding:' . $padding . 'px" valign="top">';
                    $e .= $item;
                    $e .= '</td>';
                }
                $e .= '</tr>';
            }
            $e .= '</table>';
            return $e;
        }
    }

    static function get_text_style($options, $prefix, $composer, $attrs = []) {
        return self::get_style($options, $prefix, $composer, 'text', $attrs);
    }

    static function get_title_style($options, $prefix, $composer, $attrs = []) {
        return self::get_style($options, $prefix, $composer, 'title', $attrs);
    }

    static function get_style($options, $prefix, $composer, $type = 'text', $attrs = []) {
        $style = new TNP_Style();
        $scale = 1.0;
        if (!empty($attrs['scale'])) {
            $scale = (float) $attrs['scale'];
        }
        if (!empty($prefix)) {
            $prefix .= '_';
        }

        $style->font_family = empty($options[$prefix . 'font_family']) ? $composer[$type . '_font_family'] : $options[$prefix . 'font_family'];
        $style->font_size = empty($options[$prefix . 'font_size']) ? round($composer[$type . '_font_size'] * $scale) : $options[$prefix . 'font_size'];
        $style->font_color = empty($options[$prefix . 'font_color']) ? $composer[$type . '_font_color'] : $options[$prefix . 'font_color'];
        $style->font_weight = empty($options[$prefix . 'font_weight']) ? $composer[$type . '_font_weight'] : $options[$prefix . 'font_weight'];
        if ($type === 'button') {
            $style->background = empty($options[$prefix . 'background']) ? $composer[$type . '_background_color'] : $options[$prefix . 'background'];
        }
        return $style;
    }

    static function get_button_options($options, $prefix, $composer) {
        $button_options = [];
        $scale = 1;
        $button_options['button_font_family'] = empty($options[$prefix . '_font_family']) ? $composer['button_font_family'] : $options[$prefix . '_font_family'];
        $button_options['button_font_size'] = empty($options[$prefix . '_font_size']) ? round($composer['button_font_size'] * $scale) : $options[$prefix . '_font_size'];
        $button_options['button_font_color'] = empty($options[$prefix . '_font_color']) ? $composer['button_font_color'] : $options[$prefix . '_font_color'];
        $button_options['button_font_weight'] = empty($options[$prefix . '_font_weight']) ? $composer['button_font_weight'] : $options[$prefix . '_font_weight'];
        $button_options['button_background'] = empty($options[$prefix . '_background']) ? $composer['button_background_color'] : $options[$prefix . '_background'];
        $button_options['button_align'] = empty($options[$prefix . '_align']) ? 'center' : $options[$prefix . '_align'];
        $button_options['button_width'] = empty($options[$prefix . '_width']) ? 'center' : $options[$prefix . '_width'];
        $button_options['button_url'] = empty($options[$prefix . '_url']) ? '#' : $options[$prefix . '_url'];
        $button_options['button_label'] = empty($options[$prefix . '_label']) ? '' : $options[$prefix . '_label'];

        return $button_options;
    }

    static function convert_to_text($html) {
        if (!class_exists('DOMDocument')) {
            return '';
        }

        if (!function_exists('ctype_space')) {
            return '';
        }

        // Replace '&' with '&amp;' in URLs to avoid warnings about inavlid entities from loadHTML()
        // Todo: make this more general using a regular expression
        //$logger = PlaintextNewsletterAddon::$instance->get_logger();
        //$logger->debug('html="' . $html . '"');
        $html = str_replace(
                array('&nk=', '&nek=', '&id='),
                array('&amp;nk=', '&amp;nek=', '&amp;id='),
                $html);
        //$logger->debug('new html="' . $html . '"');
        //
        $output = '';

        // Prevents warnings for problems with the HTML
        if (function_exists('libxml_use_internal_errors')) {
            libxml_use_internal_errors(true);
        }
        $dom = new DOMDocument();
        $r = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        if (!$r) {
            return '';
        }
        $bodylist = $dom->getElementsByTagName('body');
        // Of course it should be a single element
        foreach ($bodylist as $body) {
            self::process_dom_element($body, $output);
        }
        return $output;
    }

    static function process_dom_element(DOMElement $parent, &$output) {
        foreach ($parent->childNodes as $node) {
            if (is_a($node, 'DOMElement') && ($node->tagName != 'style')) {

                if ($node->tagName == 'br') {
                    $output .= "\n";
                    continue;
                }
                
                self::process_dom_element($node, $output);

                if ($node->tagName == 'li') {
                    if ((strlen($output) >= 1) && (substr($output, -1) != "\n")) {
                        $output .= "\n";
                    }
                    continue;
                }

                // If the containing tag was a block level tag, we add a couple of line ending
                if ($node->tagName == 'p' || $node->tagName == 'div' || $node->tagName == 'td') {
                    // Avoid more than one blank line between elements
                    if ((strlen($output) >= 2) && (substr($output, -2) != "\n\n")) {
                        $output .= "\n\n";
                    }
                }

                if ($node->tagName == 'a') {
                    // Check if the children is an image
                    if (is_a($node->childNodes[0], 'DOMElement')) {
                        if ($node->childNodes[0]->tagName == 'img') {
                            continue;
                        }
                    }
                    $output .= ' (' . $node->getAttribute('href') . ') ';
                    continue;
                } elseif ($node->tagName == 'img') {
                    $output .= $node->getAttribute('alt');
                }
            } elseif (is_a($node, 'DOMText')) {

                // ???
                $decoded = utf8_decode($node->wholeText);
                //$decoded = trim(html_entity_decode($node->wholeText));
                // We could avoid ctype_*
                if (ctype_space($decoded)) {
                    // Append blank only if last character output is not blank.
                    if ((strlen($output) > 0) && !ctype_space(substr($output, -1))) {
                        $output .= ' ';
                    }
                } else {
                    $output .= trim($node->wholeText);
                    $output .= ' ';
                }
            }
        }
    }

}

class TNP_Style {

    var $font_family;
    var $font_size;
    var $font_weight;
    var $font_color;
    var $background;

    function echo_css($scale = 1.0) {
        echo 'font-size: ', round($this->font_size * $scale), 'px;';
        echo 'font-family: ', $this->font_family, ';';
        echo 'font-weight: ', $this->font_weight, ';';
        echo 'color: ', $this->font_color, ';';
    }

}

/**
 * Generate multicolumn and responsive html template for email.
 * Initialize class with max columns per row and start to add cells.
 */
class TNP_Composer_Grid_System {

    /**
     * @var TNP_Composer_Grid_Row[]
     */
    private $rows;

    /**
     * @var int
     */
    private $cells_per_row;

    /**
     * @var int
     */
    private $cells_counter;

    /**
     * TNP_Composer_Grid_System constructor.
     *
     * @param int $columns_per_row Max columns per row
     */
    public function __construct($columns_per_row) {
        $this->cells_per_row = $columns_per_row;
        $this->cells_counter = 0;
        $this->rows = [];
    }

    public function __toString() {
        return $this->render();
    }

    /**
     * Add cell to grid
     *
     * @param TNP_Composer_Grid_Cell $cell
     */
    public function add_cell($cell) {

        if ($this->cells_counter % $this->cells_per_row === 0) {
            $this->add_row(new TNP_Composer_Grid_Row());
        }

        $row_idx = (int) floor($this->cells_counter / $this->cells_per_row);
        $this->rows[$row_idx]->add_cell($cell);
        $this->cells_counter++;
    }

    private function add_row($row) {
        $this->rows[] = $row;
    }

    public function render() {

        $str = '';
        foreach ($this->rows as $row) {
            $str .= $row->render();
        }

        return $str;
    }

}

/**
 * Class TNP_Composer_Grid_Row
 */
class TNP_Composer_Grid_Row {

    /**
     * @var TNP_Composer_Grid_Cell[]
     */
    private $cells;

    public function __construct(...$cells) {
        if (!empty($cells)) {
            foreach ($cells as $cell) {
                $this->add_cell($cell);
            }
        }
    }

    /**
     * @param TNP_Composer_Grid_Cell $cell
     */
    public function add_cell($cell) {
        $this->cells[] = $cell;
    }

    public function render() {
        $rendered_cells = '';
        $column_percentage_width = round(100 / $this->cells_count(), 0, PHP_ROUND_HALF_DOWN) . '%';
        foreach ($this->cells as $cell) {
            $rendered_cells .= $cell->render(['width' => $column_percentage_width]);
        }

        $row_template = $this->get_template();

        return str_replace('TNP_ROW_CONTENT_PH', $rendered_cells, $row_template);
    }

    private function cells_count() {
        return count($this->cells);
    }

    private function get_template() {
        return "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tbody><tr><td>TNP_ROW_CONTENT_PH</td></tr></tbody></table>";
    }

}

/**
 * Class TNP_Composer_Grid_Cell
 */
class TNP_Composer_Grid_Cell {

    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    public $args;

    public function __construct($content = null, $args = []) {
        $default_args = [
            'width' => '100%',
            'class' => '',
            'align' => 'left',
            'valign' => 'top'
        ];

        $this->args = array_merge($default_args, $args);

        $this->content = $content ? $content : '';
    }

    public function add_content($content) {
        $this->content .= $content;
    }

    public function render($args) {
        $this->args = array_merge($this->args, $args);

        $column_template = $this->get_template();
        $column = str_replace(
                [
                    'TNP_ALIGN_PH',
                    'TNP_VALIGN_PH',
                    'TNP_WIDTH_PH',
                    'TNP_CLASS_PH',
                    'TNP_COLUMN_CONTENT_PH'
                ], [
            $this->args['align'],
            $this->args['valign'],
            $this->args['width'],
            $this->args['class'],
            $this->content
                ], $column_template);

        return $column;
    }

    private function get_template() {
        return "<table border='0' cellpadding='0' cellspacing='0' width='TNP_WIDTH_PH' align='left' style='table-layout: fixed;' class='responsive'>
                    <tbody>
                            <tr>
                                <td border='0' style='padding: 20px 10px 40px;' align='TNP_ALIGN_PH' valign='TNP_VALIGN_PH' class='TNP_CLASS_PH'>
                                    TNP_COLUMN_CONTENT_PH
                                </td>
                            </tr>
                    </tbody>
                </table>";
    }

}

class TNP_Composer_Component_Factory {

    private $options;

    /**
     * TNP_Composer_Component_Factory constructor.
     *
     * @param Controller$controller
     */
    public function __construct($controller) {
        
    }

    function heading() {
        
    }

    function paragraph() {
        
    }

    function link() {
        
    }

    function button() {
        
    }

    function image() {
        
    }

}
