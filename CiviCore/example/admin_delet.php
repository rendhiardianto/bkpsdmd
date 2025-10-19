<?php
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$conn->query("DELETE FROM users WHERE id=$id");
echo "<script>alert('User deleted!'); window.location='admin_dashboard.php';</script>";
?>
