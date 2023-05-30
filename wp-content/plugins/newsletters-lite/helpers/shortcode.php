<?php
// phpcs:ignoreFile
if (!class_exists('wpmlShortcodeHelper')) {
	class wpmlShortcodeHelper extends wpMailPlugin
	{
		var $name = 'Shortcode';

		function __construct()
		{
			return true;
		}
		
		function woocommerce_products($atts = array())
		{
			global $woocommerce;
			
			$output = '';
			
			$defaults = array(
				'perrow'			=>	3,
				'number'			=>	9,
				'featured'			=>	0,
				'bestselling'		=>	0,
				// Image settings
				'showimage'			=>	1,
				'imagesize'			=>	'thumbnail',
				// Title settings
				'showtitle'			=>	1,
				// Price settings
				'showprice'			=>	1,
				// Button settings
				'showbutton'		=>	1,
				'buttontext'		=>	__('Buy Now', 'wp-mailinglist'),
				'orderby'			=>	'post_date',
				'order'				=>	"DESC",
			);
			
			extract(shortcode_atts($defaults, $atts, 'woocommerce_products'));
			
			if (strpos($imagesize, ',') !== false) {
				$sizes = explode(",", $imagesize);
				if (!empty($sizes) && is_array($sizes)) {
					$imagesize = $sizes;
				}
			}
			
			// Fetch products
			$params = array(
				'posts_per_page'		=>	$number,
				'post_type'				=>	'product',
				'orderby'				=>	$orderby,
				'order'					=>	$order,
			);
			
			if (!empty($featured) && $featured == 1) {
				$params['post__in'] = wc_get_featured_product_ids();
			}
			
			if (!empty($bestselling) && $bestselling == 1) {
				$params['meta_key'] = 'total_sales';
				$params['orderby'] = 'meta_value_num';
			}
			
			$wc_query = new WP_Query($params);
			
			$c = 0;
			$breaker = $perrow;
			
			if ($wc_query -> have_posts()) {
				$output .= '<div class="newsletters-wc-products">';
				$output .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
	            <tr>
	                <td>
	                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="mobilewrapper">
	                        <tr>
	                            <td>
	                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mobilewrapper" align="center">';
				
				while ($wc_query -> have_posts()) {
	                $wc_query -> the_post();
	                $product_id = get_the_ID();
					$currency = get_woocommerce_currency_symbol();
					$product = wc_get_product($product_id);
					$price = $product -> get_price();
					$image = get_the_post_thumbnail_url($product_id, $imagesize, null);
					$title = __(get_the_title());
	                
	                if ($c == 0 || $c%$breaker == 0) {
						$output .= '<tr>';
					}
					
					$output .= '<th align="center" class="colsplit">';
					$output .= '<table class="contents">';
					$output .= '<tr>';
					$output .= '<td class="product">';
					
					if (!empty($showimage)) {
						$output .= '<a href="' . get_permalink($product_id) . '"><img class="email-image" src="' . $image . '" alt="' . esc_html($title) . '" /></a>';
					}
					
					if (!empty($showtitle)) {
						$output .= '<h4 class="product-title"><a href="' . get_permalink($product_id) . '">' . $title . '</a></h4>';
					}
					
					if (!empty($showprice)) {
						$output .= '<p class="price">' . $currency . $price . '</p>';
					}
					
					if (!empty($showbutton)) {
						$output .= '<div class="button"><a href="' . get_permalink($product_id) . '">' . esc_html($buttontext) . '</a></div>';
					}
					
					$output .= '</td>';
					$output .= '</tr>';
					$output .= '</table>';
					$output .= '</th>';
	                
	                $c++;
	                if ($c%$breaker == 0 || $c == count((empty($wc_query -> posts_count) ? array() : ($wc_query -> posts_count)))) {
		             	$output .= '</tr>'; 
		            }
                }
                
	                $output .= '</table>
			                            </td>
			                        </tr>
			                    </table>
			                </td>
			            </tr>
			        </table>';
                
                $output .= '<style type="text/css">
                .newsletters-wc-products table {
	            	width: 100%;   
	            }
	            
	            .newsletters-wc-products td, 
	            .newsletters-wc-products th {
		        	border: none;   
		        }
		        
		        .newsletters-wc-products .colsplit {
			        width: ' . round(100 / $perrow) . '%;
			    }
		        
		        .newsletters-wc-products td.product {
			    	padding: 0 4% 10% 4%;   
			    	text-align: center;
			    }
			    
			    .newsletters-wc-products .email-image {
				    width: 100%;
				    max-width: 100%;
				    height: auto;
					margin-bottom: 15px;   
				}
				
				.newsletters-wc-products .price {
					font-weight:bold;
					font-size:24px;
					color:#666;
					margin-bottom:15px;
					margin-top:15px;
				}
				
				.newsletters-wc-products .product-title {
					display: block;
					text-align: center;
				}
				
				.newsletters-wc-products .button {
					padding: 0;
					text-decoration:none;
					text-transform: uppercase;
					display:inline-block;
				}
                
                <style type="text/css" data-premailer="ignore">
				@media screen and (max-device-width: 600px), screen and (max-width: 600px) {
					.mobilewrapper{
						width: 100% !important; 
						height: auto !important;
					}
					
					.colsplit{
						width: 100% !important; 
						float: left !important; 
						display:block !important;
					}
					
					.newsletters-wc-products .product {
						padding: 0 0 10% 0 !important;	
					}	
				}
				</style>';

				wp_reset_postdata();
			} else {
				$output = __('No products are available', 'wp-mailinglist');
			}
			
			return $output;
		}
		
		function video($atts = array(), $content = null)
		{
			$output = "";

			$defaults = array(
				'url'			=>	false,
				'width'			=>	false,
				'height'		=>	false,
			);

			extract(shortcode_atts($defaults, $atts));

			if (!empty($url)) {
				$output = $this -> autoembed_callback($url, $width, $height);
			}

			return $output;
		}

		function bloginfo($atts = array(), $content = null)
		{
			$output = "";

			$defaults = array(
				'show'			=>	"name",
				'filter'		=>	"raw",
			);

			extract(shortcode_atts($defaults, $atts));

			if (!empty($show)) {
				if ($value = get_bloginfo($show, $filter)) {
					$output = __(wp_unslash($value));
				}
			}

			return $output;
		}

		function newsletters_if($atts = array(), $content = null)
		{
			global $newsletters_history_id, $Db;

			$output = "";

			$defaults = array(
				0				=>	false,
				'id'			=>	false,
			);

			extract(shortcode_atts($defaults, $atts));

			switch ($atts[0]) {
				case 'newsletters_content'					:
					if (!empty($newsletters_history_id) && !empty($atts['id'])) {
						if ($contentarea = $this -> Content() -> find(array('number' => $atts['id'], 'history_id' => $newsletters_history_id))) {
							$output = do_shortcode(wp_unslash(esc_html($content)));
						}
					}
					break;
			}

			return $output;
		}

		function subscriberscount($atts = array(), $content = null)
		{
			global $wpdb, $Subscriber, $SubscribersList, $Mailinglist;
			$subscriberscount = 0;

			$defaults = array(
				'list'				=>	false
			);

			extract(shortcode_atts($defaults, $atts));

			if (!empty($list)) {
				$query = "SELECT COUNT(`rel_id`) FROM " . $wpdb -> prefix . $SubscribersList -> table . " LEFT JOIN "
				. $wpdb -> prefix . $Mailinglist -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".list_id = "
				. $wpdb -> prefix . $Mailinglist -> table . ".id WHERE " . $wpdb -> prefix . $Mailinglist -> table . ".id = '" . esc_sql($list) . "'";
			} else {
				$query = "SELECT COUNT(`id`) FROM `" . $wpdb -> prefix . $Subscriber -> table . "`";
			}

			$query_hash = md5($query);
			if ($ob_count = $this -> get_cache($query_hash)) {
				$count = $ob_count;
			} else {
				$count = $wpdb -> get_var($query);
				$this -> set_cache($query_hash, $count);
			}

			if (!empty($count)) {
				$subscriberscount = $count;
			}

			return $subscriberscount;
		}

		function post_thumbnail($atts = array(), $content = null)
		{
			global $post;

			$output = "";
			$thepost = (empty($atts['post_id'])) ? $post : get_post($atts['post_id']);
			$atts['post_id'] = $thepost -> ID;

			$defaults = array(
				'post_id'			=>	false,
				'align'				=>	"left",
				'hspace'			=>	15,
				'size'				=>	"thumbnail",
				'alt'				=>	$thepost -> post_title,
				'link'				=>	$thepost -> guid,
				'title'				=>	$thepost -> post_title,
			);

			extract(shortcode_atts($defaults, $atts));

			if (strpos($size, ',') !== false) {
				$sizes = explode(",", $size);
				if (!empty($sizes) && is_array($sizes)) {
					$size = $sizes;
				}
			}

			if (function_exists('has_post_thumbnail') && has_post_thumbnail($thepost -> ID)) {
				$output .= (!empty($link)) ? '<a href="' . $link . '" title="' . esc_attr(esc_html($title)) . '">' : '';

				$thumbnail_attr = array('align' => $align, 'style' => "margin-right:" . $hspace . "px;", 'hspace' => $hspace, 'title' => esc_html($title), 'alt' => $alt);
				$thumbnail_attr = apply_filters('newsletters_post_thumbnail_attr', $thumbnail_attr, $thepost -> ID);

				$output .= get_the_post_thumbnail($thepost -> ID, $size, $thumbnail_attr);
				$output .= (!empty($link)) ? '</a>' : '';
			} else {
				//Added by Ted Eytan
				$output .= (!empty($link)) ? '<a href="' . $link . '" title="' . esc_attr(esc_html($title)) . '">' : '';
				$thumbnail_attr = array('align' => $align, 'style' => "margin-right:" . $hspace . "px;", 'hspace' => $hspace, 'title' => esc_html($title), 'alt' => $alt);
				$thumbnail_attr = apply_filters('newsletters_post_thumbnail_attr', $thumbnail_attr, $thepost -> ID);
				require_once($this -> plugin_base() . DS . 'vendors' . DS . 'gettheimage.php');
				$output .= get_the_image(array('post_id' => $thepost -> ID, 'scan' => true, 'size' => $size, 'echo' => false));
				$output .= (!empty($link)) ? '</a>' : '';
			}

			wp_reset_query();
			return do_shortcode(apply_filters('newsletters_post_thumbnail_output', $output, $thepost));
		}

		function post_permalink($atts = array(), $content = null)
		{
			global $post;
			$output = "";
			$post_id = (empty($atts['post_id'])) ? $post -> ID : $atts['post_id'];

			if ($permalink = get_permalink($post_id)) {
				$output .= $permalink;
			}

			wp_reset_query();
			return $output;
		}

		function posts_sendas($atts = array(), $content = null)
		{
			global $post, $shortcode_posts, $shortcode_sendas, $shortcode_categories, $shortcode_post_language, $shortcode_post_showdate;
			$atts['post_id'] = (empty($atts['post_id'])) ? $post -> ID : $atts['post_id'];

			$shortcode_posts = false;
			$shortcode_sendas = false;
			$shortcode_categories = false;

			$output = "";
			$defaults = array(
				'post_id'			=> 	false,
				'showdate'			=>	"Y",
				'language'			=>	false,
				'eftype'			=>	"excerpt",
				'target'			=>	"_self",
				'thumbnail_size'		=>	"thumbnail",
				'thumbnail_align'		=>	"left",
				'thumbnail_hspace'		=>	"15",
				'thumbnail_class'		=>	"newsletters-thumbnail",
			);

			$r = shortcode_atts($defaults, $atts);
			extract($r);

			global $wpml_eftype;
			$wpml_eftype = $eftype;

			global $shortcode_thumbnail;
			$shortcode_thumbnail = array(
				'size'				=>	$thumbnail_size,
				'align'				=>	$thumbnail_align,
				'hspace'			=>	$thumbnail_hspace,
				'class'				=>	$thumbnail_class,
			);

			$arguments = array(
				'post_id'			=>	$post_id,
				'showdate'			=>	$showdate,
				'language'			=>	$language,
			);

			if (!empty($language)) {
				$this -> language_set($language);
			}

			foreach ($r as $rkey => $rval) {
				global ${'wpml_' . $rkey};
				${'wpml_' . $rkey} = $rval;
			}

			if (!empty($post_id)) {
				if ($post = get_post($post_id)) {
					$shortcode_post_showdate = $showdate;

					if ($this -> language_do()) {
						$shortcode_post_language = $language;
						$post = $this -> language_use($language, $post, false);
						$shortcode_sendas = array($post);
						$output = do_shortcode($this -> et_message('sendas', false, $language));
						//$output = do_shortcode('[newsletters_posts_loop_wrapper]');
					} else {
						$shortcode_sendas = array($post);
						$output = do_shortcode($this -> et_message('sendas'));
						//$output = do_shortcode('[newsletters_posts_loop_wrapper]');
					}
				}
			}

			wp_reset_query();
			return $output;
		}

		function posts_single($atts = array(), $content = null)
		{
			global $post, $shortcode_posts, $shortcode_post_language, $shortcode_post_showdate;
			$atts['post_id'] = (empty($atts['post_id'])) ? $post -> ID : $atts['post_id'];

			$output = "";
			$defaults = array(
				'post_id'			=> 	false,
				'showdate'			=>	"Y",
				'language'			=>	false,
				'eftype'			=>	"excerpt",
				'target'			=>	"_self",
				'thumbnail_size'		=>	"thumbnail",
				'thumbnail_align'		=>	"left",
				'thumbnail_hspace'		=>	"15",
				'thumbnail_class'		=>	"newsletters-thumbnail",
				'hidethumbnail'			=> 'N'
			);

			$r = shortcode_atts($defaults, $atts);
			extract($r);

			global $wpml_eftype;
			$wpml_eftype = $eftype;

			global $shortcode_thumbnail;
			$shortcode_thumbnail = array(
				'size'				=>	$thumbnail_size,
				'align'				=>	$thumbnail_align,
				'hspace'			=>	$thumbnail_hspace,
				'class'				=>	$thumbnail_class,
			);

			$arguments = array(
				'post_id'			=>	$post_id,
				'showdate'			=>	$showdate,
				'language'			=>	$language,
				'hidethumbnail'		=>  $hidethumbnail

			);

			if (!empty($language)) {
				$this -> language_set($language);
			}

			foreach ($r as $rkey => $rval) {
				global ${'wpml_' . $rkey};
				${'wpml_' . $rkey} = $rval;
			}

			if (!empty($post_id)) {
				if ($post = get_post($post_id)) {					
					$shortcode_post_showdate = $showdate;

					if ($this -> language_do()) {
						$shortcode_post_language = $language;
						$post = $this -> language_use($language, $post, false);
						$shortcode_posts = array($post);
						$output = do_shortcode($this -> et_message('posts', false, $language, '' ,  $arguments['hidethumbnail']));
					} else {
						$shortcode_posts = array($post);
						$output = do_shortcode($this -> et_message('posts', '' , '' , '', $arguments['hidethumbnail']), false, '');
					}
				}
			}

			wp_reset_query();
			return $output;
		}

		function posts_multiple($atts = array(), $content = null)
		{
			global $shortcode_posts, $shortcode_post_language, $shortcode_post_showdate;
			$output = "";

			$defaults = array(
				'numberposts' 			=> 	10,
				'showdate'				=>	"Y",
				'orderby' 				=> 	"post_date",
				'order' 				=> 	"DESC",
				'category' 				=> 	null,
				'language'				=>	false,
				'post_type'				=>	"post",
				'eftype'				=>	"excerpt",
				'target'				=>	"_self",
				'thumbnail_size'		=>	"thumbnail",
				'thumbnail_align'		=>	"left",
				'thumbnail_hspace'		=>	"15",
				'thumbnail_class'		=>	"newsletters-thumbnail",
				'hidethumbnail'			=> 'N'

			);

			$arguments = shortcode_atts($defaults, $atts);
            if (empty($arguments['category'])) {
                $arguments['category'] = false;
            }

			if (!empty($arguments['post_type'])) {
				if (($new_post_types = @explode(",", $arguments['post_type'])) !== false) {
					$arguments['post_type'] = $new_post_types;
				}
			}

			$currentlanguage = $arguments['language'];
			$arguments['suppress_filters'] = 0;
			extract($arguments);

			if (!empty($currentlanguage)) {
				$this -> language_set($currentlanguage);
			}

			global $wpml_eftype, $wpml_target;
			$wpml_eftype = $eftype;
			$wpml_target = $target;

			global $shortcode_thumbnail;
			$shortcode_thumbnail = array(
				'size'				=>	$thumbnail_size,
				'align'				=>	$thumbnail_align,
				'hspace'			=>	$thumbnail_hspace,
				'class'				=>	$thumbnail_class,
			);
			
			$arguments = apply_filters('newsletters_shortcode_posts_multiple_arguments', $arguments);

			if ($posts = get_posts($arguments)) {
				$shortcode_post_showdate = $showdate;

				if ($this -> language_do()) {
					$shortcode_post_language = $currentlanguage;
					foreach ($posts as $pkey => $post) {
						$posts[$pkey] = $this -> language_use($currentlanguage, $post, false);
					}

					$shortcode_posts = $posts;
					$output = do_shortcode($this -> et_message('posts', false, $language, '' ,  $arguments['hidethumbnail']));
					//$output = do_shortcode('[newsletters_posts_loop_wrapper]');
				} else {
					$shortcode_posts = $posts;
					$output = do_shortcode($this -> et_message('posts', '' , '' , '', $arguments['hidethumbnail']), false, '');
					//$output = do_shortcode('[newsletters_posts_loop_wrapper]');
				}
			}

			wp_reset_query();
			return $output;
		}

		function shortcode_posts($atts = array(), $content = null, $tag = null)
		{
			global $wpml_eftype, $wpml_target, $Html, $shortcode_posts, $shortcode_categories, $shortcode_category, $shortcode_categories_done,
			$shortcode_post, $shortcode_sendas, $shortcode_post_row, $shortcode_post_language, $shortcode_post_showdate, $shortcode_thumbnail;

			$return = "";

			if (!empty($wpml_eftype) && $wpml_eftype == "full" && $tag == "post_excerpt") {
				$tag = "post_content";
			}

			$defaults = array(
				'style'					=>	"",
			);
			
			if (!empty($shortcode_post)) {
				
				// Visual Composer stuff
				if (class_exists('WPBMap')) {
					if (method_exists(WPBMap, 'addAllMappedShortcodes')) {
						WPBMap::addAllMappedShortcodes();
					}
					
					// Run it through the_content filter	
					remove_filter('the_content', 'MeprRulesCtrl::rule_content', 999999);
					$shortcode_post -> post_content = apply_filters('the_content', $shortcode_post -> post_content);
				}
			}

			$arguments = shortcode_atts($defaults, $atts);
			extract($arguments);

			switch ($tag) {
				case 'category_heading'						:
				case 'newsletters_category_heading'			:
					$category_heading = "";

					if (!empty($shortcode_category)) {
						if (empty($shortcode_categories_done) || (!empty($shortcode_categories_done) && !in_array($shortcode_category -> cat_ID, $shortcode_categories_done))) {
							$category_heading = '<a href="' . get_category_link($shortcode_category -> cat_ID) . '">' . esc_html($shortcode_category -> name) . '</a>';
							$shortcode_categories_done[] = $shortcode_category -> cat_ID;
						}
					}

					return do_shortcode($category_heading);
					break;
				case 'post_loop'				:
				case 'newsletters_post_loop'	:
					if (!empty($shortcode_categories)) {
						$shortcode_post_row = 1;
						foreach ($shortcode_categories as $category) {
							$shortcode_category = $category['category'];
							$shortcode_posts = $category['posts'];

							foreach ($shortcode_posts as $post) {
								$shortcode_post = $post;
								$return .= do_shortcode($content);
								$shortcode_post_row++;
							}
						}
					} elseif (!empty($shortcode_posts)) {
						$shortcode_post_row = 1;
						foreach ($shortcode_posts as $post) {
							$shortcode_post = $post;
							$return .= do_shortcode($content);
							$shortcode_post_row++;
						}
					} elseif (!empty($shortcode_sendas)) {
						$shortcode_post_row = 1;
						foreach ($shortcode_sendas as $post) {
							$shortcode_post = $post;
							$return .= do_shortcode($content);
							$shortcode_post_row++;
						}
					}

					wp_reset_query();
					$shortcode_post = false;
					return $return;
					break;
				case 'post_id'					:
				case 'newsletters_post_id'		:
					if (!empty($shortcode_post)) {
						return $shortcode_post -> ID;
					}
					break;
				case 'post_author'				:
				case 'newsletters_post_author'	:
					global $post;
					$post = $shortcode_post;
					setup_postdata($post);
					$return = get_the_author();
					wp_reset_postdata();
					return do_shortcode($return);
					break;
				case 'post_anchor'				:
				case 'newsletters_post_anchor'	:
					if (!empty($shortcode_post)) {
						$linktitle = (empty($content)) ? esc_html($shortcode_post -> post_title) : esc_html($content);
						return do_shortcode('<a style="' . esc_attr(wp_unslash($style)) . '" title="' . esc_attr(wp_unslash($linktitle)) . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '">' . $linktitle . '</a>');
					}
					break;
				case 'post_title'				:
				case 'newsletters_post_title'	:
					if (!empty($shortcode_post)) {
						return do_shortcode(esc_html($shortcode_post -> post_title));
					}
					break;
				case 'post_link'				:
				case 'newsletters_post_link'	:
					if (!empty($shortcode_post)) {
						return do_shortcode($this -> direct_post_permalink(esc_html($shortcode_post -> ID)));
					}
					break;
				case 'post_date_wrapper'				:
				case 'newsletters_post_date_wrapper'	:
					if (empty($shortcode_post_showdate) || (!empty($shortcode_post_showdate) && $shortcode_post_showdate == "Y")) {
						return do_shortcode($content);
					} else {
						return "";
					}
					break;
				case 'post_date'				:
				case 'newsletters_post_date'	:
					if (!empty($shortcode_post)) {
						$format = get_option('date_format');
						return $Html -> gen_date($format, strtotime($shortcode_post -> post_date));
					}
					break;
				case 'post_thumbnail'			:
				case 'newsletters_post_thumbnail'	:
					if (empty($shortcode_post)) {
						// there is no $shortcode_post, this may be an independent [newsletters_post_thumbnail...] shortcode
						return $this -> post_thumbnail($atts, false);
					} else {
						if (!empty($atts) && is_array($atts)) {
							if (!empty($shortcode_thumbnail)) {
								$atts = wp_parse_args($atts, $shortcode_thumbnail);
							}
						} else {
							$atts = $shortcode_thumbnail;
						}
	
						$defaults = array(
							'size' 			=> 	"thumbnail",
							'align'			=>	"left",
							'hspace'		=>	"15",
							'class'			=>	"newsletters_thumbnail",
						);
	
						$defaults = apply_filters('newsletters_post_thumbnail_defaults', $defaults);
	
						$style = false;
						if (!empty($align) && !empty($hspace)) {
							switch ($align) {
								case 'left'					:
									$style = "margin-right:" . $hspace . "px;";
									break;
								case 'right'				:
									$style = "margin-left:" . $hspace . "px;";
									break;
							}
						}
	
						extract(shortcode_atts($defaults, $atts));
	
						if (strpos($size, ',') !== false) {
							$sizes = explode(",", $size);
							if (!empty($sizes) && is_array($sizes)) {
								$size = $sizes;
							}
						}						
	
						if (!empty($shortcode_post)) {
							if (function_exists('has_post_thumbnail') && has_post_thumbnail($shortcode_post -> ID)) {
								$return .= '<a target="' . $wpml_target . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '">';
								$attr = apply_filters('newsletters_post_thumbnail_attr', array('style' => $style, 'align' => $align, 'hspace' => $hspace, 'class' => $class), $shortcode_post -> ID);
								$return .= get_the_post_thumbnail($shortcode_post -> ID, $size, $attr);
								$return .= '</a>';
								return do_shortcode(apply_filters('newsletters_post_thumbnail_output', $return, $shortcode_post));
							} else {
								// added by Ted Eytan
								$return .= '<a target="' . $wpml_target . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '">';
								$attr = apply_filters('newsletters_post_thumbnail_attr', array('style' => $style, 'align' => $align, 'hspace' => $hspace, 'class' => $class), $shortcode_post -> ID);
								require_once($this -> plugin_base() . DS . 'vendors' . DS . 'gettheimage.php');								
								$return .= get_the_image(array('post_id' => $shortcode_post -> ID, 'scan' => true, 'size' => $size, 'echo' => false));
								$return .= '</a>';
								return do_shortcode(apply_filters('newsletters_post_thumbnail_output', $return, $shortcode_post));
							}
						}
					}
					break;
				case 'post_excerpt'				:
				case 'newsletters_post_excerpt'	:
				
					$postswpautop = $this -> get_option('postswpautop');
												
					if (empty($wpml_eftype) || (!empty($wpml_eftype) && $wpml_eftype != "full")) {
						$this -> add_filter('excerpt_length');
						$this -> add_filter('excerpt_more');
						$this -> add_filter('post_password_required', 'post_password_required', 10, 2);
						$this -> add_filter('the_content_more_link', 'excerpt_more');

						if (!empty($shortcode_post)) {
							global $post;
							$post = $shortcode_post;
							setup_postdata($post);
							
							if (preg_match('/<!--more(.*?)?-->/', $post -> post_content)) {
								if (!empty($postswpautop)) {
									$return .= do_shortcode(wpautop(__(get_the_content())));
								} else {
									$return .= do_shortcode(__(get_the_content()));
								}
							} else {
								if (!empty($postswpautop)) {
									$return .= do_shortcode(wpautop(__(get_the_excerpt())));
								} else {
									$return .= do_shortcode(__(get_the_excerpt()));
								}
							}
							
							wp_reset_postdata();
						}
					} else {						
						global $post;
						$post = $shortcode_post;
						setup_postdata($post);
						global $more;
						$more = true;
						
						if (!empty($postswpautop)) {
							$return = do_shortcode(wpautop(__(get_the_content())));
						} else {
							$return = do_shortcode(__(get_the_content()));
						}
							
						wp_reset_postdata();
					}

					return apply_filters('newsletters_post_excerpt', $return, $shortcode_post);
				case 'post_content'				:
				case 'newsletters_post_content'	:
				
					$postswpautop = $this -> get_option('postswpautop');
				
					if (empty($wpml_eftype) || (!empty($wpml_eftype) && $wpml_eftype != "excerpt")) {
						global $post;
						$post = $shortcode_post;
						setup_postdata($post);
						
						if (!empty($postswpautop)) {
							$return = wpautop(__(get_the_content()));
						} else {
							$return = __(get_the_content());	
						}
						
						wp_reset_postdata();
					} else {
						$this -> add_filter('excerpt_length');
						$this -> add_filter('excerpt_more');
						$this -> add_filter('post_password_required', 'post_password_required', 10, 2);
						$this -> add_filter('the_content_more_link', 'excerpt_more');

						if (!empty($shortcode_post)) {
							global $post;
							$post = $shortcode_post;
							setup_postdata($post);
							global $more;
							$more = true;
							
							if (preg_match('/<!--more(.*?)?-->/', $post -> post_content)) {
								if (!empty($postswpautop)) {
									$return .= do_shortcode(wpautop(__(get_the_content())));
								} else {
									$return .= do_shortcode(__(get_the_content()));
								}
							} else {
								if (!empty($postswpautop)) {
									$return .= do_shortcode(wpautop(__(get_the_excerpt())));
								} else {
									$return .= do_shortcode(__(get_the_excerpt()));
								}
							}
							
							wp_reset_postdata();
						}
					}

					return do_shortcode($return);
					break;
			}

			return do_shortcode(wp_unslash($content));
		}

		function direct_post_permalink($id = null)
		{
			global $Html, $shortcode_post_language, $shortcode_post;

			$post_id = (empty($id)) ? $shortcode_post -> ID : $id;

			if (!empty($post_id)) {
				if ($permalink = get_permalink($post_id)) {
					if ($this -> language_do()) {
						$permalink = $this -> language_converturl($permalink, $shortcode_post_language);
						$permalink = $Html -> retainquery('lang=' . $shortcode_post_language, $permalink);
					}

					return $permalink;
				}
			}

			return false;
		}

		function get_the_excerpt($excerpt = null)
		{
			return $excerpt;

			$excerpt_settings = $this -> get_option('excerpt_settings');
			if (!empty($excerpt_settings)) {
		    	$excerpt = do_shortcode(wp_trim_words(get_the_content(), $this -> excerpt_length()));
		    }

		    return $excerpt;
		}
		
		function post_password_required($required = null, $post = null)
		{
			$required = false;
			
			return apply_filters('newsletters_post_password_required', $required, $post);
		}

		function excerpt_length($length = null)
		{
			$excerpt_settings = $this -> get_option('excerpt_settings');

			if (!empty($excerpt_settings)) {
				$length = $this -> get_option('excerpt_length');
			}

			return $length;
		}

		function excerpt_more($more = null)
		{
			$excerpt_settings = $this -> get_option('excerpt_settings');

			if (!empty($excerpt_settings)) {
				global $shortcode_post, $shortcode_post_language, $wpml_target;
				$excerpt_more = $this -> get_option('excerpt_more');
                if (is_array($excerpt_more)) {
                    $excerpt_more = $this -> language_join($excerpt_more);
                }
				$excerpt_more = ($this -> language_do()) ? $this -> language_use($shortcode_post_language, $excerpt_more) : $excerpt_more;

				global ${'newsletters_acolor'};
                $style  = '';
				if (!empty(${'newsletters_acolor'})) {
					$style = ' style="color:' . ${'newsletters_acolor'} . ';"';
				}

				$more = ' <span class="newsletters_readmore_holder"><a class="newsletters_readmore newsletters_link" target="' . $wpml_target . '" href="' . $this -> direct_post_permalink($shortcode_post -> ID) . '"' . $style . '>' . esc_html($excerpt_more) . '</a></span>';
			}

			return $more;
		}

		function datestring($atts = array(), $content = null)
		{
			global $Html;
			
			$defaults = array(
				//'format'		=>	"%d/%m/%Y",
				'format'		=>	get_option('date_format'),
				'time'			=>	date_i18n('r'),
			);

			extract(shortcode_atts($defaults, $atts));
			$locale = get_locale();
			setlocale(LC_ALL, apply_filters('newsletters_setlocale', $locale));
			$format = $Html -> strftime_format_to_date_format($format);

			//$output = utf8_encode(date_i18n($format, strtotime($time)));
			$output = (date_i18n($format, strtotime($time)));
			return $output;
		}

		function template($atts = array(), $content = null)
		{
			global $wpdb;

			$defaults = array('id' => false);
			extract(shortcode_atts($defaults, $atts));

			if (!empty($id)) {
				$templatequery = "SELECT * FROM " . $wpdb -> prefix . parent::Template() -> table . " WHERE id = '" . esc_sql($id) . "' LIMIT 1";

				$query_hash = md5($templatequery);
				if ($ob_template = $this -> get_cache($query_hash)) {
					$template = $ob_template;
				} else {
					$template = $wpdb -> get_row($templatequery);
					$this -> set_cache($query_hash, $template);
				}

				if (!empty($template)) {
					$output = wpautop(do_shortcode(__(wp_unslash($template -> content))));
				}

				$output = apply_filters('newsletters_snippet', $output, $template);
			}

			return $output;
		}

		function history($atts = array(), $content = null)
		{
			global $wpdb, $Db, $HistoriesList, $Html;

			$output = "";

			$defaults = array(
				'number' 		=> 	false, 
				'order' 		=> 	"DESC", 
				'orderby' 		=> 	"modified", 
				'list_id' 		=> 	false, 
				'linksonly'		=>	false,
				'index' 		=> 	true
			);
				
			$r = shortcode_atts($defaults, $atts);
			extract($r);

			$listscondition = "";
			$l = 1;

			if (!empty($list_id)) {
				if ($mailinglists = explode(",", $list_id)) {
					$listscondition = " (";

					foreach ($mailinglists as $mailinglist_id) {
						$listscondition .= "" . $wpdb -> prefix . $HistoriesList -> table . ".list_id = '" . esc_sql($mailinglist_id) . "'";

						if (count($mailinglists) > $l) {
							$listscondition .= " OR ";
						}

						$l++;
					}

					$listscondition .= ")";
				}
			} else {
				$listscondition = " 1 = 1";
			}

			$query = "SELECT DISTINCT " . $wpdb -> prefix . $HistoriesList -> table . ".history_id, " .
			$wpdb -> prefix . parent::History() -> table . ".id, " .
			$wpdb -> prefix . parent::History() -> table . ".message, " .
			$wpdb -> prefix . parent::History() -> table . ".modified, " .
			$wpdb -> prefix . $HistoriesList -> table . ".history_id, " . $wpdb -> prefix . parent::History() -> table . ".subject FROM `" . $wpdb -> prefix . $HistoriesList -> table . "` LEFT JOIN `" .
			$wpdb -> prefix . parent::History() -> table . "` ON " .
			$wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . parent::History() -> table . ".id" .
			" WHERE" . $listscondition . " AND " . $wpdb -> prefix . parent::History() -> table . ".sent > '0' && " . $wpdb -> prefix . parent::History() -> table . ".senddate <= '" . $Html -> gen_date() . "'" .
			" ORDER BY " . $wpdb -> prefix . parent::History() -> table . "." . esc_sql($orderby) . " " . esc_sql($order) . "";
            if (!empty($number)) {
                $query .= " LIMIT " . esc_sql($number) . "";
            }

			$query_hash = md5($query);
			if ($ob_emails = $this -> get_cache($query_hash)) {
				$emails = $ob_emails;
			} else {
				$emails = $wpdb -> get_results($query);
				$this -> set_cache($query_hash, $emails);
			}

			if (!empty($emails)) {
				$output = $this -> render('history', array('emails' => $emails, 'history_index' => $index, 'linksonly' => $linksonly), false, 'default');
				$output = wp_unslash(do_shortcode($output));
				return $output;
			}

			return $output;
		}

		function meta($atts = array(), $content = null)
		{
			global $post, $shortcode_post;

			$thepost = (empty($atts['post_id'])) ? ((empty($shortcode_post)) ? $post : $shortcode_post) : get_post($atts['post_id']);
			$atts['post_id'] = $thepost -> ID;

			$defaults = array(
				'post_id' 			=>	false,
				'key'				=>	false,
			);

			$allatts = shortcode_atts($defaults, $atts);
			extract($allatts);

			if (!empty($post_id)) {
				global $post_ID;
				$oldpostid = $post_ID;
				$post_ID = $post_id;

				ob_start();
				if (empty($key)) {
					the_meta();
				} else {
					echo get_post_meta($thepost -> ID, $key, true);
				}

				$content = ob_get_clean();

				$post_ID = $oldpostid;
			}

			return apply_filters('newsletters_meta_shortcode', $content, $allatts, $thepost);
		}

		function subscribe_link($atts = array(), $content = null)
		{
			global $current_subscriber;
			$content = (empty($content)) ? __('Subscribe', 'wp-mailinglist') : $content;

			$output = "";

			$defaults = array(
				'list'			=>	false,
			);

			$r = shortcode_atts($defaults, $atts);
			extract($r);

			if (!empty($current_subscriber) && !empty($list)) {
				$url = $this -> gen_subscribe_url($current_subscriber, $list);
				$output = '<a href="' . $url . '" class="newsletters_subscribe newsletters_link">' . esc_attr(wp_unslash($content)) . '</a>';
			}

			return $output;
		}

		function authenticate($atts = array(), $content = null)
		{
			global $Html, $current_subscriber;

			$output = '';

			$defaults = array(
				'class'				=>	"newsletters_link newsletters_authenticate",
				'style'				=>	"",
			);

			$r = shortcode_atts($defaults, $atts);
			extract($r);

			if (!empty($current_subscriber)) {
				$subscriberauth = $this -> gen_auth($current_subscriber -> id);
				$url = $Html -> retainquery('method=loginauth&email=' . $current_subscriber -> email . '&subscriberauth=' . $subscriberauth, $this -> get_managementpost(true));
				$text = (!empty($content)) ? $content : $this -> get_option('authenticatelinktext');
                if (empty($text)) {
                    $text = __('Authenticate now', 'wp-mailinglist');
                }
                $link = '<a href="' . $url . '" class="' . esc_attr(wp_unslash($class)) . '" style="' . esc_attr(wp_unslash($style)) . '">' . esc_attr(wp_unslash(__($text))) . '</a>';

				$output = $link;
			}

			return $output;
		}

		function activate($atts = array(), $content = null)
		{
			global $Html, $current_subscriber, $current_theme_id, $Subscriber;
			$output = '';

			$subscriber = $current_subscriber;
			$theme_id = $current_theme_id;

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

			$defaults = array(
				'class'				=>	"newsletters_link newsletters_activate",
                'style'				=>	isset($style) ? $style : '',
				'urlonly'			=>	false,
			);

			$r = shortcode_atts($defaults, $atts);
			extract($r);

			if (!empty($subscriber)) {				
				$linktext = apply_filters('newsletters_activation_link_text', esc_html($this -> get_option('activationlinktext')));
				$authkey = $this -> gen_auth($subscriber -> id, (!empty($subscriber -> mailinglist_id) ? $subscriber -> mailinglist_id : false));
				$mailinglist_id = (empty($subscriber -> mailinglists)) ? $subscriber -> mailinglist_id : @implode(",", $Subscriber -> mailinglists($subscriber -> id, false, false, false));

				$querystring = $this -> pre . 'method=activate&' . $this -> pre . 'subscriber_id=' . $subscriber -> id . '&' . $this -> pre . 'mailinglist_id=' . $mailinglist_id . '&authkey=' . $authkey;
				$url = $Html -> retainquery($querystring, $this -> get_managementpost(true));

				if (empty($subscriber -> format) || $subscriber -> format == "html" || !empty($urlonly)) {
					$activationlink = '<a class="newsletters_activate newsletters_link" href="' . $url . '" title="' . $linktext . '" style="' . $style . '">' . $linktext . '</a>';
				} else {
					$activationlink = $url;
				}

				$output = apply_filters('newsletters_activation_link', $activationlink, $url, $linktext, $style, $linktext);
			}

			return $output;
		}

		function subscribe($atts = array(), $content = null)
		{
			global $Html, $Subscriber, $post;
            $post_id = isset($post -> ID) ? $post -> ID : 0;

            if (is_feed()) {
                return;
            }

			if (empty($atts['form'])) {
				if ($rand_transient = get_transient('newsletters_shortcode_subscribe_rand_' . $post_id)) {
					$rand = $rand_transient;
				} else {
					$rand = rand(999, 9999);
					set_transient('newsletters_shortcode_subscribe_rand_' . $post_id, $rand, HOUR_IN_SECONDS);
				}

				$number = 'embed' . $rand;
				$widget_id = 'newsletters-' . $number;
				$instance = $this -> widget_instance($number, $atts);

				$defaults = array(
					'list' 				=> 	"select",
					'id' 				=> 	false,
					'lists'				=>	false,
					'ajax'				=>	$instance['ajax'],
					'button'			=>	$instance['button'],
					'captcha'			=>	$instance['captcha'],
					'acknowledgement'	=>	$instance['acknowledgement'],
				);

				$r = shortcode_atts($defaults, $atts);
				extract($r);

				$action = ($this -> language_do() && !empty($instance['language'])) ? $this -> language_converturl(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $instance['language']) : sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']));
				$action = $Html -> retainquery($this -> pre . 'method=optin', $action) . '#' . $widget_id;

				$output = "";
				$output .= '<div id="' . $widget_id . '" class="newsletters ' . $this -> pre . ' widget_newsletters">';
				$output .= '<div id="' . $widget_id . '-wrapper">';

				if (!empty($_GET['success'])) {
					$output .= '<div class="newsletters-acknowledgement">' . wp_kses_post(wpautop($instance['acknowledgement'])) . '</div>';
				} else {
					$output .= $this -> render('widget', array('action' => $action, 'errors' => $Subscriber -> errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), false, 'default');
				}

				$output .= '</div>';
				$output .= '</div>';
			} elseif (!empty($atts['form'])) {
				if ($form = $this -> Subscribeform() -> find(array('id' => $atts['form']))) {
					global ${'newsletters_form' . $form -> id . '_success'};
					if (!empty(${'newsletters_form' . $form -> id . '_success'})) {
						if (!empty($form -> confirmationtype) && $form -> confirmationtype == "message") {
							if (!empty($form -> confirmation_message)) {
								$output = '<div class="newsletters-acknowledgement">' . wpautop(esc_html($form -> confirmation_message)) . '</div>';
							}
						}
					} else {
						$output = $this -> render('subscribe', array('form' => $form, 'errors' => $Subscriber -> errors), false, 'default');	
					}
				}
			}

			return $output;
		}
	}
}

?>
