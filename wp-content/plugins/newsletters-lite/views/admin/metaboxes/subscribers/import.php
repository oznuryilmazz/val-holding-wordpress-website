<?php // phpcs:ignoreFile ?>
<!-- Import Settings -->

<?php
	
$import_notification = $this -> get_option('import_notification');	
$import_createfieldoptions = $this -> get_option('import_createfieldoptions');
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="import_notification"><?php esc_html_e('Email Notification After Import', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php checked($import_notification, 1, true); ?> type="checkbox" name="import_notification" value="1" id="import_notification" /> <?php esc_html_e('Yes, send an email notification after an import completed.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('This will send an email notification to the administrator after the import completed successfully.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="import_createfieldoptions"><?php esc_html_e('Create Field Options', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php checked($import_createfieldoptions, 1, true); ?> type="checkbox" name="import_createfieldoptions" value="1" id="import_createfieldoptions" /> <?php esc_html_e('Yes, create field options on import when they do not exist.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('When you import a CSV with select, checkbox, etc. values that exist in the CSV file but not on your custom field, the options can be automatically created.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>