<?php
/**
 * html/includes/graphs/application/keepalived_state.inc.php
 *
 * VRRP instance state counts: how many local instances are MASTER / BACKUP /
 * FAULT, against the total instance count.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-keepalived-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Instances'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:inst=$rrd_filename:instances:AVERAGE";
    $rrd_options .= " DEF:master=$rrd_filename:master:AVERAGE";
    $rrd_options .= " DEF:backup=$rrd_filename:backup:AVERAGE";
    $rrd_options .= " DEF:fault=$rrd_filename:fault:AVERAGE";

    $rrd_options .= " LINE1:inst#999999:'Instances '";
    $rrd_options .= " GPRINT:inst:LAST:%4.0lf\\l";

    $rrd_options .= " LINE1.5:master#006600:'Master    '";
    $rrd_options .= " GPRINT:master:LAST:%4.0lf";
    $rrd_options .= " GPRINT:master:AVERAGE:%4.0lf";
    $rrd_options .= " GPRINT:master:MAX:%4.0lf\\l";

    $rrd_options .= " LINE1.5:backup#1E5A8C:'Backup    '";
    $rrd_options .= " GPRINT:backup:LAST:%4.0lf";
    $rrd_options .= " GPRINT:backup:AVERAGE:%4.0lf";
    $rrd_options .= " GPRINT:backup:MAX:%4.0lf\\l";

    $rrd_options .= " LINE1.5:fault#B22222:'Fault     '";
    $rrd_options .= " GPRINT:fault:LAST:%4.0lf";
    $rrd_options .= " GPRINT:fault:AVERAGE:%4.0lf";
    $rrd_options .= " GPRINT:fault:MAX:%4.0lf\\l";
}
