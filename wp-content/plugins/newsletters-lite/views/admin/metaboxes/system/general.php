<?php // phpcs:ignoreFile ?>
<!-- General System Settings -->

<?php
	
$csvdelimiter = $this -> get_option('csvdelimiter');	
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="csvdelimiter"><?php esc_html_e('Global CSV Delimiter', 'wp-mailinglist'); ?></label></th>
			<td>
				<input style="width:45px;" type="text" name="csvdelimiter" value="<?php echo esc_attr(wp_unslash($csvdelimiter)); ?>" id="csvdelimiter" />
				<span class="howto"><?php esc_html_e('The global CSV delimiter to use for exports and imports. The default is comma (,)', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>