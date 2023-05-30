<?php
// phpcs:ignoreFile

if (!class_exists('newslettersBootstrap')) {
	class newslettersBootstrap extends wpMailPlugin {
		
		function default_styles($defaultstyles = array()) {
			
			$defaultstyles = array();
			
			$defaultstyles['newsletters-bootstrap'] = array(
				'name'					=>	"Bootstrap",
				'url'					=>	$this -> render_url('bootstrap-5.1.3-dist/css/bootstrap.min.css', 'assets', false), //$this -> render_url('bootstrap-5.0.2-dist/css/bootstrap.min.css', 'assets', false),
				'version'				=>	'5.1.3',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['bootstrap-datepicker'] = array(
				'name'					=>	"Bootstrap Datepicker",
				'url'					=>	$this -> render_url('css/bootstrap-datepicker.min.css', 'default2', false),
				'version'				=>	'1.9.0',
				'deps'					=>	array('bootstrap'),
				'media'					=>	"all",
			);
			
			$defaultstyles['fontawesome'] = array(
				'name'					=>	"FontAwesome",
				'url'					=>	$this -> render_url('css/fontawesome.css', 'default2', false),
				'version'				=>	'4.7.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['select2'] = array(
				'name'					=>	"Select2",
				'url'					=>	$this -> render_url('css/select2.css', 'default2', false),
				'version'				=>	'4.0.0',
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			$defaultstyles['newsletters'] = array(
				'name'					=>	"Theme Folder style.css (recommended)",
				'url'					=>	$this -> render_url('css/style.css', 'default2', false),
				'version'				=>	false,
				'deps'					=>	false,
				'media'					=>	"all",
			);
			
			return $defaultstyles;
		}
		
		function default_scripts($defaultscripts = array()) {
			
			$defaultscripts = array();
			

			$defaultscripts['bootstrap'] = array(
				'name'					=>	"Bootstrap",
				'url'					=>	$this -> render_url('bootstrap-5.1.3-dist/js/bootstrap.min.js', 'assets', false),
				'version'				=>	'5.1.3',
				'deps'					=>	array('jquery'),
				'footer'				=>	false,
			);
			
			$defaultscripts['bootstrap-datepicker'] = array(
				'name'					=>	"Bootstrap Datepicker",
				'url'					=>	$this -> render_url('js/bootstrap-datepicker.min.js', 'default2', false),
				'version'				=>	"1.9.0",
				'deps'					=>	array('jquery', 'bootstrap'),
				'footer'				=>	false,
			);
			

			$defaultscripts['select2'] = array(
				'name'					=>	"Select2",
				'url'					=>	$this -> render_url('js/select2.js'),
				'version'				=>	"4.0.0",
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
			if (!empty($handle) && $handle == "bootstrap-datepicker") {
				wp_enqueue_script('bootstrap-datepicker-i18n', $this -> render_url('js/datepicker-i18n.js', 'default2', false), array('jquery', 'bootstrap', 'bootstrap-datepicker'));
				
				//localize our js
				global $Html, $wp_locale;
			    
			    $aryArgs = array(
				    'days'				=>	$Html -> strip_array_indices($wp_locale -> weekday),
				    'daysShort'			=>	$Html -> strip_array_indices($wp_locale -> weekday_abbrev),
				    'daysMin'			=>	$Html -> strip_array_indices($wp_locale -> weekday_initial),
				    'months'			=>	$Html -> strip_array_indices($wp_locale -> month),
				    'monthsShort'		=>	$Html -> strip_array_indices($wp_locale -> month_abbrev),
				    'today'				=>	__('Today', 'wp-mailinglist'),
				    'clear'				=>	__('Clear', 'wp-mailinglist'),
				    'rtl'				=>	(!empty($wp_locale -> is_rtl) ? true : false),
			    );
			 
			    // Pass the localized array to the enqueued JS
			    wp_localize_script('bootstrap-datepicker-i18n', 'bootstrap_datepicker_dates', $aryArgs);
			}
		}
		
		function datepicker_output($output = null, $optinid = null, $field = null) {
			global $Html, $Subscriber;
			
			if (is_admin() && !defined('DOING_AJAX')) {
				return $output;
			}
			
			$output = "";
			$locale = get_locale();
			
			ob_start();
			
			$field_value = false;
			if (!empty($Subscriber -> data[$field -> slug])) {
				$field_value = maybe_unserialize($Subscriber -> data[$field -> slug]);
			}
			
			$currentDate = "";
			if (!empty($field_value) && $field_value != "0000-00-00") {
				if (is_array($field_value)) {
					$currentDate = date_i18n(get_option('date_format'), strtotime($field_value['d'] . '/' . $field_value['m'] . '/' . $field_value['y']));
				} else {
					$currentDate = date_i18n(get_option('date_format'), strtotime($field_value));
				}
			}
			
			if (!empty($currentDate)) {
				$defaultDate = 'new Date(' . date_i18n("Y", strtotime($currentDate)) . ', ' . date_i18n("m", strtotime($currentDate)) . ', ' . date_i18n("d", strtotime($currentDate)) . ')';
			} else {
				$defaultDate = 'new Date(' . date_i18n("Y") . ', ' . date_i18n("m") . ', ' . date_i18n("d") . ')';
			}
			
			?>
			
			<div id="newsletters-<?php echo esc_html( $optinid . $field -> slug); ?>-dateholder" class="newsletters-dateholder">
				<div class="input-group date">
					<input type="text" class="form-control wpmlpredate wpmltext wpml wpmlpredate<?php echo ((!empty($_POST['wpmlerrors'][$field -> slug])) ? ' ' . 'wpmlfielderror' : ''); ?>" value="<?php echo esc_attr(wp_unslash($currentDate)); ?>" name="<?php echo esc_html( $field -> slug); ?>" id="wpml-<?php echo esc_html( $optinid . $field -> slug); ?>" />
					<span class="input-group-addon input-group-append">
						<span class="input-group-text"><i class="fa fa-calendar"></i></span>
					</span>
				</div>
			</div>
			
			<?php if (empty($offsite)) : ?>
				<script type="text/javascript">			
				jQuery(document).ready(function() {
					jQuery('#newsletters-<?php echo esc_html( $optinid . $field -> slug); ?>-dateholder .input-group.date').datepicker({
						container: '#newsletters-<?php echo esc_html( $optinid . $field -> slug); ?>-dateholder',
						autoclose: true,
						format: '<?php echo esc_html( $this -> dateformat_php_to_bootstrap_datepicker(get_option('date_format'))); ?>',
						//language: '<?php echo str_replace("_", "-", $locale); ?>',
						language: 'en',
						todayBtn: true,
						todayHighlight: true
					})
				});
				</script>
			<?php endif; ?>
			
			<?php
				
			$output = ob_get_clean();
			
			return $output;
		}
		
		function dateformat_php_to_bootstrap_datepicker($php_format = null) {
		    $SYMBOLS_MATCHING = array(
		        // Day
		        'd' => 'dd',
		        'D' => 'D',
		        'j' => 'd',
		        'l' => 'DD',
		        'N' => '',
		        'S' => '',
		        'w' => '',
		        'z' => 'o',
		        // Week
		        'W' => '',
		        // Month
		        'F' => 'MM',
		        'm' => 'mm',
		        'M' => 'M',
		        'n' => 'm',
		        't' => '',
		        // Year
		        'L' => '',
		        'o' => '',
		        'Y' => 'yyyy',
		        'y' => 'yy',
		        // Time
		        'a' => '',
		        'A' => '',
		        'B' => '',
		        'g' => '',
		        'G' => '',
		        'h' => '',
		        'H' => '',
		        'i' => '',
		        's' => '',
		        'u' => ''
		    );
		    $jqueryui_format = "";
		    $escaping = false;
		    for ($i = 0; $i < strlen($php_format); $i++) {
		        $char = $php_format[$i];
		        if ($char === '\\') {
		            $i++;
		            if($escaping) $jqueryui_format .= $php_format[$i];
		            else $jqueryui_format .= '\'' . $php_format[$i];
		            $escaping = true;
		        } else {
		            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
		            if(isset($SYMBOLS_MATCHING[$char]))
		                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
		            else
		                $jqueryui_format .= $char;
		        }
		    }
		    return $jqueryui_format;
		}
	}
	
	$newslettersBootstrap = new newslettersBootstrap();
	
	add_filter('newsletters_default_styles', array($newslettersBootstrap, 'default_styles'));
	add_filter('newsletters_default_scripts', array($newslettersBootstrap, 'default_scripts'));
	add_action('newsletters_enqueuescript_after', array($newslettersBootstrap, 'enqueuescript_after'), 10, 2);
	add_filter('newsletters_datepicker_output', array($newslettersBootstrap, 'datepicker_output'), 10, 3);
}	
	
?>