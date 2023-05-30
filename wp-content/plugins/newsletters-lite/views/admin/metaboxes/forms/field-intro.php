<?php // phpcs:ignoreFile ?>
<!-- Field Intro -->

<p>
	<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> fields . '&method=save&id=' . $field -> id)) ?>" class="button button-small"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit Custom Field', 'wp-mailinglist'); ?></a>
</p>