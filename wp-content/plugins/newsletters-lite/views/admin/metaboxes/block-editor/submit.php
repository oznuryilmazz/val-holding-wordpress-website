<?php // phpcs:ignoreFile ?>
<?php

global $action;

$post_type = $post -> post_type;
$post_type_object = get_post_type_object($post_type);
$can_publish = current_user_can($post_type_object -> cap -> publish_posts);

// Newsletter meta
$newsletters_from = get_post_meta($post -> ID, '_newsletters_from', true);
$newsletters_fromname = get_post_meta($post -> ID, '_newsletters_fromname', true);
$newsletters_format = get_post_meta($post -> ID, '_newsletters_format', true);
$newsletters_sendrecurring = get_post_meta($post -> ID, '_newsletters_sendrecurring', true);
$newsletters_sendrecurringvalue = get_post_meta($post -> ID, '_newsletters_sendrecurringvalue', true);
$newsletters_sendrecurringinterval = get_post_meta($post -> ID, '_newsletters_sendrecurringinterval', true);
$newsletters_sendrecurringlimit = get_post_meta($post -> ID, '_newsletters_sendrecurringlimit', true);
$newsletters_sendrecurringdate = get_post_meta($post -> ID, '_newsletters_sendrecurringdate', true);

?>
		
<div class="submitbox" id="submitpost">		
	<div id="minor-publishing">
		<div style="display:none;">
			<?php submit_button( __( 'Save' ), '', 'save' ); ?>
		</div>
		
		<div id="minor-publishing-actions">
			<div id="save-action">
				<input type="submit" name="savedraft" id="save-draft" value="<?php esc_attr_e('Save Draft'); ?>" class="button" />
				<span class="spinner"></span>
				
				<?php /*<?php if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status ) { ?>
				<input <?php if ( 'private' == $post->post_status ) { ?>style="display:none"<?php } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" class="button" />
				<span class="spinner"></span>
				<?php } elseif ( 'pending' == $post -> post_status && $can_publish ) { ?>
				<input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" class="button" />
				<span class="spinner"></span>
				<?php } ?>*/ ?>
			</div>
			<?php if ( is_post_type_viewable( $post_type_object ) ) : ?>
				<div id="preview-action">
					<?php
					$preview_link = esc_url( get_preview_post_link( $post ) );
					if ( 'publish' == $post -> post_status ) {
						$preview_button_text = __( 'Preview Changes' );
					} else {
						$preview_button_text = __( 'Preview' );
					}
					
					$preview_button = sprintf( '%1$s<span class="screen-reader-text"> %2$s</span>',
						$preview_button_text,
						/* translators: accessibility text */
						__( '(opens in a new window)' )
					);
					?>
					<a class="preview button" href="<?php echo esc_url_raw($preview_link); ?>" target="wp-preview-<?php echo (int) $post->ID; ?>" id="post-preview"><?php echo $preview_button; ?></a>
					<input type="hidden" name="wp-preview" id="wp-preview" value="" />
				</div>
			<?php endif; // public post type ?>
			
			<?php do_action( 'post_submitbox_minor_actions', $post ); ?>
			
			<div class="clear"></div>
		</div><!-- #minor-publishing-actions -->
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
				<input type="hidden" id="newsletters_from" name="newsletters_from" value="<?php echo (!empty($newsletters_from)) ? esc_html($newsletters_from) : ''; ?>" />
				<input type="hidden" id="newsletters_fromname" name="newsletters_fromname" value="<?php echo (!empty($$newsletters_fromname)) ? esc_html($newsletters_fromname) : ''; ?>" />
			
				<span id="sendfrom">
					<i class="fa fa-user fa-fw"></i> <?php esc_html_e('Send from', 'wp-mailinglist'); ?>
					<span id="sendfrom-edit"><a id="sendfrom-edit-link" href="" onclick="jQuery('#sendfromdiv').show(); jQuery('#smtpfromname').focus(); jQuery(this).hide(); return false;"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a></span>
					<?php echo ( $Html -> help(__('Change the From Name and From Address that this email is being sent from as it will appear in the email/webmail clients of the recipients.', 'wp-mailinglist'))); ?>
				</span>
				<span id="sendfrom-value">
					<abbr title="<?php echo (empty($newsletters_from)) ? esc_html($this -> get_option('smtpfrom')) : esc_html($newsletters_from); ?>"><?php echo (empty($newsletters_fromname)) ? esc_html($this -> get_option('smtpfromname')) : esc_html($newsletters_fromname); ?></abbr>
				</span>
				
				<div id="sendfromdiv" class="" style="display:none;">
					<div class="form-field">
						<label for="smtpfromname"><?php esc_html_e('From Name:', 'wp-mailinglist'); ?></label>
						<input type="text" name="smtpfromname" value="<?php echo (empty($newsletters_fromname)) ? esc_html($this -> get_option('smtpfromname')) : esc_html($newsletters_fromname); ?>" id="smtpfromname" />
					</div>
					<div class="form-field">
						<label for="smtpfrom"><?php esc_html_e('From Email:', 'wp-mailinglist'); ?></label>
						<input type="text" name="smtpfrom" value="<?php echo (empty($newsletters_from)) ? esc_html($this -> get_option('smtpfrom')) : esc_html($newsletters_from); ?>" id="smtpfrom" />
					</div>
					<p>
						<input type="button" name="changesendfrom" id="changesendfrom" class="button button-secondary" value="<?php esc_html_e('Ok', 'wp-mailinglist'); ?>" />
						<a href="" id="cancelsendfrom"><?php esc_html_e('Cancel', 'wp-mailinglist'); ?></a>
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
					jQuery('#newsletters_from').val(fromemail);
					jQuery('#newsletters_fromname').val(fromname).trigger('change');
					jQuery('#sendfrom-value').html('<abbr title="' + fromemail + '">' + fromname + '</abbr>');
					jQuery('#sendfromdiv').hide();
					jQuery('#sendfrom-edit-link').show();
				}
				</script>
			</div>
			
			<?php if (apply_filters('newsletters_admin_createnewsletter_recurringsettings', true)) : ?>
				<div class="misc-pub-section">
					<input type="hidden" name="newsletters_sendrecurring" id="newsletters_sendrecurringfield" value="<?php echo (!empty($newsletters_sendrecurring)) ? 1 : ''; ?>" />
				
					<span id="newsletters_sendrecurring">
						<i class="fa fa-retweet fa-fw"></i>
						<?php if (!empty($newsletters_sendrecurring)) : ?>
							<span id="sendrecurring-value">
								<?php echo sprintf(__('Send every %s %s', 'wp-mailinglist'), esc_html($newsletters_sendrecurringvalue), esc_html($newsletters_sendrecurringinterval), esc_html($newsletters_sendrecurringlimit)); ?>
								<?php if (!empty($newsletters_sendrecurringlimit)) : ?><?php echo sprintf(__(', %s times', 'wp-mailinglist'), esc_html($newsletters_sendrecurringlimit)); ?><?php endif; ?>
							</span>
						<?php else : ?>
							<span id="newsletters_sendrecurring-value"><?php esc_html_e('Send Once', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
						<span id="sendrecurring-edit"><a id="newsletters_sendrecurring-edit-link" href="" onclick="jQuery('#newsletters_sendrecurringdiv').show(); jQuery(this).hide(); return false;"><?php esc_html_e('Edit', 'wp-mailinglist'); ?></a></span>
						<?php echo ( $Html -> help(__('This newsletter can be automatically repeated at a specified interval, starting on a specific date and the repeat can also be limited. Click "Edit" to configure this as a recurring newsletter. To cancel, empty all the fields and click "Ok".', 'wp-mailinglist'))); ?>
					</span>
					
					<div id="newsletters_sendrecurringdiv" class="" style="display:none;">
						<div class="form-field">
							<?php esc_html_e('Every', 'wp-mailinglist'); ?>
							<input type="text" name="newsletters_sendrecurringvalue" value="<?php echo esc_attr(wp_unslash($newsletters_sendrecurringvalue)); ?>" id="newsletters_sendrecurringvalue" class="widefat" style="width:45px;" /> 
							<select name="newsletters_sendrecurringinterval" id="newsletters_sendrecurringinterval">
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "minutes") ? 'selected="selected"' : ''; ?> value="minutes"><?php esc_html_e('Minutes', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "hours") ? 'selected="selected"' : ''; ?> value="hours"><?php esc_html_e('Hours', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "days") ? 'selected="selected"' : ''; ?> value="days"><?php esc_html_e('Days', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "weeks") ? 'selected="selected"' : ''; ?> value="weeks"><?php esc_html_e('Weeks', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "months") ? 'selected="selected"' : ''; ?> value="months"><?php esc_html_e('Months', 'wp-mailinglist'); ?></option>
								<option <?php echo (!empty($newsletters_sendrecurringinterval) && $newsletters_sendrecurringinterval == "years") ? 'selected="selected"' : ''; ?> value="years"><?php esc_html_e('Years', 'wp-mailinglist'); ?></option>
							</select>
							<?php echo ( $Html -> help(__('Choose the interval at which this newsletter should be sent again. All data of the newsletter such as the list(s), content, template, etc. will be reused as configured.', 'wp-mailinglist'))); ?>
						</div>
						<div class="form-field">
							<?php esc_html_e('Starting', 'wp-mailinglist'); ?>
							<?php $sendrecurringdate = (empty($newsletters_sendrecurringdate)) ? $Html -> gen_date("Y-m-d H:i", current_time('timestamp')) : $Html -> gen_date("Y-m-d H:i", strtotime($newsletters_sendrecurringdate)); ?>
							<input type="text" name="newsletters_sendrecurringdate" value="<?php echo esc_html( $sendrecurringdate); ?>" id="newsletters_sendrecurringdate" class="widefat" style="width:140px;" />
							<?php echo ( $Html -> help(__('Specify a starting date and time for the first recurring instance to run. In the format YYYY-MM-DD HH:MM', 'wp-mailinglist'))); ?>
						</div>
						<div class="form-field">
							<?php echo sprintf(__('Repeat %s times', 'wp-mailinglist'), '<input type="text" name="newsletters_sendrecurringlimit" value="' . esc_attr(wp_unslash($newsletters_sendrecurringlimit)) . '" id="newsletters_sendrecurringlimit" class="widefat" style="width:45px;" />'); ?>
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
						jQuery('#cancelsendrecurring').click(function() { jQuery('#newsletters_sendrecurringdiv').hide(); jQuery('#newsletters_sendrecurring-edit-link').show(); return false; });			
						
						jQuery('#newsletters_sendrecurringdiv input').keypress(function(e) {
							var code = (e.keyCode ? e.keyCode : e.which);
							if (code == 13) {
								changesendrecurring();
								return false;
							}
						});
					});
					
					function changesendrecurring() {
						var newsletters_sendrecurringvalue = jQuery('#newsletters_sendrecurringvalue').val();
						var newsletters_sendrecurringinterval = jQuery('#newsletters_sendrecurringinterval').val();
						var newsletters_sendrecurringlimit = jQuery('#newsletters_sendrecurringlimit').val();
						
						if (newsletters_sendrecurringvalue != "" && newsletters_sendrecurringinterval != "") {
							newsletters_sendrecurringhtml = 'Send ';
							newsletters_sendrecurringhtml += ' every ' + newsletters_sendrecurringvalue + ' ' + newsletters_sendrecurringinterval + '';
							if (newsletters_sendrecurringlimit != "") { newsletters_sendrecurringhtml += ' , ' + newsletters_sendrecurringlimit + ' times'; }
							jQuery('#newsletters_sendrecurring-value').html(newsletters_sendrecurringhtml);
							jQuery('#newsletters_sendrecurringfield').val(1);
						} else {
							jQuery('#newsletters_sendrecurringfield').val(0);
							jQuery('#newsletters_sendrecurring-value').html('<?php esc_html_e('Send Once', 'wp-mailinglist'); ?>');
						}
						
						// Date stuff
						var currentdate = new Date(Date.parse('<?php echo current_time('Y/m/d H:i'); ?>'));
						
						var newsletters_sendrecurringdate = jQuery('#newsletters_sendrecurringdate').val();
						var newsletters_sendrecurringdate_object = new Date(Date.parse(newsletters_sendrecurringdate));
						
						if (newsletters_sendrecurringdate_object.getTime() > currentdate.getTime()) {
							var newsletters_sendrecurringyear = newsletters_sendrecurringdate_object.getFullYear();
							var newsletters_sendrecurringmonth = ("0" + (newsletters_sendrecurringdate_object.getMonth() + 1)).slice(-2);
							var newsletters_sendrecurringday = ("0" + newsletters_sendrecurringdate_object.getDate()).slice(-2);
							var newsletters_sendrecurringhours = ("0" + newsletters_sendrecurringdate_object.getHours()).slice(-2);
							var newsletters_sendrecurringminutes = ("0" + newsletters_sendrecurringdate_object.getMinutes()).slice(-2);
							
							jQuery('#aa').val(newsletters_sendrecurringyear);
							jQuery('#mm').val(newsletters_sendrecurringmonth);
							jQuery('#jj').val(newsletters_sendrecurringday);
							jQuery('#hh').val(newsletters_sendrecurringhours);
							jQuery('#mn').val(newsletters_sendrecurringminutes);	
						}
						
						//update_timestamp();
						
						jQuery('#newsletters_sendrecurringdiv').hide();
						jQuery('#newsletters_sendrecurring-edit-link').show();
					}
					</script>
				</div>
			<?php endif; ?>
			
			<!-- Format (HTML/TEXT) of the newsletter -->
			<div class="misc-pub-section">
				<i class="fa fa-code"></i> 
				<label>
					<?php esc_html_e('Format:', 'wp-mailinglist'); ?>
					<?php $multimime = $this -> get_option('multimime'); ?>
					<select name="newsletters_format">
						<option <?php echo (!empty($newsletters_format) && $newsletters_format == "html") ? 'selected="selected"' : ''; ?> value="html"><?php echo (!empty($multimime) && $multimime == "Y") ? __('TEXT/HTML', 'wp-mailinglist') : __('HTML', 'wp-mailinglist'); ?></option>
						<option <?php echo (!empty($newsletters_format) && $newsletters_format == "text") ? 'selected="selected"' : ''; ?> value="text"><?php esc_html_e('TEXT', 'wp-mailinglist'); ?></option>
					</select>
				</label>
			</div>
				
    		<?php /*<div class="misc-pub-section curtime misc-pub-section-last">
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
					<?php $senddate = (empty($_POST['senddate']) || strtotime($_POST['senddate']) <= current_time('timestamp')) ? $Html -> gen_date("Y-m-d H:i:s") :  sanitize_text_field(wp_unslash($_POST['senddate'])); ?>
					<input type="hidden" name="sendtype" id="sendtype" value="<?php echo ($this -> get_option('sendingprogress') == "Y") ? 'send' : 'queue'; ?>" />
					<input type="hidden" name="senddate" id="senddate" value="<?php echo esc_html( $senddate); ?>" />
					<input type="hidden" name="scheduled" id="scheduled" value="N" />
				</div>*/ ?>
				
				<script type="text/javascript">
				/*jQuery(document).ready(function() {				
					alwaysqueue = <?php if ($this -> get_option('sendingprogress') == "N") : ?>true<?php else : ?>false<?php endif; ?>;
					jQuery('.save-timestamp').click(update_timestamp);
					
					jQuery('.cancel-timestamp').click(function() { 
						jQuery('#timestampdiv').hide(); 
						jQuery('.edit-timestamp').show(); 
					});
					
					<?php if (!empty($senddate) && strtotime($senddate) > current_time('timestamp')) : ?>
						jQuery('#scheduled').val("Y");
						jQuery('#aa').val('<?php echo esc_html( $Html -> gen_date("Y", strtotime($senddate))); ?>'));
						jQuery('#mm').val('<?php echo esc_html( $Html -> gen_date("m", strtotime($senddate))); ?>'));
						jQuery('#jj').val('<?php echo esc_html( $Html -> gen_date("d", strtotime($senddate))); ?>'));
						jQuery('#hh').val('<?php echo esc_html( $Html -> gen_date("H", strtotime($senddate))); ?>'));
						jQuery('#mn').val('<?php echo esc_html( $Html -> gen_date("i", strtotime($senddate))); ?>'));
						update_timestamp();
					<?php endif; ?>
				});*/
				
				/*function update_timestamp() { 										
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
					
					jQuery('#senddate').attr("value", year + '-' + ('0' + (month + 1)).slice(-2) + '-' + day + ' ' + hours + ':' + minutes);*/
					//jQuery('#timestampdiv').hide();
					//jQuery('.edit-timestamp').show();
				//}
				</script>
            <?php /*</div>*/ ?>
		
			<!-- default WordPress things -->
			<div class="misc-pub-section misc-pub-post-status">
			esc_html_e( 'Status:' ) ?> <span id="post-status-display"><?php
			
			switch ( $post->post_status ) {
				case 'private':
					_e('Privately Published');
					break;
				case 'publish':
					_e('Published');
					break;
				case 'future':
					_e('Scheduled');
					break;
				case 'pending':
					_e('Pending Review');
					break;
				case 'draft':
				case 'auto-draft':
					_e('Draft');
					break;
			}
			?>
			</span>
			<?php if ( 'publish' == $post->post_status || 'private' == $post->post_status || $can_publish ) { ?>
			<a href="#post_status" <?php if ( 'private' == $post->post_status ) { ?>style="display:none;" <?php } ?>class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true">esc_html_e( 'Edit' ); ?></span> <span class="screen-reader-text">esc_html_e( 'Edit status' ); ?></span></a>
			
			<div id="post-status-select" class="hide-if-js">
			<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $post->post_status ) ? 'draft' : $post->post_status); ?>" />
			<label for="post_status" class="screen-reader-text">esc_html_e( 'Set status' ) ?></label>
			<select name="post_status" id="post_status">
			<?php if ( 'publish' == $post->post_status ) : ?>
			<option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php esc_html_e('Published') ?></option>
			<?php elseif ( 'private' == $post->post_status ) : ?>
			<option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php esc_html_e('Privately Published') ?></option>
			<?php elseif ( 'future' == $post->post_status ) : ?>
			<option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php esc_html_e('Scheduled') ?></option>
			<?php endif; ?>
			<option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php esc_html_e('Pending Review') ?></option>
			<?php if ( 'auto-draft' == $post->post_status ) : ?>
			<option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php esc_html_e('Draft') ?></option>
			<?php else : ?>
			<option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php esc_html_e('Draft') ?></option>
			<?php endif; ?>
			</select>
			 <a href="#post_status" class="save-post-status hide-if-no-js button"><?php esc_html_e('OK'); ?></a>
			 <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php esc_html_e('Cancel'); ?></a>
			</div>
			
			<?php } ?>
			</div><!-- .misc-pub-section -->
		
			<div class="misc-pub-section misc-pub-visibility" id="visibility">
			<?php esc_html_e('Visibility:'); ?> <span id="post-visibility-display"><?php
			
			if ( 'private' == $post->post_status ) {
				$post->post_password = '';
				$visibility = 'private';
				$visibility_trans = __('Private');
			} elseif ( !empty( $post->post_password ) ) {
				$visibility = 'password';
				$visibility_trans = __('Password protected');
			} elseif ( $post_type == 'post' && is_sticky( $post->ID ) ) {
				$visibility = 'public';
				$visibility_trans = __('Public, Sticky');
			} else {
				$visibility = 'public';
				$visibility_trans = __('Public');
			}
			
			echo esc_html( $visibility_trans ); ?></span>
			<?php if ( $can_publish ) { ?>
			<a href="#visibility" class="edit-visibility hide-if-no-js" role="button"><span aria-hidden="true">esc_html_e( 'Edit' ); ?></span> <span class="screen-reader-text">esc_html_e( 'Edit visibility' ); ?></span></a>
			
			<div id="post-visibility-select" class="hide-if-js">
			<input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />
			<?php if ($post_type == 'post'): ?>
			<input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked(is_sticky($post->ID)); ?> />
			<?php endif; ?>
			<input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />
			<input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php esc_html_e('Public'); ?></label><br />
			<?php if ( $post_type == 'post' && current_user_can( 'edit_others_posts' ) ) : ?>
			<span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> /> <label for="sticky" class="selectit">esc_html_e( 'Stick this post to the front page' ); ?></label><br /></span>
			<?php endif; ?>
			<input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php esc_html_e('Password protected'); ?></label><br />
			<span id="password-span"><label for="post_password"><?php esc_html_e('Password:'); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>"  maxlength="255" /><br /></span>
			<input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php esc_html_e('Private'); ?></label><br />
			
			<p>
			 <a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php esc_html_e('OK'); ?></a>
			 <a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel"><?php esc_html_e('Cancel'); ?></a>
			</p>
			</div>
			<?php } ?>
			
			</div><!-- .misc-pub-section -->
		
			<?php
			/* translators: Publish box date format, see https://secure.php.net/date */
			$datef = __( 'M j, Y @ H:i' );
			if ( 0 != $post->ID ) {
				if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
					/* translators: Post date information. 1: Date on which the post is currently scheduled to be published */
					$stamp = __('Scheduled for: <b>%1$s</b>');
				} elseif ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
					/* translators: Post date information. 1: Date on which the post was published */
					$stamp = __('Sent on: <b>%1$s</b>');
				} elseif ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
					$stamp = __('Send <b>immediately</b>');
				} elseif ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
					/* translators: Post date information. 1: Date on which the post is to be published */
					$stamp = __('Schedule for: <b>%1$s</b>');
				} else { // draft, 1 or more saves, date specified
					/* translators: Post date information. 1: Date on which the post is to be published */
					$stamp = __('Send on: <b>%1$s</b>');
				}
				$date = date_i18n( $datef, strtotime( $post->post_date ) );
			} else { // draft (no saves, and thus no date specified)
				$stamp = __('Send <b>immediately</b>');
				$date = date_i18n( $datef, strtotime( current_time('mysql') ) );
			}
			
			if ( ! empty( $args['args']['revisions_count'] ) ) : ?>
			<div class="misc-pub-section misc-pub-revisions">
				<?php
					/* translators: Post revisions heading. 1: The number of available revisions */
					printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( $args['args']['revisions_count'] ) . '</b>' );
				?>
				<a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( $args['args']['revision_id'] ) ); ?>"><span aria-hidden="true">esc_html_ex( 'Browse', 'revisions' ); ?></span> <span class="screen-reader-text">esc_html_e( 'Browse revisions' ); ?></span></a>
			</div>
			<?php endif;
		
			if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp">
				<?php printf($stamp, $date); ?></span>
				<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button"><span aria-hidden="true">esc_html_e( 'Edit' ); ?></span> <span class="screen-reader-text">esc_html_e( 'Edit date and time' ); ?></span></a>
				<fieldset id="timestampdiv" class="hide-if-js">
				<legend class="screen-reader-text">esc_html_e( 'Date and time' ); ?></legend>
				<?php touch_time( ( $action === 'edit' ), 1 ); ?>
				</fieldset>
			</div><?php // /misc-pub-section ?>
			<?php endif; ?>
		
			<?php if ( 'draft' === $post->post_status && get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ) : ?>
				<div class="notice notice-info notice-alt inline">
					<p>
						<?php
						echo sprintf(
							/* translators: %s: URL to the Customizer */
							__( 'This draft comes from your <a href="%s">unpublished customization changes</a>. You can edit, but there&#8217;s no need to publish now. It will be published automatically with those changes.' ),
							esc_url(
								add_query_arg(
									'changeset_uuid',
									rawurlencode( get_post_meta( $post->ID, '_customize_changeset_uuid', true ) ),
									admin_url( 'customize.php' )
								)
							)
						);
						?>
					</p>
				</div>
			<?php endif; ?>
		
			<?php do_action( 'post_submitbox_misc_actions', $post ); ?>
		</div>
		<div class="clear"></div>
	</div>
		
	<div id="major-publishing-actions">
		<?php
		/**
		 * Fires at the beginning of the publishing actions section of the Publish meta box.
		 *
		 * @since 2.7.0
		 * @since 4.9.0 Added the `$post` parameter.
		 *
		 * @param WP_Post|null $post WP_Post object for the current post on Edit Post screen,
		 *                           null on Edit Link screen.
		 */
		do_action( 'post_submitbox_start', $post );
		?>
		<div id="delete-action">
		<?php
		if ( current_user_can( "delete_post", $post->ID ) ) {
			if ( !EMPTY_TRASH_DAYS )
				$delete_text = __('Delete Permanently');
			else
				$delete_text = __('Move to Trash');
			?>
		<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo esc_html( $delete_text); ?></a><?php
		} ?>
		</div>
		
		<div id="publishing-action">
		<span class="spinner"></span>
		<?php
		if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
			if ( $can_publish ) :
				if ( !empty($post->post_date_gmt) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_attr_x( 'Schedule', 'post action/button label' ); ?>" />
				<?php submit_button( _x( 'Schedule', 'post action/button label' ), 'primary large', 'publish', false, array('disabled' => "disabled")); ?>
		<?php	else : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
				<?php submit_button( __( 'Send' ), 'primary large', 'publish', false, array('disabled' => "disabled")); ?>
		<?php	endif;
			else : ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
				<?php submit_button( __( 'Submit for Review' ), 'primary large', 'publish', false, array('disabled' => "disabled")); ?>
		<?php
			endif;
		} else { ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
				<input name="save" type="submit" class="button button-primary button-large" id="publish" value="<?php esc_attr_e( 'Send Again' ) ?>" disabled="disabled" />
		<?php
		} ?>
		</div>
		<div class="clear"></div>
		<div class="publishing-action-inside" id="subscriberscountsubmit" style="display:none;">
			<!-- Subscribers count in submit box -->
		</div>
		<?php $sendingprogress = $this -> get_option('sendingprogress'); ?>
		<div class="publishing-action-inside">
			<label><input <?php echo (!empty($sendingprogress) && $sendingprogress == "Y") ? 'checked="checked"' : ''; ?> type="checkbox" name="newsletters_sendingprogress" value="1" id="newsletters_sendingprogress" /> <?php esc_html_e('Use progress bar to queue/send', 'wp-mailinglist'); ?></label>
		</div>
	</div>
</div>