<?php
// Pfade: siehe tests/pchm/run-tests.sh (Umgebungsvariablen oder .cache/)
$cache   = __DIR__ . '/../.cache';
$novaDir = rtrim(getenv('JTL_NOVA_DIR') ?: $cache . '/jtl-shop/templates/NOVA', '/') . '/';
$smarty4 = rtrim(getenv('SMARTY4_DIR') ?: $cache . '/smarty4', '/') . '/';
$sccDir  = rtrim(getenv('SCC_DIR') ?: $cache . '/scc', '/') . '/';
$child   = realpath(__DIR__ . '/../../../templates/PCHM') . '/';
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require __DIR__ . '/stubs.php';
require $smarty4 . 'libs/Smarty.class.php';
spl_autoload_register(function ($c) use ($sccDir) {
    if (str_starts_with($c, 'scc\\')) {
        $f = $sccDir . 'src/' . str_replace('\\', '/', $c) . '.php';
        if (is_file($f)) require $f;
    }
});
require $child . 'Bootstrap.php';

// Standardwerte aus den template.xml lesen
function readDefaults(string $file): array {
    $conf = [];
    foreach (simplexml_load_file($file)->Settings->Section as $section) {
        foreach ($section->Setting as $setting) {
            $conf[(string)$section['Key']][(string)$setting['Key']] = (string)$setting['Value'];
        }
    }
    return $conf;
}
$GLOBALS['tplConf'] = array_replace_recursive(readDefaults($novaDir . 'template.xml'), readDefaults($child . 'template.xml'));

function harness_dummy_block($params, $content = null) { return (string)$content; }
function harness_dummy_modifier($v = null, ...$args) { return is_scalar($v) ? (string)$v : ''; }
function harness_dummy_function($params = [], $tpl = null) { return ''; }
function harness_lang($params, $tpl) {
    $out = '[' . ($params['section'] ?? 'global') . '.' . $params['key'] . ']';
    if (!empty($params['assign'])) { $tpl->assign($params['assign'], $out); return ''; }
    return $out;
}
function harness_route($params, $tpl) {
    $out = '/' . $params['id'];
    if (!empty($params['assign'])) { $tpl->assign($params['assign'], $out); return ''; }
    return $out;
}

function smarty(array $vars = []): Smarty {
    $s = new Smarty();
    $s->setCompileDir(sys_get_temp_dir() . '/pchm_render_' . getmypid());
    $s->setTemplateDir([]);
    $s->addTemplateDir(__DIR__ . '/fakenova/', 'NOVA');
    $s->addTemplateDir($GLOBALS['child'], 'frontend');
    $s->setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    $s->muteUndefinedOrNullWarnings();
    (new scc\DefaultComponentRegistrator(new scc\Renderer($s)))->registerComponents();
    $s->registerPlugin('function', 'lang', 'harness_lang');
    $s->registerPlugin('function', 'get_static_route', 'harness_route');
    foreach (['strip_tags', 'trim', 'ltrim', 'intval', 'explode', 'strpos', 'in_array', 'preg_match'] as $fn) {
        $s->registerPlugin('modifier', $fn, $fn);
    }
    $s->registerDefaultPluginHandler(function ($name, $type, $template, &$callback, &$script, &$cacheable) {
        $callback = $type === Smarty::PLUGIN_BLOCK ? 'harness_dummy_block'
            : ($type === Smarty::PLUGIN_MODIFIER ? 'harness_dummy_modifier' : 'harness_dummy_function');
        return true;
    });
    $boot = new Template\PCHM\Bootstrap();
    $boot->setSmarty($s);
    $boot->boot();
    $s->assign('Einstellungen', ['template' => $GLOBALS['tplConf'], 'global' => ['global_wunschliste_anzeigen' => 'Y'],
        'navigationsfilter' => ['kategorie_beschreibung_anzeigen' => 'Y']]);
    $s->assign('nSeitenTyp', 1)->assign('isMobile', false)->assign('ShopURL', 'https://shop.test')
        ->assign('currentThemeDirFull', 'https://shop.test/templates/PCHM/themes/pchm/')
        ->assign('bExclusive', false)->assign('WarenkorbArtikelAnzahl', 2)->assign('WarensummeLocalized', ['59,80 &euro;', '50,25 &euro;']);
    foreach ($vars as $k => $v) { $s->assign($k, $v); }
    return $s;
}

$fails = 0;
function check(string $name, string $html, array $contains, array $notContains = []): void {
    global $fails;
    $ok = true;
    foreach ($contains as $needle) {
        if (!str_contains($html, $needle)) { $ok = false; echo "  ✗ erwartet: " . $needle . "\n"; }
    }
    foreach ($notContains as $needle) {
        if (str_contains($html, $needle)) { $ok = false; echo "  ✗ unerwartet: " . $needle . "\n"; }
    }
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . "\n";
    if (!$ok) { $fails++; echo "----\n" . trim(preg_replace('/\s+/', ' ', $html)) . "\n----\n"; }
}
function render(string $tpl, array $vars = []): string {
    $s = smarty($vars);
    return $s->fetch($tpl);
}
$chain = fn(string $f) => "extends:[NOVA]{$f}|[frontend]{$f}";

// 1) Topbar: USPs (de), Hotline mit bereinigter tel:-Nummer, Preis-Umschalter
$GLOBALS['tplConf']['pchm_header']['phone'] = '+49 (0) 2235 / 123-45';
$html = render('snippets/pchm/topbar.tpl');
check('Topbar DE', $html, ['Schneller Versand', 'Handmontiert in Deutschland', 'href="tel:+490223512345"', 'Mo–Fr 12–17 Uhr', 'data-pchm-price-mode="net"', 'fas fa-truck'], ['de=Schnell', '|| en']);

// 2) Englisch
$GLOBALS['langCode'] = 'eng';
$html = render('snippets/pchm/topbar.tpl');
check('Topbar EN', $html, ['Fast shipping', 'Hand-built in Germany', 'Mon–Fri'], ['Schneller Versand']);
$GLOBALS['langCode'] = 'ger';

// 3) Hinweisleiste mit Countdown in der Zukunft
$GLOBALS['tplConf']['pchm_header']['announcement_active'] = 'Y';
$GLOBALS['tplConf']['pchm_header']['announcement_countdown'] = date('Y-m-d H:i', time() + 86400 * 3);
$GLOBALS['tplConf']['pchm_header']['announcement_link'] = '/Angebote"><script>';
$html = render('snippets/pchm/announcement.tpl');
check('Hinweisleiste aktiv + Countdown', $html, ['id="pchm-announcement"', 'data-pchm-countdown=', 'is-dismissible', '&quot;&gt;&lt;script&gt;'], ['"><script>']);
// abgelaufen → nicht anzeigen
$GLOBALS['tplConf']['pchm_header']['announcement_countdown'] = '2020-01-01 00:00';
$html = render('snippets/pchm/announcement.tpl');
check('Hinweisleiste abgelaufen', $html, [], ['pchm-announcement']);
$GLOBALS['tplConf']['pchm_header']['announcement_countdown'] = '';
// im Checkout nicht anzeigen
$html = render('snippets/pchm/announcement.tpl', ['nSeitenTyp' => PAGE_BESTELLVORGANG]);
check('Hinweisleiste nicht im Checkout', $html, [], ['pchm-announcement']);
$GLOBALS['tplConf']['pchm_header']['announcement_active'] = 'N';

// 4) Mobile USP-Leiste
$html = render('snippets/pchm/usp_mobile.tpl');
check('USP mobil', $html, ['data-pchm-rotate', 'pchm-usp-mobile-item is-active', '24h-Stresstest']);

// 5) Header: Body-Klassen (capture/replace), CSS-Variablen, Font-Preload, Head-Skript
$html = render($chain('layout/header.tpl'));
check('Header Body-Klassen', $html, ['<body class="pchm pchm-sticky-all pchm-mm-hover pchm-has-sidecart pchm-has-sideaccount', 'is-nova', 'data-page="1"']);
check('Header CSS-Variablen (DESIGN.md)', $html, [':root{--pchm-primary:#9013fe;', '--pchm-font-body:"PCHM Manrope"', '--pchm-font-heading:"PCHM Roboto Condensed"', '--pchm-radius:6px;--pchm-radius-lg:8px', '--pchm-mm-col:25%']);
check('Header Font-Preload', $html, ['fonts/manrope-latin-400-normal.woff2', 'fonts/roboto-condensed-latin-700-normal.woff2', 'nova.woff2']);
check('Header Head-Skript', $html, ['window.pchmConfig = {', '"groupNet":false', 'pchm-price-mode-', '<!--resources-->']);
check('Header NOVA-Inhalt erhalten', $html, ['NOVA-HEADER']);

// 6) CSS-Variablen: bösartige Farbe wird verworfen, Uppercase gesetzt
$GLOBALS['tplConf']['pchm_design']['color_primary'] = 'red;}</style><script>alert(1)</script>';
$html = render($chain('layout/header.tpl'));
check('CSS-Variablen bereinigt', $html, ['--pchm-primary-hover:#7a0fd9'], ['alert(1)', '--pchm-primary:red']);
$GLOBALS['tplConf']['pchm_design']['color_primary'] = '#9013fe';

// 7) Startseite: Reihenfolge + NOVA-Inhalt, unbekannte Bereiche ignorieren
$GLOBALS['tplConf']['pchm_home']['order'] = 'usps, nova , evil, countdown';
$GLOBALS['tplConf']['pchm_home']['countdown'] = 'Y';
$GLOBALS['tplConf']['pchm_home']['countdown_end'] = date('Y-m-d H:i', time() + 3600);
$GLOBALS['tplConf']['pchm_home']['countdown_image'] = 'mediafiles/Bilder/aktion.jpg';
$GLOBALS['tplConf']['pchm_home']['countdown_link'] = '/Black-Week';
$html = render($chain('page/index.tpl'));
check('Startseite Reihenfolge', $html, ['pchm-home-usps', 'NOVA-HOME', 'pchm-home-countdown has-image', "url('https://shop.test/mediafiles/Bilder/aktion.jpg')", 'Jetzt sichern', 'href="/Black-Week"']);
$posUsps = strpos($html, 'pchm-home-usps'); $posNova = strpos($html, 'NOVA-HOME'); $posCd = strpos($html, 'pchm-home-countdown');
check('Startseite Positionen', ($posUsps < $posNova && $posNova < $posCd) ? 'ok' : 'falsch', ['ok']);
// ohne "nova" in der Liste wird NOVA-Inhalt trotzdem angehängt
$GLOBALS['tplConf']['pchm_home']['order'] = 'usps';
$html = render($chain('page/index.tpl'));
check('Startseite NOVA immer enthalten', $html, ['NOVA-HOME']);

// 8) Side-Basket: Template mit Fortschrittsbalken, Fallback auf NOVA wenn deaktiviert
$GLOBALS['cartSum'] = 59.80;
$html = render($chain('basket/cart_dropdown_label.tpl'));
check('Side-Basket', $html, ['data-pchm-offcanvas="cart"', '<template class="pchm-cart-template">', 'Noch 40,20 &euro; bis zum kostenlosen Versand', 'width: 60%', 'id="cart-dropdown-container"', '<span class="fa-sup" title="2">2</span>'], ['NOVA-CART']);
$GLOBALS['cartSum'] = 150.0;
$html = render($chain('basket/cart_dropdown_label.tpl'));
check('Side-Basket versandkostenfrei', $html, ['is-reached', 'kostenlos versendet']);
$GLOBALS['tplConf']['pchm_header']['side_basket'] = 'N';
$html = render($chain('basket/cart_dropdown_label.tpl'));
check('Side-Basket aus → NOVA', $html, ['NOVA-CART'], ['pchm-cart-template']);
$GLOBALS['tplConf']['pchm_header']['side_basket'] = 'Y';

// 9) Konto: Seitenleiste bzw. NOVA-Dropdown
$html = render($chain('layout/header_shop_nav_account.tpl'));
check('Konto-Trigger', $html, ['data-pchm-offcanvas="account"', 'fa-user'], ['NOVA-ACCOUNT']);
$GLOBALS['tplConf']['pchm_header']['side_account'] = 'N';
$html = render($chain('layout/header_shop_nav_account.tpl'));
check('Konto aus → NOVA', $html, ['NOVA-ACCOUNT']);
$GLOBALS['tplConf']['pchm_header']['side_account'] = 'Y';

// 10) Offcanvas-Konto (ausgeloggt / eingeloggt)
$html = render('snippets/pchm/offcanvas_account.tpl');
check('Konto ausgeloggt', $html, ['name="email"', 'name="passwort"', 'name="login"', '/registrieren.php']);
$GLOBALS['customerId'] = 5;
$html = render('snippets/pchm/offcanvas_account.tpl');
check('Konto eingeloggt', $html, ['Max', '?bestellungen=1', '?logout=1', '#my-wishlists'], ['name="passwort"']);
$GLOBALS['customerId'] = 0;

// 11) Footer: Kontakt, Adresse, Zahlungsarten, Versand, Social extra
$GLOBALS['tplConf']['pchm_footer']['social_extra'] = 'Discord=https://discord.gg/pchm || Twitch=https://twitch.tv/pchm || kaputt';
$html = render($chain('layout/footer.tpl'));
check('Footer Service', $html, ['PCHM Exklusiv<br>Am Mittelpfad 16<br>50374 Erftstadt', 'fab fa-cc-paypal', 'pchm-badge-klarna', '[checkout.prepayment]' === '' ? 'x' : 'Vorkasse', 'fab fa-dhl', 'mailto:team@pchm.de', '<boxes/>']);
check('Footer Social extra', $html, ['fab fa-discord', 'href="https://discord.gg/pchm"', 'fab fa-twitch', '<social/>'], ['kaputt']);
check('Footer Offcanvas', $html, ['id="pchm-offcanvas-cart"', 'id="pchm-offcanvas-account"', 'pchm-offcanvas-backdrop', 'id="jtl-io-path"']);

// 12) Megamenü-Tiefe
$GLOBALS['tplConf']['pchm_megamenu']['depth'] = '3';
$html = render($chain('snippets/categories_mega_recursive.tpl'), ['firstChild' => true, 'mainCategory' => new class { function getURL() { return '/kat'; } }]);
check('Megamenü Tiefe 3 + Alle anzeigen', $html, ['MAX=3', 'pchm-mm-show-all', 'Alle anzeigen', 'href="/kat"']);
$html = render($chain('snippets/categories_mega_recursive.tpl'), ['firstChild' => false, 'isMobile' => true]);
check('Megamenü mobil unverändert', $html, ['MAX=5'], ['pchm-mm-show-all']);

// 13) Artikelliste: Raster je Gerät
$GLOBALS['tplConf']['pchm_list']['cols_mobile'] = '1';
$GLOBALS['tplConf']['pchm_list']['cols_tablet'] = '2';
$GLOBALS['tplConf']['pchm_list']['cols_desktop'] = '6';
$html = render($chain('productlist/index.tpl'));
check('Raster 1/2/6', $html, ['GRID=12/12/6/2']);

// 14) Preise: Alt-Element + Staffelpreise brutto/netto
$artikel = new stdClass();
$artikel->fMwSt = '19.00';
$artikel->staffelPreis_arr = [['nAnzahl' => 5, 'cPreisLocalized' => ['119,00 €', '100,00 €'], 'cBasePriceLocalized' => []]];
$html = render($chain('productdetails/price.tpl'), ['Artikel' => $artikel]);
check('Preis Alt + Staffel', $html, ['<div class="pchm-price-alt" data-pchm-tax="19.00"></div>', 'pchm-vat-gross">119,00 €', 'pchm-vat-net">100,00 €'], ['NOVA-BULK']);

// 15) Dokumente
$pdf = (object)['nMedienTyp' => 5, 'cPfad' => 'datenblatt.pdf', 'cURL' => '', 'cName' => 'Datenblatt'];
$img = (object)['nMedienTyp' => 1, 'cPfad' => 'bild.jpg', 'cURL' => '', 'cName' => 'Bild'];
$link = (object)['nMedienTyp' => 4, 'cPfad' => '', 'cURL' => 'https://example.com/manual', 'cName' => 'Handbuch'];
$a = new stdClass(); $a->oMedienDatei_arr = [$pdf, $img, $link];
$html = render('snippets/pchm/product_documents.tpl', ['Artikel' => $a]);
check('Dokumente', $html, ['https://shop.test/mediafiles/datenblatt.pdf', 'fa-file-pdf', 'https://example.com/manual', 'Handbuch'], ['bild.jpg']);

// 16) Badge unbekannt / Groß-Kleinschreibung
$html = render('snippets/pchm/brand_badge.tpl', ['key' => 'Apple Pay']);
check('Badge Apple Pay', $html, ['fab fa-cc-apple-pay', 'pchm-badge-applepay']);
$html = render('snippets/pchm/brand_badge.tpl', ['key' => 'Ratenkauf <b>']);
check('Badge unbekannt', $html, ['Ratenkauf &lt;b&gt;'], ['<b>']);

// 17) JS-Konfiguration (Englisch, Händler-Kundengruppe)
$GLOBALS['langCode'] = 'eng'; $GLOBALS['groupNet'] = true;
$s = smarty();
$json = $s->fetch('string:{pchm_js_config}');
$data = json_decode($json, true);
check('JS-Konfiguration', is_array($data) && $data['groupNet'] === true && $data['currency']['symbol'] === '€'
    && $data['texts']['priceAltGross'] === 'incl. %s %% VAT' && $data['searchPlaceholder'] === 'What are you looking for?' ? 'ok' : $json, ['ok']);
$GLOBALS['langCode'] = 'ger'; $GLOBALS['groupNet'] = false;

// 18) Eigene Sprachvariable überschreibt eingebauten Text
$GLOBALS['customLang'] = ['pchm_cart' => 'Dein Warenkorb'];
$s = smarty();
check('Sprachvariable pchm_cart', $s->fetch('string:{pchm_lang key="cart"}'), ['Dein Warenkorb']);
$GLOBALS['customLang'] = [];

// 19) lang:-Referenz im Text
$s = smarty();
check('lang:-Referenz', $s->fetch('string:{"lang:pchm_hinweis"|pchm_text}'), ['[custom.pchm_hinweis]']);

// 20) Konfigurator: Daten-JSON, Typen, Spezifikationen aus Funktionsattributen/Merkmalen, Navigation, 3D-Platz, OPC
class StubValue { public function __construct(private string $v) {} public function getValue() { return $this->v; } }
class StubMerkmal { public function __construct(private string $n, private string $v) {} public function getName() { return $this->n; } public function getCharacteristicValues() { return [new StubValue($this->v)]; } }
class StubProduct { public $FunktionsAttribute = []; public $oMerkmale_arr = []; }
class StubItem {
    public function __construct(private int $id, private string $name, private ?StubProduct $p) {}
    public function getKonfigitem() { return $this->id; } public function getName() { return $this->name; } public function getArtikel() { return $this->p; }
}
class StubLoc { public function __construct(private string $n) {} public function getName() { return $this->n; } }
class StubGroup {
    public $oItem_arr;
    public function __construct(private int $id, private string $name, array $items, private int $min = 1) { $this->oItem_arr = $items; }
    public function getID() { return $this->id; } public function getSprache() { return new StubLoc($this->name); }
    public function getMin() { return $this->min; } public function getMax() { return 1; }
}
$cpu = new StubProduct(); $cpu->oMerkmale_arr = [new StubMerkmal('Sockel', 'AM 5'), new StubMerkmal('TDP', '120 W')];
$board = new StubProduct(); $board->FunktionsAttribute = ['PCHM_SOCKET' => 'lga1851', 'pchm_ram_type' => 'ddr5'];
$board->oMerkmale_arr = [new StubMerkmal('Sockel', 'AM5')];
$case = new StubProduct(); $case->oMerkmale_arr = [new StubMerkmal('Max. Grafikkartenlänge', '360 mm')];
$artikelCfg = new stdClass();
$artikelCfg->oKonfig_arr = [
    new StubGroup(11, 'Prozessor', [new StubItem(101, 'AMD Ryzen 7 9800X3D', $cpu)]),
    new StubGroup(12, 'Mainboard', [new StubItem(201, 'MSI MAG B850 TOMAHAWK', $board)]),
    new StubGroup(13, 'Gehäuse', [new StubItem(301, 'NZXT H6 Flow', $case)]),
    new StubGroup(14, 'Zubehör', [new StubItem(401, 'Arctic P12 Lüfter 3er Pack', null)], 0),
];
$html = render($chain('productdetails/config_options_list.tpl'), ['Artikel' => $artikelCfg]);
preg_match('#<script type="application/json" id="pchm-cfg-data">(.*?)</script>#s', $html, $m);
$cfgData = json_decode($m[1] ?? 'null', true);
check('Konfigurator: JSON vorhanden + NOVA-Gruppen erhalten', $html, ['id="pchm-cfg-data"', 'id="pchm-cfg-nav"', 'nova-cfg-groups', 'GRUPPE']);
$g = $cfgData['groups'] ?? [];
check('Konfigurator: Gruppentypen (Name → Typ)', (($g[0]['type'] ?? '') === 'CPU' && ($g[1]['type'] ?? '') === 'Mainboard'
    && ($g[2]['type'] ?? '') === 'Gehäuse' && ($g[3]['type'] ?? '') === 'Kühlung') ? 'ok' : json_encode($g), ['ok']);
check('Konfigurator: Merkmale normalisiert (AM 5 → AM5, 120 W → 120)', (($g[0]['items']['101']['spec']['socket'] ?? '') === 'AM5'
    && ($g[0]['items']['101']['spec']['tdp_watts'] ?? 0) === 120) ? 'ok' : json_encode($g[0] ?? null), ['ok']);
check('Konfigurator: Funktionsattribut schlägt Merkmal', (($g[1]['items']['201']['spec']['socket'] ?? '') === 'LGA1851'
    && ($g[1]['items']['201']['spec']['ram_type'] ?? '') === 'DDR5') ? 'ok' : json_encode($g[1] ?? null), ['ok']);
check('Konfigurator: Gehäusemaß aus Merkmal', (($g[2]['items']['301']['spec']['max_gpu_length_mm'] ?? 0) === 360) ? 'ok' : json_encode($g[2] ?? null), ['ok']);
check('Konfigurator: Pflicht/optional', (($g[0]['min'] ?? -1) === 1 && ($g[3]['min'] ?? -1) === 0) ? 'ok' : 'falsch', ['ok']);
check('Konfigurator: kein 3D-Platz bei „automatisch“', $html, [], ['data-pchm-3d']);
$GLOBALS['tplConf']['pchm_konfigurator']['mount_3d'] = 'top';
$html = render($chain('productdetails/config_options_list.tpl'), ['Artikel' => $artikelCfg]);
check('Konfigurator: fester 3D-Platz für das Plugin', $html, ['<div class="pchm-cfg-3d" data-pchm-3d></div>']);
$GLOBALS['tplConf']['pchm_konfigurator']['mount_3d'] = 'auto';
$GLOBALS['tplConf']['pchm_konfigurator']['compat'] = 'N';
$GLOBALS['tplConf']['pchm_konfigurator']['group_nav'] = 'N';
$html = render($chain('productdetails/config_options_list.tpl'), ['Artikel' => $artikelCfg]);
check('Konfigurator: abgeschaltet → nur NOVA', $html, ['nova-cfg-groups'], ['pchm-cfg-data', 'pchm-cfg-nav']);
$GLOBALS['tplConf']['pchm_konfigurator']['compat'] = 'Y';
$GLOBALS['tplConf']['pchm_konfigurator']['group_nav'] = 'Y';
// Skript nur auf Konfigurator-Artikeln
$withCfg = new stdClass(); $withCfg->bHasKonfig = true;
$html = render($chain('layout/footer.tpl'), ['Artikel' => $withCfg, 'nSeitenTyp' => 1, 'currentTemplateDir' => 'templates/PCHM/', 'nTemplateVersion' => '2.0.0']);
check('Konfigurator-Skript auf Konfigurator-Artikel', $html, ['templates/PCHM/js/pchm-konfigurator.js?v=2.0.0']);
$noCfg = new stdClass(); $noCfg->bHasKonfig = false;
$html = render($chain('layout/footer.tpl'), ['Artikel' => $noCfg, 'nSeitenTyp' => 1]);
check('Kein Konfigurator-Skript auf normalen Artikeln', $html, [], ['pchm-konfigurator.js']);

echo $fails === 0 ? "\nAlle Tests bestanden.\n" : "\n$fails Test(s) fehlgeschlagen.\n";
exit($fails ? 1 : 0);
