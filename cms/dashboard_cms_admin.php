<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include "db.php";
include "auth.php";

requireRole('admin');
?>

<?php
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT nip, fullname, profile_pic FROM users WHERE id=$userId");
$user = $result->fetch_assoc();
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
  <title>CMS Admin Dashboard</title>
  <link href="dashboard_cms_admin.css" rel="stylesheet" type="text/css">
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="/icon/button/logo2.png">

</head>
<body>
  <div class="header">
            <div class="logo">
            	<a href="../index.php" target="_blank"><img src="../icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
            </div>
            <div class="navbar">
            	<a href="logout.php" class="logout" style="text-decoration: none;">Logout &#10006;</a>
            </div>
    </div>
    <div class="roleHeader">
      <h1>CMS Admin Dashboard</h1>
    </div>

<div class="content">

    <div class="leftSide">

      <div class="userBio">
        <!--<h2 style="font-family: FreeHand;">Selamat Datang,</h2>-->
        <div class="greetings">
          <?php
            date_default_timezone_set('Asia/Jakarta');
            $a = date ("H");

            if (($a >=4) && ($a<=10))
            {
              echo "Selamat Pagi ";
              echo "&#9728;";
            }
            elseif (($a>=11) && ($a<=15))
            {
              echo "Selamat Siang ";
              echo "&#9729;";
            }
            elseif (($a>=16) && ($a<=18))
            {
              echo "Selamat Sore ";
              echo "&#9788;";
            }
            else
            {
              echo "Selamat Malam ";
              echo "&#9790;";
            }
          ?>
        </div>
        <div class="namaProfil">
          <br><?php echo $user['fullname']; ?>
        </div>
        <div class="nipProfil">
          NIP: <?php echo $user['nip']; ?>
        </div>
        
      </div>

      <div class="fotoProfil">
        <?php if ($user['profile_pic']): ?>
        <img src="uploads/profile_pics/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
        <?php else: ?>
        <img src="uploads/profile_pics/default.png" alt="Default Profile">
        <?php endif; ?>
      </div>

    </div>
  
  <div class="rightSide">

    <div class="flex-item-main">
      <p><a href="pengumuman/dashboard_pengumuman.php">
        <img src="../icon/button/announcement.png"></a><br>PENGUMUMAN</p>
    </div>

    <div class="flex-item-main">
      <p><a href="berita/admin_news.php">
        <img src="../icon/button/news.png"></a><br>BERITA</p>
    </div>

    <div class="flex-item-main">
      <p><a href="blog/admin_blog.php">
        <img src="../icon/button/blog.png"></a><br>BLOG</p>
    </div>

    <div class="flex-item-main">
      <p><a href="infoGrafis/admin_infoGrafis.php">
        <img src="../icon/button/graphics.png"></a><br>INFOGRAFIS</p>
    </div>

    <div class="flex-item-main">
      <p><a href="transparansi/dashboard_transparansi.php">
        <img src="../icon/button/transparansi.png"></a><br>TRANSPARANSI</p>
    </div>
      
    <!--<div class="top-bar">
      <a href="admin_add.php" style="padding:8px 14px;background:#2ecc71;color:white;border-radius:6px;text-decoration:none;">➕ Add User</a>

      <form id="filterForm">
        <input type="text" name="search" id="search" placeholder="Search by name/email">
        <select name="role_filter" id="role_filter">
          <option value="">All Roles</option>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
        <button type="submit">Filter</button>
      </form>
    </div>

  <div id="userTable">
    AJAX loads user table here 
  </div>-->

</div><!-- CONTENT CLOSE-->

  <div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
  </div>

</body>
</html>