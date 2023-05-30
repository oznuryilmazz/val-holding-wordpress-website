<?php // phpcs:ignoreFile ?>
<div style="width:400px;">
	<h3><?php esc_html_e('Test POP/IMAP Settings', 'wp-mailinglist'); ?></h3>
	
	<?php if ($success == true) : ?>
		<p>
			<?php esc_html_e('Congratulations, your POP/IMAP settings are working!', 'wp-mailinglist'); ?><br/>
			<?php esc_html_e('Remember to save your configuration settings.', 'wp-mailinglist'); ?>
		</p>
		
		<?php if (!empty($message)) : ?>
			<p class="<?php echo esc_html($this -> pre); ?>success"><?php echo wp_kses_post($message); ?></p>
		<?php endif; ?>
	<?php else : ?>
		<p class="newsletters_error"><?php esc_html_e('Unfortunately a POP/IMAP error occurred:', 'wp-mailinglist'); ?> <?php echo wp_kses_post( wp_unslash($error)) ?></p>
	<?php endif; ?>
	
	<p>
		<input class="button-secondary" onclick="jQuery.colorbox.close();" type="button" name="close" value="<?php esc_html_e('Close', 'wp-mailinglist'); ?>" />
	</p>
</div>