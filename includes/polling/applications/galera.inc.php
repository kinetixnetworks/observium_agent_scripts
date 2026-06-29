<?php
/**
 * Observium Galera application poller
 *
 * Install path:
 *   <observium_root>/includes/polling/applications/galera.inc.php
 *
 * Reads the <<<app-galera>>> section emitted by the agent script and writes
 * metrics to RRD. Field order in the explode() below MUST match the order
 * the agent script emits — see header of the agent script for the field list.
 */

if (!empty($agent_data['app']['galera'])) {

    $app_id = discover_app($device, 'galera');

    list (
        $mysql_reachable,
        $cluster_size,
        $cluster_status,
        $local_state,
        $ready,
        $connected,
        $recv_queue_avg,
        $send_queue_avg,
        $flow_paused,
        $cert_failures,
        $bf_aborts,
        $received,
        $received_bytes,
        $replicated,
        $replicated_bytes
    ) = explode("\n", $agent_data['app']['galera']);

    $fields = array(
        'mysql_reachable'  => $mysql_reachable,
        'cluster_size'     => $cluster_size,
        'cluster_status'   => $cluster_status,
        'local_state'      => $local_state,
        'ready'            => $ready,
        'connected'        => $connected,
        'recv_queue_avg'   => $recv_queue_avg,
        'send_queue_avg'   => $send_queue_avg,
        'flow_paused'      => $flow_paused,
        'cert_failures'    => $cert_failures,
        'bf_aborts'        => $bf_aborts,
        'received'         => $received,
        'received_bytes'   => $received_bytes,
        'replicated'       => $replicated,
        'replicated_bytes' => $replicated_bytes,
    );

    update_application($app_id, $fields);

    rrdtool_update_ng($device, 'galera', $fields, $app_id);
}
