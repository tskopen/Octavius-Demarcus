<?php
$title = htmlspecialchars($entry['title'] ?? 'Untitled');
$location = htmlspecialchars($entry['location'] ?? '');
$date = formatDate($entry['date'] ?? '');
$caption = htmlspecialchars($entry['caption'] ?? '');

$has_image = !empty($entry['image']);
if ($has_image && isset($gallery_images)) {
    $has_image = in_array($entry['image'], $gallery_images, true);
}

$image_src = $has_image ? '/gallery/' . htmlspecialchars($entry['image']) : '';
$lightbox_caption = htmlspecialchars(travelLightboxCaption($entry));
?>

<article
    class="travel-card<?php echo $has_image ? ' travel-card-clickable' : ''; ?>"
    <?php if ($has_image): ?>
    data-lightbox-src="<?php echo $image_src; ?>"
    data-lightbox-title="<?php echo $title; ?>"
    data-lightbox-caption="<?php echo $lightbox_caption; ?>"
    tabindex="0"
    role="button"
    aria-label="View full photo: <?php echo $title; ?>"
    <?php endif; ?>
>
    <?php if ($has_image): ?>
        <div class="travel-card-image">
            <img
                src="<?php echo $image_src; ?>"
                alt="<?php echo $title; ?>"
                loading="lazy"
            >
            <span class="travel-card-expand">View full photo</span>
        </div>
    <?php else: ?>
        <div class="travel-card-placeholder">🍊</div>
    <?php endif; ?>

    <div class="travel-card-body">
        <p class="travel-meta">
            <?php echo $location; ?>
            · <?php echo $date; ?>
        </p>
        <h3><?php echo $title; ?></h3>
        <p><?php echo $caption; ?></p>
    </div>
</article>
