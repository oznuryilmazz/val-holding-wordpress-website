<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Check for Updates', 'wp-mailinglist'); ?></h2>
	
	<?php
	
	$update = $this -> vendor('update');
	$update_info = $update -> get_version_info();
	
	if (version_compare($this -> version, $update_info['version']) < 0) {
		$this -> render('update', array('update_info' => $update_info), true, 'admin'); ?>
		
		<p><a href="https://tribulant.com" target="_blank"><img src="<?php echo esc_url_raw( $this -> render_url('images/logo.png', 'admin', false)); ?>" alt="tribulant" /></a></p>
		
		<?php $changelog = $update -> get_changelog(); ?>
		<div style="margin:10px 0; padding: 10px 20px; border:1px solid #ccc; border-radius:4px; moz-border-radius:4px; webkit-border-radius:4px;">
			<?php echo $changelog; ?>
		</div>
					
		<?php
	} else {
		?>

		<div class="updated"><p><i class="fa fa-check"></i> <?php esc_html_e('Your version of the Newsletter plugin is up to date.', 'wp-mailinglist'); ?></p></div>
		
		<?php if ($raw_response = get_transient('newsletters_update_info')) : ?>
			<?php if (!empty($raw_response['headers']['date'])) : ?>
				<p><?php echo sprintf(__('Last checked on <b>%s</b>', 'wp-mailinglist'), get_date_from_gmt(date("Y-m-d H:i:s", strtotime($raw_response['headers']['date'])), get_option('date_format') . ' ' . get_option('time_format'))); ?></p>
				<p><a href="?page=<?php echo esc_html( $this -> sections -> settings_updates); ?>&amp;method=check" class="button-primary"><i class="fa fa-history fa-fw"></i> <?php esc_html_e('Check Again', 'wp-mailinglist'); ?></a>
				<?php echo ( $Html -> help(__('The plugin checks for new versions every 24 hours. If you want to check right now, click the "Check Again" button in order to do so.', 'wp-mailinglist'))); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php
	}
	
	?>
</div>