# Tests für PCHMTechnik

`./run-tests.sh` prüft das Child-Template gegen NOVA (Standard: JTL-Shop v5.8.1, andere Version als Parameter,
z. B. `./run-tests.sh v5.9.0`, oder eine vorhandene Installation per `JTL_NOVA_DIR=…`):

1. **Smarty kompilieren** – alle Templates inkl. NOVA-Vererbungskette mit JTLs Smarty-4-Fork und den scc-Komponenten.
   Schlägt fehl, wenn nach einem NOVA-Update Syntax oder Vererbung nicht mehr passen.
2. **Smarty rendern** – 35 Prüfungen der Bausteine mit Testdaten und der echten `Bootstrap.php`
   (`php/stubs.php` ersetzt die JTL-Klassen, `php/fakenova/` enthält NOVA-Blockgerüste).
3. **SCSS kompilieren** – Theme gegen die NOVA-Quellen (dart-sass).
4. **Browser** – 37 Prüfungen von `js/custom.js` in Chromium (Playwright): Netto/Brutto, Side-Basket inkl. AJAX,
   Countdown, Sprungnavigation, Megamenü-Klickmodus, Kaufleiste, Hinweisleiste, Neuigkeiten-Glocke …

Voraussetzungen: PHP ≥ 8.1, Node ≥ 18, git. Playwright lädt beim ersten `npx playwright install chromium` einen Browser,
alternativ `CHROME_PATH` setzen.
