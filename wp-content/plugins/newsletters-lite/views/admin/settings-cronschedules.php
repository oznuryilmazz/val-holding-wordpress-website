<?php // phpcs:ignoreFile ?>
<!-- Schedule Tasks -->

<?php

$emailarchive = $this -> get_option('emailarchive');
$Db -> model = $Email -> model;
$emailscount = $Db -> count();
$bouncemethod = $this -> get_option('bouncemethod');
$importusers = $this -> get_option('importusers');
$saveipaddress = $this -> get_option('saveipaddress');

?>

<div class="wrap newsletters <?php echo esc_html($this -> pre); ?>">
	<h2><?php esc_html_e('Scheduled Tasks', 'wp-mailinglist'); ?> <?php echo ( $Html -> link(__('Refresh', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks, array('class' => "add-new-h2"))); ?></h2>
	
	<?php $this -> render('settings-navigation', array('tableofcontents' => false), true, 'admin'); ?>
    
    <p>
		<?php esc_html_e('These are scheduled tasks which are automatically run using the WordPress cron.', 'wp-mailinglist'); ?><br/>
        <?php esc_html_e('The current time is:', 'wp-mailinglist'); ?> <strong><?php echo esc_html( $Html -> gen_date(false, false, false, true)); ?></strong>
    </p>
    
    <table class="widefat">
    	<thead>
        	<tr>
            	<th><?php esc_html_e('Schedule Task', 'wp-mailinglist'); ?></th>
                <th><?php esc_html_e('Next Scheduled Run', 'wp-mailinglist'); ?></th>
            </tr>
        </thead>
        <tfoot>
        	<tr>
            	<th><?php esc_html_e('Schedule Task', 'wp-mailinglist'); ?></th>
                <th><?php esc_html_e('Next Scheduled Run', 'wp-mailinglist'); ?></th>
            </tr>
        </tfoot>
    	<tbody>
	    	<!-- Queue Process -->
        	<tr>
            	<th>
					<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> queue); ?>"><?php esc_html_e('Email Queue', 'wp-mailinglist'); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=wp_queue_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=wp_queue_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=wp_queue_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>                
                	<?php echo wp_kses_post( $Html -> next_scheduled('wp_queue_process_cron')); ?>
                </td>
            </tr>
            <!-- Import Process -->
        	<tr>
            	<th>
					<a class="row-title" href="<?php echo esc_url_raw( admin_url('admin.php?page=' . $this -> sections -> importexport)) ?>"><?php esc_html_e('Import', 'wp-mailinglist'); ?></a>
					<?php if ($import_count = $this -> import_process -> get_import_count()) : ?>
						<?php if (!empty($import_count)) : ?>
							<small>(<?php echo sprintf(__('%s importing in the background', 'wp-mailinglist'), $import_count); ?>)</small>
						<?php endif; ?>
					<?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=wp_import_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=wp_import_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=wp_import_process_cron', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>                
                	<?php echo wp_kses_post( $Html -> next_scheduled('wp_import_process_cron')); ?>
                </td>
            </tr>
        	<!-- Scheduled and Recurring Newsletters -->
        	<tr>
            	<th>
					<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> history); ?>"><?php esc_html_e('Scheduled and Recurring Newsletters', 'wp-mailinglist'); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=cronhook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>                
                	<?php echo wp_kses_post( $Html -> next_scheduled('cronhook')); ?>
                </td>
            </tr>
            <!-- POP/IMAP Scheduling = "wpml_pophook" -->
            <tr class="alternate">
            	<th>
					<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> settings); ?>#bouncediv"><?php esc_html_e('POP/IMAP Bounce Check', 'wp-mailinglist'); ?></a>
					<?php if ($bouncemethod == "pop") : ?>
						<?php $pop_status = $this -> get_pop_status(); ?>
						<small>(<?php echo esc_html( $pop_status); ?>)</small>
					<?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=pophook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>
                	<?php if ($bouncemethod == "pop") : ?>
                		<?php echo wp_kses_post( $Html -> next_scheduled('pophook')); ?>
                    <?php else : ?>
                    	<?php esc_html_e('POP/IMAP bounce handling is turned OFF.', 'wp-mailinglist'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <!-- Autoresponder emails = "wpml_autoresponders" -->
            <tr class="alternate">
            	<th>
                	<?php
					
					$Db -> model = $this -> Autoresponderemail() -> model;
					$autoresponderemailcount = $Db -> count(array('status' => "unsent"));
					
					?>
                
					<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> autoresponderemails); ?>"><?php esc_html_e('Autoresponder Emails', 'wp-mailinglist'); ?></a>
                    <?php if (!empty($autoresponderemailcount)) : ?><small>(<?php echo esc_html( $autoresponderemailcount); ?> <?php esc_html_e('future autoresponder emails waiting', 'wp-mailinglist'); ?>)</small><?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=autoresponders', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo wp_kses_post( $Html -> next_scheduled('autoresponders')); ?>
                </td>
            </tr>
            <!-- Import WordPress Users -->
            <tr>
            	<th>
                	<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> settings_system); ?>#autoimportusersdiv"><?php esc_html_e('Auto Import WordPress Users', 'wp-mailinglist'); ?></a>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=importusers', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>
	                <?php if (!empty($importusers) && $importusers == "Y") : ?>
                		<?php echo wp_kses_post( $Html -> next_scheduled('importusers')); ?>
                	<?php else : ?>
                		<?php esc_html_e('This feature is turned off', 'wp-mailinglist'); ?>
                	<?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($emailarchive)) : ?>
            	<tr>
            		<th>
                	<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> settings); ?>#emailarchive"><?php esc_html_e('Email Archiving', 'wp-mailinglist'); ?></a>
                	<?php if (!empty($emailscount)) : ?>
                		<small>(<?php echo sprintf(__('%s emails sent', 'wp-mailinglist'), $emailscount); ?>)</small>
                	<?php endif; ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=newsletters_emailarchivehook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=newsletters_emailarchivehook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=newsletters_emailarchivehook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo wp_kses_post( $Html -> next_scheduled('newsletters_emailarchivehook')); ?>
                </td>
            	</tr>
            <?php endif; ?>
            <?php $activateaction = $this -> get_option('activateaction'); ?>
            <?php if (!empty($activateaction) && $activateaction != "none") : ?>
	            <!-- Confirmation/Activation Reminders/Deletion -->
	            <tr>
	            	<th>
	                	<a class="row-title" href="?page=<?php echo esc_html( $this -> sections -> settings_subscribers); ?>#subscribersdiv"><?php esc_html_e('Inactive Subscriptions Action', 'wp-mailinglist'); ?></a>
	                    <div class="row-actions">
	                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=activateaction', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
	                    </div>
	                </th>
	                <td>
	                	<?php echo wp_kses_post( $Html -> next_scheduled('activateaction')); ?>
	                </td>
	            </tr>
	        <?php endif; ?>
	        <tr>
            	<th>
                	<?php esc_html_e('Optimize Database', 'wp-mailinglist'); ?>
                    <div class="row-actions">
                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=newsletters_optimizehook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=newsletters_optimizehook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=newsletters_optimizehook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
                    </div>
                </th>
                <td>
                	<?php echo wp_kses_post( $Html -> next_scheduled('newsletters_optimizehook')); ?>
                </td>
            </tr>
            <?php if (!empty($saveipaddress)) : ?>
	            <tr>
	            	<th>
	                	<?php esc_html_e('Get Countries by IP Address', 'wp-mailinglist'); ?>
	                    <div class="row-actions">
	                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=newsletters_countrieshook', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=newsletters_countrieshook', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=newsletters_countrieshook', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
	                    </div>
	                </th>
	                <td>
	                	<?php echo wp_kses_post( $Html -> next_scheduled('newsletters_countrieshook')); ?>
	                </td>
	            </tr>
	        <?php endif; ?>
	        <?php if ($this -> is_plugin_active('captcha')) : ?>
	        	<tr>
	        		<th>                
						<?php esc_html_e('Really Simple Captcha cleanup', 'wp-mailinglist'); ?>
	                    <div class="row-actions">
	                    	<span class="edit"><?php echo ( $Html -> link(__('Run Now', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=runschedule&hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you want to execute this task right now? It may take a while to execute, please do not refresh or close this window.', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="edit"><?php echo ( $Html -> link(__('Reschedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=reschedule&hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you want to reset this schedule?', 'wp-mailinglist') . "')) { return false; }"))); ?> |</span>
	                        <span class="delete"><?php echo ( $Html -> link(__('Stop Schedule', 'wp-mailinglist'), '?page=' . $this -> sections -> settings_tasks . '&method=clearschedule&hook=captchacleanup', array('onclick' => "if (!confirm('" . __('Are you sure you wish to clear this scheduled task?', 'wp-mailinglist') . "')) { return false; }", 'class' => "submitdelete"))); ?></span>
	                    </div>
	                </th>
	                <td>
	                	<?php echo wp_kses_post( $Html -> next_scheduled('captchacleanup')); ?>
	                </td>
	        	</tr>
	        <?php endif; ?>
	        <?php do_action('wpml_cronschedules'); ?>
        </tbody>
    </table>
</div>