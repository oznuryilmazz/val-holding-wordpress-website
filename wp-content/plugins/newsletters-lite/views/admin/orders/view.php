<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h2><?php esc_html_e('View Order: ' . $order -> order_number, 'wp-mailinglist'); ?></h2>
	
	<div class="subsubsub" style="float:none;">
		<a href="<?php echo esc_url_raw($this -> url); ?>"><?php esc_html_e('&larr; All Orders', 'wp-mailinglist'); ?></a>
	</div>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<a class="button" href="?page=<?php echo esc_html( $this -> sections -> orders); ?>&amp;method=save&amp;id=<?php echo esc_html( $order -> id); ?>"><?php esc_html_e('Change', 'wp-mailinglist'); ?></a>
			<a class="button button-highlighted" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this order?', 'wp-mailinglist'); ?>')) { return false; }" href="?page=<?php echo esc_html( $this -> sections -> orders); ?>&amp;method=delete&amp;id=<?php echo esc_html( $order -> id); ?>"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
		</div>
	</div>
	
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e('Field', 'wp-mailinglist'); ?></th>
				<th><?php esc_html_e('Value', 'wp-mailinglist'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php esc_html_e('Field', 'wp-mailinglist'); ?></th>
				<th><?php esc_html_e('Value', 'wp-mailinglist'); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $class = ''; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></th>
				<td><a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>"><?php echo esc_html( $subscriber -> email); ?></a></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?></th>
				<td><a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=view&amp;id=<?php echo esc_html( $mailinglist -> id); ?>"><?php echo esc_html($mailinglist -> title); ?></a></td>
			</tr>
			<tr class="alternate">
				<th><?php esc_html_e('Amount', 'wp-mailinglist'); ?></th>
				<td><?php echo wp_kses_post($Html -> currency()); ?><?php echo number_format($order -> amount, 2, '.', ''); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Payment Method', 'wp-mailinglist'); ?></th>
				<td><?php echo (!empty($order -> pmethod) && $order -> pmethod == "2co") ? __('2CheckOut', 'wp-mailinglist') : __('PayPal', 'wp-mailinglist'); ?></td>
			</tr>
			<?php if (!empty($order -> pmethod) && $order -> pmethod == "2co") : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('2CO Order Number', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $order -> order_number); ?></td>
				</tr>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $order -> created); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $order -> modified); ?></td>
			</tr>
		</tbody>
	</table>
</div>