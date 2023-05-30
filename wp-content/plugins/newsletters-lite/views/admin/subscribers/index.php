<?php // phpcs:ignoreFile ?>
<!-- Manage Subscribers -->

<?php

// Are subscribers currently importing? Show a notice
if ($this -> import_process -> queued_items()) {
	$messageid = (empty($import_notification)) ? 21 : 19;
	$this -> render_message(19, array('<a href="' . admin_url('admin.php?page=' . $this -> sections -> importexport . '&method=clear') . '" onclick="if (!confirm(\'' . __('Are you sure you want to cancel and stop the import?', 'wp-mailinglist') . '\')) { return false; }" class="button"><i class="fa fa-times fa-fw"></i> ' . __('Stop Import', 'wp-mailinglist') . '</a>', '<a href="' . admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=wp_import_process_cron') . '" class="button button-secondary"><i class="fa fa-check fa-fw"></i> ' . __('Run Now', 'wp-mailinglist') . '</a>'), true);
}

$saveipaddress = $this -> get_option('saveipaddress');

?>

<div class="wrap <?php echo esc_html($this -> pre); ?> newsletters">
	<h1><?php esc_html_e('Manage Subscribers', 'wp-mailinglist'); ?> <a class="add-new-h2" href="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>&amp;method=save" title="<?php esc_html_e('Create a new subscriber', 'wp-mailinglist'); ?>"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a></h1>
	<form id="posts-filter" action="<?php echo wp_kses_post(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))); ?>" method="post">
		<?php wp_nonce_field($this -> sections -> subscribers . '_search'); ?>
    	<?php if (!empty($subscribers)) : ?>
            <ul class="subsubsub">
                <li><?php echo (empty($_GET['showall'])) ? $paginate -> allcount : count($subscribers); ?> <?php esc_html_e('subscribers', 'wp-mailinglist'); ?> |</li>
                <?php if (empty($_GET['showall'])) : ?>
                    <li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $Html -> retainquery('showall=1'))); ?></li>
                <?php else : ?>
                    <li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), "?page=" . $this -> sections -> subscribers)); ?></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : (isset($_GET[$this -> pre . 'searchterm']) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : ''); ?>" />
			<button value="1" type="submit" class="button">
				<?php esc_html_e('Search Subscribers', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<br class="clear" />
    <form id="posts-filter" action="?page=<?php echo esc_html( $this -> sections -> subscribers); ?>" method="get">
    	<input type="hidden" name="page" value="<?php echo esc_html( $this -> sections -> subscribers); ?>" />
    	
    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
    		<input type="hidden" name="<?php echo esc_html($this -> pre); ?>searchterm" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm']))); ?>" />
    	<?php endif; ?>
    	
    	<div class="alignleft actions">
            <?php _e('Filters:', 'wp-mailinglist'); ?>
            <?php $filter_list = (isset($_COOKIE['newsletters_filter_subscribers_list']) && !empty($_COOKIE['newsletters_filter_subscribers_list'])) ? $_COOKIE['newsletters_filter_subscribers_list'] : (isset($_GET['list']) ? esc_html($_GET['list']) : '' ); ?>
    		<select name="list" onchange="newsletters_change_filter('subscribers', 'list', this.value); if (jQuery('option:selected', this).data('paid') == 1) { jQuery('#expireddiv').show(); } else { jQuery('#expireddiv').hide(); }">
    			<option <?php echo (!empty($filter_list) && $filter_list == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Mailing Lists', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($filter_list) && $filter_list == "none") ? 'selected="selected"' : ''; ?> value="none"><?php esc_html_e('No Mailing Lists', 'wp-mailinglist'); ?></option>
    			<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
    				<?php foreach ($mailinglists as $list_id => $list_title) : ?>
    					<?php $mailinglist = $Mailinglist -> get($list_id); ?>
    					<option data-paid="<?php echo (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") ? 1 : 0; ?>" <?php echo (!empty($filter_list) && $filter_list == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $list_id); ?>"><?php echo esc_html($list_title); ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
    		</select>
    		<?php
	    		
	    	$filter_expired = (!empty($_COOKIE['newsletters_filter_subscribers_expired'])) ? sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_expired'])) : (isset($_GET['expired']) ? sanitize_text_field(wp_unslash($_GET['expired'])) : '');
	    	$showexpired = false;
	    	if (!empty($filter_list)) {
		    	if ($mailinglist = $Mailinglist -> get($filter_list)) {
			    	if (!empty($mailinglist -> paid) && $mailinglist -> paid == "Y") {
				    	$showexpired = true;
			    	}
		    	}
	    	}	
	    		
	    	?>
    		<span id="expireddiv" style="display:<?php echo (!empty($showexpired)) ? '' : 'none'; ?>;">
    			<select name="expired" onchange="newsletters_change_filter('subscribers', 'expired', this.value);">
	    			<option <?php echo (!empty($filter_expired) && $filter_expired == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Expired/Not Expired', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($filter_expired) && $filter_expired == "expired") ? 'selected="selected"' : ''; ?> value="expired"><?php esc_html_e('Expired', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($filter_expired) && $filter_expired == "notexpired") ? 'selected="selected"' : ''; ?> value="notexpired"><?php esc_html_e('Not Expired', 'wp-mailinglist'); ?></option>
    			</select>
    		</span>
    		<?php $filter_status = (empty($_COOKIE['newsletters_filter_subscribers_status'])) ? (isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '') : sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_status'])); ?>
    		<select name="status" onchange="newsletters_change_filter('subscribers', 'status', this.value);">
    			<option <?php echo (!empty($filter_status) && $filter_status == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Status', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($filter_status) && $filter_status == "active") ? 'selected="selected"' : ''; ?> value="active"><?php esc_html_e('Active Subscriptions', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($filter_status) && $filter_status == "inactive") ? 'selected="selected"' : ''; ?> value="inactive"><?php esc_html_e('Inactive Subscriptions', 'wp-mailinglist'); ?></option>
    		</select>
    		<?php $filter_registered = (empty($_COOKIE['newsletters_filter_subscribers_registered'])) ? (isset($_GET['registered']) ? sanitize_text_field(wp_unslash($_GET['registered'])) : '') : sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_registered'])); ?>
    		<select name="registered" onchange="newsletters_change_filter('subscribers', 'registered', this.value);">
    			<option <?php echo (!empty($filter_registered) && $filter_registered == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Subscribers', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($filter_registered) && $filter_registered == "Y") ? 'selected="selected"' : ''; ?> value="Y"><?php esc_html_e('Registered Users', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($filter_registered) && $filter_registered == "N") ? 'selected="selected"' : ''; ?> value="N"><?php esc_html_e('Not Registered', 'wp-mailinglist'); ?></option>
    		</select>
    		<?php if (!empty($saveipaddress)) : ?>
    			<?php $filter_country = (empty($_COOKIE['newsletters_filter_subscribers_country'])) ? (isset($_GET['country']) ? sanitize_text_field(wp_unslash($_GET['country'])) : '') : sanitize_text_field(wp_unslash($_COOKIE['newsletters_filter_subscribers_country'])); ?>
    			<select name="country" onchange="newsletters_change_filter('subscribers', 'country', this.value);">
	    			<option <?php selected($filter_country, "all", true); ?> <?php selected($filter_country, "", true); ?> value="all"><?php esc_html_e('All Countries', 'wp-mailinglist'); ?></option>
	    			<option value="none"><?php esc_html_e('No Country', 'wp-mailinglist'); ?></option>
	    			<?php if ($countries = $this -> Country() -> select_code()) : ?>
	    				<?php foreach ($countries as $country_code => $country_name) : ?>
	    					<option <?php selected($filter_country, $country_code, true); ?> value="<?php echo esc_html( $country_code); ?>"><?php echo esc_html( $country_name); ?></option>
	    				<?php endforeach; ?>
	    			<?php endif; ?>
    			</select>
    		<?php endif; ?>
    		<button value="1" type="submit" name="filter" class="button button-primary">
    			<?php esc_html_e('Filter', 'wp-mailinglist'); ?>
    		</button>
    	</div>
    </form>
    <br class="clear" />
	<?php $this -> render('subscribers' . DS . 'loop', array('subscribers' => $subscribers, 'paginate' => $paginate), true, 'admin'); ?>
</div>