<?php // phpcs:ignoreFile ?>
    <!-- Captcha Settings -->

<?php

$captcha_type = $this -> get_option('captcha_type');

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="captcha_type_none"><?php esc_html_e('Captcha Type', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(__('Choose the type of captcha security image you want to use or select "None" for no captcha.', 'wp-mailinglist'))); ?></th>
			<td>
				<label><input onclick="jQuery('#recaptcha_div').hide(); jQuery('#rsc_div').hide();" <?php echo (empty($captcha_type) || $captcha_type == "none") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="none" id="captcha_type_none" /> <?php esc_html_e('None', 'wp-mailinglist'); ?></label>
				<label><input onclick="jQuery('#recaptcha_div').show(); jQuery('#rsc_div').hide();" <?php echo (!empty($captcha_type) && $captcha_type == "recaptcha") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="recaptcha" id="captcha_type_recaptcha" /> <?php esc_html_e('reCAPTCHA', 'wp-mailinglist'); ?></label>
				<label><input onclick="jQuery('#recaptcha_div').hide(); jQuery('#rsc_div').show();" <?php echo (!empty($captcha_type) && $captcha_type == "rsc") ? 'checked="checked"' : ''; ?> type="radio" name="captcha_type" value="rsc" id="captcha_type_rsc" <?php echo (!$this -> is_plugin_active('captcha')) ? 'disabled="disabled"' : ''; ?> /> <?php esc_html_e('Really Simple Captcha', 'wp-mailinglist'); ?></label>
				
				<?php 
				
				$plugin = 'really-simple-captcha';	
				$path = 'really-simple-captcha/really-simple-captcha.php'; 
				
				?>
				<span class="plugin-install-rsc">
					<?php if (!$this -> is_plugin_active('captcha', true)) : ?>
						<button type="button" class="install-now button button-secondary" href="<?php echo wp_nonce_url(admin_url('plugin-install.php?tab=plugin-information&plugin=really-simple-captcha&TB_iframe=true&width=600&height=550')); ?>">
							<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Install RSC', $this -> plugin_name); ?>
						</button>
					<?php elseif (!$this -> is_plugin_active('captcha', false)) : ?>
						<a class="button button-secondary" href="<?php echo wp_nonce_url(admin_url('plugins.php?action=activate&plugin=' . $path), 'activate-plugin_' . $path); ?>" class=""><i class="fa fa-check fa-fw"></i> <?php esc_html_e('Activate RSC', 'wp-mailinglist'); ?></a>
					<?php endif; ?>
				</span>
				
				<span class="howto"><?php esc_html_e('Choose the type of captcha you want to use as a security image on subscribe forms or turn off by choosing "None"', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
(function($) {
	$document = $(document);
	$plugininstall = $('.plugin-install-rsc');
	
	$plugininstall.on('click', '.install-now', function() {
		tb_show('<?php esc_html_e('Install Really Simple Captcha Plugin', 'wp-mailinglist'); ?>', $(this).attr('href'), false);
		return false;
	});
	
	$plugininstall.on('click', '.activate-now', function() {
		window.location = $(this).attr('href');
	});
	
	$document.on('wp-plugin-installing', function(event, args) {
		$plugininstall.find('.install-now').html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php esc_html_e('Installing', 'wp-mailinglist'); ?>').prop('disabled', true);
	});
	
	$document.on('wp-plugin-install-success', function(event, response) {			
		$plugininstall.find('.install-now').html('<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Activate RSC', 'wp-mailinglist'); ?>').attr('href', response.activateUrl).prop('disabled', false);
		$plugininstall.find('.install-now').removeClass('install-now').addClass('activate-now')
	});
	
	$document.on('wp-plugin-install-error', function(event, response) {
		alert('<?php esc_html_e('An error occurred, please try again.', 'wp-mailinglist'); ?>');
		$plugininstall.find('.install-now').html('<i class="fa fa-check fa-fw"></i> <?php esc_html_e('Install RSC', 'wp-mailinglist'); ?>').prop('disabled', false);
	});
})(jQuery);
</script>

<!-- reCAPTCHA Settings -->
<?php

$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
$recaptcha_privatekey = $this -> get_option('recaptcha_privatekey');
$recaptcha_type = $this -> get_option('recaptcha_type');
$recaptcha_language = $this -> get_option('recaptcha_language');
$recaptcha_theme = $this -> get_option('recaptcha_theme');
$recaptcha_customcss = $this -> get_option('recaptcha_customcss');

?>

<div class="newsletters_indented" id="recaptcha_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "recaptcha") ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th></th>
				<td>
					<p><?php echo sprintf(__('In order to use reCAPTCHA, the public and private keys below are required.<br/>Go to the reCAPTCHA sign up and %screate a set of keys%s for this domain.', 'wp-mailinglist'), '<a href="https://www.google.com/recaptcha/admin/create" target="_blank">', '</a>'); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_type"><?php esc_html_e('reCAPTCHA Type', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input onclick="jQuery('#recaptcha_type_div_robot').show();" <?php echo (!empty($recaptcha_type) && $recaptcha_type == "robot") ? 'checked="checked"' : ''; ?> type="radio" name="recaptcha_type" value="robot" id="recaptcha_type_robot" /> esc_html_e("I'm not a robot", 'wp-mailinglist'); ?></label>
					<label><input onclick="jQuery('#recaptcha_type_div_robot').hide();" <?php echo (empty($recaptcha_type) || $recaptcha_type == "invisible") ? 'checked="checked"' : ''; ?> type="radio" name="recaptcha_type" value="invisible" id="recaptcha_type_invisible" /> <?php esc_html_e('Invisible', 'wp-mailinglist'); ?></label>
					<span class="howto"><?php esc_html_e('Choose the reCAPTCHA integration to use, make sure your keys are valid for that integration.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_publickey"><?php esc_html_e('Site Key', 'wp-mailinglist'); ?></label></th>
				<td>
					<input type="text" class="widefat" name="recaptcha_publickey" value="<?php echo esc_attr(wp_unslash($recaptcha_publickey)); ?>" id="recaptcha_publickey" />
					<span class="howto"><?php esc_html_e('Site key provided by reCAPTCHA upon signing up.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="recaptcha_privatekey"><?php esc_html_e('Secret Key', 'wp-mailinglist'); ?></label></th>
				<td>
					<input type="text" class="widefat" name="recaptcha_privatekey" value="<?php echo esc_attr(wp_unslash($recaptcha_privatekey)); ?>" id="recaptcha_privatekey" /> 
					<span class="howto"><?php esc_html_e('Secret key provided by reCAPTCHA upon signing up.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr class="advanced-setting">
				<th><label for="recaptcha_language"><?php esc_html_e('Language', 'wp-mailinglist'); ?></label></th>
				<td>
					<input type="text" class="widefat" style="width:65px;" name="recaptcha_language" value="<?php echo esc_attr(wp_unslash($recaptcha_language)); ?>" id="recaptcha_language" />
					<span class="howto"><?php echo sprintf(__('Language in which to display the captcha. See the %s', 'wp-mailinglist'), '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">' . __('language codes', 'wp-mailinglist') . '</a>'); ?></span>
				</td>
			</tr>
			<tr class="advanced-setting">
				<th><label for="recaptcha_theme"><?php esc_html_e('Theme', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('Choose the reCAPTCHA theme to show to your users. Some premade themes by reCAPTCHA are available or you can use the Custom theme and style it according to your needs.', 'wp-mailinglist'))); ?></th>
				<td>
					<?php $themes = array('light' => __('Light', 'wp-mailinglist'), 'dark' => __('Dark', 'wp-mailinglist')); ?>
					<select name="recaptcha_theme" id="recaptcha_theme">
						<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
						<?php foreach ($themes as $theme_key => $theme_value) : ?>
							<option <?php echo (!empty($recaptcha_theme) && $recaptcha_theme == $theme_key) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $theme_key); ?>"><?php echo esc_html( $theme_value); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="howto"><?php esc_html_e('Pick the reCAPTCHA theme that you want to use.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="recaptcha_type_div_robot" style="display:<?php echo (!empty($recaptcha_type) && $recaptcha_type == "robot") ? 'block' : 'none'; ?>;">
		
	</div>
</div>

<?php if ($this -> is_plugin_active('captcha')) : ?>
	<div class="newsletters_indented" id="rsc_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "rsc") ? 'block' : 'none'; ?>;">
		
		<!-- Preview of Really Simple Captcha -->
		<?php 
						    	
    	$captcha = new ReallySimpleCaptcha();
    	$captcha -> bg = $Html -> hex2rgb($this -> get_option('captcha_bg')); 
    	$captcha -> fg = $Html -> hex2rgb($this -> get_option('captcha_fg'));
    	$captcha_size = $this -> get_option('captcha_size');
    	$captcha -> img_size = array($captcha_size['w'], $captcha_size['h']);
    	$captcha -> char_length = $this -> get_option('captcha_chars');
    	$captcha -> font_size = $this -> get_option('captcha_font');
    	$captcha_word = $captcha -> generate_random_word();
    	$captcha_prefix = mt_rand();
    	$captcha_filename = $captcha -> generate_image($captcha_prefix, $captcha_word);
        $captcha_file = plugins_url() . '/really-simple-captcha/tmp/' . $captcha_filename; 
    	
    	?>
		
		<!-- Really Simple Captcha Settings -->
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for=""><?php esc_html_e('Preview', 'wp-mailinglist'); ?></label></th>
					<td>
						<div id="newsletters-captcha-preview">
							<img src="<?php echo esc_url_raw($captcha_file); ?>" alt="captcha" />
						</div>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_bg"><?php esc_html_e('Background Color', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('The background color of the captcha image in hex code eg. #FFFFFF', 'wp-mailinglist'))); ?></th>
					<td>				
						<input type="text" name="captcha_bg" id="captcha_bg" value="<?php echo esc_html( $this -> get_option('captcha_bg')); ?>" class="color-picker" />
						<span class="howto"><?php esc_html_e('Set the background color of the captcha image', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_fg"><?php esc_html_e('Text Color', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('The foreground/text color of the text on the captcha image.', 'wp-mailinglist'))); ?></th>
					<td>
						<input type="text" name="captcha_fg" id="captcha_fg" value="<?php echo esc_html( $this -> get_option('captcha_fg')); ?>" class="color-picker" />
						<span class="howto"><?php esc_html_e('Set the foreground/text color of the captcha image', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_size_w"><?php esc_html_e('Image Size', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('Choose the size of the captcha image as it will display to your users. Fill in the width and the height of the image in pixels (px). The default is 72 by 24px which is optimal.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php $captcha_size = $this -> get_option('captcha_size'); ?>
						<input type="text" class="widefat" style="width:45px;" name="captcha_size[w]" value="<?php echo esc_html( $captcha_size['w']); ?>" id="captcha_size_w" /> <?php esc_html_e('by', 'wp-mailinglist'); ?>
						<input type="text" class="widefat" style="width:45px;" name="captcha_size[h]" value="<?php echo esc_html( $captcha_size['h']); ?>" id="captcha_size_h" /> <?php esc_html_e('px', 'wp-mailinglist'); ?>
						<span class="howto"><?php esc_html_e('Choose your preferred size for the captcha image.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_chars"><?php esc_html_e('Number of Characters', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('You can increase the number of characters to show in the captcha image to increase the security. Too many characters will make it difficult for your users though. The default is 4.', 'wp-mailinglist'))); ?></th>
					<td>
						<input type="text" name="captcha_chars" value="<?php echo esc_html( $this -> get_option('captcha_chars')); ?>" id="captcha_chars" class="widefat" style="width:45px;" /> <?php esc_html_e('characters', 'wp-mailinglist'); ?>
						<span class="howto"><?php esc_html_e('The number of characters to show in the captcha image.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="captcha_font"><?php esc_html_e('Font Size', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('A larger font will make the characters easier to read for your users. The default is 14 pixels.', 'wp-mailinglist'))); ?></th>
					<td>
						<input type="text" name="captcha_font" value="<?php echo esc_html( $this -> get_option('captcha_font')); ?>" id="captcha_font" class="widefat" style="width:45px;" /> <?php esc_html_e('px', 'wp-mailinglist'); ?>
						<span class="howto"><?php esc_html_e('Choose the font size of the characters on the captcha image.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
				<tr class="advanced-setting">
					<th><label for="captchainterval"><?php esc_html_e('Cleanup Interval', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('To keep your server clean from old, unused captcha images a schedule will run at the interval specified to clean up old images. Set this to hourly or less as a recommended setting.', 'wp-mailinglist'))); ?></th>
					<td>
						<?php $captchainterval = $this -> get_option('captchainterval'); ?>
		                <select class="widefat" style="width:auto;" id="captchainterval" name="captchainterval">
			                <option value=""><?php esc_html_e('- Select Interval -', 'wp-mailinglist'); ?></option>
			                <?php $schedules = array(
                                "1minutes" => array(
                                    "interval" => 60,
                                    "display" => "Every Minute"
                                ),
                                "2minutes" => array(
                                    "interval" => 120,
                                    "display" => "Every 2 Minutes"
                                ),
                                "5minutes" => array(
                                    "interval" => 300,
                                    "display" => "Every 5 Minutes"
                                ),
                                "10minutes" => array(
                                    "interval" => 600,
                                    "display" => "Every 10 Minutes"
                                ),
                                "20minutes" => array(
                                    "interval" => 1200,
                                    "display" => "Every 20 Minutes"
                                ),
                                "30minutes" => array(
                                    "interval" => 1800,
                                    "display" => "Every 30 Minutes"
                                ),
                                "40minutes" => array(
                                    "interval" => 2400,
                                    "display" => "Every 40 Minutes"
                                ),
                                "50minutes" => array(
                                    "interval" => 3000,
                                    "display" => "Every 50 minutes"
                                ),
                                "hourly" => array(
                                    "interval" => 3600,
                                    "display" => "Once Hourly"
                                ),
                                "twicedaily" => array(
                                    "interval" => 43200,
                                    "display" => "Twice Daily"
                                ),
                                "daily" => array(
                                    "interval" => 86400,
                                    "display" => "Once Daily"
                                ),
                                "weekly" => array(
                                    "interval" => 604800,
                                    "display" => "Once Weekly"
                                ),
                                "monthly" => array(
                                    "interval" => 2664000,
                                    "display" => "Once Monthly"
                                )

                            ); ?>
			                <?php if (!empty($schedules)) : ?>
			                    <?php foreach ($schedules as $key => $val) : 
									if (preg_match('/wp_|every_minute/', $key)) continue;
								?>
			                    <?php $sel = ($captchainterval == $key) ? 'selected="selected"' : ''; ?>
			                    <option <?php echo esc_html( $sel); ?> value="<?php echo esc_html($key) ?>"><?php echo esc_html( $val['display']); ?> (<?php echo esc_html( $val['interval']) ?> <?php esc_html_e('seconds', 'wp-mailinglist'); ?>)</option>
			                    <?php endforeach; ?>
			                <?php endif; ?>
		                </select>
						<span class="howto"><?php esc_html_e('The interval at which old captcha images will be removed from the server.', 'wp-mailinglist'); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php endif; ?>
