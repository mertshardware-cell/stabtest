# PCHM-Shop-Template – Projektgedächtnis

Eigenes JTL-Shop-5-Template für pchm.de: `templates/PCHM`, ein NOVA-Child. Es löst SALEPIX „Technik“ ab und ersetzt den
Zwischenschritt `PCHMTechnik`. Doku: `templates/PCHM/README.md`, verbindliche Regeln: `REGELN.md`.

## Befehle

```bash
cd tests/pchm && ./run-tests.sh            # alles gegen JTL-Shop v5.8.1 (klont NOVA, smarty4, scc nach .cache/)
./run-tests.sh v5.9.0                      # andere Version
JTL_NOVA_DIR=… CHROME_PATH=/opt/pw-browsers/chromium node js/konfigurator.mjs   # einzelner Browser-Test
npm run build:css                          # braucht templates/NOVA neben templates/PCHM (Symlink, nicht committen)
```

Prüfschritte in `run-tests.sh`:

1. Smarty kompilieren
2. Smarty rendern (`php/render_tests.php`, echte `Bootstrap.php` + Stubs)
3. SCSS kompilieren
4. Konfigurator-Regeln (`node --test`, Parität mit `hardware-test.php`)
5. Browser: `js/run.mjs` für custom.js, `js/konfigurator.mjs` für den Konfigurator
6. Update-Sicherheit: `php/check_blocks.php` (NOVA-Blöcke) und `php/check_smarty5.php` (Modifier-Freigabe)

## Zusammenhänge

- **Companion App** (`mertshardware-cell/pchm-companion-app`):
  - `api/_hardware.php` ist die Quelle der Kompatibilitätsregeln und der Komponentenerkennung.
  - PCHM kopiert sie nach `Bootstrap::componentClassifiers()` und `js/pchm-konfigurator.js` (`PCHMKonfiguratorRules`).
  - Bei Änderungen dort nachziehen, Testfälle aus `hardware-test.php` in `tests/pchm/js/konfigurator-rules.test.mjs`.
- **DESIGN.md** (Companion App) ist die Designlinie. Kurz: Violett nur auf Bedienelementen, schlicht, Manrope/Roboto
  Condensed im Shop.
- **3D-Plugin** `pchm_konfigurator3d` (Companion App, Ordner `konfigurator-3d/`, Branch `claude/funny-darwin-qph2us`):
  - Liest `item[Gruppe][]`/`item_quantity` aus dem NOVA-DOM.
  - PCHM liefert `<div data-pchm-3d>` (Einstellung `mount_3d=top`) und `#pchm-cfg-data` (JSON mit technischen Daten).
  - Gedächtnis-Doku des Plugins: `konfigurator-3d/CLAUDE.md`, dort ist die Template-Anbindung eingetragen.

## Konfigurator (Kern)

- JTL-Formular bleibt unangetastet, PCHM liest nur.
- Block `productdetails-config-options` (Liste und Galerie) umschließt NOVA mit `snippets/pchm/cfg_top.tpl` und
  `cfg_bottom.tpl`.
- Daten pro Komponente:
  - Funktionsattribut `pchm_<feld>` vor Merkmal (Namen in Einstellungen `m_*`), normalisiert (Code: Großbuchstaben ohne
    Leer-/Bindestrich, Zahl: erste Zahl).
  - Gruppentyp: `pchm_component_type`, sonst Gruppenname, sonst Mehrheit der Artikelnamen.
- Fehlender Wert = „ungeprüft“. Bei Konflikt wird der Warenkorb gesperrt (Capture-Submit auf `#buy_form`, abschaltbar).
- `#pchm-cfg-compat` sitzt direkt in `#product-configuration-sidebar`, denn NOVA hat dort kein `.panel`.

## Stand (Version 2.0.0)

- Geprüft:
  - NOVA-Blöcke: 5.5.0, 5.6.0, 5.7.0, 5.8.0, 5.8.1, develop (5.9.0-alpha)
  - Smarty-5-Freigabeliste aus develop
- Tests: 46 Render-Prüfungen, 15 Regeltests, 37 Browser-Prüfungen custom.js, 35 Konfigurator-Browser-Prüfungen – alle grün.
- Migration der Einstellungen: `docs/migration-pchmtechnik.sql` (Keys identisch, Design bewusst nicht übernommen).

## Offen (Inhaber entscheidet)

1. Shop-Violett `#9013FE` an App-Violett `#7C3AED` angleichen? (DESIGN.md, *Nachziehen* Punkt 2) – Einstellung
   `color_primary` (+ `color_primary_hover`), für selten genutzte NOVA-Elemente zusätzlich `$primary` in
   `themes/pchm/sass/_variables.scss` und neu kompilieren.
2. Kompatibilitätsdaten in der JTL-Wawi pflegen (Funktionsattribute `pchm_*` oder Merkmale). Ohne Daten zeigt der
   Konfigurator nur „ungeprüft“.
3. Echte Konfigurator-Gruppennamen im Live-Shop prüfen (pchm.de ist aus der Cloud nicht erreichbar).
4. Der Chat „Neuer Konfigurator mit Kompatibil. (fork)“ war aus dieser Umgebung nicht lesbar. Was dort beschlossen
   wurde und hier fehlt, bitte nachreichen.

## Regeln für die Arbeit hier

- Arbeitsbranch laut Auftrag (zuletzt `claude/adoring-cannon-l1qi93`), kein PR ohne Auftrag, keine Modellangaben in Commits.
- `templates/NOVA` nur als lokaler Symlink zum Kompilieren, nie committen.
- Kommentare und Doku auf Deutsch mit Umlauten.
