<?php // phpcs:ignoreFile ?>
<?php if ($this -> get_option('tracking') == "Y" && isset($eunique)) : ?>
			<?php echo esc_url_raw($this -> gen_tracking_link($eunique)); ?>
		<?php endif; ?>
	</body>
</html>