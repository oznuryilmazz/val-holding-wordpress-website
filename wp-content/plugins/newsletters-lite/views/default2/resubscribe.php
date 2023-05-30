<?php // phpcs:ignoreFile ?>
<div class="newsletters <?php echo esc_html($this -> pre); ?>">
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<div class="alert alert-success">
		<i class="fa fa-check"></i>
		<?php esc_html_e('You have resubscribed!', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('We are happy that you still want to receive our emails.', 'wp-mailinglist'); ?>
	</div>
	
	<p><a class="newsletters_button btn btn-primary" href="<?php echo esc_url_raw($this -> get_managementpost(true)); ?>"><?php esc_html_e('Manage Subscriptions', 'wp-mailinglist'); ?></a></p>
</div>