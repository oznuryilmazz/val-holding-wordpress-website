<?php // phpcs:ignoreFile ?>
<!-- Email Scheduling Settings -->

<?php

$schedules = array(
					"1minutes" => array(
						"interval" => 60,
						"display" => "Every Minute"
					),
					"2minutes" => array(
						"interval" => 120,
						"display" => "Every 2 Minutes"
					),
					"5minutes" => array(
						"interval" => 300,
						"display" => "Every 5 Minutes"
					),
					"10minutes" => array(
						"interval" => 600,
						"display" => "Every 10 Minutes"
					),
					"20minutes" => array(
						"interval" => 1200,
						"display" => "Every 20 Minutes"
					),
					"30minutes" => array(
						"interval" => 1800,
						"display" => "Every 30 Minutes"
					),
					"40minutes" => array(
						"interval" => 2400,
						"display" => "Every 40 Minutes"
					),
					"50minutes" => array(
						"interval" => 3000,
						"display" => "Every 50 minutes"
					),
					"hourly" => array(
						"interval" => 3600,
						"display" => "Once Hourly"
					),
					"twicedaily" => array(
						"interval" => 43200,
						"display" => "Twice Daily"
					),
					"daily" => array(
						"interval" => 86400,
						"display" => "Once Daily"
					),
					"weekly" => array(
						"interval" => 604800,
						"display" => "Once Weekly"
					),
					"monthly" => array(
						"interval" => 2664000,
						"display" => "Once Monthly"
					)

				);	


// wp_get_schedules();
$scheduleinterval = $this -> get_option('scheduleinterval');
$emailsperinterval = $this -> get_option('emailsperinterval');
$notifyqueuecomplete = $this -> get_option('notifyqueuecomplete');
	
?>

<?php if (apply_filters('newsletters_whitelabel', true)) : ?>
	<p class="howto">
		<?php echo sprintf(__('Sending emails from the queue uses the WordPress cron job. If your queue is not moving, check if the WordPress cron is working and if %s is not set in your %s file. Also consider %s.', 'wp-mailinglist'), '<code>DISABLE_WP_CRON</code>', '<code>wp-config.php</code>', '<a href="https://tribulant.com/blog/wordpress/replace-wordpress-cron-with-real-cron-for-site-speed/" target="_blank">' . __('replacing the WordPress cron job with a real, server cron job', 'wp-mailinglist') . '</a>'); ?>
	</p>
<?php endif; ?>

<input type="hidden" name="croninterval" value="5minutes" />

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="scheduleinterval"><?php esc_html_e('Schedule Interval', 'wp-mailinglist'); ?></label></th>
			<td>
				<?php  //var_dump($schedules); ?>
				<select onchange="totalemails_calculate();" class="widefat" style="width:auto;" id="scheduleinterval" name="scheduleinterval">
                    <option data-interval="0" value=""><?php esc_html_e('- Select Interval -', 'wp-mailinglist'); ?></option>
                    <?php if (!empty($schedules)) : ?>
                        <?php foreach ($schedules as $key => $val) : 

							if (preg_match('/wp_|every_minute|\_|\-/', $key))  {
                                continue;
                            }

						?>
                        <?php $sel = ($scheduleinterval == $key) ? 'selected="selected"' : ''; ?>
                        <option data-interval="<?php echo esc_attr(wp_unslash($val['interval'])); ?>" <?php echo esc_html( $sel); ?> value="<?php echo esc_html($key) ?>"><?php echo esc_html( $val['display']); ?> (<?php echo esc_html( $val['interval']) ?> <?php esc_html_e('seconds', 'wp-mailinglist'); ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="howto"><?php esc_html_e('How often the queue runs to send emails out. Keep as low as possible, eg. Every 2 Minutes', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="emailsperinterval"><?php esc_html_e('Emails Per Interval', 'wp-mailinglist'); ?></label></th>
			<td>
				<input onkeyup="totalemails_calculate();" class="widefat" style="width:45px;" type="text" value="<?php echo esc_attr(wp_unslash($emailsperinterval)); ?>" id="emailsperinterval" name="emailsperinterval" />
				<span class="howto"><?php esc_html_e('The number of emails to send per schedule interval above, eg. 50.', 'wp-mailinglist'); ?>
			</td>
		</tr>
		<tr>
            <th><label for=""><?php esc_html_e('Total Emails', 'wp-mailinglist'); ?></label></th>
            <td>
	            <p id="totalemails">
		            <!-- total emails will display here -->
	            </p>
	            
	            <script type="text/javascript">
		        var totalemails_calculate = function() {
			        var emailsperinterval = jQuery('#emailsperinterval').val();
			        var scheduleinterval = jQuery('#scheduleinterval').find(':selected').data('interval');
			        
			        var totalemails_hourly = ((3600 / scheduleinterval) * emailsperinterval);
			        var totalemails_daily = (totalemails_hourly * 24);
			        
			        jQuery('#totalemails').html(totalemails_hourly + ' <?php esc_html_e('emails per hour', 'wp-mailinglist'); ?>, ' + totalemails_daily + ' <?php esc_html_e('emails per day', 'wp-mailinglist'); ?>');
		        }
		        
		        jQuery(document).ready(function() {
			        totalemails_calculate();
		        });
		        </script>
            </td>
        </tr>
		<tr>
			<th><label for="notifyqueuecomplete"><?php esc_html_e('Admin Notify on Complete', 'wp-mailinglist'); ?></label></th>
			<td>
				<label><input <?php echo (!empty($notifyqueuecomplete)) ? 'checked="checked"' : ''; ?> type="checkbox" name="notifyqueuecomplete" value="1" id="notifyqueuecomplete" /> <?php esc_html_e('Yes, send me an email when the queue has finished sending.', 'wp-mailinglist'); ?></label>
				<span class="howto"><?php esc_html_e('Turn this on to receive an email notification when the queue has finished sending.', 'wp-mailinglist'); ?></span>
			</td>
		</tr>
	</tbody>
</table>
