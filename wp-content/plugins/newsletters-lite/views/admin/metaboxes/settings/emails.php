<?php // phpcs:ignoreFile ?>
<!-- Emails Settings -->

<?php

$emailarchive = $this -> get_option('emailarchive');
$emailarchive_olderthan = $this -> get_option('emailarchive_olderthan');

$outfile_file = 'emailarchive.txt';
$outfile_path = $Html -> uploads_path() . DS . $this -> plugin_name . DS;
$outfile_full = $outfile_path . $outfile_file;

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="emailarchive"><?php esc_html_e('Email Archiving', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(sprintf(__('By turning on email archiving, the emails database table will be cleaned periodically of old emails sent. The emails are archived to %s which can be opened as a CSV file (comma separated).', 'wp-mailinglist'), '<code>' . $outfile_full . '</code>'))); ?></th>
			<td>
				<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#emailarchive_div').show(); } else { jQuery('#emailarchive_div').hide(); }" <?php echo (!empty($emailarchive)) ? 'checked="checked"' : ''; ?> type="checkbox" name="emailarchive" value="1" id="emailarchive" /> <?php esc_html_e('Enable archiving of sent emails', 'wp-mailinglist'); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div class="newsletters_indented" id="emailarchive_div" style="display:<?php echo (!empty($emailarchive)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="emailarchive_olderthan"><?php esc_html_e('Archive Older Than', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('Fill in the number of days to keep sent emails for. The default is 90 days.', 'wp-mailinglist'))); ?></th>
				<td>
					<input type="text" class="widefat" style="width:45px;" name="emailarchive_olderthan" value="<?php echo esc_attr(wp_unslash($emailarchive_olderthan)); ?>" id="emailarchive_olderthan" /> <?php esc_html_e('days', 'wp-mailinglist'); ?>
					<span class="howto"><?php esc_html_e('Archive emails older than a specific amount of days. 90 days is recommended', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>