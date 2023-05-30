<?php
/* @var $this NewsletterSubscription */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$current_language = $this->get_current_language();
$is_all_languages = $this->is_all_languages();
$is_multilanguage = $this->is_multilanguage();

$controls->add_language_warning();

if (!$controls->is_action()) {
    $controls->data = $this->get_options('lists', $current_language);

    // Migration
    for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
        // If we already have the new format options, do nothing
        if (isset($controls->data['list_' . $i . '_subscription'])) {
            continue;
        }
        if ($controls->data['list_' . $i . '_status'] != 0) {
            if ($controls->data['list_' . $i . '_status'] == 1) {
                $controls->data['list_' . $i . '_profile'] = 1;
                $controls->data['list_' . $i . '_subscription'] = 0;
            }

            if ($controls->data['list_' . $i . '_status'] == 2) {
                $controls->data['list_' . $i . '_profile'] = 1;
                $controls->data['list_' . $i . '_subscription'] = 1; // Show unchecked
            }

            if ($controls->data['list_' . $i . '_status'] == 3) {
                $controls->data['list_' . $i . '_profile'] = 0;
                $controls->data['list_' . $i . '_subscription'] = 0;
            }

            $controls->data['list_' . $i . '_status'] = 1; // Public
        }

        if ($controls->data['list_' . $i . '_forced'] == 1) {
            $controls->data['list_' . $i . '_subscription'] = 3;
        }
    }
} else {
    if ($controls->is_action('save')) {

        $this->save_options($controls->data, 'lists', null, $current_language);
        $controls->add_message_saved();
    }
    if ($controls->is_action('unlink')) {
        $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . ((int) $controls->button_data) . "=0");
        $controls->add_message_done();
    }
}

$conditions = [];
for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    if (!isset($controls->data['list_' . $i . '_forced'])) {
        $controls->data['list_' . $i . '_forced'] = empty($this->options['preferences_' . $i]) ? 0 : 1;
    }
    $conditions[] = "count(case list_$i when 1 then 1 else null end) list_$i";
}

$all_lang_options = $this->get_options('lists');

//echo json_encode($controls->data, JSON_PRETTY_PRINT);
//$status = array(0 => 'Private', 1 => 'Only on profile page', 2 => 'Even on subscription forms', '3' => 'Hidden');
$status = array(0 => __('Private', 'newsletter'), 1 => __('Public', 'newsletter'));

$count = $wpdb->get_row("select " . implode(',', $conditions) . ' from ' . NEWSLETTER_USERS_TABLE);
?>
<script>
    jQuery(function () {
        jQuery(".tnp-notes").tooltip({
            content: function () {
                // That activates the HTML in the tooltip
                return this.title;
            }
        });

        for (i = 1; i <=<?php echo NEWSLETTER_LIST_MAX ?>; i++) {

            jQuery('#options-list_' + i + '_forced').change(function () {
                let field = '#' + this.id.replace('forced', 'subscription');
                let fieldStatus = '#' + this.id.replace('forced', 'status');
                if (jQuery(fieldStatus).val() === '1') {
                    jQuery(field).toggle(!this.checked);
                }
            });
            jQuery('#options-list_' + i + '_status').change(function () {
                let field = '#' + this.id.replace('status', 'subscription');
                let fieldProfile = '#' + this.id.replace('status', 'profile');
                let fieldForced = '#' + this.id.replace('status', 'forced');
                if (this.value === '0') {
                    jQuery(field).hide();
                    jQuery(fieldProfile).hide();
                } else {
                    if (!jQuery(fieldForced).attr('checked')) {
                        jQuery(field).show();

                    }
                    jQuery(fieldProfile).show();
                }
            });
        }

        for (i = 1; i <=<?php echo NEWSLETTER_LIST_MAX ?>; i++) {
            jQuery('#options-list_' + i + '_status').change();
            jQuery('#options-list_' + i + '_forced').change();
        }
    });
</script>
<div class="wrap tnp-lists" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('/subscription/newsletter-lists/') ?>
        <h2><?php _e('Lists', 'newsletter') ?></h2>

    </div>

    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>
            <p>
                <?php $controls->button_save(); ?>
            </p>
            <table class="widefat" style="width: auto" scope="presentation">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php _e('Name', 'newsletter') ?></th>
                        <?php if ($is_all_languages) { ?>
                            <th><?php _e('Type', 'newsletter') ?></th>
                            <th><?php _e('Enforced', 'newsletter') ?> <i class="fas fa-info-circle tnp-notes" title="<?php esc_attr_e('If you check this box, all your new subscribers will be automatically added to this list', 'newsletter') ?>"></i></th>
                            <th style="white-space: nowrap"><?php _e('Subscription form', 'newsletter') ?></th>
                            <th><?php _e('Profile form', 'newsletter') ?></th>
                            <?php if ($is_multilanguage) { ?>
                                <th><?php _e('Enforced by language', 'newsletter') ?></th>
                            <?php } ?>
                        <?php } ?>
                        <th><?php _e('Subscribers', 'newsletter') ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <?php for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) { ?>
                    <?php
                    if (!$is_all_languages && empty($controls->data['list_' . $i])) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
                            <?php $controls->text('list_' . $i, 50); ?>
                            <?php if (!$is_all_languages) { ?>
                                <p class="description">Main name: <?php echo esc_html($all_lang_options['list_' . $i]) ?></p>
                            <?php } ?>
                        </td>
                        <?php if ($is_all_languages) { ?>
                            <td><?php $controls->select('list_' . $i . '_status', $status); ?></td>
                            <td style="text-align: center">
                                <?php $controls->checkbox('list_' . $i . '_forced'); ?>
                            </td>
                            <td>
                                <?php //$controls->select('list_' . $i . '_subscription', array(0 => 'Do not show', 1 => 'Show, unchecked', 2 => 'Show, checked', 3 => 'Assign to everyone'));  ?>
                                <?php $controls->select('list_' . $i . '_subscription', array(0 => 'Do not show', 1 => 'Show, unchecked', 2 => 'Show, checked')); ?>
                            </td>
                            <td><?php $controls->select('list_' . $i . '_profile', array(0 => 'Do not show', 1 => 'Show')); ?></td>
                            <?php if ($is_multilanguage) { ?>
                                <td><?php $controls->languages('list_' . $i . '_languages'); ?></td>
                            <?php } ?>
                        <?php } ?>

                        <td>
                            <?php //echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where list_" . $i . "=1 and status='C'"); ?>
                            <?php
                            $field = 'list_' . $i;
                            echo $count->$field;
                            ?>
                        </td>

                        <td>
                            <?php if ($is_all_languages) { ?>
                                <?php $controls->button_confirm('unlink', __('Unlink everyone', 'newsletter'), '', $i); ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="7">
                            <?php $notes = apply_filters('newsletter_lists_notes', array(), $i); ?>
                            <?php
                            $text = '';
                            foreach ($notes as $note) {
                                $text .= esc_html($note) . '<br>';
                            }
                            if (!empty($text)) {
                                echo $text;
                                //echo '<i class="fas fa-info-circle tnp-notes" title="', esc_attr($text), '"></i>';
                            }
                            ?> 

                        </td>
                    </tr>
                <?php } ?>
            </table>

            <p>
                <?php $controls->button_save(); ?>
            </p>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>