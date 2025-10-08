<?php
include "../auth.php";
requireRole(['admin', 'user']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM jf_bkn WHERE id=$id";
    if ($conn->query($sql)) {
        echo "🗑️ Jabatan Fungsional sudah dihapus!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
