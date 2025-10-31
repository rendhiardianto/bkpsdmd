<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';

requireRole('super_admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request.');
}

$submissionId = intval($_POST['submission_id'] ?? 0);
if ($submissionId <= 0) {
    die('Invalid submission ID.');
}

// Get document paths
$stmt = $conn->prepare("SELECT fullname, document_paths FROM jafung_submissions WHERE id = ?");
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Submission not found.');
}

$data = $result->fetch_assoc();
$stmt->close();

$fullname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $data['fullname']); // clean for file name
$docs = json_decode($data['document_paths'], true) ?? [];

if (empty($docs)) {
    die('No documents found.');
}

$zip = new ZipArchive();
$zipFilename = sys_get_temp_dir() . "/Berkas_Jafung_{$fullname}_" . date('Ymd_His') . ".zip";

if ($zip->open($zipFilename, ZipArchive::CREATE) !== TRUE) {
    die('Unable to create ZIP file.');
}

// Add each file to the ZIP
foreach ($docs as $label => $filename) {
    $filePath = __DIR__ . "/layanan/jafung/uploads/documents/" . $filename;
    if (file_exists($filePath)) {
        $safeName = strtoupper(str_replace('_', ' ', $label)) . " - " . basename($filename);
        $zip->addFile($filePath, $safeName);
    }
}

$zip->close();

// Force download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFilename) . '"');
header('Content-Length: ' . filesize($zipFilename));
readfile($zipFilename);

// Clean up
unlink($zipFilename);
exit;
?>
