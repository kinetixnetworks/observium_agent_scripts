<?php
/**
 * html/includes/graphs/application/galera_conflicts.inc.php
 *
 * Replication conflict rates:
 *   cert_failures - local transactions that failed certification (would
 *                   conflict with another node's already-committed write)
 *   bf_aborts     - "brute force" aborts: local transactions killed because
 *                   a replicated transaction got priority
 * Both are stored as DERIVE so the graph shows per-second rates.
 * Some baseline activity is normal in multi-writer setups; sustained spikes
 * suggest hot rows being written from multiple nodes.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Conflicts/sec'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:cert=$rrd_filename:cert_failures:AVERAGE";
    $rrd_options .= " DEF:bf=$rrd_filename:bf_aborts:AVERAGE";

    $rrd_options .= " LINE1.5:cert#B22222:'Cert failures'";
    $rrd_options .= " GPRINT:cert:LAST:%7.2lf";
    $rrd_options .= " GPRINT:cert:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:cert:MAX:%7.2lf\\l";

    $rrd_options .= " LINE1.5:bf#CC8800:'BF aborts    '";
    $rrd_options .= " GPRINT:bf:LAST:%7.2lf";
    $rrd_options .= " GPRINT:bf:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:bf:MAX:%7.2lf\\l";
}
