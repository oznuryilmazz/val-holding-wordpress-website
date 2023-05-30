<?php

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Newsletter_List_Table extends WP_List_Table {
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
	    global $wpMail;
	    
        $per_page = $this -> get_items_per_page('newsletters_history_perpage', 15);
        $page_number = $this -> get_pagenum();
        
        $this -> process_bulk_action();
        
        $data = $this -> table_data($per_page, $page_number);
        usort($data, array($this, 'sort_data'));
        $total_items = $this -> record_count();

        $this -> set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        $this -> _column_headers = $this -> get_column_info();
        
        $this -> items = $data;
    }
    
    /**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count() {
		global $wpdb, $wpMail, $HistoriesList;
		
		$histories_table = $wpdb -> prefix . $wpMail -> History() -> table;
		$historieslists_table = $wpdb -> prefix . $HistoriesList -> table;

		$sql = "SELECT COUNT(DISTINCT " . $histories_table . ".id) FROM " . $histories_table;
		$sql .= " LEFT JOIN " . $historieslists_table . " ON " . $histories_table . ".id = " . $historieslists_table . ".history_id";		
		$sql .= $this -> add_conditions();

		return $wpdb -> get_var($sql);
	}

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data($per_page = 15, $page_number = 1) {	    
	    global $wpdb, $wpMail, $HistoriesList;
	    
        $data = array();
        
        $histories_table = $wpdb -> prefix . $wpMail -> History() -> table;
		$historieslists_table = $wpdb -> prefix . $HistoriesList -> table;
		
		$fields = array('*');
		$columns = '';
		$f = 1;
		foreach ($fields as $field) {
			$columns .= $histories_table . "." . $field;
			if ($f < count($fields)) {
				$columns .= ", ";
			}
			$f++;
		}

		$sql = "SELECT DISTINCT " . $columns . " FROM " . $histories_table;
		$sql .= " LEFT JOIN " . $historieslists_table . " ON " . $histories_table . ".id = " . $historieslists_table . ".history_id";		
		$sql .= $this -> add_conditions();
		
		$orderby = (empty($_REQUEST['orderby'])) ? $histories_table . '.modified' : esc_html($_REQUEST['orderby']);
		$order = (empty($_REQUEST['order'])) ? 'desc' : esc_html($_REQUEST['order']);
		
		$sql .= " ORDER BY " . $orderby . " " . $order;
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb -> get_results($sql, 'ARRAY_A');
		
		if (!empty($result)) {
			$n = 0;
			foreach ($result as $record) {
				$data[$n] = (array) $wpMail -> init_class($wpMail -> History() -> model, $record);				
				$n++;
			}
		}
		
		return $data;
    }
    
    function add_conditions() {
	    global $wpMail, $wpdb, $HistoriesList;
	    
	    $query = '';
	    $conditions = array();
		
		$search = esc_html($_REQUEST['s']);
		if (!empty($search)) {
			$conditions['s'] = $search;
		}
		
		$theme_id = esc_html($_REQUEST['theme_id']);
		if (!empty($theme_id)) {
			$conditions['theme_id'] = $theme_id;
		}
		
		$list = ($_REQUEST['list']);
		if (!empty($list)) {
			$conditions['list'] = $list;
		}

		if(isset($_REQUEST['sent'])) {
            $sent = esc_html($_REQUEST['sent']);
            if (!empty($sent)) {
                $conditions['sent'] = $sent;
            } else {
                $conditions['sent'] = "all";
            }
        }
		else 
		{
			 $conditions['sent'] = "all";
		}
		
		
		$histories_table = $wpdb -> prefix . $wpMail -> History() -> table;
		$historieslists_table = $wpdb -> prefix . $HistoriesList -> table;
		
		if (!empty($conditions)) {
			$query .= " WHERE";
			$q = 0;
			
			foreach ($conditions as $key => $val) {
				$didvalue = false;
				
				switch ($key) {
					case 's'					:
						$query .= " " . $histories_table . ".subject LIKE '%" . esc_sql($val) . "%'";
						$didvalue = true;
						$q++;
						break;
					case 'theme_id'				:
						$query .= " " . $histories_table . ".theme_id = '" . $val . "'";
						$didvalue = true;
						$q++;
						break;
					case 'list'					:
						if ($val == "all") {
							//do nothing
						} elseif ($val == "none") {
							$query .= " " . $histories_table . ".id NOT IN (SELECT history_id FROM " . $historieslists_table . ")";	
							$didvalue = true;
						} else {
							$query .= " " . $historieslists_table . ".list_id = '" . esc_sql($val) . "'";
							$didvalue = true;
						}
						
						$q++;
						break;
					case 'sent'					:										
						if ($val == "sent") {
							$query .= " " . $histories_table . ".sent >= '1' && " . $histories_table . ".state != 'archived'";
							$didvalue = true;
						} elseif ($val == "draft") {
							$query .= " " . $histories_table . ".sent = '0' && " . $histories_table . ".state != 'archived'";
							$didvalue = true;
						} elseif ($val == "archived") {
							$query .= " " . $histories_table . ".state = 'archived'";
							$didvalue = true;
						} elseif ($val == "all") {
							$query .= " " . $histories_table . ".state != 'archived'";
							$didvalue = true;
						}
						
						$q++;
						break;
				}
				
				if (!empty($didvalue) && $q < count($conditions)) {
					$query .= " AND";
				}
			}			
			
		}
		
		return $query;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            'cb'				=>	'<input type="checkbox" />',
            'subject'       	=> 	__('Subject', 'wp-mailinglist'),
            'mailinglists'		=>	__('List/s', 'wp-mailinglist'),
			'theme_id'			=>	__('Template', 'wp-mailinglist'),
			'stats'				=>	__('Stats', 'wp-mailinglist'),
			'sent'				=>	__('Status', 'wp-mailinglist'),
			'recurring'			=>	__('Recurring', 'wp-mailinglist'),
			'post_id'			=>	__('Post', 'wp-mailinglist'),
			'user_id'			=>	__('Author', 'wp-mailinglist'),
			'modified'			=>	__('Date', 'wp-mailinglist'),
			'attachments'		=>	__('Attachments', 'wp-mailinglist'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
	public function get_hidden_columns() {
        return array(
	        'recurring',
	        'post_id',
	        'user_id',
	        'attachments',
        );
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        $sortable = array(
	        'subject'			=>	array('subject', true),
	        'theme_id'			=>	array('template', true),
	        'sent'				=>	array('sent', true),
	        'recurring'			=>	array('recurring', true),
	        'post_id'			=>	array('post_id', true),
	        'user_id'			=>	array('user_id', true),
	        'modified'			=>	array('modified', true),
        );
        
        return $sortable;
    }
    
    function get_bulk_actions() {
		$actions = array(
			'delete'    => __('Delete', 'wp-mailinglist'),
			'archive'	=>	__('Archive', 'wp-mailinglist'),
			'export'	=>	__('Export', 'wp-mailinglist'),
		);
		
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpMail, $Db, $Html, $Email, $Mailinglist, $Theme;
		
		$current_action = $this -> current_action();
        if (!empty($current_action)) {
	        $newsletters = $_REQUEST['newsletters'];
	        if (!empty($newsletters)) {
		        //Detect when a bulk action is being triggered...
		        if ($current_action == "delete") {
		            foreach ($newsletters as $newsletter_id) {
			            $wpMail -> History() -> delete($newsletter_id);
		            }
		        
					$wpMail -> redirect(false, 'message', 20);
		        
		        } elseif ($current_action == "archive") {
			        foreach ($newsletters as $newsletter_id) {
			            $wpMail -> History() -> save_field('state', 'archived', array('id' => $newsletter_id));
		            }
		            
		            $wpMail -> redirect(false, 'message', __('Newsletters archived', 'wp-mailinglist'));
		        } elseif ($current_action == "export") {
					$csvdelimiter = $wpMail -> get_option('csvdelimiter');
	
					if ($emails = $wpMail -> History() -> find_all(false, false, array('modified', "DESC"))) {
						$data = "";
						$data .= '"' . __('Id', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Subject', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Lists', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Template', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Author', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Read %', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Emails Sent', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Emails Read', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Created', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= '"' . __('Modified', 'wp-mailinglist') . '"' . $csvdelimiter;
						$data .= "\r\n";
	
						foreach ($emails as $email) {
							$wpMail -> remove_server_limits();			//remove the server resource limits
	
							$data .= '"' . $email -> id . '"' . $csvdelimiter;
							$data .= '"' . $email -> subject . '"' . $csvdelimiter;
	
							/* Mailing lists */
							if (!empty($email -> mailinglists)) {
								$data .= '"';
								$m = 1;
	
								foreach ($email -> mailinglists as $mailinglist_id) {
									$mailinglist = $Mailinglist -> get($mailinglist_id);
									$data .= esc_html($mailinglist -> title);
	
									if ($m < count($email -> mailinglists)) {
										$data .= $csvdelimiter . ' ';
									}
	
									$m++;
								}
	
								$data .= '"' . $csvdelimiter;
							} else {
								$data .= '""' . $csvdelimiter;
							}
	
							/* Theme */
							if (!empty($email -> theme_id)) {
								$Db -> model = $Theme -> model;
	
								if ($theme = $Db -> find(array('id' => $email -> theme_id))) {
									$data .= '"' . $theme -> title . '"' . $csvdelimiter;
								} else {
									$data .= '""' . $csvdelimiter;
								}
							} else {
								$data .= '""' . $csvdelimiter;
							}
	
							/* Author */
							if (!empty($email -> user_id)) {
								if ($user = get_userdata($email -> user_id)) {
									$data .= '"' . $user -> display_name . '"' . $csvdelimiter;
								} else {
									$data .= '""' . $csvdelimiter;
								}
							} else {
								$data .= '""' . $csvdelimiter;
							}
	
							/* read % */
							$Db -> model = $Email -> model;
							$etotal = $Db -> count(array('history_id' => $email -> id));
							$eread = $Db -> count(array('history_id' => $email -> id, 'read' => "Y"));
							$eperc = (!empty($etotal)) ? (($eread / $etotal) * 100) : 0;
							$data .= '"' . number_format($eperc, 2, '.', '') . '% ' . __('read', 'wp-mailinglist') . '"' . $csvdelimiter;
	
							$data .= '"' . $etotal . '"' . $csvdelimiter; 					// emails sent
							$data .= '"' . $eread . '"' . $csvdelimiter;					// emails read
							$data .= '"' . $email -> created . '"' . $csvdelimiter;		// created date
							$data .= '"' . $email -> modified . '"' . $csvdelimiter;		// modified date
	
							$data .= "\r\n";
						}
	
						if (!empty($data)) {
							$filename = "history-" . date_i18n("Ymd") . ".csv";
							$filepath = $Html -> uploads_path() . DS . $wpMail -> plugin_name . DS . 'export' . DS;
							$filefull = $filepath . $filename;
	
							if ($fh = fopen($filefull, "w")) {
								fwrite($fh, $data);
								fclose($fh);
								$wpMail -> redirect(admin_url('admin.php?page=' . $wpMail -> sections -> history . '&newsletters_exportlink=' . $filename));
							} else {
								$message = sprintf(__('CSV file could not be created, please check write permissions on "%s" folder.', 'wp-mailinglist'), $filepath);
								$wpMail -> redirect(false, "error", $message);
							}
						} else {
							$message = __('CSV data could not be formulated, no emails maybe? Please try again', 'wp-mailinglist');
							$wpMail -> redirect(false, "error", $message);
						}
					} else {
						$message = __('No history/draft emails are available to export!', 'wp-mailinglist');
						$wpMail -> redirect(false, "error", $message);
					}
		        }
		    }
		    
		    $wpMail -> redirect();
	    }
    }
	
	public function extra_tablenav($which = null) {
		global $Mailinglist, $Theme;
		
		if (!empty($which) && $which == "top") {
			?>
			
			<?php $rssfeed = $this -> get_option('rssfeed'); ?>
			<?php if (!empty($rssfeed) && $rssfeed == "Y" && apply_filters($this -> pre . '_admin_history_rsslink', true)) : ?>
				<div class="alignleft actions">
					<a href="<?php echo add_query_arg(array('feed' => "newsletters"), home_url()); ?>" title="<?php _e('RSS feed for all newsletter history', 'wp-mailinglist'); ?>" class="button"><i class="fa fa-rss"></i> <?php _e('RSS', 'wp-mailinglist'); ?></a>
				</div>
			<?php endif; ?>
			<div class="alignleft actions">
	    		<select name="list">
	    			<option <?php echo (empty($_GET['list'])) ? 'selected="selected"' : ''; ?> value=""><?php _e('All Mailing Lists', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($_GET['list']) && $_GET['list'] == "none") ? 'selected="selected"' : ''; ?> value="none"><?php _e('No Mailing Lists', 'wp-mailinglist'); ?></option>
	    			<?php if ($mailinglists = $Mailinglist -> select(true)) : ?>
	    				<?php foreach ($mailinglists as $list_id => $list_title) : ?>
	    					<option <?php echo (!empty($_GET['list']) && $_GET['list'] == $list_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($list_id); ?>"><?php echo esc_html($list_title); ?></option>
	    				<?php endforeach; ?>
	    			<?php endif; ?>
	    		</select>
	    		<select name="sent">
		    		<option <?php echo (empty($_GET['sent'])) ? 'selected="selected"' : ''; ?> value="all"><?php _e('All Status (Except Archived)', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "draft") ? 'selected="selected"' : ''; ?> value="draft"><?php _e('Draft', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "sent") ? 'selected="selected"' : ''; ?> value="sent"><?php _e('Sent', 'wp-mailinglist'); ?></option>
	    			<option <?php echo (!empty($_GET['sent']) && $_GET['sent'] == "archived") ? 'selected="selected"' : ''; ?> value="archived"><?php _e('Archived', 'wp-mailinglist'); ?></option>
	    		</select>
	    		<select name="theme_id">
	    			<option <?php echo (empty($_GET['theme_id'])) ? 'selected="selected"' : ''; ?> value=""><?php _e('All Templates', 'wp-mailinglist'); ?></option>
	    			<?php if ($themes = $Theme -> select()) : ?>
	    				<?php foreach ($themes as $theme_id => $theme_title) : ?>
	    					<option <?php echo (!empty($_GET['theme_id']) && $_GET['theme_id'] == $theme_id) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($theme_id); ?>"><?php echo $theme_title; ?></option>
	    				<?php endforeach; ?>
	    			<?php endif; ?>
	    		</select>
	    		<button value="1" type="submit" name="filter" class="button button-primary">
	    			<i class="fa fa-filter fa-fw"></i> <?php _e('Filter', 'wp-mailinglist'); ?>
	    		</button>
	    	</div>
			
			<?php
		}
	}

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
                return $item[$column_name];
				break;
            default:
                return $item[$column_name];
                break;
        }
    }
    
    /**
	 * Method for subject column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_subject( $item ) {
		global $wpMail;

		$title = '';

		$title .= '<strong>';
		$title .= '<a class="row-title" href="' . admin_url('admin.php?page=' . $wpMail -> sections -> history . '&method=view&id=' . $item['id']) . '">' . esc_html($item['subject']) . '</a>';
		$title .= '<span class="post-state">';
		
		if ($item['scheduled'] == "Y") :
			$title .= ' ' . __('- Scheduled', 'wp-mailinglist');
		elseif ($item['sent'] <= 0) :
			$title .= ' ' . __('- Draft', 'wp-mailinglist');
		endif;
		
		$title .= '</span>';
		$title .= '</strong>';

		$actions = [
			'send'			=>	sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '">%s</a>', $wpMail -> sections -> send, 'history', $item['id'], __('Send/Edit', 'wp-mailinglist')),
			'delete'		=>	sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '" onclick="%s">%s</a>', $wpMail -> sections -> history, 'delete', $item['id'], "if (!confirm('" . __('Are you sure you want to delete this newsletter?', 'wp-mailinglist') . "')) { return false; }", __('Delete', 'wp-mailinglist')),
			'edit'			=>	sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '">%s</a>', $wpMail -> sections -> history, 'duplicate', $item['id'], __('Duplicate', 'wp-mailinglist')),
			'archive'		=>	sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '">%s</a>', $wpMail -> sections -> history, 'archive', $item['id'], __('Archive', 'wp-mailinglist')),
			'view'			=>	sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '">%s</a>', $wpMail -> sections -> history, 'view', $item['id'], __('View', 'wp-mailinglist')),
		];

		return $title . $this -> row_actions( $actions );
	}
	
	function column_mailinglists($item = array()) {
		global $wpMail, $wpdb, $Mailinglist;
		
		$lists = '';
		
		if (!empty($item['mailinglists'])) {
			$mailinglists = maybe_unserialize($item['mailinglists']);
			if (!empty($mailinglists) && is_array($mailinglists)) {
				$m = 1;
				foreach ($mailinglists as $mailinglist_id) {
					if ($mailinglist = $Mailinglist -> get($mailinglist_id, false)) {
						$lists .= sprintf('<a href="' . admin_url('admin.php?page=%s&method=%s&id=%s') . '">%s</a>', $wpMail -> sections -> lists, 'view', $mailinglist -> id, esc_html($mailinglist -> title));
						
						if ($m < count($mailinglists)) {
							$lists .= ', ';
						}
						
						$m++;
					}
				}
			}
		} else {
			$lists = __('None', 'wp-mailinglist');
		}
		
		return $lists;
	}
	
	function column_theme_id($item = array()) {
		global $wpdb, $Db, $wpMail, $Theme;
		
		$template = '';
		
		if (!empty($item['theme_id'])) {
			$Db -> model = $Theme -> model;
			if ($theme = $Db -> find(array('id' => $item['theme_id']))) :
				$template .= '<a href="" onclick="jQuery.colorbox({iframe:true, width:\'80%\', height:\'80%\', href:\'' . add_query_arg(array('wpmlmethod' => 'themepreview', 'id' => $theme -> id), home_url()) . '\'}); return false;">' . esc_html($theme -> title) . '</a>';
			endif;
		} else {
			$template = __('None', 'wp-mailinglist');
		}
		
		return $template;
	}
	
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="newsletters[]" value="%s" />', $item['id']
        );    
    }
    
    function column_stats($item = array()) {
	    global $Db, $Html, $Email, $wpMail, $wpdb, $Bounce, $Unsubscribe;
	    
	    $stats = '';
											
		$Db -> model = $Email -> model;
		$etotal = $Db -> count(array('history_id' => $item['id']));
		$eread = $Db -> count(array('history_id' => $item['id'], 'read' => "Y"));	
		
		global $wpdb;
		$tracking = (!empty($etotal)) ? ($eread/$etotal) * 100 : 0;
		
		$query = "SELECT SUM(`count`) FROM `" . $wpdb -> prefix . $Bounce -> table . "` WHERE `history_id` = '" . esc_sql($item['id']) . "'";
		$ebounced = $wpdb -> get_var($query);
		$ebouncedperc = (!empty($etotal)) ? (($ebounced / $etotal) * 100) : 0; 
		
		$query = "SELECT COUNT(DISTINCT `email`) FROM `" . $wpdb -> prefix . $Unsubscribe -> table . "` WHERE `history_id` = '" . esc_sql($item['id']) . "'";
		$eunsubscribed = $wpdb -> get_var($query);		

		$eunsubscribeperc = (!empty($etotal)) ? (($eunsubscribed / $etotal) * 100) : 0;
		$clicks = $wpMail -> Click() -> count(array('history_id' => $item['id']));
		
		$stats .= '<a href="' . admin_url('admin.php?page=' . $wpMail -> sections -> history . '&method=view&id=' . $item['id']) . '">' . sprintf("%s / %s / %s / %s", '<span style="color:#46BFBD;">' . number_format($tracking, 2, '.', '') . '&#37;</span>', '<span class="newsletters_warning">' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;</span>', '<span class="newsletters_error">' . number_format($ebouncedperc, 2, '.', '') . '&#37;</span>', $clicks) . '</a>';
		$stats .= $Html -> help(sprintf(__('%s read %s, %s unsubscribes %s, %s bounces %s and %s clicks out of %s emails sent out', 'wp-mailinglist'), '<strong>' . $eread . '</strong>', '(' . ((!empty($etotal)) ? number_format((($eread/$etotal) * 100), 2, '.', '') : 0) . '&#37;)', '<strong>' . $eunsubscribed . '</strong>', '(' . number_format($eunsubscribeperc, 2, '.', '') . '&#37;)', '<strong>' . $ebounced . '</strong>', '(' . number_format($ebouncedperc, 2, '.', '') . '&#37;)', '<strong>' . $clicks . '</strong>', '<strong>' . $etotal . '</strong>'));
		
		return $stats;
    }
    
    function column_sent($item = array()) {
	    global $Db, $Email, $Html, $wpMail;
	    
	    $sent = '';
	    
	    ob_start();
	    
	    $Db -> model = $Email -> model;
		$etotal = $Db -> count(array('history_id' => $item['id']));
	    
	    ?>
	    
	    <?php if ($item['state'] == "archived") : ?>
			<span class="newsletters_warning"><i class="fa fa-archive fa-fw"></i> <?php _e('Archived', 'wp-mailinglist'); ?></span>
	    <?php elseif ($item['scheduled'] == "Y") : ?>
			<span class="newsletters_warning"><i class="fa fa-history fa-fw"></i> <?php _e('Scheduled', 'wp-mailinglist'); ?></span>
			<br/><small>(<abbr title="<?php echo $item['senddate']; ?>"><?php echo $Html -> gen_date(false, strtotime($item['senddate']), false, true); ?></abbr>)</small>
		<?php elseif ($item['sent'] <= 0) : ?>
			<span class="newsletters_error"><i class="fa fa-save fa-fw"></i> <?php _e('Draft', 'wp-mailinglist'); ?></span>
		<?php else : ?>
			<span class="newsletters_success"><i class="fa fa-check fa-fw"></i> <?php _e('Sent', 'wp-mailinglist'); ?></span>											
			<br/><small>(<?php echo sprintf(__('%s times and %s emails', 'wp-mailinglist'), $item['sent'], $etotal); ?>)</small>
		<?php endif; ?>
		
		<?php
			
		$sent = ob_get_clean();
	    
	    return $sent;
    }
    
    function column_recurring($item = array()) {
	    global $wpMail, $Html;
	    
	    $recurring = '';
	    
	    if (!empty($item['recurring']) && $item['recurring'] == "Y") {
		    $recurring .= '<span class="newsletters_success">' . __('Yes', 'wp-mailinglist') . '</span>';
		    
		    $helpstring = sprintf(__('Send every %s %s', 'wp-mailinglist'), $item['recurringvalue'], $item['recurringinterval']);
    		
    		if (!empty($item['recurringlimit'])) {
	    		$helpstring .= sprintf(__(' and repeat %s times', 'wp-mailinglist'), $item['recurringlimit']);
	    	}
	    	
	    	$recurringdate = $Html -> gen_date(false, strtotime($item['recurringdate']), false, true);
	    	
    		$helpstring .= sprintf(__(', send again at %s and has been sent %s times already'), $recurringdate, $item['recurringsent']);
    		$recurring .= $Html -> help($helpstring);
    		$recurring .= '<br/><small>' . sprintf(__('Next Send: %s', 'wp-mailinglist'), $recurringdate) . '</small>';
	    } else {
		    $recurring .= __('No', 'wp-mailinglist');
	    }
	    
	    return $recurring;
    }
    
    function column_post_id($item = array()) {
	    $posttext = '';
	    
	    if (!empty($item['post_id'])) {
		    if ($post = get_post($item['post_id'])) {
			    $posttext .= sprintf('<a href="%s">%s</a>', get_edit_post_link($item['post_id']), esc_html($post -> post_title));
		    }
	    } else {
		    $posttext = __('None', 'wp-mailinglist');
	    }
	    
	    return $posttext;
    }
    
    function column_user_id($item = array()) {
	    
	    $user = __('None', 'wp-mailinglist');
	    
	    if (!empty($item['user_id'])) {
		    if ($userdata = get_userdata($item['user_id'])) {
			    $user = sprintf('<a href="%s">%s</a>', get_edit_user_link($userdata -> ID), $userdata -> display_name);
		    }
	    }
	    
	    return $user;
    }
    
    function column_modified($item = array()) {
	    global $Html;
	    
	    $modified = '';
	    
	    if (!empty($item['modified'])) {
		    $modified = '<label><abbr title="' . $item['modified'] . '">' . $Html -> gen_date(false, strtotime($item['modified'])) . '</abbr></label>';
	    }
	    
	    return $modified;
    }
    
    function column_attachments($item = array()) {
	    global $Html;
	    
	    $attachmentstext = __('None', 'wp-mailinglist');
	    
	    if (!empty($item['attachments'])) {
		    $attachments = maybe_unserialize($item['attachments']);
		    if (!empty($attachments)) {
			    $attachmentstext = '';
			    $attachmentstext .= '<ul style="padding:0; margin:0;">';
			    foreach ($attachments as $attachment) {
				    $attachmentstext .= '<li class="wpmlattachment">' . $Html -> attachment_link($attachment, false) . '</li>';
			    }
			    $attachmentstext .= '</ul>';
		    }
	    }
	    
	    return $attachmentstext;
    }
    
    /** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No newsletters avaliable.', 'wp-mailinglist' );
	}

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'modified';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))         {
            $orderby = sanitize_text_field(wp_unslash($_GET['orderby']));
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = sanitize_text_field(wp_unslash($_GET['order']));
        }


        $result = strcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc') {
            return $result;
        }

        return -$result;
    }
}
