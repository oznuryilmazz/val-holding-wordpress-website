<?php // phpcs:ignoreFile ?>
<div align="center">
	<h1 id="logo"><img alt="WPMailingList" src="<?php echo esc_url_raw($this -> url()); ?>/images/wpmailinglist.jpg" /></h1>
	
	<h2><?php esc_html_e('Activation Confirmation', 'wp-mailinglist'); ?></h2>
	<br class="clear" />
	
	<?php esc_html_e('Thank you for activating your subscription', 'wp-mailinglist'); ?><br/>
	<?php esc_html_e('Go back to', 'wp-mailinglist'); ?> <a href="<?php echo esc_url_raw(get_option('home')); ?>" title="<?php echo esc_html(get_option('blogname')); ?>"><?php echo esc_html(get_option('blogname')); ?></a>
</div>