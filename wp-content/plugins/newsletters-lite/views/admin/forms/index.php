<?php // phpcs:ignoreFile ?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h1><?php esc_html_e('Manage Forms', 'wp-mailinglist'); ?> <a class="add-new-h2" onclick="jQuery.colorbox({title:'<?php esc_html_e('Create a New Form', 'wp-mailinglist'); ?>', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_forms_createform')) ?>'}); return false;" href="?page=<?php echo esc_html( $this -> sections -> forms); ?>&amp;method=save"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a></h1>
	<form id="posts-filter" action="?page=<?php echo esc_html( $this -> sections -> forms); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> forms . '_search'); ?>
		<ul class="subsubsub">
            <li><?php echo (empty($_GET['showall'])) ? (!empty($paginate) ? $paginate -> allcount : '') : count($forms);  ?> <?php _e('forms', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $this -> url . '&amp;showall=1')); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), '?page=' . $this -> sections -> forms)); ?></li>
			<?php endif; ?>
		</ul>
		<p class="search-box">
            <input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? esc_attr($_POST['searchterm']) : (isset($_GET[$this -> pre . 'searchterm']) ? esc_attr($_GET[$this -> pre . 'searchterm']) : '' ) ; ?>" />
			<button value="1" type="submit" class="button">
				<?php esc_html_e('Search Forms', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<?php $this -> render('forms' . DS . 'loop', array('forms' => $forms, 'paginate' => $paginate), true, 'admin'); ?>
</div>