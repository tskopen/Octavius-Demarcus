<?php
include 'config.php';
include 'helpers.php';

$travels = loadTravels($travels_file);
$recent = array_slice($travels, 0, 3);
$photo_count = count(scanGalleryImages($gallery_dir, $allowed_image_types));
$country_count = count(array_unique(array_column($travels, 'location')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_name); ?></title>

    <link rel="stylesheet" href="/css/simple.css?v=<?php echo $version; ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <section class="hero-full">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <span class="hero-badge">World Traveler · Crispy & Bold</span>
            <h1>The Adventures of Orange Chicken</h1>
            <p><?php echo htmlspecialchars($site_tagline); ?></p>

            <div class="hero-actions">
                <a href="/php/gallery.php" class="button">View the Gallery</a>
                <a href="#recent" class="button button-outline">Latest Stops</a>
            </div>
        </div>
    </section>

    <section class="stats-bar">
        <div class="stat">
            <strong><?php echo $photo_count; ?></strong>
            <span>Photos</span>
        </div>
        <div class="stat">
            <strong><?php echo count($travels); ?></strong>
            <span>Travel Logs</span>
        </div>
        <div class="stat">
            <strong><?php echo max($country_count, 1); ?></strong>
            <span>Destinations</span>
        </div>
    </section>

    <section class="intro-section">
        <div class="intro-inner">
            <div class="mascot">🐔</div>
            <div>
                <h2>Meet the Traveler</h2>
                <p>
                    This is a living travel diary for one very determined piece of orange chicken.
                    From street markets to mountaintops, every journey gets a photo and a story.
                </p>
                <p class="hint">
                    <strong>To add content:</strong> drop images into <code>public/gallery/</code>,
                    or use the <a href="/php/upload.php">Add Photo</a> page.
                    Edit travel stories in <code>public/data/travels.json</code>.
                </p>
            </div>
        </div>
    </section>

    <section id="recent" class="section">
        <h2>Recent Adventures</h2>

        <?php if (empty($recent)): ?>
            <p class="empty-state">No travel logs yet. Add your first entry on the upload page or in travels.json.</p>
        <?php else: ?>
            <div class="travel-grid">
                <?php foreach ($recent as $entry): ?>
                    <article class="travel-card">
                        <?php if (!empty($entry['image'])): ?>
                            <img
                                src="/gallery/<?php echo htmlspecialchars($entry['image']); ?>"
                                alt="<?php echo htmlspecialchars($entry['title'] ?? 'Travel photo'); ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="travel-card-placeholder">🍊</div>
                        <?php endif; ?>

                        <div class="travel-card-body">
                            <p class="travel-meta">
                                <?php echo htmlspecialchars($entry['location'] ?? ''); ?>
                                · <?php echo formatDate($entry['date'] ?? ''); ?>
                            </p>
                            <h3><?php echo htmlspecialchars($entry['title'] ?? 'Untitled'); ?></h3>
                            <p><?php echo htmlspecialchars($entry['caption'] ?? ''); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="section-cta">
            <a href="/php/gallery.php" class="button">See Full Gallery</a>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
