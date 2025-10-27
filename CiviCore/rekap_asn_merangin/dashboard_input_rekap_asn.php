<?php include "../db.php";
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

// --- Delete record ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT * FROM rekap_asn_merangin WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $conn->query("DELETE FROM rekap_asn_merangin WHERE id=$id");
        echo "<script>alert('Data sudah dihapus!'); window.location='dashboard_input_rekap_asn.php';</script>";
        exit;
    }
}

$result = $conn->query("SELECT * FROM rekap_asn_merangin ORDER BY id DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tahun = $conn->real_escape_string($_POST['tahun']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $sub_kategori = $conn->real_escape_string($_POST['sub_kategori']);
    $label = $conn->real_escape_string($_POST['label']);
    $jumlah = $conn->real_escape_string($_POST['jumlah']);

    // --- Insert into DB ---
    $stmt = $conn->prepare("INSERT INTO rekap_asn_merangin (tahun, semester, kategori, sub_kategori, label, jumlah, created_at) VALUES (?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssss", $tahun, $semester, $kategori, $sub_kategori, $label, $jumlah);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil diubah!'); window.location='dashboard_input_rekap_asn.php';</script>";
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
  <title>Dashboard - Input Rekap ASN Merangin</title>
  
  <link href="dashboard_input_rekap_asn.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">

</head>
<body>

<div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Input Rekap ASN Merangin</h1>
    </div>
</div>

<div class="flex-container">
  <div class="form-box">
    <h2>Tambahkan Data Baru</h2>
    
    <form id="rekapForm" method="POST" enctype="multipart/form-data">
      <label>Tahun</label>
      <input type="text" name="tahun" placeholder="Masukkan Tahun" required>

      <label>Semester</label>
      <select name="semester">
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
      </select>

      <label for="kategori">Kategori</label>
      <select id="kategori" onchange="updateSubOptions()">
        <option value="">(Pilih Kategori)</option>
        <option value="PNS">PNS</option>
        <option value="PPPK">PPPK</option>
        <option value="PPPK Paruh Waktu">PPPK Paruh Waktu</option>
      </select>

      <div id="subOptionContainer" style="display:none; margin-top:15px;">
        <label for="subKategori">Sub-Kategori</label>
        <select id="subKategori" onchange="showInputForm()">
          <option value="">(Pilih Sub-Kategori)</option>
        </select>
      </div>

      <div id="inputForm" style="display:none; margin-top:15px;">
        <h4 id="formTitle"></h4>
      </div>
    </form>
    <!-- Message area -->
    <div id="message" style="margin-top:10px; font-weight:bold;"></div>

  </div>

  <div class="tableRekap">
    <h2 style="text-align: center;">Rekapitulasi ASN Pemkab Merangin</h2>
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cari sub-kategori atau label" title="Type in a name">
    
    <div style="height:500px; border:1px solid #ccc; margin:auto;">
      <table id="userTable1" border="1" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
        <tr>
          <th>No</th>
          <th>Tahun</th>
          <th>Semester</th>
          <th>Kategori</th>
          <th>Sub-Kategori</th>
          <th>Label</th>
          <th>Jumlah</th>
          <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?php echo $row['id']; ?></td>
          <td class="tahun"><?php echo $row['tahun']; ?></td>
          <td class="semester"><?php echo $row['semester']; ?></td>
          <td class="kategori"><?php echo $row['kategori']; ?></td>
          <td class="sub_kategori"><?php echo $row['sub_kategori']; ?></td>
          <td class="label"><?php echo $row['label']; ?></td>
          <td class="jumlah" style="text-align:center;"><b><?php echo $row['jumlah']; ?></b></td>
          
          <td style="width: 15%;">
            <button class="edit">✏️ Edit</button>
            <button class="delete">🗑 Delete</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</div>

<!-- Modal for Edit -->
<div id="editModal" style="display:none; position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);">
  <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px; position:relative;">
    <h3>Edit Data</h3>
    <form id="editForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <input type="text" name="tahun" id="edit_tahun" placeholder="Tahun">
      <input type="text" name="semester" id="edit_semester" placeholder="Semester">
      <input type="text" name="kategori" id="edit_kategori" placeholder="Kategori">
      <input type="text" name="sub_kategori" id="edit_sub_kategori" placeholder="Sub-Kategori">
      <input type="text" name="label" id="edit_label" placeholder="Label">
      <input type="text" name="jumlah" id="edit_jumlah" placeholder="Jumlah">
      <button type="submit">💾 Save</button>
      <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
    </form>
  </div>
</div>

<!-- Toast Notification -->
<div id="toast"></div>

<div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>
<script src="/JavaScript/searchable_table.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/JavaScript/input_rekap_asn.js"></script>

<script>
$(document).ready(function() {
  $("#rekapForm").on("submit", function(e) {
    e.preventDefault();

    const kategori = $("#kategori").val();
    const subKategori = $("#subKategori").val();

    if (!kategori || !subKategori) {
      showToast("Kategori dan Sub-Kategori wajib diisi.", "error");
      return;
    }

    // disable submit to prevent double-click
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop("disabled", true).text("Menyimpan...");

    $.ajax({
      url: "ajax_save_rekap_asn.php",
      type: "POST",
      data: $(this).serialize() + "&kategori=" + encodeURIComponent(kategori) + "&sub_kategori=" + encodeURIComponent(subKategori),
      dataType: "text",   // <<-- important: treat response as plain text
      success: function(responseText) {
        showToast(responseText, "success");
        $("#rekapForm")[0].reset();
        $("#subOptionContainer, #inputForm").hide();
      },
      error: function(xhr, status, error) {
        // Try to show server response body if present (useful for debugging)
        const serverBody = xhr.responseText ? "\n\nServer response:\n" + xhr.responseText : "";
        showToast("Terjadi kesalahan: " + error + serverBody, "error");
      },
      complete: function() {
        $btn.prop("disabled", false).text("Tambahkan");
      }
    });
  });
});

function showToast(message, type = "success") {
  const toast = document.getElementById("toast");
  toast.innerHTML = (type === "success" ? "✅ " : "❌ ") + message;
  toast.style.background = type === "success" ? "#4CAF50" : "#E53935";
  toast.style.opacity = "1";
  toast.style.transform = "translateY(0)";

  // Hide after 3 seconds
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateY(30px)";
  }, 5000);
}
</script>

<script>
$(document).ready(function(){
  // Open Edit Modal
  $(".edit").click(function(){
    let row = $(this).closest("tr");
    let id = row.data("id");
    let tahun = row.find(".tahun").text();
    let semester = row.find(".semester").text();
    let kategori = row.find(".kategori").text();
    let sub_kategori = row.find(".sub_kategori").text();
    let label = row.find(".label").text();
    let jumlah = row.find(".jumlah").text();

    $("#edit_id").val(id);
    $("#edit_tahun").val(tahun);
    $("#edit_semester").val(semester);
    $("#edit_kategori").val(kategori);
    $("#edit_sub_kategori").val(sub_kategori);
    $("#edit_label").val(label);
    $("#edit_jumlah").val(jumlah);

    $("#editModal").show();
  });

  // Save Edit via AJAX
  $("#editForm").submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: "ajax_edit_rekap_asn.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response){
        alert(response);
        location.reload();
      }
    });
  });

  // Delete Announcement
  $(".delete").click(function(){
    if (!confirm("Apakah anda ingin menghapus data ini?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_rekap_asn.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</body>
</html>