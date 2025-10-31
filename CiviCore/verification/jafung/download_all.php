<?php
/**
 * Download Semua Berkas Jafung - Versi Windows (PowerShell ZIP)
 * Dengan nama file ZIP berisi NIP dan Nama Pegawai.
 */

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
requireRole('super_admin');

// Ambil ID submission
$submissionId = intval($_GET['id'] ?? 0);
if ($submissionId <= 0) {
    die("❌ Invalid submission ID.");
}

// Ambil data dokumen dari database
$stmt = $conn->prepare("SELECT nip, fullname, document_paths FROM jafung_submissions WHERE id = ?");
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Submission not found.");
}

$data = $result->fetch_assoc();
$stmt->close();

$docs = json_decode($data['document_paths'], true) ?? [];
if (empty($docs)) {
    die("❌ Tidak ada dokumen untuk diunduh.");
}

// Bersihkan nama agar aman untuk digunakan sebagai nama file
$nipSafe = preg_replace('/[^0-9]/', '', $data['nip']);
$nameSafe = preg_replace('/[^a-zA-Z0-9_]/', '_', $data['fullname']);

// Nama ZIP: NIP_NamaTanggal.zip
$zipFilename = "Berkas_Jafung_" . $nipSafe . "_" . $nameSafe . ".zip";
$tempZipPath = sys_get_temp_dir() . "\\" . uniqid("zip_") . ".zip";

// Kumpulkan file yang valid
$filesToZip = [];
foreach ($docs as $label => $filename) {
    $filePath = __DIR__ . "/../../../layanan/jafung/uploads/documents/" . $filename;
    if (file_exists($filePath)) {
        $filesToZip[] = $filePath;
    }
}

if (empty($filesToZip)) {
    die("❌ Tidak ada dokumen yang valid untuk diunduh.");
}

// Gunakan PowerShell untuk membuat ZIP (bawaan Windows)
$filesList = '"' . implode('","', $filesToZip) . '"';
$cmd = 'powershell -Command "Compress-Archive -Path ' . $filesList . ' -DestinationPath ' . escapeshellarg($tempZipPath) . ' -Force"';

exec($cmd, $output, $result);

// Pastikan ZIP berhasil dibuat
if (!file_exists($tempZipPath)) {
    die("❌ Gagal membuat ZIP menggunakan PowerShell.");
}

// Kirim ZIP ke browser
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
header('Content-Length: ' . filesize($tempZipPath));
readfile($tempZipPath);

// Hapus file sementara
unlink($tempZipPath);
exit;
?>
