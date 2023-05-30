<?php // phpcs:ignoreFile ?>

<div class="wrap" style="width:450px;">
	<h1><?php esc_html_e('DKIM Wizard', 'wp-mailinglist'); ?></h1>
	
	<h2><?php esc_html_e('Step 3: Verify the DKIM setup', 'wp-mailinglist'); ?></h2>
	
	<p>
		<?php esc_html_e('All done! Save the settings then send an email using the Test Email Settings utility to the email address below and you will receive the results on your From Address in a few minutes.', 'wp-mailinglist'); ?>
	</p>
	
	<textarea onmouseup="jQuery(this).unbind('mouseup'); return false;" onfocus="jQuery(this).select();" style="white-space:nowrap;" class="code" rows="2" cols="60"><?php echo esc_attr(wp_unslash("check-auth@verifier.port25.com")); ?></textarea>

	<span style="display:none;">	
		<form action="" onsubmit="jQuery('#dkimbutton').prop('disabled', true); jQuery('#dkimloading').show(); dkimwizard(jQuery(this).serialize()); return false;" id="dkimform3">
			<input type="hidden" name="domain" value="<?php echo esc_attr(wp_unslash($domain)); ?>" />
			<input type="hidden" name="selector" value="<?php echo esc_attr(wp_unslash($selector)); ?>" />
			<input type="hidden" name="public" value="<?php echo esc_attr(wp_unslash($public)); ?>" />
			<input type="hidden" name="private" value="<?php echo esc_attr(wp_unslash($private)); ?>" />
			<input type="hidden" name="goto" value="step2" />
			
			<button value="1" type="submit" name="continue">
				<?php esc_html_e('Continue', 'wp-mailinglist'); ?>
			</button>
		</form>
	</span>
		
	<p class="submit">
		<input onclick="jQuery.colorbox.close();" type="button" class="button button-secondary" name="close" value="<?php esc_html_e('Close', 'wp-mailinglist'); ?>" />
		<input onclick="jQuery('#goto').val('step2'); jQuery('#dkimform3').submit();" type="button" class="button button-secondary" name="back" value="<?php esc_html_e('&laquo; Back', 'wp-mailinglist'); ?>" />
		<input id="dkimbutton" onclick="jQuery('#settings-form').submit();" type="button" class="button button-primary" name="continue" value="<?php esc_html_e('Finished, save the settings &raquo;', 'wp-mailinglist'); ?>" />
		<span id="dkimloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
	</p>
</div>