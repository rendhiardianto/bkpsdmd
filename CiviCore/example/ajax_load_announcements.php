<?php
include "auth.php";
requireRole('admin');

$limit = 5; // number per page
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : "";
$filter = isset($_POST['filter']) ? $conn->real_escape_string($_POST['filter']) : "";

$where = "WHERE 1";
if ($search) {
    $where .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
}
if ($filter) {
    $where .= " AND created_by='$filter'";
}

// Count total
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM announcements $where");
$total = $totalRes->fetch_assoc()['total'];
$pages = ceil($total / $limit);

// Fetch data
$result = $conn->query("SELECT * FROM announcements $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
?>

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

<div class="pagination">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <button class="page-btn" data-page="<?= $i ?>"><?= $i ?></button>
  <?php endfor; ?>
</div>
