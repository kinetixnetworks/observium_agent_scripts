<?php
/**
 * Observium application GRAPH — FreeSWITCH active calls/channels/sessions.
 * Deploy to:  html/includes/graphs/application/freeswitch_calls.inc.php
 */

$rrd_filename = rrd_name($device['hostname'], ['app', 'freeswitch', $app['app_id']]);

$rrd_list = [
    ['filename' => $rrd_filename, 'descr' => 'Calls',    'ds' => 'calls',    'colour' => '4f9fdf'],
    ['filename' => $rrd_filename, 'descr' => 'Channels', 'ds' => 'channels', 'colour' => 'cf4f4f'],
    ['filename' => $rrd_filename, 'descr' => 'Sessions', 'ds' => 'sessions', 'colour' => '4fbf4f'],
];

$unit_text = 'Count';
$nototal   = TRUE;

include $config['html_dir'] . '/includes/graphs/generic_multi_line.inc.php';
