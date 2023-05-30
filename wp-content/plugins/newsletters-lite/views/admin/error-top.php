<?php // phpcs:ignoreFile ?>
<?php if (!empty($message)) : ?>
	<div id="error" class="notice notice-error notice-newsletters <?php echo (!empty($dismissable)) ? 'is-dismissible' : ''; ?>" data-notice="<?php echo esc_attr($type); ?>">
		<p><i class="fa fa-times fa-fw"></i> <?php echo wp_kses_post($message); ?></p>
	</div>
<?php endif; ?>