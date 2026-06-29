<?php
/**
 * Observium application GRAPH — keepalived VRRP advertisement rate.
 * Deploy to:  html/includes/graphs/application/keepalived_adverts.inc.php
 *
 * adv_sent / adv_received are DERIVE DS, so the graph shows adverts per second.
 */

$rrd_filename = rrd_name($device['hostname'], ['app', 'keepalived', $app['app_id']]);

$rrd_list = [
    ['filename' => $rrd_filename, 'descr' => 'Sent',     'ds' => 'adv_sent',     'colour' => '4f9fdf'],
    ['filename' => $rrd_filename, 'descr' => 'Received', 'ds' => 'adv_received', 'colour' => '4fbf4f'],
];

$unit_text = 'Adverts/sec';
$nototal   = TRUE;

include $config['html_dir'] . '/includes/graphs/generic_multi_line.inc.php';
