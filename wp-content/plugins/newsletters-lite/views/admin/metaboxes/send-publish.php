<?php // phpcs:ignoreFile ?>
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo esc_html($this -> pre); ?>publishpostN"><?php esc_html_e('Publish as Post', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($_POST['post_id'])) ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($_POST['post_id']) || (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" onclick="jQuery('#publishpostdiv').show();" name="publishpost" value="Y" id="<?php echo esc_html($this -> pre); ?>publishpostY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
				<label><input <?php echo (!empty($_POST['post_id'])) ? 'disabled="disabled"' : ''; ?> <?php echo (empty($_POST['post_id']) && (empty($_POST['publishpost']) || $_POST['publishpost'] == "N")) ? 'checked="checked"' : ''; ?> type="radio" onclick="jQuery('#publishpostdiv').hide();" name="publishpost" value="N" id="<?php echo esc_html($this -> pre); ?>publishpostN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
			</td>
		</tr>
		<?php if (!empty($_POST['post_id'])) : ?>
			<?php $post = get_post(sanitize_text_field(wp_unslash($_POST['post_id']))); ?>
			<tr>
				<th><label for=""><?php esc_html_e('Existing Post', 'wp-mailinglist'); ?></label></th>
				<td>
					<a href="<?php echo get_permalink($post -> ID); ?>" target="_blank"><?php echo esc_html($post -> post_title); ?></a>
					<a class="" href="<?php echo get_delete_post_link($post -> ID); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this post?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
					<a class="" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=unlinkpost&id=' . sanitize_text_field(wp_unslash($_POST['ishistory'])))) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to unlink this post from this newsletter?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-unlink"></i></a>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
							
<div id="publishpostdiv" style="display:<?php echo (!empty($_POST['post_id']) || (!empty($_POST['publishpost']) && $_POST['publishpost'] == "Y")) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><?php esc_html_e('Post Categories', 'wp-mailinglist'); ?></th>
				<td>
					<div style="overflow:auto; max-height:100px;">
						<?php $categories = get_categories(array('hide_empty' => false)); ?>
						<?php if (!empty($categories)) : ?>
							<?php foreach ($categories as $category) : ?>
								<?php if (!empty($_POST['cat']) && is_array($_POST['cat'])) {
									$isthiscat = (in_array($category -> cat_ID, $_POST['cat'])) ? true : false;
								} else {
									$isthiscat = false;
								} ?>
								<label><input <?php echo $check = ($isthiscat) ? 'checked="checked"' : ''; ?> type="checkbox" name="cat[]" value="<?php echo esc_attr($category -> cat_ID); ?>" /> <?php echo esc_html( $category -> cat_name); ?></label><br/>
							<?php endforeach; ?>
						<?php else : ?>
							<span style="newsletters_error"><?php esc_html_e('No categories were found', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			<tr>
				<th><label for="post_author"><?php esc_html_e('Post Author', 'wp-mailinglist'); ?></label></th>
				<td>
                <?php wp_dropdown_users(array('capability' => "authors", 'name' => 'post_author', 'selected' => get_current_user_id())); ?>
				</td>
			</tr>
			<tr>
				<th><label for="post_status"><?php esc_html_e('Post Status', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php $statuses = $this -> get_option('poststatuses'); ?>
					<select class="widefat" style="width:auto;" id="post_status" name="post_status">
						<?php foreach ($statuses as $key => $val) : ?>
                        <?php $sel = isset($_POST['post_status']) ? ($_POST['post_status'] == $key) ? 'selected="selected"' : '' : ''; ?>
                        <option <?php echo $sel; ?> value="<?php echo esc_html($key); ?>"><?php echo $val; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="newsletters_post_type"><?php esc_html_e('Post Type', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($post_types = $this -> get_custom_post_types(false)) : ?>
						<select name="newsletters_post_type" id="newsletters_post_type">
							<?php foreach ($post_types as $ptypekey => $ptype) : ?>
								<option <?php echo (!empty($_POST['newsletters_post_type']) && $_POST['newsletters_post_type'] == $ptypekey) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($ptypekey); ?>"><?php echo esc_html( $ptype -> labels -> name); ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Post Slug', 'wp-mailinglist');?></th>
				<td>
                <input class="widefat" type="text" size="25" name="post_slug" value="<?php echo isset($_POST['post_slug']) ?  esc_attr(wp_unslash($_POST['post_slug'])) : ''; ?>" />
                <span class="howto"><small><?php _e('(optional)', 'wp-mailinglist'); ?></small> <?php _e('Post slug to use for this post', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>