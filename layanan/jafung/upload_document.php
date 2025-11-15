<?php
header('Content-Type: application/json');
error_reporting(0);
session_start();

require_once __DIR__ . '/../../CiviCore/db.php'; // ✅ adjust path to your db.php

ini_set('upload_max_filesize', '3M');
ini_set('post_max_size', '10M');

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
    $no_serkom = $conn->real_escape_string($_POST['no_serkom'] ?? '');


    // --- Validation ---
    if (empty($nip) || empty($fullname) || empty($jenis_usulan)) {
        throw new Exception("Data wajib belum lengkap. Pastikan semua kolom diisi.");
    }

    if (empty($phone)) {
        throw new Exception("Nomor HP wajib diisi sebelum mengunggah dokumen.");
    } elseif (!preg_match('/^[0-9+\-() ]{8,15}$/', $phone)) {
        throw new Exception("Nomor HP tidak valid. Gunakan hanya angka atau simbol +, -, ().");
    }

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

    // ✅ Define file size limits (in bytes)
    $fileSizeLimits = [
        'surat_usul_opd'             => 500 * 1024,   // 500KB
        'sk_cpns'                    => 500 * 1024,   // 500KB
        'sk_pns'                     => 500 * 1024,   // 500KB
        'sk_kp_terakhir'             => 500 * 1024,   // 500KB
        'sk_jabatan_terakhir'        => 500 * 1024,   // 500KB
        'ijazah_dan_transkrip_nilai' => 1 * 1024 * 1024, // 1MB
        'ekinerja'                   => 2 * 1024 * 1024, // 2MB
        'pak_awal'                   => 2 * 1024 * 1024, // 2MB
        'pak_terakhir'               => 2 * 1024 * 1024, // 2MB
        'sertifikat_kompetensi'      => 500 * 1024,   // 500KB
        'anjab_abk'                  => 1 * 1024 * 1024, // 1MB
        'rekomendasi_formasi'        => 1 * 1024 * 1024, // 1MB
        'sk_pemberhentian'           => 500 * 1024,   // 500KB
        'syarat_lain'                => 10 * 1024 * 1024, // 1MB
    ];

    $uploaded_files = [];

    foreach ($fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $file = $_FILES[$field];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // 🔍 Check for upload error
            if ($file['error'] !== 0) {
                throw new Exception("Terjadi kesalahan saat mengunggah file {$field}. (Error code: {$file['error']})");
            }

            // 🔍 Validate extension
            if ($ext !== 'pdf') {
                throw new Exception("File {$field} harus berformat PDF.");
            }

            // ✅ Apply per-file size limit
            $maxFileSize = $fileSizeLimits[$field] ?? (500 * 1024); // fallback = 500KB
            if ($file['size'] > $maxFileSize) {
                $limitKB = round($maxFileSize / 1024);
                throw new Exception("Ukuran file {$field} melebihi {$limitKB}KB. Harap unggah file yang lebih kecil.");
            }

            // ✅ Safe filename
            $safeFullname = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullname);
            $newName = "{$nip}_{$safeFullname}_{$field}.pdf";
            $targetPath = $uploadDir . $newName;

            // 🔄 Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception("Gagal menyimpan file {$field} ke server.");
            }

            $uploaded_files[$field] = $newName;
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
    (ticket_number, nip, fullname, phone, jenis_usulan, document_paths, no_serkom, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    $stmt->bind_param("sssssss", $ticket_number, $nip, $fullname, $phone, $jenis_usulan, $jsonFiles, $no_serkom);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['allow_update'], $_SESSION['verified_nip']);

    $response = [
        "status" => "success",
        "message" => "Dokumen berhasil diajukan, terima kasih.",
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