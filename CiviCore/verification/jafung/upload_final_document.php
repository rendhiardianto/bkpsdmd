<?php
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../auth.php';
include_once __DIR__ . '/../../datetime_helper.php';
require_once __DIR__ . '/../../api_notification.php';

requireRole('super_admin');

$submissionId = intval($_GET['id'] ?? 0);
if ($submissionId <= 0) {
    echo "<div style='color:red; font-weight:bold;'>❌ Invalid submission ID.</div>";
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
    echo "<div style='color:red; font-weight:bold;'>❌ Submission not found.</div>";
    exit;
}
$data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['final_doc']) || $_FILES['final_doc']['error'] !== UPLOAD_ERR_OK) {
        echo "<div style='color:red;'>❌ Please select a valid file.</div>";
    } else {
        $file = $_FILES['final_doc'];
        $allowedExt = ['pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            echo "<div style='color:red;'>❌ Only PDF files are allowed.</div>";
        } else {
            $uploadDir = __DIR__ . '/uploads/final_docs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $newFileName = $data['nip'] . '_FINAL_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Update DB
                $stmt = $conn->prepare("UPDATE jafung_submissions SET final_doc=?, status='completed', completed_at=NOW() WHERE id=?");
                $stmt->bind_param("si", $newFileName, $submissionId);
                $stmt->execute();
                $stmt->close();

                // Send WhatsApp notification
                $message = "📜 Dear {$data['fullname']}, your submission (NIP: {$data['nip']}) has been *completed*. The final document is now available.";
                sendWhatsAppNotification($data['phone'], $message);

                echo "<script>alert('✅ Final document uploaded and marked as completed!'); window.location='verify_documents.php';</script>";
                exit;
            } else {
                echo "<div style='color:red;'>❌ Failed to upload file.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Upload Final Document</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f4f4;
  margin: 20px;
}
.container {
  max-width: 500px;
  margin: auto;
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
h2 { text-align: center; }
input[type=file] {
  width: 100%;
  margin: 15px 0;
}
button {
  background: #28a745;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
}
button:hover { background: #218838; }
a.back {
  display: inline-block;
  margin-bottom: 10px;
  text-decoration: none;
  color: #007bff;
}
a.back:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="container">
  <a href="verify_documents.php" class="back">← Kembali</a>
  <h2>Upload Final Document</h2>
  <p><strong>Nama:</strong> <?= htmlspecialchars($data['fullname']); ?></p>
  <p><strong>NIP:</strong> <?= htmlspecialchars($data['nip']); ?></p>
  <p><strong>Status Saat Ini:</strong> <?= ucfirst($data['status']); ?></p>

  <form method="POST" enctype="multipart/form-data">
      <label><strong>Pilih Dokumen Final (PDF):</strong></label>
      <input type="file" name="final_doc" accept=".pdf" required>
      <button type="submit">Upload & Tandai Selesai</button>
  </form>
</div>

</body>
</html>
