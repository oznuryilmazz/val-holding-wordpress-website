<?php // phpcs:ignoreFile ?>
<!-- Post/Page Editing Screen Metabox -->

<?php
	
global $post;
$post_id = $post -> ID;

$newsletters_sendasnewsletter = get_post_meta($post_id, '_newsletters_sendasnewsletter', true);
$newsletters_subject = get_post_meta($post_id, '_newsletters_subject', true);
$newsletters_showtitle = get_post_meta($post_id, '_newsletters_showtitle', true);
$newsletters_showdate = get_post_meta($post_id, '_newsletters_showdate', true);
$scheduled = get_post_meta($post_id, '_newsletters_scheduled', true);
$sent = get_post_meta($post_id, '_newsletters_sent', true);
$history_id = get_post_meta($post_id, '_newsletters_history_id', true);
$postmailinglists = (empty($_POST['newsletters_mailinglists'])) ? get_post_meta($post_id, '_newsletters_mailinglists', true) :  sanitize_text_field(wp_unslash($_POST['newsletters_mailinglists']));
$theme_id = (empty($_POST['newsletters_theme_id'])) ? get_post_meta($post_id, '_newsletters_theme_id', true) :  sanitize_text_field(wp_unslash($_POST['newsletters_theme_id']));
$language = (empty($_POST['newsletters_language'])) ? get_post_meta($post_id, '_newsletters_language', true) :  sanitize_text_field(wp_unslash($_POST['newsletters_language']));
$sendonpublishef = (empty($_POST['newsletters_sendonpublishef'])) ? get_post_meta($post_id, '_newsletters_sendonpublishef', true) :  sanitize_text_field(wp_unslash($_POST['newsletters_sendonpublishef']));

?>

<div class="<?php echo esc_html($this -> pre); ?> newsletters newsletters_write_advanced">
	
    <?php echo  __('Use this box to send a post, page or custom post type as a newsletter to your subscribers. You can choose the list(s) to send to, the template to use, etc. All emails sent this way are queued and you can find them under %s > Email Queue after the post, page or custom post type has been saved.', 'wp-mailinglist'); ?>
	<?php if (!empty($scheduled)) : ?>
		<div class="misc-pub-section">
			<p class="newsletters_warning"><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Note that this post is already scheduled to send out as a newsletter.', 'wp-mailinglist'); ?></p>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($sent)) : ?>
		<?php if ($history = $this -> History() -> find(array('id' => $history_id))) : ?>
			<div class="misc-pub-section">
				<p class="newsletters_success"><i class="fa fa-check"></i> <?php echo sprintf(__('Note that this post has already been sent: %s', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id) . '">' . esc_html($history -> subject) . '</a>'); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	
	<div class="misc-pub-section">
		<p>
			<label><input <?php echo (!empty($newsletters_sendasnewsletter)) ? 'checked="checked"' : ''; ?> onclick="if (jQuery(this).is(':checked')) { jQuery('#newsletters_sendasnewsletter_div').show(); } else { jQuery('#newsletters_sendasnewsletter_div').hide(); }" type="checkbox" name="newsletters_sendasnewsletter" value="1" id="newsletters_sendasnewsletter" /> <?php esc_html_e('Yes, send this post as a newsletter', 'wp-mailinglist'); ?></label>
			<span class="howto"><?php esc_html_e('Turn this on to send this post/page as a newsletter. Then configure it and Publish, Update or Schedule the post to execute.', 'wp-mailinglist'); ?></span>
		</p>
	</div>
	
	<div id="newsletters_sendasnewsletter_div" style="display:<?php echo (!empty($newsletters_sendasnewsletter)) ? 'block' : 'none'; ?>;">
		
		<div class="misc-pub-section">
			<p><a class="button button-secondary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_templates . '#sendasdiv')) ?>" target="_blank"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit the Email Template Used', 'wp-mailinglist'); ?></a> <?php echo ( $Html -> help(__('The system email layout used for sending a post/page as a newsletter can be changed according to your needs. Click the button to edit it.', 'wp-mailinglist'))); ?></p>
		</div>
		
		<?php if ($this -> language_do()) : ?>
			<div class="misc-pub-section">
			<p><strong><?php esc_html_e('Language', 'wp-mailinglist'); ?></strong></p>
		    <p><?php esc_html_e('Choose which title/content in the editor above should be sent to the mailing list/s chosen below.', 'wp-mailinglist'); ?></p>
		    <?php if ($languages = $this -> language_getlanguages()) : ?>
		    	<p>
					<?php foreach ($languages as $lang) : ?>
		                <label><input <?php echo ((!empty($language) && $language == $lang) || ($this -> language_default() == $lang)) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_language" value="<?php echo esc_html( $lang); ?>" id="newsletters_language_<?php echo esc_html( $lang); ?>" /> <?php echo $this -> language_flag($lang); ?> <?php echo wp_kses_post( wp_unslash($this -> language_name($lang))) ?></label><br/>
		            <?php endforeach; ?>
		        </p>
		    <?php else : ?>
		    	<p class="newsletters_error"><?php esc_html_e('No languages are available, please enable languages first.', 'wp-mailinglist'); ?></p>
		    <?php endif; ?>
		    </div>
		<?php endif; ?>
		
		<div class="misc-pub-section">		
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="newsletters_subject"><?php esc_html_e('Subject', 'wp-mailinglist'); ?></label></th>
						<td>
							<input type="text" class="widefat" name="newsletters_subject" value="<?php echo esc_attr(wp_unslash($newsletters_subject)); ?>" id="newsletters_subject" />
							<span class="howto"><?php esc_html_e('Optional. If you leave this empty, the post title will be used automatically.', 'wp-mailinglist'); ?></span>
							
							<script type="text/javascript">
							jQuery(document).ready(function() {
								var subjectchanged = null;
								jQuery('#newsletters_subject').on('change', function(event) {
									subjectchanged = true;
								});
								
								jQuery('#title').on('change, keyup', function(event) {
									var title = jQuery('#title').val();
									var subject = jQuery('#newsletters_subject').val();
									if (subjectchanged == null) {
										jQuery('#newsletters_subject').val(title);
									}
								});
							});
							</script>
						</td>
					</tr>
					<tr>
						<th><label for=""><?php esc_html_e('Full/Excerpt', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo ((!empty($sendonpublishef) && $sendonpublishef == "fp") || ($this -> get_option('sendonpublishef') == "fp")) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_sendonpublishef" value="fp" /> <?php esc_html_e('Full Post', 'wp-mailinglist'); ?></label>
							<label><input <?php echo ((!empty($sendonpublishef) && $sendonpublishef == "ep") || ($this -> get_option('sendonpublishef') == "ep")) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_sendonpublishef" value="ep" /> <?php esc_html_e('Excerpt of Post', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Choose whether the full post or only an excerpt should be used.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="newsletters_showdate"><?php esc_html_e('Show Date/Author', 'wp-mailinglist'); ?></label></th>
						<td>
							<label><input <?php echo (!empty($newsletters_showdate)) ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_showdate" value="1" id="newsletters_showdate" /> <?php esc_html_e('Yes, show the date and author', 'wp-mailinglist'); ?></label>
							<span class="howto"><?php esc_html_e('Choose whether to show post meta such as date and author.', 'wp-mailinglist'); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="misc-pub-section newsletters_templates">
			<p><strong><?php esc_html_e('Select a Template', 'wp-mailinglist'); ?></strong></p>
			<p>
				<?php $Db -> model = $Theme -> model; ?>
				<?php if ($themes = $Db -> find_all(false, false, array('title', "ASC"))) : ?>
					<div class="scroll-list">
						<table class="widefat">
							<tbody>
						<?php $default_theme_id = $this -> default_theme_id('sending'); ?>
							<tr>
								<th class="check-column"><label><input <?php echo (empty($theme_id)) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_theme_id" value="0" id="theme0" /></label></th>
								<td><?php esc_html_e('NONE', 'wp-mailinglist'); ?></td>
							</tr>
					    <?php foreach ($themes as $theme) : ?>
					       <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
						        <th class="check-column"><input <?php echo ((!empty($theme_id) && $theme_id == $theme -> id) || $theme -> id == $default_theme_id) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_theme_id" value="<?php echo esc_html( $theme -> id); ?>" id="theme<?php echo esc_html( $theme -> id); ?>" /></th>
						        <td>
							        <label for="theme<?php echo esc_html( $theme -> id); ?>"><?php echo esc_html($theme -> title); ?></label>
							        <a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo esc_html($theme -> title); ?>', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html( $theme -> id); ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a>
									<a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', 'wp-mailinglist'), $theme -> title); ?>', href:newsletters_ajaxurl + 'action=newsletters_themeedit&security=<?php echo esc_html( wp_create_nonce('themeedit')); ?>&id=<?php echo esc_html( $theme -> id); ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a>
						        </td>
					        </tr>
					    <?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</p>
		</div>
		
		<div class="misc-pub-section newsletters_mailinglists">
			<p><strong><?php esc_html_e('Select Mailing List/s', 'wp-mailinglist'); ?></strong></p>
			<div class="scroll-list">
				<table class="widefat">
					<tbody>
						<?php if ($mailinglists = $Mailinglist -> select($privatelists = true)) : ?>
							<tr>
								<th class="check-column"><input type="checkbox" name="mailinglistsselectall" value="1" id="mailinglistsselectall" onclick="jqCheckAll(this, 'post', 'wpmlmailinglists');" /></th>
								<td><label for="mailinglistsselectall" style="font-weight:bold;"><?php esc_html_e('Select All', 'wp-mailinglist'); ?></label></td>
							</tr>
							<?php foreach ($mailinglists as $id => $title) : ?>
								<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
									<th class="check-column"><input id="checklist<?php echo esc_html($id); ?>" <?php echo (!empty($postmailinglists) && in_array($id, $postmailinglists)) ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_mailinglists[]" value="<?php echo esc_html($id); ?>" /></th>
									<td><label for="checklist<?php echo esc_html($id); ?>"><?php echo esc_html( $title); ?> (<?php echo esc_html( $SubscribersList -> count(array('list_id' => $id, 'active' => "Y"))); ?> <?php esc_html_e('active subscribers', 'wp-mailinglist'); ?>)</label></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="newsletters_error"><?php esc_html_e('No mailing lists are available', 'wp-mailinglist'); ?></p>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		
		<br class="clear" />
	</div>
</div>

<?php $sendas_defaults = maybe_unserialize($this -> get_option('sendas_defaults')); ?>

<script type="text/javascript">
(function($) {	
	
	var $checked = [], 
	$sendas_defaults = [], 
	$sendasnewsletter = false, 
	$sendasnewslettercheckbox = $('#newsletters_sendasnewsletter'), 
	$sendasnewsletterdiv = $('#newsletters_sendasnewsletter_div');
	
	<?php if (!empty($sendas_defaults)) : ?>
		<?php foreach ($sendas_defaults as $sendas_default) : ?>
			$sendas_default = {'category':'<?php echo esc_html( $sendas_default['category']); ?>', 'lists':['<?php echo implode("','", $sendas_default['lists']); ?>']};
			$sendas_defaults.push($sendas_default);
		<?php endforeach; ?>
	<?php endif; ?>
	
	$post_category = $('input[name="post_category[]"]');	
	$post_category.on('click, change', function() {
		check_lists_by_categories();
	});
	
	var check_lists_by_categories = function() {		
		$('input[name="newsletters_mailinglists[]"]').each(function() {
			$(this).prop('checked', false);
		});
		
		$post_category.each(function() {
			$thiscategory = this;
			if ($thiscategory.checked) {	
				$thiscategoryval = $($thiscategory).val();
				$.each($sendas_defaults, function($index, $sendas_default) {
					if ($sendas_default.category == $thiscategoryval || $sendas_default.category == "any") {
						$sendasnewsletter = true;
						$.each($sendas_default.lists, function($index, $list) {
							$('input[name="newsletters_mailinglists[]"][value="' + $list + '"]').prop('checked', true);
						});
					}
				});
			}
		});
		
		if ($sendasnewsletter == true) {
			$sendasnewslettercheckbox.prop('checked', true);
			$sendasnewsletterdiv.show();
		} else {
			$sendasnewslettercheckbox.prop('checked', false);
			$sendasnewsletterdiv.hide();
		}
	}
	
	$(document).ready(function() {
		check_lists_by_categories();
	});
	
})(jQuery);
</script>