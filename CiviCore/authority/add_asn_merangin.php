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
    $result = $conn->query("SELECT * FROM asn_merangin WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $conn->query("DELETE FROM asn_merangin WHERE id=$id");
        echo "<script>alert('Data sudah dihapus!'); window.location='add_asn_merangin.php';</script>";
        exit;
    }
}
$result = $conn->query("SELECT * FROM asn_merangin ORDER BY id ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize input
    $nip                = $conn->real_escape_string($_POST['nip']);
    $fullname           = $conn->real_escape_string($_POST['fullname']);
    $gelar_depan        = $conn->real_escape_string($_POST['gelar_depan']);
    $gelar_belakang     = $conn->real_escape_string($_POST['gelar_belakang']);
    $tempat_lahir       = $conn->real_escape_string($_POST['tempat_lahir']);
    $tanggal_lahir      = $conn->real_escape_string($_POST['tanggal_lahir']);
    $jenis_kelamin      = $conn->real_escape_string($_POST['jenis_kelamin']);
    $agama              = $conn->real_escape_string($_POST['agama']);
    $phone              = $conn->real_escape_string($_POST['phone']);
    $status_pegawai     = $conn->real_escape_string($_POST['status_pegawai']);
    $gol_awal           = $conn->real_escape_string($_POST['gol_awal']);
    $gol_saat_ini       = $conn->real_escape_string($_POST['gol_saat_ini']);
    $tmt_gol            = $conn->real_escape_string($_POST['tmt_gol']);
    $masa_kerja_tahun   = (int) $_POST['masa_kerja_tahun'];
    $masa_kerja_bulan   = (int) $_POST['masa_kerja_bulan'];
    $jenjang_jabatan    = $conn->real_escape_string($_POST['jenjang_jabatan']);
    $jenis_jabatan      = $conn->real_escape_string($_POST['jenis_jabatan']);
    $jabatan            = $conn->real_escape_string($_POST['jabatan']);
    $tmt_jabatan        = $conn->real_escape_string($_POST['tmt_jabatan']);
    $tingkat_pendidikan = $conn->real_escape_string($_POST['tingkat_pendidikan']);
    $pendidikan         = $conn->real_escape_string($_POST['pendidikan']);
    $tahun_lulus        = $conn->real_escape_string($_POST['tahun_lulus']);
    $lokasi_kerja       = $conn->real_escape_string($_POST['lokasi_kerja']);
    $organisasi         = $conn->real_escape_string($_POST['organisasi']);
    $organisasi_induk   = $conn->real_escape_string($_POST['organisasi_induk']);
    $instansi_induk     = $conn->real_escape_string($_POST['instansi_induk']);

    // Prepare statement
    $stmt = $conn->prepare("
        INSERT INTO asn_merangin (
            nip, fullname, gelar_depan, gelar_belakang, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, phone,
            status_pegawai, gol_awal, gol_saat_ini, tmt_gol, masa_kerja_tahun, masa_kerja_bulan,
            jenjang_jabatan, jenis_jabatan, jabatan, tmt_jabatan,
            tingkat_pendidikan, pendidikan, tahun_lulus, lokasi_kerja,
            organisasi, organisasi_induk, instansi_induk, created_at
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
    ");

    $stmt->bind_param(
        "ssssssssssssiiissssssssssss",
        $nip, $fullname, $gelar_depan, $gelar_belakang, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $agama, $phone,
        $status_pegawai, $gol_awal, $gol_saat_ini, $tmt_gol, $masa_kerja_tahun, $masa_kerja_bulan,
        $jenjang_jabatan, $jenis_jabatan, $jabatan, $tmt_jabatan,
        $tingkat_pendidikan, $pendidikan, $tahun_lulus, $lokasi_kerja,
        $organisasi, $organisasi_induk, $instansi_induk
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data ASN berhasil disimpan!'); window.location='add_asn_merangin.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
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
  <title>Dashboard - Update Data ASN</title>

  <link href="add_asn_merangin.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">
</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Dashboard Update Data ASN Merangin</h1>
  </div>
</div>

<div class="flex-container">
  <div class="form-box">
    <h2>Tambahkan Data Baru</h2>

    <form id="rekapForm" method="POST" enctype="multipart/form-data">

      <label>Nomor Induk Pegawai</label>
      <input type="text" name="nip" placeholder="Masukkan NIP" required>

      <label>Nama Lengkap</label>
      <input type="text" name="fullname" placeholder="Masukkan Nama Lengkap" required>

      <label>Gelar Depan</label>
      <input type="text" name="gelar_depan" placeholder="Masukkan Gelar Depan">

      <label>Gelar Belakang</label>
      <input type="text" name="gelar_belakang" placeholder="Masukkan Gelar Belakang" required>

      <label>Tempat Lahir</label>
      <input type="text" name="tempat_lahir" placeholder="" required>

      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" placeholder="" required>

      <label>Jenis Kelamin</label>
      <select name="jenis_kelamin" required>
        <option value="">(Pilih Jenis Kelamin)</option>
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
      </select>

      <label>Agama</label>
      <select name="agama" required>
        <option value="">(Pilih Agama)</option>
        <option value="Islam">Islam</option>
        <option value="Kristen">Kristen</option>
        <option value="Katholik">Katholik</option>
        <option value="Hindu">Hindu</option>
        <option value="Buddha">Buddha</option>
        <option value="Konghucu">Konghucu</option>
      </select>

      <label>Nomor HP</label>
      <input type="text" name="phone" placeholder="" required>

      <label>Status Pegawai</label>
      <select name="status_pegawai" required>
        <option value="">(Pilih Status Pegawai)</option>
        <option value="PNS">CPNS</option>
        <option value="PNS">PNS</option>
        <option value="PPPK">PPPK</option>
      </select>

      <label>Gol Awal</label>
      <input type="text" name="gol_awal" placeholder="" required>

      <label>Gol Saat Ini</label>
      <input type="text" name="gol_saat_ini" placeholder="" required>

      <label>TMT Golongan</label>
      <input type="text" name="tmt_gol" placeholder="" required>

      <label>Masa Kerja Tahun</label>
      <input type="text" name="masa_kerja_tahun" placeholder="" required>

      <label>Masa Kerja Bulan</label>
      <input type="text" name="masa_kerja_bulan" placeholder="" required>

      <label>Jenjang Jabatan</label>
      <select name="jenjang_jabatan" required>
        <option value="">(Pilih Jenjang Jabatan)</option>
        <option value="Funsgional">Fungsional</option>
        <option value="Pelaksana">Pelaksana</option>
        <option value="Pengawas">Pengawas</option> 
        <option value="Administrator">Administrator</option>
        <option value="Jabatan Pimpinan Tinggi">Jabatan Pimpinan Tinggi</option> 
      </select>

      <label>Jenis Jabatan</label>
      <select name="jenis_jabatan" required>
        <option value="">(Pilih Jenis Jabatan)</option>
        <option value="Jabatan Fungsional">Jabatan Fungsional</option>
        <option value="Jabatan Pelaksana">Jabatan Pelaksana</option> 
        <option value="Jabatan Struktural">Jabatan Struktural</option>
      </select>           

      <label>Jabatan</label>
      <input type="text" name="jabatan" placeholder="" required>

      <label>TMT Jabatan</label>
      <input type="date" name="tmt_jabatan" placeholder="" required>  
      
      <Label>Tingkat Pendidikan</Label>
      <input type="text" name="tingkat_pendidikan" placeholder="" required>

      <label>Pendidikan</label>
      <input type="text" name="pendidikan" placeholder="" required>

      <label>Tahun Lulus</label>
      <input type="text" name="tahun_lulus" placeholder="" required>
      
      <label>Lokasi Kerja</label>
      <input type="text" name="lokasi_kerja" placeholder="" required>

      <label>Organisasi</label>
      <input type="text" name="organisasi" placeholder="" required>

      <label>Organisasi Induk</label>
      <input type="text" name="organisasi_induk" placeholder="" required>

      <label>Instansi Induk</label>
      <input type="text" name="instansi_induk" placeholder="" required>

      <label for="kategori">Status Pegawai</label>
      <select id="kategori" onchange="updateSubOptions()">
        <option value="">(Pilih Status Pegawai)</option>
        <option value="PNS">PNS</option>
        <option value="PPPK">PPPK</option>
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
    <h2 style="text-align: center;">Data ASN Pemkab Merangin</h2>
    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cari nama atau jabatan" title="Type in a name">
    
    <div style="height:500px; border:1px solid #ccc; margin:auto; overflow:auto;">
      <table id="userTable1">
        <tr>
          <th>No</th>
          <th>NIP</th>
          <th>Nama Lengkap</th>
          <th>Status Pegawai</th>
          <th>Jabatan</th>
          <th>Organisasi</th>
          <th>TTL</th>
          <th>Nomor HP</th>
          <th>Aksi</th>
        </tr>
        <?php $no=1; while ($row = $result->fetch_assoc()): ?>
        <tr data-id="<?= $row['id'] ?>">
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nip']) ?></td>
          <td><?= htmlspecialchars($row['fullname']) ?></td>
          <td><?= htmlspecialchars($row['status_pegawai']) ?></td>
          <td><?= htmlspecialchars($row['jabatan']) ?></td>
          <td><?= htmlspecialchars($row['organisasi']) ?></td>
          <td><?= htmlspecialchars($row['tempat_lahir']) . ', ' . htmlspecialchars($row['tanggal_lahir']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td>
            <button class="edit">✏️</button>
            <button class="delete">🗑</button>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);">
      <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px;">
        <h3>Edit Data ASN</h3>
        <form id="editForm">
          <input type="hidden" name="id" id="edit_id">
          <label>NIP:</label><br>
          <input type="text" name="nip" id="edit_nip"><br>
          <label>Nama Lengkap:</label><br>
          <input type="text" name="fullname" id="edit_fullname"><br>
          <label>Jabatan:</label><br>
          <input type="text" name="jabatan" id="edit_jabatan"><br>
          <label>Nomor HP:</label><br>
          <input type="text" name="phone" id="edit_phone"><br>
          
          <button type="submit">💾 Save</button>
          <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
        </form>
      </div>
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
      url: "ajax_save_asn_merangin.php",
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
    let nip = row.find(".nip").text().trim();
    let fullname = row.find(".fullname").text().trim();
    let jabatan = row.find(".jabatan").text().trim();
    let phone = row.find(".phone").text().trim();

    // Fill modal fields
    $("#edit_id").val(id);
    $("#edit_nip").val(nip);
    $("#edit_fullname").val(fullname);
    $("#edit_jabatan").val(jabatan);
    $("#edit_phone").val(phone);

    // Show modal
    $("#editModal").fadeIn();
  });

  // Close modal when clicking outside
  $(window).on("click", function(e){
    if ($(e.target).is("#editModal")) {
      $("#editModal").fadeOut();
    }
  });

  // Save Edit via AJAX
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
        location.reload();
      }
    });
  });

  // Delete Announcement
  $(".delete").click(function(){
    if (!confirm("Apakah anda ingin menghapus data ini?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_asn_merangin.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</body>
</html>