<?php // phpcs:ignoreFile ?>
<!-- 2CO Settings -->

<?php
	
$tcoaccount = $this -> get_option('tcoaccount');	
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo esc_html($this -> pre); ?>tcovendorid"><?php esc_html_e('Vendor ID/Account Number', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(__('Your 2CO (2CheckOut) vendor ID/account number as provided to you by 2CO when you registered an account with them.', 'wp-mailinglist'))); ?></th>
			<td>
				<input class="widefat" type="text" id="<?php echo esc_html($this -> pre); ?>tcovendorid" name="tcovendorid" value="<?php echo esc_attr(wp_unslash($this -> get_option('tcovendorid'))); ?>" />
				<span class="howto"><?php esc_html_e('Your 2CO vendor ID/account number provided by 2CO.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_html($this -> pre); ?>tcosecret"><?php esc_html_e('Vendor Secret', 'wp-mailinglist') ?></label>
			<?php echo ( $Html -> help(__('You can find and change the vendor secret in your 2CO account under Account > Site Management. This vendor secret is used for a hashing algorithm to ensure transactions are not tampered with.', 'wp-mailinglist'))); ?></th>
			<td>
				<input class="widefat" type="text" id="<?php echo esc_html($this -> pre); ?>tcosecret" name="tcosecret" value="<?php echo esc_attr(wp_unslash($this -> get_option('tcosecret'))); ?>" />
				<span class="howto"><?php esc_html_e('Used for hash encryption check', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="tcoaccount_live"><?php esc_html_e('Account Type', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (empty($tcoaccount) || $tcoaccount == "live") ? 'checked="checked"' : ''; ?> type="radio" name="tcoaccount" value="live" id="tcoaccount_live" /> <?php esc_html_e('Live', 'wp-mailinglist'); ?></label>
				<label><input <?php echo (!empty($tcoaccount) && $tcoaccount == "sandbox") ? 'checked="checked"' : ''; ?> type="radio" name="tcoaccount" value="sandbox" id="tcoaccount_sandbox" /> <?php esc_html_e('Sandbox', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Choose the correct setting based on whether you are using a live or sandbox 2CO account', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_html($this -> pre); ?>tcodemo"><?php esc_html_e('Demo Mode', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(__('Use 2CO demo mode for testing purposes in order to process transactions without charging the card. This setting will only work if demo mode is set to Parameter of On in your 2CO account under Account. You can use the testing card 4111111111111111 for an approved response.', 'wp-mailinglist'))); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('tcodemo') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tcodemo" value="Y" /> <?php esc_html_e('Yes'); ?></label>
				<label><input id="<?php echo esc_html($this -> pre); ?>tcodemo" <?php echo ($this -> get_option('tcodemo') == "N" || !$this -> get_option('tcodemo')) ? 'checked="checked"' : ''; ?> type="radio" name="tcodemo" value="N" /> <?php esc_html_e('No'); ?></label>
				<span class="howto"><?php esc_html_e('For testing purposes. No charges are made', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>