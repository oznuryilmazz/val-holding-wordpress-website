<?php
/* @var $wpdb wpdb */
/* @var $this NewsletterUsers */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

if ($controls->is_action('remove_unconfirmed')) {
    $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='S'");
    $controls->messages = __('Subscribers not confirmed deleted: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('remove_unsubscribed')) {
    $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='U'");
    $controls->messages = __('Subscribers unsubscribed deleted: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('remove_complained')) {
    $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='P'");
    $controls->messages = __('Subscribers complained deleted: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('remove_bounced')) {
    $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where status='B'");
    $controls->messages = __('Subscribers bounced deleted: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('unconfirm_all')) {
    $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='S' where status='C'");
    $controls->messages = __('Subscribers changed to not confirmed: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('confirm_all')) {
    $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set status='C' where status='S'");
    $controls->messages = __('Subscribers changed to confirmed: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('remove_all')) {
    $r = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE);
    $controls->messages = __('Subscribers deleted: ', 'newsletter') . $r . '.';
}

if ($controls->is_action('list_add')) {
    $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . ((int) $controls->data['list']) . "=1");
    $controls->messages = $r . ' ' . __('added to list', 'newsletter') . ' ' . $controls->data['list'];
}

if ($controls->is_action('list_remove')) {
    $r = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . " set list_" . ((int) $controls->data['list']) . "=0");
    $controls->messages = $r . ' ' . __('removed from list', 'newsletter') . ' ' . $controls->data['list'];
}

if ($controls->is_action('list_delete')) {
    $count = $wpdb->query("delete from " . NEWSLETTER_USERS_TABLE . " where list_" . ((int) $controls->data['list']) . "<>0");
    $this->clean_sent_table();
    $this->clean_stats_table();

    $controls->messages = $count . ' ' . __('deleted', 'newsletter');
}

if ($controls->is_action('language')) {
    $count = $wpdb->query($wpdb->prepare("update " . NEWSLETTER_USERS_TABLE . " set language=%s where language=''", $controls->data['language']));
    $controls->add_message_done();
}

if ($controls->is_action('list_manage')) {
    if ($controls->data['list_action'] == 'move') {
        $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . ((int) $controls->data['list_1']) . '=0, list_' . ((int) $controls->data['list_2']) . '=1' .
                ' where list_' . $controls->data['list_1'] . '=1');
    }

    if ($controls->data['list_action'] == 'add') {
        $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . ((int) $controls->data['list_2']) . '=1' .
                ' where list_' . $controls->data['list_1'] . '=1');
    }
}

if ($controls->is_action('list_none')) {
    $where = '1=1';

    for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
        $where .= ' and list_' . $i . '=0';
    }

    $count = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . ((int) $controls->data['list_3']) . '=1' .
            ' where ' . $where);
    $controls->messages = $count . ' subscribers updated';
}

if ($controls->is_action('update_inactive')) {
    //Update users 'last_activity' column
    $wpdb->query("update `{$wpdb->prefix}newsletter` n join (select user_id, max(s.time) as max_time from `{$wpdb->prefix}newsletter_sent` s where s.open>0 group by user_id) as ss
        on n.id=ss.user_id set last_activity=ss.max_time");

    $inactive_time = (int) $controls->data['inactive_time'];

    $where = 'last_activity > 0 and last_activity<' . (time() - $inactive_time * 30 * 24 * 3600);

    $count = $wpdb->query("update " . NEWSLETTER_USERS_TABLE . ' set list_' . ((int) $controls->data['list_inactive']) . '=1 where ' . $where);
    $controls->messages = $count . ' subscribers updated';
}

if ($controls->is_action('change_status')) {
    $status_1 = $controls->data['status_1'];
    $status_2 = $controls->data['status_2'];
    
    // Status validation
    if (!TNP_User::is_status_valid($status_1) || !TNP_User::is_status_valid($status_2)) {
        die('Invalid status value');
    }
    
    $count = $wpdb->query($wpdb->prepare("update `" . NEWSLETTER_USERS_TABLE . "` set status=%s where status=%s", $status_2, $status_1));

    $controls->messages = $count . ' subscribers updated';
}
?>

<div class="wrap tnp-users tnp-users-massive" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Subscribers Maintenance', 'newsletter') ?></h2>
        <p><?php _e('Please backup before running a massive action.', 'newsletter') ?></p>

    </div>

    <div id="tnp-body">

        <?php if (!empty($results)) { ?>

            <h3>Results</h3>

            <textarea wrap="off" style="width: 100%; height: 150px; font-size: 11px; font-family: monospace"><?php echo esc_html($results) ?></textarea>

        <?php } ?>


        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1"><?php _e('General', 'newsletter') ?></a></li>
                    <li><a href="#tabs-2"><?php _e('Lists', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-1">
                    <table class="widefat" style="width: auto">
                        <thead>
                            <tr>
                                <th><?php _e('Status', 'newsletter') ?></th>
                                <th><?php _e('Total', 'newsletter') ?></th>
                                <th><?php _e('Actions', 'newsletter') ?></th>
                            </tr>
                        </thead>
                        <tr>
                            <td><?php _e('Total', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE); ?>
                            </td>
                            <td nowrap>
                                <?php $controls->button_confirm('remove_all', __('Delete all', 'newsletter'), __('Are you sure you want to remove ALL subscribers?', 'newsletter')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Confirmed', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='C'"); ?>
                            </td>
                            <td nowrap>
                                <?php $controls->button_confirm('unconfirm_all', __('Unconfirm all', 'newsletter')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Not confirmed</td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='S'"); ?>
                            </td>
                            <td nowrap>
                                <?php $controls->button_confirm('remove_unconfirmed', __('Delete all', 'newsletter')); ?>
                                <?php $controls->button_confirm('confirm_all', __('Confirm all', 'newsletter'), __('Are you sure you want to mark ALL subscribers as confirmed?', 'newsletter')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Unsubscribed', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='U'"); ?>
                            </td>
                            <td>
                                <?php $controls->button_confirm('remove_unsubscribed', __('Delete all', 'newsletter')); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><?php _e('Bounced', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='B'"); ?>
                            </td>
                            <td>
                                <?php $controls->button_confirm('remove_bounced', __('Delete all', 'newsletter')); ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><?php _e('Complained', 'newsletter') ?></td>
                            <td>
                                <?php echo $wpdb->get_var("select count(*) from " . NEWSLETTER_USERS_TABLE . " where status='P'"); ?>
                            </td>
                            <td>
                                <?php $controls->button_confirm('remove_complained', __('Delete all', 'newsletter')); ?>
                            </td>
                        </tr>
                         <tr>
                            <td>
                                <?php _e('Change status', 'newsletter') ?>
                            </td>
                            <td>
                                <?php $controls->user_status('status_1'); ?>
                                <?php _e('to', 'newsletter') ?>
                                <?php $controls->user_status('status_2'); ?>
                            </td>
                            <td>
                                <?php $controls->button_confirm('change_status', __('Change', 'newsletter')); ?>
                            </td>
                         </tr>
                         
                        <tr>
                            <td>
                                <?php _e('Inactive since', 'newsletter') ?>
                                <?php $controls->field_help('https://www.thenewsletterplugin.com/documentation/subscribers-and-management/subscribers/#inactive')?>
                            </td>
                            <td>
                                <?php
                                $controls->select('inactive_time', array(
                                    '3' => '3 ' . __('months', 'newsletter'),
                                    '6' => '6 ' . __('months', 'newsletter'),
                                    '12' => '1 ' . __('year', 'newsletter'),
                                    '24' => '2 ' . __('years', 'newsletter'),
                                    '36' => '3 ' . __('years', 'newsletter'),
                                    '48' => '4 ' . __('years', 'newsletter'),
                                    '60' => '5 ' . __('years', 'newsletter'),
                                    '72' => '6 ' . __('years', 'newsletter'),
                                    '84' => '7 ' . __('years', 'newsletter'),
                                    '96' => '8 ' . __('years', 'newsletter'),
                                    '108' => '9 ' . __('years', 'newsletter'),
                                    '120' => '10 ' . __('years', 'newsletter')
                                ))
                                ?>
                                add to
                                <?php $controls->lists_select('list_inactive'); ?>

                            </td>
                            <td>
                                <?php $controls->btn('update_inactive', __('Update', 'newsletter'), ['confirm'=>true]); ?>
                            </td>
                        </tr>

                        <?php if ($this->is_multilanguage()) { ?>
                        <tr>
                            <td>Language</td>
                            <td>
                                <?php _e('Set to', 'newsletter') ?>
                                <?php $controls->language('language', false) ?> <?php _e('subscribers without a language', 'newsletter') ?>
                            </td>
                            <td>
                                <?php $controls->btn('language', '&raquo;', ['confirm'=>true]); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>


                </div>


                <div id="tabs-2">
                    <table class="form-table">
                        <tr>
                            
                            <td>
                                <?php $controls->lists_select('list') ?>
                                <?php $controls->button_confirm('list_add', 'Activate for everyone'); ?>
                                <?php $controls->button_confirm('list_remove', 'Deactivate for everyone'); ?>
                                <?php $controls->button_confirm('list_delete', 'Delete everyone in that list'); ?>
                                <p class="description">
                                    If you choose to <strong>delete</strong> users in a list, they will be
                                    <strong>physically deleted</strong> from the database (no way back).
                                </p>
                            </td>
                        </tr>
                        <tr>
                            
                            <td>
                                <?php $controls->select('list_action', array('move' => 'Change', 'add' => 'Add')); ?>
                                <?php _e('all subscribers in', 'newsletter') ?> <?php $controls->lists_select('list_1'); ?>
                                <?php _e('to', 'newsletter') ?> <?php $controls->lists_select('list_2'); ?>
                                <?php $controls->button_confirm('list_manage', '&raquo;'); ?>
                                
                            </td>
                        </tr>
                        <tr>
                            
                            <td>
                                <?php _e('Add to list', 'newsletter') ?>
                                <?php $controls->lists_select('list_3') ?> <?php _e('subscribers without a list', 'newsletter') ?> <?php $controls->button_confirm('list_none', '&raquo;'); ?>
                            </td>
                        </tr>



                    </table>
                </div>

            </div>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
