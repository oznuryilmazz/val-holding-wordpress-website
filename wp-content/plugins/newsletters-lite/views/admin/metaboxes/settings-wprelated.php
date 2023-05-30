<?php // phpcs:ignoreFile ?>
<!-- WordPress Related Settings -->

<?php
	
global $newsletters_language_loaded;

$language_location = $this -> get_option('language_location');
$language_path = $Html -> get_language_location();

$wpmailconf = $this -> get_option('wpmailconf');
$wpmailconf_template = $this -> get_option('wpmailconf_template');
$custompostslug = $this -> get_option('custompostslug');
$custompostarchive = $this -> get_option('custompostarchive');
$timezone_set = $this -> get_option('timezone_set');

?>

<div class="advanced-setting">
	
	<h3><?php esc_html_e('Language Settings', 'wp-mailinglist'); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="language_location"><?php esc_html_e('Language Location', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input <?php echo (!empty($language_location) && $language_location == "plugin") ? 'checked="checked"' : ''; ?> type="radio" name="language_location" value="plugin" id="language_location_plugin" /> <?php echo sprintf(__('Inside %s', 'wp-mailinglist'), '<code>' . $this -> plugin_name . DS . 'languages' . DS . '</code>'); ?> <?php esc_html_e('(not recommended)', 'wp-mailinglist'); ?></label><br/>
					<label><input <?php echo (!empty($language_location) && $language_location == "custom") ? 'checked="checked"' : ''; ?> type="radio" name="language_location" value="custom" id="language_location_custom" /> <?php echo sprintf(__('Inside %s', 'wp-mailinglist'), '<code>' . PLUGINDIR . DS . 'wp-mailinglist-languages' . DS . '</code>'); ?></label><br/>
					<label><input <?php echo (!empty($language_location) && $language_location == "langdir") ? 'checked="checked"' : ''; ?> type="radio" name="language_location" value="langdir" id="language_location_langdir" /> <?php echo sprintf(__('Inside %s', 'wp-mailinglist'), '<code>' . LANGDIR . DS . 'plugins' . DS . '</code>'); ?></label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e('Current Language', 'wp-mailinglist'); ?></th>
				<td>
					<?php if (!empty($newsletters_language_loaded)) : ?>
						<code><?php echo esc_html( $language_path); ?></code>
					<?php else : ?>
						<?php echo sprintf(__('No language file loaded, please ensure that %s exists.', 'wp-mailinglist'), '<code>' . $language_path . '</code>'); ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3><?php esc_html_e('WordPress Emails', 'wp-mailinglist'); ?></h3>
	
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="wpmailconf"><?php esc_html_e('Style WordPress Emails', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#wpmailconf_div').show(); } else { jQuery('#wpmailconf_div').hide(); }" <?php echo (!empty($wpmailconf)) ? 'checked="checked"' : ''; ?> type="checkbox" name="wpmailconf" value="1" id="wpmailconf" /> <?php esc_html_e('Yes, apply a template to outgoing emails', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('This will apply only if the email sent through WordPress users wp_mail() function.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="newsletters_indented" id="wpmailconf_div" style="display:<?php echo (!empty($wpmailconf)) ? 'block' : 'none'; ?>;">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="wpmailconf_template"><?php esc_html_e('Template', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php if ($themes = $Theme -> select()) : ?>
							<select name="wpmailconf_template" id="wpmailconf_template">
								<option value=""><?php esc_html_e('- None -', 'wp-mailinglist'); ?></option>
								<?php foreach ($themes as $theme_id => $theme_title) : ?>
									<option <?php echo (!empty($wpmailconf_template) && $wpmailconf_template == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $theme_id); ?>"><?php echo esc_html($theme_title); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<p class="newsletters_error"><?php esc_html_e('No templates are available', 'wp-mailinglist'); ?></p>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<h3><?php esc_html_e('Other WordPress Settings', 'wp-mailinglist'); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="rssfeedN"><?php esc_html_e('RSS Feed', 'wp-mailinglist'); ?></label> <?php echo ( $Html -> help(__('A simple RSS feed of your newsletters which your users can subscribe to.', 'wp-mailinglist'))); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('rssfeed') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="rssfeed" value="Y" id="rssfeedY" /> <?php esc_html_e('On', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('rssfeed') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="rssfeed" value="N" id="rssfeedN" /> <?php esc_html_e('Off', 'wp-mailinglist'); ?></label>
				<?php $rssurl = add_query_arg(array('feed' => "newsletters"), home_url()); ?>
				<span class="howto"><?php echo sprintf(__('Turn On to show an RSS feed of newsletters at %s', 'wp-mailinglist'), esc_url_raw( $Html -> link($rssurl, $rssurl, array('style' => "font-weight:bold;")))); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="tinymcebtnY"><?php esc_html_e('TinyMCE Editor Button', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('tinymcebtn') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebtn" value="Y" id="tinymcebtnY" /> <?php esc_html_e('Show', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('tinymcebtn') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="tinymcebtn" value="N" id="tinymcebtnN" /> <?php esc_html_e('Hide', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Would you like to show or hide the plugin button in the TinyMCE editor?', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="sendasnewsletterbox_Y"><?php esc_html_e('"Send as Newsletter" box on posts/pages', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('sendasnewsletterbox') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="sendasnewsletterbox" value="Y" id="sendasnewsletterbox_Y" /> <?php esc_html_e('Show', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('sendasnewsletterbox') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="sendasnewsletterbox" value="N" id="sendasnewsletterbox_N" /> <?php esc_html_e('Hide', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Should the "Send as Newsletter" box show on post/page editing screens?', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="subscriberegister_N"><?php esc_html_e('Register New Subscribers as Users', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo ($this -> get_option('subscriberegister') == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberegister" value="Y" id="subscriberegister_Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('subscriberegister') == "N") ? 'checked="checked"' : ''; ?> type="radio" name="subscriberegister" value="N" id="subscriberegister_N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Would you like to register all new subscribers as users?', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="custompostslug"><?php esc_html_e('Custom Post Type Slug', 'wp-mailinglist'); ?></label></th>
			<td>
				<input type="text" name="custompostslug" value="<?php echo esc_attr(wp_unslash($custompostslug)); ?>" id="custompostslug" />
				<span class="howto"><?php esc_html_e('The slug for the newsletter custom post type used internally', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="custompostarchive"><?php esc_html_e('Custom Post Public Archive', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($custompostarchive)) ? 'checked="checked"' : ''; ?> type="checkbox" name="custompostarchive" value="1" id="custompostarchive" /> <?php esc_html_e('Yes, show newsletter posts publicly', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php echo sprintf(__('Turning this on will display newsletters in an archive here %s', 'wp-mailinglist'), home_url('/' . $custompostslug . '/')); ?></span>
			</td>
		</tr>
		<tr class="advanced-setting">
			<th><label for="timezone_set"><?php esc_html_e('Set Timezone', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($timezone_set)) ? 'checked="checked"' : ''; ?> type="checkbox" name="timezone_set" value="1" id="timezone_set" /> <?php esc_html_e('Yes, set the timezone', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Turn this on for the plugin to attempt to set the PHP and server time to the current WordPress timezone.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>