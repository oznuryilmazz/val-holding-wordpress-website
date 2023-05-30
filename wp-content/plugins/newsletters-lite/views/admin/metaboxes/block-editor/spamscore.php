<?php // phpcs:ignoreFile ?>
<div id="spamscore_result">
	<?php if (!empty($_POST['spamscore'])) : ?>
		<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . sanitize_text_field(wp_unslash($_POST['spamscore'])) . '&security=' . wp_create_nonce('gauge'))) ?>"></iframe>
	<?php else : ?>
		<p style="text-align:center;"><?php esc_html_e('Click "Check Now" to test', 'wp-mailinglist'); ?></p>
	<?php endif; ?>
</div>

<p style="text-align:center;">
	<button type="button" class="button button-secondary" id="spamscore_button" name="spamscore_button" value="1">
		<?php esc_html_e('Check Now', 'wp-mailinglist'); ?>
		<span id="spamscore_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
	</button>
</p>

<script type="text/javascript">
var spamscorerequest = false;

<?php $history_id = (empty($_POST['ishistory'])) ? sanitize_text_field(wp_unslash($_GET['id'])) : sanitize_text_field(wp_unslash($_POST['ishistory'])); ?>

<?php if (!empty($history_id)) : ?>
var history_id = "<?php echo esc_html( $history_id); ?>";
<?php else : ?>
var history_id = false;
<?php endif; ?>

/*(function($) {
	
	var $spamscore_request, 
	$spamscore_button = $('#spamscore_button'), 
	$spamscore_result = $('#spamscore_result'), 
	$spamscore_loading = $('#spamscore_loading');
	
	$spamscore_button.on('click', function() {
		newsletters_spamscore_blockeditor();
	});
	
	var newsletters_spamscore_blockeditor = function() {
		if ($spamscore_request) {
			$spamscore_request.abort();
		}
		
		$('#spamscore_report_link_holder').hide();
		//$('iframe#content_ifr').attr('tabindex', "2");
		var formvalues = $('form#post').serializeToJSON();
		var content = $("iframe#content_ifr").contents().find("body#tinymce").html();		
		if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
			tinyMCE.triggerSave();
		}
		
		console.log(formvalues);
		delete formvalues.action;
		
		var $content = typeof tinymce !== 'undefined' && tinymce.get('content');
		
		if (spamscorerequest) { spamscorerequest.abort(); }
		$spamscore_button.prop('disabled', true);
		$spamscore_loading.show();
		
		$('#sendbutton, #sendbutton2').prop('disabled', true);
		//$('#savedraftbutton, #savedraftbutton2').prop('disabled', true);
		
		$spamscore_request = $.ajax({			
			url: newsletters_ajaxurl + 'action=newsletters_spamscore_blockeditor',
			data: formvalues,
			dataType: 'json',
			type: "POST"
		}).done(function(response) {
			$spamscore_result.html(response.output);
		}).fail(function() {
			$spamscore_result.html('<p>An error occurred, try again.</p>');
		}).always(function() {
			$spamscore_button.prop('disabled', false);
			$spamscore_loading.hide();
		});
		
		$('#sendbutton, #sendbutton2').prop('disabled', false);
		//$('#savedraftbutton, #savedraftbutton2').prop('disabled', false);
	}
})(jQuery);*/
</script>