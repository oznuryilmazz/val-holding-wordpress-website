<?php // phpcs:ignoreFile ?>
<iframe frameborder="0" border="0" style="border:none;" class="autoHeight" src="<?php echo esc_url_raw(home_url()); ?>/?<?php echo esc_html($this -> pre); ?>method=offsite&iframe=1&list=<?php echo esc_html( $options['list']); ?>">
	<p><?php esc_html_e('Form loading, please wait...', 'wp-mailinglist'); ?></p>
</iframe>
<script type="text/javascript" src="<?php echo esc_url_raw($this -> url()); ?>/js/jquery.autoheight.js"></script>