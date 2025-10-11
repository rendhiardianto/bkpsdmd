<?php
include "../db.php";
include "../auth.php";

requireRole(['admin', 'user']);

// Read role and back URL
$role = $_GET['role'] ?? $_SESSION['role'];
$fromPage = $_GET['from'] ?? null;
$backLinks = [
    'admin' => '../dashboard_super_admin.php',
    'user'  => '../dashboard_cms_admin.php',
];
$backUrl = $backLinks[$role];

// --- Delete record ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT * FROM jf_bkn WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Delete image file
        if ($row['image_path'] && file_exists("uploads/detail_image/" . $row['image_path'])) {
            unlink("uploads/detail_image/" . $row['image_path']);
        }

        $conn->query("DELETE FROM jf_bkn WHERE id=$id");
        echo "<script>alert('Jabatan Fungsional sudah dihapus!'); window.location='dashboard_jf.php';</script>";
        exit;
    }
}

$result = $conn->query("SELECT * FROM jf_bkn ORDER BY id ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    $rumpun = $conn->real_escape_string($_POST['rumpun']);
    $rekom_ip = $conn->real_escape_string($_POST['rekom_ip']);
    $penetapan_menpan = $conn->real_escape_string($_POST['penetapan_menpan']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $lingkup = $conn->real_escape_string($_POST['lingkup']);
    $pembina = $conn->real_escape_string($_POST['pembina']);
    $created_by = $_SESSION['fullname'];

    // Check duplicate
    $cek = mysqli_query($conn, "SELECT * FROM jf_bkn WHERE jabatan='$jabatan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script> alert('Jabatan Fungsional tersebut sudah terdaftar di database.');
            window.location.href='dashboard_jf.php';</script>";
        exit;
    }

    // --- Upload & compress image ---
    $image_path = null;
    if (!empty($_FILES['image_path']['name'])) {
        $safeJabatan = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtolower($jabatan));
        $ext = strtolower(pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION));
        $imageName = $safeJabatan . "." . $ext;

        $targetDir = "uploads/detail_image/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . $imageName;
        $i = 1;
        while (file_exists($targetPath)) {
            $imageName = $safeJabatan . "_" . $i . "." . $ext;
            $targetPath = $targetDir . $imageName;
            $i++;
        }

        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetPath)) {
            compressImage($targetPath, $targetPath, 70); // compress after upload
            $image_path = $imageName; // ✅ assign filename for DB
        } else {
            echo "<script>alert('Gagal mengunggah gambar. Pastikan file tidak rusak atau terlalu besar.');</script>";
        }
    }

    // --- Insert into DB ---
    $stmt = $conn->prepare("INSERT INTO jf_bkn (jabatan, rumpun, rekom_ip, penetapan_menpan, kategori, lingkup, pembina, image_path, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssssss", $jabatan, $rumpun, $rekom_ip, $penetapan_menpan, $kategori, $lingkup, $pembina, $image_path);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Jabatan Fungsional berhasil diunggah!'); window.location='dashboard_jf.php';</script>";
}

/**
 * Compress image to reduce size
 */
function compressImage($source, $destination, $quality = 80)
{
    $info = getimagesize($source);
    if (!$info) return;

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            $pngQuality = 9 - round(($quality / 100) * 9);
            imagepng($image, $destination, $pngQuality);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            imagewebp($image, $destination, $quality);
            break;
        default:
            return; // skip unknown formats
    }

    if (isset($image)) {
        imagedestroy($image);
    }
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
  <title>Dashboard - Jabatan Fungsional</title>
  <link href="dashboard_jf.css" rel="stylesheet" type="text/css">
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
              <h1>Dashboard Jabatan Fungsional</h1>
            </div>
  </div>

  <div class="content-pengumuman">
        <div class="leftSide">
            <div class="form-box">
            <p>Tambah Jabatan Fungsional Baru</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="jabatan" placeholder="Nama Jabatan Fungsional">
                <input type="text" name="rumpun" placeholder="Rumpun Jabatan">
                <input type="text" name="rekom_ip" placeholder="Rekomendasi Instansi Pembina">
                <input type="text" name="penetapan_menpan" placeholder="Penetapan Menpan-RB">
                <input type="text" name="kategori" placeholder="Kategori">
                <input type="text" name="lingkup" placeholder="Lingkup">
                <input type="text" name="pembina" placeholder="Instansi Pembina">
                
                <label>Image Detail JF (jpg, png)</label>
                <input type="file" name="image_path" accept="image/*">

                <button type="submit">Tambahkan</button>
            </form>   
          </div>
        </div>
      
  <div class="rightSide">
      <h2 style="text-align: center;">Daftar Jabatan Fungsional</h2>

      <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cari nama jabatan atau instansi pembina" title="Type in a name">

      <div style="height:1000px; border:1px solid #ccc; margin:auto;">  
        <table id="userTable1" border="1" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
          <tr>
            <th>Nomor</th>
            <th style="width: 30%;">Jabatan Fungsional</th>
            <th>Rumpun Jabatan</th>
            <th>Rekomendasi Instansi Pembina</th>
            <th>Penetapan Menpan-RB</th>
            <th>Kategori</th>
            <th>Ruang Lingkup</th>
            <th>Instansi Pembina</th>
            <th>Informasi Detail</th>
            <th>Actions</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
              <td style="text-align:center;"><?php echo $row['id']; ?></td>
              <td><?php echo $row['jabatan']; ?></td>
              <td><?php echo $row['rumpun']; ?></td>
              <td style="text-align:center;">

              <?php if (!empty($row['rekom_ip'])): ?>
                <a href="<?php echo $row['rekom_ip']; ?>" target="_blank">
                  <button class="lihatBtn">Lihat</button> 
                </a>
                <?php endif; ?>
              </td>

              <td style="text-align:center;">
                <?php if (!empty($row['penetapan_menpan'])): ?>
                <a href="<?php echo $row['penetapan_menpan']; ?>" target="_blank">
                  <button class="lihatBtn">Lihat</button> 
                </a>
                <?php endif; ?>
              </td>

              <td><?php echo $row['kategori']; ?></td>
              <td><?php echo $row['lingkup']; ?></td>
              <td><?php echo $row['pembina']; ?></td>

              <td>
                <?php if ($row['image_path']): ?>
                  <img src="uploads/detail_image/<?= $row['image_path'] ?>" alt="Image">
                <?php else: ?>
                  <span>No Detail Added</span>
                <?php endif; ?>

              </td>
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
        <h3>Edit Jabatan Fungsional</h3>
        <form id="editForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="edit_id">

          <input type="text" name="jabatan" id="edit_title" placeholder="Nama Jabatan Fungsional">
          <input type="text" name="rumpun" id="edit_rumpun" placeholder="Rumpun Jabatan">
          <input type="text" name="rekom_ip" id="edit_rekom_ip" placeholder="Rekomendasi Instansi Pembina">
          <input type="text" name="penetapan_menpan" id="edit_penetapan_menpan" placeholder="Penetapan Menpan-RB">
          <input type="text" name="kategori" id="edit_kategori" placeholder="Kategori">
          <input type="text" name="lingkup" id="edit_lingkup" placeholder="Lingkup">
          <input type="text" name="pembina" id="edit_pembina" placeholder="Instansi Pembina">

          <label>Image Detail JF (.jpg, .png)</label>
          <input type="file" name="image_path" accept="image/*">
          <br><br>

          <button type="submit">💾 Save</button>
          <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
        </form>
      </div>
    </div>


</div><!-- CONTENT CLOSE-->

  <div class="footer">
    <p>Copyright &copy; 2025. Tim PUSDATIN - BKPSDMD Kabupaten Merangin.</p>
  </div>

</body>

<script src="/JavaScript/searchable_table.js"></script>

<script>
  function loadPublicJF(page=1) {
    let search = $("#search").val();
    let filter = $("#filter").val();
    $.post("ajax_load_public_jf.php", { page: page, search: search, filter: filter }, function(data){
      $("#jfList").html(data);
    });
  }

  $("#search, #filter").on("input change", function(){
    loadPublicJF(1);
  });

  $(document).on("click", ".page-btn", function(){
    let page = $(this).data("page");
    loadPublicJF(page);
  });

  $(document).ready(function(){ loadPublicJF(); });
</script>

<script>
$(document).ready(function(){
  // Open Edit Modal
  $(".edit").click(function(){
    let row = $(this).closest("tr");

    let id = row.data("id");
    let jabatan = row.find(".jabatan").text();
    let rumpun = row.find(".rumpun").text();
    let rekom_ip = row.find(".rekom_ip").text();
    let penetapan_menpan = row.find(".penetapan_menpan").text();
    let kategori = row.find(".kategori").text();
    let lingkup = row.find(".lingkup").text();
    let image_path = row.find(".image_path").text();

    $("#edit_id").val(id);
    $("#edit_jabatan").val(jabatan);
    $("#edit_rumpun").val(rumpun);
    $("#edit_rekom_ip").val(rekom_ip);
    $("#edit_penetapan_menpan").val(penetapan_menpan);
    $("#edit_kategori").val(kategori);
    $("#edit_lingkup").val(lingkup);
    $("#edit_image_path").val(image_path);

    $("#editModal").show();
  });

  // Save Edit via AJAX
  $("#editForm").submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: "ajax_edit_jf.php",
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
    if (!confirm("Apakah anda ingin menghapus Jabatan Fungsional ini?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_jf.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</html>