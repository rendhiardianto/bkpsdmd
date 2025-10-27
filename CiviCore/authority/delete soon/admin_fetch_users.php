<?php
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    exit("Unauthorized");
}

// --- Pagination setup ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- Search & Filter ---
$where = "1=1";

if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (fullname LIKE '%$search%' OR email LIKE '%$search%')";
}

if (!empty($_GET['role_filter'])) {
    $role = $conn->real_escape_string($_GET['role_filter']);
    $where .= " AND role='$role'";
}

// --- Count total users ---
$countResult = $conn->query("SELECT COUNT(*) AS total FROM users WHERE $where");
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// --- Fetch users ---
$result = $conn->query("SELECT id, fullname, email, role, created_at 
                        FROM users 
                        WHERE $where 
                        ORDER BY id DESC 
                        LIMIT $offset, $limit");
?>

<table border="1" width="100%" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
  <tr style="background:#3498db; color:white;">
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Created</th>
    <th>Action</th>
  </tr>
  <?php while($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['fullname']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo ucfirst($row['role']); ?></td>
    <td><?php echo $row['created_at']; ?></td>
    <td>
  <button class="edit-btn" data-id="<?php echo $row['id']; ?>" style="background:#27ae60;color:white;padding:6px 10px;border-radius:5px;border:none;cursor:pointer;">Edit</button>
  <button class="delete-btn" data-id="<?php echo $row['id']; ?>" style="background:#e74c3c;color:white;padding:6px 10px;border-radius:5px;border:none;cursor:pointer;">Delete</button>
</td>

  </tr>
  <?php endwhile; ?>
</table>

<!-- Pagination -->
<div style="text-align:center;margin-top:15px;">
  <?php if ($page > 1): ?>
    <a href="#" class="pagination-link" data-page="<?php echo $page-1; ?>" style="margin:0 5px;">⬅ Prev</a>
  <?php endif; ?>

  <?php for ($i=1; $i <= $totalPages; $i++): ?>
    <a href="#" class="pagination-link <?php echo ($i==$page) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>" style="margin:0 5px;<?php echo ($i==$page) ? 'font-weight:bold;color:#3498db;' : ''; ?>">
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="#" class="pagination-link" data-page="<?php echo $page+1; ?>" style="margin:0 5px;">Next ➡</a>
  <?php endif; ?>
</div>
