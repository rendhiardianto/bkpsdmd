<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    // Fetch old record (to delete old files if needed)
    $old = $conn->query("SELECT thumbnail, attachment FROM announcements WHERE id=$id")->fetch_assoc();

    $updateFields = "title='$title', content='$content'";

    // === Handle thumbnail update ===
    if (!empty($_FILES['thumbnail']['name'])) {
        // Delete old thumbnail file if exists
        if (!empty($old['thumbnail']) && file_exists("uploads/thumbnails/" . $old['thumbnail'])) {
            unlink("uploads/thumbnails/" . $old['thumbnail']);
        }

        $thumbName = uniqid("thumb_") . "." . pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $tmpPath = $_FILES['thumbnail']['tmp_name'];

        list($width, $height) = getimagesize($tmpPath);
        $newWidth = 2560;
        $newHeight = 1440;

        $src = imagecreatefromstring(file_get_contents($tmpPath));
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($dst, "uploads/thumbnails/" . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($dst);

        $updateFields .= ", thumbnail='$thumbName'";
    }

    // === Handle attachment update (keep original name) ===
    if (!empty($_FILES['attachment']['name'])) {
        // Delete old attachment file if exists
        if (!empty($old['attachment']) && file_exists("uploads/files/" . $old['attachment'])) {
            unlink("uploads/files/" . $old['attachment']);
        }

        $originalName = basename($_FILES['attachment']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);

        $uploadDir = __DIR__ . "/uploads/files/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Prevent overwrite: add _1, _2, etc.
        $targetFile = $uploadDir . $originalName;
        $counter = 1;
        while (file_exists($targetFile)) {
            $newName = $nameOnly . "_" . $counter . "." . $ext;
            $targetFile = $uploadDir . $newName;
            $counter++;
        }

        $attachment = basename($targetFile);

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $updateFields .= ", attachment='$attachment'";
        } else {
            echo "Gagal mengunggah lampiran.";
            exit;
        }
    }

    // === Update database ===
    $sql = "UPDATE announcements SET $updateFields WHERE id=$id";

    if ($conn->query($sql)) {
        echo "Pengumuman berhasil diperbarui!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
