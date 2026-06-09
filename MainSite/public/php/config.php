<?php
require_once dirname(__DIR__, 2) . '/persist-path.php';

$version = "1.2.0";

$site_name = "Orange Chicken Travels";
$site_tagline = "One crispy legend. Infinite destinations.";

$persist_root = persistRoot();
$gallery_dir = galleryDir();
$travels_file = travelsFile();

$allowed_image_types = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
