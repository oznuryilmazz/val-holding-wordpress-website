<?php

if (!class_exists('WP_Import_Process')) {
	class WP_Import_Process extends WP_Background_Process {
	
	    /**
	     * @var string
	     */
	    protected $action = 'import_process';
	    
	    var $data = array();
	    
	    var $counter_reset = 100;
	    
	    public function __construct() {
			parent::__construct();
		}
		
		public function get_batch() {
			return parent::get_batch();
		}
		
		public function queued_items() {
			if ($this -> is_queue_empty()) {
				return false;
			}
			
			return true;
		}
		
		public function get_data() {
			return $this -> data;
		}
	    
	    public function reset_data() {
		    $this -> data = array();
	    }
	    
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
	    
	    function get_import_count($key = null) {
		    global $wpdb, $wpMail;
		    
		    if ($import_count = get_transient('newsletters_import_count')) {
			    return $import_count;
		    }
		    
		    $count = 0;

			$table        = $wpdb->options;
			$column       = 'option_name';
			$key_column   = 'option_id';
			$value_column = 'option_value';

			$key = $this -> identifier . '_batch_%';

			$query = $wpdb -> prepare( "
			SELECT {$value_column}
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC", $key );

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
		    
		    set_transient('newsletters_import_count', $count, (2 * MINUTE_IN_SECONDS));
		    return $count;
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
	        define('NEWSLETTERS_IMPORTING', true);
	        global $wpMail, $Db, $Subscriber;
	        
	        $wpMail -> log_error($item);
	        
	        if (!empty($item)) {
		        $subscriber = $item['subscriber'];
		        $skipsubscriberupdate = (empty($item['options']['skipsubscriberupdate'])) ? false : true;
		        $confirmation = (!empty($item['options']['confirmation'])) ? true : false;
		        
		        if ($Subscriber -> save($subscriber, true, false, $skipsubscriberupdate)) {
					if (!empty($confirmation)) {
						$confirmation_subject = $item['options']['confirmation']['confirmation_subject'];
						$confirmation_email = $item['options']['confirmation']['confirmation_email'];
						
						$Db -> model = $Subscriber -> model;
						$sub = $Db -> find(array('id' => $Subscriber -> insertid));
	
						foreach ($subscriber['mailinglists'] as $list_id) {
							$subject = $confirmation_subject;
							$message = $confirmation_email;
							
							$queue_process_data = array(
								'subscriber_id'				=>	$sub -> id,
								'subject'					=>	$subject,
								'message'					=>	$message,
								'attachments'				=>	false,
								'post_id'					=>	false,
								'history_id'				=>	false,
								'theme_id'					=>	$wpMail -> default_theme_id('system'),
								'senddate'					=>	false,
							);
							
							$wpMail -> queue_process -> reset_data();
							$wpMail -> queue_process -> push_to_queue($queue_process_data);
							$wpMail -> queue_process -> save();
							$wpMail -> queue_process -> dispatch();
						}
					}
				} else {
					$wpMail -> log_error(sprintf(__('Subscriber (%s) could not be imported: %s', $wpMail -> plugin_name), $subscriber['email'], implode(", ", $Subscriber -> errors)));
				}
			}
	        
	        return false;
	    }
	    
	    protected function memory_exceeded() {
			// override the memory check for now
			return false;
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
				echo esc_html_e('The process is already running');
				exit;
			}

			if ($this->is_queue_empty()) {
				echo esc_html_e('The imports is currently empty');
				$this->clear_scheduled_event();
				exit;
			}
			
			$this -> handle();
		}
	    
	    /**
		 * Handle
		 *
		 * Pass each queue item to the task handler, while remaining
		 * within server memory and time limit constraints.
		 */
		protected function handle() {
			$this->lock_process();
			
			$imported = 0;

			do {
				$batch = $this->get_batch();

				foreach ( $batch->data as $key => $value ) {
					$task = $this->task( $value );

					if ( false !== $task ) {
						$batch->data[ $key ] = $task;
					} else {
						unset( $batch->data[ $key ] );
						$imported++;
					}

					if ( $this->time_exceeded() || $this->memory_exceeded() ) {
						// Batch limits reached.
						break;
					}
				}

				// Update or delete current batch.
				if ( ! empty( $batch->data ) ) {
					$this->update( $batch->key, $batch->data );
				} else {
					$this->delete( $batch->key );
				}
			} while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

			$this->unlock_process();

			// Start next batch or complete process.
			if (!$this->is_queue_empty() ) {
				$this->dispatch();
			} else {
				$this->complete();
			}

			echo sprintf(__('%s subscribers were imported'), $imported);
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
	
	    /**
	     * Complete
	     *
	     * Override if applicable, but ensure that the below actions are
	     * performed, or, call parent::complete().
	     */
	    protected function complete() {
		    
		    global $wpMail;
		    $wpMail -> admin_notification_import_complete();		    
	        parent::complete();
	
	        // Show notice to user or perform some other arbitrary task...
	    }
	
		public function debug($var = array()) {
		    echo '<pre>' . print_r($var, true) . '</pre>';
	    }
	}
}