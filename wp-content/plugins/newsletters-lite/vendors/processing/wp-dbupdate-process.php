<?php

if (!class_exists('WP_Dbupdate_Process')) {
	class WP_Dbupdate_Process extends WP_Background_Process {
	
	    /**
	     * @var string
	     */
	    protected $action = 'dbupdate_process';
	    
	    function get_batches($onlykeys = false) {
		    global $wpdb;

			$table        = $wpdb->options;
			$column       = 'option_name';
			$key_column   = 'option_id';
			$value_column = 'option_value';

			$key = $this->identifier . '_batch_%';

			$query = $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC", $key );

			$results = $wpdb->get_results($query);
			
			$batches = array();
			
			if (!empty($results)) {
				foreach ($results as $result) {
					$batch = new stdClass();
					$batch -> key = $result -> {$column};
					
					if (empty($onlykeys)) {
						$batch -> data = maybe_unserialize($result -> {$value_column});
					}
					
					$batches[] = $batch;
					
					if ($this -> memory_exceeded()) {
						$this -> memory_exceeded = true;
						return $batches;
					}
				}
			}

			return $batches;
	    }
	    
	    function cancel_all_processes() {
		    if ($batches = $this -> get_batches(true)) {
			    foreach ($batches as $batch) {
				    $this -> delete($batch -> key);
			    }
		    }
		    
		    parent::complete();
		    return true;
	    }
	
	    /**
	     * Task
	     *
	     * Override this method to perform any actions required on each
	     * queue item. Return the modified item for further processing
	     * in the next pass through. Or, return false to remove the
	     * item from the queue.
	     *
	     * @param mixed $item Queue item to iterate over
	     *
	     * @return mixed
	     */
	    protected function task( $item ) {
	        // Actions to perform
	        
	        global $wpMail, $wpdb;
	        
	        $wpdb -> query($item);
	        $wpMail -> log_error('db update done: ' . $item);
	
	        return false;
	    }
	
	    /**
	     * Complete
	     *
	     * Override if applicable, but ensure that the below actions are
	     * performed, or, call parent::complete().
	     */
	    protected function complete() {
	        parent::complete();
	
	        // Show notice to user or perform some other arbitrary task...
	    }
	
	}
}