#!/usr/bin/env bash
#
# install.sh — install the staged Observium app modules into an Observium tree.
#
# Each module = a poller include + graph defs + a device app page, plus an RRD
# type definition that must live in config.php (this is what lets the poller
# CREATE the RRD; without it Observium errors "Cannot create RRD for type <name>
# - not found in definitions!"). Run ON the Observium poller (or point --root at
# a mounted copy).
#
# Usage:
#   ./install.sh --list                      # list supported modules
#   ./install.sh all                         # install every module
#   ./install.sh freeswitch                  # install one
#   ./install.sh freeswitch,keepalived       # install a comma-list
#
# Options:
#   --root PATH        Observium install root (default: $OBSERVIUM_ROOT or /opt/observium)
#   --config-php PATH  also append the RRD/app definitions to this config.php
#                      (idempotent, backs it up first). Omit to print them for
#                      manual paste instead.
#   --dry-run, -n      show what would happen, change nothing
#   --list, -l         list supported modules and exit
#   --help, -h         this help
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SUPPORTED=(freeswitch keepalived galera)

ROOT="${OBSERVIUM_ROOT:-/opt/observium}"
CONFIG_PHP=""
DRY=0
SELECTOR=""

die()  { echo "error: $*" >&2; exit 1; }
info() { echo "  $*"; }

usage() { sed -n '3,29p' "$0" | sed 's/^# \{0,1\}//'; }

is_supported() {
    local m="$1"
    for s in "${SUPPORTED[@]}"; do [ "$s" = "$m" ] && return 0; done
    return 1
}

# Echo the config.php block for a module (between the ktx-cfg markers in
# config-snippet.php), so the snippet stays the single source of truth.
extract_cfg() {
    awk -v b=">>> ktx-cfg:$1 >>>" -v e="<<< ktx-cfg:$1 <<<" '
        index($0, b) { f = 1 } f { print } index($0, e) { f = 0 }
    ' "$SCRIPT_DIR/config-snippet.php"
}

install_files() {
    local mod="$1" rel src n=0
    while IFS= read -r rel; do
        src="$SCRIPT_DIR/$rel"
        if [ "$DRY" = 1 ]; then
            info "would install $rel"
        else
            install -D -m 0644 "$src" "$ROOT/$rel"
            info "installed $rel"
        fi
        n=$((n + 1))
    done < <(cd "$SCRIPT_DIR" && find includes html -type f -name "*${mod}*" 2>/dev/null | sort)
    [ "$n" -gt 0 ] || die "no staged files found for '$mod'"
}

install_config() {
    local mod="$1"
    local block; block="$(extract_cfg "$mod")"
    [ -n "$block" ] || { info "config: no ktx-cfg:$mod block in config-snippet.php (skipping)"; return; }

    # No --config-php: just tell the operator what to paste (config.php is the
    # live, secret-bearing main config — we don't touch it unless asked).
    if [ -z "$CONFIG_PHP" ]; then
        info "config: add the ktx-cfg:$mod block from config-snippet.php to your config.php (required — creates the RRD type)"
        return
    fi
    [ -f "$CONFIG_PHP" ] || die "config.php not found: $CONFIG_PHP"
    if grep -q "ktx-cfg:$mod" "$CONFIG_PHP"; then
        info "config: ktx-cfg:$mod already present in $(basename "$CONFIG_PHP")"
        return
    fi
    # Refuse to append past a closing PHP tag (would land outside <?php and
    # render as text) — safer to have the operator paste it before the ?>.
    if grep -qE '^[[:space:]]*\?>' "$CONFIG_PHP"; then
        info "config: $(basename "$CONFIG_PHP") has a closing ?> — paste the ktx-cfg:$mod block from config-snippet.php manually, before the ?>"
        return
    fi
    if [ "$DRY" = 1 ]; then
        info "would append ktx-cfg:$mod rrd_types to $CONFIG_PHP (after a backup)"
        return
    fi
    cp -n "$CONFIG_PHP" "$CONFIG_PHP.ktx-bak" 2>/dev/null || true
    printf '\n%s\n' "$block" >> "$CONFIG_PHP"
    info "appended ktx-cfg:$mod rrd_types to $(basename "$CONFIG_PHP") (backup: ${CONFIG_PHP}.ktx-bak)"
}

install_module() {
    echo "▸ $1"
    install_files "$1"
    install_config "$1"
}

# ── Parse args ───────────────────────────────────────────────────────────────
while [ $# -gt 0 ]; do
    case "$1" in
        --root) ROOT="${2:?--root needs a path}"; shift 2 ;;
        --root=*) ROOT="${1#*=}"; shift ;;
        --config-php) CONFIG_PHP="${2:?--config-php needs a path}"; shift 2 ;;
        --config-php=*) CONFIG_PHP="${1#*=}"; shift ;;
        -n|--dry-run) DRY=1; shift ;;
        -l|--list) printf '%s\n' "${SUPPORTED[@]}"; exit 0 ;;
        -h|--help) usage; exit 0 ;;
        -*) die "unknown option: $1" ;;
        *) [ -z "$SELECTOR" ] || die "unexpected argument: $1"; SELECTOR="$1"; shift ;;
    esac
done

[ -n "$SELECTOR" ] || { usage; exit 1; }

# ── Resolve the module list ──────────────────────────────────────────────────
declare -a MODULES=()
if [ "$SELECTOR" = "all" ]; then
    MODULES=("${SUPPORTED[@]}")
else
    IFS=',' read -ra MODULES <<< "$SELECTOR"
    for m in "${MODULES[@]}"; do
        is_supported "$m" || die "unsupported module '$m' (supported: ${SUPPORTED[*]}, or 'all')"
    done
fi

# ── Sanity-check the target looks like an Observium tree ─────────────────────
[ -d "$ROOT" ] || die "Observium root not found: $ROOT (use --root)"
[ -d "$ROOT/includes/polling/applications" ] \
    || die "$ROOT doesn't look like an Observium install (no includes/polling/applications). Use --root."

echo "Observium root: $ROOT"
[ -n "$CONFIG_PHP" ] && echo "config.php: $CONFIG_PHP"
[ "$DRY" = 1 ] && echo "(dry-run — no changes will be made)"

for m in "${MODULES[@]}"; do
    install_module "$m"
done

echo
echo "Done. If you added definitions to config.php, discover + poll the host:"
echo "    cd $ROOT && ./discovery.php -h <host> -d -m applications"
echo "    cd $ROOT && ./poller.php    -h <host> -d -m unix-agent"
