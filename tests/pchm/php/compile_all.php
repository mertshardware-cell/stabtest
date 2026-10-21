<?php
// Pfade: siehe tests/pchm/run-tests.sh (Umgebungsvariablen oder .cache/)
$cache   = __DIR__ . '/../.cache';
$novaDir = rtrim(getenv('JTL_NOVA_DIR') ?: $cache . '/jtl-shop/templates/NOVA', '/') . '/';
$smarty4 = rtrim(getenv('SMARTY4_DIR') ?: $cache . '/smarty4', '/') . '/';
$sccDir  = rtrim(getenv('SCC_DIR') ?: $cache . '/scc', '/') . '/';
$child   = realpath(__DIR__ . '/../../../templates/PCHM') . '/';
// Kompiliert alle Child-Templates (inkl. Vererbungskette zu NOVA) mit JTLs Smarty-4-Fork + scc-Komponenten.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require $smarty4 . 'libs/Smarty.class.php';
spl_autoload_register(function ($c) use ($sccDir) {
    if (str_starts_with($c, 'scc\\')) {
        $f = $sccDir . 'src/' . str_replace('\\', '/', $c) . '.php';
        if (is_file($f)) require $f;
    }
});
$nova = $novaDir;

function harness_dummy_block($params, $content = null) { return (string)$content; }
function harness_dummy_modifier($v = null, ...$args) { return is_scalar($v) ? (string)$v : ''; }
function harness_dummy_function($params = [], $tpl = null) { return ''; }

function makeSmarty(string $nova, string $child): Smarty {
    $s = new Smarty();
    $s->setCompileDir(sys_get_temp_dir() . '/pchm_compile_' . getmypid());
    $s->setTemplateDir([]);
    $s->addTemplateDir($nova, 'NOVA');
    $s->addTemplateDir($child, 'frontend');
    $s->setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    $s->muteUndefinedOrNullWarnings();
    (new scc\DefaultComponentRegistrator(new scc\Renderer($s)))->registerComponents();
    // Unbekannte JTL-Tags/Modifier (lang, get_static_route, inline_script, …) als Dummy behandeln
    $s->registerDefaultPluginHandler(function ($name, $type, $template, &$callback, &$script, &$cacheable) {
        if ($type === Smarty::PLUGIN_BLOCK) {
            $callback = 'harness_dummy_block';
        } elseif ($type === Smarty::PLUGIN_MODIFIER) {
            $callback = 'harness_dummy_modifier';
        } else {
            $callback = 'harness_dummy_function';
        }
        return true;
    });
    return $s;
}

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($child, FilesystemIterator::SKIP_DOTS));
$errors = 0; $count = 0;
foreach ($files as $file) {
    if ($file->getExtension() !== 'tpl') continue;
    $rel = substr($file->getPathname(), strlen($child));
    $smarty = makeSmarty($nova, $child);
    $resource = is_file($nova . $rel) ? "extends:[NOVA]{$rel}|[frontend]{$rel}" : "[frontend]{$rel}";
    try {
        $tpl = $smarty->createTemplate($resource);
        $tpl->compileTemplateSource();
        $count++;
        echo "OK      $resource\n";
    } catch (Throwable $e) {
        $errors++;
        echo "FEHLER  $resource\n        " . $e->getMessage() . "\n";
    }
}
echo "\n$count kompiliert, $errors Fehler\n";
exit($errors > 0 ? 1 : 0);
