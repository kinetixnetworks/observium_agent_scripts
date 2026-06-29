<?php
/**
 * Observium application DEFINITIONS — FreeSWITCH + keepalived.
 *
 * Append these to:  includes/definitions/apps.inc.php
 * (or drop into the per-app definitions dir if your Observium uses one).
 *
 * Registers each app so the UNIX-agent poller routes its `<<<app-NAME>>>`
 * block to the matching includes/polling/applications/<NAME>.inc.php, and the
 * web UI knows the nice name, icon and graph list. `icon` should be an existing
 * Observium icon name — swap for a bundled one if these aren't present.
 */

// The install.sh installer copies each block (between the ktx-app markers)
// into your apps.inc.php. Keep the markers intact.

// >>> ktx-app:freeswitch >>>
$config['app']['freeswitch']['name']      = 'FreeSWITCH';
$config['app']['freeswitch']['icon']      = 'freeswitch';
$config['app']['freeswitch']['graphs']    = ['freeswitch_calls', 'freeswitch_sessions'];
// <<< ktx-app:freeswitch <<<

// >>> ktx-app:keepalived >>>
$config['app']['keepalived']['name']      = 'keepalived';
$config['app']['keepalived']['icon']      = 'keepalived';
$config['app']['keepalived']['graphs']    = ['keepalived_state', 'keepalived_adverts'];
// <<< ktx-app:keepalived <<<
