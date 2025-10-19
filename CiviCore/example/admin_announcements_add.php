<?php
include "db.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    // Upload thumbnail
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbName = uniqid("thumb_") . "." . pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $tmpPath = $_FILES['thumbnail']['tmp_name'];

        list($width, $height) = getimagesize($tmpPath);

        $newWidth = 800;   // desired width
        $newHeight = 400;  // desired height

        $src = imagecreatefromstring(file_get_contents($tmpPath));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // resize
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // save as jpeg (quality 90)
        imagejpeg($dst, "uploads/thumbnails/" . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($dst);

        $thumbnail = $thumbName;
    }

    // Upload attachment
    $attachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        $fileName = uniqid("file_") . "." . pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/files/" . $fileName);
        $attachment = $fileName;
    }

    $conn->query("INSERT INTO announcements (title, content, thumbnail, attachment, created_at) 
                  VALUES ('$title', '$content', '$thumbnail', '$attachment', NOW())");

    echo "<script>alert('Announcement added!'); window.location='admin_announcements.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Announcement</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, textarea, button { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
    button { background:#3498db; border:none; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Add Announcement</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Title" required>
      <textarea name="content" placeholder="Content" rows="5" required></textarea>
      
      <label>Thumbnail Image (jpg, png)</label>
      <input type="file" name="thumbnail" accept="image/*">
      
      <label>Attachment File (PDF, DOCX, etc.)</label>
      <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx">

      <button type="submit">Save</button>
    </form>
  </div>
</body>
</html>
