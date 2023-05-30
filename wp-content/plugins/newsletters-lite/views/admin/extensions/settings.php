<?php
// phpcs:ignoreFile

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); 

$screen = get_current_screen();
$page = $screen -> id;

?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h1><?php esc_html_e('Extensions Settings', 'wp-mailinglist'); ?></h1>
    
    <?php $this -> render('extensions' . DS . 'navigation', false, true, 'admin'); ?>
    
	<form action="?page=<?php echo esc_html( $this -> sections -> extensions_settings); ?>" method="post" id="settings-form" enctype="multipart/form-data">
		<?php wp_nonce_field($this -> sections -> extensions_settings); ?>
	
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
	</form>
</div>