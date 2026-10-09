import { chromium } from 'playwright';
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';

// Browser-Test für js/pchm-konfigurator.js mit NOVA-Konfigurator-Markup (cfg-page.html)
const here = path.dirname(new URL(import.meta.url).pathname);
const tpl = path.resolve(here, '../../../templates/PCHM');
const nova = path.resolve(process.env.JTL_NOVA_DIR || path.join(here, '../.cache/jtl-shop/templates/NOVA'));
const files = {
    '/cfg-page.html': path.join(here, 'cfg-page.html'),
    '/pchm-konfigurator.js': path.join(tpl, 'js/pchm-konfigurator.js'),
    '/pchm.css': path.join(tpl, 'themes/pchm/pchm.css'),
    '/pchm_crit.css': path.join(tpl, 'themes/pchm/pchm_crit.css'),
    '/jquery-3.7.1.min.js': path.join(nova, 'js/jquery-3.7.1.min.js'),
};
let posts = 0;
const server = http.createServer((req, res) => {
    if (req.method === 'POST') { posts++; res.writeHead(200, { 'Content-Type': 'text/html' }); return res.end('<p id="posted">posted</p>'); }
    const file = files[new URL(req.url, 'http://x').pathname];
    if (!file || !fs.existsSync(file)) { res.writeHead(404); return res.end(); }
    const type = file.endsWith('.js') ? 'text/javascript' : file.endsWith('.css') ? 'text/css' : 'text/html';
    res.writeHead(200, { 'Content-Type': type + '; charset=utf-8' });
    fs.createReadStream(file).pipe(res);
}).listen(0);
const base = `http://127.0.0.1:${server.address().port}/cfg-page.html`;

let fails = 0;
const check = (name, ok, info = '') => { console.log((ok ? 'PASS ' : 'FAIL ') + name + (ok ? '' : '  → ' + info)); if (!ok) fails++; };

const browser = await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const page = await (await browser.newContext({ viewport: { width: 1400, height: 900 } })).newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));
const open = async (query = '') => {
    await page.goto(base + query);
    await page.waitForFunction(() => window.PCHMKonfigurator !== undefined);
};
const pick = async id => {
    const tag = await page.evaluate(i => document.getElementById('item' + i).tagName, id);
    if (tag === 'OPTION') {
        const sel = await page.evaluate(i => document.getElementById('item' + i).parentElement.id, id);
        await page.selectOption('#' + sel, String(id));
    } else {
        await page.check('#item' + id, { force: true });
    }
};
const blocked = id => page.evaluate(i => {
    const el = document.getElementById('item' + i);
    return (el.tagName === 'OPTION' ? el : el.closest('.custom-control')).classList.contains('pchm-cfg-blocked');
}, id);
const text = sel => page.evaluate(s => document.querySelector(s)?.textContent.trim() ?? null, sel);

// Schrittnavigation
await open();
check('Navigation sichtbar', await page.evaluate(() => !document.getElementById('pchm-cfg-nav').hidden));
check('6 Schritte', await page.locator('.pchm-cfg-step').count() === 6);
check('Fortschritt 0 von 6', await text('.pchm-cfg-progress') === '0 von 6 Gruppen gewählt', await text('.pchm-cfg-progress'));
check('Pflichtgruppen markiert, GPU optional', await page.evaluate(() => {
    const s = [...document.querySelectorAll('.pchm-cfg-step')];
    return s.filter(e => e.classList.contains('is-required')).length === 5
        && s[3].querySelector('.pchm-cfg-step-value').textContent === 'nicht gewählt';
}));
check('Kompatibilitätsbox in der NOVA-Sidebar', await page.evaluate(() => document.getElementById('product-configuration-sidebar').firstElementChild.id === 'pchm-cfg-compat'));

// Sockel
await pick(11);
check('Schritt Prozessor erledigt', await page.evaluate(() => document.querySelector('.pchm-cfg-step').classList.contains('is-done')
    && document.querySelector('.pchm-cfg-step .pchm-cfg-step-value').textContent === 'AMD Ryzen 7 7800X3D'));
check('Fortschritt 1 von 6', await text('.pchm-cfg-progress') === '1 von 6 Gruppen gewählt', await text('.pchm-cfg-progress'));
check('AM5-CPU: LGA1700-Board gesperrt', await blocked(22));
check('AM5-CPU: AM5-Board frei', !(await blocked(21)));
check('Hinweis am gesperrten Board', (await text('.custom-control:has(#item22) > .pchm-cfg-note')) === 'Passt nicht: Sockel passt nicht: CPU AM5, Mainboard LGA1700',
    await text('.custom-control:has(#item22) > .pchm-cfg-note'));
check('Markieren-Modus lässt Auswahl zu', await page.evaluate(() => !document.getElementById('item22').disabled));

// Konflikt wählen → Warenkorb gesperrt
await pick(22);
check('Box: Konflikt', await page.evaluate(() => document.getElementById('pchm-cfg-compat').classList.contains('is-blocked')), await page.evaluate(() => document.getElementById('pchm-cfg-compat').className));
check('Box: Status 1 Konflikt', await text('.pchm-cfg-compat-status') === '1 Konflikt(e)', await text('.pchm-cfg-compat-status'));
check('Box: Konfliktzeile', await text('#pchm-cfg-compat li.is-blocked') === 'Sockel passt nicht: CPU AM5, Mainboard LGA1700', await text('#pchm-cfg-compat li.is-blocked'));
check('Button markiert', await page.evaluate(() => document.querySelector('button[name="inWarenkorb"]').classList.contains('pchm-cfg-cart-blocked')));
await page.click('button[name="inWarenkorb"]');
await page.waitForTimeout(150);
check('Absenden verhindert', posts === 0 && page.url().startsWith(base));
check('NOVA-Hinweis (showNotify)', await page.evaluate(() => window.notified.length === 1 && window.notified[0].text === 'Bitte zuerst die Konflikte lösen.'));
check('Box hervorgehoben', await page.evaluate(() => document.getElementById('pchm-cfg-compat').classList.contains('is-attention')));

// Konflikt lösen, GPU/Gehäuse/Netzteil
await pick(21);
check('Konflikt gelöst', await page.evaluate(() => !document.getElementById('pchm-cfg-compat').classList.contains('is-blocked')
    && !document.querySelector('button[name="inWarenkorb"]').classList.contains('pchm-cfg-cart-blocked')));
await pick(32);
check('DDR4 auf DDR5-Board: Konflikt', await text('#pchm-cfg-compat li.is-blocked') === 'RAM passt nicht: DDR4 statt DDR5', await text('#pchm-cfg-compat li.is-blocked'));
await pick(31);
await pick(42);
check('RTX 4090: kompaktes Gehäuse gesperrt (Option)', await blocked(51) && (await text('#item51')) === 'Compact Case – Passt nicht', await text('#item51'));
check('RTX 4090: Big Tower frei', !(await blocked(52)) && (await text('#item52')) === 'Big Tower');
check('RTX 4090: 550-W-Netzteil gesperrt', await blocked(61) && !(await blocked(62)));
check('Netzteilempfehlung (120+450+120)×1,4 = 966 W', await text('#pchm-cfg-compat li.is-psu') === 'Empfohlenes Netzteil: mind. 966 W', await text('#pchm-cfg-compat li.is-psu'));
await pick(41);
check('RTX 4070: Gehäuse wieder frei, Beschriftung zurück', !(await blocked(51)) && (await text('#item51')) === 'Compact Case');
check('RTX 4070: (120+200+120)×1,4 = 616 W → 550 W gesperrt', await blocked(61));
await pick(51);
await pick(62);
check('Alles gewählt: 6 von 6', await text('.pchm-cfg-progress') === '6 von 6 Gruppen gewählt', await text('.pchm-cfg-progress'));
check('Alles passt', await page.evaluate(() => document.getElementById('pchm-cfg-compat').classList.contains('is-ok')) && await text('.pchm-cfg-compat-status') === 'Alles passt zusammen', await text('.pchm-cfg-compat-status'));
check('Prüfzeilen vorhanden', await page.locator('#pchm-cfg-compat li.is-ok').count() >= 4, String(await page.locator('#pchm-cfg-compat li.is-ok').count()));

// Sprung über die Navigation
await page.click('[data-pchm-cfg-goto="6"]');
await page.waitForTimeout(800);
check('Navigation springt zur Gruppe', await page.evaluate(() => Math.abs(document.getElementById('cfg-grp-6').getBoundingClientRect().top) < 200),
    String(await page.evaluate(() => document.getElementById('cfg-grp-6').getBoundingClientRect().top)));

// Formularfelder unverändert → JTL nimmt die Konfiguration an
await Promise.all([page.waitForURL(/\/cart$/), page.click('button[name="inWarenkorb"]')]);
check('Passende Konfiguration wird abgeschickt', posts === 1);

// Sperren-Modus
await open('?mode=disable');
await pick(11);
check('Sperren-Modus: LGA1700-Board deaktiviert', await page.evaluate(() => document.getElementById('item22').disabled && !document.getElementById('item21').disabled));
await pick(12);
check('Sperren-Modus: nach CPU-Wechsel umgekehrt', await page.evaluate(() => !document.getElementById('item22').disabled && document.getElementById('item21').disabled));

// Warenkorb-Sperre abgeschaltet
await open('?block=0');
await pick(11); await pick(22);
await Promise.all([page.waitForURL(/\/cart$/), page.click('button[name="inWarenkorb"]')]);
check('Sperre aus: Konflikt wird nur angezeigt', posts === 2);

check('Keine JS-Fehler', errors.length === 0, errors.join(' | '));
await browser.close();
server.close();
console.log(fails === 0 ? '\nAlle Konfigurator-Tests bestanden.' : `\n${fails} Konfigurator-Test(s) fehlgeschlagen.`);
process.exit(fails ? 1 : 0);
