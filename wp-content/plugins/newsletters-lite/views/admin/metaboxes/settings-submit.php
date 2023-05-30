<?php // phpcs:ignoreFile ?>
<?php

$debugging = get_option('tridebugging');

?>

<div id="submitpost" class="submitbox">
	<div id="minor-publishing">
		<div id="minor-publishing-actions">
			<div id="save-action">
				<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> support)) ?>" class="button" id="save-post"><i class="fa fa-life-ring"></i> <?php esc_html_e('Support', 'wp-mailinglist'); ?></a>
			</div>
			<div id="preview-action">
				<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_updates)) ?>" class="button preview" id="post-preview"><i class="fa fa-cloud"></i> <?php esc_html_e('Updates', 'wp-mailinglist'); ?></a>
			</div>
			<div class="clear"></div>
		</div>
		<div id="misc-publishing-actions">
			<div class="misc-pub-section">
				<a href="?page=<?php echo esc_html( $this -> sections -> settings); ?>&amp;method=checkdb"><i class="fa fa-database"></i> <?php esc_html_e('Check/Optimize Database', 'wp-mailinglist'); ?></a>
				<?php echo ( $Html -> help(__('This function will check all database tables of the plugin to ensure that all fields/columns are available and created as intended. In addition to that, it will run a simple optimize query on each database table to clear overheads, fix indexes, etc.', 'wp-mailinglist'))); ?>
			</div>
			<div class="misc-pub-section">
				<a class="delete" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to reset all configuration settings to their defaults?', 'wp-mailinglist'); ?>')) { return false; }" href="<?php echo wp_nonce_url(admin_url('admin.php?page=newsletters-settings&method=reset'), $this -> sections -> settings . '_reset'); ?>"><i class="fa fa-undo"></i> <?php esc_html_e('Reset Defaults', 'wp-mailinglist'); ?></a>
				<?php echo ( $Html -> help(__('Upon confirmation, this action will permanently reset all configuration settings to their defaults. You will not lose lists, subscribers, sent/draft emails or other data, just the actual configuration settings are reset.', 'wp-mailinglist'))); ?>
			</div>
			<div class="misc-pub-section">
				<a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=offsitewizard" title="<?php esc_html_e('Generate HTML code for an offsite subscription form', 'wp-mailinglist'); ?>"><i class="fa fa-code"></i> <?php esc_html_e('Generate Offsite Code', 'wp-mailinglist'); ?></a>
				<?php echo ( $Html -> help(__('The offsite wizard will assist you in generating static HTML code and a URL to use on any 3rd party website or some 3rd party applications accordingly.', 'wp-mailinglist'))); ?>
			</div>
			<div class="misc-pub-section">
				<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> gdpr)) ?>"><i class="fa fa-check fa-fw"></i> <?php esc_html_e('GDPR Requirements', 'wp-mailinglist'); ?></a>
			</div>
			<div class="misc-pub-section misc-pub-section-last">
				<label><input <?php echo (!empty($debugging) && $debugging == 1) ? 'checked="checked"' : ''; ?> type="checkbox" name="debugging" value="1" id="debugging" /><i class="fa fa-bug"></i> <?php esc_html_e('Turn on debugging', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(sprintf(__('Ticking/checking this setting and saving the settings will turn on debugging. It will turn on PHP error reporting and also WordPress database errors. It will help you to troubleshoot problems where something is not working as expected or a blank page is appearing. Certain things are also logged in the %s', 'wp-mailinglist'), '<a target="_blank" href="?page=<?php echo esc_html( $this -> sections -> view_logs); ?>" >' . __('log file', 'wp-mailinglist') . '</a>'))); ?>
				<p>
					<a href="?page=<?php echo esc_html( $this -> sections -> view_logs); ?>" ><?php esc_html_e('View the log file', 'wp-mailinglist'); ?></a>
					<a onclick="if (!confirm('<?php echo esc_attr(__('Are you sure you want to clear the log file?', 'wp-mailinglist')); ?>')) { return false; }" href="<?php echo wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> settings . '&method=clearlog'), $this -> sections -> settings . '_clearlog'); ?>" class="newsletters_error"><i class="fa fa-times fa-fw"></i></a>
				</p>
			</div>
		</div>
	</div>
	<div id="major-publishing-actions">
		<div id="publishing-action">
			<button id="publish" class="button button-primary button-large" type="submit" name="save" value="1" class="button button-highlighted">
				<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Save Settings', 'wp-mailinglist'); ?>
			</button>
		</div>
		<br class="clear" />
	</div>
</div>