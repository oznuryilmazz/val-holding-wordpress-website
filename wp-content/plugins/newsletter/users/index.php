<?php
/* @var $this NewsletterUsers */
defined('ABSPATH') || exit;

require_once NEWSLETTER_INCLUDES_DIR . '/controls.php';

$controls = new NewsletterControls();

$options = $controls->data;
$options_profile = get_option('newsletter_profile');
$options_main = get_option('newsletter_main');

// Move to base zero
if ($controls->is_action()) {
    if ($controls->is_action('reset')) {
        $controls->data = array();
    } else {
        $controls->data['search_page'] = (int) $controls->data['search_page'] - 1;
    }
    $this->save_options($controls->data, 'search');
} else {
    $controls->data = $this->get_options('search');
    if (empty($controls->data['search_page'])) {
        $controls->data['search_page'] = 0;
    }
}

if ($controls->is_action('resend')) {
    $user = $this->get_user($controls->button_data);
    NewsletterSubscription::instance()->send_message('confirmation', $user, true);
    $controls->messages = __('Activation email sent.', 'newsletter');
}

if ($controls->is_action('resend_welcome')) {
    $user = $this->get_user($controls->button_data);
    NewsletterSubscription::instance()->send_message('confirmed', $user, true);
    $controls->messages = __('Welcome email sent.', 'newsletter');
}

if ($controls->is_action('delete')) {
    $this->delete_user($controls->button_data);
    unset($controls->data['subscriber_id']);
}

if ($controls->is_action('delete_selected')) {
    $r = Newsletter::instance()->delete_user($_POST['ids']);
    $controls->messages .= $r . ' user(s) deleted';
}

// We build the query condition
$where = 'where 1=1';
$query_args = array();
$text = trim($controls->get_value('search_text'));
if ($text) {
    $query_args[] = '%' . $text . '%';
    $query_args[] = '%' . $text . '%';
    $query_args[] = '%' . $text . '%';
    $query_args[] = '%' . $text . '%';
    $where .= " and (id like %s or email like %s or name like %s or surname like %s)";
}

if (!empty($controls->data['search_status'])) {
    if ($controls->data['search_status'] == 'T') {
        $where .= " and test=1";
    } else {
        $query_args[] = $controls->data['search_status'];
        $where .= " and status=%s";
    }
}

if (!empty($controls->data['search_list'])) {
    $where .= " and list_" . ((int) $controls->data['search_list']) . "=1";
}

$filtered = $where != 'where 1=1';

// Total items, total pages
$items_per_page = 20;
if (!empty($query_args)) {
    $where = $wpdb->prepare($where, $query_args);
}
$count = Newsletter::instance()->store->get_count(NEWSLETTER_USERS_TABLE, $where);
$last_page = floor($count / $items_per_page) - ($count % $items_per_page == 0 ? 1 : 0);
if ($last_page < 0) {
    $last_page = 0;
}

if ($controls->is_action('last')) {
    $controls->data['search_page'] = $last_page;
}
if ($controls->is_action('first')) {
    $controls->data['search_page'] = 0;
}
if ($controls->is_action('next')) {
    $controls->data['search_page'] = (int) $controls->data['search_page'] + 1;
}
if ($controls->is_action('prev')) {
    $controls->data['search_page'] = (int) $controls->data['search_page'] - 1;
}
if ($controls->is_action('search')) {
    $controls->data['search_page'] = 0;
}

// Eventually fix the page
if (!isset($controls->data['search_page']) || $controls->data['search_page'] < 0)
    $controls->data['search_page'] = 0;
if ($controls->data['search_page'] > $last_page)
    $controls->data['search_page'] = $last_page;

$query = "select * from " . NEWSLETTER_USERS_TABLE . ' ' . $where . " order by id desc";
$query .= " limit " . ($controls->data['search_page'] * $items_per_page) . "," . $items_per_page;
$list = $wpdb->get_results($query);

// Move to base 1
$controls->data['search_page']++;
?>

<style>
<?php include __DIR__ . '/css/users.css' ?>
</style>

<div class="wrap tnp-users tnp-users-index" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <?php $controls->title_help('/subscribers-and-management/') ?>
        <h2><?php _e('Subscribers', 'newsletter') ?></h2>

        <p>
            See the <a href="admin.php?page=newsletter_users_massive">maintenance panel</a> to move subscribers between list, massively delete and so on.
        </p>

    </div>

    <div id="tnp-body">

        <form id="channel" method="post" action="">
            <?php $controls->init(); ?>

            <div class="tnp-users-search">
                <?php $controls->text('search_text', 45, __('Search by ID, email, name', 'newsletter')); ?>

                <?php _e('filter by', 'newsletter') ?>:
                <?php
                $controls->select('search_status', ['' => __('Any', 'newsletter'), 'T' => __('Test subscribers', 'newsletter'), 'C' => TNP_User::get_status_label('C'),
                    'S' => TNP_User::get_status_label('S'), 'U' => TNP_User::get_status_label('U'), 'B' => TNP_User::get_status_label('B'), 'P' => TNP_User::get_status_label('P')]);
                ?>
                <?php $controls->lists_select('search_list', '-'); ?>

                <?php $controls->button('search', __('Search', 'newsletter')); ?>
                <?php if ($where != "where 1=1") { ?>
                    <?php $controls->btn('reset', __('Reset Filters', 'newsletter'), ['tertiary' => true]); ?>
                <?php } ?>
                <!--
                <br>
                <?php $controls->checkbox('show_preferences', __('Show lists', 'newsletter')); ?>
                -->
            </div>

            <?php if (false && $filtered) { ?>
                <p><?php _e('The list below is filtered.', 'newsletter') ?></p>
            <?php } ?>        

            <div class="tnp-paginator">

                <?php $controls->btn('first', '«', ['tertiary' => true]); ?>
                <?php $controls->btn('prev', '‹', ['tertiary' => true]); ?>
                <?php $controls->text('search_page', 3); ?> of <?php echo $last_page + 1 ?> <?php $controls->btn('go', __('Go', 'newsletter'), ['secondary' => true]); ?>
                <?php $controls->btn('next', '›', ['tertiary' => true]); ?>
                <?php $controls->btn('last', '»', ['tertiary' => true]); ?>

                <?php echo $count ?> <?php _e('subscriber(s) found', 'newsletter') ?>

                <?php $controls->btn_link('?page=newsletter_users_new', __('Add new', 'newsletter')); ?>
                <?php $controls->btn('delete_selected', __('Delete selected', 'newsletter'), ['tertiary' => true]); ?>


            </div>

            <table class="widefat">
                <thead>
                    <tr>
                        <td class="check-column"><input type="checkbox" onchange="jQuery('input.tnp-selector').prop('checked', this.checked)"></th>
                        <th>Id</th>
                        <th>Email</th>
                        <th><?php _e('Name', 'newsletter') ?></th>
                        <th><?php _e('Status', 'newsletter') ?></th>
                        <th style="white-space: nowrap"><?php $controls->checkbox('show_lists', __('Lists', 'newsletter'), ['onchange'=>'this.form.act.value=\'go\'; this.form.submit()']) ?></th>
                        <th>&nbsp;</th>

                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <?php $i = 0; ?>
                <?php foreach ($list as $s) { ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input class="tnp-selector" type="checkbox" name="ids[]" value="<?php echo $s->id; ?>">
                        </th>
                        <td><?php echo $s->id; ?></td>
                        <td><?php echo esc_html($s->email); ?></td>
                        <td><?php echo esc_html($s->name); ?> <?php echo esc_html($s->surname); ?></td>
                        <td>
                          <?php echo $this->get_user_status_label($s, true) ?>
                        </td>
                       
                            <td>
                                 <?php if (!empty($controls->data['show_lists'])) { ?>
                                <small><?php
                                    $lists = $this->get_lists();
                                    foreach ($lists as $item) {
                                        $l = 'list_' . $item->id;
                                        if ($s->$l == 1)
                                            echo esc_html($item->name) . '<br>';
                                    }
                                    ?></small>
                                <?php } ?>
                            </td>
                        
                        <td>
                            <?php $controls->button_icon_edit($this->get_admin_page_url('edit') . '&amp;id=' . $s->id) ?>
                        </td>
                        <td style="white-space: nowrap">
                            
                            <?php if ($s->status == "C") { ?>
                                <?php $controls->btn('resend_welcome', '', ['secondary' => true, 'data' => $s->id, 'icon' => 'fa-redo', 'confirm' => true, 'title' => __('Resend welcome', 'newsletter')]); ?>
                                <?php //$controls->button_icon('resend_welcome', 'fa-redo', __('Resend welcome', 'newsletter'), $s->id, true); ?>
                            <?php } else { ?>
                                <?php $controls->btn('resend', '', ['secondary' => true, 'data' => $s->id, 'icon' => 'fa-redo', 'confirm' => true, 'title' => __('Resend activation', 'newsletter')]); ?>
                            <?php } ?>
                            
                            <?php $controls->button_icon_delete($s->id); ?>

                        </td>
                    </tr>
                <?php } ?>
            </table>
            <div class="tnp-paginator">

                <?php $controls->btn('first', '«', ['tertiary' => true]); ?>
                <?php $controls->btn('prev', '‹', ['tertiary' => true]); ?>
                <?php $controls->btn('next', '›', ['tertiary' => true]); ?>
                <?php $controls->btn('last', '»', ['tertiary' => true]); ?>
            </div>
        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
