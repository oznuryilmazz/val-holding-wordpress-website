<?php
// phpcs:ignoreFile

require_once $this -> plugin_base() . DS . 'vendors' . DS . 'wp_list_table' . DS . 'newsletter.php';
$Newsletter_List_Table = new Newsletter_List_Table();	
$Newsletter_List_Table -> prepare_items();
	
?>

<div class="wrap newsletters">
	<h1><?php esc_html_e('Sent & Draft Newsletters', 'wp-mailinglist'); ?> <a class="add-new-h2" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> send)) ?>"><i class="fa fa-plus-circle fa-fw"></i> <?php esc_html_e('Create Newsletter', 'wp-mailinglist'); ?></a></h1>
	
	<form id="newsletters-history-form" action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> history)) ?>" method="get">
		<input type="hidden" name="page" value="<?php echo esc_html( $this -> sections -> history); ?>" />
		<?php $Newsletter_List_Table -> search_box(__('Search Newsletters', 'wp-mailinglist'), 'search'); ?>
		<?php /*<?php wp_nonce_field($this -> sections -> history); ?>*/ ?>
		<?php $Newsletter_List_Table -> display(); ?>
	</form>
</div>