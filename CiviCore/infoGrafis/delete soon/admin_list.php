<?php
include "../db.php";

requireRole(['admin', 'user']);

$result = $conn->query("SELECT * FROM infografis ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery Management</title>
  <style>
    body { font-family: Arial, sans-serif; margin:20px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ccc; padding:10px; text-align:left; }
    th { background:#003366; color:white; }
    img { width:100px; border-radius:4px; }
    a.button { padding:6px 10px; background:#cc0000; color:white; text-decoration:none; border-radius:4px; }
  </style>
</head>
<body>

<h2>Gallery Items</h2>
<a href="admin_infoGrafis.php">+ Add New</a>
<table>
  <tr>
    <th>ID</th>
    <th>Image</th>
    <th>Caption</th>
    <th>Created</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?php echo $row['id']; ?></td>
    <td><img src="<?php echo $row['images']; ?>"></td>
    <td><?php echo $row['caption']; ?></td>
    <td><?php echo $row['created_at']; ?></td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
