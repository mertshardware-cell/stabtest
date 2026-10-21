import { chromium } from 'playwright';
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';

// Browser-Test für js/custom.js mit NOVA-ähnlichem Markup (page.html) und NOVAs jQuery/Bootstrap/jtl.basket.js
const here = path.dirname(new URL(import.meta.url).pathname);
const tpl = path.resolve(here, '../../../templates/PCHM');
const nova = path.resolve(process.env.JTL_NOVA_DIR || path.join(here, '../.cache/jtl-shop/templates/NOVA'));
const files = {
    '/page.html': path.join(here, 'page.html'),
    '/custom.js': path.join(tpl, 'js/custom.js'),
    '/pchm.css': path.join(tpl, 'themes/pchm/pchm.css'),
    '/pchm_crit.css': path.join(tpl, 'themes/pchm/pchm_crit.css'),
    '/jquery-3.7.1.min.js': path.join(nova, 'js/jquery-3.7.1.min.js'),
    '/bootstrap.bundle.min.js': path.join(nova, 'js/bootstrap.bundle.min.js'),
    '/jquery.serialize-object.min.js': path.join(nova, 'js/jquery.serialize-object.min.js'),
    '/jtl.basket.js': path.join(nova, 'js/jtl.basket.js'),
};
const server = http.createServer((req, res) => {
    if (req.method === 'POST') { res.writeHead(200, { 'Content-Type': 'text/html' }); return res.end('<p>posted</p>'); }
    const file = files[new URL(req.url, 'http://x').pathname];
    if (!file || !fs.existsSync(file)) { res.writeHead(404); return res.end(); }
    const type = file.endsWith('.js') ? 'text/javascript' : file.endsWith('.css') ? 'text/css' : 'text/html';
    res.writeHead(200, { 'Content-Type': type + '; charset=utf-8' });
    fs.createReadStream(file).pipe(res);
}).listen(0);
const base = `http://127.0.0.1:${server.address().port}/page.html`;

let fails = 0;
const check = (name, ok, info = '') => { console.log((ok ? 'PASS ' : 'FAIL ') + name + (ok ? '' : '  → ' + info)); if (!ok) fails++; };

const browser = await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const context = await browser.newContext({ viewport: { width: 1400, height: 900 } });
const page = await context.newPage();
const errors = [];
page.on('pageerror', e => errors.push(e.message));
await page.goto(base);
await page.waitForFunction(() => window.pchm !== undefined);
await page.waitForTimeout(300);

// Preis-Umschalter
check('Startmodus brutto', await page.evaluate(() => document.documentElement.classList.contains('pchm-price-mode-gross') && !document.documentElement.classList.contains('pchm-price-alt-active')));
await page.click('[data-pchm-price-mode="net"]');
let alt = await page.textContent('#product-offer .pchm-price-alt-value');
check('Netto-Preis berechnet (1.190 → 1.000)', alt === '1.000,00 €', alt);
check('Hinweis zzgl. MwSt.', (await page.textContent('#product-offer .pchm-price-alt-note')) === 'zzgl. 19 % MwSt.', await page.textContent('#product-offer .pchm-price-alt-note'));
check('Hauptpreis ausgeblendet, Alt sichtbar', await page.evaluate(() => getComputedStyle(document.querySelector('#product-offer .price')).display === 'none' && getComputedStyle(document.querySelector('#product-offer .pchm-price-alt')).display === 'block'));
check('USt-Hinweis ausgeblendet', await page.evaluate(() => getComputedStyle(document.querySelector('.vat_info')).display === 'none'));
// NOVA ändert den Preis (Variation/Konfigurator)
await page.evaluate(() => { document.querySelector('#product-offer .price').innerHTML = '2.380,00 €'; });
await page.waitForTimeout(100);
alt = await page.textContent('#product-offer .pchm-price-alt-value');
check('Preisänderung per MutationObserver', alt === '2.000,00 €', alt);
// Gesamtbetrag im Button
await page.fill('#quantity', '3');
await page.dispatchEvent('#quantity', 'input');
let total = await page.textContent('#buy .pchm-btn-total');
check('Button-Gesamtbetrag 3 × 2.000', total === '6.000,00 €', total);
// Modus bleibt nach Reload erhalten
await page.reload();
await page.waitForFunction(() => window.pchm !== undefined);
await page.waitForTimeout(200);
check('Netto-Modus gespeichert', await page.evaluate(() => document.documentElement.classList.contains('pchm-price-mode-net') && document.documentElement.classList.contains('pchm-price-alt-active')));
check('aria-pressed gesetzt', (await page.getAttribute('[data-pchm-price-mode="net"]', 'aria-pressed')) === 'true');
await page.click('[data-pchm-price-mode="gross"]');
total = await page.textContent('#buy .pchm-btn-total');
check('Button-Gesamtbetrag brutto 1 × 1.190', total === '1.190,00 €', total);

// Side-Basket
await page.click('.pchm-cart-trigger .nav-link');
check('Warenkorb-Seitenleiste offen', await page.evaluate(() => document.querySelector('#pchm-offcanvas-cart').classList.contains('is-open') && document.body.classList.contains('pchm-offcanvas-open')));
check('Warenkorb-Inhalt übernommen', (await page.textContent('[data-pchm-cart-body]')).includes('2 Artikel'));
await page.keyboard.press('Escape');
check('ESC schließt', await page.evaluate(() => !document.querySelector('#pchm-offcanvas-cart').classList.contains('is-open') && !document.body.classList.contains('pchm-offcanvas-open')));

// AJAX: In den Warenkorb → IO → Seitenleiste mit neuem Inhalt
await page.click('#buy');
await page.waitForTimeout(300);
check('AJAX pushToBasket + getBasketItems', await page.evaluate(() => window.ioCalls.join(',') === 'pushToBasket,getBasketItems'), await page.evaluate(() => window.ioCalls.join(',')));
check('Seitenleiste öffnet automatisch mit neuem Inhalt', await page.evaluate(() => document.querySelector('#pchm-offcanvas-cart').classList.contains('is-open')) && (await page.textContent('[data-pchm-cart-body]')).includes('3 Artikel'));
check('NOVA-Popup unterdrückt', await page.evaluate(() => window.notified === undefined));
check('Seite nicht neu geladen', page.url() === base);
await page.click('.pchm-offcanvas-backdrop', { force: true });
// Wunschzettel-Button wird nicht abgefangen
await Promise.all([page.waitForURL('**/artikel', { timeout: 5000 }).catch(() => null), page.click('#wish')]);
check('Andere Submit-Buttons normal (kein AJAX)', page.url().endsWith('/artikel'), page.url());
await page.goto(base);
await page.waitForFunction(() => window.pchm !== undefined);
await page.waitForTimeout(200);

// Countdown
await page.waitForTimeout(1100);
const cd = await page.evaluate(() => ['d', 'h', 'm', 's'].map(u => document.querySelector('#cd [data-unit="' + u + '"]').textContent));
check('Countdown läuft (2 Tage 03 Std.)', cd[0] === '2' && cd[1] === '03' && /^\d\d$/.test(cd[3]), cd.join(':'));

// Sprungnavigation
check('Sprungnavigation sichtbar', await page.evaluate(() => !document.getElementById('pchm-jumpnav').hidden && document.querySelectorAll('#pchm-jumpnav a').length === 2));
await page.click('#pchm-jumpnav li:nth-child(2) a');
await page.waitForTimeout(400);
check('Klick zeigt Bewertungen-Tab', await page.evaluate(() => document.getElementById('tab-votes').classList.contains('active')));

// Einklappen
check('Beschreibung eingeklappt', await page.evaluate(() => document.getElementById('fold').classList.contains('is-folded') && !!document.querySelector('.pchm-fold-toggle')));

// Suche
check('Such-Platzhalter', (await page.getAttribute('#search-header', 'placeholder')) === 'Wonach suchst du?');

// Neuigkeiten-Glocke
check('Glocke: Punkt sichtbar', await page.evaluate(() => !document.querySelector('.pchm-news-dot').classList.contains('d-none')));
await page.click('.pchm-news-bell .nav-link');
check('Glocke: Panel offen, Punkt weg', await page.evaluate(() => document.getElementById('pchm-offcanvas-news').classList.contains('is-open') && document.querySelector('.pchm-news-dot').classList.contains('d-none')));
await page.keyboard.press('Escape');

// Hinweisleiste wegklicken + Reload
await page.click('[data-pchm-dismiss="announcement"]');
await page.reload();
await page.waitForFunction(() => window.pchm !== undefined);
await page.waitForTimeout(200);
check('Hinweisleiste bleibt ausgeblendet', await page.evaluate(() => document.getElementById('pchm-announcement').classList.contains('is-hidden')));
check('Glocke bleibt gelesen', await page.evaluate(() => document.querySelector('.pchm-news-dot').classList.contains('d-none')));

// Sticky-Kaufleiste
await page.evaluate(() => window.scrollTo({ top: 1600, behavior: 'instant' }));
await page.waitForTimeout(400);
check('Kaufleiste beim Scrollen sichtbar', await page.evaluate(() => document.getElementById('pchm-buybar').classList.contains('is-visible')));
check('Kaufleiste zeigt Preis', (await page.textContent('[data-pchm-buybar-price]')) === '1.190,00 €', await page.textContent('[data-pchm-buybar-price]'));
await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
await page.waitForTimeout(300);
check('Kaufleiste oben ausgeblendet', await page.evaluate(() => !document.getElementById('pchm-buybar').classList.contains('is-visible')));

// USP-Rotation
const first = await page.evaluate(() => document.querySelector('.pchm-usp-mobile-item.is-active').textContent);
const rotated = await page.waitForFunction(f => document.querySelector('.pchm-usp-mobile-item.is-active').textContent !== f, first, { timeout: 2000 }).then(() => true).catch(() => false);
check('USP-Leiste rotiert', rotated, first);

// Megamenü per Klick
await page.goto(base + '?cfg=' + encodeURIComponent('{"megaMenuMode":"click"}'));
await page.waitForFunction(() => window.pchm !== undefined);
await page.waitForTimeout(200);
await page.hover('#mm1');
check('Klick-Modus: NOVA-Hover entfernt', await page.evaluate(() => !window.novaHover));
await page.click('#mm1');
check('Klick öffnet Megamenü', await page.evaluate(() => document.querySelector('#mainNavigation .dropdown').classList.contains('show') && !!document.querySelector('.pchm-mm-backdrop')));
await page.click('#mm1');
check('Zweiter Klick schließt', await page.evaluate(() => !document.querySelector('#mainNavigation .dropdown').classList.contains('show') && !document.querySelector('.pchm-mm-backdrop')));

// Hover-Modus: NOVA-Handler bleibt
await page.goto(base);
await page.waitForFunction(() => window.pchm !== undefined);
await page.hover('#mm1');
check('Hover-Modus: NOVA-Hover aktiv', await page.evaluate(() => window.novaHover > 0));

// Händler-Kundengruppe (netto) → Alt zeigt Brutto
await context.clearCookies();
await page.evaluate(() => localStorage.clear());
await page.goto(base + '?cfg=' + encodeURIComponent('{"groupNet":true}'));
await page.waitForFunction(() => window.pchm !== undefined);
await page.evaluate(() => { document.querySelector('#product-offer .price').innerHTML = '1.000,00 €'; });
await page.click('[data-pchm-price-mode="gross"]');
await page.waitForTimeout(100);
alt = await page.textContent('#product-offer .pchm-price-alt-value');
check('Händler: Brutto aus Netto (1.000 → 1.190)', alt === '1.190,00 €', alt);

check('Keine JS-Fehler', errors.length === 0, errors.join(' | '));
await browser.close();
server.close();
console.log(fails === 0 ? '\nAlle Browser-Tests bestanden.' : `\n${fails} Browser-Test(s) fehlgeschlagen.`);
process.exit(fails ? 1 : 0);
