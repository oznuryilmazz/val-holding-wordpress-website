<!-- Save an Autoresponder -->
<?php // phpcs:ignoreFile ?>

<?php

global $ID, $post_ID, $post;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

$alwayssend = isset($this -> Autoresponder() -> data -> alwayssend) ? $this -> Autoresponder() -> data -> alwayssend : '';
$sendauto = isset($this -> Autoresponder() -> data -> sendauto) ? $this -> Autoresponder() -> data -> sendauto : '';

$Html -> field_value('Autoresponder[title]');
$Html -> field_value('Autoresponder[lists]');

?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h1><?php esc_html_e('Save an Autoresponder', 'wp-mailinglist'); ?></h1>
    
    <form action="?page=<?php echo esc_html( $this -> sections -> autoresponders); ?>&amp;method=save" method="post">
	    <?php wp_nonce_field($this -> sections -> autoresponders . '_save'); ?>
    	<?php echo ( $Form -> hidden('Autoresponder[id]')); ?>
    	
    	<?php do_action('newsletters_admin_autoresponder_save_fields_before', $this -> Autoresponder() -> data); ?>
    
    	<table class="form-table">
        	<tbody>
            	<tr>
                	<th><label for="Autoresponder.title"><?php esc_html_e('Name', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('The name/title of your autoresponder for internal identification purposes. Your subscribers will not see this.', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<?php echo ( $Form -> text('Autoresponder[title]', array('placeholder' => __('Enter autoresponder title here', 'wp-mailinglist')))); ?>
                    	<span class="howto"><?php esc_html_e('Fill in a name/title for this autoresponder for identification purposes.', 'wp-mailinglist'); ?></span>
                    </td>
                </tr>
                <tr>
	                <th><label for=""><?php esc_html_e('Subscribe Form/s', 'wp-mailinglist'); ?></label></th>
	                <td>
		                <?php if ($forms = $this -> Subscribeform() -> select()) : ?>
		                	<div><label style="font-weight:bold;"><input type="checkbox" name="formsall" onclick="jqCheckAll(this, false, 'Autoresponder[forms]');" value="formsall" id="formsall" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label></div>
                        	<div id="newsletters-forms-checkboxes" class="scroll-list">
				                <?php foreach ($forms as $form_id => $form_title) : ?>
				                	<div><label><input <?php echo (!empty($this -> Autoresponder() -> data -> forms) && in_array($form_id, $this -> Autoresponder() -> data -> forms)) ? 'checked="checked"' : ''; ?> type="checkbox" name="Autoresponder[forms][]" value="<?php echo esc_html( $form_id); ?>" id="Autoresponder_forms_<?php echo esc_html( $form_id); ?>" /> <?php echo esc_html( $form_title); ?></label></div>
				                <?php endforeach; ?>
			                </div>
			                <span class="howto"><?php esc_html_e('Subscriptions on these subscribe forms will send this autoresponder.', 'wp-mailinglist'); ?></span>
			            <?php else : ?>
			            	<p class="newsletters_error"><?php esc_html_e('No forms are available.', 'wp-mailinglist'); ?></p>
			            <?php endif; ?>
	                </td>
                </tr>
                <tr>
	                <th></th>
	                <td>
		                <h2><?php esc_html_e('OR', 'wp-mailinglist'); ?></h2>
	                </td>
                </tr>
                <tr>
                	<th><label for="selectall"><?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('Choose the mailing list/s to attach to this autoresponder. When a subscriber subscribes to any of the chosen list(s) and the subscription is active, this autoresponder will be sent to the subscriber.', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
                        	<div><label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label></div>
                        	<!-- loop of mailing lists -->
                        	<div id="newsletters-mailinglists-checkboxes" class="scroll-list">
                            	<?php foreach ($mailinglists as $list_id => $list_title) : ?>
                                	<div><label><input <?php echo (!empty($this -> Autoresponder() -> data -> lists) && in_array($list_id, $this -> Autoresponder() -> data -> lists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="Autoresponder[lists][]" value="<?php echo esc_html( $list_id); ?>" id="checklist<?php echo esc_html( $list_id); ?>" /> <?php echo esc_html( $list_title); ?></label></div>
                                <?php endforeach; ?>
                            </div>
                            
                            <p><a href="#" class="button" onclick="jQuery.colorbox({title:'<?php echo esc_attr(wp_unslash(__('Add a Mailing List', 'wp-mailinglist'))); ?>', href:newsletters_ajaxurl + 'action=newsletters_mailinglist_save&security=<?php echo esc_html( wp_create_nonce('mailinglist_save')); ?>&fielddiv=newsletters-mailinglists-checkboxes&fieldname=Autoresponder[lists]'}); return false;"><i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Mailing List', 'wp-mailinglist'); ?></a></p>
                        <?php else : ?>
                        	<span class="error"><?php esc_html_e('No mailinglists found, please add.', 'wp-mailinglist'); ?></span>
                        <?php endif; ?>
                        <?php echo esc_html( $Html -> field_error('Autoresponder[lists]')); ?>
                    	<span class="howto"><?php esc_html_e('Subscriptions to these list(s) will be subscribed to this autoresponder.', 'wp-mailinglist'); ?></span>
                    </td>
                </tr>
                <tr>
                	<th><label for="Autoresponder_applyexisting_N"><?php esc_html_e('Apply to Existing Subscribers?', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('If you choose to apply this autoresponder to existing subscribers, it will be sent to all existing subscribers in the database subscribed to the specified mailing list/s. By default, the autoresponder only sends to new subscribers if this option is set to "No".', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<?php $applyexisting = $Html -> field_value('Autoresponder[applyexisting]'); ?>
                    	<label><input <?php echo ($applyexisting == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[applyexisting]" value="Y" id="Autoresponder_applyexisting_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                        <label><input <?php echo (empty($applyexisting) || $applyexisting == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[applyexisting]" value="N" id="Autoresponder_applyexisting_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                        
                        <?php if ($applyexisting == "Y") : ?><div class="newsletters_error"><?php esc_html_e('Autoresponder has already been applied to the existing subscribers before.', 'wp-mailinglist'); ?><br/>
                        <?php esc_html_e('This autoresponder will not be queued to the same subscribers as before again, only new ones.'); ?></div><?php endif; ?>
                    	<span class="howto">
							<?php esc_html_e('Should this autoresponder be applied to existing subscribers of the list(s) above?', 'wp-mailinglist'); ?><br/>
                            <?php esc_html_e('The send delay will be applied from the current date/time.', 'wp-mailinglist'); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                	<th><label for="Autoresponder_alwayssend_N"><?php esc_html_e('Always Send?', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('You may want an autoresponder to always send/queue when a subscriber subscribes, even if they are already subscribed and if they have already received this autoresponder email. Set this to Yes to always send and to No to ensure that each subscriber gets an autoresponder email only once.', 'wp-mailinglist'))); ?></th>
                	<td>
                		<label><input <?php echo (!empty($alwayssend) && $alwayssend == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[alwayssend]" value="Y" id="Autoresponder_alwayssend_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                		<label><input <?php echo ((empty($alwayssend)) || (!empty($alwayssend) && $alwayssend == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[alwayssend]" value="N" id="Autoresponder_alwayssend_N" /> <?php esc_html_e('No (recommended)', 'wp-mailinglist'); ?></label>
                		<span class="howto"><?php esc_html_e('Should this autoresponder always be sent to a subscriber, disregarding if it has been sent before?', 'wp-mailinglist'); ?></span>
                	</td>
                </tr>
                <tr>
                	<th><label for="Autoresponder.newsletter.exi"><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(sprintf(__('The email which will be used for the autoresponder can be either an existing sent/draft email from the %s > Sent &amp; Draft Emails section or you can choose to create a new email below.', 'wp-mailinglist'), $this -> name))); ?></th>
                    <td>                    
                    	<label><input onclick="jQuery('#newsletterdiv_exi').show(); jQuery('#newsletterdiv_new').hide();" <?php echo (empty($this -> Autoresponder() -> data -> newsletter) || (!empty($this -> Autoresponder() -> data -> newsletter) && $this -> Autoresponder() -> data -> newsletter == "exi")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[newsletter]" value="exi" id="Autoresponder.newsletter.exi" /> <?php esc_html_e('Choose Newsletter', 'wp-mailinglist'); ?></label>
                        <label><input onclick="jQuery('#newsletterdiv_exi').hide(); jQuery('#newsletterdiv_new').show();" <?php echo (!empty($this -> Autoresponder() -> data -> newsletter) && $this -> Autoresponder() -> data -> newsletter == "new") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[newsletter]" value="new" id="Autoresponder.newsletter.new" /> <?php esc_html_e('Create Newsletter', 'wp-mailinglist'); ?></label>
                        <?php echo esc_html( $Html -> field_error('Autoresponder[newsletter]')); ?>
                        <span class="howto"><?php esc_html_e('You can choose an existing newsletter or create one now.', 'wp-mailinglist'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div id="newsletterdiv_exi" style="display:<?php echo (empty($this -> Autoresponder() -> data -> newsletter) || (!empty($this -> Autoresponder() -> data -> newsletter) && $this -> Autoresponder() -> data -> newsletter == "exi")) ? 'block' : 'none'; ?>;">
        	<table class="form-table">
            	<tbody>
                	<tr>
                    	<th><label for="Autoresponder.history_id"><?php esc_html_e('Sent/Draft Newsletter', 'wp-mailinglist'); ?></label>
                    	<?php echo ( $Html -> help(sprintf(__('Choose the existing sent/draft email to use from the %s > History/Draft Emails section as is.', 'wp-mailinglist'), $this -> name))); ?></th>
                        <td>
                        	<?php if ($histories = $this -> History() -> select()) : ?>
                            	<select name="Autoresponder[history_id]" id="Autoresponder.history_id">
                                	<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
                                    <?php foreach ($histories as $h_id => $h_subject) : ?>
                                    	<option <?php echo (!empty($this -> Autoresponder() -> data -> history_id) && $this -> Autoresponder() -> data -> history_id == $h_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $h_id); ?>"><?php echo esc_html($h_subject); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php esc_html_e('Current:', 'wp-mailinglist'); ?>
                                <a target="_blank" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> send . '&method=history&id=' . $this -> Autoresponder() -> data -> history_id)) ?>"><i class="fa fa-pencil"></i></a>
                                <a target="_blank" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $this -> Autoresponder() -> data -> history_id)) ?>"><i class="fa fa-eye"></i></a>
                            <?php else : ?>
                            	<div class="alert alert-danger ui-state-error ui-corner-all">
	                            	<?php esc_html_e('No sent/draft emails found, please add.', 'wp-mailinglist'); ?>
	                            </div>
                            <?php endif; ?>
                            <?php echo esc_html( $Html -> field_error('Autoresponder[history_id]')); ?>
                        	<span class="howto"><?php esc_html_e('Choose an existing history/draft newsletter to use as the message for this autoresponder.', 'wp-mailinglist'); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="newsletterdiv_new" style="display:<?php echo (!empty($this -> Autoresponder() -> data -> newsletter) && $this -> Autoresponder() -> data -> newsletter == "new") ? 'block' : 'none'; ?>;">
	        <div id="post-body-content">
				<div id="titlediv">
            		<div id="titlewrap">
            			<input placeholder="<?php echo esc_attr(wp_unslash(__('Enter email subject here', 'wp-mailinglist'))); ?>" class="widefat" type="text" id="title" name="Autoresponder[nnewsletter][subject]" value="<?php echo esc_attr(wp_unslash($this -> Autoresponder() -> data -> nnewsletter['subject'])); ?>" id="Autoresponder_nnewsletter_subject" />
            		</div>
            	</div>
                <?php echo esc_html( $Html -> field_error('Autoresponder[nnewsletter_subject]')); ?>
            	<span class="howto"><?php esc_html_e('Subject of the newsletter.', 'wp-mailinglist'); ?></span>
				
				<div id="poststuff">
                    <div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section">                                    
                        <!-- The Editor -->
						<?php if (version_compare(get_bloginfo('version'), "3.3") >= 0) : ?>
							<?php wp_editor(wp_unslash($this -> Autoresponder() -> data -> nnewsletter['content']), 'content', array('tabindex' => 2, 'textarea_rows' => 20, 'editor_height' => 500)); ?>
						<?php else : ?>
							<?php the_editor(wp_unslash($this -> Autoresponder() -> data -> nnewsletter['content']), 'content', 'title', true, 2); ?>
						<?php endif; ?>
                        
                        <table id="post-status-info" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td id="wp-word-count">
                                        <?php esc_html_e('Word Count', 'wp-mailinglist'); ?>:
                                        <span id="word-count">0</span>
                                    </td>
                                    <td class="autosave-info">
                                        <span id="autosave" style="display:none;"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php echo esc_html( $Html -> field_error('Autoresponder[nnewsletter_content]')); ?>
                    </div> 
                </div> 
            	<span class="howto"><?php esc_html_e('Content of the newsletter.', 'wp-mailinglist'); ?></span>
                <p>
	                <a class="button button-secondary button-large" href="" onclick="jQuery.colorbox({title:'<?php esc_html_e('Shortcodes/Variables', 'wp-mailinglist'); ?>', maxHeight:'80%', maxWidth:'80%', href:newsletters_ajaxurl + 'action=<?php echo esc_html($this -> pre); ?>setvariables&security=<?php echo esc_html( wp_create_nonce('setvariables')); ?>'}); return false;"> <?php esc_html_e('Shortcodes/Variables', 'wp-mailinglist'); ?></a>
                </p>
			</div>
            
            <table class="form-table">
	            <tbody>
		            <tr>
                    	<th><label for="Autoresponder_nnewsletter_theme_id_0"><?php esc_html_e('Newsletter Template', 'wp-mailinglist'); ?></label>
                    	<?php echo ( $Html -> help(__('Choose the template to use for the email that will be sent to the subscribers for this autoresponder. The content above will be put into this template where the [newsletters_main_content] shortcode was specified.', 'wp-mailinglist'))); ?></th>
                        <td>
                        	<?php if ($themes = $Theme -> select()) : ?>
                            	<div><label><input <?php echo (empty($this -> Autoresponder() -> data -> nnewsletter['theme_id'])) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[nnewsletter][theme_id]" id="Autoresponder_nnewsletter_theme_id_0" value="0"> <?php esc_html_e('NONE', 'wp-mailinglist'); ?></label></div>
                            	<div class="scroll-list">
	                            	<?php foreach ($themes as $theme_id => $theme_title) : ?>
	                                	<div><label><input <?php echo (!empty($this -> Autoresponder() -> data -> nnewsletter['theme_id']) && $this -> Autoresponder() -> data -> nnewsletter['theme_id'] == $theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[nnewsletter][theme_id]" value="<?php echo esc_html( $theme_id); ?>" id="Autoresponder.nnewsletter.theme_id.<?php echo esc_html( $theme_id); ?>" /> <?php echo esc_html( $theme_title); ?></label></div>
	                                <?php endforeach; ?>
	                            </div>
                            <?php else : ?>
                            	<span class="error"><?php esc_html_e('No templates found, please add one.', 'wp-mailinglist'); ?></span>
                            <?php endif; ?>
                            <?php echo esc_html( $Html -> field_error('Autorseponder[nnewsletter_theme]')); ?>
                            <span class="howto"><?php esc_html_e('Choose the template to use for this new newsletter.', 'wp-mailinglist'); ?></span>
                        </td>
                    </tr>
	            </tbody>
            </table>
        </div>
        
        <table class="form-table">
        	<tbody>
	        	<tr>
		        	<th><label for="Autoresponder_sendauto"><?php esc_html_e('Send Automatically?', 'wp-mailinglist'); ?></label></th>
		        	<td>
			        	<label><input <?php echo (!empty($sendauto) || empty($this -> Autoresponder() -> data -> id)) ? 'checked="checked"' : ''; ?> onclick="if (jQuery(this).is(':checked')) { jQuery('#Autoresponder_sendauto_div').show(); } else { jQuery('#Autoresponder_sendauto_div').hide(); }" type="checkbox" name="Autoresponder[sendauto]" id="Autoresponder_sendauto" value="1" /> <?php esc_html_e('Yes, send/schedule automatically upon subscribe', 'wp-mailinglist'); ?></label>
			        	<span class="howto"><?php esc_html_e('Specify if this will be sent automatically or untick to use for another purpose.', 'wp-mailinglist'); ?></span>
		        	</td>
	        	</tr>
        	</tbody>
        </table>
        
        <div id="Autoresponder_sendauto_div" style="display:<?php echo (!empty($sendauto) || empty($this -> Autoresponder() -> data -> id)) ? 'block' : 'none'; ?>;">
	        <table class="form-table">
		        <tbody>
			    	<tr>
	                	<th><label for="Autoresponder.delay"><?php esc_html_e('Send Delay', 'wp-mailinglist'); ?></label>
	                	<?php echo ( $Html -> help(__('The send delay is measured in days. How many days after the subscriber has subscribed do you want this autoresponder message to send to the subscriber? You can specify 0 (zero) to have the autoresponder send to the subscriber immediately upon activation.', 'wp-mailinglist'))); ?></th>
	                    <td>
	                    	<?php echo ( $Form -> text('Autoresponder[delay]', array('width' => "45px"))); ?>
	                    	<?php $delayintervals = array('minutes' => __('Minutes', 'wp-mailinglist'), 'hours' => __('Hours', 'wp-mailinglist'), 'days' => __('Days', 'wp-mailinglist'), 'weeks' => __('Weeks', 'wp-mailinglist'), 'years' => __('Years', 'wp-mailinglist')); ?>
	                    	<?php echo ( $Form -> select('Autoresponder[delayinterval]', $delayintervals)); ?>
	                    	<?php esc_html_e('after subscribing and confirming', 'wp-mailinglist'); ?>
	                    	<span class="howto"><?php esc_html_e('Delay before sending this message. Set to 0 to send immediately upon subscribe/confirm.', 'wp-mailinglist'); ?></span>
	                    </td>
	                </tr> 
		        </tbody>
	        </table>
        </div>
        
        <table class="form-table">
	        <tbody>
                <tr>
                	<th><label for="Autoresponder.status.active"><?php esc_html_e('Status', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('The status of this autoresponder will determine if it is effective or not. If it is Active, it will be effective and this autoresponder will be sent to subscribers accordingly. If it is Inactive, it will be ignored and will not be used and no messages will be sent from this autoresponder.', 'wp-mailinglist'))); ?></th>
                    <td>
                    	<label><input <?php echo (empty($this -> Autoresponder() -> data -> status) || (!empty($this -> Autoresponder() -> data -> status) && $this -> Autoresponder() -> data -> status == "active")) ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[status]" value="active" id="Autoresponder.status.active" /> <?php esc_html_e('Active', 'wp-mailinglist'); ?></label>
                        <label><input <?php echo (!empty($this -> Autoresponder() -> data -> status) && $this -> Autoresponder() -> data -> status == "inactive") ? 'checked="checked"' : ''; ?> type="radio" name="Autoresponder[status]" value="inactive" id="Autoresponder.status.inactive" /> <?php esc_html_e('Inactive', 'wp-mailinglist'); ?></label>
                    	<span class="howto"><?php esc_html_e('Deactivating this autoresponder will prevent it from sending out any messages to subscribers.', 'wp-mailinglist'); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php do_action('newsletters_admin_autoresponder_save_fields_after', $this -> Autoresponder() -> data); ?>
    
    	<p class="submit">
        	<?php echo ( $Form -> submit(__('Save Autoresponder', 'wp-mailinglist'))); ?>
        	<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing', 'wp-mailinglist'); ?></label>
			</div>
        </p>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	newsletters_focus('#Autoresponder\\.title');
});
</script>