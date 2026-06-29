<?php
/**
 * Observium application PAGE (frontend) — FreeSWITCH.
 *
 * Deploy to:  html/pages/device/apps/freeswitch.inc.php
 *
 * Renders the FreeSWITCH app graphs on the device's Apps tab. Each graph type
 * maps to html/includes/graphs/application/<type>.inc.php via the
 * `application_<type>` graph name.
 */

$graphs = [
    'freeswitch_calls'    => 'Calls, Channels & Sessions',
    'freeswitch_sessions' => 'Sessions vs Peak / Max',
];

foreach ($graphs as $type => $descr) {
    $graph_array = [
        'type'   => 'application_' . $type,
        'device' => $device['device_id'],
        'id'     => $app['app_id'],
    ];

    print_graph_row($graph_array, $descr);
}
