<?php
include "../db.php";
include "../auth.php";

requireRole(['admin', 'user']);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get current visibility
    $query = $conn->query("SELECT visible FROM news WHERE id = $id");
    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $newStatus = $row['visible'] ? 0 : 1;

        // Update visibility
        $conn->query("UPDATE news SET visible = $newStatus WHERE id = $id");
    }
}

header("Location: admin_news.php");
exit;
?>