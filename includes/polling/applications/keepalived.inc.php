<?php
/**
 * Observium application POLLER module — keepalived (VRRP).
 *
 * Deploy to:  includes/polling/applications/keepalived.inc.php
 *
 * Consumes the `<<<app-keepalived>>>` UNIX-agent block (one `key:value` line
 * per metric) emitted by the kinetix observium_agent_scripts/keepalived script
 * and stores it into RRD + application_metrics.
 */

$keepalived = $agent_data['app']['keepalived'];

// Parse "key:value" lines into an associative array.
$ka = [];
foreach (explode("\n", $keepalived) as $line) {
    if (strpos($line, ':') === false) {
        continue;
    }
    [$key, $value] = explode(':', $line, 2);
    $ka[trim($key)] = trim($value);
}

// metric => DS type. State/counts are GAUGE; the keepalived counters (which
// reset to 0 when the daemon restarts) are DERIVE with a 0 floor so a restart
// reads as "no change" rather than a negative spike. All names are <=19 chars.
$map = [
    'running'       => 'GAUGE',
    'instances'     => 'GAUGE',
    'master'        => 'GAUGE',
    'backup'        => 'GAUGE',
    'fault'         => 'GAUGE',
    'became_master' => 'DERIVE',
    'adv_sent'      => 'DERIVE',
    'adv_received'  => 'DERIVE',
];

$rrd_def = [];
$fields  = [];
$metrics = [];
foreach ($map as $metric => $type) {
    $value = isset($ka[$metric]) && is_numeric($ka[$metric]) ? $ka[$metric] : 'U';
    $min   = $type === 'DERIVE' ? '0' : 'U';
    $rrd_def[]        = "DS:$metric:$type:600:$min:U";
    $fields[$metric]  = $value;
    $metrics[$metric] = $value;
}

$rrd_filename = 'app-keepalived-' . $app['app_id'] . '.rrd';
rrdtool_update($device, $rrd_filename, $fields, $rrd_def);

update_application($app, $keepalived, $metrics);

unset($keepalived, $ka, $map, $rrd_def, $fields, $metrics, $rrd_filename);
