<?php
include "db.php";
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Announcements</title>
  <style>
    body { font-family: Arial; background:#fafafa; padding:30px; }
    .controls { margin-bottom:20px; }
    input, select { padding:8px; margin-right:10px; border-radius:6px; border:1px solid #ccc; }
    .announcement { background:#fff; padding:20px; margin-bottom:15px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    .pagination { margin-top:20px; text-align:center; }
    .pagination button { margin:0 5px; padding:6px 12px; border:none; border-radius:6px; background:#2c3e50; color:#fff; cursor:pointer; }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h1>📢 Announcements</h1>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
      
      <?php if ($row['thumbnail']): ?>
        <img src="uploads/thumbnails/<?= $row['thumbnail'] ?>" alt="Thumbnail" style="width:600px; height:auto; border-radius:8px;">
      <?php endif; ?>
      
      <h2><?= htmlspecialchars($row['title']) ?></h2>
      <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
     
      <?php if ($row['attachment']): ?>
        <br><a class="download" href="uploads/files/<?= $row['attachment'] ?>" download>📄 Download File</a>
      <?php endif; ?>

      <small>Posted on <?= $row['created_at'] ?></small>
    </div>
  <?php endwhile; ?>

  <div class="controls">
    <input type="text" id="search" placeholder="🔍 Search announcements">
    <select id="filter">
      <option value="">-- All Creators --</option>
      <?php
        $creators = $conn->query("SELECT DISTINCT created_by FROM announcements");
        while($c = $creators->fetch_assoc()):
      ?>
        <option value="<?= htmlspecialchars($c['created_by']) ?>"><?= htmlspecialchars($c['created_by']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <div id="announcementList"></div>

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

</body>
</html>
