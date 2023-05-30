<?php
$size = ['width' => $composer['width'], 'height' => 0];
$content_width = $composer['width'] - $options['block_padding_left'] - $options['block_padding_right'];
$title_style = TNP_Composer::get_title_style($options, 'title', $composer);
$text_style = TNP_Composer::get_text_style($options, '', $composer);
?>

<style>
    .title {
        <?php $title_style->echo_css() ?>
        line-height: normal;
        margin: 0;
        padding-bottom: 20px;
    }
    
    .content {
        <?php $text_style->echo_css() ?>
    }

    .p {
        <?php $text_style->echo_css() ?>
        line-height: 1.5em!important;
    }
    
    .li {
        <?php $text_style->echo_css() ?>
        line-height: normal!important;
    }

    .meta {
        <?php $text_style->echo_css(0.9) ?>
        line-height: normal!important;
        padding-bottom: 10px;
        text-align: center;
        font-style: italic;
    }

    .button {
        padding: 15px 0;
    }

</style>

<?php foreach ($posts as $post) { ?>

    <?php
    $url = tnp_post_permalink($post);

    $media = null;
    if ($show_image) {
        $media = tnp_composer_block_posts_get_media($post, $size);
        if ($media) {
            $media->set_width($content_width);
            $media->link = $url;
        }
    }

    $meta = [];

    if ($show_date) {
        $meta[] = tnp_post_date($post);
    }

    if ($show_author) {
        $author_object = get_user_by('id', $post->post_author);
        if ($author_object) {
            $meta[] = $author_object->display_name;
        }
    }

    $button_options['button_url'] = $url;
    ?>


    <table border="0" cellpadding="0" align="center" cellspacing="0" width="100%" class="responsive">
        <tr>
            <td inline-class="title">
                <?php echo $post->post_title ?>
            </td>
        </tr>

        <?php if ($meta) { ?>
            <tr>
                <td inline-class="meta">
                    <?php echo esc_html(implode(' - ', $meta)) ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($media) { ?>
            <tr>
                <td align="center">
                    <?php echo TNP_Composer::image($media) ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td align="<?php echo $align_left?>" dir="<?php echo $dir?>" inline-class="content">
                <?php echo TNP_Composer::post_content($post) ?>
            </td>
        </tr>

        <?php if ($show_read_more_button) { ?>
            <tr>
                <td align="center" inline-class="button">
                    <?php echo TNP_Composer::button($button_options) ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <br><br>

<?php } ?>
