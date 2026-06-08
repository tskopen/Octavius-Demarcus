<?php include 'config.php'; ?>

<nav class="navbar">
  <ul class="nav-menu">
    <li class="brand">
      <a href="/php/index.php">🍊 <?php echo htmlspecialchars($site_name); ?></a>
    </li>

    <li><a href="/php/index.php">Home</a></li>
    <li><a href="/php/gallery.php">Gallery</a></li>

    <li class="push-right">
      <a href="/php/upload.php" class="nav-cta">Add Photo</a>
    </li>
  </ul>
</nav>
