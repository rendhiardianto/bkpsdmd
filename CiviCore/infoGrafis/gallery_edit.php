<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

if (!isset($_GET['id'])) {
    header("Location: admin_infoGrafis.php");
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM infografis WHERE id=$id");

if ($result->num_rows == 0) {
    echo "Gallery item not found!";
    exit;
}

$gallery = $result->fetch_assoc();

/**
 * Convert uploaded image to PNG, resize and compress under 2MB.
 */
function convert_upload_to_png($tmpFile, $destPath, $maxWidth = 1200, $maxHeight = 1200, $maxBytes = 2097152, &$err = '') {
    $imgInfo = @getimagesize($tmpFile);
    if (!$imgInfo) {
        $err = 'Invalid image file.';
        return false;
    }

    $mime = $imgInfo['mime'];
    switch ($mime) {
        case 'image/jpeg': $src = @imagecreatefromjpeg($tmpFile); break;
        case 'image/png':  $src = @imagecreatefrompng($tmpFile); break;
        case 'image/gif':  $src = @imagecreatefromgif($tmpFile); break;
        case 'image/webp': $src = @imagecreatefromwebp($tmpFile); break;
        default:
            $err = 'Unsupported image type: ' . htmlentities($mime);
            return false;
    }

    if (!$src) {
        $err = 'Could not create image resource.';
        return false;
    }

    $origW = imagesx($src);
    $origH = imagesy($src);

    $scale = min(1, min($maxWidth / $origW, $maxHeight / $origH));
    $newW = max(1, floor($origW * $scale));
    $newH = max(1, floor($origH * $scale));

    $dst = imagecreatetruecolor($newW, $newH);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $transparent);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

    $compression = 6;
    imagepng($dst, $destPath, $compression);
    imagedestroy($dst);
    imagedestroy($src);

    // If file still >2MB, iteratively reduce
    $iter = 0;
    while (filesize($destPath) > $maxBytes && $iter < 8) {
        $newW = max(100, floor($newW * 0.85));
        $newH = max(100, floor($newH * 0.85));

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        $src = imagecreatefrompng($destPath); // reload last version
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, imagesx($src), imagesy($src));
        imagedestroy($src);

        imagepng($dst, $destPath, $compression);
        imagedestroy($dst);

        $iter++;
    }

    if (filesize($destPath) > $maxBytes) {
        unlink($destPath);
        $err = "Image is still larger than 2MB after compression.";
        return false;
    }

    return true;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];

    // Update caption
    $stmt = $conn->prepare("UPDATE infografis SET caption=? WHERE id=?");
    $stmt->bind_param("si", $caption, $id);
    $stmt->execute();

    // If new image uploaded → overwrite old one
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = $id . ".png"; // always PNG
        $imagePath = $targetDir . $filename;

        $err = '';
        if (!convert_upload_to_png($_FILES['image']['tmp_name'], $imagePath, 1200, 1200, 2 * 1024 * 1024, $err)) {
            die("Image upload failed: " . htmlspecialchars($err));
        }

        // Update DB with path (keeps same id)
        $stmt = $conn->prepare("UPDATE infografis SET images=? WHERE id=?");
        $stmt->bind_param("si", $imagePath, $id);
        $stmt->execute();
    }
    
    header("Location: admin_infoGrafis.php");
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
  <title>Edit Gallery</title>
  <style>
    body { font-family: Arial, sans-serif; margin:20px; }
    form { max-width:500px; margin:auto; background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    input { width:100%; padding:10px; margin-bottom:15px; }
    button { padding:10px 15px; background:#003366; color:white; border:none; border-radius:4px; cursor:pointer; }
    button:hover { background:#0055aa; }
    img { max-width:200px; margin-bottom:15px; display:block; }
  </style>
</head>
<body>

<h2 style="text-align:center;">Edit Gallery Item</h2>
<form action="" method="post" enctype="multipart/form-data">
  <label>Current Image</label>
  <img src="<?php echo $gallery['images']; ?>" alt="Current Image">

  <label>Change Image (optional)</label>
  <input type="file" name="image">

  <label>Caption</label>
  <input type="text" name="caption" value="<?php echo htmlspecialchars($gallery['caption']); ?>" required>

  <button type="submit">Update</button>
</form>

</body>
</html>
