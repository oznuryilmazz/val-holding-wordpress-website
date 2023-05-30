<?php // phpcs:ignoreFile ?>
<?php if (!empty($errors) && is_array($errors)) : ?>
	<ul class="newsletters_error">
		<?php foreach ($errors as $err) : ?>
			<li><?php echo wp_kses_post($err); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>