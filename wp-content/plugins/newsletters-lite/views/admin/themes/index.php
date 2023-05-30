<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h1><?php esc_html_e('Manage Templates', 'wp-mailinglist'); ?>
	<a class="add-new-h2" href="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=save"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a>
	<?php if (apply_filters('newsletters_whitelabel', true)) : ?><a class="add-new-h2-green" href="https://tribulant.com/emailthemes/" target="_blank"><?php esc_html_e('Get More Templates', 'wp-mailinglist'); ?></a><?php endif; ?>
	</h1>
	
	<!-- Default Template Setting -->
	<?php $defaulttemplate = $this -> get_option('defaulttemplate'); ?>	
	<form method="post" action="?page=<?php echo esc_html( $this -> sections -> themes); ?>&amp;method=defaulttemplate">
    	<label><input <?php echo (!empty($defaulttemplate)) ? 'checked="checked"' : ''; ?> type="checkbox" name="defaulttemplate" value="1" id="defaulttemplate" /> <?php esc_html_e('Use a styled, default template for newsletters and system emails', $this -> plugin_name); ?></label>
        <input class="button" type="submit" value="<?php esc_html_e('Save', 'wp-mailinglist'); ?>" name="submit" />
        <?php echo ( $Html -> help(__('Turn this on to use a styled, default template for newsletters and system emails when none is selected.', 'wp-mailinglist'))); ?>
    </form>
    
	<form id="posts-filter" action="<?php echo esc_url_raw($this -> url); ?>" method="post">
		<ul class="subsubsub">
			<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($themes); ?> <?php esc_html_e('templates', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), '?page=' . $this -> sections -> themes . "&amp;showall=1")); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), "?page=" . $this -> sections -> themes)); ?></li>
			<?php endif; ?>
		</ul>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : (isset($_GET[$this -> pre . 'searchterm']) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : ''); ?>" />
			<button value="1" type="submit" class="button">
				<?php esc_html_e('Search Templates', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<?php $this -> render('themes' . DS . 'loop', array('themes' => $themes, 'paginate' => $paginate), true, 'admin'); ?>
</div>