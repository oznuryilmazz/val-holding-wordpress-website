<?php // phpcs:ignoreFile ?>
<!-- Paid Subscription Settings -->

<?php

$paidsubscriptionredirect = $this -> get_option('paidsubscriptionredirect');
$paymentmethod = $this -> get_option('paymentmethod');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="subscriptionsY"><?php esc_html_e('Paid Subscriptions', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(sprintf(__('Turn this setting On to allow paid subscriptions. You can then create paid lists under %s > Mailing Lists with an interval and a price for the subscription.', 'wp-mailinglist'), $this -> name))); ?></th>
			<td>
				<label><input onclick="jQuery('#subscriptionsoptionsdiv').show();" <?php echo ($this -> get_option('subscriptions') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriptions" value="Y" id="subscriptionsY" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
				<label><input onclick="jQuery('#subscriptionsoptionsdiv').hide();" <?php echo ($this -> get_option('subscriptions') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriptions" value="N" id="subscriptionsN" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Turn On to allow paid subscriptions.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div class="newsletters_indented" id="subscriptionsoptionsdiv" style="display:<?php echo ($this -> get_option('subscriptions') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="<?php echo esc_html($this -> pre); ?>currency"><?php esc_html_e('Currency', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('Choose your preferred currency which will be used on the site and for the payment gateway used. It is highly recommended that you set this currency to be the same as the currency configured in your PayPal or 2CheckOut account to prevent conflicts.', 'wp-mailinglist'))); ?></th>
				<td>
					<?php $currencies = $this -> get_option('currencies'); ?>
					<?php if (!empty($currencies)) : ?>
						<select class="widefat" style="width:auto;" id="<?php echo esc_html($this -> pre); ?>currency" name="currency">
							<?php foreach ($currencies as $abb => $att) : ?>
							<option <?php echo ($this -> get_option('currency') == $abb) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $abb); ?>"><?php echo esc_html( $att['symbol']); ?> - <?php echo esc_html( $att['name']); ?> (<?php echo esc_html( $abb); ?>)</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
					<span class="howto"><?php esc_html_e('Choose the currency to charge your subscribers in.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="paidsubscriptionredirect_Y"><?php esc_html_e('Redirect Immediately to Payment', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input <?php echo (!empty($paidsubscriptionredirect) && $paidsubscriptionredirect == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="paidsubscriptionredirect" value="Y" id="paidsubscriptionredirect_Y" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
					<label><input <?php echo (!empty($paidsubscriptionredirect) && $paidsubscriptionredirect == "N") ? 'checked="checked"' : ''; ?> type="radio" name="paidsubscriptionredirect" value="N" id="paidsubscriptionredirect_N" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('Should the subscriber be redirected immediately to make a payment?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo esc_html($this -> pre); ?>adminordernotify"><?php esc_html_e('Admin Notification On Order', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('With this notification turned on, the email address specified in the Administrator Email setting will receive a notification when a paid subscription order has been placed.', 'wp-mailinglist'))); ?></th>
				<td>
					<label><input id="<?php echo esc_html($this -> pre); ?>adminordernotify" <?php echo ($this -> get_option('adminordernotify') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="adminordernotify" value="Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
					<label><input <?php echo ($this -> get_option('adminordernotify') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="adminordernotify" value="N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('Do you want to be notified via email when a paid subscription has been paid for?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="paymentmethod_paypal"><?php esc_html_e('Payment Method', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('Simply choose the payment method that you want to use. Either PayPal or 2CheckOut. If you are going to use PayPal you can make use of the auto recurring payments which will make management of paid subscriptions easier and increase residual income.', 'wp-mailinglist'))); ?></th>
				<td>
					<?php /*<label><input <?php echo (empty($paymentmethod) || $paymentmethod == "choice") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="choice" id="paymentmethod_choice" /> <?php esc_html_e('Choice', 'wp-mailinglist'); ?></label>
					<label><input id="<?php echo esc_html($this -> pre); ?>paymentmethod" onclick="jQuery('#paypal_settings').show(); jQuery('#2checkout_settings').hide();" <?php echo ($this -> get_option('paymentmethod') == "paypal") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="paypal" /> <?php esc_html_e('PayPal', 'wp-mailinglist'); ?></label>
					<label><input onclick="jQuery('#2checkout_settings').show(); jQuery('#paypal_settings').hide();" <?php echo ($this -> get_option('paymentmethod') == "2co") ? 'checked="checked"' : ''; ?> type="radio" name="paymentmethod" value="2co" /> <?php esc_html_e('2CheckOut', 'wp-mailinglist'); ?></label>*/ ?>
					
					<label><input <?php echo (!empty($paymentmethod) && in_array('paypal', $paymentmethod)) ? 'checked="checked"' : ''; ?> type="checkbox" name="paymentmethod[]" value="paypal" id="paymentmethod_paypal" /> <?php esc_html_e('PayPal', 'wp-mailinglist'); ?></label>
					<label><input <?php echo (!empty($paymentmethod) && in_array('2co', $paymentmethod)) ? 'checked="checked"' : ''; ?> type="checkbox" name="paymentmethod[]" value="2co" id="paymentmethod_2co" /> <?php esc_html_e('2CheckOut', 'wp-mailinglist'); ?></label>
					
					<span class="howto"><?php esc_html_e('Which payment method should be used for the paid subscriptions?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>