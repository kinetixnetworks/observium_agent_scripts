<?php
/**
 * keepalived application — display page
 *
 * Install path:
 *   <observium_root>/html/pages/devices/apps/keepalived.inc.php
 *
 * Lists the graphs available for this application in the device's Apps tab.
 * Keys correspond to graph files at
 * html/includes/graphs/application/keepalived_<key>.inc.php
 */

$app_graphs['default'] = array(
    'keepalived_state'   => 'VRRP Instance State',
    'keepalived_adverts' => 'VRRP Advertisements (rate)',
);
