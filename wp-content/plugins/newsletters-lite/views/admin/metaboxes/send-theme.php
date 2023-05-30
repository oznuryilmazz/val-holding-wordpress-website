<?php // phpcs:ignoreFile ?>
<!-- Send Template -->

<div class="submitbox">
	<div class="misc-pub-section misc-pub-section-last">        
        <div class="scroll-list">	        
            <?php if (apply_filters($this -> pre . '_admin_nonetheme', true)) : ?><div><label><input <?php echo ((isset($_POST['theme_id']) && $_POST['theme_id'] == 0) || ((isset($_POST['theme_id']) && empty($_POST['theme_id']))  && $this -> default_theme_id('sending') == "")) ? 'checked="checked"' : ''; ?> type="radio" name="theme_id" value="0" id="theme0" /> <?php _e('NONE', 'wp-mailinglist'); ?></label></div><?php endif; ?>
            <?php if ($themes = $Theme -> select()) : ?>
                <?php $default_theme_id = $this -> default_theme_id('sending'); ?>
                <?php foreach ($themes as $theme_id => $theme_title) : ?>
                    <div><label><input <?php echo ((isset($_POST['theme_id']) && (!empty($_POST['theme_id']) && $_POST['theme_id'] == $theme_id)) || ((empty($_POST['theme_id']) && $_POST['theme_id'] != "0" && $theme_id == $default_theme_id))) ? 'checked="checked"' : ''; ?> type="radio" name="theme_id" value="<?php echo esc_attr($theme_id); ?>" id="theme<?php echo $theme_id; ?>" /> <?php echo __($theme_title); ?></label>
                        <?php if (apply_filters($this -> pre . '_admin_themepreview', true)) : ?><a href="" onclick="jQuery.colorbox({iframe:true, width:'80%', height:'80%', title:'<?php echo __($theme_title); ?>', href:'<?php echo home_url(); ?>/?wpmlmethod=themepreview&amp;id=<?php echo esc_html($theme_id); ?>'}); return false;" class=""><i class="fa fa-eye fa-fw"></i></a><?php endif; ?>
                        <?php if (apply_filters('newsletters_admin_createnewsletter_themeedit', true)) : ?><a href="" onclick="jQuery.colorbox({title:'<?php echo sprintf(__('Edit Template: %s', 'wp-mailinglist'), $theme_title); ?>', href:newsletters_ajaxurl + 'action=newsletters_themeedit&security=<?php echo wp_create_nonce('themeedit'); ?>&id=<?php echo esc_html($theme_id); ?>'}); return false;" class=""><i class="fa fa-pencil fa-fw"></i></a><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>