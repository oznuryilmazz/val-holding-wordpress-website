<?php
defined('ABSPATH') || exit;
?>

<div style="height: 300px;">
    <canvas id="tnp-users-chart-days"></canvas>
</div>

<div style="height: 300px;">
    <canvas id="tnp-users-chart-months"></canvas>
</div>

<div class="row">
    <div class="col-md-6">
        
        <?php
        $dt = new DateTime();
        $dt->setTime(12, 0, 0)->setDate((int) date('Y'), (int) date('m'), 1);
        $i = new DateInterval('P1M');
        $months = [];
        for ($x = 1; $x <= 24; $x++) {
            $months[] = $dt->format('Y-m');
            $dt->sub($i);
        }
        $months = array_reverse($months);
        $list = $wpdb->get_results("select  concat(year(created), '-', date_format(created, '%m')) as d, count(*) as c from " . NEWSLETTER_USERS_TABLE . " where status='C' group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 24", OBJECT_K);

        $dataMonths = [];
        foreach ($months as $month) {
            $dataMonths[] = isset($list[$month]) ? $list[$month]->c : 0;
        }
        ?>

        <?php /*
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Year and month', 'newsletter') ?></th>
                    <th><?php _e('Total', 'newsletter') ?></th>
                </tr>
            </thead>
            <?php foreach ($months as $month) { ?>
                <tr>
                    <td><?php echo $month; ?></td>
                    <td><?php echo isset($list[$month]) ? $list[$month]->c : 0; ?></td>
                </tr>
            <?php } ?>
        </table>
        */?>
    </div>

    <div class="col-md-6">

       
        <?php
        $dt = new DateTime();
        $dt->setTime(12, 0, 0);
        $i = new DateInterval('P1D');
        $days = [];
        for ($x = 1; $x <= 90; $x++) {
            $days[] = $dt->format('Y-m-d');
            $dt->sub($i);
        }
        $days = array_reverse($days);

        $list = $wpdb->get_results("select date(created) as d, count(*) as c from " . NEWSLETTER_USERS_TABLE . " where status='C' group by date(created) order by d desc limit 90", OBJECT_K);

        $dataDays = [];
        foreach ($days as $day) {
            $dataDays[] = isset($list[$day]) ? $list[$day]->c : 0;
        }
        ?>
        <?php /*
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php _e('Date', 'newsletter') ?></th>
                    <th><?php _e('Total', 'newsletter') ?></th>
                </tr>
            </thead>
            <?php for ($i = 0; $i < count($days); $i++) { ?>
                <tr>
                    <td><?php echo $days[$i]; ?></td>
                    <td><?php echo $dataDays[$i] ?></td>
                </tr>
            <?php } ?>
        </table>
*/?>
    </div>

</div>

<script>
    const dataDays = {
        labels: <?php echo json_encode($days) ?>,
        datasets: [{
                label: 'By day',
                borderColor: '#3498DB',
                borderWidth: 1,
                data: <?php echo json_encode($dataDays) ?>,
            }]
    };


    const dataMonths = {
        labels: <?php echo json_encode($months) ?>,
        datasets: [{
                label: 'By month',
                borderColor: '#3498DB',
                borderWidth: 1,
                data: <?php echo json_encode($dataMonths) ?>,
            }]
    };

    jQuery(function () {
        const myChart = new Chart(
                document.getElementById('tnp-users-chart-days'),
                {
                    type: 'line',
                    data: dataDays,
                   options: {
                       maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                        }
                    }
                }
        );
        const myChartMonths = new Chart(
                document.getElementById('tnp-users-chart-months'),
                {
                    type: 'line',
                    data: dataMonths,
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                        }
                    }
                }
        );
    });
</script>

