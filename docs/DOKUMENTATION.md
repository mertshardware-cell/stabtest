# PCHM-Shop-Template – Gesamtdokumentation

Stand: 09.10.2026 · Repository `mertshardware-cell/stabtest`, Branch `claude/adoring-cannon-l1qi93`

Dieses Dokument fasst alles zusammen, was für das neue Shop-Template von PCHM.de gebaut wurde:
Ziel, Ablauf, Ergebnis, Bedienung, Tests und offene Punkte. Details stehen in den verlinkten Dateien.

---

## 1. Auftrag und Ergebnis

| Schritt | Auftrag | Ergebnis |
|---|---|---|
| 1 | Das SALEPIX-„Technik“-Template auf Basis von JTL-NOVA nachbauen, damit Ende des Jahres auf NOVA umgestellt werden kann, ohne Funktionen zu verlieren | Child-Template `PCHMTechnik` (Commit `fff6549`) |
| 2 | Langfristig weg vom Technik-Template: ein komplett eigenes, updatesicheres Template nach aktuellem JTL-Standard, sinnvoll verbunden mit dem Konfigurator; eigene bisherige Konfigurator-Arbeit einbeziehen; Gedächtnis-Doku pflegen | Eigenes Template `PCHM` 2.0.0 mit Kompatibilitätsprüfung (Commit `289bc85`), `PCHMTechnik` geht darin auf; Gedächtnis-Doku des 3D-Plugins ergänzt (Companion-Repo, Commit `2cbe5b5`) |

**Grundentscheidung:** PCHM ist ein **Child-Template von NOVA**. Das ist laut JTL-Doku der einzige updatesichere Weg.
Ein komplett eigenständiges Template müsste jedes Shop-Update von Hand nachziehen.
PCHM ergänzt NOVA nur an definierten Stellen („Blöcke“) und kopiert keinen NOVA-Code.

---

## 2. Was im Repository liegt

```
stabtest/
├── README.md                     Übersicht
├── CLAUDE.md                     Projektgedächtnis (Befehle, Zusammenhänge, Stand, offene Punkte)
├── REGELN.md                     Verbindliche Regeln: JTL-Updatesicherheit, Smarty 5, Design, Konfigurator
├── docs/
│   ├── DOKUMENTATION.md          dieses Dokument
│   └── migration-pchmtechnik.sql Einstellungen von PCHMTechnik nach PCHM übernehmen
├── templates/PCHM/               das Template (in den Shop nach templates/PCHM hochladen)
│   ├── README.md                 Installation, Funktionen, Konfigurator, Entwicklung
│   ├── template.xml              Einstellungen (9 Abschnitte „PCHM – …“)
│   ├── Bootstrap.php             Smarty-Plugins, eingebaute Texte (de/en), Konfigurator-Daten
│   ├── js/custom.js              Shop-Funktionen (Side-Basket, Netto/Brutto, Megamenü …)
│   ├── js/pchm-konfigurator.js   Konfigurator: Regeln + Oberfläche
│   ├── layout/ basket/ page/ productlist/ productdetails/ snippets/   NOVA-Erweiterungen
│   └── themes/pchm/              SCSS, kompiliertes CSS, lokale Schriften
├── tests/pchm/                   automatische Prüfungen (run-tests.sh)
└── test/                         bestehende Stabilitätstest-Seite (unverändert)
```

---

## 3. Funktionen des Templates

### Übernommen aus dem Technik-Template (neu geschrieben, kein SALEPIX-Code)

* Header:
  * Hinweisleiste mit Countdown
  * USP-Leiste
  * Hotline
  * Sticky-Header je Gerät
  * Side-Basket mit Fortschritt „Noch X € bis versandkostenfrei“
  * Konto- und News-Seitenleiste
  * Umschalter Privat/Gewerbe (Netto/Brutto)
* Megamenü: Tiefe 1–4, Hover oder Klick, Spalten, „Alle anzeigen“.
* Startseite:
  * USP-Kacheln
  * Preisaktionen mit Countdown
  * Kategorie-Kacheln
  * Countdown-Block
  * Hersteller-Slider
  * Reihenfolge frei wählbar
* Artikelliste:
  * Beschreibung oben, unten oder einklappbar
  * Unterkategorien
  * Artikel pro Zeile je Gerät
  * einklappbare Filterleiste
* Artikeldetails:
  * Sprungnavigation
  * Sticky-Kaufleiste
  * Gesamtbetrag im Warenkorb-Button
  * Dokumente
  * Vertrauens-Box
* Footer: Kontakt, Zahlungs- und Versand-Icons, weitere Social-Kanäle, freies HTML.
* Alle Texte mehrsprachig (`de=… || en=…` oder `lang:variable`). Eingebaute Texte sind über Sprachvariablen
  `pchm_<schlüssel>` (Sektion `custom`) überschreibbar.

### Neu in PCHM

* **Designlinie aus `DESIGN.md`:**
  * Violett (`#9013FE`) nur auf Bedienelementen, sonst neutrale Grautöne
  * keine Schatten und Verläufe
  * Radius 6/8 px
  * keine Versal-Überschriften
  * Schriften Manrope und Roboto Condensed, lokal eingebunden (DSGVO)
* **Konfigurator mit Kompatibilitätsprüfung**, siehe Abschnitt 4.
* **Updatesicherheit geprüft** gegen JTL-Shop 5.5.0, 5.6.0, 5.7.0, 5.8.0, 5.8.1 und develop (5.9.0-alpha).
  Die Templates sind für Smarty 5 vorbereitet.

### Bewusst nicht Teil des Templates

* **OnePageCheckout (SALEPIX):** eigenes, lizenzpflichtiges Plugin. Mit NOVA läuft der JTL-Checkout.
* **SALEPIX-OPC-Portlets:** Diese Inhalte mit den JTL-Standard-Portlets neu anlegen.
* **Netto/Brutto-Umschalter:** ändert nur die Anzeige. Steuer und Warenkorb folgen der Kundengruppe.

---

## 4. Konfigurator

### Einbezogene Vorarbeit

* **Companion App** (`mertshardware-cell/pchm-companion-app`):
  * `api/_hardware.php` enthält die Komponentenerkennung und Kompatibilitätsregeln, `hardware-test.php` die Testfälle.
  * `shop-plugin-contract.md` und `jtl-field-mapping.csv` beschreiben die Datenfelder.
  * `DESIGN.md` beschreibt die Designlinie.
* **3D-Plugin** `pchm_konfigurator3d` (Companion App, Ordner `konfigurator-3d/`, Branch `claude/funny-darwin-qph2us`).
  Es liest die JTL-Formularfelder `item[Gruppe][]` und `item_quantity` aus der Seite.
* Der Chat „Neuer Konfigurator mit Kompatibil. (fork)“ war aus der Cloud-Umgebung **nicht lesbar**.

### Funktionsweise

* Der JTL-Konfigurator bleibt **unverändert**: Formularfelder, Preise, Warenkorb, „Konfiguration bearbeiten“.
  So funktionieren JTL, NOVA und das 3D-Plugin weiter.
* PCHM ergänzt auf Artikeln mit Konfigurator:
  * **Schrittnavigation** mit Fortschritt („3 von 6 Gruppen gewählt“), Pflichtgruppen markiert, Sprung zur Gruppe
  * **Kompatibilitätsbox** in der Seitenleiste: Konflikte, fehlende Angaben, bestandene Prüfungen, Netzteil-Empfehlung
  * nicht passende Optionen werden **markiert** (mit Begründung) oder **gesperrt**, je nach Einstellung
  * **Warenkorb-Sperre** bei Konflikt (abschaltbar)
  * **fester Platz für die 3D-Vorschau** (`<div data-pchm-3d>`)
  * technische Daten aller Komponenten als JSON (`#pchm-cfg-data`) für Plugins
* Das Skript wird nur auf Artikeln mit Konfigurator geladen.

### Regeln (identisch mit `_hardware.php`)

| Prüfung | Daten | Konflikt, wenn |
|---|---|---|
| Sockel | CPU `socket` ↔ Mainboard `socket` | ungleich |
| RAM | RAM `ram_type` ↔ Mainboard `ram_type` | ungleich |
| Grafikkarte | GPU `length_mm` ↔ Gehäuse `max_gpu_length_mm` | GPU länger |
| Kühler | Kühlung `height_mm` ↔ Gehäuse `max_cooler_height_mm` | Kühler höher |
| Netzteil | `psu_watts` ↔ (CPU-TDP + GPU-TDP + 120 W) × 1,4 | Netzteil schwächer |

Fehlt ein Wert, lautet das Ergebnis „ungeprüft“, nie „passt“. Geprüft wird in beide Richtungen
(eine AM5-CPU sperrt LGA1700-Boards, ein kurzes Gehäuse sperrt lange Grafikkarten).

### Datenpflege in der JTL-Wawi (nötig!)

Pro Komponenten-Artikel gibt es zwei Wege:

1. **Funktionsattribut** (hat Vorrang): `pchm_socket`, `pchm_ram_type`, `pchm_tdp_watts`, `pchm_length_mm`,
   `pchm_height_mm`, `pchm_max_gpu_length_mm`, `pchm_max_cooler_height_mm`, `pchm_psu_watts`, optional
   `pchm_component_type`.
2. **Merkmal**: Welche Merkmal-Namen gelesen werden, steht in *Templates → PCHM → PCHM – Konfigurator*.

Werte werden vereinheitlicht („AM 5“ → `AM5`, „120 W“ → `120`).

### Einstellungen (*PCHM – Konfigurator*)

| Einstellung | Wirkung |
|---|---|
| Gruppen-Navigation | Schrittnavigation an/aus |
| Kompatibilitätsprüfung | Prüfung an/aus |
| Nicht kompatible Optionen | markieren oder sperren |
| Warenkorb bei Konflikt sperren | an/aus |
| Netzteil-Empfehlung | an/aus |
| Merkmal-Namen | kommagetrennte Merkmal-Namen je Datenfeld |
| 3D-Vorschau | automatisch / über den Gruppen / aus |

---

## 5. Installation und Umstieg

1. Ordner `templates/PCHM` nach `<shop>/templates/PCHM/` hochladen. Der Ordnername muss exakt `PCHM` sein.
2. Backend → *Darstellung → Templates* → **PCHM** aktivieren, zuerst auf dem Testsystem.
3. **Datenbank sichern**, dann `docs/migration-pchmtechnik.sql` ausführen. Das Skript übernimmt alle Einstellungen
   aus PCHMTechnik außer dem Design.
4. Einstellungen unter *Templates → PCHM* prüfen, *System → Cache* leeren.
5. Kompatibilitätsdaten in der Wawi pflegen (Abschnitt 4).
6. Testen:
   * Startseite, Kategorie, Artikel (einfach, Variation, Konfigurator-PC)
   * Warenkorb, Kasse, Konto
   * mobil, Englisch
7. Erst danach SALEPIX-Templater/OnePageCheckout bzw. PCHMTechnik deaktivieren.

---

## 6. Tests und Updatesicherheit

Aufruf: `cd tests/pchm && ./run-tests.sh`. Für eine andere Version z. B. `./run-tests.sh v5.9.0`.

| Schritt | Prüfung | Stand |
|---|---|---|
| 1 | Smarty: alle Templates inkl. NOVA-Vererbung kompilieren (JTL-Smarty-Fork) | 37 Dateien, 0 Fehler |
| 2 | Smarty: Bausteine mit Testdaten und echter `Bootstrap.php` rendern | 46 Prüfungen grün |
| 3 | SCSS gegen NOVA kompilieren | grün |
| 4 | Konfigurator-Regeln = `_hardware.php` (Fälle aus `hardware-test.php`) | 15 Tests grün |
| 5a | Browser (Chromium): `custom.js` | 37 Prüfungen grün |
| 5b | Browser (Chromium): `pchm-konfigurator.js` auf NOVA-Konfigurator-Markup | 35 Prüfungen grün |
| 6a | Alle 30 überschriebenen NOVA-Blöcke existieren | 5.5.0 bis develop grün |
| 6b | Nur unter Smarty 5 freigegebene Modifier/Funktionen | grün |

Beim Testen gefundene und behobene Fehler:

* Die Kompatibilitätsbox saß nicht in der NOVA-Seitenleiste.
* Die Hervorhebung der Box bei gesperrtem Warenkorb verschwand sofort wieder.

**Vor jedem JTL-Shop-Update** die Tests gegen die neue Version laufen lassen. Schlagen 6a oder 6b fehl, müssen die
genannten Stellen angepasst werden, bevor das Update live geht.

---

## 7. Gedächtnis-Dokus

| Datei | Inhalt |
|---|---|
| `stabtest/CLAUDE.md` | Projektgedächtnis des Templates |
| `pchm-companion-app/konfigurator-3d/CLAUDE.md` (Branch `claude/funny-darwin-qph2us`) | ergänzt um Abschnitt „Shop-Template PCHM (Anbindung)“: Einbauplatz, Formularfelder, JSON-Daten, Akzentfarbe, Tests |

---

## 8. Offene Punkte (Entscheidung beim Inhaber)

1. **Shop-Violett** `#9013FE` an das App-Violett `#7C3AED` angleichen? Das geht über die Einstellung `color_primary`.
2. **Kompatibilitätsdaten** in der Wawi pflegen. Ohne Daten zeigt die Prüfung nur „ungeprüft“.
3. **Echte Gruppennamen** im Live-Shop gegenprüfen. pchm.de war aus der Cloud nicht erreichbar.
4. Beschlüsse aus dem Chat **„Neuer Konfigurator mit Kompatibil. (fork)“** nachreichen.
5. Optional fürs 3D-Plugin: Maße und Typ aus `#pchm-cfg-data` lesen statt aus Artikelnamen raten.

---

## 9. Versionen und Commits

| Repository | Branch | Commit | Inhalt |
|---|---|---|---|
| stabtest | `claude/adoring-cannon-l1qi93` | `fff6549` | PCHMTechnik (Nachbau Technik auf NOVA) |
| stabtest | `claude/adoring-cannon-l1qi93` | `289bc85` | Eigenes Template PCHM 2.0.0 mit Konfigurator |
| pchm-companion-app | `claude/funny-darwin-qph2us` | `2cbe5b5` | Gedächtnis-Doku: Anbindung an PCHM |

Es gibt keinen Pull Request; einer wird nur auf Wunsch erstellt.
