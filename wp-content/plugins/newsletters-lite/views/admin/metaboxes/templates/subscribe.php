<?php // phpcs:ignoreFile ?>
<?php

global $ID, $post_ID;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
}

?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="etsubject_subscribe"><?php esc_html_e('Email Subject', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>				    
				    <?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabssubscribe">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabsubscribe<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etsubject_subscribe'); ?>
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabsubscribe<?php echo esc_html($tabnumber); ?>">
				            		<input type="text" name="etsubject_subscribe[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $texts))); ?>" id="etsubject_subscribe_<?php echo esc_html( $language); ?>" class="widefat" />
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabssubscribe').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<input type="text" name="etsubject_subscribe" value="<?php echo esc_attr(wp_unslash($this -> get_option('etsubject_subscribe'))); ?>" id="etsubject_subscribe" class="widefat" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="etmessage_subscribe"><?php esc_html_e('Email Message', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabssubscribemessage">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabsubscribemessage<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_subscribe'); ?>
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabsubscribemessage<?php echo esc_html($tabnumber); ?>">
					            	<?php 
					
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_subscribe[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	false,
									);
									
									wp_editor(wp_unslash($this -> language_use($language, $texts)), 'etmessage_subscribe_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabssubscribemessage').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
					
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_subscribe',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	false,
					);
					
					wp_editor(wp_unslash($this -> get_option('etmessage_subscribe')), 'etmessage_subscribe', $settings); 
					
					?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="ettemplate_subscribe"><?php esc_html_e('Email Template', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php $ettemplate_subscribe = esc_html($this -> get_option('ettemplate_subscribe')); ?>
				<?php if ($themes = $Theme -> select()) : ?>
					<select name="ettemplate_subscribe" id="ettemplate_subscribe">
						<option value=""><?php esc_html_e('- Default -', 'wp-mailinglist'); ?></option>
						<?php foreach ($themes as $theme_id => $theme_title) : ?>
							<option <?php echo (!empty($ettemplate_subscribe) && $ettemplate_subscribe == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $theme_id); ?>"><?php echo esc_attr($theme_title); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<p class="newsletters_error"><?php esc_html_e('No templates are available', 'wp-mailinglist'); ?></p>
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>