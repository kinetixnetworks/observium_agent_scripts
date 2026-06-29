<?php
/**
 * html/includes/graphs/application/kamailio_sessions.inc.php
 *
 * SIP state: registered contacts (usrloc), active dialogs/calls (dialog),
 * and in-use transactions (tm). Zero where the module isn't loaded.
 */

include($config['html_dir']."/includes/graphs/common.inc.php");

$rrd_filename = get_rrd_path($device, "app-kamailio-".$app['app_id'].".rrd");

if (is_file($rrd_filename)) {

    $rrd_options .= " --vertical-label='Count'";
    $rrd_options .= " --lower-limit=0";

    $rrd_options .= " DEF:reg=$rrd_filename:registered:AVERAGE";
    $rrd_options .= " DEF:dlg=$rrd_filename:active_dialogs:AVERAGE";
    $rrd_options .= " DEF:tx=$rrd_filename:tx_active:AVERAGE";

    $rrd_options .= " LINE1.5:reg#1E5A8C:'Registered '";
    $rrd_options .= " GPRINT:reg:LAST:%7.0lf";
    $rrd_options .= " GPRINT:reg:AVERAGE:%7.0lf";
    $rrd_options .= " GPRINT:reg:MAX:%7.0lf\\l";

    $rrd_options .= " LINE1.5:dlg#006600:'Dialogs    '";
    $rrd_options .= " GPRINT:dlg:LAST:%7.0lf";
    $rrd_options .= " GPRINT:dlg:MAX:%7.0lf\\l";

    $rrd_options .= " LINE1.5:tx#CC6600:'Transactions'";
    $rrd_options .= " GPRINT:tx:LAST:%7.0lf";
    $rrd_options .= " GPRINT:tx:MAX:%7.0lf\\l";
}
