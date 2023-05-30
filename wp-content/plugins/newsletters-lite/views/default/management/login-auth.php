<?php // phpcs:ignoreFile ?>
<div class="newsletters newsletters-management-loginauth">
	<p>
		<?php esc_html_e('Thank you for authenticating your email address.', 'wp-mailinglist'); ?><br/>
		<?php esc_html_e('If you do not get redirected in a second, click the link below.', 'wp-mailinglist'); ?>
	</p>
	
	<p><a class="newsletters_button" href="<?php echo esc_url_raw($this -> get_managementpost(true)); ?>"><?php esc_html_e('Manage Subscriptions', 'wp-mailinglist'); ?></a></p>
	
	<script type="text/javascript">jQuery(document).ready(function() { window.location = "<?php echo remove_query_arg(array('method', 'email'), $Html -> retainquery('subscriberauth=' . $subscriberauth)); ?>"; });</script>
</div>