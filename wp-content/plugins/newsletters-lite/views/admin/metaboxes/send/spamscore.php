<div id="spamscore_result">
	<?php if (!empty($_POST['spamscore'])) : ?>
		<iframe width="100%" style="width:100%;" frameborder="0" scrolling="no" class="autoHeight widefat" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_gauge&value=' . sanitize_text_field(wp_unslash($_POST['spamscore'])) . '&security=' . wp_create_nonce('gauge'))) ?>"></iframe>
	<?php else : ?>
		<p style="text-align:center;"><?php esc_html_e('Click "Check Now" to test', 'wp-mailinglist'); ?></p>
	<?php endif; ?>
</div>

<p style="text-align:center;">
	<button class="button button-secondary" id="spamscorerunnerbutton" name="spamscorerunnerbutton" value="1" onclick="newsletters_autosave(); return false;">
		<?php esc_html_e('Check Now', 'wp-mailinglist'); ?>
		<span id="spamscorerunnerloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
	</button>
</p>

<script type="text/javascript">
var spamscorerequest = false;

    <?php $history_id = (empty($_POST['ishistory'])) ? sanitize_text_field(isset($_GET['id']) ? $_GET['id'] : 0) : sanitize_text_field($_POST['ishistory']); ?>

<?php if (!empty($history_id)) : ?>
var history_id = "<?php echo esc_js($history_id); ?>";
<?php else : ?>
var history_id = false;
<?php endif; ?>

function spamscorerunner() {
	jQuery('#spamscore_report_link_holder').hide();
	jQuery('iframe#content_ifr').attr('tabindex', "2");
	var formvalues = jQuery('form#post').serialize();
	var content = jQuery("iframe#content_ifr").contents().find("body#tinymce").html();
	
	if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
		tinyMCE.triggerSave();
	}
	
	if (spamscorerequest) { spamscorerequest.abort(); }
	jQuery('#spamscorerunnerbutton').attr('disabled', "disabled");
	jQuery('#spamscorerunnerloading').show();
	
	jQuery('#sendbutton, #sendbutton2').prop('disabled', true);
	//jQuery('#savedraftbutton, #savedraftbutton2').prop('disabled', true);
	
	spamscorerequest = jQuery.ajax({
		data: formvalues,
		dataType: 'xml',
		url: newsletters_ajaxurl + 'action=newsletters_spamscorerunner&security=<?php echo esc_html( wp_create_nonce('spamscorerunner')); ?>',
		type: "POST",
		success: function(response) {
			succ = jQuery("success", response).text();
			report = jQuery("report", response).text();
			score = jQuery("score", response).text();
			output = jQuery("output", response).text();
			jQuery('#spamscore_result').html(output);
		},
		complete: function(response) {
			jQuery('#spamscorerunnerloading').hide();
			jQuery('#spamscorerunnerbutton').removeAttr('disabled');
		},
		cache: true
	});
	
	jQuery('#sendbutton, #sendbutton2').prop('disabled', false);
	//jQuery('#savedraftbutton, #savedraftbutton2').prop('disabled', false);
}

jQuery(document).ready(function() {
	/*setTimeout(function() {
		if (history_id != false) {
			spamscorerunner();
		}
	
		setInterval(spamscorerunner, 60000);
	}, 3000);*/
});
</script>