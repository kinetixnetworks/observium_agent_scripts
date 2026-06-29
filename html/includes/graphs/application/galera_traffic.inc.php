<?php
/**
 * html/includes/graphs/application/galera_traffic.inc.php
 *
 * Replication traffic in bits/sec. Bytes are stored as DERIVE; we multiply
 * by 8 here for bits-per-second display, which lines up with how Observium
 * graphs interface traffic and is the natural unit for capacity planning.
 *
 *   replicated_bytes - writesets sent from this node to the cluster
 *   received_bytes   - writesets received from the rest of the cluster
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label=bits/sec";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:rxbytes=$rrd_filename:received_bytes:AVERAGE";
    $rrd_options .= " DEF:txbytes=$rrd_filename:replicated_bytes:AVERAGE";

    $rrd_options .= " CDEF:rxbits=rxbytes,8,*";
    $rrd_options .= " CDEF:txbits=txbytes,8,*";

    $rrd_options .= " AREA:rxbits#B0E0B0:'Received   '";
    $rrd_options .= " GPRINT:rxbits:LAST:%6.2lf%s";
    $rrd_options .= " GPRINT:rxbits:AVERAGE:%6.2lf%s";
    $rrd_options .= " GPRINT:rxbits:MAX:%6.2lf%s\\l";

    $rrd_options .= " LINE1.5:txbits#1E5A8C:'Replicated '";
    $rrd_options .= " GPRINT:txbits:LAST:%6.2lf%s";
    $rrd_options .= " GPRINT:txbits:AVERAGE:%6.2lf%s";
    $rrd_options .= " GPRINT:txbits:MAX:%6.2lf%s\\l";
}
