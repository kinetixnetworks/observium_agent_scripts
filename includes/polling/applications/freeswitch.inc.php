<?php
/**
 * Observium FreeSWITCH application poller
 *
 * Install path:
 *   <observium_root>/includes/polling/applications/freeswitch.inc.php
 *
 * Reads the <<<app-freeswitch>>> section emitted by the agent script (one
 * `key:value` line per metric) and writes it to RRD. Mirrors the galera module:
 * discover_app() creates/returns the app row (this is what makes Observium
 * discover the app), update_application() stores the metric set, and
 * rrdtool_update_ng() updates the RRD by app name.
 *
 * RRD DS names are capped at 19 chars by rrdtool, so the longer agent keys are
 * mapped to short DS names below.
 */

if (!empty($agent_data['app']['freeswitch'])) {

    $app_id = discover_app($device, 'freeswitch');

    // Parse "key:value" lines from the agent into an assoc array.
    $raw = array();
    foreach (explode("\n", $agent_data['app']['freeswitch']) as $line) {
        if (strpos($line, ':') === false) { continue; }
        list($k, $v) = explode(':', $line, 2);
        $raw[trim($k)] = trim($v);
    }

    // agent key => RRD DS name (<=19 chars)
    $map = array(
        'calls'                  => 'calls',
        'channels'               => 'channels',
        'sessions'               => 'sessions',
        'sessions_peak'          => 'sess_peak',
        'sessions_persec'        => 'sess_persec',
        'sessions_persec_peak'   => 'sess_persec_pk',
        'sessions_max'           => 'sess_max',
        'sessions_since_startup' => 'sess_total',
    );

    $fields = array();
    foreach ($map as $agent_key => $ds) {
        $fields[$ds] = (isset($raw[$agent_key]) && is_numeric($raw[$agent_key])) ? $raw[$agent_key] : 0;
    }

    update_application($app_id, $fields);

    rrdtool_update_ng($device, 'freeswitch', $fields, $app_id);
}
