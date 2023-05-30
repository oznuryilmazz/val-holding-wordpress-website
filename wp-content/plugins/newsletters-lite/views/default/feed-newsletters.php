<?php // phpcs:ignoreFile ?>
<rss version="2.0" xmlns:atom="https://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo get_bloginfo('name'); ?> <?php _e($this -> name, 'wp-mailinglist'); ?></title>
		<link><?php echo home_url(); ?></link>
		<description><?php echo get_bloginfo('description'); ?></description>
		<lastBuildDate><?php echo $Html -> gen_date("r"); ?></lastBuildDate>
		
		<?php if (!empty($emails)) : ?>
			<?php foreach ($emails as $email) : ?>
				<item>
					<title><?php echo esc_attr(wp_unslash($email -> subject)); ?></title>
					<link><?php echo esc_url_raw($Html -> retainquery('newsletters_method=newsletter&id=' . $email -> id . '&fromfeed=1', home_url())); ?></link>
					<guid><?php echo esc_url_raw($Html -> retainquery('newsletters_method=newsletter&id=' . $email -> id . '&fromfeed=1', home_url())); ?></guid>
					<pubDate><?php echo esc_html($Html -> gen_date("r", strtotime($email -> modified))); ?></pubDate>
					<description><![CDATA[ <?php echo esc_attr(strip_tags(apply_filters('the_content', $this -> strip_set_variables($email -> message)))); ?> ]]></description>
				</item>
			<?php endforeach; ?>
		<?php endif; ?>
	</channel>
</rss>