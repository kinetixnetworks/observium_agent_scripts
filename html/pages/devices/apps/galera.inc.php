<?php
/**
 * Galera application — display page
 *
 * Install path:
 *   <observium_root>/html/pages/device/apps/galera.inc.php
 *
 * Lists the graphs available for this application in the device's Apps tab.
 * Keys correspond to graph files at html/includes/graphs/application/galera_<key>.inc.php
 */

$app_graphs['default'] = array(
    'galera_cluster'   => 'Cluster Membership & Health',
    'galera_state'     => 'Local Node State',
    'galera_queues'    => 'Replication Queues',
    'galera_flow'      => 'Flow Control Pause Fraction',
    'galera_conflicts' => 'Replication Conflicts (rate)',
    'galera_traffic'   => 'Replication Traffic (rate)',
);
