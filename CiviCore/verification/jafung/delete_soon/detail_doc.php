<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
include_once __DIR__ . '/../../datetime_helper.php';

// Get ticket number from URL
$ticket = $_GET['ticket'] ?? '';

if (empty($ticket)) {
    die("Ticket tidak ditemukan.");
}

// Fetch data from database
$stmt = $conn->prepare("SELECT fullname, nip, phone, jenis_usulan, document_paths, no_serkom, status, created_at 
                        FROM jafung_submissions 
                        WHERE ticket_number = ? LIMIT 1");
$stmt->bind_param("s", $ticket);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data tidak ditemukan.");
}

$data = $result->fetch_assoc();
$stmt->close();

// Decode document JSON
$documents = json_decode($data['document_paths'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pengajuan - <?= htmlspecialchars($data['fullname']) ?></title>
<style>
body { font-family: Arial, sans-serif; background: #fafafa; margin: 20px; }
.container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 6px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
h2 { color: #333; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
table th { background: #f4f4f4; }
a.pdf-link { color: #007bff; text-decoration: none; }
a.pdf-link:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
  <h2>Detail Pengajuan Jafung</h2>

  <p><strong>Nama:</strong> <?= htmlspecialchars($data['fullname']) ?></p>
  <p><strong>NIP:</strong> <?= htmlspecialchars($data['nip']) ?></p>
  <p><strong>Jenis Usulan:</strong> <?= htmlspecialchars($data['jenis_usulan']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></p>
  <p><strong>Tanggal Pengajuan:</strong> <?= htmlspecialchars($data['created_at']) ?></p>

  <hr>
  <h3>Dokumen Diupload</h3>
  <table>
    <tr><th>Nama Dokumen</th><th>File</th></tr>
    <?php
    foreach ($documents as $key => $filename) {
        $label = ucwords(str_replace('_', ' ', $key));
        $fileUrl = "uploads/documents/" . htmlspecialchars($filename);
        echo "<tr>
                <td>{$label}</td>
                <td><a class='pdf-link' href='{$fileUrl}' target='_blank'>Lihat File</a></td>
              </tr>";
    }
    ?>
  </table>

  <?php if (!empty($data['no_serkom'])): ?>
    <hr>
    <h3>Nomor Sertifikat Uji Kompetensi</h3>
    <p><strong><?= htmlspecialchars($data['no_serkom']) ?></strong></p>
  <?php endif; ?>

  <hr>
  <a href="index.php">← Kembali ke Daftar Pengajuan</a>
</div>
</body>
</html>
