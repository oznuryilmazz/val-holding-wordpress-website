<?php // phpcs:ignoreFile ?>
<!-- Subscription Behaviour -->

<?php
	
$emailvalidationextended = $this -> get_option('emailvalidationextended');
$saveipaddress = $this -> get_option('saveipaddress');
$currentusersubscribed = $this -> get_option('currentusersubscribed');	
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="emailvalidationextended"><?php esc_html_e('Extended Email Validation', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input type="checkbox" <?php checked($emailvalidationextended, 1); ?> name="emailvalidationextended" value="1" id="emailvalidationextended" /> <?php esc_html_e('Yes, check email DNS records and make a test connection to it.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Turning this on will test to see if an email address has working DNS (MX) records and also make a test SMTP connection to the email address to see if it can accept messages.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="subscriberedirect_N"><?php esc_html_e('Redirect On Success Subscribe', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(__('This redirect takes effect on the actual subscribe form when a user subscribes. You can turn this setting on to redirect a subscriber to a specific place upon successful subscribe.', 'wp-mailinglist'))); ?></th>
			<td>
				<label><input onclick="jQuery('#subscriberedirecturl_div').show();" <?php echo ($this -> get_option('subscriberedirect') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberedirect" value="Y" id="subscriberedirect_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
				<label><input onclick="jQuery('#subscriberedirecturl_div').hide();" <?php echo ($this -> get_option('subscriberedirect') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberedirect" value="N" id="subscriberedirect_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Should a subscriber be redirected after successfully subscribing?', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div class="newsletters_indented" id="subscriberedirecturl_div" style="display:<?php echo ($this -> get_option('subscriberedirect') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="subscriberedirecturl"><?php esc_html_e('Redirect URL', 'wp-mailinglist'); ?></label></th>
				<td>
					<input type="text" name="subscriberedirecturl" id="subscriberedirecturl" class="widefat" value="<?php echo esc_attr(wp_unslash($this -> get_option('subscriberedirecturl'))); ?>" />
					<span class="howto"><?php esc_html_e('Absolute URL to redirect to after successfully subscribing.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
		
<table class="form-table">
	<tbody>
		<tr class="advanced-setting">
			<th><label for="saveipaddress"><?php esc_html_e('Save IP Address', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php checked($saveipaddress, 1, true); ?> type="checkbox" name="saveipaddress" value="1" id="saveipaddress" /> <?php esc_html_e('Yes, save the IP address of subscribers on subscribe.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('For privacy or regulation reasons you can turn this off if needed.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_html($this -> pre); ?>generalredirect"><?php esc_html_e('General Redirect URL', 'wp-mailinglist'); ?></label></th>
			<td>
				<input type="text" class="widefat" id="<?php echo esc_html($this -> pre); ?>generalredirect" name="generalredirect" value="<?php echo esc_attr(wp_unslash($this -> get_option('generalredirect'))); ?>" />
				<span class="howto"><?php esc_html_e('Redirect upon unsubscription, activation, etc.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="currentusersubscribed"><?php esc_html_e('Notification to Subscribed Users', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($currentusersubscribed)) ? 'checked="checked"' : ''; ?> type="checkbox" name="currentusersubscribed" value="1" id="currentusersubscribed" /> <?php esc_html_e('Yes, show users if they are already subscribed', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Shows a notice above the subscription form if a logged in user is already subscribed.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
        <tr>
        	<th><label for="subscriberexistsredirect_management"><?php esc_html_e('Subscriber Exists Redirect', 'wp-mailinglist'); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').hide();" <?php echo ($this -> get_option('subscriberexistsredirect') == "management") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="management" id="subscriberexistsredirect_management" /> <?php esc_html_e('Management Section', 'wp-mailinglist'); ?></label>
                <label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').show();" <?php echo ($this -> get_option('subscriberexistsredirect') == "custom") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="custom" id="subscriberexistsredirect_custom" /> <?php esc_html_e('Custom URL', 'wp-mailinglist'); ?></label>
                <label><input onclick="jQuery('#subscriberexistsredirectcustomdiv').hide();" <?php echo ($this -> get_option('subscriberexistsredirect') == "nothing") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberexistsredirect" value="nothing" id="subscriberexistsredirect_nothing" /> <?php esc_html_e('Do Nothing', 'wp-mailinglist'); ?></label>
            	<span class="howto"><?php esc_html_e('What to do when a user subscribes with an existing email address?', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div class="newsletters_indented" id="subscriberexistsredirectcustomdiv" style="display:<?php echo ($this -> get_option('subscriberexistsredirect') == "custom") ? 'block' : 'none'; ?>;">
	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="subscriberexistsredirecturl"><?php esc_html_e('Custom Redirect URL', 'wp-mailinglist'); ?></label></th>
                <td>
                	<input type="text" class="widefat" name="subscriberexistsredirecturl" value="<?php echo esc_attr(wp_unslash($this -> get_option('subscriberexistsredirecturl'))); ?>" id="subscriberexistsredirecturl" />
                	<span class="howto"><?php esc_html_e('URL/Link to redirect an existing subscriber to.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
	<tbody>
    	<tr>
        	<th><label for="subscriberexistsmessage"><?php esc_html_e('Subscriber Exists Message', 'wp-mailinglist'); ?></label></th>
            <td>
            	<?php if ($this -> language_do()) : ?>
            		<?php 
					
					$languages = $this -> language_getlanguages(); 
					$subscriberexistsmessage = $this -> get_option('subscriberexistsmessage');
					
					?>
					<div id="subscriberexistsmessagetabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($languages as $language) : ?>
			                 	<li><a href="#subscriberexistsmessagetab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($languages as $language) : ?>
			            	<div id="subscriberexistsmessagetab<?php echo esc_html($tabnumber); ?>">
			            		<input type="text" name="subscriberexistsmessage[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $subscriberexistsmessage))); ?>" id="subscriberexistsmessage_<?php echo esc_html( $language); ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#subscriberexistsmessagetabs').tabs();
						}
					});
					</script>
            	<?php else : ?>
            		<input type="text" class="widefat" name="subscriberexistsmessage" value="<?php echo esc_attr(wp_unslash($this -> get_option('subscriberexistsmessage'))); ?>" id="subscriberexistsmessage" />
            	<?php endif; ?>
            	<span class="howto"><?php esc_html_e('Message to show to a user when they already exist for the specified list(s).', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
	</tbody>
</table>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="<?php echo esc_html( $this -> pre); ?>onlinelinktext"><?php esc_html_e('Online Newsletter Link Text', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$languages = $this -> language_getlanguages(); 
					$onlinelinktext = $this -> get_option('onlinelinktext');
					
					?>
					<div id="onlinelinktexttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($languages as $language) : ?>
			                 	<li><a href="#onlinelinktexttab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($languages as $language) : ?>
			            	<div id="onlinelinktexttab<?php echo esc_html($tabnumber); ?>">
			            		<input type="text" name="onlinelinktext[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $onlinelinktext))); ?>" id="onlinelinktext_<?php echo esc_html( $language); ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#onlinelinktexttabs').tabs();
						}
					});
					</script>
				<?php else : ?>
					<input class="widefat" type="text" id="<?php echo esc_html( $this -> pre); ?>onlinelinktext" name="onlinelinktext" value="<?php echo esc_attr(wp_unslash($this -> get_option('onlinelinktext'))); ?>" />
				<?php endif; ?>
				<span class="howto"><?php esc_html_e('Displays email in browser. generated by <code>[newsletters_online]</code> in content', 'wp-mailinglist'); ?></span>
			</td>
		</tr>	
		<tr>
			<th><label for="printlinktext"><?php esc_html_e('Print Link Text', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php 
					
					$languages = $this -> language_getlanguages(); 
					$printlinktext = $this -> get_option('printlinktext');
					
					?>
					<div id="printlinktexttabs">
						<ul>
							<?php $tabnumber = 1; ?>
			                <?php foreach ($languages as $language) : ?>
			                 	<li><a href="#printlinktexttab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
			                    <?php $tabnumber++; ?>
			                <?php endforeach; ?>
			            </ul>
			            
			            <?php $tabnumber = 1; ?>
			            <?php foreach ($languages as $language) : ?>
			            	<div id="printlinktexttab<?php echo esc_html($tabnumber); ?>">
			            		<input type="text" name="printlinktext[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $printlinktext))); ?>" id="printlinktext_<?php echo esc_html( $language); ?>" class="widefat" />
			            	</div>
			            	<?php $tabnumber++; ?>
			            <?php endforeach; ?>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {
						if (jQuery.isFunction(jQuery.fn.tabs)) {
							jQuery('#printlinktexttabs').tabs();
						}
					});
					</script>
				<?php else : ?>
					<input class="widefat" type="text" id="<?php echo esc_html( $this -> pre); ?>printlinktext" name="printlinktext" value="<?php echo esc_attr(wp_unslash($this -> get_option('printlinktext'))); ?>" />
				<?php endif; ?>
				<span class="howto"><?php esc_html_e('Displays printable version of newsletter in browser. Output this with <code>[newsletters_print]</code> shortcode.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Admin Notification on Subscription', 'wp-mailinglist'); ?></th>
			<td>
				<?php $adminemailonsubscription = $this -> get_option('adminemailonsubscription'); ?>
				<label><input <?php echo $check1 = ($adminemailonsubscription == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonsubscription" value="Y" /> <?php esc_html_e('Yes'); ?></label>
				<label><input <?php echo $check2 = ($adminemailonsubscription == "N") ? 'checked="checked"' : ''; ?> type="radio" name="adminemailonsubscription" value="N" /> <?php esc_html_e('No'); ?></label>
			</td>
		</tr>
		<?php $requireactivate = $this -> get_option('requireactivate'); ?>
		<tr>
			<th><?php esc_html_e('Require Confirmation?', 'wp-mailinglist'); ?></th>
			<td>
				<label><input onclick="jQuery('#requireactivatediv').show();" <?php echo $check1 = ($requireactivate == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="requireactivate" value="Y" /> <?php esc_html_e('Yes, confirm email address', 'wp-mailinglist'); ?></label>
				<label><input onclick="jQuery('#requireactivatediv').hide();" <?php echo $check2 = ($requireactivate == "N") ? 'checked="checked"' : ''; ?> type="radio" name="requireactivate" value="N" /> <?php esc_html_e('No, immediately activate', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Is double opt-in action required by subscribers to activate subscriptions?', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<div class="newsletters_indented" id="requireactivatediv" style="display:<?php echo $requireactivatedisplay = ($requireactivate == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr class="advanced-setting">
				<th><label for="activationemails_single"><?php esc_html_e('Confirmation Emails', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input <?php echo ($this -> get_option('activationemails') == "single") ? 'checked="checked"' : ''; ?> type="radio" name="activationemails" value="single" id="activationemails_single" /> <?php esc_html_e('Single Email', 'wp-mailinglist'); ?></label>
					<label><input <?php echo ($this -> get_option('activationemails') == "multiple") ? 'checked="checked"' : ''; ?> type="radio" name="activationemails" value="multiple" id="activationemails_multiple" /> <?php esc_html_e('Multiple Emails (One for each list)', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('Should a single email or multiple emails (one for each list) be sent for confirmation when subscribing to multiple lists.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo esc_html( $this -> pre); ?>activationlinktext"><?php esc_html_e('Activation Link Text', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<?php 
					
						$languages = $this -> language_getlanguages(); 
						$activationlinktext = $this -> get_option('activationlinktext');
						
						?>
						<div id="activationlinktexttabs">
							<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#activationlinktexttab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php foreach ($languages as $language) : ?>
				            	<div id="activationlinktexttab<?php echo esc_html($tabnumber); ?>">
				            		<input type="text" name="activationlinktext[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $activationlinktext))); ?>" id="activationlinktext_<?php echo esc_html( $language); ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#activationlinktexttabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<input class="widefat" type="text" id="<?php echo esc_html( $this -> pre); ?>activationlinktext" name="activationlinktext" value="<?php echo esc_attr(wp_unslash($this -> get_option('activationlinktext'))); ?>" />
					<?php endif; ?>
					<span class="howto"><?php esc_html_e('Displays an activation link generated by <code>[newsletters_activate]</code> in content', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="customactivateredirect_N"><?php esc_html_e('Confirm Redirect', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input onclick="jQuery('#customactivateredirect_div').show();" <?php echo ($this -> get_option('customactivateredirect') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="customactivateredirect" value="Y" id="customactivateredirect_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
					<label><input onclick="jQuery('#customactivateredirect_div').hide();" <?php echo ($this -> get_option('customactivateredirect') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="customactivateredirect" value="N" id="customactivateredirect_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('Defaults to the subscriber management section. This URL can be configured per mailing list as well.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="newsletters_indented" id="customactivateredirect_div" style="display:<?php echo ($this -> get_option('customactivateredirect') == "Y") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="activateredirecturl"><?php esc_html_e('Confirm Redirect URL', 'wp-mailinglist'); ?></label></th>
					<td>
						<input type="text" class="widefat" name="activateredirecturl" value="<?php echo esc_attr(wp_unslash($this -> get_option('activateredirecturl'))); ?>" id="activateredirecturl" />
						<span class="howto"><?php esc_html_e('Link/URL to which subscribers will be redirected upon activation.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<table class="form-table">
		<tbody>
			<tr class="advanced-setting">
				<th><label for="activateaction_none"><?php esc_html_e('Inactive Subscriptions', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php $activateaction = $this -> get_option('activateaction'); ?>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide();" <?php echo (empty($activateaction) || $activateaction == "none") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="none" id="activateaction_none" /> <?php esc_html_e('Do Nothing', 'wp-mailinglist'); ?></label>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide(); jQuery('#activateaction_' + this.value + '_div').show();" <?php echo (!empty($activateaction) && $activateaction == "remind") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="remind" id="activateaction_remind" /> <?php esc_html_e('Send Reminder', 'wp-mailinglist'); ?></label>
					<label><input onclick="jQuery('div[id*=\'activateaction\']').hide(); jQuery('#activateaction_' + this.value + '_div').show();" <?php echo (!empty($activateaction) && $activateaction == "delete") ? 'checked="checked"' : ''; ?> type="radio" name="activateaction" value="delete" id="activateaction_delete" /> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('How should inactive subscriptions be handled?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<!-- Activate delete settings -->
	<div class="newsletters_indented" class="advanced-setting" id="activateaction_delete_div" style="display:<?php echo (!empty($activateaction) && $activateaction == "delete") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for=""><?php esc_html_e('Delete Delay', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php $activatedelete = $this -> get_option('activatedelete'); ?>
						<?php echo sprintf(__('Delete inactive subscriptions %s days after subscribing.', 'wp-mailinglist'), '<input type="text" class="widefat" style="width:45px;" name="activatedelete" value="' . esc_attr(wp_unslash($activatedelete)) . '" id="activatedelete" />'); ?>
						<span class="howto"><?php esc_html_e('After how many days should an inactive subscription to a list be deleted?', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<!-- Activate reminder settings -->
	<div class="newsletters_indented" class="advanced-setting" id="activateaction_remind_div" style="display:<?php echo (!empty($activateaction) && $activateaction == "remind") ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="activatereminder"><?php esc_html_e('Confirmation Reminder', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php $activatereminder = $this -> get_option('activatereminder'); ?>
						<?php echo sprintf(__('Send an activate reminder to inactive subscriptions %s days after subscribing', 'wp-mailinglist'), '<input type="text" class="widefat" style="width:45px;" name="activatereminder" value="' . esc_attr(wp_unslash($activatereminder)) . '" id="activatereminder" />'); ?>
						<span class="howto"><?php esc_html_e('Send a confirmation reminder to a subscriber X days after subscribing.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
