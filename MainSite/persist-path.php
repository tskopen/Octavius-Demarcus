<?php

function persistPaths(): array
{
    static $paths = null;

    if ($paths !== null) {
        return $paths;
    }

    $generated = __DIR__ . '/generated-paths.php';
    if (is_file($generated)) {
        $paths = require $generated;
        if (is_array($paths)) {
            return $paths;
        }
    }

    $root = getenv('PERSIST_DIR') ?: getenv('RAILWAY_VOLUME_MOUNT_PATH') ?: '/var/www/site/public';
    $root = rtrim($root, '/');

    $paths = [
        'root' => $root,
        'gallery' => $root . '/gallery',
        'travels' => $root . '/data/travels.json',
    ];

    return $paths;
}

function persistRoot(): string
{
    return persistPaths()['root'];
}

function galleryDir(): string
{
    return persistPaths()['gallery'];
}

function travelsFile(): string
{
    return persistPaths()['travels'];
}
