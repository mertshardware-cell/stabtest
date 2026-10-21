#!/usr/bin/env bash
# Prüft PCHM gegen eine NOVA-Version:
#  1. Smarty: alle Child-Templates inkl. Vererbungskette zu NOVA kompilieren (JTL-Smarty-4-Fork + scc)
#  2. Smarty: Bausteine mit Testdaten und echter Bootstrap.php rendern
#  3. SCSS: Theme gegen NOVA kompilieren (dart-sass)
#  4. Konfigurator-Regeln: Parität mit _hardware.php der Companion-App (node --test)
#  5. Browser: js/custom.js und js/pchm-konfigurator.js in Chromium testen (Playwright)
#  6. Update-Sicherheit: alle überschriebenen NOVA-Blöcke existieren in der geprüften NOVA-Version
#
# Aufruf:  ./run-tests.sh [JTL-Version, Standard v5.8.1]
# Optional: JTL_NOVA_DIR=/pfad/zum/shop/templates/NOVA ./run-tests.sh   (eigene NOVA-Installation testen)
#           CHROME_PATH=/pfad/zu/chrome (vorinstallierter Browser für Playwright)
set -euo pipefail
cd "$(dirname "$0")"
VERSION="${1:-v5.8.1}"
CACHE=.cache
mkdir -p "$CACHE"

if [ -z "${JTL_NOVA_DIR:-}" ]; then
    if [ ! -d "$CACHE/jtl-shop/templates/NOVA" ]; then
        git clone -q --depth 1 --branch "$VERSION" --filter=blob:none --sparse https://gitlab.com/jtl-software/jtl-shop/core.git "$CACHE/jtl-shop"
        git -C "$CACHE/jtl-shop" sparse-checkout set templates/NOVA
    fi
    export JTL_NOVA_DIR="$PWD/$CACHE/jtl-shop/templates/NOVA"
fi
[ -d "$CACHE/smarty4" ] || git clone -q --depth 1 https://gitlab.com/jtl-software/jtl-shop/tools/smarty4.git "$CACHE/smarty4"
[ -d "$CACHE/scc" ] || git clone -q --depth 1 https://gitlab.com/jtl-software/jtl-shop/tools/scc.git "$CACHE/scc"
[ -d node_modules ] || npm install --silent

echo "== Smarty: kompilieren ==";  php php/compile_all.php | tail -3
echo "== Smarty: rendern ==";      php php/render_tests.php | tail -3

echo "== SCSS: kompilieren =="
# Die SCSS-Imports erwarten NOVA neben dem Child-Template (templates/NOVA)
LINK=../../templates/NOVA
CREATED=0
if [ ! -e "$LINK" ]; then ln -s "$JTL_NOVA_DIR" "$LINK"; CREATED=1; fi
trap '[ "$CREATED" = 1 ] && rm -f "$LINK"' EXIT
npm run --silent build:css && echo "OK"

echo "== Konfigurator: Regeln ==";  node --test js/konfigurator-rules.test.mjs 2>&1 | grep -E '^# (pass|fail)'
echo "== Browser: js/custom.js ==";  node js/run.mjs | tail -3
echo "== Browser: js/pchm-konfigurator.js ==";  node js/konfigurator.mjs | tail -3
echo "== Update-Sicherheit: NOVA-Blöcke ==";  php php/check_blocks.php "$JTL_NOVA_DIR" | tail -3
echo "== Update-Sicherheit: Smarty 5 ==";  php php/check_smarty5.php | tail -1
