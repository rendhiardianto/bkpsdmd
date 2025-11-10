<?php 
include "../db.php";
require_once("../auth.php");


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
  <title>Dashboard - Manage Data ASN</title>

  <link href="list_asn_merangin.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">
</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Dashboard Management Data ASN Merangin</h1>
  </div>
</div>

<div class="content">
      <div class="tableRekap">

        <h2 style="text-align: center;">Tabel Data ASN Kabupaten Merangin</h2>
        <!-- Button to go to tambah_pegawai.php -->
  <div style="text-align: right; margin-bottom: 10px; margin-right: 10px;">
    <a href="add_new_asn.php" 
       style="background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">
       + Tambah Pegawai Baru
    </a>
  </div>
        <div style="height:500px; border:0px solid #ccc; margin:auto; overflow:auto;">

          <input class="searchInput" type="text" id="searchInput" placeholder="Cari NIP / Nama / Jabatan">
          
          <table id="userTable1">
            <thead>
              <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Lengkap</th>
                <th>Status Pegawai</th>
                <th>Jabatan</th>
                <th>Organisasi</th>
                <th>Organisasi Induk</th>
                <th>TTL</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="searchResults">
              <tr><td colspan="9" style="text-align:center;">Ketik nama atau NIP untuk mencari data...</td></tr>
            </tbody>
          </table>
        </div>
      </div><!--TabelRekap-->
</div> <!--- CONTENT CLOSE-->

<!-- Toast Notification -->
<div id="toast"></div>

<div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/JavaScript/input_rekap_asn.js"></script>

<script>
  // JavaScript code to handle tab switching
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "flex";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>

<script>
$(document).ready(function() {
  $("#searchInput").on("keyup", function() {
    let query = $(this).val().trim();

    if (query.length < 2) {
      $("#searchResults").html("<tr><td colspan='9' style='text-align:center;'>Ketik minimal 2 huruf untuk mencari...</td></tr>");
      return;
    }

    $("#searchResults").html("<tr><td colspan='9' style='text-align:center;'>Mencari...</td></tr>");

    $.get("ajax_search_asn_merangin.php", { q: query }, function(data) {
      $("#searchResults").html(data);
    });
  });
});
</script>


</body>
</html>