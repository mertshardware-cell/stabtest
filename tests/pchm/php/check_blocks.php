<?php
/**
 * Update-Sicherheit: Jeder Block, den PCHM auf oberster Ebene einer Datei überschreibt, muss in der
 * gleichnamigen NOVA-Datei existieren. Fehlt er (NOVA-Update hat ihn umbenannt/entfernt), würde die
 * Überschreibung still wirkungslos – genau das soll hier vor einem Shop-Update auffallen.
 * Blöcke, die PCHM innerhalb eigener Überschreibungen neu definiert, gehören PCHM und werden übersprungen.
 *
 * Aufruf: php php/check_blocks.php /pfad/zu/templates/NOVA
 */
$nova = rtrim($argv[1] ?? (__DIR__ . '/../.cache/jtl-shop/templates/NOVA'), '/');
$pchm = realpath(__DIR__ . '/../../../templates/PCHM');
if (!is_dir($nova)) {
    fwrite(STDERR, "NOVA nicht gefunden: $nova\n");
    exit(2);
}

/** @return array<int, array{name: string, depth: int}> */
function blocks(string $src): array
{
    $src = preg_replace('/\{\*.*?\*\}/s', '', $src);
    preg_match_all('/\{(\/?)block\b([^}]*)\}/', $src, $m, PREG_SET_ORDER);
    $depth = 0;
    $out   = [];
    foreach ($m as $tag) {
        if ($tag[1] === '/') {
            $depth--;
            continue;
        }
        if (preg_match('/name\s*=\s*([\'"])(.+?)\1/', $tag[2], $n)) {
            $out[] = ['name' => $n[2], 'depth' => $depth];
        }
        $depth++;
    }
    return $out;
}

$checked = 0;
$missing = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pchm, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if ($file->getExtension() !== 'tpl') {
        continue;
    }
    $rel    = substr($file->getPathname(), strlen($pchm) + 1);
    $parent = $nova . '/' . $rel;
    if (!is_file($parent)) {
        continue; // eigene Datei (z. B. snippets/pchm/*), kein NOVA-Gegenstück
    }
    $parentNames = array_column(blocks(file_get_contents($parent)), 'name');
    foreach (blocks(file_get_contents($file->getPathname())) as $b) {
        if ($b['depth'] !== 0) {
            continue;
        }
        $checked++;
        if (!in_array($b['name'], $parentNames, true)) {
            $missing[] = "$rel: {$b['name']}";
        }
    }
}

$version = is_file("$nova/template.xml") && preg_match('/<Version>([^<]+)/', file_get_contents("$nova/template.xml"), $v) ? $v[1] : '?';
foreach ($missing as $line) {
    echo "FEHLT $line\n";
}
echo "$checked überschriebene Blöcke gegen NOVA $version geprüft.\n";
echo $missing ? count($missing) . " Block/Blöcke fehlen in NOVA.\n" : "Alle Blöcke vorhanden.\n";
exit($missing ? 1 : 0);
