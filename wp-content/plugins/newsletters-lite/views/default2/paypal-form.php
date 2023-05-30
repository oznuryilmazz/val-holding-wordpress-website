<?php // phpcs:ignoreFile ?>
<?php if (!empty($checkoutdata)) : ?>
	<?php if ($this -> get_option('paypalsandbox') == "Y") : ?>
		<form id="<?php echo esc_html($formid); ?>" action="<?php echo esc_html( $this -> get_option('paypalsandurl')); ?>" method="post" target="<?php echo esc_html($target); ?>">
	<?php else : ?>
		<form id="<?php echo esc_html($formid); ?>" action="<?php echo esc_html( $this -> get_option('paypalliveurl')); ?>" method="post" target="<?php echo esc_html($target); ?>">
	<?php endif; ?>	
		<?php foreach ($checkoutdata as $ckey => $cval) : ?>
			<input type="hidden" name="<?php echo esc_html( $ckey); ?>" value="<?php echo esc_attr($cval); ?>" />
		<?php endforeach; ?>
		<?php $buttontext = (empty($extend)) ? __('Pay with PayPal', 'wp-mailinglist') : __('Extend with PayPal', 'wp-mailinglist'); ?>
		<button value="1" type="submit" class="<?php echo esc_html($this -> pre); ?>button btn btn-success paybutton" name="checkout">
			<i class="fa fa-paypal fa-fw"></i> <?php echo wp_kses_post($buttontext); ?>
		</button>
	</form>
	
	<?php if ($autosubmit) : ?>
		<script type="text/javascript">
		document.getElementById('<?php echo esc_html($formid); ?>').submit();
		</script>
	<?php endif; ?>
<?php endif; ?>