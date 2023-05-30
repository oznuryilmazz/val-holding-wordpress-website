<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<div style="float:left; margin:0 10px 0 0;">
		<?php echo wp_kses_post( $Html -> get_gravatar($subscriber -> email)); ?>
	</div>
	<h1><?php esc_html_e('View Subscriber:', 'wp-mailinglist'); ?> <?php echo esc_html( $subscriber -> email); ?></h1>
	<br class="clear" />
	
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; All Subscribers', 'wp-mailinglist'), $this -> url, array('title' => __('Manage All Subscribers', 'wp-mailinglist')))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft actions">				
			<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=save&amp;id=<?php echo esc_html( $subscriber -> id); ?>" class="button"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit', 'wp-mailinglist'); ?></a>
			<a href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=delete&amp;id=<?php echo esc_html( $subscriber -> id); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this subscriber?', 'wp-mailinglist'); ?>')) { return false; }" class="button button-highlighted"><i class="fa fa-times"></i> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
			<a href="#emails" class="button"><i class="fa fa-envelope"></i> <?php esc_html_e('Emails Sent', 'wp-mailinglist'); ?></a>
			<?php if (!empty($orders)) : ?>
				<a href="#orders" class="button"><i class="fa fa-money"></i> <?php esc_html_e('Paid Orders', 'wp-mailinglist'); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php $class = ''; ?>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php esc_html_e('Field', 'wp-mailinglist'); ?></th>
				<th><?php esc_html_e('Value', 'wp-mailinglist'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php esc_html_e('Field', 'wp-mailinglist'); ?></th>
				<th><?php esc_html_e('Value', 'wp-mailinglist'); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Email Address', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $subscriber -> email); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Mailing List/s', 'wp-mailinglist'); ?></th>
				<td>
					<?php $Db -> model = $SubscribersList -> model; ?>
					<?php if ($subscriberslists = $Db -> find_all(array('subscriber_id' => $subscriber -> id))) : ?>
						<?php $m = 1; ?>
						<?php foreach ($subscriberslists as $sl) : ?>
							<?php $Db -> model = $Mailinglist -> model; ?>
							<?php if ($mailinglist = $Db -> find(array('id' => $sl -> list_id))) : ?>
								<?php echo ( $Html -> link(esc_html($mailinglist -> title), '?page=' . $this -> sections -> lists . '&amp;method=view&amp;id=' . $mailinglist -> id)); ?> <?php echo ($SubscribersList -> field('active', array('subscriber_id' => $subscriber -> id, 'list_id' => $mailinglist -> id)) == "Y") ? '<span class="newsletters_success">' . $Html -> help(__('Active', 'wp-mailinglist'), '<i class="fa fa-check"></i>') : '<span class="newsletters_error">' . $Html -> help(__('Inactive', 'wp-mailinglist'), '<i class="fa fa-times"></i>'); ?></span>
								<?php 

								if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") {	
									if ($Mailinglist -> has_expired($subscriber -> id, $mailinglist -> id)) {
										echo '<small>(' . __('Expired', 'wp-mailinglist') . ')</small>';
									} else {
										if ($expiration_date = $Mailinglist -> gen_expiration_date($subscriber -> id, $mailinglist -> id)) {
											echo '<small>' . sprintf(__('(Expires %s)', 'wp-mailinglist'), $Html -> gen_date(false, strtotime($expiration_date))) . '</small>';
										}
									}
								}
									
								?>
								<?php if ($m < count($subscriberslists)) : ?>
									<?php echo ', '; ?>
								<?php endif; ?>
								<?php $m++; ?>
							<?php endif; ?>
							<?php $m++; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<?php esc_html_e('none', 'wp-mailinglist'); ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php $saveipaddress = $this -> get_option('saveipaddress'); ?>
			<?php if (!empty($subscriber -> ip_address) && !empty($saveipaddress)) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('IP Address', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $subscriber -> ip_address); ?> <span id="newsletters_subscriber_<?php echo esc_html( $subscriber -> id); ?>_country"><?php echo esc_html($Html -> flag_by_country($subscriber -> country)); ?></span>
					
					<?php if (empty($subscriber -> country)) : ?>
						<a href="" onclick="newsletters_get_country(this); return false;" data-subscriber-id="<?php echo esc_html( $subscriber -> id); ?>" id="newsletters_subscriber_<?php echo esc_html( $subscriber -> id); ?>_get_country"><i class="fa fa-question fa-fw"></i></a>
					<?php endif; ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if (!empty($subscriber -> referer)) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Referrer', 'wp-mailinglist'); ?></th>
					<td><?php echo $subscriber -> referer; ?></td>
				</tr>
			<?php endif; ?>
            
            <!-- Custom Fields -->
			<?php $fields = $FieldsList -> fields_by_list($Subscriber -> mailinglists($subscriber -> id)); ?>
			<?php if (!empty($fields)) : ?>
				<?php foreach ($fields as $field) : ?>
					<?php if (!empty($subscriber -> {$field -> slug})) : ?>
						<?php if ($field -> slug != "email") : ?>
							<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
								<th><?php echo esc_html($field -> title); ?></th>
								<td>
									<?php $newfieldoptions = $field -> newfieldoptions; ?>
									<?php if ($field -> type == "radio" || $field -> type == "select") : ?>
										<?php echo esc_html($newfieldoptions[$subscriber -> {$field -> slug}]); ?>
									<?php elseif ($field -> type == "checkbox") : ?>
										<?php $supoptions = maybe_unserialize($subscriber -> {$field -> slug}); ?>
										<?php if (!empty($supoptions) && is_array($supoptions)) : ?>
											<?php foreach ($supoptions as $supopt) : ?>
												&raquo;&nbsp;<?php echo esc_html($newfieldoptions[$supopt]); ?><br/>
											<?php endforeach; ?>
										<?php else : ?>
											<?php esc_html_e('none', 'wp-mailinglist'); ?>
										<?php endif; ?>
									<?php elseif ($field -> type == "file") : ?>
										<?php echo wp_kses_post( $Html -> file_custom_field($subscriber -> {$field -> slug})); ?>
									<?php elseif ($field -> type == "pre_country") : ?>
										<?php echo esc_html( $this -> Country() -> field('value', array('id' => $subscriber -> {$field -> slug}))); ?>
									<?php elseif ($field -> type == "pre_date") : ?>
										<?php if (is_serialized($subscriber -> {$field -> slug})) : ?>
											<?php $date = @unserialize($subscriber -> {$field -> slug}); ?>
											<?php if (!empty($date) && is_array($date)) : ?>
												<?php echo esc_html( $date['y']); ?>-<?php echo esc_html( $date['m']); ?>-<?php echo esc_html( $date['d']); ?>
											<?php endif; ?>
										<?php else : ?>
											<?php echo esc_html( $Html -> gen_date(false, strtotime($subscriber -> {$field -> slug}))); ?>
										<?php endif; ?>
	                                <?php elseif ($field -> type == "pre_gender") : ?>
	                                	<?php echo wp_kses_post( $Html -> gender($subscriber -> {$field -> slug})); ?>
									<?php else : ?>
										<?php echo esc_html( $subscriber -> {$field -> slug}); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Emails Sent', 'wp-mailinglist'); ?></th>
				<?php
					
				$Db -> model = $Email -> model;
				$emailssent = $Db -> count(array('subscriber_id' => $subscriber -> id));	
					
				?>
				<td><?php echo esc_html( $emailssent); ?> <?php esc_html_e('newsletters', 'wp-mailinglist'); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Links Clicked', 'wp-mailinglist'); ?></th>
				<td><?php echo ( $Html -> link($this -> Click() -> count(array('subscriber_id' => $subscriber -> id)), '?page=' . $this -> sections -> clicks . '&amp;subscriber_id=' . $subscriber -> id)); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Email Format', 'wp-mailinglist'); ?></th>
				<td><?php echo (empty($subscriber -> format) || $subscriber -> format == "html") ? __('Html', 'wp-mailinglist') : __('Text', 'wp-mailinglist'); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Registered', 'wp-mailinglist'); ?></th>
				<td><?php echo (empty($subscriber -> registered) || $subscriber -> registered == "N") ? __('No', 'wp-mailinglist') : __('Yes', 'wp-mailinglist'); ?></td>
			</tr>
			<?php if ($subscriber -> registered == "Y") : ?>
				<?php $user = $Subscriber -> get_user_by_email($subscriber -> email); ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Registered Username', 'wp-mailinglist'); ?></th>
					<td><?php echo (empty($user -> user_login)) ? $user -> data -> user_login : $user -> user_login; ?></td>
				</tr>
			<?php endif; ?>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <th><?php esc_html_e('Bounces', 'wp-mailinglist'); ?></th>
                <td><?php echo esc_html( $subscriber -> bouncecount); ?></td>
            </tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $subscriber -> created); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $subscriber -> modified); ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="emails"><?php esc_html_e('Emails', 'wp-mailinglist'); ?></h3>
	<?php $this -> render('emails' . DS . 'loop', array('emails' => $emails, 'subscriber' => $subscriber, 'paginate' => $paginate), true, 'admin'); ?>
	
	<?php if (!empty($orders)) : ?>
		<h3 id="orders"><?php esc_html_e('Subscription Orders', 'wp-mailinglist'); ?></h3>
		<?php $this -> render('orders' . DS . 'loop', array('orders' => $orders, 'hide_subscriber' => true), true, 'admin'); ?>
	<?php endif; ?>
</div>