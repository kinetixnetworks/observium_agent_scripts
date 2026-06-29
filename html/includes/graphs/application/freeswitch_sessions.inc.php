<?php
/**
 * html/includes/graphs/application/freeswitch_sessions.inc.php
 *
 * Current sessions against the peak-seen and configured-max session counts.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-freeswitch-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Sessions'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:sess=$rrd_filename:sessions:AVERAGE";
    $rrd_options .= " DEF:peak=$rrd_filename:sess_peak:AVERAGE";
    $rrd_options .= " DEF:max=$rrd_filename:sess_max:AVERAGE";

    $rrd_options .= " AREA:sess#B0D8F0:'Sessions '";
    $rrd_options .= " GPRINT:sess:LAST:%6.0lf";
    $rrd_options .= " GPRINT:sess:AVERAGE:%6.0lf";
    $rrd_options .= " GPRINT:sess:MAX:%6.0lf\\l";

    $rrd_options .= " LINE1.5:peak#CC6600:'Peak     '";
    $rrd_options .= " GPRINT:peak:LAST:%6.0lf";
    $rrd_options .= " GPRINT:peak:MAX:%6.0lf\\l";

    $rrd_options .= " LINE1:max#B22222:'Max      '";
    $rrd_options .= " GPRINT:max:LAST:%6.0lf\\l";
}
