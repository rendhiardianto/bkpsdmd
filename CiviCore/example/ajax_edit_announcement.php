<?php
include "auth.php";
requireRole('admin');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $sql = "UPDATE announcements SET title='$title', content='$content' WHERE id=$id";
    if ($conn->query($sql)) {
        echo "✅ Announcement updated!";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
