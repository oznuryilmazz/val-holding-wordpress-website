<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main initiation class
 *
 * @since 1.0.0
 */
class Diaco_Elementor {

    /**
     * Add-on Version
     *
     * @since 1.0.0
     * @var  string
     */
    public $version = '1.0.1';

    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.4.0';

    /**
     * Constructor for the class
     *
     * Sets up all the appropriate hooks and actions
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        register_activation_hook(__FILE__, array($this, 'auto_deactivate'));
        if (!$this->is_supported_php()) {
            return;
        }
        $this->define_constants();
        $this->includes();
        $this->instantiate();
        $this->init_hooks();
    }

    /**
     * Initializes the class
     *
     * Checks for an existing instance
     * and if it does't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return object Class instance
     */
    public static function init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Define constants
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function define_constants() {
        define('ELECTRICITY_ELEMENTOR_VERSION', $this->version);
        define('DIACO_ELEMENTOR_FILE', __FILE__);
     
        define('DIACO_ELEMENTOR_PATH', dirname(DIACO_ELEMENTOR_FILE));
        define('DIACO_ELEMENTOR_INCLUDES', DIACO_ELEMENTOR_PATH . '/includes');
        define('DIACO_ELEMENTOR_URL', plugins_url('', DIACO_ELEMENTOR_FILE));
        define('DIACO_ELEMENTOR_ASSETS', DIACO_ELEMENTOR_URL . '/assets');
        define('DIACO_ELEMENTOR_ICONS', DIACO_ELEMENTOR_URL . '/icon');
    }

    /**
     * Include required files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require DIACO_ELEMENTOR_INCLUDES . '/functions.php';
        require DIACO_ELEMENTOR_INCLUDES . '/class-element.php';
        require DIACO_ELEMENTOR_INCLUDES . '/class-scripts.php';

        require DIACO_ELEMENTOR_PATH . '/icon/icon.php';
    }

    /**
     * Init Hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function init_hooks() {
        //Localize our plugin
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
    }

    /**
     * Instantiate classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function instantiate() {
        new \Diaco\Element();
        new \Diaco\Scripts();
    }

    /**
     * Plugin action links
     *
     * @param  array $links
     *
     * @return array
     */
    function plugin_action_links($links) {
        return $links;
    }

    /**
     * Check if the PHP version is supported
     *
     * @return bool
     */
    public function is_supported_php($min_php = null) {
        $min_php_ = $min_php ? $min_php : $this->min_php;
        if (version_compare(PHP_VERSION, $min_php_, '<=')) {
            return false;
        }
        return true;
    }

    /**
     * Show notice about PHP version
     *
     * @return void
     */
    function php_version_notice() {

        if ($this->is_supported_php() || !current_user_can('manage_options')) {
            return;
        }

        $error = __('Your installed PHP Version is: ', 'diaco-core') . PHP_VERSION . '. ';
        $error .= __('The <strong>Team Members for Elementor</strong> plugin requires PHP version <strong>', 'diaco-core') . $this->min_php . __('</strong> or greater.', 'diaco-core');
        ?>
        <div class="error">
            <p><?php printf($error); ?></p>
        </div>
        <?php
    }

    /**
     * Bail out if the php version is lower than
     *
     * @return void
     */
    function auto_deactivate() {
        if ($this->is_supported_php()) {
            return;
        }

        deactivate_plugins(plugin_basename(__FILE__));
        $error = __('<h1>An Error Occured</h1>', 'diaco-core');
        $error .= __('<h2>Your installed PHP Version is: ', 'diaco-core') . PHP_VERSION . '</h2>';
        $error .= __('You should update your PHP software or contact your host regarding this matter.</p>', 'diaco-core');
        wp_die($error, __('Plugin Activation Error', 'diaco-core'), array('back_link' => true));
    }

}

return Diaco_Elementor::init();
