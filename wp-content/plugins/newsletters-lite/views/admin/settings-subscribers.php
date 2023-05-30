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

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h1><?php esc_html_e('Subscribers Configuration', 'wp-mailinglist'); ?></h1>
	<form action="?page=<?php echo esc_html( $this -> sections -> settings_subscribers); ?>" method="post">
		<?php $this -> render('settings-navigation', array('tableofcontents' => "tableofcontents-subscribers"), true, 'admin'); ?>
		<?php wp_nonce_field($this -> sections -> settings_subscribers); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes($page, 'side', $post); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes($page, 'high', $post); ?>
					<?php do_meta_boxes($page, 'normal', $post); ?>
                    <?php do_meta_boxes($page, 'advanced', $post); ?>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function(){    
    var divOffset = jQuery("#tableofcontentsdiv").offset().top;
	
	jQuery(window).bind("scroll", function() {
	    var offset = jQuery(this).scrollTop();
	
	    if (offset >= divOffset) {
	        jQuery('#tableofcontentsdiv').addClass('fixed');
	    } else if (offset < divOffset) {
	    	jQuery('#tableofcontentsdiv').removeClass('fixed');
	    }
	});
});
</script>