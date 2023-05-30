<?php // phpcs:ignoreFile ?>
<div class="newsletters <?php echo esc_html($this -> pre); ?>unsubscribe <?php echo esc_html($this -> pre); ?>">
	<?php global $wpdb, $Mailinglist; ?>	
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<?php if ($dorender) : ?>
		<?php if (!empty($success) && $success == true) : ?>
			<h2><?php esc_html_e('Unsubscribe Successful', 'wp-mailinglist'); ?></h2>
			<p>
				<?php esc_html_e('You have successfully unsubscribed from the selected list(s).', 'wp-mailinglist'); ?><br/>
				<?php esc_html_e('You will no longer receive correspondence on the list(s) unsubscribed from.', 'wp-mailinglist'); ?>
			</p>
			
			<?php $resubscribe = $this -> get_option('resubscribe'); ?>
			<?php if (!empty($resubscribe)) : ?>
				<p>
					<?php echo sprintf(__('Was this a mistake? If it was, you can %s', 'wp-mailinglist'), $this -> gen_resubscribe_link($subscriber)); ?>
				</p>
			<?php endif; ?>
			
			<?php if (empty($deleted) && $deleted == false) : ?>
				<ul>
					<li><?php esc_html_e('Go back to', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw(home_url()); ?>" title="<?php echo esc_attr(wp_unslash(get_bloginfo('name'))); ?>"><?php echo get_bloginfo('name'); ?></a></li>
					<li><?php echo ( $Html -> link(__('Manage Subscriptions', 'wp-mailinglist'), $Html -> retainquery('email=' . $subscriber -> email, $this -> get_managementpost(true)))); ?></li>
				</ul>
			<?php endif; ?>
		<?php elseif (!empty($data)) : ?>
			<h2><?php esc_html_e('Unsubscribe Confirmation', 'wp-mailinglist'); ?></h2>
			<form action="<?php echo wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))); ?>" method="post">
				<?php foreach ($data as $gkey => $gval) : ?>
					<input type="hidden" name="<?php echo esc_html( $gkey); ?>" value="<?php echo esc_attr($gval); ?>" />
				<?php endforeach; ?>
				
				<p><?php esc_html_e('Tick/check the list(s) below from which you want to unsubscribe.', 'wp-mailinglist'); ?></p>
				
				<table class="table newsletters_table">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e('Email Address:', 'wp-mailinglist'); ?></strong></td>
							<td><?php echo esc_html( $subscriber -> email); ?></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e('You are unsubscribing from:', 'wp-mailinglist'); ?></strong></td>
							<td>
								<?php if (!empty($subscriber -> mailinglists)) : ?>
									<?php if ($listsselect = $Mailinglist -> select(true, $subscriber -> mailinglists)) : ?>
										<?php foreach ($listsselect as $list_id => $list_title) : ?>
											<div class="checkbox">
												<label class="<?php echo esc_html($this -> pre); ?>checkboxlabel <?php echo esc_html($this -> pre); ?>">
													<input checked="checked" class="<?php echo esc_html($this -> pre); ?>checkbox" <?php echo (!empty($data['unsubscribelists']) && in_array($list_id, $data['unsubscribelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="unsubscribelists[]" value="<?php echo esc_attr(wp_unslash($list_id)); ?>" id="lists_<?php echo esc_html( $list_id); ?>" /> <?php esc_html_e(wp_unslash($list_title)); ?>
												</label>
											</div>
										<?php endforeach; ?>
									<?php else : ?>
										<?php esc_html_e('No mailing lists', 'wp-mailinglist'); ?>
									<?php endif; ?>
								<?php else : ?>
									<?php esc_html_e('No mailing lists', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
						</tr>
						
						<?php 
								
						$otherlists_ids = $Subscriber -> mailinglists($subscriber -> id, false, $subscriber -> mailinglists); 
						$otherlists = (!empty($otherlists_ids)) ? $Mailinglist -> select(true, $otherlists_ids) : false;
						
						if (!empty($otherlists)) :
						
						?>
						
						<tr>
							<td><strong><?php esc_html_e('Other subscribed list(s):', 'wp-mailinglist'); ?></strong></td>
							<td>
								<?php if (!empty($otherlists)) : ?>
									<?php foreach ($otherlists as $otherlist_id => $otherlist_title) : ?>
										<div class="checkbox">
											<label class="<?php echo esc_html($this -> pre); ?>checkboxlabel <?php echo esc_html($this -> pre); ?>">
												<input class="<?php echo esc_html($this -> pre); ?>checkbox" <?php echo (!empty($data['unsubscribelists']) && in_array($otherlist_id, $data['unsubscribelists'])) ? 'checked="checked"' : ''; ?> type="checkbox" name="unsubscribelists[]" value="<?php echo esc_attr(wp_unslash($otherlist_id)); ?>" id="lists_<?php echo esc_html( $otherlist_id); ?>" /> <?php esc_html_e(wp_unslash($otherlist_title)); ?>
											</label>
										</div>
									<?php endforeach; ?>
								<?php else : ?>
									<?php esc_html_e('No other subscriptions.', 'wp-mailinglist'); ?>
								<?php endif; ?>
							</td>
						</tr>
						
						<?php endif; ?>
					</tbody>
				</table>
				
				<?php if ($this -> get_option('unsubscribecomments') == "Y") : ?>
					<h3><?php esc_html_e('Comments', 'wp-mailinglist'); ?> <?php esc_html_e('(optional)', 'wp-mailinglist'); ?></h3>
					<p>
						<textarea name="<?php echo esc_html($this -> pre); ?>comments" style="width:97%;" rows="5" class="form-control widefat"><?php echo wp_kses_post( wp_unslash(htmlentities(strip_tags($data[$this -> pre . 'comments']), false, get_bloginfo('charset')))) ?></textarea>
					</p>
				<?php endif; ?>
				
				<p class="submit">
					<button value="1" type="submit" name="confirm" value="1" class="newsletters_button btn btn-primary">
						<?php esc_html_e('Confirm Unsubscribe', 'wp-mailinglist'); ?>
					</button>
				</p>
			</form>
			
			<p><?php echo ( $Html -> link(__('&larr; Manage Subscriptions', 'wp-mailinglist'), $Html -> retainquery('email=' . $subscriber -> email, $this -> get_managementpost(true)))); ?></p>
		<?php else : ?>
			<?php foreach ($errors as $err) : ?>
				&raquo; <?php echo wp_kses_post($err); ?><br/>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>