<?php // phpcs:ignoreFile ?>
<!-- Queue Errors Page -->

<?php
		
$batchnumber = (empty($_GET['batchnumber'])) ? 1 :  sanitize_text_field(wp_unslash($_GET['batchnumber']));
	
$queue_status = $this -> get_option('queue_status');
$count = $this -> qp_get_queued_count();

?>	

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?> <?php echo esc_html( $this -> sections -> queue); ?>">
	<h1>
		<?php esc_html_e('Queue Errors', 'wp-mailinglist'); ?>
		<a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> queue)) ?>" class="add-new-h2"><?php echo sprintf(__('Go to Email Queue %s', 'wp-mailinglist'), '<span class="update-plugins count-1"><span class="update-count" id="newsletters-menu-queue-count">' . $count . '</span></span>'); ?></a>
	</h1>
	
	<?php if (!empty($errors)) : ?>
		<br class="clear" />
		<h2><?php esc_html_e('Queue Errors', 'wp-mailinglist'); ?></h2>
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
							<td><?php echo ( $Html -> link(esc_html($data['subject']), "?page=" . $this -> sections -> history . "&amp;method=view&amp;id=" . $data['history_id'])); ?></td>
		                    <td>
		                    	<i class="fa fa-exclamation-triangle fa-fw newsletters_error"></i> <?php echo esc_html( $data['error']); ?>
		                    </td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p><?php esc_html_e('There are no errors in the email queue', 'wp-mailinglist'); ?></p>
	<?php endif; ?>
</div>