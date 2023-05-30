<?php // phpcs:ignoreFile ?>
<div class="newsletters <?php echo esc_html($this -> pre); ?> widget_newsletters">
	<form action="<?php echo home_url('?' . $this -> pre . 'method=offsite&list=' . $options['list']); ?>" method="post">
		<input type="hidden" name="list_id[]" value="<?php echo esc_html( $options['list']); ?>" />
		<?php if (!empty($fields)) : ?>
			<?php foreach ($fields as $field) : ?>
				<?php $this -> render_field($field -> id, false, $options['wpoptinid'], true, false, false, true); ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php $this -> render_field($Field -> email_field_id(), false, $options['wpoptinid'], true, false, false, true); ?>
		<?php endif; ?>
		<div>
			<input class="button ui-button" type="submit" name="subscribe" value="<?php echo esc_html( $options['button']); ?>" />
		</div>
	</form>
</div>