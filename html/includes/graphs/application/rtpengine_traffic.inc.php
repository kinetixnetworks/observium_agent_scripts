<?php
/**
 * html/includes/graphs/application/rtpengine_traffic.inc.php
 *
 * Relayed media throughput in bits/sec. relayed_bytes is DERIVE; multiply by 8
 * for bits (matches how Observium graphs interface traffic).
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-rtpengine-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label=bits/sec";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:bytes=$rrd_filename:relayed_bytes:AVERAGE";
    $rrd_options .= " CDEF:bits=bytes,8,*";

    $rrd_options .= " AREA:bits#B0E0B0:'Relayed '";
    $rrd_options .= " GPRINT:bits:LAST:%6.2lf%s";
    $rrd_options .= " GPRINT:bits:AVERAGE:%6.2lf%s";
    $rrd_options .= " GPRINT:bits:MAX:%6.2lf%s\\l";
}
