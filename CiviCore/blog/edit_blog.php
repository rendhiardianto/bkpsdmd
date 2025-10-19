<?php
include "../db.php";

$id = intval($_GET['id']);

$result = $conn->query("SELECT * FROM blog WHERE id=$id");
$blog = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $imagePath = $blog['image']; // default to old image

    // Check if new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        // Delete old image if exists
        if (!empty($blog['image']) && file_exists($blog['image'])) {
            unlink($blog['image']);
        }

        // Save new image
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE blog SET title=?, content=?, category=?, image=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $content, $category, $imagePath, $id);
    $stmt->execute();

    header("Location: admin_blog.php");
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
  <link rel="stylesheet" href="edit_blog.css">
</head>
<body>
  <div class="header">
    <div class="navbar">
      <a href="admin_blog.php" class="buttonBack" style="text-decoration: none;">&#10094; Kembali</a>
      <!--<a href="admin_blog.php" class="buttonBack">&#10094; Kembali</a>-->
    </div>
    <div class="roleHeader">
      <h1>Edit Berita</h1>
    </div>
  </div>
<div class="form-box">
<form action="" method="post" enctype="multipart/form-data">
  <label>Judul</label>
  <input type="text" name="title" value="<?php echo $blog['title']; ?>" required>

  <label>Konten</label>
  <textarea name="content" rows="12" required><?php echo $blog['content']; ?></textarea>

  <label>Jabatan Penulis</label>
  <input type="text" name="category" value="<?php echo $blog['category']; ?>" required>

  <label>Thumbnail</label>
  <input type="file" name="image">
  <p>Current: <img src="<?php echo $blog['image']; ?>" width="120"></p>

  <button type="submit">Simpan</button>
</form>
</div>
</body>
</html>
