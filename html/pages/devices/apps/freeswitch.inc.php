<?php
/**
 * FreeSWITCH application — display page
 *
 * Install path:
 *   <observium_root>/html/pages/devices/apps/freeswitch.inc.php
 *
 * Lists the graphs available for this application in the device's Apps tab.
 * Keys correspond to graph files at
 * html/includes/graphs/application/freeswitch_<key>.inc.php
 */

$app_graphs['default'] = array(
    'freeswitch_calls'    => 'Calls, Channels & Sessions',
    'freeswitch_sessions' => 'Sessions vs Peak / Max',
);
