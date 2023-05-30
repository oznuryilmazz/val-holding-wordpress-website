<?php // phpcs:ignoreFile ?>
<!-- Posts Configuration -->

<?php
	
$showpostattachments = $this -> get_option('showpostattachments');
$excerpt_settings = $this -> get_option('excerpt_settings');	
$postswpautop = $this -> get_option('postswpautop'); 
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="showpostattachments"><?php esc_html_e('Show Attachments of Newsletter on Post', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($showpostattachments)) ? 'checked="checked"' : ''; ?> type="checkbox" name="showpostattachments" value="1" id="showpostattachments" /> <?php esc_html_e('Yes, show attachments of newsletter published as post below the post.', 'wp-mailinglist'); ?></label>
			</td>
		</tr>
		<tr>
			<th><label for="excerpt_settings"><?php esc_html_e('Custom Excerpt Settings', 'wp-mailinglist'); ?></label>
			<?php echo ( $Html -> help(__('By turning this on, you can specify your own excerpt length and more text. If you leave it off, the default excerpt length and more text defined by the system, a template or the plugin will be used.', 'wp-mailinglist'))); ?></th>
			<td>
				<label><input onclick="if (jQuery(this).is(':checked')) { jQuery('#excerpt_settings_div').show(); } else { jQuery('#excerpt_settings_div').hide(); }" <?php echo (!empty($excerpt_settings)) ? 'checked="checked"' : ''; ?> type="checkbox" name="excerpt_settings" value="1" id="excerpt_settings" /> <?php esc_html_e('Yes, use custom excerpt length and more text', 'wp-mailinglist'); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div class="newsletters_indented" id="excerpt_settings_div" style="display:<?php echo (!empty($excerpt_settings)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="excerpt_length"><?php esc_html_e('Excerpt Length', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('This is the length of the excerpt of posts when inserted into a newsletter. It will be the effective length when you are using the <code>[newsletters_post_excerpt]</code> shortcode for example. The length is in words, not characters.', 'wp-mailinglist'))); ?></th>
				<td>
					<input type="text" name="excerpt_length" value="<?php echo esc_attr(wp_unslash($this -> get_option('excerpt_length'))); ?>" id="excerpt_length" class="widefat" style="width:65px;" /> <?php esc_html_e('words', 'wp-mailinglist'); ?>
					<span class="howto"><?php esc_html_e('Length of the excerpt in words.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="excerpt_more"><?php esc_html_e('Excerpt More Text', 'wp-mailinglist'); ?></label>
				<?php echo ( $Html -> help(__('Set the text of the "read more" link which is placed at the end of an excerpt. This link is only shown if the length of the content is more then the excerpt length specified above.', 'wp-mailinglist'))); ?></th>
				<td>
					<?php if ($this -> language_do()) : ?>
						<?php
						
						$languages = $this -> language_getlanguages();
						$excerpt_more = $this -> get_option('excerpt_more');
						
						?>
						<?php if (!empty($languages)) : ?>					
							<div id="excerptmoretabs">
								<ul>
									<?php $tabnumber = 1; ?>
					                <?php foreach ($languages as $language) : ?>
					                 	<li><a href="#excerptmoretab<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
					                    <?php $tabnumber++; ?>
					                <?php endforeach; ?>
					            </ul>
					            
					            <?php $tabnumber = 1; ?>
					            <?php foreach ($languages as $language) : ?>
					            	<div id="excerptmoretab<?php echo esc_html($tabnumber); ?>">
					            		<input class="widefat" type="text" name="excerpt_more[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $excerpt_more))); ?>" id="excerpt_more_<?php echo esc_html( $language); ?>" />
					            	</div>
					            	<?php $tabnumber++; ?>
					            <?php endforeach; ?>
							</div>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								if (jQuery.isFunction(jQuery.fn.tabs)) {
									jQuery('#excerptmoretabs').tabs();
								}
							});
							</script>
						<?php endif; ?>
					<?php else : ?>
						<input type="text" name="excerpt_more" value="<?php echo esc_attr(wp_unslash($this -> get_option('excerpt_more'))); ?>" id="excerpt_more" class="widefat" />
					<?php endif; ?>
					<span class="howto"><?php esc_html_e('Text to use for the "read more" link of excerpts.', 'wp-mailinglist'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<p class="howto"><?php esc_html_e('When writing a WordPress post, you will see a panel named "Send to Mailing List" which allows you to send a post as a newsletter', 'wp-mailinglist'); ?></p>

<table class="form-table">
	<tbody>
		<tr>
			<th><?php esc_html_e('Full Post or Excerpt', 'wp-mailinglist'); ?></th>
			<td>
				<label><input <?php echo ($this -> get_option('sendonpublishef') == "fp") ? 'checked="checked"' : ''; ?> type="radio" name="sendonpublishef" value="fp" /> <?php esc_html_e('Full Post', 'wp-mailinglist'); ?></label>
				<label><input <?php echo ($this -> get_option('sendonpublishef') == "ep") ? 'checked="checked"' : '';; ?> type="radio" name="sendonpublishef" value="ep" /> <?php esc_html_e('Excerpt of Post', 'wp-mailinglist'); ?></label>
                
                <span class="howto"><?php esc_html_e('Excerpt will be the content before <code>&#60;!--more--&#62;</code>. if it is not available, an excerpt will be automatically generated.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="postswpautop"><?php esc_html_e('Apply Paragraphs to Posts', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($postswpautop)) ? 'checked="checked"' : ''; ?> type="checkbox" name="postswpautop" value="1" id="postswpautop" /> <?php esc_html_e('Yes, apply paragraphs to posts.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Turning this on will apply wpautop() to [newsletters_post_excerpt] and [newsletters_post_content] shortcodes output.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>