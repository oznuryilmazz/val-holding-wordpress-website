<?php // phpcs:ignoreFile ?>
<div class="total">
	<p><?php esc_html_e('Unsubscribes to date:', 'wp-mailinglist'); ?></p>
	<p class="totalnumber"><?php echo esc_html($total); ?></p>
	<p><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&method=unsubscribes" class="button button-primary button-large"><?php esc_html_e('Manage Unsubscribes', 'wp-mailinglist'); ?></a></p>
</div>