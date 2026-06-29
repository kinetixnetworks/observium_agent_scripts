<?php
/**
 * html/includes/graphs/application/freeswitch_calls.inc.php
 *
 * Active calls, channels and sessions on the switch.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-freeswitch-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Count'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:calls=$rrd_filename:calls:AVERAGE";
    $rrd_options .= " DEF:chan=$rrd_filename:channels:AVERAGE";
    $rrd_options .= " DEF:sess=$rrd_filename:sessions:AVERAGE";

    $rrd_options .= " LINE1.5:calls#1E5A8C:'Calls    '";
    $rrd_options .= " GPRINT:calls:LAST:%6.0lf";
    $rrd_options .= " GPRINT:calls:AVERAGE:%6.0lf";
    $rrd_options .= " GPRINT:calls:MAX:%6.0lf\\l";

    $rrd_options .= " LINE1.5:chan#CC6600:'Channels '";
    $rrd_options .= " GPRINT:chan:LAST:%6.0lf";
    $rrd_options .= " GPRINT:chan:AVERAGE:%6.0lf";
    $rrd_options .= " GPRINT:chan:MAX:%6.0lf\\l";

    $rrd_options .= " LINE1.5:sess#006600:'Sessions '";
    $rrd_options .= " GPRINT:sess:LAST:%6.0lf";
    $rrd_options .= " GPRINT:sess:AVERAGE:%6.0lf";
    $rrd_options .= " GPRINT:sess:MAX:%6.0lf\\l";
}
