<?php
/**
 * html/includes/graphs/application/galera_cluster.inc.php
 *
 * Cluster size over time. Should be flat at the expected node count;
 * any dip means a node has dropped out of the cluster.
 */

$scale_min = 0;

include_once($config['html_dir']."/includes/graphs/common.inc.php");

$galera_rrd = get_rrd_path($device, "app-galera-".$app['app_id'].".rrd");

if (is_file($galera_rrd)) {
    $rrd_filename = $galera_rrd;
}

$ds = "cluster_size";

$colour_area = "B0E0B0";
$colour_line = "006600";
$colour_area_max = "C8F0C8";

$graph_max = 1;

$unit_text = "Nodes";

include($config['html_dir']."/includes/graphs/generic_simplex.inc.php");
