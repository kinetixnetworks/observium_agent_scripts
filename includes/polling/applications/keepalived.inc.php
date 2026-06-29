<?php
/**
 * Observium keepalived (VRRP) application poller
 *
 * Install path:
 *   <observium_root>/includes/polling/applications/keepalived.inc.php
 *
 * Reads the <<<app-keepalived>>> section emitted by the agent script (one
 * `key:value` line per metric) and writes it to RRD. Mirrors the galera module:
 * discover_app() creates/returns the app row, update_application() stores the
 * metric set, rrdtool_update_ng() updates the RRD by app name.
 *
 * `became_master`, `adv_sent`, `adv_received` are cumulative keepalived counters
 * — rrdtool_update_ng stores them as DERIVE (as galera's cert_failures/bf_aborts
 * are), so the adverts graph reads as a per-second rate.
 */

if (!empty($agent_data['app']['keepalived'])) {

    $app_id = discover_app($device, 'keepalived');

    // Parse "key:value" lines from the agent into an assoc array.
    $raw = array();
    foreach (explode("\n", $agent_data['app']['keepalived']) as $line) {
        if (strpos($line, ':') === false) { continue; }
        list($k, $v) = explode(':', $line, 2);
        $raw[trim($k)] = trim($v);
    }

    // All keepalived metric names are <=19 chars, so they map straight to DS.
    $keys = array('running', 'instances', 'master', 'backup', 'fault', 'became_master', 'adv_sent', 'adv_received');

    $fields = array();
    foreach ($keys as $k) {
        $fields[$k] = (isset($raw[$k]) && is_numeric($raw[$k])) ? $raw[$k] : 0;
    }

    update_application($app_id, $fields);

    rrdtool_update_ng($device, 'keepalived', $fields, $app_id);
}
