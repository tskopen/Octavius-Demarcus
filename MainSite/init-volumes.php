<?php

$galleryVol = '/var/www/site/public/gallery';
$dataVol = '/var/www/site/public/data';
$seedGallery = '/var/www/site/seed/gallery';
$seedData = '/var/www/site/seed/data';

$allowedImageTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

function ensureDir(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function mergeGallery(string $seedDir, string $volDir, array $allowedTypes): int
{
    ensureDir($volDir);

    if (!is_dir($seedDir)) {
        return 0;
    }

    $copied = 0;

    foreach (scandir($seedDir) as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitkeep') {
            continue;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes, true)) {
            continue;
        }

        $target = $volDir . '/' . $file;
        if (!file_exists($target)) {
            copy($seedDir . '/' . $file, $target);
            $copied++;
        }
    }

    return $copied;
}

function travelKey(array $entry): string
{
    return strtolower(implode('|', [
        $entry['title'] ?? '',
        $entry['date'] ?? '',
        $entry['location'] ?? '',
    ]));
}

function loadTravelsFile(string $file): array
{
    if (!file_exists($file)) {
        return [];
    }

    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function mergeTravels(string $seedFile, string $volFile): int
{
    ensureDir(dirname($volFile));

    $seed = loadTravelsFile($seedFile);
    $volume = loadTravelsFile($volFile);

    if (empty($seed) && empty($volume)) {
        return 0;
    }

    if (empty($volume)) {
        file_put_contents(
            $volFile,
            json_encode($seed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );
        return count($seed);
    }

    $existing = [];
    foreach ($volume as $entry) {
        $existing[travelKey($entry)] = true;
    }

    $added = 0;
    foreach ($seed as $entry) {
        if (!isset($existing[travelKey($entry)])) {
            $volume[] = $entry;
            $added++;
        }
    }

    if ($added > 0) {
        usort($volume, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
        file_put_contents(
            $volFile,
            json_encode($volume, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );
    }

    return $added;
}

$imagesCopied = mergeGallery($seedGallery, $galleryVol, $allowedImageTypes);
$travelsAdded = mergeTravels($seedData . '/travels.json', $dataVol . '/travels.json');

echo "Volume sync: {$imagesCopied} image(s) copied, {$travelsAdded} travel log(s) merged.\n";
