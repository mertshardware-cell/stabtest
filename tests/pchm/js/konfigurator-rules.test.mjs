// Paritätstests: Die Konfigurator-Regeln im Template (templates/PCHM/js/pchm-konfigurator.js)
// müssen dieselben Ergebnisse liefern wie api/_hardware.php der Companion App.
// Fälle und Zahlen 1:1 aus pchm-companion-app/tests/hardware-test.php (Abschnitt Kompatibilität).
// Aufruf: node --test tests/pchm/js/konfigurator-rules.test.mjs
import { test } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';

const file = path.resolve(path.dirname(new URL(import.meta.url).pathname), '../../../templates/PCHM/js/pchm-konfigurator.js');
const sandbox = { window: {}, document: { readyState: 'loading', addEventListener() {} } };
vm.runInNewContext(fs.readFileSync(file, 'utf8'), sandbox);
const Rules = sandbox.window.PCHMKonfiguratorRules;

const profile = {
    'Mainboard': { spec: { socket: 'AM5', ram_type: 'DDR5' } },
    'Netzteil': { spec: { psu_watts: 850 } },
    'Gehäuse': { spec: { max_gpu_length_mm: 360, max_cooler_height_mm: 165 } },
    'CPU': { spec: { tdp_watts: 120 } },
    'GPU': { spec: { tdp_watts: 360 } },
};
// Texte mit Platzhaltern, damit die Meldungen die Werte enthalten (im Shop kommen sie aus Bootstrap::TEXTS)
const T = new Proxy({}, { get: (_, key) => String(key) + ' {0} {1}' });
const check = (candidate, p = profile) => Rules.check(p, candidate, T);

test('Sockel: passender Sockel und ausreichendes Netzteil -> ok', () => {
    assert.equal(check({ component_type: 'CPU', socket: 'AM5', tdp_watts: 105 }).status, 'ok');
});

test('Sockel passt, aber Netzteil zu schwach (170 W-CPU) -> blocked, Sockel trotzdem geprüft', () => {
    const r = check({ component_type: 'CPU', socket: 'AM5', tdp_watts: 170 });
    assert.equal(r.status, 'blocked');
    assert.match(r.checks.join(' '), /AM5/);
});

test('falscher Sockel -> blocked und benannt', () => {
    const r = check({ component_type: 'CPU', socket: 'LGA1851', tdp_watts: 125 });
    assert.equal(r.status, 'blocked');
    assert.match(r.blockers.join(' '), /LGA1851/);
});

test('ohne gepflegte Daten -> unknown statt ok', () => {
    const r = check({ component_type: 'CPU', socket: 'AM5' }, { 'Mainboard': { spec: {} } });
    assert.equal(r.status, 'unknown');
    assert.ok(r.warnings.length > 0);
});

test('GPU passt ins Gehäuse', () => {
    assert.equal(check({ component_type: 'GPU', length_mm: 340, tdp_watts: 300 }).status, 'ok');
});

test('zu lange GPU -> blocked, nennt beide Maße', () => {
    const r = check({ component_type: 'GPU', length_mm: 400, tdp_watts: 300 });
    assert.equal(r.status, 'blocked');
    assert.match(r.blockers.join(' '), /400/);
    assert.match(r.blockers.join(' '), /360/);
});

test('zu hungrige GPU (575 W) -> blocked mit Empfehlung', () => {
    const r = check({ component_type: 'GPU', length_mm: 300, tdp_watts: 575 });
    assert.equal(r.status, 'blocked');
    assert.match(r.blockers.join(' '), /1141/);
});

test('Kandidaten-GPU ersetzt die alte (200 W) -> ok', () => {
    assert.equal(check({ component_type: 'GPU', length_mm: 250, tdp_watts: 200 }).status, 'ok');
});

test('Netzteilbedarf des Ist-Builds = ceil((120 + 360 + 120) * 1.4)', () => {
    assert.equal(Rules.psuNeed(profile, null), Math.ceil((120 + 360 + 120) * 1.4));
    assert.equal(Rules.psuNeed({ CPU: { spec: {} } }, null), null);
});

test('RAM-Typ passend/falsch', () => {
    assert.equal(check({ component_type: 'RAM', ram_type: 'DDR5' }).status, 'ok');
    assert.equal(check({ component_type: 'RAM', ram_type: 'DDR4' }).status, 'blocked');
});

test('Kühler-Bauhöhe passend/zu hoch', () => {
    assert.equal(check({ component_type: 'Kühlung', height_mm: 160 }).status, 'ok');
    assert.equal(check({ component_type: 'Kühlung', height_mm: 180 }).status, 'blocked');
});

// Erweiterungen für den Shop-Konfigurator (Gegenrichtung; in der App nicht nötig)
test('Shop: Mainboard-Kandidat gegen gewählte CPU und RAM', () => {
    const p = { CPU: { spec: { socket: 'AM5' } }, RAM: { spec: { ram_type: 'DDR5' } } };
    assert.equal(check({ component_type: 'Mainboard', socket: 'AM5', ram_type: 'DDR5' }, p).status, 'ok');
    assert.equal(check({ component_type: 'Mainboard', socket: 'LGA1851', ram_type: 'DDR5' }, p).status, 'blocked');
});

test('Shop: Gehäuse-Kandidat gegen gewählte GPU und Kühler', () => {
    const p = { GPU: { spec: { length_mm: 340 } }, 'Kühlung': { spec: { height_mm: 160 } } };
    assert.equal(check({ component_type: 'Gehäuse', max_gpu_length_mm: 360, max_cooler_height_mm: 165 }, p).status, 'ok');
    assert.equal(check({ component_type: 'Gehäuse', max_gpu_length_mm: 320, max_cooler_height_mm: 165 }, p).status, 'blocked');
});

test('Shop: Netzteil-Kandidat gegen Bedarf', () => {
    const p = { CPU: { spec: { tdp_watts: 120 } }, GPU: { spec: { tdp_watts: 360 } } };
    assert.equal(check({ component_type: 'Netzteil', psu_watts: 850 }, p).status, 'ok');
    assert.equal(check({ component_type: 'Netzteil', psu_watts: 750 }, p).status, 'blocked');
});

test('Shop: Gesamtauswertung der Auswahl', () => {
    const data = { items: {
        1: { type: 'CPU', spec: { socket: 'AM5', tdp_watts: 120 } },
        2: { type: 'Mainboard', spec: { socket: 'LGA1851' } },
        3: { type: 'Netzteil', spec: { psu_watts: 850 } },
    } };
    const r = Rules.evaluate(data, ['1', '2', '3'], {});
    assert.equal(r.status, 'blocked');
    assert.equal(r.psuNeed, Math.ceil((120 + 120) * 1.4));
});
