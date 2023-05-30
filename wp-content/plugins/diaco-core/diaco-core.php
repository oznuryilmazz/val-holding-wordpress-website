<?php
/*
  Plugin Name: Diaco Core
  Plugin URI: http://smartdemowp.com/diaco/
  Description: Diaco Core theme functions and library files.
  Version: 1.5
  Author: SmartDataSoft
  Author URI: http://smartdatasoft.com/
  License: GPLv2 or later
  Text Domain: diaco-core
  Domain Path: /languages/
 */

define( 'PLUGIN_DIR', dirname( __FILE__ ) . '/' );

if ( ! defined( 'DIACO_CORE_PLUGIN_URI' ) ) {
	define( 'DIACO_CORE_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
}

if ( ! class_exists( 'DiacoCore' ) ) {

	class DiacoCore {

		public static $plugindir, $pluginurl;

		function __construct() {

			self::$plugindir = dirname( __FILE__ );

			self::$pluginurl = plugins_url( '', __FILE__ );
		}


	}

	$diacoCore = new DiacoCore();


	require_once diacoCore::$plugindir . '/widgets/diaco-sidebar-recent-post-widget.php';
	require_once diacoCore::$plugindir . '/widgets/diaco-latest-post-widget.php';
	require_once DiacoCore::$plugindir . '/meta-box/page-meta.php';


	require_once diacoCore::$plugindir . '/elementor-addons/diaco-elementor.php';

	// post type
	require_once DiacoCore::$plugindir . '/post-type/custom-post-work.php';

}

function diaco_core_load_textdomain() {
	load_plugin_textdomain( 'diaco-core', false, dirname( __FILE__ ) . '/languages' );
}


add_action( 'plugins_loaded', 'diaco_core_load_textdomain' );
