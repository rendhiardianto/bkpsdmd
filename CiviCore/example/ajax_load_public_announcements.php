<?php
include "db.php";

$limit = 5;
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

$totalRes = $conn->query("SELECT COUNT(*) AS total FROM announcements $where");
$total = $totalRes->fetch_assoc()['total'];
$pages = ceil($total / $limit);

$result = $conn->query("SELECT * FROM announcements $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

while ($row = $result->fetch_assoc()):
?>
  <div class="announcement">
    <h2><?= htmlspecialchars($row['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
    <small>👤 <?= htmlspecialchars($row['created_by']) ?> | 📅 <?= $row['created_at'] ?></small>
  </div>
<?php endwhile; ?>

<div class="pagination">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <button class="page-btn" data-page="<?= $i ?>"><?= $i ?></button>
  <?php endfor; ?>
</div>
