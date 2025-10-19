<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);
session_start();

$userId = $_SESSION['user_id'];

// Get form inputs safely
$oldPassword   = trim($_POST['old_password'] ?? '');
$newPassword   = trim($_POST['new_password'] ?? '');
$newEmail      = trim($_POST['new_email'] ?? '');

// If both empty — nothing to do
if (empty($newPassword) && empty($newEmail)) {
    die("Tidak ada data yang diubah.");
}

// Fetch current user data
$stmt = $conn->prepare("SELECT password, email FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan.");
}

// ===============================
//  UPDATE EMAIL
// ===============================
if (!empty($newEmail)) {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid.");
    }

    // Check if email already used
    $check = $conn->prepare("SELECT id FROM users WHERE email=? AND id != ?");
    $check->bind_param("si", $newEmail, $userId);
    $check->execute();
    $checkResult = $check->get_result();
    if ($checkResult->num_rows > 0) {
        die("Email sudah digunakan oleh pengguna lain.");
    }
    $check->close();

    $updateEmail = $conn->prepare("UPDATE users SET email=? WHERE id=?");
    $updateEmail->bind_param("si", $newEmail, $userId);
    $updateEmail->execute();
    $updateEmail->close();

    $emailUpdated = true;
}

// ===============================
//  UPDATE PASSWORD
// ===============================
if (!empty($newPassword)) {
    if (empty($oldPassword)) {
        die("Kata sandi lama wajib diisi untuk mengganti kata sandi.");
    }

    // Verify old password
    if (!password_verify($oldPassword, $user['password'])) {
        die("Kata sandi lama salah.");
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updatePassword = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $updatePassword->bind_param("si", $hashedPassword, $userId);
    $updatePassword->execute();
    $updatePassword->close();

    $passwordUpdated = true;
}

// ===============================
//  Redirect Back
// ===============================
$conn->close();

$queryParams = [];
if (!empty($emailUpdated)) $queryParams[] = "email_success=1";
if (!empty($passwordUpdated)) $queryParams[] = "password_success=1";
$query = !empty($queryParams) ? '?' . implode('&', $queryParams) : '';

header("Location: edit_profile.php" . $query);
exit();
?>
