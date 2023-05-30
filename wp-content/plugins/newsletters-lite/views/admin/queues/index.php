<?php // phpcs:ignoreFile ?>
<!-- Email Queue Page -->

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?> <?php echo esc_html( $this -> sections -> queue); ?>">
	<h1>
		<?php esc_html_e('Email Queue', 'wp-mailinglist'); ?>
		<a href="?page=<?php echo esc_html( $this -> sections -> queue); ?>" class="add-new-h2"><i class="fa fa-refresh fa-fw"></i> <?php esc_html_e('Refresh', 'wp-mailinglist'); ?></a>
		<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings . '#schedulingdiv')) ?>" class="add-new-h2"><i class="fa fa-cog fa-fw"></i> <?php esc_html_e('Configure Queue', 'wp-mailinglist'); ?></a>
	</h1>
	
	<?php
		
	$batchnumber = (empty($_GET['batchnumber'])) ? 1 :  sanitize_text_field(wp_unslash($_GET['batchnumber']));
		
	$queue_status = $this -> get_option('queue_status');
	delete_transient('newsletters_queue_count');
	$count = $this -> qp_get_queued_count();
	echo '<p>' . sprintf(__('There are currently %s emails in the queue.', 'wp-mailinglist'), '<span class="update-plugins count-1"><span class="update-count" id="newsletters-menu-queue-count">' . $count . '</span></span>') . '</p>';	
	
	$errors = $this -> qp_get_batches(false, true, $batchnumber);
		
	?>
	
	<div class="actions">
		<?php if (empty($queue_status) || $queue_status == "unpause") : ?>
			<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('pause'); return false;" class="button"><i id="pausequeueicon" class="fa fa-pause"></i> <?php esc_html_e('Pause', 'wp-mailinglist'); ?></a>
		<?php else : ?>
			<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('unpause'); return false;" class="button"><i class="fa fa-play"></i> <?php esc_html_e('Unpause', 'wp-mailinglist'); ?></a>
		<?php endif; ?>
		<?php if (!empty($count)) : ?>
			<a href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=clear" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to purge the email queue?', 'wp-mailinglist'); ?>')) { return false; }" class="button"><i class="fa fa-trash"></i> <?php esc_html_e('Clear Queue', 'wp-mailinglist'); ?></a>
			<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=wp_queue_process_cron')) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to run the queue schedule now and send out emails?', 'wp-mailinglist'); ?>')) { return false; }" class="button"><i class="fa fa-send"></i> <?php esc_html_e('Run Now', 'wp-mailinglist'); ?></a>
		<?php endif; ?>
	</div>
	
	<?php if (!empty($count)) : ?>
		<p><?php echo wp_kses_post( $Html -> next_scheduled('wp_queue_process_cron')); ?></p>
	<?php endif; ?>
	
	<script type="text/javascript">
		function newsletters_pause_queue(status) {
			
			jQuery('#newsletters_pause_queue_button').attr('disabled', "disabled").find('i').attr('class', "fa fa-refresh fa-spin");
			
			jQuery.ajax({
				url: newsletters_ajaxurl + 'action=newsletters_pause_queue&security=<?php echo esc_html( wp_create_nonce('pause_queue')); ?>',
				data: {status:status},
				cache: false,
				type: "POST",
				success: function(response) {					
					if (response == false) {
						alert('<?php esc_html_e('Queue could not be paused, please try again', 'wp-mailinglist'); ?>');
					} else {
						if (status == "unpause") {
							var pause_queue_html = '<i class="fa fa-pause"></i> <?php esc_html_e('Pause', 'wp-mailinglist'); ?>';
							var pause_queue_action = 'pause';
						} else {
							var pause_queue_html = '<i class="fa fa-play"></i> <?php esc_html_e('Unpause', 'wp-mailinglist'); ?>';
							var pause_queue_action = 'unpause';
						}
						
						jQuery('#newsletters_pause_queue_button').removeAttr('disabled').html(pause_queue_html).attr('onclick', "newsletters_pause_queue('" + pause_queue_action + "'); return false;");		
					}
				}
			});
		}
	</script>
	
	<h2><?php esc_html_e('Queue Emails Not Sending? Try These Things:', 'wp-mailinglist'); ?></h2>
	<p><?php esc_html_e('There could be several reasons why queued emails are not sending, please check these things:', 'wp-mailinglist'); ?></p>
	
	<ul>
		<li><i class="fa fa-check fa-fw newsletters_success"></i> <?php echo sprintf(__('"Process already running" or emails stuck? %s.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> queue . '&method=unlock') . '">' . __('Unlock the queue process', 'wp-mailinglist') . '</a>'); ?></li>
		<li><i class="fa fa-check fa-fw newsletters_success"></i> <?php echo sprintf(__('Check if there are %s preventing emails from going out.', 'wp-mailinglist'), '<a href="' . admin_url('admin.php?page=' . $this -> sections -> queue . '&method=errors') . '">' . __('queue errors', 'wp-mailinglist') . '</a>'); ?></li>
		<?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) : ?>
			<li><i class="fa fa-times fa-fw newsletters_error"></i> <?php echo sprintf(__('Your WordPress cron is turned off! <code>DISABLE_WP_CRON</code> is defined in your <code>wp-config.php</code> file, %s', 'wp-mailinglist'), '<a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/11164" target="_blank">' . __('make sure the WordPress cron is running', 'wp-mailinglist') . '</a>'); ?></li>
		<?php endif; ?>
		<?php if (!$this -> wp_cron_file_exists()) : ?>
			<li><i class="fa fa-times fa-fw newsletters_error"></i> <?php echo sprintf(__('The %s file does not exist, please check if it was deleted or renamed.', 'wp-mailinglist'), '<code>wp-cron.php</code>'); ?></li>
		<?php endif; ?>
		<li>
			<i class="fa fa-check fa-fw newsletters_success"></i> <?php echo sprintf(__('Are your email settings working? You can %s.', 'wp-mailinglist'), '<a id="testsettings" onclick="testsettings(); return false;" href="' . admin_url('admin.php?page=' . $this -> sections -> settings) . '">' . __('Test Email Settings', 'wp-mailinglist') . '</a>'); ?>
			<span id="testsettingsloading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
		</li>
	</ul>
	
	<script type="text/javascript">
	function testsettings() {
		jQuery('#testsettingsloading').show();
		jQuery('#testsettings').attr('disabled', "disabled");
		var formvalues = jQuery('#settings-form').serialize();
		
		jQuery.post(newsletters_ajaxurl + 'action=<?php echo esc_html($this -> pre); ?>testsettings&security=<?php echo esc_html( wp_create_nonce('testsettings')); ?>&init=1', formvalues, function(response) {
			jQuery.colorbox({html:response}).resize();
			jQuery('#testsettingsloading').hide();
			jQuery('#testsettings').removeAttr('disabled');
		});
	}
	</script>
	
	<?php if (!empty($errors)) : ?>
		<br class="clear" />
		<h2><?php esc_html_e('Queue Errors', 'wp-mailinglist'); ?></h2>
		<p>
			<?php esc_html_e('The emails below are still in the queue with errors and will be retried.', 'wp-mailinglist'); ?><br/>
			<?php esc_html_e('When the queue is complete, only error emails will remain and you can then press "Clear Queue" to empty it.', 'wp-mailinglist'); ?>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<td><?php esc_html_e('Subscriber/User', 'wp-mailinglist'); ?></td>
					<td><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></td>
					<td><?php esc_html_e('Error', 'wp-mailinglist'); ?></td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td><?php esc_html_e('Subscriber/User', 'wp-mailinglist'); ?></td>
					<td><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></td>
					<td><?php esc_html_e('Error', 'wp-mailinglist'); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($errors as $error) : ?>
					<?php foreach ($error -> data as $data) : ?>
						<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
							<td>
								<?php
									
								$post_id = $this -> History() -> field('p_id', array('id' => $data['history_id']));
									
								if (!empty($data['subscriber_id'])) {
									$subscriber = $Subscriber -> get($data['subscriber_id']); 
								} elseif (!empty($data['user_id'])) {
									$user = $this -> userdata($data['user_id']);
								}	
									
								?>
								<?php if (!empty($subscriber)) : ?>
									<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>" class="row-title" title="<?php esc_html_e('View this subscriber', 'wp-mailinglist'); ?>"><?php echo esc_html( $subscriber -> email); ?></a>
								<?php elseif (!empty($user)) : ?>
									<a href="<?php echo get_edit_user_link($user -> ID); ?>" class="row-title"><?php echo esc_html( $user -> display_name); ?></a>
									<br/><small><?php echo esc_html( $user -> user_email); ?></small>
								<?php else : ?>
									<span class="howto"><?php esc_html_e('Subscriber or user does not exist anymore.', 'wp-mailinglist'); ?></span>
								<?php endif; ?>
							</td>
							<?php /*<td><?php echo ( $Html -> link(esc_html($data['subject']), "?page=" . $this -> sections -> history . "&amp;method=view&amp;id=" . $data['history_id'])); ?></td>*/ ?>
							<td><?php echo edit_post_link(esc_html($data['subject']), false, false, $post_id); ?></td>
		                    <td>
		                    	<i class="fa fa-exclamation-triangle fa-fw newsletters_error"></i> <?php echo esc_html( $data['error']); ?>
		                    </td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	
	<?php
			
	$batches = $this -> qp_get_batches(true, false);
	$batchescount = count($batches);
		
	?>
	
	<?php if ($batches = $this -> qp_get_batches(false, false, $batchnumber)) : ?>
		<br class="clear" />
		<h2><?php esc_html_e('Batches and Emails', 'wp-mailinglist'); ?></h2>
		<p>
			<?php esc_html_e('One batch is displayed at a time with the emails in the batch.', 'wp-mailinglist'); ?><br/>
			<?php esc_html_e('A batch can have 1 to 100 emails in it.', 'wp-mailinglist'); ?>
		</p>
		<?php $batch = $batches[0]; ?>
		<?php if (!empty($batch -> data)) : ?>
			<form action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&method=batchbulk')) ?>" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you want to apply this action on the selected emails?', 'wp-mailinglist'); ?>')) { return false; }">
				<div class="tablenav">
					<div class="alignleft">
						<select name="action">
							<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
							<option value="delete"><?php esc_html_e('Delete Emails', 'wp-mailinglist'); ?></option>
						</select>
						<button value="1" type="submit" name="submit" class="button">
							<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
						</button>
						<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&method=deletebatch&batch=' . $batch -> key)) ?>" class="button" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this batch?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i> <?php esc_html_e('Delete Batch', 'wp-mailinglist'); ?></a>
						<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&method=sendbatch&batch=' . $batch -> key)) ?>" class="button button-primary"><i class="fa fa-paper-plane"></i> <?php esc_html_e('Send Batch', 'wp-mailinglist'); ?></a>
					</div>
					
					<div class="tablenav-pages">
						<span class="displaying-num"><?php echo sprintf(__('%s batches', 'wp-mailinglist'), $batchescount); ?></span>
						<span class="pagination-links">							
							<?php if ($batchnumber > 1) : ?>
								<a class="first-page button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&batchnumber=1')) ?>">&laquo;</a>
								<a class="prev-page button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&batchnumber=' . ($batchnumber - 1))) ?>">&#8249;</a>
							<?php else : ?>
								<span class="tablenav-pages-navspan button" aria-hidden="true">&laquo;</span>
								<span class="tablenav-pages-navspan button" aria-hidden="true">&#8249;</span>
							<?php endif; ?>
							
							<span class="paging-input">
								<input class="newsletters-paged-input current-page" id="current-page-selector" type="text" name="paged" value="<?php echo esc_attr($batchnumber); ?>" size="<?php echo (strlen($batchnumber) + 1); ?>" aria-describedby="table-paging">
								<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo esc_html( $batchescount); ?></span></span>
							</span>
							
							<?php if ($batchnumber < $batchescount) : ?>
								<a class="next-page button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&batchnumber=' . ($batchnumber + 1))) ?>">&#8250;</a>
								<a class="last-page button" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue . '&batchnumber=' . $batchescount)) ?>">&raquo;</a>
							<?php else : ?>
								<span class="tablenav-pages-navspan button" aria-hidden="true">&#8250;</span>
								<span class="tablenav-pages-navspan button" aria-hidden="true">&raquo;</span>
							<?php endif; ?>
						</span>
					</div>
					<br class="clear" />
				</div>
				<table class="widefat">
					<thead>
						<tr>
							<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
							<th><?php esc_html_e('Image', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Template', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Error', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Date', 'wp-mailinglist'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
							<th><?php esc_html_e('Image', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Newsletter', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Template', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Error', 'wp-mailinglist'); ?></th>
							<th><?php esc_html_e('Date', 'wp-mailinglist'); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php foreach ($batch -> data as $key => $data) : ?>
							<?php
								
							$post_id = $this -> History() -> field('p_id', array('id' => $data['history_id']));
								
							if (!empty($data['subscriber_id'])) {
								$subscriber = $Subscriber -> get($data['subscriber_id']);
							} elseif (!empty($data['user_id'])) {
								$user = $this -> userdata($data['user_id']);
							}
								
							?>
							<tr>
								<input type="hidden" name="batch" value="<?php echo esc_html( $batch -> key); ?>" />
								<th class="check-column"><input type="checkbox" id="email-checkbox-<?php echo esc_html($key); ?>" name="emails[]" value="<?php echo esc_html($key); ?>" /></th>
								<!-- Avatar -->
								<td>
									<?php if (!empty($subscriber)) : ?>
										<label for="email-checkbox-<?php echo esc_html($key); ?>"><?php echo wp_kses_post( $Html -> get_gravatar($subscriber -> email)); ?></label>
									<?php elseif (!empty($user)) : ?>
										<label for="email-checkbox-<?php echo esc_html($key); ?>"><?php echo wp_kses_post( $Html -> get_gravatar($user -> user_email)); ?></label>
									<?php endif; ?>
								</td>
								<!-- Subscriber or User Details -->
								<td>
									<?php if (!empty($subscriber)) : ?>
										<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>" class="row-title" title="<?php esc_html_e('View this subscriber', 'wp-mailinglist'); ?>"><?php echo esc_html( $subscriber -> email); ?></a>
									<?php elseif (!empty($user)) : ?>
										<a href="<?php echo get_edit_user_link($user -> ID); ?>" class="row-title"><?php echo esc_html( $user -> display_name); ?></a>
										<br/><small><?php echo esc_html( $user -> user_email); ?></small>
									<?php else : ?>
										<span class="howto"><?php esc_html_e('Subscriber or user does not exist anymore.', 'wp-mailinglist'); ?></span>
									<?php endif; ?>
									<div class="row-actions">
										<span class="delete"><a onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this queued email?', 'wp-mailinglist'); ?>')) { return false; }" class="submitdelete" href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=deleteemail&batch=<?php echo esc_html( $batch -> key); ?>&id=<?php echo esc_html($key); ?>"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a> |</span>
										<span class="edit"><a href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=sendemail&batch=<?php echo esc_html( $batch -> key); ?>&id=<?php echo esc_html($key); ?>"><?php esc_html_e('Send Now', 'wp-mailinglist'); ?></a></span>
									</div>
								</td>
								<!-- Newsletter -->
								<td>
									<?php if (!empty($post_id)) : ?>
										<label for="email-checkbox-<?php echo esc_html($key); ?>"><?php echo edit_post_link(esc_html($data['subject']), false, false, $post_id); ?></label>
									<?php else : ?>
										<label for="email-checkbox-<?php echo esc_html($key); ?>"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $data['history_id'])) ?>"><?php echo esc_html($data['subject']); ?></label>
									<?php endif; ?>
								</td>
								<!-- Newsletter Theme/Template -->
			                    <td>
			                    	<?php $Db -> model = $Theme -> model; ?>
			                    	<?php if (!empty($data['theme_id']) && $theme = $Db -> find(array('id' => $data['theme_id']))) : ?>
			                        	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html( $theme -> id); ?>'}); return false;" title="<?php esc_html_e('Template Preview:', 'wp-mailinglist'); ?> <?php echo esc_html( $theme -> title); ?>"><?php echo esc_html( $theme -> title); ?></a>
			                        <?php else : ?>
			                        	<?php esc_html_e('None', 'wp-mailinglist'); ?>
			                        <?php endif; ?>
			                    </td>
			                    <!-- Attachments -->
			                    <td>
			                    	<?php if (!empty($data['attachments'])) : ?>
			                        	<?php $data['attachments'] = maybe_unserialize($data['attachments']); ?>
			                        	<ul style="padding:0; margin:0;">
			                            	<?php foreach ($data['attachments'] as $attachment) : ?>
			                                	<li class="<?php echo esc_html($this -> pre); ?>attachment">
			                                    	<?php echo esc_url_raw( $Html -> attachment_link($attachment)); ?>
			                                        
			                                    </li>
			                                <?php endforeach; ?>
			                            </ul>
			                        <?php else : ?>
			                        	<?php esc_html_e('None', 'wp-mailinglist'); ?>
			                        <?php endif; ?>
			                    </td>
			                    <!-- Errors -->
			                    <td>
			                    	<?php if (!empty($data['error'])) : ?>
			                    		<span class="newsletters_error"><i class="fa fa-exclamation-triangle fa-fw"></i> <?php esc_html_e('Yes', 'wp-mailinglist'); ?></span>
			                    		<?php echo ( $Html -> help($data['error'])); ?>
			                    	<?php else : ?>
			                    		<span class="newsletters_success"><i class="fa fa-check fa-fw"></i> <?php esc_html_e('No', 'wp-mailinglist'); ?></span>
			                    	<?php endif; ?>
			                    </td>
			                    <!-- Senddate -->
			                    <td>
			                    	<?php echo esc_html( $data['senddate']); ?>
			                    </td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</form>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.newsletters-paged-input').keypress(function(e) {
					code = (e.keyCode ? e.keyCode : e.which);
		            if (code == 13) {
		            	window.location = '?page=<?php echo esc_html( $this -> sections -> queue); ?>&batchnumber=' + jQuery(this).val();
		            	e.preventDefault();
		            }
				});
			});
			</script>
		<?php endif; ?>
	<?php endif; ?>
</div>