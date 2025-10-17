<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);

    // Fetch existing record
    $result = $conn->query("SELECT * FROM jf_bkn WHERE id=$id");
    if (!$result || $result->num_rows === 0) {
        die("Data tidak ditemukan.");
    }
    $old = $result->fetch_assoc();

    // Use old value if new one is empty
    $jabatan = !empty($_POST['jabatan']) ? $conn->real_escape_string($_POST['jabatan']) : $old['jabatan'];
    $rumpun = !empty($_POST['rumpun']) ? $conn->real_escape_string($_POST['rumpun']) : $old['rumpun'];
    $rekom_ip = !empty($_POST['rekom_ip']) ? $conn->real_escape_string($_POST['rekom_ip']) : $old['rekom_ip'];
    $penetapan_menpan = !empty($_POST['penetapan_menpan']) ? $conn->real_escape_string($_POST['penetapan_menpan']) : $old['penetapan_menpan'];
    $kategori = !empty($_POST['kategori']) ? $conn->real_escape_string($_POST['kategori']) : $old['kategori'];
    $lingkup = !empty($_POST['lingkup']) ? $conn->real_escape_string($_POST['lingkup']) : $old['lingkup'];
    $pembina = !empty($_POST['pembina']) ? $conn->real_escape_string($_POST['pembina']) : $old['pembina'];

    $imageUpdate = "";

    // === Handle new image upload ===
    if (!empty($_FILES['image_path']['name']) && is_uploaded_file($_FILES['image_path']['tmp_name'])) {

        // Delete old file if exists
        if (!empty($old['image_path']) && file_exists("uploads/detail_image/" . $old['image_path'])) {
            unlink("uploads/detail_image/" . $old['image_path']);
        }

        // Clean jabatan for file name
        $safeJabatan = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtolower($jabatan));

        // Prepare new image name
        $ext = strtolower(pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION));
        $imageName = $safeJabatan . "." . $ext;

        // Ensure unique file name
        $targetDir = "uploads/detail_image/";
        $targetPath = $targetDir . $imageName;
        $i = 1;
        while (file_exists($targetPath)) {
            $imageName = $safeJabatan . "_" . $i . "." . $ext;
            $targetPath = $targetDir . $imageName;
            $i++;
        }

        // Make sure uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Save uploaded file
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetPath)) {
            // Compress only if upload succeeded and file exists
            if (file_exists($targetPath)) {
                compressImage($targetPath, $targetPath, 70); // 70% quality
            }
            $imageUpdate = ", image_path='$imageName'";
        }
    }

    // === Update database ===
    $sql = "UPDATE jf_bkn SET 
                jabatan='$jabatan',
                rumpun='$rumpun',
                rekom_ip='$rekom_ip',
                penetapan_menpan='$penetapan_menpan',
                kategori='$kategori',
                lingkup='$lingkup',
                pembina='$pembina'
                $imageUpdate
            WHERE id=$id";

    if ($conn->query($sql)) {
        echo "Jabatan Fungsional berhasil diperbarui!";
    } else {
        echo "Error: " . $conn->error;
    }
}

/**
 * Compress image safely
 */
function compressImage($source, $destination, $quality = 75) {
    if (empty($source) || !file_exists($source)) {
        return; // Don't process if path missing or invalid
    }

    $info = getimagesize($source);
    if (!$info) return; // Not a valid image

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            $pngQuality = 9 - round(($quality / 100) * 9);
            imagepng($image, $destination, $pngQuality);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            imagewebp($image, $destination, $quality);
            break;
        default:
            return; // Skip unsupported formats
    }

    imagedestroy($image);
}
?>
