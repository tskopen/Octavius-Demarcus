<?php
include 'config.php';
include 'helpers.php';

$gallery_images = scanGalleryImages($gallery_dir, $allowed_image_types);
$travels = loadTravels($travels_file);
$travel_by_image = travelByImage($travels);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | <?php echo htmlspecialchars($site_name); ?></title>

    <link rel="stylesheet" href="/css/simple.css?v=<?php echo $version; ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <section class="page-header">
        <h1>Travel Gallery</h1>
        <p>Every photo from the road — click any photo to view it and its travel log.</p>
    </section>

    <section class="section">
        <h2>Photos</h2>

        <?php if (empty($gallery_images)): ?>
            <p class="empty-state">
                No photos yet. Drop images into <code>public/gallery/</code> or
                <a href="/php/upload.php">upload one here</a>.
            </p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($gallery_images as $image): ?>
                    <?php include 'gallery-item.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="section section-alt">
        <h2>Travel Log</h2>

        <?php if (empty($travels)): ?>
            <p class="empty-state">No entries in travels.json yet.</p>
        <?php else: ?>
            <div class="travel-grid">
                <?php foreach ($travels as $entry): ?>
                    <?php include 'travel-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
<?php include 'lightbox.php'; ?>

</body>
</html>
