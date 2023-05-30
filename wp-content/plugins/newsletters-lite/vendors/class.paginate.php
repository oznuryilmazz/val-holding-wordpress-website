<?php

class wpmlpaginate extends wpMailPlugin {
	
	/**
	 * DB table name to paginate on
	 *
	 */
	var $table = '';
	var $url_page = "";
	
	/**
	 * Fields for SELECT query
	 * Only these fields will be fetched.
	 * Use asterix for all available fields
	 *
	 */
	var $fields = '*';
	
	/**
	 * Current page
	 *
	 */
	var $page = 1;
	
	/**
	 * Records to show per page
	 *
	 */
	var $perpage = 10;
	
	var $order = array('modified', "DESC");
	
	/**
	 * WHERE conditions
	 * This should be an array
	 *
	 */
	var $where = '';
	
	var $plugin_url = '';
	var $sub = '';
	var $parent = '';
	var $after = '';
	var $allRecords = array();
	
	var $pagination = '';
	var $allcount = "0";
	
	function __construct($table = null, $fields = "*", $sub = null, $parent = null) {	
		$this -> sub = $sub;
		$this -> parentd = $parent;
	
		if (!empty($table)) {
			$this -> table = $table;
		}
		
		if (!empty($fields)) {
			$this -> fields = $fields;
		}
	}
	
	function start_paging($page = null) {
		global $wpdb, $Html, $Subscriber, $wpmlLink, $Bounce, $Email, $SubscribersList, $HistoriesList;
	
		$page = (empty($page)) ? 1 : $page;
		$section = sanitize_text_field(wp_unslash($_GET['page']));
		
		$autoresponders_table = $wpdb -> prefix . $this -> Autoresponder() -> table;
		$subscribers_table = $wpdb -> prefix . $Subscriber -> table;
		$subscriberslists_table = $wpdb -> prefix . $SubscribersList -> table;
		$emails_table = $wpdb -> prefix . $Email -> table;
		$clicks_table = $wpdb -> prefix . $this -> Click() -> table;
		$links_table = $wpdb -> prefix . $this -> Link() -> table;
		$bounces_table = $wpdb -> prefix . $Bounce -> table;
	
		if (!empty($page)) {
			$this -> page = $page;
		}
		
		switch ($this -> model) {
			case 'AutorespondersList'				:
			case 'FieldsList'						:
			case 'SubscribersList'					:
				$countvar = "rel_id";
				break;
			case 'Click'							:
				$countvar = $clicks_table . '.id';
				break;
			default 								:
				$countvar = "id";
				break;
		}
		
		$query = "SELECT " . $this -> fields . " FROM " . $this -> table . "";
		$countquery = "SELECT COUNT(DISTINCT " . $countvar . ") FROM " . $this -> table . "";
		
		$didwhere = false;
		
		switch ($this -> model) {
			case 'Email'						:
				//$query = "SELECT DISTINCT " . $this -> table . ".* FROM `" . $this -> table . "`";
				
				$fields = array('id', 'history_id', 'type', 'subscriber_id', 'mailinglists', 'mailinglist_id', 'user_id', 'status', 'read', 'bounced', 'created');
				$columns = '';
				$f = 1;
				foreach ($fields as $field) {
					$columns .= $emails_table . "." . $field;
					if ($f < count($fields)) {
						$columns .= ", ";
					}
					$f++;
				}
				
				
				if (!empty($section) && isset($this -> sections -> history) && $section == $this -> sections -> history) {
					$columns .= ", COUNT(DISTINCT " . $emails_table . ".id) AS subscribercount";
				}
				
				$query = "SELECT " . $columns . " FROM `" . $this -> table . "`";
				$countquery = "SELECT COUNT(DISTINCT " . $emails_table . ".id) FROM `" . $this -> table . "`";
			
				if (!empty($_GET['orderby']) && $_GET['orderby'] == "subscriber_id") {					
					$query .= " LEFT JOIN " . $subscribers_table . " ON " . $emails_table . ".subscriber_id = " . $subscribers_table . ".id";
					$countquery .= " LEFT JOIN " . $subscribers_table . " ON " . $emails_table . ".subscriber_id = " . $subscribers_table . ".id";
				}
				
				if (!empty($this -> where_and['clicked'])) {
					$didwhere = true;
					$clicked = $this -> where_and['clicked'];
					unset($this -> where_and['clicked']);
					
					if ($clicked == "Y") {
						$group_by = " GROUP BY " . $clicks_table . ".id";
						$didwhere = false;
						
						$query .= " INNER JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id)";
						$countquery .= " INNER JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id)";
					} else {
						$group_by = false;
						$didwhere = true;
						
						$query .= " LEFT JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id) WHERE " . $clicks_table . ".id IS NULL";
						$countquery .= " LEFT JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id) WHERE " . $clicks_table . ".id IS NULL";
					}
				} else {
					if (!empty($_GET['orderby']) && $_GET['orderby'] == "clicked") {
						$query .= " LEFT JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id)";
						$countquery .= " LEFT JOIN " . $clicks_table . " ON (" . $emails_table . ".user_id = " . $clicks_table . ".user_id AND " . $emails_table . ".subscriber_id = " . $clicks_table . ".subscriber_id)";
					}
				}
				
				if (!empty($this -> order[0]) && $this -> order[0] == "clicked") {
					$this -> order[0] = $clicks_table . ".id";	
				}
				break;
			case 'Subscriber'					:
				if (!empty($this -> order[0]) && $this -> order[0] == "lists") {
					$query .= " LEFT JOIN " . $subscriberslists_table . " ON " . $subscribers_table . ".id = " . $subscriberslists_table . ".subscriber_id";
					$countquery .= " LEFT JOIN " . $subscriberslists_table . " ON " . $subscribers_table . ".id = " . $subscriberslists_table . ".subscriber_id";
				}
				break;
			case 'Click'						:
				$query = "SELECT DISTINCT " . $this -> table . ".* FROM `" . $this -> table . "`";
				$countquery = "SELECT COUNT(DISTINCT " . $this -> table . ".id) FROM `" . $this -> table . "`";
				$query .= " LEFT JOIN " . $links_table . " ON " . $clicks_table . ".link_id = " . $links_table . ".id";
				$countquery .= " LEFT JOIN " . $links_table . " ON " . $clicks_table . ".link_id = " . $links_table . ".id";
				break;
			case 'SubscribersList'				:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $Subscriber -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . $Subscriber -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $Subscriber -> table . " ON " . $wpdb -> prefix . $SubscribersList -> table . ".subscriber_id = " . $wpdb -> prefix . $Subscriber -> table . ".id";	
				break;
			case 'HistoriesList'				:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $this -> History() -> table . " ON " . $wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . $this -> History() -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $this -> History() -> table . " ON " . $wpdb -> prefix . $HistoriesList -> table . ".history_id = " . $wpdb -> prefix . $this -> History() -> table . ".id";	
				break;
			case 'AutorespondersList'			:
				$query .= " LEFT JOIN " . $wpdb -> prefix . $this -> Autoresponder() -> table . " ON " . $wpdb -> prefix . $this -> AutorespondersList() -> table . ".autoresponder_id = " . $wpdb -> prefix . $this -> Autoresponder() -> table . ".id";	
				$countquery .= " LEFT JOIN " . $wpdb -> prefix . $this -> Autoresponder() -> table . " ON " . $wpdb -> prefix . $this -> AutorespondersList() -> table . ".autoresponder_id = " . $wpdb -> prefix . $this -> Autoresponder() -> table . ".id";	
				break;
		}
		
		if (!empty($this -> where_and['bounced'])) {
			$bounced = $this -> where_and['bounced'];
			unset($this -> where_and['bounced']);
			$bounce_cond = ($bounced == "Y") ? "IN" : "NOT IN";
			
			if (!$didwhere) {
				$query .= " WHERE";
				$countquery .= " WHERE";
			} else {
				$query .= " AND";
				$countquery .= " AND";
			}
			
			$query .= " " . $emails_table . ".subscriber_id " . $bounce_cond . " 
			(SELECT " . $subscribers_table . ".id FROM " . $subscribers_table . " LEFT JOIN " . 
			$bounces_table . " ON " . $subscribers_table . ".email = " . $bounces_table . ".email";
			
			$countquery .= " " . $emails_table . ".subscriber_id " . $bounce_cond . " 
			(SELECT " . $subscribers_table . ".id FROM " . $subscribers_table . " LEFT JOIN " . 
			$bounces_table . " ON " . $subscribers_table . ".email = " . $bounces_table . ".email";
			
			if (!empty($this -> where[$emails_table . '.history_id'])) {
				$query .= " WHERE " . $bounces_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
				$countquery .= " WHERE " . $bounces_table . ".history_id = '" . $this -> where[$emails_table . '.history_id'] . "'";
			}
			
			$query .= ")";
			$countquery .= ")";
			$didwhere = true;
		}
		
		if (!empty($this -> where)) {
			if (!$didwhere) {
				$didwhere = true;
				$query .= " WHERE (";
				$countquery .= " WHERE (";
			} else {
				$query .= " AND (";
				$countquery .= " AND (";
			}
				
			$c = 1;
			
			foreach ($this -> where as $key => $val) {				
				if (preg_match("/(LIKE)/si", $val)) {
					$query .= " " . $key . " " . $val . "";	
					$countquery .= " " . $key . " " . $val . "";
				} elseif (preg_match("/(NOT IN)/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val . "";
				} elseif (preg_match("/(IN \()/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val;
				} else {
					$query .= " " . $key . " = '" . $val . "'";
					$countquery .= " " . $key . " = '" . $val . "'";
				}
				
				if ($c < count($this -> where)) {
					$query .= " OR";
					$countquery .= " OR";
				}
				
				$c++;
			}
			
			$query .= ")";
			$countquery .= ")";
		}
		
		if (!empty($this -> where_and)) {
			if (!$didwhere) {
				$query .= " WHERE";
				$countquery .= " WHERE";
			} else {
				$query .= " AND";
				$countquery .= " AND";
			}
		
			$a = 1;
		
			foreach ($this -> where_and as $key => $val) {
				if (preg_match("/(LIKE)/si", $val)) {
					$query .= " " . $key . " " . $val . "";	
					$countquery .= " " . $key . " " . $val . "";
				} elseif (preg_match("/(NOT IN)/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val . "";
				} elseif (preg_match("/(IN \()/si", $val)) {
					$query .= " " . $key . " " . $val . "";
					$countquery .= " " . $key . " " . $val;
				} elseif (preg_match("/LE (.*)/si", $val, $vmatches)) {
					$query .= " " . $key . " >= '" . $vmatches[1] . "'";
					$countquery .= " " . $key . " >= '" . $vmatches[1] . "'";
				} elseif (preg_match("/SE (.*)/si", $val, $vmatches)) {
					$query .= " " . $key . " <= '" . $vmatches[1] . "'";
					$countquery .= " " . $key . " <= '" . $vmatches[1] . "'";
				} else {
					$query .= " " . $key . " = '" . $val . "'";
					$countquery .= " " . $key . " = '" . $val . "'";
				}
				
				if ($a < count($this -> where_and)) {
					$query .= " AND";
					$countquery .= " AND";
				}
				
				$a++;
			}
		}
		
		$r = 1;
		
		if ($this -> page > 1) {
			$begRecord = (($this -> page * $this -> perpage) - ($this -> perpage));
		} else {
			$begRecord = 0;
		}
		
		// Group by ?
		switch ($this -> model) {
			case 'Email'					:
			
				break;
			case 'Subscriber'				:
				if (!empty($this -> order[0]) && $this -> order[0] == "lists") {
					$query .= " GROUP BY " . $subscribers_table . ".id";
				}
				break;
			case 'SubscribersList'			:
				$query .= " GROUP BY " . $this -> table . ".subscriber_id";
				break;
		}
			
		$endRecord = $begRecord + $this -> perpage;
		list($ofield, $odir) = $this -> order;
		
		switch ($this -> model) {
			case 'Email'					:		
			
				if (!empty($section) && isset($this -> sections -> history) && $section == $this -> sections -> history) {
					$query .= " GROUP BY " . $emails_table . ".subscriber_id";
				}
							
				if ($ofield == $clicks_table . ".subscriber_id" && $odir == "DESC") {
					$query .= " ORDER BY " . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";
				} else {
					$query .= " ORDER BY " . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";
				}
				
				if ($ofield == $clicks_table . ".subscriber_id") {
					$ofield = "clicked";
				} elseif ($ofield == $subscribers_table . ".email") {
					$ofield = "subscriber_id";
				}
				break;
			case 'Subscriber'				:			
				switch ($ofield) {
					case 'lists'				:
						$query .= " ORDER BY " . $subscriberslists_table . ".list_id " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";
						break;
					default						:
						$query .= " ORDER BY " . $this -> table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";	
						break;
				}
				break;
			case 'SubscribersList'			:
				switch ($ofield) {
					case 'lists'				:
						$query .= " ORDER BY " . $subscriberslists_table . ".list_id " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";
						break;
					default						:
						$query .= " ORDER BY " . $subscribers_table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";	
						break;
				}
				break;
			case 'AutorespondersList'		:
				$query .= " ORDER BY " . $autoresponders_table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";	
				break;
			default							:
				$query .= " ORDER BY " . $this -> table . "." . $ofield . " " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";	
				break;
		}
		
		$records = $wpdb -> get_results($query);					
		$records_count = count($records);
		
		if ($result = $wpdb -> get_var($countquery)) {
			$this -> allcount = (int) $result;
		}
			
		$totalpagescount = ceil($this -> allcount / $this -> perpage);
		
		if (empty($this -> url_page)) {
			$this -> url_page = $this -> sub;	
		}
		
		if (($ofields = explode(".", $ofield)) !== false) {
			if (count($ofields) > 1) {
				$ofield = $ofields[1];
			} else {
				$ofield = $ofields[0];
			}
		}
		
		if (count($records) < $this -> allcount && $this -> allcount > $this -> perpage) {			
			$p = 1;
			$k = 1;
			$n = $this -> page;
			$search = (empty($this -> searchterm)) ? '' : '&' . $this -> pre . 'searchterm=' . urlencode($this -> searchterm);
			$orderby = (empty($ofield)) ? '' : '&orderby=' . $ofield;
			$order = (empty($odir)) ? '' : '&order=' . strtolower($odir);
			$this -> pagination .= '<span class="displaying-num">' . sprintf(__('%s items', 'wp-mailinglist'), $this -> perpage) . '</span>';
			$this -> pagination .= '<span class="pagination-links">';
			$this -> pagination .= '<a href="' . $Html -> retainquery(((!empty($this -> sub)) ? 'page=' . $this -> sub . '&' : false) . $this -> pre . 'page=1' . $search . $orderby . $order . $this -> after) . '" class="first-page button' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '">&laquo;</a>';
			$this -> pagination .= '<a class="prev-page button' . (($this -> page == 1) ? ' disabled" onclick="return false;' : '') . '" href="' . $Html -> retainquery(((!empty($this -> sub)) ? 'page=' . $this -> sub . '&' : false) . $this -> pre . 'page=' . ($this -> page - 1) . $search . $orderby . $order . $this -> after) . '" title="' . __('Previous Page', 'wp-mailinglist') . '">&#8249;</a>';
			$this -> pagination .= '<span class="paging-input">';
			$this -> pagination .= '<input class="newsletters-paged-input current-page" type="text" name="paged" id="paged-input" value="' . esc_attr(wp_unslash($this -> page)) . '" size="2"> ';
			$this -> pagination .= '<span class="tablenav-paging-text">';
			$this -> pagination .= __('of', 'wp-mailinglist'); 
			$this -> pagination .= ' <span class="total-pages">' . $totalpagescount . '</span>';
			$this -> pagination .= '</span>';
			$this -> pagination .= '</span>';
			$this -> pagination .= '<a class="next-page button' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '" href="' . $Html -> retainquery(((!empty($this -> sub)) ? 'page=' . $this -> sub . '&' : false) . $this -> pre . 'page=' . ($this -> page + 1) . $search . $orderby . $order . $this -> after) . '" title="' . __('Next Page', 'wp-mailinglist') . '">&#8250;</a>';
			$this -> pagination .= '<a href="' . $Html -> retainquery(((!empty($this -> sub)) ? 'page=' . $this -> sub . '&' : false) . $this -> pre . 'page=' . $totalpagescount . $search . $orderby . $order . $this -> after) . '" class="last-page button' . (($this -> page == $totalpagescount) ? ' disabled" onclick="return false;' : '') . '">&raquo;</a>';
			$this -> pagination .= '</span>';
			
			ob_start();
			
			?>
			
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.newsletters-paged-input').keypress(function(e) {
					code = (e.keyCode ? e.keyCode : e.which);
		            if (code == 13) {
		            	window.location = '?page=<?php echo $this -> url_page; ?>&<?php echo $this -> pre; ?>page=' + jQuery(this).val() + '<?php echo $search . $orderby . $order . $this -> after; ?>';
		            	e.preventDefault();
		            }
				});
			});
			</script>
			
			<?php
			
			$script = ob_get_clean();
			$this -> pagination .= $script;
		}
		
		return $records;
	}
}

?>