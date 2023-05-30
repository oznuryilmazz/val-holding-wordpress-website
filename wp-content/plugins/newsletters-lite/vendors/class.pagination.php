<?php

if (!class_exists('wpMailPaginate')) {
class wpMailPaginate extends wpMailPlugin {
	
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
	
	var $allRecords = array();
	
	var $pagination = '';
	
	function __construct($table = null, $fields = null, $sub = null, $parent = null) {	
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
		global $wpdb;
	
		$page = (empty($page)) ? 1 : $page;
	
		if (!empty($page)) {
			$this -> page = $page;
		}
		
		$query = "SELECT " . $this -> fields . " FROM `" . $this -> table . "`";
		$countquery = "SELECT COUNT(`id`) FROM `" . $this -> table . "`";
		
		//check if some conditions where passed.
		if (!empty($this -> where)) {
			//append the "WHERE" command to the query
			$query .= " WHERE";
			$countquery .= " WHERE";
			$c = 1;
			
			foreach ($this -> where as $key => $val) {
				if (preg_match("/^LIKE/si", $val, $matches)) {					
					$query .= " " . $key . " " . $val . "";	
					$countquery .= " " . $key . " " . $val . "";
				} else {
					$query .= " " . $key . " = " . $val . "";
					$countquery .= " " . $key . " = " . $val . "";
				}
				
				if ($c < count($this -> where)) {
					$query .= " OR";
					$countquery .= " OR";
				}
				
				$c++;
			}
		}
		
		$r = 1;
		
		if ($this -> page > 1) {
			$begRecord = (($this -> page * $this -> perpage) - ($this -> perpage));
		} else {
			$begRecord = 0;
		}
			
		$endRecord = $begRecord + $this -> perpage;
		list($ofield, $odir) = $this -> order;
		$query .= " ORDER BY IF (`" . $ofield . "` = '' OR `" . $ofield . "` IS NULL,1,0), `" . $ofield . "` " . $odir . " LIMIT " . $begRecord . " , " . $this -> perpage . ";";
		$records = $wpdb -> get_results($query);
				
		$records_count = count($records);
		$this -> allcount = $allRecordsCount = $wpdb -> get_var($countquery);
		$totalpagescount = round($records_count / $this -> perpage);
		
		if (empty($this -> url_page)) {
			$this -> url_page = $this -> sub;	
		}
		
		if (count($records) < $allRecordsCount) {			
			$p = 1;
			$k = 1;
			$n = $this -> page;
			$search = (empty($this -> searchterm)) ? '' : '&' . $this -> pre . 'searchterm=' . urlencode($this -> searchterm);
			$orderby = (empty($ofield)) ? '' : '&orderby=' . $ofield;
			$order = (empty($odir)) ? '' : '&order=' . strtolower($odir);			
			$this -> pagination .= '<span class="displaying-num">' . sprintf(__('Displaying %s - %s of %s', 'wp-mailinglist'), ($begRecord + 1), ($begRecord + count($records)), $allRecordsCount) . '</span>';
		
			if ($this -> page > 1) {
				$this -> pagination .= '<a class="prev page-numbers" href="?page=' . $this -> url_page . '&' . $this -> pre . 'page=' . ($this -> page - 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Previous Page', 'wp-mailinglist') . '">&laquo;</a>';
			}
			
			while ($p <= $allRecordsCount) {			
				if ($k >= ($this -> page - 5) && $k <= ($this -> page + 5)) {
					if ($k != $this -> page) {
						$this -> pagination .= '<a class="page-numbers" href="?page=' . $this -> url_page . '&' . $this -> pre . 'page=' . ($k) . $search . $orderby . $order . $this -> after . '" title="' . __('Page', 'wp-mailinglist') . ' ' . $k . '">' . $k . '</a>';
					} else {
						$this -> pagination .= '<span class="page-numbers current">' . $k . '</span>';
					}
				}
				
				$p = $p + $this -> perpage;
				$k++;
			}
			
			if ((count($records) + $begRecord) < $allRecordsCount) {
				$this -> pagination .= '<a class="next page-numbers" href="?page=' . $this -> url_page . '&' . $this -> pre . 'page=' . ($this -> page + 1) . $search . $orderby . $order . $this -> after . '" title="' . __('Next Page', 'wp-mailinglist') . '">&raquo;</a>';
			}
		}
		
		return $records;
	}
}
}

?>