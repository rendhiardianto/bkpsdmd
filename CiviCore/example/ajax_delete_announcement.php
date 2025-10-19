<?php
include "auth.php";
requireRole('admin');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM announcements WHERE id=$id";
    if ($conn->query($sql)) {
        echo "🗑️ Announcement deleted!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
