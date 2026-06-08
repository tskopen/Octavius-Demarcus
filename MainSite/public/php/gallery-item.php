<?php
$image_name = $image;
$image_src = '/gallery/' . htmlspecialchars($image_name);
$travel_entry = $travel_by_image[$image_name] ?? null;

if ($travel_entry) {
    $lightbox_title = htmlspecialchars($travel_entry['title'] ?? 'Untitled');
    $lightbox_caption = htmlspecialchars(travelLightboxCaption($travel_entry));
    $figcaption = $lightbox_title;
} else {
    $lightbox_title = htmlspecialchars($image_name);
    $lightbox_caption = '';
    $figcaption = htmlspecialchars($image_name);
}
?>

<figure
    class="gallery-item gallery-item-clickable"
    data-lightbox-src="<?php echo $image_src; ?>"
    data-lightbox-title="<?php echo $lightbox_title; ?>"
    data-lightbox-caption="<?php echo $lightbox_caption; ?>"
    tabindex="0"
    role="button"
    aria-label="View photo: <?php echo $lightbox_title; ?>"
>
    <img
        src="<?php echo $image_src; ?>"
        alt="<?php echo $lightbox_title; ?>"
        loading="lazy"
    >
    <figcaption><?php echo $figcaption; ?></figcaption>
</figure>
