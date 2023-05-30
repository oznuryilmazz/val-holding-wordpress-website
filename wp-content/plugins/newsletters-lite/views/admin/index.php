<?php // phpcs:ignoreFile ?>
<?php

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

$screen = get_current_screen();
$page = $screen -> id;

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> <?php echo esc_html( $this -> sections -> welcome); ?> newsletters">
	<h1><i class="fa fa-envelope fa-lg fa-fw"></i> <?php echo sprintf(__('%s %s', 'wp-mailinglist'), $this -> name, $this -> get_option('version')); ?></h1>    
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<?php do_action('submitpage_box'); ?>
				<?php do_meta_boxes($page, 'side', $post); ?>
			</div>
			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes($page, 'normal', $post); ?>
                <?php do_meta_boxes($page, 'advanced', $post); ?>
			</div>
		</div>
	</div>
</div>