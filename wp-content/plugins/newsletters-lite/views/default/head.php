<?php // phpcs:ignoreFile ?>
<?php $embed = $this -> get_option('embed'); ?>

<script type="text/javascript">
var wpmlAjax = '<?php echo esc_url_raw(rtrim($this -> url(), '/')); ?>/<?php echo esc_html($this -> plugin_name); ?>-ajax.php';
var wpmlUrl = '<?php echo esc_url_raw($this -> url()); ?>';
var wpmlScroll = "<?php echo ($embed['scroll'] == "Y") ? 'Y' : 'N'; ?>";

<?php if ($this -> language_do()) : ?>
	var newsletters_ajaxurl = '<?php echo esc_url_raw(admin_url('admin-ajax.php?lang=' . $this -> language_current() . '&')); ?>';
<?php else : ?>
	var newsletters_ajaxurl = '<?php echo esc_url_raw(admin_url('admin-ajax.php?')); ?>';
<?php endif; ?>

$ = jQuery.noConflict();

jQuery(document).ready(function() {
	if (jQuery.isFunction(jQuery.fn.select2)) {
		jQuery('.newsletters select').select2();
	}
	 
	if (jQuery.isFunction(jQuery.fn.button)) {
		jQuery('.<?php echo esc_html($this -> pre); ?>button, .newsletters_button').button();
	}
});
</script>

<?php if (get_option('wpmlcustomcss') == "Y") : ?>
	<style type="text/css">
	<?php echo wp_kses_post( wp_unslash(get_option('wpmlcustomcsscode'))) ?>
	</style>
<?php endif; ?>