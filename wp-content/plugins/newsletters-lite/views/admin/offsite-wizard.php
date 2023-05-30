<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Offsite Wizard', 'wp-mailinglist'); ?></h2>
	<form action="?page=<?php echo esc_html( $this -> sections -> lists); ?>&amp;method=offsitewizard#code" method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="formtype_popup"><?php esc_html_e('Form Type', 'wp-mailinglist'); ?></label></th>
					<td>
						<label><input onclick="jQuery('#formtype_popup_div').show(); jQuery('#formtype_html_div').hide();" <?php echo (empty($_POST['formtype']) || $_POST['formtype'] == "popup") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="popup" id="formtype_popup" /> <?php esc_html_e('Popup', 'wp-mailinglist'); ?></label>
						<label><input onclick="jQuery('#formtype_popup_div').hide(); jQuery('#formtype_html_div').hide();" <?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "iframe") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="iframe" id="formtype_iframe" /> <?php esc_html_e('iFrame', 'wp-mailinglist'); ?></label>
						<label><input onclick="jQuery('#formtype_popup_div').hide(); jQuery('#formtype_html_div').show();" <?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "html") ? 'checked="checked"' : ''; ?> type="radio" name="formtype" value="html" id="formtype_html" /> <?php esc_html_e('HTML', 'wp-mailinglist'); ?></label>
						<span class="howto"><?php esc_html_e('Should this offsite form open as a popup upon submission or just use an iFrame to load in itself?', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<input type="hidden" name="subscribe" value="list" />
		
		<!-- Subscribe Form -->
		<div id="subscribe_form_div" style="display:<?php echo (!empty($_POST['subscribe']) && $_POST['subscribe'] == "form") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for=""><?php esc_html_e('Subscribe Form', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php if ($forms = $this -> Subscribeform() -> select()) : ?>
								<select name="form" class="widefat" style="width:auto;">
									<?php foreach ($forms as $form_id => $form_title) : ?>
										<option value="<?php echo esc_attr($form_id); ?>"><?php echo esc_html($form_title); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<p class="newsletters_error"><?php esc_html_e('No subscribe forms are available', 'wp-mailinglist'); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<!-- Mailing List -->
		<div id="subscribe_list_div" style="display:<?php echo (empty($_POST['subscribe']) || (!empty($_POST['subscribe']) && $_POST['subscribe'] == "list")) ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="<?php echo esc_html($this -> pre); ?>list"><?php esc_html_e('Mailing List', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php $lists = $Mailinglist -> select($privatelists = true); ?>
							<select class="widefat" style="width:auto;" id="<?php echo esc_html($this -> pre); ?>list" name="list">
								<option value="">- <?php esc_html_e('Select List', 'wp-mailinglist'); ?> -</option>
								<?php if (!empty($lists) && is_array($lists)) : ?>
									<?php foreach ($lists as $id => $title) : ?>
										<option <?php echo (!empty($listid) && $listid == $id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($id); ?>"><?php echo esc_html( $title); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<span class="howto"><?php esc_html_e('Choose the mailing list to subscribe users to.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div id="formtype_html_div" style="display:<?php echo (!empty($_POST['formtype']) && $_POST['formtype'] == "html") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="html_fields_Y"><?php esc_html_e('Show Custom Fields', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['html_fields']) || (!empty($_POST['html_fields']) && $_POST['html_fields'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="html_fields" value="Y" id="html_fields_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (!empty($_POST['html_fields']) && $_POST['html_fields'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="html_fields" value="N" id="html_fields_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Should custom fields be generated in this HTML code?', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div id="formtype_popup_div" style="display:<?php echo (empty($_POST['formtype']) || $_POST['formtype'] == "popup") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="fieldsY"><?php esc_html_e('Show Custom Fields', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['fields']) || (!empty($_POST['fields']) && $_POST['fields'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="fields" value="Y" id="fieldsY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (!empty($_POST['fields']) && $_POST['fields'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="fields" value="N" id="fieldsN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Should custom fields be generated in this HTML code?', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo esc_html($this -> pre); ?>title"><?php esc_html_e('PopUp Window Title', 'wp-mailinglist'); ?></label></th>
						<td>
							<input type="text" class="widefat" style="width:auto;" id="<?php echo esc_html($this -> pre); ?>title" name="title" value="<?php echo esc_attr($this -> get_option('offsitetitle')); ?>" />
							<span class="howto"><?php esc_html_e('Title for the popup window in the browser.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo esc_html($this -> pre); ?>width"><?php esc_html_e('PopUp Dimensions', 'wp-mailinglist'); ?></label></th>
						<td>
							<input type="text" id="<?php echo esc_html($this -> pre); ?>width" name="width" value="<?php echo esc_attr($this -> get_option('offsitewidth')); ?>" class="widefat" style="width:45px;" />
							<?php esc_html_e('by', 'wp-mailinglist'); ?>
							<input type="text" id="<?php echo esc_html($this -> pre); ?>height" name="height" value="<?php echo esc_attr($this -> get_option('offsiteheight')); ?>" class="widefat" style="width:45px;" />
							<?php esc_html_e('pixels', 'wp-mailinglist'); ?>
							<span class="howto"><?php esc_html_e('Width and height of the popup window.', 'wp-mailinglist'); ?>
						</td>
					</tr>
					<tr>
						<th><label for="<?php echo esc_html($this -> pre); ?>button"><?php esc_html_e('Button Text', 'wp-mailinglist'); ?></label></th>
						<td>
							<input class="widefat" style="width:auto;" type="text" name="button" value="<?php echo esc_attr($this -> get_option('offsitebutton')); ?>" id="<?php echo esc_html($this -> pre); ?>button" style="width:97%;" />
							<span class="howto"><?php esc_html_e('Name/caption to display on the submit button.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="stylesheetY"><?php esc_html_e('Include Stylesheet', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo (empty($_POST['stylesheet']) || (!empty($_POST['stylesheet']) && $_POST['stylesheet'] == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="stylesheet" value="Y" id="stylesheetY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (!empty($_POST['stylesheet']) && $_POST['stylesheet'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="stylesheet" value="N" id="stylesheetN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Include a stylesheet with styles for the subscribe form?', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<p class="submit">
			<button value="1" type="submit" name="generate" class="button-primary">
				<?php esc_html_e('Generate Offsite Code', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	
	<?php if (!empty($offsiteurl)) : ?>
		<h3><?php esc_html_e('Offsite URL', 'wp-mailinglist'); ?></h3>
		<p class="howto"><?php esc_html_e('Direct URL for accessing this offsite form.', 'wp-mailinglist'); ?></p>
		<p><code><?php echo esc_html( $offsiteurl); ?></code></p>
	<?php endif; ?>
	
	<?php if (!empty($code)) : ?>
		<label>
			<h3 id="code"><label for="<?php echo esc_html($this -> pre); ?>code"><?php esc_html_e('Offsite Code', 'wp-mailinglist'); ?></label></h3>
			<p class="howto"><?php esc_html_e('HTML and Javascript code to accept subscriptions on external websites into this one.', 'wp-mailinglist'); ?></p>
			<textarea wrap="off" name="code" rows="15" cols="100%" class="widefat" id="<?php echo esc_html($this -> pre); ?>code" onclick="this.select();"><?php echo htmlentities(trim($code), false, get_bloginfo('charset')); ?></textarea>
			<span class="howto"><?php esc_html_e('Copy and paste the code into any webpage.', 'wp-mailinglist'); ?></span>
		</label>
	<?php endif; ?>
</div>