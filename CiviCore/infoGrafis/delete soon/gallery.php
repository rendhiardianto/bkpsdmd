<?php
include "../db.php";

$result = $conn->query("SELECT * FROM infografis ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px; }
    h1 { text-align:center; color:#003366; }
    .gallery { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; max-width:1200px; margin:auto; }
    .item { background:white; padding:10px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; }
    .item img { width:100%; height:200px; object-fit:cover; border-radius:6px; }
    .caption { margin-top:10px; font-size:14px; color:#333; }
  </style>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px; }
    h1 { text-align:center; color:#003366; }
    .gallery { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; max-width:1200px; margin:auto; }
    .item { background:white; padding:10px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; }
    .item img { width:100%; height:200px; object-fit:cover; border-radius:6px; }
    .caption { margin-top:10px; font-size:14px; color:#333; }
    .edit-link { display:inline-block; margin-top:8px; font-size:12px; color:#0066cc; text-decoration:none; }
    .edit-link:hover { text-decoration:underline; }
  </style>

</head>
<body>

<h1>Gallery</h1>
<div class="gallery">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="item">
      <img src="<?php echo $row['images']; ?>" alt="Gallery Image">
      <div class="caption"><?php echo $row['caption']; ?></div>
      <a class="edit-link" href="gallery_edit.php?id=<?php echo $row['id']; ?>">✎ Edit</a>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
