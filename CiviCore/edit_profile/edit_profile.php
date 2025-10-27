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
    'admin'   => '../dashboard_admin.php',
];
$backUrl = $backLinks[$role];

$userId = $_SESSION['user_id'];
$result = $conn->query("SELECT id, nip, fullname, jabatan, organisasi, 
organisasi_induk, organisasi_induk_singkatan, email, profile_pic, role FROM users WHERE id=$userId");


$user = $result->fetch_assoc();
if (!$user) {
    die("User not found.");
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
  <title>MyProfile Management Dashboard</title>
  <link href="edit_profile.css" rel="stylesheet" type="text/css">
  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="/icon/button/logo2.png">

</head>
<body>
<div class="header">
    <div class="navbar">
        <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
      <h1>Edit MyProfile</h1>
    </div>
</div>

<div class="container">   
    <!-- ✅ Update Profile Info -->
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">

      <label>Management Role</label>
      <input type="text" value="<?php echo htmlspecialchars($user['role']); ?>" disabled>

      <label>Nomor Induk Pegawai (NIP)</label>
      <input type="text" value="<?php echo $user['nip']; ?>" disabled>
      <input type="hidden" name="nip" value="<?php echo $user['nip']; ?>">

      <label>Ganti Foto Profil (Maks. 2MB)</label>
      <?php if (!empty($user['profile_pic'])): ?>
        <div id="profile-pic-container" style="text-align:center;">
          <img src="/CiviCore/uploads/profile_pics/<?php echo htmlspecialchars($user['profile_pic']); ?>" 
              alt="Profile Picture"
              id="profile-pic-preview"
              style="width:100px; height:100px; object-fit:cover; border-radius:50%; display:block; margin:auto; margin-bottom:10px;">
          <button type="button" id="delete-pic-btn"
                  style="background-color:#dc2626; color:white; border:none; padding:5px 5px; border-radius:6px; cursor:pointer;">
            🗑️ Hapus Foto
          </button>
        </div>
      <?php endif; ?>
      <input type="file" name="profile_pic" accept="image/*">
      
      <label>Nama Lengkap</label>
      <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

      <label>Jabatan</label>
      <input type="text" name="jabatan" value="<?php echo htmlspecialchars($user['jabatan']); ?>" required>

      <label>Organisasi</label>
      <input type="text" name="organisasi" value="<?php echo htmlspecialchars($user['organisasi']); ?>" disabled>

      <label>Organisasi Induk</label>
      <input type="text" name="organisasi_induk" value="<?php echo htmlspecialchars($user['organisasi_induk']); ?>" disabled>

      <button type="submit">Simpan</button>
    </form>

    <br><hr><br>

    <!-- ✅ Change Email -->
    <form action="change_password.php" method="POST">
      <label>Email Saat Ini</label>
      <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

      <label>Email Baru</label>
      <input type="email" name="new_email" placeholder="Masukkan email baru" required>

      <button type="submit">Simpan</button>
    </form>

    <br><hr><br>

    <!-- ✅ Change Password -->
    <form action="change_password.php" method="POST">
      <label>Kata Sandi Lama</label>
      <input type="password" name="old_password" placeholder="Masukkan kata sandi lama" required>

      <label>Kata Sandi Baru</label>
      <input type="password" name="new_password" placeholder="Masukkan kata sandi baru" required>

      <button type="submit">Simpan</button>
    </form>
</div>


<div class="footer">
    <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const deleteBtn = document.getElementById("delete-pic-btn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", function() {
      if (confirm("Yakin ingin menghapus foto profil?")) {
        fetch("delete_profile_pic.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "user_id=<?php echo $user['id']; ?>"
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message);
          
          if (data.success) {
            document.getElementById("profile-pic-preview").src = "/CiviCore/uploads/profile_pics/default.png";
            alert(data.message);
          }
        })
        .catch(error => console.error("Error:", error));
      }
    });
  }
});
</script>

</body>
</html>