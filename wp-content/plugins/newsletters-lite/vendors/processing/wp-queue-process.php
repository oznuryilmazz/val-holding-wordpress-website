<?php

if (!class_exists('WP_Queue_Process')) {
	class WP_Queue_Process extends WP_Background_Process {
	
	    /**
	     * @var string
	     */
	    protected $action = 'queue_process';
	    
	    var $counter_reset = 100;
	    var $queue_lock_time = 300;
	    var $memory_exceeded = false;
	    
	    protected $data = array();
	    
	    public function __construct() {
			parent::__construct();

		    add_filter($this -> identifier . '_default_time_limit', array($this, 'default_time_limit'));
	    }
	    
	    public function default_time_limit($time_limit = null) {
		    $time_limit = (MINUTE_IN_SECONDS * 2);
		    
		    $scheduleintervalseconds = get_option('wpmlscheduleintervalseconds');
		    if (!empty($scheduleintervalseconds)) {
			    $time_limit = $scheduleintervalseconds;
		    }
		    		    
		    return $time_limit;
	    }
	    
	    public function reset_data() {
		    $this -> data = array();
	    }
	    
	    function get_batch_key_string($key = null) {
		    if (!empty($key)) {
			    $string = str_replace($this -> identifier . '_batch_', "", $key);
			    return $string;
		    }
		    
		    return false;
	    }
	    
	    function get_specific_batch($key = null) {
		    if (!empty($key)) {
			    global $wpdb;

				$table        = $wpdb -> options;
				$column       = 'option_name';
				$key_column   = 'option_id';
				$value_column = 'option_value';
	
				$query = $wpdb -> get_row($wpdb -> prepare("SELECT * FROM {$table} WHERE {$column} = '%s' LIMIT 1", $key));
	
				if (!empty($query)) {
					$batch = new stdClass();
					$batch -> id = $query -> option_id;
					$batch -> key = $query -> option_name;
					$batch -> data = maybe_unserialize($query -> option_value);
					
					return $batch;
				}
		    }
		    
		    return false;
	    }
	    
	    function get_batches($onlykeys = false, $onlyerrors = false, $number = false) {
		    global $wpdb;

			$table        = $wpdb -> options;
			$column       = 'option_name';
			$key_column   = 'option_id';
			$value_column = 'option_value';

			$key = $this->identifier . '_batch_%';

			$query = "SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s 
			ORDER BY {$key_column} ASC";
			
			if (!empty($number)) {
				$query .= " LIMIT " . ($number - 1) . ", " . ($number) . "";
			}

			$query = $wpdb -> prepare($query, $key);
			$results = $wpdb -> get_results($query);
			
			$batches = array();
			
			if (!empty($results)) {
				foreach ($results as $result) {
					$batch = new stdClass();
					$batch -> key = $result -> {$column};
					
					if (empty($onlykeys)) {
						$data = maybe_unserialize($result -> {$value_column});
						
						if (empty($onlyerrors)) {
							$batch -> data = $data;
						} else {
							foreach ($data as $dkey => $dval) {
								if (empty($dval['error'])) {
									unset($data[$dkey]);
								}
							}
							
							$batch -> data = $data;
						}
						
						if (!empty($batch -> data)) {
							$batches[] = $batch;
						}
					} else {
						$batches[] = $batch;
					}
					
					if ($this -> memory_exceeded()) {
						$this -> memory_exceeded = true;
						return $batches;
					}
				}
			}

			return $batches;
	    }
	    
	    function get_queued_count($key = null) {
		    global $wpdb, $wpMail;
		    
		    $count = 0;

			$table        = $wpdb->options;
			$column       = 'option_name';
			$key_column   = 'option_id';
			$value_column = 'option_value';

			$key = $this->identifier . '_batch_' . "%";

			$query = $wpdb -> prepare( "
			SELECT {$value_column}
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC", $key);

			$query = "SELECT " . $value_column . " FROM " . $table . " WHERE " . $column . " LIKE '" . $key . "' ORDER BY " . $key_column . " ASC";
			$results = $wpdb -> get_results($query);
			
			foreach ($results as $result) {
				$batchcount = preg_match("/^a:([0-9]+):.*/si", $result -> {$value_column}, $matches);
				$batchcount = $matches[1];
				$count += $batchcount;
				
				if ($this -> memory_exceeded()) {
					$count = (string) $count . '+';
					break;
				}
			}
		    
		    return $count;
	    }
	    
	    function unlock() {
		    $this -> unlock_process();
		    return true;
	    }
	    
	    function cancel_all_processes() {
		    global $wpdb;
		    
		    $table = $wpdb -> options;
		    $key = $this -> identifier . '_batch_%';
		    $query = "DELETE FROM `" . $table . "` WHERE `option_name` LIKE '" . $key . "'";
		    $wpdb -> query($query);
		    
		    if ($batches = $this -> get_batches(true)) {			    
			    delete_transient('newsletters_queue_count');
			    foreach ($batches as $batch) {
				    $this -> delete($batch -> key);
			    }
		    }
		    
		    parent::complete();
		    return true;
	    }
	    
	    function do_specific_item($item = null, $override = false) {
		    $result = $this -> task($item, true);
		    return $result;
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
	    protected function task($item = null, $override = false) {
	        // Actions to perform	        
	        global $wpMail, $wpdb, $Html, $Db, $Email, $Subscriber, $SubscribersList;
	        
	        if (empty($override)) {
		        $queue_status = $wpMail -> get_option('queue_status');
		        if (!empty($queue_status) && $queue_status == "pause") {
			        return $item;
		        }
		    }
	        
	        // Try to send the queued email
			if ($wpMail -> send_queued_email($item)) {				
				return false;
			} else {
				global $mailerrors;
				$item['error'] = strip_tags($mailerrors);
				$this -> push_to_queue($item);
				return false;
			}
	
	        return $item;
	    }
		
		/**
		 * Handle cron healthcheck
		 *
		 * Restart the background process if not already running
		 * and data exists in the queue.
		 */
		public function handle_cron_healthcheck() {	
			if ( $this->is_process_running() ) {
				// Background process already running.
				echo esc_html_e('The process is already running') . '<br/>';
				return;
			}
			
			$queue_status = get_option('wpmlqueue_status');
			if (!empty($queue_status) && $queue_status == "pause") {
				echo esc_html_e('The queue is currently paused') . '<br/>';
				return;
			}

			if ( $this->is_queue_empty() ) {
				echo esc_html_e('The queue is currently empty') . '<br/>';
				$this -> clear_scheduled_event();
				return;
			}
			
			$this->handle();
		}
		
		/**
		 * Schedule cron healthcheck
		 *
		 * @access public
		 * @param mixed $schedules Schedules.
		 * @return mixed
		 */
		public function schedule_cron_healthcheck( $schedules ) {
			$interval = apply_filters($this -> identifier . '_cron_interval', 2);

			if ( property_exists( $this, 'cron_interval' ) ) {
				$interval = apply_filters($this -> identifier . '_cron_interval', $this -> cron_interval_identifier);
			}
			
			$scheduleinterval = get_option('wpmlscheduleinterval');
			
			if (!empty($schedules[$scheduleinterval])) {
				$schedules[$this -> identifier . '_cron_interval'] = $schedules[$scheduleinterval];
			} else {				
				// Adds every 2 minutes to the existing schedules.
				$schedules[$this -> identifier . '_cron_interval'] = array(
					'interval' => MINUTE_IN_SECONDS * $interval,
					'display'  => sprintf( __( 'Every %d Minutes' ), $interval ),
				);
			}

			return $schedules;
		}
		
		public function scheduling() {
			$this -> clear_scheduled_event();
			$this -> schedule_event();
		}
		
		public function clear_scheduled_event() {
			$timestamp = wp_next_scheduled( $this->cron_hook_identifier );

			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $this->cron_hook_identifier );
			}
		}
		
		protected function memory_exceeded() {
			// override the memory check for now
			return false;
		}

		/**
		 * Handle
		 *
		 * Pass each queue item to the task handler, while remaining
		 * within server memory and time limit constraints.
		 */
		protected function handle() {		
			$scheduleintervalseconds = get_option('wpmlscheduleintervalseconds');	
			$this -> queue_lock_time = (empty($scheduleintervalseconds)) ? (MINUTE_IN_SECONDS * 2) : $scheduleintervalseconds;
			$this -> lock_process();
			
			//$emailsperinterval = round((int) get_option('wpmlemailsperinterval') / 3);
            $emailsperinterval = (int) get_option('wpmlemailsperinterval');
			
			$e = 0;
			$successful = 0;
			
			do {
				$batch = $this->get_batch();

				foreach ( $batch->data as $key => $value ) {					
					$task = $this -> task($value);

					if ( false !== $task ) {
						$batch -> data[ $key ] = $task;
					} else {
						unset( $batch->data[ $key ] );
						$successful++;
					}
					
					// Update or delete current batch.
					if (!empty($batch -> data)) {
						$this -> update( $batch->key, $batch->data );
						// Comment out it was already updated above.
					} else {
						$this -> delete($batch->key);
					}
					
					$e++;

					if ( $this->time_exceeded() || $this->memory_exceeded() || $e >= $emailsperinterval) {
						// Batch limits reached.						
						break 2;
					}
				}
			} while ( (! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty()) && $e < $emailsperinterval);
			
			// save and dispatch any previous error messages in the queue
			$this -> save();
			$this -> reset_data();

			$this -> unlock_process();

			// Start next batch or complete process.
			if ( ! $this->is_queue_empty() ) {
				$this->dispatch();
			} else {
				$this->complete();
			}

			echo sprintf(__('%s emails have been sent out'), $successful) . '<br/>';
			return true;
		}
		
		/**
		 * Dispatch
		 *
		 * @access public
		 * @return void
		 */
		public function dispatch() {
			// Schedule the cron healthcheck.
			$this -> schedule_event();

			// Perform remote post.
			//return parent::dispatch();
		}
		
		/**
		 * Get memory limit
		 *
		 * @return int
		 */
		protected function get_memory_limit() {
			if ( function_exists( 'ini_get' ) ) {
				$memory_limit = ini_get( 'memory_limit' );
			} else {
				// Sensible default.
				$memory_limit = '1024M';
			}

			if (empty($memory_limit) || -1 == (int) $memory_limit ) {
				// Unlimited, set to 32GB.
				$memory_limit = '32000M';
			}

			return intval( $memory_limit ) * 1024 * 1024;
		}
	
	    /**
	     * Complete
	     *
	     * Override if applicable, but ensure that the below actions are
	     * performed, or, call parent::complete().
	     */
	    protected function complete() {
		    
		    global $wpMail;
		    $wpMail -> admin_notification_queue_complete();
		    
	        parent::complete();
	
	        // Show notice to user or perform some other arbitrary task...
	    }	
	    
	    public function debug($var = array()) {
		    echo '<pre>' . print_r($var, true) . '</pre>';
	    }
	}
}