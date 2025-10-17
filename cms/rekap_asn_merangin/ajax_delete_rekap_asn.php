<?php
include "../auth.php";
requireRole(['super_admin', 'admin']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM rekap_asn_merangin WHERE id=$id";
    if ($conn->query($sql)) {
        echo "🗑️ Data berhasil dihapus!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
