<?php // phpcs:ignoreFile ?>
<!-- Save a Mailing List -->

<?php
if (isset($mailinglist -> doubleopt)) {
$doubleopt = $mailinglist -> doubleopt;
}
if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
}

if (!isset($errors)) {
    $errors = array();
}
?>

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h2><?php esc_html_e('Save a Mailing List', 'wp-mailinglist'); ?></h2>
	<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
	<form action="?page=<?php echo esc_html( $this -> sections -> lists); ?>&method=save" method="post" id="mailinglistform">
		<?php wp_nonce_field($this -> sections -> lists . '_save'); ?>
		<?php echo ( $Form -> hidden('Mailinglist[id]')); ?>
	
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Mailinglist.title"><?php esc_html_e('List Title', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<div id="mailinglist-title-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#mailinglist-title-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="mailinglist-title-tabs-<?php echo esc_html( $language); ?>">
										<input placeholder="<?php echo esc_attr(wp_unslash(__('Enter mailing list title here', 'wp-mailinglist'))); ?>" type="text" class="widefat" name="Mailinglist[title][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $Mailinglist -> data -> title))); ?>" id="Mailinglist_title_<?php echo esc_html( $language); ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#mailinglist-title-tabs').tabs();
								}
							});
							</script>
						<?php else : ?>
							<?php echo ( $Form -> text('Mailinglist[title]', array('placeholder' => __('Enter mailing list title here', 'wp-mailinglist')))); ?>
						<?php endif; ?>
                    	<span class="howto"><?php esc_html_e('Fill in a title for your list as your users will see it.', 'wp-mailinglist'); ?></span>
                    </td>
				</tr>
				<?php if (apply_filters('newsletters_admin_mailinglists_save_privatelist_show', true)) : ?>
					<tr>
						<th><label for="privatelist"><?php esc_html_e('Private List', 'wp-mailinglist'); ?></label>
						<?php echo ( $Html -> help(__('A private list will not be shown to users/subscribers publicly, it is for internal use. You can send to subscribers in a private list and they can still unsubscribe from it though.', 'wp-mailinglist'))); ?></th>
						<td>
							<label><input <?php echo ($mailinglist -> privatelist == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[privatelist]" id="privatelist2" value="Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
							<label><input <?php echo (empty($mailinglist -> privatelist) || $mailinglist -> privatelist == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[privatelist]" id="privatelist" value="N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
							<?php echo esc_html( $Html -> field_error('Mailinglist[privatelist]')); ?>
	                        <span class="howto"><?php esc_html_e('A private list is for internal use only and will not be visible to users.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ($fields = $Field -> select()) : ?>
					<tr>
						<th><label for="checkboxall"><?php esc_html_e('Custom Fields', 'wp-mailinglist'); ?></label></th>
						<td>
							<label style="font-weight:bold;"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /> <?php esc_html_e('Select all', 'wp-mailinglist'); ?></label>
							<div class="scroll-list">
								<?php echo ( $Form -> checkbox('Mailinglist[fields][]', $fields)); ?>
							</div>
                            <span class="howto"><?php esc_html_e('Attach custom fields to this list to be displayed in the subscribe form.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				<?php endif; ?>
                <tr>
                	<th><label for="Mailinglist.group_id"><?php esc_html_e('Group', 'wp-mailinglist'); ?></label></th>
                    <td>
                    	<?php if ($groupsselect = $this -> Group() -> select()) : ?>
                        	<?php echo ( $Form -> select('Mailinglist[group_id]', $groupsselect)); ?>
                            <span class="howto"><small><?php esc_html_e('(optional)', 'wp-mailinglist'); ?></small> <?php esc_html_e('Put this mailing list into a group of lists.', 'wp-mailinglist'); ?></span>
                        <?php else : ?>
                        	<p class="newsletters_error"><?php esc_html_e('No groups are available.', 'wp-mailinglist'); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                	<th><label for="doubleopt_Y"><?php esc_html_e('Double Opt-In', 'wp-mailinglist'); ?></label>
                	<?php echo ( $Html -> help(__('With "Require Activation?" setting turned on in configuration, this is effective. You can then specify for this specific list whether double opt-in is required or not. If you specify Yes, a subscriber will need to activate/confirm via an email with confirmation link.', 'wp-mailinglist'))); ?></th>
                	<td>
                		<label><input <?php echo (empty($doubleopt) || (!empty($doubleopt) && $doubleopt == "Y")) ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[doubleopt]" value="Y" id="doubleopt_Y" /> <?php esc_html_e('Yes, require activation', 'wp-mailinglist'); ?></label>
                		<label><input <?php echo (!empty($doubleopt) && $doubleopt == "N") ? 'checked="checked"' : ''; ?> type="radio" name="Mailinglist[doubleopt]" value="N" id="doubleopt_N" /> <?php esc_html_e('No, activate immediately', 'wp-mailinglist'); ?></label>
                		<span class="howto"><?php esc_html_e('This is only effective when "Require Activation?" is turned on in configuration', 'wp-mailinglist'); ?></span>
                	</td>
                </tr>
                <tr>
	                <th><label for="Mailinglist.subredirect"><?php esc_html_e('Subscribe Redirect URL', 'wp-mailinglist'); ?></label></th>
	                <td>
		                <?php if ($this -> language_do()) : ?>
							<div id="mailinglist-subredirect-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#mailinglist-subredirect-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="mailinglist-subredirect-tabs-<?php echo esc_html( $language); ?>">
										<input placeholder="<?php echo esc_attr(wp_unslash(__('https://domain.com/custom/url/to/go/to/', 'wp-mailinglist'))); ?>" type="text" class="widefat" name="Mailinglist[subredirect][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $Mailinglist -> data -> subredirect))); ?>" id="Mailinglist_subredirect_<?php echo esc_html( $language); ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#mailinglist-subredirect-tabs').tabs();
								}
							});
							</script>
						<?php else : ?>
		                	<?php echo ( $Form -> text('Mailinglist[subredirect]', array('placeholder' => __('https://domain.com/custom/url/to/go/to/', 'wp-mailinglist')))); ?>
		                <?php endif; ?>
		                <span class="howto"><small><?php esc_html_e('(optional)', 'wp-mailinglist'); ?></small> <?php esc_html_e('Leave empty for default, global behaviour. Else fill in a subscribe redirect URL for this list', 'wp-mailinglist'); ?></span>
	                </td>
                </tr>
                <tr>
                	<th><label for="Mailinglist.redirect"><?php esc_html_e('Confirm Redirect URL', 'wp-mailinglist'); ?></label></th>
                	<td>
	                	<?php if ($this -> language_do()) : ?>
							<div id="mailinglist-redirect-tabs">
								<ul>
									<?php foreach ($languages as $language) : ?>
										<li><a href="#mailinglist-redirect-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
									<?php endforeach; ?>
								</ul>
								<?php foreach ($languages as $language) : ?>
									<div id="mailinglist-redirect-tabs-<?php echo esc_html( $language); ?>">
										<input placeholder="<?php echo esc_attr(wp_unslash(__('https://domain.com/custom/url/to/go/to/', 'wp-mailinglist'))); ?>" type="text" class="widefat" name="Mailinglist[redirect][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $Mailinglist -> data -> redirect))); ?>" id="Mailinglist_redirect_<?php echo esc_html( $language); ?>" />
									</div>
								<?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#mailinglist-redirect-tabs').tabs();
								}
							});
							</script>
						<?php else : ?>
                			<?php echo ( $Form -> text('Mailinglist[redirect]', array('placeholder' => __('https://domain.com/custom/url/to/go/to/', 'wp-mailinglist')))); ?>
                		<?php endif; ?>
                		<span class="howto"><small><?php esc_html_e('(optional)', 'wp-mailinglist'); ?></small> <?php esc_html_e('Leave empty for default, global behaviour, else fill in a confirmation redirect URL location for this list.', 'wp-mailinglist'); ?></span>
                	</td>
                </tr>
                <?php if (apply_filters('newsletters_admin_mailinglists_save_paidlist_show', true)) : ?>
					<tr>
						<th><label for="Mailinglist.paidNo"><?php esc_html_e('Paid List', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php $radios = array('Y' => __('Yes', 'wp-mailinglist'), 'N' => __('No', 'wp-mailinglist')); ?>
							<?php echo ( $Form -> radio('Mailinglist[paid]', $radios, array('separator' => false, 'default' => "N", 'onclick' => "if (this.value == 'Y') { jQuery('#paiddiv').show(); } else { jQuery('#paiddiv').hide(); }"))); ?>
	                        <span class="howto"><?php esc_html_e('A paid list requires a payment per interval to keep the subscription active.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<div id="paiddiv" style="display:<?php echo ($Html -> field_value('Mailinglist[paid]') == "Y") ? 'block' : 'none'; ?>;">
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Mailinglist.price"><?php esc_html_e('Subscription Price', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php echo wp_kses_post($Html -> currency()); ?><?php echo ( $Form -> text('Mailinglist[price]', array('width' => '65px'))); ?>
                            <span class="howto"><?php esc_html_e('Payment price at the interval below to stay subscribed to this list.', 'wp-mailinglist'); ?></span>
                        </td>
					</tr>
					<tr>
						<th><label for="Mailinglist.interval"><?php esc_html_e('Subscription Interval', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php echo ( $Form -> select('Mailinglist[interval]', $this -> get_option('intervals'))); ?>
                        	<span class="howto"><?php esc_html_e('Interval at which to charge the payment price above.', 'wp-mailinglist'); ?></span>
                        </td>
					</tr>
					<tr>
						<th><label for="Mailinglist.maxperinterval"><?php esc_html_e('Max Emails per Interval', 'wp-mailinglist'); ?></label>
						<?php echo ( $Html -> help(__('Specify the maximum number of emails/newsletters the subscriber may receive on this paid mailing list before it stops sending. Leave it zero (0) or empty for no limit.', 'wp-mailinglist'))); ?></th>
						<td>
							<?php echo ( $Form -> text('Mailinglist[maxperinterval]', array('width' => "65px"))); ?>
							<span class="howto"><?php esc_html_e('Maximum allowed emails per interval set above.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php if (apply_filters('newsletters_admin_mailinglists_save_adminemail_show', true)) : ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="Mailinglist.adminemail"><?php esc_html_e('Administrator Email', 'wp-mailinglist'); ?></label></th>
						<td>
							<?php echo ( $Form -> text('Mailinglist[adminemail]', array('placeholder' => __('Enter a valid email address', 'wp-mailinglist')))); ?>
							<span class="howto"><small><?php esc_html_e('(optional)', 'wp-mailinglist'); ?></small> <?php esc_html_e('Email address to send notifications to for events of this mailing list.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>
		
		<p class="submit">
			<?php echo ( $Form -> submit(__('Save Mailing List', 'wp-mailinglist'))); ?>
			<div class="newsletters_continueediting">
				<label><input <?php echo (!empty($_REQUEST['continueediting'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="continueediting" value="1" id="continueediting" /> <?php esc_html_e('Continue editing', 'wp-mailinglist'); ?></label>
			</div>
		</p>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php if ($this -> language_do()) : ?>
		newsletters_focus('#Mailinglist_title_<?php echo esc_html( $languages[0]); ?>');
	<?php else : ?>
		newsletters_focus('#Mailinglist\\.title');
	<?php endif; ?>
});
</script>