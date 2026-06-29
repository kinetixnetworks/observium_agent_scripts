<?php
/**
 * Observium config.php snippet — RRD type + app definitions for the FreeSWITCH
 * and keepalived application modules.
 *
 * These MUST be added to the Observium poller's main config.php (same place
 * galera's live). rrdtool_update_ng() looks up $config['rrd_types'][<name>] to
 * CREATE the per-device RRD (%index% = app_id); without it the poller fails with
 * "Cannot create RRD for type <name> - not found in definitions!". $config['app']
 * [<name>]['top'] picks the graphs shown on the application overview.
 *
 * Deploy: paste the two blocks below into /opt/observium/config.php, or let
 * install.sh do it:  ./install.sh --config-php /opt/observium/config.php all
 * (install.sh appends them between the ktx-cfg markers, idempotently, after
 * backing config.php up).
 *
 * DS notes: gauges are instantaneous; the cumulative keepalived/FreeSWITCH
 * counters are DERIVE (min 0 clamps the reset-to-zero on daemon restart to
 * "unknown" instead of a negative spike).
 */

// >>> ktx-cfg:rtpengine >>>
$config['rrd_types']['rtpengine'] = array(
    'file' => 'app-rtpengine-%index%.rrd',
    'ds'   => array(
        'sessions'         => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sessions_own'     => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sessions_foreign' => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'transcoded'       => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'relayed_packets'  => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'relayed_bytes'    => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'relayed_errors'   => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
    ),
);
$config['app']['rtpengine']['top'] = array('sessions', 'traffic');
// <<< ktx-cfg:rtpengine <<<

// >>> ktx-cfg:kamailio >>>
$config['rrd_types']['kamailio'] = array(
    'file' => 'app-kamailio-%index%.rrd',
    'ds'   => array(
        'registered'     => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'active_dialogs' => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'tx_active'      => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'rcv_requests'   => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'rcv_replies'    => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'fwd_requests'   => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'shmem_used'     => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'shmem_max'      => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
    ),
);
$config['app']['kamailio']['top'] = array('sessions', 'messages');
// <<< ktx-cfg:kamailio <<<

// >>> ktx-cfg:freeswitch >>>
$config['rrd_types']['freeswitch'] = array(
    'file' => 'app-freeswitch-%index%.rrd',
    'ds'   => array(
        'calls'          => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'channels'       => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sessions'       => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sess_peak'      => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sess_persec'    => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sess_persec_pk' => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sess_max'       => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'sess_total'     => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
    ),
);
$config['app']['freeswitch']['top'] = array('calls', 'sessions');
// <<< ktx-cfg:freeswitch <<<

// >>> ktx-cfg:keepalived >>>
$config['rrd_types']['keepalived'] = array(
    'file' => 'app-keepalived-%index%.rrd',
    'ds'   => array(
        'running'       => array('type' => 'GAUGE',  'min' => 0, 'max' => 1),
        'instances'     => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'master'        => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'backup'        => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'fault'         => array('type' => 'GAUGE',  'min' => 0, 'max' => 'U'),
        'became_master' => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'adv_sent'      => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
        'adv_received'  => array('type' => 'DERIVE', 'min' => 0, 'max' => 'U'),
    ),
);
$config['app']['keepalived']['top'] = array('state', 'adverts');
// <<< ktx-cfg:keepalived <<<

// >>> ktx-cfg:galera >>>
// Mirror of the galera definition already in this poller's config.php — kept
// here so a fresh Observium box can be brought up entirely from this repo.
$config['rrd_types']['galera'] = array(
    'file' => 'app-galera-%index%.rrd',
    'ds'   => array(
        'mysql_reachable'  => array('type' => 'GAUGE',  'min' => 0,   'max' => 1),
        'cluster_size'     => array('type' => 'GAUGE',  'min' => 0,   'max' => 100),
        'cluster_status'   => array('type' => 'GAUGE',  'min' => -2,  'max' => 1),
        'local_state'      => array('type' => 'GAUGE',  'min' => 0,   'max' => 6),
        'ready'            => array('type' => 'GAUGE',  'min' => -1,  'max' => 1),
        'connected'        => array('type' => 'GAUGE',  'min' => -1,  'max' => 1),
        'recv_queue_avg'   => array('type' => 'GAUGE',  'min' => 0,   'max' => 1000000),
        'send_queue_avg'   => array('type' => 'GAUGE',  'min' => 0,   'max' => 1000000),
        'flow_paused'      => array('type' => 'GAUGE',  'min' => 0,   'max' => 1),
        'cert_failures'    => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
        'bf_aborts'        => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
        'received'         => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
        'received_bytes'   => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
        'replicated'       => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
        'replicated_bytes' => array('type' => 'DERIVE', 'min' => 0,   'max' => 125000000000),
    ),
);
$config['app']['galera']['top'] = array('cluster', 'queues', 'flow', 'traffic');
// <<< ktx-cfg:galera <<<
