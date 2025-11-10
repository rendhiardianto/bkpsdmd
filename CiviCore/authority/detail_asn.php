<?php
include "../db.php";
require_once("../auth.php");

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM asn_merangin WHERE id = $id");

if ($result->num_rows === 0) {
    die("Data tidak ditemukan.");
}

$row = $result->fetch_assoc();

// Determine role and backlink
$role = $_GET['role'] ?? $_SESSION['role'];
$backLinks = [
    'super_admin' => '../dashboard_super_admin.php',
    'admin'       => '../dashboard_admin.php',
];
$backUrl = $backLinks[$role] ?? 'list_asn_merangin.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Data ASN - Merangin</title>

  <link href="detail_asn.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">
  
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Detail Data ASN Kabupaten Merangin</h1>
  </div>
</div>

<!-- CONTENT -->
<div class="content">
  <div class="detail-card">
    <h2>Data ASN</h2>

    <form method="POST" action="update_asn.php">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">

      <label>NIP</label>
      <input type="text" name="nip" value="<?= htmlspecialchars($row['nip']) ?>" required>

      <label>Nama Lengkap</label>
      <input type="text" name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>" required>

      <label>Tempat Lahir</label>
      <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($row['tempat_lahir']) ?>">

      <label>Tanggal Lahir</label>
      <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($row['tanggal_lahir']) ?>">

      <label>Status Pegawai</label>
      <input type="text" name="status_pegawai" value="<?= htmlspecialchars($row['status_pegawai']) ?>">

      <label>Jabatan</label>
      <input type="text" name="jabatan" value="<?= htmlspecialchars($row['jabatan']) ?>">

      <label>Organisasi</label>
      <input type="text" name="organisasi" value="<?= htmlspecialchars($row['organisasi']) ?>">

      <label>Organisasi Induk</label>
      <input type="text" name="organisasi_induk" value="<?= htmlspecialchars($row['organisasi_induk']) ?>">

      <div class="actions">
        <a href="delete_asn.php?id=<?= $row['id'] ?>" 
           class="btn btn-delete" 
           onclick="return confirm('Yakin ingin menghapus data Pegawai ini?')">Hapus</a>
        
        <button type="submit" class="btn btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- FOOTER -->
<div class="footer">
  <p>Copyright &copy; 2025. BKPSDMD Kabupaten Merangin. All Rights Reserved.</p>
</div>

</body>
</html>
