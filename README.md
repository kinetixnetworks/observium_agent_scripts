# observium_agent_scripts

Kinetix Observium UNIX-agent scripts **and** the matching poller-side
application modules.

## Two halves of an application

A working Observium application needs both:

1. **Agent script** (repo root, e.g. `freeswitch`, `keepalived`, `galera`) —
   runs on the monitored host under `observium_agent`, emits an
   `<<<app-NAME>>>` block. Deployed by the Skynet SNMP onboarding playbook
   (downloaded from this repo) into `/usr/lib/check_mk_agent/plugins/`.

2. **Poller module** (this repo's `includes/` + `html/` trees) — runs on the
   Observium poller, parses that block, and graphs it:
   - `includes/polling/applications/<name>.inc.php` — parse + store. Calls
     `discover_app()` (creates the app row → **this is what makes Observium
     discover it**), `update_application()`, `rrdtool_update_ng()`.
   - `html/includes/graphs/application/<name>_*.inc.php` — graph defs.
   - `html/pages/devices/apps/<name>.inc.php` — the device Apps-tab page.

3. **config.php RRD definition** — `rrdtool_update_ng()` looks up
   `$config['rrd_types'][<name>]` to **create** the RRD. Without it the poll
   fails with *"Cannot create RRD for type <name> - not found in
   definitions!"*. These live in the poller's `config.php` (see
   `config-snippet.php`), not in `includes/definitions/`.

> All three are required. Miss the agent script → no data on :36602. Miss the
> poller module → "no polling module". Miss the config.php rrd_type → the
> RRD-create error above. Also: the device's **applications** module must be
> enabled (`devices_attribs`), or the agent block is never even looked at.

## Install the poller side

On the Observium poller:

    ./install.sh --list                                          # supported modules
    ./install.sh --config-php /opt/observium/config.php all      # everything + config.php
    ./install.sh --config-php /opt/observium/config.php freeswitch,keepalived
    ./install.sh --root /opt/observium --dry-run all             # preview

`install.sh` copies each module's files into the tree and, with `--config-php`,
appends its `$config['rrd_types']`/`$config['app']` block from
`config-snippet.php` (idempotent via `ktx-cfg:<name>` markers, backs config.php
up first, refuses to write past a closing `?>`). Without `--config-php` it
prints what to paste. Then:

    ./discovery.php -h <host> -d -m applications    # creates the app row
    ./poller.php    -h <host> -d -m unix-agent      # first data point

## Adding a new app

1. Add the agent script at the repo root (emit `<<<app-NAME>>>`, `key:value`).
2. Add `includes/polling/applications/NAME.inc.php` — copy an existing one;
   keep the `discover_app()` / `update_application($app_id,$fields)` /
   `rrdtool_update_ng($device,'NAME',$fields,$app_id)` shape.
3. Add `html/includes/graphs/application/NAME_*.inc.php` and
   `html/pages/devices/apps/NAME.inc.php`.
4. Add a `ktx-cfg:NAME` block to `config-snippet.php` (RRD DS names ≤19 chars;
   GAUGE for gauges, DERIVE for cumulative counters).
5. Add `NAME` to `SUPPORTED` in `install.sh`.
