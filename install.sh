#!/usr/bin/env bash
#
# install.sh — install the staged Observium app modules into an Observium tree.
#
# Each module = a poller include + graph defs + a device app page + a definition
# block appended to includes/definitions/apps.inc.php. Run ON the Observium
# poller (or point --root at a mounted copy).
#
# Usage:
#   ./install.sh --list                      # list supported modules
#   ./install.sh all                         # install every module
#   ./install.sh freeswitch                  # install one
#   ./install.sh freeswitch,keepalived       # install a comma-list
#
# Options:
#   --root PATH    Observium install root (default: $OBSERVIUM_ROOT or /opt/observium)
#   --dry-run, -n  show what would happen, change nothing
#   --list, -l     list supported modules and exit
#   --help, -h     this help
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SUPPORTED=(freeswitch keepalived galera)

ROOT="${OBSERVIUM_ROOT:-/opt/observium}"
DRY=0
SELECTOR=""

die()  { echo "error: $*" >&2; exit 1; }
info() { echo "  $*"; }

usage() { sed -n '3,24p' "$0" | sed 's/^# \{0,1\}//'; }

is_supported() {
    local m="$1"
    for s in "${SUPPORTED[@]}"; do [ "$s" = "$m" ] && return 0; done
    return 1
}

# Echo the definition block for a module (between the ktx-app markers in the
# snippet), so the snippet stays the single source of truth.
extract_def() {
    awk -v b=">>> ktx-app:$1 >>>" -v e="<<< ktx-app:$1 <<<" '
        index($0, b) { f = 1 } f { print } index($0, e) { f = 0 }
    ' "$SCRIPT_DIR/definitions-snippet.inc.php"
}

install_module() {
    local mod="$1"
    echo "▸ $mod"

    # 1. Copy every staged file for this module, preserving the tree layout.
    local rel src dst n=0
    while IFS= read -r rel; do
        src="$SCRIPT_DIR/$rel"
        dst="$ROOT/$rel"
        if [ "$DRY" = 1 ]; then
            info "would install $rel"
        else
            install -D -m 0644 "$src" "$dst"
            info "installed $rel"
        fi
        n=$((n + 1))
    done < <(cd "$SCRIPT_DIR" && find includes html -type f -name "*${mod}*" 2>/dev/null | sort)
    [ "$n" -gt 0 ] || die "no staged files found for '$mod'"

    # 2. Append the app definition (idempotent — keyed on the ktx-app marker).
    local apps="$ROOT/includes/definitions/apps.inc.php"
    if [ ! -f "$apps" ]; then
        info "WARN: $apps not found — append the '$mod' block from definitions-snippet.inc.php manually"
        return
    fi
    if grep -q "ktx-app:$mod" "$apps"; then
        info "definition already present in apps.inc.php"
        return
    fi
    local block; block="$(extract_def "$mod")"
    [ -n "$block" ] || { info "WARN: no definition block for '$mod' in the snippet"; return; }
    if [ "$DRY" = 1 ]; then
        info "would append definition to apps.inc.php"
    else
        printf '\n%s\n' "$block" >> "$apps"
        info "appended definition to apps.inc.php"
    fi
}

# ── Parse args ───────────────────────────────────────────────────────────────
while [ $# -gt 0 ]; do
    case "$1" in
        --root) ROOT="${2:?--root needs a path}"; shift 2 ;;
        --root=*) ROOT="${1#*=}"; shift ;;
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
[ "$DRY" = 1 ] && echo "(dry-run — no changes will be made)"

for m in "${MODULES[@]}"; do
    install_module "$m"
done

echo
echo "Done. Verify with a manual poll, e.g.:"
echo "    cd $ROOT && ./poller.php -h <host> -d -m unix-agent"
