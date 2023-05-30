<?php // phpcs:ignoreFile ?>
<script type="text/javascript">
	
(function($) {	
	$.fn.newsletters_blockeditor = function() {
		
		var $document = $(document),
		$spamscore_button = $('#spamscore_button'), 
		$spamscore_loading = $('#spamscore_loading'), 
		$spamscore_result = $('#spamscore_result'),
		$preview_button = $('#preview_button'), 
		$preview_loading = $('#preview_loading'), 
		$autosave_request, 
		$form = $('form#post'), 
		$content, 
		$mailinglists = [],
		$spamscore_interval, 
		$spamscore_report_link = $('#spamscore_report_link_holder');
		
		$($spamscore_button).add($preview_button).add('input[name="newsletters_theme_id"]').on('click', function() {		
			newsletters_autosave();
		});
		
		var newsletters_autosave = function() {				
			if ($autosave_request) {
				$autosave_request.abort();
			}
			
			$content = newsletters_tinymce_content('content');
			
			if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function") {
				tinyMCE.triggerSave();
			}
			
			//$formdata = $('input[name!="action"]', $form).serialize();
			
			$formdata = $('form#post').serializeToJSON();			
			
			$('input[name="newsletters_mailinglists[]"]:checked').each(function () {
			    $mailinglists.push(parseInt($(this).val()));
			});
			$formdata.newsletters_mailinglists = $mailinglists;
			$formdata.content = $content;
			delete $formdata.action;
			
			newsletters_autosave_running();
			
			newsletters_autosave_request = $.ajax({
				cache: false,
				data: $formdata,
				dataType: "json",
				url: newsletters_ajaxurl + 'action=newsletters_autosave_blockeditor&security=<?php echo esc_html( wp_create_nonce('newsletters_autosave_blockeditor')); ?>',
				type: "POST",
				success: function(response) {
					
					console.log(response);	
					
					$spamscore_result.html(response.parts.spamscore.output);
					
					//$spamscore_result.html(response.parts.spamscore.output);
					$('#newwindowbutton').prop('disabled', false).attr('href', response.parts.preview.url);
					$('#newsletters_history_id').val(response.history_id); 
					$('#p_id').val(response.post_id);
					$('#edit-slug-box').show();
					$('#sample-permalink').html(response.parts.preview.url);
					$('#view-post-btn a').attr('href', response.parts.preview.url);
					$('#shortlink').attr('value', response.parts.preview.url).val(response.parts.preview.url);
					
					// Update the HTML preview
					if (typeof response.parts.preview.html !== 'undefined') { $('#previewiframe').contents().find('html').html(response.parts.preview.html); }
					var iframeheight = $("#previewiframe").contents().find("html").outerHeight();
					$("#previewiframe").height(iframeheight).css({height: iframeheight}).attr("height", iframeheight);
					
					// Update the TEXT preview
					if (typeof response.parts.preview.text !== 'undefined') { $('#textiframe').contents().find('html').html(response.parts.preview.text); }
					var iframeheight = $("#textiframe").contents().find("html").outerHeight();
					$("#textiframe").height(iframeheight).css({height: iframeheight}).attr("height", iframeheight);
					
					var date = new Date();
					var year = date.getFullYear();
					var month = ("0" + (date.getMonth() + 1)).slice(-2);
					var day = ("0" + date.getDate()).slice(-2);
					var hours = ("0" + date.getHours()).slice(-2);
					var minutes = ("0" + date.getMinutes()).slice(-2);
					var today = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
					var autosavedate = year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes;
					$('#autosave').html('<?php esc_html_e('Draft saved at', 'wp-mailinglist'); ?> ' + autosavedate).show();
					
					newsletters_autosave_done();
				}
			}).error(function(response) {
				newsletters_autosave_done();
			}).always(function() {
				newsletters_autosave_done();
			});
			
			return true;
		}
		
		var newsletters_autosave_running = function() {
			$('#sendbutton, #sendbutton2').prop('disabled', true);	
			$spamscore_report_link.hide();
			$('iframe#content_ifr').attr('tabindex', "2");
			$spamscore_button.prop('disabled', true);
			$spamscore_loading.show();
			$preview_button.prop('disabled', true);
			$preview_loading.show();
		}
		
		var newsletters_autosave_done = function() {
			$('#sendbutton, #sendbutton2').prop('disabled', false);
			//$('#savedraftbutton, #savedraftbutton2').prop('disabled', false);
			
			$spamscore_button.prop('disabled', false);
			$spamscore_loading.hide();
			$preview_button.prop('disabled', false);
			$preview_loading.hide();
			
			warnMessage = null;
		}
		
		// Automatically autosave every so often
		/*setTimeout(function() {
			newsletters_autosave();
	    	var newsletters_autosave_interval = setInterval(newsletters_autosave, 60000);
	    }, 30000);*/
	    
	    $document.on('after-autosave', function(event, data) {
		    newsletters_autosave();
	    });
	}
	
	$(document).ready(function() {
		// Add 'newsletters' class to the wrap
		$('.wrap').addClass('newsletters');
		$('#newsletters_submit').attr('id', "submitdiv");
		
		if (typeof(postboxes) !== "undefined") {
			postboxes.add_postbox_toggles(pagenow);
		}
		
		var $newsletters_blockeditor = $(this).newsletters_blockeditor();
		
		jQuery('input:not(:button,:submit),textarea,select').change(function() {    
	        window.onbeforeunload = function () {
	            if (warnMessage != null) return warnMessage;
	        }
	    });
	    
	    jQuery(':submit').click(function(e) {
	        warnMessage = null;
	    });
	    
	    postL10n.publish = '<?php echo esc_js(__('Send', 'wp-mailinglist')); ?>';
	    postL10n.publishOn = '<?php echo esc_js(__('Send on:', 'wp-mailinglist')); ?>';
	    postL10n.publishOnPast = '<?php echo esc_js(__('Sent on', 'wp-mailinglist')); ?>';
	    postL10n.update = '<?php echo esc_js(__('Send Again', 'wp-mailinglist')); ?>';
	    
	});
	
})(jQuery);

</script>