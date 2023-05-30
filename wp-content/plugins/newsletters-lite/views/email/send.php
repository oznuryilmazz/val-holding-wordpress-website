<?php // phpcs:ignoreFile ?>
<?php echo wp_kses_post($message); ?>

<?php if (!empty($print)) : ?>
<script type="text/javascript">
window.onload = function() {
	window.print();
}
</script>
<?php endif; ?>