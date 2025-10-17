<?php
include "../db.php";

requireRole(['super_admin', 'admin']);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: login.php");
    exit;
}


$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Announcements</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .container { max-width:1000px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
    th { background:#3498db; color:white; }
    tr:hover { background:#f1f1f1; }
    img { max-width:80px; border-radius:6px; }
    a.btn { padding:6px 12px; border-radius:6px; color:white; text-decoration:none; margin-right:5px; }
    .edit { background:#27ae60; }
    .delete { background:#e74c3c; }
    .add { background:#3498db; float:right; }
  </style>
</head>
<body>
  
</body>
</html>
