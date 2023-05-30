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
			<th><label for="etmessage_sendas"><?php esc_html_e('Email Message', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php if ($this -> language_do()) : ?>
					<?php if (!empty($languages) && is_array($languages)) : ?>
				    	<div id="languagetabssendas">
				        	<ul>
								<?php $tabnumber = 1; ?>
				                <?php foreach ($languages as $language) : ?>
				                 	<li><a href="#languagetabsendas<?php echo esc_html($tabnumber); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
				                    <?php $tabnumber++; ?>
				                <?php endforeach; ?>
				            </ul>
				            
				            <?php $tabnumber = 1; ?>
				            <?php $texts = $this -> get_option('etmessage_sendas'); ?>
				            
				            <?php foreach ($languages as $language) : ?>
				            	<div id="languagetabsendas<?php echo esc_html($tabnumber); ?>">
					            	<?php 
					
									$settings = array(
										'media_buttons'		=>	true,
										'textarea_name'		=>	'etmessage_sendas[' . $language . ']',
										'textarea_rows'		=>	10,
										'quicktags'			=>	true,
										'teeny'				=>	false,
									);
									
									wp_editor(wp_unslash($this -> language_use($language, $texts)), 'etmessage_sendas_' . $language, $settings); 
									
									?>
				            	</div>
				            	<?php $tabnumber++; ?>
				            <?php endforeach; ?>
				    	</div>
				    <?php endif; ?>
				    
				    <script type="text/javascript">
				    jQuery(document).ready(function() {
					    if (jQuery.isFunction(jQuery.fn.tabs)) {
					    	jQuery('#languagetabssendas').tabs();
					    }
				    });
				    </script>
				<?php else : ?>
					<?php 
						
					$settings = array(
						'media_buttons'		=>	true,
						'textarea_name'		=>	'etmessage_sendas',
						'textarea_rows'		=>	10,
						'quicktags'			=>	true,
						'teeny'				=>	false,
					);
						
					wp_editor(wp_unslash($this -> get_option('etmessage_sendas')), 'etmessage_sendas', $settings); 
					
					?>
				<?php endif; ?>
				
				<div class="howto">
					<strong><?php esc_html_e('Shortcode Information', 'wp-mailinglist'); ?></strong><br/>
					<code>[newsletters_post_loop]...[/newsletters_post_loop]</code> <?php esc_html_e('The posts loop. Use the codes below inside.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_id]</code> <?php esc_html_e('The ID of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_author]</code> <?php esc_html_e('The display name of the author.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_title]</code> <?php esc_html_e('The title of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_link]</code> <?php esc_html_e('The URL of the post.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_date_wrapper]</code> <?php esc_html_e('A wrapper for the date, simply to work with the "showdate" parameter in the shortcode.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_date format="F jS, Y"]</code> <?php esc_html_e('The date of the post with an optional "format" parameter.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_thumbnail size="thumbnail"]</code> <?php esc_html_e('The thumbnail (if any) of the post with an optional "size" parameter.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_excerpt]</code> <?php esc_html_e('The excerpt of the post taken from the content.', 'wp-mailinglist'); ?><br/>
					<code>[newsletters_post_content]</code> <?php esc_html_e('The full content of the post as published.', 'wp-mailinglist'); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<h3><?php esc_html_e('Default "Send as Newsletter" Settings', 'wp-mailinglist'); ?></h3>
<p class="howto"><?php esc_html_e('Set default list/s to preselect in the "Send as Newsletter" box based on category selections when creating/editing a post.', 'wp-mailinglist'); ?></p>

<?php
	
$select = wp_dropdown_categories(array('show_option_none' => false, 'echo' => 0, 'name' => "sendas_defaults[][category]", 'id' => "", 'class' => "categoryselect noselect", 'hide_empty' => 0, 'show_count' => 1));
$select = preg_replace("#<select([^>]*)>#", '<select$1><option value="">' . __('- Select Category -', 'wp-mailinglist') . '</option><option value="any">' . __('- Any/All Category -', 'wp-mailinglist') . '</option>', $select);

$sendas_defaults_postbyemail = $this -> get_option('sendas_defaults_postbyemail');
$sendas_defaults_postbyemailoutput = $this -> get_option('sendas_defaults_postbyemailoutput');
$sendas_defaults = maybe_unserialize($this -> get_option('sendas_defaults'));
	
?>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="sendas_defaults_postbyemail"><?php esc_html_e('Post by Email or API', 'wp-mailinglist'); ?></label></th>
			<td><label><input <?php checked($sendas_defaults_postbyemail, 1, true); ?> onclick="if (jQuery(this).is(':checked')) { jQuery('#postbyemail_div').show(); } else { jQuery('#postbyemail_div').hide(); }" type="checkbox" name="sendas_defaults_postbyemail" value="1" id="sendas_defaults_postbyemail" /> <?php esc_html_e('Use these settings when posting by email or API.', 'wp-mailinglist'); ?></label></td>
		</tr>
	</tbody>
</table>

<div id="postbyemail_div" style="display:<?php echo (!empty($sendas_defaults_postbyemail)) ? 'block' : 'none'; ?>;">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="sendas_defaults_postbyemailoutput_full"><?php esc_html_e('Post by Email Output', 'wp-mailinglist'); ?></label></th>
				<td>
					<label><input <?php echo (empty($sendas_defaults_postbyemailoutput) || $sendas_defaults_postbyemailoutput == "full") ? 'checked="checked"' : ''; ?> type="radio" name="sendas_defaults_postbyemailoutput" value="full" id="sendas_defaults_postbyemailoutput_full" /> <?php esc_html_e('Full Post', 'wp-mailinglist'); ?></label>
					<label><input <?php echo (!empty($sendas_defaults_postbyemailoutput) && $sendas_defaults_postbyemailoutput == "excerpt") ? 'checked="checked"' : ''; ?> type="radio" name="sendas_defaults_postbyemailoutput" value="excerpt" id="sendas_defaults_postbyemailoutput_excerpt" /> <?php esc_html_e('Excerpt of Post', 'wp-mailinglist'); ?></label>
				</td>
			</tr>		
		</tbody>
	</table>
</div>

<table class="widefat" id="sendas_defaults_table">
	<thead>
		<tr>
			<th><button type="button" class="button button-primary addrow"><i class="fa fa-plus fa-fw"></i></button></th>
			<th><?php esc_html_e('Category', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('List/s', 'wp-mailinglist'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr id="sample" style="display:none;">
			<td><button type="button" class="button button-secondary delrow"><i class="fa fa-trash fa-fw"></i></button></td>
			<td>
				<?php echo wp_kses_post($select); ?>
			</td>
			<td>
				<?php if ($lists = $Mailinglist -> select(true)) : ?>
					<div style="overflow:scroll; max-height:200px;">
						<?php foreach ($lists as $list_id => $list_title) : ?>
							<label><input type="checkbox" name="sendas_defaults[][lists][]" class="listscheckboxes" value="<?php echo esc_html( $list_id); ?>" /> <?php echo esc_attr($list_title); ?></label><br/>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="newsletters_error"><?php esc_html_e('No mailing lists are available.', 'wp-mailinglist'); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php if (empty($sendas_defaults)) : ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="3"><?php esc_html_e('No defaults are set yet.', 'wp-mailinglist'); ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<script type="text/javascript">
(function($) {
	
	var $table = $('#sendas_defaults_table'), 
	$n = 0, 
	$addbutton = $table.find('.addrow'), 
	$delbutton = $table.find('.delrow'), 
	$noitems = $table.find('tr.no-items');
	
	$addbutton.on('click', function() {
		sendas_addrow();
	});
	
	$table.on('click', '.delrow', function() {
		if (confirm('<?php esc_html_e('Are you sure you want to remove this?', 'wp-mailinglist'); ?>')) {
			$(this).closest('tr').remove();
		}
	});
	
	var sendas_addrow = function() {		
		$noitems.hide();
		$row = $('tr#sample').clone().removeAttr('style').removeAttr('id');
		$row.find('.categoryselect').attr('name', "sendas_defaults[" + $n + "][category]");
		$row.find('.listscheckboxes').attr('name', "sendas_defaults[" + $n + "][lists][]");
		$table.prepend($row);
		
		$n++;
		
		return $row;
	}
	
	$(document).ready(function() {
		<?php if (!empty($sendas_defaults)) : ?>
			<?php foreach ($sendas_defaults as $sendas_default) : ?>
				$row = sendas_addrow();
				$row.find('select.categoryselect').val('<?php echo esc_html( $sendas_default['category']); ?>');
				
				<?php if (!empty($sendas_default['lists'])) : ?>
					<?php foreach ($sendas_default['lists'] as $list_id) : ?>
						$row.find('.listscheckboxes[value="<?php echo esc_html( $list_id); ?>"]').prop('checked', true);
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	});
	
})(jQuery);
</script>