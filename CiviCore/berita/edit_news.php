<?php
include "../db.php";

$id = intval($_GET['id']);

$result = $conn->query("SELECT * FROM news WHERE id=$id");
$news = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image_desc = $_POST['image_desc'];
    $imagePath = $news['image']; // keep old image

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE news SET title=?, content=?, category=?, image=?, image_desc=? WHERE id=?");
    $stmt->bind_param("sssssi", $title, $content, $category, $imagePath, $image_desc, $id);
    $stmt->execute();

    header("Location: admin_news.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Berita</title>
  <link rel="stylesheet" href="edit_news.css">
</head>
<body>
  <div class="header">
    <div class="navbar">
      <a href="admin_news.php" class="buttonBack" style="text-decoration: none;">&#10094; Kembali</a>
      <!--<a href="admin_news.php" class="buttonBack">&#10094; Kembali</a>-->
    </div>
    <div class="roleHeader">
      <h1>Edit Berita</h1>
    </div>
  </div>
<div class="form-box">
<form action="" method="post" enctype="multipart/form-data">
  <label>Judul</label>
  <input type="text" name="title" value="<?php echo $news['title']; ?>" required>

  <label>Konten</label>
  <textarea name="content" rows="12" required><?php echo $news['content']; ?></textarea>

  <label>Kategori</label>
  <input type="text" name="category" value="<?php echo $news['category']; ?>" required>

  <label>Thumbnail</label>
  <input type="file" name="image">
  <p>Current: <img src="<?php echo $news['image']; ?>" width="120"></p>

  <label>Image Description</label>
  <input type="text" name="image_desc" value="<?php echo $news['image_desc']; ?>" required>



  <button type="submit">Simpan</button>
</form>
</div>
</body>
</html>
