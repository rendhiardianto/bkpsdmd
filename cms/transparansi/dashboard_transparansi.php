<?php
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
    'admin'   => '../dashboard_cms_admin.php',
];
$backUrl = $backLinks[$role];

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT * FROM transparansi WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Delete files from server
        if ($row['attachment'] && file_exists("uploads/files/" . $row['attachment'])) {
            unlink("uploads/files/" . $row['attachment']);
        }

        $conn->query("DELETE FROM transparansi WHERE id=$id");
        echo "<script>alert('Dokumen sudah dihapus!'); window.location='dashboard_transparansi.php';</script>";
        exit;
    }
}

$result = $conn->query("SELECT * FROM transparansi ORDER BY created_at DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipe_dokumen = $conn->real_escape_string($_POST['tipe_dokumen']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $nomor = $conn->real_escape_string($_POST['nomor']);
    $tahun = $conn->real_escape_string($_POST['tahun']);
    $created_by = $_SESSION['fullname'];  

    // === Upload Attachment (keep original name) ===
    $attachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        // Original name
        $originalName = basename($_FILES['attachment']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);

        $uploadDir = __DIR__ . "/uploads/files/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Prevent overwrite: add _1, _2, etc.
        $targetFile = $uploadDir . $originalName;
        $counter = 1;
        while (file_exists($targetFile)) {
            $newName = $nameOnly . "_" . $counter . "." . $ext;
            $targetFile = $uploadDir . $newName;
            $counter++;
        }

        $attachment = basename($targetFile); // store only file name

        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            echo "<script>alert('Gagal mengunggah lampiran.');</script>";
            $attachment = null;
        }
    }

    // === Save to DB ===
    $stmt = $conn->prepare("INSERT INTO transparansi (tipe_dokumen, judul, nomor, tahun, attachment, created_by, created_at) VALUES (?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssss", $tipe_dokumen, $judul, $nomor, $tahun, $attachment, $created_by);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Dokumen berhasil diunggah!'); window.location='dashboard_transparansi.php';</script>";
}
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
  <title>Dashboard - Transparansi</title>
  <link href="dashboard_transparansi.css" rel="stylesheet" type="text/css">
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="images/button/logo2.png">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <div class="header">
            <div class="navbar">
            	<a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
            </div>
            <div class="roleHeader">
              <h1>Dashboard Transparansi</h1>
            </div>
    </div>

  <div class="content-pengumuman">
        <div class="leftSide">
            <div class="form-box">
            <p>Tambah Dokumen Baru</p>

            <form method="POST" enctype="multipart/form-data">

                <label>Tipe Dokumen</label>
                <select name="tipe_dokumen" required>
                    <option value="Peraturan Bupati">Peraturan Bupati</option>
                    <option value="Rencana Strategis">Rencana Strategis</option>
                    <option value="Rencana Kerja">Rencana Kerja</option>
                    <option value="Indikator Kinerja Utama">Indikator Kinerja Utama</option>
                    <option value="Casscading">Casscading</option>
                    <option value="Perjanjian Kinerja">Perjanjian Kinerja</option>
                    <option value="Rencana Aksi">Rencana Aksi</option>
                    <option value="Laporan Kinerja">Laporan Kinerja</option>
                    <option value="Standar Operasional Prosedur">Standar Operasional Prosedur</option>
                    <option value="RAPBD">RAPBD</option>
                    <option value="APBD">APBD</option>
                    <option value="LPPD">LPPD</option>
                </select>

                <label>Judul Dokumen</label>
                <input type="text" name="judul" placeholder="Judul Dokumen" required>

                <label>No Dokumen</label>
                <input type="text" name="nomor" placeholder="Nomor Dokumen" required>

                <label>Tahun Dokumen</label>
                <input type="text" name="tahun" placeholder="Tahun Dokumen" required>

                <label>File Dokumen (.pdf)</label>
                <input type="file" name="attachment" accept=".pdf" required>

                <button type="submit">Publish</button>
            </form>   
          </div>
        </div>
      
  <div class="rightSide">
    <div class="container">
        <h2>Daftar Dokumen Transparansi</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>Tipe Dokumen</th>
            <th>Judul Dokumen</th>
            <th>Nomor Dokumen</th>
            <th>Tahun Dokumen</th>
            <th>File Dokumen</th>
            <th>Diunggah pada</th>
            <th>Diunggah oleh</th>
            <th style="width: 130px;">Actions</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">

              <td><?= $row['id'] ?></td>

              <td class="tipe_dokumen"><?= htmlspecialchars($row['tipe_dokumen']) ?></td>

              <td class="judul"><?= htmlspecialchars($row['judul']) ?></td>
              <td class="nomor"><?= htmlspecialchars($row['nomor']) ?></td>
              <td class="tahun"><?= htmlspecialchars($row['tahun']) ?></td>

              <td>
                <?php if ($row['attachment']): ?>
                  <a href="uploads/files/<?= $row['attachment'] ?>" target="_blank" download>📄 Unduh Dokumen</a>
                <?php else: ?>
                  <span>No File</span>
                <?php endif; ?>
              </td>

              <td><?= $row['created_at'] ?></td>

              <td><?= htmlspecialchars($row['created_by']) ?></td>

              <td>
                <button class="edit">✏️ Edit</button>
                <button class="delete">🗑 Delete</button>
              </td>
            </tr>
            <?php endwhile; ?>

        </table>
    </div>
  </div>

    <!-- Modal for Edit -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);">
      <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px; position:relative;">
        <h3>Edit Dokumen</h3>
        <form id="editForm" enctype="multipart/form-data">

          <input type="hidden" name="id" id="edit_id">

          <label>Tipe Dokumen</label>
                <select name="tipe_dokumen">
                    <option value="Peraturan Bupati">Peraturan Bupati</option>
                    <option value="Rencana Strategis">Rencana Strategis</option>
                    <option value="Rencana Kerja">Rencana Kerja</option>
                    <option value="Indikator Kinerja Utama">Indikator Kinerja Utama</option>
                    <option value="Casscading">Casscading</option>
                    <option value="Perjanjian Kinerja">Perjanjian Kinerja</option>
                    <option value="Rencana Aksi">Rencana Aksi</option>
                    <option value="Laporan Kinerja">Laporan Kinerja</option>
                    <option value="Standar Operasional Prosedur">Standar Operasional Prosedur</option>
                    <option value="RAPBD">RAPBD</option>
                    <option value="APBD">APBD</option>
                    <option value="LPPD">LPPD</option>
                </select>

          <label>Ubah Judul Dokumen</label>
          <input type="text" name="judul" placeholder="Judul Dokumen">
          
          <label>Ubah Nomor Dokumen</label>
          <input type="text" name="nomor" placeholder="Nomor Dokumen">
          
          <label>Ubah Tahun Dokumen</label>
          <input type="text" name="tahun" placeholder="Tahun Dokumen" >
          
          <label>File Dokumen Baru (.pdf)</label>
          <input type="file" name="attachment" accept=".pdf">
          <br>
          <button type="submit">💾 Simpan</button>
          <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
        </form>
      </div>
    </div>

</div><!-- CONTENT CLOSE-->

  <div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
  </div>

</body>

<!--<script>
  function loadPublicAnnouncements(page=1) {
    let search = $("#search").val();
    let filter = $("#filter").val();
    $.post("ajax_load_public_announcements.php", { page: page, search: search, filter: filter }, function(data){
      $("#announcementList").html(data);
    });
  }

  $("#search, #filter").on("input change", function(){
    loadPublicAnnouncements(1);
  });

  $(document).on("click", ".page-btn", function(){
    let page = $(this).data("page");
    loadPublicAnnouncements(page);
  });

  $(document).ready(function(){ loadPublicAnnouncements(); });
</script>-->

<script>
$(document).ready(function(){
  // Open Edit Modal
  $(".edit").click(function(){
    let row = $(this).closest("tr");
    let id = row.data("id");
    let tipe_dokumen = row.find(".tipe_dokumen").text();
    let judul = row.find(".judul").text();
    let nomor = row.find(".nomor").text();
    let tahun = row.find(".tahun").text();

    $("#edit_id").val(id);
    $("#edit_tipe_dokumen").val(tipe_dokumen);
    $("#edit_judul").val(judul);
    $("#edit_nomor").val(nomor);
    $("#edit_tahun").val(tahun);
    $("#editModal").show();
  });

  // Save Edit via AJAX
  $("#editForm").submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: "ajax_edit_transparansi.php",
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



  // Delete Dokumen
  $(".delete").click(function(){
    if (!confirm("Apakah Anda yakin ingin menghapus dokumen ini?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_transparansi.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</html>