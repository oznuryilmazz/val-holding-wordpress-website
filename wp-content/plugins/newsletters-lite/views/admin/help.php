<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h1><?php esc_html_e('Support &amp; Help', 'wp-mailinglist'); ?></h1>
	
	<?php if (apply_filters('newsletters_whitelabel', true)) : ?>
		<h2><?php esc_html_e('Useful Links', 'wp-mailinglist'); ?></h2>
		<ul>
			<li><a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank"><?php esc_html_e('Newsletter plugin', 'wp-mailinglist'); ?></a></li>
			<li><a href="https://tribulant.com/plugins/extensions/1/wordpress-newsletter-plugin" target="_blank"><?php esc_html_e('Extension plugins', 'wp-mailinglist'); ?></a></li>
			<li><a href="https://tribulant.com/blog/" title="<?php esc_html_e('Tribulant News Blog', 'wp-mailinglist'); ?>" target="_blank"><?php esc_html_e('News Blog', 'wp-mailinglist'); ?></a></li>
			<li><a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/wordpress-mailing-list-plugin/" title="Tribulant Documentation" target="_blank"><?php esc_html_e('Documentation', 'wp-mailinglist'); ?></a></li>
			<li><a href="https://tribulant.com/support/" title="Tribulant Support" target="_blank"><?php esc_html_e('Support Ticket System', 'wp-mailinglist'); ?></a></li>
			<li><a href="https://tribulant.com/forums/" target="_blank"><?php esc_html_e('Support Forums', 'wp-mailinglist'); ?></a></li>
		</ul>
	<?php endif; ?>
	
	<?php do_action('newsletters_support_below'); ?>
</div>