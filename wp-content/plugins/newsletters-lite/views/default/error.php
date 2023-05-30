<?php // phpcs:ignoreFile ?>
<?php if (!empty($errors) && is_array($errors)) : ?>
	<div class="ui-state-error ui-corner-all">
		<ul class="newsletters_nolist">
			<?php foreach ($errors as $err) : ?>
				<li><i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post($err); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>