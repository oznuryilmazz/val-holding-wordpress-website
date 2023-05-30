<?php // phpcs:ignoreFile ?>
<!-- Mailing List Form Field -->

<?php

if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
	
	$settings = $this -> language_split($form_field -> settings);
	if (!empty($settings) && is_array($settings)) {
		foreach ($settings as $language => $sett) {
			$settings[$language] = maybe_unserialize($sett);
		}
	}
} else {
	$settings = maybe_unserialize($form_field -> settings);	
}
	
?>

<input type="hidden" name="form_fields[<?php echo esc_html( $field -> id); ?>][id]" value="<?php echo esc_attr(wp_unslash($form_field -> id)); ?>" />
<input type="hidden" name="form_fields[<?php echo esc_html( $field -> id); ?>][required]" value="1" />

<div class="misc-pub-section">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_label"><?php esc_html_e('Label', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<div id="<?php echo esc_html( $field -> slug); ?>-label-tabs">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li><a href="#<?php echo esc_html( $field -> slug); ?>-label-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="<?php echo esc_html( $field -> slug); ?>-label-tabs-<?php echo esc_html( $language); ?>">
									<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][label][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form_field -> label))); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_label_<?php echo esc_html( $language); ?>" placeholder="<?php echo esc_attr(wp_unslash($this -> language_use($language, $field -> title))); ?>" />
								</div>
							<?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#<?php echo esc_html( $field -> slug); ?>-label-tabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][label]" id="form_fields_<?php echo esc_html( $field -> id); ?>_label" value="<?php echo esc_attr(wp_unslash((empty($form_field -> label) ? esc_html($field -> title) : esc_html($form_field -> label)))); ?>" />
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_caption"><?php esc_html_e('Caption', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<div id="<?php echo esc_html( $field -> slug); ?>-caption-tabs">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li><a href="#<?php echo esc_html( $field -> slug); ?>-caption-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="<?php echo esc_html( $field -> slug); ?>-caption-tabs-<?php echo esc_html( $language); ?>">
									<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][caption][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form_field -> caption))); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_caption_<?php echo esc_html( $language); ?>" placeholder="<?php echo esc_attr(wp_unslash($this -> language_use($language, $field -> caption))); ?>" />
								</div>
							<?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#<?php echo esc_html( $field -> slug); ?>-caption-tabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][caption]" id="form_fields_<?php echo esc_html( $field -> id); ?>_caption" value="<?php echo esc_attr(wp_unslash((empty($form_field -> caption) ? esc_html($field -> caption) : esc_html($form_field -> caption)))); ?>" />
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_errormessage"><?php esc_html_e('Error Message', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<div id="<?php echo esc_html( $field -> slug); ?>-errormessage-tabs">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li><a href="#<?php echo esc_html( $field -> slug); ?>-errormessage-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="<?php echo esc_html( $field -> slug); ?>-errormessage-tabs-<?php echo esc_html( $language); ?>">
									<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][errormessage][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form_field -> errormessage))); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_errormessage_<?php echo esc_html( $language); ?>" placeholder="<?php echo esc_attr(wp_unslash($this -> language_use($language, $field -> errormessage))); ?>" />
								</div>
							<?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#<?php echo esc_html( $field -> slug); ?>-errormessage-tabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<input type="text" class="widefat" name="form_fields[<?php echo esc_html( $field -> id); ?>][errormessage]" id="form_fields_<?php echo esc_html( $field -> id); ?>_errormessage" value="<?php echo esc_attr(wp_unslash($form_field -> errormessage)); ?>" placeholder="<?php echo esc_attr(wp_unslash(esc_html($field -> errormessage))); ?>" />
					<?php endif; ?>
					<span class="howto"><?php esc_html_e('Error message to display. Leave empty to use default.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin"><?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></label></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<div id="<?php echo esc_html( $field -> slug); ?>-listchoice-tabs">
							<ul>
								<?php foreach ($languages as $language) : ?>
									<li><a href="#<?php echo esc_html( $field -> slug); ?>-listchoice-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php foreach ($languages as $language) : ?>
								<div id="<?php echo esc_html( $field -> slug); ?>-listchoice-tabs-<?php echo esc_html( $language); ?>">
									<label><input onclick="jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div_<?php echo esc_html( $language); ?>').hide(); jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div_<?php echo esc_html( $language); ?>').show();" <?php echo (!empty($settings[$language]['listchoice']) && $settings[$language]['listchoice'] == "admin") ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][listchoice]" value="admin" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_<?php echo esc_html( $language); ?>_admin" /> <?php esc_html_e('Admin Choice', 'wp-mailinglist'); ?></label>
									<label><input onclick="jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div_<?php echo esc_html( $language); ?>').hide(); jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div_<?php echo esc_html( $language); ?>').show();" <?php echo (empty($settings[$language]['listchoice']) || (!empty($settings[$language]['listchoice']) && $settings[$language]['listchoice'] == "user")) ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][listchoice]" value="user" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_<?php echo esc_html( $language); ?>_user" /> <?php esc_html_e('User Choice', 'wp-mailinglist'); ?></label>
									
									<br/><br/>
									
									<!-- Admin Choice -->
									<div id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div_<?php echo esc_html( $language); ?>" style="display:<?php echo (!empty($settings[$language]['listchoice']) && $settings[$language]['listchoice'] == "admin") ? 'block' : 'none'; ?>;">
										<?php if ($lists = $Mailinglist -> select(true)) : ?>
											<label style="font-weight:bold;"><input type="checkbox" name="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_checkall_<?php echo esc_html( $language); ?>" onclick="jqCheckAll(this, false, 'form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][adminlists]');" value="1" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_checkall_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label>
											<div class="scroll-list">
												<?php foreach ($lists as $list_id => $list_title) : ?>
													<label><input <?php echo (!empty($settings[$language]['adminlists']) && in_array($list_id, $settings[$language]['adminlists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][adminlists][]" value="<?php echo esc_html( $list_id); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_<?php echo esc_html( $list_id); ?>_<?php echo esc_html( $language); ?>" /> <?php echo esc_html( $this -> language_use($language, $list_title)); ?></label><br/>
												<?php endforeach; ?>
											</div>
										<?php else : ?>
											<span class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></span>
										<?php endif; ?>
									</div>
									
									<!-- User Choice -->
									<div id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div_<?php echo esc_html( $language); ?>" style="display:<?php echo (empty($settings[$language]['listchoice']) || (!empty($settings[$language]['listchoice']) && $settings[$language]['listchoice'] == "user")) ? 'block' : 'none'; ?>;">
										<?php if ($lists = $Mailinglist -> select(true)) : ?>
											<label style="font-weight:bold;"><input onclick="jqCheckAll(this, false, 'form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][includelists]');" type="checkbox" name="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_checkall_<?php echo esc_html( $language); ?>" value="1" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_checkall_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label><br/>
											<div class="scroll-list">
												<?php foreach ($lists as $list_id => $list_title) : ?>
													<label><input <?php echo (!empty($settings[$language]['includelists']) && in_array($list_id, $settings[$language]['includelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][<?php echo esc_html( $language); ?>][includelists][]" value="<?php echo esc_html( $list_id); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_<?php echo esc_html( $list_id); ?>_<?php echo esc_html( $language); ?>" /> <?php echo esc_html( $this -> language_use($language, $list_title)); ?></label>
													<?php /*<small>(<label><input type="checkbox" /> <?php esc_html_e('Precheck this list', 'wp-mailinglist'); ?></label>)</small>*/ ?><br/>
												<?php endforeach; ?>
											</div>
										<?php else : ?>
											<span class="newsletters_error"><?php esc_html_e('No mailing lists are available.', 'wp-mailinglist'); ?></span>
										<?php endif; ?>
										<span class="howto"><?php esc_html_e('Choose which lists should be included in the available selection. Leave empty for all.', 'wp-mailinglist'); ?></span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						
						<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery.isFunction(jQuery.fn.tabs)) {
								jQuery('#<?php echo esc_html( $field -> slug); ?>-listchoice-tabs').tabs();
							}
						});
						</script>
					<?php else : ?>
						<label><input onclick="jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div').hide(); jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div').show();" <?php echo (!empty($settings['listchoice']) && $settings['listchoice'] == "admin") ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][listchoice]" value="admin" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin" /> <?php esc_html_e('Admin Choice', 'wp-mailinglist'); ?></label>
						<label><input onclick="jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div').hide(); jQuery('#form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div').show();" <?php echo (empty($settings['listchoice']) || (!empty($settings['listchoice']) && $settings['listchoice'] == "user")) ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][listchoice]" value="user" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user" /> <?php esc_html_e('User Choice', 'wp-mailinglist'); ?></label>
					<?php endif; ?>
					<span class="howto"><?php esc_html_e('Do you want to specify list(s) to subscribe users to or should they choose their list(s)?', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php if (!$this -> language_do()) : ?>
		<div id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_admin_div" style="display:<?php echo (!empty($settings['listchoice']) && $settings['listchoice'] == "admin") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_checkall"><?php esc_html_e('Choose List(s)', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php if ($lists = $Mailinglist -> select(true)) : ?>
								<label style="font-weight:bold;"><input type="checkbox" name="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_checkall" onclick="jqCheckAll(this, false, 'form_fields[<?php echo esc_html( $field -> id); ?>][settings][adminlists]');" value="1" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_checkall" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label>
								<div class="scroll-list">
									<?php foreach ($lists as $list_id => $list_title) : ?>
										<label><input <?php echo (!empty($settings['adminlists']) && in_array($list_id, $settings['adminlists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][adminlists][]" value="<?php echo esc_html( $list_id); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_adminlists_<?php echo esc_html( $list_id); ?>" /> <?php echo esc_attr($list_title); ?></label><br/>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<span class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_div" style="display:<?php echo (empty($settings['listchoice']) || (!empty($settings['listchoice']) && $settings['listchoice'] == "user")) ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_type_select"><?php esc_html_e('Type', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo (empty($settings['listchoice_user_type']) || (!empty($settings['listchoice_user_type']) && $settings['listchoice_user_type'] == "select")) ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][listchoice_user_type]" value="select" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_type_select" /> <?php esc_html_e('Single (Select)', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (!empty($settings['listchoice_user_type']) && $settings['listchoice_user_type'] == "checkboxes") ? 'checked="checked"' : ''; ?> type="radio" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][listchoice_user_type]" value="checkboxes" id="form_fields_<?php echo esc_html( $field -> id); ?>_listchoice_user_type_checkboxes" /> <?php esc_html_e('Multiple (Checkbox)', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Specify the selection type, select drop down or checkboxes list.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_checkall"><?php esc_html_e('Include Only', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php if ($lists = $Mailinglist -> select(true)) : ?>
								<label style="font-weight:bold;"><input onclick="jqCheckAll(this, false, 'form_fields[<?php echo esc_html( $field -> id); ?>][settings][includelists]');" type="checkbox" name="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_checkall" value="1" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_checkall" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label><br/>
								<div class="scroll-list">
									<?php foreach ($lists as $list_id => $list_title) : ?>
										<label><input <?php echo (!empty($settings['includelists']) && in_array($list_id, $settings['includelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="form_fields[<?php echo esc_html( $field -> id); ?>][settings][includelists][]" value="<?php echo esc_html( $list_id); ?>" id="form_fields_<?php echo esc_html( $field -> id); ?>_settings_includelists_<?php echo esc_html( $list_id); ?>" /> <?php echo esc_attr($list_title); ?></label><br/>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<span class="newsletters_error"><?php esc_html_e('No mailing lists are available.', 'wp-mailinglist'); ?></span>
							<?php endif; ?>
							<span class="howto"><?php esc_html_e('Choose which lists should be included in the available selection. Leave empty for all.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>