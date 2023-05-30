<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters">
	<h1><?php esc_html_e('Manage Orders', 'wp-mailinglist'); ?></h1>
	<form id="posts-filter" action="?page=<?php echo esc_html( $this -> sections -> orders); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> orders . '_search'); ?>
		<ul class="subsubsub">
            <li><?php echo (empty($_GET['showall'])) ? (!empty($paginate) ? $paginate -> allcount : '') : count($orders);  ?> <?php _e('subscription orders', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $this -> url . '&amp;showall=1')); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), '?page=' . $this -> pre . 'orders')); ?></li>
			<?php endif; ?>
		</ul>
		<p class="search-box">
            <input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? esc_attr($_POST['searchterm']) : (isset($_GET[$this -> pre . 'searchterm']) ? esc_attr($_GET[$this -> pre . 'searchterm']) : '' ) ; ?>" />
			<button value="1" type="submit" class="button">
				<?php esc_html_e('Search Orders', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<?php $this -> render('orders' . DS . 'loop', array('orders' => $orders, 'paginate' => $paginate), true, 'admin'); ?>
</div>