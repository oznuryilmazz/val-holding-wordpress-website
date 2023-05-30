<?php // phpcs:ignoreFile ?>
<script type="text/javascript">
	

	
<?php $history_id = (empty($_POST['ishistory'])) ? sanitize_text_field(wp_unslash($_GET['id'])) : sanitize_text_field(wp_unslash($_POST['ishistory'])); ?>

<?php if (!empty($history_id)) : ?>
var history_id = "<?php echo esc_js($history_id); ?>";
<?php else : ?>
var history_id = false;
<?php endif; ?>
	
var warnMessage = "<?php echo addslashes(__('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', 'wp-mailinglist')); ?>";

function deletecontentarea(number, history_id) {
	if (history_id != "") {
		var data = {number:number, history_id:history_id};
		jQuery.post(newsletters_ajaxurl + 'action=newsletters_deletecontentarea&security=<?php echo esc_html( wp_create_nonce('deletecontentarea')); ?>', data, function(response) {
			//all good, the request was successful
			
		});
	} else {
		tinyMCE.execCommand("mceRemoveEditor", false, 'contentarea' + number);
		contentarea--;
	}
	
	jQuery('#contentareabox' + number).remove();
}

function addcontentarea() {	
	jQuery('#addcontentarea_button').prop('disabled', true);
	jQuery('#contentarea_loading').show();
	jQuery.post(newsletters_ajaxurl + 'action=newsletters_load_new_editor', {contentarea:contentarea}, function(response) {
		jQuery('#contentareas').append(response);
		jQuery('#addcontentarea_button').prop('disabled', false);
		
		if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
			jQuery('#contentarea_loading').hide();
			quicktags({id:'contentarea' + contentarea});
			tinyMCE.execCommand("mceAddEditor", false, 'contentarea' + contentarea);	
			wpml_scroll('#contentareabox' + contentarea);		
			contentarea++;
		}
	});
}

/*jQuery(document).ready(function() {
	
	newsletters_focus('#title');
	
	jQuery('#title').on('keyup', function(e) {
		jQuery('.newsletters-preview-subject').html(jQuery(this).val());
	});
	
	jQuery('#fromname').on('change', function(e) {
		jQuery('.newsletters-preview-fromname').html(jQuery(this).val());
	});
	
	_wpMediaViewsL10n.insertIntoPost = "<?php esc_html_e('Insert into Newsletter', 'wp-mailinglist'); ?>";
	_wpMediaViewsL10n.uploadedToThisPost = "<?php esc_html_e('Uploaded to this Newsletter', 'wp-mailinglist'); ?>";
	
	jQuery('iframe#content_ifr').attr('tabindex', "2");

    jQuery('input:not(:button,:submit),textarea,select').change(function() {    
        window.onbeforeunload = function () {
            if (warnMessage != null) return warnMessage;
        }
    });
    
    if (history_id != false) {
	    setTimeout(function() {
	    	newsletters_autosave();
	    }, 30000);
    }
    
    setTimeout(function() {
    	var newsletters_autosave_interval = setInterval(newsletters_autosave, 60000);
    }, 30000);
    
    jQuery(':submit').click(function(e) {
        warnMessage = null;
    });
});*/
</script>