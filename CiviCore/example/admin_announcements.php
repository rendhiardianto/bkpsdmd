<?php
include "auth.php";
requireRole('admin');

// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Announcements</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
    th, td { padding:12px; border:1px solid #ddd; text-align:left; }
    th { background:#2c3e50; color:#fff; }
    button { padding:6px 12px; border:none; border-radius:6px; cursor:pointer; }
    .edit { background:#3498db; color:#fff; }
    .delete { background:#e74c3c; color:#fff; }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h1>📢 Manage Announcements</h1>
  <p><a href="admin_announcements_add.php">➕ Add Announcement</a></p>

  <table>
    <tr>
      <th>Title</th>
      <th>Content</th>
      <th>Created By</th>
      <th>Created At</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr data-id="<?= $row['id'] ?>">
        <td class="title"><?= htmlspecialchars($row['title']) ?></td>
        <td class="content"><?= htmlspecialchars($row['content']) ?></td>
        <td><?= htmlspecialchars($row['created_by']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <button class="edit">✏️ Edit</button>
          <button class="delete">🗑 Delete</button>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <!-- Modal for Edit -->
  <div id="editModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);">
    <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:12px; position:relative;">
      <h3>Edit Announcement</h3>
      <form id="editForm">
        <input type="hidden" name="id" id="edit_id">
        <input type="text" name="title" id="edit_title" required>
        <textarea name="content" id="edit_content" rows="5" required></textarea>
        <br>
        <button type="submit">💾 Save</button>
        <button type="button" onclick="$('#editModal').hide()">❌ Cancel</button>
      </form>
    </div>
  </div>

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
    $.post("ajax_edit_announcement.php", $(this).serialize(), function(response){
      alert(response);
      location.reload();
    });
  });

  // Delete Announcement
  $(".delete").click(function(){
    if (!confirm("Are you sure you want to delete this announcement?")) return;
    let id = $(this).closest("tr").data("id");

    $.post("ajax_delete_announcement.php", { id: id }, function(response){
      alert(response);
      location.reload();
    });
  });
});
</script>

</body>
</html>
