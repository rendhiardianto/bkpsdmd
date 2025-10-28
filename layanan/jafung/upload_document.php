<?php
header('Content-Type: application/json');
error_reporting(0);
session_start();

require_once __DIR__ . '/../../CiviCore/db.php'; // ✅ adjust path to your db.php

$response = [
    "status" => "error",
    "message" => "Terjadi kesalahan tak terduga."
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metode tidak valid. Gunakan POST.");
    }

    // --- Collect input ---
    $nip = $conn->real_escape_string($_POST['nip'] ?? '');
    $fullname = $conn->real_escape_string($_POST['fullname'] ?? '');
    $phone = trim($conn->real_escape_string($_POST['phone'] ?? ''));
    $jenis_usulan = $conn->real_escape_string($_POST['jenis_usulan'] ?? '');

    // --- Validation ---
    if (empty($nip) || empty($fullname) || empty($jenis_usulan)) {
        throw new Exception("Data wajib belum lengkap. Pastikan semua kolom diisi.");
    }

    if (empty($phone)) {
        throw new Exception("Nomor HP wajib diisi sebelum mengunggah dokumen.");
    } elseif (!preg_match('/^[0-9+\-() ]{8,15}$/', $phone)) {
        throw new Exception("Nomor HP tidak valid. Gunakan hanya angka atau simbol +, -, ().");
    }

    // --- Upload directory ---
    $uploadDir = __DIR__ . "/uploads/documents/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fields = [
        'surat_usul_opd', 'sk_cpns', 'sk_pns', 'sk_kp_terakhir', 'sk_jabatan_terakhir',
        'ijazah_dan_transkrip_nilai', 'ekinerja', 'pak_awal', 'pak_terakhir',
        'sertifikat_kompetensi', 'no_serkom', 'anjab_abk', 'rekomendasi_formasi',
        'sk_pemberhentian', 'syarat_lain'
    ];

    $uploaded_files = [];

    foreach ($fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $file = $_FILES[$field];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] === 0 && $ext === 'pdf') {
                $safeFullname = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullname);
                $newName = "{$nip}_{$safeFullname}_{$field}.pdf";
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $uploaded_files[$field] = $newName;
                }
            }
        }
    }

    if (empty($uploaded_files)) {
        throw new Exception("Tidak ada dokumen PDF valid yang diunggah.");
    }

    // --- Generate ticket ---
    do {
        $ticket_number = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $check = $conn->prepare("SELECT COUNT(*) FROM jafung_submissions WHERE ticket_number = ?");
        $check->bind_param("s", $ticket_number);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();
    } while ($count > 0);

    // --- Save submission ---
    $jsonFiles = json_encode($uploaded_files, JSON_UNESCAPED_SLASHES);
    $stmt = $conn->prepare("
        INSERT INTO jafung_submissions 
        (ticket_number, nip, fullname, phone, jenis_usulan, document_paths, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())
    ");
    $stmt->bind_param("ssssss", $ticket_number, $nip, $fullname, $phone, $jenis_usulan, $jsonFiles);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['allow_update'], $_SESSION['verified_nip']);

    $response = [
        "status" => "success",
        "message" => "Dokumen berhasil diunggah.",
        "ticket" => $ticket_number
    ];

} catch (Exception $e) {
    $response = [
        "status" => "error",
        "message" => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
?>
