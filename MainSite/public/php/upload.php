<?php
include 'config.php';
include 'helpers.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $caption = trim($_POST['caption'] ?? '');

    if (!is_dir($gallery_dir)) {
        mkdir($gallery_dir, 0755, true);
    }

    $image_name = '';

    if (!empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_image_types, true)) {
            $error = 'Please upload a JPG, PNG, WEBP, or GIF image.';
        } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Upload failed. Please try again.';
        } else {
            $safe_base = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($title ?: 'travel'));
            $image_name = $safe_base . '-' . date('Ymd-His') . '.' . $ext;
            $target = $gallery_dir . '/' . $image_name;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $error = 'Could not save the image to the gallery folder.';
                $image_name = '';
            }
        }
    }

    if ($error === '' && $title === '' && $caption === '' && $image_name === '') {
        $error = 'Add a photo or at least a title for your travel log entry.';
    }

    if ($error === '') {
        $saved = saveTravelEntry($travels_file, [
            'title' => $title ?: 'New Adventure',
            'location' => $location ?: 'Unknown',
            'date' => $date,
            'caption' => $caption,
            'image' => $image_name,
        ]);

        if ($saved) {
            $message = $image_name
                ? 'Photo uploaded and travel log updated!'
                : 'Travel log entry saved!';
        } else {
            $error = 'Could not save the travel log entry.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Photo | <?php echo htmlspecialchars($site_name); ?></title>

    <link rel="stylesheet" href="/css/simple.css?v=<?php echo $version; ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <section class="page-header">
        <h1>Add to the Journey</h1>
        <p>Upload a photo and optional story ù or just drop files into <code>public/gallery/</code>.</p>
    </section>

    <section class="section">
        <?php if ($message): ?>
            <p class="form-message success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="form-message error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form class="upload-form" method="POST" enctype="multipart/form-data">
            <label>
                Photo
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif">
            </label>

            <label>
                Title
                <input type="text" name="title" placeholder="Sunset in Lisbon">
            </label>

            <label>
                Location
                <input type="text" name="location" placeholder="Lisbon, Portugal">
            </label>

            <label>
                Date
                <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
            </label>

            <label>
                Story
                <textarea name="caption" rows="5" placeholder="What happened on this leg of the trip?"></textarea>
            </label>

            <button type="submit" class="button">Save Entry</button>
        </form>

        <div class="upload-help">
            <h3>Other ways to add content</h3>
            <ul>
                <li>Copy images directly into <code>public/gallery/</code> ù they appear automatically.</li>
                <li>Edit <code>public/data/travels.json</code> to add or update travel stories by hand.</li>
            </ul>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
