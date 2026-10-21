<?php
/**
 * Smarty-5-Vorbereitung: Ab JTL-Shop 5.6 läuft Smarty 5 (noch mit SMARTY_LEGACY_MODE). Ohne Legacy-Modus
 * dürfen Templates nur Smarty-eigene Modifier/Funktionen, JTLs registrierte Plugins und die
 * SECURE_PHP_FUNCTIONS verwenden. Dieses Skript findet alles andere in templates/PCHM.
 *
 * Aufruf: php php/check_smarty5.php [/pfad/zum/shop]   (mit Shop-Pfad wird dessen defines.php gelesen)
 */
$pchm  = realpath(__DIR__ . '/../../../templates/PCHM');
$shop  = $argv[1] ?? '';
$list  = $shop !== '' && is_file("$shop/includes/defines.php")
    && preg_match("/ifndef\('SECURE_PHP_FUNCTIONS',\s*'([^']+)'/", file_get_contents("$shop/includes/defines.php"), $m)
    ? $m[1]
    : implode(',', array_filter(file(__DIR__ . '/secure_php_functions.txt', FILE_IGNORE_NEW_LINES), fn($l) => $l !== '' && $l[0] !== '#'));
$secure = array_map('trim', explode(',', $list));

// Smarty 5 (Smarty\Extension\CoreExtension/DefaultExtension) + Sprachkonstrukte im Compiler
$smarty5 = ['cat', 'capitalize', 'count', 'count_characters', 'count_paragraphs', 'count_sentences', 'count_words',
    'date_format', 'debug_print_var', 'default', 'escape', 'explode', 'from_charset', 'implode', 'in_array', 'indent',
    'is_array', 'isset', 'empty', 'join', 'json_encode', 'lower', 'mb_wordwrap', 'nl2br', 'noprint', 'number_format',
    'regex_replace', 'replace', 'round', 'spacify', 'split', 'str_repeat', 'string_format', 'strip', 'strip_tags',
    'strlen', 'substr', 'to_charset', 'truncate', 'unescape', 'upper', 'wordwrap', 'array'];
// JTL PluginCollection (Modifier/Funktionen) – nur die von PCHM genutzten Template-Funktionen müssen hier stehen
$jtl = ['replace_delim', 'string_date_format', '__', 'd__', 'n__', 'trans', 'lang', 'get_static_route', 'url'];

$allowed = array_flip(array_merge($secure, $smarty5, $jtl));
$bad     = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pchm, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if ($file->getExtension() !== 'tpl') {
        continue;
    }
    $src = preg_replace('/\{\*.*?\*\}/s', '', file_get_contents($file->getPathname()));
    // Smarty-Tags: { gefolgt von $, Buchstabe oder / – CSS/JS-Klammern mit Leerzeichen/Zeilenumbruch fallen raus
    preg_match_all('/\{[$\/a-zA-Z][^{}\n]*\}/', $src, $tags);
    foreach ($tags[0] as $tag) {
        $tag = preg_replace('/([\'"]).*?\1/', '""', $tag); // Strings ignorieren
        preg_match_all('/\|@?([a-zA-Z_]\w*)/', $tag, $mods);
        preg_match_all('/(?<![\w$>:])([a-zA-Z_]\w*)\s*\(/', $tag, $calls);
        foreach (array_merge($mods[1], $calls[1]) as $name) {
            if (str_starts_with($name, 'pchm_') || isset($allowed[$name])) {
                continue;
            }
            $bad[] = substr($file->getPathname(), strlen($pchm) + 1) . ": $name in $tag";
        }
    }
}
foreach (array_unique($bad) as $line) {
    echo "NICHT ERLAUBT $line\n";
}
echo count($secure) . " sichere PHP-Funktionen geladen.\n";
echo $bad ? count(array_unique($bad)) . " Verwendung(en) ohne Smarty-5-Freigabe.\n" : "Alle Modifier/Funktionen sind unter Smarty 5 erlaubt.\n";
exit($bad ? 1 : 0);
