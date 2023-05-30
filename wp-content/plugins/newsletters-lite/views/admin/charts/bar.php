<div id="<?php echo esc_html($id); ?>-legend" class="newsletters-chart-legend"></div>

<div class="newsletters-bar-chart-holder">
	<canvas id="<?php echo esc_html($id); ?>" style="width:<?php echo esc_html( $attributes['width']); ?>; height:<?php echo esc_html( $attributes['height']); ?>"></canvas>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	var data = <?php echo wp_json_encode($data); ?>;
	var options = <?php echo wp_json_encode($options); ?>;
	var ctx = document.getElementById('<?php echo esc_html($id); ?>').getContext("2d");
	
	var chart = new Chart(ctx, {
		type: 'bar',
		data: data,
		options: options
	});
});
</script>