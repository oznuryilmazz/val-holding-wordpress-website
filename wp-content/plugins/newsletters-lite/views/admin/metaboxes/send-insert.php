<?php // phpcs:ignoreFile ?>
<?php

$inserttabs = array('fields' => __('Fields', 'wp-mailinglist'), 'posts' => __('Posts', 'wp-mailinglist'), 'snippets' => __('Snippets', 'wp-mailinglist'));
$inserttabs = apply_filters($this -> pre . '_admin_createnewsletter_inserttabs', $inserttabs);

?>

<div id="inserttabs">
	<ul>
		<?php if (!empty($inserttabs['fields'])) : ?><li><a href="#inserttabs-1"><?php esc_html_e('Fields', 'wp-mailinglist'); ?></a></li><?php endif; ?>
		<?php if (!empty($inserttabs['posts'])) : ?><li><a href="#inserttabs-2"><?php esc_html_e('Posts', 'wp-mailinglist'); ?></a></li><?php endif; ?>
		<?php if (!empty($inserttabs['snippets'])) : ?><li><a href="#inserttabs-3"><?php echo apply_filters('newsletters_admin_tabtitle_createnewsletter_insertsnippets', __('Snippets', 'wp-mailinglist')); ?></a></li><?php endif; ?>
	</ul>
	
	<?php if (!empty($inserttabs['fields'])) : ?>
		<div id="inserttabs-1">
			<h4><?php esc_html_e('Insert Custom Fields', 'wp-mailinglist'); ?> <?php echo ( $Html -> help(__('Below are all custom fields for your subscribers. Click on the custom field that you want to insert into the newsletter and the shortcode will be replaced with the value for each respective subscriber as the newsletter is sent. You can use this to personalize your newsletters.', 'wp-mailinglist'))); ?></h4>
			
			<?php $Db -> model = $Field -> model; ?>
	        <?php $fields = $Db -> find_all(false, array('id', 'title', 'slug'), array('title', "ASC")); ?>
	        <?php if (!empty($fields)) : ?>
	        	<ul class="insertfieldslist">
		            <?php foreach ($fields as $field) : ?>
		            	<li>
		            		<a href="" class="press button button-secondary" onclick='wpml_tinymcetag("[newsletters_field name=<?php echo esc_html( $field -> slug); ?>]"); return false;'><?php echo esc_html($field -> title); ?></a>
		            	</li>
		            <?php endforeach; ?>
	        	</ul>
	        <?php endif; ?>
	        <?php if (!empty($Subscriber -> table_fields)) : ?>
	        	<p>
		        	<a href="" onclick="jQuery('#morefieldslist').toggle(); return false;" class="button button-primary"><i class="fa fa-caret-down"></i> <?php esc_html_e('More Fields', 'wp-mailinglist'); ?></a>
	        	</p>
	        
				<div id="morefieldslist" style="display:none;">
		        	<ul class="insertfieldslist">
			        	<?php foreach ($Subscriber -> table_fields as $field => $attributes) : ?>
			        		<?php if ($field != "email" && $field != "key") : ?>
				        		<li>
				        			<a href="" class="press button button-secondary" onclick='wpml_tinymcetag("[newsletters_field name=<?php echo esc_html( $field); ?>]"); return false;'><?php echo esc_html( $Field -> title_by_slug($field)); ?></a>
				        		</li>
				        	<?php endif; ?>
			        	<?php endforeach; ?>
		        	</ul>
				</div>
	        <?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if (!empty($inserttabs['posts'])) : ?>
		<div id="inserttabs-2">
			<h4><?php esc_html_e('Insert Posts', 'wp-mailinglist'); ?> <?php echo ( $Html -> help(__('Insert single posts, multiple posts and post featured images into your newsletter as needed. Follow the selections below to make the posts available and then click to insert.', 'wp-mailinglist'))); ?></h4>
		
			<p>
				<label><input type="radio" name="ptype" checked="checked" value="single" id="ptype_single" /> <?php esc_html_e('Single', 'wp-mailinglist'); ?></label>
				<label><input type="radio" name="ptype" value="page" id="ptype_page" /> <?php esc_html_e('Page', 'wp-mailinglist'); ?></label>
				<br/>
				<label><input type="radio" name="ptype" value="multiple" id="ptype_multiple" /> <?php esc_html_e('Multiple', 'wp-mailinglist'); ?></label>
				<label><input type="radio" name="ptype" value="thumbnail" id="ptype_thumbnail" /> <?php esc_html_e('Thumbnail', 'wp-mailinglist'); ?></label>
			</p>
			
			<div id="ptypeglobal" style="display:block;">
				<?php if ($this -> language_do()) : ?>
					<label for=""><?php esc_html_e('Language:', 'wp-mailinglist'); ?></label>
		        	<?php if ($languages = $this -> language_getlanguages()) : ?>
		                <?php foreach ($languages as $language) : ?>
		                    <label><input <?php echo ($language == $this -> language_default()) ? 'checked="checked"' : ''; ?> onclick="get_posts();" type="radio" name="postslanguage" value="<?php echo esc_html( $language); ?>" id="postslanguage<?php echo esc_html( $language); ?>" /> <?php echo wp_kses_post( $this -> language_flag($language)); ?></label>
		                <?php endforeach; ?>
		            <?php else : ?>
		            
		            <?php endif; ?>
		            <?php echo ( $Html -> help(__('Since you are using multilingual, choose the language of the post(s) that you want to use in the newsletter.', 'wp-mailinglist'))); ?>
		        <?php endif; ?>
		        
		        <p>
					<label for="post_showdate_Y"><?php esc_html_e('Show Date:', 'wp-mailinglist'); ?></label>
					<label><input type="radio" name="post_showdate" value="Y" id="post_showdate_Y" checked="checked" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
					<label><input type="radio" name="post_showdate" value="N" id="post_showdate_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('Choose whether or not to show the published date of the post.', 'wp-mailinglist'))); ?>
				</p>
				
				<p>
					<label for="post_eftype_excerpt"><?php esc_html_e('Display:', 'wp-mailinglist'); ?></label>
					<label><input type="radio" name="post_eftype" value="full" id="post_eftype_full" /> <?php esc_html_e('Full', 'wp-mailinglist'); ?></label>
					<label><input type="radio" name="post_eftype" value="excerpt" id="post_eftype_excerpt" checked="checked" /> <?php esc_html_e('Excerpt/Short', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('Do you want to display the full post or an excerpt of the post? Note that the excerpt is a short version of the first few characters of the post and all HTML will be stripped from it.', 'wp-mailinglist'))); ?>
				</p>
			</div>
			
			<div id="ptypediv_single" style="display:block;">
				<?php if ($posttypes = $this -> get_custom_post_types(true)) : ?>
					<p>
						<label for="posttype"><?php esc_html_e('Post Type:', 'wp-mailinglist'); ?></label><br/>
						<select style="max-width:200px;" onchange="change_posttype(this.value)" name="posttype" id="posttype">
							<option value="post"><?php esc_html_e('Post', 'wp-mailinglist'); ?></option>
							<?php foreach ($posttypes as $posttypekey => $posttype) : ?>
								<option value="<?php echo esc_html( $posttypekey); ?>"><?php echo esc_html( $posttype -> labels -> name); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo ( $Html -> help(__('Since you have custom post types available, this menu is showing. Choose the post type to fetch posts from.', 'wp-mailinglist'))); ?>
					</p>
				<?php else : ?>
					<input type="hidden" id="posttype" name="posttype" value="post" />
				<?php endif; ?>
				
				<div id="posttype_post" style="display:block">
					<p>
						<label for="posts_category_menu"><?php esc_html_e('Post Category:', 'wp-mailinglist'); ?></label><br/>
						<?php $select = wp_dropdown_categories(array('show_option_none' => __('- Select Category -', 'wp-mailinglist'), 'echo' => 0, 'name' => "posts_single_category", 'id' => "posts_category_menu", 'hide_empty' => 0, 'show_count' => 1)); ?>
		                <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="get_posts();" style="max-width:200px;">', $select); ?>
		                <?php echo $select; ?>
		                <?php echo ( $Html -> help(__('Select a post category to narrow down posts by category for easier selection.', 'wp-mailinglist'))); ?>
					</p>
				</div>
				
				<div>
					<p>
						<label for="posts_search_number"><?php esc_html_e('Number of Posts:', 'wp-mailinglist'); ?></label>
						<input onkeyup="get_posts();" style="width:65px;" class="widefat" type="text" name="posts_search_number" value="" id="posts_search_number" placeholder="eg. 200" />
						<?php echo ( $Html -> help(__('Number of posts to show from the above category', 'wp-mailinglist'))); ?></p
					</p>
					<p>
						<label for=""><?php esc_html_e('Order:', 'wp-mailinglist'); ?></label><br/>
						<select name="posts_search_orderby" onchange="get_posts();" id="posts_search_orderby" style="max-width:100px;">
		                	<option value="post_date"><?php esc_html_e('Date', 'wp-mailinglist'); ?></option>
		                    <option value="author"><?php esc_html_e('Author', 'wp-mailinglist'); ?></option>
		                    <option value="category"><?php esc_html_e('Category', 'wp-mailinglist'); ?></option>
		                    <option value="content"><?php esc_html_e('Post Content', 'wp-mailinglist'); ?></option>
		                    <option value="ID"><?php esc_html_e('Post ID', 'wp-mailinglist'); ?></option>
		                    <option value="menu_order"><?php esc_html_e('Menu Order', 'wp-mailinglist'); ?></option>
		                    <option value="title"><?php esc_html_e('Post Title', 'wp-mailinglist'); ?></option>
		                    <option value="rand"><?php esc_html_e('Random Order', 'wp-mailinglist'); ?></option>
		                </select>
		                <select name="posts_search_order" onchange="get_posts();" id="posts_search_order" style="max-width:100px;">
		                	<option value="ASC"><?php esc_html_e('Ascending', 'wp-mailinglist'); ?></option>
		                	<option value="DESC"><?php esc_html_e('Descending', 'wp-mailinglist'); ?></option>
		                </select>
					</p>
				</div>
				
				<div>
					<p>
						<button class="button button-secondary" type="button" value="1" onclick="get_posts();" id="postsloadingbutton">
							<span id="postsloading" style=""><i class="fa fa-refresh fa-fw"></i></span>
							<?php esc_html_e('Search Posts', 'wp-mailinglist'); ?>
						</button>
					</p>
				</div>
				
				<div id="postsdiv" style="display:none;">
					<p>
						<label for=""><?php esc_html_e('Choose Post:', 'wp-mailinglist'); ?></label>
						<?php echo ( $Html -> help(__('Click on a post below to insert it into your newsletter or use the checkboxes (you can select a range by holding Shift and clicking) to tick multiple posts and then click the "Insert Selected" button to insert all the selected posts.', 'wp-mailinglist'))); ?>
						<div id="ajaxposts">
							<span class="howto"><?php esc_html_e('Choose all settings above.', 'wp-mailinglist'); ?></span>
						</div>
					</p>
				</div>
			</div>
			
			<div id="ptypediv_page" style="display:none;">
				<p>
					<label for="page_id"><?php esc_html_e('Page:', 'wp-mailinglist'); ?></label>
					<?php wp_dropdown_pages(array('depth' => 0, 'child_of' => 0, 'echo' => 1, 'name' => "page_id", 'show_option_none' => false)); ?>
				</p>
				
				<button type="button" class="button button-secondary" onclick="insert_post(jQuery('#page_id').val(), false);" name="insertpage" value="1">
					<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Insert Page', 'wp-mailinglist'); ?>
				</button>
			</div>
			
			<div id="ptypediv_multiple" style="display:none;">
				<p>
					<label for="posts_number"><?php esc_html_e('Number:', 'wp-mailinglist'); ?></label>
					<input type="text" name="posts_number" value="10" id="posts_number" class="widefat" style="width:45px;" />
					<?php echo ( $Html -> help(__('Number', 'wp-mailinglist'))); ?>
				</p>
				
				<p>
					<label for=""><?php esc_html_e('Order:', 'wp-mailinglist'); ?></label><br/>
					<select name="posts_orderby" id="posts_orderby" style="max-width:100px;">
	                	<option value="post_date"><?php esc_html_e('Date', 'wp-mailinglist'); ?></option>
	                    <option value="author"><?php esc_html_e('Author', 'wp-mailinglist'); ?></option>
	                    <option value="category"><?php esc_html_e('Category', 'wp-mailinglist'); ?></option>
	                    <option value="content"><?php esc_html_e('Post Content', 'wp-mailinglist'); ?></option>
	                    <option value="ID"><?php esc_html_e('Post ID', 'wp-mailinglist'); ?></option>
	                    <option value="menu_order"><?php esc_html_e('Menu Order', 'wp-mailinglist'); ?></option>
	                    <option value="title"><?php esc_html_e('Post Title', 'wp-mailinglist'); ?></option>
	                    <option value="rand"><?php esc_html_e('Random Order', 'wp-mailinglist'); ?></option>
	                </select>
	                <select name="posts_order" id="posts_order" style="max-width:100px;">
	                	<option value="ASC"><?php esc_html_e('Ascending', 'wp-mailinglist'); ?></option>
	                	<option value="DESC"><?php esc_html_e('Descending', 'wp-mailinglist'); ?></option>
	                </select>
	                <?php echo ( $Html -> help(__('Order', 'wp-mailinglist'))); ?>
				</p>
				
				<p>
					<label for=""><?php esc_html_e('Posts Category:', 'wp-mailinglist'); ?></label><br/>
					<?php $select = wp_dropdown_categories(array('show_option_all' => __('- All Categories -', 'wp-mailinglist'), 'echo' => 0, 'name' => "posts_categories", 'id' => "posts_categories", 'hide_empty' => 0, 'show_count' => 1)); ?>
	                <?php $select = preg_replace("#<select([^>]*)>#", '<select$1 onchange="change_category();" style="max-width:200px;">', $select); ?>
	                <?php echo $select; ?>
				</p>
				
				<p>
					<label for=""><?php esc_html_e('Post Types:', 'wp-mailinglist'); ?></label>
					<?php if ($post_types = $this -> get_custom_post_types()) : ?>
		            	<ul>
		        			<?php foreach ($post_types as $ptypekey => $ptype) : ?>
		        				<label><input onclick="jQuery('#posts_categories').val('0');" type="checkbox" name="posts_types[]" value="<?php echo $ptypekey; ?>" id="posts_types_<?php echo $ptypekey; ?>" /> <?php echo esc_html( $ptype -> labels -> name); ?></label><br/>
		        			<?php endforeach; ?>
		            	</ul>
		            <?php endif; ?>
				</p>
				
				<button onclick="insert_post(false, false);" type="button" name="insertmultiple" class="button button-secondary" value="1">
					<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Insert Posts', 'wp-mailinglist'); ?>
				</button>
			</div>
			
			<div id="ptypediv_thumbnail" style="display:none;">
				<p>
					<label for="thumbnail_post_id"><?php esc_html_e('Post ID:', 'wp-mailinglist'); ?></label>
					<input type="text" class="widefat" style="width:65px;" name="thumbnail_post_id" id="thumbnail_post_id" value="" />
					<?php echo ( $Html -> help(__('Specify the ID of the post', 'wp-mailinglist'))); ?>
				</p>
				
				<p>
					<label for="thumbnail_size"><?php esc_html_e('Thumbnail Size:', 'wp-mailinglist'); ?></label><br/>
					<select name="thumbnail_size" id="thumbnail_size">
						<option value="thumbnail"><?php esc_html_e('Thumbnail', 'wp-mailinglist'); ?></option>
						<option value="medium"><?php esc_html_e('Medium', 'wp-mailinglist'); ?></option>
						<option value="large"><?php esc_html_e('Large', 'wp-mailinglist'); ?></option>
						<option value="full"><?php esc_html_e('Full', 'wp-mailinglist'); ?></option>
					</select>
				</p>
				
				<button type="button" class="button button-secondary" onclick="insert_post(false, false);" name="insertthumbnail" value="1">
					<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Insert Thumbnail', 'wp-mailinglist'); ?>
				</button>
			</div>
			
			<script type="text/javascript">	
			jQuery('input[name="posts_types[]"]').shiftClick();
			
			jQuery('input[name="ptype"]').click(function() {
				var ptype = jQuery(this).val();
				jQuery('div[id^="ptypediv"]').hide();
				jQuery('#ptypediv_' + ptype).show();
				if (ptype == "thumbnail") { jQuery('#ptypeglobal').hide(); }
				else { jQuery('#ptypeglobal').show(); }
			});
								
			function insert_single_multiple() {
				var multishortcode = "";
				
				jQuery('input[name="insertposts[]"]:checked').each(function() {
					var post_id = jQuery(this).val();
					multishortcode += insert_post(post_id, true) + "<br/>";
				});
				
				wpml_tinymcetag(multishortcode);
			}
			
			function insert_post(post_id, returnshortcode) {
				var ptype = jQuery('input[name="ptype"]:checked').val();
				if (jQuery('input[name="postslanguage"]').length > 0) { var postslanguage = jQuery('input[name="postslanguage"]:checked').val(); } 
				else { var postslanguage = false; }
				
				if (ptype == "single") {
					var shortcode = "";
					shortcode += '[newsletters_post post_id="' + post_id + '"';					
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
					shortcode += ' showdate="' + post_showdate + '"';
					shortcode += ' eftype="' + post_eftype + '"';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ']';	
				} else if (ptype == "page") {
					var page_id = post_id;
					var shortcode = "";
					shortcode += '[newsletters_post post_id="' + page_id + '"';					
					var post_showdate = jQuery('input[name="post_showdate"]:checked').val();
					var post_eftype = jQuery('input[name="post_eftype"]:checked').val();
					shortcode += ' showdate="' + post_showdate + '"';
					shortcode += ' eftype="' + post_eftype + '"';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ']';
				} else if (ptype == "multiple") {
					var shortcode = "";
					shortcode += '[newsletters_posts';
					if (postslanguage) { shortcode += ' language="' + postslanguage + '"'; }
					shortcode += ' numberposts="' + jQuery('#posts_number').val() + '"';
					shortcode += ' showdate="' + jQuery('input[name="post_showdate"]:checked').val() + '"';
					shortcode += ' eftype="' + jQuery('input[name="post_eftype"]:checked').val() + '"';
					shortcode += ' orderby="' + jQuery('#posts_orderby').val() + '"';
					shortcode += ' order="' + jQuery('#posts_order').val() + '"';
					shortcode += ' category="' + jQuery('#posts_categories').val() + '"';
					if (jQuery('input[name="posts_types[]"]').length > 0) {
						var posts_types = new Array();
						jQuery('input[name="posts_types[]"]:checked').each(function() {
							posts_types.push(jQuery(this).val());
						});
						
						if (posts_types != "") { 
							shortcode += ' post_type="' + posts_types + '"';
						}
					}
					shortcode += ']';
				} else if (ptype == "thumbnail") {
					var shortcode = "";
					var thumbnail_post_id = jQuery('#thumbnail_post_id').val();
					var thumbnail_size = jQuery('#thumbnail_size').val();
					shortcode += '[newsletters_post_thumbnail';
					
					if (thumbnail_post_id != "") {
						shortcode += ' post_id="' + thumbnail_post_id + '"';
					}
					
					shortcode += ' size="' + thumbnail_size + '"';
					shortcode += ']';
				}
				
				if (returnshortcode == true) {
					return shortcode;
				} else {
					wpml_tinymcetag(shortcode);
				}
			}
			
			function change_category() {
				jQuery('input[name="posts_types[]"]:checked').attr('checked', false);
			}
			
			function change_posttype(posttype) {
				jQuery('div[id^="posttype"]').hide();
				jQuery('div#posttype_' + posttype).show();
				
				if (posttype != "post") { 
					jQuery('#posts_category_menu').val('-1');
					get_posts(); 
				}
			}
			
			function get_posts() {
				jQuery('#postsdiv').hide();
				jQuery('#postsloading i').addClass('fa-spin');
				jQuery('#postsloadingbutton').prop('disabled', true);
					
				var varguments = {
					language: jQuery('input[name="postslanguage"]:checked').val(),
					posttype: jQuery('#posttype').val(),
					category: jQuery('#posts_category_menu').val(),
					number: jQuery('#posts_search_number').val(),	
					orderby: jQuery('#posts_search_orderby').val(),
					order: jQuery('#posts_search_order').val(),
				};
			
				jQuery.ajax(newsletters_ajaxurl + 'action=newsletters_getposts&security=<?php echo esc_html( wp_create_nonce('getposts')); ?>', {
					type: 'POST',
					data: varguments,
					success: function(response) {
						
						if (response != "") { 
							jQuery('#postsdiv').show();
							jQuery('#ajaxposts').html(response); 
						}
						
						jQuery('input[name="insertposts[]"]').shiftClick();
					},
					complete: function() {
						jQuery('#postsloading i').removeClass('fa-spin');
						jQuery('#postsloadingbutton').prop('disabled', false);
					}
				});
			}
			</script>
		</div>
	<?php endif; ?>
	<?php if (!empty($inserttabs['snippets'])) : ?>
		<div id="inserttabs-3">
			<h4><?php echo apply_filters('newsletters_admin_tabheading_createnewsletter_insertsnippets', __('Insert Snippets', 'wp-mailinglist')); ?> <?php echo ( $Html -> help(apply_filters('newsletters_admin_tooltip_createnewsletter_insertsnippets', __('Below are all your email snippets. Click on the snippet to insert it into the newsletter and the shortcode will be replaced with the content of the email snippet. Alternatively click "Load into Editor" to load the email snippet into the editor in full.', 'wp-mailinglist')))); ?></h4>
			<?php if ($snippets = $this -> Template() -> find_all(false, array('id', 'title'), array('title', "ASC"))) : ?>
				<?php $snippets = apply_filters('newsletters_admin_createnewsletter_snippets', $snippets); ?>
				<ul class="insertfieldslist">
					<?php foreach ($snippets as $snippet) : ?>
						<li>
							<a href="<?php echo apply_filters($this -> pre . '_admin_createnewsletter_snippetbuttonhref', "", $snippet); ?>" class="press button button-secondary" onclick='<?php echo apply_filters($this -> pre . '_admin_createnewsletter_snippetbuttononclick', 'wpml_tinymcetag("[newsletters_snippet id=\"' . $snippet -> id . '\"]"); return false;', $snippet); ?>'><?php echo esc_html($snippet -> title); ?></a>
							<?php if (apply_filters($this -> pre . '_admin_createnewsletter_loadintoeditorlinks', true)) : ?><small><a href="?page=<?php echo esc_html( $this -> sections -> send); ?>&method=snippet&id=<?php echo esc_html( $snippet -> id); ?>" class=""><?php esc_html_e('Load into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="newsletters_error"><?php esc_html_e('No email snippets available.', 'wp-mailinglist'); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {		
	if (jQuery.isFunction(jQuery.fn.tabs)) {
		jQuery('#inserttabs').tabs();
	}
});
</script>