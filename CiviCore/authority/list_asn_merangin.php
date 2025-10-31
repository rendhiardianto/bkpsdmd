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

  <!-- Edit Modal --> 
  <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);"> 
      <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px;"> 
          <h3>Edit Data ASN</h3> 
          <form id="editForm"> <input type="hidden" name="id" id="edit_id"> 
              <label>NIP:</label><br> <input type="text" name="nip" id="edit_nip"><br> 
              <label>Nama Lengkap:</label><br> <input type="text" name="fullname" id="edit_fullname"><br> 
              <label>Jabatan:</label><br> <input type="text" name="jabatan" id="edit_jabatan"><br> 
              <button type="submit">💾 Save</button> 
              <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button> 
          </form> 
      </div>
  </div>


<!-- Detail Modal -->
<div id="detailModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px;">
        <h2>Detail Data</h2>
        <p><strong>NIP:</strong> <span id="detail_nip"></span></p>
        <p><strong>Full Name:</strong> <span id="detail_fullname"></span></p>
        <p><strong>Pangkat:</strong> <span id="detail_pangkat"></span></p>
        <p><strong>Jabatan:</strong> <span id="detail_jabatan"></span></p>
        <p><strong>Organisasi:</strong> <span id="detail_skpd"></span></p>
        <p><strong>Tempat Lahir:</strong> <span id="detail_tempat_lahir"></span></p>
        <button id="closeDetail">Close</button>
    </div>
</div>

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

    $("#searchResults").html("<tr><td colspan='9' style='text-align:center;'>🔍 Mencari...</td></tr>");

    $.get("ajax_search_asn_merangin.php", { q: query }, function(data) {
      $("#searchResults").html(data);
    });
  });
});
</script>

<script>
$(document).ready(function() {

  // --- Delegated click handler for EDIT ---
  $(document).on("click", ".edit", function() {
    let row = $(this).closest("tr");
    let id = row.data("id");

    // Grab cell values (based on column index)
    let nip = row.children().eq(1).text().trim();
    let fullname = row.children().eq(2).text().trim();
    let jabatan = row.children().eq(4).text().trim();
    let phone = row.children().eq(7).text().trim();

    $("#edit_id").val(id);
    $("#edit_nip").val(nip);
    $("#edit_fullname").val(fullname);
    $("#edit_jabatan").val(jabatan);

    $("#editModal").fadeIn();
  });

  // --- Close modal when clicking outside ---
  $(window).on("click", function(e){
    if ($(e.target).is("#editModal")) {
      $("#editModal").fadeOut();
    }
  });

  // --- Save Edit (AJAX POST) ---
  $("#editForm").submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: "ajax_edit_asn_merangin.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response){
        alert(response);
        $("#editModal").fadeOut();
        $("#searchInput").keyup(); // refresh current search results
      }
    });
  });

  // --- Delegated click handler for DELETE ---
  $(document).on("click", ".delete", function() {
    if (!confirm("Apakah anda ingin menghapus data ini?")) return;

    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_asn_merangin.php", { id: id }, function(response){
      alert(response);
      $("#searchInput").keyup(); // refresh table results
    });
  });

});

// --- Delegated click handler for DETAIL ---
$(document).on("click", ".detail", function() {
    let row = $(this).closest("tr");

    $("#detail_nip").text(row.children().eq(1).text().trim());
    $("#detail_fullname").text(row.children().eq(2).text().trim());
    $("#detail_pangkat").text(row.children().eq(3).text().trim());
    $("#detail_jabatan").text(row.children().eq(4).text().trim());
    $("#detail_skpd").text(row.children().eq(5).text().trim());
    $("#detail_tempat_lahir").text(row.children().eq(6).text().trim());

    $("#detailModal").fadeIn();
});

// Close detail modal
$("#closeDetail").click(function(){
    $("#detailModal").fadeOut();
});

// Also close modal when clicking outside
$(window).on("click", function(e){
    if ($(e.target).is("#detailModal")) {
        $("#detailModal").fadeOut();
    }
});

</script>



</body>
</html>