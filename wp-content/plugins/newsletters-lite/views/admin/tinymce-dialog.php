<?php // phpcs:ignoreFile ?>
<?php

global $wpdb, $Mailinglist;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title><?php esc_html_e('Newsletter Functions', 'wp-mailinglist'); ?></title>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
		<script language="javascript" type="text/javascript">
		jQuery.noConflict();
		var newsletters_ajaxurl = '<?php echo esc_url_raw( admin_url('admin-ajax.php')) ?>?';
		
		var _self = tinyMCEPopup;
		function init () {
			tinyMCEPopup.resizeToInnerSize();
		}
		
		function closePopup() {
			tinyMCEPopup.close();
		}
		
		function insertTag() {
			var tag = "";
			var subscriptionpanel = document.getElementById('subscription_panel');
			var postspanel = document.getElementById('posts_panel');
			var thumbnailpanel = document.getElementById('thumbnail_panel');
			var historypanel = document.getElementById('history_panel');
			var templatepanel = document.getElementById('template_panel');
			
			if (subscriptionpanel.className.indexOf('current') != -1) {
				var list = jQuery('#list').val();
				var lists = jQuery('#lists').val();
				
				if (list == "select") {
					tag += '[newsletters_subscribe list="select"';
					if (lists) { tag += ' lists="' + lists + '"'; }
				} else if (list == "checkboxes") {
					tag += '[newsletters_subscribe list="checkboxes"';
					if (lists) { tag += ' lists="' + lists + '"'; }
				} else {
					tag += '[newsletters_subscribe list="' + list + '"';
				}
				
				tag += ']';
			}
			
			if (postspanel.className.indexOf('current') != -1) {
				var posts_insert = jQuery('input[name=posts_insert]:checked').val();
				
				if (jQuery('input[name="postslanguage"]').length > 0) {
					var postslanguage = jQuery('input[name="postslanguage"]:checked').val();
				} else { var postslanguage = false; }
				
				if (posts_insert == "single") {
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
									
					if (posts_single_id = jQuery('#posts_post_menu').val()) {
						tag += '[newsletters_post post_id="' + posts_single_id + '" showdate="' + post_showdate + '" eftype="' + post_eftype + '"';
						if (postslanguage) { tag += ' language="' + postslanguage + '"'; }
						tag += ']';
					} else {
						alert('<?php esc_html_e('Please select a post from the menu.', 'wp-mailinglist'); ?>');
					}
				} else if (posts_insert == "multiple") {
					var posts_number = jQuery('#posts_number').val();
					var posts_showdate = jQuery('input[name="posts_showdate"]:checked').val();
					var posts_eftype = jQuery('input[name="posts_eftype"]:checked').val();
					var posts_orderby = jQuery('#posts_orderby').val();
					var posts_order = jQuery('#posts_order').val();
					var posts_categories = jQuery('#posts_categories').val();
					
					tag += '[newsletters_posts numberposts="' + posts_number + '" orderby="' + posts_orderby + '" showdate="' + posts_showdate + '" eftype="' + posts_eftype + '" order="' + posts_order + '" category="' + posts_categories + '"';
					if (postslanguage) { tag += ' language="' + postslanguage + '"'; }
					
					/* Are there custom post type checkboxes? */
					if (jQuery('input[name="posts_types[]"]').length > 0) {
						var posts_types = new Array();
						jQuery('input[name="posts_types[]"]:checked').each(function() {
							posts_types.push(jQuery(this).val());
						});
						
						tag += ' post_type="' + posts_types + '"';
					}
					
					tag += ']';
				}
			}
			
			if (thumbnailpanel.className.indexOf('current') != -1) {
				var thumbnail_post_id = jQuery('#thumbnail_post_id').val();
				var thumbnail_size = jQuery('#thumbnail_size').val();
				tag += '[newsletters_post_thumbnail';
				
				if (thumbnail_post_id != "") {
					tag += ' post_id="' + thumbnail_post_id + '"';
				}
				
				tag += ' size="' + thumbnail_size + '"';
				tag += ']';
			}
			
			if (historypanel.className.indexOf('current') != -1) {
				tag += "[newsletters_history";
				
				if (history_number = jQuery('#history_number').val()) {
					tag += ' number="' + history_number + '"';
				}
				
				if (history_orderby = jQuery('#history_orderby').val()) {
					tag += ' orderby="' + history_orderby + '"';	
				}
				
				if (history_order = jQuery('#history_order').val()) {
					tag += ' order="' + history_order + '"';
				}
				
				if (history_lists = jQuery('#history_lists').val()) {
					if (history_lists != "") {
						tag += ' list_id="' + history_lists + '"';	
					}
				}
				
				tag += "]";
			}
			
			if (templatepanel.className.indexOf('current') != -1) {
				var template_id = jQuery('#template').val();
				if (!template_id) {
					alert('<?php esc_html_e('Choose a snippet', 'wp-mailinglist'); ?>');
				} else {
					tag += '[newsletters_template id="' + template_id + '"]';
				}
			}
			
			if (window.tinyMCE && tag != "") {
				window.tinyMCE.execCommand('mceInsertContent', false, tag);
				tinyMCEPopup.editor.execCommand('mceRepaint');
				tinyMCEPopup.close();
			}
			return;
		}	
		
		function posts_changeCategory() {			
			var posts_category_menu = jQuery('#posts_category_menu');
			var posts_post_menu = jQuery('#posts_post_menu');	
			
			if (jQuery('input[name="postslanguage"]').length > 0) {				
				var postslanguage = jQuery('input[name="postslanguage"]:checked').val();
	
				if (postslanguage == "" || postslanguage == "undefined" || postslanguage == undefined) {
					alert('<?php esc_html_e('Please choose a language.', 'wp-mailinglist'); ?>');
					return false;
				}
			}
			
			var post_type = new Array();
			if (jQuery('input[name="post_types[]"]').length > 0) {
				jQuery('input[name="post_types[]"]:checked').each(function() {
					post_type.push(jQuery(this).val());
				});
			} else { post_type.push('post'); }

			jQuery('#posts_multiple_message').show();
			
			jQuery.post(newsletters_ajaxurl + "action=newsletters_posts_by_category&security=<?php echo esc_html( wp_create_nonce('posts_by_category')) ?>&cat_id=" + posts_category_menu.val(), {category:posts_category_menu.val(),language:postslanguage,post_type:post_type}, function(response) {
				posts_post_menu.empty().html(response);
				jQuery('#posts_multiple_message').hide();
			});
		}
		</script>
		
		<style type="text/css" media="screen">
			table th { vertical-align: top; }
			.panel_wrapper { border-top: none !important; }
			.panel_wrapper div.current { height: auto !important; }
			#list { width: 200px; }
			label { cursor:pointer; }
		</style>	
	</head>

	<body onload="init(); document.body.style.display = '';">	
		<form onsubmit="insertTag(); return false;" action="#">
        	<div class="tabs">
				<ul>
					<li id="subscription_tab" class="current"><span><a href="javascript:mcTabs.displayTab('subscription_tab','subscription_panel');" onmousedown="return false;"><?php esc_html_e('Subscription Form', 'wp-mailinglist'); ?></a></span></li>
					<li id="posts_tab"><span><a href="javascript:mcTabs.displayTab('posts_tab','posts_panel');" onmousedown="return false;"><?php esc_html_e('Posts', 'wp-mailinglist'); ?></a></span></li>
					<li id="thumbnail_tab"><span><a href="javascript:mcTabs.displayTab('thumbnail_tab','thumbnail_panel');" onmousedown="return false;"><?php esc_html_e('Thumbnail', 'wp-mailinglist'); ?></a></span></li>
					<li id="history_tab"><span><a href="javascript:mcTabs.displayTab('history_tab','history_panel');" onmousedown="return false;"><?php esc_html_e('Email History', 'wp-mailinglist'); ?></a></span></li>
					<li id="template_tab"><span><a href="javascript:mcTabs.displayTab('template_tab','template_panel');" onmousedown="return false;"><?php esc_html_e('Template', 'wp-mailinglist'); ?></a></span></li>
				</ul>
			</div>
        
			<div class="panel_wrapper">
				<div id="subscription_panel" class="panel current">
                	<br/>
                    
                    <p><?php esc_html_e('Choose to which list(s) subscribers will be subscribed to.', 'wp-mailinglist'); ?></p>
                    
					<table cellpadding="4" cellspacing="4" border="0">
						<tbody>
							<tr>
								<td nowrap="nowrap" valign="top"><label for="list"><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?>:</label></td>
								<td>                                															
									<select onchange="if (this.value == 'select' || this.value == 'checkboxes') { jQuery('#listsdiv').show(); } else { jQuery('#listsdiv').hide(); }" class="widefat" style="width:210px;" name="list" id="list">
										<optgroup label="<?php esc_html_e('Multiple Choice', 'wp-mailinglist'); ?>">
											<option value="select"><?php esc_html_e('Show Select Drop Down', 'wp-mailinglist'); ?></option>
											<option value="checkboxes"><?php esc_html_e('Show Checkbox List', 'wp-mailinglist'); ?></option>
										</optgroup>
										
										<?php if ($lists = $Mailinglist -> select($private = true)) : ?>
											<optgroup label="<?php esc_html_e('Specific Mailing List', 'wp-mailinglist'); ?>">
											<?php foreach ($lists as $list_id => $list_title) : ?>
												<option value="<?php echo esc_html( $list_id); ?>"><?php echo esc_html( $list_title); ?></option>
											<?php endforeach; ?>
											</optgroup>
										<?php endif; ?>
									</select>
                                    <br/><small><?php esc_html_e('You can allow multiple choices or a specific mailinglist.', 'wp-mailinglist'); ?></small>
								</td>
							</tr>
							<tr id="listsdiv">
								<td nowrap="nowrap" valign="top"><label for="lists"><?php esc_html_e('Lists:', 'wp-mailinglist'); ?></label></td>
								<td>
									<input type="text" name="lists" value="" id="lists" />
									<br/><small><?php esc_html_e('Comma separated list IDs to show (eg. 11,3,7), leave empty for all.', 'wp-mailinglist'); ?></small>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
                
                <!-- WordPress Posts -->
                <div id="posts_panel" class="panel">
                	<br/>
                    <table cellpadding="4" cellspacing="4" border="0">
                    	<tbody>
                        	<?php if ($this -> language_do()) : ?>
                                <tr>
                                    <td nowrap="nowrap" valign="top"><label for=""><?php esc_html_e('Language:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                        <?php if ($languages = $this -> language_getlanguages()) : ?>
                                            <?php foreach ($languages as $language) : ?>
                                                <label><input onclick="posts_changeCategory();" type="radio" name="postslanguage" value="<?php echo esc_html( $language); ?>" id="postslanguage<?php echo esc_html( $language); ?>" /> <?php echo wp_kses_post( $this -> language_flag($language)); ?></label>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                        
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        	<tr>
                            	<td nowrap="nowrap" valign="top"><label for="posts_insert_single"><?php esc_html_e('Insert:', 'wp-mailinglist'); ?></label></td>
                                <td>
                                	<label><input onclick="jQuery('#posts_insert_single_div').show(); jQuery('#posts_insert_multiple_div').hide();" type="radio" name="posts_insert" value="single" id="posts_insert_single" /> <?php esc_html_e('Single Post', 'wp-mailinglist'); ?></label>
                                    <label><input checked="checked" onclick="jQuery('#posts_insert_single_div').hide(); jQuery('#posts_insert_multiple_div').show();" type="radio" name="posts_insert" value="multiple" id="posts_insert_multiple" /> <?php esc_html_e('Multiple Posts', 'wp-mailinglist'); ?></label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Single Post -->
                    <div id="posts_insert_single_div" style="display:none;">
                    	<table cellpadding="4" cellspacing="4" border="0">
                        	<tbody>
                        		<tr>
                                	<td nowrap="nowrap" valign="top"><label for="post_showdate_Y"><?php esc_html_e('Show Date:', 'wp-mailinglist'); ?></label></td>
                                	<td>
                                		<label><input checked="checked" type="radio" name="post_showdate" value="Y" id="post_showdate_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                                		<label><input type="radio" name="post_showdate" value="N" id="post_showdate_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                                		<br/><small><?php esc_html_e('Show the posted date for this post?', 'wp-mailinglist'); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="post_eftype_excerpt"><?php esc_html_e('Full/Excerpt:', 'wp-mailinglist'); ?></label></td>
                                	<td>
                                		<label><input type="radio" name="post_eftype" value="full" id="post_eftype_full" /> <?php esc_html_e('Full', 'wp-mailinglist'); ?></label>
                                		<label><input checked="checked" type="radio" name="post_eftype" value="excerpt" id="post_eftype_excerpt" /> <?php esc_html_e('Excerpt', 'wp-mailinglist'); ?></label>
                                		<br/><small><?php esc_html_e('Insert a full post or the excerpt of a post.', 'wp-mailinglist'); ?></small>
                                	</td>
                                </tr>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_category_menu"><?php esc_html_e('Category:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<?php $select = wp_dropdown_categories(array('show_option_none' => __('- Select Category -', 'wp-mailinglist'), 'echo' => 0, 'name' => "posts_single_category", 'id' => "posts_category_menu", 'hide_empty' => 0, 'show_count' => 1)); ?>
                                        <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="posts_changeCategory();" style="width:210px;">', $select); ?>
                                        <?php echo wp_kses_post($select); ?>
                                        <span id="posts_multiple_message" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
                                    </td>
                                </tr>
                                <?php if ($post_types = $this -> get_custom_post_types()) : ?>
                                	<tr>
                                		<td nowrap="nowrap" valign="top"><label for=""><?php esc_html_e('Custom Post Types:', 'wp-mailinglist'); ?></label></td>
                                		<td>
                                			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
                                				<label><input onclick="jQuery('#posts_category_menu').val('-1'); posts_changeCategory();" type="checkbox" name="post_types[]" value="<?php echo esc_html( $ptypekey); ?>" id="post_types_<?php echo esc_html( $ptypekey); ?>" /> <?php echo esc_html( $ptype -> labels -> name); ?></label><br/>
                                			<?php endforeach; ?>
                                			<small><?php esc_html_e('Choose the custom post types to take posts from.', 'wp-mailinglist'); ?></small>
                                		</td>
                                	</tr>
                                <?php endif; ?>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_post_menu"><?php esc_html_e('Choose Post:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<select name="posts_single_post" id="posts_post_menu" size="6" style="width:210px;">
                                        
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Multiple Posts -->
                    <div id="posts_insert_multiple_div" style="display:block;">
                    	<table cellpadding="4" cellspacing="4" border="0">
                        	<tbody>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_number"><?php esc_html_e('Number of Posts:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<input style="width:45px;" type="text" name="posts_number" value="10" id="posts_number" />
                                    </td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_showdate_Y"><?php esc_html_e('Show Date', 'wp-mailinglist'); ?></label></td>
                                	<td>
                                		<label><input checked="checked" type="radio" name="posts_showdate" value="Y" id="posts_showdate_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                                		<label><input type="radio" name="posts_showdate" value="N" id="posts_showdate_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                                		<br/><small><?php esc_html_e('Show the posted date of each post?', 'wp-mailinglist'); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_eftype_excerpt"><?php esc_html_e('Full/Excerpt:', 'wp-mailinglist'); ?></label></td>
                                	<td>
                                		<label><input type="radio" name="posts_eftype" value="full" id="posts_eftype_full" /> <?php esc_html_e('Full', 'wp-mailinglist'); ?></label>
                                		<label><input checked="checked" type="radio" name="posts_eftype" value="excerpt" id="posts_eftype_excerpt" /> <?php esc_html_e('Excerpt', 'wp-mailinglist'); ?></label>
                                		<br/><small><?php esc_html_e('Insert full posts or the excerpt of posts.', 'wp-mailinglist'); ?></small>
                                	</td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_orderby"><?php esc_html_e('Order By:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<select name="posts_orderby" id="posts_orderby" style="width:210px;">
                                        	<option value="post_date"><?php esc_html_e('Date', 'wp-mailinglist'); ?></option>
                                            <option value="author"><?php esc_html_e('Author', 'wp-mailinglist'); ?></option>
                                            <option value="category"><?php esc_html_e('Category', 'wp-mailinglist'); ?></option>
                                            <option value="content"><?php esc_html_e('Post Content', 'wp-mailinglist'); ?></option>
                                            <option value="ID"><?php esc_html_e('Post ID', 'wp-mailinglist'); ?></option>
                                            <option value="menu_order"><?php esc_html_e('Menu Order', 'wp-mailinglist'); ?></option>
                                            <option value="title"><?php esc_html_e('Post Title', 'wp-mailinglist'); ?></option>
                                            <option value="rand"><?php esc_html_e('Random Order', 'wp-mailinglist'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_order"><?php esc_html_e('Order Direction:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<select style="width:210px;" name="posts_order" id="posts_order">
                                        	<option value="DESC"><?php esc_html_e('Descending (new to old/Z to A/Large to Small)', 'wp-mailinglist'); ?></option>
                                        	<option value="ASC"><?php esc_html_e('Ascending (old to new/A to Z/Small to Large)', 'wp-mailinglist'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            	<tr>
                                	<td nowrap="nowrap" valign="top"><label for="posts_categories"><?php esc_html_e('Posts Category:', 'wp-mailinglist'); ?></label></td>
                                    <td>
                                    	<?php $select = wp_dropdown_categories(array('show_option_all' => __('- All Categories -', 'wp-mailinglist'), 'echo' => 0, 'name' => "posts_categories", 'id' => "posts_categories", 'hide_empty' => 0, 'show_count' => 1)); ?>
                                        <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 style="width:210px;">', $select); ?>
                                        <?php echo $select; ?>
                                    </td>
                                </tr>
                                <?php if ($post_types = $this -> get_custom_post_types()) : ?>
                                	<tr>
                                		<td nowrap="nowrap" valign="top"><label for=""><?php esc_html_e('Custom Post Types:', 'wp-mailinglist'); ?></label></td>
                                		<td>
                                			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
                                				<label><input onclick="jQuery('#posts_categories').val('0');" type="checkbox" name="posts_types[]" value="<?php echo esc_html( $ptypekey); ?>" id="posts_types_<?php echo esc_html( $ptypekey); ?>" /> <?php echo esc_html( $ptype -> labels -> name); ?></label><br/>
                                			<?php endforeach; ?>
                                			<small class="howto"><?php esc_html_e('Choose the custom post types to take posts from.', 'wp-mailinglist'); ?></small>
                                		</td>
                                	</tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Featured Image Thumbnail -->
                <div id="thumbnail_panel" class="panel">
                	<br/>
                	<table cellpadding="4" cellspacing="4" border="0">
                		<tbody>
                			<tr>
                				<td nowrap="nowrap" valign="top">
                					<label for="thumbnail_post_id"><?php esc_html_e('Post ID:', 'wp-mailinglist'); ?></label>
                				</td>
                				<td>
                					<input type="text" name="thumbnail_post_id" value="" id="thumbnail_post_id" />
                					<br/>
                					<small class="howto"><?php esc_html_e('Featured image post ID. Leave empty for current post.', 'wp-mailinglist'); ?></small>
                				</td>
                			</tr>
                			<tr>
                				<td nowrap="nowrap" valign="top">
                					<label for="thumbnail_size"><?php esc_html_e('Size:', 'wp-mailinglist'); ?></label>
                				</td>
                				<td>
                					<select name="thumbnail_size" id="thumbnail_size">
                						<option value="thumbnail"><?php esc_html_e('Thumbnail', 'wp-mailinglist'); ?></option>
                						<option value="medium"><?php esc_html_e('Medium', 'wp-mailinglist'); ?></option>
                						<option value="large"><?php esc_html_e('Large', 'wp-mailinglist'); ?></option>
                						<option value="full"><?php esc_html_e('Full', 'wp-mailinglist'); ?></option>
                					</select>
                					<br/>
                					<small class="howto"><?php esc_html_e('Preferred size of the image.', 'wp-mailinglist'); ?>
                				</td>
                			</tr>
                		</tbody>
                	</table>
                </div>
                
                <!-- Email History -->
                <div id="history_panel" class="panel">
                	<br/>
                	<table cellpadding="4" cellspacing="4" border="0">
                    	<tbody>
                        	<tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_number"><?php esc_html_e('Number of Emails:', 'wp-mailinglist'); ?></label></td>
                                <td>
                                	<input style="width:45px;" type="text" name="history_number" value="" id="history_number" /><br/>
                                    <span class="howto"><?php esc_html_e('Leave empty for unlimited/all emails.', 'wp-mailinglist'); ?></span>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_orderby"><?php esc_html_e('Order By:', 'wp-mailinglist'); ?></label></td>
                                <td>
                                	<select name="history_orderby" id="history_orderby" style="width:210px;">
                                    	<option value="modified"><?php esc_html_e('Date', 'wp-mailinglist'); ?></option>
                                        <option value="subject"><?php esc_html_e('Email Subject', 'wp-mailinglist'); ?></option>
                                        <option value="sent"><?php esc_html_e('Times Sent', 'wp-mailinglist'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_order"><?php esc_html_e('Order Direction:', 'wp-mailinglist'); ?></label></td>
                                <td>
                                	<select style="width:210px;" name="history_order" id="history_order">
                                    	<option value="DESC"><?php esc_html_e('Descending (new to old/Z to A/Large to Small)', 'wp-mailinglist'); ?></option>
                                        <option value="ASC"><?php esc_html_e('Ascending (old to new/A to Z/Small to Large)', 'wp-mailinglist'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                            	<td nowrap="nowrap" valign="top"><label for="history_lists"><?php esc_html_e('Mailing List/s:', 'wp-mailinglist'); ?></label></td>
                                <td>
                                	<select name="history_lists" id="history_lists" multiple="multiple">
                                    	<option value=""><?php esc_html_e('- All Mailing Lists -', 'wp-mailinglist'); ?></option>
                                        <?php if ($mailinglists = $Mailinglist -> select()) : ?>
                                        	<?php foreach ($mailinglists as $list_id => $list_title) : ?>
                                            	<option value="<?php echo esc_html( $list_id); ?>"><?php echo esc_html( $list_title); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <br/><span class="howto"><?php esc_html_e('You can select multiple mailing lists by holding CTRL key.', 'wp-mailinglist'); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Template -->
				<div id="template_panel" class="panel">
					<br/>
					<table cellpadding="4" cellspacing="4" border="0">
						<tbody>
							<tr>
								<td nowrap="nowrap" valign="top"><label for="template"><?php esc_html_e('Snippet:', 'wp-mailinglist'); ?></label></td>
								<td>
									<?php
									
									$templatesquery = "SELECT * FROM " . $wpdb -> prefix . $this -> Template() -> table . " ORDER BY title ASC";
									$templates = $wpdb -> get_results($templatesquery);
									
									?>
									<?php if (!empty($templates)) : ?>
										<select name="template" id="template">
											<option value=""><?php esc_html_e('- Select Template -', 'wp-mailinglist'); ?></option>
											<?php foreach ($templates as $template) : ?>
												<option value="<?php echo esc_html( $template -> id); ?>"><?php echo esc_html( $template -> title); ?></option>
											<?php endforeach; ?>
										</select>
									<?php else : ?>
									
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="mceActionPanel">
				<div style="float:left;">
					<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="closePopup();"/>
				</div>
		
				<div style="float:right;">
					<input type="button" id="insert" name="insert" value="{#insert}" onclick="insertTag();" />
				</div>
			</div>
		</form>
	</body>
</html>