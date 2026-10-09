<?php

declare(strict_types=1);

namespace Template\PCHM;

use JTL\Catalog\Product\Preise;
use JTL\Helpers\Category;
use JTL\Helpers\CMS;
use JTL\Session\Frontend;
use JTL\Shop;

/**
 * Class Bootstrap
 *
 * NOVA-Child-Template "PCHM" – eigenes Template von PCHM.de auf Basis von JTL NOVA.
 * Registriert die Smarty-Funktionen des Templates (Regeln: REGELN.md im Template-Ordner).
 *
 * Wichtig: Der Verzeichnisname des Templates muss "PCHM" lauten, sonst passt der
 * Namespace oben nicht und JTL-Shop lädt diese Klasse nicht.
 *
 * @package Template\PCHM
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
            'cfgNav'            => 'Konfigurator-Schritte',
            'cfgProgress'       => '{0} von {1} Gruppen gewählt',
            'cfgRequired'       => 'Pflicht',
            'cfgNotChosen'      => 'noch nicht gewählt',
            'cfgCompatTitle'    => 'Kompatibilität',
            'cfgCompatOk'       => 'Alle hinterlegten Prüfungen bestanden.',
            'cfgCompatBlocked'  => '{0} Konflikt(e) – bitte Auswahl anpassen.',
            'cfgCompatUnknown'  => '{0} Punkt(e) ungeprüft, weil Daten fehlen. Wir prüfen jede Bestellung vor dem Bau.',
            'cfgPsuNeed'        => 'Empfohlenes Netzteil: ab {0} W',
            'cfgCartBlocked'    => 'Bitte zuerst die Konflikte in der Konfiguration lösen.',
            'cfgItemBlocked'    => 'Passt nicht',
            'cfgItemUnknown'    => 'Ungeprüft',
            'cfgSocketOk'       => 'Sockel {0} passt zum Mainboard.',
            'cfgSocketBad'      => 'Sockel {0} passt nicht zum Mainboard ({1}).',
            'cfgSocketUnknown'  => 'Sockel nicht hinterlegt – bitte Mainboard-Sockel prüfen.',
            'cfgGpuOk'          => 'Länge {0} mm passt ins Gehäuse (max. {1} mm).',
            'cfgGpuBad'         => 'Karte ist {0} mm lang, das Gehäuse fasst nur {1} mm.',
            'cfgGpuUnknown'     => 'Gehäusemaß nicht hinterlegt – bitte Einbaulänge prüfen.',
            'cfgRamOk'          => '{0} passt zum Mainboard.',
            'cfgRamBad'         => '{0} passt nicht zum Mainboard ({1}).',
            'cfgRamUnknown'     => 'RAM-Typ nicht hinterlegt – bitte Mainboard prüfen.',
            'cfgCoolerOk'       => 'Bauhöhe {0} mm passt (max. {1} mm).',
            'cfgCoolerBad'      => 'Kühler ist {0} mm hoch, das Gehäuse erlaubt {1} mm.',
            'cfgCoolerUnknown'  => 'Bauhöhe nicht hinterlegt – bitte Gehäuse prüfen.',
            'cfgPsuOk'          => 'Netzteil {0} W reicht (empfohlen ab {1} W).',
            'cfgPsuBad'         => 'Netzteil liefert {0} W, empfohlen sind {1} W.',
            'cfgPsuUnknown'     => 'Leistungsdaten unvollständig – Netzteil bitte prüfen.',
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
            'cfgNav'            => 'Configurator steps',
            'cfgProgress'       => '{0} of {1} groups chosen',
            'cfgRequired'       => 'Required',
            'cfgNotChosen'      => 'not chosen yet',
            'cfgCompatTitle'    => 'Compatibility',
            'cfgCompatOk'       => 'All stored checks passed.',
            'cfgCompatBlocked'  => '{0} conflict(s) – please adjust your selection.',
            'cfgCompatUnknown'  => '{0} item(s) unchecked because data is missing. We check every order before building.',
            'cfgPsuNeed'        => 'Recommended power supply: {0} W or more',
            'cfgCartBlocked'    => 'Please resolve the conflicts in your configuration first.',
            'cfgItemBlocked'    => 'Does not fit',
            'cfgItemUnknown'    => 'Unchecked',
            'cfgSocketOk'       => 'Socket {0} fits the mainboard.',
            'cfgSocketBad'      => 'Socket {0} does not fit the mainboard ({1}).',
            'cfgSocketUnknown'  => 'Socket not stored – please check the mainboard socket.',
            'cfgGpuOk'          => 'Length {0} mm fits the case (max. {1} mm).',
            'cfgGpuBad'         => 'Card is {0} mm long, the case only fits {1} mm.',
            'cfgGpuUnknown'     => 'Case dimensions not stored – please check the card length.',
            'cfgRamOk'          => '{0} fits the mainboard.',
            'cfgRamBad'         => '{0} does not fit the mainboard ({1}).',
            'cfgRamUnknown'     => 'RAM type not stored – please check the mainboard.',
            'cfgCoolerOk'       => 'Height {0} mm fits (max. {1} mm).',
            'cfgCoolerBad'      => 'Cooler is {0} mm tall, the case allows {1} mm.',
            'cfgCoolerUnknown'  => 'Cooler height not stored – please check the case.',
            'cfgPsuOk'          => 'Power supply {0} W is sufficient (recommended {1} W or more).',
            'cfgPsuBad'         => 'Power supply delivers {0} W, {1} W are recommended.',
            'cfgPsuUnknown'     => 'Power data incomplete – please check the power supply.',
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
     * Lokal eingebundene Schriften (themes/pchm/fonts, siehe sass/_fonts.scss).
     *
     * @var array<string, string>
     */
    private const FONTS = [
        'manrope'          => 'PCHM Manrope',
        'roboto-condensed' => 'PCHM Roboto Condensed',
        'inter'            => 'PCHM Inter',
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
        'color_warning'          => '--pchm-warning',
        'color_danger'           => '--pchm-danger',
        'color_body_bg'          => '--pchm-body-bg',
        'color_footer_bg'        => '--pchm-footer-bg',
        'color_footer_text'      => '--pchm-footer-text',
        'color_footer_heading'   => '--pchm-footer-heading',
    ];

    /**
     * Spezifikationsfelder für die Kompatibilitätsprüfung im Konfigurator.
     * Gleiche Namen wie component_catalog der Companion App (api/_hardware.php).
     * Quelle je Komponente: Funktionsattribut "pchm_<feld>" (Vorrang) oder Merkmal
     * (Namen einstellbar unter „PCHM – Konfigurator“).
     *
     * @var array<string, string> feld => 'text' | 'int'
     */
    private const SPEC_FIELDS = [
        'component_type'       => 'text',
        'socket'               => 'code',
        'ram_type'             => 'code',
        'tdp_watts'            => 'int',
        'length_mm'            => 'int',
        'height_mm'            => 'int',
        'max_gpu_length_mm'    => 'int',
        'max_cooler_height_mm' => 'int',
        'psu_watts'            => 'int',
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
            ->registerPlugin('function', 'pchm_free_shipping', $this->freeShipping(...))
            ->registerPlugin('function', 'pchm_cfg_specs', $this->cfgSpecs(...))
            ->registerPlugin('function', 'pchm_cfg_data', $this->cfgData(...));
    }

    /**
     * Komponententypen und Erkennungsmuster – unverändert aus api/_hardware.php der Companion App
     * (pchm_component_types / pchm_component_classifiers). Erste passende Regel gewinnt.
     * Bei Änderungen dort hier nachziehen (siehe REGELN.md, Abschnitt Konfigurator).
     *
     * @return array<int, array{type: string, pattern: string}>
     */
    public static function componentClassifiers(): array
    {
        return [
            ['type' => 'GPU', 'pattern' => '/\b(geforce|radeon|rtx|gtx|arc\s*a\d|rx\s*\d{3,4}|grafikkarten?|graphics\s*cards?)\b/iu'],
            ['type' => 'CPU', 'pattern' => '/\b(ryzen|threadripper|core\s*i[3579]|core\s*ultra|pentium|celeron|xeon|athlon|prozessor(en)?)\b/iu'],
            ['type' => 'Mainboard', 'pattern' => '/\b(mainboards?|motherboards?|hauptplatinen?|[bxzha]\d{3}[a-z]?\s*(chipsatz|wifi|mainboard|pro|plus|tomahawk|gaming)?)\b/iu'],
            ['type' => 'Netzteil', 'pattern' => '/\b(netzteile?n?|power\s*supply|psu|\d{3,4}\s*w(att)?\b.*\b(80\+|atx|bronze|gold|platinum|titanium)|(80\+|atx\s*3)\b.*\d{3,4}\s*w)/iu'],
            ['type' => 'Kühlung', 'pattern' => '/\b(k(ü|ue)hl|cooler|aio|wasserk(ü|ue)hlung|luftk(ü|ue)hler|l(ü|ue)fter|radiator|heatsink)\w*/iu'],
            ['type' => 'Gehäuse', 'pattern' => '/\b(geh(ä|ae)use|cases?|tower|midi[- ]?tower|big[- ]?tower)\b/iu'],
            ['type' => 'SSD', 'pattern' => '/\b(ssds?|nvme|m\.?2|festplatten?|hdd|hard\s*drive|\d+\s*(gb|tb)\s*(nvme|ssd|m\.?2))\b/iu'],
            ['type' => 'RAM', 'pattern' => '/\b(ddr[345]|arbeitsspeicher|\bram\b|dimm|so-dimm)\b/iu'],
            ['type' => 'Windows', 'pattern' => '/\b(windows|betriebssystem(e)?|operating\s*system|win\s*1[01])\b/iu'],
        ];
    }

    /**
     * Typ aus Freitext (Gruppenname oder Artikelname); exakter Typname hat Vorrang.
     */
    public static function classifyComponent(string $text): ?string
    {
        $text = \trim($text);
        if ($text === '') {
            return null;
        }
        foreach (['CPU', 'GPU', 'RAM', 'SSD', 'Mainboard', 'Netzteil', 'Kühlung', 'Gehäuse', 'Windows'] as $type) {
            if (\mb_strtolower($type) === \mb_strtolower($text)) {
                return $type;
            }
        }
        foreach (self::componentClassifiers() as $rule) {
            if (\preg_match($rule['pattern'], $text) === 1) {
                return $rule['type'];
            }
        }

        return null;
    }

    /**
     * {pchm_cfg_data groups=$Artikel->oKonfig_arr assign='json'} – Gruppen, Komponenten, Typen und
     * Spezifikationen des Konfigurators als JSON (wird nur gelesen, ändert nichts am JTL-Formular).
     *
     * Typ einer Gruppe: Funktionsattribut pchm_component_type der Komponenten, sonst Gruppenname,
     * sonst Mehrheit der Artikelnamen (wie pchm_classify_component: gepflegter Wert schlägt Namen).
     *
     * @param array<string, mixed> $params
     */
    public function cfgData(array $params, mixed $template): string
    {
        $groups = [];
        try {
            foreach ((array)($params['groups'] ?? []) as $group) {
                if (!\is_object($group) || !\method_exists($group, 'getID')) {
                    continue;
                }
                $name  = \method_exists($group, 'getSprache') && \is_object($group->getSprache())
                    ? (string)$group->getSprache()->getName()
                    : '';
                $items = [];
                $votes = [];
                foreach ((array)($group->oItem_arr ?? []) as $item) {
                    if (!\is_object($item) || !\method_exists($item, 'getKonfigitem')) {
                        continue;
                    }
                    $product  = \method_exists($item, 'getArtikel') ? $item->getArtikel() : null;
                    $spec     = \is_object($product) ? $this->readSpecs($product) : [];
                    $itemName = (string)($item->getName() ?? '');
                    $type     = isset($spec['component_type'])
                        ? self::classifyComponent((string)$spec['component_type'])
                        : null;
                    if ($type !== null) {
                        $votes['fa:' . $type] = ($votes['fa:' . $type] ?? 0) + 1;
                    }
                    $nameType = self::classifyComponent($itemName);
                    if ($nameType !== null) {
                        $votes[$nameType] = ($votes[$nameType] ?? 0) + 1;
                    }
                    $items[(string)$item->getKonfigitem()] = [
                        'name' => $itemName,
                        'type' => $type ?? $nameType,
                        'spec' => $spec === [] ? new \stdClass() : $spec,
                    ];
                }
                $groupType = null;
                foreach ($votes as $key => $count) {
                    if (\str_starts_with((string)$key, 'fa:')) {
                        $groupType = \substr((string)$key, 3);
                        break;
                    }
                }
                $groupType ??= self::classifyComponent($name);
                if ($groupType === null && $votes !== []) {
                    \arsort($votes);
                    $groupType = (string)\array_key_first($votes);
                }
                $groups[] = [
                    'id'    => (int)$group->getID(),
                    'name'  => $name,
                    'type'  => $groupType,
                    'min'   => \method_exists($group, 'getMin') ? (int)$group->getMin() : 0,
                    'max'   => \method_exists($group, 'getMax') ? (int)$group->getMax() : 0,
                    'items' => $items === [] ? new \stdClass() : $items,
                ];
            }
        } catch (\Throwable $e) {
            Shop::Container()->getLogService()->warning('PCHM cfg data: ' . $e->getMessage());
        }
        $json = (string)\json_encode(
            ['groups' => $groups],
            \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE
        );
        if (!empty($params['assign'])) {
            $template->assign($params['assign'], $json);

            return '';
        }

        return $json;
    }

    /**
     * {pchm_cfg_specs item=$oItem} – Spezifikation einer Konfigurator-Komponente als JSON
     * (für die Kompatibilitätsprüfung in js/custom.js). Liest nur gepflegte Daten aus JTL-Wawi;
     * fehlende Werte bleiben leer und werden im Frontend als „ungeprüft“ gemeldet.
     *
     * @param array<string, mixed> $params
     */
    public function cfgSpecs(array $params, mixed $template): string
    {
        $spec = [];
        try {
            $item    = $params['item'] ?? null;
            $product = \is_object($item) && \method_exists($item, 'getArtikel') ? $item->getArtikel() : null;
            if (\is_object($product)) {
                $spec = $this->readSpecs($product);
            }
        } catch (\Throwable $e) {
            Shop::Container()->getLogService()->warning('PCHM cfg specs: ' . $e->getMessage());
        }
        $json = (string)\json_encode(
            $spec === [] ? new \stdClass() : $spec,
            \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE
        );
        if (!empty($params['assign'])) {
            $template->assign($params['assign'], $json);

            return '';
        }

        return $json;
    }

    /**
     * @return array<string, int|string>
     */
    public function readSpecs(object $product): array
    {
        $konfig    = (array)($this->getTemplateConfig()['pchm_konfigurator'] ?? []);
        $functions = \array_change_key_case((array)($product->FunktionsAttribute ?? []), \CASE_LOWER);
        $merkmale  = [];
        foreach ((array)($product->oMerkmale_arr ?? []) as $characteristic) {
            if (!\is_object($characteristic) || !\method_exists($characteristic, 'getName')) {
                continue;
            }
            $values = \method_exists($characteristic, 'getCharacteristicValues')
                ? (array)$characteristic->getCharacteristicValues()
                : [];
            $first  = \reset($values);
            if (\is_object($first) && \method_exists($first, 'getValue')) {
                $merkmale[\mb_strtolower(\trim((string)$characteristic->getName()))] = (string)$first->getValue();
            }
        }
        $spec = [];
        foreach (self::SPEC_FIELDS as $field => $kind) {
            $raw = $functions['pchm_' . $field] ?? null;
            if ($raw === null || $raw === '') {
                foreach (\explode(',', (string)($konfig['m_' . $field] ?? '')) as $name) {
                    $name = \mb_strtolower(\trim($name));
                    if ($name !== '' && isset($merkmale[$name]) && $merkmale[$name] !== '') {
                        $raw = $merkmale[$name];
                        break;
                    }
                }
            }
            $value = $this->normalizeSpec((string)($raw ?? ''), $kind);
            if ($value !== null) {
                $spec[$field] = $value;
            }
        }

        return $spec;
    }

    /**
     * "320 mm" → 320, "AM 5" → "AM5", "ddr5" → "DDR5"; leere/ungültige Werte → null.
     */
    private function normalizeSpec(string $raw, string $kind): int|string|null
    {
        $raw = \trim($raw);
        if ($raw === '') {
            return null;
        }
        if ($kind === 'int') {
            return \preg_match('/(\d+(?:[.,]\d+)?)/', $raw, $hit) === 1
                ? (int)\round((float)\str_replace(',', '.', $hit[1]))
                : null;
        }
        if ($kind === 'code') {
            return \strtoupper((string)\preg_replace('/[\s\-]+/', '', $raw));
        }

        return \mb_substr($raw, 0, 60);
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
        $konfig   = (array)($conf['pchm_konfigurator'] ?? []);
        $keys     = ['priceAltGross', 'priceAltNet', 'days', 'hours', 'minutes', 'seconds', 'readMore', 'readLess',
            'jumpTo', 'close', 'filterToggle'];
        foreach (\array_keys(self::TEXTS['ger']) as $key) {
            if (\str_starts_with($key, 'cfg')) {
                $keys[] = $key;
            }
        }
        foreach ($keys as $key) {
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
            'konfigurator'      => [
                'groupNav'  => ($konfig['group_nav'] ?? 'Y') === 'Y',
                'compat'    => ($konfig['compat'] ?? 'Y') === 'Y',
                'compatMode'=> ($konfig['compat_mode'] ?? 'mark') === 'disable' ? 'disable' : 'mark',
                'blockCart' => ($konfig['compat_block_cart'] ?? 'Y') === 'Y',
                'psuHint'   => ($konfig['psu_hint'] ?? 'Y') === 'Y',
            ],
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
            $radius                   = \max(0, \min(40, (int)$design['border_radius']));
            $vars['--pchm-radius']    = $radius . 'px';
            $vars['--pchm-radius-lg'] = ($radius > 0 ? $radius + 2 : 0) . 'px';
        }
        if ((int)($design['container_max_width'] ?? 0) > 0) {
            $vars['--pchm-container-max'] = \max(960, (int)$design['container_max_width']) . 'px';
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
            Shop::Container()->getLogService()->warning('PCHM news: ' . $e->getMessage());
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
            Shop::Container()->getLogService()->warning('PCHM categories: ' . $e->getMessage());
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
