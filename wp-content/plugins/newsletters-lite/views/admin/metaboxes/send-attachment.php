<?php // phpcs:ignoreFile ?>
    <!-- Send Attachments -->

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="sendattachment"><?php esc_html_e('Send Attachment(s)', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($_POST['attachments'])) ? 'checked="checked"' : ''; ?> onclick="if (jQuery(this).is(':checked')) { jQuery('#attachmentdivinside').show(); } else { jQuery('#attachmentdivinside').hide(); }" type="checkbox" name="sendattachment" value="1" id="sendattachment" /> <?php esc_html_e('Yes, I want to attach files to this email', 'wp-mailinglist'); ?></label>
                <span class="howto"><?php esc_html_e('You can attach files to this email for your subscribers to receive.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div id="attachmentdivinside" style="display:<?php echo (!empty($_POST['attachments'])) ? 'block' : 'none'; ?>;">
    <table class="form-table">
    	<tbody>
            <tr>
            	<th><label for="addattachment"><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></label></th>
                <td>
                	<?php if (!empty($_POST['attachments'])) : ?>
                        <div id="currentattachments">
                           <ul style="margin:0; padding:0;"> 
                                <?php foreach (map_deep(wp_unslash($_POST['attachments']), 'sanitize_text_field') as $attachment) : ?>
                                	<li class="<?php echo esc_html($this -> pre); ?>attachment">
                                    	<?php echo wp_kses_post( $Html -> attachment_link($attachment, false)); ?>
                                        <a class="button button-primary" href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=removeattachment&amp;id=<?php echo esc_html($attachment['id']); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to remove this attachment?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
                                    </li>    
                                <?php endforeach; ?>
                           </ul>
                        </div>
                    <?php endif; ?>
                
                	<div id="newattachments"></div>
                    
                    <h4><a href="" id="addattachment" class="button button-secondary" onclick="add_attachment(); return false;"><i class="fa fa-paperclip"></i> <?php esc_html_e('Add an attachment', 'wp-mailinglist'); ?></a></h4>
                </td>
            </tr>
        </tbody>
    </table>
    
    <script type="text/javascript">
	var attachmentcount = 1;
	
	function delete_attachment(countid) {
		jQuery('#newattachment' + countid).remove();
	}
	
	function add_attachment() {
		var atthtml = "";
		atthtml += '<div class="newattachment" id="newattachment' + attachmentcount + '" style="display:none;">';
		atthtml += '<input type="file" name="attachments[]" value="" />';
		atthtml += ' <a class="button button-secondary button-small" href="" onclick="if (confirm(\'<?php esc_html_e('Are you sure you want to remove this?', 'wp-mailinglist'); ?>\')) { delete_attachment(' + attachmentcount + '); } return false;"><?php esc_html_e('Remove'); ?></a>';
		atthtml += '</div>';
		
		jQuery('#newattachments').append(atthtml);
		jQuery('#newattachment' + attachmentcount).fadeIn();
		attachmentcount++;	
	}
	
	function delete_current_attachment(attachmentid) {
			
	}
	</script>
</div>

<?php do_action('newsletters_create_email_attachment_below'); ?>