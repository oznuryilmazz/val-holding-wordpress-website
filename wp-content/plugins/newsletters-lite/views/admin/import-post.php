<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Importing Subscribers', 'wp-mailinglist'); ?></h2>
	
	<?php if (!empty($subscribers)) : ?>		
		<p class="newsletters_importajaxcount"><span id="importajaxcount"><strong><span id="importajaxcountinside" class="newsletters_success">0</span></strong></span> <span id="importajaxfailedcount">(<strong><span id="importajaxfailedcountinside" class="newsletters_error">0</span></strong> failed)</span> <?php esc_html_e('out of', 'wp-mailinglist'); ?> <strong><?php echo count($subscribers); ?></strong> <?php esc_html_e('subscribers have been imported.', 'wp-mailinglist'); ?></p>
		
		<div id="importprogressbar"></div>
		
		<p class="submit">
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><i class="fa fa-arrow-left"></i> <?php esc_html_e('Back', 'wp-mailinglist'); ?></a>
			<a href="" onclick="cancelimporting(); return false;" id="cancelimporting" disabled="disabled" style="display:none;" class="button-secondary"><i class="fa fa-pause"></i> <?php esc_html_e('Pause', 'wp-mailinglist'); ?></a>
			<a href="" onclick="startimporting(); return false;" id="startimporting" disabled="disabled" class="button-primary"><i class="fa fa-refresh fa-spin"></i> <?php esc_html_e('Reading data, please wait', 'wp-mailinglist'); ?></a>
			<span id="import_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
			<span id="importmore" style="display:none;"><a href="?page=<?php echo $this -> sections -> importexport; ?>" id="" class="button-secondary"><?php esc_html_e('Import More', 'wp-mailinglist'); ?></a></span>
		</p>
		
		<div id="confirmation_subject" style="display:none;"><?php echo esc_html($confirmation_subject); ?></div>
		<div id="confirmation_email" style="display:none;"><?php echo esc_html($confirmation_email); ?></div>
		
		<h3 style="display:none;"><?php esc_html_e('Subscribers Imported', 'wp-mailinglist'); ?></h3>
		<div id="importajaxsuccessrecords" class="scroll-list" style="display:none;"><!-- successful records --></div>
		
		<h3 style="display:none;"><?php esc_html_e('Failed Subscribers', 'wp-mailinglist'); ?></h3>
		<div id="importajaxfailedrecords" class="scroll-list" style="display:none;"><!-- failed records --></div>
		
		<div id="importajaxresponse"><!-- response here --></div>
		
		<script type="text/javascript">
		var allsubscribers = [];
			
		jQuery(document).ready(function() {		
			<?php if (!empty($subscribers)) : ?>
				<?php foreach ($subscribers as $subscriber) : ?>
					allsubscribers.push(<?php echo wp_json_encode($subscriber); ?>);
				<?php endforeach; ?>
			<?php endif; ?>
			
			requestArray = new Array();
			cancelimport = "N";
			importingnumber = 50;
			confirmation_subject = jQuery('#confirmation_subject').html();
			confirmation_email = jQuery('#confirmation_email').html(); 
			import_preventbu = "<?php echo (!empty($_POST['import_preventbu'])) ? 'Y' : 'N'; ?>";
			import_overwrite = "<?php echo (!empty($_POST['import_overwrite'])) ? 'Y' : 'N'; ?>";
			jQuery('#startimporting').removeAttr('disabled').html('<i class="fa fa-play"></i> <?php echo addslashes(__("Start Importing", 'wp-mailinglist')); ?>');
		});
		
		function cancelimporting() {
			cancelimport = "Y";
			jQuery('#cancelimporting').attr('disabled', "disabled");
			jQuery('#startimporting').removeAttr('disabled').attr('onclick', 'resumeimporting(); return false;').html('<i class="fa fa-play"></i> <?php echo addslashes(__('Resume Importing', 'wp-mailinglist')); ?>');
			jQuery('#import_loading').hide();
			
			for (var r = 0; r < requestArray.length; r++) {
				requestArray[r].abort();
			}
		}
		
		function startimporting() {
			jQuery('#cancelimporting').removeAttr('disabled').show();
			jQuery('#startimporting').attr('disabled', "disabled").html('<i class="fa fa-play"></i> <?php echo addslashes(__('Importing Now', 'wp-mailinglist')); ?>');
			jQuery('#import_loading').show();

			subscribercount = allsubscribers.length;
			subscribers = allsubscribers;
			completed = 0;
			cancelimport = "N";
			requests = 0;
			imported = 0;
			failed = 0;
			
			jQuery('#importprogressbar').progressbar({value:0});
			
			var importsubscribers = [];
			var i = 0;
			
			while (subscribers.length > i) {
				importsubscribers.push(subscribers[i]);				
				if (importsubscribers.length == importingnumber || (i + 1) >= subscribers.length) {
					importmultiple(importsubscribers);															
					importsubscribers = [];
				}
				
				i++;
			}
		}
		
		function resumeimporting() {
			cancelimport = "N";
			jQuery('#startimporting').attr('disabled', "disabled").html('<i class="fa fa-play"></i> <?php echo addslashes(__('Importing Now', 'wp-mailinglist')); ?>');
			jQuery('#cancelimporting').removeAttr('disabled');
			jQuery('#import_loading').show();
			
			var importsubscribers = [];
			var i = (completed - 1);
			requests = i;
			
			while (subscribers.length > i) {
				importsubscribers.push(subscribers[i]);				
				if (importsubscribers.length == importingnumber || (i + 1) >= subscribers.length) {
					importmultiple(importsubscribers);								
					importsubscribers = [];
				}
				
				i++;
			}
		}
		
		function importmultiple(importsubscribers) {
			if (requests >= subscribercount || cancelimport == "Y") { return; }
		
			requests += importsubscribers.length;
		
			requestArray.push(jQuery.post(newsletters_ajaxurl + 'action=newsletters_importmultiple&security=<?php echo esc_html( wp_create_nonce('importmultiple')); ?>', {subscribers:importsubscribers, import_preventbu:import_preventbu, import_overwrite:import_overwrite, confirmation_subject:confirmation_subject, confirmation_email:confirmation_email}, function(response) {
				var importdata = response.split("<||>");
				
				for (d = 0; d < importdata.length; d++) {
					var response = importdata[d];
					if (response != "") {
						var data = response.split('<|>');
						
						var success = data[0];
						var email = data[1];
						var message = data[2];
													
						if (success == "Y") {
							imported++;
							
							if ((imported + failed) <= subscribercount) {
								jQuery('#importajaxcountinside').text(imported);
								jQuery('#importajaxsuccessrecords').prepend('<div class="ui-state-highlight ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-check"></i> ' + email + '</p></div>').fadeIn().prev().fadeIn();
							}
						} else {
							failed++;
							
							if ((imported + failed) <= subscribercount) {
								jQuery('#importajaxfailedcountinside').text(failed);
								jQuery('#importajaxfailedrecords').prepend('<div class="ui-state-error ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-exclamation-triangle"></i> ' + email + ' - ' + message + '</p></div>').fadeIn().prev().fadeIn();
							}
						}
						
						completed++;
						var value = (completed * 100) / subscribercount;
						jQuery("#importprogressbar").progressbar("value", value);
					}
				}
			}).done(function(response) {							 			
				if (completed >= subscribercount) {
					jQuery('#cancelimporting').hide();
					warnMessage = null;
					jQuery('#startimporting').html('<?php echo addslashes(__('Continue to Subscribers', 'wp-mailinglist')); ?> <i class="fa fa-arrow-right"></i>').removeAttr('disabled').removeAttr('onclick').attr("href", "?page=<?php echo esc_html( $this -> sections -> subscribers); ?>");
					jQuery('#importmore').show();
					jQuery('#import_loading').hide();
				}
			}).error(function(response) {
				failed += importsubscribers.length;
				completed += importsubscribers.length;
			}).always(function(response) {
				
			}));
		}
		
		var warnMessage = "<?php esc_html_e('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', 'wp-mailinglist'); ?>";

		jQuery(document).ready(function() {
		    window.onbeforeunload = function () {
		        if (warnMessage != null) return warnMessage;
		    }
		});
		</script>
	<?php else : ?>
		<p class="newsletters_error"><?php esc_html_e('No subscribers are available for import, please try again.', 'wp-mailinglist'); ?></p>
		<p>
			<a href="javascript:history.go(-1);" class="button button-primary" onclick=""><?php esc_html_e('&laquo; Back', 'wp-mailinglist'); ?></a>
		</p>
	<?php endif; ?>
</div>