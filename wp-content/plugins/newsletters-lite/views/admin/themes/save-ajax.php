<?php // phpcs:ignoreFile ?>
<div style="width:800px;" class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h1><?php esc_html_e('Save a Template', 'wp-mailinglist'); ?></h1>
    
    <p>
    	<?php esc_html_e('This is a full HTML template and should contain at least <code>[newsletters_main_content]</code> somewhere.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('Upload your images, stylesheets and other elements via FTP or the media uploader in WordPress.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('Please ensure that all links, images and other references use full, absolute URLs.', 'wp-mailinglist'); ?>
    </p>
    
    <?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
    
    <?php if ($success) : ?>
    	<p class="newsletters_success"><?php esc_html_e('Template has been saved', 'wp-mailinglist'); ?></p>
    	
    	<script type="text/javascript">
    	jQuery(document).ready(function() {
	    	jQuery.colorbox.close();
	    	newsletters_autosave();
    	});
    	</script>
    <?php endif; ?>
    
    <form onsubmit="newsletters_save_theme(this); return false;" action="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=save" method="post" enctype="multipart/form-data">
    	<?php echo ( $Form -> hidden('Theme[id]')); ?>
    	<?php echo ( $Form -> hidden('Theme[name]')); ?>
    
    	<table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Theme.title"><?php esc_html_e('Title', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('The title of this newsletter template for internal usage.', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<?php echo ( $Form -> text('Theme[title]', array('placeholder' => __('Enter template title here', 'wp-mailinglist')))); ?>
                    </td>
                </tr>
                <tr>
                	<th><label for="Theme.type_upload"><?php esc_html_e('Template Type', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('Choose how you want to save this newsletter template. You can either paste HTML code or upload a .html file.', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<label><input <?php echo ($Html -> field_value('Theme[type]') == "upload" || $Html -> field_value('Theme[type]') == "") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_upload').show(); jQuery('#typediv_paste').hide();" type="radio" name="Theme[type]" value="upload" id="Theme.type_upload" /> <?php esc_html_e('Upload an HTML File', 'wp-mailinglist'); ?></label>
                        <label><input <?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'checked="checked"' : ''; ?> onclick="jQuery('#typediv_paste').show(); jQuery('#typediv_upload').hide();" type="radio" name="Theme[type]" value="paste" id="Theme.type_paste" /> <?php esc_html_e('HTML Code', 'wp-mailinglist'); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="typediv_upload" style="display:<?php echo ($Html -> field_value('Theme[type]') == "" || $Html -> field_value('Theme[type]') == "upload") ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for=""><?php esc_html_e('Choose HTML File', 'wp-mailinglist'); ?></label></th>
                        <td>
                        	<input class="widefat" type="file" name="upload" value="" />
                            <?php if (!empty($Theme -> errors['upload'])) : ?>
                            	<div class="newsletters_error"><?php echo wp_kses_post($Theme -> errors['upload']); ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="typediv_paste" style="display:<?php echo ($Html -> field_value('Theme[type]') == "paste") ? 'block' : 'none'; ?>;">
        	<textarea name="Theme[paste]" class="widefat" id="Theme_paste" rows="10" cols="100%"><?php echo esc_attr(wp_unslash($Theme -> data -> paste)); ?></textarea>
        	
        	<script type="text/javascript">
        	jQuery(document).ready(function() {
            	jQuery('textarea#Theme_paste').ckeditor({
                	fullPage: true,
					allowedContent: true,
					height: 500,
					entities: false
            	});
        	});
        	</script>
        </div>
        
        <table class="form-table">
        	<tbody>
        		<tr>
        			<th><label for="Theme_inlinestyles_N"><?php esc_html_e('Inline Styles', 'wp-mailinglist'); ?></label>
        			<?php echo ( $Html -> help(__('Set this setting to "Yes" to automatically convert all CSS rules into inline, style attributes in the HTML elements. If you use this setting, be sure to create a backup of your original HTML for easier editing later on.', 'wp-mailinglist'))); ?></th>
        			<td>
        				<label><input onclick="if (!confirm('<?php esc_html_e('Please ensure that you create a local copy/backup of your newsletter template HTML for editing in the future.', 'wp-mailinglist'); ?>')) { return false; }" type="radio" name="Theme[inlinestyles]" value="Y" id="Theme_inlinestyles_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
        				<label><input type="radio" checked="checked" name="Theme[inlinestyles]" value="N" id="Theme_inlinestyles_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
        				<span class="howto"><?php esc_html_e('Convert CSS rules into inline, style attributes on elements.', 'wp-mailinglist'); ?></span>
        			</td>
        		</tr>
        		<tr>
        			<th><label for="Theme_acolor"><?php esc_html_e('Shortcode Link Color', 'wp-mailinglist'); ?></label>
        			<?php echo ( $Html -> help(__('Set the color of the links generated from the plugin shortcodes dynamically.', 'wp-mailinglist'))); ?></th>
        			<td>
        				<input type="text" class="color-picker" name="Theme[acolor]" value="<?php echo esc_html($Html -> field_value('Theme[acolor]')); ?>" id="Theme_acolor" />
						<span class="howto"><?php echo sprintf(__('Control the color of the links generated by shortcodes such as %s, %s, %s, etc.', 'wp-mailinglist'), '[newsletters_online]', '[newsletters_activate]', '[newsletters_unsubscribe]'); ?></span>
        			</td>
        		</tr>
        	</tbody>
        </table>
        
        <p class="submit">
	        <button value="1" type="submit" id="theme_save_button" name="save" class="button button-primary">
        		<span id="newsletters_themeedit_loader" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
        		<?php esc_html_e('Save Template', 'wp-mailinglist'); ?>
	        </button>
        </p>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	newsletters_focus('#Theme\\.title');
});
	
function newsletters_save_theme(form) {
	var formvalues = jQuery(form).serialize();
	jQuery('#newsletters_themeedit_loader').show();
	jQuery('#theme_save_button').prop('disabled', true);
	
	jQuery.post(newsletters_ajaxurl + 'action=newsletters_themeedit&security=<?php echo esc_html( wp_create_nonce('themeedit')); ?>&id=<?php echo sanitize_text_field(wp_unslash($_GET['id'])); ?>', formvalues, function(response) {
		jQuery('#cboxLoadedContent').html(response);
		jQuery.colorbox.resize();
	});
}
</script>