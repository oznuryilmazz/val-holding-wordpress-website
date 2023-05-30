<?php // phpcs:ignoreFile ?>
<?php

global $ID, $post, $post_ID, $wp_meta_boxes;
$ID = $this -> get_option('imagespost');
$post_ID = $this -> get_option('imagespost');

$screen = get_current_screen();
$page = $screen -> id;	

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h1><?php esc_html_e('System Emails Configuration', 'wp-mailinglist'); ?></h1>
    <p>
    	<?php esc_html_e('System emails are messages sent as notifications to users/admins on events.', 'wp-mailinglist'); ?><br/>
    	<?php esc_html_e('You can configure each email template individually according to your needs.', 'wp-mailinglist'); ?><br/>
    	<?php esc_html_e('You may use any of the', 'wp-mailinglist'); ?> <a class="button button-secondary" title="<?php esc_html_e('Shortcodes/Variables', 'wp-mailinglist'); ?>" href="" onclick="jQuery.colorbox({title:'<?php esc_html_e('Shortcodes/Variables', 'wp-mailinglist'); ?>', maxHeight:'80%', maxWidth:'80%', href:'<?php echo esc_url_raw( admin_url('admin-ajax.php')) ?>?action=<?php echo esc_html($this -> pre); ?>setvariables&security=<?php echo esc_html(wp_create_nonce('setvariables')) ?>'}); return false;"> <?php esc_html_e('shortcodes/variables', 'wp-mailinglist'); ?></a> <?php esc_html_e('inside the subjects/messages of system emails.', 'wp-mailinglist'); ?><br/>
    	<?php echo sprintf(__('Each template is inserted where the <code>[newsletters_main_content]</code> tag is in the default template chosen under %s > Templates.', 'wp-mailinglist'), $this -> name); ?>
    </p>
	<form action="?page=<?php echo esc_html( $this -> sections -> settings_templates); ?>" method="post">
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
		<?php $this -> render('settings-navigation', array('tableofcontents' => "tableofcontents-templates"), true, 'admin'); ?>
		<?php wp_nonce_field($this -> sections -> settings_templates); ?>
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