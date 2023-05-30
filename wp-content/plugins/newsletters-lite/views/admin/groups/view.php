<?php // phpcs:ignoreFile ?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('View Group:', 'wp-mailinglist'); ?> <?php echo esc_html($group -> title); ?></h2>
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; All Groups', 'wp-mailinglist'), $this -> url, array('title' => __('Manage All Groups', 'wp-mailinglist')))); ?></div>
	
	<div class="tablenav">
		<div class="alignleft">
			<a href="?page=<?php echo esc_html( $this -> sections -> groups); ?>&amp;method=save&amp;id=<?php echo esc_html( $group -> id); ?>" class="button"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit', 'wp-mailinglist'); ?></a>
			<a href="?page=<?php echo esc_html( $this -> sections -> groups); ?>&amp;method=delete&amp;id=<?php echo esc_html( $group -> id); ?>" class="button button-highlighted" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this group?', 'wp-mailinglist'); ?>')) { return false; }"><i class="fa fa-times"></i> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
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
				<td><?php echo esc_html($group -> title); ?></td>
			</tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<th><?php esc_html_e('Lists', 'wp-mailinglist'); ?></th>
                <td>
                	<?php echo ( $Html -> link($Mailinglist -> count(array('group_id' => $group -> id)), '#mailinglists')); ?>
                </td>
            </tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $group -> created); ?></td>
			</tr>
			<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
				<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
				<td><?php echo esc_html( $group -> modified); ?></td>
			</tr>
		</tbody>
	</table>
	
	<h3 id="mailinglists"><?php esc_html_e('Mailing Lists', 'wp-mailinglist'); ?> <?php echo ( $Html -> link(__('Add New', 'wp-mailinglist'), '?page=' . $this -> sections -> lists . '&amp;method=save&amp;group_id=' . $group -> id, array('class' => "add-new-h2"))); ?></h3>
	<?php $this -> render('mailinglists' . DS . 'loop', array('mailinglists' => $mailinglists, 'paginate' => $paginate), true, 'admin'); ?>
</div>