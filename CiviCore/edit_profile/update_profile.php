<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

$userId = $_SESSION['user_id'];

// Sanitize inputs
$fullname  = trim($_POST['fullname'] ?? '');
$jabatan   = trim($_POST['jabatan'] ?? '');
$nip   = trim($_POST['nip'] ?? '');

// Check required fields
if (empty($fullname) || empty($jabatan)) {
    die("Nama lengkap dan jabatan wajib diisi.");
}

// ===============================
// Get current profile picture
// ===============================
$sql = "SELECT profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($oldProfilePic);
$stmt->fetch();
$stmt->close();

// ===============================
//  Handle Profile Picture Upload
// ===============================
$profilePic = null;

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileSize = $_FILES['profile_pic']['size'];
    $fileType = $_FILES['profile_pic']['type'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        die("Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.");
    }

    if ($fileSize > 2 * 1024 * 1024) { // 2MB limit
        die("Ukuran file terlalu besar. Maksimal 2MB.");
    }

    // Generate new file name: NIP_FullName.jpg
    $safeFullname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fullname);
    $newFileName = $nip . "_" . $safeFullname . "." . $fileExtension;

    $uploadDir = __DIR__ . '/../uploads/profile_pics/';
    $destPath = $uploadDir . $newFileName;

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // ✅ Delete old photo (except default.png)
    if (!empty($oldProfilePic) && $oldProfilePic !== 'default.png') {
        $oldPath = $uploadDir . $oldProfilePic;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    // Move uploaded file
    if (!move_uploaded_file($fileTmpPath, $destPath)) {
        die("Gagal mengunggah foto profil.");
    }

    // Save new filename to DB later
    $profilePic = $newFileName;
}

// ===============================
//  Update User Information
// ===============================
if ($profilePic) {
    $stmt = $conn->prepare("UPDATE users SET fullname=?, jabatan=?, profile_pic=? WHERE id=?");
    $stmt->bind_param("sssi", $fullname, $jabatan, $profilePic, $userId);
} else {
    $stmt = $conn->prepare("UPDATE users SET fullname=?, jabatan=? WHERE id=?");
    $stmt->bind_param("ssi", $fullname, $jabatan, $userId);
}

if ($stmt->execute()) {
    // Redirect back to profile page
    header("Location: edit_profile.php?success=1");
    exit();
} else {
    echo "Gagal memperbarui profil. Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
