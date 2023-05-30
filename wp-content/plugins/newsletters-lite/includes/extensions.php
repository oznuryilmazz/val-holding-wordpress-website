<?php

// All extensions are listed in this file and can be seen under Newsletters > Extensions in the plugin.

global $extensions;

$extensions = array(
	'embedimages'		=>	array(
		'name' 				=> 	__('Embedded Images', 'wp-mailinglist'),
		'description'		=>	__("Attach/embed images into emails instead of loading them remotely to prevent image disabling and immediate loading of newsletters.", 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/6/embedded-images",
		'slug'				=>	'embedimages',
		'plugin_name'		=>	'newsletters-embedimages',
		'plugin_file'		=>	'embedimages.php',
	),
	'formidable'		=>	array(
		'name' 				=> 	__('Formidable Subscribers', 'wp-mailinglist'),
		'description'		=>	__("Subscribe users from Formidable forms entries into the Newsletter plugin.", 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/16/formidable-subscribers",
		'slug'				=>	'formidable',
		'plugin_name'		=>	'newsletters-formidable',
		'plugin_file'		=>	'formidable.php',
	),
	'gforms'			=>	array(
		'name' 				=> 	__('Gravity Forms', 'wp-mailinglist'),
		'description'		=>	__("Capture subscribers through Gravity Forms into the Newsletter plugin.", 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/17/gravity-forms",
		'slug'				=>	'gforms',
		'plugin_name'		=>	'newsletters-gforms',
		'plugin_file'		=>	'gforms.php',
	),
	'mscontrol'			=>	array(
		'name'				=>	__('Total MS Control', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/26/total-ms-control",
		'description'		=>	__("Total control over your Newsletter plugin installation for WordPress multi-site.", 'wp-mailinglist'),
		'slug'				=>	'mscontrol',
		'plugin_name'		=>	'newsletters-mscontrol',
		'plugin_file'		=>	'mscontrol.php',
	),
	'cf7'				=>	array(
		'name'				=>	__('Contact Form 7 Subscribers', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/28/contact-form7-subscribers",
		'description'		=>	__('Capture newsletter subscribers into the Newsletter plugin from your Contact Form 7 plugin forms.', 'wp-mailinglist'),
		'slug'				=>	'cf7',
		'plugin_name'		=>	'newsletters-cf7',
		'plugin_file'		=>	'cf7.php',
	),
	'dap'				=>	array(
		'name'				=>	__('Digital Access Pass', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/43/digital-access-pass",
		'description'		=>	sprintf(__("Capture email/newsletters subscribes from %sDigital Access Pass%s.", 'wp-mailinglist'), '<a href="https://digitalaccesspass.com" target="_blank">', '</a>'),
		'slug'				=>	'dap',
		'plugin_name'		=>	'newsletters-dap',
		'plugin_file'		=>	'dap.php',
	),
	'newsletters_ga'	=>	array(
		'name'				=>	__('Google Analytics', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/",
		'description'		=>	sprintf(__("Google Analytics link tracking for the %s.", 'wp-mailinglist'), '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">' . __('Newsletter plugin', 'wp-mailinglist') . '</a>'),
		'slug'				=>	'newsletters_ga',
		'plugin_name'		=>	'newsletters-ga',
		'plugin_file'		=>	'ga.php',
	),
	'wpemember'			=>	array(
		'name'				=>	__('WP eMember Subscribers', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/31/wp-emember-subscribers",
		'description'		=>	__('Capture subscribers from WP eMember into the Newsletter plugin.', 'wp-mailinglist'),
		'slug'				=>	'wpemember',
		'plugin_name'		=>	'newsletters-wpemember',
		'plugin_file'		=>	'wpemember.php',
	),
	's2member'			=>	array(
		'name'				=>	__('s2Member Subscribers', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/32/s2member-subscribers",
		'description'		=>	__('Capture subscribers from s2Member into the Newsletter plugin.', 'wp-mailinglist'),
		'slug'				=>	's2member',
		'plugin_name'		=>	'newsletters-s2member',
		'plugin_file'		=>	's2member.php',
	),
	'sendtofriend' 		=> array(
		'name'				=>	__('Send to Friend', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/",
		'description'		=>	sprintf(__("Let subscribers suggest newsletters to friends for the %s.", 'wp-mailinglist'), '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">' . __('Newsletter plugin', 'wp-mailinglist') . '</a>'),
		'slug'				=>	'sendtofriend',
		'plugin_name'		=>	'newsletters-sendtofriend',
		'plugin_file'		=>	'sendtofriend.php',
	),
	'control'			=>	array(
		'name'				=>	__('Total Control', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/36/total-control",
		'description'		=>	__('Total control over your Newsletter plugin for WordPress.', 'wp-mailinglist'),
		'slug'				=>	'control',
		'plugin_name'		=>	'newsletters-control',
		'plugin_file'		=>	'control.php',
	),
	'newsletters_woocommerce'	=>	array(
		'name'				=>	__('WooCommerce Subscribers', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/42/woocommerce-subscribers",
		'description'		=>	sprintf(__("Capture email subscribers from the WooCommerce plugin to the %sNewsletter plugin%s.", 'wp-mailinglist'), '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">', '</a>'),
		'slug'				=>	'woocommerce',
		'plugin_name'		=>	'newsletters-woocommerce',
		'plugin_file'		=>	'woocommerce.php',
	),
	'profilebuilder' 			=> array(	//13
		'name'				=>	__('Profile Builder', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view//66/profile-builder-subscribers",
		'description'		=>	__('Capture newsletter subscribers from Profile Builder registration', 'wp-mailinglist'),
		'slug'				=>	'profilebuilder',
		'plugin_name'		=>	'newsletters-profilebuilder',
		'plugin_file'		=>	'profilebuilder.php',
	),
	'eventsmanager'				=>	array(	//14
		'name'				=>	__('Events Manager', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/68/events-manager-subscribers",
		'description'		=>	__('Capture newsletter subscribers from Events Manager registration', 'wp-mailinglist'),
		'slug'				=>	'eventsmanager',
		'plugin_name'		=>	'newsletters-eventsmanager',
		'plugin_file'		=>	'eventsmanager.php',
	),
	'dates'						=>	array(	//15
		'name'				=>	__('Special Dates Autoresponder', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/63/special-dates-autoresponder",
		'description'		=>	sprintf(__("Send autoresponder emails to subscribers on special/specific dates based on custom field values for %s.", 'wp-mailinglist'), '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">' . __('Newsletter plugin', 'wp-mailinglist') . '</a>'),
		'slug'				=>	'dates',
		'plugin_name'		=>	'newsletters-dates',
		'plugin_file'		=>	'dates.php',
	),
	'newsletters_bloom'			=>	array(	//16
		'name'				=>	__('Bloom Subscribers', 'wp-mailinglist'),
		'link'				=>	"https://tribulant.com/extensions/view/69/bloom-subscribers",
		'description'		=>	sprintf(__("Capture email subscribers from the Bloom plugin to the %sNewsletter plugin%s.", 'wp-mailinglist'), '<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin" target="_blank">', '</a>'),
		'slug'				=>	'newsletters_bloom',
		'plugin_name'		=>	'newsletters-bloom',
		'plugin_file'		=>	'bloom.php',
	)
);

$titles = array();
foreach ($extensions as $ext) {
	$titles[] = $ext['name'];
}

array_multisort($titles, SORT_ASC, $extensions);

?>
