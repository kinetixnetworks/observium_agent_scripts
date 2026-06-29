<?php
/**
 * html/includes/graphs/application/keepalived_adverts.inc.php
 *
 * VRRP advertisement rate. adv_sent / adv_received are cumulative keepalived
 * counters stored as DERIVE, so this reads as adverts per second.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-keepalived-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Adverts/sec'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:sent=$rrd_filename:adv_sent:AVERAGE";
    $rrd_options .= " DEF:recv=$rrd_filename:adv_received:AVERAGE";

    $rrd_options .= " LINE1.5:sent#1E5A8C:'Sent     '";
    $rrd_options .= " GPRINT:sent:LAST:%7.2lf";
    $rrd_options .= " GPRINT:sent:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:sent:MAX:%7.2lf\\l";

    $rrd_options .= " LINE1.5:recv#006600:'Received '";
    $rrd_options .= " GPRINT:recv:LAST:%7.2lf";
    $rrd_options .= " GPRINT:recv:AVERAGE:%7.2lf";
    $rrd_options .= " GPRINT:recv:MAX:%7.2lf\\l";
}
