<?php
// Minimale Stubs der JTL-Klassen, die Bootstrap.php verwendet
namespace {
    const CONF_NEWS = 113; const CONF_TEMPLATE = 11; const C_WARENKORBPOS_TYP_ARTIKEL = 1;
    const PAGE_BESTELLVORGANG = 11; const PFAD_MEDIAFILES = 'mediafiles/'; const LINKTYP_VERSAND = 6;
    class LangStub {
        public function get($key, $section = 'global') { return "[$section.$key]"; }
        public function gibSektionsWerte($section) { return $GLOBALS['customLang'] ?? []; }
    }
    class LogStub { public function warning($m) { fwrite(STDERR, "LOG: $m\n"); } }
    class ContainerStub { public function getLogService() { return new LogStub(); } }
    class CurrencyStub {
        public function getDecimalSeparator() { return ','; }
        public function getThousandsSeparator() { return '.'; }
        public function getHtmlEntity() { return '&euro;'; }
        public function getForcePlacementBeforeNumber() { return false; }
        public function getConversionFactor() { return 1.0; }
    }
    class CartStub { public function gibGesamtsummeWarenExt($t, $g) { return $GLOBALS['cartSum'] ?? 0.0; } }
    class CustomerStub { public $cVorname = 'Max'; public $cNachname = 'Muster'; public function getID() { return $GLOBALS['customerId'] ?? 0; } }
    class GroupStub { public function isMerchant() { return !empty($GLOBALS['groupNet']); } public function getIsMerchant() { return !empty($GLOBALS['groupNet']) ? 1 : 0; } }
}
namespace JTL {
    class Shop {
        public static function getLanguageCode() { return $GLOBALS['langCode'] ?? 'ger'; }
        public static function Lang() { return new \LangStub(); }
        public static function getSettings($x) { return ['template' => $GLOBALS['tplConf'], 'news' => ['news_benutzen' => 'Y', 'news_anzahl_content' => 3]]; }
        public static function Container() { return new \ContainerStub(); }
        public static function getLanguageID() { return 1; }
    }
}
namespace JTL\Session {
    class Frontend {
        public static function getCurrency() { return new \CurrencyStub(); }
        public static function getCustomerGroup() { return new \GroupStub(); }
        public static function getCart() { return new \CartStub(); }
        public static function getCustomer() { return new \CustomerStub(); }
    }
}
namespace JTL\Catalog\Product {
    class Preise {
        public static function getLocalizedPriceString($p) { return number_format((float)$p, 2, ',', '.') . ' &euro;'; }
    }
}
namespace JTL\Helpers {
    class CMS { public static function getHomeNews($conf) { return new \Illuminate\Support\CollectionStub($GLOBALS['news'] ?? []); } }
    class Category {
        public static function getInstance() { return new self(); }
        public function combinedGetAll() { return $GLOBALS['cats'] ?? []; }
        public function getCategoryById($id) { return $GLOBALS['catsById'][$id] ?? null; }
    }
}
namespace Illuminate\Support {
    class CollectionStub {
        public function __construct(private array $items) {}
        public function slice($o, $l) { return new self(array_slice($this->items, $o, $l, true)); }
        public function values() { return new self(array_values($this->items)); }
        public function all() { return $this->items; }
    }
}
namespace JTL\Template { class Bootstrapper {} }
namespace Template\NOVA {
    class Bootstrap extends \JTL\Template\Bootstrapper {
        protected $smarty;
        public function setSmarty($s) { $this->smarty = $s; }
        public function getSmarty() { return $this->smarty; }
        public function boot(): void { $this->registerPlugins(); }
        protected function registerPlugins(): void {}
    }
}
