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
    $result = $conn->query("SELECT * FROM announcements WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Delete files from server
        if ($row['thumbnail'] && file_exists("uploads/thumbnails/" . $row['thumbnail'])) {
            unlink("uploads/thumbnails/" . $row['thumbnail']);
        }
        if ($row['attachment'] && file_exists("uploads/files/" . $row['attachment'])) {
            unlink("uploads/files/" . $row['attachment']);
        }

        $conn->query("DELETE FROM announcements WHERE id=$id");
        echo "<script>alert('Pengumuman berhasil dihapus!'); window.location='dashboard_pengumuman.php';</script>";
        exit;
    }
}

$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $created_by = $_SESSION['fullname'];

    // === Upload Thumbnail (keep your resize logic) ===
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbName = uniqid("thumb_") . "." . pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $tmpPath = $_FILES['thumbnail']['tmp_name'];

        list($width, $height) = getimagesize($tmpPath);
        $newWidth = 2560;
        $newHeight = 1440;

        $src = imagecreatefromstring(file_get_contents($tmpPath));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($dst, "uploads/thumbnails/" . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($dst);

        $thumbnail = $thumbName;
    }

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
    $stmt = $conn->prepare("INSERT INTO announcements (title, content, thumbnail, attachment, created_by, created_at) VALUES (?,?,?,?,?,NOW())");
    $stmt->bind_param("sssss", $title, $content, $thumbnail, $attachment, $created_by);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Pengumuman berhasil diunggah!'); window.location='dashboard_pengumuman.php';</script>";
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
  <title>Dashboard - Announcement</title>
  <link href="dashboard_pengumuman.css" rel="stylesheet" type="text/css">
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
              <h1>Dashboard Pengumuman</h1>
            </div>
    </div>

  <div class="content-pengumuman">
        <div class="leftSide">
            <div class="form-box">
            <p>Tambah Pengumuman Baru</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Judul Pengumuman" required>
                <textarea name="content" placeholder="Konten" rows="10" required></textarea>
                
                <label>Thumbnail Image (jpg, png)</label>
                <input type="file" name="thumbnail" accept="image/*">
                
                <label>Attachment File (PDF, DOCX, etc.)</label>
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx">

                <button type="submit">Publish</button>
            </form>   
          </div>
        </div>
      
  <div class="rightSide">
    <div class="container">
        <h2>Daftar Pengumuman</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>Thumbnail</th>
            <th>Title</th>
            <th>Content</th>
            <th>File</th>
            <th>Date</th>
            <th>Dibuat oleh</th>
            <th style="width: 130px;">Actions</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
              <td><?= $row['id'] ?></td>
              <td>
                <?php if ($row['thumbnail']): ?>
                  <img src="uploads/thumbnails/<?= $row['thumbnail'] ?>" alt="Thumbnail">
                <?php else: ?>
                  <span>No Image</span>
                <?php endif; ?>
              </td>
              <td class="title"><?= htmlspecialchars($row['title']) ?></td>
              <td class="content"><?= htmlspecialchars($row['content']) ?></td>
              <td>
                <?php if ($row['attachment']): ?>
                  <a href="uploads/files/<?= $row['attachment'] ?>" target="_blank" download>📄 Unduh lampiran</a>
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
        <h3>Edit Announcement</h3>
        <form id="editForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="edit_id">

          <input type="text" name="title" id="edit_title" placeholder="Judul Pengumuman" required>
          <textarea name="content" id="edit_content" placeholder="Konten" rows="5" required></textarea>

          <label>Thumbnail Baru (opsional)</label>
          <input type="file" name="thumbnail" accept="image/*">

          <label>File Lampiran Baru (opsional)</label>
          <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx">
          <br><br>

          <button type="submit">💾 Save</button>
          <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
        </form>
      </div>
    </div>


</div><!-- CONTENT CLOSE-->

  <div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
  </div>

</body>

<script>
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
</script>

<script>
$(document).ready(function(){
  // Open Edit Modal
  $(".edit").click(function(){
    let row = $(this).closest("tr");
    let id = row.data("id");
    let title = row.find(".title").text();
    let content = row.find(".content").text();

    $("#edit_id").val(id);
    $("#edit_title").val(title);
    $("#edit_content").val(content);
    $("#editModal").show();
  });

  // Save Edit via AJAX
  $("#editForm").submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: "ajax_edit_announcement.php",
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
    if (!confirm("Apakah anda ingin menghapus pengumuman ini?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_announcement.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</html>