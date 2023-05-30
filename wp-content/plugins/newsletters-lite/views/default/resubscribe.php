<?php // phpcs:ignoreFile ?>
<div class="newsletters <?php echo esc_html($this -> pre); ?>">
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<h2><?php esc_html_e('You have resubscribed!', 'wp-mailinglist'); ?></h2>
	<p><?php esc_html_e('We are happy that you still want to receive our emails.', 'wp-mailinglist'); ?></p>
	<p><?php echo esc_url_raw($Html -> link(__('Manage Subscriptions', 'wp-mailinglist'), $Html -> retainquery('email=' . $subscriber -> email, $this -> get_managementpost(true)))); ?></p>
</div>