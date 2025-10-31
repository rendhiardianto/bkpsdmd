<?php
include "../db.php";
require_once("../auth.php");

requireRole('super_admin');
$role = 'super_admin';

// Read role (GET first, session fallback)
$role = $_GET['role'] ?? $_SESSION['role'];

// Read “from” page if provided
$fromPage = $_GET['from'] ?? null;

// Define back links for each role
$backLinks = [
    'super_admin'  => 'list_asn_merangin.php',
];
$backUrl = $backLinks[$role];

// --- Step 2: Proses data jika form disubmit ---
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize input
    $nip                = $conn->real_escape_string($_POST['nip']);
    $fullname           = $conn->real_escape_string($_POST['fullname']);
    $gelar_depan        = $conn->real_escape_string($_POST['gelar_depan']);
    $gelar_belakang     = $conn->real_escape_string($_POST['gelar_belakang']);
    $tempat_lahir       = $conn->real_escape_string($_POST['tempat_lahir']);
    $tanggal_lahir      = $conn->real_escape_string($_POST['tanggal_lahir']);
    $jenis_kelamin      = $conn->real_escape_string($_POST['jenis_kelamin']);
    $agama              = $conn->real_escape_string($_POST['agama']);
    $phone              = $conn->real_escape_string($_POST['phone']);
    $status_pegawai     = $conn->real_escape_string($_POST['status_pegawai']);
    $gol_awal           = $conn->real_escape_string($_POST['gol_awal']);
    $gol_saat_ini       = $conn->real_escape_string($_POST['gol_saat_ini']);
    $tmt_gol            = $conn->real_escape_string($_POST['tmt_gol']);
    $masa_kerja_tahun   = (int) $_POST['masa_kerja_tahun'];
    $masa_kerja_bulan   = (int) $_POST['masa_kerja_bulan'];
    $jenjang_jabatan    = $conn->real_escape_string($_POST['jenjang_jabatan']);
    $jenis_jabatan      = $conn->real_escape_string($_POST['jenis_jabatan']);
    $jabatan            = $conn->real_escape_string($_POST['jabatan']);
    $tmt_jabatan        = $conn->real_escape_string($_POST['tmt_jabatan']);
    $tingkat_pendidikan = $conn->real_escape_string($_POST['tingkat_pendidikan']);
    $pendidikan         = $conn->real_escape_string($_POST['pendidikan']);
    $tahun_lulus        = $conn->real_escape_string($_POST['tahun_lulus']);
    $lokasi_kerja       = $conn->real_escape_string($_POST['lokasi_kerja']);
    $organisasi         = $conn->real_escape_string($_POST['organisasi']);
    $organisasi_induk   = $conn->real_escape_string($_POST['organisasi_induk']);
    $instansi_induk     = $conn->real_escape_string($_POST['instansi_induk']);

    // Prepare statement
    $stmt = $conn->prepare("
        INSERT INTO asn_merangin (
            nip, fullname, gelar_depan, gelar_belakang, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, phone,
            status_pegawai, gol_awal, gol_saat_ini, tmt_gol, masa_kerja_tahun, masa_kerja_bulan,
            jenjang_jabatan, jenis_jabatan, jabatan, tmt_jabatan,
            tingkat_pendidikan, pendidikan, tahun_lulus, lokasi_kerja,
            organisasi, organisasi_induk, instansi_induk, created_at
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
    ");

    $stmt->bind_param(
        "ssssssssssssiiissssssssssss",
        $nip, $fullname, $gelar_depan, $gelar_belakang, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $agama, $phone,
        $status_pegawai, $gol_awal, $gol_saat_ini, $tmt_gol, $masa_kerja_tahun, $masa_kerja_bulan,
        $jenjang_jabatan, $jenis_jabatan, $jabatan, $tmt_jabatan,
        $tingkat_pendidikan, $pendidikan, $tahun_lulus, $lokasi_kerja,
        $organisasi, $organisasi_induk, $instansi_induk
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data ASN berhasil disimpan!'); window.location='add_new_asn.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-65T4XSDM2Q');
  </script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Data Pegawai Baru</title>

  <link href="add_new_asn.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">

</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Dashboard Management Data ASN Merangin</h1>
  </div>
</div>

<div class="form-box">
  <h2>Tambahkan Data Baru</h2>

  <form method="POST">
    <label>Nomor Induk Pegawai</label>
    <input type="text" name="nip" required>

    <label>Nama Lengkap</label>
    <input type="text" name="fullname" required>

    <label>Gelar Depan</label>
    <input type="text" name="gelar_depan">

    <label>Gelar Belakang</label>
    <input type="text" name="gelar_belakang" required>

    <label>Tempat Lahir</label>
    <input type="text" name="tempat_lahir" required>

    <label>Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" required>

    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin" required>
      <option value="">(Pilih Jenis Kelamin)</option>
      <option value="Laki-laki">Laki-laki</option>
      <option value="Perempuan">Perempuan</option>
    </select>

    <label>Agama</label>
    <select name="agama" required>
      <option value="">(Pilih Agama)</option>
      <option value="Islam">Islam</option>
      <option value="Kristen">Kristen</option>
      <option value="Katholik">Katholik</option>
      <option value="Hindu">Hindu</option>
      <option value="Buddha">Buddha</option>
      <option value="Konghucu">Konghucu</option>
    </select>

    <label>Nomor HP</label>
    <input type="text" name="phone" required>

    <label>Status Pegawai</label>
    <select name="status_pegawai" required>
      <option value="">(Pilih Status Pegawai)</option>
      <option value="CPNS">CPNS</option>
      <option value="PNS">PNS</option>
      <option value="PPPK">PPPK</option>
    </select>

    <label>Gol Awal</label>
    <input type="text" name="gol_awal" required>

    <label>Gol Saat Ini</label>
    <input type="text" name="gol_saat_ini" required>

    <label>TMT Golongan</label>
    <input type="text" name="tmt_gol" required>

    <label>Masa Kerja Tahun</label>
    <input type="text" name="masa_kerja_tahun" required>

    <label>Masa Kerja Bulan</label>
    <input type="text" name="masa_kerja_bulan" required>

    <label>Jenjang Jabatan</label>
    <select name="jenjang_jabatan" required>
      <option value="">(Pilih Jenjang Jabatan)</option>
      <option value="Fungsional">Fungsional</option>
      <option value="Pelaksana">Pelaksana</option>
      <option value="Pengawas">Pengawas</option>
      <option value="Administrator">Administrator</option>
      <option value="Jabatan Pimpinan Tinggi">Jabatan Pimpinan Tinggi</option>
    </select>

    <label>Jenis Jabatan</label>
    <select name="jenis_jabatan" required>
      <option value="">(Pilih Jenis Jabatan)</option>
      <option value="Jabatan Fungsional">Jabatan Fungsional</option>
      <option value="Jabatan Pelaksana">Jabatan Pelaksana</option>
      <option value="Jabatan Struktural">Jabatan Struktural</option>
    </select>

    <label>Jabatan</label>
    <input type="text" name="jabatan" required>

    <label>TMT Jabatan</label>
    <input type="date" name="tmt_jabatan" required>

    <label>Tingkat Pendidikan</label>
    <input type="text" name="tingkat_pendidikan" required>

    <label>Pendidikan</label>
    <input type="text" name="pendidikan" required>

    <label>Tahun Lulus</label>
    <input type="text" name="tahun_lulus" required>

    <label>Lokasi Kerja</label>
    <input type="text" name="lokasi_kerja" required>

    <label>Organisasi</label>
    <input type="text" name="organisasi" required>

    <label>Organisasi Induk</label>
    <input type="text" name="organisasi_induk" required>

    <label>Instansi Induk</label>
    <input type="text" name="instansi_induk" required>

    <button type="submit">Simpan Data</button>
  </form>

  <div id="message" style="margin-top:10px; font-weight:bold;">
    <?= $message ?>
  </div>
</div>

</body>
</html>
