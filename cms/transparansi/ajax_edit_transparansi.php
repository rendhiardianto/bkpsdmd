<?php
include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $tipe_dokumen= $conn->real_escape_string($_POST['tipe_dokumen']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $nomor = $conn->real_escape_string($_POST['nomor']);
    $tahun = $conn->real_escape_string($_POST['tahun']);

    // Fetch old record (to delete old files if needed)
    $old = $conn->query("SELECT attachment FROM transparansi WHERE id=$id")->fetch_assoc();

    $updateFields = "tipe_dokumen='$tipe_dokumen', judul='$judul', nomor='$nomor', tahun='$tahun'";

    // === Handle attachment update (keep original name) ===
    if (!empty($_FILES['attachment']['name'])) {
        // Delete old attachment file if exists
        if (!empty($old['attachment']) && file_exists("uploads/files/" . $old['attachment'])) {
            unlink("uploads/files/" . $old['attachment']);
        }

        $originalName = basename($_FILES['attachment']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);

        $uploadDir = __DIR__ . "/uploads/files/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Prevent overwrite: add _1, _2, etc.
        $targetFile = $uploadDir . $originalName;
        $counter = 1;
        while (file_exists($targetFile)) {
            $newName = $nameOnly . "_" . $counter . "." . $ext;
            $targetFile = $uploadDir . $newName;
            $counter++;
        }

        $attachment = basename($targetFile);

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $updateFields .= ", attachment='$attachment'";
        } else {
            echo "Gagal mengunggah lampiran.";
            exit;
        }
    }

    // === Update database ===
    $sql = "UPDATE transparansi SET $updateFields WHERE id=$id";

    if ($conn->query($sql)) {
        echo "Dokumen berhasil diperbarui!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
