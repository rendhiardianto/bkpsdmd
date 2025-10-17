<?php
include "../auth.php";

requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM announcements WHERE id=$id";
    if ($conn->query($sql)) {
        echo "🗑️ Pengumuman berhasil dihapus!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
