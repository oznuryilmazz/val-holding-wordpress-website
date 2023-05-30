<?php // phpcs:ignoreFile ?>
<div class="total">
	<p><?php esc_html_e('Emails sent to date:', 'wp-mailinglist'); ?></p>
	<p class="totalnumber"><?php echo esc_html($total); ?></p>
	<p><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history)) ?>" class="button button-primary button-large"><?php esc_html_e('Manage History Emails', 'wp-mailinglist'); ?></a></p>
</div>