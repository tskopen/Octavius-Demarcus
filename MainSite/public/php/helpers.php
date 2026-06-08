<?php

function loadTravels(string $file): array
{
    if (!file_exists($file)) {
        return [];
    }

    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data)) {
        return [];
    }

    usort($data, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
    return $data;
}

function scanGalleryImages(string $dir, array $extensions): array
{
    if (!is_dir($dir)) {
        return [];
    }

    $images = [];
    foreach (scandir($dir) as $file) {
        if ($file[0] === '.') {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions, true)) {
            $images[] = $file;
        }
    }

    rsort($images);
    return $images;
}

function saveTravelEntry(string $file, array $entry): bool
{
    $travels = loadTravels($file);
    $travels[] = $entry;

    usort($travels, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));

    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    return file_put_contents(
        $file,
        json_encode($travels, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    ) !== false;
}

function formatDate(string $date): string
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    return date('F j, Y', $timestamp);
}

function travelByImage(array $travels): array
{
    $map = [];

    foreach ($travels as $entry) {
        if (!empty($entry['image'])) {
            $map[$entry['image']] = $entry;
        }
    }

    return $map;
}

function travelLightboxCaption(array $entry): string
{
    $location = $entry['location'] ?? '';
    $date = formatDate($entry['date'] ?? '');
    $caption = $entry['caption'] ?? '';

    $line = $location . ' · ' . $date;
    if ($caption !== '') {
        $line .= ' — ' . $caption;
    }

    return $line;
}
