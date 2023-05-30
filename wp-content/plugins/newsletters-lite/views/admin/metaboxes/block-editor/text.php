<?php // phpcs:ignoreFile ?>
<!-- TEXT Version for Multipart Emails -->

<?php
	
global $post;
	
$defaulttexton = $this -> get_option('defaulttexton');
$defaulttextversion = $this -> get_option('defaulttextversion');	
$newsletters_customtexton = get_post_meta($post -> ID, '_newsletters_customtexton', true);
$newsletters_customtext = get_post_meta($post -> ID, '_newsletters_customtext', true);
$customtext = (!empty($defaulttexton)) ? $defaulttextversion : $newsletters_customtext;
	
?>

<p class="howto">
	<?php esc_html_e('By default, the TEXT version of multipart emails is automatically generated.', 'wp-mailinglist'); ?><br/>
	<?php esc_html_e('You may override that and specify your own TEXT version below.', 'wp-mailinglist'); ?>
</p>

<p>
	<label>
		<input onclick="if (jQuery(this).is(':checked')) { jQuery('#multimime_div').show(); } else { jQuery('#multimime_div').hide(); }" <?php echo (!empty($newsletters_customtexton) || !empty($defaulttexton)) ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_customtexton" value="1" id="newsletters_customtexton" />
		<?php esc_html_e('Specify a custom TEXT version of this newsletter', 'wp-mailinglist'); ?>
	</label>
</p>

<div id="multimime_div" style="display:<?php echo (!empty($newsletters_customtext) || !empty($defaulttexton)) ? 'block' : 'none'; ?>;">
	<textarea name="newsletters_customtext" id="newsletters_customtext" rows="6" cols="100%" class="widefat"><?php echo esc_attr(strip_tags(wp_unslash($customtext))); ?></textarea>
	<p><label><input <?php checked($defaulttexton, 1, true); ?> type="checkbox" name="defaulttexton" value="1" id="defaulttexton" /> <?php esc_html_e('Make this the default TEXT template for future use.', 'wp-mailinglist'); ?></label>
	<span class="howto"><?php esc_html_e('Specify the TEXT version of this multipart email. Only plain TEXT, no HTML is allowed.', 'wp-mailinglist'); ?></span>
</div>