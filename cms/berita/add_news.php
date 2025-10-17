<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

/** 
 * Function to create URL-friendly slug from title 
 */
function createSlug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image_desc = $_POST['image_desc'];
    $created_by = $_SESSION['fullname']; 

    // Create slug from title
    $slug = createSlug($title);

    // Upload image
    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Insert including slug column
    $stmt = $conn->prepare("INSERT INTO news (title, slug, content, image, image_desc, category, created_by, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param("sssssss", $title, $slug, $content, $imagePath, $image_desc, $category, $created_by);  

    $stmt->execute();

    header("Location: admin_news.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Berita</title>
  <link rel="stylesheet" href="add_news.css">
</head>
<body>
  <div class="header">
    <div class="navbar">
     <a href="admin_news.php" class="buttonBack" style="text-decoration: none;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Tambah Berita Baru</h1>
    </div>
  </div>
  
<div class="form-box">
  <form action="" method="post" enctype="multipart/form-data">
    <label>Judul</label>
    <input type="text" name="title" required>

    <label>Konten</label>
    <textarea name="content" rows="8" required></textarea>

    <label>Kategori</label>
    <input type="text" name="category" required>

    <label>Thumbnail</label>
    <input type="file" name="image">

    <label>Image Description</label>
    <input type="text" name="image_desc">

    <button type="submit">Simpan</button>
  </form>
</div>

</body>
</html>
