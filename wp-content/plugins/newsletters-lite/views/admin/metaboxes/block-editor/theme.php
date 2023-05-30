<?php // phpcs:ignoreFile ?>
<!-- Send Template -->

<?php
	
global $post;
$newsletters_theme_id = get_post_meta($post -> ID, '_newsletters_theme_id', true);	
	
?>

<div class="submitbox">
	<div class="misc-pub-section misc-pub-section-last">        
        <div class="scroll-list">	        
            <?php if (apply_filters($this -> pre . '_admin_nonetheme', true)) : ?><div><label><input <?php echo ($newsletters_theme_id == 0 || (empty($newsletters_theme_id) && $this -> default_theme_id('sending') == "")) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_theme_id" value="0" id="theme0" /> <?php esc_html_e('NONE', 'wp-mailinglist'); ?></label></div><?php endif; ?>
            <?php if ($themes = $Theme -> select()) : ?>
                <?php $default_theme_id = $this -> default_theme_id('sending'); ?>
                <?php foreach ($themes as $theme_id => $theme_title) : ?>
                    <div><label><input <?php echo ((!empty($newsletters_theme_id) && $newsletters_theme_id == $theme_id) || (empty($newsletters_theme_id) && $newsletters_theme_id != "0" && $theme_id == $default_theme_id)) ? 'checked="checked"' : ''; ?> type="radio" name="newsletters_theme_id" value="<?php echo esc_html( $theme_id); ?>" id="newsletters_theme<?php echo esc_html( $theme_id); ?>" /> <?php echo esc_html($theme_title); ?></label>
                    <?php if (apply_filters($this -> pre . '_admin_themepreview', true)) : ?><a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo esc_html($theme_title); ?>', href:'<?php echo esc_url_raw(home_url()); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html($theme_id); ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a><?php endif; ?>
                    <?php if (apply_filters('newsletters_admin_createnewsletter_themeedit', true)) : ?><a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', 'wp-mailinglist'), $theme_title); ?>', href:newsletters_ajaxurl + 'action=newsletters_themeedit&security=<?php echo esc_html( wp_create_nonce('themeedit')); ?>&id=<?php echo esc_html( $theme_id); ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>