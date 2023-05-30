<?php // phpcs:ignoreFile ?>
<div id="submitpost" class="submitbox">
	<div id="minor-publishing">
		
	</div>
	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php if ($Html -> field_value('Template[id]') != "") : ?>
				<a class="submitdelete deletion" href="?page=<?php echo esc_html( $this -> sections -> templates); ?>&amp;method=delete&amp;id=<?php echo esc_html($Html -> field_value('Template[id]')); ?>" title="<?php esc_html_e('Delete this template', 'wp-mailinglist'); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this snippet?', 'wp-mailinglist'); ?>')) { return false; }"><?php esc_html_e('Delete Snippet', 'wp-mailinglist'); ?></a>
			<?php endif; ?>
		</div>
		<div id="publishing-action">
			<input id="publish" type="submit" class="button button-primary button-large" name="save" value="<?php esc_html_e('Save Snippet', 'wp-mailinglist'); ?>" />
		</div>
		<br class="clear" />
		<div style="text-align:right; margin:15px 0 5px 0;">
			<label><input style="min-width:0;" <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing?', 'wp-mailinglist'); ?></label>
		</div>
	</div>
</div>