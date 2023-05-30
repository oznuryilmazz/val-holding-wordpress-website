<?php // phpcs:ignoreFile ?>
<!-- Latest Posts Subscriptions Settings -->

<?php
	
$latestpostssubscriptions = $this -> Latestpostssubscription() -> find_all();	
	
?>

<p><?php echo sprintf(__('The current time is: %s', 'wp-mailinglist'), $Html -> gen_date(false, false, false, true)); ?></p>

<table class="widefat" id="latestposts_table">
	<thead>
		<tr>
			<th><?php esc_html_e('Subject', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('Interval', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('Status', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('Lists', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('Posts', 'wp-mailinglist'); ?></th>
			<th><?php esc_html_e('Actions', 'wp-mailinglist'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (empty($latestpostssubscriptions)) : ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="6"><?php esc_html_e('No latest posts subscriptions', 'wp-mailinglist'); ?></td>
			</tr>
		<?php else : ?>
			<?php foreach ($latestpostssubscriptions as $latestpostssubscription) : ?>
				<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>" id="latestposts_row_<?php echo esc_html( $latestpostssubscription -> id); ?>">
					<td>
						<span class="row-title"><?php echo esc_html( $latestpostssubscription -> subject); ?></span>
						<div class="row-actions">
							<span class="edit"><a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&amp;method=runschedule&amp;hook=newsletters_latestposts&id=' . $latestpostssubscription -> id)) ?>"><?php esc_html_e('Run Now', 'wp-mailinglist'); ?></a></span>
							<?php if (!empty($latestpostssubscription -> history_id)) : ?>
								<span class="edit">| <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history . '&method=view&id=' . $latestpostssubscription -> history_id)) ?>"><?php esc_html_e('View Newsletter', 'wp-mailinglist'); ?></a></span>
							<?php endif; ?>
						</div>
					</td>
					<td>
						<?php if (!empty($latestpostssubscription -> interval)) : ?>
							<?php echo wp_kses_post( $Html -> next_scheduled('newsletters_latestposts', array((int) $latestpostssubscription -> id))); ?>
						<?php else : ?>
							<span class="newsletters_error"><?php esc_html_e('None', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if (empty($latestpostssubscription -> status) || $latestpostssubscription -> status == "active") : ?>
							<span class="newsletters_success"><i class="fa fa-check"></i></span>
						<?php else : ?>
							<span class="newsletters_error"><i class="fa fa-times"></i></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if (!empty($latestpostssubscription -> lists)) : ?>
							<?php $lists = maybe_unserialize($latestpostssubscription -> lists); ?>
							<?php $l = 1; ?>
							<?php foreach ($lists as $list_id) : ?>
								<?php $list = $Mailinglist -> get($list_id); ?>
								<?php echo '<a href="' . admin_url('admin.php?page=' . $this -> sections -> lists . '&method=view&id=' . $list_id) . '">' . esc_html($list -> title) . '</a>'; ?>
								<?php echo ($l < count($lists)) ? ', ' : ''; ?>
								<?php $l++; ?>
							<?php endforeach; ?>
						<?php else : ?>
							<span class="newsletters_error"><?php esc_html_e('None', 'wp-mailinglist'); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php 
						
						$posts_used = $this -> get_latestposts_used($latestpostssubscription);	
						
						?>
						
						<a href="" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_lpsposts&id=<?php echo esc_html( $latestpostssubscription -> id); ?>&security=<?php echo esc_html( wp_create_nonce('lpsposts')); ?>'}); return false;" id="latestposts_history_count_<?php echo esc_attr(wp_unslash($latestpostssubscription -> id)); ?>"><?php echo sprintf(__('%s posts used/sent', 'wp-mailinglist'), $posts_used); ?></a>
						<a onclick="if (confirm('<?php esc_html_e('Are you sure you want to clear the posts history for this latest posts subscription?', 'wp-mailinglist'); ?>')) { latestposts_clearhistory('<?php echo esc_attr(wp_unslash($latestpostssubscription -> id)); ?>'); } return false;" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings . '&amp;method=clearlpshistory&id=' . $latestpostssubscription -> id)) ?>" class=""><i class="fa fa-trash"></i></a>
						<span id="latestposts_history_loading_<?php echo esc_attr(wp_unslash($latestpostssubscription -> id)); ?>" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
					</td>
					<td>
						<?php if (empty($latestpostssubscription -> status) || $latestpostssubscription -> status == "active") : ?>
							<a href="" id="latestpostssubscription_changestatus_<?php echo esc_html( $latestpostssubscription -> id); ?>" onclick="latestposts_changestatus('<?php echo esc_html( $latestpostssubscription -> id); ?>','inactive'); return false;" class="button"><i class="fa fa-pause"></i></a>
						<?php else : ?>
							<a href="" id="latestpostssubscription_changestatus_<?php echo esc_html( $latestpostssubscription -> id); ?>" onclick="latestposts_changestatus('<?php echo esc_html( $latestpostssubscription -> id); ?>','active'); return false;" class="button"><i class="fa fa-play"></i></a>
						<?php endif; ?>
						<a href="" title="<?php echo esc_attr(wp_unslash($latestpostssubscription -> subject)); ?>" onclick="jQuery(this).colorbox({iframe:true, width:'80%', height:'80%', href:newsletters_ajaxurl + 'action=wpmllatestposts_preview&id=<?php echo esc_html( $latestpostssubscription -> id); ?>&security=<?php echo esc_html( wp_create_nonce('latestposts_preview')); ?>'}); return false;" class="button"><i class="fa fa-eye fa-fw"></i></a>
						<a href="" onclick="jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_latestposts_save&id=<?php echo esc_html( $latestpostssubscription -> id); ?>'}); return false;" class="button editrow"><i class="fa fa-pencil fa-fw"></i></a>
						<a href="" id="latestposts_delete_<?php echo esc_html( $latestpostssubscription -> id); ?>" onclick="if (confirm('<?php esc_html_e('Are you sure you want to delete this latest posts subscription?', 'wp-mailinglist'); ?>')) { latestposts_del_row('<?php echo esc_html( $latestpostssubscription -> id); ?>'); } return false;" class="button delrow"><i class="fa fa-trash"></i></a>
						<span id="latestposts_loading_<?php echo esc_html( $latestpostssubscription -> id); ?>" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<p>
	<button type="button" class="button latestposts-addrow">
		<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Instance', 'wp-mailinglist'); ?>
	</button>
</p>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.latestposts-addrow').on('click', function(event) {
		latestposts_add_row();
		return false;
	});
});

function latestposts_changestatus(id, status) {
	jQuery('#latestpostssubscription_changestatus_' + id).attr('disabled', "disabled").html('<i class="fa fa-refresh fa-spin"></i>');
	jQuery.ajax({
		type: "POST",
		url: newsletters_ajaxurl + 'action=newsletters_latestposts_changestatus&security=<?php echo esc_html( wp_create_nonce('latestposts_changestatus')); ?>',
		data: {
			id:id, 
			status:status
		}
	}).done(function(response) {
		wpml_scroll('#latestposts_wrapper');
		jQuery('#latestposts_wrapper').html(response).fadeIn();
	});
}

function latestposts_add_row() {	
	jQuery.colorbox({href:newsletters_ajaxurl + 'action=newsletters_latestposts_save'});
}

function latestposts_del_row(id) {
	jQuery('#latestposts_delete_' + id).attr('disabled', "disabled").html('<i class="fa fa-refresh fa-spin"></i>');
	jQuery.post(newsletters_ajaxurl + 'action=newsletters_latestposts_delete&id=' + id + '&security=<?php echo esc_html( wp_create_nonce('latestposts_delete')); ?>', false, function(response) {
		jQuery('#latestposts_row_' + id).remove();
	});
}

function latestposts_clearhistory(id) {
	var count = jQuery('#latestposts_history_count_' + id);
	var loading = jQuery('#latestposts_history_loading_' + id);
	loading.show();
	
	jQuery.ajax({
		type: "POST",
		url: newsletters_ajaxurl + 'action=newsletters_latestposts_clearhistory&security=<?php echo esc_html( wp_create_nonce('latestposts_clearhistory')); ?>',
		data: {
			id: id
		}
	}).done(function(data, textStatus, jqXHR) {		
		// this is successful
		count.html('<?php echo esc_attr(wp_unslash(__('0 posts used/sent', 'wp-mailinglist'))); ?>');
	}).error(function(data) {
		alert('<?php esc_html_e('Ajax call failed, please try again', 'wp-mailinglist'); ?>');
	}).always(function(data) {
		loading.hide();
	});
}
</script>