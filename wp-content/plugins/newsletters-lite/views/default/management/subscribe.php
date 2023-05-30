<?php // phpcs:ignoreFile ?>
<!-- Subscribe -->

<div class="newsletters newsletters_subscribe <?php echo esc_html($this -> pre); ?>">
	<?php if (!empty($success)) : ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i>
			<?php esc_html_e('You have subscribed!', 'wp-mailinglist'); ?>
		</div>
	<?php else : ?>
		<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	<?php endif; ?>
	
	<p><a class="newsletters_button btn btn-primary" href="<?php echo esc_url_raw($this -> get_managementpost(true)); ?>"><?php esc_html_e('Manage Subscriptions', 'wp-mailinglist'); ?></a></p>
</div>