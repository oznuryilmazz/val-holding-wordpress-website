<?php // phpcs:ignoreFile ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title><?php echo esc_html($title); ?></title>

    <?php

	wp_enqueue_script('jquery');
	wp_enqueue_script($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/js/' . $this -> plugin_name . '.js', array('jquery'), '1.0', false);
	wp_enqueue_style($this -> plugin_name, plugins_url() . '/' . $this -> plugin_name . '/views/' . $this -> get_option('theme_folder') . '/css/style.css', false, $this -> version, "all");

	?>

    <?php wp_head(); ?>
</head>
<body style="background:none;">
	<div id="<?php echo esc_html($widget_id); ?>" class="newsletters <?php echo esc_html($this -> pre); ?> widget_newsletters">
	<div id="<?php echo esc_html($widget_id); ?>-wrapper">
		<?php

		if (!empty($form)) {
			$action = $Html -> retainquery($this -> pre . 'method=offsite&form=' . $form -> id . (!empty($_GET['iframe']) ? '&iframe=1' : ''), home_url());
			$this -> render('subscribe', array('form' => $form, 'action' => $action, 'errors' => $Subscriber -> errors), true, 'default');	
		} else {
			$action = $Html -> retainquery($this -> pre . 'method=offsite' . (!empty($_GET['iframe']) ? '&iframe=1' : '') . '&list=' . esc_html($instance['list']), home_url());
			$this -> render('widget', array('action' => $action, 'errors' => $Subscriber -> errors, 'instance' => $instance, 'widget_id' => $widget_id, 'number' => $number), true, 'default');
		}

		?>
	</div>
	</div>

    <?php wp_footer(); ?>
</body>
</html>