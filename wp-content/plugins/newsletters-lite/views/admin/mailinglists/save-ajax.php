<?php // phpcs:ignoreFile ?>
<?php if (empty($ajax)) : ?>
	<div id="newsletters-mailinglist-save-wrapper">
<?php endif; ?>

<div class="wrap">
	<h1><?php esc_html_e('Save a Mailing List', 'wp-mailinglist'); ?></h1>
	
	<?php if (!empty($errors)) : ?>
		<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
	<?php endif; ?>
	
	<form action="" method="post" id="newsletters-mailinglist-form">
		<?php wp_nonce_field($this -> sections -> lists . '_save'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="Mailinglist.title"><?php esc_html_e('List Title', 'wp-mailinglist'); ?></label></th>
					<td>
						<?php if ($this -> language_do()) : ?>
							<?php $languages = $this -> language_getlanguages(); ?>
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
			</tbody>
		</table>
		
		<p class="submit">
			<a href="" onclick="jQuery.colorbox.close(); return false;" class="button button-secondary"><?php esc_html_e('Cancel', 'wp-mailinglist'); ?></a>
			<button value="1" type="submit" id="newsletters-mailinglist-save-button" name="save" class="button button-primary">
				<?php esc_html_e('Save Mailing List', 'wp-mailinglist'); ?>
				<span id="newsletters-mailinglist-save-loading" style="display:none;"><i class="fa fa-refresh fa-spin"></i></span>
			</button>
		</p>
	</form>
</div>

<?php if (empty($ajax)) : ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php if ($this -> language_do()) : ?>
		newsletters_focus('#Mailinglist_title_<?php echo esc_html( $languages[0]); ?>');
	<?php else : ?>
		newsletters_focus('#Mailinglist\\.title');
	<?php endif; ?>
	
	jQuery('#newsletters-mailinglist-form').submit(function() {
		jQuery('#newsletters-mailinglist-save-loading').show();
		jQuery('#newsletters-mailinglist-save-button').attr('disabled', "disabled");
		var formvalues = jQuery('#newsletters-mailinglist-form, #newsletters-subscriber-form').serialize();
		
		jQuery.ajax({
			url: newsletters_ajaxurl + 'action=newsletters_mailinglist_save&security=<?php echo esc_html( wp_create_nonce('mailinglist_save')); ?>&fielddiv=<?php echo esc_html( $fielddiv); ?>&fieldname=<?php echo esc_html( $fieldname); ?>',
			data: formvalues,
			dataType: "json",
			method: "POST",
			success: function(response) {
				jQuery('#newsletters-mailinglist-save-button').removeAttr('disabled');
				jQuery('#newsletters-mailinglist-save-loading').hide();
				
				var success = response.success;
				var errors = response.errors;
				var form = response.blocks.form;
				var checklist = response.blocks.checklist;
				
				if (success == true) {
					jQuery('#<?php echo esc_html( $fielddiv); ?>').html(checklist);
					jQuery.colorbox.close();
				} else {
					jQuery('#newsletters-mailinglist-save-wrapper').html(form);
					jQuery.colorbox.resize();
				}
			}
		});
		
		return false;
	});
});
</script>