<?php
/**
 * html/includes/graphs/application/galera_flow.inc.php
 *
 * Flow control paused fraction. 0.0 is ideal; values approaching 1.0 mean
 * this node is throttling the entire cluster's write throughput.
 * Anything above ~0.1 sustained warrants investigation.
 */

$scale_min = 0;

include_once($config['html_dir']."/includes/graphs/common.inc.php");

$galera_rrd = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($galera_rrd)) {
    $rrd_filename = $galera_rrd;
}

$ds = "flow_paused";

$colour_area = "F4C0C0";
$colour_line = "B22222";
$colour_area_max = "F8D8D8";

$graph_max = 1;
$multiplier = 100;

$unit_text = "%";

include($config['html_dir']."/includes/graphs/generic_simplex.inc.php");
