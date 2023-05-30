<?php // phpcs:ignoreFile ?>
<?php if (!empty($histories)) : ?>
	<table class="widefat">
		<tbody>
			<?php $class = false; ?>
			<?php foreach ($histories as $history) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<td>
						<?php
			
						global $wpdb;
						$Db -> model = $Email -> model;
						$etotal = $Db -> count(array('history_id' => $history -> id));
						$eread = $Db -> count(array('history_id' => $history -> id, 'read' => "Y"));
						$tracking = (!empty($etotal)) ? ($eread / $etotal) * 100 : 0;
						$ebounced = $wpdb -> get_var("SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
						$eunsubscribed = $wpdb -> get_var("SELECT COUNT(DISTINCT `email`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . $history -> id . "'");
						$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
						$clicks = $this -> Click() -> count(array('history_id' => $history -> id));
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
						<h3>
							<p class="submit">
								<a class="button button-secondary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id)) ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View', 'wp-mailinglist'); ?></a>
								<a class="button button-primary" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> send . '&amp;method=history&amp;id=' . $history -> id)) ?>"><i class="fa fa-paper-plane"></i> <?php esc_html_e('Send/Edit', 'wp-mailinglist'); ?></a>
							</p>
							<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $history -> id)) ?>"><?php echo esc_attr($history -> subject); ?></a>
							<p class="howto">
								<?php echo sprintf(__('Created on %s by %s', 'wp-mailinglist'), $Html -> gen_date(false, strtotime($history -> created)), ((!empty($history -> user_id)) ? get_the_author_meta('display_name', $history -> user_id) : __('unknown', 'wp-mailinglist'))); ?>
							</p>
						</h3>
						<p><?php echo wp_kses_post( $Html -> truncate(strip_tags(do_shortcode($this -> strip_set_variables(esc_html($history -> message)))), 500)); ?></p>
						<p>
							<?php 
											
							$Db -> model = $Email -> model;
							$etotal = $Db -> count(array('history_id' => $history -> id));
							$eread = $Db -> count(array('history_id' => $history -> id, 'read' => "Y"));	
							
							global $wpdb;
							$tracking = (!empty($etotal)) ? ($eread/$etotal) * 100 : 0;
							
							$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . $history -> id . "'";
							
							$query_hash = md5($query);
							if ($ob_ebounced = $this -> get_cache($query_hash)) {
								$ebounced = $ob_ebounced;
							} else {
								$ebounced = $wpdb -> get_var($query);
								$this -> set_cache($query_hash, $ebounced);
							}
							
							$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
							
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
							<a href="?page=<?php echo esc_html( $this -> sections -> history); ?>&amp;method=view&amp;id=<?php echo esc_html( $history -> id); ?>"><?php echo sprintf("%s / %s / %s / %s", '<span style="color:#46BFBD;">' . number_format($tracking, 2, '.', '') . '&#37;</span>', '<span style="color:#FDB45C;">' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;</span>', '<span style="color:#F7464A;">' . number_format($ebouncedperc, 2, '.', '') . '&#37;</span>', $clicks); ?></a>
							<?php echo ( $Html -> help(sprintf(__('%s read %s, %s unsubscribes %s, %s bounces %s and %s clicks out of %s emails sent out', 'wp-mailinglist'), '<strong>' . $eread . '</strong>', '(' . ((!empty($etotal)) ? number_format((($eread/$etotal) * 100), 2, '.', '') : 0) . '&#37;)', '<strong>' . $eunsubscribed . '</strong>', '(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', '<strong>' . $ebounced . '</strong>', '(' . number_format($ebouncedperc, 2, '.', '') . '&#37;)', '<strong>' . $clicks . '</strong>', '<strong>' . $etotal . '</strong>'))); ?>
						</p>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
	<p class="textright">
		<a class="button button-primary button-hero" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history)) ?>"><?php esc_html_e('View All Newsletters', 'wp-mailinglist'); ?></a>
		<?php esc_html_e('or', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> send)) ?>"><?php esc_html_e('create a new one', 'wp-mailinglist'); ?></a>
	</p>
<?php else : ?>
	<p>
		<?php esc_html_e('Sent emails and saved drafts will be displayed here as soon as you create them.', 'wp-mailinglist'); ?>
	</p>
<?php endif; ?>