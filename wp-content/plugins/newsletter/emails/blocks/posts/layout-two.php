<?php
$size = array('width' => 600, 'height' => 400, "crop" => true);
$total_width = 600 - $options['block_padding_left'] - $options['block_padding_right'];
$column_width = $total_width / 2 - 20;

$title_style = TNP_Composer::get_style($options, 'title', $composer, 'title', ['scale' => .8]);
$text_style = TNP_Composer::get_style($options, '', $composer, 'text');

$items = [];
?>
<style>
    .title {
        <?php $title_style->echo_css()?>
        line-height: 1.3em;
        padding: 15px 0 0 0;
    }

    .excerpt-td {
        padding: 5px 0 0 0;
    }
    .excerpt {
        <?php $text_style->echo_css()?>
        line-height: 1.4em;
        text-decoration: none;
    }

    .meta {
        <?php $text_style->echo_css(0.9)?>
        padding: 10px 0 0 0;
        font-style: italic;
        line-height: normal !important;
    }
    .button {
        padding: 15px 0;
    }
    .column-left {
        padding-right: 10px; 
        padding-bottom: 20px;
    }
    .column-right {
        padding-left: 10px; 
        padding-bottom: 20px;
    }

</style>


<?php foreach ($posts AS $p) { ?>
    <?php
    $url = tnp_post_permalink($p);
    $media = null;
    if ($show_image) {
        $media = tnp_composer_block_posts_get_media($p, $size, $image_placeholder_url);
        if ($media) {
            $media->link = $url;
            $media->set_width($column_width);
        }
    }

    $meta = [];

    if ($show_date) {
        $meta[] = tnp_post_date($p);
    }

    if ($show_author) {
        $author_object = get_user_by('id', $p->post_author);
        if ($author_object) {
            $meta[] = $author_object->display_name;
        }
    }

    $button_options['button_url'] = $url;
    ob_start();
    ?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <?php if ($media) { ?>
            <tr>
                <td align="center" valign="middle">
                    <?php echo TNP_Composer::image($media, ['class' => 'fluid']) ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td align="center" inline-class="title" class="title tnpc-row-edit tnpc-inline-editable" data-type="title" data-id="<?php echo $p->ID ?>">
                <?php
                echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'title', $p->ID) ?
                        TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'title', $p->ID) :
                        tnp_post_title($p)
                ?>
            </td>
        </tr>
        <?php if ($meta) { ?>
            <tr>
                <td align="center" inline-class="meta" class="meta">
                    <?php echo esc_html(implode(' - ', $meta)) ?>
                </td>
            </tr>
        <?php } ?>


        <?php if ($excerpt_length) { ?>
            <tr>
                <td align="center" inline-class="excerpt-td">
                    <a href="<?php echo $url?>" inline-class="excerpt" class="title tnpc-row-edit tnpc-inline-editable" data-type="text" data-id="<?php echo $p->ID ?>" dir="<?php echo $dir ?>">
                    <?php
                    echo TNP_Composer::is_post_field_edited_inline($options['inline_edits'], 'text', $p->ID) ?
                            TNP_Composer::get_edited_inline_post_field($options['inline_edits'], 'text', $p->ID) :
                            tnp_post_excerpt($p, $excerpt_length, $excerpt_length_in_chars)
                    ?>
                    </a>
                </td>
            </tr>
        <?php } ?>

        <?php if ($show_read_more_button) { ?>
            <tr>
                <td align="center" inline-class="button">
                    <?php echo TNP_Composer::button($button_options) ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php
    $items[] = ob_get_clean();
}
?>


<?php echo TNP_Composer::grid($items, ['width' => $total_width, 'responsive' => true, 'padding' => 5]) ?>



