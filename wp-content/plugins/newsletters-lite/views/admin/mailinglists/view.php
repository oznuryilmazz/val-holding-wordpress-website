<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('View List:', 'wp-mailinglist'); ?> <?php echo esc_html($mailinglist -> title); ?></h2>
	
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; All Mailing Lists', 'wp-mailinglist'), $this -> url, array('title' => __('Manage All Mailing Lists', 'wp-mailinglist')))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft">
			<a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&method=offsite&listid=<?php echo esc_html( $mailinglist -> id); ?>" class="button"><i class="fa fa-code"></i> <?php esc_html_e('Offsite Form', 'wp-mailinglist'); ?></a>
			<a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&method=save&id=<?php echo esc_html( $mailinglist -> id); ?>" class="button"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit', 'wp-mailinglist'); ?></a>
			<a href="?page=<?php echo esc_html( $this -> sections -> lists); ?>&method=delete&id=<?php echo esc_html( $mailinglist -> id); ?>" class="button button-highlighted" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this mailing list?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-times"></i> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
			<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> lists . '&method=deletesubscribers&id=' . $mailinglist -> id)) ?>" class="button button-secondary" onclick="if (!confirm('<?php esc_html_e('Are you sure you want to delete all subscribers in this mailing list?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-user-times fa-fw"></i> <?php esc_html_e('Delete Subscribers', 'wp-mailinglist'); ?></a>
		</div>
	</div>
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
				<th><?php esc_html_e('Title', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html($mailinglist -> title); ?></td>
			</tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php esc_html_e('Group', 'wp-mailinglist'); ?></th>
                <td>
                	<?php if (!empty($mailinglist -> group_id) && !empty($mailinglist -> group)) : ?>
                    	<?php echo ( $Html -> link(esc_html($mailinglist -> group -> title), '?page=' . $this -> sections -> groups . '&method=view&id=' . $mailinglist -> group_id)); ?>
                    <?php else : ?>
                    	<?php esc_html_e('none', 'wp-mailinglist'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($mailinglist -> adminemail)) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            		<th><?php esc_html_e('Admin Email', 'wp-mailinglist'); ?></th>
            		<td><?php echo esc_html( $mailinglist -> adminemail); ?></td>
            	</tr>
            <?php endif; ?>
            <?php if (!empty($mailinglist -> redirect)) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            		<th><?php esc_html_e('Confirm Redirect URL', 'wp-mailinglist'); ?></th>
            		<td><?php echo '<a href="' . esc_attr(wp_unslash(esc_html($mailinglist -> redirect))) . '" target="_blank">' . esc_html($mailinglist -> redirect) . '</a>'; ?></td>
            	</tr>
            <?php endif; ?>
            <?php if (!empty($mailinglist -> subredirect)) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            		<th><?php esc_html_e('Subscribe Redirect URL', 'wp-mailinglist'); ?></th>
            		<td><?php echo '<a href="' . esc_attr(wp_unslash(esc_html($mailinglist -> subredirect))) . '" target="_blank">' . esc_html($mailinglist -> subredirect) . '</a>'; ?></td>
            	</tr>
            <?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Subscribers', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $SubscribersList -> count(array('list_id' => $mailinglist -> id))); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Private List', 'wp-mailinglist'); ?></th>
				<td><?php echo (empty($mailinglist -> privatelist) || $mailinglist -> privatelist == "N") ? __('No', 'wp-mailinglist') : __('Yes', 'wp-mailinglist'); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Paid List', 'wp-mailinglist'); ?></th>
				<td><?php echo (empty($mailinglist -> paid) || $mailinglist -> paid == "N") ? __('No', 'wp-mailinglist') : __('Yes', 'wp-mailinglist'); ?></td>
			</tr>
			<?php if ($mailinglist -> paid == "Y") : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Price', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $Html -> currency()); ?><?php echo esc_html( $mailinglist -> price); ?></td>
				</tr>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
					<th><?php esc_html_e('Paid Interval', 'wp-mailinglist'); ?></th>
					<td>
						<?php
						
						$intervals = array(
							'daily'			=>	__('Daily', 'wp-mailinglist'),
							'weekly'		=>	__('Weekly', 'wp-mailinglist'),
							'monthly'		=>	__('Monthly', 'wp-mailinglist'),
							'2months'		=>	__('Every Two Months', 'wp-mailinglist'),
							'3months'		=>	__('Every Three Months', 'wp-mailinglist'),
							'biannually'	=>	__('Twice Yearly (Six Months)', 'wp-mailinglist'),
							'9months'		=>	__('Every Nine Months', 'wp-mailinglist'),
							'yearly'		=>	__('Yearly', 'wp-mailinglist'),
							'once'			=>	__('Once Off', 'wp-mailinglist'),
						);
						
						?>
						<?php echo esc_html( $intervals[$mailinglist -> interval]); ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $mailinglist -> created); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $mailinglist -> modified); ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="subscribers"><?php esc_html_e('Subscribers', 'wp-mailinglist'); ?> <?php echo ( $Html -> link(__('Add New', 'wp-mailinglist'), '?page=' . $this -> sections -> subscribers . '&method=save&mailinglist_id=' . $mailinglist -> id, array('class' => "add-new-h2"))); ?></h3>
	<?php $this -> render('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate), true, 'admin'); ?>
</div>