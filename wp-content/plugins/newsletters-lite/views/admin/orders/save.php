<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h2><?php esc_html_e('Save Subscription Order', 'wp-mailinglist'); ?></h2>

	<form action="?page=<?php echo esc_html( $this -> sections -> orders); ?>&amp;method=save&amp;id=<?php echo esc_html( $order -> id); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> orders . '_save'); ?>
		<input type="hidden" name="id" value="<?php echo esc_html( $order -> id); ?>" />
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmlOrder.subscriber_id"><?php esc_html_e('Subscriber ID', 'wp-mailinglist'); ?></label></th>
					<td>
						<input type="text" name="subscriber_id" value="<?php echo esc_html( $order -> subscriber_id); ?>" id="subscriber_id" class="widefat" style="width:65px;" />
					</td>
				</tr>
				<tr>
					<th><label for="list_id"><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
							<select name="list_id" id="list_id">
								<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
								<?php foreach ($mailinglists as $list_id => $list_title) : ?>
									<option <?php echo (!empty($order -> list_id) && $order -> list_id == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $list_id); ?>"><?php echo esc_html($list_title); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<span class="newsletters_error"><?php esc_html_e('No mailing lists are available.', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e('Amount', 'wp-mailinglist'); ?></th>
					<td><?php echo wp_kses_post($Html -> currency()); ?><input size="5" type="text" name="amount" value="<?php echo $order -> amount; ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Order Number', 'wp-mailinglist'); ?></th>
					<td><input type="text" size="15" name="order_number" value="<?php echo esc_html( $order -> order_number); ?>" /></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Product ID', 'wp-mailinglist'); ?></th>
					<td><input type="text" size="10" name="product_id" value="<?php echo esc_html( $order -> product_id); ?>" /></td>
				</tr>
				<tr>
					<th><label for="pmethod"><?php esc_html_e('Payment Method', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php $pmethods = array('pp' => __('PayPal', 'wp-mailinglist'), '2co' => __('2CheckOut', 'wp-mailinglist')); ?>
						<select name="pmethod" id="pmethod">
							<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
							<?php foreach ($pmethods as $pkey => $pval) : ?>
								<option <?php echo (!empty($order -> pmethod) && $order -> pmethod == $pkey) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $pkey); ?>"><?php echo esc_html( $pval); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<button value="1" type="submit" name="save_order" class="button-primary">
				<?php esc_html_e('Save Order', 'wp-mailinglist'); ?>
			</button>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing', 'wp-mailinglist'); ?></label>
			</div>
		</p>
	</form>
</div>