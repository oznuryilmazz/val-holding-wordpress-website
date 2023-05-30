<?php // phpcs:ignoreFile ?>
<?php if (!empty($mailinglist)) : ?>
	<?php if ($mailinglist -> paid == "Y") : ?>
		<label><input type="radio" name="paid" value="Y" /> <?php esc_html_e('Paid', 'wp-mailinglist'); ?></label>
		<label><input checked="checked" type="radio" name="paid" value="N" /> <?php esc_html_e('Not Paid', 'wp-mailinglist'); ?></label>
	<?php endif; ?>
<?php endif; ?>