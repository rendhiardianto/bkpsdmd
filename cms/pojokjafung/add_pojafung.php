<?php include "../db.php";
require_once("../auth.php");

requireRole(['super_admin', 'admin']);

// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'super_admin'  => '../dashboard_super_admin.php',
    'admin'   => '../dashboard_cms_admin.php',
];
$backUrl = $backLinks[$role];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $total    = $conn->real_escape_string($_POST['total']);
    $link    = $conn->real_escape_string($_POST['link']);

    $cek = mysqli_query($conn, "SELECT * FROM jf_meranginkab WHERE jabatan='$jabatan'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "";
        ?><script>
                alert('Jabatan sudah terdaftar di database.');
                window.location.href='add_pojafung.php';
        </script><?php
    } else {
        $insert = mysqli_query($conn, "INSERT INTO jf_meranginkab (jabatan, total, link)
        VALUES ('$jabatan','$total','$link')");
          if ($insert) {
            $success = "";
            ?><script>
                alert('Data Jabatan sudah ditambahkan.');
                window.location.href='add_pojafung.php';
            </script><?php
          } 
          else {
            $errMSG = "" . mysqli_error($conn);
            ?><script>
                alert('Maaaf, terjadi kesalahan, silahkan ulangi kembali');
                window.location.href='add_pojafung.php';
        </script><?php
          }
        }
  }
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
  <title>Dashboard - Add Jabatan Fungsional</title>
  
  <link href="add_pojafung.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">

  

</head>
<body>

<div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Add Jabatan Fungsional Merangin</h1>
    </div>
</div>

<div class="flex-container">

  <div class="form-box">
    <h2>Tambahkan Jabatan Fungsional</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="jabatan" placeholder="Nama Jabatan Fungsional" required>
      <input type="text" name="total" placeholder="Total Penjabat" required>
      <button type="submit">Tambahkan</button>
    </form>
  </div>

</div>

<div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>

</body>
</html>