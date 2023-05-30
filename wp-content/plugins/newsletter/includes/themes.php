<?php

defined('ABSPATH') || exit;

/**
 * @property string $id Theme identifier
 * @property string $dir Absolute path to the theme folder
 * @property string $name Theme name
 */
class TNP_Theme {

    var $dir;
    var $name;

    public function get_defaults() {
        @include $this->dir . '/theme-defaults.php';
        if (!isset($theme_defaults) || !is_array($theme_defaults)) {
            return array();
        }
        return $theme_defaults;
    }
}

/**
 * Registers a Newsletter theme to be shown as option on standard newsletter creation
 * Designers love functions...
 * @param string $dir The absolute path of a folder containing a Newsletter theme
 */
function tnp_register_theme($dir) {
    NewsletterThemes::register_theme($dir);
}

class NewsletterThemes {

    var $module;
    var $is_extension = false;
    static $registered_theme_dirs = array();

    static function register_theme($dir) {
        if (!file_exists($dir . '/theme.php')) {
            $error = new WP_Error('1', 'theme.php missing on folder ' . $dir);
            return $error;
        }
        self::$registered_theme_dirs[] = $dir;
        return true;
    }

    function __construct($module, $is_extension = false) {
        $this->module = $module;
        $this->is_extension = $is_extension;
    }

    /** 
     * Build an associative array which represent a theme starting from the theme
     * parsing the files in the theme folder.<br>
     * dir - the full path to the theme folder<br>
     * url - the full url to the theme folder (to reference assets like images)<br>
     * id - the folder name, used as unique identifier<br>
     * screenshot - url to an image representing the theme (400x400)<br>
     * name - the readable theme name extracted from the theme.php<br>
     * 
     * description - not used
     * type - not used
     * 
     * @param string $dir
     * @return array
     */
    function build_theme($dir) {

        if (!is_dir($dir)) {
            return null;
        }
        
        if (!is_file($dir . '/theme.php')) {
            return null;
        }

        $data = get_file_data($dir . '/theme.php', array('name' => 'Name', 'type' => 'Type', 'description' => 'Description'));
        $data['id'] = basename($dir);
        $data['dir'] = $dir;
        if (empty($data['name'])) {
            $data['name'] = $data['id'];
        }
        
        if (empty($data['type'])) {
            $data['type'] = 'standard';
        }
        $relative_dir = substr($dir, strlen(WP_CONTENT_DIR));
        $data['url'] = content_url($relative_dir);
        $screenshot = $dir . '/screenshot.png';
        if (is_file($screenshot)) {
            $relative_dir = substr($dir, strlen(WP_CONTENT_DIR));
            $data['screenshot'] = $data['url'] . '/screenshot.png';
        } else {
            $data['screenshot'] = plugins_url('newsletter') . '/emails/images/theme-screenshot.png';
        }
        return $data;
    }

    function get_all_with_data() {
        $list = array();

        // Packaged themes
        $list['default'] = $this->build_theme(NEWSLETTER_DIR . '/emails/themes/default');
        $list['blank'] = $this->build_theme(NEWSLETTER_DIR . '/emails/themes/blank');
        $list['cta-2015'] = $this->build_theme(NEWSLETTER_DIR . '/emails/themes/cta-2015');
        $list['vimeo-like'] = $this->build_theme(NEWSLETTER_DIR . '/emails/themes/vimeo-like');
        $list['pint'] = $this->build_theme(NEWSLETTER_DIR . '/emails/themes/pint');

        // Extensions folder scan
        $dir = WP_CONTENT_DIR . '/extensions/newsletter/' . $this->module . '/themes';
        $handle = @opendir($dir);

        if ($handle !== false) {
            while ($file = readdir($handle)) {

                $data = $this->build_theme($dir . '/' . $file);

                if (!$data || isset($list[$data['id']])) {
                    continue;
                }

                $list[$data['id']] = $data;
            }
            closedir($handle);
        }
        
        // Registered themes
        do_action('newsletter_register_themes');

        foreach (self::$registered_theme_dirs as $dir) {
            $data = $this->build_theme($dir);
            if (!$data || isset($list[$data['id']])) {
                continue;
            }

            $list[$data['id']] = $data;
        }

        return $list;
    }

    /**
     * Returns a data structure containing the theme details.
     * 
     * @param string $id
     * @return array
     */
    function get_theme($id) {
        $themes = $this->get_all_with_data();
        if (isset($themes[$id])) {
            return $themes[$id];
        }
        return null;
    }

    /**
     *
     * @param type $theme
     * @param type $options
     * @param type $module
     */
    function save_options($theme_id, &$options) {
        $theme_options = array();
        foreach ($options as $key => &$value) {
            if (substr($key, 0, 6) != 'theme_')
                continue;
            $theme_options[$key] = $value;
        }
        update_option('newsletter_' . $this->module . '_theme_' . $theme_id, $theme_options, false);
    }

    function get_options($theme_id) {
        $options = get_option('newsletter_' . $this->module . '_theme_' . $theme_id);
        // To avoid merge problems.
        if (!is_array($options)) {
            $options = array();
        }
        
        $theme = $this->get_theme($theme_id);
        
        $file = $theme['dir'] . '/theme-defaults.php';
        if (is_file($file)) {
            @include $file;
        }
        if (isset($theme_defaults) && is_array($theme_defaults)) {
            $options = array_merge($theme_defaults, $options);
        }
        
        // main options merge
        $main_options = Newsletter::instance()->options;
        foreach ($main_options as $key => $value) {
            $options['main_' . $key] = $value;
        }
        
        $info_options = Newsletter::instance()->get_options('info');
        foreach ($info_options as $key => $value) {
            $options['main_' . $key] = $value;
        }
        
        return $options;
    }
}

