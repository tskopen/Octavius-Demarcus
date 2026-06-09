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
        <p>Every photo from the road  auto-loaded from the gallery folder.</p>
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
                    <article class="travel-card"
                        data-title="<?php echo htmlspecialchars($entry['title'] ?? 'Untitled'); ?>"
                        data-location="<?php echo htmlspecialchars($entry['location'] ?? ''); ?>"
                        data-date="<?php echo htmlspecialchars(formatDate($entry['date'] ?? '')); ?>"
                        data-caption="<?php echo htmlspecialchars($entry['caption'] ?? ''); ?>"
                        <?php if (!empty($entry['image']) && in_array($entry['image'], $images, true)): ?>
                        data-img-src="/gallery/<?php echo htmlspecialchars($entry['image']); ?>"
                        data-img-alt="<?php echo htmlspecialchars($entry['title'] ?? 'Travel photo'); ?>"
                        <?php endif; ?>
                    >

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
                                 <?php echo formatDate($entry['date'] ?? ''); ?>
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

<!-- ===================== MODAL OVERLAY ===================== -->
<div id="site-modal" class="modal" role="dialog" aria-modal="true" aria-label="Image viewer">
    <div class="modal-content" id="modal-content-box">
        <button class="modal-close" id="modal-close-btn" aria-label="Close">&times;</button>
        <!-- Content injected by JS -->
    </div>
</div>

<script>
(function () {
    'use strict';

    var modal      = document.getElementById('site-modal');
    var contentBox = document.getElementById('modal-content-box');
    var closeBtn   = document.getElementById('modal-close-btn');

    /* ── helpers ── */

    function openModal(html) {
        // Inject content then re-prepend the persistent close button
        contentBox.innerHTML = html;
        contentBox.prepend(closeBtn);

        modal.classList.remove('is-closing');
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function closeModal() {
        modal.classList.add('is-closing');
        // Wait for fade-out transition then fully hide
        setTimeout(function () {
            modal.classList.remove('is-open', 'is-closing');
            contentBox.innerHTML = '';
            contentBox.prepend(closeBtn);
            document.body.style.overflow = '';
        }, 250);
    }

    /* ── gallery image modal ── */

    function buildImageModal(src, alt) {
        return '<img class="modal-image" src="' + src + '" alt="' + escHtml(alt) + '">';
    }

    /* ── travel card modal ── */

    function buildTravelModal(card) {
        var imgSrc   = card.dataset.imgSrc   || '';
        var imgAlt   = card.dataset.imgAlt   || 'Travel photo';
        var title    = card.dataset.title    || 'Untitled';
        var location = card.dataset.location || '';
        var date     = card.dataset.date     || '';
        var caption  = card.dataset.caption  || '';

        var metaParts = [];
        if (location) metaParts.push(escHtml(location));
        if (date)     metaParts.push(escHtml(date));
        var metaHtml = metaParts.join(' &middot; ');

        var imageHtml = imgSrc
            ? '<img class="modal-image" src="' + escHtml(imgSrc) + '" alt="' + escHtml(imgAlt) + '">'
            : '<div class="modal-travel-placeholder">\uD83D\uDDFA\uFE0F</div>';

        return '<div class="modal-travel-expanded">'
            + imageHtml
            + '<div class="modal-body">'
            +   (metaHtml ? '<p class="modal-meta">' + metaHtml + '</p>' : '')
            +   '<h2>' + escHtml(title) + '</h2>'
            +   (caption ? '<p>' + escHtml(caption) + '</p>' : '')
            + '</div>'
            + '</div>';
    }

    /* ── XSS escape helper ── */

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ── event delegation (single listener on document) ── */

    document.addEventListener('click', function (e) {
        // Close button
        if (e.target === closeBtn || closeBtn.contains(e.target)) {
            closeModal();
            return;
        }

        // Click on the backdrop (outside modal-content)
        if (modal.classList.contains('is-open') && e.target === modal) {
            closeModal();
            return;
        }

        // Gallery item click
        var galleryItem = e.target.closest('.gallery-item');
        if (galleryItem) {
            var img = galleryItem.querySelector('img');
            if (img) {
                openModal(buildImageModal(img.src, img.alt));
            }
            return;
        }

        // Travel card click
        var travelCard = e.target.closest('.travel-card');
        if (travelCard) {
            openModal(buildTravelModal(travelCard));
            return;
        }
    });

    /* ── keyboard: ESC closes modal ── */

    document.addEventListener('keydown', function (e) {
        if ((e.key === 'Escape' || e.key === 'Esc') && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

})();
</script>

</body>
</html>
