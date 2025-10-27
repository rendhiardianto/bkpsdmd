<?php
include "../db.php";
include "../auth.php";
requireRole('user');
  //PENGUMUMAN
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $created_by = $_SESSION['fullname'];

    $sql = "INSERT INTO announcements (title, content, created_by) 
            VALUES ('$title', '$content', '$created_by')";
    if ($conn->query($sql)) {
        echo "<script>alert('✅ Announcement added!'); window.location='dashboard_cms_admin.php';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $conn->error . "');</script>";
    }
}
?>

<?php
$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT nip, fullname, profile_pic FROM users WHERE id=$userId");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <title>CMS Admin Dashboard</title>
  <link href="dashboard_cms_admin.css" rel="stylesheet" type="text/css">
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="/images/button/logo2.png">

</head>
<body>
  <div class="header">
            <div class="logo">
            	<a href="/index.php" target="_blank"><img src="/icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
            </div>
            <div class="navbar">
              
            	<a href="../logout.php" class="logout" style="text-decoration: none;">Logout  <img src="/icon/button/exit.png" width="30px" style="vertical-align:middle;"></a>
            </div>
    </div>
    <div class="roleHeader">
      <h1>CMS Admin Dashboard</h1>
    </div>

<div class="content">

    <div class="leftSide">

      <div class="userBio">
        <h2 style="font-family: FreeHand;">Selamat Datang,</h2>
        <?php echo $user['fullname']; ?>
      </div>

      <div class="fotoProfil">
        <?php if ($user['profile_pic']): ?>
        <img src="../uploads/profile_pics/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
        <?php else: ?>
        <img src="../uploads/profile_pics/default.png" alt="Default Profile">
        <?php endif; ?>
      </div>

    </div>
  
  <div class="rightSide">

    <div class="flex-item-main">
      <p><a href="../pengumuman/dashboard_pengumuman.php">
        <img src="/icon/button/announcement.png" width="100"></a><br>PENGUMUMAN</p>
    </div>

    <div class="flex-item-main">
      <p><a href="../berita/admin_news.php">
        <img src="/icon/button/news.png" width="100"></a><br>BERITA</p>
    </div>

    <div class="flex-item-main">
      <p><a href="../infoGrafis/admin_infoGrafis.php">
        <img src="/icon/button/graphics.png" width="100"></a><br>INFO GRAFIS</p>
    </div>
      
    <!--<div class="top-bar">
      <a href="admin_add.php" style="padding:8px 14px;background:#2ecc71;color:white;border-radius:6px;text-decoration:none;">➕ Add User</a>

      <form id="filterForm">
        <input type="text" name="search" id="search" placeholder="Search by name/email">
        <select name="role_filter" id="role_filter">
          <option value="">All Roles</option>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
        <button type="submit">Filter</button>
      </form>
    </div>

  <div id="userTable">
    AJAX loads user table here 
  </div>-->

</div><!-- CONTENT CLOSE-->

  <div class="footer">
    <p>Copyright &copy; 2025. Tim PUSDATIN - BKPSDMD Kabupaten Merangin.</p>
  </div>

</body>
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

// Handle Edit button
document.addEventListener("click", function(e) {
  if (e.target.classList.contains("edit-btn")) {
    const id = e.target.getAttribute("data-id");

    // Prompt simple inline edit (can be replaced with a modal form)
    const fullname = prompt("Enter new full name:");
    const email = prompt("Enter new email:");
    const role = prompt("Enter role (admin/user):");

    if (fullname && email && role) {
      const formData = new FormData();
      formData.append("action", "edit");
      formData.append("id", id);
      formData.append("fullname", fullname);
      formData.append("email", email);
      formData.append("role", role);

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
</html>