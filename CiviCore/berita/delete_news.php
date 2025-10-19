<?php
include "../db.php";

$id = intval($_GET['id']);
$conn->query("DELETE FROM news WHERE id=$id");

header("Location: admin_news.php");
exit;
