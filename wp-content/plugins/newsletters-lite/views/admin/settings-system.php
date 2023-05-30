<?php // phpcs:ignoreFile ?>
<?php

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

$screen = get_current_screen();
$page = $screen -> id;

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h2><?php esc_html_e('System Configuration', 'wp-mailinglist'); ?></h2>
	<form action="?page=<?php echo esc_html( $this -> sections -> settings_system); ?>" method="post">
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
		<?php $this -> render('settings-navigation', array('tableofcontents' => "tableofcontents-system"), true, 'admin'); ?>
		<?php wp_nonce_field($this -> sections -> settings_system); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_action('submitpage_box'); ?>
					<?php do_meta_boxes($page, 'side', false); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes($page, 'high', false); ?>
					<?php do_meta_boxes($page, 'normal', false); ?>
                    <?php do_meta_boxes($page, 'advanced', false); ?>
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