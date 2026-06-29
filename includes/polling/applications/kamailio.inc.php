<?php
/**
 * Observium kamailio application poller
 *
 * Install path:
 *   <observium_root>/includes/polling/applications/kamailio.inc.php
 *
 * Reads the <<<app-kamailio>>> section (key:value) and writes it to RRD.
 * Mirrors galera: discover_app() registers/returns the app row,
 * update_application() stores the metric set, rrdtool_update_ng() updates the
 * RRD by app name (needs $config['rrd_types']['kamailio'] in config.php).
 *
 * rcv_requests/rcv_replies/fwd_requests are cumulative counters → DERIVE.
 */

if (!empty($agent_data['app']['kamailio'])) {

    $app_id = discover_app($device, 'kamailio');

    $raw = array();
    foreach (explode("\n", $agent_data['app']['kamailio']) as $line) {
        if (strpos($line, ':') === false) { continue; }
        list($k, $v) = explode(':', $line, 2);
        $raw[trim($k)] = trim($v);
    }

    $keys = array(
        'registered', 'active_dialogs', 'tx_active',
        'rcv_requests', 'rcv_replies', 'fwd_requests',
        'shmem_used', 'shmem_max',
    );

    $fields = array();
    foreach ($keys as $k) {
        $fields[$k] = (isset($raw[$k]) && is_numeric($raw[$k])) ? $raw[$k] : 0;
    }

    update_application($app_id, $fields);

    rrdtool_update_ng($device, 'kamailio', $fields, $app_id);
}
