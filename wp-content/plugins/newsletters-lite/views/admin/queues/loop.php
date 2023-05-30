<?php // phpcs:ignoreFile ?>
<!-- Email Queue Loop -->
<?php
	
$queue_status = $this -> get_option('queue_status');
	
?>

	<form action="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=mass" method="post" onsubmit="if (!confirm('<?php esc_html_e('Are you sure you wish to execute this action?', 'wp-mailinglist'); ?>')) { return false; }">
		<div class="tablenav">
			<div class="alignleft actions">
				<?php if (empty($queue_status) || $queue_status == "unpause") : ?>
					<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('pause'); return false;" class="button"><i id="pausequeueicon" class="fa fa-pause"></i> <?php esc_html_e('Pause', 'wp-mailinglist'); ?></a>
				<?php else : ?>
					<a id="newsletters_pause_queue_button" href="" onclick="newsletters_pause_queue('unpause'); return false;" class="button"><i class="fa fa-play"></i> <?php esc_html_e('Unpause', 'wp-mailinglist'); ?></a>
				<?php endif; ?>
				<a href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=clear" title="<?php esc_html_e('Clear the email queue', 'wp-mailinglist'); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to purge the email queue?', 'wp-mailinglist'); ?>')) { return false; }" class="button"><i class="fa fa-trash"></i> <?php esc_html_e('Clear Queue', 'wp-mailinglist'); ?></a>
			</div>
			<div class="alignleft actions">
				<select name="action" class="widefat" style="width:auto;">
					<option value=""><?php esc_html_e('- Bulk Actions -', 'wp-mailinglist'); ?></option>
					<option value="delete"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></option>
					<option value="send"><?php esc_html_e('Send Now', 'wp-mailinglist'); ?></option>
				</select>				
				<button value="1" type="submit" name="execute" class="button action">
					<?php esc_html_e('Apply', 'wp-mailinglist'); ?>
				</button>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
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
			
		jQuery(document).ready(function() {
			
		});
		</script>
		
		<?php
		
		$screen_custom = $this -> get_option('screenoptions_subscribers_custom');
		$orderby = (empty($_GET['orderby'])) ? 'modified' : sanitize_text_field(wp_unslash($_GET['orderby']));
		$order = (empty($_GET['order'])) ? 'desc' : strtolower(sanitize_text_field(wp_unslash($_GET['order'])));
		$otherorder = ($order == "desc") ? 'asc' : 'desc';
		
		$colspan = 9;
		
		?>
		
		<table class="widefat">
			<thead>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=id&order=' . (($orderby == "id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
						<th><?php esc_html_e('Image', 'wp-mailinglist'); ?></th>
						<?php $colspan++; ?>
					<?php endif; ?>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('History Email', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-theme_id <?php echo ($orderby == "theme_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Template', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></th>
					<th class="column-error <?php echo ($orderby == "error") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=error&order=' . (($orderby == "error") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Error', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="check-column"><input type="checkbox" name="checkboxall" value="checkboxall" id="checkboxall" /></td>
					<th class="column-id <?php echo ($orderby == "id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('ID', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
						<th><?php esc_html_e('Image', 'wp-mailinglist'); ?></th>
					<?php endif; ?>
					<th class="column-subscriber_id <?php echo ($orderby == "subscriber_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=subscriber_id&order=' . (($orderby == "subscriber_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Subscriber', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-history_id <?php echo ($orderby == "history_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=history_id&order=' . (($orderby == "history_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('History Email', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
                    <th class="column-theme_id <?php echo ($orderby == "theme_id") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=theme_id&order=' . (($orderby == "theme_id") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Template', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></th>
					<th class="column-error <?php echo ($orderby == "error") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=error&order=' . (($orderby == "error") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Error', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-senddate <?php echo ($orderby == "senddate") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=senddate&order=' . (($orderby == "senddate") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Send Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th class="column-modified <?php echo ($orderby == "modified") ? 'sorted ' . esc_html($order) : 'sortable desc'; ?>">
						<a href="<?php echo esc_url_raw($Html -> retainquery('orderby=modified&order=' . (($orderby == "modified") ? $otherorder : "asc"))); ?>">
							<span><?php esc_html_e('Date', 'wp-mailinglist'); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php if (empty($queues)) : ?>
					<tr class="no-items">
						<td class="colspanchange" colspan="<?php echo esc_html($colspan); ?>"><?php esc_html_e('No queued emails found', 'wp-mailinglist'); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ($queues as $queue) : ?>
						<?php 
						
						if (!empty($queue -> subscriber_id)) {
							$subscriber = $Subscriber -> get($queue -> subscriber_id); 
						} elseif (!empty($queue -> user_id)) {
							$user = $this -> userdata($queue -> user_id);
						}
						
						?>
						<?php $class = ($class == "alternate") ? '' : 'alternate'; ?>
						<tr id="queuerow<?php echo esc_html( $queue -> id); ?>" class="<?php echo esc_html( $class); ?>">
							<th class="check-column"><input type="checkbox" id="checklist<?php echo esc_html( $queue -> id); ?>" name="Queue[checklist][]" value="<?php echo esc_html( $queue -> id); ?>" /></th>
							<td><label for="checklist<?php echo esc_html( $queue -> id); ?>"><?php echo esc_html( $queue -> id); ?></label></td>
							<?php if (!empty($screen_custom) && in_array('gravatars', $screen_custom)) : ?>
								<td>
									<?php if (!empty($subscriber)) : ?>
										<label for="checklist<?php echo esc_html( $queue -> id); ?>"><?php echo wp_kses_post( $Html -> get_gravatar($subscriber -> email)); ?></label>
									<?php elseif (!empty($user)) : ?>
										<label for="checklist<?php echo esc_html( $queue -> id); ?>"><?php echo wp_kses_post( $Html -> get_gravatar($user -> user_email)); ?></label>
									<?php endif; ?>
								</td>
							<?php endif; ?>
							<td>
								<?php if (!empty($subscriber)) : ?>
									<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=view&amp;id=<?php echo esc_html( $subscriber -> id); ?>" class="row-title" title="<?php esc_html_e('View this subscriber', 'wp-mailinglist'); ?>"><?php echo esc_html( $subscriber -> email); ?></a>
								<?php elseif (!empty($user)) : ?>
									<a href="<?php echo get_edit_user_link($user -> ID); ?>" class="row-title"><?php echo esc_html( $user -> display_name); ?></a>
									<br/><small><?php echo esc_html( $user -> user_email); ?></small>
								<?php endif; ?>
								<div class="row-actions">
									<span class="delete"><a onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this queued email?', 'wp-mailinglist'); ?>')) { return false; }" class="submitdelete" href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=delete&amp;id=<?php echo esc_html( $queue -> id); ?>"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a> |</span>
									<span class="edit"><a href="?page=<?php echo esc_html( $this -> sections -> queue); ?>&amp;method=send&amp;id=<?php echo esc_html( $queue -> id); ?>"><?php esc_html_e('Send Now', 'wp-mailinglist'); ?></a></span>
								</div>
							</td>
							<td><label for="checklist<?php echo esc_html( $queue -> id); ?>"><?php echo ( $Html -> link(esc_html($queue -> subject), "?page=" . $this -> sections -> history . "&amp;method=view&amp;id=" . $queue -> history_id, array('title' => $queue -> subject))); ?></label></td>
		                    <td>
		                    	<?php $Db -> model = $Theme -> model; ?>
		                    	<?php if (!empty($queue -> theme_id) && $theme = $Db -> find(array('id' => $queue -> theme_id))) : ?>
		                        	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html( $theme -> id); ?>'}); return false;" title="<?php esc_html_e('Template Preview:', 'wp-mailinglist'); ?> <?php echo esc_html( $theme -> title); ?>"><?php echo esc_html( $theme -> title); ?></a>
		                        <?php else : ?>
		                        	<?php esc_html_e('None', 'wp-mailinglist'); ?>
		                        <?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php if (!empty($queue -> attachments)) : ?>
		                        	<?php $queue -> attachments = maybe_unserialize($queue -> attachments); ?>
		                        	<ul style="padding:0; margin:0;">
		                            	<?php foreach ($queue -> attachments as $attachment) : ?>
		                                	<li class="<?php echo esc_html($this -> pre); ?>attachment">
		                                    	<?php echo esc_url_raw( $Html -> attachment_link($attachment)); ?>
		                                        
		                                    </li>
		                                <?php endforeach; ?>
		                            </ul>
		                        <?php else : ?>
		                        	<?php esc_html_e('None', 'wp-mailinglist'); ?>
		                        <?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php if (!empty($queue -> error)) : ?>
		                    		<span class="wpmlerror"><?php esc_html_e('Yes', 'wp-mailinglist'); ?></span>
		                    		<?php echo ( $Html -> help($queue -> error)); ?>
		                    	<?php else : ?>
		                    		<span class="wpmlsuccess"><?php esc_html_e('No', 'wp-mailinglist'); ?></span>
		                    	<?php endif; ?>
		                    </td>
		                    <td>
		                    	<?php echo esc_html( $queue -> senddate); ?>
		                    </td>
							<td><label for="checklist<?php echo esc_html( $queue -> id); ?>"><abbr title="<?php echo esc_html( $queue -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($queue -> modified))); ?></abbr></label></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<?php if (empty($_GET['showall'])) : ?>
					<select class="widefat" style="width:auto;" name="perpage" onchange="change_perpage(this.value);">
						<option value=""><?php esc_html_e('- Per Page -', 'wp-mailinglist'); ?></option>
						<?php $s = 5; ?>
						<?php while ($s <= 200) : ?>
							<option <?php echo (isset($_COOKIE[$this -> pre . 'queuesperpage']) && $_COOKIE[$this -> pre . 'queuesperpage'] == $s) ? 'selected="selected"' : ''; ?> value="<?php echo wp_kses_post($s); ?>"><?php echo wp_kses_post($s); ?> <?php esc_html_e('emails', 'wp-mailinglist'); ?></option>
							<?php $s += 5; ?>
						<?php endwhile; ?>
						<?php if (isset($_COOKIE[$this -> pre . 'queuesperpage'])) : ?>
							<option selected="selected" value="<?php echo (int) $_COOKIE[$this -> pre . 'queuesperpage']; ?>"><?php echo (int) $_COOKIE[$this -> pre . 'queuesperpage']; ?></option>
						<?php endif; ?>
					</select>
				<?php endif; ?>
			</div>
			<?php $this -> render('pagination', array('paginate' => $paginate), true, 'admin'); ?>
		</div>
		
		<script type="text/javascript">
		function change_perpage(perpage) {
			if (perpage != "") {
				document.cookie = "<?php echo esc_html($this -> pre); ?>queuesperpage=" + perpage + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
				window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
			}
		}
		
		function change_sorting(field, dir) {
			document.cookie = "<?php echo esc_html($this -> pre); ?>queuessorting=" + field + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			document.cookie = "<?php echo esc_html($this -> pre); ?>queues" + field + "dir=" + dir + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
			window.location = "<?php echo preg_replace("/\&?" . $this -> pre . "page\=(.*)?/si", "", wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])))); ?>";
		}
		</script>