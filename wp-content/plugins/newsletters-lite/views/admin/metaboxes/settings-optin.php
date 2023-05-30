<?php // phpcs:ignoreFile ?>
    <!-- Default Subscription Form Settings -->
<?php 

$embed = $this -> get_option('embed'); 
$captcha_type = $this -> get_option('captcha_type');
$rr_active = (empty($captcha_type) || $captcha_type == "none") ? false : true;

?>

<div class="alert alert-warning">
	<i class="fa fa-exclamation-triangle fa-fw"></i> <?php echo sprintf(__('Please use the new subscribe forms interface under %s instead if possible.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> forms) . '">' . __('Newsletters > Subscribe Forms', 'wp-mailinglist') . '</a>'); ?>
</div>

<p>
	<?php esc_html_e('These settings will affect post/page embedded and hardcoded subscribe forms.', 'wp-mailinglist'); ?>
</p>

<?php if ($this -> language_do()) : ?>
    <?php 
    
    $languages = $this -> language_getlanguages(); 
    
    if (!empty($embed)) {
	    foreach ($embed as $ekey => $eval) {
		    $embed[$ekey] = $this -> language_split($eval);
	    }
    }
    
    ?>
    
    <?php if (!empty($languages) && is_array($languages)) : ?>
    	<div id="languagetabs">
        	<ul>
				<?php $tabnumber = 1; ?>
                <?php foreach ($languages as $language) : ?>
                 	<li><a href="#languagetab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
                    <?php $tabnumber++; ?>
                <?php endforeach; ?>
            </ul>
            
            <?php $tabnumber = 1; ?>
            <?php foreach ($languages as $language) : ?>
            	<div id="languagetab<?php echo esc_html($tabnumber); ?>">
                	<table class="form-table">
                    	<tbody>
                        	<tr>
                                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_acknowledgement_<?php echo esc_html( $language); ?>"><?php esc_html_e('Acknowledgement', 'wp-mailinglist'); ?></label></th>
                                <td>
	                                <?php 
					
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'embed[acknowledgement][' . $language . ']',
										'textarea_rows'		=>	5,
										'quicktags'			=>	true,
										'teeny'				=>	false,
									);
									
									wp_editor(wp_unslash($embed['acknowledgement'][$language]), 'embed_acknowledgement_' . $language, $settings); 
									
									?>
	                            </td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_ajax_<?php echo esc_html( $language); ?>"><?php esc_html_e('Ajax Features', 'wp-mailinglist'); ?></label></th>
                                <td>
                                    <label><input id="<?php echo esc_html( $this -> pre); ?>embed_ajax_<?php echo esc_html( $language); ?>" <?php echo ($embed['ajax'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax][<?php echo esc_html( $language); ?>]" value="Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                                    <label><input <?php echo ($embed['ajax'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax][<?php echo esc_html( $language); ?>]" value="N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_button_<?php echo esc_html( $language); ?>"><?php esc_html_e('Button Text', 'wp-mailinglist'); ?></label></th>
                                <td><input type="text" class="widefat" id="<?php echo esc_html( $this -> pre); ?>embed_button_<?php echo esc_html( $language); ?>" name="embed[button][<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($embed['button'][$language])); ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="embed_scrollY_<?php echo esc_html( $language); ?>"><?php esc_html_e('Scroll to Subscription Form', 'wp-mailinglist'); ?></label></th>
                                <td>
                                    <label><input <?php echo ($embed['scroll'][$language] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll][<?php echo esc_html( $language); ?>]" value="Y" id="embed_scrollY_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                                    <label><input <?php echo ($embed['scroll'][$language] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll][<?php echo esc_html( $language); ?>]" value="N" id="embed_scrollN_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                                    <span class="howto"><?php esc_html_e('Should the page scroll to focus on the subscription form?', 'wp-mailinglist'); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="captchaN_<?php echo esc_html( $language); ?>"><?php esc_html_e('Use Captcha for Form', 'wp-mailinglist'); ?></label></th>
                                <td>
                                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo ($embed['captcha'][$language] == "Y") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha][<?php echo esc_html( $language); ?>]" value="Y" id="captchaY_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo (empty($embed['captcha'][$language]) || $embed['captcha'][$language] == "N") ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha][<?php echo esc_html( $language); ?>]" value="N" id="captchaN_<?php echo esc_html( $language); ?>" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                                    <?php if (!$this -> use_captcha()) : ?>
										<div class="newsletters_error"><?php echo sprintf(__('Please configure a security captcha under %s > Configuration > System > Captcha in order to use this.', 'wp-mailinglist'), $this -> name); ?></div>
									<?php else : ?>
										<div class="newsletters_success"><?php echo sprintf(__('Captcha is already setup, you can %s.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> settings_system) . '#captchadiv">' . __('configure it here', 'wp-mailinglist') . '</a>'); ?></div>
									<?php endif; ?>
                                    <span class="howto"><?php esc_html_e('Requires captcha/turing image input upon subscribing.', 'wp-mailinglist'); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php $tabnumber++; ?>
            <?php endforeach; ?>
        </div>
        
        <script type="text/javascript">
		jQuery(document).ready(function(e) {
			if (jQuery.isFunction(jQuery.fn.tabs)) {
            	jQuery('#languagetabs').tabs();
            }
        });
		</script>
    <?php else : ?>
    	<p class="newsletters_error"><?php esc_html_e('No languages have been defined.', 'wp-mailinglist'); ?></p>
    <?php endif; ?>
<?php else : ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_acknowledgement"><?php esc_html_e('Acknowledgement', 'wp-mailinglist'); ?></label></th>
                <td>                	
                	<?php 
					
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'embed[acknowledgement]',
						'textarea_rows'		=>	5,
						'quicktags'			=>	true,
						'teeny'				=>	false,
					);
					
                    $embed['acknowledgement'] = isset($embed['acknowledgement']) ? $embed['acknowledgement'] : '';
					wp_editor(wp_unslash($embed['acknowledgement']), 'embed_acknowledgement', $settings); 
					
					?>
                	
                	<span class="howto"><?php esc_html_e('Acknowledgement message to show after a successful subscribe.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_subscribeagain"><?php esc_html_e('Subscribe Again Link', 'wp-mailinglist'); ?></label></th>
                <td>
                    <label><input id="<?php echo esc_html( $this -> pre); ?>embed_subscribeagain" <?php echo (empty($embed['subscribeagain']) || $embed['subscribeagain'] == "Y") ? 'checked="checked"' : ''; ?> type="radio" name="embed[subscribeagain]" value="Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                    <label><input <?php echo (!empty($embed['subscribeagain']) && $embed['subscribeagain'] == "N") ? 'checked="checked"' : ''; ?> type="radio" name="embed[subscribeagain]" value="N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                    <span class="howto"><?php esc_html_e('Display a "subscribe again" link on success. useful for forms with multiple mailing lists.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_ajax"><?php esc_html_e('Ajax Features', 'wp-mailinglist'); ?></label></th>
                <td>
                    <label><input id="<?php echo esc_html( $this -> pre); ?>embed_ajax" <?php echo isset($embed['ajax']) && $embed['ajax'] == "Y" ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax]" value="Y" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                    <label><input <?php echo isset($embed['ajax']) && $embed['ajax'] == "N" ? 'checked="checked"' : ''; ?> type="radio" name="embed[ajax]" value="N" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                </td>
            </tr>
            <tr>
                <th><label for="<?php echo esc_html( $this -> pre); ?>embed_button"><?php esc_html_e('Button Text', 'wp-mailinglist'); ?></label></th>
                <td><input type="text" class="widefat" id="<?php echo esc_html( $this -> pre); ?>embed_button" name="embed[button]" value="<?php echo isset($embed['button']) ? esc_attr(wp_unslash($embed['button'])) : ''; ?>" /></td>
            </tr>
            <tr>
                <th><label for="embed_scrollY"><?php esc_html_e('Scroll to Subscription Form', 'wp-mailinglist'); ?></label></th>
                <td>
                    <label><input <?php echo isset($embed['scroll']) && $embed['scroll'] == "Y" ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll]" value="Y" id="embed_scrollY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                    <label><input <?php echo isset($embed['scroll']) && $embed['scroll'] == "N" ? 'checked="checked"' : ''; ?> type="radio" name="embed[scroll]" value="N" id="embed_scrollN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                    <span class="howto"><?php esc_html_e('Should the page scroll to focus on the subscription form?', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captchaN"><?php esc_html_e('Use Captcha for Form', 'wp-mailinglist'); ?></label></th>
                <td>
                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled"'; } else { echo isset($embed['captcha']) && $embed['captcha'] == "Y" ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha]" value="Y" id="captchaY" /> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></label>
                    <label><input <?php if (!$rr_active) { echo 'disabled="disabled" checked="checked"'; } else { echo isset($embed['captcha']) && $embed['captcha'] == "N" ? 'checked="checked"' : ''; } ?> type="radio" name="embed[captcha]" value="N" id="captchaN" /> <?php esc_html_e('No', 'wp-mailinglist'); ?></label>
                    <span class="howto"><?php esc_html_e('Requires captcha image input upon subscribing.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>