<?php
require_once __DIR__ . '/../../db.php';

$result = $conn->query("SELECT * FROM wa_notifications ORDER BY created_at DESC");
?>
<table border="1" cellpadding="8">
  <tr><th>ID</th><th>Phone</th><th>Message</th><th>Status</th><th>Response</th><th>Time</th></tr>
  <?php while($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id']; ?></td>
    <td><?= htmlspecialchars($row['phone']); ?></td>
    <td><?= htmlspecialchars($row['message']); ?></td>
    <td><?= $row['status']; ?></td>
    <td><?= htmlspecialchars(substr($row['response'], 0, 80)); ?>...</td>
    <td><?= $row['created_at']; ?></td>
  </tr>
  <?php endwhile; ?>
</table>
