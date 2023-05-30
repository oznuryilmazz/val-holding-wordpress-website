<?php // phpcs:ignoreFile ?>
<!-- Subscribers Screen Options -->

<?php
	
$show_avatars = get_option('show_avatars');	
$saveipaddress = $this -> get_option('saveipaddress');
	
?>

<div class="newsletters">
	<form action="" method="post">
		<input type="hidden" name="screenoptions" value="1" />
		
		<?php $curfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_fields')); ?>
    	<?php $curcustomfields = maybe_unserialize($this -> get_option('screenoptions_subscribers_custom')); ?>
		<h5><?php esc_html_e('Show on screen', 'wp-mailinglist'); ?></h5>
        <div class="metabox-prefs">
        	<label><input <?php echo (empty($show_avatars)) ? 'disabled="disabled"' : ''; ?> <?php echo (!empty($curcustomfields) && in_array('gravatars', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="gravatars" id="custom_gravatars" /> <?php esc_html_e('Image', 'wp-mailinglist'); ?></label>
        	<label><input <?php echo (!empty($curcustomfields) && in_array('format', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="format" id="custom_format" /> <?php esc_html_e('Format (HTML/TEXT)', 'wp-mailinglist'); ?></label>
        	<label><input <?php echo (!empty($curcustomfields) && in_array('device', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="device" id="custom_device" /> <?php esc_html_e('Device', 'wp-mailinglist'); ?></label>
        	<label><input <?php echo (!empty($curcustomfields) && in_array('mandatory', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="mandatory" id="custom_mandatory" /> <?php esc_html_e('Mandatory', 'wp-mailinglist'); ?></label>
        	<?php if (!empty($saveipaddress)) : ?>
        		<label><input <?php echo (!empty($curcustomfields) && in_array('ip_address', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="ip_address" id="custom_ip_address" /> <?php esc_html_e('IP Address', 'wp-mailinglist'); ?></label>
				<label><input <?php echo (!empty($curcustomfields) && in_array('country', $curcustomfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custom[]" value="country" id="custom_country" /> <?php esc_html_e('Country', 'wp-mailinglist'); ?></label>
			<?php endif; ?>
	
			<?php if (!empty($fields)) : ?>		    	
	        	<?php foreach ($fields as $field) : ?>
	            	<label><input <?php echo (!empty($curfields) && in_array($field -> id, $curfields)) ? 'checked="checked"' : ''; ?> type="checkbox" name="fields[]" value="<?php echo esc_html( $field -> id); ?>" id="fields_<?php echo esc_html( $field -> id); ?>" /> <?php echo esc_html($field -> title); ?></label>
	            <?php endforeach; ?>
		    <?php endif; ?>
        </div>
	
		<input onclick="" type="submit" class="button" value="<?php esc_html_e('Apply', 'wp-mailinglist'); ?>" />
	</form>
</div>