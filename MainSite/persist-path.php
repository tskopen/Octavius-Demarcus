<?php

function persistRoot(): string
{
    if ($dir = getenv('PERSIST_DIR')) {
        return rtrim($dir, '/');
    }

    if ($dir = getenv('RAILWAY_VOLUME_MOUNT_PATH')) {
        return rtrim($dir, '/');
    }

    return '/var/www/site/public';
}

function galleryDir(): string
{
    return persistRoot() . '/gallery';
}

function travelsFile(): string
{
    return persistRoot() . '/data/travels.json';
}
