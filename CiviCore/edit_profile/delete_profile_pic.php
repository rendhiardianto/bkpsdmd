<?php
header('Content-Type: application/json');
include "../db.php";
session_start();

// Make sure the request is POST and user_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    // Get the current profile picture name
    $result = $conn->query("SELECT profile_pic FROM users WHERE id = $userId");
    if ($result && $row = $result->fetch_assoc()) {
        $oldPic = $row['profile_pic'];

        if (!empty($oldPic)) {
            // ✅ Correct path to uploaded pictures
            $filePath = realpath(__DIR__ . "/uploads/profile_pics/" . $oldPic);

            // ✅ Safety check — only delete inside uploads folder
            $uploadsDir = realpath(__DIR__ . "/uploads/profile_pics/");
            if ($filePath && strpos($filePath, $uploadsDir) === 0 && file_exists($filePath)) {
                unlink($filePath); // delete old file from folder
            }
        }

        // ✅ Clear filename in the database
        $conn->query("UPDATE users SET profile_pic = NULL WHERE id = $userId");

        echo json_encode([
            "success" => true,
            "message" => "Foto profil berhasil dihapus dari database dan folder."
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User tidak ditemukan."
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Permintaan tidak valid."
]);
?>
