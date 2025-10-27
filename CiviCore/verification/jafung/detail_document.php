<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
include_once __DIR__ . '/../../datetime_helper.php';

requireRole('super_admin');

$submissionId = intval($_GET['id'] ?? 0);
if ($submissionId <= 0) {
    echo "<div style='color:red; font-weight:bold;'>❌ Invalid submission ID.</div>";
    exit;
}

// Fetch submission
$stmt = $conn->prepare("
    SELECT nip, fullname, jenis_usulan, document_paths, status, created_at 
    FROM jafung_submissions 
    WHERE id = ?
");
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div style='color:red; font-weight:bold;'>❌ Submission not found.</div>";
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

$docs = json_decode($data['document_paths'], true) ?? [];

// Map jenis_usulan
$jenisMap = [
    'JF4' => '(JF4) Kenaikan Jenjang JF',
    'JF3' => '(JF3) Pengangkatan Kembali ke dalam JF',
    'JF2' => '(JF2) Perpindahan dari Jabatan Lain ke dalam JF',
    'JF1' => '(JF1) Pengangkatan Pertama dalam JF'
];
$displayJenis = $jenisMap[$data['jenis_usulan']] ?? htmlspecialchars($data['jenis_usulan']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Dokumen - <?= htmlspecialchars($data['fullname']); ?></title>
<link href="style.css" rel="stylesheet" type="text/css">
<style>
body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background: #f4f4f4;
}
.container {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
  max-width: 900px;
  margin: auto;
}
h2 { text-align: center; }
.pdf-container {
  margin-top: 20px;
}
.pdf-item {
  margin-bottom: 25px;
}
iframe {
  width: 100%;
  height: 400px;
  border: 1px solid #ccc;
  border-radius: 8px;
}
.back-btn {
  display: inline-block;
  background: #007bff;
  color: white;
  padding: 10px 15px;
  border-radius: 6px;
  text-decoration: none;
  margin-bottom: 15px;
}
.back-btn:hover {
  background: #0056b3;
}
.file-label {
  text-decoration: none;
}
.file-label span {
  text-transform: uppercase;
  text-decoration: none;
}
</style>
</head>
<body>

<div class="container">
  <a href="verify_documents.php" class="back-btn">← Kembali</a>

  <h2>Detail Dokumen - <?= htmlspecialchars($data['fullname']); ?></h2>
  <p><strong>NIP:</strong> <?= htmlspecialchars($data['nip']); ?></p>
  <p><strong>Jenis Usulan:</strong> <?= $displayJenis; ?></p>
  <p><strong>Status:</strong> <?= ucfirst($data['status']); ?></p>
  <p><strong>Tanggal Pengajuan:</strong> <?= formatTanggalIndonesia($data['created_at']); ?></p>

  <hr>

  <div class="pdf-container">
    <?php if (!empty($docs)): ?>
      <?php foreach ($docs as $label => $filename): ?>
        <div class="pdf-item">
          <h4 style="text-decoration:none;">
            📄 <?= strtoupper(str_replace('_', ' ', htmlspecialchars($label))); ?>
          </h4>
          <iframe src="/layanan/jafung/uploads/documents/<?= urlencode($filename); ?>"></iframe>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color:gray;">Tidak ada dokumen yang diunggah.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
