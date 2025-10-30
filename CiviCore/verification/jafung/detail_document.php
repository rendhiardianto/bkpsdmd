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
    SELECT nip, fullname, jenis_usulan, document_paths, no_serkom, status, created_at 
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<title>Detail Dokumen - <?= htmlspecialchars($data['fullname']); ?></title>
<link href="detail_document.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="container">
  <a href="verify_documents.php" class="back-btn">&#10094; Kembali</a>

  <h2>Detail Dokumen - <?= htmlspecialchars($data['fullname']); ?></h2>
  <p><strong>NIP:</strong> <?= htmlspecialchars($data['nip']); ?></p>
  <p><strong>Jenis Usulan:</strong> <?= $displayJenis; ?></p>
  <p><strong>Status:</strong> <?= ucfirst($data['status']); ?></p>
  <p><strong>Tanggal Pengajuan:</strong> <?= formatTanggalIndonesia($data['created_at']); ?></p>

  <?php if (!empty($data['no_serkom'])): ?>
    <p><strong>Nomor Sertifikat Uji Kompetensi:</strong> 
      <?= htmlspecialchars($data['no_serkom']); ?>
    </p>
  <?php endif; ?>

  <hr>

  <div class="pdf-container">
    <?php if (!empty($docs)): ?>
      <?php foreach ($docs as $label => $filename): ?>
        <div class="pdf-item">
          <h3 style="text-decoration:none;">
            &#10148; <?= strtoupper(str_replace('_', ' ', htmlspecialchars($label))); ?>
          </h3>
          <a href="/layanan/jafung/uploads/documents/<?= urlencode($filename); ?>" 
              download="<?= htmlspecialchars($filename); ?>" class="download-btn">
              <i class="fas fa-download"></i> Download
          </a>
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
