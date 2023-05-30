<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<div style="width:600px;">
		<h1><?php esc_html_e('Posts Logged', 'wp-mailinglist'); ?></h1>
		
		<?php if (!empty($latestpostssubscription)) : ?>
			<h2><?php echo esc_html( $latestpostssubscription -> subject); ?></h2>
		<?php endif; ?>
		
		<?php if (!empty($posts)) : ?>
			<div class="tablenav">
			
			</div>
			<table id="posts_table" class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e('ID', 'wp-mailinglist'); ?></th>
						<th><?php esc_html_e('Post', 'wp-mailinglist'); ?></th>
						<th><?php esc_html_e('Date', 'wp-mailinglist'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th><?php esc_html_e('ID', 'wp-mailinglist'); ?></th>
						<th><?php esc_html_e('Post', 'wp-mailinglist'); ?></th>
						<th><?php esc_html_e('Date', 'wp-mailinglist'); ?></th>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($posts as $p) : ?>
						<?php
						
						global $post;
						if ($post = get_post($p -> post_id)) {
							setup_postdata($post);
							
							?>
							<tr id="post_row_<?php echo esc_html( $p -> id); ?>">
								<td>
									<?php the_ID(); ?>
								</td>
								<td>
									<?php the_title(); ?>
									<div class="row-actions">
										<span class="delete"><a href="" onclick="if (confirm('<?php esc_html_e('Are you sure you want to delete this logged post? You are not actually deleting the post itself, just the fact that it was sent already.', 'wp-mailinglist'); ?>')) { delete_lps_post('<?php echo esc_html( $p -> id); ?>'); } return false;"><?php esc_html_e('Delete', 'wp-mailinglist'); ?></a></span>
									</div>
								</td>
								<td>
									<abbr title="<?php echo esc_html( $p -> created); ?>"><?php echo esc_html( $Html -> gen_date(false, strtotime($p -> created))); ?></abbr>
								</td>
							</tr>
						<?php } ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p class="newsletters_error"><?php esc_html_e('No posts have been logged  yet.', 'wp-mailinglist'); ?></p>
		<?php endif; ?>
		
		<p class="submit">
			<input type="button" class="button button-secondary button-large" onclick="jQuery.colorbox.close();" value="<?php esc_html_e('Close This', 'wp-mailinglist'); ?>" />
		</p>
	</div>
</div>

<script type="text/javascript">
function delete_lps_post(id) {
	jQuery.post(newsletters_ajaxurl + 'action=newsletters_delete_lps_post&security=<?php echo esc_html( wp_create_nonce('delete_lps_post')); ?>', {id:id}, function(response) {
		jQuery('table#posts_table tr#post_row_' + id).fadeOut();
	});
}
</script>