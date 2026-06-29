<?php
/**
 * html/includes/graphs/application/galera_queues.inc.php
 *
 * Send and receive queue averages. Sustained recv_queue_avg > 1.0 means this
 * node is falling behind the cluster (apply too slow). Sustained
 * send_queue_avg > 0.0 typically indicates network throughput problems.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Avg queue length'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:recv=$rrd_filename:recv_queue_avg:AVERAGE";
    $rrd_options .= " DEF:send=$rrd_filename:send_queue_avg:AVERAGE";

    $rrd_options .= " LINE1.5:recv#1E5A8C:'Receive queue avg'";
    $rrd_options .= " GPRINT:recv:LAST:%7.2lf";
    $rrd_options .= " GPRINT:recv:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:recv:MAX:%7.2lf\\l";

    $rrd_options .= " LINE1.5:send#CC6600:'Send queue avg   '";
    $rrd_options .= " GPRINT:send:LAST:%7.2lf";
    $rrd_options .= " GPRINT:send:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:send:MAX:%7.2lf\\l";
}
