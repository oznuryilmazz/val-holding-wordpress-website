<?php // phpcs:ignoreFile ?>
<div class="total">
	<p><?php esc_html_e('Subscribers total to date:', 'wp-mailinglist'); ?></p>
	<p class="totalnumber"><?php echo esc_html($total); ?></p>
	<p>
		<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>" class="button button-primary button-large"><?php esc_html_e('Manage Subscribers', 'wp-mailinglist'); ?></a>
	</p>
	<p>
		<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes')) ?>"><i class="fa fa-sign-out"></i> <?php esc_html_e('Unsubscribes', 'wp-mailinglist'); ?></a>
		<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces')) ?>"><i class="fa fa-ban"></i> <?php esc_html_e('Bounces', 'wp-mailinglist'); ?></a>
	</p>
	<p>
		<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> importexport)) ?>"><i class="fa fa-hdd-o"></i> <?php esc_html_e('Import/Export', 'wp-mailinglist'); ?></a>
	</p>
</div>