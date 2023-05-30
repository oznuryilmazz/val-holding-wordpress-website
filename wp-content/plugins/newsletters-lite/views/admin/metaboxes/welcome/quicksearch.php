<?php // phpcs:ignoreFile ?>
<div>
	<form action="<?php echo esc_url_raw( admin_url('admin.php')) ?>?page=<?php echo esc_html( $this -> sections -> subscribers); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> subscribers . '_search'); ?>
		<p>
			<label>
				<?php esc_html_e('Subscriber:', 'wp-mailinglist'); ?><br/>
				<input placeholder="<?php echo esc_attr(wp_unslash(__('Subscriber...', 'wp-mailinglist'))); ?>" type="text" name="searchterm" value="" id="newsletters_quicksearch_input" />
			</label>
		</p>
		<p class="submit">
			<button value="1" type="submit" name="search" id="newsletters_quicksearch_submit" class="button button-primary"> 
				<i class="fa fa-search fa-fw"></i><?php esc_html_e('Search Now', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
</div>