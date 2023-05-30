<?php
defined('ABSPATH') || exit;
?>
<div id="tnp-footer">
    <div>
        <ul>
            <li><a href="https://www.thenewsletterplugin.com" target="_blank">The Newsletter Plugin</a></li>
            <li><a href="https://www.thenewsletterplugin.com/premium" target="_blank"><?php _e('Get Premium', 'newsletter') ?></a></li>
        </ul>
    </div>
    <div>
        <ul>
            <li><a href="https://www.thenewsletterplugin.com/account"><?php _e('Your Account', 'newsletter') ?></a></li>
            <li><a href="https://www.thenewsletterplugin.com/forums"><?php _e('Forum', 'newsletter') ?></a></li>
            <!--<li><a href="https://www.thenewsletterplugin.com/blog"><?php _e('Blog', 'newsletter') ?></a></li>-->
        </ul>
    </div>
    <div>
        <form target="_blank" action="https://www.thenewsletterplugin.com/?na=s" method="post" style="margin: 0">
            <input type="email" name="ne" placeholder="Your email" required size="20" value="<?php echo esc_attr($current_user_email) ?>">
            <input type="hidden" value="plugin-footer" name="nr">
            <input type="hidden" value="3" name="nl[]">
            <input type="hidden" value="1" name="nl[]">
            <input type="hidden" value="double" name="optin">
            <input type="submit" value="<?php _e('Get news and promotions', 'newsletter') ?>">
            <span style="color: #bbb; margin-bottom: 0px; display: block; line-height: normal">
                Proceeding you agree to the 
                <a href="https://www.thenewsletterplugin.com/privacy" target="_blank" style="color: #2ECC71">privacy policy</a>
            </span>

        </form>
    </div>
</div>