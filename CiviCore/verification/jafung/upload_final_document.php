<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
include_once __DIR__ . '/../../datetime_helper.php';
require_once __DIR__ . '/../../api_notification.php';

requireRole('super_admin');

$submissionId = intval($_GET['id'] ?? 0);
if ($submissionId <= 0) {
    echo "<div style='color:red; font-weight:bold;'>❌ NIP tidak ditemukan.</div>";
    exit;
}

// Fetch existing submission data
$stmt = $conn->prepare("
    SELECT s.id, s.nip, s.fullname, s.status, a.phone
    FROM jafung_submissions s
    LEFT JOIN asn_merangin a ON a.nip = s.nip
    WHERE s.id = ?
");
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div style='color:red; font-weight:bold;'>❌ SK tidak ditemukan.</div>";
    exit;
}
$data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['final_doc']) || $_FILES['final_doc']['error'] !== UPLOAD_ERR_OK) {
        echo "<div style='color:red;'>❌ Pilih file yang benar.</div>";
    } else {
        $file = $_FILES['final_doc'];
        $allowedExt = ['pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            echo "<div style='color:red;'>❌ Hanya PDF yang diperbolehkan.</div>";
        } else {
            $uploadDir = __DIR__ . '/uploads/final_docs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $newFileName = $data['nip'] .'_'. $data['fullname']. '_SK_JAFUNG_' . date('Y') . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Update DB
                $stmt = $conn->prepare("UPDATE jafung_submissions SET final_doc=?, status='completed', completed_at=NOW() WHERE id=?");
                $stmt->bind_param("si", $newFileName, $submissionId);
                $stmt->execute();
                $stmt->close();


                echo "<script>alert('✅ SK JAFUNG berhasil diupload, terima kasih!'); window.location='verify_documents.php';</script>";
                exit;
            } else {
                echo "<div style='color:red;'>❌ Gagal mengunggah file.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Upload SK JAFUNG</title>
<link href="upload_final_document.css" rel="stylesheet">
</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="verify_documents.php" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Upload SK Jafung</h1>
  </div>
</div>

<div class="container"> 
  
  <table>
    <tr>
      <td><strong>Nama</strong></td>
      <td><strong>:</strong></td>
      <td><?= htmlspecialchars($data['fullname']); ?></td>
    </tr>
    <tr>
      <td><strong>NIP</strong></td>
      <td><strong>:</strong></td>
      <td><?= htmlspecialchars($data['nip']); ?></td>
    </tr>
    <tr>
      <td><strong>Status Saat ini</strong></td>
      <td><strong>:</strong></td>
      <td><?= ucfirst($data['status']); ?></td>
    </tr>
  </table>

  <form method="POST" enctype="multipart/form-data">
      <label><strong>Pilih Dokumen Final (PDF):</strong></label>
      <input type="file" name="final_doc" accept=".pdf" required>
      <button type="submit">Upload SK</button>
  </form>

</div>

</body>
</html>
