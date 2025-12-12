<?php
// Usage: php generate_merge_report.php <src1> <src2> <dest>
if ($argc < 4) {
    echo "Usage: php generate_merge_report.php <src1> <src2> <dest>\n";
    exit(1);
}
$src1 = realpath($argv[1]);
$src2 = realpath($argv[2]);
$dest = realpath($argv[3]);
if (!$src1 || !is_dir($src1)) { echo "src1 not found\n"; exit(1); }
if (!$src2 || !is_dir($src2)) { echo "src2 not found\n"; exit(1); }
if (!$dest || !is_dir($dest)) { echo "dest not found\n"; exit(1); }

function collect($base) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
    $map = [];
    foreach ($it as $f) {
        if ($f->isFile()) {
            $rel = substr($f->getPathname(), strlen($base) + 1);
            $map[$rel] = $f->getPathname();
        }
    }
    return $map;
}

$map1 = collect($src1);
$map2 = collect($src2);
$mapd = collect($dest);

$all = array_values(array_unique(array_merge(array_keys($map1), array_keys($map2), array_keys($mapd))));

$report = [];
$conflicts = [];
foreach ($all as $rel) {
    $in1 = isset($map1[$rel]);
    $in2 = isset($map2[$rel]);
    $ind = isset($mapd[$rel]);
    $sourceChosen = '';
    $note = '';
    $md1 = $in1 ? md5_file($map1[$rel]) : '';
    $md2 = $in2 ? md5_file($map2[$rel]) : '';
    $mdd = $ind ? md5_file($mapd[$rel]) : '';
    if ($ind) {
        if ($in1 && $md1 === $mdd) $sourceChosen = 'src1';
        elseif ($in2 && $md2 === $mdd) $sourceChosen = 'src2';
        else $sourceChosen = 'dest-only/modified';
    } else {
        $sourceChosen = $in1 ? 'src1-only' : ($in2 ? 'src2-only' : 'missing');
    }
    if ($in1 && $in2 && $md1 !== $md2) {
        $conflicts[] = $rel;
    }
    $report[] = [$rel, $in1?'Y':'N', $in2?'Y':'N', $ind?'Y':'N', $sourceChosen];
}

$csv = fopen($dest . DIRECTORY_SEPARATOR . 'merge_report.csv', 'w');
if ($csv) {
    fputcsv($csv, ['relative_path','in_src1','in_src2','in_dest','chosen_source']);
    foreach ($report as $r) fputcsv($csv, $r);
    fclose($csv);
    echo "Report written to " . $dest . DIRECTORY_SEPARATOR . "merge_report.csv\n";
    if (!empty($conflicts)) {
        echo "Conflicts (files present in both sources with different contents):\n";
        foreach ($conflicts as $c) echo " - $c\n";
    } else {
        echo "No content conflicts detected (files identical or only in one source).\n";
    }
} else {
    echo "Failed to write report.\n";
}
