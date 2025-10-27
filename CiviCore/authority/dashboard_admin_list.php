<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include "../db.php";
include "../auth.php";

requireRole('super_admin');

// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'super_admin'  => '../dashboard_super_admin.php',
    'admin'   => '../dashboard_admin.php',
];
$backUrl = $backLinks[$role];

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
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
    $where .= " AND (fullname LIKE '%$search%' OR nip LIKE '%$search%')";
}

if (!empty($_GET['role_filter'])) {
    $role = $conn->real_escape_string($_GET['role_filter']);
    $where .= " AND role='$role'";
}

// --- Count total users ---
$countResult = $conn->query("SELECT COUNT(*) AS total FROM users WHERE $where");
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// --- Fetch users for the table ---
$result = $conn->query("SELECT id, fullname, nip, email, role, created_at 
                        FROM users 
                        WHERE $where 
                        ORDER BY id DESC 
                        LIMIT $offset, $limit");

// --- Fetch current logged-in user profile ---
$userId = $_SESSION['user_id'];
$profileResult = $conn->query("SELECT fullname, profile_pic FROM users WHERE id=$userId");
$user = $profileResult->fetch_assoc();

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
  <title>Dashboard - CiviCore Users </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="dashboard_admin_list.css" rel="stylesheet" type="text/css">
</head>

<body>
  <div class="header">
    <div class="navbar">
      <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard CiviCore User</h1>
    </div>
  </div>

  <div class="top-bar">
    <form id="filterForm">
      <input type="text" name="search" id="search" placeholder="Search by name/nip/email">
      <select name="role_filter" id="role_filter">
        <option value="">All Roles</option>
        <option value="super_admin">Super Admin</option>
        <option value="admin">Admin</option>
        <option value="master">Super Admin</option>
        <option value="verificator_jf">Verificator Jafung</option>
        <option value="verificator_kp">Verificator KP</option>
        <option value="verificator_cuti">Verificator Cuti</option>
        <option value="verificator_presensi">Verificator Presensi</option>
      </select>
      <button type="submit">Filter</button>
    </form>
  </div>

  <div id="userTable">
    <table border="1" width="100%" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
      <tr style="background:#3498db; color:white;">
        <th>ID</th>
        <th>Nama Lengkap</th>
        <th>NIP</th>
        <th>Email</th>
        <th>Role</th>
        <th>Register On</th>
        <th>Action</th>
      </tr>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr style="text-align: left;">
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['nip']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td>
          <select class="role-select" data-id="<?php echo $row['id']; ?>">
            <option value="super_admin"  <?php if($row['role']=='super_admin') echo 'selected'; ?>>Super Admin</option>
            <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
            <option value="master" <?php if($row['role']=='master') echo 'selected'; ?>>Master</option>
            <option value="verificator_jf" <?php if($row['role']=='verificator_jf') echo 'selected'; ?>>Verificator Jafung</option>
            <option value="verificator_kp" <?php if($row['role']=='verificator_kp') echo 'selected'; ?>>Verificator KP</option>
            <option value="verificator_cuti" <?php if($row['role']=='verificator_cuti') echo 'selected'; ?>>Verificator Cuti</option>
            <option value="verificator_presensi" <?php if($row['role']=='verificator_presensi') echo 'selected'; ?>>Verificator Presensi</option>
          </select>
          <span class="status-msg" style="display:none; font-size:12px; margin-left:6px;"></span>
        </td>
        <td><?php echo $row['created_at']; ?></td>
        <td style="text-align: center;">
          <button class="delete-btn" data-id="<?php echo $row['id']; ?>" style="background:#e74c3c;color:white;padding:6px 10px;border-radius:5px;border:none;cursor:pointer;">Delete</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
  
<div class="footer">
    <p>Copyright &copy; 2025. Tim PUSDATIN - BKPSDMD Kabupaten Merangin.</p>
</div>

<!-- Pagination -->
<div style="text-align:center;margin-top:15px;">
  <?php if ($page > 1): ?>
    <a href="?page=<?php echo $page-1; ?>" style="margin:0 5px;">⬅ Prev</a>
  <?php endif; ?>

  <?php for ($i=1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i==$page) ? 'active' : ''; ?>" style="margin:0 5px;<?php echo ($i==$page) ? 'font-weight:bold;color:#3498db;' : ''; ?>">
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="?page=<?php echo $page+1; ?>" style="margin:0 5px;">Next ➡</a>
  <?php endif; ?>
</div>

<script>
document.querySelectorAll(".role-select").forEach(select => {
    select.addEventListener("change", function() {
        let userId = this.dataset.id;
        let newRole = this.value;
        let statusMsg = this.nextElementSibling;

        // Disable dropdown while saving
        this.disabled = true;

        fetch("update_role.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(userId) + "&role=" + encodeURIComponent(newRole)
        })
        .then(res => res.text())
        .then(data => {
            console.log("Response:", data); // debug

            statusMsg.style.display = "inline";
            if (data.includes("successfully")) {
                statusMsg.style.color = "green";
                statusMsg.textContent = "✔ Saved";
            } else {
                statusMsg.style.color = "red";
                statusMsg.textContent = "✘ Failed";
            }

            setTimeout(() => {
                statusMsg.style.display = "none";
            }, 2000);
        })
        .catch(err => {
            console.error(err);
            statusMsg.style.display = "inline";
            statusMsg.style.color = "red";
            statusMsg.textContent = "✘ Error";
            setTimeout(() => {
                statusMsg.style.display = "none";
            }, 2000);
        })
        .finally(() => {
            this.disabled = false; // re-enable dropdown
        });
    });
});
</script>
  <script>
    // Handle Delete button
    document.addEventListener("click", function(e) {
      if (e.target.classList.contains("delete-btn")) {
        const id = e.target.getAttribute("data-id");
        if (confirm("Are you sure you want to delete this user?")) {
          const formData = new FormData();
          formData.append("action", "delete");
          formData.append("id", id);

          fetch("admin_actions.php", {
            method: "POST",
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            alert(data.message);
            loadUsers(); // reload table
          });
        }
      }
    });

    function loadUsers(page = 1) {
      const search = document.getElementById('search').value;
      const role = document.getElementById('role_filter').value;

      const xhr = new XMLHttpRequest();
      xhr.open("GET", "admin_fetch_users.php?page=" + page + "&search=" + encodeURIComponent(search) + "&role_filter=" + role, true);
      xhr.onload = function() {
        if (this.status === 200) {
          document.getElementById('userTable').innerHTML = this.responseText;
        }
      }
      xhr.send();
    }

    // On page load
    window.onload = function() {
      loadUsers();
    }

    // Filter submit
    document.getElementById('filterForm').addEventListener("submit", function(e) {
      e.preventDefault();
      loadUsers();
    });

    // Handle pagination clicks
    document.addEventListener("click", function(e) {
      if (e.target.classList.contains("pagination-link")) {
        e.preventDefault();
        const page = e.target.getAttribute("data-page");
        loadUsers(page);
      }
    });
  </script>

</body>
</html>