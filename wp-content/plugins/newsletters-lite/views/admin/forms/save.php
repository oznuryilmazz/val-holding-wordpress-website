<?php
// phpcs:ignoreFile

global $ID, $post, $post_ID, $wp_meta_boxes;

$imagespost = $this -> get_option('imagespost');
$p_id = (empty($_POST['p_id'])) ? $imagespost :  sanitize_text_field(wp_unslash($_POST['p_id']));
$ID = $p_id;
$post_ID = $p_id;

//wp_enqueue_media(array('post' => $p_id));
wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

$screen = get_current_screen();
$page = $screen -> id;

$manualpostboxes = array();
$email_field_id = $Field -> email_field_id();
$list_field_id = $Field -> list_field_id();
if (!empty($form -> form_fields)) {
	foreach ($form -> form_fields as $form_field) {				
	    add_meta_box('newsletters_forms_field_' . $form_field -> field_id, '<i class="fa fa-bars fa-fw"></i> ' . ((empty($form_field -> label)) ? esc_html($form_field -> field -> title) : esc_html($form_field -> label)) . ' <span class="newsletters_handle_more">' . $Html -> field_type($form_field -> field -> type, $form_field -> field -> slug) . '</span>' . ((!empty($form_field -> required)) ? ' <span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span>' : ''), array($Metabox, 'forms_field'), $page, 'normal', 'core', array('form_field' => $form_field));
	    
		$manualpostboxes[] = array(
			'field_id'			=>	$form_field -> field_id,
			'mandatory'			=>	(($form_field -> field_id != $email_field_id && $form_field -> field_id != $list_field_id) ? false : true),
		);
	}
}

if ($this -> language_do()) {
	$languages = $this -> language_getlanguages();
}

$styling = maybe_unserialize($form -> styling);

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> <?php echo esc_html( $this -> sections -> forms); ?> newsletters">
	<?php if (!empty($_GET['id'])) : ?>
		<h1><?php esc_html_e('Edit Form', 'wp-mailinglist'); ?> <a onclick="jQuery.colorbox({title:'<?php esc_html_e('Create a New Form', 'wp-mailinglist'); ?>', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_forms_createform')) ?>'}); return false;" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=save')) ?>" class="add-new-h2"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a></h1>
	<?php else : ?>
		<h1><?php esc_html_e('Create Form', 'wp-mailinglist'); ?></h1>
	<?php endif; ?>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<form action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&amp;method=save')) ?>" method="post" id="post" name="post" enctype="multipart/form-data">
		<?php wp_nonce_field($this -> sections -> forms . '_save'); ?>
		
		<input type="hidden" name="fields" id="fields" value="" />
        <input type="hidden" name="id" id="id" value="<?php echo esc_attr(wp_unslash($form -> id)); ?>" />
        <input type="hidden" name="ajax" id="ajax" value="<?php echo esc_attr(wp_unslash($form -> ajax)); ?>" />
        <input type="hidden" name="scroll" id="scroll" value="<?php echo esc_attr(wp_unslash($form -> scroll)); ?>" />


        <?php
        if ($this -> language_do()) {
            foreach ($languages as $language) {
                ?>
                <input type="hidden" name="buttontext[<?php echo $language; ?>]" id="buttontext_<?php echo $language; ?>" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form -> buttontext))); ?>" />
                <input type="hidden" name="confirmation_message[<?php echo $language; ?>]" id="confirmation_message_<?php echo $language; ?>" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form -> confirmation_message))); ?>" />
                <input type="hidden" name="styling_beforeform[<?php echo $language; ?>]" id="styling_beforeform_<?php echo $language; ?>" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form -> styling_beforeform))); ?>" />
                <input type="hidden" name="styling_afterform[<?php echo $language; ?>]" id="styling_afterform_<?php echo $language; ?>" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form -> styling_afterform))); ?>" />

                <?php
            }
          ?>
        <?php
        }
        else {
            ?>
            <input type="hidden" name="buttontext" id="buttontext" value="<?php echo esc_attr(wp_unslash($language, $form -> buttontext)); ?>" />

            <input type="hidden" name="confirmation_message" id="confirmation_message" value="<?php echo esc_attr(wp_unslash($form -> confirmation_message)); ?>" />
            <input type="hidden" name="styling_beforeform" id="styling_beforeform" value="<?php echo esc_attr(wp_unslash($form -> styling_beforeform)); ?>" />
            <input type="hidden" name="styling_afterform" id="styling_afterform" value="<?php echo esc_attr(wp_unslash($form -> styling_afterform)); ?>" />

            <?php

        }
        ?>
        <input type="hidden" name="confirmationtype" id="confirmationtype" value="<?php echo esc_attr(wp_unslash($form -> confirmationtype)); ?>" />
        <input type="hidden" name="confirmation_redirect" id="confirmation_redirect" value="<?php echo esc_attr(wp_unslash($form -> confirmation_redirect)); ?>" />
        <input type="hidden" name="captcha" id="captcha" value="<?php echo esc_attr(wp_unslash($form -> captcha)); ?>" />
        <input type="hidden" name="styling_customcss" id="styling_customcss" value="<?php echo esc_attr(wp_unslash($form -> styling_customcss)); ?>" />

		<?php if (!empty($styling)) : ?>
			<?php foreach ($styling as $skey => $sval) : ?>
				<?php if ($skey != "twocolumns") : ?>
					<input type="hidden" name="styling[<?php echo esc_html( $skey); ?>]" value="<?php echo esc_attr(wp_unslash($sval)); ?>" />
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" for="title"></label>
							<?php if ($this -> language_do()) : ?>
								<div id="title-tabs">
									<ul>
										<?php foreach ($languages as $language) : ?>
											<li><a href="#title-tabs-<?php echo esc_html( $language); ?>"><?php echo wp_kses_post( $this -> language_flag($language)); ?></a></li>
										<?php endforeach; ?>
									</ul>
									<?php foreach ($languages as $language) : ?>
										<div id="title-tabs-<?php echo esc_html( $language); ?>">
											<input type="text" class="widefat title" name="title[<?php echo esc_html( $language); ?>]" value="<?php echo esc_attr(wp_unslash($this -> language_use($language, $form -> title))); ?>" id="title_<?php echo esc_html( $language); ?>" placeholder="<?php esc_html_e('Enter form name here', 'wp-mailinglist'); ?>" />
										</div>
									<?php endforeach; ?>
								</div>
								
								<script type="text/javascript">
								jQuery(document).ready(function() {
									if (jQuery.isFunction(jQuery.fn.tabs)) {
										jQuery('#title-tabs').tabs();
									}
								});
								</script>
								
								<style type="text/css">
								#titlediv .title {
									padding: 3px 8px;
									font-size: 1.7em;
									line-height: 100%;
									height: 1.7em;
									width: 100%;
									outline: 0;
									margin: 0 0 3px;
									background-color: #fff
								}
								</style>
							<?php else : ?>
								<input onclick="jQuery('iframe#content_ifr').attr('tabindex', '2');" tabindex="1" id="title" autocomplete="off" type="text" placeholder="<?php echo esc_attr(wp_unslash(__('Enter form name here', 'wp-mailinglist'))); ?>" name="title" value="<?php echo esc_attr(wp_unslash($form -> title)); ?>" />
							<?php endif; ?>
						</div>
						<?php if (!empty($form -> id)) : ?>
							<div class="inside">
								<div id="edit-slug-box">
									<strong><?php esc_html_e('Shortcode:', 'wp-mailinglist'); ?></strong>
									<span id="sample-permalink">
										<code>[newsletters_subscribe form=<?php echo esc_html($form -> id); ?>]</code>
										<button type="button" class="button button-secondary button-small copy-button" data-clipboard-text="[newsletters_subscribe form=<?php echo esc_html($form -> id); ?>]">
											<i class="fa fa-clipboard fa-fw"></i>
										</button>
										<?php echo ( $Html -> help(__('Copy/paste this shortcode into any post/page to display this subscribe form.', 'wp-mailinglist'))); ?>
										<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> forms . '&method=codes&id=' . $form -> id)) ?>"><?php esc_html_e('More embedding options', 'wp-mailinglist'); ?></a>
									</span>
								</div>
							</div>
						<?php endif; ?>
						<p>
							<label style="cursor: pointer; font-weight:bold;"><input onclick="jQuery('#postbox-container-2').toggleClass('newsletters-two-columns');" <?php echo (!empty($styling['twocolumns'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="styling[twocolumns]" value="1" /> <?php esc_html_e('Use two columns inside posts/pages', 'wp-mailinglist'); ?></label>
						</p>
						<?php if (!empty($errors['title'])) : ?>
							<div class="ui-state-error ui-corner-all">
								<p><i class="fa fa-exclamation-triangle"></i> <?php echo esc_html( $errors['title']); ?></p>
							</div>
						<?php endif; ?>
						<div class="inside">
						<div id="edit-slug-box" class="hide-if-no-js" style="display:<?php echo (!empty($_POST['ishistory'])) ? 'block' : 'none'; ?>;">
                                <?php $newsletter_url = $Html -> retainquery('newsletters_method=newsletter&id=' . esc_html(isset($_POST['ishistory']) ? $_POST['ishistory'] : ''), home_url()); ?>
                                <strong><?php _e('Permalink:', 'wp-mailinglist'); ?></strong>
                                <span id="sample-permalink" tabindex="-1"><?php echo $newsletter_url; ?></span>
                                <span id="view-post-btn"><a href="<?php echo $newsletter_url; ?>" target="_blank" class="button button-small"><?php _e('View Newsletter', 'wp-mailinglist'); ?></a></span>
							<input id="shortlink" type="hidden" value="<?php echo esc_attr($newsletter_url); ?>">
							<a href="#" class="button button-small" onclick="prompt('URL:', jQuery('#shortlink').val()); return false;"><?php esc_html_e('Get Link', 'wp-mailinglist'); ?></a></div>
						</div>
					</div>
					<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea edit-form-section" style="position:relative;">
						<!-- Editor will go here -->
						<?php $this -> render('error', array('errors' => $errors), true, 'admin'); ?>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes($page, 'side', $post); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container <?php echo (!empty($styling['twocolumns'])) ? 'newsletters-two-columns' : ''; ?>">
					<?php do_meta_boxes($page, 'normal', $post); ?>
                    <?php do_meta_boxes($page, 'advanced', $post); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<style type="text/css">
.temp-placeholder {
	border: 1px dashed #b4b9be;
    margin-bottom: 20px;
    background: #FFFFFF !important;
    box-shadow: none !important;
    width: 100% !important;
    padding-left: 10px;
}

.sortable-placeholder {
	width: 100% !important;
	height: 35px !important;
	background: #FFFFFF !important;
	box-shadow: none !important;
}

.newsletters-two-columns .sortable-placeholder {
	width: 48% !important;
	float: left !important;
	margin: 0 20px 20px 0;
}
</style>

<script type="text/javascript">
var warnMessage = "<?php echo addslashes(__('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', 'wp-mailinglist')); ?>";

function newsletters_forms_field_delete(field_id) {
	jQuery('#newsletters_forms_field_' + field_id).remove();
	jQuery('#newsletters_forms_availablefield_' + field_id).removeAttr('disabled');
	
	jQuery.ajax({
		url: newsletters_ajaxurl + "action=newsletters_forms_deletefield&security=<?php echo esc_html(  wp_create_nonce('forms_deletefield')) ?>",
		method: "POST",
		data: {field_id:field_id, form_id:jQuery('input#id').val()},
	}).done(function(response) {
		//all good
	});
	
	return true;
}

function newsletters_forms_field_add(element, target) {
	jQuery(target).attr('disabled', "disabled");
	jQuery('.temp-placeholder').remove();
	var field_id = jQuery(element).data('id');
	var field_type = jQuery(element).data('type');
	var field_slug = jQuery(element).data('slug');
	
	var loading = '<div id="newsletters_forms_loading_' + field_id + '" class="newsletters_loading postbox"><h2 class="hndle ui-sortable-handle"><span><i class="fa fa-refresh fa-spin fa-fw"></i> <?php esc_html_e('Loading field...', 'wp-mailinglist'); ?></span></h2></div>';
    
    if (jQuery('#normal-sortables > div').length > 0 && index != 0) {					    	    
	    jQuery("#normal-sortables > div:nth-child(" + index + ")").after(loading);
	} else if (index == 0) {
		jQuery('#normal-sortables').prepend(loading);
	} else {								
		jQuery('#normal-sortables').append(loading);
	}
	
	jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_forms_addfield&security=<?php echo esc_html( wp_create_nonce('forms_addfield')) ?>',
		method: "POST",
		data: {
			id: field_id,
			type: field_type,
			slug: field_slug,
		},
		success: function(response) {					
			jQuery('#newsletters_forms_loading_' + field_id).remove();
											
			if (jQuery('#normal-sortables > div').length > 0 && index != 0) {						
				jQuery("#normal-sortables > div:nth-child(" + index + ")").after(response);
			} else if (index == 0) {
				jQuery('#normal-sortables').prepend(response);
			} else {						
				jQuery('#normal-sortables').append(response);
			}
				
			jQuery('#normal-sortables').sortable('refresh');
		}
	});
}

var index = 0;
var hasdropped = false;

jQuery(document).ready(function() {	
	<?php if ($this -> language_do()) : ?>
		newsletters_focus('#title_<?php echo esc_html( $languages[0]); ?>');
	<?php else : ?>
		newsletters_focus('#title');
	<?php endif; ?>
	
	jQuery('form#post').on('submit', function() {
		jQuery('#normal-sortables').sortable('refresh');
		var sortable = jQuery('#normal-sortables').sortable('toArray');
		jQuery('#fields').val(sortable);
	});
	
	jQuery('#normal-sortables').sortable({
		//axis: 'y',
		placeholder: 'ui-placeholder',
		over: function() {
	        jQuery('.temp-placeholder').hide();
	    },
	    out: function() {
	        jQuery('.temp-placeholder').show();
	    },
	    stop: function() {
	        jQuery('.temp-placeholder').remove();
	    },
		start: function(e, ui) {						
	        ui.placeholder.width(ui.item.width());
	    },
	    receive: function(event, ui) {
		    
	    },
	    update: function(event, ui) {		  
		    var element = ui.item;
		    index = jQuery(element).index();
		    hasdropped = true;
		    
		    window.onbeforeunload = function () {			    
		        if (warnMessage != null) return warnMessage;
		    }
	    }
	});
	
	jQuery('#form_availablefields li input').draggable({
		cancel: false,
		connectToSortable: "#normal-sortables",
		helper: "clone",
		revert: "invalid",
		start: function(event, ui) {		
			hasdropped = false;	
			jQuery(ui.helper).css('width', jQuery('#normal-sortables').width());	
		},
		stop: function(event, ui) {		
			if (hasdropped == true) {
				// the field has been dropped				
				newsletters_forms_field_add(ui.helper, event.target);
			}
				
			jQuery('.temp-placeholder').remove();
			jQuery(ui.helper).remove();
		}
	}).on('click', function(e) {
		index = jQuery('#normal-sortables > div').length;
		newsletters_forms_field_add(e.target, jQuery(this));
	});
	
	jQuery('ul, li').disableSelection();
	
	<?php if (!empty($manualpostboxes)) : ?>
		<?php foreach ($manualpostboxes as $postbox) : ?>
			var postboxhtml = '';
			<?php if (empty($postbox['mandatory'])) : ?>
				postboxhtml += '<div class="newsletters_delete_handle"><a href="" onclick="if (confirm(\'<?php esc_html_e('Are you sure you want to delete this field?', 'wp-mailinglist'); ?>\')) { newsletters_forms_field_delete(\'<?php echo esc_html( $postbox['field_id']); ?>\'); } return false;"><i class="fa fa-times fa-fw"></i></a></div>';
				postboxhtml += '<div class="newsletters_edit_handle"><a href="" onclick="jQuery(this).closest(\'div.postbox\').toggleClass(\'closed\'); return false;"><i class="fa fa-pencil fa-fw"></i></a></div>';
			<?php endif; ?>
			jQuery('#newsletters_forms_field_<?php echo esc_html( $postbox['field_id']); ?>').find('.hndle').before(postboxhtml);
		<?php endforeach; ?>
	<?php else : ?>
		jQuery('#normal-sortables').append('<div class="temp-placeholder" style="width:auto; height:auto;"><p><i class="fa fa-reply"></i> <?php esc_html_e('Drag fields here to add them to the form', 'wp-mailinglist'); ?></p></div>');
	<?php endif; ?>

    jQuery('input:not(:button,:submit),textarea,select').change(function() {    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    jQuery(':submit').click(function(e) {	    
        warnMessage = null;
        return true;
    });
});
</script>