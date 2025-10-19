<?php
include "../db.php";

$id = intval($_GET['id']);
$conn->query("DELETE FROM blog WHERE id=$id");

header("Location: admin_blog.php");
exit;
