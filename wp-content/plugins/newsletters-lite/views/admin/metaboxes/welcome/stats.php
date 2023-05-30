<?php // phpcs:ignoreFile ?>
<!-- Overview Chart -->

<?php

$user_chart_type = $this -> get_user_option(false, 'chart_type');
$chart_type = (empty($user_chart_type)) ? 'days' : esc_html($user_chart_type);

$fromdate = (empty($_GET['from'])) ? $Html -> gen_date("Y-m-d", strtotime("-13 days")) :  sanitize_text_field(wp_unslash($_GET['from']));
$todate = (empty($_GET['to'])) ? $Html -> gen_date("Y-m-d") :  sanitize_text_field(wp_unslash($_GET['to']));

$user_chart = $this -> get_user_option(false, 'chart');
$chart = (empty($user_chart)) ? 'bar' : esc_html($user_chart);

?>

<div class="alignleft actions">
	<p>
		<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=years')); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "years") ? 'active' : ''; ?>"><?php esc_html_e('Years', 'wp-mailinglist'); ?></a>
		<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=months')); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "months") ? 'active' : ''; ?>"><?php esc_html_e('Months', 'wp-mailinglist'); ?></a>
		<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart_type&value=days')); ?>" class="button <?php echo (!empty($chart_type) && $chart_type == "days") ? 'active' : ''; ?>"><?php esc_html_e('Days', 'wp-mailinglist'); ?></a>
		<?php echo ( $Html -> help(__('Display the chart with stats below by days, months or years according to your needs. The default is days.', 'wp-mailinglist'))); ?>
		
		<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart&value=bar')); ?>" class="button <?php echo (empty($chart) || $chart == "bar") ? 'active' : ''; ?>"><i class="fa fa-bar-chart"></i></a>
		<a href="<?php echo esc_url_raw($Html -> retainquery('newsletters_method=set_user_option&option=chart&value=line')); ?>" class="button <?php echo (!empty($chart) && $chart == "line") ? 'active' : ''; ?>"><i class="fa fa-line-chart"></i></a>
		<?php echo ( $Html -> help(__('Switch between bar and line charts.', 'wp-mailinglist'))); ?>
	</p>
</div>
<div class="alignright actions">
	<p>
		<form action="" method="get">
			<input type="hidden" name="chart" value="<?php echo esc_html($chart); ?>" />
			<input type="hidden" name="chart_type" value="<?php echo esc_html($chart_type); ?>" />
			<input type="hidden" name="page" value="<?php echo esc_html($this -> sections -> welcome); ?>" />
			<label for="fromdate"><i class="fa fa-calendar"></i></label>
			<input style="width:100px;" type="text" name="from" value="<?php echo esc_html($fromdate); ?>" id="fromdate" />
			<?php esc_html_e('to', 'wp-mailinglist'); ?>
			<input style="width:100px;" type="text" name="to" value="<?php echo esc_html($todate); ?>" id="todate" />
			<input class="button button-primary" type="submit" name="changedate" value="<?php esc_html_e('Change', 'wp-mailinglist'); ?>" />
			<?php echo wp_kses_post($Html -> help(__('By default, the chart will show stats for the last 30 days, including today. Use the two date inputs to choose a from and to date to create a range.', 'wp-mailinglist'))); ?>
		</form>
	</p>
</div>

<canvas id="canvas" style="width:100%; height:300px;"></canvas>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#fromdate').datepicker({showButtonPanel:true, numberOfMonths:2, changeMonth:true, changeYear:true, defaultDate:"<?php echo esc_html($fromdate); ?>", dateFormat:"yy-mm-dd"});
	jQuery('#todate').datepicker({showButtonPanel:true, numberOfMonths:2, changeMonth:true, changeYear:true, defaultDate:"<?php echo esc_html($todate); ?>", dateFormat:"yy-mm-dd"});
	
	var ajaxdata = {type:'<?php echo esc_html($chart_type); ?>', chart:'<?php echo esc_html($chart); ?>', from:'<?php echo esc_html($fromdate); ?>', to:'<?php echo esc_html($todate); ?>'};
	
	jQuery.getJSON(newsletters_ajaxurl + 'action=wpmlwelcomestats&security=<?php echo esc_html(wp_create_nonce('welcomestats')); ?>', ajaxdata, function(json) {
		
		helpers = Chart.helpers;
		var chartdata = json;
		var ctx = document.getElementById("canvas").getContext("2d");
		
		var chart = new Chart(ctx, {
			type: '<?php echo esc_js((empty($chart) || $chart == "bar") ? 'bar' : 'line'); ?>',
			data: chartdata,
			options: {
				 tooltips: {
					 mode: 'index'
				 }
			}
		});
	});
});
</script>