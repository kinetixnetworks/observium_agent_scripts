<?php
/**
 * html/includes/graphs/application/kamailio_messages.inc.php
 *
 * SIP message rates (DERIVE counters): received requests/replies and forwarded
 * requests, per second.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-kamailio-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Msgs/sec'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:rxreq=$rrd_filename:rcv_requests:AVERAGE";
    $rrd_options .= " DEF:rxrpl=$rrd_filename:rcv_replies:AVERAGE";
    $rrd_options .= " DEF:fwd=$rrd_filename:fwd_requests:AVERAGE";

    $rrd_options .= " LINE1.5:rxreq#1E5A8C:'Rx requests '";
    $rrd_options .= " GPRINT:rxreq:LAST:%7.1lf";
    $rrd_options .= " GPRINT:rxreq:AVERAGE:%7.1lf";
    $rrd_options .= " GPRINT:rxreq:MAX:%7.1lf\\l";

    $rrd_options .= " LINE1.5:rxrpl#006600:'Rx replies  '";
    $rrd_options .= " GPRINT:rxrpl:LAST:%7.1lf";
    $rrd_options .= " GPRINT:rxrpl:MAX:%7.1lf\\l";

    $rrd_options .= " LINE1.5:fwd#CC6600:'Fwd requests'";
    $rrd_options .= " GPRINT:fwd:LAST:%7.1lf";
    $rrd_options .= " GPRINT:fwd:MAX:%7.1lf\\l";
}
