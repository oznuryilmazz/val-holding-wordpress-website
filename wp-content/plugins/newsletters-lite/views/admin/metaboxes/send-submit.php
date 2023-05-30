<?php // phpcs:ignoreFile ?>
<!-- Submit box with some settings -->

<?php
	
$previewemail = (empty($_REQUEST['previewemail'])) ? $this -> get_option('adminemail') :  sanitize_text_field(wp_unslash($_REQUEST['previewemail']));
	
?>

<div class="submitbox" id="submitpost">
	<div id="minor-publishing">
		<div id="minor-publishing-actions">
			<div id="save-action">
				<input id="savedraftbutton" style="float:left;" type="submit" name="draft" value="<?php esc_html_e('Save Draft', 'wp-mailinglist'); ?>" class="button button-highlighted" />
			</div>
			<div id="preview-action">
				<input type="button" name="previewemail_button" id="previewemail_button" class="button" value="<?php echo apply_filters('newsletters_admin_createnewsletter_sendpreview_text', __('Send Preview', 'wp-mailinglist')); ?>" />
			</div>
			<br class="clear" />
		</div>
		<div id="misc-publishing-actions">
			<div class="misc-pub-section" id="previewemail_div" style="display:none;">
				<div class="form-field">
					<label for="previewemail"><?php esc_html_e('Send To:', 'wp-mailinglist'); ?></label>
					<?php echo ( $Html -> help(__('Specify the email address(es) to send this newsletter as a preview to. For multiple emails, separate them with a comma (,)', 'wp-mailinglist'))); ?>
					<input type="text" name="previewemail" value="<?php echo esc_attr(wp_unslash($previewemail)); ?>" id="previewemail" />
				</div>
				<p>
					<input class="button button-primary" type="submit" name="preview" value="<?php esc_html_e('Send', 'wp-mailinglist'); ?>" id="previewemailbutton" />
					<a href="" id="cancelpreviewemail"><?php esc_html_e('Cancel', 'wp-mailinglist'); ?></a>
				</p>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#previewemail').on('keypress', function(event) {
						if (event.which == 13 || event.keyCode == 13) {
							event.preventDefault();
							jQuery('#previewemailbutton').trigger('click');
						}
					});
				});
				</script>
			</div>
			<div class="misc-pub-section sendfromwrapper">
				<input type="hidden" id="from" name="from" value="<?php echo (!empty($_POST['from'])) ? esc_html_e($_POST['from']) : ''; ?>" />
				<input type="hidden" id="fromname" name="fromname" value="<?php echo (!empty($_POST['fromname'])) ? esc_html_e($_POST['fromname']) : ''; ?>" />
			
                <span id="sendfrom">
					<i class="fa fa-user fa-fw pull-left"></i> <?php esc_html_e('Send from', 'wp-mailinglist'); ?>
					<span id="sendfrom-edit"><a id="sendfrom-edit-link" href="" onclick="jQuery('#sendfromdiv').show(); jQuery('#smtpfromname').focus(); jQuery(this).hide(); return false;"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a></span>
					<?php echo ( $Html -> help(__('Change the From Name and From Address that this email is being sent from as it will appear in the email/webmail clients of the recipients.', 'wp-mailinglist'))); ?>
				</span>
                <span id="sendfrom-value">
					<abbr title="<?php echo (empty($_POST['from'])) ? esc_html_e($this -> get_option('smtpfrom')) : esc_html_e($_POST['from']); ?>"><?php echo (empty($_POST['fromname'])) ? esc_html_e($this -> get_option('smtpfromname')) :  esc_html_e($_POST['fromname']); ?></abbr>
				</span>

             
				

                <div id="sendfromdiv" class="" style="display:none;">
                    <div class="form-field">
                        <label for="smtpfromname"><?php _e('From Name:', 'wp-mailinglist'); ?></label>
                        <input type="text" name="smtpfromname" value="<?php echo (empty($_POST['fromname'])) ? __($this -> get_option('smtpfromname')) : esc_html_e($_POST['fromname']); ?>" id="smtpfromname" />
                    </div>
                    <div class="form-field">
                        <label for="smtpfrom"><?php _e('From Email:', 'wp-mailinglist'); ?></label>
                        <input type="text" name="smtpfrom" value="<?php echo (empty($_POST['from'])) ? __($this -> get_option('smtpfrom')) : esc_html_e($_POST['from']); ?>" id="smtpfrom" />
                    </div>
                    <p>
                        <input type="button" name="changesendfrom" id="changesendfrom" class="button button-secondary" value="<?php _e('Ok', 'wp-mailinglist'); ?>" />
                        <a href="" id="cancelsendfrom"><?php _e('Cancel', 'wp-mailinglist'); ?></a>
                    </p>
                </div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#cancelpreviewemail').click(function() { jQuery('#previewemail_div').hide(); return false; });
					jQuery('#previewemail_button').click(function() { jQuery('#previewemail_div').show(); jQuery('#previewemail').focus(); });
					
					jQuery('#changesendfrom').click(function() { changesendfrom(); });
					jQuery('#cancelsendfrom').click(function() { jQuery('#sendfromdiv').hide(); jQuery('#sendfrom-edit-link').show(); return false; });			
					
					jQuery('#sendfromdiv input').keypress(function(e) {
						var code = (e.keyCode ? e.keyCode : e.which);
						if (code == 13) {
							changesendfrom();
							return false;
						}
					});
				});
				
				function changesendfrom() {
					var fromname = jQuery('#smtpfromname').val();
					var fromemail = jQuery('#smtpfrom').val();
					jQuery('#from').val(fromemail);
					jQuery('#fromname').val(fromname).trigger('change');
					jQuery('#sendfrom-value').html('<abbr title="' + fromemail + '">' + fromname + '</abbr>');
					jQuery('#sendfromdiv').hide();
					jQuery('#sendfrom-edit-link').show();
				}
				</script>
			</div>
			
			<?php if (apply_filters('newsletters_admin_createnewsletter_recurringsettings', true)) : ?>
				<div class="misc-pub-section">
					<input type="hidden" name="sendrecurring" id="sendrecurringfield" value="<?php echo (!empty($_POST['sendrecurring']) && $_POST['sendrecurring'] == "Y") ? 'Y' : ''; ?>" />
				
					<span id="sendrecurring">
						<i class="fa fa-retweet fa-fw pull-left"></i>
						<?php if (!empty($_POST['sendrecurring']) && $_POST['sendrecurring'] == "Y") : ?>
							<span id="sendrecurring-value">
								<?php echo sprintf(__('Send every %s %s', 'wp-mailinglist'), sanitize_text_field(wp_unslash($_POST['sendrecurringvalue'])), sanitize_text_field(wp_unslash($_POST['sendrecurringinterval']), sanitize_text_field(wp_unslash($_POST['sendrecurringlimit'])))); ?>
								<?php if (!empty($_POST['sendrecurringlimit'])) : ?><?php echo sprintf(__(', %s times', 'wp-mailinglist'), sanitize_text_field(wp_unslash($_POST['sendrecurringlimit']))); ?><?php endif; ?>
							</span>
						<?php else : ?>
							<span id="sendrecurring-value"><?php esc_html_e('Send Once', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
						<span id="sendrecurring-edit"><a id="sendrecurring-edit-link" href="" onclick="jQuery('#sendrecurringdiv').show(); jQuery(this).hide(); return false;"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a></span>
						<?php echo ( $Html -> help(__('This newsletter can be automatically repeated at a specified interval, starting on a specific date and the repeat can also be limited. Click "Edit" to configure this as a recurring newsletter. To cancel, empty all the fields and click "Ok".', 'wp-mailinglist'))); ?>
					</span>
					
					<div id="sendrecurringdiv" class="" style="display:none;">
						<div class="form-field">
                            <?php _e('Every', 'wp-mailinglist'); ?>
                            <input type="text" name="sendrecurringvalue" value="<?php echo isset($_POST['sendrecurringvalue']) ? esc_attr(wp_unslash($_POST['sendrecurringvalue'])) : ""; ?>" id="sendrecurringvalue" class="widefat" style="width:45px;" />
							<select name="sendrecurringinterval" id="sendrecurringinterval">
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "minutes") ? 'selected="selected"' : ''; ?> value="minutes"><?php esc_html_e('Minutes', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "hours") ? 'selected="selected"' : ''; ?> value="hours"><?php esc_html_e('Hours', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "days") ? 'selected="selected"' : ''; ?> value="days"><?php esc_html_e('Days', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "weeks") ? 'selected="selected"' : ''; ?> value="weeks"><?php esc_html_e('Weeks', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "months") ? 'selected="selected"' : ''; ?> value="months"><?php esc_html_e('Months', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($_POST['sendrecurringinterval']) && $_POST['sendrecurringinterval'] == "years") ? 'selected="selected"' : ''; ?> value="years"><?php esc_html_e('Years', 'wp-mailinglist'); ?></option>
							</select>
							<?php echo ( $Html -> help(__('Choose the interval at which this newsletter should be sent again. All data of the newsletter such as the list(s), content, template, etc. will be reused as configured.', 'wp-mailinglist'))); ?>
						</div>
						<div class="form-field">
							<?php esc_html_e('Starting', 'wp-mailinglist'); ?>
							<?php $sendrecurringdate = (empty($_POST['sendrecurringdate'])) ? $Html -> gen_date("Y-m-d H:i") : $Html -> gen_date("Y-m-d H:i", strtotime(sanitize_text_field(wp_unslash($_POST['sendrecurringdate'])))); ?>
							<input type="text" name="sendrecurringdate" value="<?php echo esc_html( $sendrecurringdate); ?>" id="sendrecurringdate" class="widefat" style="width:140px;" />
							<?php echo ( $Html -> help(__('Specify a starting date and time for the first recurring instance to run. In the format YYYY-MM-DD HH:MM', 'wp-mailinglist'))); ?>
						</div>
						<div class="form-field">
							<?php echo sprintf(__('Repeat %s times', 'wp-mailinglist'), '<input type="text" name="sendrecurringlimit" value="' . esc_attr(wp_unslash(sanitize_text_field(wp_unslash($_POST['sendrecurringlimit'])))) . '" id="sendrecurringlimit" class="widefat" style="width:45px;" />'); ?>
							<?php echo ( $Html -> help(__('How many times should this newsletter be sent? Leave this field empty for unlimited/inifinite, else specify a number.', 'wp-mailinglist'))); ?>
						</div>
						<p>
							<input type="button" name="changesendrecurring" id="changesendrecurring" class="button button-secondary" value="<?php esc_html_e('Ok', 'wp-mailinglist'); ?>" />
							<a href="" id="cancelsendrecurring"><?php esc_html_e('Cancel', 'wp-mailinglist'); ?></a>
						</p>
					</div>
					
					<script type="text/javascript">
					jQuery(document).ready(function() {					
						jQuery('#changesendrecurring').click(function() { changesendrecurring(); });
						jQuery('#cancelsendrecurring').click(function() { jQuery('#sendrecurringdiv').hide(); jQuery('#sendrecurring-edit-link').show(); return false; });			
						
						jQuery('#sendrecurringdiv input').keypress(function(e) {
							var code = (e.keyCode ? e.keyCode : e.which);
							if (code == 13) {
								changesendrecurring();
								return false;
							}
						});
					});
					
					function changesendrecurring() {
						var sendrecurringvalue = jQuery('#sendrecurringvalue').val();
						var sendrecurringinterval = jQuery('#sendrecurringinterval').val();
						var sendrecurringlimit = jQuery('#sendrecurringlimit').val();
						
						if (sendrecurringvalue != "" && sendrecurringinterval != "") {
							sendrecurringhtml = 'Send ';
							sendrecurringhtml += ' every ' + sendrecurringvalue + ' ' + sendrecurringinterval + '';
							if (sendrecurringlimit != "") { sendrecurringhtml += ' , ' + sendrecurringlimit + ' times'; }
							jQuery('#sendrecurring-value').html(sendrecurringhtml);
							jQuery('#sendrecurringfield').val('Y');
						} else {
							jQuery('#sendrecurringfield').val('');
						}
						
						// Date stuff
						var currentdate = new Date(Date.parse('<?php echo current_time('Y/m/d H:i'); ?>'));
						
						var sendrecurringdate = jQuery('#sendrecurringdate').val();
						var sendrecurringdate_object = new Date(Date.parse(sendrecurringdate));
						
						if (sendrecurringdate_object.getTime() > currentdate.getTime()) {
							var sendrecurringyear = sendrecurringdate_object.getFullYear();
							var sendrecurringmonth = ("0" + (sendrecurringdate_object.getMonth() + 1)).slice(-2);
							var sendrecurringday = ("0" + sendrecurringdate_object.getDate()).slice(-2);
							var sendrecurringhours = ("0" + sendrecurringdate_object.getHours()).slice(-2);
							var sendrecurringminutes = ("0" + sendrecurringdate_object.getMinutes()).slice(-2);
							
							jQuery('#aa').val(sendrecurringyear);
							jQuery('#mm').val(sendrecurringmonth);
							jQuery('#jj').val(sendrecurringday);
							jQuery('#hh').val(sendrecurringhours);
							jQuery('#mn').val(sendrecurringminutes);	
						}
						
						update_timestamp();
						
						jQuery('#sendrecurringdiv').hide();
						jQuery('#sendrecurring-edit-link').show();
					}
					</script>
				</div>
			<?php endif; ?>
			
			<!-- Format (HTML/TEXT) of the newsletter -->
			<div class="misc-pub-section">
				<i class="fa fa-code"></i> 
				<label>
					<?php esc_html_e('Format:', 'wp-mailinglist'); ?>
					<select name="format">
						<option <?php echo (!empty($_POST['format']) && $_POST['format'] == "html") ? 'selected="selected"' : ''; ?> value="html"><?php esc_html_e('HTML', 'wp-mailinglist'); ?></option>
						<option <?php echo (!empty($_POST['format']) && $_POST['format'] == "text") ? 'selected="selected"' : ''; ?> value="text"><?php esc_html_e('TEXT', 'wp-mailinglist'); ?></option>
					</select>
				</label>
			</div>
				
    		<div class="misc-pub-section curtime misc-pub-section-last">
    			<?php if ($this -> get_option('sendingprogress') == "Y") : ?>
            		<i class="fa fa-clock-o fa-fw"></i> <span id="timestamp"><?php esc_html_e('Send immediately', 'wp-mailinglist'); ?></span>
            	<?php else : ?>
            		<i class="fa fa-clock-o fa-fw"></i> <span id="timestamp"><?php esc_html_e('Queue immediately', 'wp-mailinglist'); ?></span>
            	<?php endif; ?>
            	
            	<style type="text/css">
	            #timestamp:before {
		            content: "" !important;
	            }
	            </style>
            	
            	<a href="" onclick="jQuery('#timestampdiv').show(); jQuery(this).hide(); return false;" class="edit-timestamp hide-if-no-js" style="display:inline;"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a>
            	
            	<?php echo ( $Html -> help(__('You can choose to send this newsletter immediately or you can click the "Edit" link to change the date/time to a future date/time at which it will be sent.', 'wp-mailinglist'))); ?>
            	
				<div id="timestampdiv" class="" style="display:none;">
					<p class="howto"><?php echo sprintf(__('Current time is %s', 'wp-mailinglist'), '<strong>' . $Html -> gen_date("Y-m-d H:i:s") . '</strong>'); ?></p>
					<?php touch_time(0, 0, 0, 0); ?>
					<?php $senddate = (empty($_POST['senddate']) || strtotime(sanitize_text_field(wp_unslash($_POST['senddate']))) <= current_time('timestamp')) ? $Html -> gen_date("Y-m-d H:i:s") :  sanitize_text_field(wp_unslash($_POST['senddate'])); ?>
					<input type="hidden" name="sendtype" id="sendtype" value="<?php echo ($this -> get_option('sendingprogress') == "Y") ? 'send' : 'queue'; ?>" />
					<input type="hidden" name="senddate" id="senddate" value="<?php echo esc_attr($senddate); ?>" />
					<input type="hidden" name="scheduled" id="scheduled" value="N" />
				</div>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {				
					alwaysqueue = <?php if ($this -> get_option('sendingprogress') == "N") : ?>true<?php else : ?>false<?php endif; ?>;
					jQuery('.save-timestamp').click(update_timestamp);
					
					jQuery('.cancel-timestamp').click(function() { 
						jQuery('#timestampdiv').hide(); 
						jQuery('.edit-timestamp').show(); 
					});
					
					<?php if (!empty($senddate) && strtotime($senddate) > current_time('timestamp')) : ?>
						jQuery('#scheduled').val("Y");
						jQuery('#aa').val('<?php echo esc_html( $Html -> gen_date("Y", strtotime($senddate))); ?>');
						jQuery('#mm').val('<?php echo esc_html( $Html -> gen_date("m", strtotime($senddate))); ?>');
						jQuery('#jj').val('<?php echo esc_html( $Html -> gen_date("d", strtotime($senddate))); ?>');
						jQuery('#hh').val('<?php echo esc_html( $Html -> gen_date("H", strtotime($senddate))); ?>');
						jQuery('#mn').val('<?php echo esc_html( $Html -> gen_date("i", strtotime($senddate))); ?>');
						update_timestamp();
					<?php endif; ?>
				});
				
				function update_timestamp() { 										
					var date = new Date(Date.parse('<?php echo current_time('Y/m/d H:i:s'); ?>'));
					var year = date.getFullYear();
					var month = ("0" + (date.getMonth() + 1)).slice(-2);
					var day = ("0" + date.getDate()).slice(-2);
					var hours = ("0" + date.getHours()).slice(-2);
					var minutes = ("0" + date.getMinutes()).slice(-2);
					var today = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
					
					var year = jQuery('#aa').val();
					var month = (jQuery('#mm').val() - 1);
					var day = jQuery('#jj').val();
					var hours = jQuery('#hh').val();
					var minutes = jQuery('#mn').val();						
					var senddate = new Date(year, month, day, hours, minutes, date.getSeconds());
					
					if (senddate.getTime() > date.getTime()) {
						jQuery('#timestamp').html('<?php esc_html_e('Schedule for:', 'wp-mailinglist'); ?> <strong>' + year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes + '</strong>');
						jQuery('#sendbutton, #sendbutton2').attr("name", "queue").attr("value", "<?php echo addslashes(__('Schedule Newsletter', 'wp-mailinglist')); ?>");
						jQuery('#sendtype').attr("value", "schedule");
						jQuery('#scheduled').attr("value", "Y");
					} else if (alwaysqueue == true) {
						jQuery('#timestamp').html('<?php esc_html_e('Queue immediately', 'wp-mailinglist'); ?>');
						jQuery('#sendbutton, #sendbutton2').attr("name", "queue").attr("value", "<?php echo addslashes(__('Queue Newsletter', 'wp-mailinglist')); ?>");
						jQuery('#sendtype').attr("value", "queue");
						jQuery('#scheduled').attr("value", "N");
					} else {
						jQuery('#timestamp').html('<?php esc_html_e('Send immediately', 'wp-mailinglist'); ?>');
						jQuery('#sendbutton, #sendbutton2').attr("name", "send").attr("value", "<?php echo addslashes(__('Send Newsletter', 'wp-mailinglist')); ?>");
						jQuery('#sendtype').attr("value", "send");
						jQuery('#scheduled').attr("value", "N");
					}
					
					jQuery('#senddate').attr("value", year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes);
					jQuery('#timestampdiv').hide();
					jQuery('.edit-timestamp').show();
				}
				</script>
            </div>
        </div>
		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">
		<?php if (!empty($_POST['ishistory'])) : ?>
			<div id="delete-action">
				<a href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=delete&amp;id=<?php echo sanitize_text_field(wp_unslash($_POST['ishistory'])); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this newsletter from your history?', 'wp-mailinglist'); ?>')) { return false; }" title="<?php esc_html_e('Remove this newsletter from your history', 'wp-mailinglist'); ?>" class="submitdelete deletion"><?php esc_html_e('Delete Email', 'wp-mailinglist'); ?></a>
				<?php echo ( $Html -> help(__('Since this is a saved sent/draft email, you can click this "Delete Email" link to permanently delete it from your history. Please note that this is undoable.', 'wp-mailinglist'))); ?>
			</div>
		<?php endif; ?>
		<div id="publishing-action">
			<?php $sendbutton = ($this -> get_option('sendingprogress') == "N") ? __('Queue Newsletter', 'wp-mailinglist') : __('Send Newsletter', 'wp-mailinglist'); ?>
			<input class="button button-primary button-large" type="submit" name="send" id="sendbutton" value="<?php echo esc_html( $sendbutton); ?>" />
		</div>
		<br class="clear" />
		<?php
			
		$sendingprogress = $this -> get_option('sendingprogress');
		
		?>
		<div class="publishing-action-inside">
			<label><input <?php echo (!empty($sendingprogress) && $sendingprogress == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="sendingprogress" value="1" id="sendingprogress" /> <?php esc_html_e('Use progress bar to queue/send', 'wp-mailinglist'); ?></label>
		</div>
	</div>
</div>