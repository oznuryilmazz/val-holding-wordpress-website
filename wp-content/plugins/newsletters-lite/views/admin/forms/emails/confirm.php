<?php
// phpcs:ignoreFile

// Form Email Confirm

if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
}

$etsubject_confirm = $this -> get_option('etsubject_confirm_form_' . $form -> id);
$etmessage_confirm = $this -> get_option('etmessage_confirm_form_' . $form -> id);

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="etsubject_confirm"><?php esc_html_e('Email Subject', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabsconfirm">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabconfirm<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabconfirm<?php echo esc_html($tabnumber); ?>">
				            		<input type="text" placeholder="<?php echo esc_attr(wp_unslash($this -> et_subject('confirm', null, $language))); ?>" name="etsubject_confirm[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $etsubject_confirm))); ?>" id="etsubject_confirm_<?php echo esc_html( $language); ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsconfirm').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" placeholder="<?php echo esc_attr(wp_unslash($this -> et_subject('confirm'))); ?>" name="etsubject_confirm" value="<?php echo esc_attr(wp_unslash($this -> get_option('etsubject_confirm_form_' . $form -> id))); ?>" id="etsubject_confirm" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_confirm"><?php esc_html_e('Email Message', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabsconfirmmessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabconfirmmessage<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabconfirmmessage<?php echo esc_html($tabnumber); ?>">
					            	<?php 
					
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_confirm[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	false,
									);
									
									wp_editor(wp_unslash($this -> language_use($language, $etmessage_confirm)), 'etmessage_confirm_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsconfirmmessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_confirm',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	false,
					);
					
					wp_editor(wp_unslash($this -> get_option('etmessage_confirm_form_' . $form -> id)), 'etmessage_confirm', $settings); 
					
					?>
				<?php endif; ?>
				<p class="howto">
					<?php echo sprintf(__('Use %s to generate a confirmation/activation link.', 'wp-mailinglist'), '<code>[newsletters_activate]</code>'); ?><br/>
					<?php echo sprintf(__('Use %s to generate the list/s being confirmed.', 'wp-mailinglist'), '<code>[newsletters_mailinglist]</code>'); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>