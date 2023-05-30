<?php // phpcs:ignoreFile ?>
<div class="total">
	<p><?php esc_html_e('Mailing lists total:', 'wp-mailinglist'); ?></p>
	<p class="totalnumber"><?php echo esc_html($total_public); ?> / <?php echo esc_html($total_private); ?></p>
	<p class="totalsmall"><?php esc_html_e('public', 'wp-mailinglist'); ?> / <?php esc_html_e('private', 'wp-mailinglist'); ?></p>
	<p><a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>" class="button button-primary button-large"><?php esc_html_e('Manage Lists', 'wp-mailinglist'); ?></a></p>
</div>