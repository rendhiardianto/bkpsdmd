<?php
session_start();
include_once __DIR__ . '/../../CiviCore/db.php';
include_once __DIR__ . '/../../CiviCore/config.php';
require_once __DIR__ . '/../../api_notification.php';

header('Content-Type: application/json');

$nip = $_SESSION['verified_nip'] ?? null;
$phone = trim($_POST['phone'] ?? '');

if (!$nip) {
    echo json_encode(['status' => 'error', 'message' => 'Akses tidak sah.']);
    exit;
}

if (empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor tidak boleh kosong.']);
    exit;
}

if (!preg_match('/^(?:\+62|62|0)[0-9]{8,15}$/', $phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor tidak valid. Gunakan format +628xxx atau 08xxx']);
    exit;
}

// Generate 6-digit verification code
$code = rand(100000, 999999);

// Save to session
$_SESSION['phone_verify_code'] = $code;
$_SESSION['pending_phone'] = $phone;
$_SESSION['phone_verify_expires'] = time() + 300; // 5 minutes validity

// Send message via WhatsApp API (example using Wablas or Fonnte)
$apiToken = "YOUR_WABLAS_OR_FONNTE_TOKEN";
$apiUrl = "https://api.fonnte.com/send"; // Example Fonnte URL

$message = "Kode verifikasi Pengajuan Jabatan Fungsional Anda adalah: *$code*. Berlaku selama 5 menit.";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
  'target' => $phone,
  'message' => $message,
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: $apiToken"
]);
$response = curl_exec($ch);
curl_close($ch);

echo json_encode(['status' => 'success', 'message' => 'Kode verifikasi telah dikirim via WhatsApp!']);
