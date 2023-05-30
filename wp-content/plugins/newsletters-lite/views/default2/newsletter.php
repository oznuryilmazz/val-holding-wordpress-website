<?php // phpcs:ignoreFile ?>
<?php if (!empty($email)) : ?>
	<?php echo wpautop($this -> process_set_variables($subscriber, $user, esc_html($email -> message), $email -> id)); ?>
<?php endif; ?>