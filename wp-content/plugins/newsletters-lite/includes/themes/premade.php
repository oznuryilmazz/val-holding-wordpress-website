<?php

global $wpdb, $Db, $Theme;

$themes = array(
	'blueretro'			=>	array('title' => __('Blue Retro', 'wp-mailinglist'), 'name' => "blueretro"),
	'getaway'			=>	array('title' => __('Getaway', 'wp-mailinglist'), 'name' => "getaway"),
	'nightlife'			=>	array('title' => __('Night Life', 'wp-mailinglist'), 'name' => "nightlife"),
	'paperphase'		=>	array('title' => __('Paper Phase', 'wp-mailinglist'), 'name' => "paperphase"),
	'redray'			=>	array('title' => __('Red Ray', 'wp-mailinglist'), 'name' => "redray"),
	'simplyelegant'		=>	array('title' => __('Simply Elegant', 'wp-mailinglist'), 'name' => "simplyelegant"),
	'snazzy'			=>	array('title' => __('Snazzy', 'wp-mailinglist'), 'name' => "snazzy"),
	'pronews'			=>	array('title' => __('Pro News', 'wp-mailinglist'), 'name' => "pronews"),
	'lagoon'			=>	array('title' => __('Lagoon', 'wp-mailinglist'), 'name' => "lagoon"),
	'themailer'			=>	array('title' => __('The Mailer', 'wp-mailinglist'), 'name' => "themailer"),
	'creator'			=>	array('title' => __('Creator', 'wp-mailinglist'), 'name' => "creator"),
);

$themespath = $this -> plugin_base() . DS . 'includes' . DS . 'themes' . DS;

foreach ($themes as $theme) {
	$themequery = "SELECT * FROM `" . $wpdb -> prefix . $Theme -> table . "` WHERE name = '" . esc_sql($theme['name']) . "' LIMIT 1";
	
	if (!$wpdb -> get_row($themequery)) {
		$themefile = $themespath . $theme['name'] . DS . 'index.html';
		
		if (file_exists($themefile)) {
			if ($fh = fopen($themefile, "r")) {
				$content = "";
				
				while (!feof($fh)) {
					$content .= fread($fh, 1024);
				}
				
				fclose($fh);
			}
			
			$newcontent = "";
			ob_start();
			// phpcs:ignore
			echo wp_unslash($content);
			$newcontent = ob_get_clean();
			
			$theme_data = array(
				'title'				=>	$theme['title'],
				'name'				=>	$theme['name'],
				'premade'			=>	"Y",
				'paste'				=>	$newcontent,
				'type'				=>	"paste",
				'def'				=>	"N",
			);
			
			$Db -> model = $Theme -> model;
			$Db -> save($theme_data);
		}
	}
}

?>