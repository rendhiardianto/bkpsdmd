<?php

session_start();
include_once __DIR__ . '/../../CiviCore/db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['document'];

    if ($file['error'] === 0 && pathinfo($file['name'], PATHINFO_EXTENSION) === 'pdf') {
        $newName = 'DOC_' . $user_id . '_' . time() . '.pdf';
        $target = "uploads/documents/" . $newName;
        move_uploaded_file($file['tmp_name'], $target);

        $stmt = $conn->prepare("INSERT INTO service_submissions (user_id, document_path) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $newName);
        $stmt->execute();

        echo "✅ Document uploaded successfully.";
    } else {
        echo "❌ Please upload a valid PDF.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
  <label>Upload PDF Document:</label>
  <input type="file" name="document" accept=".pdf" required>
  <button type="submit">Submit</button>
</form>
