<?php // phpcs:ignoreFile ?>
<?php
	
global $newsletters_is_management;
$newsletters_is_management = true;	
$management_password = $this -> get_option('management_password');
	
$errors = isset($errors) ? $errors : [];

?>

<h3><?php esc_html_e('Profile', 'wp-mailinglist'); ?></h3>
<p><?php esc_html_e('Manage your subscriber profile data in the fields below.', 'wp-mailinglist'); ?></p>

<?php if (!empty($errors)) : ?>
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> <?php esc_html_e('Profile could not be saved, see errors below.', 'wp-mailinglist'); ?>
	</div>
<?php endif; ?>
	
<?php if (!empty($success) && $success == true) : ?>
	<div class="alert alert-success">
		<i class="fa fa-check"></i> <?php echo wp_kses_post($success); ?>
	</div>
<?php endif; ?>

<?php if (!empty($fields) && is_array($fields)) : ?>
	<form action="" method="post" onsubmit="wpmlmanagement_savefields(this);" id="subscribersavefieldsform" enctype="multipart/form-data">
    	<input type="hidden" name="subscriber_id" value="<?php echo esc_attr($subscriber -> id); ?>" />
    
		<?php 
		foreach ($fields as $field) : 
			$errors = isset($errors) ? $errors : [];
			$this -> render_field($field -> id, true, 'manage', true, true, false, false, $errors); 
        endforeach; 
		?>
        
        <?php $managementformatchange = $this -> get_option('managementformatchange'); ?>
        <?php if (!empty($managementformatchange) && $managementformatchange == "Y") : ?>
	        <div class="newsletters-fieldholder format">
		        <div class="form-group">
		        	<label for="format_html" class="control-label wpmlcustomfield"><?php esc_html_e('Email Format:', 'wp-mailinglist'); ?></label>
		        	<div class="radio">
		        		<label><input <?php echo ($subscriber -> format == "html") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="html" id="format_html" /> <?php esc_html_e('HTML (recommended)', 'wp-mailinglist'); ?></label>
						<label><input <?php echo ($subscriber -> format == "text") ? 'checked="checked"' : ''; ?> type="radio" name="format" value="text" id="format_text" /> <?php esc_html_e('TEXT', 'wp-mailinglist'); ?></label>
		        	</div>
		        </div>
	        </div>
	    <?php endif; ?>
	    
	    <div class="clearfix"></div>
	    
	    <?php if (!empty($management_password)) : ?>
		    <div>
			    <div class="col-md-12">
			    	<p><?php esc_html_e('Optional. Enter or update password for future login to manage subscriptions.', 'wp-mailinglist'); ?></p>
			    	
			    	<?php if (!empty($errors['password'])) : ?>
			    		<div class="alert alert-danger">
				    		<i class="fa fa-exclamation-triangle"></i> <?php echo wp_kses_post($errors['password']); ?>
			    		</div>
			    	<?php endif; ?>
			    </div>
		    </div>
		    <div>
			    <div class="newsletters-fieldholder">
				    <div class="form-group <?php echo (!empty($errors['password'])) ? 'has-error' : ''; ?>">
					    <label class="control-label"><?php esc_html_e('Password', 'wp-mailinglist'); ?></label>
					    <input type="password" class="form-control" name="password1" />
				    </div>
			    </div>
			    <div class="newsletters-fieldholder">
				    <div class="form-group <?php echo (!empty($errors['password'])) ? 'has-error' : ''; ?>">
					    <label class="control-label"><?php esc_html_e('Re-Enter Password', 'wp-mailinglist'); ?></label>
					    <input type="password" class="form-control" name="password2" />
				    </div>
			    </div>
		    </div>
		<?php endif; ?>
        
        <div id="<?php echo isset($widget_id) ? esc_html($widget_id) : 0; ?>-submit" class="newsletters-fieldholder newsletters_submit">
			<div class="form-group">
		        <div class="wpmlsubmitholder">
		            <button value="1" id="savefieldsbutton" class="newsletters_button btn btn-primary" type="submit" name="savefields">
		            	<span id="savefieldsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
		            	<?php esc_html_e('Save Profile', 'wp-mailinglist'); ?>
		            </button>
		        </div>
			</div>
        </div>
    </form>
    
    <script type="text/javascript">jQuery(document).ready(function() { if (jQuery.isFunction(jQuery.fn.select2)) { jQuery('.newsletters select').select2(); } jQuery('input:not(:button,:submit),textarea,select').focus(function(element) { jQuery(this).removeClass('newsletters_fielderror').nextAll('div.newsletters-field-error').slideUp(); }); });</script>
<?php else : ?>
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> <?php esc_html_e('No custom fields are available at this time.', 'wp-mailinglist'); ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() { 	
	$form = jQuery('#subscribersavefieldsform');
	var divs = jQuery($form).find('.newsletters-fieldholder:not(.newsletters_submit, .hidden)');
	for (var i = 0; i < divs.length; i += 2) {
		divs.slice(i, i + 2).wrapAll("<div class='row'></div>");
	}
	jQuery(divs).wrap('<div class="col-md-6"></div>');

	if (jQuery.isFunction(jQuery.fn.ajaxForm)) {
		jQuery('#subscribersavefieldsform').ajaxForm({
			url: newsletters_ajaxurl + "action=managementsavefields&security=<?php echo esc_html( wp_create_nonce('managementsavefields')); ?>",
			type: "POST",
			cache: false,
			success: function(response) {							
				jQuery('#savefields').html(response);
				jQuery('#savefieldsbutton').prop('disabled', false);
			}, 
			error: function(response) {
				alert('<?php echo esc_js('An error occurred, please try again', 'wp-mailinglist'); ?>');
				jQuery('#savefieldsbutton').prop('disabled', false);
			}
		});	
	}
});
</script>