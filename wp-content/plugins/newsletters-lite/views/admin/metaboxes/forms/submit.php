<?php // phpcs:ignoreFile ?>
<!-- Form Submit -->

<div class="submitbox" id="submitpost">
	<div id="minor-publishing">
		<div id="minor-publishing-actions">
			
		</div>
		<div id="misc-publishing-actions">
			
        </div>
		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">
		<?php if (!empty($_GET['id'])) : ?>
			<div id="delete-action">
				<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=delete&id=' . sanitize_text_field(wp_unslash($_GET['id'])))) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this?', 'wp-mailinglist'); ?>')) { return false; }" class="submitdelete deletion"><?php esc_html_e('Delete Form', 'wp-mailinglist'); ?></a>
			</div>
		<?php endif; ?>
		<div id="publishing-action">
			<input class="button button-primary button-large" type="submit" name="save" id="saveform" value="<?php esc_html_e('Save Form', 'wp-mailinglist'); ?>" />
		</div>
		<br class="clear" />
		<div style="text-align:right; margin:15px 0 5px 0;">
			<label><input style="min-width:0;" checked="checked" type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing?', 'wp-mailinglist'); ?></label>
		</div>
	</div>
</div>