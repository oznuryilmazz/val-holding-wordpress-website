<?php // phpcs:ignoreFile ?>
<?php $this -> render('metaboxes' . DS . 'admin-mode-switcher', false, true, 'admin'); ?>

<p class="savebutton">
	<input id="savedraftbutton2" style="float:left;" type="submit" name="draft" value="<?php esc_html_e('Save Draft', 'wp-mailinglist'); ?>" class="button button-highlighted" />
	
	<?php $sendbutton = ($this -> get_option('sendingprogress') == "N") ? __('Queue Newsletter', 'wp-mailinglist') : __('Send Newsletter', 'wp-mailinglist'); ?>
	<input class="button button-primary button-large" type="submit" name="send" id="sendbutton2" disabled="disabled" value="<?php echo esc_html( $sendbutton); ?>" />
</p>