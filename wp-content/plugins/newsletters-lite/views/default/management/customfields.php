<?php // phpcs:ignoreFile ?>
<?php
	
global $newsletters_is_management;
$newsletters_is_management = true;	
	
?>

<h3><?php esc_html_e('Profile', 'wp-mailinglist'); ?></h3>
<p><?php esc_html_e('Manage your subscriber profile data in the fields below.', 'wp-mailinglist'); ?></p>

<?php if (!empty($errors)) : ?>
	<?php /*$this -> render('error', array('errors' => $errors), true, 'default');*/ ?>
<?php endif; ?>
	
<?php if (!empty($success) && $success == true) : ?>
	<div class="ui-state-highlight ui-corner-all">
		<p><i class="fa fa-check"></i> <?php echo $successmessage; ?></p>
	</div>
<?php endif; ?>

<?php if (!empty($fields) && is_array($fields)) : ?>
	<form action="" method="post" onsubmit="wpmlmanagement_savefields(this); return false;" id="subscribersavefieldsform">
    	<input type="hidden" name="subscriber_id" value="<?php echo esc_html( $subscriber -> id); ?>" />
    
		<?php foreach ($fields as $field) : ?>
            <?php $this -> render_field($field -> id, true, 'manage', true, true, false, false, $errors); ?>
        <?php endforeach; ?>
        
        <?php $managementformatchange = $this -> get_option('managementformatchange'); ?>
        <?php if (!empty($managementformatchange) && $managementformatchange == "Y") : ?>
	        <div class="newsletters-fieldholder format">
	        	<label for="format_html" class="wpmlcustomfield"><?php esc_html_e('Email Format:', 'wp-mailinglist'); ?></label>
	        	<label><input <?php echo ($subscriber -> format == "html") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="html" id="format_html" /> <?php esc_html_e('HTML (recommended)', 'wp-mailinglist'); ?></label>
	        	<label><input <?php echo ($subscriber -> format == "text") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="text" id="format_text" /> <?php esc_html_e('TEXT', 'wp-mailinglist'); ?></label>
	        </div>
	    <?php endif; ?>
        
        <div class="wpmlsubmitholder">
            <button value="1" id="savefieldsbutton" class="<?php echo esc_html($this -> pre); ?>button ui-button-primary" type="submit" name="savefields">
            	<?php esc_html_e('Save Profile', 'wp-mailinglist'); ?>
            	<span id="savefieldsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
            </button>
        </div>
    </form>
    
    <script type="text/javascript">jQuery(document).ready(function() { if (jQuery.isFunction(jQuery.fn.button)) { jQuery('#savefieldsbutton').button(); } if (jQuery.isFunction(jQuery.fn.select2)) { jQuery('.newsletters select').select2(); } jQuery('input:not(:button,:submit),textarea,select').focus(function(element) { jQuery(this).removeClass('newsletters_fielderror').nextAll('div.newsletters-field-error').slideUp(); }); });</script>
<?php else : ?>
	<div class="ui-state-error ui-corner-all">
		<p><i class="fa fa-exclamation-triangle"></i> <?php esc_html_e('No custom fields are available at this time.', 'wp-mailinglist'); ?></p>
	</div>
<?php endif; ?>