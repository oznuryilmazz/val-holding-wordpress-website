<?php // phpcs:ignoreFile ?>
<div id="templates">
	<div class="wrap newsletters">
		<h2><?php esc_html_e('View Snippet', 'wp-mailinglist'); ?> : <?php echo esc_html( $template -> title); ?></h2>
		
		<div style="float:none;" class="subsubsub">
			<?php echo ( $Html -> link(__('&larr; All Snippets', 'wp-mailinglist'), $this -> url, array('title' => __('Manage All Snippets', 'wp-mailinglist')))); ?>
		</div>
		
		<div class="tablenav">
			<div class="alignleft">				
				<a href="?page=<?php echo esc_html( $this -> sections -> send); ?>&method=template&id=<?php echo esc_html( $template -> id); ?>" class="button button-primary"><i class="fa fa-paper-plane"></i> <?php esc_html_e('Send', 'wp-mailinglist'); ?></a>
				<a href="?page=<?php echo esc_html( $this -> sections -> templates_save); ?>&amp;id=<?php echo esc_html( $template -> id); ?>" class="button"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit', 'wp-mailinglist'); ?></a>
				<a href="?page=<?php echo esc_html( $this -> sections -> templates); ?>&amp;method=delete&amp;id=<?php echo esc_html( $template -> id); ?>" onclick="if (!confirm('<?php esc_html_e('Are you sure you wish to remove this snippet?', 'wp-mailinglist'); ?>')) { return false; }" class="button button-highlighted"><i class="fa fa-times"></i> <?php esc_html_e('Delete', 'wp-mailinglist'); ?></a>
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
				<tr class="alternate">
					<th><?php esc_html_e('Title', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html($template -> title); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Created', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $template -> created); ?></td>
				</tr>
				<tr class="alternate">
					<th><?php esc_html_e('Modified', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $template -> modified); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Times Sent', 'wp-mailinglist'); ?></th>
					<td><?php echo esc_html( $template -> sent); ?></td>
				</tr>
			</tbody>
		</table>
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin-top:15px;" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_template_iframe&security=' . wp_create_nonce('template_iframe') . '&id=' . $template -> id)) ?>"></iframe>
		<div class="tablenav">
			
		</div>
	</div>
</div>