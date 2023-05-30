<?php // phpcs:ignoreFile ?>

<!-- Clicks -->
<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h1><?php esc_html_e('Manage Clicks', 'wp-mailinglist'); ?></h1>
	
	<div style="float:none;" class="subsubsub"><?php echo ( $Html -> link(__('&larr; Back to Links', 'wp-mailinglist'), admin_url("admin.php?page=" . $this -> sections -> links))); ?></div>
	
	<form id="posts-filter" action="<?php echo wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> clicks . '_search'); ?>
    	<?php if (!empty($clicks)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($clicks); ?> <?php esc_html_e('clicks', 'wp-mailinglist'); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $Html -> retainquery('showall=1'))); ?></li>
                <?php else : ?>
                    <li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), "?page=" . $this -> sections -> clicks)); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])); ?>" />
			<button value="1" name="submit" type="submit" class="button">
				<?php esc_html_e('Search Clicks', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<br class="clear" />
	<?php $this -> render('clicks' . DS . 'loop', array('clicks' => $clicks, 'paginate' => $paginate), true, 'admin'); ?>
</div>