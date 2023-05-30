<?php // phpcs:ignoreFile ?>
<?php $this -> render('metaboxes' . DS . 'admin-mode-switcher', false, true, 'admin'); ?>

<label for="tableofcontents"><?php esc_html_e('Go to section', 'wp-mailinglist'); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); window.location.hash = '#' + this.value; }">
	<option value=""><?php esc_html_e('Choose section...', 'wp-mailinglist'); ?></option>
	<option value="managementdiv"><?php esc_html_e('Subscriber Management Section', 'wp-mailinglist'); ?></option>
	<option value="subscribersdiv"><?php esc_html_e('Subscription Behaviour', 'wp-mailinglist'); ?></option>
	<option value="unsubscribediv"><?php esc_html_e('Unsubscribe Behaviour', 'wp-mailinglist'); ?></option>
</select>

<p class="savebutton">
	<button value="1" type="submit" class="button button-primary button-large" name="save">
		<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Save Settings', 'wp-mailinglist'); ?>
	</button>
</p>