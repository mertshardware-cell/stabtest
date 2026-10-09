-- Einstellungen von PCHMTechnik nach PCHM übernehmen (JTL-Shop 5, Tabelle ttemplateeinstellungen)
--
-- Ablauf: 1. PCHM hochladen und im Backend aktivieren (JTL legt dabei die Standardwerte an).
--         2. Dieses Skript in der Shop-Datenbank ausführen (phpMyAdmin/Adminer, vorher Datenbank sichern).
--         3. Cache leeren (System → Cache) und Einstellungen unter Templates → PCHM prüfen.
--
-- Die Setting-Keys sind in beiden Templates gleich. Bewusst NICHT übernommen:
--   * pchm_design  – PCHM bringt die neue Designlinie (DESIGN.md: Violett nur auf Bedienelementen, schlicht,
--                    Manrope/Roboto Condensed). Wer die alten Farben behalten will, Abschnitt unten einkommentieren.
--   * theme        – Theme heißt jetzt „pchm“ statt „pchm-technik“.
-- NOVA-eigene Abschnitte (header, footer, productdetails, colors …) werden mit übernommen.

-- Sicherung der betroffenen Zeilen
CREATE TABLE IF NOT EXISTS ttemplateeinstellungen_backup_pchm AS
    SELECT * FROM ttemplateeinstellungen WHERE cTemplate IN ('PCHM', 'PCHMTechnik');

DELETE FROM ttemplateeinstellungen
 WHERE cTemplate = 'PCHM'
   AND cSektion IN (SELECT cSektion FROM (
        SELECT DISTINCT cSektion FROM ttemplateeinstellungen
         WHERE cTemplate = 'PCHMTechnik' AND cSektion NOT IN ('pchm_design', 'theme')
   ) AS s);

INSERT INTO ttemplateeinstellungen (cTemplate, cSektion, cName, cWert)
SELECT 'PCHM', cSektion, cName, cWert
  FROM ttemplateeinstellungen
 WHERE cTemplate = 'PCHMTechnik'
   AND cSektion NOT IN ('pchm_design', 'theme');

-- Optional: alte Farben/Schriften ebenfalls übernehmen (heading_uppercase entfällt in PCHM)
-- DELETE FROM ttemplateeinstellungen WHERE cTemplate = 'PCHM' AND cSektion = 'pchm_design'
--    AND cName IN (SELECT cName FROM (SELECT cName FROM ttemplateeinstellungen
--                  WHERE cTemplate = 'PCHMTechnik' AND cSektion = 'pchm_design') AS d);
-- INSERT INTO ttemplateeinstellungen (cTemplate, cSektion, cName, cWert)
-- SELECT 'PCHM', cSektion, cName, cWert FROM ttemplateeinstellungen
--  WHERE cTemplate = 'PCHMTechnik' AND cSektion = 'pchm_design' AND cName <> 'heading_uppercase';
