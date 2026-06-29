<?php
/**
 * Observium application GRAPH — FreeSWITCH sessions vs peak vs max.
 * Deploy to:  html/includes/graphs/application/freeswitch_sessions.inc.php
 */

$rrd_filename = rrd_name($device['hostname'], ['app', 'freeswitch', $app['app_id']]);

$rrd_list = [
    ['filename' => $rrd_filename, 'descr' => 'Sessions',      'ds' => 'sessions',  'colour' => '4fbf4f'],
    ['filename' => $rrd_filename, 'descr' => 'Sessions peak', 'ds' => 'sess_peak', 'colour' => 'df8f3f'],
    ['filename' => $rrd_filename, 'descr' => 'Sessions max',  'ds' => 'sess_max',  'colour' => 'cf4f4f'],
];

$unit_text = 'Sessions';
$nototal   = TRUE;

include $config['html_dir'] . '/includes/graphs/generic_multi_line.inc.php';
