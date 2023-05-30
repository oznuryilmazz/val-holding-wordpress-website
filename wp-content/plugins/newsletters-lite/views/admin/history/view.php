<!-- History View -->
<?php // phpcs:ignoreFile ?>

<?php

$preview_src = admin_url('admin-ajax.php?action=' . $this -> pre . 'historyiframe&id=' . $history -> id . '&security=' . wp_create_nonce('historyiframe') . '&rand=' . rand(1,999));

$user_chart = $this -> get_user_option(false, 'chart');
$chart = (empty($user_chart)) ? "bar" : $user_chart; 

$type = (empty($_GET['type'])) ? 'days' :  sanitize_text_field(wp_unslash($_GET['type']));
$fromdate = (empty($_GET['from'])) ? $Html -> gen_date("Y-m-d", strtotime("-13 days")) :  sanitize_text_field(wp_unslash($_GET['from']));
$todate = (empty($_GET['to'])) ? $Html -> gen_date("Y-m-d") :  sanitize_text_field(wp_unslash($_GET['to']));

?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?> newsletters">
	<h2><?php esc_html_e('Sent/Draft:', 'wp-mailinglist'); ?> <?php echo esc_html( $history -> subject); ?> <a href="?page=<?php echo esc_html( $this -> sections -> history); ?>&method=view&id=<?php echo esc_html( $history -> id); ?>" class="add-new-h2"><?php esc_html_e('Refresh', 'wp-mailinglist'); ?></a></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; All Sent &amp; Drafts', 'wp-mailinglist'), $this -> url)); ?></div>
	
	<div class="tablenav">
		<div class="alignleft actions">
			<a href="?page=<?php echo esc_html( $this -> sections -> send); ?>&amp;method=history&amp;id=<?php echo esc_html( $history -> id); ?>" class="button button-primary"><i class="fa fa-paper-plane"></i> <?php esc_html_e('Send/Edit', 'wp-mailinglist'); ?></a>
			<a onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo esc_url_raw($preview_src); ?>'}); return false;" href="#" class="button"><i class="fa fa-eye"></i> <?php esc_html_e('Preview', 'wp-mailinglist'); ?></a>
			<a href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=delete&amp;id=<?php echo esc_html( $history -> id); ?>" class="button button-highlighted" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this history email?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
			<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&amp;method=duplicate&amp;id=' . $history -> id)) ?>" class="button"><i class="fa fa-clipboard"></i> <?php esc_html_e('Duplicate', 'wp-mailinglist'); ?></a>
			<a href="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_history_download&id=' . $history -> id . '&security=' . wp_create_nonce('history_download'))) ?>" class="button"><i class="fa fa-download fa-fw"></i> <?php esc_html_e('Download HTML', 'wp-mailinglist'); ?></a>
		</div>
	</div>
	
	<div class="postbox" style="padding:10px;">
		<p>
			<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart&value=bar')); ?>" class="button <?php echo (empty($chart) || $chart == "bar") ? 'active' : ''; ?>"><i class="fa fa-bar-chart"></i></a>
			<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart&value=line')); ?>" class="button <?php echo (!empty($chart) && $chart == "line") ? 'active' : ''; ?>"><i class="fa fa-line-chart"></i></a>
			<?php echo ( $Html -> help(__('Switch between bar and line charts.', 'wp-mailinglist'))); ?>
		</p>
		
		<div id="chart-legend" class="newsletters-chart-legend"></div>
		<canvas id="canvas" style="width:100%; height:300px;"></canvas>
		
		<script type="text/javascript">
		jQuery(document).ready(function() {	
                var ajaxdata = {type:'<?php echo $type; ?>', chart:'<?php echo $chart; ?>', from:'<?php echo $fromdate; ?>', to:'<?php echo $todate; ?>', history_id:'<?php echo $history -> id; ?>'};
			
			jQuery.getJSON(newsletters_ajaxurl + 'action=wpmlwelcomestats&security=<?php echo esc_html( wp_create_nonce('welcomestats')); ?>', ajaxdata, function(json) {
				var chartdata = json;
				var ctx = document.getElementById("canvas").getContext("2d");
				
				var chart = new Chart(ctx, {
					type: '<?php echo (empty($chart) || $chart == "bar") ? 'bar' : 'line'; ?>',
					data: chartdata,
					options: {
						tooltips: {
							mode: 'index'
						}
					}
				});
			});
		});
		</script>
	</div>
	
	<?php $class = ''; ?>
	<div class="postbox" style="padding:10px;">
		<table class="widefat queuetable">
			<tbody>
				<?php if (!empty($history -> from) || !empty($history -> fromname)) : ?>
					<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
						<th><?php esc_html_e('From', 'wp-mailinglist'); ?></th>
						<td>
							<?php echo (empty($history -> fromname)) ? esc_html($this -> get_option('smtpfromname')) : $history -> fromname; ?>; <?php echo (empty($history -> from)) ? esc_html($this -> get_option('smtpfrom')) : $history -> from; ?>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Email Subject', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html($history -> subject); ?></td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></th>
					<td>
						<?php if (!empty($history -> mailinglists)) : ?>
							<?php $mailinglists = $history -> mailinglists; ?>
							<?php $m = 1; ?>
							<?php if (is_array($mailinglists) || is_object($mailinglists)) : ?>
								<?php foreach ($mailinglists as $mailinglist_id) : ?>
									<?php $mailinglist = $Mailinglist -> get($mailinglist_id, false); ?>
									<?php echo ( $Html -> link(esc_html($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id)); ?><?php echo ($m < count($mailinglists)) ? ', ' : ''; ?>
									<?php $m++; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php else : ?>
							<?php esc_html_e('None', 'wp-mailinglist'); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Roles', 'wp-mailinglist'); ?></th>
					<td>
						<?php
							
						$roles = maybe_unserialize($history -> roles); 	
							
						?>
						<?php if (!empty($roles) && is_array($roles)) : ?>
							<?php 
								
							global $wp_roles;
							$role_names = $wp_roles -> get_names();
							$roles_output = array();
							
							if (!empty($roles) && is_array($roles)) {
								foreach ($roles as $role) {
									$roles_output[] = '<a href="' . admin_url('users.php?role=' . $role) . '">' . esc_html($role_names[$role]) . '</a>';
								}
								
								$roles_output = implode(", ", $roles_output);
							}
							
							echo $roles_output;
							
							?>
						<?php else : ?>
							<?php esc_html_e('None', 'wp-mailinglist'); ?>
						<?php endif; ?>
					</td>
				</tr>
	            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	            	<th><?php esc_html_e('Template', 'wp-mailinglist'); ?></th>
	                <td>
	                	<?php $Db -> model = $Theme -> model; ?>
	                    <?php if (!empty($history -> theme_id) && $theme = $Db -> find(array('id' => $history -> theme_id))) : ?>
	                    	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html( $theme -> id); ?>'}); return false;" title="<?php esc_html_e('Template Preview:', 'wp-mailinglist'); ?> <?php echo esc_html( $theme -> title); ?>"><?php echo esc_html( $theme -> title); ?></a>
	                    	<a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo esc_html($theme -> title); ?>', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html( $theme -> id); ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a>
	                    	<a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', 'wp-mailinglist'), esc_html($theme -> title)); ?>', href:newsletters_ajaxurl + 'action=newsletters_themeedit&security=<?php echo esc_html( wp_create_nonce('themeedit')); ?>&id=<?php echo esc_html( $theme -> id); ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a>
	                    <?php else : ?>
	                    	<?php esc_html_e('None', 'wp-mailinglist'); ?>
	                    <?php endif; ?>
	                </td>
	            </tr>
	            <?php if (!empty($history -> post_id)) : ?>
	            	<?php $post = get_post($history -> post_id); ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            	<th><?php esc_html_e('Post', 'wp-mailinglist'); ?>
		            	<?php echo ( $Html -> help(__('If a post/page was published from this newsletter, it will be linked/associated and shown here.', 'wp-mailinglist'))); ?></th>
		            	<td>
			            	<a href="<?php echo get_permalink($history -> post_id); ?>" target="_blank"><?php echo esc_html($post -> post_title); ?></a>
			            	<a class="" href="<?php echo get_delete_post_link($post -> ID); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete this post?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
			            	<a class="" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=unlinkpost&id=' . $history -> id)) ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to unlink this post from this newsletter?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-unlink"></i></a>
		            	</td>
	            	</tr>
	            <?php endif; ?>
	            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	            	<th><?php esc_html_e('Author', 'wp-mailinglist'); ?></th>
	            	<td>
	            		<?php if (!empty($history -> user_id)) : ?>
	            			<?php $user = $this -> userdata($history -> user_id); ?>
	            			<a href="<?php echo get_edit_user_link($user -> ID); ?>"><?php echo esc_html( $user -> display_name); ?></a>
	            		<?php else : ?>
	            			<?php esc_html_e('None', 'wp-mailinglist'); ?></td>
	            		<?php endif; ?>
	            	</td>
	            </tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            <th><?php esc_html_e('Recurring', 'wp-mailinglist'); ?></th>
		            <td>
			            <?php if (!empty($history -> recurring) && $history -> recurring == "Y") : ?>
                    		<?php esc_html_e('Yes', 'wp-mailinglist'); ?>
                    		<?php $helpstring = sprintf(__('Send every %s %s', 'wp-mailinglist'), $history -> recurringvalue, $history -> recurringinterval); ?>
                    		<?php if (!empty($history -> recurringlimit)) : ?><?php $helpstring .= sprintf(__(' and repeat %s times', 'wp-mailinglist'), $history -> recurringlimit); ?><?php endif; ?>
                    		<?php $helpstring .= sprintf(__(' starting %s and has been sent %s times already'), $history -> recurringdate, $history -> recurringsent); ?>
                    		(<?php echo $helpstring; ?>)
                    	<?php else : ?>
                    		<?php esc_html_e('No', 'wp-mailinglist'); ?>
                    	<?php endif; ?>
		            </td>
	            </tr>
	            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            <th><?php esc_html_e('Scheduled', 'wp-mailinglist'); ?></th>
		            <td>
		            	<?php if (!empty($history -> scheduled) && $history -> scheduled == "Y") : ?>
		            		<?php esc_html_e('Yes', 'wp-mailinglist'); ?> - <strong><?php echo esc_html( $history -> senddate); ?></strong>
		            	<?php else : ?>
			            	<?php esc_html_e('No', 'wp-mailinglist'); ?>
			            <?php endif; ?>
		            </td>
	            </tr>
	            <?php $Db -> model = $this -> Autoresponder() -> model; ?>
	            <?php if ($autoresponders = $Db -> find_all(array('history_id' => $history -> id))) : ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
		            	<th><?php esc_html_e('Autoresponders', 'wp-mailinglist'); ?>
		            	<?php echo ( $Html -> help(__('Autoresponders linked to this newsletter', 'wp-mailinglist'))); ?></th>
		            	<td>
			            	<ul>
				            	<?php foreach ($autoresponders as $autoresponder) : ?>
				            		<li><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&amp;method=save&amp;id=' . $autoresponder -> id)) ?>"><?php echo esc_attr($autoresponder -> title); ?></a></li>
				            	<?php endforeach; ?>
			            	</ul>
		            	</td>
	            	</tr>
	            <?php endif; ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Tracking', 'wp-mailinglist'); ?></th>
					<td>
						<?php 
							
						global $wpdb; $Db -> model = $Email -> model;
						$etotal = $Db -> count(array('history_id' => $history -> id));
						$eread = $Db -> count(array('read' => "Y", 'history_id' => $history -> id));
						$tracking = ((!empty($etotal)) ? (($eread/$etotal) * 100) : 0);
						
						$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'";
						
						$query_hash = md5($query);
						if ($ob_ebounced = $this -> get_cache($query_hash)) {
							$ebounced = $ob_ebounced;
						} else {
							$ebounced = $wpdb -> get_var($query);
							$this -> set_cache($query_hash, $ebounced);
						}
						
						$ebouncedperc = (!empty($etotal)) ? number_format((($ebounced/$etotal) * 100), 2, '.', '') : 0;
						
						$query = "SELECT COUNT(DISTINCT `email`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'";
						
						$query_hash = md5($query);
						if ($ob_eunsubscribed = $this -> get_cache($query_hash)) {
							$eunsubscribed = $ob_eunsubscribed;
						} else {
							$eunsubscribed = $wpdb -> get_var($query);
							$this -> set_cache($query_hash, $eunsubscribed);
						}
						
						$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
						$clicks = $this -> Click() -> count(array('history_id' => $history -> id));
						
						?>
						<?php 
						
						echo sprintf(__('%s read %s, %s%s unsubscribes%s %s, %s%s bounces%s %s and %s%s clicks%s out of %s emails sent out', 'wp-mailinglist'), 
						'<strong>' . $eread . '</strong>', 
						'(' . ((!empty($etotal)) ? number_format($tracking, 2, '.', '') : 0) . '&#37;)', 
						'<a href="' . admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=unsubscribes&history_id=' . $history -> id) . '">',
						'<strong>' . $eunsubscribed . '</strong>', 
						'</a>',
						'(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', 
						'<a href="' . admin_url('admin.php?page=' . $this -> sections -> subscribers . '&method=bounces&history_id=' . $history -> id) . '">',
						'<strong>' . (empty($ebounced) ? 0 : $ebounced) . '</strong>', 
						'</a>',
						'(' . $ebouncedperc . '&#37;)', 
						'<a href="?page=' . $this -> sections -> clicks . '&amp;history_id=' . $history -> id . '">', 
						'<strong>' . $clicks . '</strong>', 
						'</a>', 
						'<strong>' . $etotal . '</strong>'); 
						
						$options = array();
						
						$data = array(
							'datasets'		=>	array(
								array(
									'data'					=>	array(
										number_format($tracking, 0, '.', ''),
										number_format((100 - $tracking), 0, '.', ''),
										number_format($ebouncedperc, 0, '.', ''),
										number_format($eunsubscribeperc, 0, '.', ''),
									),
									'backgroundColor'		=>	array(
										'#46BFBD',
										'#949FB1',
										'#F7464A',
										'#FDB45C',
									)
								)
							),
							'labels'		=>	array(
								__('Read', 'wp-mailinglist'),
								__('Unread', 'wp-mailinglist'),
								__('Bounced', 'wp-mailinglist'),
								__('Unsubscribed', 'wp-mailinglist'),
							)
						);
							
						$Html -> pie_chart('email-chart-' . $history -> id, array('width' => 150, 'height' => 150), $data, $options); 
						
						?>
					</td>
				</tr>
	            <?php if (!empty($history -> attachments)) : ?>
	            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
	                	<th><?php esc_html_e('Attachments', 'wp-mailinglist'); ?></th>
	                    <td>
	                    	<ul style="padding:0; margin:0;">
								<?php foreach ($history -> attachments as $attachment) : ?>
	                            	<li class="<?php echo esc_html($this -> pre); ?>attachment">
	                                	<?php echo esc_url_raw( $Html -> attachment_link($attachment, false)); ?>
	                                    <a class="button button-primary" href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=removeattachment&amp;id=<?php echo esc_html( $attachment['id']); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to remove this attachment?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-trash"></i></a>
	                                </li>
	                            <?php endforeach; ?>
	                        </ul>
	                    </td>
	                </tr>
	            <?php endif; ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
					<td><abbr title="<?php echo esc_html( $history -> created); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($history -> created))); ?></abbr></td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
					<td><abbr title="<?php echo esc_html( $history -> modified); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($history -> modified))); ?></abbr></td>
				</tr>
			</tbody>
		</table>
	</div>
    
    <!-- Individual Emails -->
    <h3 id="emailssent"><?php esc_html_e('Emails Sent', 'wp-mailinglist'); ?></h3>
    <?php $this -> render('emails' . DS . 'loop', array('history' => $history, 'emails' => $emails, 'paginate' => $paginate), true, 'admin'); ?>
    
    <!-- History Preview -->
    <h3><?php esc_html_e('Preview', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=' . $this -> pre . 'historyiframe&id=' . $history -> id . '&security=' . wp_create_nonce('historyiframe'))) ?>" target="_blank" class="add-new-h2"><?php esc_html_e('Open in New Window', 'wp-mailinglist'); ?></a></h3>
	<?php $multimime = $this -> get_option('multimime'); ?>
	<?php if (!empty($history -> text) && $multimime == "Y") : ?>  
		<h4><?php esc_html_e('TEXT Version', 'wp-mailinglist'); ?></h4>  
	    <div class="scroll-list">
	    	<?php echo nl2br($history -> text); ?>
	    </div>
	    
	    <h4><?php esc_html_e('HTML Version', 'wp-mailinglist'); ?></h4>
	<?php endif; ?>
    <div class="postbox" style="padding:10px;">
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo esc_url_raw($preview_src); ?>" id="historypreview<?php echo esc_html( $history -> id); ?>"></iframe>
    </div>
    
	<div class="tablenav">
	
	</div>
</div>