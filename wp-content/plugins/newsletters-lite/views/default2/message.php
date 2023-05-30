<?php // phpcs:ignoreFile ?>
<?php if (!empty($message)) : ?>
	<div class="alert alert-success">
		<li><i class="fa fa-check"></i>
		<?php echo wp_kses_post( wp_unslash($message)) ?>
	</div>
<?php endif; ?>