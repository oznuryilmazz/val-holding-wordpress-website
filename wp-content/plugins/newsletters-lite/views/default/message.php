<?php // phpcs:ignoreFile ?>
<?php if (!empty($message)) : ?>
	<div class="ui-state-highlight ui-corner-all">
		<p>
			<li><i class="fa fa-check"></i>
			<?php echo wp_kses_post( wp_unslash($message)) ?>
		</p>
	</div>
<?php endif; ?>