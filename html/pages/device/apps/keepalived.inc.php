<?php
/**
 * Observium application PAGE (frontend) — keepalived.
 *
 * Deploy to:  html/pages/device/apps/keepalived.inc.php
 *
 * Renders the keepalived app graphs on the device's Apps tab. Each graph type
 * maps to html/includes/graphs/application/<type>.inc.php via the
 * `application_<type>` graph name.
 */

$graphs = [
    'keepalived_state'   => 'VRRP Instance State',
    'keepalived_adverts' => 'VRRP Advertisements',
];

foreach ($graphs as $type => $descr) {
    $graph_array = [
        'type'   => 'application_' . $type,
        'device' => $device['device_id'],
        'id'     => $app['app_id'],
    ];

    print_graph_row($graph_array, $descr);
}
