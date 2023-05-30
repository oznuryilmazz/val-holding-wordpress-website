<?php // phpcs:ignoreFile ?>
<?php $this -> render('metaboxes' . DS . 'admin-mode-switcher', false, true, 'admin'); ?>

<label for="tableofcontents"><?php esc_html_e('Go to section', 'wp-mailinglist'); ?></label>
<select name="tableofcontents" id="tableofcontents" onchange="if (this.value != '') { jQuery('#' + this.value).removeClass('closed'); wpml_scroll('#' + this.value); window.location.hash = '#' + this.value; }">
	<option value=""><?php esc_html_e('Choose section...', 'wp-mailinglist'); ?></option>
	<option value="generaldiv"><?php esc_html_e('General Mail Settings', 'wp-mailinglist'); ?></option>
	<option value="sendingdiv"><?php esc_html_e('Sending Settings', 'wp-mailinglist'); ?></option>
	<option value="optindiv"><?php esc_html_e('Default Subscription Form Settings', 'wp-mailinglist'); ?></option>
	<option value="subscriptionsdiv"><?php esc_html_e('Paid Subscriptions', 'wp-mailinglist'); ?></option>
	<option value="ppdiv"><?php esc_html_e('PayPal Configuration', 'wp-mailinglist'); ?></option>
	<option value="tcdiv"><?php esc_html_e('2CheckOut Configuration', 'wp-mailinglist'); ?></option>
	<option value="publishingdiv"><?php esc_html_e('Posts Configuration', 'wp-mailinglist'); ?></option>
	<option value="schedulingdiv"><?php esc_html_e('Email Scheduling', 'wp-mailinglist'); ?></option>
	<option value="bouncediv"><?php esc_html_e('Bounce Configuration', 'wp-mailinglist'); ?></option>
	<option value="emailsdiv"><?php esc_html_e('History &amp; Emails Configuration', 'wp-mailinglist'); ?></option>
	<option value="latestposts"><?php esc_html_e('Latest Posts Subscription', 'wp-mailinglist'); ?></option>
	<option value="customcss"><?php esc_html_e('Theme, Scripts &amp; Custom CSS', 'wp-mailinglist'); ?></option>
</select>

<p class="savebutton">
	<button value="1" type="submit" class="button button-primary button-large" name="save">
		<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Save Settings', 'wp-mailinglist'); ?>
	</button>
</p>