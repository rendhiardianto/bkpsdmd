<?php
include "../db.php";
include "../auth.php";

requireRole(['admin', 'user']);
// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'admin'  => '../dashboard_super_admin.php',
    'user'   => '../dashboard_cms_admin.php',
];
$backUrl = $backLinks[$role];

$result = $conn->query("SELECT * FROM infografis ORDER BY created_at ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];

    // Step 1: insert caption first (empty image)
    $stmt = $conn->prepare("INSERT INTO infografis (images, caption) VALUES ('', ?)");
    $stmt->bind_param("s", $caption);
    $stmt->execute();

    $lastId = $conn->insert_id; // get ID
    
    // Resize before save (optional but recommended)
    $maxWidth = 1200;
    $maxHeight = 1200;

    $width = imagesx($image);
    $height = imagesy($image);

    if ($width > $maxWidth || $height > $maxHeight) {
        $scale = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = floor($width * $scale);
        $newHeight = floor($height * $scale);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // preserve transparency for PNG/GIF
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0,
            $newWidth, $newHeight, $width, $height);

        imagedestroy($image);
        $image = $resized;
    }

    // Save as PNG with compression (0 = none, 9 = max compression)
    imagepng($image, $imagePath, 6);
    imagedestroy($image);

    // Double check file size (in case still >2MB)
    if (filesize($imagePath) > 2 * 1024 * 1024) {
        unlink($imagePath); // delete too-big file
        die("Image too large after compression. Please upload smaller image.");
    }

    // Step 2: upload image
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $filename = $lastId . "." . strtolower($ext);
        $imagePath = $targetDir . $filename;

        if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = $lastId . ".png"; // always PNG
        $imagePath = $targetDir . $filename;

        // Convert any uploaded image to PNG
        $tmpFile = $_FILES["image"]["tmp_name"];
        $imgInfo = getimagesize($tmpFile);
        $mime = $imgInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($tmpFile);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tmpFile);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($tmpFile);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($tmpFile);
                break;
            default:
                die("Unsupported image type");
        }

        // Save only as PNG
        imagepng($image, $imagePath);
        imagedestroy($image);

        // Update DB with path
        $stmt = $conn->prepare("UPDATE infografis SET images=? WHERE id=?");
        $stmt->bind_param("si", $imagePath, $lastId);
        $stmt->execute();
    }

    }

    header("Location: gallery.php");
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
  <title>Dashboard - Infographics </title>
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="/icon/button/logo2.png">
  <link href="admin_infoGrafis.css" rel="stylesheet" type="text/css">

  <style>
    
  </style>

</head>
<body>

  <div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Info Grafis</h1>
    </div>
  </div>

  <div class="content-infoGrafis">
    <div class="leftSide">
      <h2>Tambah Info Grafis Baru</h2>
      <form action="" method="post" enctype="multipart/form-data">
        <label>Caption</label>
        <input type="text" name="caption" rows="10" required>
        <label>Image</label>
        <input type="file" name="image" required>
        <button type="submit">Save</button>
      </form>
    </div>
        
    <div class="rightSide">
      <h1>Gallery</h1>
      <di class="gallery">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="item">
            <img src="<?php echo $row['images']; ?>" alt="Gallery Image">
            <div class="caption"><?php echo $row['caption']; ?></div>
            <a class="edit-link" href="gallery_edit.php?id=<?php echo $row['id']; ?>">✎ Edit</a>
          </div>
        <?php endwhile; ?>
    </div>
  </div>

<div class="footer">
    <p>Copyright &copy; 2025. Tim PUSDATIN - BKPSDMD Kabupaten Merangin.</p>
</div>

</body>
</html>
