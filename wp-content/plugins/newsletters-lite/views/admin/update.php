<?php // phpcs:ignoreFile ?>
    <!-- Top Update Notice -->

<?php delete_transient('newsletters_update_info'); ?>

<?php $upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($this -> plugin_file), 'upgrade-plugin_' . $this -> plugin_file); ?>
<?php if ($this -> ci_serial_valid()) : ?>
	<?php if (!empty($update_info) && $update_info['is_valid_key'] == "1") : ?>		
		<div class="update-nag newsletters-update-nag-wrapper">
			<span class="newsletters-update-nag"></span> <?php echo sprintf(__('%s plugin %s is available.', 'wp-mailinglist'), $this -> name, $update_info['version']); ?><br/>
			<?php esc_html_e('You can update automatically or download to install manually.', 'wp-mailinglist'); ?>
			<br/><br/>
			<a href="<?php echo $upgrade_url; ?>" title="" class="button-primary"><i class="fa fa-magic"></i> <?php esc_html_e('Update Automatically', 'wp-mailinglist'); ?></a>
			<a target="_blank" href="<?php echo esc_url_raw($update_info['url']); ?>" title="" class="button-secondary"><i class="fa fa-download"></i> <?php esc_html_e('Download', 'wp-mailinglist'); ?></a>
			<a style="color:black; text-decoration:none;" href="<?php echo esc_url_raw( admin_url('admin.php')) ?>?page=<?php echo esc_html( $this -> sections -> settings_updates); ?>&amp;method=check" class="button button-secondary"><i class="fa fa-history fa-fw"></i> <?php esc_html_e('Check Again', 'wp-mailinglist'); ?></a>
			<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
				<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_updates)) ?>"><i class="fa fa-list-ul"></i> <?php esc_html_e('Changelog', 'wp-mailinglist'); ?></a>
				<a href="?newsletters_method=hideupdate&version=<?php echo esc_html( $update_info['version']); ?>" class="" style="position: absolute; top: 0; right: 0; margin: 10px 10px 0 0;"><i class="fa fa-times"></i></a>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="update-nag newsletters-update-nag-wrapper">
			<span class="newsletters-update-nag"></span> <?php echo sprintf(__('%s plugin %s is available.', 'wp-mailinglist'), $this -> name, $update_info['version']); ?><br/>
			<?php esc_html_e('Unfortunately your download has expired, please renew to gain access.', 'wp-mailinglist'); ?>
			<br/><br/>
			<a style="color:white; text-decoration:none;" href="<?php echo esc_url_raw($update_info['url']); ?>" target="_blank" title="" class="button button-primary"><?php esc_html_e('Renew Now', 'wp-mailinglist'); ?></a>
			<a style="color:black; text-decoration:none;" href="<?php echo esc_url_raw( admin_url('admin.php')) ?>?page=<?php echo esc_html( $this -> sections -> settings_updates); ?>&amp;method=check" class="button button-secondary"><i class="fa fa-history fa-fw"></i> <?php esc_html_e('Check Again', 'wp-mailinglist'); ?></a>
			<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
				<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_updates)) ?>"><i class="fa fa-list-ul"></i> <?php esc_html_e('Changelog', 'wp-mailinglist'); ?></a>
				<a href="?newsletters_method=hideupdate&version=<?php echo esc_html( $update_info['version']); ?>" class="" style="position: absolute; top: 0; right: 0; margin: 10px 10px 0 0;"><i class="fa fa-times"></i></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php else : ?>
	<div class="update-nag newsletters-update-nag-wrapper">
		<span class="newsletters-update-nag"></span> <?php echo sprintf(__('%s plugin %s is available.', 'wp-mailinglist'), $this -> name, $update_info['version']); ?><br/>
		<?php esc_html_e('You can update automatically or download to install manually.', 'wp-mailinglist'); ?>
		<br/><br/>
		<a href="<?php echo $upgrade_url; ?>" title="" class="button-primary"><i class="fa fa-magic"></i> <?php esc_html_e('Update Automatically', 'wp-mailinglist'); ?></a>
		<a target="_blank" href="https://wordpress.org/plugins/newsletters-lite/" title="" class="button-secondary"><i class="fa fa-download"></i> <?php esc_html_e('Download', 'wp-mailinglist'); ?></a>
		<a style="color:black; text-decoration:none;" href="<?php echo esc_url_raw( admin_url('admin.php')) ?>?page=<?php echo esc_html( $this -> sections -> settings_updates); ?>&amp;method=check" class="button button-secondary"><i class="fa fa-history fa-fw"></i> <?php esc_html_e('Check Again', 'wp-mailinglist'); ?></a>
		<?php if (empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page'] != $this -> sections -> settings_updates)) : ?>
			<a class="button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_updates)) ?>"><i class="fa fa-list-ul"></i> <?php esc_html_e('Changelog', 'wp-mailinglist'); ?></a>
			<a href="?newsletters_method=hideupdate&version=<?php echo esc_html( $update_info['version']); ?>" class="" style="position: absolute; top: 0; right: 0; margin: 10px 10px 0 0;"><i class="fa fa-times"></i></a>
		<?php endif; ?>
	</div>
<?php endif; ?>