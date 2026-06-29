<?php
/**
 * html/includes/graphs/application/rtpengine_sessions.inc.php
 *
 * Active media sessions on the proxy: total, own (this node), foreign
 * (offloaded to/from another node), and currently-transcoded media.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-rtpengine-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Sessions'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:total=$rrd_filename:sessions:AVERAGE";
    $rrd_options .= " DEF:own=$rrd_filename:sessions_own:AVERAGE";
    $rrd_options .= " DEF:foreign=$rrd_filename:sessions_foreign:AVERAGE";
    $rrd_options .= " DEF:trans=$rrd_filename:transcoded:AVERAGE";

    $rrd_options .= " AREA:total#B0D8F0:'Total      '";
    $rrd_options .= " GPRINT:total:LAST:%6.0lf";
    $rrd_options .= " GPRINT:total:AVERAGE:%6.0lf";
    $rrd_options .= " GPRINT:total:MAX:%6.0lf\\l";

    $rrd_options .= " LINE1.5:own#1E5A8C:'Own        '";
    $rrd_options .= " GPRINT:own:LAST:%6.0lf\\l";

    $rrd_options .= " LINE1.5:foreign#CC6600:'Foreign    '";
    $rrd_options .= " GPRINT:foreign:LAST:%6.0lf\\l";

    $rrd_options .= " LINE1.5:trans#B22222:'Transcoded '";
    $rrd_options .= " GPRINT:trans:LAST:%6.0lf\\l";
}
