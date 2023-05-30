<?php // phpcs:ignoreFile ?>
<!-- Form Fields -->

<?php
	
$disabled_fields = false;
$id = (int) sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0);
if (!empty($id)) {
	if ($form_fields = $this -> FieldsForm() -> find_all(array('form_id' => $id))) {
		foreach ($form_fields as $form_field) {
			$disabled_fields[] = $form_field -> field_id;
		}
	}
}	
	
?>

<div id="minor-publishing">
	<div>
		<div>
			<?php $Db -> model = $Field -> model; ?>
			<?php if ($fields = $Db -> find_all()) : ?>
				<ul id="form_availablefields" class="">
					<?php foreach ($fields as $field) : ?>
						<li>
							<input <?php echo (!empty($disabled_fields) && in_array($field -> id, $disabled_fields)) ? 'disabled="disabled"' : ''; ?> type="button" class="button" id="newsletters_forms_availablefield_<?php echo esc_html( $field -> id); ?>" data-slug="<?php echo esc_attr(wp_unslash($field -> slug)); ?>" data-id="<?php echo esc_attr(wp_unslash($field -> id)); ?>" data-type="<?php echo esc_attr(wp_unslash($field -> type)); ?>" value="<?php echo esc_attr(wp_unslash(esc_html($field -> title))); ?>" />
						</li>
					<?php endforeach; ?>
				</ul>
				
				<br class="clear" />
			<?php else : ?>
				<p class="newsletters_error"><?php esc_html_e('No fields are available', 'wp-mailinglist'); ?></p>
			<?php endif; ?>		
		</div>
	</div>
	<div id="misc-publishing-actions">
		<div class="misc-pub-section">
			<p>
				<a class="button button-primary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> fields)) ?>"><?php esc_html_e('Manage Fields', 'wp-mailinglist'); ?></a>
				<a class="button button-secondary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> fields . '&method=save')) ?>"><i class="fa fa-plus-circle fa-fw"></i> <?php esc_html_e('Add Field', 'wp-mailinglist'); ?></a>
			</p>
		</div>
	</div>
</div>

