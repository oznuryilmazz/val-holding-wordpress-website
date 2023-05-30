<!-- Manage Fields -->
<?php // phpcs:ignoreFile ?>

<?php
	
global $Field;
$Field -> check_default_fields();	
	
?>

<div class="wrap newsletters">
	<h1><?php esc_html_e('Manage Custom Fields', 'wp-mailinglist'); ?> <a class="add-new-h2" href="?page=<?php echo esc_html( $this -> sections -> fields); ?>&amp;method=save" title="<?php esc_html_e('Create a new custom field', 'wp-mailinglist'); ?>"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a></h1>
	<form id="posts-filter" action="?page=<?php echo esc_html( $this -> sections -> fields); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> fields . '_search'); ?>
		
		<ul class="subsubsub">
			<li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($fields); ?> <?php esc_html_e('custom fields', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $this -> url . '&amp;showall=1')); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), '?page=' . $this -> sections -> fields)); ?></li>
			<?php endif; ?>
		</ul>
		<p class="search-box">
            <input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? esc_attr($_POST['searchterm']) : (isset($_GET[$this -> pre . 'searchterm']) ? esc_attr($_GET[$this -> pre . 'searchterm']) : '' ) ; ?>" />
            <input class="button" name="search" type="submit" value="<?php _e('Search Fields', 'wp-mailinglist'); ?>" />
		</p>
	</form>
	<?php $this -> render('fields' . DS . 'loop', array('fields' => $fields, 'paginate' => $paginate), true, 'admin'); ?>
</div>