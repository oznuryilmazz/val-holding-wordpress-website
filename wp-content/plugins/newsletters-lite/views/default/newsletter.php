<?php // phpcs:ignoreFile ?>
<?php if (!empty($email)) : ?>
	<?php echo wp_kses_post($this -> process_set_variables($subscriber, $user, $email -> message, $email -> id)); ?>
<?php endif; ?>