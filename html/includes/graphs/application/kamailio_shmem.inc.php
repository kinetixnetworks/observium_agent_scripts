<?php
/**
 * html/includes/graphs/application/kamailio_shmem.inc.php
 *
 * Shared memory usage (bytes): current used vs peak/real used.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-kamailio-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label=bytes";
    $rrd_options .= " --base=1024";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:used=$rrd_filename:shmem_used:AVERAGE";
    $rrd_options .= " DEF:max=$rrd_filename:shmem_max:AVERAGE";

    $rrd_options .= " AREA:used#B0D8F0:'Used '";
    $rrd_options .= " GPRINT:used:LAST:%6.2lf%s";
    $rrd_options .= " GPRINT:used:AVERAGE:%6.2lf%s";
    $rrd_options .= " GPRINT:used:MAX:%6.2lf%s\\l";

    $rrd_options .= " LINE1.5:max#B22222:'Peak '";
    $rrd_options .= " GPRINT:max:LAST:%6.2lf%s\\l";
}
