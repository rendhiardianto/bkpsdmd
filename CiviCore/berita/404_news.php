<?php
include "../db.php";

if (!isset($_GET['id'])) {
    // No id → redirect back to news list
    header("Location: news.php");
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM news WHERE id=$id");

if ($result->num_rows == 0) {
    // News not found → show 404 page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Berita Tidak Ditemukan</title>
  <style>
        body { 
          font-family: Arial, sans-serif; 
          background:#f5f5f5; 
          margin:0; padding:0; 
          display:flex; justify-content:center; align-items:center; 
          height:100vh; text-align:center; 
        }
        .error-box {
          background:white; padding:40px; border-radius:8px; 
          box-shadow:0 2px 8px rgba(0,0,0,0.2); 
        }
        h1 { font-size:48px; margin:0; color:#cc0000; }
        p { font-size:16px; color:#555; }
        a {
          display:inline-block; margin-top:20px; 
          text-decoration:none; background:#003366; 
          color:white; padding:10px 20px; border-radius:5px;
        }
        a:hover { background:#0055aa; }
  </style>
</head>
<body>
  <div class="error-box">
    <h1>404</h1>
    <p>Maaf, artikel berita yang anda cari tidak tersedia.</p>
    <a href="../news.php">Back to News</a>
  </div>
</body>
</html>

<?php exit;} $news = $result->fetch_assoc(); ?>