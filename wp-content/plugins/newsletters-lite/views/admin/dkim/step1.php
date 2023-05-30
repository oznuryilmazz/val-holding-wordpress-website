<?php // phpcs:ignoreFile ?>

<div class="wrap" style="width:450px;">
	<h1><?php esc_html_e('DKIM Wizard', 'wp-mailinglist'); ?></h1>
	
	<p class="howto"><?php echo sprintf(__('Setting up DKIM for the domain %s using the selector %s.', 'wp-mailinglist'), '<strong>' . $domain . '</strong>', '<strong>' . $selector . '</strong>'); ?></p>
	
	<h2><?php esc_html_e('Step 1: Save the private key', 'wp-mailinglist'); ?></h2>
	
	<textarea onmouseup="jQuery(this).unbind('mouseup'); return false;" onfocus="jQuery(this).select();" style="white-space:nowrap;" class="code" rows="13" cols="60"><?php echo wp_kses_post( wp_unslash($private)) ?></textarea>
	
	<p>
		<?php esc_html_e('The private key above has been filled into the DKIM Private Key box for you.', 'wp-mailinglist'); ?>
	</p>

	<form action="" onsubmit="do_private_key(); jQuery('#dkimbutton').prop('disabled', true); jQuery('#dkimloading').show(); dkimwizard(jQuery(this).serialize()); return false;">	
		<input type="hidden" name="domain" value="<?php echo esc_attr($domain); ?>" />
		<input type="hidden" name="selector" value="<?php echo esc_attr($selector); ?>" />
		<input type="hidden" name="public" value="<?php echo esc_attr($public); ?>" />
		<input type="hidden" name="private" value="<?php echo esc_attr($private); ?>" />
		<input type="hidden" name="goto" value="step2" />
		
		<p class="submit">
			<input onclick="jQuery.colorbox.close();" class="button button-secondary" type="button" name="close" value="<?php esc_html_e('Close', 'wp-mailinglist'); ?>" />
			<input id="dkimbutton" class="button button-primary" type="submit" name="continue" value="<?php esc_html_e('Great, next step &raquo;', 'wp-mailinglist'); ?>" />
			<span id="dkimloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
		</p>
	</form>
</div>

<script type="text/javascript">
function do_private_key() {
	jQuery('#dkim_private_div').show();
	jQuery('#dkim_private').val(<?php echo wp_json_encode($private); ?>);
}
</script>