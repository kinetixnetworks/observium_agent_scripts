<?php
/**
 * html/includes/graphs/application/galera_state.inc.php
 *
 * Local node state. Healthy value is 4 (Synced). Other states:
 *   1 = Joining, 2 = Donor/Desynced, 3 = Joined, 4 = Synced.
 * Anything other than 4 sustained means this node isn't serving traffic.
 */

$scale_min = 0;

include_once($config['html_dir']."/includes/graphs/common.inc.php");

$galera_rrd = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($galera_rrd)) {
    $rrd_filename = $galera_rrd;
}

$ds = "local_state";

$colour_area = "C0D8E8";
$colour_line = "1E5A8C";
$colour_area_max = "D8E8F4";

$graph_max = 1;

$unit_text = "State";

include($config['html_dir']."/includes/graphs/generic_simplex.inc.php");
