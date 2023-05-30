<?php // phpcs:ignoreFile ?>
<div class="newsletters newsletters-management-logout">
	<div class="alert alert-success">
		<i class="fa fa-check"></i>
		<?php esc_html_e('You have now logged out of your subscriber profile management.', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('If you wish to go back, please click the link below.', 'wp-mailinglist'); ?>
	</div>

	<p><a class="newsletters_button btn btn-primary" href="<?php echo esc_url_raw($this -> get_managementpost(true)); ?>"><?php esc_html_e('Manage Subscriptions', 'wp-mailinglist'); ?></a></p>
</div>