<?php
// upload_image.php
session_start();

// ✅ Security: allow only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$targetDir = __DIR__ . "/uploads/announcements/";
$webPath   = "uploads/announcements/";

// Validate file
if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Upload failed']);
    exit;
}

$allowed = ['jpg','jpeg','png','gif','webp'];
$fileName = basename($_FILES['file']['name']);
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Unique filename
$newName = uniqid("img_", true) . "." . $ext;
$targetFile = $targetDir . $newName;

// Resize + Optimize function
function resizeImage($sourcePath, $destPath, $maxWidth = 1200, $quality = 80) {
    list($width, $height, $type) = getimagesize($sourcePath);

    // Calculate new size
    $ratio = $width / $height;
    if ($width > $maxWidth) {
        $newWidth  = $maxWidth;
        $newHeight = $maxWidth / $ratio;
    } else {
        $newWidth  = $width;
        $newHeight = $height;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($sourcePath);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            break;
        case IMAGETYPE_GIF:
            $src = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $src = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    // Resize
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save optimized
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $destPath, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $destPath, 6); // compression 0-9
            break;
        case IMAGETYPE_GIF:
            imagegif($dst, $destPath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($dst, $destPath, $quality);
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

// Save uploaded file temporarily
$tmpFile = $_FILES['file']['tmp_name'];

if (resizeImage($tmpFile, $targetFile)) {
    echo json_encode(['location' => $webPath . $newName]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process image']);
}
?>
