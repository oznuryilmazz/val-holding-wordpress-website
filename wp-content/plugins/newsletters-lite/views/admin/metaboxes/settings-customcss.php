<?php // phpcs:ignoreFile ?>
<!-- Custom CSS, Theme and Scripts -->

<table class="form-table">
	<tbody>
		<tr>
        	<th><label for="theme_folder"><?php esc_html_e('Select Theme Folder', 'wp-mailinglist'); ?></label></th>
            <td>
            	<?php if ($themefolders = $this -> get_themefolders()) : ?>
                	<select onchange="newsletters_change_themefolder(this.value);" name="theme_folder" id="theme_folder">
                    	<?php foreach ($themefolders as $themefolder) : ?>
                        	<option <?php echo ($this -> get_option('theme_folder') == $themefolder) ? 'selected="selected"' : ''; ?> name="<?php echo esc_html( $themefolder); ?>"><?php echo esc_html( $themefolder); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="change_themefolder_loading" style="display:none;"><i class="fa fa-spin fa-fw fa-refresh"></i></span>
                    <span class="howto"><?php echo sprintf(__('Select the folder inside "%s" to render template files from. eg. "default"', 'wp-mailinglist'), $this -> plugin_name . '/views/'); ?></span>
                <?php else : ?>
                	<p class="newsletters_error"><?php esc_html_e('No theme folders could be found inside the "' . $this -> plugin_name . '/views/" folder.', 'wp-mailinglist'); ?>
                <?php endif; ?>
            </td>
        </tr>
	        <tr>
	        	<th><?php esc_html_e('Child Theme Folder', 'wp-mailinglist'); ?></th>
	        	<td>
		        	<?php if ($this -> has_child_theme_folder()) : ?>
	        			<p><?php echo sprintf(__('Yes, there is a %s folder inside your theme folder %s', 'wp-mailinglist'), '<code>newsletters</code>', '<code>' . basename(get_stylesheet_directory()) . '</code>'); ?></p>
	        		<?php else : ?>
	        			<?php if (apply_filters('newsletters_whitelabel', true)) : ?>
	        				<p><?php echo sprintf(__('No child theme folder. See the %s to use this.', 'wp-mailinglist'), '<a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/7890" target="_blank">' . __('documentation', 'wp-mailinglist') . '</a>'); ?></p>
	        			<?php else : ?>
	        				<p><?php esc_html_e('No child theme folder.', 'wp-mailinglist'); ?></p>
	        			<?php endif; ?>
	        		<?php endif; ?>
	        	</td>
	        </tr>
	</tbody>
</table>

<script type="text/javascript">
	var newsletters_change_themefolder = function(themefolder) {
		if (typeof themefolder !== 'undefined') {
			jQuery('#change_themefolder_loading').show();
			jQuery('#defaultscriptsstyles').slideUp();
			
			jQuery.ajax({
				url: newsletters_ajaxurl + 'action=newsletters_change_themefolder&security=<?php echo esc_html( wp_create_nonce('change_themefolder')); ?>',
				method: "POST",
				data: {themefolder:themefolder}
			}).done(function(response) {
				jQuery('#defaultscriptsstyles').html(response).slideDown();
			}).fail(function(jqXHR, textStatus, errorThrown) {
				alert('Ajax call failed: ' + errorThrown);
			}).always(function() {
				jQuery('#change_themefolder_loading').hide();
			});
		}
	}
</script>

<!-- Default Scripts & Styles -->
<div id="defaultscriptsstyles">
	<?php $this -> render('settings' . DS . 'defaultscriptsstyles', false, true, 'admin'); ?>
</div>

<table class="form-table">
	<tbody>
		<tr class="advanced-setting">
			<th><label for="customcssN"><?php esc_html_e('Use Custom CSS', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('customcss') == "Y") ? 'checked="checked"' : ''; ?> onclick="jQuery('#customcssdiv').show();" type="radio" name="customcss" value="Y" id="customcssY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('customcss') == "N") ? 'checked="checked"' : ''; ?> onclick="jQuery('#customcssdiv').hide();" type="radio" name="customcss" value="N" id="customcssN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                <span class="howto"><?php esc_html_e('Load any additional CSS into the site as needed.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="customcssdiv" style="display:<?php echo ($this -> get_option('customcss') == "Y") ? 'block' : 'none'; ?>;">
	<div id="customcsseditor"></div>
	<textarea name="customcsscode" id="customcsscode" rows="12" class="widefat"><?php echo htmlspecialchars(wp_unslash($this -> get_option('customcsscode'))); ?></textarea>
</div>

<style type="text/css">
#customcsseditor {
	position: relative;
	width: 100%;
	height: 300px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
	var editor = ace.edit("customcsseditor", {
		mode: 'ace/mode/css'	
	});
	
	var textarea = jQuery('#customcsscode').hide();
	editor.getSession().setValue(textarea.val());
	
	editor.getSession().on('change', function(){
		textarea.val(editor.getSession().getValue());
	});
});
</script>