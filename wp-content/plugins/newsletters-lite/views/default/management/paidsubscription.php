<?php // phpcs:ignoreFile ?>
<!-- Paid Subscription -->

<?php
	
$intervals = $this -> get_option('intervals');
$paymentmethod = $this -> get_option('paymentmethod');
	
?>

<div class="newsletters">
	<p>
		<a class="btn btn-secondary" href="<?php echo esc_attr(wp_unslash($this -> get_managementpost(true, false, false))); ?>"><?php esc_html_e('&laquo; Back to Manage Subscriptions', 'wp-mailinglist'); ?></a>
	</p>
	
	<h2><?php esc_html_e('Paid Subscription', 'wp-mailinglist'); ?></h2>
	<p>
		<?php echo sprintf(__('You are paying for your subscription to %s.', 'wp-mailinglist'), esc_html($mailinglist -> title)); ?><br/>
		<?php echo sprintf(__('You will be charged %s %s', 'wp-mailinglist'), $Html -> currency() . number_format($mailinglist -> price, 2, '.', ''), $intervals[$mailinglist -> interval]); ?>
	</p>
	
	<?php
	
	if (!empty($paymentmethod)) {
		if (count($paymentmethod) > 1) {
			foreach ($paymentmethod as $pmethod) {
				echo '<div class="pull-left" style="margin:15px 15px 0 0;">';
				$this -> paidsubscription_form($subscriber, $mailinglist, false, "_self", $extend, $pmethod);
				echo '</div>';
			}
		} else {
			$this -> paidsubscription_form($subscriber, $mailinglist, true, "_self", $extend, $paymentmethod[0]);
		}
	}	
		
	?>
</div>