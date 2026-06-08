<?php
include 'config.php';
include 'helpers.php';

$images = scanGalleryImages($gallery_dir, $allowed_image_types);
$travels = loadTravels($travels_file);
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
        <p>Every photo from the road ù auto-loaded from the gallery folder.</p>
    </section>

    <section class="section">
        <h2>Photos</h2>

        <?php if (empty($images)): ?>
            <p class="empty-state">
                No photos yet. Drop images into <code>public/gallery/</code> or
                <a href="/php/upload.php">upload one here</a>.
            </p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($images as $image): ?>
                    <figure class="gallery-item">
                        <img
                            src="/gallery/<?php echo htmlspecialchars($image); ?>"
                            alt="<?php echo htmlspecialchars($image); ?>"
                            loading="lazy"
                        >
                        <figcaption><?php echo htmlspecialchars($image); ?></figcaption>
                    </figure>
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
                    <article class="travel-card">
                        <?php if (!empty($entry['image']) && in_array($entry['image'], $images, true)): ?>
                            <img
                                src="/gallery/<?php echo htmlspecialchars($entry['image']); ?>"
                                alt="<?php echo htmlspecialchars($entry['title'] ?? 'Travel photo'); ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="travel-card-placeholder">??</div>
                        <?php endif; ?>

                        <div class="travel-card-body">
                            <p class="travel-meta">
                                <?php echo htmlspecialchars($entry['location'] ?? ''); ?>
                                ù <?php echo formatDate($entry['date'] ?? ''); ?>
                            </p>
                            <h3><?php echo htmlspecialchars($entry['title'] ?? 'Untitled'); ?></h3>
                            <p><?php echo htmlspecialchars($entry['caption'] ?? ''); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
