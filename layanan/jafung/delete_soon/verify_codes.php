<?php
session_start();
header('Content-Type: application/json');

$inputCode = trim($_POST['code'] ?? '');
$realCode = $_SESSION['phone_verify_code'] ?? '';
$expires = $_SESSION['phone_verify_expires'] ?? 0;
$pendingPhone = $_SESSION['pending_phone'] ?? '';

if (empty($inputCode) || empty($realCode)) {
    echo json_encode(['status' => 'error', 'message' => 'Kode tidak ditemukan.']);
    exit;
}

if (time() > $expires) {
    echo json_encode(['status' => 'error', 'message' => 'Kode sudah kedaluwarsa.']);
    exit;
}

if ($inputCode !== $realCode) {
    echo json_encode(['status' => 'error', 'message' => 'Kode salah.']);
    exit;
}

// ✅ Verification success — store phone permanently
include_once __DIR__ . '/../../CiviCore/db.php';
$nip = $_SESSION['verified_nip'] ?? '';
if ($nip && $pendingPhone) {
    $stmt = $conn->prepare("UPDATE asn_merangin SET phone = ? WHERE nip = ?");
    $stmt->bind_param("ss", $pendingPhone, $nip);
    $stmt->execute();
    $stmt->close();
}

// Cleanup
unset($_SESSION['phone_verify_code'], $_SESSION['pending_phone'], $_SESSION['phone_verify_expires']);

echo json_encode(['status' => 'success', 'message' => 'Nomor WhatsApp berhasil diverifikasi!']);
