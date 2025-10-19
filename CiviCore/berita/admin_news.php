<?php
session_start();

include "../db.php";
include "../auth.php";

requireRole(['super_admin', 'admin']);

// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'super_admin'  => '../dashboard_super_admin.php',
    'admin'   => '../dashboard_admin.php',
];
$backUrl = $backLinks[$role];

// (Optional) check if admin logged in
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit; }

$result = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - News</title>
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="../images/button/logo2.png">
  <link href="admin_news.css" rel="stylesheet" type="text/css">

</head>
<body>

  <div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Berita</h1>
    </div>
  </div>
  
  <div class="topbar">
      <a href="add_news.php" class="button add">+ Tambah Berita Baru</a>
  </div>

  <table>
    <tr>
      <th style="text-align: center;">ID</th>
      <th style="text-align: center;">Title</th>
      <th style="text-align: center;">Category</th>
      <th style="text-align: center;">Created</th>
      <th style="text-align: center;">Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr <?php if (!$row['visible']) echo 'style="background-color: #f8d7da;"'; ?>>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo $row['title']; ?></td>
      <td><?php echo $row['category']; ?></td>
      <td><?php echo $row['created_at']; ?></td>
      <td style="text-align: center;">
        <a href="edit_news.php?id=<?php echo $row['id']; ?>" class="button edit">Edit</a>
        <a href="delete_news.php?id=<?php echo $row['id']; ?>" class="button red" onclick="return confirm('Hapus berita ini?');">Delete</a>
        
        <?php if ($row['visible']): ?>
          <a href="toggle_visibility.php?id=<?php echo $row['id']; ?>" class="button gray">Hide</a>
        <?php else: ?>
          <a href="toggle_visibility.php?id=<?php echo $row['id']; ?>" class="button blue">Unhide</a>
        <?php endif; ?>

        <a href="/berita/news_detail.php?slug=<?php echo $row['slug']; ?>" target="_blank" class="button lihat">Lihat</a>

      </td>

    </tr>
    <?php endwhile; ?>
  </table>

<div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>

</body>
</html>
