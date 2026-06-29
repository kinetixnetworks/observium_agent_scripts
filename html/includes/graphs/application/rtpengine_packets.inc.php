<?php
/**
 * html/includes/graphs/application/rtpengine_packets.inc.php
 *
 * Relayed packet rate and relay errors/sec. Both DERIVE counters.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-rtpengine-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Packets/sec'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:pkts=$rrd_filename:relayed_packets:AVERAGE";
    $rrd_options .= " DEF:errs=$rrd_filename:relayed_errors:AVERAGE";

    $rrd_options .= " LINE1.5:pkts#1E5A8C:'Relayed '";
    $rrd_options .= " GPRINT:pkts:LAST:%7.1lf";
    $rrd_options .= " GPRINT:pkts:AVERAGE:%7.1lf";
    $rrd_options .= " GPRINT:pkts:MAX:%7.1lf\\l";

    $rrd_options .= " LINE1.5:errs#B22222:'Errors  '";
    $rrd_options .= " GPRINT:errs:LAST:%7.1lf";
    $rrd_options .= " GPRINT:errs:MAX:%7.1lf\\l";
}
