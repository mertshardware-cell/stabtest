<?php

declare(strict_types=1);

namespace Template\PCHMTechnik;

use JTL\Catalog\Product\Preise;
use JTL\Helpers\Category;
use JTL\Helpers\CMS;
use JTL\Session\Frontend;
use JTL\Shop;

/**
 * Class Bootstrap
 *
 * NOVA-Child-Template "PCHMTechnik" – registriert die zusätzlichen Smarty-Funktionen,
 * mit denen die Funktionen des bisherigen Technik-Templates auf NOVA nachgebildet werden.
 *
 * Wichtig: Der Verzeichnisname des Templates muss "PCHMTechnik" lauten, sonst passt der
 * Namespace oben nicht und JTL-Shop lädt diese Klasse nicht.
 *
 * @package Template\PCHMTechnik
 */
class Bootstrap extends \Template\NOVA\Bootstrap
{
    /**
     * Eingebaute Texte für neue Oberflächenelemente. Jeder Eintrag kann im Backend unter
     * "Sprachverwaltung" in der Sektion "custom" mit dem Schlüssel "pchm_<key>" überschrieben werden.
     *
     * @var array<string, array<string, string>>
     */
    private const TEXTS = [
        'ger' => [
            'priceGross'        => 'Privat (inkl. MwSt.)',
            'priceNet'          => 'Gewerbe (zzgl. MwSt.)',
            'priceGrossShort'   => 'Brutto',
            'priceNetShort'     => 'Netto',
            'priceSwitchLabel'  => 'Preisanzeige',
            'priceAltGross'     => 'inkl. %s %% MwSt.',
            'priceAltNet'       => 'zzgl. %s %% MwSt.',
            'news'              => 'Neuigkeiten',
            'newsEmpty'         => 'Aktuell gibt es keine Neuigkeiten.',
            'newsAll'           => 'Alle Neuigkeiten',
            'close'             => 'Schließen',
            'cart'              => 'Warenkorb',
            'freeShippingLeft'  => 'Noch %s bis zum kostenlosen Versand',
            'freeShippingDone'  => 'Deine Bestellung wird kostenlos versendet!',
            'toCart'            => 'Zum Warenkorb',
            'toCheckout'        => 'Zur Kasse',
            'total'             => 'Summe',
            'account'           => 'Mein Konto',
            'register'          => 'Neu hier? Jetzt Kundenkonto anlegen',
            'readMore'          => 'Weiterlesen',
            'readLess'          => 'Weniger anzeigen',
            'documents'         => 'Dokumente & Downloads',
            'jumpTo'            => 'Springe zu',
            'showAll'           => 'Alle anzeigen',
            'days'              => 'Tage',
            'hours'             => 'Std.',
            'minutes'           => 'Min.',
            'seconds'           => 'Sek.',
            'dealEndsIn'        => 'Endet in',
            'filterToggle'      => 'Filter ein-/ausblenden',
            'hotline'           => 'Hotline',
            'paymentMethods'    => 'Zahlungsarten',
            'shippingPartners'  => 'Versandpartner',
            'contact'           => 'Kontakt',
            'badgePrepayment'   => 'Vorkasse',
            'badgeInvoice'      => 'Rechnung',
            'badgeFinancing'    => 'Finanzierung',
            'badgeCash'         => 'Barzahlung',
            'badgeFreight'      => 'Spedition',
        ],
        'eng' => [
            'priceGross'        => 'Private (incl. VAT)',
            'priceNet'          => 'Business (excl. VAT)',
            'priceGrossShort'   => 'Gross',
            'priceNetShort'     => 'Net',
            'priceSwitchLabel'  => 'Prices',
            'priceAltGross'     => 'incl. %s %% VAT',
            'priceAltNet'       => 'excl. %s %% VAT',
            'news'              => 'News',
            'newsEmpty'         => 'There is no news right now.',
            'newsAll'           => 'All news',
            'close'             => 'Close',
            'cart'              => 'Cart',
            'freeShippingLeft'  => '%s left until free shipping',
            'freeShippingDone'  => 'Your order ships for free!',
            'toCart'            => 'View cart',
            'toCheckout'        => 'Checkout',
            'total'             => 'Total',
            'account'           => 'My account',
            'register'          => 'New here? Create an account',
            'readMore'          => 'Read more',
            'readLess'          => 'Show less',
            'documents'         => 'Documents & downloads',
            'jumpTo'            => 'Jump to',
            'showAll'           => 'Show all',
            'days'              => 'days',
            'hours'             => 'hrs',
            'minutes'           => 'min',
            'seconds'           => 'sec',
            'dealEndsIn'        => 'Ends in',
            'filterToggle'      => 'Show/hide filters',
            'hotline'           => 'Hotline',
            'paymentMethods'    => 'Payment methods',
            'shippingPartners'  => 'Shipping partners',
            'contact'           => 'Contact',
            'badgePrepayment'   => 'Prepayment',
            'badgeInvoice'      => 'Invoice',
            'badgeFinancing'    => 'Financing',
            'badgeCash'         => 'Cash',
            'badgeFreight'      => 'Freight forwarding',
        ],
    ];

    /**
     * ISO-639-1 (Einstellungen) auf JTL-Sprachcodes abbilden.
     *
     * @var array<string, string>
     */
    private const ISO_MAP = [
        'de' => 'ger', 'en' => 'eng', 'fr' => 'fre', 'nl' => 'dut', 'it' => 'ita',
        'es' => 'spa', 'pl' => 'pol', 'cs' => 'cze', 'da' => 'dan', 'sv' => 'swe',
    ];

    /**
     * Lokal eingebundene Schriften (themes/pchm-technik/fonts, siehe sass/_fonts.scss).
     *
     * @var array<string, string>
     */
    private const FONTS = [
        'inter'         => 'PCHM Inter',
        'roboto'        => 'PCHM Roboto',
        'lato'          => 'PCHM Lato',
        'open-sans'     => 'PCHM Open Sans',
        'montserrat'    => 'PCHM Montserrat',
        'poppins'       => 'PCHM Poppins',
        'titillium-web' => 'PCHM Titillium Web',
        'exo-2'         => 'PCHM Exo 2',
        'rajdhani'      => 'PCHM Rajdhani',
        'oswald'        => 'PCHM Oswald',
    ];

    private const FONT_FALLBACK = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

    /**
     * Farbeinstellung (Sektion pchm_design) => CSS-Variable
     *
     * @var array<string, string>
     */
    private const COLOR_VARS = [
        'color_primary'          => '--pchm-primary',
        'color_primary_hover'    => '--pchm-primary-hover',
        'color_primary_contrast' => '--pchm-primary-contrast',
        'color_secondary'        => '--pchm-secondary',
        'color_topbar_bg'        => '--pchm-topbar-bg',
        'color_topbar_text'      => '--pchm-topbar-text',
        'color_header_bg'        => '--pchm-header-bg',
        'color_header_text'      => '--pchm-header-text',
        'color_nav_bg'           => '--pchm-nav-bg',
        'color_nav_text'         => '--pchm-nav-text',
        'color_price'            => '--pchm-price',
        'color_special'          => '--pchm-special',
        'color_success'          => '--pchm-success',
        'color_body_bg'          => '--pchm-body-bg',
        'color_footer_bg'        => '--pchm-footer-bg',
        'color_footer_text'      => '--pchm-footer-text',
        'color_footer_heading'   => '--pchm-footer-heading',
    ];

    /**
     * @var array<string, string>|null
     */
    private ?array $customLangVars = null;

    protected function registerPlugins(): void
    {
        parent::registerPlugins();
        $smarty = $this->getSmarty();
        if ($smarty === null) {
            return;
        }
        // 'function' / 'modifier' statt Klassenkonstanten: funktioniert mit Smarty 4 (Legacy-Modus) und Smarty 5.
        $smarty->registerPlugin('modifier', 'pchm_text', $this->text(...))
            ->registerPlugin('function', 'pchm_lang', $this->lang(...))
            ->registerPlugin('function', 'pchm_news', $this->news(...))
            ->registerPlugin('function', 'pchm_categories', $this->categories(...))
            ->registerPlugin('function', 'pchm_list', $this->splitList(...))
            ->registerPlugin('function', 'pchm_timestamp', $this->timestamp(...))
            ->registerPlugin('function', 'pchm_css_vars', $this->cssVars(...))
            ->registerPlugin('function', 'pchm_font_files', $this->fontFiles(...))
            ->registerPlugin('function', 'pchm_config', $this->config(...))
            ->registerPlugin('function', 'pchm_js_config', $this->jsConfig(...))
            ->registerPlugin('function', 'pchm_free_shipping', $this->freeShipping(...));
    }

    /**
     * {pchm_config assign='pchmConf'} – Template-Einstellungen, auch in AJAX/IO-Antworten
     * (dort enthält $Einstellungen keine Template-Sektion).
     *
     * @param array<string, mixed> $params
     */
    public function config(array $params, mixed $template): string
    {
        $template->assign($params['assign'] ?? 'pchmConf', $this->getTemplateConfig());

        return '';
    }

    /**
     * {pchm_js_config} – Konfiguration für js/custom.js als JSON (window.pchmConfig).
     *
     * @param array<string, mixed> $params
     */
    public function jsConfig(array $params, mixed $template): string
    {
        $conf     = $this->getTemplateConfig();
        $header   = (array)($conf['pchm_header'] ?? []);
        $mega     = (array)($conf['pchm_megamenu'] ?? []);
        $details  = (array)($conf['pchm_details'] ?? []);
        $list     = (array)($conf['pchm_list'] ?? []);
        $currency = Frontend::getCurrency();
        $texts    = [];
        foreach (['priceAltGross', 'priceAltNet', 'days', 'hours', 'minutes', 'seconds', 'readMore', 'readLess',
                     'jumpTo', 'close', 'filterToggle'] as $key) {
            $texts[$key] = $this->lang(['key' => $key], $template);
        }
        $data = [
            'groupNet'          => Frontend::getCustomerGroup()->isMerchant(),
            'priceSwitch'       => ($header['price_switch'] ?? 'N') === 'Y',
            'sideBasket'        => ($header['side_basket'] ?? 'N') === 'Y',
            'sideBasketOpen'    => ($header['side_basket_autoopen'] ?? 'N') === 'Y',
            'sideAccount'       => ($header['side_account'] ?? 'N') === 'Y',
            'sticky'            => (string)($header['sticky'] ?? 'all'),
            'searchPlaceholder' => $this->text($header['search_placeholder'] ?? ''),
            'megaMenuMode'      => (string)($mega['open_mode'] ?? 'hover'),
            'tabJumpNav'        => ($details['tab_jumpnav'] ?? 'N') === 'Y',
            'stickyBuyBar'      => ($details['sticky_buybar'] ?? 'N') === 'Y',
            'buttonTotal'       => ($details['button_total'] ?? 'N') === 'Y',
            'descriptionFold'   => ($list['description_collapse'] ?? 'N') === 'Y',
            'sidebarToggle'     => ($list['sidebar_toggle'] ?? 'N') === 'Y',
            'currency'          => [
                'decimal'   => $currency->getDecimalSeparator(),
                'thousands' => $currency->getThousandsSeparator(),
                'symbol'    => \html_entity_decode($currency->getHtmlEntity(), \ENT_QUOTES | \ENT_HTML5, 'UTF-8'),
                'before'    => (bool)$currency->getForcePlacementBeforeNumber(),
            ],
            'texts'             => $texts,
        ];

        return (string)\json_encode(
            $data,
            \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * {pchm_free_shipping assign='pchmFree'} – Fortschritt bis zur Versandkostenfreiheit (Bruttowarenwert).
     *
     * @param array<string, mixed> $params
     */
    public function freeShipping(array $params, mixed $template): string
    {
        $basket    = (array)($this->getTemplateConfig()['pchm_basket'] ?? []);
        $threshold = (float)\str_replace(',', '.', (string)($basket['free_shipping_from'] ?? '0'));
        $result    = ['active' => false];
        if (($basket['free_shipping_bar'] ?? 'N') === 'Y' && $threshold > 0) {
            $sum     = (float)Frontend::getCart()->gibGesamtsummeWarenExt([\C_WARENKORBPOS_TYP_ARTIKEL], true);
            $missing = \max(0.0, $threshold - $sum);
            $result  = [
                'active'           => true,
                'threshold'        => $threshold,
                'sum'              => $sum,
                'missing'          => $missing,
                'missingLocalized' => Preise::getLocalizedPriceString($missing),
                'percent'          => (int)\min(100, \round($sum / $threshold * 100)),
                'reached'          => $missing <= 0.0,
            ];
        }
        $template->assign($params['assign'] ?? 'pchmFree', $result);

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function getTemplateConfig(): array
    {
        return (array)(Shop::getSettings([\CONF_TEMPLATE])['template'] ?? []);
    }

    /**
     * {pchm_css_vars config=$Einstellungen.template} – gibt bereinigte CSS-Variablen (:root) aus.
     *
     * @param array<string, mixed> $params
     */
    public function cssVars(array $params, mixed $template): string
    {
        $config = (array)($params['config'] ?? []);
        $design = (array)($config['pchm_design'] ?? []);
        $header = (array)($config['pchm_header'] ?? []);
        $mega   = (array)($config['pchm_megamenu'] ?? []);
        $vars   = [];
        foreach (self::COLOR_VARS as $setting => $var) {
            $color = $this->sanitizeColor((string)($design[$setting] ?? ''));
            if ($color !== '') {
                $vars[$var] = $color;
            }
        }
        foreach (['announcement_bg' => '--pchm-announcement-bg', 'announcement_color' => '--pchm-announcement-color'] as $k => $var) {
            $color = $this->sanitizeColor((string)($header[$k] ?? ''));
            if ($color !== '') {
                $vars[$var] = $color;
            }
        }
        foreach (['font_body' => '--pchm-font-body', 'font_heading' => '--pchm-font-heading'] as $k => $var) {
            $slug = (string)($design[$k] ?? 'nova');
            if (isset(self::FONTS[$slug])) {
                $vars[$var] = '"' . self::FONTS[$slug] . '", ' . self::FONT_FALLBACK;
            } elseif ($slug === 'system') {
                $vars[$var] = self::FONT_FALLBACK;
            }
        }
        if (($design['border_radius'] ?? '') !== '') {
            $vars['--pchm-radius'] = \max(0, \min(40, (int)$design['border_radius'])) . 'px';
        }
        if ((int)($design['container_max_width'] ?? 0) > 0) {
            $vars['--pchm-container-max'] = \max(960, (int)$design['container_max_width']) . 'px';
        }
        if (($design['heading_uppercase'] ?? 'N') === 'Y') {
            $vars['--pchm-heading-transform'] = 'uppercase';
        }
        $cols                     = \max(3, \min(6, (int)($mega['columns'] ?? 4)));
        $vars['--pchm-mm-col']    = \round(100 / $cols, 4) . '%';
        $css                      = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }

        return $css . '}';
    }

    /**
     * {pchm_font_files config=$Einstellungen.template assign='fonts'} – WOFF2-Dateien der gewählten Schriften (Preload).
     *
     * @param array<string, mixed> $params
     */
    public function fontFiles(array $params, mixed $template): string
    {
        $design = (array)(((array)($params['config'] ?? []))['pchm_design'] ?? []);
        $files  = [];
        foreach ([(string)($design['font_body'] ?? ''), (string)($design['font_heading'] ?? '')] as $slug) {
            if (!isset(self::FONTS[$slug])) {
                continue;
            }
            foreach ([400, 700] as $weight) {
                $files[] = $slug . '-latin-' . $weight . '-normal.woff2';
            }
        }
        $template->assign($params['assign'] ?? 'pchmFontFiles', \array_values(\array_unique($files)));

        return '';
    }

    /**
     * Erlaubt Hex-, rgb()/rgba()/hsl()-Farben und Farbnamen – alles andere wird verworfen.
     */
    private function sanitizeColor(string $color): string
    {
        $color = \trim($color);
        if (\preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) === 1
            || \preg_match('/^(rgb|rgba|hsl|hsla)\([0-9.,%\s\/]+\)$/', $color) === 1
            || \preg_match('/^[a-zA-Z]{3,20}$/', $color) === 1
        ) {
            return $color;
        }

        return '';
    }

    /**
     * Mehrsprachige Texte aus Template-Einstellungen auflösen.
     *
     * Unterstützte Formate:
     *  - "Einfacher Text"                        → wird unverändert ausgegeben
     *  - "de=Deutscher Text || en=English text"  → Text der aktuellen Sprache (Fallback: erster Eintrag)
     *  - "lang:schluessel" / "lang:sektion:key"  → JTL-Sprachvariable (Standardsektion "custom")
     */
    public function text(mixed $value): string
    {
        $value = \trim((string)$value);
        if ($value === '') {
            return '';
        }
        if (\str_starts_with($value, 'lang:')) {
            $parts   = \explode(':', \substr($value, 5));
            $key     = \array_pop($parts);
            $section = $parts[0] ?? 'custom';

            return Shop::Lang()->get($key, $section);
        }
        if (!\str_contains($value, '||') && \preg_match('/^[a-z]{2,3}=/', $value) !== 1) {
            return $value;
        }
        $current      = Shop::getLanguageCode();
        $translations = [];
        foreach (\explode('||', $value) as $chunk) {
            $chunk = \trim($chunk);
            if (\preg_match('/^([a-z]{2,3})=(.*)$/su', $chunk, $hits) === 1) {
                $iso                = self::ISO_MAP[$hits[1]] ?? $hits[1];
                $translations[$iso] = \trim($hits[2]);
            } elseif ($chunk !== '') {
                $translations[] = $chunk;
            }
        }
        if ($translations === []) {
            return $value;
        }

        return $translations[$current] ?? \reset($translations);
    }

    /**
     * {pchm_lang key='cart'} bzw. {pchm_lang key='freeShippingLeft' printf='12,00 €'}
     *
     * @param array<string, mixed> $params
     */
    public function lang(array $params, mixed $template): string
    {
        $key    = (string)($params['key'] ?? '');
        $custom = $this->getCustomLangVars();
        $text   = $custom['pchm_' . $key]
            ?? self::TEXTS[Shop::getLanguageCode()][$key]
            ?? self::TEXTS['eng'][$key]
            ?? $key;
        if (isset($params['printf'])) {
            $text = \sprintf($text, ...\array_map('strval', (array)$params['printf']));
        }
        if (!empty($params['assign'])) {
            $template->assign($params['assign'], $text);

            return '';
        }

        return $text;
    }

    /**
     * {pchm_news limit=5 assign='pchmNews'} – aktuelle News (gleiche Logik wie die NOVA-Startseite).
     *
     * @param array<string, mixed> $params
     */
    public function news(array $params, mixed $template): string
    {
        $items = [];
        try {
            $conf  = Shop::getSettings([\CONF_NEWS]);
            $limit = \max(1, (int)($params['limit'] ?? 5));
            if (($conf['news']['news_benutzen'] ?? 'N') === 'Y') {
                $conf['news']['news_anzahl_content'] = $limit;
                $items                               = CMS::getHomeNews($conf)->slice(0, $limit)->values()->all();
            }
        } catch (\Throwable $e) {
            Shop::Container()->getLogService()->warning('PCHMTechnik news: ' . $e->getMessage());
        }
        $template->assign($params['assign'] ?? 'pchmNews', $items);

        return '';
    }

    /**
     * {pchm_categories ids='12,15,20' limit=8 assign='pchmCategories'}
     * Ohne IDs werden die Hauptkategorien geliefert.
     *
     * @param array<string, mixed> $params
     */
    public function categories(array $params, mixed $template): string
    {
        $limit  = (int)($params['limit'] ?? 0);
        $ids    = \array_filter(\array_map('intval', \explode(',', (string)($params['ids'] ?? ''))));
        $helper = Category::getInstance();
        $list   = [];
        try {
            if ($ids === []) {
                $list = \array_values(\array_filter(
                    $helper->combinedGetAll(),
                    static fn($item): bool => \method_exists($item, 'isOrphaned') ? !$item->isOrphaned() : true
                ));
            } else {
                foreach ($ids as $id) {
                    $item = $helper->getCategoryById($id);
                    if ($item !== null) {
                        $list[] = $item;
                    }
                }
            }
        } catch (\Throwable $e) {
            Shop::Container()->getLogService()->warning('PCHMTechnik categories: ' . $e->getMessage());
        }
        if ($limit > 0) {
            $list = \array_slice($list, 0, $limit);
        }
        $template->assign($params['assign'] ?? 'pchmCategories', $list);

        return '';
    }

    /**
     * {pchm_list value='a, b ,c' assign='items'} – kommagetrennte Einstellung als bereinigtes Array.
     *
     * @param array<string, mixed> $params
     */
    public function splitList(array $params, mixed $template): string
    {
        $separator = (string)($params['separator'] ?? ',');
        $items     = \array_values(\array_filter(
            \array_map('trim', \explode($separator, (string)($params['value'] ?? ''))),
            static fn(string $item): bool => $item !== ''
        ));
        $template->assign($params['assign'] ?? 'pchmList', $items);

        return '';
    }

    /**
     * {pchm_timestamp value='2026-11-27 23:59' assign='ts'} – Datum (Serverzeitzone) als Unix-Timestamp, 0 bei Fehler.
     *
     * @param array<string, mixed> $params
     */
    public function timestamp(array $params, mixed $template): string
    {
        $value = \trim((string)($params['value'] ?? ''));
        $ts    = $value === '' ? 0 : (int)\strtotime($value);
        // reine Datumsangaben (z. B. Sonderpreis-Ende "2026-11-30") gelten bis zum Ende des Tages
        if ($ts > 0 && !empty($params['endOfDay']) && \preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            $ts += 86399;
        }
        $template->assign($params['assign'] ?? 'pchmTimestamp', \max(0, $ts));

        return '';
    }

    /**
     * Eigene Sprachvariablen (Sektion "custom", Präfix "pchm_") einmal pro Request laden.
     *
     * @return array<string, string>
     */
    private function getCustomLangVars(): array
    {
        if ($this->customLangVars !== null) {
            return $this->customLangVars;
        }
        $this->customLangVars = [];
        try {
            foreach (Shop::Lang()->gibSektionsWerte('custom') as $name => $value) {
                if (\str_starts_with((string)$name, 'pchm_')) {
                    $this->customLangVars[(string)$name] = (string)$value;
                }
            }
        } catch (\Throwable) {
            // Sektion "custom" existiert nicht oder DB nicht erreichbar – eingebaute Texte verwenden
        }

        return $this->customLangVars;
    }
}
