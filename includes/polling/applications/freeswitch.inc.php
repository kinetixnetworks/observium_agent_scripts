<?php
/**
 * Observium application POLLER module — FreeSWITCH.
 *
 * Deploy to:  includes/polling/applications/freeswitch.inc.php
 *
 * Consumes the `<<<app-freeswitch>>>` UNIX-agent block (one `key:value` line
 * per metric) emitted by the kinetix observium_agent_scripts/freeswitch script
 * and stores it into RRD + application_metrics.
 *
 * RRD DS names are capped at 19 chars by rrdtool, so the longer agent keys are
 * mapped to short DS names below; application_metrics keep the full key.
 */

// Raw agent payload Observium collected for this app on this device.
$freeswitch = $agent_data['app']['freeswitch'];

// Parse "key:value" lines into an associative array.
$fs = [];
foreach (explode("\n", $freeswitch) as $line) {
    if (strpos($line, ':') === false) {
        continue;
    }
    [$key, $value] = explode(':', $line, 2);
    $fs[trim($key)] = trim($value);
}

// agent key => [ rrd DS name (<=19 chars), DS type ]
// Instantaneous values are GAUGE; the cumulative "since startup" total is
// DERIVE so a FreeSWITCH restart (counter reset) doesn't spike the graph.
$map = [
    'calls'                  => ['calls',          'GAUGE'],
    'channels'               => ['channels',       'GAUGE'],
    'sessions'               => ['sessions',       'GAUGE'],
    'sessions_peak'          => ['sess_peak',      'GAUGE'],
    'sessions_persec'        => ['sess_persec',    'GAUGE'],
    'sessions_persec_peak'   => ['sess_persec_pk', 'GAUGE'],
    'sessions_max'           => ['sess_max',       'GAUGE'],
    'sessions_since_startup' => ['sess_total',     'DERIVE'],
];

$rrd_def = [];
$fields  = [];
$metrics = [];
foreach ($map as $key => [$ds, $type]) {
    $value = isset($fs[$key]) && is_numeric($fs[$key]) ? $fs[$key] : 'U';
    $min   = $type === 'DERIVE' ? '0' : 'U';
    $rrd_def[]      = "DS:$ds:$type:600:$min:U";
    $fields[$ds]    = $value;
    $metrics[$key]  = $value;   // alerting / auto-discovery use the full name
}

$rrd_filename = 'app-freeswitch-' . $app['app_id'] . '.rrd';
rrdtool_update($device, $rrd_filename, $fields, $rrd_def);

update_application($app, $freeswitch, $metrics);

unset($freeswitch, $fs, $map, $rrd_def, $fields, $metrics, $rrd_filename);
