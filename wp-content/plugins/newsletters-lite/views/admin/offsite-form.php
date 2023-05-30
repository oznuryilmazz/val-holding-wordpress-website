<?php // phpcs:ignoreFile ?>
    <div class="newsletters <?php echo esc_html($this -> pre); ?> widget_newsletters">
	<form action="<?php echo esc_url_raw(home_url()); ?>/?<?php echo esc_html($this -> pre); ?>method=offsite&title=<?php echo urlencode($options['title']); ?>&list=<?php echo esc_html( $options['list']); ?>" onsubmit="wpmloffsite(this);" method="post">
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

<script type="text/javascript">
function wpmloffsite(form) {
	window.open('', 'formpopup', 'resizable=0,scrollbars=1,width=<?php echo esc_html( $this -> get_option('offsitewidth')); ?>,height=<?php echo esc_html( $this -> get_option('offsiteheight')); ?>,status=0,toolbar=0');
	form.target = 'formpopup';
}
</script>

<?php if (!empty($options['stylesheet']) && $options['stylesheet'] == "Y") : ?>
	<style type="text/css">
	@import url('<?php echo esc_url_raw($this -> url()); ?>/views/<?php echo esc_html( $this -> get_option('theme_folder')); ?>/css/style.css');
	</style>
<?php endif; ?>