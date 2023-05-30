<?php // phpcs:ignoreFile ?>
<?php
	
if (!class_exists('newslettersDefault')) {
	class newslettersDefault extends wpMailPlugin {
		
		function default_styles($defaultstyles = array()) {
			
			$defaultstyles = array();
			
			$defaultstyles['newsletters'] = array(
				'name'					=>	"Theme Folder style.css",
				'url'					=>	$this -> render_url('css/style.css', 'default', false),
				'version'				=>	false,
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['jquery-ui'] = array(
				'name'					=>	"jQuery UI",
				'url'					=>	$this -> render_url('css/jquery-ui.css', 'default', false),
				'version'				=>	false,
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['fontawesome'] = array(
				'name'					=>	"FontAwesome",
				'url'					=>	$this -> render_url('css/fontawesome.css', 'default', false),
				'version'				=>	'4.7.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['select2'] = array(
				'name'					=>	"Select2",
				'url'					=>	$this -> render_url('css/select2.css', 'default', false),
				'version'				=>	'4.0.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			return $defaultstyles;
		}
		
		function default_scripts($defaultscripts = array()) {		
			
			$defaultscripts = array();
				
			$defaultscripts['jquery-ui-tabs'] = array(
				'name'					=>	__('jQuery UI Tabs', 'wp-mailinglist'),
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
				'footer'				=>	false,
			); 
			
			$defaultscripts['jquery-ui-button'] = array(
				'name'					=>	__('jQuery UI Button', 'wp-mailinglist'),
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
				'footer'				=>	false,
			); 
			
			$defaultscripts['jquery-ui-dialog'] = array(
				'name'					=>	__('jQuery UI Dialog', 'wp-mailinglist'),
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
				'footer'				=>	false,
			);
			
			$defaultscripts['jquery-ui-datepicker'] = array(
				'name'					=>	__('jQuery UI Datepicker', 'wp-mailinglist'),
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery', 'jquery-ui-core', 'jquery-ui-widget'),
				'footer'				=>	false,
			);
			
			$defaultscripts['select2'] = array(
				'name'					=>	__('Select2 - Drop Downs', 'wp-mailinglist'),
				'url'					=>	$this -> render_url('js/select2.js', 'default', false),
				'version'				=>	'4.0.0',
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			$defaultscripts['jquery-form'] = array(
				'name'					=>	__('jQuery Form', 'wp-mailinglist'),
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			); 
			
			$defaultscripts['recaptcha'] = array(
				'name'					=>	"reCAPTCHA",
				'url'					=>	false,
				'version'				=>	false,
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			return $defaultscripts;
		}
		
		function enqueuescript_after($handle = null, $script = null) {
			if (!empty($handle) && $handle == "jquery-ui-datepicker") {
				global $Html, $wp_locale;
				
				wp_enqueue_script('datepicker-i18n', $this -> render_url('js/datepicker-i18n.js', 'admin', false), array('jquery-ui-datepicker'));
			    
			    $isRTL = (empty($wp_locale -> is_rtl)) ? false : true;
			 
			    //localize our js
			    $aryArgs = array(
			        'closeText'         => __('Done', 'wp-mailinglist'),
			        'currentText'       => __('Today', 'wp-mailinglist'),
			        'monthNames'        => $Html -> strip_array_indices($wp_locale -> month),
			        'monthNamesShort'   => $Html -> strip_array_indices($wp_locale -> month_abbrev),
			        'monthStatus'       => __('Show a different month', 'wp-mailinglist'),
			        'dayNames'          => $Html -> strip_array_indices($wp_locale -> weekday),
			        'dayNamesShort'     => $Html -> strip_array_indices($wp_locale -> weekday_abbrev),
			        'dayNamesMin'       => $Html -> strip_array_indices($wp_locale -> weekday_initial),
			        'dateFormat'        => $Html -> dateformat_PHP_to_jQueryUI(get_option('date_format')),
			        'firstDay'          => get_option('start_of_week'),
			        'isRTL'             => $isRTL,
			    );
			 
			    // Pass the localized array to the enqueued JS
			    wp_localize_script('datepicker-i18n', 'objectL10n', $aryArgs);
			}
		}
	}
	
	$newslettersDefault = new newslettersDefault();
	
	add_filter('newsletters_default_styles', array($newslettersDefault, 'default_styles'));
	add_filter('newsletters_default_scripts', array($newslettersDefault, 'default_scripts'));
	add_action('newsletters_enqueuescript_after', array($newslettersDefault, 'enqueuescript_after'), 10, 2);
}	
	
?>