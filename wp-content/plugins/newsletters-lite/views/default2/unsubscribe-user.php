<?php // phpcs:ignoreFile ?>
<div class="newsletters <?php echo esc_html($this -> pre); ?>unsubscribe <?php echo esc_html($this -> pre); ?>">
	<?php global $wpdb, $Mailinglist; ?>	
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
	
	<?php if (!empty($success) && $success == true) : ?>
		<h2><?php esc_html_e('Unsubscribe Successful', 'wp-mailinglist'); ?></h2>
		<p>
			<?php esc_html_e('You have successfully unsubscribed.', 'wp-mailinglist'); ?><br/>
			<?php esc_html_e('You will no longer receive correspondence.', 'wp-mailinglist'); ?>
		</p>
		
		<?php if (empty($deleted) && $deleted == false) : ?>
			<ul>
				<li><?php esc_html_e('Go back to', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw(home_url()); ?>" title="<?php echo esc_attr(wp_unslash(get_bloginfo('name'))); ?>"><?php echo get_bloginfo('name'); ?></a></li>
			</ul>
		<?php endif; ?>
	<?php elseif (!empty($data)) : ?>
		<h2><?php esc_html_e('Unsubscribe Confirmation', 'wp-mailinglist'); ?></h2>
		<form action="<?php echo wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))); ?>" method="post">
			<?php foreach ($data as $gkey => $gval) : ?>
				<input type="hidden" name="<?php echo esc_html( $gkey); ?>" value="<?php echo esc_attr($gval); ?>" />
			<?php endforeach; ?>
			
			<p><?php esc_html_e('Please confirm that you want to unsubscribe.', 'wp-mailinglist'); ?></p>
			
			<table>
				<tbody>
					<tr>
						<td><strong><?php esc_html_e('Email Address:', 'wp-mailinglist'); ?></strong></td>
						<td><?php echo esc_html( $user -> user_email); ?></td>
					</tr>
				</tbody>
			</table>
			
			<?php if ($this -> get_option('unsubscribecomments') == "Y") : ?>
				<h3><?php esc_html_e('Comments', 'wp-mailinglist'); ?> <?php esc_html_e('(optional)', 'wp-mailinglist'); ?></h3>
				<p>
					<textarea name="<?php echo esc_html($this -> pre); ?>comments" style="width:97%;" rows="5" class="widefat"><?php echo wp_kses_post( wp_unslash(htmlentities(strip_tags($data[$this -> pre . 'comments']), false, get_bloginfo('charset')))) ?></textarea>
				</p>
			<?php endif; ?>
			
			<p class="submit">
				<button type="submit" name="confirm" value="1" class="<?php echo esc_html($this -> pre); ?>button">
					<?php esc_html_e('Confirm Unsubscribe', 'wp-mailinglist'); ?>
				</button>
			</p>
		</form>
	<?php else : ?>
		<?php foreach ($errors as $err) : ?>
			&raquo; <?php echo wp_kses_post($err); ?><br/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>