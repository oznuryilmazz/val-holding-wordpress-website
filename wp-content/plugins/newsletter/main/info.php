<?php
/* @var $this Newsletter */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$current_language = $this->get_current_language();

$is_all_languages = $this->is_all_languages();

//if (!$is_all_languages) {
//    $controls->warnings[] = 'You are configuring the language "<strong>' . $current_language . '</strong>". Switch to "all languages" to see every options.';
//}

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
} else {

    if ($controls->is_action('save')) {
        $controls->data['googleplus_url'] = '';
        $this->merge_options($controls->data);
        $this->save_options($controls->data, 'info');
        $controls->add_message_saved();
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Company Info', 'newsletter') ?></h2>

    </div>
    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-general"><?php _e('General', 'newsletter') ?></a></li>
                    <li><a href="#tabs-social"><?php _e('Social', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-general">
                    <h3><?php _e('Header Settings', 'newsletter') ?></h3>

                    <table class="form-table">
                        <tr>
                            <th>
                                <?php _e('Logo', 'newsletter') ?><br>
                            </th>
                            <td style="cursor: pointer">
                                <?php $controls->media('header_logo', 'medium'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Title', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('header_title', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Motto', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('header_sub', 40); ?>
                            </td>
                        </tr>
                    </table>

                    <h3><?php _e('Footer Settings', 'newsletter') ?></h3>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Company name', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_title', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Address', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_contact', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Copyright or legal text', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('footer_legal', 40); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tabs-social">

                    <table class="form-table">
                        <tr>
                            <th>Facebook URL</th>
                            <td>
                                <?php $controls->text('facebook_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Twitter URL</th>
                            <td>
                                <?php $controls->text('twitter_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Instagram URL</th>
                            <td>
                                <?php $controls->text('instagram_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Pinterest URL</th>
                            <td>
                                <?php $controls->text('pinterest_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Linkedin URL</th>
                            <td>
                                <?php $controls->text('linkedin_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Tumblr URL</th>
                            <td>
                                <?php $controls->text('tumblr_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>YouTube URL</th>
                            <td>
                                <?php $controls->text('youtube_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Vimeo URL</th>
                            <td>
                                <?php $controls->text('vimeo_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Soundcloud URL</th>
                            <td>
                                <?php $controls->text('soundcloud_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Telegram URL</th>
                            <td>
                                <?php $controls->text('telegram_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>VK URL</th>
                            <td>
                                <?php $controls->text('vk_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Twitch</th>
                            <td>
                                <?php $controls->text('twitch_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Discord</th>
                            <td>
                                <?php $controls->text('discord_url', 40); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>TikTok</th>
                            <td>
                                <?php $controls->text('tiktok_url', 40); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="tnp-buttons">
                <?php $controls->button_save(); ?>
            </div>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
