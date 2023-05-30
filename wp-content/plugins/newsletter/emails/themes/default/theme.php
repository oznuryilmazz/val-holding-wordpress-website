<?php
/*
 * Name: Default
 * Type: standard
 * Some variables are already defined:
 *
 * - $theme_options An array with all theme options
 * - $theme_url Is the absolute URL to the theme folder used to reference images
 * - $theme_subject Will be the email subject if set by this theme
 *
 */

global $newsletter, $post;

defined('ABSPATH') || exit;

include NEWSLETTER_INCLUDES_DIR . '/helper.php';

$color = $theme_options['theme_color'];
$background = $theme_options['theme_background'];
$logo = false;

if ($theme_options['main_header_logo']['id']) {
    $logo = tnp_media_resize($theme_options['main_header_logo']['id'], array(600, 200));
}

$title = $theme_options['main_header_title'];
if (empty($title)) $title = get_option('blogname');

if (isset($theme_options['theme_posts'])) {
    $filters = array();

    $filters['posts_per_page'] = (int) $theme_options['theme_max_posts'];

    if (!empty($theme_options['theme_categories'])) {
        $filters['category__in'] = $theme_options['theme_categories'];
    }

    if (!empty($theme_options['theme_tags'])) {
        $tags = explode(',', $theme_options['theme_tags']);
        $tags = array_unique(array_map('sanitize_title', $tags));
        $filters['tag'] = $tags;
    }

    if (!empty($theme_options['theme_post_types'])) {
        $filters['post_type'] = $theme_options['theme_post_types'];
    }

    if (!isset($theme_options['theme_language'])) $theme_options['theme_language'] = '';
    $posts = Newsletter::instance()->get_posts($filters, $theme_options['theme_language']);
    
    $this->switch_language($theme_options['theme_language']);
}

?><!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <title>{email_subject}</title>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        * {
            line-height: normal;
        }
        h1, h2, h3, h4, h5 {
            line-height: normal;
        }
        a {
            text-decoration: none;
            color: <?php echo $color; ?>;
        }
        #outlook a {
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        p {
            display: block;
            margin: 13px 0;
        }
        @media all and (max-width: 525px) {
            td {
                float: left;
                display: block;
            }
        }
    </style>
    <!--[if mso]>
    <xml>
    <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    
    <!--[if lte mso 11]>
    <style type="text/css">
        .mj-outlook-group-fix { width:100% !important; }
    </style>
    <![endif]-->
</head>

<body style="margin: 0!important; padding: 0!important; background-color: <?php echo $background ?>;">

<div style="background-color: <?php echo $background ?>;">
    
    <br>
    
<div style="background-color: #ffffff; margin:0px auto;max-width:600px;font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666; padding: 0; border: 0">    

    <?php echo tnp_outlook_wrapper_open() ?>
    
    <table align="center" bgcolor="#ffffff" width="100%" style="max-width: 600px; width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td valign="top" bgcolor="#ffffff" width="100%" align="left" style="text-align: left; font-family: Helvetica Neue, Helvetica, Arial, sans-serif; font-size: 14px; color: #666;">

                <div style="text-align: center">

                    <?php if ($logo) { ?>
                        <img style="max-width: 500px" alt="<?php echo esc_attr($title) ?>" src="<?php echo esc_attr($logo) ?>">
                    <?php } else { ?>
                        <div style="padding: 30px 0; color: #000; font-size: 28px; border-bottom: 1px solid #ddd; text-align: center;">
                            <?php echo $title ?>
                        </div>
                        <?php if (!empty($theme_options['main_header_sub'])) { ?>
                            <div style="padding: 10px 0; color: #000; font-size: 16px; text-align: center;">
                                <?php echo esc_html($theme_options['main_header_sub']) ?>
                            </div>
                        <?php } ?>    
                    <?php } ?>

                </div>


                <div style="padding: 10px 20px 20px 20px; background-color: #fff; line-height: 18px">

                    <p style="text-align: center; font-size: small;"><a target="_blank"  href="{email_url}">View this email online</a></p>

                    <p>Here you can start to write your message. Be polite with your readers! Don't forget the subject of this message.</p>

                    <?php if (!empty($posts)) { ?>

                        <table cellpadding="5">
                            <?php foreach ($posts as $post) { ?>
                            <?php
                                setup_postdata($post);
                                $image = false;

                                if (isset($theme_options['theme_thumbnails'])) {
                                    // Will be replaces with the new media resizer
                                    $image = tnp_post_thumbnail_src($post, $theme_options['theme_image_size']);
                                }
                                
                                if ($theme_options['theme_image_size'] == 'thumbnail') {
                                    $image_width = 150;
                            } else {
                                $image_width = 300;
                            }

                                $url = get_permalink($post);
                                $excerpt = '';
                                if (isset($theme_options['theme_excerpts'])) {
                                    $excerpt = '<p>' . tnp_post_excerpt($post) . '</p>';
                                }
                                ?>
                                <tr>
                                    <!-- Image column -->
                                    <?php if (isset($theme_options['theme_thumbnails'])) { ?>
                                        <td valign="top" width="<?php echo $image_width?>">
                                            <?php if ($image) { ?>
                                            <a target="_blank"  href="<?php echo $url ?>"><img width="<?php echo $image_width?>" style="width: <?php echo $image_width?>px; min-width: <?php echo $image_width?>px;" src="<?php echo $image ?>" alt="image"></a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    
                                    <td valign="top">
                                        <a target="_blank"  href="<?php echo $url ?>" style="font-size: 20px; line-height: 26px"><?php the_title(); ?></a>
                                        <?php echo $excerpt ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>

                    <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/footer.php'; ?>

                </div>

            </td>
        </tr>
    </table>

    <?php echo tnp_outlook_wrapper_close() ?>

</div>

</div>
    
</body>
</html>
