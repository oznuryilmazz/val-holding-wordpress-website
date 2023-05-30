<?php // phpcs:ignoreFile ?>
<!-- Admin Mode Switcher -->

<?php
	
$admin_mode = get_user_option('newsletters_admin_mode', get_current_user_id());
if (empty($admin_mode)) $admin_mode = 'standard';
	
?>

<span class="newsletters-admin-mode-switcher">
	<label><?php esc_html_e('Admin Mode:', 'wp-mailinglist'); ?></label>
	<a href="" class="newsletters-admin-mode newsletters-admin-mode-standard btn btn-sm btn-info <?php echo ($admin_mode == "standard") ? 'active' : ''; ?>"><?php esc_html_e('Standard', 'wp-mailinglist'); ?></a>
	<a href="" class="newsletters-admin-mode newsletters-admin-mode-advanced btn btn-sm btn-warning <?php echo ($admin_mode == "advanced") ? 'active' : ''; ?>"><?php esc_html_e('Advanced', 'wp-mailinglist'); ?></a>
</span>