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
			<th><label for="etmessage_posts"><?php esc_html_e('Email Message', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabsposts">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabposts<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_posts'); ?>
				            
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabposts<?php echo esc_html($tabnumber); ?>">
					            	<?php 
					
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_posts[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	false,
									);
									
									wp_editor(wp_unslash($this -> language_use($language, $texts)), 'etmessage_posts_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabsposts').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
						
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_posts',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	false,
					);
						
					wp_editor(wp_unslash($this -> get_option('etmessage_posts')), 'etmessage_posts', $settings); 
					
					?>
				<?php endif; ?>
				
				<?php if ($posts_backup = $this -> get_option('etmessage_posts_backup')) : ?>
					<h3><?php esc_html_e('Backup', 'wp-mailinglist'); ?></h3>
					<p><?php esc_html_e('A new system email has been loaded, your backup is below:', 'wp-mailinglist'); ?></p>
					<textarea class="widefat" cols="100%" rows="5"><?php echo esc_attr(wp_unslash($posts_backup)); ?></textarea>
				<?php endif; ?>
				
				<div class="howto">
					<strong><?php esc_html_e('Shortcode Information', 'wp-mailinglist'); ?></strong><br/>
					<code>[newsletters_post_loop]...[/newsletters_post_loop]</code> <?php esc_html_e('The posts loop. Use the codes below inside.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_id]</code> <?php esc_html_e('The ID of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_author]</code> <?php esc_html_e('The display name of the author.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_anchor style="..."]</code> <?php esc_html_e('Link to the post', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_title]</code> <?php esc_html_e('The title of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_link]</code> <?php esc_html_e('The URL of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_date_wrapper]</code> <?php esc_html_e('A wrapper for the date, simply to work with the "showdate" parameter in the shortcode.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_date format="F jS, Y"]</code> <?php esc_html_e('The date of the post with an optional "format" parameter.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_thumbnail size="thumbnail" hspace="15" align="left"]</code> <?php esc_html_e('The thumbnail (if any) of the post with an optional "size" parameter.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_excerpt]</code> <?php esc_html_e('The excerpt of the post taken from the content.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_content]</code> <?php esc_html_e('The full content of the post as published.', 'wp-mailinglist'); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>