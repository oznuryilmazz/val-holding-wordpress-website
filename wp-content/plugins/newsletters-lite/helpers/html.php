<?php
// phpcs:ignoreFile

if (!class_exists('wpmlHtmlHelper')) {
	class wpmlHtmlHelper extends wpMailPlugin
    {
	
		var $name = 'Html';
		
		function __construct()
        {
			return true;
		}
		
		/**
		 * Convert date/time format between `date()` and `strftime()`
		 *
		 * Timezone conversion is done for Unix. Windows users must exchange %z and %Z.
		 *
		 * Unsupported date formats : S, n, t, L, B, G, u, e, I, P, Z, c, r
		 * Unsupported strftime formats : %U, %W, %C, %g, %r, %R, %T, %X, %c, %D, %F, %x
		 *
		 * @example Convert `%A, %B %e, %Y, %l:%M %P` to `l, F j, Y, g:i a`, and vice versa for "Saturday, March 10, 2001, 5:16 pm"
		 * @link http://php.net/manual/en/function.strftime.php#96424
		 *
		 * @param string $format The format to parse.
		 * @param string $syntax The format's syntax. Either 'strf' for `strtime()` or 'date' for `date()`.
		 * @return bool|string Returns a string formatted according $syntax using the given $format or `false`.
		 */
		function date_format_to( $format, $syntax )
		{
			// http://php.net/manual/en/function.strftime.php
			$strf_syntax = [
				// Day - no strf eq : S (created one called %O)
				'%O', '%d', '%a', '%e', '%A', '%u', '%w', '%j',
				// Week - no date eq : %U, %W
				'%V',
				// Month - no strf eq : n, t
				'%B', '%m', '%b', '%h', '%-m',
				// Year - no strf eq : L; no date eq : %C, %g
				'%G', '%Y', '%y',
				// Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
				'%P', '%p', '%l', '%I', '%H', '%M', '%S',
				// Timezone - no strf eq : e, I, P, Z
				'%z', '%Z',
				// Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
				'%s'
			];
			// http://php.net/manual/en/function.date.php
			$date_syntax = [
				'S', 'd', 'D', 'j', 'l', 'N', 'w', 'z',
				'W',
				'F', 'm', 'M', 'M', 'n',
				'o', 'Y', 'y',
				'a', 'A', 'g', 'h', 'H', 'i', 's',
				'O', 'T',
				'U'
			];
			switch ( $syntax ) {
				case 'date':
					$from = $strf_syntax;
					$to   = $date_syntax;
					break;
				case 'strf':
					$from = $date_syntax;
					$to   = $strf_syntax;
					break;
				default:
					return false;
			}
			$pattern = array_map(
				function ( $s ) {
					return '/(?<!\\\\|\%)' . $s . '/';
				},
				$from
			);
			return preg_replace( $pattern, $to, $format );
		}
		/**
		 * Equivalent to `date_format_to( $format, 'date' )`
		 *
		 * @param string $strf_format A `strftime()` date/time format
		 * @return string
		 */
		function strftime_format_to_date_format( $strf_format )
		{
			return $this -> date_format_to( $strf_format, 'date' );
		}
		/**
		 * Equivalent to `convert_datetime_format_to( $format, 'strf' )`
		 *
		 * @param string $date_format A `date()` date/time format
		 * @return string
		 */
		function date_format_to_strftime_format( $date_format )
		{
			return $this -> date_format_to( $date_format, 'strf' );
		}
		
		function get_loading_icon($icon = null) {
			$loading = "fa-refresh fa-spin fa-fw";
	
			if (!empty($icon)) {		
				include($this -> plugin_base() . DS . 'includes' . DS . 'variables.php');
				if (!empty($spinners) && !empty($spinners[$icon])) {
					$loading = $spinners[$icon];
				}
			}
			
			return $loading;
		}
		
		function get_language_location() {
			$locale = get_locale();
			$language_file = $this -> plugin_name . '-' . $locale . '.mo';
			$language_location = $this -> get_option('language_location');
			
			switch ($language_location) {
				case 'plugin'				:
					$language_location = $this -> plugin_name . DS . 'languages' . DS . $language_file;
					break;
				case 'custom'				:
					$language_location = 'wp-mailinglist-languages' . DS . $language_file;
					break;
				case 'langdir'				:
					$language_location = LANGDIR . DS . 'plugins' . DS . $language_file;
					break;
			}
			
			return apply_filters('newsletters_language_location', $language_location);
		}
		
		public function detectDelimiter($csvFile = null) {
		    $delimiters = array(
		        ';' => 0,
		        ',' => 0,
		        "\t" => 0,
		        "|" => 0
		    );
		
		    $handle = fopen($csvFile, "r");
		    $firstLine = wp_unslash(fgets($handle));
		    fclose($handle); 
		    foreach ($delimiters as $delimiter => $count) {
		        $count = count(str_getcsv($firstLine, $delimiter));
		        $delimiters[$delimiter] = $count;
		    }
		
		    return array_search(max($delimiters), $delimiters);
		}
		
		function paymentmethod($pmethod = null)
        {
			
			if (!empty($pmethod)) {
				switch ($pmethod) {
					case 'paypal'				:
						$paymentmethod = __('PayPal', 'wp-mailinglist');
						break;
					case '2co'					:
						$paymentmethod = __('2CheckOut', 'wp-mailinglist');
						break;
				}
			}
			
			return apply_filters('newsletters_paymentmethod_title', $paymentmethod);
		}
		
		function get_image_sizes( $size = null) {
	
	        global $_wp_additional_image_sizes;
	
	        $sizes = array();
	        $get_intermediate_image_sizes = get_intermediate_image_sizes();
	
	        // Create the full array with sizes and crop info
	        foreach( $get_intermediate_image_sizes as $_size ) {
	            if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
						$sizes[$_size]['title'] = ucfirst($_size);
	                    $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
	                    $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
	                    $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
	            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
	                    $sizes[ $_size ] = array(
	                        	'title'			=>	$_size,
	                            'width' 		=> 	$_wp_additional_image_sizes[ $_size ]['width'],
	                            'height' 		=> 	$_wp_additional_image_sizes[ $_size ]['height'],
	                            'crop' 			=>  $_wp_additional_image_sizes[ $_size ]['crop']
	                    );
	            }
	        }
	
	        // Get only 1 size if found
	        if ( $size ) {
	                if( isset( $sizes[ $size ] ) ) {
	                        return $sizes[ $size ];
	                } else {
	                        return false;
	                }
	        }
	
	        return $sizes;
		}
		
		/**
		 * Format array for the datepicker
		 *
		 * WordPress stores the locale information in an array with a alphanumeric index, and
		 * the datepicker wants a numerical index. This function replaces the index with a number
		 */
		function strip_array_indices( $ArrayToStrip )
        {
		    foreach( $ArrayToStrip as $objArrayItem) {
		        $NewArray[] =  $objArrayItem;
		    }
		 
		    return( $NewArray );
		}
		
		function bar_chart($id = null, $attributes = array(), $data = array(), $options = array())
        {
			$default_attributes = array(
				'width'					=>	"100%",
				'height'				=>	"300px",
			);
			
			$attributes = wp_parse_args($attributes, $default_attributes);
			
			$default_options = array();			
			$options = wp_parse_args($options, $default_options);
			
			$this -> render('charts' . DS . 'bar', array('id' => $id, 'attributes' => $attributes, 'data' => $data, 'options' => $options), true, 'admin');
		}
		
		function pie_chart($id = null, $attributes = array(), $data = array(), $options = array())
        {
			$default_attributes = array(
				'width'					=>	300,
				'height'				=>	300,
			);
			
			$attributes = wp_parse_args($attributes, $default_attributes);
			
			$default_options = array();			
			$options = wp_parse_args($options, $default_options);
			
			if (!empty($attributes['width']) && $attributes['width'] < 150) {
				foreach ($data as $dkey => $dval) {
					$data[$dkey]['label'] = substr($dval['label'], 0, 1);
				}
			}
			
			$this -> render('charts' . DS . 'pie', array('id' => $id, 'attributes' => $attributes, 'data' => $data, 'options' => $options), true, 'admin');
		}
		
		function is_json($string = null)
        {
			if (!empty($string)) {
				if (is_string($string) && is_object(json_decode($string))) {
					return true;
				}
			}
			
			return false;
		}
		
		function hidden_type_operator($key = null)
        {
			$operator = false;
		
			if (!empty($key)) {
				switch ($key) {
					case 'post'			:
						$operator = "&#36;_POST";
						break;
					case 'get'			:
						$operator = "&#36;_GET";
						break;
					case 'global'		:
						$operator = "&#36;GLOBALS";
						break;
					case 'cookie'		:
						$operator = "&#36;_COOKIE";
						break;
					case 'session'		:
						$operator = "&#36;_SESSION";
						break;
					case 'server'		:
						$operator = "&#36;_SERVER";
						break;
				}
			}
			
			return $operator;
		}
		
		function fragment_cache($content = null, $object = null, $method = null, $data = null)
        {
			$output = $content;
			return wp_unslash($content);
		
			if (!empty($content)) {				
				if (is_plugin_active(plugin_basename('wp-super-cache/wp-cache.php'))) {			
					//return $content;
				
					//global $wp_cache_config_file, $newsletters_wpsc_cachedata;
					//include $wp_cache_config_file;
					//if (empty($wp_cache_mfunc_enabled)) { wp_cache_replace_line('^ *\$wp_cache_mfunc_enabled', "\$wp_cache_mfunc_enabled = 1;", $wp_cache_config_file); }
					//if (empty($wp_super_cache_late_init)) { wp_cache_replace_line('^ *\$wp_super_cache_late_init', "\$wp_super_cache_late_init = 1;", $wp_cache_config_file); }
					//if (empty($wp_cache_mod_rewrite)) { wp_cache_replace_line('^ *\$wp_cache_mod_rewrite', "\$wp_cache_mod_rewrite = 0;", $wp_cache_config_file); }
				} elseif (is_plugin_active(plugin_basename('w3-total-cache/w3-total-cache.php'))) {													
					$content = wp_unslash($content);				
					$output .= '<!--mfunc ' . W3TC_DYNAMIC_SECURITY . ' ?>' . $content . '<?php -->';
					$output .= $content;
					$output .= '<!--/mfunc ' . W3TC_DYNAMIC_SECURITY . ' -->';
				} elseif (is_plugin_active(plugin_basename('quick-cache/quick-cache.php'))) {
					define('QUICK_CACHE_ALLOWED', FALSE);
					$output = $content;
				}
			}
			
			return wp_unslash($output);
		}
		
		function wp_has_current_submenu($submenu = false)
        {
			$menu = false;
			
			if (!empty($submenu)) {
				if (preg_match("/^newsletters\-([^-]+)?/si", $submenu, $matches)) {
					$menu = $matches[0];
					
					$this -> sections = (object) $this -> sections;				
					switch ($menu) {
						case $this -> sections -> importexport				:
							$menu = $this -> sections -> subscribers;
							break;
					}
				}
			} ?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('li#toplevel_page_newsletters').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
				jQuery('li#toplevel_page_newsletters > a').attr('class', "wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_newsletters menu-top-last");
				<?php if (!empty($menu)) : ?>jQuery('li#toplevel_page_newsletters ul.wp-submenu li a[href="admin.php?page=<?php echo esc_html( $menu); ?>"]').attr('class', "current").parent().attr('class', "current");<?php endif; ?>
			});
			</script>
			
			<?php
		}
		
		function flag_by_country($countrycode = null)
        {
			$flagimage = false;
			if (!empty($countrycode)) {
				$flagpath = $this -> plugin_base() . DS . 'images' . DS . 'flags' . DS . strtolower($countrycode) . '.png';
				if (file_exists($flagpath)) {
					$flagurl = $this -> render_url('images/flags/' . strtolower($countrycode) . '.png', 'admin', false);
					$flagimage = $this -> help($countrycode, '<img src="' . $flagurl . '" alt="' . $countrycode . '" class="newsletters_flag" />', false);
				}
			}
			
			return $flagimage;
		}
		
		function help($help = null, $content = null, $link = null)
        {
			if (!empty($help)) {
				ob_start();
				
				$content = (empty($content)) ? '<i class="fa fa-question-circle fa-fw"></i>' : $content; ?>
				
				<span class="wpmlhelp"><a href="<?php echo esc_attr(wp_unslash($link)); ?>" <?php if (empty($link)) : ?>onclick="return false;"<?php endif; ?> title="<?php echo esc_attr(wp_unslash($help)); ?>"><?php echo wp_kses_post( wp_unslash($content)) ?></a></span>
				
				<?php
				
				$html = ob_get_clean();
				return apply_filters('newsletters_help', $html, $content, $link);
			}
		}
		
		function hex2rgb( $colour )
        {
		    if ( $colour[0] == '#' ) {
		            $colour = substr( $colour, 1 );
		    }
		    if ( strlen( $colour ) == 6 ) {
		            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		    } elseif ( strlen( $colour ) == 3 ) {
		            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		    } else {
		            return false;
		    }
		    $r = hexdec( $r );
		    $g = hexdec( $g );
		    $b = hexdec( $b );
		    
		    return array($r, $g, $b);
		}
		
		function eunique($subscriber = null, $email_id = null, $type = null)
        {
			if (!empty($subscriber) && (!empty($email_id) || !empty($type))) {
                $subscriber -> mailinglist_id = isset($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : 0;
				if (!empty($subscriber -> ID)) {
					$user = $subscriber;
					return md5($user -> ID . $user -> roles[0] . $email_id . $type . date_i18n("YmdH"));	
				} else {
					return md5($subscriber -> id . $subscriber -> mailinglist_id . $email_id . $type . date_i18n("YmdH"));	
				}
			}
		}
		
		function time_difference($time_one = null, $time_two = null, $interval = 'days')
        {
			$difference = 0;
		
			if (!empty($time_one) && !empty($time_two)) {
				switch ($interval) {
					case 'minutes'				:
						$one = strtotime($time_one);
						$two = strtotime($time_two);			
						$difference = floor(($one - $two) / (60));
						break;
					case 'hours'				:
						$one = strtotime($time_one);
						$two = strtotime($time_two);			
						$difference = floor(($one - $two) / (60 * 60));
						break;
					case 'days'					:
					default						:
						$one = strtotime($time_one);
						$two = strtotime($time_two);			
						$difference = floor(($one - $two) / (60 * 60 * 24));
						break;
					case 'weeks'				:
						$one = strtotime($time_one);
						$two = strtotime($time_two);			
						$difference = floor(($one - $two) / (60 * 60 * 24 * 7));
						break;
					case 'years'				:
						$one = strtotime($time_one);
						$two = strtotime($time_two);			
						$difference = floor(($one - $two) / (60 * 60 * 24 * 7 * 52));
						break;
				}
			}
			
			return $difference;
		}
		
		/*
		 * Matches each symbol of PHP date format standard
		 * with jQuery equivalent codeword
		 * @author Tristan Jahier
		 */
		function dateformat_PHP_to_jQueryUI($php_format = null)
        {
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
		        'Y' => 'yy',
		        'y' => 'y',
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
		    for($i = 0; $i < strlen($php_format); $i++) {
		        $char = $php_format[$i];
                if ($char === '\\') {
		            $i++;
                    if ($escaping) {
                        $jqueryui_format .= $php_format[$i];
                    } else {
                        $jqueryui_format .= '\'' . $php_format[$i];
                    }
		            $escaping = true;
		        } else {
                    if ($escaping) {
                        $jqueryui_format .= "'";
                        $escaping = false;
                    }
                    if (isset($SYMBOLS_MATCHING[$char])) {
		                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                    } else {
		                $jqueryui_format .= $char;
		        }
		    }
            }

		    return $jqueryui_format;
		}
		
		function days_difference($date_one = null, $date_two = null)
        {
			return $this -> time_difference($date_one, $date_two, 'days');
		
			$days = 0;
			
			if (!empty($date_one) && !empty($date_two)) {
				$one = strtotime($date_one);
				$two = strtotime($date_two);			
				$days = floor(($one - $two) / (60 * 60 * 24));
			}
			
			return $days;	
		}
		
		function field_type($type = null, $slug = null)
        {
			if (!empty($type)) {
				$fieldtypes = array(
					'special'		=>	__('Special', 'wp-mailinglist'),
					'text'			=>	__('Text Field', 'wp-mailinglist'),
					'textarea'		=>	__('Text Area', 'wp-mailinglist'),
					'select'		=>	__('Select Drop Down', 'wp-mailinglist'),
					'radio'			=>	__('Radio Buttons', 'wp-mailinglist'),
					'checkbox'		=>	__('Checkboxes', 'wp-mailinglist'),
					'file'			=>	__('File Upload', 'wp-mailinglist'),
					'pre_country'	=>	__('Predefined : Country Select', 'wp-mailinglist'),
					'pre_date'		=>	__('Predefined : Date Picker', 'wp-mailinglist'),
					'pre_gender'	=>	__('Predefined : Gender', 'wp-mailinglist'),
					'hidden'		=>	__('Hidden', 'wp-mailinglist'),
				);	
				
				switch ($slug) {
					case 'email'				:
						$field_type = __('Email Address', 'wp-mailinglist');
						break;
					case 'list'					:
						$field_type = __('Mailing List', 'wp-mailinglist');
						break;
					default 					:
						$field_type = $fieldtypes[$type];	
						break;
				}
				
				return $field_type;
			}
		
			return false;	
		}
		
		function uploads_path($dated = false)
        {
			if ($upload_dir = wp_upload_dir()) {	
				if ($dated) {
					return str_replace("\\", "/", $upload_dir['path']);	
				} else {
					return str_replace("\\", "/", $upload_dir['basedir']);
				}
			}
			
			return str_replace("\\", "/", WP_CONTENT_DIR . '/uploads');
		}
		
		function uploads_subdir()
        {
			$subdir = '';
		
			if ($upload_dir = wp_upload_dir()) {
				if (!empty($upload_dir['subdir'])) {
					$subdir = $upload_dir['subdir'];
				}
			}
			
			return $subdir;
		}
		
		function uploads_url()
        {
			if ($upload_dir = wp_upload_dir()) {
				return $upload_dir['baseurl'];
			}
		}
		
		function file_custom_field($value = null, $limit = false, $types = false, $field = null, $removefile = false)
        {
			$output = false;
			
			if (!empty($value)) {
				$currentfile = '<p class="newsletters-currentfile">';			
				$currentfile .= '<a class="btn btn-light btn-sm" href="' . esc_attr(wp_unslash($value)) . '" target="_blank"><i class="fa fa-paperclip"></i> ' . __('Uploaded file', 'wp-mailinglist') . '</a>';			
				
				if (!empty($removefile)) {
					$currentfile .= ' <a href="" class="text-danger" onclick="if (confirm(\'' . __('Are you sure you want to remove this file?', 'wp-mailinglist') . '\')) { jQuery(\'#newsletters_oldfile_' . $field -> id . '\').remove(); jQuery(this).parent().remove(); } return false;"><i class="fa fa-times"></i></a>';
				}
				
				$currentfile .= '</p>';
				$output .= $currentfile;
			}
	
			return $output;
		}
		
		function get_gravatar($email = null, $s = 50, $d = 'mm', $r = 'g', $img = true, $atts = array() )
        {
			// Uses WordPress get_avatar() function
			return get_avatar($email, $s, null, false);
		}
		
		function wordpress_usermeta_fields()
        {
			$usermeta = array(
				'first_name'				=>	__('First Name', 'wp-mailinglist'),
				'last_name'					=>	__('Last Name', 'wp-mailinglist'),
				'nickname'					=>	__('Nickname', 'wp-mailinglist'),
				'description'				=>	__('Biographical Info', 'wp-mailinglist'),
			);
			
			return $usermeta;
		}
		
		function RoundUptoNearestN($biggs)
        {
	       $rounders = (strlen($biggs) - 2) * -1;
	       $places = (strlen($biggs) -2);
	
	       $counter = 0;
	       while ($counter <= $places) {
	           $counter++;
                if ($counter == 1) {
                    $holder = $holder . '1';
                } else {
                    $holder = $holder . '0';
                }
	       }
	
	       $biggs = $biggs + $holder;
	       $biggs = round($biggs, $rounders);
            if ($biggs < 30) {
                $biggs = 30;
            } elseif ($biggs < 50) {
                $biggs = 50;
            } elseif ($biggs < 80) {
                $biggs = 80;
            } elseif ($biggs < 100) {
                $biggs = 100;
            }
	       return $biggs;
		}
		
		function next_scheduled($hook = null, $args = null)
        {
			switch ($hook) {
				case 'wp_queue_process_cron'					:
				case 'wp_import_process_cron'					:
					// leave the hook as it is			
					break;
				default 										:
					if (!preg_match("/(newsletters)/si", $hook)) {
						$hook = $this -> pre . '_' . $hook;
					}
					break;
			}
			
			$args = (empty($args)) ? array() : $args;
		
			if (!empty($hook) && $schedules = wp_get_schedules()) {						
				if ($hookinterval = wp_get_schedule($hook, $args)) {
					if ($hookschedule = wp_next_scheduled($hook, $args)) {				
						return $schedules[$hookinterval]['display'] . '<br/><small>' . __('Next: ', 'wp-mailinglist') . ' <b>' . get_date_from_gmt(date_i18n("Y-m-d H:i:s", $hookschedule, true), get_option('date_format') . ' ' . get_option('time_format')) . '</b></small>';
					} else {
						return __('This task does not have a next schedule.', 'wp-mailinglist');	
					}
				} else {
					switch ($hook) {
						case 'wp_queue_process_cron'			:
							return __('Queue empty or paused, schedule not running. When emails are queued, it will automatically start.', 'wp-mailinglist');
							break;
						case 'wp_import_process_cron'			:
							return __('No import currently scheduled', 'wp-mailinglist');
							break;
						default 								:
							return __('No schedule has been set for this task.', 'wp-mailinglist');		
							break;
					}
				}
			} else {
				return __('No cron schedules are available or no task was specified.', 'wp-mailinglist');	
			}
			
			return false;
		}
		
		function attachment_link($attachment = null, $icononly = false, $truncate = 20)
        {
			$attachmentfile = "";
			
			if (!empty($attachment['subdir'])) {
				$attachmentfile .= $attachment['subdir'] . '/';
			}
			
			$attachmentfile .= basename($attachment['filename']);
			$attachmentfile = ltrim($attachmentfile, "/");
		
			if (!empty($attachmentfile)) {			
				if ($icononly == false) {
					return '<a class="button" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '"><i class="fa fa-paperclip"></i> ' . $this -> truncate(basename($attachmentfile), $truncate) . '</a>';
				} else {
					return '<a class="button" style="text-decoration:none;" target="_blank" href="' . $this -> uploads_url() . '/' . $attachmentfile . '" title="' . basename($attachmentfile) . '"><i class="fa fa-paperclip"></i></a>';
				}
			}
			
			return false;
		}
		
		function system_email($slug = null)
        {
			$name = false;
			if (!empty($slug)) {
				switch ($slug) {
					case 'queuecomplete'		:
						$name = __('Queue Completed', 'wp-mailinglist');
						break;
					case 'expiration'			:
						$name = __('Expiration', 'wp-mailinglist');
						break;
					case 'authentication'		:
						$name = __('Authentication', 'wp-mailinglist');
						break;
					case 'testing'				:
						$name = __('Testing', 'wp-mailinglist');
						break;
					case 'confirmation'			:
						$name = __('Confirmation', 'wp-mailinglist');
						break;
					case 'subscription'			:
						$name = __('Subscription', 'wp-mailinglist');
						break;
					case 'unsubscription'		:
						$name = __('Unsubscription', 'wp-mailinglist');
						break;
					case 'newsletter'			:
					default  					:
						$name = __('Newsletter', 'wp-mailinglist');
						break;
				}
			}
			
			$name = apply_filters('newsletters_system_email', $name, $slug);
			return $name;
		}
		
		function section_name($slug = null)
        {
			$name = "";
			
			if (!empty($slug)) {
				switch ($slug) {
					case 'welcome'			:
						$name = __('Overview', 'wp-mailinglist');
						break;
					case 'submitserial'		:
						$name = __('Submit Serial', 'wp-mailinglist');
						break;
					case 'forms'			:
						$name = __('Subscribe Forms', 'wp-mailinglist');
						break;
					case 'send'				:
						$name = __('Create Newsletter', 'wp-mailinglist');
						break;
					case 'autoresponders'	:
						$name = __('Autoresponders', 'wp-mailinglist');
						break;
					case 'autoresponderemails'	:
						$name = __('Autoresponder Emails', 'wp-mailinglist');
						break;
					case 'lists'			:
						$name = __('Mailing Lists', 'wp-mailinglist');
						break;
					case 'groups'			:
						$name = __('Groups', 'wp-mailinglist');
						break;
					case 'subscribers'		:
						$name = __('Subscribers', 'wp-mailinglist');
						break;
					case 'fields'			:
						$name = __('Custom Fields', 'wp-mailinglist');
						break;
					case 'importexport'		:
						$name = __('Import/Export', 'wp-mailinglist');
						break;
					case 'themes'			:
						$name = __('Templates', 'wp-mailinglist');
						break;
					case 'templates'		:
						$name = __('Email Snippets', 'wp-mailinglist');
						break;
					case 'templates_save'	:
						$name = __('Save Email Snippets', 'wp-mailinglist');
						break;
					case 'queue'			:
						$name = __('Email Queue', 'wp-mailinglist');
						break;
					case 'history'			:
						$name = __('Sent & Draft Emails', 'wp-mailinglist');
						break;
					case 'emails'			:
						$name = __('All Emails', 'wp-mailinglist');
						break;
					case 'links'			:
						$name = __('Links', 'wp-mailinglist');
						break;
					case 'clicks'			:
						$name = __('Clicks', 'wp-mailinglist'); 
						break;
					case 'orders'			:
						$name = __('Subscribe Orders', 'wp-mailinglist');
						break;
					case 'settings'			:
						$name = __('General Configuration', 'wp-mailinglist');
						break;
					case 'settings_subscribers'	:
						$name = __('Subscribers Configuration', 'wp-mailinglist');
						break;
					case 'settings_templates'	:
						$name = __('System Emails Configuration', 'wp-mailinglist');
						break;
					case 'settings_system'		:
						$name = __('System Configuration', 'wp-mailinglist');
						break;
					case 'settings_tasks'		:
						$name = __('Scheduled Tasks', 'wp-mailinglist'); 
						break;
					case 'settings_updates'		:
						$name = __('Updates', 'wp-mailinglist'); 
						break;
					case 'settings_api'			:
						$name = __('API', 'wp-mailinglist');
						break;
                    case 'view_logs'			:
                        $name = __('View Logs', 'wp-mailinglist');
                        break;
					case 'extensions'			:
						$name = __('Extensions', 'wp-mailinglist');
						break;
					case 'extensions_settings'	:
						$name = __('Extensions Settings', 'wp-mailinglist');
						break;
					case 'support'				:
						$name = __('Support & Help', 'wp-mailinglist');
						break;
					case 'lite_upgrade'			:
						$name = __('Upgrade to PRO', 'wp-mailinglist');
						break;
				}
			}
			
			return $name;
		}
		
		function getppt($interval = null)
        {
			switch ($interval) {
				case 'daily'		:
					$t = "D";
					break;
				case 'weekly'		:
					$t = "W";
					break;
				case 'monthly'		:
				case '2months'		:
				case '3months'		:
				case 'biannually'	:
				case '9months'		:
					$t = "M";
					break;
				case 'yearly'		:
					$t = "Y";
					break;
				default				:
					$t = "D";
					break;
			}
			
			return $t;
		}
		
		function getpptd($interval)
        {
			switch ($interval) {
				case 'daily'		:
					$d = "1";
					break;
				case 'weekly'		:
					$d = "1";
					break;
				case 'monthly'		:
					$d = "1";
					break;
				case '2months'		:
					$d = "2";
					break;
				case '3months'		:
					$d = "3";
					break;
				case 'biannually'	:
					$d = "6";
					break;
				case '9months'		:
					$d = "9";
					break;
				case 'yearly'		:
					$d = "1";
					break;
				default				:
					$d = "1";
					break;
			}
			
			return $d;
		}
	
	    function priority_val($priority_key)
        {
	        switch ($priority_key) {
	            case 1              :
	                $priority_val = "High";
	                break;
	            case 3              :
	                $priority_val = "Normal";
	                break;
	            case 5              :
	                $priority_val = "Low";
	                break;
	            default             :
	                $priority_val = "Normal";
	                break;
	        }
	
	        return $priority_val;
	    }
		
		function link($name = null, $href = null, $options = array())
        {
			if (!empty($name) || $name == "0") {
				$defaults = array(
					'target' 		=> 	'_self', 
					'title' 		=> 	$name,
					'onclick'		=>	"",
					'class'			=>	"",
				);
				
				$r = wp_parse_args($options, $defaults);
				extract($r, EXTR_SKIP);
					
				ob_start();
				
				?><a class="<?php echo esc_html($class); ?>" href="<?php echo esc_html($href); ?>" onclick="<?php echo esc_html($onclick); ?>" title="<?php echo esc_html($title); ?>" target="<?php echo esc_html($target); ?>"><?php echo esc_html($name); ?></a>
				
				<?php
				
				$link = ob_get_clean();
				return $link;
			}
			
			return false;
		}
		
		function reCaptchaErrorMessage($errorCode = null)
        {
		    $messages = array(
			    'missing-input-secret'		=>	__('The secret parameter is missing.', 'wp-mailinglist'),
				'invalid-input-secret'		=>	__('The secret parameter is invalid or malformed.', 'wp-mailinglist'),
				'missing-input-response'	=>	__('Captcha response is needed.', 'wp-mailinglist'),
				'invalid-input-response'	=>	__('Captcha response is invalid.', 'wp-mailinglist'),
		    );
		    
		    if (!empty($messages[$errorCode])) {
			    return $messages[$errorCode];
		    }
		    
		    return false;
	    }
		
		function tabi()
        {
			global $wpmltabindex;
            if (empty($wpmltabindex) || !$wpmltabindex) {
                $wpmltabindex = 1;
            };
			return $wpmltabindex;
		}
		
		function tabindex($optinid = null, $onlynumber = false)
        {
			global $wpmltabindex;
			
			if (empty($wpmltabindex) || !$wpmltabindex) {
				$wpmltabindex = 1;
			}
			
			$wpmltabindex++;
			$string = $optinid . $wpmltabindex;
			$string = preg_replace("/[^0-9]+/si", "", $string);
			
			if (empty($onlynumber)) {
				$tabindex = 'tabindex="9' . $string . '"';
			} else {
				$tabindex = '9' . $string;
			}
			return $tabindex;
		}
		
		function str_time($string = null)
        {
			$time = time();
			
			if (!empty($string)) {
				$time = strtotime($this -> gen_date("Y-m-d H:i:s", false, false, true) . " " . $string);
			}
			
			return $time;
		}
		
		function gen_date($format = "Y-m-d H:i:s", $time = false, $gmt = false, $includetime = false, $localize = false)
        {
			if (empty($format)) {
				$format = get_option('date_format'); 
				
				if (!empty($includetime)) {
					$format .= ' ' . get_option('time_format');
				}
			} 
			
			if (!empty($localize)) {
				$this -> set_timezone();
				$newtime = (empty($time)) ? false : $time;
				return date_i18n($format, $newtime, $gmt);
			} else {
				$newtime = (empty($time)) ? time() : $time;
				return empty($time) ? current_time($format) : date($format, $newtime);
			}
		}
		
		function gender($gender = null)
        {
			switch ($gender) {
				case 'male'			:
					return __('Male', 'wp-mailinglist');
					break;
				case 'female'		:
					return __('Female', 'wp-mailinglist');
					break;
			}
		}
		
		function currency()
        {
			$currency = $this -> get_option('currency');
			$currencies = maybe_unserialize($this -> get_option('currencies'));		
			return $currencies[$currency]['symbol'];
		}
		
		function field_value($name = null, $language = false)
        {
			$value = "";
			
			if (!empty($name)) {				
				if ($mn = $this -> strip_mn($name)) {
					$model = $mn[1];
					$field = $mn[2];
					
					global ${$mn[1]}, $Db;
					
                    if (!empty($Db -> {$model} -> data)) {
						if (is_array($Db -> {$model} -> data) && !empty($Db -> {$model} -> data[$model])) {					
							$value = $Db -> {$model} -> data[$model] -> {$field};
						} else {					
							$value = $Db -> {$model} -> data -> {$field};
						}
					} else {										
                        if (isset( ${$model} -> data )) {
                            if (is_array(${$model}->data) && !empty(${$model}->data[$model])) {
                                $value = ${$model}->data[$model]->{$mn[2]};
						} else {											
                                if ('object' == gettype(${$model}->data)) {
                                    // error_log('its an object');
                                    // error_log(json_encode( $field));
                                    // error_log(json_encode(${$model} -> data));
                                    $value = isset(${$model}->data->{$field}) ? ${$model}->data->{$field}
                                        : '';
                                } else {
                                    // error_log('its an ' . gettype(${$model} -> data));
                                    $value = isset(${$model}->data->{$field}) ? (is_array(${$model}->data->{$field}) ? implode(" ", ${$model}->data->{$field}) : ${$model}->data->{$field}) : '';
                                }
                            }
						}
					}
					
					if ($this -> language_do() && !empty($language)) {					
						if ($mn[2] == "fieldoptions") {												
							$alloptions = maybe_unserialize($value);
							$optionarray = array();
							
							if (!empty($alloptions)) {
								foreach ($alloptions as $alloption) {
									$alloptionsplit = $this -> language_split($alloption);
									$optionarray[] = trim($alloptionsplit[$language]);
								}
							}
							
							return trim(@implode("\r\n", $optionarray));
						} else {						
							return $this -> language_use($language, $value);
						}
					}
				}
			}
	
	        return $value;
		}
		
		function has_field_error($name = null)
        {
			if (!empty($name)) {
				if ($mn = $this -> strip_mn($name)) {
					global ${$mn[1]}, $Db;
					
					$model = $mn[1];
					$field = $mn[2];
					
					if (!empty($Db -> {$model}() -> errors[$field])) {
						return true;
					} elseif (!empty(${$mn[1]} -> errors[$mn[2]])) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		function field_error($name = null)
        {
			if (!empty($name)) {		
				if ($mn = $this -> strip_mn($name)) {
					global ${$mn[1]}, $Db;
					
					$model = $mn[1];
					$field = $mn[2];
					
					if (!empty($Db -> {$model}() -> errors[$field])) {
						ob_start();
						echo '<div class="alert alert-danger ui-state-error ui-corner-all"><i class="fa fa-exclamation-triangle"></i> ' . $Db -> {$model}() -> errors[$field] . '</div>';
						return ob_get_clean();
					} elseif (!empty(${$mn[1]} -> errors[$mn[2]])) {
						ob_start();
						echo '<div class="alert alert-danger ui-state-error ui-corner-all"><i class="fa fa-exclamation-triangle"></i> ' . ${$mn[1]} -> errors[$mn[2]] . '</div>';
						return ob_get_clean();
					}
				}
			}
			
			return false;
		}
		
		function field_id($name = null)
        {
			if (!empty($name)) {
				if ($matches = $this -> strip_mn($name)) {
					$id = $matches[1] . '.' . $matches[2];
					return $id;
				}
			}
		
			return false;
		}
		
		function file_upload_error($code = 0)
        {
			if (!empty($code)) {
				switch ($code) {
					case 1			:
						$error = __('The uploaded file exceeds the PHP upload_max_filesize directive.', 'wp-mailinglist');
						break;
					case 2			:
						$error = __('The uploaded file exceeds the max_file_size directive specified in the form.', 'wp-mailinglist');
						break;
					case 3			:
						$error = __('The uploaded file was only partially uploaded.', 'wp-mailinglist');
						break;
					case 4			:
						$error = __('No file was uploaded.', 'wp-mailinglist');
						break;
					case 6			:
						$error = __('Missing a temporary folder.', 'wp-mailinglist');
						break;
					case 7			:
						$error = __('Failed to write file to disk.', 'wp-mailinglist');
						break;
					case 8			:
						$error = __('A PHP extension stopped the file upload.', 'wp-mailinglist');
						break;
					default			:
						$error = __('An error occurred. Please try again.', 'wp-mailinglist');
						break;
				}
				
				return $error;
			}
			
			return false;
		}
		
		function sanitize($string = null, $sep = '-')
        {
			if (!empty($string)) {
				//$string = ereg_replace("[^0-9a-z" . $sep . "]", "", strtolower(str_replace(" ", $sep, $string)));
				$string = strtolower(preg_replace("/[^0-9A-Za-z" . $sep . "]/si", "", str_replace(" ", $sep, $string)));
				$string = preg_replace("/" . $sep . "[" . $sep . "]*/si", $sep, $string);
				
				return $string;
			}
		
			return false;
		}
		
		function strip_mn($name = null)
        {
			if (!empty($name)) {
				if (preg_match("/(.*?)\[(.*?)\]/si", $name, $matches)) {
					return $matches;
				}
			}
		
			return false;
		}
		
		function truncate($text = null, $length = 100, $ending = '...', $exact = true, $considerHtml = false)
        {
			if (is_array($ending)) {
				extract($ending);
			}
			
			if ($considerHtml) {
				if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
					return $text;
				}
	
				preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
				$total_length = strlen($ending);
				$open_tags = array();
				$truncate = '';
	
				foreach ($lines as $line_matchings) {
					if (!empty($line_matchings[1])) {
						if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
							$pos = array_search($tag_matchings[1], $open_tags);
							if ($pos !== false) {
								unset($open_tags[$pos]);
							}
						} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
							array_unshift($open_tags, strtolower($tag_matchings[1]));
						}
						$truncate .= $line_matchings[1];
					}
	
					$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
					if ($total_length+$content_length > $length) {
						$left = $length - $total_length;
						$entities_length = 0;
						if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
							foreach ($entities[0] as $entity) {
								if ($entity[1]+1-$entities_length <= $left) {
									$left--;
									$entities_length += strlen($entity[0]);
								} else {
									break;
								}
							}
						}
						$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
						break;
					} else {
						$truncate .= $line_matchings[2];
						$total_length += $content_length;
					}
	
					if ($total_length >= $length) {
						break;
					}
				}
			} else {
				if (strlen($text) <= $length) {
					return $text;
				} else {
					$truncate = substr($text, 0, $length - strlen($ending));
				}
			}
	
			if (!$exact) {
				$spacepos = strrpos($truncate, ' ');
				if (isset($spacepos)) {
					$truncate = substr($truncate, 0, $spacepos);
				}
			}
	
			$truncate .= $ending;
	
			if ($considerHtml) {
				foreach ($open_tags as $tag) {
					$truncate .= '</' . $tag . '>';
				}
			}
	
			return $truncate;
		}
		
		function queryString($params, $name = null)
        {
			$ret = "";
			foreach ($params as $key => $val) {
				if (is_array($val)) {
					if ($name == null) {
						$ret .= $this -> queryString($val, $key);
					} else {
						$ret .= $this -> queryString($val, $name . "[$key]");   
					}
				} else {
					if ($name != null) {
						$ret .= esc_html($name . "[$key]") . "=" . esc_html($val) . "&";
					} else {
						$ret .= esc_html($key) . "=" . esc_html($val) . "&";
					}
				}
			}
			
			return rtrim($ret, "&");   
		} 
		
		function retainquery($add = null, $old_url = null, $endslash = true, $onlyquery = false)
        {
			$add_parts = $add;
			if (!is_array($add)) {
				$add = str_replace("&amp;", "&", $add);
				parse_str($add, $add_parts);
			}
			
			$url = (empty($old_url)) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : rtrim($old_url, '&');
			return add_query_arg($add_parts, $url);
		}
	}
}

?>