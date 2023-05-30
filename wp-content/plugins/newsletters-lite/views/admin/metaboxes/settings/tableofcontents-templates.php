<?php // phpcs:ignoreFile ?>
<?php $this -> render('metaboxes' . DS . 'admin-mode-switcher', false, true, 'admin'); ?>

<label for="tableofcontents"><?php esc_html_e('Go to section', 'wp-mailinglist'); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); window.location.hash = '#' + this.value; }">
	<option value=""><?php esc_html_e('Choose section...', 'wp-mailinglist'); ?></option>
	<option value="postsdiv"><?php esc_html_e('Posts', 'wp-mailinglist'); ?></option>
	<option value="latestpostsdiv"><?php esc_html_e('Latest Posts', 'wp-mailinglist'); ?></option>
	<option value="sendasdiv"><?php esc_html_e('Send as Newsletter', 'wp-mailinglist'); ?></option>
	<option value="confirmdiv"><?php esc_html_e('Confirmation Email', 'wp-mailinglist'); ?></option>
	<option value="bouncediv"><?php esc_html_e('Bounce Email', 'wp-mailinglist'); ?></option>
	<option value="unsubscribediv"><?php esc_html_e('Unsubscribe Admin Email', 'wp-mailinglist'); ?></option>
	<option value="unsubscribeuserdiv"><?php esc_html_e('Unsubscribe User Email', 'wp-mailinglist'); ?></option>
	<option value="expirediv"><?php esc_html_e('Expiration Email', 'wp-mailinglist'); ?></option>
	<option value="orderdiv"><?php esc_html_e('Paid Subscription Email', 'wp-mailinglist'); ?></option>
	<option value="schedulediv"><?php esc_html_e('Cron Schedule Email', 'wp-mailinglist'); ?></option>
	<option value="subscribediv"><?php esc_html_e('New Subscription Email', 'wp-mailinglist'); ?></option>
	<option value="authenticatediv"><?php esc_html_e('Authentication Email', 'wp-mailinglist'); ?></option>
</select>

<p class="savebutton">
	<button value="1" type="submit" class="button button-primary button-large" name="save">
		<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Save Settings', 'wp-mailinglist'); ?>
	</button>
</p>