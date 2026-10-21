# Tests für PCHM

`./run-tests.sh` prüft das Template gegen NOVA. Standard ist JTL-Shop v5.8.1. Eine andere Version wird als Parameter
übergeben (z. B. `./run-tests.sh v5.9.0`), eine vorhandene Installation per `JTL_NOVA_DIR=…`.

1. **Smarty kompilieren:** alle Templates inkl. NOVA-Vererbungskette mit JTLs Smarty-4-Fork und den scc-Komponenten.
2. **Smarty rendern:** 46 Prüfungen der Bausteine mit Testdaten und der echten `Bootstrap.php`, inkl. Konfigurator-JSON.
   `php/stubs.php` ersetzt die JTL-Klassen, `php/fakenova/` enthält NOVA-Blockgerüste.
3. **SCSS kompilieren:** Theme gegen die NOVA-Quellen (dart-sass).
4. **Konfigurator-Regeln:** 15 Fälle, gleiche Ergebnisse wie `api/_hardware.php` der Companion App
   (`js/konfigurator-rules.test.mjs`).
5. **Browser:** Playwright mit Chromium.
   * 37 Prüfungen von `js/custom.js`: Netto/Brutto, Side-Basket, Countdown, Megamenü, Kaufleiste …
   * 35 Prüfungen von `js/pchm-konfigurator.js` auf NOVA-Konfigurator-Markup (`js/cfg-page.html`): Schritte,
     Kompatibilitätsbox, Markieren/Sperren, Netzteil-Empfehlung, Warenkorb-Sperre, Absenden passender Konfigurationen.
6. **Update-Sicherheit:**
   * `php/check_blocks.php [NOVA-Ordner]`: Jeder überschriebene Block existiert in der NOVA-Datei.
   * `php/check_smarty5.php [Shop-Ordner]`: nur unter Smarty 5 freigegebene Modifier/Funktionen.

Voraussetzungen: PHP ≥ 8.1, Node ≥ 18, git. Einen Browser liefert `npx playwright install chromium`, alternativ `CHROME_PATH` setzen.
