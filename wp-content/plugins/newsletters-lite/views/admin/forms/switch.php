<!-- Form Switch -->
<?php // phpcs:ignoreFile ?>

<?php if ($forms = $this -> Subscribeform() -> find_all()) : ?>
	<select name="switchform" onchange="if (this.value != '') { window.location = '<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save&id=')) ?>' + this.value; }">
		<option value=""><?php esc_html_e('- Switch Form -', 'wp-mailinglist'); ?></option>
		<?php foreach ($forms as $form) : ?>
			<option value="<?php echo esc_attr($form -> id); ?>"><?php echo esc_attr($form -> title); ?></option>
		<?php endforeach; ?>
	</select>
<?php endif; ?>