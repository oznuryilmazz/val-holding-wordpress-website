<!-- Extensions Navigation -->
<?php // phpcs:ignoreFile ?>

<div class="wp-filter">
	<ul class="filter-links">
		<li><a class="<?php echo ($_GET['page'] == $this -> sections -> extensions) ? 'current' : ''; ?>" href="?page=<?php echo esc_html( $this -> sections -> extensions); ?>"><?php esc_html_e('Manage', 'wp-mailinglist'); ?></a></li>
		<?php if (current_user_can('newsletters_extensions_settings')) : ?>
			<li><a class="<?php echo ($_GET['page'] == $this -> sections -> extensions_settings) ? 'current' : ''; ?>" href="?page=<?php echo esc_html( $this -> sections -> extensions_settings); ?>"><?php esc_html_e('Settings', 'wp-mailinglist'); ?></a></li>
		<?php endif; ?>
	</ul>
	
	<?php if (!empty($section)) : ?>
		<div class="search-form" id="tableofcontentsdiv">
			<div class="inside">
				<input type="text" class="<?php echo $section; ?>-search" placeholder="<?php esc_html_e('Search', 'wp-mailinglist'); ?>" />
			</div>
		</div>
	<?php endif; ?>
</div>