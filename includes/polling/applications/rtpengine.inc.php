<?php
/**
 * Observium rtpengine application poller
 *
 * Install path:
 *   <observium_root>/includes/polling/applications/rtpengine.inc.php
 *
 * Reads the <<<app-rtpengine>>> section (key:value) and writes it to RRD.
 * Mirrors galera: discover_app() registers/returns the app row,
 * update_application() stores the metric set, rrdtool_update_ng() updates the
 * RRD by app name (needs $config['rrd_types']['rtpengine'] in config.php).
 *
 * relayed_* are cumulative counters → DERIVE in the rrd_type definition.
 */

if (!empty($agent_data['app']['rtpengine'])) {

    $app_id = discover_app($device, 'rtpengine');

    $raw = array();
    foreach (explode("\n", $agent_data['app']['rtpengine']) as $line) {
        if (strpos($line, ':') === false) { continue; }
        list($k, $v) = explode(':', $line, 2);
        $raw[trim($k)] = trim($v);
    }

    $keys = array(
        'sessions', 'sessions_own', 'sessions_foreign', 'transcoded',
        'relayed_packets', 'relayed_bytes', 'relayed_errors',
    );

    $fields = array();
    foreach ($keys as $k) {
        $fields[$k] = (isset($raw[$k]) && is_numeric($raw[$k])) ? $raw[$k] : 0;
    }

    update_application($app_id, $fields);

    rrdtool_update_ng($device, 'rtpengine', $fields, $app_id);
}
