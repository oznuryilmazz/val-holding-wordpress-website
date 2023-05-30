<?php
$size = ['width' => $composer['width'], 'height' => 0];
$total_width = $composer['width'] - $options['block_padding_left'] - $options['block_padding_right'];
$column_width = $total_width / 2 - 20;

$title_style = TNP_Composer::get_style($options, 'title', $composer, 'title');
$text_style = TNP_Composer::get_style($options, '', $composer, 'text');
?>
<style>
    .title {
        <?php $title_style->echo_css() ?>
        line-height: normal;
        padding: 0 0 10px 0;
    }
    
    .excerpt-td {
        padding: 0 0 15px 0;
    }

    .excerpt {
        <?php $text_style->echo_css() ?>
        line-height: 1.5em;   
        text-decoration: none;
    }

    .meta {
        <?php $text_style->echo_css(0.9) ?>
        padding: 0 0 5px 0;
        line-height: normal !important;
        font-style: italic;
    }
    .button {
        padding: 15px 0;
    }
</style>


<table border="0" cellpadding="0" cellspacing="0" width="100%" class="responsive">

    <?php foreach ($posts as $post) { ?>
        <?php
        $url = tnp_post_permalink($post);

        $media = null;
        if ($show_image) {
            $media = tnp_composer_block_posts_get_media($post, $size);
            if ($media) {
                $media->link = $url;
                $media->set_width($column_width);
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
        $button_options['button_align'] = 'left';
        ?>

        <tr>
            <td align="<?php echo $align_left ?>" inline-class="title" class="tnpc-row-edit tnpc-inline-editable" data-type="title" data-id="<?php echo $post->ID ?>" dir="<?php echo $dir ?>">
                <?php
                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'title', $post->ID) ?
                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'title', $post->ID) :
                        tnp_post_title($post)
                ?>
            </td>
        </tr>

        <tr>

            <td valign="top" style="padding: 20px 0 25px 0;">

                <?php if ($media) { ?>
                    <table width="<?php echo $column_width ?>" cellpadding="0" cellspacing="0" border="0" align="left" style="margin: 0;" class="responsive">
                        <tr>
                            <td class="pb-1">
                                <?php echo TNP_Composer::image($media, ['class' => 'fluid']) ?>
                            </td>
                        </tr>
                    </table>
                <?php } ?>

                <table width="<?php echo $media ? $column_width : '100%' ?>" cellpadding="0" cellspacing="0" border="0" style="margin: 0;" class="responsive" align="right">
                    <tr>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin: 0;">
                                <?php if ($meta) { ?>
                                    <tr>
                                        <td inline-class="meta" dir="<?php echo $dir ?>" align="<?php echo $align_left ?>">
                                            <?php echo esc_html(implode(' - ', $meta)) ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($excerpt_length) { ?>
                                    <tr>
                                        <td align="<?php echo $align_left ?>" dir="<?php echo $dir ?>" inline-class="excerpt-td">
                                            <a href="<?php $url ?>" data-id="<?php echo $post->ID ?>" inline-class="excerpt" class="tnpc-row-edit tnpc-inline-editable" data-type="text">
                                                <?php
                                                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'text', $post->ID) ?
                                                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'text', $post->ID) :
                                                        tnp_post_excerpt($post, $excerpt_length, $excerpt_length_in_chars)
                                                ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php if ($show_read_more_button) { ?>
                                    <tr>
                                        <td align="<?php echo $align_left ?>" inline-class="button">
                                            <?php echo TNP_Composer::button($button_options) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>

                        </td>
                    </tr>
                </table>

            </td>
        </tr>

    <?php } ?>

</table>
