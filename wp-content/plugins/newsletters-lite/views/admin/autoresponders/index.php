<?php // phpcs:ignoreFile ?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
    <h1><?php esc_html_e('Manage Autoresponders', 'wp-mailinglist'); ?> <a class="add-new-h2" href="?page=<?php echo esc_html( $this -> sections -> autoresponders); ?>&amp;method=save" title="<?php esc_html_e('Add a new autoresponder', 'wp-mailinglist'); ?>"><?php esc_html_e('Add New', 'wp-mailinglist'); ?></a></h1>
    
    <?php if (apply_filters('newsletters_autoresponders_allow_schedule_interval', true)) : ?>
	    <form method="post" action="<?php echo wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> autoresponders . '&method=autoresponderscheduling'), $this -> sections -> autoresponders . '_scheduling'); ?>">
	    	<label for="autoresponderscheduling"><?php esc_html_e('Schedule Interval:', 'wp-mailinglist'); ?></label>
	        <?php $scheduleinterval = $this -> get_option('autoresponderscheduling'); ?>
	    	<select name="autoresponderscheduling" id="autoresponderscheduling">
	        	<?php $schedules = wp_get_schedules(); ?>
				<?php if (!empty($schedules)) : ?>
	                <?php foreach ($schedules as $key => $val) : 
						if (preg_match('/wp_|every_minute/', $key)) continue;
					?>
	                <?php $sel = ($scheduleinterval == $key) ? 'selected="selected"' : ''; ?>
	                <option <?php echo $sel; ?> value="<?php echo esc_html($key) ?>"><?php echo wp_kses_post($val['display']); ?> (<?php echo wp_kses_post($val['interval']); ?> <?php esc_html_e('seconds', 'wp-mailinglist'); ?>)</option>
	                <?php endforeach; ?>
	            <?php endif; ?>
	        </select>
	        <button class="button" value="1" type="submit" name="submit">
	        	<?php esc_html_e('Save Interval', 'wp-mailinglist'); ?>
	        </button>
	        <a href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> settings_tasks . '&amp;method=runschedule&amp;hook=autoresponders')) ?>" class="button button-secondary"><?php esc_html_e('Run Now', 'wp-mailinglist'); ?></a>
	        <?php echo ( $Html -> help(sprintf(__('You can set the interval at which the plugin will check for autoresponder emails which need to be loaded and sent to the subscribers. This schedule runs using the WordPress cron jobs and can be monitored under %s > Configuration > Scheduled Tasks as well.', 'wp-mailinglist'), $this -> name))); ?>
	    </form>
	<?php endif; ?>
	    
	<form id="posts-filter" action="<?php echo wp_nonce_url(admin_url('admin.php?page=' . $this -> sections -> autoresponders), $this -> sections -> autoresponders . '_search'); ?>" method="post">
		<ul class="subsubsub">
            <li><?php echo (empty($_GET['showall'])) ? (!empty($paginate) ? (is_array($paginate -> allcount) ? $paginate -> allcount : '') : '') : (isset($autoresponders) && (is_array($autoresponders)) ?   count($autoresponders) : 0); ?> <?php _e('autoresponders', 'wp-mailinglist'); ?> |</li>
			<?php if (empty($_GET['showall'])) : ?>
				<li><?php echo ( $Html -> link(__('Show All', 'wp-mailinglist'), $this -> url . '&amp;showall=1')); ?></li>
			<?php else : ?>
				<li><?php echo ( $Html -> link(__('Show Paging', 'wp-mailinglist'), '?page=' . $this -> sections -> autoresponders)); ?></li>
			<?php endif; ?>
		</ul>
		<p class="search-box">
			<input id="post-search-input" class="search-input" type="text" name="searchterm" value="<?php echo (!empty($_POST['searchterm'])) ? sanitize_text_field(wp_unslash($_POST['searchterm'])) : (isset($_GET[$this -> pre . 'searchterm']) ? sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm'])) : ''); ?>" />
			<button type="submit" value="1" name="submit" class="button">
				<?php esc_html_e('Search Autoresponders', 'wp-mailinglist'); ?>
			</button>
		</p>
	</form>
	<br class="clear" />
	<form id="posts-filter" action="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> autoresponders)) ?>" method="get">
		<input type="hidden" name="page" value="<?php echo esc_html( $this -> sections -> autoresponders); ?>" />
		<?php wp_nonce_field($this -> sections -> autoresponders . '_filter'); ?>
    	
    	<?php if (!empty($_GET[$this -> pre . 'searchterm'])) : ?>
    		<input type="hidden" name="<?php echo esc_html($this -> pre); ?>searchterm" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET[$this -> pre . 'searchterm']))); ?>" />
    	<?php endif; ?>
    	
    	<div class="alignleft actions">
    		<?php esc_html_e('Filters:', 'wp-mailinglist'); ?>
    		<select name="list">
    			<option <?php echo (!empty($_GET['list']) && $_GET['list'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Mailing Lists', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($_GET['list']) && $_GET['list'] == "none") ? 'selected="selected"' : ''; ?> value="none"><?php esc_html_e('No Mailing Lists', 'wp-mailinglist'); ?></option>
    			<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
    				<?php foreach ($mailinglists as $list_id => $list_title) : ?>
    					<option <?php echo (!empty($_GET['list']) && $_GET['list'] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_html( $list_id); ?>"><?php echo esc_html($list_title); ?></option>
    				<?php endforeach; ?>
    			<?php endif; ?>
    		</select>
    		<select name="status">
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "all") ? 'selected="selected"' : ''; ?> value="all"><?php esc_html_e('All Status', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "active") ? 'selected="selected"' : ''; ?> value="active"><?php esc_html_e('Active', 'wp-mailinglist'); ?></option>
    			<option <?php echo (!empty($_GET['status']) && $_GET['status'] == "inactive") ? 'selected="selected"' : ''; ?> value="inactive"><?php esc_html_e('Inactive', 'wp-mailinglist'); ?></option>
    		</select>
    		<button type="submit" name="filter" value="1" class="button button-primary">
    			<?php esc_html_e('Filter', 'wp-mailinglist'); ?>
    		</button>
    	</div>
    </form>
    <br class="clear" />
    <?php $this -> render('autoresponders' . DS . 'loop', array('autoresponders' => $autoresponders, 'paginate' => $paginate), true, 'admin'); ?>
</div>