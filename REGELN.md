# Regeln für das PCHM-Template

Verbindliche Regeln für alle Änderungen an `templates/PCHM`. Quellen sind die JTL-Entwicklerdoku
(Shop 5.x und Smarty-5-Umstellung), die Designlinie `DESIGN.md` (Companion App) und `api/_hardware.php`
(Companion App). Bei Widerspruch gilt: JTL-Technik vor Design, Daten-Regeln aus `_hardware.php` vor eigenen Ideen.

---

## 1. Update-Sicherheit (JTL)

1. **Child statt Kopie.** PCHM ist ein NOVA-Child (`<Parent>NOVA</Parent>`). NOVA-Dateien werden nie kopiert oder
   geändert. JTL verkettet automatisch `extends:[NOVA]datei|[frontend]datei`, deshalb steht in Child-Dateien
   **kein `{extends}`**, nur `{block}`-Überschreibungen.
2. **Blöcke erweitern statt ersetzen.** `{block name='…' append}` / `prepend` oder `{$smarty.block.parent}` verwenden.
   Ganz ersetzen nur, wenn ein Schalter es verlangt (z. B. Side-Basket), und dann per `{if}` mit
   `{$smarty.block.parent}` im Sonst-Zweig.
3. **Blocknamen prüfen.** Jeder überschriebene Block muss in der gleichnamigen NOVA-Datei existieren.
   Sonst wirkt die Überschreibung nach einem Update still nicht mehr. Das prüft `tests/pchm/php/check_blocks.php`,
   bisher gegen 5.5.0 bis develop.
4. **Ordnername = `<Name>` = Namespace.** `templates/PCHM`, `<Name>PCHM</Name>`, `namespace Template\PCHM;`
   (PSR-4). Die `Bootstrap.php` erbt von `\Template\NOVA\Bootstrap` und ruft in `registerPlugins()` zuerst
   `parent::registerPlugins()` auf.
5. **Einstellungen.**
   * Eigene Sektionen heißen `pchm_*`.
   * NOVA-Einstellungen nur per `override="true"` anpassen, zum Ausblenden mit `Editable="false"`.
   * Keys nie umbenennen, sonst gehen gespeicherte Werte verloren (`ttemplateeinstellungen`).
6. **JavaScript.**
   * Eigenes JS gehört in `js/custom.js` (lädt NOVA automatisch) oder in den Footer über Block `layout-footer-js` mit `defer`.
   * Inline-JS nur über `{inline_script}`. Einzige Ausnahme: `snippets/pchm/head_script.tpl` muss vor dem ersten
     Zeichnen laufen (Preis-Modus, Hinweisleiste) und bleibt ein kleines `<script>` im `<head>`.
   * Kein jQuery-Plugin von Dritten.
7. **OPC.** Neue Bereiche bekommen `{opcMountPoint id='pchm_…' title='…'}`. NOVA-Mountpoints nie entfernen.
8. **Sprache.**
   * Eingebaute Texte stehen in `Bootstrap::TEXTS` (ger/eng).
   * Überschreibbar sind sie über die Sprachvariable `pchm_<schlüssel>` in Sektion `custom`.
   * Texte in Einstellungen: `de=… || en=…` oder `lang:variable`.
9. **Artikeldaten.**
   * Funktionsattribute über `$Artikel->FunktionsAttribute`, Schlüssel immer kleingeschrieben.
   * Merkmale über `$Artikel->oMerkmale_arr`.
   * Keine eigenen Datenbankabfragen im Template.
10. **Theme.**
    * SCSS unter `themes/pchm/sass`; JTL kompiliert im Backend mit scssphp.
    * Darum nur Sass-Funktionen, die scssphp 1.13/2.x kann, und keine `@use`-Module außer denen, die NOVA nutzt.
    * Farben aus Einstellungen als CSS-Variablen (`--pchm-*`), damit sie ohne Kompilieren wirken.

## 2. Smarty 5

JTL-Shop 5.6+ läuft auf Smarty 5, derzeit noch mit `SMARTY_LEGACY_MODE`. PCHM muss auch ohne Legacy-Modus laufen:

* In Templates nur Smarty-eigene Modifier/Funktionen, JTL-Plugins und die `SECURE_PHP_FUNCTIONS` aus
  `includes/defines.php` verwenden (Liste: `tests/pchm/php/secure_php_functions.txt`).
  Alles andere gehört als Plugin in die `Bootstrap.php`. Das prüft `tests/pchm/php/check_smarty5.php`.
* Plugins mit den Strings `'function'` / `'modifier'` registrieren (nicht `Smarty::PLUGIN_*`, die Klasse heißt in 5
  anders). Namen immer mit Präfix `pchm_`.
* Keine `{php}`-Tags, kein `{include_php}`, keine statischen Klassenaufrufe im Template.

## 3. Design (aus `DESIGN.md`)

* **Violett nur auf Bedienelementen:** Primär-Buttons, aktiver Menüpunkt, Links, Fokus, Auswahl/Hover.
  Nie als Flächenschleier, Verlauf, Rahmen ruhender Elemente oder Leuchtschatten.
  Shop-Violett `#9013FE`, App-Violett `#7C3AED`. Ob der Shop angeglichen wird, ist **offen** (Entscheidung Inhaber).
* **Flächen neutral:**
  * Text `#171717`, gedämpft `#737373`/`#A3A3A3`, Linien `#E5E5E5`
  * dunkle Flächen (Topbar, Footer) `#171717`
* **Status gedeckt** und nur für Zustände: Erfolg `#15803D`, Warnung `#B45309`, Fehler `#DC2626`.
* **Form:**
  * keine Schatten außer Fokusring (PCHM verzichtet ganz auf die „Papierkante“, Rahmen statt Schatten)
  * keine Verläufe
  * Radius 6 px (Buttons, Felder), 8 px (Flächen)
* **Schrift:**
  * Fließtext Manrope, Überschriften Roboto Condensed (Markenhandbuch, gilt für den Shop), lokal als WOFF2
  * höchstens 700
  * keine Versal-Überschriften, keine gesperrten Versalien
* **Kontrast:** mindestens 4,5:1 (große/fette Schrift 3:1) gegen den tatsächlichen Hintergrund.
* Keine Emojis in der Oberfläche. ✓ und × sind als Zeichen erlaubt.

## 4. Konfigurator

* **JTL-Konfigurator nicht umbauen.** Formularfelder `item[<kKonfiggruppe>][]` und `item_quantity`, IDs `#cfg-grp-<id>`
  und `#item<kKonfigitem>` sowie Preise und Warenkorb bleiben JTL-Standard. Davon hängen JTL, NOVA-JS und das
  3D-Plugin `pchm_konfigurator3d` ab.
* PCHM liest nur. Daten kommen als JSON `#pchm-cfg-data`, die Oberfläche hängt sich daneben
  (`#pchm-cfg-nav`, `#pchm-cfg-compat`, `[data-pchm-3d]`).
* **Regeln = `_hardware.php`.**
  * `Bootstrap::componentClassifiers()` ist eine 1:1-Kopie von `pchm_component_classifiers()`.
  * `PCHMKonfiguratorRules.check` portiert `pchm_check_compatibility()`: gleiche Felder, Grundlast 120 W, Reserve 1,4.
  * Ändert sich `_hardware.php`, beides nachziehen. `tests/pchm/js/konfigurator-rules.test.mjs` muss mit den Fällen
    aus `hardware-test.php` grün bleiben.
* **Fehlender Wert = „ungeprüft“, nie „passt“.** Ein Konflikt entsteht nur aus gepflegten Daten.
* Datenfelder: `socket`, `ram_type`, `tdp_watts`, `length_mm`, `height_mm`, `max_gpu_length_mm`, `max_cooler_height_mm`,
  `psu_watts`, `component_type`. Funktionsattribut `pchm_<feld>` hat Vorrang vor den Merkmal-Namen aus den Einstellungen.
* Das Skript lädt nur auf Artikeln mit Konfigurator.

## 5. Ablauf bei Änderungen

1. Ändern, dann `cd tests/pchm && ./run-tests.sh` ausführen. Alles muss grün sein.
2. SCSS geändert → `npm run build:css` und die kompilierten Dateien mitcommitten.
3. Neue Einstellung → `template.xml` und Test in `php/render_tests.php`. Neuer Text → `TEXTS` (ger + eng).
4. Vor einem Shop-Update: `./run-tests.sh <neue Version>` (z. B. `v5.9.0`).
5. Arbeitsbranch laut Auftrag, kein PR ohne Auftrag, Commits ohne Modellangaben.
