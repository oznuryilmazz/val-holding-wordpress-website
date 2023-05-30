<?php // phpcs:ignoreFile ?>
<?php
	
global $post;
$newsletters_history_id = get_post_meta($post -> ID, '_newsletters_history_id', true);	
	
?>

<input type="hidden" name="newsletters_history_id" value="<?php echo esc_attr(wp_unslash($newsletters_history_id)); ?>" id="newsletters_history_id" />