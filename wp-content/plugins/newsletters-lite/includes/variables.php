<?php
	
$defaultstyles = array(
	// Default styles go here
);

$defaultstyles = apply_filters('newsletters_default_styles', $defaultstyles);
	
$defaultscripts = array(
	// Default scripts go here
);

$defaultscripts = apply_filters('newsletters_default_scripts', $defaultscripts);

$validation_rules = array(
	'notempty'				=>	array(
		'title'					=>	__('Not Empty', 'wp-mailinglist'),
		'regex'					=>	"",
	),
	'alphanumeric'			=>	array(
		'title'					=>	__('Alpha-Numeric', 'wp-mailinglist'),
		'regex'					=>	"/^[a-zA-Z0-9]+$/",
	),
	'alphabetic'			=>	array(
		'title'					=>	__('Alphabetic', 'wp-mailinglist'),
		'regex'					=>	"/^[a-zA-Z]+$/",
	),
	'numeric'				=>	array(
		'title'					=>	__('Numeric', 'wp-mailinglist'),
		'regex'					=>	"/^[0-9]+$/",
	),
	'email'					=>	array(
		'title'					=>	__('Email', 'wp-mailinglist'),
		'regex'					=>	"/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/",
	),
	'ipaddress'				=>	array(
		'title'					=>	__('IP Address', 'wp-mailinglist'),
		'regex'					=>	"/^((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))*$/",
	),
	'urls'					=>	array(
		'title'					=>	__('URLs', 'wp-mailinglist'),
		'regex'					=>	"/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/",
	),
);

$validation_rules = apply_filters('newsletters_validation_rules', $validation_rules);
$validation_rules['custom'] = array('title' => __('CUSTOM REGEX', 'wp-mailinglist'));

$wordpress_reserved_terms = array(
	// Plugin Reserved
	'country',
	'format',
	
	// WordPress Reserved
	'attachment',
	'attachment_id',
	'author',
	'author_name',
	'calendar',
	'cat',
	'category',
	'category__and',
	'category__in',
	'category__not_in',
	'category_name',
	'comments_per_page',
	'comments_popup',
	'customize_messenger_channel',
	'customized',
	'cpage',
	'day',
	'debug',
	'error',
	'exact',
	'feed',
	'hour',
	'link_category',
	'm',
	'minute',
	'monthnum',
	'more',
	'name',
	'nav_menu',
	'nonce',
	'nopaging',
	'offset',
	'order',
	'orderby',
	'p',
	'page',
	'page_id',
	'paged',
	'pagename',
	'pb',
	'perm',
	'post',
	'post__in',
	'post__not_in',
	'post_format',
	'post_mime_type',
	'post_status',
	'post_tag',
	'post_type',
	'posts',
	'posts_per_archive_page',
	'posts_per_page',
	'preview',
	'robots',
	's',
	'search',
	'second',
	'sentence',
	'showposts',
	'static',
	'subpost',
	'subpost_id',
	'tag',
	'tag__and',
	'tag__in',
	'tag__not_in',
	'tag_id',
	'tag_slug__and',
	'tag_slug__in',
	'taxonomy',
	'tb',
	'term',
	'theme',
	'type',
	'w',
	'withcomments',
	'withoutcomments',
	'year',
);

$wordpress_reserved_terms = apply_filters('newsletters_wordpress_reserved_terms', $wordpress_reserved_terms);

$spinners = array(
	'refresh'			=>	"fa fa-refresh fa-spin fa-fw",
	'gear'				=>	"fa fa-cog fa-spin fa-fw",
	'circle-o-notch'	=>	"fa fa-circle-o-notch fa-spin fa-fw",
	'spinner'			=>	"fa fa-spinner fa-pulse fa-fw",
);

$spinners = apply_filters('newsletters_loading_spinners', $spinners);

?>