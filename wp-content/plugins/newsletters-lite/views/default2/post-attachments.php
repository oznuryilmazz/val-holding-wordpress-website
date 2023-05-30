<?php // phpcs:ignoreFile ?>
<?php if (!empty($attachments)) : ?>
	<h3><?php esc_html_e('Attached Files', 'wp-mailinglist'); ?></h3>
	<p class="newsletters_attachments">
		<ul>
			<?php foreach ($attachments as $attachment) : ?>
				<li><?php echo esc_url_raw( $Html -> attachment_link($attachment, false, 999)); ?></li>
			<?php endforeach; ?>
		</ul>
	</p>
<?php endif; ?>