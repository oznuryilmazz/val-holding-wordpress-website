<?php // phpcs:ignoreFile ?>
<!-- Comments and Registration Form Settings -->
<table class="form-table">
	<tbody>
    	<tr>
        	<th><label for="commentformcheckbox_Y"><?php esc_html_e('Comment Form Checkbox', 'wp-mailinglist'); ?></label></th>
            <td>
            	<label><input onclick="jQuery('#commentformcheckbox_div').show();" <?php echo ($this -> get_option('commentformcheckbox') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="commentformcheckbox" value="Y" id="commentformcheckbox_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                <label><input onclick="jQuery('#commentformcheckbox_div').hide();" <?php echo ($this -> get_option('commentformcheckbox') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="commentformcheckbox" value="N" id="commentformcheckbox_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
            	<span class="howto"><?php esc_html_e('Turn this on (Yes) to display a checkbox on the WordPress comment form for commentors to subscribe to a mailing list.', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
    </tbody>
</table>

<div class="newsletters_indented" id="commentformcheckbox_div" style="display:<?php echo ($this -> get_option('commentformcheckbox') == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
    	<tbody>
        	<tr>
            	<th><label for="commentformlist"><?php esc_html_e('Subscribe List', 'wp-mailinglist'); ?></label></th>
                <td>
                	<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
                    	<select name="commentformlist" id="commentformlist">
                        	<?php foreach ($mailinglists as $id => $name) : ?>
                            	<option <?php echo ($this -> get_option('commentformlist') == $id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr(wp_unslash($id)); ?>"><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <span class="howto"><?php esc_html_e('Choose the list to which commentors will be subscribed when the checkbox is checked.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
        	<tr>
            	<th><label for="commentformlabel"><?php esc_html_e('Comments Checkbox Label', 'wp-mailinglist'); ?></label></th>
                <td>
                	<?php if ($this -> language_do()) : ?>
						<?php
						
						$languages = $this -> language_getlanguages();
						$commentformlabel = $this -> get_option('commentformlabel');
						
						?>
						<?php if (!empty($languages)) : ?>					
							<div id="commentformlabeltabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($languages as $language) : ?>
					                 	<li><a href="#commentformlabeltab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($languages as $language) : ?>
					            	<div id="commentformlabeltab<?php echo esc_html($tabnumber); ?>">
					            		<input class="widefat" type="text" name="commentformlabel[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $commentformlabel))); ?>" id="commentformlabel<?php echo esc_html( $language); ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#commentformlabeltabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
                		<input class="widefat" type="text" name="commentformlabel" value="<?php echo esc_attr(wp_unslash($this -> get_option('commentformlabel'))); ?>" id="commentformlabel" />
                	<?php endif; ?>
                	<span class="howto"><?php esc_html_e('Type a label/caption to display for the checkbox which your commentors will see.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
            	<th><label for="commentformautocheck_N"><?php esc_html_e('Auto Check Checkbox', 'wp-mailinglist'); ?></label></th>
                <td>
                	<label><input <?php echo ($this -> get_option('commentformautocheck') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="commentformautocheck" value="Y" id="commentformautocheck_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                    <label><input <?php echo ($this -> get_option('commentformautocheck') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="commentformautocheck" value="N" id="commentformautocheck_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                    <span class="howto"><?php esc_html_e('automatically check the checkbox on the comment form.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table class="form-table">
	<tbody>
    	<tr>
			<th><?php esc_html_e('Registration Checkbox', 'wp-mailinglist'); ?></th>
			<td>
				<?php $registercheckbox = $this -> get_option('registercheckbox'); ?>
				<label><input onclick="jQuery('#registercheckboxdiv').show();" <?php echo $check = ($registercheckbox == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="registercheckbox" value="Y" /> <?php esc_html_e('Yes'); ?></label>
				<label><input onclick="jQuery('#registercheckboxdiv').hide();" <?php echo $check = ($registercheckbox == "N") ? 'checked="checked"' : ''; ?> type="radio" name="registercheckbox" value="N" /> <?php esc_html_e('No'); ?></label>
				
				<?php $users_can_register = get_option('users_can_register'); ?>
				<?php if (empty($users_can_register) || $users_can_register == 0) : ?>
					<div class="newsletters_error"><?php esc_html_e('WordPress registration is currently deactivated', 'wp-mailinglist'); ?></div>
				<?php endif; ?>
                
                <span class="howto"><?php esc_html_e('Turn this on to place a subscribe checkbox on the registration form.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
    </tbody>
</table>

<div class="newsletters_indented" id="registercheckboxdiv" style="display:<?php echo $display = ($registercheckbox == "Y") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
        	<tr>
            	<th><label for="registerformlabel"><?php esc_html_e('Registration Checkbox Label', 'wp-mailinglist'); ?></label></th>
                <td>
                	<?php if ($this -> language_do()) : ?>
						<?php
						
						$languages = $this -> language_getlanguages();
						$registerformlabel = $this -> get_option('registerformlabel');
						
						?>
						<?php if (!empty($languages)) : ?>					
							<div id="registerformlabeltabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($languages as $language) : ?>
					                 	<li><a href="#registerformlabeltab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($languages as $language) : ?>
					            	<div id="registerformlabeltab<?php echo esc_html($tabnumber); ?>">
					            		<input class="widefat" type="text" name="registerformlabel[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $registerformlabel))); ?>" id="registerformlabel<?php echo esc_html( $language); ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#registerformlabeltabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
                		<input class="widefat" type="text" name="registerformlabel" value="<?php echo esc_attr(wp_unslash($this -> get_option('registerformlabel'))); ?>" id="registerformlabel" />
                	<?php endif; ?>
                    <span class="howto"><?php esc_html_e('Label/caption text next to the checkbox on the registration form.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
			<tr>
				<th><?php esc_html_e('Auto check to subscribe'); ?></th>
				<td>
					<?php $checkboxon = $this -> get_option('checkboxon'); ?>
					<label><input <?php echo $check = ($checkboxon == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="checkboxon" value="Y" /> <?php esc_html_e('Yes'); ?></label>
					<label><input <?php echo $check = ($checkboxon == "N") ? 'checked="checked"' : ''; ?> type="radio" name="checkboxon" value="N" /> <?php esc_html_e('No'); ?></label>
					<span class="howto"><?php esc_html_e('Should the subscribe checkbox be ticked/checked by default or not?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="<?php echo esc_html( $this -> pre); ?>autosubscribelist"><?php esc_html_e('Registration List(s)', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('New users will be subscribed to the chosen list(s) upon successful registration. The subscribe will only happen if the subscribe checkbox was ticked/checked by the user accordingly.', 'wp-mailinglist'))); ?></th>
				<td>
					<?php $autosubscribelist = $this -> get_option('autosubscribelist'); ?>
					<?php
					
					if (!empty($autosubscribelist) && is_numeric($autosubscribelist) && !is_array($autosubscribelist)) {
						$autosubscribelist = array($autosubscribelist);
					}
					
					?>
					<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
						<div class="scroll-list">
							<?php foreach ($mailinglists as $list_id => $list_title) : ?>
								<label><input <?php echo (!empty($autosubscribelist) && in_array($list_id, $autosubscribelist)) ? 'checked="checked"' : ''; ?> type="checkbox" name="autosubscribelist[]" value="<?php echo esc_html( $list_id); ?>" id="autosubscribelist_<?php echo esc_html( $list_id); ?>" /> <?php echo esc_attr($list_title); ?></label><br/>
							<?php endforeach; ?>
						</div>
						<span class="howto"><?php esc_html_e('To which list(s) should new users be subscribed upon registration?', 'wp-mailinglist'); ?></span>
					<?php else : ?>
						<span class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>