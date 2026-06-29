<?php
/**
 * Observium application GRAPH — keepalived VRRP instance state.
 * Deploy to:  html/includes/graphs/application/keepalived_state.inc.php
 */

$rrd_filename = rrd_name($device['hostname'], ['app', 'keepalived', $app['app_id']]);

$rrd_list = [
    ['filename' => $rrd_filename, 'descr' => 'Master',    'ds' => 'master',    'colour' => '4fbf4f'],
    ['filename' => $rrd_filename, 'descr' => 'Backup',    'ds' => 'backup',    'colour' => '4f9fdf'],
    ['filename' => $rrd_filename, 'descr' => 'Fault',     'ds' => 'fault',     'colour' => 'cf4f4f'],
    ['filename' => $rrd_filename, 'descr' => 'Instances', 'ds' => 'instances', 'colour' => '999999'],
];

$unit_text = 'Instances';
$nototal   = TRUE;

include $config['html_dir'] . '/includes/graphs/generic_multi_line.inc.php';
