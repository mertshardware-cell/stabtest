# PCHMTechnik – NOVA-Child-Template für PCHM.de

Dieses Child-Template bildet die Funktionen des bisherigen **SALEPIX „Technik“-Templates** auf Basis des
JTL-Standard-Templates **NOVA** nach. Ziel: Ende des Jahres auf NOVA umstellen, ohne die gewohnten Funktionen zu verlieren,
und künftige NOVA-Updates weiter mitnehmen können.

* **Basis:** NOVA (Theme „clear“), entwickelt und getestet gegen **JTL-Shop / NOVA 5.8.1**
* **Mindestversion:** JTL-Shop 5.5 (`MinShopVersion` in `template.xml`)
* **Prinzip:** Es werden ausschließlich **NOVA-Blöcke ergänzt** (`append`/`prepend`) oder gezielt ersetzt. Kein NOVA-Code wird
  kopiert, NOVA-Updates kommen also weiterhin an.
* **Kein Code des Technik-Templates:** Alles ist neu geschrieben. Es werden keine Lizenzdateien von SALEPIX benötigt.

---

## 1. Installation

1. Den Ordner **`PCHMTechnik`** unverändert nach `<shop>/templates/PCHMTechnik/` hochladen.
   **Der Ordnername muss exakt `PCHMTechnik` lauten**, sonst passt der PHP-Namespace in `Bootstrap.php` nicht
   (`namespace Template\PCHMTechnik;`).
2. NOVA muss unter `<shop>/templates/NOVA/` vorhanden sein (Standard bei JTL-Shop 5).
3. Backend → *Darstellung → Templates* → **PCHMTechnik** aktivieren (ggf. vorher auf dem Testsystem / per Vorschau).
4. Einstellungen unter *Templates → PCHMTechnik → Einstellungen* prüfen (Abschnitte „PCHM Technik – …“).
5. Cache leeren (*System → Cache*), damit Template-Einstellungen und kompilierte Templates neu geladen werden.

> Empfehlung: Zuerst auf einer Kopie des Shops (Staging) aktivieren, alle Seiten durchklicken, dann live schalten.

---

## 2. Funktionsabgleich Technik-Template → PCHMTechnik

| Technik-Template | PCHMTechnik (NOVA) | Einstellung |
|---|---|---|
| Hinweisleiste über dem Header | Hinweisleiste mit Link, optionalem Countdown, wegklickbar (7 Tage) | *Header → Hinweisleiste* |
| USP-Leiste / Topbar | USP-Leiste (4 Einträge, Icons) in der Topbar, auf Smartphones rotierend | *Header → USP-Leiste* |
| Hotline / Servicezeiten | Hotline + Servicezeiten in Topbar, mobilem Menü und Footer | *Header → Kontakt* |
| Sticky Header je Gerät | Sticky an/aus für Smartphone / Tablet / Desktop | *Header → Sticky Header* |
| Megamenü (Tiefe, Hover/Klick) | Tiefe 1–4 Ebenen, Öffnen per Hover oder Klick, Spaltenanzahl, „Alle anzeigen“-Link | *Megamenü* |
| Side-Basket (Seitenwarenkorb) | Warenkorb als Seitenleiste, öffnet nach „In den Warenkorb“, Fortschritt „Noch X € bis versandkostenfrei“ | *Header → Side-Basket*, *Warenkorb* |
| „In den Warenkorb“ mit einem Klick | Auf der Artikelseite per AJAX ohne Seitenneuladen (außer Konfigurator/Uploads), Liste: NOVA-Standard | automatisch mit Side-Basket |
| Login/Registrierung im Seitenmenü | Konto-Seitenleiste (Login, Passwort vergessen, Registrieren bzw. Kontolinks) | *Header → Anmeldung als Seitenleiste* |
| Benachrichtigungsglocke (News) | Glocke im Header, Seitenleiste mit den neuesten News, Markierung „ungelesen“ | *Header → Neuigkeiten-Glocke* |
| Netto/Brutto (B2B/B2C) | Umschalter Privat/Gewerbe, rechnet alle Artikelpreise inkl. Staffelpreise um (reine Anzeige) | *Header → Umschalter* |
| Staffelpreise netto/brutto | Staffelpreistabelle folgt dem Umschalter | automatisch |
| Farben & Schriften ohne CSS | 17 Farben + 10 lokal eingebundene Schriften (DSGVO), Eckenradius, Seitenbreite, Großbuchstaben | *Farben & Schriften* |
| Mehrsprachige Texte | Alle Texte als `de=… || en=…` oder `lang:variable` (JTL-Sprachvariable) | alle Textfelder |
| Startseite: Preisaktionen-Slider | Slider (Sonderangebote/Top-Angebote/Neu/Bestseller) mit Countdown bis Aktionsende | *Startseite → Preisaktionen* |
| Startseite: Banner / BigSlider | NOVA-Slider & Banner (Backend *Inhalte → Slider/Banner*), Position „zwischen Header und Content“ in NOVA-Theme-Einstellungen | NOVA *Theme* |
| Startseite: Brandslider | Hersteller-Laufband mit Logos (Graustufen → farbig) | *Startseite → Hersteller-Slider* |
| Parallax-Counter | Countdown-Block mit Parallax-Hintergrund, blendet sich nach Ablauf aus | *Startseite → Countdown-Block* |
| Kategorie-Kacheln | Kacheln für frei wählbare oder Hauptkategorien | *Startseite → Kategorie-Kacheln* |
| USP-Kacheln | USP-Kacheln auf der Startseite (nutzt die USPs aus „Header“) | *Startseite → USP-Kacheln* |
| Reihenfolge der Startseitenelemente | frei sortierbar (`usps,categories,specials,countdown,nova,brands`) | *Startseite → Reihenfolge* |
| OnPage Composer / eigene Inhalte | NOVA-OPC vollständig nutzbar (alle Mountpoints bleiben erhalten) | OPC |
| Kategoriebeschreibung oben/unten | oben, unten oder aus, lange Texte mit „Weiterlesen“ | *Artikelliste* |
| Unterkategorien ein/aus | ja/nein | *Artikelliste* |
| Artikel pro Zeile je Gerät | Smartphone 1–2, Tablet 2–4, Desktop 2–6 | *Artikelliste* |
| Galerie/Liste, Filter | NOVA-Standard (Galerie/Liste, Filter in Seitenleiste oder Popup) + Filterleiste ein-/ausklappbar | NOVA *Listen- und Galerieansicht*, *Artikelliste* |
| Tab-Schnellnavigation | Sticky-Sprungnavigation zu Beschreibung, Bewertungen, Zubehör … | *Artikeldetails* |
| Warenkorb-Button mit Gesamtbetrag | Button zeigt Menge × Preis (auch mit Konfigurator/Variationen) | *Artikeldetails* |
| Dokumente in der Sidebar | PDF-/Download-Mediendateien direkt neben dem Kaufbereich | *Artikeldetails* |
| Passende Produkte | NOVA-Cross-Selling/Zubehör-Slider (in Sprungnavigation verlinkt) | JTL-Wawi Cross-Selling |
| Variantenkombinationen als Tabelle | NOVA-Variationsmatrix | Backend *Artikeldetails → Warenkorbmatrix* |
| – | Sticky-Kaufleiste beim Scrollen (Preis + Button) | *Artikeldetails* |
| – | Vertrauens-Box unter dem Warenkorb-Button | *Artikeldetails* |
| Footer: Zahlungsarten | Zahlungs-Icons (PayPal, Visa, Mastercard, Klarna, Apple/Google Pay, SEPA, Vorkasse …) | *Footer* |
| Footer: Versandarten | Versandpartner (DHL, DPD, UPS, GLS, Hermes …) | *Footer* |
| Footer: Kontakt | Kontaktblock mit Telefon, Servicezeiten, E-Mail, Adresse | *Footer* |
| Footer: Newsletter, Social Media | NOVA-Standard + zusätzliche Kanäle (Discord, Twitch, X, Steam …) | NOVA *Footer*, *Footer* |
| Footer: freier Inhalt | Freies HTML (z. B. Trusted-Shops-Siegel) | *Footer* |
| Nach-oben-Button | NOVA-Standard | NOVA *Theme* |

### Bewusst nicht (oder anders) nachgebaut

* **OnePageCheckout (SALEPIX-Plugin):** Das ist ein eigenes, lizenzpflichtiges Plugin und kein Teil des Templates.
  Mit NOVA läuft der JTL-Standard-Checkout. Wer einen einseitigen Checkout behalten will, braucht ein Checkout-Plugin
  (z. B. aus dem JTL-Extension-Store). Die Plugin-Funktionen „KI-Auswertung“, „Warenkorb-Erinnerung“ und „Abbruchstatistik“
  gehören ebenfalls zum Plugin.
* **SALEPIX-OPC-Portlets:** Inhalte, die mit Portlets des SALEPIX-Templaters gebaut wurden, müssen mit den
  **Standard-OPC-Portlets** von JTL (Produkt-Stream, Video, Tabs, Accordion, Bild, Banner …) neu angelegt werden.
* **1.100 Google-Fonts:** Enthalten sind 10 Schriften, lokal eingebunden (DSGVO-konform). Weitere Schriften lassen sich
  über `@fontsource` ergänzen (siehe Abschnitt 5).
* **Netto/Brutto-Umschalter:** Er ändert nur die **Anzeige** der Artikelpreise. Warenkorb, Kasse und Steuerberechnung
  richten sich weiter nach der Kundengruppe. Dadurch gibt es keine Rundungs- oder Steuerabweichungen gegenüber der JTL-Wawi.
* **Schnellbestellung mehrerer Artikelnummern:** NOVA bietet die Box „Direktkauf“ (Artikelnummer/EAN → Warenkorb),
  die in der Boxenverwaltung aktiviert werden kann.

---

## 3. Migrations-Checkliste (Technik → PCHMTechnik)

1. **Einstellungen übertragen:** Farben, USPs, Hotline, Hinweistexte, Footer-Inhalte aus dem SALEPIX-Templater
   in *Templates → PCHMTechnik → Einstellungen* übernehmen.
2. **Logo:** Backend *Darstellung → Logo* (NOVA verwendet das Shop-Logo, Höhe unter NOVA *Header → Logo Höhe*).
3. **Slider/Banner:** in *Inhalte → Slider* bzw. *Banner* prüfen und der Startseite zuweisen.
4. **Boxen:** Boxenverwaltung prüfen (NOVA: linke Seitenleiste und Footer-Boxen).
5. **OPC-Inhalte:** Seiten mit SALEPIX-Portlets öffnen und mit JTL-Portlets neu aufbauen.
6. **Sprachvariablen:** Englische Texte in den Einstellungen im Format `de=… || en=…` pflegen.
7. **Plugins:** SALEPIX-Templater/OnePageCheckout erst nach erfolgreicher Umstellung deaktivieren. Trusted-Shops-,
   Zahlungs- und Konfigurator-Plugins funktionieren mit NOVA unverändert.
8. **Tests:** Startseite, Kategorie, Artikel (einfach, Variation, Konfigurator-PC), Suche, Warenkorb, Kasse, Kundenkonto,
   Newsletter, mobil (Smartphone/Tablet), Englisch.
9. **Cache leeren**, Seiten mit PageSpeed Insights prüfen.

---

## 4. Einstellungen im Detail

### Mehrsprachige Texte
Alle Textfelder akzeptieren:

* einfachen Text: `Kostenloser Versand ab 100 €`
* Sprachvarianten: `de=Kostenloser Versand ab 100 € || en=Free shipping from €100`
  (ISO-Kürzel `de`, `en`, `fr`, `nl`, `it`, `es`, `pl`, `cs`, `da`, `sv` oder JTL-Kürzel `ger`, `eng` …; Fallback = erster Eintrag)
* JTL-Sprachvariable: `lang:meine_variable` (Sektion `custom`) oder `lang:sektion:variable`

### Eingebaute Texte anpassen
Texte wie „Warenkorb“, „Noch %s bis zum kostenlosen Versand“ oder „Brutto/Netto“ sind auf Deutsch/Englisch eingebaut
(`Bootstrap.php`, Konstante `TEXTS`). Jeder Text lässt sich ohne Code-Änderung überschreiben:
Backend *Sprachverwaltung* → Sektion **custom** → Variable **`pchm_<schlüssel>`** anlegen, z. B. `pchm_cart`,
`pchm_freeShippingLeft`, `pchm_priceNet`.

### Farben
Die Farben unter „PCHM Technik – Farben & Schriften“ werden als CSS-Variablen ausgegeben und wirken **sofort,
ohne Kompilieren**. Die NOVA-Sektion „Farben“ (kompiliert das Theme neu) kann zusätzlich genutzt werden, um auch
selten genutzte NOVA-Elemente exakt einzufärben. Am besten dort dieselbe Primär-/Sekundärfarbe eintragen.

---

## 5. Entwicklung

### Aufbau

```
PCHMTechnik/
├── template.xml            Einstellungen (Parent = NOVA)
├── Bootstrap.php           Smarty-Funktionen (pchm_text, pchm_lang, pchm_news, pchm_css_vars, …)
├── js/custom.js            Frontend-Funktionen (wird von NOVA automatisch geladen)
├── layout/, basket/, page/, productlist/, productdetails/, snippets/
│                           NOVA-Block-Erweiterungen (gleiche Dateinamen wie in NOVA)
├── snippets/pchm/          neue Bausteine (Topbar, Seitenleisten, Startseitenbereiche, …)
└── themes/pchm-technik/
    ├── sass/               _variables, _pchm (Styles), _pchm_crit (Critical CSS), _fonts
    ├── fonts/              lokal eingebundene WOFF2-Schriften (SIL OFL, Lizenzen liegen bei)
    ├── pchm-technik.css    kompiliert
    ├── pchm-technik_crit.css kompiliert (wird inline im <head> ausgegeben)
    └── custom.css          eigene CSS-Ergänzungen ohne Kompilieren
```

### Überschriebene NOVA-Blöcke

| Datei | Blöcke |
|---|---|
| `layout/header.tpl` | `layout-header-head-resources-crit` (append), `…-preload` (append), `layout-header-head-resources` (append), `layout-header-body-tag` (Klassen ergänzt, NOVA-Inhalt bleibt), `layout-header-header` (prepend/append) |
| `layout/header_top_bar.tpl` | `layout-header-top-bar` (append) |
| `layout/header_nav_icons.tpl` | `layout-header-nav-icons-include-header-shop-nav-account` (prepend) |
| `layout/header_shop_nav_account.tpl` | `layout-header-shop-nav-account` (ersetzt, wenn Konto-Seitenleiste aktiv) |
| `layout/header_categories.tpl` | `layout-header-categories-include-include-categories-body` (append) |
| `layout/footer.tpl` | `layout-footer-boxes` (append), `layout-footer-additional` (append), `layout-footer-io-path` (prepend) |
| `basket/cart_dropdown_label.tpl` | `basket-cart-dropdown-label` (ersetzt, wenn Side-Basket aktiv) |
| `snippets/categories_mega_recursive.tpl` | `…-max-subsub-items` (ersetzt), `…-child-categories` (append) |
| `page/index.tpl` | `page-index` (NOVA-Inhalt wird per `{$smarty.block.parent}` eingebettet) |
| `productlist/index.tpl` | `…-header-grid-gallery` (nach NOVA), `…-include-productlist-footer` (append) |
| `productlist/header.tpl` | `productlist-header-description-category`, `productlist-header-subcategories` |
| `productlist/item_slider.tpl` | `productlist-item-slider-include-price` (append) |
| `productdetails/details.tpl` | `productdetails-details-info` (append), `…-include-tabs` (prepend), `…-content-not-quickview` (append) |
| `productdetails/price.tpl` | `productdetails-price-snippets` (prepend), `productdetails-price-detail-bulk-price-body` |

Nach einem NOVA-Update prüfen, ob diese Blocknamen noch existieren (die Tests in `tests/pchm-technik` tun das automatisch).

### Theme neu kompilieren
* **Im Shop:** JTL kompiliert `sass/pchm-technik.scss` automatisch beim Speichern der Template-Einstellungen, wenn in den
  NOVA-Sektionen „Farben“ oder „Custom Sass“ etwas geändert wurde (getestet mit scssphp 1.13 und 2.1).
* **Lokal:** Template neben NOVA ablegen (`templates/NOVA`, `templates/PCHMTechnik`), dann
  ```
  npx sass --quiet --style=expanded themes/pchm-technik/sass/pchm-technik.scss themes/pchm-technik/pchm-technik.css
  npx sass --quiet --no-source-map --style=compressed themes/pchm-technik/sass/pchm-technik_crit.scss themes/pchm-technik/pchm-technik_crit.css
  ```

### Weitere Schriften
`npm install @fontsource/<schrift>`, WOFF2-Dateien (latin, latin-ext) nach `themes/pchm-technik/fonts/` kopieren,
`@font-face` in `sass/_fonts.scss` ergänzen (Familienname `PCHM <Name>`), Schrift in `Bootstrap.php` (`FONTS`) und
`template.xml` (Optionen) eintragen, neu kompilieren.
