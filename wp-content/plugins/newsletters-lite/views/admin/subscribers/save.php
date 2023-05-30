<?php // phpcs:ignoreFile ?>
<!-- Save a Subscriber -->

<?php

$mandatory = $Html -> field_value('Subscriber[mandatory]');
	
?>

<div class="wrap newsletters <?php echo $this -> pre; ?>">
    <h2><?php _e('Save a Subscriber', 'wp-mailinglist'); ?></h2>
    <?php  (isset($errors)&& !empty($errors) ) ? $this -> render('error', array('errors' => $errors)) : ''; ?>
    <form id="newsletters-subscriber-form" name="optinform<?php echo (isset($subscriber) && !empty($subscriber) ) ? $subscriber -> id : ''; ?>" action="?page=<?php echo $this -> sections -> subscribers; ?>&amp;method=save" method="post" enctype="multipart/form-data">
		<input type="hidden" name="Subscriber[active]" value="Y" />
		
		<?php
			
		$fields = $Subscriber -> table_fields;
		unset($fields['key'], $fields['email'], $fields['registered'], $fields['format'], $fields['mandatory'], $fields['modified']);	
			
		?>
		<?php foreach ($fields as $field => $attributes) : ?>
			<?php echo ( $Form -> hidden('Subscriber[' . $field . ']')); ?>
		<?php endforeach; ?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Subscriber.email"><?php esc_html_e('Email Address', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('This is the email address of the subscriber on which the subscriber will receive email newsletters and other notifications.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php echo ( $Form -> text('Subscriber[email]', array('placeholder' => __('Enter email address here', 'wp-mailinglist')))); ?>
						<span class="howto"><?php esc_html_e('Valid email address of the subscriber to receive newsletters.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="checkboxall"><?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?><label>
					<?php echo ( $Html -> help(__('Choose the mailing list/s to subscribe this user to. Sending to any of the list(s) that you subscribe this user to will result in this user receiving the email newsletter.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>						
							<div><label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" onclick="jqCheckAll(this, 'newsletters-subscriber-form', 'Subscriber[mailinglists]');" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label></div>
							<div class="scroll-list" id="newsletters-mailinglists-checkboxes">
								<?php foreach ($mailinglists as $list_id => $list_title) : ?>
									<?php $mailinglist = $Mailinglist -> get($list_id); ?>
									<label><input onclick="jQuery('#mailinglist_<?php echo esc_html( $list_id); ?>_expiration').toggle();" <?php echo (!empty($Subscriber -> data -> mailinglists) && in_array($list_id, $Subscriber -> data -> mailinglists)) ? 'checked="checked"' : ''; ?> type="checkbox" class="Mailinglist_checklist" name="Subscriber[mailinglists][]" value="<?php echo esc_html( $list_id); ?>" id="Subscriber_mailinglists_<?php echo esc_html( $list_id); ?>" /> <?php echo esc_html($list_title); ?> (<?php echo esc_html( $SubscribersList -> count(array('list_id' => $list_id))); ?> <?php esc_html_e('subscribers', 'wp-mailinglist'); ?>)</label><br/>
									<?php if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") : ?>
										<div id="mailinglist_<?php echo esc_html( $list_id); ?>_expiration" style="display:<?php echo (!empty($Subscriber -> data -> mailinglists) && in_array($list_id, $Subscriber -> data -> mailinglists)) ? 'block' : 'none'; ?>;">
											<?php esc_html_e('Expires: ', 'wp-mailinglist'); ?>
											<input type="text" name="Subscriber[listexpirations][<?php echo esc_html( $list_id); ?>]" value="<?php echo esc_attr(wp_unslash($Mailinglist -> gen_expiration_date($Subscriber -> data -> id, $list_id))); ?>" id="" />
											<?php echo ( $Html -> help(__('Choose the date on which this paid subscription should expire. Leave empty to keep inactive.', 'wp-mailinglist'))); ?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<p class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></p>
						<?php endif; ?>
						<p><a href="#" class="button" onclick="jQuery.colorbox({title:'<?php echo esc_attr(wp_unslash(__('Add a Mailing List', 'wp-mailinglist'))); ?>', href:newsletters_ajaxurl + 'action=newsletters_mailinglist_save&security=<?php echo esc_html( wp_create_nonce('mailinglist_save')); ?>&fielddiv=newsletters-mailinglists-checkboxes&fieldname=Subscriber[mailinglists]'}); return false;"><i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Mailing List', 'wp-mailinglist'); ?></a></p>
						<?php echo esc_html( $Html -> field_error('Subscriber[mailinglists]')); ?>
						<span class="howto"><?php esc_html_e('All ticked/checked subscriptions are activated immediately.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="preventautoresponders"><?php esc_html_e('Prevent Autoresponders?', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('Tick this box to prevent the automatic creation of autoresponder emails as you save this subscriber.', 'wp-mailinglist'))); ?></th>
					<td>
						<label><input type="checkbox" name="Subscriber[preventautoresponders]" value="1" id="preventautoresponders" /> <?php esc_html_e('Yes, prevent creation of autoresponders', 'wp-mailinglist'); ?></label>
					</td>
				</tr>
				<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>										
				<tr>
					<th><?php esc_html_e('Register as WordPress user?', 'wp-mailinglist'); ?>
					<?php echo ( $Html -> help(__('Would you like to register this subscriber as a WordPress user? The subscribers are separate from WordPress users at all times and is not the same list of emails. In this case you can add this subscriber as a user in WordPress.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php $registered = array('Y' => __('Yes', 'wp-mailinglist'), 'N' => __('No', 'wp-mailinglist')); ?>
						<?php echo ( $Form -> radio('Subscriber[registered]', $registered, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#registereddiv').show(); } else { jQuery('#registereddiv').hide(); }"))); ?>
					</td>
				</tr>	
				<?php endif; ?>			
			</tbody>
		</table>
		
		<?php if (apply_filters($this -> pre . '_admin_subscriber_save_register', true)) : ?>
		<div id="registereddiv" style="display:<?php echo ($Html -> field_value('Subscriber[registered]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php esc_html_e('WordPress Username', 'wp-mailinglist'); ?></th>
						<td><?php echo ( $Form -> text('Subscriber[username]', array('placeholder' => __('Enter username here', 'wp-mailinglist')))); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php esc_html_e('Email Format', 'wp-mailinglist'); ?>
					<?php echo ( $Html -> help(__('The preferred email format that this subscriber wants to receive. Available formats are HTML and PLAIN TEXT. If you are going to send multi-mime emails, this setting is ineffective and the email/webmail client of the subscriber will automatically decide.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php $formats = array('html' => __('Html', 'wp-mailinglist'), 'text' => __('Text', 'wp-mailinglist')); ?>
						<?php echo ( $Form -> radio('Subscriber[format]', $formats, array('default' => "html", 'separator' => false))); ?>
						
						<span class="howto"><?php echo sprintf(__('it is recommended that you use HTML format and turn on multi-part emails under %s > Configuration for compatibility.', 'wp-mailinglist'), $this -> name); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>
		
		<?php
		
		global $wpdb;
		$fieldsquery = "SELECT * FROM `" . $wpdb -> prefix . $Field -> table . "` WHERE `slug` != 'email' AND `slug` != 'list' ORDER BY `order` ASC";
		
		$query_hash = md5($fieldsquery);
		if ($ob_fields = $this -> get_cache($query_hash)) {
			$fields = $ob_fields;
		} else {
			$fields = $wpdb -> get_results($fieldsquery);
			$this -> set_cache($query_hash, $fields);
		}
		
		?>
		
        <?php if (!empty($fields)) : ?>
			<br/>
			<h3><?php esc_html_e('Custom Fields', 'wp-mailinglist'); ?> (<?php echo ( $Html -> link(__('show/hide', 'wp-mailinglist'), '#void', array('onclick' => "jQuery('#customfieldsdiv').toggle();"))); ?>))
			<?php echo ( $Html -> help(__('Click "show/hide" to display the available custom fields and fill in values for the custom fields for this subscriber.', 'wp-mailinglist'))); ?></h3>
			<div id="customfieldsdiv" style="display:block;">
				<table class="form-table">
					<tbody>
                    	<?php $optinid = rand(1, 999); ?>
						<?php foreach ($fields as $field) : ?>
							<tr>
								<th><label for="<?php echo esc_html( $field -> slug); ?>"><?php echo esc_html($field -> title); ?></label></th>
								<td><?php $this -> render_field($field -> id, true, $optinid); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Subscriber_mandatory_N"><?php esc_html_e('Mandatory?', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('A mandatory subscriber is a subscriber that must be subscribed and stay subscribed and cannot unsubscribe. This could be a client or a site administrator that must always be subscribed.', 'wp-mailinglist'))); ?></th>
					<td>
						<label><input <?php echo (!empty($mandatory) && $mandatory == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Subscriber[mandatory]" value="Y" id="Subscriber_mandatory_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
						<label><input <?php echo (empty($mandatory) || (!empty($mandatory) && $mandatory == "N")) ? 'checked="checked"' : ''; ?> type="radio" name="Subscriber[mandatory]" value="N" id="Subscriber_mandatory_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
						<span class="howto"><?php esc_html_e('A mandatory subscriber cannot unsubscribe', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<?php echo ( $Form -> submit(__('Save Subscriber', 'wp-mailinglist'))); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing', 'wp-mailinglist'); ?></label>
			</div>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	newsletters_focus('#Subscriber\\.email');
});
</script>
