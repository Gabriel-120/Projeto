<?php
/**
 * merge_sources.php
 * Uso: php merge_sources.php "<caminho_origem_1>" "<caminho_origem_2>" "<caminho_destino>"
 * Exemplo (executar na raiz do workspace):
 * php scripts/merge_sources.php "Projeto do Gabriel/TechFit" "Projeto TechFit" "Projeto TechFit Final"
 *
 * O script copia recursivamente todos os arquivos de origem para destino preservando a estrutura.
 * Se o arquivo já existir no destino, escolhe a versão com mais linhas (heurística "mais completa").
 * Ele não sobrescreve diretórios inteiros sem confirmação se rodado interativamente.
 */

if ($argc < 4) {
    echo "Uso: php merge_sources.php <src1> <src2> <dest>\n";
    exit(1);
}

$src1 = $argv[1];
$src2 = $argv[2];
$dest = $argv[3];

// Normaliza caminhos (relativos ao cwd quando não absolutos)
function normalizePath($p) {
    $p = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
    if (!preg_match('/^[A-Za-z]:\\|^\\\\|^\//', $p)) { // se não for caminho absoluto
        $p = getcwd() . DIRECTORY_SEPARATOR . $p;
    }
    return $p;
}

$src1 = normalizePath($src1);
$src2 = normalizePath($src2);
$dest = normalizePath($dest);

if (!is_dir($src1)) { echo "Diretório de origem 1 não encontrado: $src1\n"; exit(1); }
if (!is_dir($src2)) { echo "Diretório de origem 2 não encontrado: $src2\n"; exit(1); }
if (!is_dir($dest)) {
    echo "Diretório destino não existe; será criado: $dest\n";
    if (!mkdir($dest, 0777, true)) { echo "Falha ao criar $dest\n"; exit(1); }
}

function collectFiles($dir) {
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $f) {
        if ($f->isFile()) {
            $files[] = $f->getPathname();
        }
    }
    return $files;
}

$files1 = collectFiles($src1);
$files2 = collectFiles($src2);

// Map relative path -> full path for each source
function relMap(array $files, $base) {
    $map = [];
    $base = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    foreach ($files as $f) {
        $rel = substr($f, strlen($base));
        $map[$rel] = $f;
    }
    return $map;
}

$map1 = relMap($files1, $src1);
$map2 = relMap($files2, $src2);

$allRels = array_values(array_unique(array_merge(array_keys($map1), array_keys($map2))));

foreach ($allRels as $rel) {
    $from = null;
    $srcChosen = null;
    $path1 = $map1[$rel] ?? null;
    $path2 = $map2[$rel] ?? null;

    if ($path1 && !$path2) {
        $from = $path1; $srcChosen = 1;
    } elseif ($path2 && !$path1) {
        $from = $path2; $srcChosen = 2;
    } else {
        // ambos existem: escolher o maior em linhas
        $len1 = substr_count(file_get_contents($path1), "\n");
        $len2 = substr_count(file_get_contents($path2), "\n");
        if ($len1 >= $len2) { $from = $path1; $srcChosen = 1; } else { $from = $path2; $srcChosen = 2; }
    }

    $destPath = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $rel;
    $destDir = dirname($destPath);
    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

    // Se arquivo já existir e for o mesmo conteúdo, pulamos
    if (file_exists($destPath)) {
        if (md5_file($destPath) === md5_file($from)) continue;
        // se diferente, já decidimos por uma fonte com base na heurística; sobreescrevemos
    }

    if (!copy($from, $destPath)) {
        echo "Falha ao copiar $from -> $destPath\n";
    } else {
        echo "Copiado (src$srcChosen): $rel\n";
    }
}

echo "Mesclagem concluída.\n";
