# PCHM – eigenes Shop-Template für PCHM.de

**PCHM** ist das eigene Template von PCHM.de. Es löst das SALEPIX-„Technik“-Template dauerhaft ab und ist als
**Child-Template des JTL-Standard-Templates NOVA** gebaut. So bleibt es updatesicher und verbindet den
JTL-Konfigurator mit der Kompatibilitätsprüfung der PCHM Companion App und der 3D-Vorschau.

* **Basis:** NOVA, geprüft gegen **JTL-Shop 5.5.0, 5.6.0, 5.7.0, 5.8.0, 5.8.1 und develop (5.9.0-alpha)**
* **Mindestversion:** JTL-Shop 5.5 (`MinShopVersion`)
* **Updatesicher:**
  * Es werden nur NOVA-Blöcke erweitert (`append`/`prepend`/`{$smarty.block.parent}`). Kein NOVA-Code wird kopiert.
  * Smarty-5-tauglich: nur freigegebene Modifier/Funktionen.
  * Die Tests in `tests/pchm` prüfen beides automatisch.
* **Design:** Designlinie aus `DESIGN.md` (Companion App), Regeln siehe [`REGELN.md`](../../REGELN.md):
  * Violett nur auf Bedienelementen, sonst neutrale Grautöne
  * schlicht: keine Schatten und Verläufe, Radius 6/8 px
  * keine Großbuchstaben-Überschriften
  * Schriften Manrope und Roboto Condensed, lokal eingebunden

PCHM ersetzt den Zwischenschritt `PCHMTechnik` (gleiche Einstellungs-Keys, Migration siehe Abschnitt 3).

---

## 1. Installation

1. Ordner **`PCHM`** nach `<shop>/templates/PCHM/` hochladen. Der Ordnername muss exakt `PCHM` lauten
   (PHP-Namespace `Template\PCHM` in `Bootstrap.php`).
2. NOVA muss unter `<shop>/templates/NOVA/` liegen (Standard bei JTL-Shop 5).
3. Backend → *Darstellung → Templates* → **PCHM** aktivieren (zuerst auf dem Testsystem).
4. Einstellungen unter *Templates → PCHM* prüfen (Abschnitte „PCHM – …“).
5. *System → Cache* leeren.

---

## 2. Funktionen

| Bereich | Funktion | Einstellung |
|---|---|---|
| Header | Hinweisleiste mit Countdown, USP-Leiste, Hotline, Sticky je Gerät | *PCHM – Header* |
| Header | Side-Basket mit Versandkosten-Fortschritt, Konto- und News-Seitenleiste | *PCHM – Header*, *PCHM – Warenkorb* |
| Header | Umschalter Privat/Gewerbe (Netto/Brutto-Anzeige inkl. Staffelpreise) | *PCHM – Header* |
| Megamenü | Tiefe 1–4, Hover oder Klick, Spalten, „Alle anzeigen“ | *PCHM – Megamenü* |
| Startseite | USP-Kacheln, Preisaktionen mit Countdown, Kategorie-Kacheln, Countdown-Block, Hersteller-Slider, Reihenfolge frei | *PCHM – Startseite* |
| Artikelliste | Beschreibung oben/unten/einklappen, Unterkategorien, Artikel pro Zeile je Gerät, Filterleiste einklappbar | *PCHM – Artikelliste* |
| Artikeldetails | Sprungnavigation, Sticky-Kaufleiste, Gesamtbetrag im Button, Dokumente, Vertrauens-Box | *PCHM – Artikeldetails* |
| **Konfigurator** | Gruppen-Navigation mit Fortschritt, **Kompatibilitätsprüfung** (Sockel, RAM, GPU-Länge, Kühlerhöhe, Netzteil), Netzteil-Empfehlung, Warenkorb-Sperre bei Konflikt, Platz für die 3D-Vorschau | *PCHM – Konfigurator* |
| Footer | Kontakt, Zahlungs- und Versand-Icons, weitere Social-Kanäle, freies HTML | *PCHM – Footer* |
| Design | 19 Farben als CSS-Variablen (ohne Kompilieren), Schriften, Radius, Seitenbreite | *PCHM – Design* |
| Texte | mehrsprachig `de=… \|\| en=…` oder `lang:variable`; eingebaute Texte über Sprachvariablen `pchm_<schlüssel>` (Sektion `custom`) | alle Textfelder |
| OPC | alle NOVA-Mountpoints + `pchm_cfg_before_groups`/`pchm_cfg_after_groups` im Konfigurator | OPC |

---

## 3. Umstieg von Technik bzw. PCHMTechnik

1. PCHM hochladen und aktivieren (siehe 1.).
2. Einstellungen aus PCHMTechnik übernehmen: [`docs/migration-pchmtechnik.sql`](../../docs/migration-pchmtechnik.sql)
   in der Shop-Datenbank ausführen (sichert die betroffenen Zeilen vorher). Das Design wird bewusst nicht übernommen.
   PCHM startet mit der neuen Designlinie, die alten Farben lassen sich im Skript optional mitnehmen.
3. Wer direkt von SALEPIX-Technik kommt, trägt die Werte von Hand ein:
   * Farben, USPs, Hotline, Hinweistexte, Footer
   * Slider/Banner (*Inhalte*)
   * Boxen
   * OPC-Seiten mit SALEPIX-Portlets mit JTL-Portlets neu aufbauen
4. **Konfigurator-Daten pflegen** (Abschnitt 4), sonst zeigt die Prüfung „ungeprüft“ statt „passt“.
5. Testen:
   * Startseite, Kategorie, Artikel (einfach, Variation, Konfigurator-PC)
   * Warenkorb, Kasse, Konto
   * mobil, Englisch
6. Erst danach SALEPIX-Templater/OnePageCheckout und PCHMTechnik deaktivieren.

Nicht Teil des Templates (unverändert gültig):

* **OnePageCheckout von SALEPIX:** ein eigenes Plugin. Mit NOVA läuft der JTL-Checkout.
* **Netto/Brutto-Umschalter:** ändert nur die Anzeige. Warenkorb und Steuer richten sich weiter nach der Kundengruppe.

---

## 4. Konfigurator und Kompatibilitätsprüfung

Der JTL-Konfigurator bleibt unverändert. Formularfelder (`item[<Gruppe>][]`, `item_quantity`), Preise, Warenkorb und
„Konfiguration bearbeiten“ sind JTL-Standard. PCHM fügt nur hinzu (Block `productdetails-config-options`, Liste und Galerie):

* `<script type="application/json" id="pchm-cfg-data">` mit Gruppen, Typ und technischen Daten jeder Komponente
  (aus `Bootstrap.php`, `pchm_cfg_data`).
* `<nav id="pchm-cfg-nav">` – Schrittnavigation („3 von 6 Gruppen gewählt“, Pflichtgruppen markiert, Sprung zur Gruppe).
* `#pchm-cfg-compat` in der NOVA-Sidebar `#product-configuration-sidebar`. Die Box zeigt Konflikte, fehlende Angaben,
  bestandene Prüfungen und die Netzteil-Empfehlung.
* Nicht passende Optionen werden markiert (mit Begründung) oder gesperrt (Einstellung „Nicht kompatible Optionen“).
* Bei Konflikt verhindert PCHM „In den Warenkorb“ (abschaltbar) und zeigt die NOVA-Meldung.
* `<div data-pchm-3d>` – fester Platz für die 3D-Vorschau (Plugin `pchm_konfigurator3d`), wenn „3D-Vorschau“ auf
  „über den Gruppen“ steht. Bei „automatisch“ sucht sich das Plugin selbst seinen Platz.
* `js/pchm-konfigurator.js` wird nur auf Artikeln mit Konfigurator geladen (Footer, `defer`).

### Regeln (identisch mit `api/_hardware.php` der Companion App)

| Prüfung | Daten | Konflikt, wenn |
|---|---|---|
| Sockel | CPU `socket` ↔ Mainboard `socket` | ungleich |
| RAM | RAM `ram_type` ↔ Mainboard `ram_type` | ungleich |
| Grafikkarte | GPU `length_mm` ↔ Gehäuse `max_gpu_length_mm` | GPU länger |
| Kühler | Kühlung `height_mm` ↔ Gehäuse `max_cooler_height_mm` | Kühler höher (Wasserkühlung ohne Höhe: keine Prüfung) |
| Netzteil | Netzteil `psu_watts` ↔ (CPU-TDP + GPU-TDP + 120 W) × 1,4 | Netzteil schwächer |

Fehlt ein Wert, gilt die Kombination als **„ungeprüft“**, nie als „passt“. Geprüft wird in beide Richtungen, zum Beispiel
sperrt eine AM5-CPU LGA1700-Boards und ein kurzes Gehäuse lange Grafikkarten.

### Daten in der JTL-Wawi pflegen

Pro Komponenten-Artikel (der Artikel hinter der Konfigurator-Komponente) gibt es zwei Wege:

1. **Funktionsattribut** `pchm_<feld>` hat Vorrang. Beispiele: `pchm_socket = AM5`, `pchm_tdp_watts = 120`,
   `pchm_length_mm = 304`, `pchm_max_gpu_length_mm = 365`, `pchm_psu_watts = 850`, `pchm_ram_type = DDR5`,
   `pchm_height_mm = 155`, `pchm_max_cooler_height_mm = 170`.
   Optional `pchm_component_type` (CPU, GPU, RAM, SSD, Mainboard, Netzteil, Kühlung, Gehäuse, Windows).
2. **Merkmal**: Die Merkmal-Namen stehen kommagetrennt in den Einstellungen (Standard z. B. „Sockel, CPU-Sockel,
   Prozessorsockel“). Werte werden normalisiert: „AM 5“ → `AM5`, „120 W“ → `120`.

Der Typ einer Gruppe kommt aus `pchm_component_type`, sonst aus dem Gruppennamen („Prozessor“, „Grafikkarte“ …),
sonst aus der Mehrheit der Artikelnamen. Die Erkennungsregeln sind 1:1 aus `_hardware.php` übernommen.

### Anbindung für Plugins

* `window.PCHMKonfigurator.data` enthält Gruppen und Komponenten. `window.PCHMKonfigurator.update()` rechnet neu.
* `window.PCHMKonfiguratorRules` ist die reine Regel-Logik (`check`, `evaluate`, `psuNeed`) ohne DOM.
* `window.pchmConfig.konfigurator` enthält die Einstellungen, `window.pchmConfig.texts` die Texte (`cfg*`).

---

## 5. Entwicklung

### Aufbau

```
PCHM/
├── template.xml            Einstellungen (Parent = NOVA, Theme „pchm“)
├── Bootstrap.php           Smarty-Plugins (pchm_text, pchm_cfg_data, pchm_css_vars, …), Texte, Konfigurator-Daten
├── js/custom.js            Frontend (von NOVA automatisch geladen)
├── js/pchm-konfigurator.js Konfigurator: Regeln + Oberfläche (nur auf Konfigurator-Artikeln)
├── layout/, basket/, page/, productlist/, productdetails/, snippets/
│                           NOVA-Block-Erweiterungen (gleiche Dateinamen wie in NOVA)
├── snippets/pchm/          eigene Bausteine (Topbar, Seitenleisten, Startseite, cfg_top/cfg_bottom …)
└── themes/pchm/
    ├── sass/               _variables, _components, _components_crit, _fonts, pchm.scss, pchm_crit.scss
    ├── fonts/              WOFF2 (Manrope, Roboto Condensed, Inter – SIL OFL, Lizenzen liegen bei)
    ├── pchm.css, pchm_crit.css   kompiliert (crit wird inline im <head> ausgegeben)
    └── custom.css          CSS-Ergänzungen ohne Kompilieren
```

### Überschriebene NOVA-Blöcke

| Datei | Blöcke |
|---|---|
| `layout/header.tpl` | `layout-header-head-resources-crit`, `…-preload`, `layout-header-head-resources` (append), `layout-header-body-tag` (Klassen ergänzt), `layout-header-header` (prepend/append) |
| `layout/header_top_bar.tpl` | `layout-header-top-bar` (append) |
| `layout/header_nav_icons.tpl` | `layout-header-nav-icons-include-header-shop-nav-account` (prepend) |
| `layout/header_shop_nav_account.tpl` | `layout-header-shop-nav-account` (ersetzt, nur mit Konto-Seitenleiste) |
| `layout/header_categories.tpl` | `layout-header-categories-include-include-categories-body` (append) |
| `layout/footer.tpl` | `layout-footer-boxes`, `layout-footer-additional`, `layout-footer-js` (append), `layout-footer-io-path` (prepend) |
| `basket/cart_dropdown_label.tpl` | `basket-cart-dropdown-label` (ersetzt, nur mit Side-Basket) |
| `snippets/categories_mega_recursive.tpl` | `…-max-subsub-items` (ersetzt), `…-child-categories` (append) |
| `page/index.tpl` | `page-index` (NOVA-Inhalt per `{$smarty.block.parent}`) |
| `productlist/index.tpl` | `…-header-grid-gallery`, `…-include-productlist-footer` (append) |
| `productlist/header.tpl` | `productlist-header-description-category`, `productlist-header-subcategories` |
| `productlist/item_slider.tpl` | `productlist-item-slider-include-price` (append) |
| `productdetails/details.tpl` | `productdetails-details-info` (append), `…-include-tabs` (prepend), `…-content-not-quickview` (append) |
| `productdetails/price.tpl` | `productdetails-price-snippets` (prepend), `productdetails-price-detail-bulk-price-body` |
| `productdetails/config_options_list.tpl`, `…_gallery.tpl` | `productdetails-config-options` (NOVA-Inhalt per `{$smarty.block.parent}`, davor/danach PCHM) |

### Prüfen

```
cd tests/pchm && ./run-tests.sh            # gegen JTL-Shop v5.8.1
./run-tests.sh v5.9.0                      # gegen eine andere Version
```

Vor jedem Shop-Update ausführen: Kompilieren, Rendern, SCSS, Konfigurator-Regeln, Browser, NOVA-Blöcke, Smarty 5.

### Theme kompilieren

* **Im Shop:** JTL kompiliert `sass/pchm.scss` beim Speichern der NOVA-Sektionen „Farben“/„Custom Sass“.
  Die PCHM-Farben wirken ohne Kompilieren (CSS-Variablen).
* **Lokal:** `templates/NOVA` neben `templates/PCHM` legen, dann `cd tests/pchm && npm run build:css`.

### Weitere Schriften

1. `npm install @fontsource/<schrift>` ausführen und die WOFF2-Dateien (latin, latin-ext) nach `themes/pchm/fonts/` kopieren.
2. `@font-face` in `sass/_fonts.scss` ergänzen (Familienname `PCHM <Name>`).
3. Schrift in `Bootstrap.php` (`FONTS`) und `template.xml` eintragen.
4. Neu kompilieren.
