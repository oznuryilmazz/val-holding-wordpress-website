<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('wpMailPlugin')) {
	class wpMailPlugin extends wpMailCheckinit {

		var $name = 'Newsletters';
		var $plugin_base;
		var $pre = 'wpml';
		var $version = '4.8.8';
		var $dbversion = '1.2.3';
		var $debugging = false;			//set to "true" to turn on debugging  
		var $debug_level = 2; 			//set to 1 for only database errors and var dump; 2 for PHP errors as well
		var $post_errors = array();
		var $intervals = array();
		var $menus = array();
		var $submenus = array();
		var $cache = array();

		var $sections = array(
			//'about'						=>	"newsletters-about",
			'welcome'					=> 	"newsletters",
			'submitserial'				=>	"newsletters-submitserial",
			'gdpr'						=>	"newsletters-gdpr",
			'forms'						=>	"newsletters-forms",
			'send'						=>	"newsletters-create",
			'autoresponders'			=>	"newsletters-autoresponders",
			'autoresponderemails'		=>	"newsletters-autoresponderemails",
			'lists'						=>	"newsletters-lists",
			'groups'					=>	"newsletters-groups",
			'subscribers'				=>	"newsletters-subscribers",
			'fields'					=>	"newsletters-fields",
			'importexport'				=>	"newsletters-importexport",
			'themes'					=>	"newsletters-themes",
			'templates'					=>	"newsletters-templates",
			'templates_save'			=>	"newsletters-templates-save",
			'queue'						=>	"newsletters-queue",
			'history'					=>	"newsletters-history",
			'emails'					=>	"newsletters-emails",
			'links'						=>	"newsletters-links",
			'clicks'					=>	"newsletters-links-clicks",
			'orders'					=>	"newsletters-orders",
			'settings'					=>	"newsletters-settings",
			'settings_subscribers'		=>	"newsletters-settings-subscribers",
			'settings_templates'		=>	"newsletters-settings-templates",
			'settings_system'			=>	"newsletters-settings-system",
			'settings_tasks'			=>	"newsletters-settings-tasks",
			'settings_updates'			=>	"newsletters-settings-updates",
			'settings_api'				=>	"newsletters-settings-api",
			'extensions'				=>	"newsletters-extensions",
			'extensions_settings'		=>	"newsletters-extensions-settings",
			'support'					=>	"newsletters-support",
            'view_logs'				    =>	"newsletters-view-logs",
		);

		var $blocks = array(
			'newsletters_admin_send_sendtoroles'
		);

		var $extensions = array();

		var $classes = array(
			'Subscriber',
			'Bounce',
			'Unsubscribe',
			'Mailinglist',
			//'Queue',
			//'Latestpost',
			'FieldsList',
			'Field',
			//'History',
			'HistoriesList',
			'HistoriesAttachment',
			'Email',
			//'wpmlOrder',
			//'Post',
			'Theme',
			//'Template',
			//'SubscribersList',
			//'wpmlCountry',
			//'Autoresponder',
			//'AutorespondersList',
			//'Autoresponderemail',
			//'wpmlGroup',
		);

		var $models = array('Link', 'Click', 'Subscribeform', 'Option', 'SubscribersOption', 'Content', 'Latestpost', 
		'Latestpostssubscription', 'History', 'Order', 'Post', 'Template', 'SubscribersList', 'Country', 'Group', 'Autoresponder', 
		'Autoresponderemail', 'AutorespondersList', 'AutorespondersForm', 'FieldsForm', 'SubscriberMeta');

		var $helpers = array('Checkinit', 'Html', 'Form', 'Metabox', 'Shortcode', 'Authnews', 'Db');
		var $tables = array();
		var $tablenames = array();
		
		function __construct() {			
			// this is the parent
				
			include($this -> plugin_base() . DS . 'vendors' . DS . 'processing' . DS . 'wp-import-process.php');
			if (class_exists('WP_Import_Process')) {
				$this -> import_process = new WP_Import_Process();
			}
			
			include($this -> plugin_base() . DS . 'vendors' . DS . 'processing' . DS . 'wp-queue-process.php');
			if (class_exists('WP_Queue_Process')) {
				$this -> queue_process_1 = new WP_Queue_Process();
			}
			
			include($this -> plugin_base() . DS . 'vendors' . DS . 'processing' . DS . 'wp-queue-process_2.php');
			if (class_exists('WP_Queue_Process_2')) {
				$this -> queue_process_2 = new WP_Queue_Process_2();
			}
			
			include($this -> plugin_base() . DS . 'vendors' . DS . 'processing' . DS . 'wp-queue-process_3.php');
			if (class_exists('WP_Queue_Process_3')) {
				$this -> queue_process_3 = new WP_Queue_Process_3();
			}
			
			include($this -> plugin_base() . DS . 'vendors' . DS . 'processing' . DS . 'wp-dbupdate-process.php');
			if (class_exists('WP_Dbupdate_Process')) {
				$this -> dbupdate_process = new WP_Dbupdate_Process();
			}
		}

		public function __call($method = null, $args = null) {
			global $Db;

			if (!empty($method) && in_array($method, $this -> models)) {
				$class = $this -> pre . $method;

				if (!class_exists($class)) {
					$file = $this -> plugin_base() . DS . 'models' . DS . strtolower($method) . '.php';
					if (file_exists($file)) {
						include($file);
					}
				}

				if (empty($this -> {$method}) || !is_object($this -> {$method})) {					
					if (class_exists($class)) {	
						$newmethod = new $class($args);
						$this -> {$method} = $newmethod;
						
						switch ($method) {
							case 'Order'					:		
							case 'Option'					:	
							case 'Country'					:				
								global ${'wpml' . $method};
								${'wpml' . $method} = $newmethod;								
								break;
							default 						:
								global ${$method};
								${$method} = $newmethod;
								break;
						}
						
						return $newmethod;					
					}
				} else {
					
					if (empty($this -> {$method} -> data)) {
						if (!empty($Db -> {$method} -> data)) {
							$this -> {$method} -> data = $Db -> {$method} -> data;
						}
					}

					if (empty($this -> {$method} -> insertid) && !empty($Db -> {$method} -> insertid)) {
						$this -> {$method} -> insertid = $Db -> {$method} -> insertid;
					}

					if (empty($this -> {$method} -> errors) && !empty($this -> {$method} -> {$method} -> errors)) {
						$this -> {$method} -> errors = $this -> {$method} -> {$method} -> errors;
					}

					if (empty($this -> {$method} -> data) && !empty($this -> {$method} -> {$method} -> data)) {
						$this -> {$method} -> data = $this -> {$method} -> {$method} -> data;
					}

					if (empty($this -> {$method} -> model)) {
						$this -> {$method} -> model = $method;
					}

					return $this -> {$method};
				}
			}

			return false;
		}

		/**
		 * Register the plugin
		 * Sets the plugin name and base directory for universal use.
		 * @param STRING. Name of the plugin
		 * @param STRING. Base directory of the plugin
		 *
		 */
		function register_plugin($name = null, $base = null) {			
			$this -> api_key = $this -> get_option('api_key');
			$this -> plugin_name = basename(NEWSLETTERS_DIR);
			$this -> plugin_base = rtrim(dirname($base), DS);
			$this -> plugin_file = plugin_basename($base);
			if (!defined('NEWSLETTERS_LOG_FILE')) { define("NEWSLETTERS_LOG_FILE", $this -> plugin_base() . DS . "newsletters.log"); }
			$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);
			$this -> set_timezone();
			$this -> extensions = $this -> get_extensions();

			### Get our models ready for action!
			$this -> initialize_classes();

			global $wpdb;
			$wpdb -> query("SET sql_mode = '';");

			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : false;

            if(!is_file(NEWSLETTERS_LOG_FILE)){
                file_put_contents(NEWSLETTERS_LOG_FILE, '');     // Save our content to the file.
            }
			$this -> debugging($this -> debugging);
			//add_action('plugins_loaded', array($this, 'ci_initialize'));
			$this -> add_action('plugins_loaded');

			if (is_admin() && !defined('DOING_AJAX')) {
				global $extensions;
				require_once($this -> plugin_base() . DS . 'includes' . DS . 'extensions.php');
				$this -> extensions = $extensions;
			}
		}
		
		function clear_memcached() {
			if (class_exists('Memcached')) {
				$cache = new Memcached();
				$cache -> addServer('localhost', '11211');				
				$keys = $cache -> getAllKeys();
				
				if (!empty($keys)) {
					$cache -> getDelayed($keys);
					$store = $cache -> fetchAll();				
					$keys = $cache -> getAllKeys();				
					
					$pattern = "/(newsletters)/si";
					foreach ($keys as $item) {					
						if (preg_match($pattern, $item, $matches)) {
							$cache -> delete($item);
						}
					}
				}
			}
		}

		function set_cache($hash = null, $data = null, $type = 'query') {

			wp_cache_set($hash, maybe_serialize($data), 'newsletters');
			return true;
	    }

	    function get_cache($hash = null, $type = 'query') {

		    if ($data = wp_cache_get($hash, 'newsletters')) {
			    return maybe_unserialize($data);
		    }

		    return false;
	    }

	    function delete_cache($hash = null, $type = 'query') {

		    if (wp_cache_delete($hash, 'newsletters')) {
			    return true;
		    }

		    return false;
	    }

	    function delete_all_cache($type = 'query') {

		    if (wp_cache_flush()) {
			    return true;
		    }

		    return false;
		}
		
		function qp_do_crons() {
			do_action('wp_queue_process_cron');
			do_action('wp_queue_process_2_cron');
			do_action('wp_queue_process_3_cron');
		}
	    
	    function qp_reset_data() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> reset_data();
		    }
	    }
	    
	    function qp_save() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> save();
		    }
	    }
	    
	    function qp_dispatch() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> dispatch();
		    }
	    }
	    
	    function qp_get_queued_count() {

			if ($newsletters_queue_count = get_transient('newsletters_queue_count')) {
				return $newsletters_queue_count;
			}

		    $count = 0;
		    for ($q = 1; $q <= 3; $q++) {
			    $count += $this -> {'queue_process_' . $q} -> get_queued_count();
			}
			
			set_transient('newsletters_queue_count', $count, (2 * MINUTE_IN_SECONDS));		    
		    return $count;
	    }
	    
	    function qp_get_specific_batch($key = null) {
		    return $this -> queue_process_1 -> get_specific_batch($key);
	    }
	    
	    function qp_update($key = null, $data = null) {
		    $this -> queue_process_1 -> update($key, $data);
	    }
	    
	    function qp_delete($key = null) {
		    $this -> queue_process_1 -> delete($key);
	    }
	    
	    function qp_do_specific_item($item = null, $override = false) {
		    $this -> queue_process_1 -> do_specific_item($item, $override);
	    }
	    
	    function qp_unlock() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> unlock();
		    }
	    }
	    
	    function qp_cancel_all_processes() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> cancel_all_processes();
		    }
	    }
	    
	    function qp_get_batches($onlykeys = false, $onlyerrors = false, $number = false) {
		    $batches = array();
		    for ($q = 1; $q <= 3; $q++) {
			    $newbatches = $this -> {'queue_process_' . $q} -> get_batches($onlykeys, $onlyerrors, $number);			    
			    $batches = array_merge($batches, $newbatches);
		    }
		    
		    return $batches;
	    }
	    
	    function qp_scheduling() {
		    for ($q = 1; $q <= 3; $q++) {
			    $this -> {'queue_process_' . $q} -> scheduling();
		    }
	    }
	    
	    function get_country_by_ip($ipaddress = null) {
		    global $Html;
		    
		    $ipcountry = false;
		    
		    if (!empty($ipaddress)) {
			    $ipaddress = esc_html($ipaddress);
			    if (function_exists('wp_remote_get')) {
				    $random = rand(1, 3);
				    switch ($random) {
					    case 1					:
					 		$url = 'https://www.iplocate.io/api/lookup/' . $ipaddress;   
					 		if ($response = wp_remote_get($url)) {
						 		$ipinfo = wp_remote_retrieve_body($response);
						 		$ipinfo = json_decode($ipinfo);
						 		if (!empty($ipinfo) && empty($ipinfo -> error)) {
							 		$ipcountry = $ipinfo -> country_code;
						 		}
						 	}   
					    	break;
					    case 2					:
					    	$url = 'http://api.hostip.info/country.php?ip=' . $ipaddress;
					    	if ($response = wp_remote_get($url)) {
						    	$ipinfo = wp_remote_retrieve_body($response);
						    	if (!empty($ipinfo) && $ipinfo != "XX") {
						    		$ipcountry = $ipinfo;
						    	}
					    	}
					    	break;
					    case 3					:
					    	$url = 'http://www.geoplugin.net/php.gp?ip=' . $ipaddress;
					    	if ($response = wp_remote_get($url)) {
						    	$ipinfo = wp_remote_retrieve_body($response);
						    	if (!empty($ipinfo)) {
							    	$ipinfo = maybe_unserialize($ipinfo);
							    	if ( is_array( $ipinfo ) ) $ipcountry = $ipinfo['geoplugin_countryCode']; 
							    }
					    	}
					    	break;
				    }
			    }
		    }
		    
		    return apply_filters('newsletters_ipcountry', $ipcountry, $ipaddress);
	    }
	    
	    function get_useragent() {
		    require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
			$detect = new Mobile_Detect();
			
			$agent = false;
			
			if ($useragents = $detect -> getUserAgents()) {
				foreach ($useragents as $useragent => $regex) {					
					if (!empty($_SERVER['HTTP_USER_AGENT'])) {
						if (strpos( $_SERVER['HTTP_USER_AGENT'], $useragent) !== false) {
							$agent = $useragent;
						}
					}
				}
			}
			
			return $agent;
	    }

	    function get_device() {
		    $device = false;
			
			require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
			$detect = new Mobile_Detect();

			if ($detect -> isTablet()) {
				$device = "tablet";
			} elseif ($detect -> isMobile()) {
				$device = "mobile";
			} else {
				$device = "desktop";
			}

			return apply_filters('newsletters_device', $device, $detect);
	    }

	    function is_php_module($module = null) {
		    if (!empty($module)) {
			    ob_start();
				phpinfo(INFO_MODULES);
				$contents = ob_get_clean();

				if (strpos($contents, $module) !== false) {
					return true;
				}
		    }

		    return false;
	    }

		function media_insert($html = null, $id = null, $attachment = null) {
			$align = get_post_meta($id, '_wpmlalign', true);
			$hspace = get_post_meta($id, '_wpmlhspace', true);

			if (!empty($align) || !empty($hspace)) {
				if (class_exists('DOMDocument')) {
					$dom = new DOMDocument();
					$dom -> loadHTML($html);
	
					foreach ($dom -> getElementsByTagName('img') as $img) {
						if (!empty($align) && $align != "none") {
							$img -> setAttribute('align', $align);
						}
	
						if (!empty($hspace)) {
							$img -> setAttribute('hspace', $hspace);
						}
					}
	
					$html = $dom -> saveHTML();
					$html = trim(preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $html));
				}
			}

			return $html;
		}

		function attachment_fields_to_save($post = null, $attachment = null) {

			if (!empty($attachment[$this -> pre . 'align'])) {
				update_post_meta($post['ID'], '_wpmlalign', $attachment[$this -> pre . 'align']);
			} else {
				delete_post_meta($post['ID'], '_wpmlalign');
			}

			if (!empty($attachment[$this -> pre . 'hspace'])) {
				update_post_meta($post['ID'], '_wpmlhspace', $attachment[$this -> pre . 'hspace']);
			} else {
				delete_post_meta($post['ID'], '_wpmlhspace');
			}

			return $post;
		}

		function attachment_fields_to_edit($form_fields = null, $post = null) {
			$align = get_post_meta($post -> ID, "_wpmlalign", true);
			$hspace = get_post_meta($post -> ID, "_wpmlhspace", true);

	        $html = '<label for="attachments_' . $post -> ID . '_wpmlalign_none"><input ' . ((empty($align) || (!empty($align) && $align == "none")) ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="none" id="attachments_' . $post -> ID . '_wpmlalign_none" /> ' . __('None', 'wp-mailinglist') . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_left"><input ' . ((!empty($align) && $align == "left") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="left" id="attachments_' . $post -> ID . '_wpmlalign_left" /> ' . __('Left', 'wp-mailinglist') . '</label>';
	        $html .= ' <label for="attachments_' . $post -> ID . '_wpmlalign_right"><input ' . ((!empty($align) && $align == "right") ? 'checked="checked"' : '') . ' style="width:auto;" type="radio" name="attachments[' . $post -> ID . '][wpmlalign]" value="right" id="attachments_' . $post -> ID . '_wpmlalign_right" /> ' . __('Right', 'wp-mailinglist') . '</label>';

	        $form_fields['wpmlalign'] = array(
	            'label' =>  __('Email Align', 'wp-mailinglist'),
	            'input' =>  'html',
	            'html'	=>	$html,
	            'value'	=>	$align,
	            'show_in_modal'		=>	true,
	            'show_in_edit'		=>	true,
	        );

	        $form_fields['wpmlhspace'] = array(
	        	'label'	=>	__('Email Hspace', 'wp-mailinglist'),
	        	'input'	=>	'html',
	        	'html'	=>	'<input type="text" style="width:45px;" class="widefat" name="attachments[' . $post -> ID . '][wpmlhspace]" value="' . $hspace . '" id="attachments_' . $post -> ID . '_wpmlhspace" /> px',
	        	'value'	=>	$hspace,
	        	'show_in_modal'		=>	true,
	            'show_in_edit'		=>	true,
	        );

		    return $form_fields;
		}

		function debugging($debug = false) {
			global $wpdb;

			if (!empty($debug) && $debug == true) {
				if (!defined('WP_DEBUG')) { define('WP_DEBUG', true); }
				$wpdb -> show_errors();

				if (!empty($this -> debug_level) && $this -> debug_level == 2) {
					error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
					@ini_set('display_errors', 1);
					@ini_set("log_errors", 1);
				}
			} else {
				//Get the current server log configuration and apply the same values accordingly
				$wpdb_errors = false;
                $error_reporting = error_reporting();
                $display_errors = @ini_get( 'display_errors' );
                $log_errors = @ini_get( 'log_errors' );
                $wpdb->show_errors( $wpdb_errors ); // When $wpdb_errors is false, this is the same as calling hide_errors()
                error_reporting( $error_reporting & ~E_NOTICE & ~E_WARNING ); // set this to error_reporting( $error_reporting); if you want to see the warnings and notices too
                @ini_set( 'display_errors', $display_errors );
                @ini_set( 'log_errors', $log_errors );

			}
		}

		function after_plugin_row($plugin_name = null) {
			
			if (apply_filters('newsletters_updates', true)) {
		        $key = $this -> get_option('serialkey');
		        $update = $this -> vendor('update');
		        $version_info = $update -> get_version_info();
	
		        if (!empty($version_info) && $version_info['is_valid_key'] == "0") {		        
			        ?>
			        
			        <tr class="plugin-update-tr active" id="<?php echo esc_html($this -> plugin_name); ?>-update" data-slug="<?php echo esc_html($this -> plugin_name); ?>" data-plugin="<?php echo esc_html( $this -> plugin_file); ?>">
				        <td colspan="3" class="plugin-update colspanchange">
					        <div class="update-message notice inline notice-warning notice-alt">
						        <p>
			        
						        <?php
				
								if (!$this -> ci_serial_valid()) {
									echo wp_kses_post(sprintf(__('You are running %s LITE. To remove limits, you can submit a serial key or %s.', 'wp-mailinglist'), $this -> name, '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '">' . __('Upgrade to PRO', 'wp-mailinglist') . '</a>'));
								} else {
									echo wp_kses_post(sprintf('Your download for the Newsletter plugin has expired, please <a href="%s" target="_blank">renew it</a> for updates!', $version_info['url']));
								}
				
						        ?>
			        
			        			</p>
			        		</div>
			        	</td>
			        </tr>
	
			        <?php
		        }
	        }
	    }

		/**
		 * This function outputs the changelog on the 'Plugins' page when the "View Details" link is clicked.
		 */
	    function display_changelog() {		        
	    	if (!empty($_GET['plugin']) && ($_GET['plugin'] == $this -> plugin_name || $_GET['plugin'] == "newsletters-lite")) {
		    	$section = sanitize_text_field(wp_unslash($_GET['section']));
		    	switch ($section) {
			    	case 'changelog'				:
			    		if (apply_filters('newsletters_updates', true)) {
					    	$update = $this -> vendor('update');
					    	$changelog = $update -> get_changelog();
							$this -> render('changelog', array('changelog' => $changelog), true, 'admin');
					    }	
					    
					    exit();
			    		break;
		    	}
	    	}
	    }

		function has_update($cache = true) {
			if (apply_filters('newsletters_updates', true)) {
				$update = $this -> vendor('update');
		        $version_info = $update -> get_version_info($cache);
		        return version_compare($this -> version, $version_info["version"], '<');
		    }
		    
		    return false;
	    }

	    function ajax_importfile() {
		    
		    check_ajax_referer('importfile', 'security');
		    
		    if (!current_user_can('newsletters_importexport')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
		    
		    global $Db, $Html, $Field;

		    ob_start();

		    if (!function_exists('wp_handle_upload')) {
			    require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			$uploadedfile = map_deep(wp_unslash($_FILES['file']), 'sanitize_text_field');

			$upload_overrides = array( 
				'test_form' 	=> 	false,
				'test_type'		=>	false,
				'ext'			=>	'csv',
				'type'			=>	'text/csv',
			);

			$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

			if ($movefile && !isset($movefile['error'])) {
			    $csvtypes = array('text/comma-separated-values', 'data/csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/octet-stream', 'application/vnd.msexcel', 'text/anytext', 'text/plain');
			    $vcardtypes = array('text/x-vcard', 'application/vcard', 'text/anytext', 'text/directory', 'application/x-versit', 'text/x-versit', 'text/x-vcalendar');
			    $filetype = wp_check_filetype($movefile['file']);

			    $delimiter = $Html -> detectDelimiter($movefile['file']);
			    
			    $rows = 3;
			    $done = 1;
			    $headingdone = false;
			    $fields = $Field -> get_all();

			    if (!empty($filetype['type']) && in_array($filetype['type'], $csvtypes)) {
				    $preview = '';
				    $preview .= '<h3>' . __('Preview of File', 'wp-mailinglist') . '</h3>';

				    if ($fp = file($movefile['file'])) {
				    	$count = count($fp);
				    	$preview .= '<p>' . sprintf(__('The file contains %s records in total.', 'wp-mailinglist'), '<b>' . $count . '</b>') . '</p>';
					}

				    $preview .= '<table class="widefat">';

				    if (($fh = fopen($movefile['file'], "r")) !== false) {
					    while (($row = fgetcsv($fh, 1000, $delimiter)) !== false && $done <= $rows) {
						    if (empty($headingdone)) {
							    $preview .= '<thead>';
							    $preview .= '<tr>';
							    for ($i = 1; $i <= count($row); $i++) {
								    $preview .= '<th>' . sprintf(__('Column %s', 'wp-mailinglist'), $i) . '</th>';
							    }
							    $preview .= '</tr>';

								$preview .= '<tr>';
								for ($i = 1; $i <= count($row); $i++) {
									$preview .= '<td>';
									$preview .= '<select name="columns[' . $i . ']">';
									$preview .= '<option value="">' . __('- Do not import -', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="email">' . __('Email Address', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="ip_address">' . __('IP Address', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="created">' . __('Created Date', 'wp-mailinglist') . '</option>';
									if (!empty($fields)) {
										foreach ($fields as $field) {
											$preview .= '<option value="' . $field -> slug . '">' . esc_html($field -> title) . '</option>';
										}
									}
									$preview .= '</select>';
									$preview .= '</td>';
								}
								$preview .= '</tr>';

							    $preview .= '</thead>';
							    $preview .= '<tbody>';
							    $headingdone = true;
						    }

						    $class = (empty($class)) ? 'alternate' : '';
						    $preview .= '<tr class="' . $class . '">';
						    foreach ($row as $cell) {							    
							    $preview .= '<td>' . $cell . '</td>';
						    }
						    $preview .= '</tr>';
						    $done++;
					    }
				    }

				    $preview .= '</tbody></table>';
					$preview = $this -> maybe_utf8_encode($preview);
	
				    $response = array(
					    'success'			=>	true,
					    'movefile'			=>	$movefile,
					    'preview'			=>	$preview,
					    'delimiter'			=>	$delimiter,
				    );
				} elseif (!empty($filetype['type']) && in_array($filetype['type'], $vcardtypes)) {										
					ob_start();
					
					$preview = '';
					
					include_once($this -> plugin_base() . DS . 'vendors' . DS . 'class.vcard.php');
					$conv = new vcard_convert();
					$conv -> fromFile($movefile['file']);
					$data = $conv -> toCSV(',', false, false, null);
	
					$fileName = 'vcard.csv';
					$filePath = $Html -> uploads_path() . DS . $this -> plugin_name . DS;
					$fileFull = $filePath . $fileName;
					$fh = fopen($fileFull, "w");
	
					if ($fh) {
						fwrite($fh, $data);
						fclose($fh);
					} else {
						$preview .= sprintf(__('CSV file could not be created! Check "%s" permissions!', 'wp-mailinglist'), $filePath);
					}
					
					$uploadedfile = array(
						'file'				=>	$fileFull,
						'type'				=>	false,
						'url'				=>	false,
					);
					
					$delimiter = $Html -> detectDelimiter($fileFull);			    

				    if (($fh = fopen($fileFull, "r")) !== false) {					    
					    $preview .= '<h3>' . __('Preview of File', 'wp-mailinglist') . '</h3>';
					    
					    if ($fp = file($fileFull)) {
					    	$count = count($fp);
					    	$preview .= '<p>' . sprintf(__('The file contains %s records in total.', 'wp-mailinglist'), '<b>' . $count . '</b>') . '</p>';
						}
					    
					    $preview .= '<table class="widefat">';
					    
					    while (($row = fgetcsv($fh, 1000, $delimiter)) !== false && $done <= $rows) {							    					    
						    if (empty($headingdone)) {
							    $preview .= '<thead>';
							    $preview .= '<tr>';
							    for ($i = 1; $i <= count($row); $i++) {
								    $preview .= '<th>' . sprintf(__('Column %s', 'wp-mailinglist'), $i) . '</th>';
							    }
							    $preview .= '</tr>';

								$preview .= '<tr>';
								for ($i = 1; $i <= count($row); $i++) {
									$preview .= '<td>';
									$preview .= '<select name="columns[' . $i . ']">';
									$preview .= '<option value="">' . __('- Do not import -', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="email">' . __('Email Address', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="ip_address">' . __('IP Address', 'wp-mailinglist') . '</option>';
									$preview .= '<option value="created">' . __('Created Date', 'wp-mailinglist') . '</option>';
									if (!empty($fields)) {
										foreach ($fields as $field) {
											$preview .= '<option value="' . $field -> slug . '">' . esc_html($field -> title) . '</option>';
										}
									}
									$preview .= '</select>';
									$preview .= '</td>';
								}
								$preview .= '</tr>';

							    $preview .= '</thead>';
							    $preview .= '<tbody>';
							    $headingdone = true;
						    }

						    $class = (empty($class)) ? 'alternate' : '';
						    $preview .= '<tr class="' . $class . '">';
						    foreach ($row as $cell) {
							    $preview .= '<td>' . $cell . '</td>';
						    }
						    $preview .= '</tr>';
						    $done++;
					    }
					    
					    $preview .= '</tbody></table>';
				    } else {
					    $preview .= '<p>' . __('CSV file cannot be open for reading', 'wp-mailinglist') . '</p>';
				    }

					echo wp_kses_post($preview);
					$preview = ob_get_clean();
					$preview = $this -> maybe_utf8_encode($preview);
					
					$response = array(
						'success'			=>	true,
						'movefile'			=>	$uploadedfile,
						'preview'			=>	$preview,
						'delimiter'			=>	$delimiter,
					);
				} else {
					$response = array(
						'success'			=>	false,
						'errormessage'		=>	__('File type is not allowed.', 'wp-mailinglist'),
					);
				}
			} else {
			    $response = array(
				    'success'			=>	false,
				    'errormessage'		=>	$movefile['error'],
			    );
			}

			$process = ob_get_clean();
			echo wp_json_encode($response);

		    exit();
		    die();
	    }
	    
	    function maybe_utf8_encode($content = null) {
		    if (function_exists('mb_detect_encoding')) {
				$encoding = mb_detect_encoding($content, mb_detect_order(), true);
				if (empty($encoding) || $encoding != "UTF-8") {
					$content = utf8_encode($content);
				}
			}
			
			return $content;
	    }

	    function ajax_importmultiple() {
		    
		    check_ajax_referer('importmultiple', 'security');
		    
		    if (!current_user_can('newsletters_importexport')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
		    
			global $wpdb, $Db, $Subscriber, $SubscribersList, $Unsubscribe, $Bounce;

			if (!empty($_REQUEST['subscribers'])) {
				
				$this -> qp_reset_data();

				foreach (map_deep(wp_unslash($_REQUEST['subscribers']), 'sanitize_text_field') as $skey => $subscriber) {
					$subscriber = stripslashes_deep($subscriber);
					$subscriber['justsubscribe'] = true;
					$email = $subscriber['email'];
					$mailinglists = $subscriber['mailinglists'];
					$confirmation_subject = wp_kses_post(wp_unslash($_REQUEST['confirmation_subject']));
					$confirmation_email = wp_kses_post(wp_unslash($_REQUEST['confirmation_email']));
					$import_preventbu = sanitize_text_field(wp_unslash($_REQUEST['import_preventbu']));
					$import_overwrite = sanitize_text_field(wp_unslash($_REQUEST['import_overwrite']));

					//if ($current_id = $Subscriber -> email_exists($email) && $import_overwrite == "Y") {
					if (true) {
						$Db -> model = $Unsubscribe -> model;
						if (empty($import_preventbu) || $import_preventbu == "N" || ($import_preventbu == "Y" && !$Db -> find(array('email' => $email)))) {
							$Db -> model = $Bounce -> model;
							if (empty($import_preventbu) || $import_preventbu == "N" || ($import_preventbu == "Y" && !$Db -> find(array('email' => $email)))) {
								if (!empty($subscriber)) {
									$subscriber['fromregistration'] = true;
									$subscriber['username'] = $email;

									$skipsubscriberupdate = false;

									if ($current_id = $Subscriber -> email_exists($email)) {
										$subscriber['id'] = $current_id;

										if (empty($import_overwrite) || $import_overwrite == "N") {
											$skipsubscriberupdate = true;
										} else {
											$skipsubscriberupdate = false;
										}
									} else {
										$skipsubscriberupdate = false;
										$subscriber['id'] = false;
									}

									if ($Subscriber -> save($subscriber, true, false, $skipsubscriberupdate)) {
										$subscriber_id = $Subscriber -> insertid;
										$afterlists = $subscriber['afterlists'];

										if (!empty($afterlists)) {
											foreach ($afterlists as $mailinglist) {
												$sl_data = array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist['id'], 'paid' => $mailinglist['paid'], 'active' => $mailinglist['active']);
												$sl_query = $SubscribersList -> save($sl_data, false, true);

												if (!empty($sl_query)) {
													$wpdb -> query($sl_query);
												}
											}
										}

										if ($subscriber['active'] == "N") {
											if (!empty($mailinglists)) {
												$allmailinglists = $mailinglists;
												$Db -> model = $Subscriber -> model;
												$sub = $Db -> find(array('id' => $subscriber_id));

												foreach ($allmailinglists as $mailinglist_id) {
													$subject = $confirmation_subject;
													$message = $confirmation_email;
													
													$message = wpautop($message);
													$message = $this -> process_set_variables($sub, false, $message, false, false, true);
													
													$queue_process_data = array(
														'subscriber_id'				=>	$sub -> id,
														'subject'					=>	$subject,
														'message'					=>	$message,
														'attachments'				=>	false,
														'post_id'					=>	false,
														'history_id'				=>	false,
														'theme_id'					=>	$this -> default_theme_id('system'),
														'senddate'					=>	false,
													);
													
													$this -> queue_process_1 -> push_to_queue($queue_process_data);
												}
											}
										}

										$success = "Y<|>" . $email;
										$message = __('Subscriber was imported.', 'wp-mailinglist');
									} else {
										$success = "N<|>" . $email;
										$message = implode(" | ", $Subscriber -> errors);
									}
								} else {
									$success = "N<|>" . $email;
									$message = __('No subscriber data is available.', 'wp-mailinglist');
								}
							} else {
								$success = "N<|>" . $email;
								$message = __('Subscriber has previously bounced', 'wp-mailinglist');
							}
						} else {
							$success = "N<|>" . $email;
							$message = __('Subscriber has previously unsubscribed', 'wp-mailinglist');
						}
					} else {
						$success = "Y<|>" . $email;
						$message = __('Subscriber exists, not updating/overwriting', 'wp-mailinglist');
					}

					echo wp_kses_post($success) . "<|>" . wp_kses_post($message) . "<||>";
				}
				
				$this -> qp_save();
				$this -> qp_dispatch();
			} else {
				$success = "N<|>" . $email;
				$message = __('No data was posted, blank row?', 'wp-mailinglist');
				echo wp_kses_post($success) . "<|>" . wp_kses_post($message) . "<||>";
			}

			exit();
			die();
		}

        function ajax_builderon() {
            define('DOING_AJAX', true);
            define('SHORTINIT', true);

            $builderon = (empty($_POST['status']) || $_POST['status'] == "false") ? false : true;
            update_user_option(get_current_user_id(), 'newsletters_builderon', $builderon);

            exit();
            die();
        }

	    function ajax_admin_mode() {
		    
		    check_admin_referer('admin_mode', 'security');
		    
		    if (!current_user_can('newsletters_settings')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }

		    $user_id = get_current_user_id();
		    $mode = (empty($_POST['mode'])) ? 'standard' : sanitize_text_field(wp_unslash($_POST['mode']));
		    update_user_option($user_id, 'newsletters_admin_mode', $mode);

		    exit();
		    die();
	    }
	    
	    function ajax_get_country() {		    
		    global $Subscriber, $Html;
		    
		    check_ajax_referer('get_country', 'security');
		    
		    $success = false;
		    $data = array();
		    
		    if (current_user_can('newsletters_subscribers')) {
			    if (!empty($_POST['subscriber_id'])) {
				    $subscriber_id = (int) sanitize_text_field($_POST['subscriber_id']);
				    $data['subscriber_id'] = $subscriber_id;
				    if ($subscriber = $Subscriber -> get($subscriber_id)) {
					    if (!empty($subscriber -> ip_address)) {
						    if ($ipcountry = $this -> get_country_by_ip($subscriber -> ip_address)) {
							    $country = $ipcountry;
							    
							    $Subscriber -> save_field('country', $country, $subscriber -> id);
							    
							    $data['country'] = $country;
							    $data['flag'] = $Html -> flag_by_country($country);
							    $success = true;
						    }
					    }
				    }
			    }
			} else {
				$success = false;
				$data['message'] = __('You do not have permission', 'wp-mailinglist');
			}
		    
		    $data['success'] = $success;
		    echo wp_json_encode($data);
		    
		    exit();
		    die();
	    }

	    function ajax_mailinglist_save() {		    
		    if (!current_user_can('newsletters_lists')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
		    
		    global $wpdb, $Db, $Mailinglist, $SubscribersList;

		    $success = false;
		    $errors = false;

		    $fielddiv = sanitize_text_field(wp_unslash($_GET['fielddiv']));
		    $fieldname = sanitize_text_field(wp_unslash($_GET['fieldname']));

		    if (!empty($_POST)) {
			    check_ajax_referer($this -> sections -> lists . '_save');
			    
			    $_POST['Mailinglist']['privatelist'] = "N";

			    if ($Mailinglist -> save($_POST)) {
				    $success = true;
				    $insertid = $Mailinglist -> insertid;
			    } else {
				    $errors = $Mailinglist -> errors;
			    }

			    $checklist = '';
			    if ($mailinglists = $Mailinglist -> select(true)) {
				    foreach ($mailinglists as $id => $title) {
						$Db -> model = $SubscribersList -> model;
						$checklist .= '<label><input ' . ($id == $insertid || (!empty($_POST['Subscriber']['mailinglists']) && in_array($id, $_POST['Subscriber']['mailinglists'])) ? 'checked="checked"' : '') . ' type="checkbox" name="' . $fieldname . '[]" value="' . esc_attr($id) . '" id="checklist' . esc_attr($id) . '" /> ' . esc_html($title) . ' (' . $Db -> count(array('list_id' => $id)) . ' ' . __('subscribers', 'wp-mailinglist') . ')</label><br/>';
				    }
			    }

			    $output = array(
				    'success'			=>	$success,
				    'errors'			=>	$errors,
				    'blocks'			=>	array(
					    'form'				=>	$this -> render('mailinglists' . DS . 'save-ajax', array('fielddiv' => $fielddiv, 'fieldname' => $fieldname, 'ajax' => true, 'errors' => $errors, 'success' => $success), false, 'admin'),
					    'checklist'			=>	$checklist,
				    ),
			    );

			    echo wp_json_encode($output);
		    } else {
			    $this -> render('mailinglists' . DS . 'save-ajax', array('fielddiv' => $fielddiv, 'fieldname' => $fieldname), true, 'admin');
		    }

		    exit();
		    die();
	    }

	    function ajax_posts_by_category() {
		    
		    check_ajax_referer('posts_by_category', 'security');
		    
		    if (!current_user_can('edit_posts')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
		    
		    header('Content-Type: application/json');

		    $posts_by_category = array();

			$arguments = array(
				'numberposts'			=>	"-1",
				'orderby'				=>	'post_date',
				'order'					=>	"DESC",
				'post_type'				=>	"post",
				'post_status'			=>	"publish",
			);

			if (!empty($_REQUEST['cat_id']) && $_REQUEST['cat_id'] > 0) {
				$arguments['category'] = map_deep(wp_unslash($_REQUEST['cat_id']), 'sanitize_text_field');
			}

			if (!empty($_REQUEST['post_type'])) {
				$arguments['post_type'] = sanitize_text_field(wp_unslash($_REQUEST['post_type']));
			}
			
			if (!empty($_REQUEST['language'])) {
				$language = sanitize_text_field(wp_unslash($_REQUEST['language']));
				$arguments['lang'] = $language;
				$arguments['language'] = $language;
				$this -> language_set($language);
			}

			if ($posts = get_posts($arguments)) {
				$posts_by_category[] = array('text' => __('- Select -', 'wp-mailinglist'), 'value' => false);

				foreach ($posts as $post) {
					if ($this -> language_do()) {
						$posts_by_category[] = array('text' => esc_html($this -> language_use(sanitize_text_field(wp_unslash($_REQUEST['language'])), $post -> post_title, false)), 'value' => $post -> ID);
					} else {
						$posts_by_category[] = array('text' => esc_html($post -> post_title), 'value' => $post -> ID);
					}
				}
			} else {
				$posts_by_category[] = array('text' => __('No posts in this category', 'wp-mailinglist'), 'value' => false);
			}

			echo wp_json_encode($posts_by_category);

		    exit();
		    die();
	    }

	    function ajax_getposts() {
		    
		    check_ajax_referer('getposts', 'security');
		    
		    if (!current_user_can('edit_posts')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }

		    if (!empty($_POST)) {
			    $arguments = array(
			    	'numberposts'			=>	"-1",
			    	'orderby'				=>	"post_title",
			    	'order'					=>	"ASC",
			    	'post_type'				=>	"post",
			    	'post_status'			=>	"publish",
			    );

			    if (!empty($_POST['posttype'])) { $arguments['post_type'] = sanitize_text_field($_POST['posttype']); }
			    if (!empty($_POST['category'])) { $arguments['category'] = sanitize_text_field($_POST['category']); }
			    if (!empty($_POST['number'])) { $arguments['numberposts'] = sanitize_text_field($_POST['number']); }
			    if (!empty($_POST['orderby'])) { $arguments['orderby'] = sanitize_text_field($_POST['orderby']); }
			    if (!empty($_POST['order'])) { $arguments['order'] = sanitize_text_field($_POST['order']); }
			    
			    if (!empty($_POST['language'])) {
				    $language = sanitize_text_field($_POST['language']);
				    $arguments['lang'] = $language;
				    $arguments['language'] = $language;
				    $this -> language_set($language);
			    }

			    if ($posts = get_posts($arguments)) {
				    ?>

				    <ul class="insertfieldslist">
				    	<li>
				    		<span class="insertfieldslistcheckbox">
				    			<input onclick="jqCheckAll(this, false, 'insertposts');" type="checkbox" name="checkall_insertposts" id="checkall_insertposts" value="1" />
				    		</span>
				    		<span class="">
				    			<label for="checkall_insertposts" style="font-weight:bold;"><?php esc_html_e('Select All', 'wp-mailinglist'); ?></label>
				    		</span>
				    	</li>
				    	<?php foreach ($posts as $post) : ?>
				    		<li>
				    			<span class="insertfieldslistcheckbox"><input type="checkbox" name="insertposts[]" value="<?php echo esc_attr($post -> ID); ?>" id="insertposts_<?php echo esc_html( $post -> ID); ?>" /></span>

				    			<?php if ($this -> language_do()) : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo esc_html( $post -> ID); ?>', false); return false;" class="button button-secondary press" title="<?php echo esc_attr(wp_unslash($this -> language_use(sanitize_text_field(wp_unslash($_POST['language'])), $post -> post_title, false))); ?>"><?php echo esc_html( $this -> language_use(sanitize_text_field(wp_unslash($_POST['language'])), $post -> post_title, false)); ?></a></span>
				    			<?php else : ?>
				    				<span class="insertfieldslistbutton"><a href="" onclick="insert_post('<?php echo esc_html( $post -> ID); ?>', false); return false;" class="button button-secondary press" title="<?php echo esc_attr(wp_unslash(esc_html($post -> post_title))); ?>"><?php echo esc_html($post -> post_title); ?></a></span>
				    			<?php endif; ?>
				    		</li>
				    	<?php endforeach; ?>
				    </ul>

				    <span>
				    	<button onclick="insert_single_multiple();" class="button button-primary" type="button" name="insert" value="1">
				    		<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Insert Selected', 'wp-mailinglist'); ?>
				    	</button>
				    </span>

				    <?php
			    }
		    }

		    exit();
		    die();
	    }

        function ajax_newsletters_get_template($template_id = null) {
            check_ajax_referer('newsletters_get_template', 'security');

            $template_id = sanitize_text_field(isset($_POST['template_id']) ? $_POST['template_id'] : '');
            if (!empty($template_id)) {
                global $wpdb;

                    $query = "SELECT * FROM `" . $wpdb -> prefix . 'wpmlthemes' . "` WHERE `id` = '" . esc_sql($template_id) . "'";

                    $query_hash = md5($query);


                    if ($template = $wpdb -> get_row($query)) {
                        $template_content = $template -> content;
                        header('Content-Type: text/html');
                        $content = ob_get_clean();
                        echo do_shortcode(wp_unslash($template_content));
                        exit();
                    }


            }
            return '';
            // return wpml_get_themes($conditions, $fields, $order, $limit);
        }

	    function ajax_api_newkey() {
		    
		    check_ajax_referer('api_newkey', 'security');
		    
		    if (!current_user_can('newsletters_settings_api')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }

			$key = strtoupper(md5(current_time('timestamp')));
			$this -> update_option('api_key', $key);
			echo esc_html($key);

			exit();
			die();
		}

	    function ajax_welcomestats() {
		    
		    check_ajax_referer('welcomestats', 'security');
		    
		    if (!current_user_can('newsletters_welcome')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
		    
		    global $wpdb, $Html, $Subscriber, $Email, $Bounce, $Unsubscribe;

		    $chart = (empty($_GET['chart'])) ? "bar" : sanitize_text_field(wp_unslash($_GET['chart']));
		    $type = (empty($_GET['type'])) ? "days" : sanitize_text_field(wp_unslash($_GET['type']));
			$fromdate = (empty($_GET['from'])) ? date_i18n("Y-m-d", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " -13 days")) : sanitize_text_field(wp_unslash($_GET['from']));
			$todate = (empty($_GET['to'])) ? date_i18n("Y-m-d") : sanitize_text_field(wp_unslash($_GET['to']));
			$history_id = (empty($_GET['history_id'])) ? false : sanitize_text_field(wp_unslash($_GET['history_id']));

			$history_condition = (!empty($history_id)) ? " `history_id` = '" . $history_id . "' AND" : false;
			$history_condition = apply_filters('newsletters_stats_history_condition', $history_condition, $type, $fromdate, $todate, $history_id);
			$subscribers_condition = apply_filters('newsletters_stats_subscriber_condition', false, $type, $fromdate, $todate, $history_id);
			$clicks_condition = apply_filters('newsletters_stats_clicks_condition', false, $type, $fromdate, $todate, $history_id);
			$emails_condition = apply_filters('newsletters_stats_emails_condition', false, $type, $fromdate, $todate, $history_id);
			$bounces_condition = apply_filters('newsletters_stats_bounces_condition', false, $type, $fromdate, $todate, $history_id);
			$unsubscribes_condition = apply_filters('newsletters_stats_unsubscribes_condition', false, $type, $fromdate, $todate, $history_id);
			$reads_condition = apply_filters('newsletters_stats_reads_condition', false, $type, $fromdate, $todate, $history_id);

			switch ($type) {
				case 'years'			:
					// Subscribers
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $records = $wpdb -> get_results($query);

				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("Y", strtotime($record -> date))] = $record -> subscriberscount;

							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }

				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);

				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("Y", strtotime($record -> date))] = $record -> clickscount;

							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }

				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`created`)";
				    $records = $wpdb -> get_results($query);

				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("Y", strtotime($record -> date))] = $record -> emailscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }

				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $bounces_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("Y", strtotime($record -> date))] = $record -> bouncecount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }

				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY YEAR(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("Y", strtotime($record -> date))] = $record -> unsubscribescount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }

				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);

				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("Y", strtotime($record -> date))] = $record -> readscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }

				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();

				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$yearsdiff = round(abs($tostamp - $fromstamp) / (60 * 60 * 24 * 365));

					$d = new DateTime($todate);
					$d -> modify("next year");

				    $j = 0;
				    for ($i = 0; $i <= $yearsdiff; $i++) {
					    $d -> modify("previous year");
				    	//$datestring = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    //$dates_data[$j] = date_i18n("Y", strtotime("-" . $i . " years", $tostamp));
					    $datestring = $d -> format("Y");
					    $dates_data[$j] = $d -> format("Y");

					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}

						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }

					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }

					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }

					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }

					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }

					    $j++;
				    }

				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $clicks_data = array_reverse($clicks_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
					break;
				case 'months'			:
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
					$records = $wpdb -> get_results($query);

				    $subscribers_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("mY", strtotime($record -> date))] = $record -> subscriberscount;

							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }

				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);

				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("mY", strtotime($record -> date))] = $record -> clickscount;

							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }

				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`created`)";
				    $records = $wpdb -> get_results($query);

				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("mY", strtotime($record -> date))] = $record -> emailscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }

				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $bounces_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("mY", strtotime($record -> date))] = $record -> bouncecount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }

				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE" . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY MONTH(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("mY", strtotime($record -> date))] = $record -> unsubscribescount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }

				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE" . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);

				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("mY", strtotime($record -> date))] = $record -> readscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }

				    $dates_data = array();
				    $subscribers_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();

				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$monthsdiff = round(abs($tostamp - $fromstamp) / 2628000);

					$d = new DateTime($todate);
					//$d -> modify("next month");

				    $j = 0;
				    for ($i = 0; $i <= $monthsdiff; $i++) {
						$d -> modify("previous month");

				    	//$datestring = date_i18n("mY", strtotime("-" . $i . " month", $tostamp), false);
					    //$dates_data[$j] = date_i18n("F Y", strtotime("-" . $i . " month", $tostamp), false);
					    $datestring = $d -> format("mY");
					    $dates_data[$j] = $d -> format("F Y");

					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}

						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }

					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }

					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }

					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }

					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }

					    $j++;
				    }

				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $bounces_data = array_reverse($bounces_data);
				    $clicks_data = array_reverse($clicks_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
					break;
				case 'days'				:
				default 				:

					// Subscribers
					$query = "SELECT COUNT(`id`) as `subscriberscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $Subscriber -> table . "` WHERE " . $subscribers_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);

				    $subscribers_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$subscribers_array[date_i18n("dmY", strtotime($record -> date))] = $record -> subscriberscount;

							if (empty($y_max) || (!empty($y_max) && $record -> subscriberscount > $y_max)) {
								$y_max = $record -> subscriberscount;
							}
					    }
				    }

				    // Clicks
				    $query = "SELECT COUNT(`id`) as `clickscount`, DATE(`created`) as `date` FROM `" . $wpdb -> prefix . $this -> Click() -> table . "` WHERE " . $clicks_condition . " " . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
					$records = $wpdb -> get_results($query);

				    $clicks_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$clicks_array[date_i18n("dmY", strtotime($record -> date))] = $record -> clickscount;

							if (empty($y_max) || (!empty($y_max) && $record -> clickscount > $y_max)) {
								$y_max = $record -> clickscount;
							}
					    }
				    }

				    // Emails Sent
				    $query = "SELECT COUNT(id) as `emailscount`, DATE(created) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE " . $emails_condition . " " . $history_condition . " CAST(`created` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`created`)";
				    $records = $wpdb -> get_results($query);

				    $emails_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$emails_array[date_i18n("dmY", strtotime($record -> date))] = $record -> emailscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> emailscount;
							}
					    }
				    }

				    // Bounces
				    $query = "SELECT COUNT(id) as `bounces`, SUM(count) as `bouncecount`, DATE(modified) as `date` FROM " . $wpdb -> prefix . $Bounce -> table . " WHERE " . $bounces_condition . " " . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $bounces_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $bounces_array[date_i18n("dmY", strtotime($record -> date))] = $record -> bouncecount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> bouncecount > $y_right_max)) {
								$y_right_max = $record -> bouncecount;
							}
					    }
				    }

				    // Unsubscribes
				    $query = "SELECT COUNT(`id`) AS `unsubscribescount`, DATE(`modified`) AS `date` FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE " . $unsubscribes_condition . " " . $history_condition . " CAST(`modified` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`modified`)";
				    $records = $wpdb -> get_results($query);

				    $unsubscribes_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
						    $unsubscribes_array[date_i18n("dmY", strtotime($record -> date))] = $record -> unsubscribescount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> unsubscribescount > $y_right_max)) {
								$y_right_max = $record -> unsubscribescount;
							}
					    }
				    }

				    // Reads
				    $query = "SELECT COUNT(id) as `readscount`, DATE(read_date) as `date` FROM " . $wpdb -> prefix . $Email -> table . " WHERE " . $reads_condition . " " . $history_condition . " `read` = 'Y' AND CAST(`read_date` AS DATE) BETWEEN '" . $fromdate . "' AND '" . $todate . "' GROUP BY DATE(`read_date`)";
				    $records = $wpdb -> get_results($query);

				    $reads_array = array();
				    if (!empty($records)) {
					    foreach ($records as $record) {
							$reads_array[date_i18n("dmY", strtotime($record -> date))] = $record -> readscount;

							if (empty($y_right_max) || (!empty($y_right_max) && $record -> emailscount > $y_right_max)) {
								$y_right_max = $record -> readscount;
							}
					    }
				    }

				    $dates_data = array();
				    $subscribers_data = array();
				    $clicks_data = array();
				    $emails_data = array();
				    $bounces_data = array();
				    $unsubscribes_data = array();
				    $reads_data = array();

				    $fromstamp = strtotime($fromdate);
					$tostamp = strtotime($todate);
					$daysdiff = round(abs($tostamp - $fromstamp) / 86400);

					$d = new DateTime($todate);
					$d -> modify("next day");

				    $j = 0;
				    for ($i = 0; $i <= $daysdiff; $i++) {
				    	//$datestring = date_i18n("m-d", strtotime("-" . $i . " days", $tostamp));
					    //$dates_data[$j] = date_i18n("M j", strtotime("-" . $i . " days", $tostamp));
					    $d -> modify("previous day");
					    $datestring = $d -> format("dmY");
					    $dates_data[$j] = $d -> format("M j");

					    if (empty($history_id)) {
						    if (!empty($subscribers_array[$datestring])) {
							    $subscribers_data[$j] = $subscribers_array[$datestring];
						    } else {
							    $subscribers_data[$j] = 0;
						    }
						}

						if (!empty($clicks_array[$datestring])) {
						    $clicks_data[$j] = $clicks_array[$datestring];
					    } else {
						    $clicks_data[$j] = 0;
					    }

					    if (!empty($emails_array[$datestring])) {
						    $emails_data[$j] = $emails_array[$datestring];
					    } else {
						    $emails_data[$j] = 0;
					    }

					    if (!empty($bounces_array[$datestring])) {
						    $bounces_data[$j] = $bounces_array[$datestring];
					    } else {
						    $bounces_data[$j] = 0;
					    }

					    if (!empty($unsubscribes_array[$datestring])) {
						    $unsubscribes_data[$j] = $unsubscribes_array[$datestring];
					    } else {
						    $unsubscribes_data[$j] = 0;
					    }

					    if (!empty($reads_array[$datestring])) {
						    $reads_data[$j] = $reads_array[$datestring];
					    } else {
						    $reads_data[$j] = 0;
					    }

					    $j++;
				    }

				    $unsubscribes_data = array_reverse($unsubscribes_data);
				    $subscribers_data = array_reverse($subscribers_data);
				    $clicks_data = array_reverse($clicks_data);
				    $bounces_data = array_reverse($bounces_data);
				    $emails_data = array_reverse($emails_data);
				    $dates_data = array_reverse($dates_data);
				    $reads_data = array_reverse($reads_data);
			}

			$data = array();
		    $data['labels'] = $dates_data;
		    $data['datasets'] = array();

		    $data['datasets'][] = array(
				'label'					=>	__('Emails Sent', 'wp-mailinglist'),
	            'backgroundColor'		=>	"#4D5360",
	            'fill'					=>	false,
	            'highlightBackground'			=>	"#616774",
	            'borderColor'			=>	"#4D5360",
	            'pointColor'			=>	"#4D5360",
	            'pointBorderColor'		=>	"#4D5360",
	            'pointHighlightBackground'	=>	"#616774",
	            'pointHighlightBorder'	=>	"#616774",
	            'data'					=>	$emails_data,
			);

		    if (empty($history_id)) {
			    $data['datasets'][] = array(
					'label'					=>	__('Subscribers', 'wp-mailinglist'),
		            'backgroundColor'		=>	"#4679bf", //"#46BFBD",
		            'fill'					=>	false,
		            'highlightBackground'			=>	"#4679bf", //"#5AD3D1",
		            'borderColor'			=>	"#4679bf",
		            'pointColor'			=>	"#4679bf",
		            'pointBorderColor'		=>	"#4679bf",
		            'pointHighlightBackground'	=>	"#4679bf",
		            'pointHighlightBorder'	=>	"#4679bf",
		            'data'					=>	$subscribers_data,
				);
			}

			$data['datasets'][] = array(
				'label'					=>	__('Clicks', 'wp-mailinglist'),
	            'backgroundColor'		=>	"#949FB1",
	            'fill'					=>	false,
	            'highlightBackground'			=>	"#A8B3C5",
	            'borderColor'			=>	"#949FB1",
	            'pointColor'			=>	"#949FB1",
	            'pointBorderColor'		=>	"#949FB1",
	            'pointHighlightBackground'	=>	"#A8B3C5",
	            'pointHighlightBorder'	=>	"#A8B3C5",
	            'data'					=>	$clicks_data,
			);

			$data['datasets'][] = array(
				'label'					=>	__('Unsubscribes', 'wp-mailinglist'),
	            'backgroundColor'		=>	"#FDB45C",
	            'fill'					=>	false,
	            'highlightBackground'			=>	"#FFC870",
	            'borderColor'			=>	"#FDB45C",
	            'pointColor'			=>	"#FDB45C",
	            'pointBorderColor'		=>	"#FDB45C",
	            'pointHighlightBackground'	=>	"#FFC870",
	            'pointHighlightBorder'	=>	"#FFC870",
	            'data'					=>	$unsubscribes_data,
			);

			$data['datasets'][] = array(
				'label'					=>	__('Reads', 'wp-mailinglist'),
	            'backgroundColor'		=>	"#46BFBD",
	            'fill'					=>	false,
	            'highlightBackground'			=>	"#5AD3D1",
	            'borderColor'			=>	"#46BFBD",
	            'pointColor'			=>	"#46BFBD",
	            'pointBorderColor'		=>	"#46BFBD",
	            'pointHighlightBackground'	=>	"#5AD3D1",
	            'pointHighlightBorder'	=>	"#5AD3D1",
	            'data'					=>	$reads_data,
			);

			$data['datasets'][] = array(
				'label'					=>	__('Bounces', 'wp-mailinglist'),
				'backgroundColor'		=>	"#F7464A",
				'fill'					=>	false,
				'highlightBackground'			=>	"#FF5A5E",
				'borderColor'			=>	"#F7464A",
	            'pointColor'			=>	"#F7464A",
	            'pointBorderColor'		=>	"#F7464A",
	            'pointHighlightBackground'	=>	"#FF5A5E",
	            'pointHighlightBorder'	=>	"#FF5A5E",
				'data'					=>	$bounces_data,
			);

		    echo wp_json_encode($data);

		    exit();
		    die();
	    }

	    /* Forms Ajax */
	    function ajax_forms_createform() {
		    global $Field, $wpdb;

		    $ajax = false;
		    $success = false;
		    $errors = false;

		    if (!empty($_POST)) {	
			    check_ajax_referer($this -> sections -> forms . '_createform');
			    	
			    if (current_user_can('newsletters_forms')) {	    
                    $subscribeform = $this -> Subscribeform();
				    if ( $subscribeform -> save($_POST[ $subscribeform -> model])) {
                        $object_manual = $this -> getobject($subscribeform -> model);
                        $query = "SELECT MAX(id) FROM  ". $wpdb -> prefix . "wpmlforms ; ";
                        $insertID = $wpdb->get_var( $query );


					    $success = true;
					    $insertid =  $insertID;
                        $this -> Subscribeform() -> insertid = $insertID;
					    // Create the default email address and mailing list fields
					    $emailfield = $Field -> email_field();
					    $fieldform_data = array(
							'id'						=>	false,
							'form_id'					=>	$insertid,
							'field_id'					=>	$emailfield -> id,
							'order'						=>	1,
							'label'						=>	false,
							'caption'					=>	false,
							'placeholder'				=>	false,
							'required'					=>	1,
						);
	
						$this -> FieldsForm() -> save($fieldform_data);
	
						$listfield = $Field -> list_field();
						$fieldform_data = array(
							'id'						=>	false,
							'form_id'					=>	$insertid,
							'field_id'					=>	$listfield -> id,
							'order'						=>	2,
							'label'						=>	false,
							'caption'					=>	false,
							'placeholder'				=>	false,
							'required'					=>	1,
						);
	
						$this -> FieldsForm() -> save($fieldform_data);
				    }
	
				    $ajax = true;
				    $errors = $this -> Subscribeform() -> errors;
				} else {
					$errors[] = __('You do not have permission', 'wp-mailinglist');
				}
		    }

		    $this -> render('forms' . DS . 'createform', array('ajax' => $ajax, 'success' => $success, 'errors' => $errors), true, 'admin');

		    exit();
		    die();
	    }

	    function ajax_forms_addfield() {
		    
		    check_ajax_referer('forms_addfield', 'security');
		    
		    if (!current_user_can('newsletters_forms')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }

		    global $wpdb, $Db, $Field;
		    $Db -> model = $Field -> model;
		    $field = $Db -> find(array('id' => sanitize_text_field(wp_unslash($_POST['id']))));

		    global $Metabox;
		    $form_field = new stdClass();
		    $form_field -> field = $field;
		    $form_field -> field_id = $field -> id;
		    $metabox = array('args' => array('form_field' => $form_field));

		    ob_start();
		    $Metabox -> forms_field(false, $metabox);
		    $content = ob_get_clean();
		    $this -> render('forms' . DS . 'field', array('field' => $field, 'form_field' => $form_field, 'content' => $content), true, 'admin');

		    exit();
		    die();
	    }

	    function ajax_forms_deletefield() {
		    
		    check_ajax_referer('forms_deletefield', 'security');
		    
		    if (!current_user_can('newsletters_forms')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }

		    if (!empty($_POST['field_id']) && !empty($_POST['form_id'])) {
		   		$this -> FieldsForm() -> delete_all(array('field_id' => sanitize_text_field(wp_unslash($_POST['field_id'])), 'form_id' => sanitize_text_field(wp_unslash($_POST['form_id']))));
		   	}

		    exit();
		    die();
	    }

		function ajax_setvariables() {
			
			check_ajax_referer('setvariables', 'security');
			
			if (!current_user_can('newsletters_send')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			$this -> render('setvariables', array('noinsert' => true), true, 'admin');

			exit();
			die();
		}

		function ajax_change_themefolder() {
			
			check_ajax_referer('change_themefolder', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			$message = false;
			if (!empty($_POST['themefolder'])) {
				$this -> update_option('theme_folder', sanitize_text_field(wp_unslash($_POST['themefolder'])));
				$this -> delete_all_cache('all');
				$this -> theme_folder_functions(sanitize_text_field(wp_unslash($_POST['themefolder'])));
				$message = __('Theme folder has been changed, please reconfigure styles/scripts below', 'wp-mailinglist');
			}

			$this -> render('settings' . DS . 'defaultscriptsstyles', array('successmessage' => $message), true, 'admin');

			exit();
			die();
		}

		function ajax_delete_option() {
			
			check_ajax_referer('delete_option', 'security');
			
			if (!current_user_can('newsletters_fields')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));	
			}

			$success = false;

			if (!empty($_REQUEST['id'])) {
				$id = sanitize_text_field(wp_unslash($_REQUEST['id']));
				if ($this -> Option() -> delete($id)) {
					$success = true;
				}
			}

			echo wp_kses_post($success);

			exit();
			die();
		}

		function ajax_pause_queue() {
			
			check_ajax_referer('pause_queue', 'security');
			
			if (!current_user_can('newsletters_queue')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			$success = false;

			if (!empty($_POST['status'])) {
				$status = sanitize_text_field(wp_unslash($_POST['status']));

				if ($this -> update_option('queue_status', $status)) {
					$success = true;
					$this -> delete_option('hidemessage_queue_status');
				}
			}

			echo wp_kses_post($success);

			exit();
			die();
		}

		function ajax_executemultiple() {
			
			check_ajax_referer('executemultiple', 'security');
			
			if (!current_user_can('newsletters_history')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $wpdb, $Html, $Db, $Subscriber, $HistoriesAttachment;
			$subscribers = (object) stripslashes_deep($_REQUEST['subscribers']);

			if (!empty($subscribers)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $this -> History() -> table . " WHERE id = '" . esc_sql($_REQUEST['history_id']) . "' LIMIT 1";
				$history = $wpdb -> get_row($historyquery);

				if (!empty($history)) {
					foreach ($subscribers as $subscriber_request) {
						$subscriber_request = (object)$subscriber_request;

						if (!empty($subscriber_request -> user_id)) {
							$user = $this -> userdata($subscriber_request -> user_id);
							$email = $user -> user_email;
							$eunique = $Html -> eunique($user, $history -> id);
							$subscriber = false;
						} else {
							$subscriber = $Subscriber -> get($subscriber_request -> id, false);
							$email = $subscriber -> email;
							$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
							$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
							$eunique = $Html -> eunique($subscriber, $history -> id);
							$user = false;
						}

						$content = $history -> message;
						$subject = wp_unslash($history -> subject);
						$history_id = sanitize_text_field($_REQUEST['history_id']);
						$post_id = sanitize_text_field($_REQUEST['post_id']);
						$theme_id = sanitize_text_field($_REQUEST['theme_id']);
						$shortlinks = true;

						$newattachments = array();
						$Db -> model = $HistoriesAttachment -> model;
						if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
							foreach ($attachments as $attachment) {
								$newattachments[] = array(
									'id'					=>	$attachment -> id,
									'title'					=>	$attachment -> title,
									'filename'				=>	$attachment -> filename,
								);
							}
						}

						$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id, 'post_id' => $post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $theme_id, true);

						if ($this -> execute_mail($subscriber, $user, $subject, $message, $newattachments, $history_id, $eunique, true, "newsletter")) {
							$success = "Y<|>" . $email . "<|>" . __('Success', 'wp-mailinglist') . "<||>";
						} else {
							global $mailerrors;
							$success = "N<|>" . $email . "<|>" . strip_tags($mailerrors) . "<||>";
						}

						echo wp_kses_post($success);
					}
				} else {
					$success = "N<|>" . $email . "<|>" . __('History email could not be read', 'wp-mailinglist') . "<||>";
					echo wp_kses_post($success);
				}
			} else {
				$success = "N<|>" . $email . "<|>" . __('No data was posted', 'wp-mailinglist') . "<||>";
				echo wp_kses_post($success);
			}

			exit();
			die();
		}

		function ajax_queuemultiple() {
			
			check_ajax_referer('queuemultiple', 'security');
			
			if (!current_user_can('newsletters_queue')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $wpdb, $Html, $Db, $Subscriber, $HistoriesAttachment;

			$subscribers = stripslashes_deep($_REQUEST['subscribers']);
			
			$this -> qp_reset_data();

			if (!empty($subscribers)) {
				$historyquery = "SELECT id, message, subject FROM " . $wpdb -> prefix . $this -> History() -> table . " WHERE id = '" . esc_sql($_REQUEST['history_id']) . "' LIMIT 1";
				$history = $wpdb -> get_row($historyquery);

				if (!empty($history)) {
					foreach ($subscribers as $subscriber_request) {
						$subscriber_request = (object) $subscriber_request;
						
						$content = $history -> message;
						$subject = wp_unslash($history -> subject);
						$history_id = sanitize_text_field($_REQUEST['history_id']);
						$post_id = sanitize_text_field($_REQUEST['post_id']);
						$theme_id = sanitize_text_field($_REQUEST['theme_id']);
						$senddate = sanitize_text_field($_REQUEST['senddate']);
						$shortlinks = true;

						$newattachments = array();
						$Db -> model = $HistoriesAttachment -> model;
						if ($attachments = $Db -> find_all(array('history_id' => $history_id))) {
							foreach ($attachments as $attachment) {
								$newattachments[] = array(
									'id'					=>	$attachment -> id,
									'title'					=>	$attachment -> title,
									'filename'				=>	$attachment -> filename,
								);
							}
						}

						if (!empty($subscriber_request -> user_id)) {
							$subscriber = false;
							$user = $this -> userdata($subscriber_request -> user_id);
							$email = $user -> user_email;
							
							$queue_process_data = array(
								'user_id'					=>	$user -> ID,
								'subject'					=>	$subject,
								//'message'					=>	$content,
								'attachments'				=>	$newattachments,
								'post_id'					=>	$post_id,
								'history_id'				=>	$history_id,
								'theme_id'					=>	$theme_id,
								'senddate'					=>	$senddate,
							);
						} else {
							$user = false;
							$subscriber = $Subscriber -> get($subscriber_request -> id, false);
							$email = $subscriber -> email;
							$subscriber -> mailinglist_id = $subscriber_request -> mailinglist_id;
							$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, $subscriber_request -> mailinglists);
							
							$queue_process_data = array(
								'subscriber_id'				=>	$subscriber -> id,
								'subject'					=>	$subject,
								//'message'					=>	$content,
								'attachments'				=>	$newattachments,
								'post_id'					=>	$post_id,
								'history_id'				=>	$history_id,
								'theme_id'					=>	$theme_id,
								'senddate'					=>	$senddate,
							);
						}
						
						$this -> queue_process_1 -> push_to_queue($queue_process_data);
						$success = "Y<|>" . $email . "<|>" . __('Success', 'wp-mailinglist') . "<||>";
						echo wp_kses_post($success);
					}
				} else {
					$success = "N<|>" . $email . "<|>" . __('History email could not be read', 'wp-mailinglist') . "<||>";
					echo wp_kses_post($success);
				}
			} else {
				$success = "N<|>" . $email . "<|>" . __('No data was posted', 'wp-mailinglist') . "<||>";
				echo wp_kses_post($success);
			}
			
			$this -> qp_save();
			$this -> qp_dispatch();

			exit();
			die();
		}

		function ajax_exportmultiple() {
			
			check_ajax_referer('exportmultiple', 'security');
			
			if (!current_user_can('newsletters_importexport')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));	
			}
			
			global $Html;
			$exportfilename = sanitize_text_field(wp_unslash($_REQUEST['exportfile']));
			$exportfilepath = $Html -> uploads_path() . '/' . $this -> plugin_name . '/export/';
			$exportfilefull = $exportfilepath . $exportfilename;

			if ($fp = fopen($exportfilefull, "a")) {
				$csvdelimiter = $this -> get_option('csvdelimiter');
				$delimiter = (empty($_REQUEST['delimiter'])) ? $csvdelimiter :  sanitize_text_field(wp_unslash($_REQUEST['delimiter']));
				$headings = map_deep(wp_unslash($_REQUEST['headings']), 'sanitize_text_field');
				$subscribers = map_deep(wp_unslash($_REQUEST['subscribers']), 'sanitize_text_field');

				$headings_keys = array();
				foreach ($headings as $hkey => $hval) {
					$headings_keys[$hkey] = '';
				}

				if (!empty($subscribers)) {
					foreach ($subscribers as $subscriber) {
						$subscriber = array_merge($headings_keys, $subscriber);

						if (!empty($subscriber)) {
							fputcsv($fp, $subscriber, $delimiter, '"');
						}

						echo esc_html( $subscriber['email'] . "<|>");
					}
				}

				fclose($fp);
			}

			exit();
			die();
		}
		
		function ajax_dismissed_notice() {
			
			check_ajax_referer('dismissed_notice', 'security');
			
			if (current_user_can('newsletters_welcome')) {
				// Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
			    $type = sanitize_text_field(wp_unslash($_REQUEST['type']));
			    // Store it in the options table
			    $this -> update_option('dismissed-' . $type, true);
			}
		    
		    exit();
		    die();
		}

		function ajax_subscribe() {
			global $Subscriber, $Mailinglist, $Html;
			
			check_ajax_referer('subscribe', 'security');
			
			// No current_user_can required, open on the front-end

			$widget_id = sanitize_text_field(wp_unslash($_GET['widget_id']));
			$number = sanitize_text_field(wp_unslash($_GET['number']));
			$instance = $this -> widget_instance($number);

            $newinstance = null;

            if (isset($_POST['instance']) && is_array($_POST['instance'])) {
                $newinstance = array_map('sanitize_text_field', $_POST['instance']);
            }

            if (!empty($newinstance)) {
				$r = wp_parse_args($newinstance, $instance);
				$instance = $r;
			}

			$action = ($this -> language_do()) ? $this -> language_converturl(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $instance['language']) : sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
			$action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;

			if ($subscriber_id = $Subscriber -> optin($_POST)) {
				// This is not a subscribe form
				if (empty($_POST['form_id'])) {
					echo '<div class="newsletters-acknowledgement">' . wp_kses_post(wpautop(esc_html($instance['acknowledgement']))) . '</div>';

					if ($paidlist_id = $Mailinglist -> has_paid_list(sanitize_text_field(wp_unslash($_POST['list_id'])))) {
						$subscriber = $Subscriber -> get($subscriber_id, false);
						$paidlist = $Mailinglist -> get($paidlist_id, false);						
						$this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $paidlist -> id . '&extend=0', $this -> get_managementpost(true)), false, false, true);
					}

					if ($this -> get_option('subscriberedirect') == "Y") {
						$subscriberedirecturl = $this -> get_option('subscriberedirecturl');

						if (!empty($_POST['list_id']) && (!is_array($_POST['list_id']) || count($_POST['list_id']) == 1)) {
							if ($subscribelist = $Mailinglist -> get((int) $_POST['list_id'][0])) {
								if (!empty($subscribelist -> subredirect)) {
									$subscriberedirecturl = esc_html($subscribelist -> subredirect);
								}
							}
						}

						$this -> redirect($subscriberedirecturl, false, false, true);
					}
				// This is a subscribe form
				} elseif (!empty($_POST['form_id'])) {
					if ($form = $this -> Subscribeform() -> find(array('id' => (int) $_POST['form_id']))) {
						$subscriber = $Subscriber -> get($subscriber_id, false);
						
						if ($paidlist_id = $Mailinglist -> has_paid_list((int) $_POST['list_id'])) {
							$paidlist = $Mailinglist -> get($paidlist_id, false);
							$this -> redirect($Html -> retainquery('method=paidsubscription&subscriber_id=' . $subscriber -> id . '&list_id=' . $paidlist -> id . '&extend=0', $this -> get_managementpost(true)), false, false, true);
						}

						if (empty($form -> confirmationtype) || $form -> confirmationtype == "message") {
							if (!empty($form -> confirmation_message)) {
							    // phpcs:ignore
								echo '<div class="newsletters-acknowledgement">' . wpautop(esc_html($form -> confirmation_message)) . '</div>';
							}
						} elseif ($form -> confirmationtype == "redirect") {
							if (!empty($form -> confirmation_redirect)) {
								$redirect = do_shortcode(wp_unslash(esc_html($form -> confirmation_redirect)));
								$redirect = $this -> process_set_variables($subscriber, false, $redirect);								
								$this -> redirect($redirect, false, false, true);
							}
						}
					}
				}
			} else {
				$errors = $Subscriber -> errors;
				
				if (empty($_POST['form_id'])) {
					$this -> render('widget', array('action' => $action, 'widget' => sanitize_text_field(wp_unslash($_GET['widget'])), 'errors' => $errors, 'args' => $widget, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), true, 'default');
				} elseif (!empty($_POST['form_id'])) {
					if ($form = $this -> Subscribeform() -> find(array('id' => (int) $_POST['form_id']))) {
						$this -> render('subscribe', array('form' => $form, 'errors' => $Subscriber -> errors), true, 'default');
					}
				}
			}

			exit();
			die();
		}

		function ajax_template_iframe() {
			
			check_ajax_referer('template_iframe', 'security');
			
			if (!current_user_can('newsletters_templates')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));	
			}

			global $Db;
			$id = sanitize_text_field(wp_unslash($_REQUEST['id']));
			$template = $this -> Template() -> find(array('id' => $id));
			$this -> render('templates' . DS . 'iframe', array('template' => $template), true, 'admin');

			exit();
			die();
		}
		
		function getbodyandcss($html = null) {
			ob_start();
			$content = '';
			
			// Check if DOMDocument class exists
			if (!class_exists('DOMDocument')) {
				return $html;
			}
			
			$d = new DOMDocument;
			$mock = new DOMDocument;
			$d -> loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . wp_unslash($html));
			
			foreach ($d -> getElementsByTagName('style') as $style) {
				$mock -> appendChild($mock -> importNode($style, true));
			}
			
			$body = $d -> getElementsByTagName('body') -> item(0);
			foreach ($body -> childNodes as $child) {
			    $mock -> appendChild($mock -> importNode($child, true));
			}
			
			$html = $mock -> saveHTML();
            $allowed_html = array(

                'address'    => array(),
                'a'          => array(
                    'href'     => true,
                    'rel'      => true,
                    'rev'      => true,
                    'name'     => true,
                    'target'   => true,
                    'download' => array(
                        'valueless' => 'y',
                    ),
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'abbr'       => array(),
                'acronym'    => array(),
                'area'       => array(
                    'alt'    => true,
                    'coords' => true,
                    'href'   => true,
                    'nohref' => true,
                    'shape'  => true,
                    'target' => true,
                ),
                'article'    => array(
                    'align' => true,
                ),
                'aside'      => array(
                    'align' => true,
                ),
                'audio'      => array(
                    'autoplay' => true,
                    'controls' => true,
                    'loop'     => true,
                    'muted'    => true,
                    'preload'  => true,
                    'src'      => true,
                ),
                'b'          => array(),
                'bdo'        => array(),
                'big'        => array(),
                'blockquote' => array(
                    'cite' => true,
                ),
                'br'         => array(),
                'button'     => array(
                    'disabled' => true,
                    'name'     => true,
                    'type'     => true,
                    'value'    => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'caption'    => array(
                    'align' => true,
                ),
                'cite'       => array(),
                'code'       => array(),
                'col'        => array(
                    'align'   => true,
                    'char'    => true,
                    'charoff' => true,
                    'span'    => true,
                    'valign'  => true,
                    'width'   => true,
                ),
                'colgroup'   => array(
                    'align'   => true,
                    'char'    => true,
                    'charoff' => true,
                    'span'    => true,
                    'valign'  => true,
                    'width'   => true,
                ),
                'del'        => array(
                    'datetime' => true,
                ),
                'dd'         => array(),
                'dfn'        => array(),
                'details'    => array(
                    'align' => true,
                    'open'  => true,
                ),
                'div'        => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'dl'         => array(),
                'dt'         => array(),
                'em'         => array(),
                'fieldset'   => array(),
                'figure'     => array(
                    'align' => true,
                ),
                'figcaption' => array(
                    'align' => true,
                ),
                'font'       => array(
                    'color' => true,
                    'face'  => true,
                    'size'  => true,
                ),
                'footer'     => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'head' => array(),

                'h1'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'h2'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'h3'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'h4'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'h5'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'h6'         => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'header'     => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'hgroup'     => array(
                    'align' => true,
                ),
                'hr'         => array(
                    'align'   => true,
                    'noshade' => true,
                    'size'    => true,
                    'width'   => true,
                ),
                'i'          => array(),
                'img'        => array(
                    'alt'      => true,
                    'align'    => true,
                    'border'   => true,
                    'height'   => true,
                    'hspace'   => true,
                    'loading'  => true,
                    'longdesc' => true,
                    'vspace'   => true,
                    'src'      => true,
                    'usemap'   => true,
                    'width'    => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'ins'        => array(
                    'datetime' => true,
                    'cite'     => true,
                ),
                'kbd'        => array(),
                'label'      => array(
                    'for' => true,
                    'id' => true,
                    'class' => true,
                ),
                'legend'     => array(
                    'align' => true,
                ),
                'li'         => array(
                    'align' => true,
                    'value' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'main'       => array(
                    'align' => true,
                ),
                'map'        => array(
                    'name' => true,
                ),
                'mark'       => array(),
                'menu'       => array(
                    'type' => true,
                ),
                'nav'        => array(
                    'align' => true,
                ),
                'object'     => array(
                    'data' => array(
                        'required'       => true,
                        'value_callback' => '_wp_kses_allow_pdf_objects',
                    ),
                    'type' => array(
                        'required' => true,
                        'values'   => array( 'application/pdf' ),
                    ),
                ),
                'style'      => array (
                     'type' =>    true,
                ),
                'p'          => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'pre'        => array(
                    'width' => true,
                ),
                'q'          => array(
                    'cite' => true,
                ),
                'rb'         => array(),
                'rp'         => array(),
                'rt'         => array(),
                'rtc'        => array(),
                'ruby'       => array(),
                's'          => array(),
                'samp'       => array(),
                'span'       => array(
                    'align' => true,
                ),
                'section'    => array(
                    'align' => true,
                    'id' => true,
                    'class' => true,
                ),
                'small'      => array(),
                'strike'     => array(),
                'strong'     => array(),
                'sub'        => array(),
                'summary'    => array(
                    'align' => true,
                ),
                'sup'        => array(),
                'table'      => array(
                    'align'       => true,
                    'bgcolor'     => true,
                    'border'      => true,
                    'cellpadding' => true,
                    'cellspacing' => true,
                    'rules'       => true,
                    'summary'     => true,
                    'width'       => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'tbody'      => array(
                    'align'   => true,
                    'char'    => true,
                    'charoff' => true,
                    'valign'  => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'td'         => array(
                    'abbr'    => true,
                    'align'   => true,
                    'axis'    => true,
                    'bgcolor' => true,
                    'char'    => true,
                    'charoff' => true,
                    'colspan' => true,
                    'headers' => true,
                    'height'  => true,
                    'nowrap'  => true,
                    'rowspan' => true,
                    'scope'   => true,
                    'valign'  => true,
                    'width'   => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'textarea'   => array(
                    'cols'     => true,
                    'rows'     => true,
                    'disabled' => true,
                    'name'     => true,
                    'readonly' => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'tfoot'      => array(
                    'align'   => true,
                    'char'    => true,
                    'charoff' => true,
                    'valign'  => true,
                ),
                'th'         => array(
                    'abbr'    => true,
                    'align'   => true,
                    'axis'    => true,
                    'bgcolor' => true,
                    'char'    => true,
                    'charoff' => true,
                    'colspan' => true,
                    'headers' => true,
                    'height'  => true,
                    'nowrap'  => true,
                    'rowspan' => true,
                    'scope'   => true,
                    'valign'  => true,
                    'width'   => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'thead'      => array(
                    'align'   => true,
                    'char'    => true,
                    'charoff' => true,
                    'valign'  => true,
                ),
                'title'      => array(),
                'tr'         => array(
                    'align'   => true,
                    'bgcolor' => true,
                    'char'    => true,
                    'charoff' => true,
                    'valign'  => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                ),
                'track'      => array(
                    'default' => true,
                    'kind'    => true,
                    'label'   => true,
                    'src'     => true,
                    'srclang' => true,
                ),
                'tt'         => array(),
                'u'          => array(),
                'ul'         => array(
                    'type' => true,
                ),
                'ol'         => array(
                    'start'    => true,
                    'type'     => true,
                    'reversed' => true,
                ),
                'var'        => array(),
                'video'      => array(
                    'autoplay'    => true,
                    'controls'    => true,
                    'height'      => true,
                    'loop'        => true,
                    'muted'       => true,
                    'playsinline' => true,
                    'poster'      => true,
                    'preload'     => true,
                    'src'         => true,
                    'width'       => true,
                    'id' => true,
                    'class' => true,
                    'style' =>  true,
                )
            );

            $allowedProtocols = [ 'http', 'https' ];
			echo wp_kses($html , $allowed_html, $allowedProtocols);
		
			$output = ob_get_clean();
			return wp_unslash($output);
		}

		function ajax_form_preview() {
			
			check_ajax_referer('form_preview', 'security');
			
			if (!current_user_can('newsletters_forms')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			$id = sanitize_text_field(wp_unslash($_GET['id']));
			if (!empty($id)) {				
				if ($form = $this -> Subscribeform() -> find(array('id' => $id))) {	
					
					if (!empty($_REQUEST['saveform'])) {
						
						$languagefields = array('title', 'buttontext', 'confirmation_message', 'confirmation_redirect', 'styling_beforeform', 'styling_afterform', 'etsubject_confirm', 'etmessage_confirm');
						
						foreach ($_REQUEST as $pkey => $pval) {
						    $pkey = sanitize_text_field($pkey);
						    $pval = sanitize_text_field($pval);

							// Language fields
							if (!empty($pval) && in_array($pkey, $languagefields)) {
								switch ($key) {
									default 					:
										if (is_array($pval) && array_filter($pval)) {
											$_REQUEST[$pkey] = $this -> language_join($pval);
										} else {
											$_REQUEST[$pkey] = false;
										}
										break;
								}
							}
						}
						
						$form = wp_parse_args($_REQUEST, (array) $form);
						$form = (object) $form;
					}
									
					?>

					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
							<title><?php echo urldecode(esc_html(isset($_GET['title']) ? $_GET['title'] : '')); ?></title>

                            <?php wp_head(); ?>
						</head>
						<body style="background:none;">
							<div class="entry">
								<?php $this -> render('subscribe', array('form' => $form), true, 'default'); ?>
							</div>
							<div style="display:none;">
								<?php wp_footer(); ?>
							</div>
						</body>
					</html>

					<?php
				} else {
					echo '<p class="newsletters_error">' . wp_kses_post(__('Form could not be loaded', 'wp-mailinglist')) . '</p>';
				}
			} else {
				echo '<p class="newsletters_error">' . wp_kses_post(__('No form was specified', 'wp-mailinglist')) . '</p>';
			}

			exit();
			die();
		}

		function widget_object() {
			$widget = new Newsletters_Widget();
			return $widget;
		}

		function widget_settings() {
			$widget = new Newsletters_Widget();
			$settings = $widget -> get_settings();
			return $settings;
		}

		function widget_instance($number = null, $atts = array()) {
			if (!empty($number)) {
				$widget = new Newsletters_Widget();
				$settings = $widget -> get_settings();

				if (!empty($settings[$number])) {
					$instance = $settings[$number];

					if ($this -> language_do()) {
						$instance['lang'] = $this -> language_current();
					}
				} else {
					if ($embed = $this -> get_option('embed')) {
						$instance = wp_parse_args($atts, $embed);

						if (empty($instance['list'])) {
							if ($instance['type'] == "list") {
								$instance['list'] = $instance['id'];
							} else {
								$instance['list'] = $instance['type'];
								if (empty($instance['lists']) && !empty($instance['id'])) {
									$instance['lists'] = $instance['id'];
								}
							}
						}

						unset($instance['type']);
						unset($instance['id']);
					}
				}

				return $instance;
			}

			return false;
		}

		function ajax_getlistfields() {
			
			check_ajax_referer('refreshfields', 'security');
			
			// No current_user_can required, this is open to all roles and non-users on the front-end
			
			global $wpdb, $FieldsList, $Subscriber;
			$widget_id = sanitize_text_field(wp_unslash($_GET['widget_id']));
			$instance = map_deep(wp_unslash($_POST['instance']), 'sanitize_text_field');

			$Subscriber -> data = map_deep(wp_unslash($_POST), 'sanitize_text_field');
			
			echo '<div class="newsletters-new-fields">';

			if ($fields = $FieldsList -> fields_by_list(sanitize_text_field(wp_unslash($_POST['list_id'])), "order", "ASC")) {
				foreach ($fields as $field) {
					$this -> render_field($field -> id, true, $widget_id, true, true, $instance);
				}
			}
			
			echo '</div>';

			?>

			<script type="text/javascript">
			jQuery(document).ready(function() {
				if (jQuery.isFunction(jQuery.fn.select2)) {
					jQuery('.newsletters select').select2();
				}

				jQuery('#<?php echo (int) $widget_id; ?>-form .newsletters-list-checkbox').on('click', function() { newsletters_refreshfields('<?php echo esc_html($widget_id); ?>'); });
				jQuery('#<?php echo (int) $widget_id; ?>-form .newsletters-list-select').on('change', function() { newsletters_refreshfields('<?php echo esc_html($widget_id); ?>'); });
			});
			</script>

			<?php

			exit();
			die();
		}

		function ajax_testsettings() {
			
			check_ajax_referer('testsettings', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $Subscriber, $Html;
			$errors = array();
			$success = false;

			if (!empty($_GET['init']) && !empty($_POST)) {
				foreach ($_POST as $pkey => $pval) {
					$this -> update_option($pkey, $pval);

					switch ($pkey) {
						case 'smtpfromname'			:
						case 'smtpfrom'				:
						case 'excerpt_more'			:
							if ($this -> language_do()) {
								$this -> update_option($pkey, $this -> language_join($pval));
							} else {
								$this -> update_option($pkey, $pval);
							}
							break;
					}
				}
			}

			if (empty($_GET['init']) && !empty($_POST)) {
				if (empty($_POST['testemail'])) { $errors[] = __('Please fill in an email address', 'wp-mailinglist'); }
				elseif (!$Subscriber -> email_validate(sanitize_email(wp_unslash($_POST['testemail'])))) { $errors[] = __('Please fill in a valid email address', 'wp-mailinglist'); }
				if (empty($_POST['subject'])) { $errors[] = __('Please fill in a subject', 'wp-mailinglist'); }
				if (empty($_POST['message'])) { $errors[] = __('Please fill in a message', 'wp-mailinglist'); }

				if (empty($errors)) {
					$email = sanitize_email(wp_unslash($_POST['testemail']));
					
					if (!$subscriber_id = $Subscriber -> email_exists($email)) {
						$subscriber_data = array('email' => $email);
						$Subscriber -> save($subscriber_data, false);
						$subscriber_id = $Subscriber -> insertid;
					}
					
					$subscriber = $Subscriber -> get($subscriber_id, false);
					
					$subject = wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject'])));
					$message = wp_kses_post(sanitize_text_field(wp_unslash($_POST['message'])));
					$message = $this -> render_email('send', array('message' => $message, 'subject' => $subject, 'subscriber' => $subscriber), false, true, true, false, true);

					$attachments = false;
					if (!empty($_POST['testattachment']) && $_POST['testattachment'] == 1) {
						$attachments = array(
							array(
								'title'					=>	__('Test Attachment', 'wp-mailinglist'),
								'filename'				=>	$this -> plugin_base() . DS . 'images' . DS . 'testattachment.png',
							)
						);
					}
					
					$eunique = $Html -> eunique($subscriber, false, 'test');

					if ($this -> execute_mail($subscriber, false, $subject, $message, $attachments, false, $eunique, false, "testing")) {						
						$success = true;
						$errors[] = __('Email was successfully sent, your settings are working!', 'wp-mailinglist');
					} else {
						global $mailerrors;
						$errors[] = $mailerrors;
					}
				}
			}

			echo '<div id="testsettingswrapper">';
			$this -> render('testsettings', array('errors' => $errors, 'success' => $success), true, 'admin');
			echo '</div>';

			exit();
			die();
		}
		
		function ajax_mailapi_mandrill_keytest() {
			
			check_ajax_referer('mailapi_mandrill_keytest', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			require_once($this -> plugin_base() . DS . 'vendors' . DS . 'mailapis' . DS . 'mandrill' . DS . 'Mandrill.php');

			try {
			    $mandrill = new Mandrill(sanitize_text_field(wp_unslash($_POST['key'])));
			    $result = $mandrill -> users -> ping();
			    echo '<span class="newsletters_success"><i class="fa fa-check"></i></span>';
			} catch(Mandrill_Error $e) {
			    // phpcs:ignore
			    echo '<span class="newsletters_error"><i class="fa fa-times"></i> ' . $e -> getMessage() . '</span>';
			}

			exit(); die();
		}

		function ajax_mailapi_mailgun_action() {
			
			check_ajax_referer('mailapi_mailgun_action', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			if (!empty($_POST['ac'])) {
				$mailgun_apikey = sanitize_text_field(wp_unslash($_POST['key']));
				$mailgun_domain = sanitize_text_field(wp_unslash($_POST['domain']));
				$mailgun_region = (empty($_POST['region']) || $_POST['region'] == "US") ? 'https://api.mailgun.net' : 'https://api.eu.mailgun.net';

				require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
				//$mailgun = new Mailgun\Mailgun($mailgun_apikey);
				$mg = Mailgun\Mailgun::create($mailgun_apikey, $mailgun_region);

				switch ($_POST['ac']) {
					case 'verify'					:
						try {
							$result = $mg -> events() -> get($mailgun_domain);
							$this -> render_message(__('MailGun API key works', 'wp-mailinglist'), false, false);

						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'checkdomains'				:
						try {
							$result = $mg -> domains() -> index();

							$items = $result -> getDomains();
							if (!empty($items)) {
								echo '<p>';
								foreach ($items as $item) {
									$class = (!empty($item -> getState()) && $item -> getState() == "active") ? 'newsletters_success' : 'newsletters_warning';
									// phpcs:ignore
									echo '<span class="' . $class . '">' . $item -> getName() . '</span><br/>';
								}
								echo '</p>';
							} else {
								$this -> render_error(__('No domains available.', 'wp-mailinglist'), false, false);
							}
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'adddomain'				:
						try {
							$password = wp_generate_password(12, false, false);
							$result = $mg -> domains() -> create($mailgun_domain, $password);

							$this -> render_message($result -> getMessage(), false, false);
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'events'					:
						try {
							$result = $mg -> events() -> get($mailgun_domain);
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
					case 'stats'					:
						try {
							$result = $mg -> stats() -> total($mailgun_domain);
						} catch (Exception $e) {
							$this -> render_error($e -> getMessage(), false, false);
						}
						break;
				}
			}

			exit();
			die();
		}

		function ajax_mailapi_amazonses_action() {
			
			check_ajax_referer('mailapi_amazonses_action', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			$success = false;
			$message = false;
			$error = false;

			if (!empty($_REQUEST['ac'])) {				
				require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');

				$signature_version = SimpleEmailService::REQUEST_SIGNATURE_V4;
				$ses = new SimpleEmailService(sanitize_text_field(wp_unslash($_REQUEST['key'])), sanitize_text_field(wp_unslash($_REQUEST['secret'])), 'email.' . sanitize_text_field(wp_unslash($_REQUEST['region']) . '.amazonaws.com'), false, $signature_version );

				switch ($_REQUEST['ac']) {
					case 'verifyemail'				:
						$email = esc_html($this -> get_option('smtpfrom'));
						if ($result = $ses -> verifyEmailAddress($email)) {
							$success = true;
							$message = sprintf(__('Email address %s has been verified.', 'wp-mailinglist'), $email);
						} else {
							//failed
							$error = __('Email could not be verified', 'wp-mailinglist');
						}
						break;
					case 'getverifiedemails'		:
						if ($result = $ses -> listVerifiedEmailAddresses()) {
							$success = true;

							$message = __('Currently verified email addresses:', 'wp-mailinglist');
							$message .= '<ul class="newsletters_success">';
							foreach ($result['Addresses'] as $address) {
								$message .= '<li>' . $address . '</li>';
							}
							$message .= '</ul>';
						} else {
							$error = __('Could not get list of verified email addresses.', 'wp-mailinglist');
						}
						break;
					case 'getsendquota'				:
						if ($result = $ses -> getSendQuota()) {
							$success = true;
							$message = '';
							$message .= sprintf(__('Max 24 Hour Send: %s', 'wp-mailinglist'), $result['Max24HourSend']);
							$message .= '<br/>';
							$message .= sprintf(__('Max Send Rate: %s', 'wp-mailinglist'), $result['MaxSendRate']);
							$message .= '<br/>';
							$message .= sprintf(__('Sent in Last 24 Hours: %s', 'wp-mailinglist'), $result['SentLast24Hours']);
						} else {
							$error = __('Could not get send quota.', 'wp-mailinglist');
						}
						break;
				}
			}

			if (!empty($success)) {
				echo esc_html('<p class="newsletters_success">' . $message . '</p>');
			} elseif (!empty($error)) {
				echo esc_html('<p class="newsletters_error">' . $error . '</p>');
			}

			exit(); die();
		}

		function ajax_dkimwizard() {
			
			check_ajax_referer('dkimwizard', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			switch ($_POST['goto']) {
				case 'step2'				:
					$this -> render('dkim' . DS . 'step2', array('domain' => sanitize_text_field(wp_unslash($_POST['domain'])), 'selector' => sanitize_text_field(wp_unslash($_POST['selector'])), 'public' => sanitize_text_field(wp_unslash($_POST['public'])), 'private' => sanitize_text_field(wp_unslash($_POST['private'])), true, 'admin'));
					break;
				case 'step3'				:
					$this -> render('dkim' . DS . 'step3', array('domain' => sanitize_text_field(wp_unslash($_POST['domain'])), 'selector' => sanitize_text_field(wp_unslash($_POST['selector'])), 'public' => sanitize_text_field(wp_unslash($_POST['public'])), 'private' => sanitize_text_field(wp_unslash($_POST['private'])), true, 'admin'));
					break;
				case 'step1'				:
				default 					:										
					require_once $this -> plugin_base() . DS . 'vendors' . DS . 'dkim' . DS . 'Crypt' . DS . 'RSA.php';
					$rsa = new Crypt_RSA();
					$keys = $rsa -> createKey();
					
					$this -> render('dkim' . DS . 'step1', array('domain' => sanitize_text_field(wp_unslash($_POST['domain'])), 'selector' => sanitize_text_field(wp_unslash($_POST['selector'])), 'public' => sanitize_text_field(wp_unslash($keys['publickey'])), 'private' => sanitize_text_field(wp_unslash($keys['privatekey'])), true, 'admin'));
					break;
			}

			exit();
			die();
		}

		function get_pop_status() {
			$pop_status = false;
			
			require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
			
			$type = $this -> get_option('bouncepop_type');
			$host = $this -> get_option('bouncepop_host');
			$user = $this -> get_option('bouncepop_user');
			$pass = $this -> get_option('bouncepop_pass');
			$port = $this -> get_option('bouncepop_port');
			$bouncepop_prot = $this -> get_option('bouncepop_prot');
			$ssl = (empty($bouncepop_prot)) ? false : (($bouncepop_prot == "ssl") ? true : false);
			
			try {
				$mailbox = new PhpImap\Mailbox('{' . $host . ':' . $port . '/' . $type . ((!empty($ssl)) ? '/ssl' : '') . '/novalidate-cert}INBOX', $user, $pass, false);
				
				try {
					
					$status = $mailbox -> checkMailbox();
					$pop3_status = sprintf(__('There are %s emails in the mailbox', 'wp-mailinglist'), $status -> Nmsgs);
					
				} catch (Exception $e) {
					$pop3_status = $e -> getMessage();
				}
			} catch (Exception $e) {
				$pop3_status = $e -> getMessage();
			}

	        return $pop3_status;
		}

		function ajax_autocomplete_users() {
			
			check_ajax_referer('autocomplete_users', 'security');
			
			if (!current_user_can('list_users')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			$args = array(
				'search'				=>	'*' . sanitize_text_field(wp_unslash($_REQUEST['q'])) . '*',
			);

			$user_query = new WP_User_Query($args);
			$users = $user_query -> get_results();

			$json = json_encode(array());

			if (!empty($users)) {
				$titles = array();
				$t = 0;
				foreach ($users as $user) {
					$titles[$t]['id'] = $user -> ID;
					$titles[$t]['text'] = $user -> display_name;
					$t++;
				}

				$json = json_encode($titles);
			}

			echo $json;

			exit();
			die();
		}

		function ajax_autocomplete_histories() {
			
			check_ajax_referer('autocomplete_histories', 'security');
			
			if (!current_user_can('newsletters_history')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			global $Db;

			$conditions = array();
			if (!empty($_REQUEST['q'])) {
				$conditions['subject'] = "LIKE '%" . esc_sql(sanitize_text_field(wp_unslash($_REQUEST['q']))) . "%'";
			}

			if ($histories = $this -> History() -> find_all($conditions)) {
				if (!empty($histories)) {
					$t = 0;

					foreach ($histories as $history) {
						$titles[$t]['id'] = $history -> id;
						$titles[$t]['text'] = esc_html($history -> subject);
						$t++;
					}

					$json = $titles;
				}
			}

			wp_send_json($json, 200);

			exit();
			die();
		}

		function ajax_testbouncesettings() {
			
			check_ajax_referer('testbouncesettings', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
			
			$type = (empty($_POST['type'])) ? 'imap' :  sanitize_text_field(wp_unslash($_POST['type']));
			$host = sanitize_text_field(wp_unslash($_POST['host']));
			$user = sanitize_text_field(wp_unslash($_POST['user']));
			$pass = sanitize_text_field(wp_unslash($_POST['pass']));
			$port = sanitize_text_field(wp_unslash($_POST['port']));
			$ssl = (empty($_POST['prot'])) ? false : (($_POST['prot'] == "ssl") ? true : false);
			
			try {
				$mailbox = new PhpImap\Mailbox('{' . $host . ':' . $port . '/' . $type . ((!empty($ssl)) ? '/ssl' : '') . '/novalidate-cert}INBOX', $user, $pass, false);
				
				try {
					
					$status = $mailbox -> checkMailbox();
					$message = sprintf(__('There are %s emails in the mailbox', 'wp-mailinglist'), $status -> Nmsgs);
					$success = true;
					
					// Read all messaged into an array:
					/*$mailsIds = $mailbox->searchMailbox('ALL');
					if(!$mailsIds) {
					    die('Mailbox is empty');
					}
					
					// Get the first message and save its attachment(s) to disk:
					$mail = $mailbox->getMail($mailsIds[0]);
					
					var_dump($mail);
					echo "\n\n\n\n\n";
					var_dump($mail->getAttachments());*/
					
				} catch (Exception $e) {
					$error = $e -> getMessage();
				}
			} catch (Exception $e) {
				$error = $e -> getMessage();
			}
	        
	        $this -> render('testbouncesettings', array('success' => $success, 'message' => $message, 'error' => $error), true, 'admin');

			exit();
			die();
		}

		function ajax_deletecontentarea() {
			
			check_ajax_referer('deletecontentarea', 'security');
			
			if (!current_user_can('newsletters_history')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			if (!empty($_POST['number']) && !empty($_POST['history_id'])) {
				$this -> Content() -> delete_all(array('number' => sanitize_text_field(wp_unslash($_POST['number'])), 'history_id' => sanitize_text_field(wp_unslash($_POST['history_id']))));
			}

			exit();
			die();
		}

		function ajax_order_fields() {
			
			check_ajax_referer('order_fields', 'security');
			
			if (!current_user_can('newsletters_fields')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $Db, $FieldsList, $Field;

			if (!empty($_REQUEST)) {
				if (!empty($_REQUEST['fields'])) {
					foreach (map_deep(wp_unslash($_REQUEST['fields']), 'sanitize_text_field') as $order => $field_id) {
						$Db -> model = $Field -> model;
						$Db -> save_field('order', $order, array('id' => $field_id));

						$Db -> model = $FieldsList -> model;
						$Db -> save_field('order', $order, array('field_id' => $field_id));
					}

					// phpcs:ignore
					_e('Custom fields order has been successfully saved', 'wp-mailinglist');
				} else {
				    // phpcs:ignore
					_e('No fields are available', 'wp-mailinglist');
				}
			} else {
			    // phpcs:ignore
				_e('No data posted', 'wp-mailinglist');
			}

			exit();
			die();
		}

		function ajax_themeedit() {
			
			check_ajax_referer('themeedit', 'security');
			
			if (!current_user_can('newsletters_themes')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $Db, $Theme;
			$success = false;
			$errors = array();

			if (!empty($_REQUEST)) {
				$id = sanitize_text_field(wp_unslash($_REQUEST['id']));
				if (!empty($id)) {
					if (!empty($_REQUEST['Theme'])) {
						$Db -> model = $Theme -> model;
						if ($Db -> save(map_deep(wp_unslash($_REQUEST), 'sanitize_text_field'))) {
							$success = true;
						} else {
							$errors = $Theme -> errors;
						}
					}

					$Db -> model = $Theme -> model;
					$Db -> find(array('id' => $id));
					$Theme -> data -> paste = $Theme -> data -> content;
				} else {
					$errors[] = __('No template was specified', 'wp-mailinglist');
				}
			} else {
				$errors[] = __('No data was specified', 'wp-mailinglist');
			}

			$this -> render('themes' . DS . 'save-ajax', array('success' => $success, 'errors' => $errors), true, 'admin');

			exit();
			die();
		}
		
		function ajax_newsletters_autosave_blockeditor() {
			
			check_ajax_referer('newsletters_autosave_blockeditor', 'security');
			
			if (!current_user_can('edit_posts')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $wpdb, $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
			
			$post_data = map_deep(wp_unslash($_POST), 'sanitize_text_field');
			$post_id = $post_data['ID'] = (!empty($_POST['post_ID'])) ? (int) sanitize_text_field(wp_unslash($_POST['post_ID'])) : false;
			$post = get_post($post_id);
			$save_newsletter = $this -> save_newsletter($post_id, $post, true);
			
			$history_id = get_post_meta($post_id, '_newsletters_history_id', true);
			if (!empty($history_id)) {
				$_GET['id'] = $history_id;
				$success = false;
				$error = false;
				
				ob_start();
				
				$subject = wp_kses_post(wp_unslash($_POST['post_title']));
                $content = $_POST['content'];
				
				$preview = $this -> ajax_historyiframe(true, false, $history_id, false);					
		    	$textpreview = $this -> ajax_historyiframe(true, true, $history_id, false);
		    	
		    	// Calculate the spam score
		    	$newsletters_presend = true;
		    	$subscriber_id = $Subscriber -> admin_subscriber_id();
		    	$subscriber = $Subscriber -> get($subscriber_id, false);
		    	$theme_id = sanitize_text_field(wp_unslash($_POST['newsletters_theme_id']));
				$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, $theme_id);
				$eunique = $Html -> eunique($subscriber, $history_id);
				$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, $eunique, false);
				$spamscore = $this -> spam_score($subscriber -> email, "long");
	
				// Save the 'spamscore'
				if (!empty($spamscore -> score)) {
					$this -> History() -> save_field('spamscore', $spamscore -> score, array('id' => $history_id));
				}
	
				if (empty($spamscore -> success) || $spamscore -> success == false) {
					$score = 0;
				} else {
					$score = $spamscore -> score;
				}
				
				$process = ob_get_clean();
	
				$spamscore_output = "";
				ob_start();
	
				?>
	
				<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php esc_html_e('See Report', 'wp-mailinglist'); ?></a></p>
				<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score . '&security=' . wp_create_nonce('gauge'))) ?>"></iframe>
	
				<div style="display:none;">
					<div id="spamscore_report">
						<div class="wrap newsletters">
							<h2><?php esc_html_e('Spam Score Report', 'wp-mailinglist'); ?></h2>
							<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>

	<?php // phpcs:ignore ?>
							<p><?php echo sprintf(__('The spam score is %s out of 10 for this email', 'wp-mailinglist'), $score); ?></p>
	
							<?php if (is_wp_error($spamscore)) : ?>
								<p class="newsletters_error"><?php echo esc_html( $spamscore -> get_error_message()); ?></p>
							<?php elseif (empty($spamscore -> success) || $spamscore -> success == false) : ?>
								<p class="newsletters_error"><?php echo esc_html( $spamscore -> message); ?></p>
							<?php else : ?>
								<h3><?php esc_html_e('Report', 'wp-mailinglist'); ?></h3>
								<p><pre><?php echo wp_kses_post($spamscore -> report); ?></pre></p>
							<?php endif; ?>
	
							<h3><?php esc_html_e('RAW Email', 'wp-mailinglist'); ?></h3>
							<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php esc_html_e('Toggle RAW Email', 'wp-mailinglist'); ?></a></p>
							<div id="rawemail-holder" style="display:none;">
								<p><pre><?php echo wp_kses_post(htmlspecialchars($newsletters_emailraw)); ?></pre></p>
							</div>
							<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
						</div>
					</div>
				</div>
	
				<?php
	
				$spamscore_output = ob_get_clean();
				//$success = true;
				//$error = false;
			} else {
				$error = __('No history ID was found', 'wp-mailinglist');
				$success = false;
			}
	    	
	    	$response = array(
		    	'success'				=>	$success,
		    	'error'					=>	$error,
		    	'history_id'			=>	$history_id,
		    	'post_id'				=>	$post_id,
		    	'parts'					=>	array(
			    	'spamscore'				=>	array(				    	
				    	'report'				=>	nl2br($spamscore -> report),
				    	'score'					=>	$score,
				    	'output'				=>	$spamscore_output,
			    	),
			    	'preview'				=>	array(
				    	'url'					=>	add_query_arg(array('newsletters_method' => "newsletter", 'id' => $history_id), home_url()),
				    	'html'					=>	$preview,
				    	'text'					=>	$textpreview,
			    	),
		    	),
	    	);
			
			echo wp_json_encode($response);
			
			exit();
			die();
		}

        function ajax_newsletter_drag_and_drop_builder_save() {

            check_ajax_referer('newsletters_autosave', 'security');

            if (!current_user_can('newsletters_history')) {
                wp_die(__('You do not have permission', 'wp-mailinglist'));
            }

            global $wpdb, $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
            $using_grapeJs = isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : "";
            $grapeJSContent = isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : "";
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : "";

            update_post_meta($post_id, 'grapejs_content' ,$grapeJSContent);
            update_post_meta($post_id, 'using_grapeJS' , $using_grapeJs);

            return;

        }


        function ajax_newsletters_autosave($internal_call = false) {
            if(!$internal_call) {
			    check_ajax_referer('newsletters_autosave', 'security');
            }
			
			if (!current_user_can('newsletters_history')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			global $wpdb, $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
			
			ob_start();
			
			// Save the history email and get the preview
			$history_data = array(
		    	'from'				=>	isset($_POST['from']) ? sanitize_text_field($_POST['from']) : "",
		    	'fromname'			=>	isset($_POST['fromname']) ? sanitize_text_field($_POST['fromname']) : "",
	    		'post_id'			=>	isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : "",
				'subject'			=>	isset($_POST['subject']) ? wp_kses_post(wp_unslash($_POST['subject'])) : "",
				'message'			=>	isset($_POST['content']) ? $_POST['content'] : "",
				'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? sanitize_textarea_field($_POST['customtext']) : false),
                'language'			=>	isset($_POST['language']) ? sanitize_text_field($_POST['language']) : "",
                'preheader'			=>	isset($_POST['preheader']) ? sanitize_text_field($_POST['preheader']) : "",
                'status'			=>	isset($_POST['status']) ? sanitize_text_field($_POST['status']) : "",
                'state'			    =>	isset($_POST['state']) ? sanitize_text_field($_POST['state']) : "",
                'theme_id'			=>	isset($_POST['theme_id']) ? sanitize_text_field($_POST['theme_id']) : "",
				'condquery'			=>	isset($_POST['condquery']) ? maybe_serialize(map_deep($_POST['condquery'], 'sanitize_text_field')) : "",
				'conditions'		=>	isset($_POST['fields']) ? maybe_serialize(map_deep($_POST['fields'], 'sanitize_text_field')) : "",
				'conditionsscope'	=>	isset($_POST['fieldsconditionsscope']) ? sanitize_text_field($_POST['fieldsconditionsscope']) : "",
				'daterange'			=>	isset($_POST['daterange']) ? sanitize_text_field($_POST['daterange']) :  "",
				'daterangefrom'		=>	isset($_POST['daterangefrom']) ? sanitize_text_field($_POST['daterangefrom']) : "",
				'daterangeto'		=>	isset($_POST['daterangeto']) ? sanitize_text_field($_POST['daterangeto']) : "",
				'countries'			=>	isset($_POST['countries']) ? map_deep($_POST['countries'], 'sanitize_text_field') : "",
				'selectedcountries'	=>	isset($_POST['selectedcountries']) ? maybe_serialize(map_deep($_POST['selectedcountries'], 'sanitize_text_field')) : "",
				'mailinglists'		=>	isset($_POST['mailinglists']) ? maybe_serialize(map_deep($_POST['mailinglists'], 'sanitize_text_field')) : "",
				'groups'			=>	isset($_POST['groups']) ? maybe_serialize(map_deep($_POST['groups'], 'sanitize_text_field')) : "",
				'roles'				=>	isset($_POST['roles']) ? maybe_serialize(map_deep($_POST['roles'], 'sanitize_text_field')) : "",
				'senddate'			=>	isset($_POST['senddate']) ? sanitize_text_field($_POST['senddate']) : "",
				//'scheduled'			=>	$_POST['scheduled'],
				'scheduled'			=>	"N",
				'format'			=>	isset($_POST['format']) ? sanitize_text_field($_POST['format']) : "",
                'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                'using_grapeJS'     =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''
			);

			if (isset($_POST['ishistory']) && !empty($_POST['ishistory'])) {
				$history_data['id'] = sanitize_text_field($_POST['ishistory']);
				$history_curr = $this -> History() -> find(array('id' => $history_data['id']));
				$history_data['sent'] = $history_curr -> sent;
			}

			if (isset($_POST['sendrecurring']) && !empty($_POST['sendrecurring'])) {
				if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
					$history_data['recurring'] = "Y";
					$history_data['recurringvalue'] = sanitize_text_field($_POST['sendrecurringvalue']);
					$history_data['recurringinterval'] = sanitize_text_field($_POST['sendrecurringinterval']);

					/*if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
						$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
					} else {*/
						$history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['sendrecurringdate']));
					//}

					$history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']));
				}
			}
            else {
                $history_data['recurring'] = "N";
                $history_data['recurringvalue'] = '0';
                $history_data['recurringinterval'] = '';
                $history_data['recurringdate'] = '0000-00-00 00:00:00';
                $history_data['recurringlimit'] = '0';

            }
            $history_data['recurringsent'] = isset($_POST['recurringsent']) ? sanitize_text_field($_POST['recurringsent']) : "0";
			if ($this -> History() -> save($history_data, false)) {
				$history_id = $this -> History() -> insertid;
				$p_id = $this -> History() -> field('p_id', array('id' => $history_id));

				if (!empty($_POST['contentarea'])) {
					foreach ($_POST['contentarea'] as $number => $content) {
						$content_data = array(
							'number'			=>	$number,
							'history_id'		=>	$history_id,
							'content'			=>	$content,
						);

						$this -> Content() -> save($content_data, true);
					}
				}
			}

			$history_id = $this -> History() -> insertid;
	    	$_GET['id'] = $history_id;
	    	$preview = $this -> ajax_historyiframe(true, false, false, false);
	    	$textpreview = $this -> ajax_historyiframe(true, true, false, false);
	    	
	    	// Calculate the spam score
	    	$newsletters_presend = true;
	    	$subscriber_id = $Subscriber -> admin_subscriber_id();
	    	$subscriber = $Subscriber -> get($subscriber_id, false);
	    	$subject = wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject'])));
	    	$content = wp_kses_post($_POST['content']);
			$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, sanitize_text_field(wp_unslash($_POST['theme_id'])));
			$eunique = $Html -> eunique($subscriber, $history_id);
			//$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, $eunique, false);
			$spamscore = $this -> spam_score($subscriber -> email, "long");

			// Save the 'spamscore'
			if (!empty($spamscore -> score)) {
				$this -> History() -> save_field('spamscore', $spamscore -> score, array('id' => $history_id));
			}

			if (empty($spamscore -> success) || $spamscore -> success == false) {
				$score = 0;
			} else {
				$score = $spamscore -> score;
			}
			
			$process = ob_get_clean();

			$spamscore_output = "";
            $spam_report = "";
			ob_start();

			?>

			<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php esc_html_e('See Report', 'wp-mailinglist'); ?></a></p>
			<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score . '&security=' . wp_create_nonce('gauge'))) ?>"></iframe>

			<div style="display:none;">
				<div id="spamscore_report">
					<div class="wrap newsletters">
						<h2><?php esc_html_e('Spam Score Report', 'wp-mailinglist'); ?></h2>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>

						<p><?php echo wp_kses_post(sprintf(__('The spam score is %s out of 10 for this email', 'wp-mailinglist'), $score)); ?></p>

						<?php if (is_wp_error($spamscore)) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> get_error_message()); ?></p>
						<?php elseif (empty($spamscore -> success) || $spamscore -> success == false) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> message); ?></p>
						<?php else : ?>
							<h3><?php _e('Report', 'wp-mailinglist'); ?></h3>
							<p><pre><?php echo ($spamscore -> report); ?></pre></p>
                            <?php $spam_report = $spamscore -> report; ?>
						<?php endif; ?>

						<h3><?php esc_html_e('RAW Email', 'wp-mailinglist'); ?></h3>
						<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php esc_html_e('Toggle RAW Email', 'wp-mailinglist'); ?></a></p>
						<div id="rawemail-holder" style="display:none;">
							<p><pre><?php echo wp_kses_post(htmlspecialchars($newsletters_emailraw)); ?></pre></p>
						</div>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
					</div>
				</div>
			</div>

			<?php

			$spamscore_output = ob_get_clean();
	    	
	    	$response = array(
		    	'history_id'			=>	$history_id,
		    	'post_id'				=>	$p_id,
		    	'parts'					=>	array(
			    	'spamscore'				=>	array(
				    	'report'				=>	nl2br($spamscore -> report),
				    	'score'					=>	$score,
				    	'output'				=>	$spamscore_output,
			    	),
			    	'preview'				=>	array(
				    	'url'					=>	add_query_arg(array('newsletters_method' => "newsletter", 'id' => $history_id), home_url()),
				    	'html'					=>	$preview,
				    	'text'					=>	$textpreview,
			    	),
		    	),
	    	);
			
			echo wp_json_encode($response);
			
			exit();
			die();
		}

		function ajax_previewrunner($justsave = false) {
			
			check_ajax_referer('previewrunner', 'security');
			
			if (!current_user_can('newsletters_history')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
	    	global $wpdb, $Db, $Html;

	    	if (empty($_POST['content'])) { exit(); }
	    	if (empty($_POST['subject']) || $_POST['subject'] == __('Enter email subject here', 'wp-mailinglist')) { exit(); }

	    	ob_start();
	    	$history_data = array(
		    	'from'				=>	sanitize_text_field($_POST['from']),
		    	'fromname'			=>	sanitize_text_field($_POST['fromname']),
	    		'post_id'			=>	sanitize_text_field($_POST['post_id']),
				'subject'			=>	wp_kses_post(wp_unslash($_POST['subject'])),
				'message'			=>	wp_kses_post(wp_unslash($_POST['content'])),
				'text'				=>	((!empty($_POST['customtexton']) && !empty($_POST['customtext'])) ? sanitize_textarea_field($_POST['customtext']) : false),
				'theme_id'			=>	sanitize_text_field($_POST['theme_id']),
				'condquery'			=>	maybe_serialize(map_deep($_POST['condquery'], 'sanitize_text_field')),
				'conditions'		=>	maybe_serialize(map_deep($_POST['fields'], 'sanitize_text_field')),
				'conditionsscope'	=>	sanitize_text_field($_POST['fieldsconditionsscope']),
				'daterange'			=>	sanitize_text_field($_POST['daterange']),
				'daterangefrom'		=>	sanitize_text_field($_POST['daterangefrom']),
				'daterangeto'		=>	sanitize_text_field($_POST['daterangeto']),
				'countries'			=>	map_deep($_POST['countries'], 'sanitize_text_field'),
				'selectedcountries'	=>	maybe_serialize(map_deep($_POST['selectedcountries'], 'sanitize_text_field')),
				'mailinglists'		=>	maybe_serialize(map_deep($_POST['mailinglists'], 'sanitize_text_field')),
				'groups'			=>	maybe_serialize(map_deep($_POST['groups'], 'sanitize_text_field')),
				'roles'				=>	maybe_serialize(map_deep($_POST['roles'], 'sanitize_text_field')),
				'senddate'			=>	sanitize_text_field($_POST['senddate']),
				'scheduled'			=>	sanitize_text_field($_POST['scheduled']),
				'format'			=>	sanitize_text_field($_POST['format']),
                'grapejs_content'   =>  isset($_POST['grapejs_content']) ? $_POST['grapejs_content'] : '',
                'using_grapeJS'     =>  isset($_POST['using_grapeJS']) ? $_POST['using_grapeJS'] : ''

			);

			if (!empty($_POST['ishistory'])) {
				$history_data['id'] = sanitize_text_field(wp_unslash($_POST['ishistory']));
				$history_curr = $this -> History() -> find(array('id' => $history_data['id']));
				$history_data['sent'] = $history_curr -> sent;
			}

			if (!empty($_POST['sendrecurring'])) {
				if (!empty($_POST['sendrecurringvalue']) && !empty($_POST['sendrecurringinterval']) && !empty($_POST['sendrecurringdate'])) {
					$history_data['recurring'] = "Y";
					$history_data['recurringvalue'] = sanitize_text_field(wp_unslash($_POST['sendrecurringvalue']));
					$history_data['recurringinterval'] = sanitize_text_field(wp_unslash($_POST['sendrecurringinterval']));

					/*if (!empty($history_curr) && $_POST['sendrecurringdate'] != $history_curr -> recurringdate) {
						$history_data['recurringdate'] = date_i18n("Y-m-d H:i:s", (strtotime($_POST['sendrecurringdate'] . " +" . $_POST['sendrecurringvalue'] . " " . $_POST['sendrecurringinterval'])));
					} else {*/
						$history_data['recurringdate'] = sanitize_text_field(wp_unslash($_POST['sendrecurringdate']));
					//}

					$history_data['recurringlimit'] = sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']));
				}
			}

			if ($this -> History() -> save($history_data, false)) {
				$history_id = $this -> History() -> insertid;
				$p_id = $this -> History() -> field('p_id', array('id' => $history_id));

				if (!empty($_POST['contentarea'])) {
				    //phpcs:ignore
					foreach (map_deep(wp_unslash($_POST['contentarea']), 'sanitize_text_field') as $number => $content) {
						$content_data = array(
							'number'			=>	$number,
							'history_id'		=>	$history_id,
							'content'			=>	$content,
						);

						$this -> Content() -> save($content_data, true);
					}
				}
			}

			$history_id = $this -> History() -> insertid;
	    	$_GET['id'] = $history_id;

	    	if (!empty($justsave)) {
		    	return $history_id;
	    	}

	    	$output = ob_get_clean();
	    	$preview = $this -> ajax_historyiframe(true, false, false, false);

	    	header("Content-Type: text/xml; charset=UTF-8");

	    	?>

	    	<result>
				<history_id><?php echo esc_html( $history_id); ?></history_id>
				<p_id><?php echo esc_html( $p_id); ?></p_id>
				<previewcontent><![CDATA[<?php echo wp_kses_post($preview); ?>]]></previewcontent>
				<textcontent><![CDATA[<?php echo wp_kses_post($preview); ?>]]></textcontent>
				<newsletter_url><![CDATA[<?php echo esc_url_raw($Html -> retainquery('newsletters_method=newsletter&id=' . $history_id, home_url())); ?>]]></newsletter_url>
			</result>

	    	<?php

		    exit();
		    die();
	    }
	    
	    function ajax_spamscore_blockeditor() {
		    
		    check_ajax_referer('spamscore_blockeditor', 'security');
		    
		    if (!current_user_can('newsletters_history')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
	    	
	    	$history_data = array(
		    	
	    	);
	    	
	    	global $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
	    	$newsletters_presend = true;
	    	$subscriber_id = $Subscriber -> admin_subscriber_id();
	    	$subscriber = $Subscriber -> get($subscriber_id, false);
	    	$subject = wp_kses_post(wp_unslash($_POST['post_title']));
	    	//$history_id = $_POST['ishistory'];
	    	$content = wp_kses_post($_POST['content']);
	    	$history_id = false;
	    	
			$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, sanitize_text_field(wp_unslash($_POST['newsletters_theme_id'])));
			$eunique = $Html -> eunique($subscriber, $history_id);
			$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, $eunique, false);			
			$spamscore = $this -> spam_score($subscriber -> email, "long");
			
			if (empty($spamscore -> success) || $spamscore -> success == false) {
				$score = 0;
				$success = false;
			} else {
				$score = $spamscore -> score;
				$success = true;
				$report = $spamscore -> report;
			}
	    	
	    	$output = "";
			ob_start();

			?>

			<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php esc_html_e('See Report', 'wp-mailinglist'); ?></a></p>
			<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score . '&security=' . wp_create_nonce('gauge'))) ?>"></iframe>

			<div style="display:none;">
				<div id="spamscore_report">
					<div class="wrap newsletters">
						<h2><?php esc_html_e('Spam Score Report', 'wp-mailinglist'); ?></h2>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>

						<p><?php echo wp_kses_post(sprintf(__('The spam score is %s out of 10 for this email', 'wp-mailinglist'), $score)); ?></p>

						<?php if (is_wp_error($spamscore)) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> get_error_message()); ?></p>
						<?php elseif (empty($success) || $success == false) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> message); ?></p>
						<?php else : ?>
							<h3><?php esc_html_e('Report', 'wp-mailinglist'); ?></h3>
							<p><pre><?php echo wp_kses_post($spamscore -> report); ?></pre></p>
						<?php endif; ?>

						<h3><?php esc_html_e('RAW Email', 'wp-mailinglist'); ?></h3>
						<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php esc_html_e('Toggle RAW Email', 'wp-mailinglist'); ?></a></p>
						<div id="rawemail-holder" style="display:none;">
							<p><pre><?php echo wp_kses_post(htmlspecialchars($newsletters_emailraw)); ?></pre></p>
						</div>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
					</div>
				</div>
			</div>

			<?php

			$output = ob_get_clean();
	    	
	    	$data = array(
				'success'			=>	$success,
				'report'			=>	$report,
				'score'				=>	$score,
				'output'			=>	$output,
			);
			
			echo wp_json_encode($data);
			exit(); die();

	    	global $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
	    	$newsletters_presend = true;
	    	$subscriber_id = $Subscriber -> admin_subscriber_id();
	    	$subscriber = $Subscriber -> get($subscriber_id, false);
	    	$subject = wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject'])));
	    	$content = wp_kses_post($_POST['content']);
	    	$history_id = sanitize_text_field(wp_unslash($_POST['ishistory']));
	    	$theme_id = sanitize_text_field(wp_unslash($_POST['theme_id']));
			$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, $theme_id);
			$eunique = $Html -> eunique($subscriber, $history_id);
			$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, $eunique, false);
			$spamscore = $this -> spam_score($subscriber -> email, "long");

			// Save the 'spamscore'
			if (!empty($spamscore -> score)) {
				$this -> History() -> save_field('spamscore', $spamscore -> score, array('id' => $history_id));
			}

			if (empty($spamscore -> success) || $spamscore -> success == false) {
				$score = 0;
			} else {
				$score = $spamscore -> score;
			}

			$output = "";
			ob_start();

			?>

			<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php esc_html_e('See Report', 'wp-mailinglist'); ?></a></p>
			<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_html( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score . '&security=' . wp_create_nonce('gauge'))); ?>"></iframe>

			<div style="display:none;">
				<div id="spamscore_report">
					<div class="wrap newsletters">
						<h2><?php esc_html_e('Spam Score Report', 'wp-mailinglist'); ?></h2>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>

						<p><?php echo wp_kses_post(sprintf(__('The spam score is %s out of 10 for this email', 'wp-mailinglist'), $score)); ?></p>

						<?php if (is_wp_error($spamscore)) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> get_error_message()); ?></p>
						<?php elseif (empty($spamscore -> success) || $spamscore -> success == false) : ?>
							<p class="newsletters_error"><?php echo esc_html( $spamscore -> message); ?></p>
						<?php else : ?>
							<h3><?php esc_html_e('Report', 'wp-mailinglist'); ?></h3>
							<p><pre><?php echo wp_kses_post($spamscore -> report); ?></pre></p>
						<?php endif; ?>

						<h3><?php esc_html_e('RAW Email', 'wp-mailinglist'); ?></h3>
						<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php esc_html_e('Toggle RAW Email', 'wp-mailinglist'); ?></a></p>
						<div id="rawemail-holder" style="display:none;">
							<p><pre><?php echo wp_kses_post(htmlspecialchars($newsletters_emailraw)); ?></pre></p>
						</div>
						<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
					</div>
				</div>
			</div>

			<?php

			$output = ob_get_clean();
			
			$data = array(
				'success'			=>	$spamscore -> success,
				'report'			=>	$spamscore -> report,
				'score'				=>	$score,
				'output'			=>	$output,
			);
			
			echo wp_json_encode($data);
			exit(); die();

			header("Content-Type: text/xml; charset=UTF-8");

	    	?>

	    	<result>
				<success><?php echo esc_html( $spamscore -> success); ?></success>
				<?php // phpcs:ignore ?>
				<report><![CDATA[<?php echo nl2br($spamscore -> report); ?>]]></report>
				<score><?php echo esc_html( $score); ?></score>
				<output><![CDATA[<?php echo wp_kses_post($output); ?>]]></output>
			</result>

	    	<?php

		    exit();
		    die();
	    }

	    function ajax_spamscorerunner() {
		    
		    check_ajax_referer('spamscorerunner', 'security');
		    
		    if (current_user_can('newsletters_send')) {

		    	global $Db, $Html, $Subscriber, $newsletters_presend, $newsletters_emailraw;
		    	$newsletters_presend = true;
		    	$subscriber_id = $Subscriber -> admin_subscriber_id();
		    	$subscriber = $Subscriber -> get($subscriber_id, false);
		    	$subject = wp_kses_post(sanitize_text_field(wp_unslash($_POST['subject'])));
		    	$content = wp_kses_post($_POST['content']);
		    	$history_id = sanitize_text_field(wp_unslash($_POST['ishistory']));
				$message = $this -> render_email('send', array('message' => $content, 'subject' => $subject, 'subscriber' => $subscriber, 'history_id' => $history_id), false, true, true, sanitize_text_field(wp_unslash($_POST['theme_id'])));
				$eunique = $Html -> eunique($subscriber, $history_id);
				$this -> execute_mail($subscriber, false, $subject, $message, $history -> attachments, $history_id, $eunique, false);
				$spamscore = $this -> spam_score($subscriber -> email, "long");
	
				// Save the 'spamscore'
				if (!empty($spamscore -> score)) {
					$this -> History() -> save_field('spamscore', $spamscore -> score, array('id' => $history_id));
				}
	
				if (empty($spamscore -> success) || $spamscore -> success == false) {
					$score = 0;
				} else {
					$score = $spamscore -> score;
				}
	
				$output = "";
				ob_start();
	
				?>
	
				<p id="spamscore_report_link_holder" style="text-align:center; display:block;"><a href="#spamscore_report" onclick="jQuery.colorbox({inline:false, html:jQuery('#spamscore_report').html(), maxWidth:'80%', maxHeight:'80%'}); return false;"><?php esc_html_e('See Report', 'wp-mailinglist'); ?></a></p>
				<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_html( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . $score . '&security=' . wp_create_nonce('gauge'))); ?>"></iframe>
	
				<div style="display:none;">
					<div id="spamscore_report">
						<div class="wrap newsletters">
							<h2><?php esc_html_e('Spam Score Report', 'wp-mailinglist'); ?></h2>
							<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
	<?php // phpcs:ignore ?>
							<p><?php echo sprintf(__('The spam score is %s out of 10 for this email', 'wp-mailinglist'), $score); ?></p>
	
							<?php if (is_wp_error($spamscore)) : ?>
								<p class="newsletters_error"><?php echo esc_html( $spamscore -> get_error_message()); ?></p>
							<?php elseif (empty($spamscore -> success) || $spamscore -> success == false) : ?>
								<p class="newsletters_error"><?php echo esc_html( $spamscore -> message); ?></p>
							<?php else : ?>
								<h3><?php esc_html_e('Report', 'wp-mailinglist'); ?></h3>
								<p><pre><?php echo wp_kses_post($spamscore -> report); ?></pre></p>
							<?php endif; ?>
	
							<h3><?php esc_html_e('RAW Email', 'wp-mailinglist'); ?></h3>
							<p><a href="" onclick="jQuery('#rawemail-holder').toggle(); return false;" class="button button-secondary"><?php esc_html_e('Toggle RAW Email', 'wp-mailinglist'); ?></a></p>
							<div id="rawemail-holder" style="display:none;">
								<p><pre><?php echo wp_kses_post(htmlspecialchars($newsletters_emailraw)); ?></pre></p>
							</div>
							<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><?php esc_html_e('Close Report', 'wp-mailinglist'); ?></a></p>
						</div>
					</div>
				</div>
	
				<?php
	
				$output = ob_get_clean();
				$success = $spamscore -> success;
			} else {
				$success = false;
				$output = __('You do not have permission', 'wp-mailinglist');
			}

			header("Content-Type: text/xml; charset=UTF-8");

	    	?>

	    	<result>
				<success><?php echo wp_kses_post($success); ?></success>
				<?php // phpcs:ignore ?>
				<report><![CDATA[<?php echo nl2br($spamscore -> report); ?>]]></report>
				<score><?php echo esc_html( $score); ?></score>
				<output><![CDATA[<?php echo wp_kses_post($output); ?>]]></output>
			</result>

	    	<?php

		    exit();
		    die();
	    }

	    function spam_score($email = null, $options = "long") {
			$data = array("email" => $email, "options" => $options);
			$data_string = wp_json_encode($data);

			$header = array(
				'Accept: application/json',
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($data_string),
			);

			$url = 'https://spamcheck.postmarkapp.com/filter';

			$args = array(
				'method'			=>	"POST",
				'body'				=>	$data,
				'headers'			=>	$header,
				'timeout'			=>	120,
			);

			$response = wp_remote_request($url, $args);

			if (!is_wp_error($response)) {
				return json_decode($response['body']);
			} else {
				return $response;
			}
		}

	    function ajax_gauge() {
		    
		    check_ajax_referer('gauge', 'security');
		    
		    if (current_user_can('newsletters_send')) {
		    	$value = (empty($_REQUEST['value'])) ? 0 :  sanitize_text_field(wp_unslash($_REQUEST['value']));
		    	
		    	wp_enqueue_script('raphael', $this -> render_url('js/raphael.js', 'admin', false), array('jquery'), false, false);
		    	wp_enqueue_script('justgage', $this -> render_url('js/justgage.js', 'admin', false), array('jquery'), false, false);
	
			    ?>
	
			    <html>
				    <head>
					    <meta charset="utf-8" />
					    
					    <?php wp_head(); ?>
				    </head>
			    	<body style="margin:0; padding:0;">					    
					    <div id="gauge"></div>
	
					    <script>
						document.addEventListener("DOMContentLoaded", function(event) {
							var g = new JustGage({
								id: "gauge",
								value: <?php echo esc_js($value); ?>,
								min: 0,
								max: 10,
								// phpcs:ignore
								title: "<?php echo ($value >= 5) ? esc_html_e('This is spam!', 'wp-mailinglist') : esc_html_e('This is safe!', 'wp-mailinglist'); ?>",
								label: "<?php esc_html_e('Spam Score', 'wp-mailinglist'); ?>",
								levelColorsGradient: false
							});
						});
						</script>
			    	</body>
			    </html>
	
			    <?php
			} else {esc_html_e('You do not have permission', 'wp-mailinglist');
			}

		    exit();
		    die();
	    }
	    
	    function ajax_history_download() {
		    global $Db, $Subscriber;
		    
		    check_ajax_referer('history_download', 'security');
		    
		    if (!current_user_can('newsletters_history')) {
			    wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
		    }
				
			$id = sanitize_text_field(wp_unslash($_GET['id']));
			if (!empty($id)) {			
				$email = $this -> History() -> find(array('id' => $id));
	
				$subscriber_id = $Subscriber -> admin_subscriber_id();
				$subscriber = $Subscriber -> get($subscriber_id);
	
				if (!empty($email -> post_id)) {
					if ($thepost = get_post($email -> post_id)) {
						global $post, $shortcode_post;
						$post = $thepost;
						$shortcode_post = $thepost;
					}
				}
	
				$message = $email -> message;
				$content = $this -> render_email('send', array('message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $id), false, true, true, $email -> theme_id, true);
				
				$output = $content;
	
				$output = "";
				ob_start();
				echo do_shortcode(wp_unslash($content));
				$output = ob_get_clean();
	
				ob_start();
				echo $this -> process_set_variables($subscriber, null, $output, esc_html($email -> id));
				$output = ob_get_clean();
	
				if (!empty($email -> format) && $email -> format == "text") {
					$output = wpautop($output);
				}
				
				$output = $this -> inlinestyles($output);
	
				if (ini_get('zlib.output_compression')) {
					ini_set('zlib.output_compression', 'Off');
				}

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: public", false);
				header("Content-Description: Download Newsletter");
				header("Content-Type: text/html");
				header("Accept-Ranges: bytes");
				header("Content-Disposition: attachment; filename=\"" . esc_html($email -> subject) . ".html\";");
				header("Content-Transfer-Encoding: binary");
				 // phpcs:ignore
				header("Content-Length: " . strlen($output));

				// phpcs:ignore
				print($output);
				exit();
				die();
			}
	    }

		function ajax_historyiframe($returnoutput = false, $text = false, $history_id = false, $ajax = true) {
			
			if (!empty($ajax)) {
				check_ajax_referer('historyiframe', 'security');
				
				if (!current_user_can('newsletters_send')) {
					wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
				}
			}
			
			global $Db, $Subscriber;
			
			if (empty($text)) {				
				if (!empty($_GET['text'])) {
					$text = true;
				}
			}
			
			$id = (empty($history_id)) ? sanitize_text_field(wp_unslash($_GET['id'])) : $history_id;
			$email = $this -> History() -> find(array('id' => $id));

			$subscriber_id = $Subscriber -> admin_subscriber_id();
			$subscriber = $Subscriber -> get($subscriber_id);

			if (!empty($email -> post_id)) {
				if ($thepost = get_post($email -> post_id)) {
					global $post, $shortcode_post;
					$post = $thepost;
					$shortcode_post = $thepost;
				}
			}

			$message = $email -> message;
			$content = $this -> render_email('send', array('message' => $message, 'subject' => $email -> subject, 'subscriber' => $subscriber, 'history_id' => $id), false, true, true, $email -> theme_id, true);
			
			$output = $content;

			$output = "";
			ob_start();
			echo do_shortcode(wp_unslash($content));
			$output = ob_get_clean();

			ob_start();
            //echo $this -> process_set_variables($subscriber, $user, $output, esc_html($email -> id));

            //below modified by Mohsen. Above commented line was original
            $user = null;
			echo $this -> process_set_variables($subscriber, $user, $output, esc_html($email -> id));
			$output = ob_get_clean();

			if (!empty($email -> format) && $email -> format == "text") {
				$output = wpautop($output);
			}
			
			$output = $this -> inlinestyles($output);
			
			// do we want the TEXT version?
			if (!empty($text)) {				
				global $wpml_textmessage;
				$altbody = $wpml_textmessage;
				
				if (!empty($email -> text)) {					
					$altbody = $email -> text;
					$altbody = do_shortcode($altbody);
					$altbody = $this -> process_set_variables($subscriber, $user, wp_unslash($altbody), $id, $eunique);
					$altbody = apply_filters('newsletters_execute_mail_textmessage', $altbody);
					$wpml_textmessage = $altbody;
				}
					
				if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
					if (class_exists('DOMDocument')) {
						require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
						$html2text = new Html2Text();
						$altbody = $html2text -> convert($wpml_textmessage);
					}
				}
				
				$output = nl2br($altbody);
			}

			if ($returnoutput) {
				return $output;
			}
             // phpcs:ignore
			echo $output;
			exit();
			die();
		}

		function ajax_serialkey() {
			$errors = array();
			$success = false;
			
			check_ajax_referer('serialkey', 'security');

			if (current_user_can('newsletters_welcome')) {
				if (!empty($_GET['delete'])) {
					$this -> delete_option('serialkey');
					$errors[] = __('Serial key has been deleted.', 'wp-mailinglist');
				} else {
					if (!empty($_POST)) {
						if (empty($_REQUEST['serialkey'])) { $errors[] = __('Please fill in a serial key.', 'wp-mailinglist'); }
						else {
							$this -> update_option('serialkey', sanitize_text_field(wp_unslash($_REQUEST['serialkey'])));	//update the DB option
							$this -> delete_all_cache('all');
	
							if (!$this -> ci_serial_valid()) { $errors[] = __('Serial key is invalid, please try again.', 'wp-mailinglist'); }
							else {
								delete_transient($this -> pre . 'update_info');
								$success = true;
							}
						}
					}
				}
			} else {
				$success = false;
				$errors[] = __('You do not have permission', 'wp-mailinglist');
			}

			if (empty($_POST)) { ?><div id="<?php echo esc_html($this -> pre); ?>submitserial"><?php }
			$this -> render('submitserial', array('errors' => $errors, 'success' => $success), true, 'admin');
			if (empty($_POST)) { ?></div><?php }

			exit();
			die();
		}

		function ajax_managementcustomfields() {
			
			check_ajax_referer('managementcustomfields', 'security');
			
			// No current_user_can required, this is front-end
			
			global $wpdb, $Db, $Subscriber, $FieldsList;

			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;

				if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;
						}
					}

					$fields = $FieldsList -> fields_by_list($lists, "order", "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));
				} else {
					$errors[] = __('Subscriber could not be read.', 'wp-mailinglist');
				}
			}

			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields), true, 'default');
			$this -> render('js' . DS . 'management', false, true, 'default');

			exit();
			die();
		}

		function ajax_managementsavefields() {
			
			check_ajax_referer('managementsavefields', 'security');
			
			// No current_user_can required, this is front-end

			global $wpdb, $Db, $Field, $Subscriber, $FieldsList;

			$errors = array();
			$oldpost = map_deep(wp_unslash($_POST), 'sanitize_text_field');
			$success = false;
			$successmessage = "";

			if (!empty($_POST)) {
				$Db -> model = $Subscriber -> model;

				if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {
					
					do_action('newsletters_subscriber_management_savefields_before', $subscriber);
					
					$lists = array();
					if (!empty($subscriber -> subscriptions)) {
						foreach ($subscriber -> subscriptions as $subscription) {
							$lists[] = $subscription -> mailinglist -> id;
						}
					}

					$fields = $FieldsList -> fields_by_list($lists, 'order', "ASC", (($this -> get_option('managementallowemailchange') == "Y") ? true : false));

					$_POST = $oldpost;
					unset($_POST['action']);
					unset($_POST['subscriber_id']);

					if (!empty($_POST)) {
						$emailfield = $Field -> email_field();

						if (!empty($_POST['email'])) {
							if (!$Subscriber -> email_validate(sanitize_email(wp_unslash($_POST['email'])))) { $errors['email'] = esc_html($emailfield -> errormessage); }
							elseif ($_POST['email'] != $subscriber -> email && $Subscriber -> email_exists(sanitize_email(wp_unslash($_POST['email'])))) { $errors[] = __('Email address is already in use, try another.', 'wp-mailinglist'); }
						} else {
							$errors['email'] = esc_html($emailfield -> errormessage);
						}

						$_POST['list_id'] = $lists;

						$_POST = $data = $Field -> validate_optin($_POST, 'management');
						if (!empty($Field -> errors)) {
							$errors = array_merge($errors, $Field -> errors);
						}

						$data['password'] = false;
						$management_password = $this -> get_option('management_password');
						if (!empty($_POST['password1'])) {
							if (empty($_POST['password2'])) { $errors['password'] = __('Re-enter the first password again.', 'wp-mailinglist'); }
							elseif ($_POST['password1'] != $_POST['password2']) { $errors['password'] = __('Passwords do not match', 'wp-mailinglist'); }
							else {
								//password seems correct
								$password = sanitize_text_field(wp_unslash($_POST['password1']));
								$password_hashed = md5($password);
								$data['password'] = $password_hashed;
								
								do_action('newsletters_subscriber_management_password', $subscriber, $password);
							}
						}

						if (empty($errors)) {
							if (!empty($_POST['email']) && $_POST['email'] != $subscriber -> email) {
								$Db -> model = $Subscriber -> model;
								$Db -> save_field('email', sanitize_text_field(wp_unslash($_POST['email'])), array('id' => $subscriber -> id));
								
								do_action('newsletters_subscriber_management_email_change', $subscriber);
							}

							$data['justsubscribe'] = true;

							if ($Subscriber -> save($data, true, false, false, true)) {
								$success = true;
								$successmessage = __('Profile has been saved.', 'wp-mailinglist');

								$this -> delete_all_cache('all');
								$Db -> model = $Subscriber -> model;
								$subscriber = $Db -> find(array('id' => $subscriber -> id), false, false, true, true, false);
							} else {
								$errors[] = __('Could not be saved', 'wp-mailinglist');
							}
						} else {
							$_POST[$this -> pre . 'errors'] = $errors;
						}
					} else {
						$errors[] = __('No data was posted.', 'wp-mailinglist');
					}
				} else {
					$errors[] = __('Subscriber could not be read.', 'wp-mailinglist');
				}
			} else {
				$errors[] = __('No data was posted.', 'wp-mailinglist');
			}

			$this -> render('management' . DS . 'customfields', array('subscriber' => $subscriber, 'fields' => $fields, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors), true, 'default');

			exit();
			die();
		}

		function ajax_managementcurrentsubscriptions() {
			
			check_ajax_referer('managementcurrentsubscriptions', 'security');
			
			global $wpdb, $Db, $Subscriber;

			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;
				if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {					
					$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber), true, 'default');
				}
			}

			exit();
			die();
		}

		function ajax_managementnewsubscriptions() {
			
			check_ajax_referer('managementnewsubscriptions', 'security');
			
			global $wpdb, $Db, $Subscriber, $Mailinglist;
			$otherlists = array();

			if (!empty($_POST['subscriber_id'])) {
				$Db -> model = $Subscriber -> model;

				if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {
					$managementshowprivate = $this -> get_option('managementshowprivate');
					if ($mailinglists = $Mailinglist -> select(((!empty($managementshowprivate)) ? true : false))) {
						foreach ($mailinglists as $mkey => $mval) {
							$otherlists[$mkey] = $mkey;
						}

						if (!empty($subscriber -> subscriptions)) {
							foreach ($subscriber -> subscriptions as $subscription) {
								if (in_array($subscription -> mailinglist -> id, $otherlists)) {
									unset($otherlists[$subscription -> mailinglist -> id]);
								}
							}
						}

						$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'otherlists' => $otherlists), true, 'default');
					}
				}
			}

			exit();
			die();
		}

		function ajax_subscribercountdisplay() {			
			check_ajax_referer('subscribercountdisplay', 'security');
			
			if (!current_user_can('newsletters_subscribers')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			global $wpdb, $Subscriber;

			$subscribercount_query = get_option('newsletters_subscribercount');
			if (!empty($subscribercount_query)) {
				$total_subscribers = count($wpdb -> get_results($subscribercount_query));

				$perpage = 50;
				$start = (empty($_GET['start'])) ? 0 :  sanitize_text_field(wp_unslash($_GET['start']));
				$realstart = ($start + 1);
				$end = ($start + $perpage);
				$realend = ($end > $total_subscribers) ? $total_subscribers : $end;
				$prevstart = ($start - $perpage);
				$nextstart = ($end);

				$subscribercount_query .= " LIMIT " . esc_sql($start) . ", " . esc_sql($perpage) . "";

				if ($subscribers = $wpdb -> get_results($subscribercount_query)) {

					?>
<?php // phpcs:ignore ?>
					<p><?php echo sprintf(__('Showing %s - %s of %s subscribers', 'wp-mailinglist'), $realstart, $realend, $total_subscribers); ?></p>

					<table class="widefat">
						<tbody>
							<?php foreach ($subscribers as $subscriber) : ?>
								<?php $subscriber = $Subscriber -> get($subscriber -> id); ?>
								<tr class="<?php echo $class = (empty($class)) ? 'alternate' : false; ?>">
									<td>
										<?php echo esc_html( $subscriber -> email); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<p>
					<?php if ($start > 0) : ?>
						<a href="" class="button button-secondary" onclick="jQuery.colorbox({width:'80%', height:'80%', title:'<?php esc_html_e('Selected Subscribers', 'wp-mailinglist'); ?>', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&security=' . wp_create_nonce('subscribercountdisplay') . '&start=' . $prevstart)) ?>'}); return false;"><i class="fa fa-caret-left"></i> <?php esc_html_e('Previous Page', 'wp-mailinglist'); ?></a>
					<?php endif; ?>
					<?php if ($total_subscribers > $end) : ?>
						<a href="" class="button button-secondary" onclick="jQuery.colorbox({width:'80%', height:'80%', title:'<?php esc_html_e('Selected Subscribers', 'wp-mailinglist'); ?>', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&security=' . wp_create_nonce('subscribercountdisplay') . '&start=' . $nextstart)) ?>'}); return false;"><?php esc_html_e('Next Page', 'wp-mailinglist'); ?> <i class="fa fa-caret-right"></i></a>
					<?php endif; ?>
					</p>

					<p><a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-primary"><i class="fa fa-times"></i> <?php esc_html_e('Close', 'wp-mailinglist'); ?></a></p>

					<?php
				}
			}

			exit(); die();
		}

		function ajax_subscribercount($count = true) {
			
			check_ajax_referer('subscribercount', 'security');
			
			if (!current_user_can('newsletters_subscribers')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));	
			}

			$status = (empty($_POST['status'])) ? 'active' :  sanitize_text_field(wp_unslash($_POST['status']));

			global $wpdb, $Db, $Field, $Subscriber, $Mailinglist, $SubscribersList;
			$subscribercount = 0;

			if (!empty($_POST['groups'])) {
				foreach (map_deep(wp_unslash($_POST['groups']), 'sanitize_text_field') as $group_key => $group_id) {
					$Db -> model = $Mailinglist -> model;

					if ($lists = $Db -> find_all(array('group_id' => $group_id), array('id'))) {
						foreach ($lists as $list) {
							$_POST['mailinglists'][] = $list -> id;
						}
					}
				}
			}

			// Count the users based on roles
			$users_count = 0;
			if (!empty($_POST['roles'])) {
				if ($count_users = count_users()) {					
					foreach ($count_users['avail_roles'] as $role => $count) {						
						if (in_array($role, $_POST['roles'])) {							
							$users_count += (int) $count;
						}
					}
				}
			}
            $query = "";
			if (!empty($_POST['mailinglists'])) {
				$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id FROM " . $wpdb -> prefix . "" . $SubscribersList -> table . " LEFT JOIN
				" . $wpdb -> prefix . "" . $Subscriber -> table . " ON
				" . $wpdb -> prefix . "" . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . "" . $Subscriber -> table . ".id LEFT JOIN
				" . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = " . $wpdb -> prefix . $Mailinglist -> table . ".id LEFT JOIN
				" . $wpdb -> prefix . $this -> SubscribersOption() -> table . " ON " . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $this -> SubscribersOption() -> table . ".subscriber_id WHERE (";

				$m = 1;
				foreach (map_deep(wp_unslash($_POST['mailinglists']), 'sanitize_text_field') as $mailinglist_id) {
					$query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($mailinglist_id) . "'";

					if ($m < count($_POST['mailinglists'])) {
						$query .= " OR ";
					}

					$m++;
				}

				if (empty($status) || $status == "active") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
				} elseif ($status == "inactive") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'N'";
				} elseif ($status == "all") {
					$query .= ")";
				}

				$query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval
				OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')";
				
				// Check if "Filter by custom fields" was selected
				if (!empty($_POST['dofieldsconditions'])) {
					$fields = array_filter(map_deep(wp_unslash($_POST['fields']), 'sanitize_text_field'));
					$scopeall = (empty($_POST['fieldsconditionsscope']) || $_POST['fieldsconditionsscope'] == "all") ? true : false;
					$condquery = sanitize_text_field(wp_unslash($_POST['condquery']));
					$fieldsquery = $Subscriber -> get_segmented_query($fields, $scopeall, $condquery);
					$query .= $fieldsquery;
				}

				if (!empty($_POST['daterange']) && $_POST['daterange'] == "Y") {
					if (!empty($_POST['daterangefrom']) && !empty($_POST['daterangeto'])) {
						$daterangefrom = date_i18n("Y-m-d H:i:s", strtotime(sanitize_text_field(wp_unslash($_POST['daterangefrom']))));
						$daterangeto = date_i18n("Y-m-d H:i:s", strtotime(sanitize_text_field(wp_unslash($_POST['daterangeto']))));
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . esc_sql($daterangefrom) . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . esc_sql($daterangeto) . "')";
					}
				}
				
				if (!empty($_POST['countries'])) {
					if (!empty($_POST['selectedcountries']) && is_array($_POST['selectedcountries'])) {
						$countries = implode("', '", map_deep(wp_unslash($_POST['selectedcountries']), 'sanitize_text_field'));
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".country IN ('" . esc_sql($countries) . "'))";
					}
				}

				$query_hash = md5($query);
				if ($ob_subscribers = $this -> get_cache($query_hash)) {
					$subscribers = $ob_subscribers;
				} else {
					$subscribers = $wpdb -> get_results($query);
					$this -> set_cache($query_hash, $subscribers);
				}

				if (!empty($subscribers)) {
					$subscribercount = count($subscribers);
				}
			}

			if (!empty($users_count)) {
				$subscribercount += $users_count;
			}

			if (!empty($subscribercount)) {
				do_action('newsletters_admin_createnewsletter_subscribercount_result', $subscribercount);
				update_option('newsletters_subscribercount', $query);

				 // phpcs:ignore
				echo '<p><a href="" onclick="jQuery.colorbox({width:\'80%\', height:\'80%\', title:\'' . __('Selected Subscribers', 'wp-mailinglist') . '\', href:\'' . admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&security=' . wp_create_nonce('subscribercountdisplay')) . '\'}); return false;"><i class="fa fa-eye"></i> ' . $subscribercount . ' ' . __('emails total', 'wp-mailinglist') . '</a></p>';
			} else {
				echo 0;
			}

			exit();
			die();
		}
		
		function is_block_editor() {
			global $post;
			
			if (apply_filters('replace_editor', false, $post) !== true) {
				if (function_exists('use_block_editor_for_post')) {
					if (use_block_editor_for_post($post)) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function edit_form_top($post = null) {
			$custompostslug = $this -> get_option('custompostslug');
			if (!empty($post -> post_type) && $post -> post_type == $custompostslug) {
				$this -> render('edit-form-top', array('post' => $post), true, 'admin');
			}
		}
		
		function ajax_subscribercount_blockeditor($count = true) {
			
			check_ajax_referer('subscribercount_blockeditor', 'security');
			
			if (!current_user_can('newsletters_subscribers')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}
			
			ob_start();
			$data = map_deep(wp_unslash($_POST), 'sanitize_text_field');

			$status = (empty($data['newsletters_status'])) ? 'active' : $data['newsletters_status'];

			global $wpdb, $Db, $Field, $Subscriber, $Mailinglist, $SubscribersList;
			$subscribercount = 0;

			if (!empty($data['newsletters_groups'])) {
				foreach ($data['newsletters_groups'] as $group_key => $group_id) {
					$Db -> model = $Mailinglist -> model;

					if ($lists = $Db -> find_all(array('group_id' => $group_id), array('id'))) {
						foreach ($lists as $list) {
							$data['newsletters_mailinglists'][] = $list -> id;
						}
					}
				}
			}

			// Count the users based on roles
			$users_count = 0;
			if (!empty($data['newsletters_roles'])) {
				if ($count_users = count_users()) {					
					foreach ($count_users['avail_roles'] as $role => $count) {						
						if (in_array($role, $data['newsletters_roles'])) {							
							$users_count += (int) $count;
						}
					}
				}
			}
// phpcs:disable
			if (!empty($data['newsletters_mailinglists'])) {
				$query = "SELECT DISTINCT " . $wpdb -> prefix . $Subscriber -> table . ".id FROM " . $wpdb -> prefix . "" . $SubscribersList -> table . " LEFT JOIN
				" . $wpdb -> prefix . "" . $Subscriber -> table . " ON
				" . $wpdb -> prefix . "" . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . "" . $Subscriber -> table . ".id LEFT JOIN
				" . $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = " . $wpdb -> prefix . $Mailinglist -> table . ".id LEFT JOIN
				" . $wpdb -> prefix . $this -> SubscribersOption() -> table . " ON " . $wpdb -> prefix . $Subscriber -> table . ".id = " . $wpdb -> prefix . $this -> SubscribersOption() -> table . ".subscriber_id WHERE (";

				$m = 1;
				foreach ($data['newsletters_mailinglists'] as $mailinglist_id) {
					$query .= "" . $wpdb -> prefix . $SubscribersList -> table . ".list_id = '" . esc_sql($mailinglist_id) . "'";

					if ($m < count($data['newsletters_mailinglists'])) {
						$query .= " OR ";
					}

					$m++;
				}

				if (empty($status) || $status == "active") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'Y'";
				} elseif ($status == "inactive") {
					$query .= ") AND " . $wpdb -> prefix . $SubscribersList -> table . ".active = 'N'";
				} elseif ($status == "all") {
					$query .= ")";
				}

				$query .= " AND (" . $wpdb -> prefix . $SubscribersList -> table . ".paid_sent < " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval
				OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval IS NULL OR " . $wpdb -> prefix . $Mailinglist -> table . ".maxperinterval = '')";
				
				// Check if "Filter by custom fields" was selected
				if (!empty($data['newsletters_dofieldsconditions'])) {
					$fields = array_filter($data['newsletters_fields']);
					$scopeall = (empty($data['newsletters_fieldsconditionsscope']) || $data['newsletters_fieldsconditionsscope'] == "all") ? true : false;
					$condquery = $data['newsletters_condquery'];
					$fieldsquery = $Subscriber -> get_segmented_query($fields, $scopeall, $condquery);
					$query .= $fieldsquery;
				}

				if (!empty($data['newsletters_daterange']) && $data['newsletters_daterange'] == "Y") {
					if (!empty($data['newsletters_daterangefrom']) && !empty($data['newsletters_daterangeto'])) {
						$daterangefrom = date_i18n("Y-m-d H:i:s", strtotime($data['newsletters_daterangefrom']));
						$daterangeto = date_i18n("Y-m-d H:i:s", strtotime($data['newsletters_daterangeto']));
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".created >= '" . $daterangefrom . "' AND " . $wpdb -> prefix . $Subscriber -> table . ".created <= '" . $daterangeto . "')";
					}
				}
				
				if (!empty($data['newsletters_countries'])) {
					if (!empty($data['newsletters_selectedcountries']) && is_array($data['newsletters_selectedcountries'])) {
						$countries = implode("', '", $data['newsletters_selectedcountries']);
						$query .= " AND (" . $wpdb -> prefix . $Subscriber -> table . ".country IN ('" . $countries . "'))";
					}
				}

				$query_hash = md5($query);
				if ($ob_subscribers = $this -> get_cache($query_hash)) {
					$subscribers = $ob_subscribers;
				} else {
					$subscribers = $wpdb -> get_results($query);
					$this -> set_cache($query_hash, $subscribers);
				}

				if (!empty($subscribers)) {
					$subscribercount = count($subscribers);
				}
			}

			if (!empty($users_count)) {
				$subscribercount += $users_count;
			}

			if (!empty($subscribercount)) {
				do_action('newsletters_admin_createnewsletter_subscribercount_result', $subscribercount);
				update_option('newsletters_subscribercount', $query);
				
				$data = array(
					'success' 		=>	true,
					'count'			=>	$subscribercount,
					'message'		=>	'<p><a href="" onclick="jQuery.colorbox({width:\'80%\', height:\'80%\', title:\'' . __('Selected Subscribers', 'wp-mailinglist') . '\', href:\'' . admin_url('admin-ajax.php?action=newsletters_subscribercountdisplay&security=' . wp_create_nonce('subscribercountdisplay')) . '\'}); return false;"><i class="fa fa-eye"></i> ' . $subscribercount . ' ' . __('emails total', 'wp-mailinglist') . '</a></p>',
				);
			} else {
				$data = array(
					'success'		=>	false,
					'count'			=>	0,
					'message'		=>	'<p><i class="fa fa-exclamation-triangle fa-fw"></i> ' . __('No subscribers to send to', 'wp-mailinglist') . '</p>',
				);
			}
			
			$process = ob_get_clean();				
			$data['process'] = $process;		
			echo wp_json_encode($data);

			exit();
			die();
		}

		function ajax_managementsubscribe() {
			
			check_ajax_referer('managementsubscribe', 'security');
			
			global $wpdb, $Db, $Authnews, $Subscriber, $Mailinglist;

			$errors = array();
			$success = false;
			$successmessage = "";
			$otherlists = array();

			if ($mailinglists = $Mailinglist -> select()) {
				foreach ($mailinglists as $mailinglist_id => $mailinglist_title) {
					$otherlists[] = $mailinglist_id;
				}
			}

			if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id'])) {
				$Db -> model = $Subscriber -> model;

				if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {

					$data = (array) $subscriber;

					$data['email'] = $subscriber -> email;
					$data['format'] = $subscriber -> format;
					$data['cookieauth'] = $Authnews -> read_cookie();
					$Db -> model = $Mailinglist -> model;

					if ($mailinglist = $Db -> find(array('id' => (int) $_POST['mailinglist_id']))) {
						$data['mailinglists'] = $data['list_id'] = array((int) $_POST['mailinglist_id']);

						if ($mailinglist -> paid == "Y") {
							$data['active'] = "N";
						} else {
							$data['active'] = "Y";
						}
					}

					do_action('newsletters_management_subscribe_before', $data);

					if ($Subscriber -> optin($data, false, false, false, false)) {
						$success = true;

						do_action('newsletters_management_subscribe_success', $data);

						$this -> delete_all_cache('all');
						$Db -> model = $Subscriber -> model;
						$subscriber = $Subscriber -> find(array('id' => $subscriber -> id), false, false, true, true, false);

						if ($mailinglist -> paid == "Y") {
							$successmessage = __('Subscription successful, please click the "Pay Now" button under current subscriptions to make a payment and activate your subscription.', 'wp-mailinglist');
						} else {
							$successmessage = __('Subscription successful and activated.', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('Subscription was not successful.', 'wp-mailinglist');
					}

					// Other lists…
					$subscribedlists = $Subscriber -> mailinglists($subscriber -> id, false, false, false);
					$managementshowprivate = $this -> get_option('managementshowprivate');
					$alllists = $Mailinglist -> select(((!empty($managementshowprivate)) ? true : false));
					$otherlists = array();

					$otherlists = array();
					if (!empty($alllists)) {
						foreach ($alllists as $alist_id => $alist_title) {
							if (empty($subscribedlists) || (!empty($subscribedlists) && !in_array($alist_id, $subscribedlists))) {
								$otherlists[] = $alist_id;
							}
						}
					}
				} else {
					$errors[] = __('Subscriber cannot be read.', 'wp-mailinglist');
				}
			} else {
				$errors[] = __('No subscriber/mailing list data posted.', 'wp-mailinglist');
			}

			$this -> render('management' . DS . 'newsubscriptions', array('subscriber' => $subscriber, 'success' => $success, 'successmessage' => $successmessage, 'errors' => $errors, 'otherlists' => $otherlists), true, 'default');

			exit();
			die();
		}

		function ajax_managementactivate() {
			
			check_ajax_referer('managementactivate', 'security');
			
			$success = false;
			$successmessage = "";
			$errors = array();

			if (!empty($_POST)) {
				if (!empty($_POST['subscriber_id']) && !empty($_POST['mailinglist_id']) && !empty($_POST['activate'])) {
					global $wpdb, $Db, $Subscriber, $SubscribersList, $Html, $Authnews, $Mailinglist, $Unsubscribe;

					$Db -> model = $Subscriber -> model;
					if ($subscriber = $Db -> find(array('id' => (int) $_POST['subscriber_id']), false, false, true, true, false)) {						
						if ($subscriber -> id == (int) $_POST['subscriber_id']) {
							$Db -> model = $Mailinglist -> model;
							$query = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE `id` = '" . esc_sql((int)$_POST['mailinglist_id']) . "'";
							$mailinglist = $wpdb -> get_row($query);

							$paid = $mailinglist -> paid;
							$subscriber -> mailinglist_id = $mailinglist -> id;
							$Db -> model = $SubscribersList -> model;

							if ($_POST['activate'] == "N") {								
								if (empty($subscriber -> mandatory) || $subscriber -> mandatory != "Y") {									
									if ($Db -> delete_all(array('subscriber_id' => (int) $_POST['subscriber_id'], 'list_id' => (int) $_POST['mailinglist_id']))) {
										$Db -> model = $Unsubscribe -> model;
										$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => (int) $_POST['mailinglist_id'], 'comments' => map_deep(wp_unslash($_POST['comments']), 'sanitize_text_field'));
										$_POST[$this -> pre . 'comments'] = map_deep(wp_unslash($_POST['comments']), 'sanitize_text_field');
										$Db -> save($unsubscribe_data, true);

										$Db -> model = $this -> Autoresponderemail() -> model;
										$Db -> delete_all(array('subscriber_id' => (int) $_POST['subscriber_id'], 'list_id' => (int) $_POST['mailinglist_id']));

										//send the administrator a notice
										$this -> admin_unsubscription_notification($subscriber, (int) $_POST['mailinglist_id']);
										$this -> user_unsubscription_notification($subscriber, array((int) $_POST['mailinglist_id']));

										//Should the subscriber be deleted?
										$deleted = false;
										if ($this -> get_option('unsubscribedelete') == "Y") {
											$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists
											if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {
												$Db -> model = $Subscriber -> model;
												$Db -> delete($subscriber -> id);
												$deleted = true;
											}
										}

										$this -> delete_all_cache('all');

										if (!empty($deleted) || $deleted == true) {
											$message = __('You were deleted since no subscriptions remained but you can resubscribe at any time.', 'wp-mailinglist');

											$afterdeleteurl = $Html -> retainquery('updated=1&success=' . $message, $this -> get_managementpost(true));
											$this -> redirect($afterdeleteurl, 'success', false, true);
										} else {
											$success = true;
											$successmessage = __('Subscription has been removed.', 'wp-mailinglist');
											$subscriber = $Authnews -> logged_in($subscriber -> id);
										}
									} else {
										$errors[] = __('Subscription could not be removed.', 'wp-mailinglist');
									}
								} else {
									$errors[] = __('You are a mandatory subscriber and cannot unsubscribe', 'wp-mailinglist');
								}
							} else {
								if (false && $this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {
									$success = true;
									$successmessage = __('A confirmation email has been sent through to your email address.', 'wp-mailinglist');
									$this -> subscription_confirm($subscriber);
								} else {
									$Db -> model = $SubscribersList -> model;

									if ($Db -> save_field('active', "Y", array('subscriber_id' => (int) $_POST['subscriber_id'], 'list_id' => (int) $_POST['mailinglist_id']))) {
										$success = true;
										$successmessage = __('Subscription has been activated.', 'wp-mailinglist');
										$this -> delete_all_cache('all');
										$subscriber = $Authnews -> logged_in($subscriber -> id);
									} else {
										$errors[] = __('Subscription could not be activated.', 'wp-mailinglist');
									}
								}
							}
						} else {
							$errors[] = __('You are logged in as a different subscriber.', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('You are not currently logged in.', 'wp-mailinglist');
					}
				} else {
					$errors[] = __('No subscriber/mailing list data posted.', 'wp-mailinglist');
				}
			} else {
				$errors[] = __('No data was posted.', 'wp-mailinglist');
			}

			$this -> render('management' . DS . 'currentsubscriptions', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success, 'successmessage' => $successmessage), true, 'default');

			exit();
			die();
		}
		
		function autoresponders_form_send($subscriber = null, $form = null) {
			global $wpdb, $Db, $HistoriesAttachment, $Subscriber, $SubscribersList, $Html, $Email;

			do_action('newsletters_autoresponders_form_send', $subscriber, $form);
			
			if (!empty($subscriber) && !empty($form)) {
				$subscriber_id = $subscriber -> id;
				if ($autorespondersforms = $this -> AutorespondersForm() -> find_all(array('form_id' => $form -> id))) {
					foreach ($autorespondersforms as $autorespondersform) {
						if ($autoresponder = $this -> Autoresponder() -> find(array('id' => $autorespondersform -> autoresponder_id, 'sendauto' => 1))) {
							if (!empty($autoresponder -> status) && $autoresponder -> status == "active") {
								//Send the 0 delay autoresponders right now
								if (!defined('NEWSLETTERS_IMPORTING') && (empty($autoresponder -> delay) || $autoresponder -> delay <= 0)) {
									$Db -> model = $SubscribersList -> model;
									$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'form_id' => $form -> id));
									
									if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {
										$Db -> model = $this -> Autoresponderemail() -> model;
										if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id)))) {
											$history = $this -> History() -> find(array('id' => $autoresponder -> history_id));
											$history -> attachments = array();
											$attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";
	
											if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
												foreach ($attachments as $attachment) {
													$history -> attachments[] = array(
														'id'					=>	$attachment -> id,
														'title'					=>	$attachment -> title,
														'filename'				=>	$attachment -> filename,
													);
												}
											}
	
											$eunique = $Html -> eunique($subscriber, $history -> id);
	
											$autoresponderemail_data = array(
												'autoresponder_id'				=>	$autoresponder -> id,
												'form_id'						=>	$form -> id,
												'subscriber_id'					=>	$subscriber -> id,
												'senddate'						=>	date_i18n("Y-m-d H:i:s", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval)),
												'status'						=>	'unsent',
											);
	
											$this -> Autoresponderemail() -> save($autoresponderemail_data, true);
											global $wpdb;
											$ae_id = $wpdb -> insert_id;
	
											$Db -> model = $Email -> model;
											$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);
	
											if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {
												$this -> Autoresponderemail() -> save_field('status', "sent", array('id' => $ae_id));
												$this -> History() -> save_field('sent', 1, array('id' => $history -> id));
											}
										}
									}
								//Save the 1+ delay autoresponders to send later
								} else {																		
									$Db -> model = $this -> Autoresponderemail() -> model;
									if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || !$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id))) {
										if (empty($autoresponder -> delay) || empty($autoresponder -> delayinterval)) {												
											$senddate = date_i18n("Y-m-d H:i:s");
										} else {												
											//$senddate = date_i18n("Y-m-d H:i:s", strtotime($subscriber -> created . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval));
											$senddate = date_i18n("Y-m-d H:i:s", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval));
										}

										$autoresponderemail_data = array(
											'autoresponder_id'				=>	$autoresponder -> id,
											'form_id'						=>	$form -> id,
											'subscriber_id'					=>	$subscriber -> id,
											'status'						=>	'unsent',
											'senddate'						=>	$senddate,
										);
										
										$this -> Autoresponderemail() -> save($autoresponderemail_data, true);
									}
								}
							}
						}
					}
				}
			}
		}

		function autoresponders_send($subscriber = null, $mailinglist = null) {
			global $wpdb, $Db, $HistoriesAttachment, $Subscriber, $SubscribersList, $Html, $Email;

			do_action('newsletters_autoresponders_send', $subscriber, $mailinglist);

			if (!empty($subscriber) && !empty($mailinglist)) {
				$subscriber_id = $subscriber -> id;
				if ($autoresponserslists = $this -> AutorespondersList() -> find_all(array('list_id' => $mailinglist -> id))) {
					foreach ($autoresponserslists as $al) {
						$Db -> model = $this -> Autoresponder() -> model;
						if ($autoresponder = $Db -> find(array('id' => $al -> autoresponder_id, 'sendauto' => 1))) {							
							if (!empty($autoresponder -> status) && $autoresponder -> status == "active") {
								//Send the 0 delay autoresponders right now
								if (!defined('NEWSLETTERS_IMPORTING') && (empty($autoresponder -> delay) || $autoresponder -> delay <= 0)) {									
									$Db -> model = $SubscribersList -> model;
									$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));
									if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {
										$Db -> model = $this -> Autoresponderemail() -> model;
										if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || (!$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id)))) {
											$history = $this -> History() -> find(array('id' => $autoresponder -> history_id));
											$history -> attachments = array();
											$attachmentsquery = "SELECT id, title, filename FROM " . $wpdb -> prefix . $HistoriesAttachment -> table . " WHERE history_id = '" . $history -> id . "'";

											if ($attachments =  $wpdb -> get_results($attachmentsquery)) {
												foreach ($attachments as $attachment) {
													$history -> attachments[] = array(
														'id'					=>	$attachment -> id,
														'title'					=>	$attachment -> title,
														'filename'				=>	$attachment -> filename,
													);
												}
											}

											$subscriber -> mailinglist_id = $mailinglist -> id;
											$eunique = $Html -> eunique($subscriber, $history -> id);

											$autoresponderemail_data = array(
												'autoresponder_id'				=>	$autoresponder -> id,
												'list_id'						=>	$mailinglist -> id,
												'subscriber_id'					=>	$subscriber -> id,
												'senddate'						=>	date_i18n("Y-m-d H:i:s", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval)),
												'status'						=>	'unsent',
											);

											$this -> Autoresponderemail() -> save($autoresponderemail_data, true);
											global $wpdb;
											$ae_id = $wpdb -> insert_id;

											$Db -> model = $Email -> model;
											$message = $this -> render_email('send', array('message' => $history -> message, 'subject' => $history -> subject, 'subscriber' => $subscriber, 'history_id' => $history -> id, 'post_id' => $history -> post_id, 'eunique' => $eunique), false, $this -> htmltf($subscriber -> format), true, $history -> theme_id);

											if ($this -> execute_mail($subscriber, false, $history -> subject, $message, $history -> attachments, $history -> id, $eunique, true, "newsletter")) {
												$this -> Autoresponderemail() -> save_field('status', "sent", array('id' => $ae_id));
												$this -> History() -> save_field('sent', 1, array('id' => $history -> id));
											}
										}
									}
								//Save the 1+ delay autoresponders to send later
								} else {																											
									$Db -> model = $SubscribersList -> model;
									$subscriberslist = $Db -> find(array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id));
									if (!empty($subscriberslist -> active) && $subscriberslist -> active == "Y") {
										$Db -> model = $this -> Autoresponderemail() -> model;
										if ((!empty($autoresponder -> alwayssend) && $autoresponder -> alwayssend == "Y") || !$Db -> find(array('subscriber_id' => $subscriber -> id, 'autoresponder_id' => $autoresponder -> id))) {
											if (empty($autoresponder -> delay) || empty($autoresponder -> delayinterval)) {																								
												$senddate = date_i18n("Y-m-d H:i:s");
											} else {			
												$created_date = (!empty($subscriber -> created) && $subscriber -> created != "0000-00-00 00:00:00") ? $subscriber -> created : false;
																					
												$senddate = date_i18n("Y-m-d H:i:s", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval));
												//$senddate = date_i18n("Y-m-d H:i:s", strtotime($created_date . " +" . $autoresponder -> delay . " " . $autoresponder -> delayinterval));
											}

											$autoresponderemail_data = array(
												'autoresponder_id'				=>	$autoresponder -> id,
												'list_id'						=>	$mailinglist -> id,
												'subscriber_id'					=>	$subscriber -> id,
												'status'						=>	'unsent',
												'senddate'						=>	$senddate,
											);
											
											$this -> Autoresponderemail() -> save($autoresponderemail_data, true);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		function paidsubscription_form($subscriber = null, $mailinglist = null, $autosubmit = true, $target = "_self", $extend = false, $pmethod = "paypal") {
			global $Html;

			if (!empty($subscriber) && !empty($mailinglist)) {
				if ($pmethod == "2co") {
					$checkoutdata = array(
						'sid'					=>	$this -> get_option('tcovendorid'),
						'cart_order_id'			=>	$subscriber -> id . $mailinglist -> id,
						'total'					=>	$mailinglist -> price,
						'id_type'				=>	1,
						'c_prod'				=>	$mailinglist -> id . ',1',
						'c_name'				=>	esc_html($mailinglist -> title),
						'c_description'			=>	esc_html($mailinglist -> title),
						'c_price'				=>	$mailinglist -> price,
						'quantity'				=>	1,
						'return_url'			=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						'x_receipt_link_url'	=>	home_url() . '/?' . $this -> pre . 'method=twocheckout',
						$this -> pre . 'method'	=>	'twocheckout',
						'subscriber_id'			=>	$subscriber -> id,
						'subscriber_email'		=>	$subscriber -> email,
						'mailinglist_id'		=>	$mailinglist -> id,
						'fixed'					=>	'Y',
						'demo'					=>	$this -> get_option('tcodemo'),
						'currency_code'			=>	$this -> get_option('currency'),
						'email'					=>	$subscriber -> email,
					);

					if (!empty($extend)) {
						$checkoutdata['subscription_extend'] = 1;
					}

					$formid = '2co_paidsubscriptionform' . $mailinglist -> id;
					$this -> render('twocheckout-form', array('checkoutdata' => $checkoutdata, 'extend' => $extend, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				} elseif ($pmethod == "paypal") {
					$pp_return = ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once") ?
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true)) :
					$Html -> retainquery('method=paidsubscriptionsuccess', $this -> get_managementpost(true));

					$checkoutdata = array(
						'charset'							=>	get_option('blog_charset'),
						'return'							=>	$pp_return,
						'rm'								=>	2,
						'notify_url'						=>	home_url() . '/?' . $this -> pre . 'method=paypal',
						'cbt'								=>	__('Click here to complete your order', 'wp-mailinglist'),
						'currency_code'						=>	$this -> get_option('currency'),
						'business'							=>	$this -> get_option('paypalemail'),
						'item_name'							=>	esc_html($mailinglist -> title),
						'item_number'						=>	$mailinglist -> id,
						'custom'							=>	array(
							'subscriber_id'			=>	$subscriber -> id,
							'mailinglist_id'		=>	$mailinglist -> id,
						),
						'no_shipping'						=>	1,
						'no_note'							=>	1,
						'bn'								=>	"TribulantSoftware_SP",
					);

					if (!empty($extend)) {
						$checkoutdata['custom']['subscription_extend'] = 1;
					}

					//$checkoutdata['custom'] = urlencode(maybe_serialize($checkoutdata['custom']));
					$checkoutdata['custom'] = urlencode(wp_json_encode($checkoutdata['custom']));

					if ($this -> get_option('paypalsubscriptions') == "Y" && $mailinglist -> interval != "once" && $extend == false) {
						$checkoutdata['cmd'] = "_xclick-subscriptions";
						$checkoutdata['a3'] = number_format($mailinglist -> price, 2, '.', '');
						$checkoutdata['p3'] = $Html -> getpptd($mailinglist -> interval);
						$checkoutdata['t3'] = $Html -> getppt($mailinglist -> interval);
						$checkoutdata['src'] = 1;
						$checkoutdata['sra'] = 1;
					} else {
						$checkoutdata['cmd'] = "_xclick";
						$checkoutdata['amount'] = $mailinglist -> price;
						$checkoutdata['quantity'] = 1;
					}

					$formid = 'paypal_paidsubscriptionform' . $mailinglist -> id;
					$this -> render('paypal-form', array('checkoutdata' => $checkoutdata, 'extend' => $extend, 'autosubmit' => $autosubmit, 'formid' => $formid, 'target' => $target));
				}
			}
		}
		
		function process_unsubscribe($subscriber = null, $mailinglists = null, $history_id = null) {			
			global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList, $Authnews, $Unsubscribe,
			$newsletters_errors, $Field, $Html;
			
			$unsubscribeemails = $this -> get_option('unsubscribeemails');
			
			if (empty($mailinglists)) {
				$subscribedlists = $Subscriber -> mailinglists($subscriber -> id, false, false, false);	//all subscribed mailing lists
			} else {
				$subscribedlists = $Subscriber -> mailinglists($subscriber -> id, $mailinglists, false, false);
			}
			
			$this -> user_unsubscription_notification($subscriber, $mailinglists);

			foreach ($subscribedlists as $ukey => $unsubscribelist_id) {
				$Db -> model = $Mailinglist -> model;
				$mailinglist = $Db -> find(array('id' => $unsubscribelist_id));
				$subscriber -> mailinglist_id = $unsubscribelist_id;

				if (!empty($unsubscribeemails) && $unsubscribeemails == "multiple") {
					$this -> admin_unsubscription_notification($subscriber, $unsubscribelist_id);
				}

				if (!empty($subscriber -> id) && !empty($unsubscribelist_id)) {
					$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
					$Db -> model = $this -> Autoresponderemail() -> model;
					$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
					
					unset($subscribedlists[$ukey]);

					$Db -> model = $Unsubscribe -> model;
					$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => $unsubscribelist_id, 'history_id' => $history_id);
					$Db -> save($unsubscribe_data, true);
				}
			}

			if (!empty($unsubscribeemails) && $unsubscribeemails == "single") {
				$this -> admin_unsubscription_notification($subscriber, $unsubscribelists);
			}

			do_action('newsletters_subscriber_unsubscribe', $subscriber -> id, $data['unsubscribelists']);

			if ($this -> get_option('unsubscriberemoveallsubscriptions') == "Y") {
				foreach ($subscribedlists as $ukey => $subscribedlist_id) {
					if (!empty($subscriber -> id) && !empty($subscribedlist_id)) {
						$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $subscribedlist_id));
						
						unset($subscribedlists[$ukey]);
					}
				}
			} 

			//Should the subscriber be deleted?
			if ($this -> get_option('unsubscribedelete') == "Y") {								
				if (empty($subscribedlists)) {										
					$Db -> model = $Subscriber -> model;
					$Db -> delete($subscriber -> id);
					$deleted = true;
				}
			}


            //Should the WP User deleted on unsubscribe?
			if ($this -> get_option('unsubscribewpuserdeletebyuser') == "Y") {
                $useridfrommail = get_user_by( 'email', $subscriber -> email );
                $wpuserid = $useridfrommail->ID;
                require_once(ABSPATH.'wp-admin/includes/user.php' );
                wp_delete_user($wpuserid);
            }
			
			return true;
		}

		function sc_management($atts = array(), $content = null) {
			global $wpdb, $Db, $Mailinglist, $Subscriber, $SubscribersList, $Authnews, $Unsubscribe,
			$newsletters_errors, $Field, $Html;

			if (!defined('NEWSLETTERS_IS_MANAGEMENT')) {
				define('NEWSLETTERS_IS_MANAGEMENT', true);
			}

			$errors = array();

			$output = "";
			$defaults = array();
			extract(shortcode_atts($defaults, $atts));
			$emailfield = $Field -> email_field();

			ob_start();

			$method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
			$method = (!empty($method)) ? $method : false;

			switch ($method) {
				case 'paidsubscription'			:
					$subscriber_id = sanitize_text_field(wp_unslash($_REQUEST['subscriber_id']));
					$list_id = sanitize_text_field(wp_unslash($_REQUEST['list_id']));
					$extend = sanitize_text_field(wp_unslash($_REQUEST['extend']));

					if (!empty($subscriber_id)) {
						if (!empty($list_id)) {

							$subscriber = $Subscriber -> get($subscriber_id);
							$data = (array) $subscriber;
							$data['list_id'] = $list_id;
							$mailinglist = $Mailinglist -> get($list_id);
							$extend = (empty($extend)) ? false : true;

							$Field -> validate_optin($data, 'management');

							if (empty($Field -> errors)) {
								$this -> render('management' . DS . 'paidsubscription', array('subscriber' => $subscriber, 'mailinglist' => $mailinglist, 'extend' => $extend), true, 'default');
							} else {
								$url = $Html -> retainquery('updated=1&error=' . __('Some profile fields are required, please complete them.', 'wp-mailinglist') . '#profile', $this -> get_managementpost(true));
								$this -> redirect($url);
							}
						} else {
							$errors[] = __('No mailing list was specified', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('No subscriber was specified', 'wp-mailinglist');
					}

					break;
				case 'paidsubscriptionsuccess'	:
					$message = __('Thank you for your payment! Please allow a moment for the subscription to be activated.', 'wp-mailinglist');
					$this -> redirect($this -> get_managementpost(true), "success", $message);
					break;
				case 'subscribe'			:

					$errors = false;
					$success = false;
					$id = (int) sanitize_text_field(wp_unslash($_GET['id']));

					if (!empty($id)) {
						if (!empty($_GET['mailinglists'])) {
							$Db -> model = $Subscriber -> model;
							if ($subscriber = $Db -> find(array('id' => $id))) {

								$subscriber -> mailinglists = explode(",", esc_html($_GET['mailinglists']));
								$subscriber -> active = "Y";

								if ($Subscriber -> optin($subscriber, false, false, false)) {
									$success = true;

									$Authnews -> set_emailcookie($subscriber -> email, "+30 days");
									if (empty($subscriber -> cookieauth)) {
										$subscriberauth = $Authnews -> gen_subscriberauth();
										$Db -> model = $Subscriber -> model;
										$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
									} else {
										$subscriberauth = $subscriber -> cookieauth;
									}

									$Authnews -> set_cookie($subscriberauth, "+30 days", true);
								} else {
									$errors = $Subscriber -> errors;
								}
							} else {
								$errors[] = __('Subscriber cannot be read', 'wp-mailinglist');
							}
						} else {
							$errors[] = __('No list was specified', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('No subscriber was specified', 'wp-mailinglist');
					}

					$this -> render('management' . DS . 'subscribe', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success), true, 'default');
					break;
				case 'resubscribe'			:
					$errors = false;
					$success = false;

					if (!empty($_GET['email']) && (!empty($_GET['mailinglists']))) {

						$Db -> model = $Subscriber -> model;
						if (!$subscriber = $Db -> find(array('email' => sanitize_email(wp_unslash($_GET['email']))))) {
							$subscriber = array();
						}

						$subscriber = (array) $subscriber;

						$subscriber['email'] = sanitize_text_field(wp_unslash($_GET['email']));
						$subscriber['mailinglists'] = explode(",", sanitize_text_field(wp_unslash($_GET['mailinglists'])));
						$subscriber['active'] = "Y";

						if ($Subscriber -> optin($subscriber, false, false, false)) {
							$success = true;

							$Authnews -> set_emailcookie($subscriber -> email, "+30 days");
							if (empty($subscriber -> cookieauth)) {
								$subscriberauth = $Authnews -> gen_subscriberauth();
								$Db -> model = $Subscriber -> model;
								$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
							} else {
								$subscriberauth = $subscriber -> cookieauth;
							}

							$Authnews -> set_cookie($subscriberauth, "+30 days", true);
						} else {
							$errors = $Subscriber -> errors;
						}
					} else {
						$errors[] = __('Some data is missing, please try again', 'wp-mailinglist');
					}

					$this -> render('resubscribe', array('subscriber' => $subscriber, 'errors' => $errors, 'success' => $success), true, 'default');
					break;
				case 'unsubscribe'			:
					global $wpdb, $Html, $Authnews, $Db, $Subscriber, $Mailinglist, $SubscribersList;

					$data = map_deep(wp_unslash($_REQUEST), 'sanitize_text_field');
					$dorender = true;
					$error = false;
					$success = false;
					$deleted = false;
					$userfile = "-success";

					$history_id = $data[$this -> pre . 'history_id'];
					$history = $this -> History() -> find(array('id' => $history_id));
					$subscriber_id = $data[$this -> pre . 'subscriber_id'];

					if (!empty($subscriber_id) || !empty($data['user_id'])) {
						if (($mailinglists = explode(",", $data[$this -> pre . 'mailinglist_id'])) !== false) {
							//do nothing, it's good...
							$includeonly = (empty($mailinglists)) ? $history -> mailinglists : $mailinglists;
							$mailinglists = $Subscriber -> mailinglists($subscriber_id, $includeonly, false, false);
						} else {
							$mailinglists = false;
						}

						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							$subscriber_query = "SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE id = '" . $data[$this -> pre . 'subscriber_id'] . "'";

							$subscriber = $wpdb -> get_row($subscriber_query);

							if (!empty($subscriber)) {
								if ($subscriber -> authkey == $_GET['authkey']) {
									if (empty($subscriber -> mandatory) || $subscriber -> mandatory == "N") {
										/* Management Auth */
										if (empty($data['cookieauth'])) {
											$Authnews -> set_emailcookie($subscriber -> email);
											$subscriberauth = $Authnews -> gen_subscriberauth();
											$Db -> model = $Subscriber -> model;
											$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
											$Authnews -> set_cookie($subscriberauth);
										}

										$subscriber -> mailinglists = $mailinglists;

										if (empty($subscriber -> mailinglists)) {
											$errors[] = __('This email was not sent to any lists.', 'wp-mailinglist');
										}
									} else {
										$dorender = false;
										$errors[] = __('You are a mandatory subscriber and cannot unsubscribe', 'wp-mailinglist');
									}
								} else {
									$dorender = false;
									$errors[] = __('You are not authorised to use this link', 'wp-mailinglist');
								}
							} else {
								$errors[] = __('Your subscriber record cannot be read, please try again.', 'wp-mailinglist');
							}
						} elseif (!empty($data['user_id'])) {
							if ($user = $this -> userdata($data['user_id'])) {
								// all good
								$userfile = "-user";
							} else {
								$errors[] = __('User cannot be read', 'wp-mailinglist');
							}
						} else {
							$errors[] = __('No subscriber or user was specified', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('No subscriber ID was specified, please try again.', 'wp-mailinglist');
					}

					$clicktrack = $this -> get_option('clicktrack');
					if (!empty($clicktrack) && $clicktrack == "Y") {
						$click_data = array(
							'referer'			=>	"unsubscribe",
							'history_id'		=>	(int) $_GET[$this -> pre . 'history_id'],
							'user_id'			=>	(int) $_GET[$this -> pre . 'user_id'],
							'subscriber_id'		=>	(int) $_GET[$this -> pre . 'subscriber_id'],
							'device'			=>	$this -> get_device()
						);

						$this -> Click() -> save($click_data, true);
					}

					if (!empty($data['confirm']) || $this -> get_option('unsubscribeconfirmation') == "N") {
						$unsubscribeemails = $this -> get_option('unsubscribeemails');
						$unsubscribelists = $data['unsubscribelists'];

						if (!empty($data[$this -> pre . 'subscriber_id'])) {
							if ($this -> get_option('unsubscribeconfirmation') == "N") {
								$data['unsubscribelists'] = $mailinglists;
							}

							if (!empty($data['unsubscribelists'])) {
								$this -> user_unsubscription_notification($subscriber, $mailinglists);

								$subscribedlists = $Subscriber -> mailinglists($subscriber -> id);	//all subscribed mailing lists

								foreach ($data['unsubscribelists'] as $unsubscribelist_id) {
									$Db -> model = $Mailinglist -> model;
									$mailinglist = $Db -> find(array('id' => $unsubscribelist_id));
									$subscriber -> mailinglist_id = $unsubscribelist_id;

									if (!empty($unsubscribeemails) && $unsubscribeemails == "multiple") {
										$this -> admin_unsubscription_notification($subscriber, $unsubscribelist_id);
									}

									if (!empty($subscriber -> id) && !empty($unsubscribelist_id)) {
										$subscribedlists = array_diff($subscribedlists, array($unsubscribelist_id));
										$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));
										$Db -> model = $this -> Autoresponderemail() -> model;
										$Db -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $unsubscribelist_id));

										$Db -> model = $Unsubscribe -> model;
										$unsubscribe_data = array('email' => $subscriber -> email, 'mailinglist_id' => $unsubscribelist_id, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
										$Db -> save($unsubscribe_data, true);
									}
								}

								if (!empty($unsubscribeemails) && $unsubscribeemails == "single") {
									$this -> admin_unsubscription_notification($subscriber, $unsubscribelists);
								}

								do_action('newsletters_subscriber_unsubscribe', $subscriber -> id, $data['unsubscribelists']);

								if ($this -> get_option('unsubscriberemoveallsubscriptions') == "Y") {
									foreach ($subscribedlists as $subscribedlist_id) {
										if (!empty($subscriber -> id) && !empty($subscribedlist_id)) {
											$SubscribersList -> delete_all(array('subscriber_id' => $subscriber -> id, 'list_id' => $subscribedlist_id));
										}
									}
								}

								//Should the subscriber be deleted?
								if ($this -> get_option('unsubscribedelete') == "Y") {
									if (empty($subscribedlists) || !is_array($subscribedlists) || count($subscribedlists) <= 0) {
										$Db -> model = $Subscriber -> model;
										$Db -> delete($subscriber -> id);
										$deleted = true;
									}
								}

                                //Should the WP User deleted on unsubscribe?
                                if ($this -> get_option('unsubscribewpuserdeletebyuser') == "Y") {
                                    $useridfrommail = get_user_by( 'email', $subscriber -> email );
                                    $wpuserid = $useridfrommail->ID;
                                    require_once(ABSPATH.'wp-admin/includes/user.php' );
                                    wp_delete_user($wpuserid);
                                }


								$success = true;
							} else {
								$errors[] = __('You did not select any list(s) to unsubscribe from.', 'wp-mailinglist');
								$success = false;
							}
						} elseif (!empty($data['user_id'])) {
							$Db -> model = $Unsubscribe -> model;
							$unsubscribe_data = array('user_id' => $user -> ID, 'email' => $user -> user_email, 'mailinglist_id' => false, 'history_id' => $data[$this -> pre . 'history_id'], 'comments' => $data[$this -> pre . 'comments']);
							$Db -> save($unsubscribe_data, true);
							$success = true;
							$errors = false;
						} else {
							$errors[] = __('No subscriber or user was specified', 'wp-mailinglist');
						}
					}
					
					// Is the unsubscribe successful?
					if (!empty($success)) {
						$unsubscribe_redirect = $this -> get_option('unsubscribe_redirect');
						if (!empty($unsubscribe_redirect)) {
							$unsubscribe_redirect_url = $this -> get_option('unsubscribe_redirect_url');
							if (!empty($unsubscribe_redirect_url)) {
								$this -> redirect($unsubscribe_redirect_url);
							}
						}
					}

					$this -> render('unsubscribe' . $userfile, array('subscriber' => $subscriber, 'history' => $history, 'dorender' => $dorender, 'user' => $user, 'data' => $data, 'errors' => $errors, 'success' => $success, 'deleted' => $deleted), true, 'default');
					break;
				case 'delete'				:
				
					global $Authnews, $Db, $Subscriber;
					
					$subscriber = $Authnews -> logged_in();					
					$Db -> model = $Subscriber -> model;
					$Db -> delete($subscriber -> id);
					$this -> delete_all_cache();
					
					$this -> render('management' . DS . 'delete', false, true, 'default');
					
					break;
				case 'logout'				:
					global $wpmljavascript;
					$subscriberemailauth = $Authnews -> read_emailcookie();
					$subscriberauth = $Authnews -> read_cookie();

					$managementauthtype = $this -> get_option('managementauthtype');
					switch ($managementauthtype) {
						case 1					:
							$Authnews -> delete_cookie($Authnews -> cookiename, $subscriberauth);
							$Authnews -> delete_cookie($Authnews -> emailcookiename, $subscriberemailauth);
							break;
						case 2					:
							$this -> end_session();
							break;
						case 3					:
						default 				:
							$Authnews -> delete_cookie($Authnews -> cookiename, $subscriberauth);
							$Authnews -> delete_cookie($Authnews -> emailcookiename, $subscriberemailauth);
							$this -> end_session();
							break;
					}

					echo html_entity_decode(str_replace("\'", "'", str_replace('\n', '', esc_js($wpmljavascript))));
					$this -> render('management' . DS . 'logout-auth', false, true, 'default');
					break;
				case 'loginauth'			:
					if (empty($_GET['email'])) {
					    // phpcs:ignore
						$subscriberemailauth = $_POST['email'] = $Authnews -> read_emailcookie();
					} else {
						$subscriberemailauth = sanitize_text_field($_GET['email']);
					}

					$subscriberauth = sanitize_text_field($_GET['subscriberauth']);

					if (!empty($subscriberemailauth)) {
						if (!empty($subscriberauth)) {
							$Db -> model = $Subscriber -> model;							
							if ($subscriber = $Db -> find(array('email' => $subscriberemailauth, 'cookieauth' => $subscriberauth))) {
								global $wpmljavascript;
								$Authnews -> set_cookie($subscriber -> cookieauth);
								$Authnews -> set_emailcookie($subscriberemailauth);
							} else {
								$errors[] = __('Authentication failed, please try again.', 'wp-mailinglist');
							}
						} else {
							$errors[] = __('No authentication string passed, please click the link again.', 'wp-mailinglist');
						}
					} else {
						$errors[] = __('No email saved, please try again.', 'wp-mailinglist');
					}

					if (empty($errors)) {
						$this -> render('management' . DS . 'login-auth', array('subscriberauth' => $subscriberauth, 'subscriberemailauth' => $subscriberemailauth), true, 'default');
					} else {
						$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');
					}
					break;
				case 'login'				:
					$errors = array_merge($errors, $newsletters_errors);
					$this -> render('management' . DS . 'login', array('errors' => $errors), true, 'default');
					break;
				default						:
					if (!empty($_GET['subscriberauth'])) {
						$_COOKIE['subscriberauth'] = esc_html($_GET['subscriberauth']);
					}

					if ($subscriber = $Authnews -> logged_in()) {												
						if ($this -> get_option('subscriptions') == "Y") {
							$SubscribersList -> check_expirations(false, false, true, $subscriber -> id);
						}

						$subscriber = $Authnews -> logged_in();
						$this -> render('management' . DS . 'index', array('subscriber' => $subscriber), true, 'default');
					} else {
						$this -> render('management' . DS . 'login', false, true, 'default');
					}
					break;
			}

			global $wpmljavascript;
			if (!empty($wpmljavascript)) {
				echo wp_kses_post($wpmljavascript);
			}

			$output = ob_get_clean();
			global $Html;
			$output = $Html -> fragment_cache($output, 'this', 'sc_management', false);
			return $output;
		}

		function is_plugin_screen($screen = null) {
			if (!empty($_GET['page'])) {
				if (!empty($screen)) {
					if (in_array($_GET['page'], (array) $this -> sections -> {$screen})) {
						return true;
					}
				} else {
					if (in_array($_GET['page'], (array) $this -> sections)) {
						return true;
					}
				}
			}

			return false;
		}
		
		function get_menu_names($dofilter = true) {
			$menunames = array(
				'newsletters'					=>	__('Overview', 'wp-mailinglist'),
				'newsletters-settings'			=>	__('Configuration', 'wp-mailinglist'),
				'newsletters-forms'				=>	__('Subscribe Forms', 'wp-mailinglist'),
				'newsletters-send'				=>	__('Create Newsletter', 'wp-mailinglist'),
				'newsletters-history'			=>	__('Sent & Draft Emails', 'wp-mailinglist'),
				'newsletters-links'				=>	__('Links & Clicks', 'wp-mailinglist'),
				'newsletters-autoresponders'	=>	__('Autoresponders', 'wp-mailinglist'),
				'newsletters-lists'				=>	__('Mailing Lists', 'wp-mailinglist'),
				'newsletters-groups'			=>	__('Groups', 'wp-mailinglist'),
				'newsletters-subscribers'		=>	__('Subscribers', 'wp-mailinglist'),
				'newsletters-import'			=>	__('Import/Export Subscribers', 'wp-mailinglist'),
				'newsletters-fields'			=>	__('Custom Fields', 'wp-mailinglist'),
				'newsletters-themes'			=>	__('Themes/Templates', 'wp-mailinglist'),
				'newsletters-templates'			=>	__('Email Snippets', 'wp-mailinglist'),
				'newsletters-queue'				=>	__('Email Queue', 'wp-mailinglist'),
				'newsletters-orders'			=>	__('Subscription Orders', 'wp-mailinglist'),
				'newsletters-extensions'		=>	__('Extensions', 'wp-mailinglist'),
				'newsletters-updates'			=>	__('Updates', 'wp-mailinglist'),
				'newsletters-support'			=>	__('Support & Help', 'wp-mailinglist'),
				'newsletters-submitserial'		=>	__('Submit Serial', 'wp-mailinglist'),
				'newsletters-gdpr'				=>	__('GDPR Compliance', 'wp-mailinglist'),
			);
			
			if (!empty($dofilter)) {
				$menunames = apply_filters('newsletters_menu_names', $menunames);
			}
			
			return $menunames;
		}

		function plugins_loaded() {
			$this -> name = apply_filters('newsletters_plugin_name', $this -> name);
			
			$this -> initialize_classes();
			$this -> ci_initialize();
			$this -> theme_folder_functions();

			return;
		}

		function manage_users_columns($columns = array()) {
		    $columns['newsletters'] = esc_html($this -> name, 'wp-mailinglist');
		    return $columns;
		}

		function manage_users_custom_column($value = null, $column_name = null, $user_id = null) {
			switch ($column_name) {
				case 'newsletters'				:
					global $Db, $Email;

					$newsletters = 0;

					if (!empty($user_id)) {
						$Db -> model = $Email -> model;
						if ($emails_count = $Db -> count(array('user_id' => $user_id))) {
							$newsletters = $emails_count;
						}
					}

					return $newsletters;
					break;
			}

		    return $value;
		}

		function language_useordefault($content) {
			$text = $content;

			if (!empty($text)) {
				$current_language = $this -> language_current();				
				$language = (empty($current_language) || $current_language == "all") ? $this -> language_default() : $current_language;				
				$text = $this -> language_use($language, $content, false);
			}

			return $text;
		}

		function language_use($lang = null, $text = null, $show_available = false) {

			if (!$this -> language_isenabled($lang)) {
				return $text;
			}

			if (is_array($text) || is_object($text)) {
				// handle arrays recursively
				if (is_array($text)) {
					foreach($text as $key => $t) {
						$text[$key] = $this -> language_use($lang, $text[$key], $show_available);
					}
				} elseif (is_object($text)) {
					foreach($text as $key => $t) {
						$text -> {$key} = $this -> language_use($lang, $text -> {$key}, $show_available);
					}
				}

				return $text;
			}

			if(is_object($text) && get_class($text) == '__PHP_Incomplete_Class') {
				foreach(get_object_vars($text) as $key => $t) {
					$text->$key = $this -> language_use($lang,$text -> $key,$show_available);
				}
				return $text;
			}

			// prevent filtering weird data types and save some resources
			if(!is_string($text) || $text == '') {
				return $text;
			}

			// get content
			$content = $this -> language_split($text);

			if (!is_array($content)) {
				return $content;
			}

			// find available languages
			$available_languages = array();
			foreach($content as $language => $lang_text) {
				$lang_text = trim($lang_text);
				if(!empty($lang_text)) $available_languages[] = $language;
			}

			// if no languages available show full text
			if(sizeof($available_languages)==0) return $text;
			// if content is available show the content in the requested language
			if(!empty($content[$lang])) {
				return $content[$lang];
			}
			// content not available in requested language (bad!!) what now?
			if(!$show_available){
				// check if content is available in default language, if not return first language found. (prevent empty result)
				if($lang != $this -> language_default()) {
					$str = $this -> language_use($this -> language_default(), $text, $show_available);

					if ($q_config['show_displayed_language_prefix'])
						$str = "(". $this -> language_name($this -> language_default()) .") " . $str;
					return $str;
				}
				foreach($content as $language => $lang_text) {
					$lang_text = trim($lang_text);
					if (!empty($lang_text)) {
						$str = $lang_text;
						if ($q_config['show_displayed_language_prefix'])
							$str = "(". $this -> language_name($language) .") " . $str;
						return $str;
					}
				}
			}
			// display selection for available languages
			$available_languages = array_unique($available_languages);
			$language_list = "";
			if(preg_match('/%LANG:([^:]*):([^%]*)%/',$q_config['not_available'][$lang],$match)) {
				$normal_seperator = $match[1];
				$end_seperator = $match[2];
				// build available languages string backward
				$i = 0;
				foreach($available_languages as $language) {
					if($i==1) $language_list  = $end_seperator.$language_list;
					if($i>1) $language_list  = $normal_seperator.$language_list;
					$language_list = "<a href=\"". $this -> language_converturl('', $language)."\">". $this -> language_name($language) ."</a>".$language_list;
					$i++;
				}
			}
			return "<p>".preg_replace('/%LANG:([^:]*):([^%]*)%/', $language_list, $q_config['not_available'][$lang])."</p>";
		}

		function language_converturl($url = null, $language = null) {
			global $newsletters_languageplugin;

			if (!empty($url) && !empty($language)) {
				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
						$url = qtrans_convertURL($url, $language);
						break;
					case 'qtranslate-x'				:
						$url = qtranxf_convertURL($url, $language);
						break;
					case 'polylang'					:
						$url = add_query_arg(array('lang' => $language), $url);
						break;
					case 'wpglobus'					:
						if (class_exists('WPGlobus_Utils')) {
							$url = WPGlobus_Utils::localize_url($url, $language);
						}
						break;
					case 'wpml'						:
						$url = apply_filters('wpml_permalink', $url, $language); 
						break;
				}
			}

			return $url;
		}

		function language_default() {
			global $newsletters_languageplugin, $newsletters_languagedefault;
			$default = false;

			if (!empty($newsletters_languagedefault)) {
				return $newsletters_languagedefault;
			}

			switch ($newsletters_languageplugin) {
				case 'qtranslate'				:
				case 'qtranslate-x'				:
					global $q_config;
					$default = $q_config['default_language'];
					break;
				case 'polylang'					:
					if (function_exists('pll_default_language')) {
						$default = pll_default_language();
					}
					break;
				case 'wpglobus'					:
					if (class_exists('WPGlobus')) {
						$default = WPGlobus::Config() -> default_language;
					}
					break;
				case 'wpml'						:
					global $sitepress;
					if (method_exists($sitepress, 'get_default_language')) {
						$default = $sitepress -> get_default_language();
					}
					break;
			}

			$newsletters_languagedefault = $default;
			return $default;
		}

		function language_name($language = null) {
			$name = false;

			if (!empty($language)) {
				global $newsletters_languageplugin, ${'newsletters_languagename_' . $language};

				if (!empty(${'newsletters_languagename_' . $language})) {
					return ${'newsletters_languagename_' . $language};
				}

				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
					case 'qtranslate-x'				:
						global $q_config;
						$name = $q_config['language_name'][$language];
						break;
					case 'polylang'					:
						global $polylang;
						if ($pll_language = $polylang -> model -> get_language($language)) {
							$name = $pll_language -> name;
						}
						break;
					case 'wpglobus'					:
						if (class_exists('WPGlobus')) {
							$name = WPGlobus::Config() -> language_name[$language];
						}
						break;
					case 'wpml'						:
						if (function_exists('icl_get_languages')) {
							$languages = icl_get_languages();
							if (!empty($languages[$language]['translated_name'])) {
								$name = $languages[$language]['translated_name'];
							}
						}
						break;
				}
			}

			${'newsletters_languagename_' . $language} = $name;
			return $name;
		}

		function language_do() {
			global $newsletters_languageplugin;
			
			$result = false;

			if (empty($newsletters_languageplugin)) {
				if ($this -> is_plugin_active('qtranslate')) {
					$newsletters_languageplugin = "qtranslate";
					$result = true;
				} elseif ($this -> is_plugin_active('qtranslate-x')) {
					$newsletters_languageplugin = 'qtranslate-x';
					$result = true;
				} elseif ($this -> is_plugin_active('wpml')) {
					$newsletters_languageplugin = "wpml";

					if (!empty($_GET['lang']) && $_GET['lang'] == "all") {						
						$result = false;
					}

					$result = true;
				} elseif ($this -> is_plugin_active('polylang')) {
					$newsletters_languageplugin = "polylang";
					$result = true;
				} elseif ($this -> is_plugin_active('wpglobus')) {
					$newsletters_languageplugin = "wpglobus";
					$result = true;
				}
			} else {
				$result = true;
			}
			
			return apply_filters('newsletters_language_do', $result, $newsletters_languageplugin);
		}

		function language_set($language = null) {
			global $newsletters_languageplugin, $newsletters_languagecurrent;
			$this -> language_do();
			
			do_action('newsletters_language_set_before', $language, $newsletters_languageplugin);

			if (!empty($language) && !empty($newsletters_languageplugin)) {
				$newsletters_languagecurrent = $language;

				switch ($newsletters_languageplugin) {
					case 'qtranslate'					:
					case 'qtranslate-x'					:
						if (function_exists('qtranxf_set_language_cookie')) {
							qtranxf_set_language_cookie($language);
						}
						break;
					case 'polylang'						:
						global $polylang;
						if ($pll_language = $polylang -> model -> get_language($language)) {
							$polylang -> curlang = $pll_language;
						}
						break;
					case 'wpglobus'						:
						if (class_exists('WPGlobus')) {
							WPGlobus::Config() -> set_language($language);
						}
						break;
					case 'wpml'							:
						global $sitepress;
						if (method_exists($sitepress, 'switch_lang')) {
							$sitepress -> switch_lang($language, true);
						}
						break;
				}
				
				do_action('newsletters_language_set_success', $language, $newsletters_languageplugin);

				return true;
			}
			
			do_action('newsletters_language_set_failed', $language, $newsletters_languageplugin);

			return false;
		}

		function language_current() {
			global $newsletters_languageplugin, $newsletters_languagecurrent;
			$current = false;

			if (!empty($newsletters_languagecurrent)) {
				return $newsletters_languagecurrent;
			}

			switch ($newsletters_languageplugin) {
				case 'qtranslate'			:
					if (function_exists('qtrans_getLanguage')) {
						$current = qtrans_getLanguage();
					}
					break;
				case 'qtranslate-x'			:
					if (function_exists('qtranxf_getLanguage')) {
						$current = qtranxf_getLanguage();
					}
					break;
				case 'polylang'				:
					if (function_exists('pll_current_language') && function_exists('pll_default_language')) {
						$current = pll_current_language();
						
						if (empty($current)) {
							$current = pll_default_language();
						}
					}
					break;
				case 'wpglobus'				:
					if (class_exists('WPGlobus')) {
						$current = WPGlobus::Config() -> language;
					}
					break;
				case 'wpml'					:
					//$current = ICL_LANGUAGE_CODE;
					global $sitepress;
					$current = $sitepress -> get_current_language();
					break;
			}
			
			if (empty($current)) {
				$current = $this -> language_default();
			}

			$newsletters_languagecurrent = $current;
			return $current;
		}

		function language_flag($language = null) {
			global $newsletters_languageplugin, ${'newsletters_languageflag_' . $language};
			$flag = false;

			if (!empty(${'newsletters_languageflag_' . $language})) {
				return ${'newsletters_languageflag_' . $language};
			}

			switch ($newsletters_languageplugin) {
				case 'qtranslate'			:
				case 'qtranslate-x'			:
					global $q_config;
					$flag = '<img src="' . content_url() . '/' . $q_config['flag_location'] . '/' . $q_config['flag'][$language] . '" alt="' . $language . '" />';
					break;
				case 'polylang'				:
					global $polylang;
					$pll_language = $polylang -> model -> get_language($language);
					$flag = $pll_language -> flag;
					break;
				case 'wpglobus'				:
					if (class_exists('WPGlobus')) {
						$flag = '<img src="' . WPGlobus::Config() -> flags_url . WPGlobus::Config() -> flag[$language] . '" alt="' . $language . '" />';
					}
					break;
				case 'wpml'					:
					if (function_exists('icl_get_languages')) {
						$languages = icl_get_languages();
						$flag = '<img src="' . $languages[$language]['country_flag_url'] . '" alt="' . $language . '" />';
					}
					break;
			}

			${'newsletters_languageflag_' . $language} = $flag;
			return $flag;
		}

		function language_isenabled($language = null) {
			$enabled = false;

			if (!empty($language)) {
				global $newsletters_languageplugin, ${'newsletters_languageenabled_' . $language};

				if (!empty(${'newsletters_languageenabled_' . $language})) {
					return ${'newsletters_languageenabled_' . $language};
				}

				switch ($newsletters_languageplugin) {
					case 'qtranslate'				:
						$enabled = qtrans_isEnabled($language);
						break;
					case 'qtranslate-x'				:
						$enabled = qtranxf_isEnabled($language);
						break;
					case 'polylang'					:					
						global $polylang;
						if ($pll_language = $polylang -> model -> get_language($language)) {
							if (empty($pll_language -> active) || $pll_language -> active == true) {
								$enabled = true;
							}
						}
						break;
					case 'wpglobus'					:
						if (class_exists('WPGlobus_Utils')) {
							if (WPGlobus_Utils::is_enabled($language)) {
								$enabled = true;
							}
						}	
						break;
					case 'wpml'						:
						if (function_exists('icl_get_languages')) {
							$languages = icl_get_languages();
							if (!empty($languages[$language])) {
								$enabled = true;
							}
						}
						break;
				}
			}

			${'newsletters_languageenabled_' . $language} = $enabled;
			return $enabled;
		}

		function language_join($texts = array(), $tagTypeMap = array(), $strip_tags = false) {
			if(!is_array($texts)) $texts = $this -> language_split($texts, false);
			$split_regex = "#<!--more-->#ism";
			$max = 0;
			$text = "";
			$languages = $this -> language_getlanguages();

			foreach ($languages as $language) {
				$tagTypeMap[$language] = true;
			}

			foreach($languages as $language) {
				if (!empty($texts[$language])) {
					$texts[$language] = preg_split($split_regex, $texts[$language]);
					if(sizeof($texts[$language]) > $max) $max = sizeof($texts[$language]);
				}
			}

			for ($i = 0; $i < $max; $i++) {
				if($i>=1) {
					$text .= '<!--more-->';
				}
				foreach($languages as $language) {
					if (isset($texts[$language][$i]) && $texts[$language][$i] !== '') {
						if ($strip_tags) {
							$texts[$language][$i] = strip_tags($texts[$language][$i]);
						}

						if (empty($tagTypeMap[$language]))
							$text .= '<!--:'.$language.'-->'.$texts[$language][$i].'<!--:-->';
						else
							$text .= "[:{$language}]{$texts[$language][$i]}";
					}
				}
			}

			return $text;
		}

		function language_split($text, $quicktags = true, array $languageMap = NULL) {
			$array = false;

			if (!empty($text)) {
				//init vars
				$split_regex = "#(<!--[^-]*-->|\[:[a-z-]{2,10}\])#ism";
				$current_language = "";
				$result = array();

				$languages = $this -> language_getlanguages();
				foreach ($languages as $language) {
					$result[$language] = "";
				}

				// split text at all xml comments
				$blocks = preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

				foreach($blocks as $block) {
					# detect language tags
					if(preg_match("#^<!--:([a-z-]{2,10})-->$#ism", $block, $matches)) {
						if($this -> language_isenabled($matches[1])) {
							$current_language = $matches[1];
							$languageMap[$current_language] = false;
						} else {
							$current_language = "invalid";
						}
						continue;
					// detect quicktags
					} elseif($quicktags && preg_match("#^\[:([a-z-]{2,10})\]$#ism", $block, $matches)) {
						if($this -> language_isenabled($matches[1])) {
							$current_language = $matches[1];
							$languageMap[$current_language] = true;
						} else {
							$current_language = "invalid";
						}

						continue;
					} elseif(preg_match("#^<!--:-->$#ism", $block, $matches)) {
						$current_language = "";
						continue;
					} elseif(preg_match("#^<!--more-->$#ism", $block, $matches)) {
						foreach($languages as $language) {
							$result[$language] .= $block;
						}

						continue;
					}

					if($current_language == "") {
						foreach($languages as $language) {
							$result[$language] .= $block;
						}
					} elseif($current_language != "invalid") {
						$result[$current_language] .= $block;
					}
				}

				foreach($result as $lang => $lang_content) {
					$result[$lang] = str_replace('[:]', '', preg_replace("#(<!--more-->|<!--nextpage-->)+$#ism", "", $lang_content));
				}

				return $result;
			}

			return $array;
		}

		function language_getlanguages() {
			global $newsletters_languageplugin, $newsletters_languagelanguages;
			$languages = false;

			if (!empty($newsletters_languagelanguages)) {
				return $newsletters_languagelanguages;
			}

			switch ($newsletters_languageplugin) {
				case 'qtranslate'					:
					if (function_exists('qtrans_getSortedLanguages')) {
						$languages = qtrans_getSortedLanguages();
					}
					break;
				case 'qtranslate-x'					:
					if (function_exists('qtranxf_getSortedLanguages')) {
						$languages = qtranxf_getSortedLanguages();
					}
					break;
				case 'polylang'						:	
					global $polylang;	
					if (!empty($polylang -> model) && method_exists($polylang -> model, 'get_languages_list')) {		
						if ($pll_languages = $polylang -> model -> get_languages_list()) {
							foreach ($pll_languages as $lang) {
								$languages[] = $lang -> slug;
							}
						}
					}
					break;
				case 'wpglobus'						:
					if (class_exists('WPGlobus')) {
						$languages = WPGlobus::Config() -> enabled_languages;
					}
					break;
				case 'wpml'							:
					if (function_exists('icl_get_languages')) {
						$icl_languages = icl_get_languages();
						$languages = array();
						foreach ($icl_languages as $lang => $icl_language) {
							$languages[] = $lang;
						}
					}
					break;
			}
			
			$languages = apply_filters('newsletters_language_getlanguages', $languages);

			$newsletters_languagelanguages = $languages;
			return $languages;
		}
		
		function wp_cron_file_exists() {
			$result = false;
			$wp_cron = ABSPATH . 'wp-cron.php';
			
			if (file_exists($wp_cron)) {
				$result = true;
			} else {
				$this -> log_error(__('The wp-cron.php file does not exist, please check if it is deleted or renamed.', 'wp-mailinglist'));
			}
			
			return apply_filters('newsletters_wp_cron_file_exists', $result);
		}

		function paginate($model = null, $fields = '*', $sub = null, $conditions = false, $searchterm = null, $perpage = 10, $order = array('modified', "DESC"), $conditions_and = null) {
			global $wpdb, $Db, $Subscriber, $SubscribersList, $Mailinglist,
			${$model}, $Mailinglist, $Unsubscribe, $Bounce;

			$object = (!is_object(${$model})) ? $this -> {$model}() : ${$model};
			$conditions = apply_filters('newsletters_admin_paginate_' . strtolower($object -> model) . '_conditions', $conditions);
			$conditions_and = apply_filters('newsletters_admin_paginate_' . strtolower($object -> model) . '_conditions_and', $conditions_and);

			if (!empty($model)) {
				global $paginate;
				$paginate = $this -> vendor('paginate');
				$paginate -> plugin_name = $this -> plugin_name;
				$paginate -> model = $model;
				$paginate -> table = $wpdb -> prefix . $this -> pre . $object -> controller;
				$paginate -> sub = (empty($sub)) ? $object -> controller : $sub;
				$paginate -> fields = (empty($fields)) ? '*' : $fields;
				$paginate -> where = (empty($conditions)) ? false : $conditions;
				$paginate -> where_and = (empty($conditions_and)) ? false : $conditions_and;
				$paginate -> searchterm = (empty($searchterm)) ? false : $searchterm;
				$paginate -> perpage = $perpage;
				$paginate -> order = $order;

				$page = (empty($_GET[$this -> pre . 'page'])) ? 1 : esc_html($_GET[$this -> pre . 'page']);
				$data = $paginate -> start_paging($page);

				if (!empty($data)) {
					$newdata = array();
					$n = 0;

					foreach ($data as $record) {
						//$newdata[$n] = $this -> init_class($model, $record);
						$newdata[$n] = $record;

						switch ($model) {
							case 'Bounce'						:
							case 'Unsubscribe'					:
								$newdata[$n] = $this -> init_class($model, $record);
								break;
							case 'History'						:
								$newdata[$n] = $this -> init_class($this -> History() -> model, $record);
								break;
							case 'Subscriber'					:
								$Db -> model = $SubscribersList -> model;
								if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $record -> id))) {
									foreach ($subscriberslists as $sl) {
										$listquery = "SELECT * FROM " . $wpdb -> prefix . $Mailinglist -> table . " WHERE id = '" . $sl -> list_id . "' LIMIT 1";
										$list = $wpdb -> get_row($listquery);
										$newdata[$n] -> Mailinglist[] = $list;
									}
								}
								break;
							case 'SubscribersList'				:
								$Db -> model = $Subscriber -> model;

								if ($subscriber = $Db -> find(array('id' => $record -> subscriber_id))) {
									$newdata[$n] = $subscriber;

									foreach ($record as $rkey => $rval) {
										$newdata[$n] -> {$rkey} = $rval;
									}
								}
								break;
							case 'Autoresponder'				:
								/* Pending Emails */
								//$newdata[$n] -> pending = $this -> Autoresponderemail() -> count(array('autoresponder_id' => $record -> id, 'status' => "unsent"));

								/* Mailing Lists */
								$newdata[$n] -> mailinglists = array();

								if ($autoresponderslists = $this -> AutorespondersList() -> find_all(array('autoresponder_id' => $record -> id))) {
									foreach ($autoresponderslists as $autoresponderslist) {
										$Db -> model = $Mailinglist -> model;
										$newdata[$n] -> lists[] = $autoresponderslist -> list_id;
										$newdata[$n] -> mailinglists[] = $Db -> find(array('id' => $autoresponderslist -> list_id));
									}
								}
								break;
							case 'Autoresponderemail'			:
								/* Autoresponder */
								$Db -> model = $this -> Autoresponder() -> model;
								$newdata[$n] -> autoresponder = $Db -> find(array('id' => $record -> autoresponder_id), false, false, false, false);

								/* Subscriber */
								$Db -> model = $Subscriber -> model;
								$newdata[$n] -> subscriber = $Db -> find(array('id' => $record -> subscriber_id), false, false, false, false);
								break;
						}

						$n++;
					}

					$data = array();
					$data[$model] = (object) $newdata;
					$data['Paginate'] = $paginate;
				}

				return $data;
			}

			return false;
		}
		
		function admin_pointers() {
		    if ($this -> custom_admin_pointers_check()) {
		        $this -> add_action('admin_print_footer_scripts', 'custom_admin_pointers_footer');
		
		        wp_enqueue_script('wp-pointer');
		        wp_enqueue_style('wp-pointer');
		    }
		}
		
		function custom_admin_pointers_check() {
		    $admin_pointers = $this -> custom_admin_pointers();
		    
		    foreach ($admin_pointers as $pointer => $array) {
		        if (!empty($array['active'])) {
		            return true;
		        }
		    }
		}
		
		function custom_admin_pointers_footer() {
		    $admin_pointers = $this -> custom_admin_pointers();
		    
		    ?>
		    <script type="text/javascript">
		    (function($) {
		    	<?php
		        
		        foreach ($admin_pointers as $pointer => $array) {
		        	if (!empty($array['active'])) {
					
					?>
					
		            $('<?php echo $array['anchor_id']; ?>').pointer( {
		                content: '<?php echo $array['content']; ?>',
		                position: {
		                    edge: '<?php echo $array['edge']; ?>',
		                    align: '<?php echo $array['align']; ?>'
		                },
		                close: function() {
		                    $.post( ajaxurl, {
		                        pointer: '<?php echo $pointer; ?>',
		                        action: 'dismiss-wp-pointer'
		                    });
		                }
		            }).pointer('open');
		            
		            <?php
		         }
		      }
		      
		      ?>
		    })(jQuery);
		    </script>
		<?php
		}
		
		function custom_admin_pointers() {
			$dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
			$prefix = 'newsletters_admin_pointers_';
			
			$admin_pointers = array();
			
			$gdpr_pointer_content = '';
			$gdpr_pointer_content .= '<h3>' . __('Newsletters: GDPR', 'wp-mailinglist') . '</h3>';
			$gdpr_pointer_content .= '<p>' . sprintf(__('Newsletters: Are you GDPR compliant? Check the %s to make sure you comply.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> gdpr) . '">' . __('GDPR Requirements', 'wp-mailinglist') . '</a>') . '</p>';
			
			$admin_pointers[$prefix . 'gdpr'] = array(
				'content'				=>	$gdpr_pointer_content,
				'anchor_id'				=>	'#toplevel_page_newsletters',
				'edge'					=>	'left',
				'align'					=>	'middle',
				'active'				=>	(!in_array($prefix . 'gdpr', $dismissed)),
			);
			
			return $admin_pointers;
			
		}

		function ci_print_styles() {
			wp_enqueue_style('newsletters', $this -> render_url('css/wp-mailinglist.css', 'admin', false), null, $this -> version, "all");
			wp_enqueue_style('colorbox', plugins_url() . '/' . $this -> plugin_name . '/css/colorbox.css', false, $this -> version, "all");
		}

		function ci_print_scripts() {
			wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/wp-mailinglist.js', array('jquery'), '1.0', true);
			wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, true);
		}

		function print_scripts() {
			$this -> enqueue_scripts();
		}

		function print_styles() {
			$this -> enqueue_styles();
		}

		function enqueue_scripts() {
			global $wp_locale, $wp_scripts, $Html, $Db;
			$custompostslug = $this -> get_option('custompostslug');
			$page = sanitize_text_field(isset($_GET['page']) ? $_GET['page'] : "");

			//enqueue jQuery JS Library
			if (apply_filters('newsletters_enqueuescript_jquery', true)) { wp_enqueue_script('jquery'); }

			if (is_admin() && !defined('DOING_AJAX')) {
				$donotloadpages = array(
					'codestyling-localization/codestyling-localization.php'
				);

				if (!empty($_GET['page']) && in_array($_GET['page'], $donotloadpages)) {
					return;
				}

				if (apply_filters('newsletters_enqueuescript_jqueryuicore', true)) { wp_enqueue_script('jquery-ui-core'); }
				if (apply_filters('newsletters_enqueuescript_jqueryuiwidget', true)) { wp_enqueue_script('jquery-ui-widget'); }

				$screen = get_current_screen();

				// Charts
				// phpcs:ignore
				if ($screen -> id == "dashboard" || preg_match("/(index.php)/si", $_SERVER['REQUEST_URI']) ||
					(!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> welcome || $_GET['page'] == $this -> sections -> history))) {
					wp_enqueue_script('chartjs', $this -> render_url('chartjs-3.4.1-dist/chart.min.js', 'assets', false), array('jquery'), '3.4.1', false);
				}

				// Tooltips
				// phpcs:ignore
				if (preg_match("/(index\.php|widgets\.php|post\.php|post\-new\.php)/", $_SERVER['REQUEST_URI'], $matches)) {
					wp_enqueue_script('jquery-ui-tooltip', false, array('jquery'), false, true);
				}

				// phpcs:ignore
				if ((!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) || preg_match("/(post\.php|post\-new\.php)/", $_SERVER['REQUEST_URI'], $matches)) {

					// Select 2
					if (in_array($_GET['page'], (array) $this -> sections)) {
						wp_deregister_script('select2');
						wp_deregister_script('wc-enhanced-select');
						wp_enqueue_script('select2', $this -> render_url('js/select2.js', 'admin', false), array('jquery'), '4.0.0', false);
					}

					// CKEditor
					// Let's not load the CKEditor on post editing screens
					if (empty($matches)) {
						wp_enqueue_script('ckeditor', $this -> render_url('js/ckeditor/ckeditor.js', 'admin', false), array('jquery'), '4.16.1', false);
						wp_enqueue_script('ckeditor-jquery', $this -> render_url('vendors/ckeditor/adapters/jquery.js', 'admin', false), array('ckeditor', 'jquery'), "4.16.1", false);
					}

					// Color Picker
					wp_enqueue_script('iris', admin_url('js/iris.min.js'), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1);
				    wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array( 'iris' ), false, 1);
				    wp_enqueue_script('jquery-ui-tooltip', false, array('jquery'), false, true);
				    //wp_enqueue_script('jquery-ui-button', false, array('jquery'), false, true);
				}

				if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections) || (get_post_type() == $custompostslug)) {
					wp_enqueue_media();
					
					wp_enqueue_script('jquery-serialize-object', $this -> render_url('js/jquery.serialize-object.js', 'admin', false), array('jquery'), false, true);
					wp_enqueue_script('jquery-serialize-json', $this -> render_url('js/jquery.serialize-json.js', 'admin', false), array('jquery'), false, true);
					
					wp_enqueue_script('jquery-autoheight', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.autoheight.js', array('jquery'), false, true);
					wp_enqueue_script('jquery-form', false, array('jquery'), false, true);
					
					// Ace editor
					if (!empty($_GET['page']) && ($_GET['page'] == $this -> sections -> settings || $page == $this -> sections -> settings_api || ($_GET['page'] == $this -> sections -> forms))) {
						wp_enqueue_script('ace', $this -> render_url('js/ace/ace.js', 'admin', false), array('jquery'), false, true);
					}
					
					// List
					if (!empty($page) && $page == $this -> sections -> extensions) {
						wp_enqueue_script('jslist', $this -> render_url('js/list.js', 'admin', false), array('jquery'), false, true);
					}

					//countdown script
					if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
						wp_enqueue_script('jquery-countdown', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.countdown.js', array('jquery'), false, true);
					}

					//sortables
					$method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
					if ($_GET['page'] == $this -> sections -> fields && $method == "order") {
						wp_enqueue_script('jquery-ui-sortable', false, false, false, true);
					}
					
					wp_enqueue_script('clipboard');

                    // GrapesJS
                    if (isset($_GET['page']) && ($_GET['page'] == $this -> sections -> themes ||
                            $_GET['page'] == $this -> sections -> send)) {
                        $grapesjs = plugins_url() . '/' . $this -> plugin_name . '/vendors/grapesjs/grapes.min.js';
                        wp_enqueue_script('grapesjs', $grapesjs, array('jquery'), false, false);

                        $grapesjs_preset_newsletter = plugins_url() . '/' . $this -> plugin_name . '/vendors/grapesjs/grapesjs-preset-newsletter.min.js';
                        wp_enqueue_script('grapesjs-preset-newsletter', $grapesjs_preset_newsletter, array('jquery', 'grapesjs'), false, false);

                        $grapesjs_plugin_wordpress = plugins_url() . '/' . $this -> plugin_name . '/vendors/grapesjs/grapesjs-plugin-wordpress.js';
                        wp_enqueue_script('grapesjs-plugin-wordpress', $grapesjs_plugin_wordpress, array('jquery', 'grapesjs'), false, false);


                        wp_deregister_script('jetpack-gallery-settings');
                        wp_dequeue_script('jetpack-gallery-settings');



                        $grapesjs = plugins_url() . '/' . $this -> plugin_name . '/vendors/grapesjs/css/grapes.min.css';
                        wp_enqueue_style('grapesjs', $grapesjs, false, false, 'all');

                        $grapesjs_preset_newsletter = plugins_url() . '/' . $this -> plugin_name . '/vendors/grapesjs/css/grapesjs-preset-newsletter.css';
                        wp_enqueue_style('grapesjs-preset-newsletter', $grapesjs_preset_newsletter, false, false, 'all');


                    }

					/* Progress Bar */
					if ($_GET['page'] == $this -> sections -> importexport ||
						$_GET['page'] == $this -> sections -> send) {
						wp_enqueue_script('jquery-ui-progressbar', false, array('jquery-ui-core', 'jquery-ui-widget'));
						
						$datejs = $this -> render_url('js/date.js', 'admin', false);
						wp_enqueue_script('datejs', $datejs, array('jquery'), false, false);
					}

					if ($_GET['page'] == $this -> sections -> welcome ||
						$_GET['page'] == $this -> sections -> send ||
						$_GET['page'] == $this -> sections -> forms ||
						$_GET['page'] == $this -> sections -> autoresponders ||
						$_GET['page'] == $this -> sections -> templates_save || 
						$_GET['page'] == $this -> sections -> themes ||
						$_GET['page'] == $this -> sections -> settings ||
						$_GET['page'] == $this -> sections -> settings_templates ||
						$_GET['page'] == $this -> sections -> settings_subscribers ||
						$_GET['page'] == $this -> sections -> settings_system ||
						$_GET['page'] == $this -> sections -> extensions_settings) {
							//meta boxes
							wp_enqueue_script('common', false, false, false, true);
							wp_enqueue_script('wp-lists', false, false, false, true);
							wp_enqueue_script('postbox', false, false, false, true);
							wp_enqueue_script('plugin-install');
							wp_enqueue_script('updates');

							//editor
							wp_enqueue_script('editor', false, false, false, true);
							wp_enqueue_script('quicktags', false, false, false, true);
							wp_enqueue_script('wplink', false, false, false, true);
							wp_enqueue_script('wpdialogs-popup', false, false, false, true);
							wp_enqueue_style('wp-jquery-ui-dialog', false, false, false, true);
							wp_enqueue_script('word-count', false, false, false, true);
							wp_enqueue_script('media-upload', false, false, false, true);
							wp_admin_css();
							wp_enqueue_script('utils', false, false, false, true);

							//editors files
							if ($_GET['page'] == $this -> sections -> welcome) { wp_enqueue_script('welcome-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/welcome-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> send) { wp_enqueue_script('send-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/send-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> forms) { wp_enqueue_script('forms-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/forms-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> templates_save) { wp_enqueue_script('templates-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/templates-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> themes) { wp_enqueue_script('themes-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/themes-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings) { wp_enqueue_script('settings-editor', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_templates) { wp_enqueue_script('settings-editor-templates', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-templates.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_subscribers) { wp_enqueue_script('settings-editor-subscribers', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-subscribers.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> settings_system) { wp_enqueue_script('settings-editor-system', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-system.js', array('jquery'), false, true); }
							if ($_GET['page'] == $this -> sections -> extensions_settings) { wp_enqueue_script('settings-editor-extensions-settings', plugins_url() . '/' . $this -> plugin_name . '/js/editors/settings-editor-extensions-settings.js', array('jquery'), false, true); }
					}
				}

				add_thickbox();
				
				wp_register_script('wp-mailinglist', plugins_url() . '/' . $this -> plugin_name . '/js/wp-mailinglist.js', array('jquery'), '1.0', true);

				$params = array(
					'ajaxnonce'			=>	array(
						'get_country'			=>	wp_create_nonce('get_country'),
						'serialkey'				=>	wp_create_nonce('serialkey'),
						'refreshfields'			=>	wp_create_nonce('refreshfields'),
						'dismissed_notice'		=>	wp_create_nonce('dismissed_notice'),	
					)
				);
				
				wp_localize_script('wp-mailinglist', 'newsletters', $params);
				wp_enqueue_script('wp-mailinglist');
				
				wp_enqueue_script('jquery-ui-tabs');
				wp_enqueue_script('jquery-shiftclick', plugins_url() . '/' . $this -> plugin_name . '/js/jquery.shiftclick.js', array('jquery'));
				wp_enqueue_script('jquery-ui-droppable');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('colorbox', plugins_url() . '/' . $this -> plugin_name . '/js/colorbox.js', array('jquery'), false, false);

				//add our instantiator js
			    wp_register_script('datepicker-i18n', $this -> render_url('js/datepicker-i18n.js', 'admin', false), array('jquery-ui-datepicker'));

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
			    wp_enqueue_script('datepicker-i18n');

			/* Front-End Scripts */
			} else {
				$functions_variables = array();
				
				$loadscripts = $this -> get_option('loadscripts');
				$loadscripts_handles = $this -> get_option('loadscripts_handles');
				$loadscripts_pages = $this -> get_option('loadscripts_pages');

				$loadrecaptcha = false;
				include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');

				if (!empty($defaultscripts)) {
					// Deregister WooCommerce Select2 on Manage Subscriptions
					if (wpml_is_management()) {
						wp_deregister_script('select2');
						wp_deregister_script('wc-enhanced-select');
					}
					
					foreach ($defaultscripts as $handle => $script) {
						$custom_handle = (empty($loadscripts_handles[$handle])) ? $handle : $loadscripts_handles[$handle];
						$custom_pages = (empty($loadscripts_pages[$handle])) ? false : explode(",", $loadscripts_pages[$handle]);

						if ((!empty($loadscripts) && in_array($handle, $loadscripts)) || wpml_is_management()) {
							if (empty($custom_pages) || (!empty($custom_pages) && (is_single($custom_pages) || is_page($custom_pages)))) {
									if (apply_filters('newsletters_enqueuescript_' . $handle, true)) {
										switch ($handle) {
											case 'recaptcha'				:
												$loadrecaptcha = true;
												break;
											default 						:
												wp_enqueue_script($custom_handle, $script['url'], $script['deps'], $script['version'], $script['footer']);
												break;
										}
										
										do_action('newsletters_enqueuescript_after', $handle, $script);
									}
							}
						}
					}
				}

				if (apply_filters('newsletters_enqueuescript_' . $this -> plugin_name, true)) { wp_enqueue_script($this -> plugin_name, $this -> render_url('js/wp-mailinglist.js', 'admin', false), array('jquery'), false, true); }
				
				if ($captcha_type = $this -> use_captcha()) {
					$functions_variables['has_captcha'] = true;
					
					if (!empty($loadrecaptcha) && !empty($captcha_type) && $captcha_type == "recaptcha") {						
						$functions_variables['captcha'] = "recaptcha";
						
						$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
						$recaptcha_privatekey = $this -> get_option('recaptcha_privatekey');
						$recaptcha_type = $this -> get_option('recaptcha_type');
						$recaptcha_language = $this -> get_option('recaptcha_language');
						$recaptcha_theme = $this -> get_option('recaptcha_theme');
						
						$functions_variables['recaptcha_sitekey'] = $recaptcha_publickey;
						$functions_variables['recaptcha_secretkey'] = $recaptcha_privatekey;
						$functions_variables['recaptcha_type'] = (empty($recaptcha_type) || $recaptcha_type == "robot") ? 'robot' : 'invisible';
						$functions_variables['recaptcha_language'] = (empty($recaptcha_language)) ? 'en' : $recaptcha_language;
						$functions_variables['recaptcha_theme'] = (empty($recaptcha_theme)) ? 'light' : $recaptcha_theme;
						
						$handle = "google-recaptcha";
						if (!empty($recaptcha_type) && $recaptcha_type == "invisible") {
							$handle = "google-invisible-recaptcha";
						}
						
						// Multilingual reCAPTCHA
						if ($this -> language_do()) {
							if ($language = $this -> language_current()) {
								$recaptcha_language = $language;
							}
						}
						
						$handle = 'newsletters-recaptcha';
						wp_enqueue_script($handle, 'https://www.google.com/recaptcha/api.js?render=explicit&hl=' . esc_attr(wp_unslash($recaptcha_language)), array('jquery'), false, false);
					} elseif ($captcha_type == "rsc") {
						$functions_variables['captcha'] = "rsc";
					}
				}
				
				$functions_variables['ajax_error'] = __('An Ajax error occurred, please submit again.', 'wp-mailinglist');
				
				$functions_variables['ajaxnonce'] = array(
					'subscribe'						=>	wp_create_nonce('subscribe'),
				);
				
				wp_register_script('newsletters-functions', $this -> render_url('js/functions.js', 'default', false), array('jquery'), false, true);
				
				wp_localize_script('newsletters-functions', 'newsletters', $functions_variables);
				wp_enqueue_script('newsletters-functions');
			}

			return true;
		}

		function enqueue_styles() {
			global $wp_styles;
			
			$load = false;
			$theme_folder = $this -> get_option('theme_folder');

			// Admin dashboard
			if (is_admin() && !defined('DOING_AJAX')) {
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_style('colorbox', $this -> render_url('css/colorbox.css', 'admin', false), false, $this -> version, "all");
				wp_enqueue_style('fontawesome', $this -> render_url('css/fontawesome.css', 'admin', false), false, false, "all");

				// phpcs:ignore
				if ((preg_match("/(widgets\.php)/", $_SERVER['REQUEST_URI'], $matches)) || (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) || (get_post_type() == "newsletter")) {
					$uisrc = $this -> render_url('css/jquery-ui.css', 'admin', false);
					wp_enqueue_style('jquery-ui', $uisrc, false, '1.0', "all");
					wp_enqueue_style('bootstrap', $this -> render_url('css/bootstrap.css', 'admin', false), false, false, "all");
					wp_deregister_style('select2');
					wp_enqueue_style('select2', $this -> render_url('css/select2.css', 'admin', false), false, false, "all");
				}

				// Count Down
				if (!empty($_GET['page']) && $_GET['page'] == $this -> sections -> queue) {
					wp_enqueue_style('jquery-countdown', $this -> render_url('css/jquery-countdown.css', 'admin', false), false, false, "all");
				}
				
				wp_enqueue_style('wp-mailinglist', $this -> render_url('css/wp-mailinglist.css', 'admin', false), false, $this -> version, "screen");

			// Front-end
			} else {
				$loadstyles = $this -> get_option('loadstyles');
				$loadstyles_handles = $this -> get_option('loadstyles_handles');
				$loadstyles_pages = $this -> get_option('loadstyles_pages');

				include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');

				if (!empty($defaultstyles)) {
					foreach ($defaultstyles as $handle => $style) {
						$custom_handle = (empty($loadstyles_handles[$handle])) ? $handle : $loadstyles_handles[$handle];
						$custom_pages = (empty($loadstyles_pages[$handle])) ? false : explode(",", $loadstyles_pages[$handle]);

						if ((!empty($loadstyles) && in_array($handle, $loadstyles)) || wpml_is_management()) {
							if (empty($custom_pages) || (!empty($custom_pages) && (is_single($custom_pages) || is_page($custom_pages)))) {
									if (apply_filters('newsletters_enqueuestyle_' . $handle, true)) {
										wp_enqueue_style($custom_handle, $style['url'], $style['deps'], $style['version'], $style['media']);
										do_action('newsletters_enqueuestyle_after', $handle, $style);
									}
							}
						}
					}
				}
			}

			return true;
		}

		function delete_default_themes() {
			global $wpdb, $Theme;

			$themes = array(
				'blueretro'			=>	array('title' => __('Blue Retro', 'wp-mailinglist'), 'name' => "blueretro"),
				'getaway'			=>	array('title' => __('Getaway', 'wp-mailinglist'), 'name' => "getaway"),
				'nightlife'			=>	array('title' => __('Night Life', 'wp-mailinglist'), 'name' => "nightlife"),
				'paperphase'		=>	array('title' => __('Paper Phase', 'wp-mailinglist'), 'name' => "paperphase"),
				'redray'			=>	array('title' => __('Red Ray', 'wp-mailinglist'), 'name' => "redray"),
				'simplyelegant'		=>	array('title' => __('Simply Elegant', 'wp-mailinglist'), 'name' => "simplyelegant"),
				'snazzy'			=>	array('title' => __('Snazzy', 'wp-mailinglist'), 'name' => "snazzy"),
				'pronews'			=>	array('title' => __('Pro News', 'wp-mailinglist'), 'name' => "pronews"),
				'lagoon'			=>	array('title' => __('Lagoon', 'wp-mailinglist'), 'name' => "lagoon"),
				'themailer'			=>	array('title' => __('The Mailer', 'wp-mailinglist'), 'name' => "themailer"),
			);

			foreach ($themes as $theme) {
				$query = "DELETE FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `name` = '" . esc_sql($theme['name']) . "'";
				$wpdb -> $query($query);
			}

			return true;
		}

		function initialize_default_themes() {
			if (!is_admin() && !defined('DOING_AJAX')) return;

			$premade_include = $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS . 'premade.php';
			include($premade_include);

			return true;
		}

		function set_timezone() {
			$timezone_set = $this -> get_option('timezone_set');
			if (!empty($timezone_set)) {
				$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
				$current_offset = get_option('gmt_offset');
				$tzstring = wp_timezone_string(); //get_option('timezone_string');
				$check_zone_info = true;
				if (false !== strpos($tzstring,'Etc/GMT')) { $tzstring = ''; }
				
				
				if (empty($tzstring)) {
					$check_zone_info = false;
					if (0 == $current_offset) {
						$tzstring = 'UTC+0';
					} elseif ($current_offset < 0) {
						$tzstring = 'UTC' . $current_offset;
					} else {
						$tzstring = 'UTC+' . $current_offset;
					}
				}
	
				$timezone = new DateTimeZone( $tzstring );
				wp_date($timezone_format, null, $timezone );

				@putenv("TZ=" . $tzstring);
				@ini_set('date.timezone', $tzstring);
				
				$timezone = new DateTimeZone( $tzstring );
				wp_date($timezone_format, null, $timezone );

				/* if (function_exists('date_default_timezone_set')) {
					@date_default_timezone_set($tzstring);
				} */
			}
			
			return true;
		}

		function init_class($name = null, $params = array()) {
			if (!empty($name)) {
				$name = (!preg_match("/" . $this -> pre . "/si", $name)) ? $this -> pre . $name : $name;
				if (class_exists($name)) {
					if ($class = new $name($params)) {
						if (!empty($this -> plugin_name)) {
							$class -> plugin_name = $this -> plugin_name;
						}
						return $class;
					}
				}
			}

			return false;
		}

		function register_external_model($model = null) {
			if (!empty($model)) {
				if ($this -> {$model}()) {
					$this -> tablenames[$this -> pre . $this -> {$model}() -> controller] = $wpdb -> prefix . $this -> {$model}() -> table;
					$this -> tables[$this -> pre . $this -> {$model}() -> controller] = (empty($this -> {$model}() -> table_fields)) ? $this -> {$model}() -> fields : $this -> {$model}() -> table_fields;
					$this -> tables_tv[$this -> pre . $this -> {$model}() -> controller] = (empty($this -> {$model}() -> tv_fields)) ? false : $this -> {$model}() -> tv_fields;
					$this -> indexes[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> indexes)) ? $this -> {$model}() -> indexes : false;

					return true;
				}
			}

			return false;
		}

		function initialize_classes() {
			global $wpdb;

			if (!empty($this -> helpers)) {
				foreach ($this -> helpers as $helper) {
					global ${$helper};

					$helpername = $this -> pre . $helper . 'Helper';
					if (!is_object(${$helper})) {
						${$helper} = $this -> init_class($helpername);
					}
				}
			}
			
			if (!empty($this -> models)) {
				foreach ($this -> models as $model) {
					//global ${$model};
					//$newmethod = $this -> {$model}();					
					//${$model} = $newmethod;
					//$this -> {$model} = $newmethod;
					$this -> {$model}();
				}
			}

			//make sure that we have some classes defined.
			if (!empty($this -> classes)) {
				//loop our classes
				foreach ($this -> classes as $class) {
					global ${$class};

					if (!is_object(${$class})) {
						switch ($class) {
							//case 'wpmlGroup'		:
							//case 'wpmlOrder'		:
							case 'wpmlCountry'		:
								${$class} = $this -> init_class($class);
								break;
							default					:
								${$class} = $this -> init_class($this -> pre . $class);
								break;
						}

						if (!empty(${$class} -> table_fields)) {
							${$class} -> table_fields = apply_filters('newsletters_db_table_fields', ${$class} -> table_fields, $class);
						} elseif (!empty(${$class} -> fields)) {
							${$class} -> fields = apply_filters('newsletters_db_table_fields', ${$class} -> fields, $class);
						}

						$this -> tablenames[$this -> pre . ${$class} -> controller] = $wpdb -> prefix . ${$class} -> table;
						$this -> tables[$this -> pre . ${$class} -> controller] = (empty(${$class} -> table_fields)) ? ${$class} -> fields : ${$class} -> table_fields;
						$this -> tables_tv[$this -> pre . ${$class} -> controller] = (empty(${$class} -> tv_fields)) ? false : ${$class} -> tv_fields;
						$this -> indexes[$this -> pre . ${$class} -> controller] = (!empty(${$class} -> indexes)) ? ${$class} -> indexes : false;
					}

					if (empty($this -> {$class}) || !is_object($this -> {$class})) {
						$this -> {$class} = ${$class};
					}
				}
			}

			$this -> tables = apply_filters('newsletters_db_tables', $this -> tables);

			do_action('newsletters_initialize_classes_done', $this -> tables);

			return true;
		}

		function activateaction_scheduling() {
			$activateaction = $this -> get_option('activateaction');

			if (!empty($activateaction) && $activateaction != "none") {
				$timestamp = time();

				if (!wp_next_scheduled($this -> pre . '_activateaction')) {
					wp_schedule_event($timestamp, "hourly", $this -> pre . '_activateaction');
				}
			}

			return true;
		}

		function latestposts_scheduling($interval = null, $startdate = null, $args = null) {			
			if (!empty($interval) && !empty($args[0])) {
				wp_clear_scheduled_hook('newsletters_latestposts', $args);
				$schedules = wp_get_schedules();

				if (empty($startdate) || strtotime($startdate) < current_time('timestamp')) {										
					$new_timestamp = time();
				} else {			
					$new_timestamp = strtotime(get_gmt_from_date($startdate));
				}
				
				if (!wp_next_scheduled('newsletters_latestposts', $args)) {					
					wp_schedule_event($new_timestamp, $interval, 'newsletters_latestposts', $args);
				}
			}

			return true;
		}

	    function pop_scheduling() {
	        wp_clear_scheduled_hook($this -> pre . '_pophook');
	        if ($this -> get_option('bouncemethod') == "pop") {
	            $schedules = wp_get_schedules();
				$interval = $this -> get_option('bouncepop_interval');
				$new_timestamp = time() + $schedules[$interval]['interval'];

				if (!wp_next_scheduled($this -> pre . '_pophook')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_pophook');
				}
	        }

	        return;
	    }

		function importusers_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_importusers');
			if ($this -> get_option('importusers') == "Y") {
				$schedules = wp_get_schedules();
				$interval = $this -> get_option('importusersscheduling');
				$interval = (empty($interval)) ? "hourly" : $interval;
				
				$new_timestamp = time() + $schedules[$interval]['interval'];

				if (!wp_next_scheduled($this -> pre . '_importusers')) {
					wp_schedule_event($new_timestamp, $interval, $this -> pre . '_importusers');
				}
			}
		}

		function autoresponder_scheduling() {
			$schedules = wp_get_schedules();
			$interval = $this -> get_option('autoresponderscheduling');
			$new_timestamp = time() + $schedules[$interval]['interval'];

			if (!wp_next_scheduled($this -> pre . '_autoresponders')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_autoresponders');
			}

			return true;
		}

		function captchacleanup_scheduling() {
			wp_clear_scheduled_hook($this -> pre . '_captchacleanup');
			$schedules = wp_get_schedules();
			$interval = $this -> get_option('captchainterval');
			$new_timestamp = time() + $schedules[$interval]['interval'];

			if (!wp_next_scheduled($this -> pre . '_captchacleanup')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_captchacleanup');
			}

			return true;
		}
		
		function scheduling($increase = false) {			
			wp_clear_scheduled_hook($this -> pre . '_cronhook');
			
			$schedules = wp_get_schedules();
			$interval = $this -> get_option('croninterval');
			$interval = (empty($interval)) ? 'hourly' : $interval;
			$new_timestamp = time() + $schedules[$interval]['interval'];
			if ($increase == true) { $new_timestamp += 300; }

			if (!wp_next_scheduled($this -> pre . '_cronhook')) {
				wp_schedule_event($new_timestamp, $interval, $this -> pre . '_cronhook');
			}
		}
		
		function countries_scheduling() {
			if (!wp_next_scheduled('newsletters_countrieshook')) {
				wp_schedule_event(time(), 'hourly', 'newsletters_countrieshook');
			}
		}

		function optimize_scheduling() {
			if (!wp_next_scheduled('newsletters_optimizehook')) {
				wp_schedule_event(time(), 'daily', 'newsletters_optimizehook');
			}
		}

		function emailarchive_scheduling() {
			if (!wp_next_scheduled('newsletters_emailarchivehook')) {
				wp_schedule_event(time(), 'daily', 'newsletters_emailarchivehook');
			}
		}

		function get_custom_post_types($removedefaults = true) {
			if ($post_types = get_post_types(null, 'objects')) {
				$default_types = array('post', 'page', 'attachment', 'revision', 'nav_menu_item');

				if ($removedefaults) {
					foreach ($default_types as $dpt) {
						unset($post_types[$dpt]);
					}
				}

				return $post_types;
			}

			return false;
		}

		function check_tables() {
			global $wpdb;
			$this -> initialize_classes();

			if (!empty($this -> models)) {
				foreach ($this -> models as $model) {
					if (!empty($this -> {$model}() -> table_fields)) {
						$this -> {$model}() -> table_fields = apply_filters('newsletters_db_table_fields', $this -> {$model}() -> table_fields, $model);
					}
                    if( isset($this -> {$model}()->fields) && !is_array($this -> {$model}()->fields) && property_exists(get_class($this -> {$model}()),$this -> {$model}() -> fields)) {
                        $this->{$model}()->fields = apply_filters('newsletters_db_table_fields_new', $this->{$model}()->fields, $model);
                    }
					$this -> tablenames[$this -> pre . $this -> {$model}() -> controller] = $wpdb -> prefix . $this -> {$model}() -> table;
					$this -> tables[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> table_fields)) ? $this -> {$model}() -> table_fields : $this -> {$model}() -> fields;
					$this -> tables_tv[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> tv_fields)) ? $this -> {$model}() -> tv_fields : false;
					$this -> indexes[$this -> pre . $this -> {$model}() -> controller] = (!empty($this -> {$model}() -> indexes)) ? $this -> {$model}() -> indexes : false;
				}
			}

			if (!empty($this -> tablenames)) {
				foreach ($this -> tablenames as $controller => $tablename) {
					$this -> check_table($controller);
				}
			}
		}

		function check_table($name = null) {
			//global WP variables
			global $wpdb;
			if (!is_admin() && !defined('DOING_AJAX')) return;

			//ensure that a "name" was passed
			if (!empty($name)) {
				//add the WP prefix to the table name
				$oldname = $name;
				$name = $wpdb -> prefix . $name;

				//make sure that the table fields are available
				if (!empty($this -> tables[$oldname])) {
					//check if the table exists. boolean value returns
					$query = "SHOW TABLES LIKE '" . esc_sql($name) . "'";
					if (!$wpdb -> get_var($query)) {
						
						//let's start the query for a new table!
						$query = "CREATE TABLE `" . $name . "` (";
						$c = 1;

						//loop the table fields.
						foreach ($this -> tables[$oldname] as $field => $attributes) {
							//we might need to use a KEY declaration
							//in case not "key", continue with normal attributes set.
							if ($field != "key") {
								//append the field name and attributes
								$query .= "`" . $field . "` " . $attributes . "";
							} else {
								//this is a "key" field. declare it
								$query .= "" . $attributes . "";
							}

							//the last query doesn't get a comma at the end.
							//ensure that it is not the last query section.
							if ($c < count($this -> tables[$oldname])) {
								//append a comma "," to the query
								$query .= ",";
							}

							$c++;
						}

						// Commented... do not add another `id` index to the database table, it is redundant
						/*if (array_key_exists('id', $this -> tables[$oldname])) {
							$query .= ", INDEX (`id`)";
						}*/

						//end the query!
						//$query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=UTF8 COLLATE=utf8_general_ci;";
						//change to utf8mb4 and utf8mb4_unicode_ci if possible
						$charset = (empty($wpdb -> charset)) ? 'utf8' : $wpdb -> charset;
						$collate = (empty($wpdb -> collate)) ? 'utf8_general_ci' : $wpdb -> collate;
						$query .= ") ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=" . $charset . " COLLATE=" . $collate . ";";

						if (!empty($query)) {
							$this -> table_query[$name] = $query;
						}
					} else {
						//get the current fields for this table.
						$field_array = $this -> get_fields($oldname);

						//loop the fields of the table
						foreach ($this -> tables[$oldname] as $field => $attributes) {
							//make sure that its not a KEY value.
							if ($field != "key") {
								//add the database table field.
								$this -> add_field($oldname, $field, $attributes);
							}
						}

						global $wpdb, $Db, $Field, $Subscriber;
						switch ($oldname) {
							case $Subscriber -> table			:
								$Db -> model = $Field -> model;
								if ($fields = $Db -> find_all()) {
									foreach ($fields as $field) {
										$this -> add_field($oldname, $field -> slug);
									}
								}
								break;
						}

						if (!empty($this -> indexes[$oldname])) {
							$indexes = $this -> indexes[$oldname];

							// drop any redundant `id` index
							if ($wpdb -> get_var("SHOW INDEX FROM `" . $name . "` WHERE `Key_name` = 'id'")) {
								$query = "ALTER TABLE `" . $name . "` DROP INDEX `id`";
								$wpdb -> query($query);
							}

							foreach ($indexes as $index) {
								if (is_array($index)) {
									$indexname = implode("_", $index);
									if (!empty($indexname)) {
										$query = "SHOW INDEX FROM `" . $name . "` WHERE `Key_name` = '" . $indexname . "'";
										if (!$wpdb -> get_row($query)) {
											$query = "ALTER TABLE `" . $name . "` ADD UNIQUE INDEX " . $indexname . " (" . implode(", ", $index) . ");";
											$wpdb -> query($query);
										}
									}
								} else {
									$query = "SHOW INDEX FROM `" . $name . "` WHERE `Key_name` = '" . $index . "'";
									if (!$wpdb -> get_row($query)) {
										$query = "ALTER TABLE `" . $name . "` ADD INDEX(`" . $index . "`);";
										$wpdb -> query($query);
									}
								}
							}
						}
					}

					//make sure that the query is not empty.
					if (!empty($this -> table_query)) {
						foreach ($this -> table_query as $query) {
							$wpdb -> query($query);
						}
					}
				}
			} else {
				return false;
			}
		}

		/**
		 * Retrieves all the fields for a specific database table
		 * @param $table STRING The name of the table to check.
		 * @return $field_array ARRAY An array of fields for the specific table.
		 *
		 **/
		function get_fields($table = null) {
			global $wpdb;

			//make sure the table nae is available
			if (!empty($table)) {
				$fullname = $wpdb -> prefix . $table;

				$field_array = array();
				if ($fields = $wpdb -> get_results("SHOW COLUMNS FROM " . $fullname)) {
					foreach ($fields as $field) {
						$field_array[] = $field -> Field;
					}
				}

				return $field_array;
			}

			return false;
		}

		function delete_field($table = null, $field = null) {
			global $wpdb;

			if (!empty($table)) {
				if (!empty($field)) {
					$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` DROP `" . $field . "`";

					if ($wpdb -> query($query)) {
						return true;
					}
				}
			}

			return false;
		}

		function change_field($table = null, $field = null, $newfield = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;

			if (!empty($table)) {
				if (!empty($field)) {
					if (!empty($newfield)) {
						$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` CHANGE `" . $field . "` `" . $newfield . "` " . $attributes . "";

						if ($wpdb -> query($query)) {
							return true;
						}
					}
				}
			}

			return false;
		}

		function add_field($table = null, $field = null, $attributes = "TEXT NOT NULL") {
			global $wpdb;

			if (!empty($table)) {
				if (!empty($field)) {
					$field_array = $this -> get_fields($table);

					if (!empty($field_array)) {
						if (!in_array($field, $field_array)) {
							$query = "ALTER TABLE `" . $wpdb -> prefix . $table . "` ADD `" . $field . "` " . $attributes . "";
							if ($field == "id") { $query .= ", ADD PRIMARY KEY (id)"; }

							if ($wpdb -> query($query)) {
								return true;
							}
						}
					}
				}
			}

			return false;
		}

		function add_user_option($user_id = null, $option = null, $value = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;

			if (!empty($user_id) && !empty($option)) {
				if (add_user_meta($user_id, 'newsletters_' . $option, $value, false)) {
					return true;
				}
			}

			return false;
		}

		function update_user_option($user_id = null, $option = null, $value = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;

			if (!empty($user_id) && !empty($option)) {
			    $option = sanitize_text_field($option);

				if (update_user_meta($user_id, 'newsletters_' . $option, $value)) {
					return true;
				}
			}

			return false;
		}

		function get_user_option($user_id = null, $option = null, $single = true) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;

			if (!empty($user_id) && !empty($option)) {
				if ($value = get_user_meta($user_id, 'newsletters_' . $option, $single)) {
					return $value;
				}
			}

			return false;
		}

		function delete_user_option($user_id = null, $option = null) {
			$user_id = (empty($user_id)) ? get_current_user_id() : $user_id;

			if (!empty($user_id) && !empty($option)) {
				if (delete_user_meta($user_id, 'newsletters_' . $option)) {
					return true;
				}
			}

			return false;
		}

		function userdata($user_id = null) {
			if (!empty($user_id)) {
				if ($user = get_userdata($user_id)) {
					return $user;
				}
			}

			return false;
		}

		function user_role($user_id = null, $user = null) {
			if (!empty($user_id)) {
				if (!empty($user) || $user = $this -> userdata($user_id)) {
					$user_roles = $user -> roles;
					$user_role = array_shift($user_roles);

					return $user_role;
				}
			}

			return false;
		}

		function vendor($name = null, $pre = 'class', $classit = true) {
			if (!empty($name)) {
				$filename = $pre . '.' . strtolower($name) . '.php';
				$filepath = rtrim(NEWSLETTERS_DIR, DS) . DS . 'vendors' . DS;
				$filefull = $filepath . $filename;

				if (file_exists($filefull)) {
					require_once($filefull);

					if ($classit == true) {
						$class = $this -> pre . $name;

						if (${$name} = new $class) {
							return ${$name};
						}
					} else {
						return true;
					}
				}
			}

			return false;
		}

		function render_field($field_id = null, $fieldset = true, $optinid = null, $showcaption = true, $watermark = true, $instance = null, $offsite = false, $errors = array(), $form_id = null, $form_field = null) {
			global $Html, $Field, $wpmltabindex, $Mailinglist, $Subscriber,
			${'newsletters_fields_count_' . $optinid}, $newsletters_is_management;

			if (empty(${'newsletters_fields_count_' . $optinid})) {
				${'newsletters_fields_count_' . $optinid} = 1;
			}

			if (!empty($field_id)) {
				if ($field = $Field -> get($field_id)) {
					$Subscriber -> data = (array) $Subscriber -> data;

					$col = '';

					if (!empty($form_id) && !empty($form_field)) {
						$form = $this -> Subscribeform() -> find(array('id' => $form_id));
						$form_styling = maybe_unserialize($form -> styling);
						
						if ($this -> language_do()) {
							$settings = $this -> language_use($this -> language_current(), $form_field -> settings);
							$settings = maybe_unserialize($settings);	
						} else {
							$settings = maybe_unserialize($form_field -> settings);
						}
						
						$list = false;
						if ($field -> slug == "list") {							
							if (empty($settings['listchoice']) || $settings['listchoice'] == "user") {
								if (empty($settings['listchoice_user_type']) || $settings['listchoice_user_type'] == "checkboxes") {
									$list = "checkboxes";
									$listsselectall = '<div class="checkbox"><input onclick="jqCheckAll(this, false, \'list_id\');" type="checkbox" name="listsselectall" value="1" id="listsselectall" /></div>';
								} else {
									$list = "select";
								}

								$includelists = (!empty($settings['includelists'])) ? $settings['includelists'] : false;
							} elseif ($settings['listchoice'] == "admin") {
								$list = "admin";
								$adminlists = $settings['adminlists'];
							}
						}

						$visible = true;
						if ($field -> type == "hidden" || ($field -> type == "special" && $field -> slug == "list" && !empty($list) && (is_numeric($list) || $list == "all" || $list == "admin")) || (!empty($newsletters_is_management) && $field -> slug == "list")) {
							$visible = false;
							$newsletters_is_management = false;
						}

						if (!empty($fieldset) && $fieldset == true) {
							echo '<div class="form-group' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' has-error' : '') . ' newsletters-fieldholder ' . ((empty($visible) && (!is_admin() || defined('DOING_AJAX'))) ? 'newsletters-fieldholder-hidden hidden' : 'newsletters-fieldholder-visible') . '" ' . ((empty($visible) && (!is_admin() || defined('DOING_AJAX'))) ? 'hidden' : '') . '>';
						}
						
						if (!empty($offsite) && $offsite == true) {
							echo '<p>' . "\r\n";
						}
						
						if (!empty($form_styling['fieldshowlabel'])) {
							echo '<label for="' . ((!empty($list) && $list == "checkboxes") ? 'listsselectall' : ( '' . $this -> pre . '-' . $optinid . $field -> slug . '' )) . '" class="control-label' . ((!empty($list) && $list == "checkboxes") ? ' form-inline' : '') . ' ' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">' . ((!empty($list) && $list == "checkboxes") ? $listsselectall . ' ' : '') . ((empty($form_field -> label)) ? esc_html($field -> title) : esc_html($form_field -> label)) . ((empty($form_field -> required)) ? ' <small class="small text-muted">' . __('(optional)', 'wp-mailinglist') . '</small>' : '') . '</label> ';
	
							if (!empty($offsite)) {
								echo '<br/>';
							}
						}

						$watermark = (empty($form_field -> placeholder)) ? ((empty($field -> watermark)) ? false : esc_html($field -> watermark)) : esc_html($form_field -> placeholder);
						if (!empty($watermark)) {
							$placeholder = ' placeholder="' . $watermark . '"';
						}
						
						$required = (!empty($form_field -> required)) ? true : false;
					} else {
						// Determine the list(s) to use
						$list = (empty($instance['list'])) ? ((!empty($Subscriber -> data['list_id'])) ? esc_html($Subscriber -> data['list_id'][0]) : false) : esc_html($instance['list']);
						if ($this -> language_do() && !empty($instance['lang'])) {
							$list = $this -> language_use($instance['lang'], $list, false);
						}
						
						$visible = true;
						if ($field -> type == "hidden" || ($field -> type == "special" && $field -> slug == "list" && !empty($list) && (is_numeric($list) || $list == "all")) || (!empty($newsletters_is_management) && $field -> slug == "list")) {
							$visible = false;
							$newsletters_is_management = false;
						}

						$placeholder =  ' placeholder="' . ((!empty($field -> watermark) && $watermark == true && empty($offsite)) ? esc_attr(wp_unslash(esc_html($field -> watermark))) : '') . '"';

						if (!empty($fieldset) && $fieldset == true) {
							echo '<div id="newsletters-' . $optinid . $field -> slug . '-holder" class="form-group ' . $col . ' newsletters-fieldholder' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' has-error' : '') . ' ' . ((empty($visible) && (!is_admin() || defined('DOING_AJAX'))) ? 'newsletters-fieldholder-hidden hidden' : 'newsletters-fieldholder-visible') . ' ' . $field -> slug . '">';
						}
						
						if (!empty($offsite) && $offsite == true) {
							echo '<p>' . "\r\n";
						}

						if ($fieldset == true && $field -> type != "special" && $field -> type != "hidden") {
							if ($field -> type == "file") {
								?><label for="file_upload_<?php echo esc_html( $field -> id); ?><?php echo esc_html( $optinid); ?>" class="control-label <?php echo esc_html( $this -> pre . 'customfield'); ?> <?php echo esc_html($this -> pre); ?>customfield<?php echo esc_html( $field_id); ?>"><?php
							} else {
								echo '<label for="' . $this -> pre . '-' . $optinid . $field -> slug . '" class="control-label ' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
							}

							echo esc_attr($field -> title);
							if (empty($field -> required) || $field -> required == "N") { echo ' <small class="small text-muted">' . __('(optional)', 'wp-mailinglist') . '</small>'; };
							echo '</label><br/>';
						}
						
						$required = (!empty($field -> required)) ? true : false;
					}

					$fieldname = $field -> slug;
					$method = sanitize_text_field(isset($_GET['method']) ? $_GET['method'] : "");
					if (is_admin() && !defined('DOING_AJAX') && $field -> type != "file" 
						&& ($_GET['page'] != $this -> sections -> forms)
						&& (empty($method) || $method != "offsitewizard")) {
						$fieldname = 'Subscriber[' . $field -> slug . ']';
					}
					
					// Either subscriber data or a GET/POST value
					$fieldvalue = false;
					if (!empty($Subscriber -> data[$field -> slug])) {
						$fieldvalue = $Subscriber -> data[$field -> slug];
					} elseif (!empty($_REQUEST[$field -> slug])) {
						$fieldvalue = sanitize_text_field(wp_unslash($_REQUEST[$field -> slug]));
					}

					switch ($field -> type) {
						case 'hidden'			:
							if (empty($Subscriber -> data[$field -> slug])) {
								switch ($field -> hidden_type) {
									case 'post'					:
										$hidden_value = sanitize_text_field(wp_unslash($_REQUEST[$field -> hidden_value]));
										break;
									case 'predefined'			:
										$hidden_value = $field -> hidden_value;
										break;
									case 'custom'				:
									default  					:
										$hidden_value = $Subscriber -> data[$field -> slug];
										break;
									case 'get'					:
										$hidden_value = sanitize_text_field(wp_unslash($_GET[$field -> hidden_value]));
										break;
									case 'global'				:
										$hidden_value = sanitize_text_field(wp_unslash($GLOBALS[$field -> hidden_value]));
										break;
									case 'cookie'				:
										$hidden_value = sanitize_text_field(wp_unslash($_COOKIE[$field -> hidden_value]));
										break;
									case 'session'				:
										$hidden_value = sanitize_text_field(wp_unslash($_SESSION[$field -> hidden_value]));
										break;
									case 'server'				:
										$hidden_value = sanitize_text_field(wp_unslash($_SERVER[$field -> hidden_value]));
										break;
								}
							} else {
								$hidden_value = $Subscriber -> data[$field -> slug];
							}

							//parse any shortcodes in the hidden field value
							$hidden_value = wp_unslash(do_shortcode($hidden_value));
							$method = sanitize_text_field(isset($_GET['method']) ? sanitize_text_field(wp_unslash($_GET['method'])) : "");

							if (!is_admin() || defined('DOING_AJAX') || (!empty($method) && $method == "offsitewizard")) {
								echo '<input type="hidden" name="' . $fieldname . '" value="' . esc_attr(wp_unslash($hidden_value)) . '" />';
							} else {
								echo '<input type="text" class="" name="' . $fieldname . '" value="' . esc_attr(wp_unslash($hidden_value)) . '" />';
							}
							break;
						case 'text'				:
							$placeholder = isset($placeholder) ? $placeholder : '';
							$tabindex = $field -> slug === 'email' ? '' : $Html -> tabindex($optinid); 
							echo '<input' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'text' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $tabindex  . ' type="text" name="' . $fieldname . '" value="' . esc_attr(wp_unslash($fieldvalue)) . '" />';
							break;
						case 'special'			:
							switch ($field -> slug) {
								case 'list'				:
								default 				:
									// Form
									if (!empty($form_id) && !empty($form_field)) {
										if (!empty($list)) {
											switch ($list) {
												case 'admin'			:
													$lists = $Mailinglist -> select(true, $adminlists);
													if (!empty($lists)) {
														foreach ($lists as $list_id => $list_title) {
															echo '<input type="hidden" name="list_id[]" value="' . $list_id . '" />';
														}
													}
													break;
												case 'select'			:
													if (!empty($includelists)) {
														$lists = $Mailinglist -> select(true, $includelists);
													} else {
														$lists = $Mailinglist -> select(false);
													}

													if (!empty($lists)) {
														echo '<select ' . $Html -> tabindex($optinid) . ' class="' . ((!empty($Subscriber -> errors['list_id'])) ? ' newsletters_fielderror ' : '') . $this -> pre . ' autowidth ' . $this -> pre . 'select newsletters-list-select" id="' . $optinid . $field -> slug . '-list-select" name="list_id[]">';
														echo '<option value="">' . __('- Select -', 'wp-mailinglist') . '</option>';

														foreach ($lists as $list_id => $list_title) {
															echo '<option' . ((!empty($Subscriber -> data['list_id']) && $Subscriber -> data['list_id'][0] == $list_id) ? ' selected="selected"' : '') . ' value="' . $list_id . '">' . esc_html($list_title) . '</option>';
														}

														echo '</select>';
													}
													break;
												case 'checkboxes'		:
													if (!empty($includelists)) {
														$lists = $Mailinglist -> select(true, $includelists);
													} else {
														$lists = $Mailinglist -> select(false);
													}

													if (!empty($lists)) {
														foreach ($lists as $list_id => $list_title) {
															echo '<div class="checkbox">';
															echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '">';
															echo '<input' . ((!empty($Subscriber -> data['list_id']) && in_array($list_id, $Subscriber -> data['list_id'])) ? ' checked="checked"' : '') . ' type="checkbox" name="list_id[]" value="' . $list_id . '" class="newsletters-list-checkbox" id="' . $optinid . $field -> slug . '-list-checkbox" /> ';
															echo esc_html($list_title) . '</label>';
															echo '</div>';
														}
													}
													break;
											}
										}
									// Not Form
									} else {
										if (!empty($errors['list_id'])) {
											$errors['list'] = $errors['list_id'];
										}

										preg_match("/[0-9]/si", $optinid, $matches);
										$number = isset($matches[0]) ? $matches[0] : 0;

										if (!empty($list)) {
											if (is_numeric($list)) {
												echo '<input type="hidden" name="list_id[]" value="' . $list . '" />';
											} else {
												if (empty($form_id) && (empty($list) || $list != "all")) {
													echo '<label class="' . $this -> pre . 'customfield ' . $this -> pre . 'customfield' . $field_id . '">';
													echo esc_attr($field -> title);
													if (empty($field -> required) || $field -> required == "N") { echo ' <small class="small text-muted">' . __('(optional)', 'wp-mailinglist') . '</small>'; };
													echo '</label>';
												}

												$instance['lists'] = array_filter(explode(",", $instance['lists']));
												$lists = (empty($instance['lists'])) ? $Mailinglist -> select(false) : $Mailinglist -> select(true, $instance['lists']);

												if ($list == "checkboxes") {
													foreach ($lists as $list_id => $list_title) {
														echo '<div class="checkbox">';
														echo '<label class="wpmlcheckboxlabel ' . $this -> pre . '">';
														echo '<input' . ((!empty($Subscriber -> data['list_id']) && in_array($list_id, $Subscriber -> data['list_id'])) ? ' checked="checked"' : '') . ' type="checkbox" name="list_id[]" value="' . $list_id . '" class="newsletters-list-checkbox" id="' . $optinid . $field -> slug . '-list-checkbox" /> ';
														echo esc_html($list_title) . '</label>';
														echo '</div>';
													}
												} elseif ($list == "all") {
													echo '<input type="hidden" name="list_id[]" value="all" />';
												} else {
													echo '<select ' . $Html -> tabindex($optinid) . ' class="' . ((!empty($Subscriber -> errors['list_id'])) ? ' newsletters_fielderror ' : '') . $this -> pre . ' autowidth ' . $this -> pre . 'select newsletters-list-select" id="' . $optinid . $field -> slug . '-list-select" name="list_id[]">';
													echo '<option value="">' . __('- Select -', 'wp-mailinglist') . '</option>';

													foreach ($lists as $list_id => $list_title) {
														echo '<option' . ((!empty($Subscriber -> data['list_id']) && $Subscriber -> data['list_id'][0] == $list_id) ? ' selected="selected"' : '') . ' value="' . $list_id . '">' . esc_html($list_title) . '</option>';
													}

													echo '</select>';
												}

												if (!empty($field -> caption) && $showcaption == true && $field -> type != "special") {
													echo '<span class="' . $this -> pre . 'customfieldcaption">' . __(wp_unslash($field -> caption)) . '</span>';
												}
											}
										}
									}
									break;
							}
							break;
						case 'textarea'			:
							echo '<textarea' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'textarea' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' rows="4" name="' . $fieldname . '">' . strip_tags($fieldvalue) . '</textarea>';
							break;
						case 'select'			:						
							echo '<select' . $placeholder . ' class="form-control ' . $this -> pre . ' ' . $this -> pre . 'select' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" style="width:auto;" id="' . $this -> pre . '-' . $optinid . '' . $field -> slug . '" ' . $Html -> tabindex($optinid) . ' name="' . $fieldname . '">';
							echo '<option value="">' . __('- Select -', 'wp-mailinglist') . '</option>';

							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {
									$select = (!empty($fieldvalue) && ($fieldvalue == $option_id || $fieldvalue == esc_html($option_value))) ? 'selected="selected"' : '';
									echo '<option ' . $select . ' value="' . $option_id . '">' . esc_html($option_value) . '</option>';
								}
							}

							echo '</select>';
							break;
						case 'radio'			:
							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {
									$checked = ($fieldvalue == $option_id || (!empty($fieldvalue) && $fieldvalue == esc_html($value))) ? 'checked="checked"' : '';
									echo '<div class="radio">';
									echo '<label class="control-label wpmlradiolabel ' . $this -> pre . '">';
									echo '<input class="' . $this -> pre . 'radio' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="radio" ' . $checked . ' name="' . $fieldname . '" value="' . $option_id . '" /> ' . esc_html($option_value);
									echo '</label>';
									echo '</div>';
								}
							}
							break;
						case 'checkbox'			:
							$subscribercheckboxes = false;
							if (!empty($fieldvalue)) {
								$subscribercheckboxes = maybe_unserialize($fieldvalue);
							}

							if (!empty($field -> newfieldoptions)) {
								foreach ($field -> newfieldoptions as $option_id => $option_value) {
									$checked = (!empty($subscribercheckboxes) && (is_array($subscribercheckboxes) && in_array($option_id, $subscribercheckboxes))) ? 'checked="checked"' : '';
									echo '<div class="checkbox">';
									echo '<label class="control-label wpmlcheckboxlabel ' . $this -> pre . '">';
									echo '<input class="' . $this -> pre . 'checkbox' . ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : '') . '" ' . $Html -> tabindex($optinid) . ' type="checkbox" ' . $checked . ' name="' . $fieldname . '[]" value="' . $option_id . '" /> ' . esc_html($option_value);
									echo '</label>';
									echo '</div>';
								}
							}
							break;
						case 'file'				:
							$filetypes = false;
							if (!empty($field -> filetypes)) {
								if (($types = @explode(",", $field -> filetypes)) !== false) {
									if (is_array($types)) {
										$f = 1;
										foreach ($types as $type) {
											$filetypes .= '*' . $type;
											if ($f < count($types)) { $filetypes .= "; "; }
											$f++;
										}
									}
								}
							}

							?>

							<div class="clearfix"></div>
							<label class="btn btn-secondary btn-file">
								<?php esc_html_e('Browse...', 'wp-mailinglist'); ?> <input onchange="jQuery('#file_upload_<?php echo esc_html( $field -> id); ?>_info').html(jQuery(this).val().replace('C:\\fakepath\\', ''));" type="file" <?php echo esc_html( $Html -> tabindex($optinid)); ?> name="<?php echo esc_attr(wp_unslash($fieldname)); ?>" id="file_upload_<?php echo esc_html( $field -> id); ?>" />
							</label>
							<span class="label label-info" id="file_upload_<?php echo esc_html( $field -> id); ?>_info"></span>

							<?php
							break;
						case 'pre_country'		:
							global $Db, $Form;

							if ($countries = $this -> Country() -> select()) {

								$saveipaddress = $this -> get_option('saveipaddress');
								if (!empty($saveipaddress)) {								
									$ipaddress = $this -> get_ip_address();
									if ($ipcountry = $this -> get_country_by_ip($ipaddress)) {
										if ($country_id = $this -> Country() -> field('id', array('code' => $ipcountry))) {
											if (empty($fieldvalue)) {
												$Subscriber -> data[$field -> slug] = $country_id;
											}
										}
									}
								}
								
								?>

								<select class="<?php echo esc_html($this -> pre); ?>country <?php echo esc_html($this -> pre); ?> <?php echo esc_html($this -> pre); ?>precountry<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" id="<?php echo esc_html($this -> pre); ?>-<?php echo esc_html( $optinid . $field -> slug); ?>" <?php echo esc_html( $Html -> tabindex($optinid)); ?> name="<?php echo esc_html( $fieldname); ?>">
									<option value=""><?php esc_html_e('- Select Country -', 'wp-mailinglist'); ?></option>
									<?php foreach ($countries as $id => $value) : ?>
										<option <?php echo (!empty($fieldvalue) && $fieldvalue == $id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($id); ?>"><?php echo esc_html( $value); ?></option>
									<?php endforeach; ?>
								</select>

								<?php
							}
							break;
						case 'pre_date'			:
							ob_start();

							$field_value = false;
							if (!empty($fieldvalue)) {
								$field_value = maybe_unserialize($fieldvalue);
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

							<input type="text" class="<?php echo esc_html($this -> pre); ?>predate <?php echo esc_html($this -> pre); ?>text <?php echo esc_html($this -> pre); ?> <?php echo esc_html($this -> pre); ?>predate<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" value="<?php echo esc_attr(wp_unslash($currentDate)); ?>" name="<?php echo esc_html( $fieldname); ?>" id="<?php echo esc_html($this -> pre); ?>-<?php echo esc_html( $optinid . $field -> slug); ?>" />

							<?php if (empty($offsite)) : ?>
								<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('#<?php echo esc_html($this -> pre); ?>-<?php echo esc_html( $optinid . $field -> slug); ?>').datepicker({
										changeMonth: true,
										changeYear: true,
										yearRange: "<?php echo esc_js(date_i18n("Y", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " -100 years")) . ':' . date_i18n("Y", strtotime($Html -> gen_date("Y-m-d H:i:s", false, false, true) . " +100 years"))); ?>",
										dateFormat: "<?php echo esc_js($Html -> dateformat_PHP_to_jQueryUI(get_option('date_format'))); ?>",
										defaultDate: <?php echo esc_js($defaultDate); ?>,									
										showOn: "both"
									});
								});
								</script>
							<?php endif; ?>

							<?php

							$datepicker_output = ob_get_clean();
							echo apply_filters('newsletters_datepicker_output', $datepicker_output, $optinid, $field);
							break;
						case 'pre_gender'		:
							?>

							<select<?php echo esc_html( $placeholder); ?> class="<?php echo esc_html($this -> pre); ?> <?php echo esc_html($this -> pre); ?>pregender<?php echo ((!empty($Subscriber -> errors[$field -> slug])) ? ' newsletters_fielderror' : ''); ?>" style="width:auto;" id="<?php echo esc_html($this -> pre); ?>-<?php echo esc_html( $optinid); ?><?php echo esc_html( $field -> slug); ?>" <?php echo esc_html( $Html -> tabindex($optinid)); ?> name="<?php echo esc_html( $fieldname); ?>">
								<option value=""><?php esc_html_e('- Select Gender -', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($fieldvalue) && $fieldvalue == "male") ? 'selected="selected"' : ''; ?> value="male"><?php esc_html_e('Male', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($fieldvalue) && $fieldvalue == "female") ? 'selected="selected"' : ''; ?> value="female"><?php esc_html_e('Female', 'wp-mailinglist'); ?></option>
							</select>

							<?php
							break;
					}

					if (!empty($form_id) && !empty($form_field)) {
						$caption = (empty($form_field -> caption)) ? ((empty($field -> caption)) ? false : esc_html($field -> caption)) : esc_html($form_field -> caption);
						if (!empty($caption) && !empty($form_styling['fieldcaptions'])) {
							echo '<p class="help-block">' . wp_unslash($caption) . '</p>';
						}
					} else {
						$docaption = false;
						if (!empty($field -> caption) && $showcaption == true && $field -> type != "special" && $field -> type != "hidden") {
							echo '<p class="help-block ' . $this -> pre . 'customfieldcaption">' . __(wp_unslash($field -> caption)) . '</p>';
							$docaption = true;
						}
					}

					if (!empty($errors[$field -> slug])) {
						if (empty($form) || (!empty($form) && !empty($form_styling['fielderrors']))) {
							?>
	
							<div id="newsletters-<?php echo esc_html( $optinid); ?>-<?php echo esc_html( $field -> slug); ?>-error" class="newsletters-field-error alert alert-danger my-1 ui-state-error ui-corner-all">
								<i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post( wp_unslash($errors[$field -> slug])) ?>
							</div>
	
							<?php
						}
					}

					if (!empty($field -> type) && $field -> type == "file") {
						if (!empty($field -> filesizelimit)) { echo '<small>' . sprintf(__('Maximum file size of <strong>%s</strong>', 'wp-mailinglist'), $field -> filesizelimit) . '</small><br/>'; }
						if (!empty($filetypes)) { echo '<small>' . sprintf(__('Allowed file types are <strong>%s</strong>', 'wp-mailinglist'), $filetypes) . '</small><br/>'; }

						if (!empty($fieldvalue)) {
							echo wp_kses_post( $Html -> file_custom_field($fieldvalue, $field -> filesizelimit, $filetypes, $field, true));
							echo '<input id="newsletters_oldfile_' . $field -> id . '" type="hidden" name="oldfiles[' . $field -> slug . ']" value="' . esc_attr(wp_unslash($fieldvalue)) . '" />';
						}
					}
					
					if (!empty($offsite) && $offsite == true) {
						echo '</p>' . "\r\n";
					}

					if (!empty($fieldset) && $fieldset == true) {						
						echo '</div>' . "\r\n";
					}

					/*if (!empty($visible) && $visible == true) {
						if (${'newsletters_fields_count_' . $optinid}%2 == 0) {
							if (empty($form_id) || (!empty($form_id) && $form_styling['formlayout'] == "normal")) {
								?><div class="clearfix"></div><?php
							}
						}

						${'newsletters_fields_count_' . $optinid}++;
					}*/
				}
			}

			return true;
		}
		
		function get_ip_address() {			
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				return sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { 
				return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
			}
			
			return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
		}

	    function output_custom_fields($subscriber = null) {
	        global $Db, $Html, $Field;
	        $customfields = "";
	        ob_start();

	        if (!empty($subscriber)) {
	            $fields = $Field -> get_all();

	            if (!empty($fields)) {
	                $class = "alternate";

	                ?><table><tbody><?php

	  			    foreach ($fields as $field) {
						if (!empty($subscriber -> {$field -> slug})) {

							$fieldoptions = $field -> newfieldoptions;

	                        ?>

							<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
								<th><?php echo esc_attr($field -> title); ?></th>
								<td>
									<?php if ($field -> type == "radio" || $field -> type == "select") : ?>
										<?php echo esc_html($fieldoptions[$subscriber -> {$field -> slug}]); ?>
									<?php elseif ($field -> type == "checkbox") : ?>
										<?php $supoptions = maybe_unserialize($subscriber -> {$field -> slug}); ?>
										<?php if (!empty($supoptions) && is_array($supoptions)) : ?>
											<?php foreach ($supoptions as $supopt) : ?>
												&raquo;&nbsp;<?php echo esc_html($fieldoptions[$supopt]); ?><br/>
											<?php endforeach; ?>
										<?php else : ?>
											<?php esc_html_e('none', 'wp-mailinglist'); ?>
										<?php endif; ?>
									<?php elseif ($field -> type == "file") : ?>
										<?php echo wp_kses_post( $Html -> file_custom_field($subscriber -> {$field -> slug})); ?>
									<?php elseif ($field -> type == "pre_country") : ?>
										<?php echo esc_html( $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug}))); ?>
									<?php elseif ($field -> type == "pre_date") : ?>
										<?php if (is_serialized($subscriber -> {$field -> slug})) : ?>
											<?php $date = @unserialize($subscriber -> {$field -> slug}); ?>
											<?php if (!empty($date) && is_array($date)) : ?>
												<?php echo esc_html( $date['y']); ?>-<?php echo esc_html( $date['m']); ?>-<?php echo esc_html( $date['d']); ?>
											<?php endif; ?>
										<?php else : ?>
											<?php echo date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug})); ?>
										<?php endif; ?>
									<?php else : ?>
										<?php echo esc_html( $subscriber -> {$field -> slug}); ?>
									<?php endif; ?>
								</td>
							</tr>

	                        <?php
						}
					}

	                ?></tbody></table><?php
				}
	        }

	        $customfields = ob_get_clean();
	        return $customfields;
	    }

		function gen_auth($subscriber_id = null, $mailinglist_id = null) {
			$mailinglist_id = false;

			if (!empty($subscriber_id)) {
				global $Db, $Subscriber, $SubscribersList;
				$Db -> model = $Subscriber -> model;
				$subscriber = $Db -> find(array('id' => $subscriber_id));
				$authkey = (empty($subscriber -> authkey)) ? md5($subscriber_id) : $subscriber -> authkey;

				if (!empty($mailinglist_id)) {
					$Db -> model = $SubscribersList -> model;
					if ($subscriberslist = $Db -> find(array('subscriber_id' => $subscriber_id, 'list_id' => $mailinglist_id))) {
						if ($subscriberslist -> authinprog == "Y" && !empty($subscriberslist -> authkey) && $subscriberslist -> authkey == $authkey) {
							$authkey = $subscriberslist -> authkey;
						} else {
							$Db -> model = $SubscribersList -> model;
							$Db -> save_field('authkey', $authkey, array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
							$Db -> model = $SubscribersList -> model;
							$Db -> save_field('authinprog', "Y", array('list_id' => $mailinglist_id, 'subscriber_id' => $subscriber_id));
						}
					}
				} else {
					if (!empty($subscriber)) {
						if ($subscriber -> authinprog == "Y" && !empty($subscriber -> authkey)) {
							$authkey = $subscriber -> authkey;
						} else {
							$Db -> model = $Subscriber -> model;
							$Db -> save_field('authkey', $authkey, array('id' => $subscriber_id));
							$Db -> model = $Subscriber -> model;
                            $Db -> save_field('cookieauth', $authkey, array('id' => $subscriber_id));
							$Db -> model = $Subscriber -> model;
							$Db -> save_field('authinprog', "Y", array('id' => $subscriber_id));
						}
					}
				}
			}

			return $authkey;
		}

		function htmltf($format = 'html') {
			switch ($format) {
				case 'html'			:
					return true;
					break;
				case 'text'			:
					return false;
					break;
			}

			return true;
		}

		function gen_subscribe_url($subscriber = null, $mailinglists = null) {
			global $Html;
			$url = "";

			try {
				if (!empty($subscriber)) {
					if (!empty($mailinglists) && is_array($mailinglists)) {
						$mailinglists = is_array($mailinglists) ? implode(",", $mailinglists) : $mailinglists;
					}

					$querystring = 'method=subscribe&id=' . $subscriber -> id . '&mailinglists=' . $mailinglists;
					$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
				}
			}
            catch (Exception $ex) {
                error_log( 'Caught exception: ',  $ex->getMessage(), "\n");
            }


			return $url;

		}

		function gen_resubscribe_link($subscriber = null, $urlonly = false) {
			global $Html;
			$link = "";
			try {
				if (!empty($subscriber)) {
					$querystring = 'method=resubscribe&email=' . $subscriber -> email . '&mailinglists=' .  (is_array($subscriber -> mailinglists) ? implode(",", $subscriber -> mailinglists) : $subscriber -> mailinglists);
					$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));

					if (!empty($urlonly)) {
						$link = $url;
					} else {
						$link = '<a class="newsletters_resubscribe newsletters_link" href="' . $url . '">' . esc_html($this -> get_option('resubscribetext')) . '</a>';
					}
				}
			}
            catch (Exception $ex) {
                error_log( 'Caught exception: ',  $ex->getMessage(), "\n");
            }


			return $link;
		}

		function gen_unsubscribe_link($subscriber = null, $user = null, $theme_id = null, $history_id = null, $alllists = false, $urlonly = false) {
			global $Db, $Html, $Subscriber, $HistoriesList;

			try {
				if (!empty($subscriber) || !empty($user)) {
					$linktext = esc_html($this -> get_option('unsubscribetext'));
					$auth_id = (empty($subscriber)) ? $user -> ID : $subscriber -> id;
					$auth_string = (empty($subscriber)) ? $user -> roles[0] : ((!empty($subscriber -> mailinglist_id)) ? $subscriber -> mailinglist_id : false);
					$authkey = $this -> gen_auth($auth_id, $auth_string);

					if (!empty($theme_id)) {
						global $wpdb, $Theme;

						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . esc_sql($theme_id) . "' LIMIT 1";

						$query_hash = md5($acolorquery);
						if ($ob_acolor = $this -> get_cache($query_hash)) {
							$acolor = $ob_acolor;
						} else {
							$acolor = $wpdb -> get_var($acolorquery);
							$this -> set_cache($query_hash, $acolor);
						}

						$style = "color:" . $acolor . ";";
					}

					if (!empty($subscriber)) {
						$mailinglists = "";
						if (!empty($alllists) && $alllists == true) {
							$slists = $Subscriber -> mailinglists($subscriber -> id);
							$mailinglists = is_array($slists) ? implode(",", $slists) : $slists ;
							$linktext = esc_html($this -> get_option('unsubscribealltext'));
						} else {

							$history = $this -> History() -> find(array('id' => $history_id));
							$history_lists = is_object($history) ? maybe_unserialize($history -> mailinglists) : null;
							$includeonly = (empty($history_lists)) ? (isset($subscriber -> mailinglists) ? $subscriber -> mailinglists : '') : $history_lists;
							$subscriber_lists = maybe_unserialize($Subscriber -> mailinglists($subscriber -> id, $includeonly, false, false));
							$mailinglists = (empty($subscriber_lists)) ? $history_lists : $subscriber_lists;
						}

						if (!empty($mailinglists) && is_array($mailinglists)) {
							$mailinglists = implode(",", $mailinglists);
						}
						
						$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'mailinglist_id=' . $mailinglists . '&authkey=' . $authkey;
					} elseif (!empty($user)) {
						$querystring = 'method=unsubscribe&' . $this -> pre . 'history_id=' . $history_id . '&user_id=' . $user -> ID . '&authkey=' . $authkey;
					}

					$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));
					if (empty($urlonly) || $urlonly == false) {
						$style = isset($style) ? $style : '';
						$unsubscribelink = '<a class="newsletters_unsubscribe newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
					} else {
						$unsubscribelink = $url;
					}

					return $unsubscribelink;
				}
			}
            catch (Exception $ex) {
                error_log( 'Caught exception: ',  $ex->getMessage(), "\n");
            }


			return false;
		}

		function gen_manage_link($subscriber = array(), $theme_id = null, $history_id = null) {
			global $Db, $Subscriber, $Html, $Authnews;
			try {
				if (!empty($subscriber)) {
					$linktext = esc_html($this -> get_option('managelinktext'));

					if (empty($subscriber -> cookieauth)) {
						$subscriberauth = $Authnews -> gen_subscriberauth();
						$Db -> model = $Subscriber -> model;
						$Db -> save_field('cookieauth', $subscriberauth, array('id' => $subscriber -> id));
					} else {
						$subscriberauth = $subscriber -> cookieauth;
					}

					$url = $Html -> retainquery('method=loginauth&email=' . $subscriber -> email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true));

					if (!empty($theme_id)) {
						global $wpdb, $Theme;

						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . esc_sql($theme_id) . "' LIMIT 1";

						$query_hash = md5($acolorquery);
						if ($ob_acolor = $this -> get_cache($query_hash)) {
							$acolor = $ob_acolor;
						} else {
							$acolor = $wpdb -> get_var($acolorquery);
							$this -> set_cache($query_hash, $acolor);
						}

						$style = "color:" . $acolor . ";";
					}

					if (empty($subscriber -> format) || $subscriber -> format == "html") {
						$style = isset($style) ? $style : '';
						$managelink = '<a class="newsletters_manage newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
					} else {
						$managelink = $url;
					}

					return $managelink;
				}
			}
            catch (Exception $ex) {
                error_log( 'Caught exception: ',  $ex->getMessage(), "\n");
            }


			return false;
		}

		function gen_online_link($subscriber = null, $user = null, $history_id = null, $onlyurl = false, $theme_id = null, $print = false) {			
			if (!empty($history_id)) {
				global $Db, $Html;
				if ($email = $this -> History() -> find(array('id' => $history_id))) {
					$auth_id = (empty($subscriber)) ? ((isset($user) && !empty($user)) ? $user -> ID : 0) : $subscriber -> id;
					$authkey = !empty($auth_id) ? $this -> gen_auth($auth_id) : '';

					if (!empty($subscriber)) {
						$querystring = 'newsletters_method=newsletter&id=' . $email -> id . '&mailinglist_id=' . ((!empty($subscriber -> mailinglist_id)) ? $subscriber -> mailinglist_id : false)  . '&subscriber_id=' . $subscriber -> id . '&authkey=' . $authkey;
						$url = $Html -> retainquery($querystring, home_url());
					} else {
						$querystring = 'newsletters_method=newsletter&id=' . esc_html($email -> id) . '&user_id=' . ((isset($user) && !empty($user)) ? $user -> ID : 0) . '&authkey=' . $authkey;
						$url = $Html -> retainquery($querystring, home_url());
					}

					if (!empty($print) && $print == true) {
						$url = $Html -> retainquery('print=1', $url);
					}
                    $style = "";
					if (!empty($theme_id)) {
						global $wpdb, $Theme;

						$acolorquery = "SELECT `acolor` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `id` = '" . esc_sql($theme_id) . "' LIMIT 1";

						$query_hash = md5($acolorquery);
						if ($ob_acolor = $this -> get_cache($query_hash)) {
							$acolor = $ob_acolor;
						} else {
							$acolor = $wpdb -> get_var($acolorquery);
							$this -> set_cache($query_hash, $acolor);
						}

						$style = "color:" . $acolor . ";";
					}

					if (!empty($print)) {
						$url = 'https://www.printfriendly.com/print?url=' . urlencode($url);
					}

					if (!empty($onlyurl) && $onlyurl == true) {
						return $url;
					} else {
						if (empty($subscriber -> format) || $subscriber -> format == "html") {
							$text = (empty($print)) ? esc_html($this -> get_option('onlinelinktext')) : esc_html($this -> get_option('printlinktext'));
							$onlinelink = '<a class="newsletters_online newsletters_link" href="' . $url . '" style="' . $style . '">' . $text . '</a>';
						} else {
							$onlinelink = $url;
						}
					}

					return $onlinelink;
				}
			}

			return false;
		}

		function gen_tracking_link($eunique = null) {
			$tracking = "";

			if (!empty($eunique)) {
				if ($this -> get_option('tracking') == "Y") {
					
					$track_alt = '';
					$tracking_image_alt = $this -> get_option('tracking_image_alt');
					if (!empty($tracking_image_alt)) {
						$track_alt = esc_html($tracking_image_alt);
					}
					
					$tracking = '<img alt="' . esc_attr($track_alt) . '" class="newsletters-tracking" src="' . html_entity_decode(add_query_arg(array($this -> pre . 'method' => "track", 'id' => $eunique), home_url())) . '" />';
				}
			}

			return apply_filters('newsletters_tracking_image', $tracking, $eunique);
		}

		function strip_set_variables($message = null) {
			if (!empty($message)) {
				$newpatterns = array(
					"/\[(" . $this -> pre . "|news|newsletters_)email\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)unsubscribe\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)blogname\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)siteurl\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)activate\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)manage\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)online\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)track\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)mailinglist\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)subject\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)historyid\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)unsubscribecomments\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)bouncecount\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)customfields\]/",
					"/\[(" . $this -> pre . "|news|newsletters_)date\]/",
				);

				$message = preg_replace($newpatterns, "", $message);

				$pattern = get_shortcode_regex(array("wpmlfield", "newsfield", "newsletters_field"));
				$message = preg_replace("/" . $pattern . "/si", "", $message);

				$message = apply_filters('newsletters_strip_set_variables', $message);
				return $message;
			}

			return false;
		}

		function gen_role_names($user = null) {
			$role_names = "";

			if (!empty($user -> roles) && is_array($user -> roles)) {
				global $wp_roles;
				$role_names = array();

				foreach ($user -> roles as $role_key) {
					$role_names[] = $wp_roles -> role_names[$role_key];
				}

				$role_names = implode(", ", $role_names);
			}

			return $role_names;
		}

		function gen_mailinglist_names($subscriber = null) {
			global $wpdb, $Mailinglist;
			$mailinglist_names = "";
			$titles = array();

			if (is_null($subscriber)) {
				return $mailinglist_names;
			}

			if (empty($subscriber -> mailinglists)) {
				if (!empty($_POST['mailinglists'])) {
				    // phpcs:ignore
					$subscriber -> mailinglists = map_deep(wp_unslash($_POST['mailinglists']), 'sanitize_text_field');
				} else {
					$subscriber -> mailinglists = isset($subscriber -> mailinglist_id) ? array($subscriber -> mailinglist_id) : [0];
				}
			}

			if (!empty($subscriber -> mailinglists)) {
				foreach ($subscriber -> mailinglists as $list_id) {
					if ($title = $Mailinglist -> get_title_by_id($list_id)) {
						$titles[] = esc_html($title);
					}
				}

				$mailinglist_names = implode(", ", $titles);
			}

			return $mailinglist_names;
		}

		function process_set_variables($subscriber = null, $user = null, $message = null, $history_id = null, $eunique = null, $issubject = false) {
			global $wpdb, $Db, $Mailinglist, $Subscriber, $Html, $Theme;

			if (!empty($issubject) && $issubject == true) {
				$subject = $message;
			}

			if (!empty($message)) {
				// Process shortcodes from subscriber
				if (!empty($subscriber) || empty($user)) {					
					global $current_subscriber;
					$current_subscriber = $subscriber;

					if (!empty($history_id)) {

						global $current_history_id, $current_theme_id;
						$current_history_id = $history_id;

						$history = $this -> History -> find(array('id' => $history_id));
						$subject = $this -> History() -> field('subject', array('id' => $history_id));
						$post_id = $this -> History() -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post, $shortcode_post;
							$post = $getpost;
							$shortcode_post = $getpost;
						}

						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . esc_sql($history_id) . "' LIMIT 1";

						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}

						$current_theme_id = $theme_id;
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";

						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					}

					$newsearch = array(
						"/\[(" . $this -> pre . "|news|newsletters_)email\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribe\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribeurl\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribeall\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)blogname\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)siteurl\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)manage\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)print\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)online\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)online_url\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)track\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)mailinglist\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)subject\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)historyid\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribecomments\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)bouncecount\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)customfields\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)resubscribe\]/",
					);

					$newsearch = apply_filters('newsletters_processvariables_search', $newsearch, $subscriber);
					
					$subject = isset($subject) ? $subject : '';
					
					$newreplace = array(
						isset($subscriber -> email) ? $subscriber -> email : '',
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false),
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link($subscriber, false, $theme_id, $history_id, true),
						get_bloginfo('name'),
						home_url(),
						$this -> gen_manage_link($subscriber, $theme_id, $history_id),
						$this -> gen_online_link($subscriber, false, $history_id, false, $theme_id, true),
						$this -> gen_online_link($subscriber, false, $history_id, false, $theme_id, false),
						$this -> gen_online_link($subscriber, false, $history_id, true, $theme_id, false),
						$this -> gen_tracking_link($eunique),
						$this -> gen_mailinglist_names(((empty($history)) ? $subscriber : $history)),
						(preg_replace('/\$(\d)/', '\\\$$1', wp_unslash($subject))),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						isset($subscriber -> bouncecount) ? $subscriber -> bouncecount : 0,
						$this -> output_custom_fields($subscriber),
						$this -> gen_resubscribe_link($subscriber),
					);

					$newreplace = apply_filters('newsletters_processvariables_replace', $newreplace, $subscriber);

					// Default subscriber fields
					if (!empty($Subscriber -> table_fields)) {
						foreach ($Subscriber -> table_fields as $field => $attributes) {
							$newsearch[$field] = '/\[(' . $this -> pre . '|news|newsletters_)field name="' . $field . '"\]/';
							$newreplace[$field] = isset($subscriber -> {$field}) ? $subscriber -> {$field} : '';
						}
					}

					
					$subject = preg_replace($newsearch, $newreplace, wp_unslash($subject));
					$subject = apply_filters('newsletters_process_set_variables_subscriber_subject', $subject, $subscriber);
					$message = preg_replace($newsearch, $newreplace, wp_unslash($message));
					$message = apply_filters('newsletters_process_set_variables_subscriber_message', $message, $subscriber);

					$this -> replace_subscriber = $subscriber;
					// get regular expression on [wpmlfield...] and [newsletters_field...]
					$pattern = get_shortcode_regex(array("wpmlfield", "newsfield", "newsletters_field"));
					
					$message = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $message);
					$message = htmlspecialchars_decode($message, ENT_NOQUOTES);
					$subject = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $subject);
					$subject = htmlspecialchars_decode($subject, ENT_NOQUOTES);

					$this -> replace_subscriber = false;
				// Process shortcodes from user
				} elseif (!empty($user)) {					
					global $current_user;
					$current_user = $user;

					if (!empty($history_id)) {
						$subject = $this -> History() -> field('subject', array('id' => $history_id));
						$post_id = $this -> History() -> field('post_id', array('id' => $history_id));
						if (!empty($post_id) && $getpost = get_post($post_id)) {
							global $post, $shortcode_post;
							$post = $getpost;
							$shortcode_post = $getpost;
						}

						$themeidquery = "SELECT `theme_id` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . esc_sql($history_id) . "' LIMIT 1";

						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					} else {
						$themeidquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE `def` = 'Y' LIMIT 1";

						$query_hash = md5($themeidquery);
						if ($ob_theme_id = $this -> get_cache($query_hash)) {
							$theme_id = $ob_theme_id;
						} else {
							$theme_id = $wpdb -> get_var($themeidquery);
							$this -> set_cache($query_hash, $theme_id);
						}
					}

					$newsearch = array(
						"/\[(" . $this -> pre . "|news|newsletters_)email\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribe\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribeurl\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribeall\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)blogname\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)siteurl\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)activate\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)manage\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)online\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)track\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)mailinglist\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)subject\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)historyid\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)unsubscribecomments\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)bouncecount\]/",
						"/\[(" . $this -> pre . "|news|newsletters_)customfields\]/",
					);

					$newreplace = array(
						$user -> user_email,
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false),
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, false, true),
						$this -> gen_unsubscribe_link(false, $user, $theme_id, $history_id, true),
						get_bloginfo('name'),
						home_url(),
						"",
						get_edit_user_link($user -> ID),
						$this -> gen_online_link(false, $user, $history_id, false, $theme_id),
						$this -> gen_tracking_link($eunique),
						$this -> gen_role_names($user),
						wp_unslash($subject),
						$history_id,
						$this -> gen_unsubscribe_comments(),
						$subscriber -> bouncecount,
						"",
					);

					$subject = preg_replace($newsearch, $newreplace, wp_unslash($subject));
					$subject = apply_filters('newsletters_process_set_variables_user_subject', $subject, $user);
					$message = preg_replace($newsearch, $newreplace, wp_unslash($message));
					$message = apply_filters('newsletters_process_set_variables_user_message', $message, $user);

					$this -> replace_user = $user;
					$pattern = get_shortcode_regex(array("wpmlfield", "newsfield", "newsletters_field"));
					$message = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $message);
					$message = htmlspecialchars_decode($message, ENT_NOQUOTES);
					$subject = preg_replace_callback("/" . $pattern . "/si", array($this, "replace_custom_field"), $subject);
					$subject = htmlspecialchars_decode($subject, ENT_NOQUOTES);

					$this -> replace_user = false;
				}
			}

			if (!empty($issubject) && $issubject == true) {
				$subject = do_shortcode($subject);
				$return = $subject;
			} else {
				$message = do_shortcode($message);
				$return = $message;
			}

			//** Bit.ly and Click tracking
			$pattern = '/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si';
			if (preg_match_all($pattern, $return, $regs)) {												
				$return = apply_filters('newsletters_emailbody_links', $return, $history_id, $regs);
				preg_match_all($pattern, $return, $regs);
				
				$shortlinks = $this -> get_option('shortlinks');

				/* Bit.ly if shortlinks are enabled */
				if (!empty($shortlinks) && $shortlinks == "Y") {
					if (!empty($regs[1])) {
						$results = $regs[1];
						foreach($results as $k => $v) {
							if (apply_filters('wpml_bitlink_loop', true, $v, $regs)) {
								$bitlink = $this -> make_bitly_url($v);
								if (!empty($bitlink)) {
									$pattern = '/[\'"](' . preg_quote($v, '/') . ')[\'"]/si';
									$return = preg_replace($pattern, '"' . $bitlink . '"', $return);
									$regs[1][$k] = $bitlink;
								}
							}
						}
					}
				}

				/* Click Tracking */
				$clicktrack = $this -> get_option('clicktrack');
				if ($clicktrack == "Y") {					
					if (!empty($regs[1])) {
						$results = $regs[1];
						foreach ($results as $rkey => $result) {							
							if (apply_filters('wpml_hashlink_loop', true, $result, $regs)) {								
								$user_id = is_object($user) ? $user->ID : 0;
								$hashlink = $this -> hashlink($result, $history_id, ((isset($subscriber) && !empty($subscriber)) ? $subscriber -> id : 0), $user_id);
								$pattern = '/[\'"](' . preg_quote($result, '/') . ')[\'"]/si';
								$return = preg_replace($pattern, '"' . $hashlink . '"', $return);
							}
						}
					}
				}
			}
			
			// Replace video URLs
			$videoembed = $this -> get_option('videoembed');
			if (!empty($videoembed)) {
				require_once(ABSPATH . WPINC . DS . 'class-wp-oembed.php');
				$embed = new WP_oEmbed();

				$patterns = array();
				if (!empty($embed -> providers)) {
					foreach ($embed -> providers as $regex => $data) {						
						if (empty($data[1])) {
							$regex = '#' . str_replace( '___wildcard___', '(.+)', preg_quote(str_replace('*', '___wildcard___', $regex), '#')) . '#i';
							$regex = preg_replace('|^#http\\\://|', '#https?\://', $regex);
						}

						$patterns[] = $regex;
					}
				}

				$return = preg_replace_callback($patterns, array($this, 'autoembed_callback'), $return);
			}

			return $return;
		}
		
		function autoembed_callback($match = null, $width = false, $height = false) {
			if (!empty($match) && is_array($match)) {
				$url = trim(strip_tags($match[0]));
			} else {
				$url = $match;
			}

	        if (!empty($url)) {
		        
		        // See if this video was cached
		        if ($oc_output = $this -> get_cache(md5($url), 'video')) {			        
			        return $oc_output;
		        }
		        
		        require_once(ABSPATH . WPINC . DS . 'class-wp-oembed.php');
		        $embed = new WP_oEmbed();
		        if ($provider = $embed -> get_provider($url)) {
			        if ($response = $embed -> fetch($provider, $url)) {
				        if (empty($response -> errorCode)) {
					        $play_icon = $this -> render_url('img/play.png', 'default');
	
							$imagesize = getimagesize($response -> thumbnail_url);
							$width = (empty($width)) ? $imagesize[0] : $width;
							$height = (empty($height)) ? $imagesize[1] : $height;
	
							$output .= '<table border="0" style="width:' . $width . 'px;" width="' . $width . '" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td background="' . $response -> thumbnail_url . '" style="padding:0 !important; border:0; background-image: url(\'' . $response -> thumbnail_url . '\'); background-repeat:no-repeat; text-align:center; vertical-align:middle;" align="center" valign="middle" bgcolor="#FFFFFF" width="100%" height="' . $height . '">
											<!--[if gte mso 9]>
												<v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:' . $width . 'px; height:' . $height . 'px;" strokecolor="none">
												<v:fill type="tile" color="#ffffff" src="' . $response -> thumbnail_url . '" /></v:fill>
											<![endif]-->
												<table width="' . $width . '" border="0" cellspacing="0" cellpadding="0">
									            	<tbody>
									            		<tr>
									            			<td style="padding:0 !important; border:0; text-align:center; vertical-align:middle;" width="100%" height="' . $height . '"" valign="middle" align="center">
																<a href="' . $url . '"><img src="' . $play_icon . '" /></a>
															</td>
														</tr>
													</tbody>
												</table>
											<!--[if gte mso 9]>
												</v:rect>
											<![endif]-->
										</td>
									</tr>
								</tbody>
							</table>';
							
							// Cache the video output so it doesn't have to be fetched each time
							$this -> set_cache(md5($url), $output, 'video');
	
							return $output;
						} else {
							$error = sprintf(__('Could not auto embed URL %s because of error code %s and error message %s', 'wp-mailinglist'), $url, $response -> errorCode, $response -> errorMessage);
							$this -> log_error($error);
						}
					} else {
						$error = sprintf(__('%s No response received from the video provider, please check that the video has embedding enabled.', 'wp-mailinglist'), $url);
						$this -> log_error($error);
					}
				} else {
					$error = sprintf(__('%s No provider found for this video, the video provider may not be supported.', 'wp-mailinglist'), $url);
					$this -> log_error($error);
				}
	        }

	        return $match[0];
	    }
	    
	    function replace_custom_field($matches = null) {
			global $Db, $Field, $Html;

          //  $user  = $subscriber = array();
          //  print_r(get_class($this));
           // print_r($this);
           // if(property_exists(get_class($this), 'replace_subscriber')) {
                $subscriber = $this->replace_subscriber;
           // }
          //  $user  = $subscriber = array();
		//	if(property_exists(get_class($this), 'replace_user')) {
                $user = $this->replace_user;
        //    }
			if (!empty($matches)) {
				$atts = shortcode_parse_atts($matches['3']);

				if (!empty($atts['name'])) {
					
					$namesplit = explode("|", $atts['name']);
                   // error_log('hahaha');
                   // error_log(json_encode($namesplit));
					//if(is_array())
					
                    $atts['name'] = (is_array($namesplit) && count($namesplit) >= 1) ? $namesplit[0] : $namesplit;
					$defaultvalue = (is_array($namesplit) && count($namesplit) > 1) ? $namesplit[1] : (is_array($namesplit) ? $namesplit[0] : $namesplit);
					//print_r($defaultvalue);
					$Db -> model = $Field -> model;
					if ($field = $Db -> find(array('slug' => (is_array($atts['name']) ? implode(',',  $atts['name']) : $atts['name']) ))) {
						$fieldoptions = $field -> newfieldoptions;

						if (!empty($subscriber)) {
							switch ($field -> type) {
								case 'pre_country'		:
									$shortcode_replace = $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug}));
									break;
								case 'pre_date'			:
									$date = @unserialize($subscriber -> {$field -> slug});
									if (!empty($date) && is_array($date)) {
										$shortcode_replace = $date['y'] . '-' . $date['m'] . '-' . $date['d'];
									} else {
										$shortcode_replace = date_i18n(get_option('date_format'), strtotime($subscriber -> {$field -> slug}));
									}
									break;
								case 'pre_gender'		:
									$shortcode_replace = $Html -> gender($subscriber -> {$field -> slug});
									break;
								case 'checkbox'			:
									$supoptions = maybe_unserialize($subscriber -> {$field -> slug});
									if (!empty($supoptions) && is_array($supoptions)) {
										$replace = "";
										foreach ($supoptions as $supopt) {
											$replace .= '&raquo; ' . esc_html($fieldoptions[$supopt]) . "\r\n";
										}
										$shortcode_replace = $replace;
									} else {
										$shortcode_replace = __('none', 'wp-mailinglist');
									}
									break;
								case 'radio'			:
								case 'select'			:
									$value = $subscriber -> {$field -> slug};
									$shortcode_replace = esc_html($fieldoptions[$value]);
									break;
								case 'special'			:
									switch ($field -> slug) {
										case 'list'			:
											$shortcode_replace = $this -> gen_mailinglist_names($subscriber);
											break;
									}
									break;
								default					:
									$value = $subscriber -> {$field -> slug};
									if (!empty($value)) {
										if (($varray = @unserialize($value)) !== false) {
											$subscriber -> {$field -> slug} = '';
											$newline = (empty($subscriber -> format) || $subscriber -> format == "html") ? "<br/>" : "\r\n";

											foreach ($varray as $vkey => $vval) {
												$subscriber -> {$field -> slug} .= '&raquo; ' . esc_html($vval) . $newline;
											}
										} else {
											if (!empty($field -> type) && $field -> type == "textarea") {
												$subscriber -> {$field -> slug} = wpautop($subscriber -> {$field -> slug});
											}
										}
									}

									$shortcode_replace = $subscriber -> {$field -> slug};
									break;
							}
							
							if (empty($shortcode_replace) && !empty($defaultvalue)) {
								return $defaultvalue;
							}

							return $shortcode_replace;
						} elseif (!empty($user)) {
							switch ($field -> type) {
								case 'email'				:
									$shortcode_replace = $user -> user_email;
									break;
								case 'special'				:
									switch ($field -> slug) {
										case 'list'			:
											global $wp_roles;
											$role = $user -> roles[0];
											$shortcode_replace = $wp_roles -> role_names[$role];
											break;
									}
									break;
								default 					:
									$importusersfields = $this -> get_option('importusersfields');
									$importusersfieldspre = $this -> get_option('importusersfieldspre');
		
									if (!empty($importusersfieldspre[$field -> id])) {
										if (!empty($user -> {$importusersfieldspre[$field -> id]})) {
											$shortcode_replace = $user -> {$importusersfieldspre[$field -> id]};
										}
									} elseif (!empty($importusersfields[$field -> id])) {
										if (!empty($user -> {$importusersfields[$field -> id]})) {
											$shortcode_replace = $user -> {$importusersfields[$field -> id]};
										}
									}
									break;
							}
							
							if (empty($shortcode_replace) && !empty($defaultvalue)) {
								return $defaultvalue;
							}

							return $shortcode_replace;
						}
					} else {
						if (isset($subscriber) && !empty($subscriber -> {$atts['name']})) {
							return wp_unslash($subscriber -> {$atts['name']});
						} elseif (isset($defaultvalue) &&  !empty($defaultvalue)) {
							return $defaultvalue;
						}
					}
				}
			}

			return false;
		}

		function gen_unsubscribe_comments() {
			/* Unsubscribe Comments */
			$unsubscribecomments = __('No feedback was provided by the subscriber.', 'wp-mailinglist');
			if (!empty($_POST[$this -> pre . 'comments'])) {
				$unsubscribecomments = "";
				$unsubscribecomments .= __('Comments:', 'wp-mailinglist') . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
				$unsubscribecomments .= esc_html(sanitize_text_field(wp_unslash($_POST[$this -> pre . 'comments']))) . "\r\n";
				$unsubscribecomments .= "------------------------------------" . "\r\n";
			}

			return wpautop($unsubscribecomments);
		}

		function subscription_confirm($subscriber = array()) {			
			global $wpdb, $Db, $Html, $Subscriber, $Mailinglist, $SubscribersList;

			if (!empty($subscriber)) {
				if (!empty($_POST['list_id'])) {
					$subscriber -> mailinglists = map_deep(wp_unslash($_POST['list_id']), 'sanitize_text_field');
				} elseif (!empty($subscriber -> mailinglists)) {
					//do nothing, it's ready
				} else {
					$subscriber -> mailinglists = $Subscriber -> mailinglists($subscriber -> id, false, false, "N");
				}

				if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {					
					if ($this -> get_option('activationemails') == "multiple") {
						foreach ($subscriber -> mailinglists as $list_id) {
							$isactive = $SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
							$subscriber -> mailinglist_id = $list_id;
							$subscriber -> mailinglists = array($list_id);
							$mailinglist = $Mailinglist -> get($list_id, false);

							if ($isactive == "N") {
								if (empty($mailinglist -> doubleopt) || $mailinglist -> doubleopt == "Y") {
									if ($this -> get_option('requireactivate') == "Y" || $mailinglist -> paid == "Y") {
										
										$subject = wp_unslash($this -> et_subject('confirm'));
										$fullbody = $this -> et_message('confirm', $subscriber);
										
										$form_id = sanitize_text_field(wp_unslash($_POST['form_id']));
										if (!empty($form_id)) {
											if ($etsubject = $this -> et_subject('confirm_form_' . $form_id, $subscriber)) {
												$subject = $etsubject;
											}
											
											if ($etmessage = $this -> et_message('confirm_form_' . $form_id, $subscriber)) {
												$fullbody = $etmessage;
											}
										}
										
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('confirm'), false, $fullbody);
										$eunique = $Html -> eunique($subscriber, false, "confirmation");
										$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "confirmation");
									}
								} else {
									$Db -> model = $SubscribersList -> model;
									$Db -> save_field('active', "Y", array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
								}
							}
						}
					} else {						
						foreach ($subscriber -> mailinglists as $lkey => $list_id) {
							$mailinglist = $Mailinglist -> get($list_id, false);
							if (!empty($mailinglist -> doubleopt) && $mailinglist -> doubleopt == "N") {
								unset($subscriber -> mailinglists[$lkey]);
								$Db -> model = $SubscribersList -> model;
								$Db -> save_field('active', "Y", array('subscriber_id' => $subscriber -> id, 'list_id' => $list_id));
							}
						}

						if (!empty($subscriber -> mailinglists)) {							
							$subject = wp_unslash($this -> et_subject('confirm'));						
							$fullbody = $this -> et_message('confirm', $subscriber);
							
							$form_id = sanitize_text_field(wp_unslash($_POST['form_id']));
							if (!empty($form_id)) {
								if ($etsubject = $this -> et_subject('confirm_form_' . $form_id, $subscriber)) {
									$subject = $etsubject;
								}
								
								if ($etmessage = $this -> et_message('confirm_form_' . $form_id, $subscriber)) {
									$fullbody = $etmessage;
								}
							}
							
							$message = $this -> render_email(false, array('subscriber' => $subscriber), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('confirm'), false, $fullbody);
							$eunique = $Html -> eunique($subscriber, false, "confirmation");
							$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "confirmation");
						}
					}

					return true;
				}
			}

			return false;
		}
		
		function admin_notification_import_complete() {	
			global $Html, $Subscriber;
			
			$import_notification = $this -> get_option('import_notification');
			if (!empty($import_notification)) {
				$adminemail = $this -> get_option('adminemail');
				$subject = wp_unslash($this -> et_subject('import_complete'));
				$fullbody = $this -> et_message('import_complete');
				$message = $this -> render_email(false, false, false, true, true, $this -> et_template('import_complete'), false, $fullbody);
	
				if (strpos($adminemail, ",") !== false) {
					$adminemails = explode(",", $adminemail);
					foreach ($adminemails as $adminemail) {
						if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
							$subscriber = $Subscriber -> get_by_email($adminemail);
							$eunique = $Html -> eunique($subscriber, false, "importcomplete");
							$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "importcomplete");
							$emailsused[] = $adminemail;
						}
					}
				} else {				
					$subscriber = $Subscriber -> get_by_email($adminemail);
					$eunique = $Html -> eunique($subscriber, false, "importcomplete");
					$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "importcomplete");
				}
			}
		}
		
		function admin_notification_queue_complete() {	
			global $Html, $Subscriber;
					

			//<!-- Changing from 3 notifications for the 3 queues to 1 notification at the end of queue. Needs work. -->
			$newsletters_queue_count = get_transient('newsletters_queue_count');
           // if(!empty($newsletters_queue_count) )
           // {

			$notifyqueuecomplete = $this -> get_option('notifyqueuecomplete');
			if (!empty($notifyqueuecomplete)) {
				$adminemail = $this -> get_option('adminemail');
				$subject = wp_unslash($this -> et_subject('queue_complete'));
				$fullbody = $this -> et_message('queue_complete');
				$message = $this -> render_email(false, false, false, true, true, $this -> et_template('queue_complete'), false, $fullbody);
	
				if (strpos($adminemail, ",") !== false) {
					$adminemails = explode(",", $adminemail);
					foreach ($adminemails as $adminemail) {
						if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
							$subscriber = $Subscriber -> get_by_email($adminemail);
							$eunique = $Html -> eunique($subscriber, false, "queuecomplete");
							$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "queuecomplete");
							$emailsused[] = $adminemail;
						}
					}
				} else {				
					$subscriber = $Subscriber -> get_by_email($adminemail);
					$eunique = $Html -> eunique($subscriber, false, "queuecomplete");
					$this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "queuecomplete");
				}
			}
		   //}
		}

		function admin_subscription_notification($subscriber = array()) {
			global $wpdb, $Mailinglist, $Html;

			if (!empty($subscriber)) {
				if ($this -> get_option('adminemailonsubscription') == "Y") {

					$emailsused = array();

					$adminemail = $this -> get_option('adminemail');	// main administrator email
					$to = (object) array('email' => $adminemail);
					$subject = wp_unslash($this -> et_subject('subscribe', $subscriber));
					$fullbody = $this -> et_message('subscribe', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);

					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$eunique = $Html -> eunique($to, false, "subscription");
								$this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "subscription");
								$emailsused[] = $adminemail;
							}
						}
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "subscription");
					}

					if (!empty($subscriber -> mailinglists)) {
						foreach ($subscriber -> mailinglists as $mailinglist_id) {
							$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "'";
							if ($email = $wpdb -> get_var($adminemailquery)) {
								if (!empty($email) && $email != $adminemail) {
									if (empty($emailsused) || !in_array($email, $emailsused)) {
										$to = (object) array('email' => $email);
										$subject = wp_unslash($this -> et_subject('subscribe', $subscriber));
										$fullbody = $this -> et_message('subscribe', $subscriber);
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);
										$eunique = $Html -> eunique($to, false, "subscription");
										$this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "subscription");
										$emailsused[] = $email;
									}
								}
							}
						}
					}

					return true;
				}
			}

			return false;
		}

		function user_unsubscription_notification($subscriber = null, $mailinglists = null) {
			global $wpdb, $Mailinglist, $Subscriber, $Html;
			$unsubscribe_usernotification = $this -> get_option('unsubscribe_usernotification');

			if (!empty($subscriber) && !empty($mailinglists)) {
				if (!empty($unsubscribe_usernotification)) {
					$subject = wp_unslash($this -> et_subject('unsubscribeuser', $subscriber));
					$fullbody = $this -> et_message('unsubscribeuser', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglists' => $mailinglists), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('unsubscribeuser'), false, $fullbody);
					$eunique = $Html -> eunique($subscriber, false, "unsubscription");

					if ($this -> execute_mail($subscriber, false, $subject, $message, false, false, $eunique, false, "unsubscription")) {
						return true;
					}
				}
			}

			return false;
		}

		function admin_unsubscription_notification($subscriber = array(), $mailinglist = array()) {
			global $wpdb, $Mailinglist, $Subscriber, $Html;

			if (!empty($subscriber) && !empty($mailinglist)) {
				if ($this -> get_option('adminemailonunsubscription') == "Y") {
					$emailsused = array();
					$adminemail = $this -> get_option('adminemail');	// main administrator email

					if (!empty($subscriber -> mailinglists)) {
						foreach ($subscriber -> mailinglists as $mailinglist_id) {
							$adminemailquery = "SELECT `adminemail` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . $mailinglist_id . "'";
							if ($email = $wpdb -> get_var($adminemailquery)) {
								if (!empty($email) && $email != $adminemail) {
									if (empty($emailsused) || !in_array($email, $emailsused)) {
										$to = (object) array('email' => $email);
										$subject = wp_unslash($this -> et_subject('unsubscribe', $subscriber));
										$fullbody = $this -> et_message('unsubscribe', $subscriber);
										$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('subscribe'), false, $fullbody);
										$eunique = $Html -> eunique($to, false, "unsubscription");
										$this -> execute_mail($to, false, $subject, $message, false, false, $eunique, false, "unsubscription");
										$emailsused[] = $email;
									}
								}
							}
						}
					}

					if (is_array($mailinglist)) {
						$subscriber -> mailinglists = $mailinglist;
					} else {
						$subscriber -> mailinglists = array($mailinglist);
					}

					$to = new stdClass();
					$to -> id = $Subscriber -> admin_subscriber_id();
					$to -> email = $adminemail;
					$subject = wp_unslash($this -> et_subject('unsubscribe', $subscriber));
					$fullbody = $this -> et_message('unsubscribe', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('unsubscribe'), false, $fullbody);

					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "unsubscription");
								$emailsused[] = $adminemail;
							}
						}

						return true;
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "unsubscription");

						return true;
					}
				}
			}

			return false;
		}

		function admin_bounce_notification($subscriber = array()) {
			if ($this -> get_option('adminemailonbounce') == "Y") {
				if (!empty($subscriber)) {
					$emailsused = array();
					$adminemail = $this -> get_option('adminemail');
					$subject = wp_unslash($this -> et_subject('bounce', $subscriber));
					$fullbody = $this -> et_message('bounce', $subscriber);
					$message = $this -> render_email(false, array('subscriber' => $subscriber, 'mailinglist' => $mailinglist), false, $this -> htmltf($subscriber -> format), true, $this -> et_template('bounce'), false, $fullbody);

					if (strpos($adminemail, ",") !== false) {
						$adminemails = explode(",", $adminemail);
						foreach ($adminemails as $adminemail) {
							if (empty($emailsused) || !in_array($adminemail, $emailsused)) {
								$to -> email = $adminemail;
								$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "bounce");
								$emailsused[] = $adminemail;
							}
						}
					} else {
						$to -> email = $adminemail;
						$this -> execute_mail($to, false, $subject, $message, false, false, false, false, "bounce");
					}

					return true;
				}
			}

			return false;
		}

		function strip_title($title = null) {
			global $Html;
			return $Html -> sanitize($title, "_");

			return false;
		}

		function replace_history($match = array()) {
			if (!empty($match)) {
				global $Db, $Html;

				if ($emails = $this -> History() -> find_all(array('sent' => "> 0"), false, array('modified', "DESC"))) {
					$content = '';

					foreach ($emails as $email) {
						ob_start();

						?>

						<h3><a href="<?php echo $Html -> retainquery('newsletters_method=newsletter&id=' . esc_html($email -> id), home_url()); ?>" title="<?php echo $email -> subject; ?>"><?php echo $email -> subject; ?></a></h3>
						<div><small><?php _e('Sent on', 'wp-mailinglist'); ?> : <?php echo $email -> modified; ?></small></div>
						<?php echo $this -> strip_set_variables($email -> message); ?>

						<?php

						$content .= ob_get_clean();
					}

					$content = wpautop($content);
					return $content;
				}
			}

			return false;
		}

		function replace_meta($matches = array()) {
			if (!empty($matches[0])) {
				if (preg_match("/" . $this -> pre . "meta\_([0-9]*)/i", $matches[0], $matches2)) {
					if (!empty($matches2[1])) {
						global $post_ID;
						$oldpostid = $post_ID;
						$post_ID = $matches2[1];

						ob_start();
						the_meta();
						$meta = ob_get_clean();

						$post_ID = $oldpostid;
						return $meta;
					}
				}
			}

			return false;
		}

		function remove_server_limits() {
			@set_time_limit(0);
			@ini_set('memory_limit', -1);
			@ini_set('upload_max_filesize', '128M');
			@ini_set('post_max_size', '1024M');
			@ini_set('max_execution_time', 3000);
			@ini_set('max_input_time', 3000);
			return true;
		}

		function set_time_limit($time = 0) {
			if (ini_get('max_execution_time')) {
				ini_set('max_execution_time', 0);
			}

			//check if "set_time_limit" is available
			if (ini_get('set_time_limit')) {
				//set the "max_execution_time" to unlimited
				set_time_limit(0);
			}
		}

		function phpmailer_messageid() {
			$messageid = "<";
			$messageid .= md5(uniqid(current_time('timestamp')));
			$messageid .= "@";
			$messageid .= sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME']));
			$messageid .= ">";
			return $messageid;
		}

		function inlinestyles($html = null) {
			global $newsletters_plaintext;

			if (!empty($newsletters_plaintext) || !class_exists('DOMDocument')) {
				return $html;
			}

			$inlinestyles = $this -> get_option('inlinestyles');

			if (!empty($inlinestyles)) {
				
				require_once($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
				
				$emogrifier = new \Pelago\Emogrifier($html);
				$html = $emogrifier -> emogrify();
				
				return apply_filters('newsletters_inlinestyles_html', $html);
			}

			return apply_filters('newsletters_inlinestyles_html', $html);
		}


        //Added by Mohsen to prevent duplicated emails
        function has_been_sent_already($subscriber = null, $user = null, $history_id = null, $eunique = null )
        {
            global $wpdb, $Db, $Html, $Email, $phpmailer, $Mailinglist, $Subscriber,
                   $SubscribersList, $orig_message, $wpml_message, $wpml_textmessage, $fromwpml, $newsletters_plaintext;

            $email_table = $wpdb -> prefix . $Email -> table;

            if (empty($history_id)){
                return false;
            }
            $subscriber_or_user_id = "subscriber_id";
            $identifier_of_either_user_or_subscriber = $subscriber;
            if (empty($subscriber) && !empty($user))
            {
                $subscriber_or_user_id = "user_id";
                $identifier_of_either_user_or_subscriber = $user->ID;
            }
            else
            {
                $identifier_of_either_user_or_subscriber = $subscriber -> id;
            }
            $query =  "SELECT count(id) FROM `" . $email_table . "` WHERE ". $subscriber_or_user_id . " = " . $identifier_of_either_user_or_subscriber . " and history_id = " . esc_sql($history_id) . "   and eunique = '" . esc_sql($eunique) . "' ;";
            $count = $wpdb -> get_var($query);

            if($count > 0)
            {
                return true;
            }
            else {
                return false;
            }
        }

        function is_using_grapeJS ($history_id = null) {
            global $wpdb;

            $history_table = $wpdb -> prefix . $this -> History() -> table;

            if (empty($history_id)){
                return false;
            }

            $query =  "SELECT using_grapeJS FROM `" . $history_table . "` WHERE id = " . $history_id . ";";
            $result = $wpdb -> get_var($query);
            return $result;

        }

        function get_grapeJS_content ($history_id = null) {
            global $wpdb;

            $history_table = $wpdb -> prefix . $this -> History() -> table;

            if (empty($history_id)){
                return false;
            }

            $query =  "SELECT grapejs_content FROM `" . $history_table . "` WHERE id = " . $history_id . ";";
            $result = $wpdb -> get_var($query);
            return $result;

        }


		function execute_mail($subscriber = null, $user = null, $subject = null, $message = null, $attachments = null, $history_id = null, $eunique = null, $shortlinks = true, $emailtype = "newsletter") {
			global $wpdb, $Db, $Html, $Email, $phpmailer, $Mailinglist, $Subscriber,
			$SubscribersList, $orig_message, $wpml_message, $wpml_textmessage, $fromwpml, $newsletters_plaintext;

			$sent = false;
			$fromwpml = true;


            if (!empty($history_id)) {
                $is_using_grapeJS = $this -> is_using_grapeJS($history_id);
                if ($is_using_grapeJS) {
                    $message = $this -> get_grapeJS_content($history_id);
                }
            }

			if (empty($subscriber) && empty($user)) { $error[] = __("No subscriber specified", 'wp-mailinglist'); }
			if (empty($subject)) { $error[] = __('No subject specified', 'wp-mailinglist'); }
			if (empty($message)) { $error[] = __('No message specified', 'wp-mailinglist'); }

			global $wpdb;
			if (!empty($history_id)) {
				$query = "SELECT `id`, `from`, `fromname`, `text`, `mailinglists`, `user_id` FROM `" . $wpdb -> prefix . $this -> History() -> table . "` WHERE `id` = '" . esc_sql($history_id) . "'";
				$his = $wpdb -> get_row($query);
				$history = stripslashes_deep($his);
			}

            $found_duplicate = false;
            if(!empty($history_id) && ($emailtype == 'newsletter') && $this -> has_been_sent_already($subscriber, $user , $history_id, $eunique) )
            {
                $error[] = __('Duplicated Email (already sent)', 'wp-mailinglist');
				$found_duplicate = true;
            }

			$smtpfrom = (empty($history -> from)) ? __($this -> get_option('smtpfrom')) : $history -> from;
			$smtpfromname = (empty($history -> fromname)) ? __($this -> get_option('smtpfromname')) : $history -> fromname;

			$validationdata = array('subscriber' => $subscriber, 'user' => $user, 'subject' => $subject, 'message' => $message, 'history_id' => $history_id);
			$error = isset($error) ? $error : [];
			$error = apply_filters($this -> pre . '_sendmail_validation', $error, $validationdata);

			if (!empty($attachments) && $attachments != false) {
				$attachments = maybe_unserialize($attachments);
			}
			
			$attachments = apply_filters('newsletters_execute_mail_attachments', $attachments, $subscriber, $user, $history_id);

			if (empty($error)  && !$found_duplicate ) {
				$Db -> model = $Email -> model;

				$message = str_replace("[wpmlsubject]", $subject, $message);
				$message = str_replace("[newsletters_subject]", $subject, $message);

				$subject = do_shortcode($subject);
				$message = do_shortcode($message);
				$wpml_textmessage = do_shortcode($wpml_textmessage);

				$subject = $this -> process_set_variables($subscriber, $user, wp_unslash($subject), $history_id, $eunique, true);
				$message = $this -> process_set_variables($subscriber, $user, wp_unslash($message), $history_id, $eunique);
				$message = apply_filters('newsletters_execute_mail_message', $message);
				$wpml_textmessage = $this -> process_set_variables($subscriber, $user, wp_unslash($wpml_textmessage), $history_id, $eunique);
				$wpml_textmessage = apply_filters('newsletters_execute_mail_textmessage', $wpml_textmessage);

				if (!empty($subscriber -> id)) {
					$Subscriber -> inc_sent($subscriber -> id);
				}

				if (!empty($subscriber -> mailinglists)) {
					foreach ($subscriber -> mailinglists as $mailinglist_id) {
						$query = "SELECT `paid` FROM `" . $wpdb -> prefix . $Mailinglist -> table . "` WHERE `id` = '" . esc_sql($mailinglist_id) . "' LIMIT 1";
						$paid = $wpdb -> get_var($query);

						if (!empty($paid) && $paid == "Y") {
							$query = "UPDATE `" . $wpdb -> prefix . $SubscribersList -> table . "` SET `paid_sent` = (`paid_sent` + 1) WHERE `subscriber_id` = '" . esc_sql($subscriber -> id) . "' AND `list_id` = '" . esc_sql($mailinglist_id) . "' LIMIT 1";
							$wpdb -> query($query);
						}
					}
				}
				
				$multimime = $this -> get_option('multimime');

				// Should an AltBody be generated?
				// This is used with multi-mime emails are turned on
				$altbody = false;
				if (!empty($history -> text)) {					
					$altbody = $history -> text;
					$altbody = do_shortcode($altbody);
					$altbody = $this -> process_set_variables($subscriber, $user, wp_unslash($altbody), $history_id, $eunique);
					$altbody = apply_filters('newsletters_execute_mail_textmessage', $altbody);
					$wpml_textmessage = $altbody;
				}
				
				if (version_compare(PHP_VERSION, '5.3.2') >= 0) {					
					if (class_exists('DOMDocument')) {						
						require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
						$html2text = new Html2Text();

						if (!empty($wpml_textmessage)) {							
							$altbody = $html2text -> convert($wpml_textmessage);
						}
					}
				}

				$to = false;
				if (!empty($subscriber)) {					
					$to = $subscriber -> email;
				} elseif (!empty($user)) {					
					$to = $user -> user_email;
				}

				$mailtype = $this -> get_option('mailtype');
				$mailpriority = $this -> get_option('mailpriority');

				global $newsletters_presend, $newsletters_emailraw, $mailerrors, $messageid;
				
				$mailapi = $this -> get_option('mailapi');

				$data = array(
					'mailtype'		=>	$mailtype,
					'mailapi'		=>	$mailapi,
					'to'			=>	$to,
					'smtpfrom'		=>	$smtpfrom,
					'smtpfromname'	=>	$smtpfromname,
					'smtpreply'		=>	isset($smtpreply) ? $smtpreply : '',
					'subject'		=>	$subject,
					'message'		=>	$message,
					'altbody'		=>	$altbody,
					'attachments'	=>	$attachments,
					'mailpriority'	=>	$mailpriority,
				);

				$sent = apply_filters('newsletters_execute_mail', false, $data);

				if (!apply_filters('newsletters_execute_mail_override', false)) {					
					if ($mailtype == "smtp" || $mailtype == "gmail" || (!empty($newsletters_presend) && $newsletters_presend == true)) {
						
						if (!is_object($phpmailer)) {
							
							/**
							 * Since 4.7
							 * PHPMailer update - support for phpMailer 6.0 (WP 5.5+)
							 */


							if ( version_compare( get_bloginfo( 'version' ), '5.5-alpha', '<' ) ) {

								require_once(ABSPATH . WPINC . DS . 'class-phpmailer.php');
								$phpmailer = new PHPMailer(true);

							} else {

								if ( ! class_exists( '\PHPMailer\PHPMailer\PHPMailer', false ) ) {
									require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
								}
								
								if ( ! class_exists( '\PHPMailer\PHPMailer\Exception', false ) ) {
									require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
								}
					
								if ( ! class_exists( '\PHPMailer\PHPMailer\SMTP', false ) ) {
									require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
								}
					
								$phpmailer = new \PHPMailer\PHPMailer\PHPMailer(true);

							}

						}
	
						try {
							//clear all recipients
							$phpmailer -> ClearAddresses();
							$phpmailer -> ClearAllRecipients();
							$phpmailer -> ClearCCs();
							$phpmailer -> ClearBCCs();
							$phpmailer -> ClearAttachments();
							$phpmailer -> ClearReplyTos();
							$phpmailer -> ClearCustomHeaders();
	
							//set the language
							$phpmailer_language = $this -> plugin_base() . DS . 'vendors' . DS . 'phpmailer-language';
							$phpmailer -> SetLanguage('en', $phpmailer_language);
	
							$phpmailer -> IsSMTP();
							$phpmailer -> Host = $this -> get_option('smtphost');
							$phpmailer -> Port = $this -> get_option('smtpport');
							$phpmailer -> SMTPKeepAlive = true;
	
							$smtpsecure = $this -> get_option('smtpsecure');
							if (!empty($smtpsecure) && $smtpsecure != "N") {
								$phpmailer -> SMTPSecure = $smtpsecure;
							}
	
							$phpmailer -> SMTPAutoTLS = false;
	
							if ($this -> debugging) {
								$phpmailer -> SMTPDebug = 1;
								$phpmailer -> Debugoutput = 'html';
							}
	
							if ($this -> get_option('smtpauth') == "Y") {
								$phpmailer -> SMTPAuth = true;
								$phpmailer -> Username = $this -> get_option('smtpuser');
								$phpmailer -> Password = $this -> get_option('smtppass');
							}
	
							//DKIM-Signature (DomainKeys Identified Mail)
							if ($this -> get_option('dkim') == "Y") {								
								$phpmailer -> DKIM_identity = $smtpfrom;
								$phpmailer -> DKIM_private_string = $this -> get_option('dkim_private');
								$phpmailer -> DKIM_domain = $this -> get_option('dkim_domain');
								$phpmailer -> DKIM_selector = $this -> get_option('dkim_selector');
							}
	
							if (!empty($attachments) && $attachments != false) {
								if (is_array($attachments)) {
									foreach ($attachments as $attachment) {
										if (empty($attachment['data'])) {
											$phpmailer -> AddAttachment($attachment['filename'], $attachment['title']);
										} else {
											$phpmailer -> addStringAttachment($attachment['data'], $attachment['filename']);
										}
									}
								} else {
									if (empty($attachments['data'])) {
										$phpmailer -> AddAttachment($attachments['filename'], $attachments['title']);
									} else {
										$phpmailer -> addStringAttachment($attachments['data'], $attachments['filename']);
									}
								}
							}
	
							//set the Charset to that of Wordpress
							$phpmailer -> CharSet = get_option('blog_charset');
							$phpmailer -> SetFrom($smtpfrom, $smtpfromname);
	
							$phpmailer -> Sender = $this -> get_option('bounceemail');
	
							if (!empty($subscriber)) {
								$to = $subscriber -> email;
							} elseif (!empty($user)) {
								$to = $user -> user_email;
							}
	
							$phpmailer -> AddCustomHeader('Precedence', "bulk");
							$phpmailer -> AddCustomHeader('List-Unsubscribe', $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
	
							$phpmailer -> AddAddress($to);
							$phpmailer -> AddReplyTo($smtpfrom, $smtpfromname);
	
							// Should the Reply-To header be different?
							$replytodifferent = $this -> get_option('replytodifferent');
							if (!empty($replytodifferent)) {
								$smtpreply = $this -> get_option('smtpreply');
								$phpmailer -> AddReplyTo($smtpreply, $smtpfromname);
							}
							
							$bccemails = $this -> get_option('bccemails');
							if (!empty($bccemails)) {
								$bccemails_address = $this -> get_option('bccemails_address');
								if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
									$phpmailer -> addBCC($bccemails_address);
								}
							}
							
							if (!empty($altbody) && $multimime == "Y") {
								$phpmailer -> AltBody = $altbody;
							}
	
							if (!empty($newsletters_plaintext)) {
								$phpmailer -> ContentType = "text/plain";
								$phpmailer -> IsHTML(false);
								$message = strip_tags($message);
								$phpmailer -> AltBody = false;
							} else {
								$phpmailer -> ContentType = "text/html";
								$phpmailer -> IsHTML(true);
							}
	
							$phpmailer -> Subject = wp_unslash($subject);
							$phpmailer -> Body = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id));
	
							if (!empty($mailpriority) && $mailpriority != 3) {
								$phpmailer -> Priority = $mailpriority; //set the email priority
							}
							$phpmailer -> WordWrap = 0;
							$phpmailer -> Encoding = $this -> get_option('emailencoding');
							$phpmailer -> MessageID = $this -> phpmailer_messageid();
	
							global $newsletters_presend, $newsletters_emailraw;
							if (!empty($newsletters_presend) && $newsletters_presend == true) {
								$yourmailexchange = gethostname() ;
								$yourmailexchange = sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST']));
								$yourmailexchangeip_response = wp_remote_get("http://ipecho.net/plain");
								$yourmailexchangeip = wp_remote_retrieve_body($yourmailexchangeip_response);
								$receivermailexchange = sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST']));
								$receiveddate = date_i18n('D, d M Y H:i:s O');
								$receivermailexchange = 'gmail.com';
								$phpmailer -> AddCustomHeader('Received', 'from ' . $yourmailexchange . ' (' . $yourmailexchange . ' [' . $yourmailexchangeip . ']) by ' . $receivermailexchange . ' with SMTP; ' . $receiveddate);
								$phpmailer -> PreSend();
								$emailraw = $phpmailer -> getSentMIMEMessage();
								$newsletters_emailraw = $emailraw;
								return $emailraw;
							}
							
							$phpmailer = apply_filters($this->pre . '_phpmailer_before_send', $phpmailer);

							if ($phpmailer -> Send()) {
								$sent = true;
								$messageid = $phpmailer -> MessageID;
							} else {
								global $mailerrors;
								$mailerrors = $phpmailer -> ErrorInfo;
								return false;
							}

						// Since 4.7 - catch exception for phpMailer 6.0
						} catch (PHPMailer\PHPMailer\Exception $e) {
							$mailerrors = $e -> errorMessage(); //Pretty error messages from PHPMailer 6.0
						} catch (phpmailerException $e) {
							$mailerrors = $e -> errorMessage(); //Pretty error messages from PHPMailer < 6.0
						} catch (Exception $e) {
							$mailerrors = $e -> getMessage(); //Boring error messages from anything else!
						}

					} elseif ($mailtype == "api") {
						$mailapi = $this -> get_option('mailapi');
	
						$data = array(
							'api'			=>	$mailapi,
							'to'			=>	$to,
							'from'			=>	$smtpfrom,
							'fromname'		=>	$smtpfromname,
							'replyto'		=>	$smtpreply,
							'subject'		=>	$subject,
							'html'			=>	$message,
							'text'			=>	$altbody,
							'attachments'	=>	$attachments,
							'mailpriority'	=>	$mailpriority,
						);
	
						do_action('newsletters_sendmail_api', $data);
	
						switch ($mailapi) {
							case 'sparkpost'					:
								require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
								
								$sparkpost_apikey = $this -> get_option('mailapi_sparkpost_apikey');
								
								$client = new GuzzleHttp\Client();
								$httpClient = new Http\Adapter\Guzzle6\Client($client);
								$sparky = new SparkPost\SparkPost($httpClient, array('key' => $sparkpost_apikey));
															
								try {
									$replytodifferent = $this -> get_option('replytodifferent');
									if (!empty($replytodifferent)) {
										$smtpreply = $this -> get_option('smtpreply');
									} else {
										$smtpreply = $smtpfrom;
									}
									
									$headers = array();
									$headers['Precedence'] = "bulk";
									$headers['List-Unsubscribe'] = $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true);
									
									$sparkpost_data = array(
										'content'				=>	array(
											'from'					=>	array(
												'name'					=>	$smtpfromname,
												'email'					=>	$smtpfrom,
											),
											'reply_to'				=> 	$smtpreply,
											'headers'				=>	$headers,
											'subject'				=>	wp_unslash($subject),
											'html'					=>	((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id, 'sparkpost', false)) : null),
											//'text'					=>	$altbody,
										),
										'recipients'			=>	array(
											array(
												'address'			=>	array(
													'email'				=>	$to,
												),
											)
										),
									);
									
									$bccemails = $this -> get_option('bccemails');
									if (!empty($bccemails)) {
										$bccemails_address = $this -> get_option('bccemails_address');
										if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
											$sparkpost_data['recipients'][0]['address']['email'] = $bccemails_address;
											$sparkpost_data['recipients'][0]['address']['header_to'] = $to;
										}
									}
									
									if (!empty($altbody)) {
										$sparkpost_data['content']['text'] = $altbody;
									}
									
									if (!empty($attachments)) {
										$sparkpost_data['content']['attachments'] = array();
										foreach ($attachments as $attachment) {
											$filetype = wp_check_filetype($attachment['filename']);
											
											$response = wp_remote_get($attachment['filename']);
											$data = wp_remote_retrieve_body($response);
											
											$sparkpost_data['content']['attachments'][] = array(
												'type'			=>	$filetype['type'],
												'name'			=>	$attachment['title'],
												'data'			=>	base64_encode($data),
											);
										}
									}
									
									$messageid = $this -> phpmailer_messageid();
									
									$sparkpost_data['metadata'] = array(
										'messageid'				=>	$messageid,
									);
									
									$sparkpost_data = apply_filters('newsletters_mailapi_sparkpost_data', $sparkpost_data);
									
									$promise = $sparky -> transmissions -> post($sparkpost_data);
									
									$response = $promise -> wait();
									$statuscode = $response -> getStatusCode();
									
									if (!empty($statuscode) && $statuscode < 400) {
										$responsebody = $response -> getBody();	
																	
										$sent = true;	
									} else {
										global $mailerrors;
										$mailerrors = sprintf(__('%s status code received', 'wp-mailinglist'), $statuscode);
										return false;
									}
								} catch (Exception $e) {
									global $mailerrors;
									$errors = $e -> getMessage();	
									$errors = json_decode($errors);
									$error = $errors -> errors[0];
									$mailerrors = $error -> code . ': ' . $error -> message . ' (' . $error -> description . ')';			
									$this -> log_error($mailerrors);
									return false;
								}					
								break;
							case 'mailgun'						:	
								$mailgun_apikey = $this -> get_option('mailapi_mailgun_apikey');
								$mailgun_domain = $this -> get_option('mailapi_mailgun_domain');
								$mailgun_region = $this -> get_option('mailapi_mailgun_region');
								$region = (empty($mailgun_region) || $mailgun_region == "US") ? 'https://api.mailgun.net' : 'https://api.eu.mailgun.net';
								
	
								require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
								//$mailgun = new Mailgun\Mailgun($mailgun_apikey);
								//$mailgun = new Mailgun\Mailgun::create($mailgun_apikey);
                                //$mg = $mailgun -> create($mailgun_apikey, $region);
								//$mg = Mailgun\Mailgun::create($mailgun_apikey, $region);
								$mg = Mailgun\Mailgun::create($mailgun_apikey, $region);
	

								try {
	
									$data = array(
										'from'				=>	$smtpfromname . '<' . $smtpfrom . '>',
										'to'				=>	$to,
										'subject'			=>	wp_unslash($subject),
										'text'				=>	$altbody,
										'html'				=>	((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id, 'mailgun', $mg)) : null),
										'attachment'		=>	array(),
									);
									
									$bccemails = $this -> get_option('bccemails');
									if (!empty($bccemails)) {
										$bccemails_address = $this -> get_option('bccemails_address');
										if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
											$data['bcc'] = $bccemails_address;
										}
									}
	
									$data['h:Precedence'] = "bulk";
									$data['h:List-Unsubscribe'] = $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true);
	
									$files = array();
	
									if (!empty($attachments) && $attachments != false) {
										if (is_array($attachments)) {
											foreach ($attachments as $attachment) {
												$files['attachment'][] = $attachment['filename'];
											}
										} else {
											$files['attachment'][] = $attachment['filename'];
										}
									}
									
									$messageid = $this -> phpmailer_messageid();
									$data['v:my-custom-data'] = wp_json_encode(array('MessageID' => $messageid));
	
									$data = apply_filters('newsletters_mailapi_mailgun_data', $data);
									$files = apply_filters('newsletters_mailapi_mailgun_files', $files);
	
									$result = $mg -> messages() -> send($mailgun_domain, $data, $files);
	
									$sent = true;
								} catch (Exception $e) {
									global $mailerrors;
									$mailerrors = $e -> getMessage();								
									return false;
								}
								break;
							case 'mailjet'						:
	
								break;
							case 'amazonses'					:
	
								$mailapi_amazonses_key = $this -> get_option('mailapi_amazonses_key');
								$mailapi_amazonses_secret = $this -> get_option('mailapi_amazonses_secret');
								$mailapi_amazonses_region = $this -> get_option('mailapi_amazonses_region');
	
								require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
	
								global $sesmessage, $ses;
								$sesmessage = new SimpleEmailServiceMessage();
								$sesmessage -> addTo($to);
								$sesmessage -> setFrom($smtpfromname . '<' . $smtpfrom . '>');
								$sesmessage -> setSubject(wp_unslash($subject));
								$htmlbody = ((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id, 'amazonses', $sesmessage)) : null);
								$sesmessage -> setMessageFromString($altbody, $htmlbody);
	
								if (!empty($attachments) && $attachments != false) {
									if (is_array($attachments)) {
										foreach ($attachments as $attachment) {
											$sesmessage -> addAttachmentFromFile($attachment['title'], $attachment['filename']);
										}
									} else {
										$sesmessage -> addAttachmentFromFile($attachment['title'], $attachment['filename']);
									}
								}
								
								$bccemails = $this -> get_option('bccemails');
								if (!empty($bccemails)) {
									$bccemails_address = $this -> get_option('bccemails_address');
									if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
										$sesmessage -> addBCC($bccemails_address);
									}
								}
	
								$sesmessage -> addCustomHeader("Precedence: bulk");
								$sesmessage -> addCustomHeader("List-Unsubscribe: " . $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
	
								do_action('newsletters_mailapi_amazonses_beforesend', $sesmessage);
	
								if (empty($ses)) {		
									$signature_version = SimpleEmailService::REQUEST_SIGNATURE_V4;						
									$ses = new SimpleEmailService($mailapi_amazonses_key, $mailapi_amazonses_secret, 'email.' . $mailapi_amazonses_region . '.amazonaws.com', false, $signature_version);
									$ses -> setBulkMode(true);
								}
									
								$result = $ses -> sendEmail($sesmessage, false, false);
	
								if (!empty($result) && empty($result -> error)) {
									$messageid = $result['MessageId'];
									$sent = true;
								} else {
									global $mailerrors;
								    $error = $result -> error['Error']['Message'];
								    $this -> log_error($error);
								    $mailerrors = $error;							    
								    return false;
								}
	
								break;
							case 'sendgrid'						:
								$sendgrid_apikey = $this -> get_option('mailapi_sendgrid_apikey');
	
								require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
								$sendgrid = new \SendGrid($sendgrid_apikey);
	
								global $sendgridemail;
								$sendgridemail = new \SendGrid\Mail\Mail();
	
								$sendgridemail -> addTo($to);
								$sendgridemail -> setFrom($smtpfrom, $smtpfromname);
								$sendgridemail -> setSubject(wp_unslash($subject));
	
								if (empty($newsletters_plaintext)) {				
									$sendgridemail -> addContent("text/plain", $altbody . " ");
									$html = $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id, 'sendgrid', $sendgridemail));
									$sendgridemail -> addContent("text/html", $html . " ");
								} else {
									//$sendgridemail -> addContent("text/html", false);
									$sendgridemail -> addContent("text/plain", $altbody . " ");
								}
	
								if (!empty($attachments) && $attachments != false) {									
									if (is_array($attachments)) {
										foreach ($attachments as $attachment) {
											
											$attachment_response = wp_remote_get($attachment['filename']);
											$attachment_data = wp_remote_retrieve_body($attachment_response);
											
											$file_encoded = base64_encode($attachment_data);
											$sendgridemail -> addAttachment($file_encoded, null, basename($attachment['filename']));
										}
									} else {
										$attachment_response = wp_remote_get($attachment['filename']);
										$attachment_data = wp_remote_retrieve_body($attachment_response);
										
										$file_encoded = base64_encode($attachment_data);
										$sendgridemail -> addAttachment($file_encoded, null, basename($attachment['filename']));
									}
								}
	
								do_action('newsletters_mailapi_sendgrid_beforesend', $sendgridemail);
	
								$replytodifferent = $this -> get_option('replytodifferent');
								if (!empty($replytodifferent)) {
									$smtpreply = $this -> get_option('smtpreply');
									$sendgridemail -> setReplyTo($smtpreply);
								}
								
								$bccemails = $this -> get_option('bccemails');
								if (!empty($bccemails)) {
									$bccemails_address = $this -> get_option('bccemails_address');
									if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
										$sendgridemail -> addBcc($bccemails_address);
									}
								}
								
								$messageid = $this -> phpmailer_messageid();
	
								$sendgridemail -> addHeader('Precedence', "bulk");
								$sendgridemail -> addHeader('List-Unsubscribe', $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true));
								$sendgridemail -> addCustomArg('MessageID', $messageid);
								
								try {
									$result = $sendgrid -> send($sendgridemail);
									$body = json_decode($result -> body());
									
									if (!empty($this -> debugging)) {
										echo '<p>' . sprintf(__('Status Code: %s', 'wp-mailinglist'), $result -> statusCode()) . '</p>';
										echo implode("<br/>", $result -> headers());
									}
									
									if (empty($body -> errors)) {
										$sent = true;
									} else {
										global $mailerrors;
										foreach ($body -> errors as $error) {
											$this -> log_error($error -> message);
											$mailerrors = $error -> message;
										}
										
										return false;
									}
								} catch (Exception $e) {
									global $mailerrors;
									$this -> log_error($e -> getMessage());
									$mailerrors = $e -> getMessage();
									return false;
								}
								break;
							case 'mandrill'						:
	
								$headers['Precedence'] = "bulk";
								$headers['List-Unsubscribe'] = $this -> gen_unsubscribe_link($subscriber, $user, $theme_id, $history_id, false, true);
	
								$mailpriority = $this -> get_option('mailpriority');
	
								try {
									require_once($this -> plugin_base() . DS . 'vendors' . DS . 'mailapis' . DS . 'mandrill' . DS . 'Mandrill.php');
									$mailapi_mandrill_key = $this -> get_option('mailapi_mandrill_key');
								    $mandrill = new Mandrill($mailapi_mandrill_key);
	
								    global $mandrillmessage;
	
								    $mandrillmessage = array(
								        'html' => ((empty($newsletters_plaintext)) ? $this -> inlinestyles(apply_filters($this -> pre . '_send_body', wp_unslash($message), $phpmailer, $history_id, 'mandrill', $mandrill)) : null),
								        'text' => $altbody,
								        'subject' => wp_unslash($subject),
								        'from_email' => $smtpfrom,
								        'from_name' => $smtpfromname,
								        'to' => array(
								            array(
								                'email' => $to,
								                //'name' => 'Recipient Name',
								                'type' => 'to'
								            )
								        ),
								        'headers' => $headers,
								        'important' => ((!empty($mailpriority) && $mailpriority == 1) ? true : false),
								        'track_opens' => null,
								        'track_clicks' => null,
								        'auto_text' => null,
								        'auto_html' => null,
								        'inline_css' => null,
								        'url_strip_qs' => null,
								        'preserve_recipients' => null,
								        'view_content_link' => null,
								        //'bcc_address' => 'message.bcc_address@example.com',
								        'tracking_domain' => null,
								        'signing_domain' => null,
								        'return_path_domain' => null,
								        //'merge' => true,
								        //'merge_language' => 'mailchimp',
								        /*'global_merge_vars' => array(
								            array(
								                'name' => 'merge1',
								                'content' => 'merge1 content'
								            )
								        ),
								        'merge_vars' => array(
								            array(
								                'rcpt' => 'recipient.email@example.com',
								                'vars' => array(
								                    array(
								                        'name' => 'merge2',
								                        'content' => 'merge2 content'
								                    )
								                )
								            )
								        ),
								        'tags' => array('password-resets'),*/
								        //'subaccount' => ,
								        /*'google_analytics_domains' => array('example.com'),
								        'google_analytics_campaign' => 'message.from_email@example.com',
								        'metadata' => array('website' => 'www.example.com'),
								        'recipient_metadata' => array(
								            array(
								                'rcpt' => 'recipient.email@example.com',
								                'values' => array('user_id' => 123456)
								            )
								        ),
								        'attachments' => array(
								            array(
								                'type' => 'text/plain',
								                'name' => 'myfile.txt',
								                'content' => 'ZXhhbXBsZSBmaWxl'
								            )
								        ),
								        'images' => array(
								            array(
								                'type' => 'image/png',
								                'name' => 'IMAGECID',
								                'content' => 'ZXhhbXBsZSBmaWxl'
								            )
								        )*/
								    );
								    
								    $bccemails = $this -> get_option('bccemails');
									if (!empty($bccemails)) {
										$bccemails_address = $this -> get_option('bccemails_address');
										if (!empty($bccemails_address) && $Subscriber -> email_validate($bccemails_address)) {
											$mandrillmessage['to'][] = array(
												'email'					=>	$bccemails_address,
												'type'					=>	'bcc',
											);
										}
									}
	
								    $mandrillmessage = apply_filters('newsletters_mailapi_mandrill_messagearray', $mandrillmessage);
	
								    $subaccount = $this -> get_option('mailapi_mandrill_subaccount');
								    if (!empty($subaccount)) {
									    $mandrillmessage['subaccount'] = $subaccount;
								    }
	
								    if (!empty($attachments) && $attachments != false) {
									    $mandrillmessage['attachments'] = array();
	
										if (is_array($attachments)) {
											foreach ($attachments as $attachment) {
												$filetype = wp_check_filetype($attachment['filename']);
												
												$attachment_response = wp_remote_get($attachment['filename']);
												$attachment_data = wp_remote_retrieve_body($attachment_response);
	
												$mandrillmessage['attachments'][] = array(
													'type'				=>	$filetype['type'],
													'name'				=>	$attachment['title'],
													'content'			=>	base64_encode($attachment_data),
												);
											}
										} else {
											$filetype = wp_check_filetype($attachment['filename']);
											
											$attachment_response = wp_remote_get($attachment['filename']);
											$attachment_data = wp_remote_retrieve_body($attachment_response);
	
											$mandrillmessage['attachments'][] = array(
												'type'				=>	$filetype['type'],
												'name'				=>	$attachment['title'],
												'content'			=>	base64_encode($attachment_data),
											);
										}
									}
	
									/*$mandrillmessage['headers'] = array(
										'Content-Type'				=>	"text/plain",
									);*/
	
								    $async = true;
								    //$ip_pool = 'Main Pool';
								    $ip_pool = $this -> get_option('mailapi_mandrill_ippool');
								    //$send_at = 'example send_at';
								    $send_at = null;
								    $result = $mandrill -> messages -> send($mandrillmessage, $async, $ip_pool, $send_at);
	
	
								    if (!empty($result[0]['status']) && ($result[0]['status'] == "sent" || $result[0]['status'] == "queued")) {
								    	$sent = true;
								    }
	
								    /*
								    Array
								    (
								        [0] => Array
								            (
								                [email] => recipient.email@example.com
								                [status] => sent
								                [reject_reason] => hard-bounce
								                [_id] => abc123abc123abc123abc123abc123
								            )
	
								    )
								    */
								} catch(Mandrill_Error $e) {
								    // Mandrill errors are thrown as exceptions
								    //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
								    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
								    //throw $e;
	
								    global $mailerrors;
								    $mailerrors = $e -> getMessage();
								    return false;
								}
								break;
						}
					} else {						
						if (!empty($subscriber)) {
							$to = $subscriber -> email;
						} elseif (!empty($user)) {
							$to = $user -> user_email;
						}
	
						$content_type = (!empty($newsletters_plaintext)) ? 'text/plain' : 'text/html';
	
						$subject = wp_unslash($subject);
						$message = wp_unslash($message);
						$headers = '';
						$headers .= 'Content-Type: ' . $content_type . '; charset="UTF-8"' . "\r\n";
						$headers .= 'From: ' . $smtpfromname . ' <' . $smtpfrom . '>' . "\r\n";
	
						$atts = array();
						if (!empty($attachments) && is_array($attachments)) {
							foreach ($attachments as $attachment) {
								$atts[] = $attachment['filename'];
							}
						}
	
						global $wpml_message, $wpmlhistory_id;
						$wpml_message = $message;
						$wpmlhistory_id = $history_id;
	
						if ($result = wp_mail($to, $subject, $message, $headers, $atts)) {
							$sent = true;
							$messageid = $phpmailer -> MessageID;
						} else {
							global $mailerrors, $phpmailer;
							$mailerrors = $phpmailer -> ErrorInfo;
	
							return false;
						}
					}
				}

				if (!empty($sent) && $sent == true) {
					global $phpmailer;
					$this -> delete_all_cache('all');

					$history_lists = isset($history) ? maybe_unserialize($history -> mailinglists) : null;
					$includeonly = (empty($history_lists)) ? $subscriber -> mailinglists : $history_lists;
					$subscriber_lists = maybe_unserialize($Subscriber -> mailinglists($subscriber -> id, $includeonly, false, false));
					$e_mailinglists = (empty($subscriber_lists)) ? $history_lists : $subscriber_lists;
					
					$owner_id = 0;
					$owner_role = '';
					if (!empty($history -> user_id) && $current_user = get_userdata($history -> user_id)) {
						$owner_id = $current_user -> ID;
						$owner_role = $current_user -> roles[0];
					}

					if (!empty($eunique)) {
						$e_data = array(
							'eunique'				=>	$eunique,
							'subscriber_id'			=>	(!empty($subscriber) ? $subscriber -> id : 0),
							'user_id'				=>	(!empty($user) ? $user -> ID : 0),
							'mailinglist_id'		=>	(!empty($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : ''),
							'mailinglists'			=>	maybe_serialize($e_mailinglists),
							'history_id'			=>	$history_id,
							'owner_id'				=>	$owner_id,
							'owner_role'			=>	$owner_role,
							'type'					=>	$emailtype,
							'read'					=>	"N",
							'status'				=>	"sent",
							'messageid'				=>	$messageid,
						);
	
						$Db -> model = $Email -> model;
	
						if (!$Db -> save($e_data, true)) {
							$this -> log_error('Email could not be saved: ' . implode("; ", $Email -> errors));
						}
					}
				}
			}

			return $sent;
		}

		function log_error($error = null) {
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;

			if (!empty($error)) {
				if (is_array($error) || is_object($error)) {
					$error = '<pre>' . print_r($error, true) . '</pre>';
				}
				
				error_log(date_i18n('[Y-m-d H:i:s] ') . $error . PHP_EOL, 3, NEWSLETTERS_LOG_FILE);

				return true;
			}

			return false;
		}

		/**
		 * Prints a variable or an array encapsulated in PRE tags
		 * Creates an easy to read hierarchy/structure
		 *
		 * @param ARRAY/STRING
		 * @return BOOLEAN
		 */
		function debug($var = array(), $output = true, $specialchars = false) {
			if ($output == false) { ob_start(); }
			$debugging = get_option('tridebugging');
			$this -> debugging = (empty($debugging)) ? $this -> debugging : true;

			if ($this -> debugging == true) {
				if (!empty($specialchars) && $specialchars == true) {
					echo '<pre>' . print_r(htmlspecialchars($var), true) . '</pre>';
				} else {
					echo '<pre>' . print_r($var, true) . '</pre>';
				}
			}

			if ($output == false) {
				$debug = ob_get_clean();
				ob_end_clean();
				return $debug;
			}

			return true;
		}

		function debug_trace($var = null) {
			$this -> debug($var);
			$this -> debug(array_reverse(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)));
		}

		function add_option($name = null, $value = null) {
			global $wpml_add_option_count;
			$value = apply_filters('newsletters_add_option', $value, $name);
			if (add_option($this -> pre . $name, $value)) {
				$wpml_add_option_count++;
				return true;
			}

			return false;
		}

		function update_option($name = null, $value = null) {
			$name = sanitize_text_field($name);
		    $value = map_deep($value, 'wp_kses_post');

			$value = apply_filters('newsletters_update_option', $value, $name);
			if (update_option($this -> pre . $name, $value)) {
				return true;
			}

			return false;
		}

		function get_managementpost($permalink = false, $autocreate = false, $language = null) {
			global $wpdb, $wp_rewrite, $newsletters_managementpost_error;
			$user_id = get_current_user_id();
			require_once(ABSPATH . WPINC . DS . 'rewrite.php');
			if (!is_object($wp_rewrite)) { $wp_rewrite = new WP_Rewrite(); }

			$language = (empty($language)) ? $this -> language_default() : $language;
			$managementpost = $this -> language_use($language, $this -> get_option('managementpost'));
			$managementpost = apply_filters('wpml_object_id', (int) $managementpost, 'page', true);
			
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . esc_sql($managementpost) . "' AND `post_status` = 'publish'";

			$query_hash = md5($query);
			if ($ob_post = $this -> get_cache($query_hash)) {
				$post = $ob_post;
			} else {
				$post = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $post);
			}

			if (empty($managementpost) || !$post || !get_post($managementpost)) {
				if ($autocreate == true) {
					$postdata = array(
						'post_title'			=>	__('Manage Subscriptions', 'wp-mailinglist'),
						'post_content'			=>	__('[newsletters_management]', 'wp-mailinglist'),
						'post_type'				=>	"page",
						'post_status'			=>	"publish",
						'post_author'			=>	$user_id,
						'comment_status'		=>	"closed",
						'ping_status'			=>	"closed",
					);

					$post_id = wp_insert_post($postdata);
					update_option($this -> pre . 'managementpost', $post_id);

					if ($permalink == true) {
						return get_permalink($post_id);
					} else {
						return $post_id;
					}
				} else {
					if (is_admin() && !defined('DOING_AJAX')) {
						if (!$newsletters_managementpost_error) {
							$error = sprintf(__('Newsletter plugin subscriber management post/page does not exist %s', 'wp-mailinglist'), '<a href="' . wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> settings . '&method=managementpost'), $this -> sections -> settings . '_managementpost') . '" class="button button-secondary">' . __('please create it now', 'wp-mailinglist') . '</a>');
							$this -> render_warning($error);
							$newsletters_managementpost_error = true;
						}
					}
				}
			} else {
				if ($permalink == true) {
					return get_permalink($post -> ID);
				} else {
					return $post -> ID;
				}
			}

			return false;
		}

		function get_imagespost() {
			global $wpdb;
			$user_id = get_current_user_id();

			$imagespost = get_option($this -> pre . 'imagespost');
			$query = "SELECT `ID` FROM `" . $wpdb -> posts . "` WHERE `ID` = '" . esc_sql($imagespost) . "'";

			$query_hash = md5($query);
			if ($ob_post = $this -> get_cache($query_hash)) {
				$post = $ob_post;
			} else {
				$post = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $post);
			}

			if (empty($imagespost) || !$post) {
				$postdata = array(
					'post_title'			=>	__('Newsletter Images (do not remove)', 'wp-mailinglist'),
					'post_content'			=>	__('This is a placeholder for the Newsletter plugin images. You may edit and reuse this post but do not remove it.', 'wp-mailinglist'),
					'post_type'				=>	"post",
					'post_status'			=>	"draft",
					'post_author'			=>	$user_id,
				);

				$post_id = wp_insert_post($postdata);
				update_option($this -> pre . 'imagespost', $post_id);
				return $post_id;
			} else {
				return $post -> ID;
			}

			return false;
		}

		function get_option($name = null, $stripslashes = true) {
			switch ($name) {
				case 'imagespost'			:
					if ($imagespost = $this -> get_imagespost()) {
						$this -> update_option('imagespost', $imagespost);
					}
					break;
			}

			/* if ($option = $this -> get_cache($name, 'option')) {
				return $option;
			} */

			if ($option = get_option($this -> pre . $name)) {
				if (maybe_unserialize($option) !== false) {
					$option = maybe_unserialize($option);
				}

				if ($stripslashes == true) {
					$option = stripslashes_deep($option);
				}

				${'newsletters_option_' . $name} = $option;
				$option = apply_filters('newsletters_get_option', $option, $name);
				$this -> set_cache($name, $option, 'option');
				return $option;
			}

			return false;
		}

		function delete_option($name = null) {
			if (!empty($name)) {
				if (delete_option($this -> pre . $name)) {
					return true;
				}
			}

			return false;
		}

		function ajax_latestposts_settings() {
			
			check_ajax_referer('latestposts_settings', 'security');

			if (current_user_can('newsletters_settings')) {
				$this -> render('metaboxes' . DS . 'settings-latestposts', false, true, 'admin');
			} else {
				esc_html_e('You do not have permission', 'wp-mailinglist');
			}

			exit();
			die();
		}

		function ajax_latestposts_delete() {
			
			check_ajax_referer('latestposts_delete', 'security');

			if (current_user_can('newsletters_settings')) {
				$id = sanitize_text_field(wp_unslash($_GET['id']));
				if (!empty($id)) {
					if ($this -> Latestpostssubscription() -> delete($id)) {
						echo 'success';
					}
				}
			}

			exit();
			die();
		}
		
		function ajax_latestposts_clearhistory() {
			
			check_ajax_referer('latestposts_clearhistory', 'security');
			
			$success = false;

			if (current_user_can('newsletters_settings')) {
				$id = sanitize_text_field(wp_unslash($_POST['id']));
				if (!empty($id)) {
					if ($this -> Latestpost() -> delete_all(array('lps_id' => $id))) {
						$success = true;
					} else {
						$success = false;
					}
				} else {
					$success = false;
				}
			}
			
			echo wp_json_encode(array('success' => $success));

			exit();
			die();
		}

		function ajax_load_new_editor() {
			define('DOING_AJAX', true);

			wp_enqueue_script('jquery');
			//meta boxes
			wp_enqueue_script('common', false, false, false, true);
			wp_enqueue_script('wp-lists', false, false, false, true);
			wp_enqueue_script('postbox', false, false, false, true);
			//editor
			wp_enqueue_script('editor', false, false, false, true);
			wp_enqueue_script('quicktags', false, false, false, true);
			wp_enqueue_script('wplink', false, false, false, true);
			wp_enqueue_script('wpdialogs-popup', false, false, false, true);
			wp_enqueue_style('wp-jquery-ui-dialog', false, false, false, true);
			wp_enqueue_script('word-count', false, false, false, true);
			wp_enqueue_script('media-upload', false, false, false, true);
			wp_admin_css();
			wp_enqueue_script('utils', false, false, false, true);

			?>

			<div class="postbox" id="contentareabox<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))); ?>">
				<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><span><?php esc_html_e('Content Area', 'wp-mailinglist'); ?> <?php echo wp_kses_post(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))); ?></span></h3>
					<div class="inside">

					<?php

					wp_editor("", 'contentarea' . esc_attr(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))), array(
						'textarea_name'				=>	'contentarea[' . esc_attr(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))) . ']',
					));

					?>

					<table id="post-status-info" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<td id="wp-word-count">
									<span id="word-count"><code>[newsletters_content id="<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))); ?>"]</code></span>
								</td>
								<td class="autosave-info">
									<span id="autosave" style="display:none;"></span>
								</td>
							</tr>
						</tbody>
					</table>
					<p><a href="" onclick="if (confirm('<?php esc_html_e('Are you sure you want to remove this content area?', 'wp-mailinglist'); ?>')) { deletecontentarea('<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['contentarea']))); ?>', ''); } return false;" class="button button-secondary"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a></p>
				</div>
			</div>

			<?php

			exit();
			die();
		}

		function ajax_latestposts_changestatus() {
			
			check_ajax_referer('latestposts_changestatus', 'security');

			$success = false;

			if (current_user_can('newsletters_settings')) {
				if (!empty($_POST['id']) && !empty($_POST['status'])) {
					$status = sanitize_text_field(wp_unslash($_POST['status']));
					$id = sanitize_text_field(wp_unslash($_POST['id']));
					if ($this -> Latestpostssubscription() -> save_field('status', $status, array('id' => $id))) {
						$success = __('Status has been changed', 'wp-mailinglist');
					}
				}
			}

			$this -> render('metaboxes' . DS . 'settings-latestposts', array('success' => $success), true, 'admin');

			exit();
			die();
		}

		function ajax_latestposts_save() {
			$ajax = false;
			$success = false;
			if (!empty($_POST)) {
				check_ajax_referer('newsletters_latestposts_save');
				$ajax = true;

				if (current_user_can('newsletters_settings')) {
					foreach ($_POST as $pkey => $pval) {
						if (!empty($pval) && is_array($pval)) {
							$_POST[$pkey] = maybe_serialize($pval);
						}
					}
	
					if ($this -> Latestpostssubscription() -> save(map_deep(wp_unslash($_POST), 'sanitize_text_field'))) {
						$success = true;
					} else {
						$success = false;
					}
	
					$latestpostssubscription = $this -> Latestpostssubscription() -> data;
				} else {
					$success = false;
				}
			} else {
				$id = sanitize_text_field($_GET['id']);
				if (!empty($id)) {
					$latestpostssubscription = $this -> Latestpostssubscription() -> find(array('id' => $id));
				}
			}

			$this -> render('latestposts-save', array('latestpostssubscription' => $latestpostssubscription, 'errors' => $this -> Latestpostssubscription() -> errors, 'success' => $success, 'ajax' => $ajax), true, 'admin');

			exit();
			die();
		}

		function ajax_latestposts_preview() {
			
			check_ajax_referer('latestposts_preview', 'security');

			if (current_user_can('newsletters_settings')) {
				$id = sanitize_text_field($_GET['id']);
		    	if ($content = $this -> latestposts_hook($id, true)) {
					echo $content;
				} else {
					echo __('Latest posts preview could not load', 'wp-mailinglist');
				}
			} else {
				echo __('You do not have permission', 'wp-mailinglist');
			}

			exit(); die();
		}

		function ajax_lpsposts() {
			
			check_ajax_referer('lpsposts', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(__('You do not have permission', 'wp-mailinglist'));
			}

			global $Db;

			$conditions = array();
			$id = (int) sanitize_text_field(wp_unslash($_GET['id']));
			
			if (!empty($id)) {
				$conditions['lps_id'] = $id;
				$latestpostssubscription = $this -> Latestpostssubscription() -> find(array('id' => $id));
			}

			$posts = $this -> Latestpost() -> find_all($conditions, null, array('created', "DESC"));

			$this -> render('posts', array('posts' => $posts, 'latestpostssubscription' => $latestpostssubscription), true, 'admin');

			exit(); die();
		}

		function ajax_delete_lps_post() {
			
			check_ajax_referer('delete_lps_post', 'security');
			
			if (!current_user_can('newsletters_settings')) {
				wp_die(wp_kses_post(__('You do not have permission', 'wp-mailinglist')));
			}

			global $Db;

			if (!empty($_POST['id'])) {
				$this -> Latestpost() -> delete(sanitize_text_field(wp_unslash($_POST['id'])));
			}

			exit(); die();
		}

		function get_latestposts_used($latestpostssubscription = null) {
			global $wpdb, $Db;

			if (!empty($latestpostssubscription)) {
				$count = $this -> Latestpost() -> count(array('lps_id' => $latestpostssubscription -> id));
				return $count;
			}

			return 0;
		}

		function get_latestposts($latestpostssubscription = null) {
			global $wpdb, $post, $Db, $Html, $Mailinglist, $Subscriber, $SubscribersList;
			$post_criteria = false;

			if (!empty($latestpostssubscription)) {
				$exclude = array();
				if (!empty($latestpostssubscription -> exclude)) {
					if (($exclude = @explode(",", $latestpostssubscription -> exclude)) !== false) {
						//exclude array exists…
						foreach ($exclude as $exkey => $exval) {
							$exclude[$exkey] = trim($exval);
						}
					}
				}

				$order = (!empty($latestpostssubscription -> order)) ? $latestpostssubscription -> order : "DESC";
				$orderby = (!empty($latestpostssubscription -> orderby)) ? $latestpostssubscription -> orderby : "date";

				$post_criteria = array(
					'numberposts'			=>	$latestpostssubscription -> number,
					'orderby'				=>	$orderby,
					'order'					=>	$order,
					'exclude'				=>	$exclude,
					'post_type'				=>	"post",
					'post_status'			=>	"publish",
				);
				
				if (!empty($latestpostssubscription -> categories) && $latestpostssubscription -> categories != "all") {
					$post_criteria['category'] = @implode(",", maybe_unserialize($latestpostssubscription -> categories));
				}

				if (!empty($latestpostssubscription -> takefrom)) {
					if ($latestpostssubscription -> takefrom == "posttypes") {
						$post_criteria['category'] = 0;
						$post_criteria['post_type'] = maybe_unserialize($latestpostssubscription -> posttypes);
					} elseif ($latestpostssubscription -> takefrom == "pages") {
						$post_criteria['post_type'] = "page";
					}
				}

				$latestpostsquery = "SELECT id, post_id FROM " . $wpdb -> prefix . $this -> Latestpost() -> table . " WHERE `lps_id` = '" . $latestpostssubscription -> id . "'";
				$latestposts = $wpdb -> get_results($latestpostsquery);

				if (!empty($latestposts)) {
					foreach ($latestposts as $latestpost) {
						if (!empty($post_criteria['exclude'])) {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						} else {
							$post_criteria['exclude'][] = $latestpost -> post_id;
						}
					}
				}

				$olderthanquery = "SELECT ID FROM " . $wpdb -> posts . " WHERE post_date < '" . date_i18n("Y-m-d H:i:s", strtotime($latestpostssubscription -> olderthan)) . "'";
				$olderthan = $wpdb -> get_results($olderthanquery);

				if (!empty($olderthan)) {
					foreach ($olderthan as $olderthanpost) {
						$post_criteria['exclude'][] = $olderthanpost -> ID;
					}
				}

				$post_criteria['suppress_filters'] = 0;
			}

			return apply_filters('newsletters_latest_posts_criteria', $post_criteria);
		}

		function updating_plugin() {
			if (!is_admin() && !defined('DOING_AJAX')) return;
			
			if (defined('NEWSLETTERS_UPDATING')) {
				return;
			}
			
			define('NEWSLETTERS_UPDATING', true);

			$dbversion = $this -> get_option('dbversion');
			if (empty($dbversion)) {
				$this -> add_option('dbversion', $this -> dbversion);
			}

			if (!$this -> get_option('version')) {
				$this -> add_option('version', $this -> version);
				$this -> update_options();
				return;
			}

			$cur_dbversion = $this -> get_option('dbversion');
			if (version_compare($cur_dbversion, $this -> dbversion) < 0) {
				$this -> update_option('showmessage_dbupdate', true);
			}

			$cur_version = $this -> get_option('version');
			$version = $this -> version;

			if (version_compare($this -> version, $cur_version) === 1) {
				if (version_compare("3.8.4", $cur_version) === 1) {
					if (!empty($this -> classes)) {
						global $wpdb;
						$this -> update_options();
						$this -> initialize_classes();

						foreach ($this -> classes as $class_name) {
							global ${$class_name};

							$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` ENGINE=MyISAM AUTO_INCREMENT=1 CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
							$wpdb -> query($query);

							if (!empty(${$class_name} -> tv_fields)) {
								foreach (${$class_name} -> tv_fields as $table_field_name => $table_field_attributes) {
									if (!empty($table_field_name) && $table_field_name != "key") {
										if (!preg_match("/(INT|DATETIME)/si", $table_field_attributes[0])) {
											$query = "ALTER TABLE `" . $wpdb -> prefix . "" . $this -> pre . "" . ${$class_name} -> controller . "` CHANGE `" . $table_field_name . "` `" . $table_field_name . "` " . $table_field_attributes[0] . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci " . $table_field_attributes[1] . ";";
											$wpdb -> query($query);
										}
									}
								}
							}
						}

						global $wpdb, $Theme;
						$wpdb -> flush();

						$db_queries = array(
							"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> History() -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> Template() -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
							"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
						);

						foreach ($db_queries as $db_query) {
							$wpdb -> query($db_query);
						}
					}

					$version = "3.8.4";
				} elseif (version_compare("3.8.4.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.4.1";
				} elseif (version_compare("3.8.5.1", $cur_version) === 1) {
					$this -> update_options();
					$version = "3.8.5.1";
				} elseif (version_compare("3.8.6", $cur_version) === 1) {
					$this -> update_options();

					/* Currencies */
					global $currencies;
					require_once $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
					$this -> update_option('currencies', $currencies);

					/* Permissions */
					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['autoresponders'] = 10;
					$this -> update_option('permissions', $permissions);

					$this -> update_option('sendnewsletteronsubscribe', "N");
					$version = "3.8.6";
				} elseif (version_compare("3.8.7", $cur_version) === 1) {
					$this -> update_options();

					$permissions = maybe_unserialize($this -> get_option('permissions'));
					$permissions['groups'] = "10";
					$this -> update_option('permissions', $permissions);
					$this -> get_managementpost();

					global $wpdb, $Theme;
					$wpdb -> flush();

					$db_queries = array(
						"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> History() -> table . "` CHANGE `message` `message` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $this -> Template() -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . "" . $Theme -> table . "` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL",
						"ALTER TABLE `" . $wpdb -> prefix . $this -> Autoresponderemail() -> table . "` ADD UNIQUE INDEX (subscriber_id, autoresponder_id)",
					);

					foreach ($db_queries as $db_query) {
						$wpdb -> query($db_query);
					}

					$version = '3.8.7';
				} elseif (version_compare("3.8.7.2", $cur_version) === 1) {
					$this -> update_options();
					$version = '3.8.7.2';
				}

				if (version_compare($cur_version, "3.8.9.2") < 0) {
					$this -> update_options();
					$this -> initialize_default_themes();

					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							echo do_shortcode(wp_unslash($theme -> content));
							$newcontent = ob_get_clean();

							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";
							$wpdb -> query($themequery);
						}
					}

					$version = "3.8.9.2";
				}

				if (version_compare($cur_version, "3.8.9.4") < 0) {
					global $wpdb, $Mailinglist;
					$this -> update_options();

					$intervals = array(
						'daily'			=>	__('Daily', 'wp-mailinglist'),
						'weekly'		=>	__('Weekly', 'wp-mailinglist'),
						'monthly'		=>	__('Monthly', 'wp-mailinglist'),
						'2months'		=>	__('Every Two Months', 'wp-mailinglist'),
						'3months'		=>	__('Every Three Months', 'wp-mailinglist'),
						'biannually'	=>	__('Twice Yearly (Six Months)', 'wp-mailinglist'),
						'9months'		=>	__('Every Nine Months', 'wp-mailinglist'),
						'yearly'		=>	__('Yearly', 'wp-mailinglist'),
						'once'			=>	__('Once Off', 'wp-mailinglist'),
					);

					$this -> update_option('intervals', $intervals);

					$query = "ALTER TABLE `" . $wpdb -> prefix . $Mailinglist -> table . "` CHANGE `interval` `interval` ENUM('daily', 'weekly', 'monthly', '2months', '3months', 'biannually', '9months', 'yearly', 'once') NOT NULL DEFAULT 'monthly';";
					$wpdb -> query($query);

					$version = "3.8.9.4";
				}

				if (version_compare($cur_version, "3.9") < 0) {
					$this -> update_options();
					$this -> initialize_default_themes();

					global $wpdb, $Db, $Theme;
					$themesquery = "SELECT * FROM " . $wpdb -> prefix . $Theme -> table . "";
					if ($themes = $wpdb -> get_results($themesquery)) {
						foreach ($themes as $theme) {
							$newcontent = "";
							ob_start();
							$newcontent = ob_get_clean();
							$themequery = "UPDATE `" . $wpdb -> prefix . $Theme -> table . "` SET `content` = '" . esc_sql($newcontent) . "' WHERE `id` = '" . $theme -> id . "'";
							//$wpdb -> query($themequery);
						}
					}

					$version = "3.9";
				}

				if (version_compare($cur_version, "3.9.4") < 0) {
					global $wpdb;
					$this -> update_options();

					/* convert database tables to MyISAM */
					if (!empty($this -> tablenames)) {
						foreach ($this -> tablenames as $tablename) {
							$query = "ALTER TABLE `" . $tablename . "` ENGINE=MyISAM;";
							$wpdb -> query($query);
						}
					}

					/* Auto import WordPress users */
					if ($importuserslist = $this -> get_option('importuserslist')) {
						$this -> update_option('importuserslists', array($importuserslist));
					}

					$version = "3.9.4";
				}

				if (version_compare($cur_version, "3.9.9") < 0) {
					global $wpdb, $Field;
					$this -> update_options();
					$wpdb -> query("ALTER TABLE `" . $wpdb -> prefix . $Field -> table . "` CHANGE `type` `type` VARCHAR(255) NOT NULL DEFAULT 'text'");
					$version = "3.9.9";
				}

				if (version_compare($cur_version, "4.4.4") < 0) {
					$this -> update_options();

					$latestposts = $this -> get_option('latestposts');
					if (!empty($latestposts) && $latestposts == "Y") {
						$latestpostssubscription = array(
							'subject'				=>	$this -> get_option('latestposts_subject'),
							'number'				=>	$this -> get_option('latestposts_number'),
							'language'				=>	$this -> get_option('latestposts_language'),
							'takefrom'				=>	$this -> get_option('latestposts_takefrom'),
							'posttypes'				=>	maybe_serialize($this -> get_option('latestposts_posttypes')),
							'categories'			=>	maybe_serialize($this -> get_option('latestposts_categories')),
							'groupbycategory'		=>	$this -> get_option('latestposts_groupbycategory'),
							'exclude'				=>	$this -> get_option('latestposts_exclude'),
							'order'					=>	$this -> get_option('latestposts_order'),
							'orderby'				=>	$this -> get_option('latestposts_orderby'),
							'olderthan'				=>	$this -> get_option('latestposts_olderthan'),
							'lists'					=>	maybe_serialize($this -> get_option('latestposts_lists')),
							'startdate'				=>	$this -> get_option('latestposts_startdate'),
							'interval'				=>	$this -> get_option('latestposts_interval'),
							'theme_id'				=>	$this -> get_option('latestposts_theme'),
						);

						$this -> Latestpostssubscription() -> save($latestpostssubscription);
						$this -> latestposts_scheduling($latestpostssubscription['interval'], $latestpostssubscription['startdate'], array((int) $this -> Latestpostssubscription() -> insertid));
					}

					// Set the 'rel_id' field on fieldslists table as AUTO_INCREMENT
					global $wpdb, $Db, $FieldsList;
					$query = "ALTER TABLE " . $wpdb -> prefix . $this -> Latestpost() -> table . " CHANGE `post_id` `post_id` INT(11) NOT NULL DEFAULT '0'";
					$wpdb -> query($query);
					$query = "ALTER TABLE " . $wpdb -> prefix . $FieldsList -> table . " CHANGE `rel_id` `rel_id` INT(11) NOT NULL AUTO_INCREMENT";
					$wpdb -> query($query);

					$version = '4.4.4';
				}

				if (version_compare($cur_version, "4.4.6.1") < 0) {
					global $wpdb, $Db;

					$this -> update_options();

					$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> Latestpost() -> table . "` DROP INDEX `post_id`";
					$wpdb -> query($query);

					$version = '4.4.6.1';
				}

				if (version_compare($cur_version, "4.5.4.2") < 0) {
					global $wpdb;
					$this -> update_options();

					//update the theme folder to default
					$this -> update_option('theme_folder', "default");
					$this -> theme_folder_functions();
					include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
					$stylesdone = false;
					$scriptsdone = false;

					if (!empty($defaultstyles)) {
						$loadstyles = array();
						foreach ($defaultstyles as $handle => $style) {
							$loadstyles[] = $handle;
						}
						$this -> update_option('loadstyles', $loadstyles);
						$stylesdone = true;
					}
					if (!empty($defaultscripts)) {
						$loadscripts = array();
						foreach ($defaultscripts as $handle => $script) {
							$loadscripts[] = $handle;
						}
						$this -> update_option('loadscripts', $loadscripts);
						$scriptsdone = true;
					}

					if (!empty($stylesdone) && !empty($scriptsdone)) {
						// all done, update the version
						$version = '4.5.4.2';
					}
				}

				if (version_compare($cur_version, "4.5.5.4.4") < 0) {
					global $wpdb, $Subscriber, $SubscribersList;
					$this -> update_options();

					// Change of 'bootstrap' to 'newsletters-bootstrap' handle
					$this -> theme_folder_functions();
					include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
					$loadstyles = $this -> get_option('loadstyles');
					if (!empty($loadstyles) && in_array('bootstrap', $loadstyles)) {
						$loadstyles[] = 'newsletters-bootstrap';
						$this -> update_option('loadstyles', $loadstyles);
					}

					/* Unique 'email' column stuff */
					$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
					$subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
					// Delete duplicate subscribers and only keep the one with the smallest ID value
					$query = "DELETE n1 FROM `" . $subscribers_table . "` n1, `" . $subscribers_table . "` n2 WHERE n1.id > n2.id AND n1.email = n2.email";
					$wpdb -> query($query);
					// Remove empty associations
					$query = "DELETE FROM `" . $subscriberslists_table . "` WHERE `subscriber_id` NOT IN (SELECT `id` FROM `" . $subscribers_table . "`)";
					$wpdb -> query($query);
					// Make the 'email' column a unique index/key to prevent duplicates
					$query = "ALTER TABLE  `" . $subscribers_table . "` ADD UNIQUE KEY `email_unique` (`email`)";
					$wpdb -> query($query);

					$version = '4.5.5.4.4';
				}

				if (version_compare($cur_version, "4.5.5.9") < 0) {
					$this -> update_options();

					$loadscripts = $this -> get_option('loadscripts');
					$loadscripts[] = 'jquery-form';
					$this -> update_option('loadscripts', $loadscripts);

					$version = "4.5.5.9";
				}

				if (version_compare($cur_version, "4.6.3") < 0) {
					$this -> update_options();

					$paymentmethod = $this -> get_option('paymentmethod');
					$this -> update_option('paymentmethod', array($paymentmethod));

					// Update the "Posts" and "Latest Posts" templates
					include($this -> plugin_base() . DS . 'includes' . DS . 'email-templates.php');

					$current_posts = $this -> get_option('etmessage_posts');
					if (!preg_match("/(newsletters_post_anchor)/si", $current_posts)) {
						$this -> update_option('etmessage_posts_backup', $current_posts);
						$this -> update_option('etmessage_posts', $email_templates['posts']['message']);
					}

					$current_latestposts = $this -> get_option('etmessage_latestposts');
					if (!preg_match("/(newsletters_post_anchor)/si", $current_latestposts)) {
						$this -> update_option('etmessage_latestposts_backup', $current_latestposts);
						$this -> update_option('etmessage_latestposts', $email_templates['latestposts']['message']);
					}

					$version = "4.6.3";
				}
				
				if (version_compare($cur_version, "4.6.4") < 0) {
					
					global $wpdb, $Email, $Subscriber;
					
					$this -> update_options();
					$this -> predefined_templates();
					
					$queries = array();
					
					// Update the read_date column of emails with the modified date where empty
					$queries[] = "UPDATE `" . $wpdb -> prefix . $Email -> table . "` SET `read_date` = `modified` WHERE `read_date` IS NULL;";
					
					// Update messageid database field on emails table to VARCHAR for indexing
					$this -> change_field($Email -> table, 'messageid', 'messageid', "VARCHAR(255) NOT NULL DEFAULT ''");
					
					// Change all tables and their fields to utf8mb4_unicode_ci collation
					if (!empty($this -> tables_tv)) {
						foreach ($this -> tables_tv as $tablename => $tablefields) {
							$queries[] = "ALTER TABLE `" . $wpdb -> prefix . $tablename . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
							
							foreach ($tablefields as $field => $attributes) {
								if (!empty($field) && $field != "key") {
									if (!preg_match("/(INT|DATETIME|FLOAT)/si", $attributes[0])) {
										$queries[] = "ALTER TABLE `" . $wpdb -> prefix . $tablename . "` CHANGE `" . $field . "` `" . $field . "` " . $attributes[0] . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci " . $attributes[1] . ";";
									}
								}
							}
						}
					}
					
					if (!empty($queries)) {
						foreach ($queries as $query) {
							$this -> dbupdate_process -> push_to_queue($query);
						}
						
						$this -> dbupdate_process -> save();
						$this -> dbupdate_process -> dispatch();
					}
					
					$version = '4.6.4';
				}
				
				if (version_compare($cur_version, "4.6.6.1") < 0) { 
					$this -> update_options();
					$this -> initialize_default_themes();
					
					// Update the default styling settings on all forms
					if ($forms = $this -> Subscribeform() -> find_all()) {
						foreach ($forms as $form) {
							$formstyling = maybe_unserialize($form -> styling);
							$defaults = $this -> Subscribeform() -> defaults();
							$defaultstyling = $defaults['styling'];
							
							if (empty($formstyling)) {
								$this -> Subscribeform() -> save_field('styling', maybe_serialize($defaultstyling), array('id' => $form -> id));
							} else {
								foreach ($defaultstyling as $stylekey => $stylevalue) {
									if (!isset($formstyling[$stylekey])) {
										$formstyling[$stylekey] = $stylevalue;
									}
								}
								$this -> Subscribeform() -> save_field('styling', maybe_serialize($formstyling), array('id' => $form -> id));
							}
						}
					}
					
					$version = '4.6.6.1';
				}
				
				if (version_compare($cur_version, "4.6.6.2") < 0) { 
					$this -> update_options();
					
					$loadscripts = $this -> get_option('loadscripts');
					$loadscripts[] = 'recaptcha';
					$this -> update_option('loadscripts', $loadscripts);
					
					global $wpdb, $Theme;
					$query = "ALTER TABLE `" . $wpdb -> prefix . $Theme -> table . "` CHANGE `type` `type` ENUM('upload','paste','builder') NOT NULL DEFAULT 'paste';";
					$wpdb -> query($query);
					
					$version = '4.6.6.2';
				}
				
				if (version_compare($cur_version, "4.6.22") < 0) {
					$this -> update_options();
					$this -> check_tables();
					$this -> initialize_default_themes();
					
					// Insert mailing list slugs
					global $Mailinglist, $Db, $Html;
					if ($mailinglists = $Mailinglist -> select()) {
						foreach ($mailinglists as $list_id => $list_title) {
							$slug = $Html -> sanitize($list_title);
							$Db -> model = $Mailinglist -> model;
							$Db -> save_field('slug', $slug, array('id' => $list_id));
						}
					}
					
					// Reload the countries with country codes
					global $wpdb;
					$query = "TRUNCATE TABLE `" . $wpdb -> prefix . $this -> Country() -> table . "`";
					$wpdb -> query($query);
					include($this -> plugin_base() . DS . 'vendors' . DS . 'sql.countries.php');
					$wpdb -> query($countriesquery);
					
					$version = '4.7.1';
				}

				if (version_compare($cur_version, "4.7.9.2") < 0) {
                    
					if (empty(get_option('unsubscribewpuserdelete'))) {
						$this -> add_option('unsubscribewpuserdelete', 'N');
					}
					
					$version = '4.7.9.2';
                }


				if (version_compare($cur_version, "4.7.9.7") < 0) {

                    if (empty(get_option('unsubscribewpuserdeletebyuser'))) {
                        $this -> add_option('unsubscribewpuserdeletebyuser', 'N');
                    }

                    $version = '4.7.9.7';
                }

                if (version_compare($cur_version, "4.8.1") < 0) {
                    global $wpdb;
					$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> History() -> table . "`
						ADD COLUMN builderon LONGTEXT NULL AFTER modified ";
					$wpdb -> query($query);

					$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> History() -> table . "`
                        ADD COLUMN using_grapeJS INT(1) NOT NULL DEFAULT '0'";
                    $wpdb -> query($query);


                    $version = '4.8.1';
                }

				 if (version_compare($cur_version, "4.8.2") < 0) {
                    global $wpdb;
                    $query = "Delete from  `" . $wpdb -> prefix .  "options` where `option_name` like '_transient_%process_lock'";
                    $wpdb -> query($query);

                    $query = "Delete from  `" . $wpdb -> prefix .  "options` where `option_name` like '_transient_%newsletter%'";
                    $wpdb -> query($query);

                    $version = '4.8.2';
                }


                if (version_compare($cur_version, "4.8.5") < 0) {
                    global $wpdb;

					$query = "ALTER TABLE `" . $wpdb -> prefix . $this -> History() -> table . "`
                            ADD COLUMN grapejs_content LONGTEXT NULL AFTER builderon";
					$wpdb -> query($query);


                    $version = '4.8.5';
                }



                if (version_compare($cur_version, $this->version) < 0) {
				    $version = $this->version;
				}

				//the current version is older.
				//let's update the database
				$this -> update_option('version', $version);
			}
		}

		function update_options() {
			if (!is_admin() && !defined('DOING_AJAX')) return;
			$this -> check_tables();
			$this -> get_managementpost(false, true);

			global $wpml_add_option_count, $wpdb, $Field, $Theme;
			$wpml_add_option_count = 0;

			$options = array();
			$options['defaulttemplate'] = true;
			$options['old_folder_name'] = "newsletters-lite";
			$options['screenoptions_subscribers_custom'] = array('gravatars', 'device');
			
			// Import settings
			$options['import_notification'] = true;
			$options['import_createfieldoptions'] = true;
			
			$options['managementloginsubject'] = __('Authenticate Subscriber Account', 'wp-mailinglist');
			$options['authenticatelinktext'] = __('Authenticate now', 'wp-mailinglist');
			$options['managementauthtype'] = 3;
			$options['managementallowemailchange'] = "Y";
			$options['managementformatchange'] = "Y";
			$options['managementallownewsubscribes'] = "Y";
			$options['managementshowsubscriptions'] = "Y";
			$options['managementdelete'] = true;
			$options['managementcustomfields'] = "Y";
			$options['cookieformat'] = "D, j M Y H:i:s";
			$options['defaultlistcreated'] = "N";
			$options['subscriptionmessage'] = __('Subscription Successful', 'wp-mailinglist');
			$options['sendingprogress'] = "N";
			$options['createpreview'] = "Y";
			$options['createspamscore'] = "Y";
			$options['emailencoding'] = "8bit";
			$options['clicktrack'] = "Y";
			$options['shortlinks'] = "N";
			$options['theme_folder'] = "default2";
			$options['theme_usestyle'] = "Y";
			$options['customcss'] = "N";
			$options['multimime'] = "N"; //should multi mime (text/html) emails be sent?
			$options['videoembed'] = true;
			$options['mailtype'] = 'mail';
			$options['smtphost'] = 'mail.domain.com';
			$options['smtpport'] = 25;
			$options['smtpsecure'] = "N";
			$options['smtpauth'] = 'N';
			$options['smtpuser'] = __('username', 'wp-mailinglist');
			$options['smtppass'] = __('password', 'wp-mailinglist');
			$options['adminemail'] = get_option('admin_email');
			$options['smtpfrom'] = get_option('admin_email');
			$options['smtpreply'] = get_option('admin_email');
			$options['smtpfromname'] = get_option('blogname');
			$options['dkim'] = "N";
			$options['dkim_domain'] = "domain.com";
			$options['dkim_selector'] = "newsletters";
			$options['tracking'] = "Y";
			$options['tracking_image'] = "invisible";
			$options['tracking_image_alt'] = "track";
			$options['servertype'] = 'cpanel';
	        $options['mailpriority'] = 3; //set the mail priority to "Normal"
			$options['unsubscribeondelete'] = 'N';
			$options['unsubscribeemails'] = "single";
			$options['unsubscribeconfirmation'] = "Y";
			$options['unsubscribecomments'] = "Y";
			$options['registercheckbox'] = 'Y';
			$options['registerformlabel'] = __('Receive news updates via email from this site', 'wp-mailinglist');
			$options['checkboxon'] = 'N';
			$options['autosubscribelist'] = array(1);
			$options['sendonpublish'] = 'Y';
			$options['sendonpublishef'] = 'ep';
			$options['postswpautop'] = true;
			$options['sendonpublishexcerptlength'] = 250;
			$options['resubscribe'] = 1;
			$options['resubscribetext'] = __('resubscribe', 'wp-mailinglist');
			$options['unsubscribetext'] = __('Unsubscribe from this newsletter', 'wp-mailinglist');
			$options['unsubscribealltext'] = __('Unsubscribe from all emails', 'wp-mailinglist');
			$options['unsubscribedelete'] = "N";
			$options['unsubscribewpuserdelete'] = "N";
			$options['unsubscribewpuserdeletebyuser'] = "N";
			$options['saveipaddress'] = true;
			$options['adminemailonsubscription'] = 'Y';
			$options['adminemailonunsubscription'] = 'Y';
			$options['activationlinktext'] = __('Confirm Subscription', 'wp-mailinglist');
			$options['customactivateredirect'] = "N";
			$options['activateredirecturl'] = home_url();
			$options['managelinktext'] = __('Manage Subscriptions', 'wp-mailinglist');
			$options['onlinelinktext'] = __('View in your browser', 'wp-mailinglist');
			$options['printlinktext'] = __('Print Email', 'wp-mailinglist');
			$options['autoresponderscheduling'] = "hourly";
			$options['tinymcebtn'] = "Y";
			$options['sendasnewsletterbox'] = "Y";
			$options['subscriberegister'] = "N";
			$options['custompostslug'] = "newsletter";
			$options['importusers'] = "N";
			$options['importusersscheduling'] = "hourly";
			$options['importuserslists'] = array(1);
			$options['importusersrequireactivate'] = "N";
			$options['subscriptions'] = "Y";
			$options['paidsubscriptionredirect'] = "Y";
			$options['rssfeed'] = "N";
			$options['language_location'] = 'langdir';
			$options['deleteonbounce'] = 'Y';
	        $options['bouncecount'] = 3;
			$options['adminemailonbounce'] = 'Y';
			$options['bounceemail'] = get_option('admin_email');
	        $options['bouncemethod'] = "off";
	        $options['bouncepop_interval'] = "3600";
	        $options['bouncepop_type'] = "imap";
	        $options['bouncepop_host'] = "localhost";
	        $options['bouncepop_user'] = "bounce@domain.com";
	        $options['bouncepop_pass'] = "mailboxpassword";
	        $options['bouncepop_port'] = "110";
	        $options['bouncepop_prot'] = "normal";
			$options['subscriberexistsredirect'] = "management";
			$options['subscriberexistsmessage'] = __('You are already subscribed, redirecting to the management page...', 'wp-mailinglist');
			$options['subscriberexistsredirecturl'] = get_permalink($this -> get_managementpost());
			$options['emailvalidationextended'] = 0;
			$options['requireactivate'] = 'Y';
			$options['activateaction'] = "none";
			$options['activatereminder'] = 3;
			$options['activatedelete'] = 7;
			$options['activationemails'] = "single";
			$options['tcodemo'] = 'N';
			$options['tcovendorid'] = '123456';
			$options['tcosecret'] = __('secretstring', 'wp-mailinglist');
			$options['tcoaccount'] = "live";
			$options['adminordernotify'] = 'Y';
			$options['subscriberedirect'] = "N";
			$options['subscriberedirecturl'] = $this -> get_managementpost(true);
			$options['paymentmethod'] = array('2co', 'paypal');
			$options['csvdelimiter'] = ",";
			$options['captcha_type'] = ($this -> is_plugin_active('captcha')) ? 'rsc' : 'none';
			$options['recaptcha_type'] = "robot";
			$options['recaptcha_theme'] = "light";
			$options['recaptcha_language'] = "en";
			$options['recaptcha_customcss'] = '.recaptcha_widget { margin: 10px 0 15px 0; }
			.recaptcha_widget .recaptcha_image { margin: 10px 0 5px 0; }
			.recaptcha_widget .recaptcha_image img { width: 250px; box-shadow: none; }
			.recaptcha_widget .recaptcha_links { font-size: 85%; }
			.recaptcha_widget .recaptcha_response { }';
			$options['captcha_rgb'] = array(255, 255, 255);
			$options['captcha_bg'] = "#FFFFFF";
			$options['captcha_fg'] = "#333333";
			$options['farbtastic_fg'] = "#000000";
			$options['captcha_size'] = array('w' => 72, 'h' => 24);
			$options['captcha_chars'] = "4";
			$options['captcha_font'] = "14";
			$options['captchainterval'] = "hourly";
			$this -> captchacleanup_scheduling();
			$options['commentformcheckbox'] = "Y";
			$options['commentformlabel'] = __('Receive news updates via email from this site', 'wp-mailinglist');
			$options['commentformautocheck'] = "N";
			$options['commentformlist'] = "1";
			$options['excerpt_settings'] = 1;
			$options['excerpt_length'] = 55;
			$options['excerpt_more'] = __('Read more', 'wp-mailinglist');
			$options['timezone_set'] = 0;
			$options['croninterval'] = "5minutes";
			
			// Email scheduling settings
			$options['scheduleinterval'] = "1minute";
			$options['scheduleintervalseconds'] = 60;
			$options['emailsperinterval'] = 99;
			
			$options['paypalemail'] = get_option('admin_email');
			$options['paypalsubscriptions'] = "N";
			$options['paypalsandbox'] = "N";
			$options['paypalliveurl'] = "https://www.paypal.com/cgi-bin/webscr";
			$options['paypalsandurl'] = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$options['countriesinserted'] = "N";
			$options['generalredirect'] = $this -> get_managementpost(true);
			$options['offsitetitle'] = get_bloginfo('name');
			$options['offsitelist'] = 'checkboxes';
			$options['offsitewidth'] = 400;
			$options['offsiteheight'] = 300;
			$options['offsitebutton'] = __('Subscribe Now', 'wp-mailinglist');
			$options['currency'] = 'USD';
			include $this -> plugin_base() . DS . 'includes' . DS . 'currencies.php';
			$options['currencies'] = $currencies;

			$intervals = array(
				'daily'			=>	__('Daily', 'wp-mailinglist'),
				'weekly'		=>	__('Weekly', 'wp-mailinglist'),
				'monthly'		=>	__('Monthly', 'wp-mailinglist'),
				'2months'		=>	__('Every Two Months', 'wp-mailinglist'),
				'3months'		=>	__('Every Three Months', 'wp-mailinglist'),
				'biannually'	=>	__('Twice Yearly (Six Months)', 'wp-mailinglist'),
				'9months'		=>	__('Every Nine Months', 'wp-mailinglist'),
				'yearly'		=>	__('Yearly', 'wp-mailinglist'),
				'once'			=>	__('Once Off', 'wp-mailinglist'),
			);

			$options['intervals'] = maybe_serialize($intervals);

			$embed = array(
				'acknowledgement'		=>	__('Thank you for subscribing.', 'wp-mailinglist'),			//default acknowledgement message
				'subtitle'				=>	__('Subscribe to our newsletter.', 'wp-mailinglist'),		//subtitle of the subscription form
				'subscribeagain'		=>	"N",															//show a "Subscribe again" link?
				'ajax'					=>	"N",															//turn on Ajax features?
				'button'				=>	__('Subscribe Now', 'wp-mailinglist'),						//button text
				'scroll'				=>	"Y",															//scroll to the subscription form?
				'captcha'				=>	"N",															//security captcha image?
			);

			if ($this -> language_do()) {
				foreach ($embed as $ekey => $eval) {
						$embed[$ekey] = array();
						$embed[$ekey][$this -> language_default()] = $eval;
					}
				}

			$options['embed'] = $embed;

			$poststatuses = array(
				'publish'			=>	__('Published', 'wp-mailinglist'),
				'pending'			=>	__('Pending', 'wp-mailinglist'),
				'draft'				=>	__('Draft', 'wp-mailinglist'),
				'private'			=>	__('Private', 'wp-mailinglist')
			);

			$options['poststatuses'] = $poststatuses;

			// API Stuff
			// phpcs:ignore
			$options['api_key'] = strtoupper(md5($_SERVER['SERVER_NAME']));

			foreach ($options as $okey => $oval) {
				$this -> add_option($okey, $oval);
			}

			$this -> theme_folder_functions();
			// Styles & Scripts
			include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
			if (!empty($defaultstyles)) {
				$loadstyles = array();
				foreach ($defaultstyles as $handle => $style) {
					$loadstyles[] = $handle;
				}
				$this -> add_option('loadstyles', $loadstyles);
			}
			if (!empty($defaultscripts)) {
				$loadscripts = array();
				foreach ($defaultscripts as $handle => $script) {
					$loadscripts[] = $handle;
				}
				$this -> add_option('loadscripts', $loadscripts);
			}

			// Scheduled tasks
			$ratereview_scheduled = $this -> get_option('ratereview_scheduled');
			if (empty($ratereview_scheduled)) {
				wp_schedule_single_event(strtotime("+30 day"), 'newsletters_ratereviewhook', array(30));
				wp_schedule_single_event(strtotime("+60 day"), 'newsletters_ratereviewhook', array(60));
				wp_schedule_single_event(strtotime("+90 day"), 'newsletters_ratereviewhook', array(90));
				$this -> update_option('ratereview_scheduled', true);
			}
			
			wp_schedule_single_event(time(), 'newsletters_upgradehook', array(1));
			wp_schedule_single_event(strtotime("+7 day"), 'newsletters_upgradehook', array(7));
			wp_schedule_single_event(strtotime("+14 day"), 'newsletters_upgradehook', array(14));

			$this -> get_imagespost();
			$this -> get_managementpost();

			$themesquery = "SELECT `id` FROM `" . $wpdb -> prefix . $Theme -> table . "` LIMIT 1";
			$themes = $wpdb -> get_results($themesquery);
			if (empty($themes)) { $this -> initialize_default_themes(); }

			$permissions = $this -> get_option('permissions');
			if (empty($permissions)) { $this -> init_roles(); }
			$this -> check_roles();

			$this -> qp_scheduling();
			$this -> scheduling();
			$this -> countries_scheduling();
			$this -> optimize_scheduling();
			$this -> emailarchive_scheduling();
			$this -> autoresponder_scheduling();
			$this -> init_fieldtypes();
			$this -> predefined_templates();

			$Field -> check_default_fields();
			return $wpml_add_option_count;
		}

		function predefined_templates() {
			require_once $this -> plugin_base() . DS . 'includes' . DS . 'email-templates.php';

			if (!empty($email_templates)) {
				foreach ($email_templates as $etk => $email_template) {
					$this -> add_option('etsubject_' . $etk, $email_template['subject']);
					$this -> add_option('etmessage_' . $etk, $email_template['message']);
				}
			}
		}

		function init_fieldtypes() {
			global $wpdb;

			$fieldtypes = array(
				'hidden'		=> 	__('Hidden', 'wp-mailinglist'),
				'special'		=>	__('Special', 'wp-mailinglist'),
				'text'			=>	__('Text Field', 'wp-mailinglist'),
				'textarea'		=>	__('Text Area', 'wp-mailinglist'),
				'select'		=>	__('Select Drop Down', 'wp-mailinglist'),
				'radio'			=>	__('Radio Buttons', 'wp-mailinglist'),
				'checkbox'		=>	__('Checkboxes', 'wp-mailinglist'),
				'file'			=>	__('File Upload', 'wp-mailinglist'),
				'pre_country'	=>	__('Predefined : Country Select', 'wp-mailinglist'),
				'pre_date'		=>	__('Predefined : Date Picker (YYYY-MM-DD)', 'wp-mailinglist'),
				'pre_gender'	=>	__('Predefined : Gender', 'wp-mailinglist'),
			);

			$this -> update_option('fieldtypes', $fieldtypes);

			$fieldtypesquery = "ALTER TABLE `" . $wpdb -> prefix . "wpmlfields` CHANGE `type` `type` VARCHAR(255) NOT NULL DEFAULT 'text';";
			$wpdb -> query($fieldtypesquery);
			return true;
		}

		function check_roles() {
			global $wp_roles;
			$permissions = $this -> get_option('permissions');

			if (empty($permissions) || !is_array($permissions)) {
				$permissions = array();
			}

			if ($role = get_role('administrator')) {
				if (!empty($this -> sections)) {
					foreach ($this -> sections as $section_key => $section_menu) {
						if (empty($role -> capabilities['newsletters_' . $section_key])) {
							$role -> add_cap('newsletters_' . $section_key);

							if (empty($permissions[$section_key]) || !is_array($permissions[$section_key])) {
								$permissions[$section_key] = array();
							}

							$permissions[$section_key][] = 'administrator';
						}
					}
				}

				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$role -> add_cap($block);
						$permissions[$block][] = 'administrator';
					}
				}

				$this -> update_option('permissions', $permissions);
			}

			return false;
		}

		function init_roles($sections = null) {
			global $wp_roles;
			$sections = $this -> sections;

			/* Get the administrator role. */
			$role = get_role('administrator');

			/* If the administrator role exists, add required capabilities for the plugin. */
			if (!empty($role)) {
				if (!empty($sections)) {
					foreach ($sections as $section_key => $section_menu) {
						$role -> add_cap('newsletters_' . $section_key);
					}
				}

				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$role -> add_cap($block);
					}
				}
			} elseif (empty($role) && !is_multisite()) {
				$newrolecapabilities = array();
				$newrolecapabilities[] = 'read';

				if (!empty($sections)) {
					foreach ($sections as $section_key => $section_menu) {
						$newrolecapabilities[] = 'newsletters_' . $section_key;
					}
				}

				if (!empty($this -> blocks)) {
					foreach ($this -> blocks as $block) {
						$newrolecapabilities[] = $block;
					}
				}

				add_role(
					'newsletters',
					_e('Newsletters Manager', 'wp-mailinglist'),
					$newrolecapabilities
				);
			}

			if (!empty($sections)) {
				$permissions = array();

				foreach ($sections as $section_key => $section_menu) {
					$wp_roles -> add_cap('administrator', 'newsletters_' . $section_key);
					$permissions[$section_key][] = 'administrator';
				}

				foreach ($this -> blocks as $block) {
					$wp_roles -> add_cap('administrator', $block);
					$permissions[$block][] = 'administrator';
				}

				$this -> update_option('permissions', $permissions);
			}
		}

		function list_exists($list_id = null) {
			if (!empty($list_id)) {
				if ($Mailinglist -> list_exists($list_id)) {
					return true;
				}
			}

			return false;
		}

		function array_to_object($array = array()) {
			if (!empty($array)) {
				return (object) $array;
			}

			return false;
		}

		function truncatetext($text = null, $start = 0, $end = 0, $append = '...') {
			return substr($text, $start, $end) . $append;
		}

		function gen_date($format = "Y-m-d H:i:s", $time = null) {
			$newtime = (empty($time)) ? false : $time;
			return date_i18n($format, $newtime);
		}

		function override_mce_options($initArray = null) {
			if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
				$opts = '*[*]';
			    $initArray['valid_elements'] = $opts;
			    $initArray['extended_valid_elements'] = $opts;
			    //$initArray['entities'] = "169,copy,174,reg,8482,trade";
			    //$initArray['entity_encoding'] = "raw";
			    //$initArray['verify_html'] = 0;
			    //$initArray['cleanup'] = 0;
			    //$initArray['cleanup_on_startup'] = 0;
			    //$initArray['plugins'] .= ",fullpage";
			    //$initArray['validate_children'] = 0;
			    //$initArray['valid_children'] = $opts;
			}

			return $initArray;
		}

		function the_content($content = null) {
			if (!is_admin() && !defined('DOING_AJAX')) {
				$showpostattachments = $this -> get_option('showpostattachments');
				if (!empty($showpostattachments)) {
					global $post, $Db;
					if ($history = $this -> History() -> find(array('post_id' => $post -> ID))) {
						if (!empty($history -> attachments)) {
							$post_attachments = $this -> render('post-attachments', array('attachments' => $history -> attachments), false, 'default');
							$content .= $post_attachments;
						}
					}
				}
			}

			return $content;
		}

		function stripext($filename = null, $return = 'ext') {
			if (!empty($filename)) {
				//$extArray = split("[/\\.]", $filename);

				if ($return == 'ext') {
					//$p = count($extArray) - 1;
					//$extension = $extArray[$p];
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					return $extension;
				} else {
					///$p = count($extArray) - 2;
					//$filename = $extArray[$p];
					$file = pathinfo($filename, PATHINFO_FILENAME);
					return $file;
				}
			}

			return false;
		}

		function bounce($email = null, $type = 'cgi', $status = null, $messageid = null) {
			global $wpdb, $Bounce, $Email, $Db, $Subscriber, $SubscribersList;

	        $deleted_subscribers = 0;
	        $deleted_emails = 0;

	        $deleteonbounce = $this -> get_option('deleteonbounce');
	        $bouncecount = $this -> get_option('bouncecount');
	        
	        do_action('newsletters_process_bounce', $email, $type, $status, $messageid);

	        switch ($type) {
	            case 'cgi'              :
	        		if (!empty($email)) {
	        			$email = urldecode($email);
	        			preg_match_all("/[<](.*)[>]/i", $email, $matches);

	        			if ($this -> get_option('servertype') == "plesk") {
	        				$email = trim($matches[1][0]);
	        			} else {
	        				$email = trim($matches[1][2]);
	        			}

	        			$Db -> model = $Subscriber -> model;
	        			if ($subscriber = $Db -> find(array('email' => $email))) {
	                        $Db -> model = $Subscriber -> model;
	                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
		                        $Db -> delete($subscriber -> id);
		                        $deleted_subscribers++;
	                        } else {
		                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
	                        }

	                        $bouncedata = array('email' => $subscriber -> email);
	                        $Bounce -> save($bouncedata);

	                        do_action('newsletters_subscriber_bounce', $subscriber -> id, ($subscriber -> bouncecount + 1), false);

	                        //send a notification to the administrator
	                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
	                        $this -> admin_bounce_notification($subscriber);

	                        return true;
	                    }
	        		}

	        		return false;
	                break;
	            case 'pop'					:	            
	            	require_once($this -> plugin_base() . DS . 'vendors' . DS . 'bounce' . DS . 'bounce_driver.class.php');
	            	$bouncehandler = new BounceHandler();
	            	
	            	$deleted_subscribers = 0;
	            	$deleted_emails = 0;
	            	
	            	require($this -> plugin_base() . DS . 'vendor' . DS . 'autoload.php');
			
					$type = $this -> get_option('bouncepop_type');
					$host = $this -> get_option('bouncepop_host');
					$user = $this -> get_option('bouncepop_user');
					$pass = $this -> get_option('bouncepop_pass');
					$port = $this -> get_option('bouncepop_port');
					$bouncepop_prot = $this -> get_option('bouncepop_prot');
					$ssl = (empty($bouncepop_prot)) ? false : (($bouncepop_prot == "ssl") ? true : false);
					
					try {
						$mailbox = new PhpImap\Mailbox('{' . $host . ':' . $port . '/' . $type . ((!empty($ssl)) ? '/ssl' : '') . '/novalidate-cert}INBOX', $user, $pass, false);
						
						try {							
							$mailids = $mailbox -> searchMailbox('ALL');
							
							if (!empty($mailids)) {
								$m = 0;
								
								foreach ($mailids as $mailid) {
									$mail = $mailbox -> getMail($mailid);
									$source = imap_fetchheader($mailbox -> getImapStream(), $mailid, FT_UID) . imap_body($mailbox -> getImapStream(), $mailid, FT_UID);
									$the_facts = $bouncehandler -> get_the_facts($source);
									
									$message_array = explode("\r\n", $mail -> textPlain);
									if (!empty($message_array)) {
										foreach ($message_array as $mkey => $mval) {
											if (preg_match("/^Message-ID:(.*)/si", $mval, $matches)) {
												$messageid = trim($matches[1]);												
												if (!empty($messageid)) {													
													$Db -> model = $Email -> model;
													$bouncedemail = $Db -> find(array('messageid' => $messageid));
													break;
												}
											}
										}
									}

									if (!empty($the_facts[0]['recipient']) && !empty($the_facts[0]['action'])) {
				                        if ($the_facts[0]['action'] == "failed") {
					                        $email = trim($the_facts[0]['recipient']);
					                        $status = trim($the_facts[0]['status']);

					                        include($this -> plugin_base() . DS . 'vendors' . DS . 'bounce' . DS . 'bounce_responses.php');
											$status_message = $bouncehandler -> fetch_status_messages(trim($the_facts[0]['status']));
											if (!empty($status_message)) {
												$status .= ' - ' . $status_message[0][0] . ', ' . $status_message[1][0];
											}

					                 		$Db -> model = $Subscriber -> model;
			                                if ($subscriber = $Db -> find(array('email' => $email))) {
			                                	$subscriber_id = $subscriber -> id;

			                                    $Db -> model = $Subscriber -> model;
						                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
							                        $Db -> delete($subscriber -> id);
							                        $deleted_subscribers++;
						                        } else {
							                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
						                        }

			                                    $bouncedata = array(
			                                    	'email' 		=> 	$subscriber -> email,
			                                    	'status' 		=> 	$status,
			                                    	'history_id' 	=> 	$bouncedemail -> history_id
			                                    );

			                                    $Bounce -> save($bouncedata);

			                                    do_action('newsletters_subscriber_bounce', $subscriber -> id, ($subscriber -> bouncecount + 1), $bouncedemail -> history_id);

			                                    $Db -> model = $Email -> model;
			                                    $Db -> save_field('bounced', "Y", array('id' => $bouncedemail -> id));

			                                    //send a notification to the administrator
			                                    $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
				                                $this -> admin_bounce_notification($subscriber);
			                                }
				                        }
			                        }
			                        
			                        $mailbox -> deleteMail($mailid);
	                                $deleted_emails++;
								}
								
								$mailbox -> expungeDeletedMails();

								return array($deleted_subscribers, $deleted_emails);								
							} else {
								$error = __('There are no emails in the mailbox', 'wp-mailinglist');
							}
						} catch (Exception $e) {
							$error = $e -> getMessage();
						}
					} catch (Exception $e) {
						$error = $e -> getMessage();
					}
	            	
	            	echo esc_html($error);
	            	return false;
	            	break;
	            case 'sendgrid'			:
	            case 'sns'				:
	            case 'mailgun'			:
	            case 'sparkpost'		:
	            
	            	$Db -> model = $Subscriber -> model;
                    if ($subscriber = $Db -> find(array('email' => $email))) {
                    	$subscriber_id = $subscriber -> id;

                        $Db -> model = $Subscriber -> model;
                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
	                        $Db -> delete($subscriber -> id);
	                        $deleted_subscribers++;
                        } else {
	                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
                        }
                        
                        if (!empty($messageid)) {
	                        $Db -> model = $Email -> model;
							$bouncedemail = $Db -> find(array('messageid' => $messageid));
							$Db -> model = $Email -> model;
			                $Db -> save_field('bounced', "Y", array('id' => $bouncedemail -> id));
                        }

                        $bouncedata = array(
                        	'email' 		=> 	$subscriber -> email,
                        	'status'		=>	$status,
                        	'history_id' 	=>	$bouncedemail -> history_id,
                        );

                        $Bounce -> save($bouncedata);
                        
                        do_action('newsletters_subscriber_bounce', $subscriber -> id, ($subscriber -> bouncecount + 1), $bouncedemail -> history_id);
                        
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;
                    }

	            	return array($deleted_subscribers, $deleted_emails);
	            case 'mandrill-bounce'							:
	            	$Db -> model = $Subscriber -> model;
                    if ($subscriber = $Db -> find(array('email' => $email))) {
                    	$subscriber_id = $subscriber -> id;

                        $Db -> model = $Subscriber -> model;
                        if (!empty($deleteonbounce) && $deleteonbounce == "Y" && (empty($bouncecount) || $bouncecount == 1 || $bouncecount < 1 || (($subscriber -> bouncecount + 1) >= $bouncecount))) {
	                        $Db -> delete($subscriber -> id);
	                        $deleted_subscribers++;
                        } else {
	                        $Db -> save_field('bouncecount', ($subscriber -> bouncecount + 1), array('id' => $subscriber -> id));
                        }

                        $bouncedata = array(
                        	'email' 			=> 	$subscriber -> email,
                        	'status'			=>	$status,
                        );

                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;
                    }

	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	            case 'mandrill-delete'				:
	            	$Db -> model = $Subscriber -> model;
                    if ($subscriber = $Db -> find(array('email' => $email))) {
                    	$subscriber_id = $subscriber -> id;

                        $Db -> model = $Subscriber -> model;
                        $Db -> delete($subscriber -> id);
                        $deleted_subscribers++;

                        $bouncedata = array(
                        	'email' 			=> 	$subscriber -> email,
                        	'status'			=>	$status,
                        );

                        $Bounce -> save($bouncedata);
                        $subscriber -> bouncecount = ($subscriber -> bouncecount + 1);
                        $this -> admin_bounce_notification($subscriber);
                        $deleted_emails++;
                    }

	            	return array($deleted_subscribers, $deleted_emails);
	            	break;
	        }
		}

		function add_action($action = null, $function = null, $priority = 10, $params = 1) {
			add_action($action, array($this, $function == '' ? $action : $function), $priority, $params);
		}

		function remove_action($action = null, $function = null) {
			remove_action($action, array($this, $function));
		}

		function add_filter($filter = null, $function = null, $priority = 10, $params = 1) {
			add_filter($filter, array($this, $function == '' ? $filter : $function), $priority, $params);
		}

		function plugin_base($path = null) {
			
			if (empty($path)) {
				$path = __FILE__;
			}
			
			return rtrim(dirname($path), '/');
		}

		function url() {
			$url = rtrim(plugins_url(false, __FILE__));
			return $url;
		}

		function redirect($location = null, $msgtype = null, $message = null, $jsredirect = false) {
			global $Html;
			if (empty($location)) {
				$url = remove_query_arg(array('action', 'action2', '_wpnonce', '_wp_http_referer'), wp_get_referer());				
			} else {
				$url = $location;
			}

			if (!empty($msgtype)) {
				if (is_admin() && !defined('DOING_AJAX')) {
					if ($msgtype == "message") {
						$url = $Html -> retainquery($this -> pre . 'updated=true', $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery($this -> pre . 'error=true', $url);
					}
				} else {
					if ($msgtype == "success") {
						$url = $Html -> retainquery('updated=1&success=' . $message, $url);
					} elseif ($msgtype == "error") {
						$url = $Html -> retainquery('updated=1&error=' . $message, $url);
					}
				}
			}

			if (!empty($message) && is_admin() && !defined('DOING_AJAX')) {
				$message = rawurlencode($message);
				$url = $Html -> retainquery($this -> pre . 'message=' . ($message), $url);
			}

			if (headers_sent() || $jsredirect == true) {				
				?>

				<script type="text/javascript">
				window.location.href = '<?php echo addslashes($url); ?>';
				</script>

				<?php
			} else {
				header("Location: " . $url . "");
				exit();
			}
		}

		function render_error($message, $vars = array(), $dismissable = true, $type = null) {
			
			if (!empty($dismissable) && !empty($type)) {
				$dismissed = $this -> get_option('dismissed-' . $type);
				if (!empty($dismissed)) {
					return;
				}
			}
			
			//$message = esc_html($message); TO DO
			if (!empty($message) && is_numeric($message)) {
				include $this -> plugin_base() . DS . 'includes' . DS . 'messages.php';
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}

			$this -> render('error-top', array('message' => $message, 'dismissable' => $dismissable, 'type' => $type), true, 'admin');
		}
		
		function render_warning($message, $vars = array(), $dismissable = true, $type = null) {
			
			if (!empty($dismissable) && !empty($type)) {
				$dismissed = $this -> get_option('dismissed-' . $type);
				if (!empty($dismissed)) {
					return;
				}
			}
			
			if (!empty($message) && is_numeric($message)) {
				include($this -> plugin_base() . DS . 'includes' . DS . 'messages.php');
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}

			$this -> render('warning', array('message' => $message, 'dismissable' => $dismissable, 'type' => $type), true, 'admin');
		}
		
		function render_info($message, $vars = array(), $dismissable = true, $type = null) {
			
			if (!empty($dismissable) && !empty($type)) {
				$dismissed = $this -> get_option('dismissed-' . $type);				
				if (!empty($dismissed)) {
					return;
				}
			}
			
			if (!empty($message) && is_numeric($message)) {
				include($this -> plugin_base() . DS . 'includes' . DS . 'messages.php');
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}

			$this -> render('info', array('message' => $message, 'dismissable' => $dismissable, 'type' => $type), true, 'admin');
		}

		function render_message($message, $vars = array(), $dismissable = true, $type = null) {				
			if (!empty($dismissable) && !empty($type)) {
				$dismissed = $this -> get_option('dismissed-' . $type);
				if (!empty($dismissed)) {
					return;
				}
			}
					
			//$message = esc_html($message); TO DO
			if (!empty($message) && is_numeric($message)) {
				include($this -> plugin_base() . DS . 'includes' . DS . 'messages.php');
				if (!empty($messages[$message])) {
					$message = vsprintf($messages[$message], $vars);
				}
			}

			$this -> render('message', array('message' => $message, 'dismissable' => $dismissable, 'type' => $type), true, 'admin');
		}

		function et_template($type = null, $subscriber = null, $language = null) {
			$theme_id = $this -> default_theme_id('system');

			if (!empty($type)) {
				if (!empty($language)) {
					$configured_theme_id = $this -> language_use($language, $this -> get_option('ettemplate_' . $type));
				} else {
					$configured_theme_id = esc_html($this -> get_option('ettemplate_' . $type));
				}				

				if (!empty($configured_theme_id)) {
					$theme_id = $configured_theme_id;
				}
			}

			return $theme_id;
		}
		
		function et_subject($type = null, $subscriber = null, $language = null) {
			$subject = false;

			if (!empty($type)) {
				if (!empty($language)) {
					$subject = $this -> language_use($language, $this -> get_option('etsubject_' . $type));
				} else {
					$subject = esc_html($this -> get_option('etsubject_' . $type));
				}
			}
			$user = isset($user) ? $user : null;
			$subject = $this -> process_set_variables($subscriber, $user, $subject, false, false, true);
			return $subject;
		}

		function et_message($type = null, $subscriber = null, $language = null, $processvariables = true, $hidethubmnail = null) {
			$message = false;

			if (!empty($type)) {
				$template = $this -> get_option('etmessage_' . $type);
				if('Y' == $hidethubmnail) {
					$template = str_replace( "[newsletters_post_thumbnail]", "", $template);
				}

				if ($this -> language_do()) {
					if (empty($language)) {
						$language = $this -> language_current();
					}
				}

				switch ($type) {
					case 'posts'				:
					case 'latestposts'			:
					case 'sendas'				:
						if (!empty($language) && $this -> language_do()) {
							$message = $this -> language_use($language, $template, false);
						} else {
							$message = esc_html($template);
						}
						break;
					default 					:
						if (!empty($language) && $this -> language_do()) {
							$message = wpautop($this -> language_use($language, $template, false));
						} else {
							$message = wpautop(esc_html($template));
						}

						// Should variables be processed? Shortcodes, etc.
						if (!empty($processvariables)) {
							$user = isset($user) ? $user : null;
							$message = $this -> process_set_variables($subscriber, $user, $message, false, false, true);
						}
						break;
				}
			}

			return $message;
		}

		function get_themefolders() {
			$dir = $this -> plugin_base() . DS . 'views' . DS;
			$themefolders = array();

			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						$filetype = filetype($dir . $file);
						if (!empty($filetype) && $filetype == "dir") {
							if ($file != "admin" && $file != "email" && $file != "." && $file != "..") {
								$themefolders[] = $file;
							}
						}
					}

					closedir($dh);
				}
			}

			return $themefolders;
		}

		function render($file = null, $params = array(), $output = true, $folder = 'default', $extension = null) {
			$this -> plugin_name = basename(NEWSLETTERS_DIR);
			$this -> sections = apply_filters('newsletters_sections', (object) $this -> sections);

			if (!empty($file)) {
				$filename = $file . '.php';

				if (!empty($folder) && $folder != "admin") {
					$theme_folder = $this -> get_option('theme_folder');
					$folder = (!empty($theme_folder)) ? $theme_folder : $folder;

					$template_url = get_stylesheet_directory_uri();
					$theme_path = get_stylesheet_directory();
					$full_path = $theme_path . DS . 'newsletters' . DS . $filename;

					if (!empty($theme_path) && file_exists($full_path)) {
						$folder = $theme_path . DS . 'newsletters';
						$theme_serve = true;
					}
				}

				if (!empty($extension)) {
					if ($extensions = $this -> get_extensions()) {
						foreach ($extensions as $ext) {
							if ($extension == $ext['slug']) {
								$extension_folder = $ext['plugin_name'];
							}
						}
					}

					$filepath = dirname(plugin_dir_path(__FILE__)) . DS . $extension_folder . DS;
				} else {
					if (empty($theme_serve)) {
						$filepath = $this -> plugin_base() . DS . 'views' . DS . $folder . DS;
					} else {
						$filepath = $folder . DS;
					}
				}

				$filefull = $filepath . $filename;

				if (!empty($params)) {
					foreach ($params as $key => $val) {
						${$key} = $val;
					}
				}

				if (file_exists($filefull)) {
					ob_start();

					if (!empty($this -> classes)) {
						foreach ($this -> classes as $class) {
							global ${$class};
						}
					}
					
					if (!empty($this -> models)) {
						foreach ($this -> models as $model) {
							global ${$model};
						}	
					}

					if (!empty($this -> helpers)) {
						foreach ($this -> helpers as $helper) {
							global ${$helper};
						}
					}

					include($filefull);

					$data = ob_get_clean();
					$data = apply_filters('newsletters_render', $data, $file, $params, $output);

					if ($output == false) {
						return $data;
					}

					echo $data;
					return true;
				} else {
				     // phpcs:ignore
					echo sprintf(__('Rendering of %s has failed!', 'wp-mailinglist'), '"' . $filefull . '"');
				}
			} else {
				echo esc_html_e('No file was specified for rendering', 'wp-mailinglist');
			}
		}

		function has_child_theme_folder() {
			$theme_path = get_stylesheet_directory();
			$full_path = $theme_path . DS . 'newsletters';

			if (file_exists($full_path)) {
				return true;
			}

			return false;
		}

		function active_theme_folder() {
			$theme_folder = $this -> get_option('theme_folder');
			$theme_folder = (!empty($theme_folder)) ? $theme_folder : 'default';

			if ($this -> has_child_theme_folder()) {
				$theme_path = get_stylesheet_directory();
				$theme_folder = $theme_path . DS . 'checkout' . DS;
			} else {
				$theme_folder = $this -> plugin_base() . DS . 'views' . DS . $theme_folder . DS;
			}

			return $theme_folder;
		}

		function theme_folder_functions() {
			if ($theme_folder = $this -> active_theme_folder()) {
				$functions_path = $theme_folder . 'functions.php';

				$theme_folder_option = $this -> get_option('theme_folder');
				$functions_path_original = $this -> plugin_base() . DS . 'views' . DS . $theme_folder_option . DS . 'functions.php';

				if (file_exists($functions_path)) {
					require_once($functions_path);

					return true;
				} elseif (file_exists($functions_path_original)) {
					require_once($functions_path_original);

					return true;
				}
			}

			return false;
		}

		function render_url($file = null, $folder = 'admin', $extension = null) {
			$this -> plugin_name = basename(NEWSLETTERS_DIR);
			$folderurl = plugins_url() . '/' . $this -> plugin_name . '/';

			if (!empty($file)) {
				if (!empty($extension)) {
					if ($extensions = $this -> get_extensions()) {
						foreach ($extensions as $ext) {
							if ($extension == $ext['slug']) {
								$extension_folder = $ext['plugin_name'];
								$folderurl = plugins_url() . '/' . $extension_folder . '/';
							}
						}
					}					
				} else {
					if (!empty($folder) && $folder != "admin") {
						$theme_folder = $this -> get_option('theme_folder');
						$folder = (!empty($theme_folder) && $folder != "assets") ? $theme_folder : $folder;
						$folderurl = plugins_url() . '/' . $this -> plugin_name . '/views/' . $folder . '/';
	
						$template_url = get_stylesheet_directory_uri();
						$theme_path = get_stylesheet_directory();
						$full_path = $theme_path . DS . 'newsletters' . DS . $file;

						if (!empty($theme_path) && file_exists($full_path)) {
							$folderurl = $template_url . '/newsletters/';
						}
					} else {
						$folderurl = plugins_url() . '/' . $this -> plugin_name . '/';
					}
				}

				$url = $folderurl . $file;

				return $url;
			}

			return false;
		}

		function default_theme_id($type = "sending") {
			global $Db, $Theme;
			$Db -> model = $Theme -> model;
			$theme_id = 0;

			switch ($type) {
				case 'system'			:
					if ($theme = $Db -> find(array('defsystem' => "Y"))) {
						return $theme -> id;
					}
					break;
				case 'sending'			:
				default 				:
					if ($theme = $Db -> find(array('def' => "Y"))) {
						return $theme -> id;
					}
					break;
			}

			return $theme_id;
		}

		function make_bitly_url($url = null, $format = 'txt') {
			//if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $url)) {
			$management_post_id = $this -> get_managementpost();
			$management_post = get_post($management_post_id);
				
			if (!preg_match("/(" . $management_post -> post_name . "|newsletters_method)/si", $url)) {
				if (preg_match("/^http\:\/\//si", $url) || preg_match("/^https\:\/\//si", $url)) {
					$login = $this -> get_option('shortlinkLogin');
					$appkey = $this -> get_option('shortlinkAPI');
					$bitly = 'https://api.bit.ly/v3/shorten?longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;
					$bitly = apply_filters('newsletters_bitly_url', $bitly);

					$result = wp_remote_get($bitly, array('timeout' => 120));
					if (!is_wp_error($result)) {
						$body = trim($result['body']);

						if (filter_var($body, FILTER_VALIDATE_URL) !== FALSE) {
							$bitlink = $body;
							return $bitlink;
						}
					}
				}
			}

			return $url;
		}

		function hashlink($link = null, $history_id = null, $subscriber_id = null, $user_id = null) {
			global $Html, $wpmlLink;
			$hashlink = $link;
			
			if (!empty($link)) {								
				//if (!preg_match("/(manage\-subscriptions|loginauth|wpml|wpmlmethod|jpg|png|gif|jpeg|bmp|wpmltrack|wpmllink)/si", $link)) {					
				$management_post_id = $this -> get_managementpost();
				$management_post = get_post($management_post_id);
					
				if (!preg_match("/(" . $management_post -> post_name . "|newsletters_method)/si", $link)) {
					if (preg_match("/^http\:\/\//si", $link) || preg_match("/^https\:\/\//si", $link)) {						
						$hash = md5($link);

						$queryargs = array();
						$queryargs['newsletters_link'] = $hash;
						$queryargs['history_id'] = $history_id;

						if (!empty($subscriber_id)) $queryargs['subscriber_id'] = $subscriber_id;
						if (!empty($user_id)) $queryargs['user_id'] = $user_id;

						$queryargs = apply_filters('newsletters_hashlink_queryargs', $queryargs, $link, $hash, $history_id);
						$hashlink = add_query_arg($queryargs, home_url());

						if (!$curlink = $this -> Link() -> find(array('hash' => $hash))) {
							$link_data = array(
								'link'			=>	$link,
								'hash'			=>	$hash,
							);

							$this -> Link() -> save($link_data, true);
						}
					}
				}
			}

			$hashlink = apply_filters('newsletters_hashlink', $hashlink, $link, $history_id, $subscriber_id, $user_id);
			return $hashlink;
		}

		function admin_footer_text($text = null) {
			if (!empty($_GET['page']) && in_array($_GET['page'], (array) $this -> sections)) {
				$plugin = '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">Tribulant Newsletters</a>';

				$stars = '<a href="https://wordpress.org/support/plugin/newsletters-lite/reviews/?rate=5#new-post" target="_blank"><span class="newsletters_footer_rating">
		          <span class="star"></span><span class="star"></span><span class="star"></span><span class="star"></span><span class="star"></span>
		        </span></a>';

	        	$stars .= '<style type="text/css">
	        	.newsletters_footer_rating {
				    unicode-bidi: bidi-override;
				    direction: rtl;
				    font-size: 16px;
				}

				.newsletters_footer_rating span.star {
				    font-family: FontAwesome !important;
				    font-weight: normal;
				    font-style: normal;
				    display: inline-block;
				}

				.newsletters_footer_rating span.star:before,
				.newsletters_footer_rating span.star ~ span.star:before {
					font-family: FontAwesome;
				    content: "\f005";
				    color: #e3cf7a;
				    padding-right: 2px;
				}
	        	</style>';

				$newsletters_text = '</p><br class="clear" /><p class="alignleft">' . sprintf(__('If you like %s, please leave us a %s rating on WordPress.org. Thank you in advance!', 'wp-mailinglist'), $plugin, $stars) . '';
				$text .= $newsletters_text;
			}

			return $text;
		}

		function render_email($file = null, $params = array(), $output = false, $html = true, $renderht = true, $theme_id = 0, $shortlinks = true, $fullbody = false) {
			global $newsletters_history_id, $newsletters_plaintext;
			$this -> plugin_name = basename(NEWSLETTERS_DIR);

			if (!empty($file) || !empty($fullbody)) {
				$defaulttemplate = $this -> get_option('defaulttemplate');
				if (!empty($defaulttemplate)) {
					$head = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'head-default.php';
					$foot = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'foot-default.php';
				} else {
					$head = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'head.php';
					$foot = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . 'foot.php';
				}

				/* Go through the parameters */
				if (!empty($params)) {
					foreach ($params as $pkey => $pval) {
						${$pkey} = $pval;

						switch ($pkey) {
							case 'subscriber'	:
								global $current_subscriber;
								$current_subscriber = $subscriber;
								break;
							case 'message'		:
								global $orig_message;
								$orig_message = wp_unslash($pval);
								break;
						}
					}
				}

				if (!empty($this -> classes)) {
					foreach ($this -> classes as $class) {
						global ${$class};
					}
				}

				if (!empty($this -> helpers)) {
					foreach ($this -> helpers as $helper) {
						global ${$helper};
					}
				}

				/* Head */
				if (true || $html == true) {
					if ($renderht == true && file_exists($head)) {
						if ($output == false) { ob_start(); }
						include($head);
						if ($output == false) { $head = ob_get_clean(); }
					} else {
						$head = "";
					}
				}

				/** Body Part BEG **/
				if (empty($fullbody)) {
					$filefull = $this -> plugin_base() . DS . 'views' . DS . 'email' . DS . $file . '.php';

					if (file_exists($filefull)) {
						if ($output == false) { ob_start(); }

						include($filefull);
						if ($output == false) { $body = ob_get_clean(); }

						if ($output == false && (true || $html == true)) {
							$body = wpautop($body);
						}
					}
				} else {
					$body = "";
					ob_start();
					echo wpautop(wp_unslash($fullbody));
					$body = ob_get_clean();
				}

				$bodyonly = $body;
				/** Body Part END **/

				/* Foot */
				if (true || $html == true) {
					if ($renderht == true && file_exists($foot)) {
						if ($output == false) { ob_start(); }
						include($foot);
						if ($output == false) { $foot = ob_get_clean(); }
					} else {
						$foot = "";
					}
				}

				if (!empty($history_id)) {
					$this -> history_id = $newsletters_history_id = $history_id;
					$history = $this -> History() -> get($history_id);
					
					if (!empty($history -> post_id)) {
						if ($getpost = get_post($history -> post_id)) {
							global $post, $shortcode_post;
							$post = $getpost;
							$shortcode_post = $getpost;
						}
					}

					if (!empty($history -> language)) {
						$this -> language_set($history -> language);
					}
				}

				/*$eunique = (empty($user)) ?
				md5($subscriber -> id . $subscriber -> mailinglist_id . $email -> history_id . date_i18n("YmdH")) :
				md5($user -> ID . $user -> roles[0] . $email -> history_id . date_i18n("YmdH"));*/

				//$body = $this -> process_set_variables($subscriber, $user, wp_unslash($body), $history_id, $eunique);

				//pass the $body through the shortcodes
				//$body = do_shortcode(wp_unslash($body));
				$body = str_replace("$", "&#36;", $body);
				$body = preg_replace('/\$(\d)/', '\\\$$1', $body);

				$themeintextversion = $this -> get_option('themeintextversion');
				if (empty($themeintextversion)) {
					global $wpml_textmessage;
					$wpml_textmessage = wp_unslash($body);
				}

				if (empty($output) || $output == false) {
					global $Db, $Theme;
					$Db -> model = $Theme -> model;
					
                    if (!empty($history -> builderon)) {
                        $buildercontent = stripslashes($body);

                        $body = '';
                        $body .= '<!doctype html>';
                        $body .= '<html lang="en">';
                        $body .= '<head>';
                        $body .= '<meta name="viewport" content="width=device-width" />';
                        $body .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                        $body .= '</head>';
                        $body .= '<body>';
                        $body .= $buildercontent;
                        $body .= '</body>';
                        $body .= '</html>';
                    } elseif (!empty($theme_id) && $theme = $Db -> find(array('id' => $theme_id))) {

						$theme_content = "";
						ob_start();
						echo wp_unslash($theme -> content);
						$theme_content = ob_get_clean();
						$theme_content = apply_filters('newsletters_theme_before_wpmlcontent_replace', $theme_content);

						$body = '<div class="newsletters_content">' . apply_filters($this -> pre . '_wpmlcontent_before_replace', $body) . '</div>';
						
						$body = preg_replace("/\[(wpmlcontent|newscontent|newsletters_main_content)\]/si", wp_unslash($body), $theme_content);
						
						if (!empty($theme -> themestylesheet)) {
							$dom = new DOMDocument();
							$dom -> loadHTML($body);
							$css = 'body { background:red; }';
							$stylesheet = get_stylesheet_uri();
							$css_response = wp_remote_get($stylesheet);
							$css = wp_remote_retrieve_body($css_response);
							$style_el = $dom -> createElement('style', $css);
							$style_el -> setAttribute('type', "text/css");
							$head = $dom -> getElementsByTagName('head') -> item(0);
							$head -> appendChild($style_el);
							$body = $dom -> saveHTML();
						}
					} else {
						// No theme, load default
						$body = do_shortcode(wp_unslash($head)) . wp_unslash($body) . do_shortcode(wp_unslash($foot));
					}

					// Parse the content areas
					$pattern = "/\[(\[?)(newsletters_content)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s";
					$body = preg_replace_callback($pattern, array($this, 'newsletters_content'), $body);
					$body = htmlspecialchars_decode($body, ENT_NOQUOTES);
					$body = apply_filters($this -> pre . '_wpmlcontent_after_replace', $body);

					// Remove width/height attributes from images
					$body = $this -> remove_width_height_attr($body);

					/*$preheader = 'This is my custom preheader text that will show up in the auto preview on the email client to capture the attention of the reader reading the email.';

					$doc = new DOMDocument();
					$doc -> loadHTML($body);
					$body = $doc -> getElementsByTagName('body') -> item(0);
					$intro = $doc -> createElement('span', $preheader);
					$intro -> setAttribute('style', "display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0;");
					$body -> insertBefore($intro, $body -> firstChild);
					$body = $doc -> saveHTML();*/

					if (!empty($themeintextversion)) {
						global $wpml_textmessage;
						$wpml_textmessage = $body;
					}

					global $newsletters_plaintext;
					$newsletters_plaintext = false;
					if (empty($html) || $html != true || (!empty($history -> format) && $history -> format == "text")) {
						if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
							if (class_exists('DOMDocument')) {
								require_once $this -> plugin_base() . DS . 'vendors' . DS . 'class.html2text.php';
			    				$html2text = new Html2Text();
								$body = $html2text -> convert($wpml_textmessage);
								$newsletters_plaintext = true;
							}
						}
					} else {
						$newsletters_plaintext = false;
					}

					return $body;
				} else {
					return true;
				}
			}

			return false;
		}

		function remove_width_height_attr($html = null) {
			$remove_width_height_attr = $this -> get_option('remove_width_height_attr');

			if (!empty($remove_width_height_attr)) {
				if (class_exists('DOMDocument')) {
					$dom = new DOMDocument();
					$dom -> loadHTML($html);
	
					foreach ($dom -> getElementsByTagName('img') as $img) {
						$img -> removeAttribute('width');
						$img -> removeAttribute('height');
					}
	
					$html = urldecode($dom -> saveHTML());
					$html = trim(preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $html));
				}
			}

			return $html;
		}

		function newsletters_content($matches = null) {
			$output = "";
			if (!empty($matches)) {
				$atts = shortcode_parse_atts($matches['3']);
				if (!empty($this -> history_id) && !empty($atts['id'])) {
					//if (is_object($this -> Content)) {
						if ($contentarea = $this -> Content() -> find(array('number' => $atts['id'], 'history_id' => $this -> history_id))) {
							$output = wpautop(do_shortcode(wp_unslash($contentarea -> content)));
						}
					//}
				}
			}

			return $output;
		}

		function check_uploaddir() {
			if (!is_admin() && !defined('DOING_AJAX')) return;

			global $uploaddir, $Html;
			$uploaddir = $Html -> uploads_path() . '/' . $this -> plugin_name . '/';

			if (file_exists($uploaddir)) {
				/* Export subscribers folder */
				$exportdir = $uploaddir . 'export' . DS;
				if (!file_exists($exportdir)) {
					@mkdir($exportdir, 0755);
					@chmod($exportdir, 0755);
				} else {
					$exportindex = $exportdir . 'index.php';
					$exportindexcontent = "<?php /* Silence */ ?>";
					$exporthtaccess = $exportdir . '.htaccess';
					$exporthtaccesscontent = "order allow,deny\r\ndeny from all\r\n\r\nOptions All -Indexes";
					if (!file_exists($exportindex) && $fh = fopen($exportindex, "w")) { fwrite($fh, $exportindexcontent); fclose($fh); }
					if (!file_exists($exporthtaccess) && $fh = fopen($exporthtaccess, "w")) { fwrite($fh, $exporthtaccesscontent); fclose($fh); }
				}

				/* Embedded images folder */
				if ($this -> is_plugin_active('embedimages')) {
					$embedimagesdir = $uploaddir . 'embedimages' . DS;
					if (!file_exists($embedimagesdir)) {
						@mkdir($embedimagesdir, 0755);
						@chmod($embedimagesdir, 0755);
					}
				}
			} else {
				@mkdir($uploaddir, 0755);
				@chmod($uploaddir, 0755);
			}
		}

		function extension_vendor($name = null) {
			if (!empty($name)) {
				switch ($name) {
					/* Embedded Images */
					case 'embedimages'						:
						$filepath = 'newsletters-embedimages' . DS . 'embedimages.php';
						break;
				}

				$filefull = dirname(plugin_dir_path(__FILE__)) . DS . $filepath;

				if (file_exists($filefull)) {

					require_once $filefull;
					$class = $this -> pre . $name;

					if (class_exists($class)) {
						${$name} = new $class;
						return ${$name};
					}
				}
			}
		}

		function get_extensions() {
			include $this -> plugin_base() . DS . 'includes' . DS . 'extensions.php';
			$extensions = apply_filters($this -> pre . '_extensions_list', $extensions);
			$this -> extensions = $extensions;

			if (!empty($extensions) && is_array($extensions)) {
				$titles = array();
				foreach ($extensions as $extension) {
					$titles[] = $extension['name'];
				}

				array_multisort($titles, SORT_ASC, $extensions);
				return $extensions;
			}

			return false;
		}

		function is_plugin_active($name = null, $orinactive = false) {
			if (!empty($name)) {
				global $Html;
				$slug = $Html -> sanitize($name);

				$hash = ('active' . $slug . $orinactive);
				if ($ob_active = $this -> get_cache($hash, 'pluginactive')) {
					return $ob_active;
					$active = (!empty($ob_active) && $ob_active == "Y") ? true : false;
					return $active;
				}

				require_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'plugin.php';

				if ($extensions = $this -> get_extensions()) {
					foreach ($extensions as $extension) {
						if ($name == $extension['slug']) {
							$path = $extension['plugin_name'] . DS . $extension['plugin_file'];
						}
					}
				}

				if (empty($path)) {
					switch ($name) {
						case 'embedimages'							:
							$path = 'newsletters-embedimages' . DS . 'embedimages.php';
							break;
						case 'newsletters-cforms'					:
							$path = 'newsletters-cforms' . DS . 'cforms.php';
							break;
						case 'qtranslate'							:
							$path = 'qtranslate' . DS . 'qtranslate.php';
							break;
						case 'qtranslate-x'							:
							$path = 'qtranslate-x' . DS . 'qtranslate.php';
							break;
						case 'wpml'									:
							$path = 'sitepress-multilingual-cms' . DS . 'sitepress.php';
							break;
						case 'polylang'								:
							$path = 'polylang' . DS . 'polylang.php';
							break;
						case 'wpglobus'								:
							$path = 'wpglobus' . DS . 'wpglobus.php';
							break;
						case 'captcha'								:
							$path = 'really-simple-captcha' . DS . 'really-simple-captcha.php';
							break;
					}
				}

				if (!empty($path)) $path2 = str_replace("\\", "/", $path);

				if (!empty($name) && $name == "qtranslate") {
					$path2 = 'mqtranslate' . DS . 'mqtranslate.php';
				}

				if (!empty($path)) {
					$plugins = get_plugins();

					if (!empty($plugins)) {
						if (array_key_exists($path, $plugins) || array_key_exists($path2, $plugins)) {
							/* Let's see if the plugin is installed and activated */
							if (is_plugin_active(plugin_basename($path)) ||
								is_plugin_active(plugin_basename($path2))) {

								$this -> set_cache($hash, true, 'pluginactive');

								return true;
							}

							/* Maybe the plugin is installed but just not activated? */
							if (!empty($orinactive) && $orinactive == true) {
								if (is_plugin_inactive(plugin_basename($path)) ||
									is_plugin_inactive(plugin_basename($path2))) {
									$this -> set_cache($hash, true, 'pluginactive');

									return true;
								}
							}
						}
					}
				}
			}

			$this -> set_cache($hash, false, 'pluginactive');
			return false;
		}
// phpcs:disable
		// Make sure that all metaboxes (fields) are closed when the form builder is loaded
		function closed_meta_boxes_form($closed = null) {
			$id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
			if (!empty($id)) {
				if ($form = $this -> Subscribeform() -> find(array('id' => $id))) {
					$closed = array();

					if (!empty($form -> form_fields)) {
						foreach ($form -> form_fields as $form_field) {
							$id = 'newsletters_forms_field_' . $form_field -> field_id;
							if (empty($closed) || !in_array($id, $closed)) {
								$closed[] = $id;
							}
						}
					}
				}
			}

		    return $closed;
		}

		// Reset the meta box order on the save a form page
		// The meta boxes in WordPress are actually used as fields on the form here
		function meta_box_order($order = null, $option = null, $user = null) {
			if (!empty($order)) {
				$order = false;
			}

			return $order;
		}

		function use_captcha($status = "Y") {
			if ($status == 'Y') {
				$captcha_type = $this -> get_option('captcha_type');
				if (!empty($captcha_type)) {
					switch ($captcha_type) {
						case 'rsc'				:
							if ($this -> is_plugin_active('captcha')) {
								return "rsc";
							}
							break;
						case 'recaptcha'		:
							return "recaptcha";
							break;
						case 'none'				:
						default 				:
							return false;
							break;
					}
				}
			}

			return false;
		}


        function pro_only_badge($link = null) {
            $badge_text = "";
            if (!$this -> ci_serial_valid()) {
                if(isset($link) && $link) {
                    $badge_text = '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lite_upgrade) . '" >';
                }

                $badge_text .= __('(PRO only)', 'wp-mailinglist');

                if(isset($link) && $link) {
                    $badge_text .= '</a>';
                }
            }
            return $badge_text;

        }
	}
}

if (!class_exists('fakemailer')) {
	class fakemailer {
	    public function Send() {
		    return 'Cancelling mail';
	        //throw new fakephpmailerException('Cancelling mail');
	    }
	}
}

if (!class_exists('fakephpmailerException' )) {
	/*class phpmailerException extends Exception {
	    public function errorMessage() {
	        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
	        return $errorMsg;
	    }
	}*/
}

?>
