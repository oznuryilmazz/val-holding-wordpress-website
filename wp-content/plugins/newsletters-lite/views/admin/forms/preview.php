<!-- Form Preview -->
<?php // phpcs:ignoreFile ?>

<div class="wrap newsletters">	
	<h2><?php esc_html_e('Form Preview', 'wp-mailinglist'); ?></h2>
	
	<?php $this -> render('forms' . DS . 'navigation', array('form' => $form), true, 'admin'); ?>
	
	<div class="postbox" style="padding:10px;">
		<iframe width="100%" frameborder="0" scrolling="no" class="autoHeight widefat" style="width:100%; margin:15px 0 0 0;" src="<?php echo esc_url_raw( admin_url('admin-ajax.php?action=newsletters_form_preview&security=' . wp_create_nonce('form_preview') . '&id=' . $form -> id)) ?>" id="newsletters_form_preview_<?php echo esc_html($form -> id); ?>"></iframe>
    </div>	
</div>