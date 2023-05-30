<?php // phpcs:ignoreFile ?>
<!-- Subscribe Form -->
<?php if(!isset($errors)) {
    $errors = array();
}
?>
<div class="newsletters newsletters-form-wrapper" id="newsletters-<?php echo $form -> id; ?>-form-wrapper">
    <form class="newsletters-subscribe-form" action="<?php echo $Html -> retainquery($this -> pre . 'method=offsite&form=' . $form -> id, home_url()); ?>" method="post" id="newsletters-<?php echo $form -> id; ?>-form">
		<?php if (!empty($form -> form_fields)) : ?>
			<?php foreach ($form -> form_fields as $field) : ?>
				<?php $this -> render_field($field -> field_id, false, $form -> id, false, false, false, true, $errors, $form -> id, $field); ?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php $this -> render_field($Field -> email_field_id(), false, $form -> id, false, false, false, true); ?>
		<?php endif; ?>
		<p>
			<input class="button ui-button" type="submit" name="subscribe" value="<?php echo esc_attr(wp_unslash(esc_html($form -> buttontext))); ?>" />
		</p>
	</form>
</div>