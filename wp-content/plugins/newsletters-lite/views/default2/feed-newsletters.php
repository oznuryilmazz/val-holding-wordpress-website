<?php // phpcs:ignoreFile ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="<?php echo esc_attr(wp_unslash(home_url($_SERVER['REQUEST_URI']))); ?>" rel="self" type="application/rss+xml" />
		<title><![CDATA[<?php echo get_bloginfo('name'); ?> <?php _e($this -> name, 'wp-mailinglist'); ?>]]></title>
		<link><![CDATA[<?php echo home_url(); ?>]]></link>
		<description><![CDATA[<?php echo get_bloginfo('description'); ?>]]></description>
		<lastBuildDate><![CDATA[<?php echo esc_html( $Html -> gen_date(DATE_RSS, false, false, false, false)); ?>]]></lastBuildDate>
		
		<?php if (!empty($emails)) : ?>
			<?php foreach ($emails as $email) : ?>
				<item>
					<title><![CDATA[<?php echo wp_unslash($email -> subject); ?>]]></title>
					<link><![CDATA[<?php echo ($Html -> retainquery('newsletters_method=newsletter&id=' . esc_html($email -> id) . '&fromfeed=1', home_url())); ?>]]></link>
					<guid><![CDATA[<?php echo ($Html -> retainquery('newsletters_method=newsletter&id=' . esc_html($email -> id) . '&fromfeed=1', home_url())); ?>]]></guid>
					<pubDate><![CDATA[<?php echo $Html -> gen_date(DATE_RSS, strtotime($email -> modified), false, false, false); ?>]]></pubDate>
					<description><![CDATA[ <?php echo (strip_tags(apply_filters('the_content', $this -> strip_set_variables($email -> message)))); ?> ]]></description>
				</item>
			<?php endforeach; ?>
		<?php endif; ?>
	</channel>
</rss>