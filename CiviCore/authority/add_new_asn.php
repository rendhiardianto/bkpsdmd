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

     // 🔍 Step 1: Check if NIP already exists
    $check = $conn->prepare("SELECT nip FROM asn_merangin WHERE nip = ?");
    $check->bind_param("s", $nip);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('⚠️ NIP sudah terdaftar! Silakan periksa kembali.'); window.history.back();</script>";
        $check->close();
        exit;
    }
    $check->close();

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
    "sssssssssssssiisssssssssss",
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashbboard - Tambah Data Pegawai Baru</title>

  <link href="add_new_asn.css" rel="stylesheet">
  <link rel="shortcut icon" href="/images/button/logo.png">
</head>
<body>

<div class="header">
  <div class="navbar">
    <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn btn-secondary" 
    style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Dashboard Tambah Data ASN Baru</h1>
  </div>
</div>

<div class="form-box">
  <!-- 🧭 Progress Bar -->
<div class="progressbar-container">
  <div class="progressbar">
    <div class="progress" id="progress"></div>
    <div class="progress-step active" data-title="Data Pribadi"></div>
    <div class="progress-step" data-title="Pendidikan"></div>
    <div class="progress-step" data-title="Data Kepegawaian"></div>
  </div>
</div>

<form method="POST" id="multiStepForm">
  <!-- Step 1: Data Pribadi -->
  <div class="form-step active">
    
  <div class="form-group">
    <label>Nomor Induk Pegawai</label>
    <input type="text" name="nip" required>
  </div>

  <div class="form-group">
    <label>Nama Lengkap</label>
    <input type="text" name="fullname" required>
  </div>

  <div class="form-group">
    <label>Gelar Depan</label>
    <input type="text" name="gelar_depan">
  </div>

  <div class="form-group">
    <label>Gelar Belakang</label>
    <input type="text" name="gelar_belakang">
  </div>

  <div class="form-group">
    <label>Tempat Lahir</label>
    <input type="text" name="tempat_lahir" required>
  </div>

  <div class="form-group">
    <label>Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" required>
  </div>

  <div class="form-group">
    <label>Jenis Kelamin</label>
    <select name="jenis_kelamin" required>
      <option value="">(Pilih Jenis Kelamin)</option>
      <option value="Laki-laki">Laki-laki</option>
      <option value="Perempuan">Perempuan</option>
    </select>
  </div>

  <div class="form-group">
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
  </div>

  <div class="form-group">
    <label>Nomor HP</label>
    <input type="text" name="phone" required>
  </div>

  <div class="form-nav">
    <button type="button" class="next-btn">Lanjut ke Pendidikan ➜</button>
  </div>
</div>


  <!-- Step 2: Pendidikan -->
<div class="form-step">

  <div class="form-group">
    <label>Tingkat Pendidikan</label>
    <input type="text" name="tingkat_pendidikan" required>
  </div>

  <div class="form-group">
    <label>Pendidikan</label>
    <input type="text" name="pendidikan" required>
  </div>

  <div class="form-group">
    <label>Tahun Lulus</label>
    <input type="text" name="tahun_lulus" required>
  </div>

  <div class="form-nav">
    <button type="button" class="back-btn">⬅ Kembali</button>
    <button type="button" class="next-btn">Lanjut ke Data Kepegawaian ➜</button>
  </div>
</div>

<!-- Step 3: Status Kepegawaian -->
<div class="form-step">

  <div class="form-group">
    <label>Data Pegawai</label>
    <select name="status_pegawai" required>
      <option value="">(Pilih Status Pegawai)</option>
      <option value="CPNS">CPNS</option>
      <option value="PNS">PNS</option>
      <option value="PPPK">PPPK</option>
    </select>
  </div>

  <div class="form-group">
    <label>Golongan Awal</label>
    <input type="text" name="gol_awal" required>
  </div>

  <div class="form-group">
    <label>Golongan Saat Ini</label>
    <input type="text" name="gol_saat_ini" required>
  </div>

  <div class="form-group">
    <label>TMT Golongan</label>
    <input type="date" name="tmt_gol" required>
  </div>

  <div class="form-group">
    <label>Masa Kerja (Tahun)</label>
    <input type="number" name="masa_kerja_tahun" required>
  </div>

  <div class="form-group">
    <label>Masa Kerja (Bulan)</label>
    <input type="number" name="masa_kerja_bulan" required>
  </div>

  <div class="form-group">
    <label>Jenjang Jabatan</label>
    <select name="jenjang_jabatan" required>
      <option value="">(Pilih Jenjang Jabatan)</option>
      <option value="Fungsional">Fungsional</option>
      <option value="Pelaksana">Pelaksana</option>
      <option value="Pengawas">Pengawas</option>
      <option value="Administrator">Administrator</option>
      <option value="Jabatan Pimpinan Tinggi">Jabatan Pimpinan Tinggi</option>
    </select>
  </div>

  <div class="form-group">
    <label>Jenis Jabatan</label>
    <select name="jenis_jabatan" required>
      <option value="">(Pilih Jenis Jabatan)</option>
      <option value="Jabatan Fungsional">Jabatan Fungsional</option>
      <option value="Jabatan Pelaksana">Jabatan Pelaksana</option>
      <option value="Jabatan Struktural">Jabatan Struktural</option>
    </select>
  </div>

  <div class="form-group">
    <label>Jabatan</label>
    <input type="text" name="jabatan" required>
  </div>

  <div class="form-group">
    <label>TMT Jabatan</label>
    <input type="date" name="tmt_jabatan" required>
  </div>

  <div class="form-group">
    <label>Lokasi Kerja</label>
    <input type="text" name="lokasi_kerja" required>
  </div>

  <div class="form-group">
    <label>Organisasi</label>
    <input type="text" name="organisasi" required>
  </div>

  <div class="form-group">
    <label>Organisasi Induk</label>
    <input type="text" name="organisasi_induk" required>
  </div>

  <div class="form-group">
    <label>Instansi Induk</label>
    <input type="text" name="instansi_induk" required>
  </div>

  <div class="form-nav">
    <button type="button" class="back-btn">⬅ Kembali</button>
    <button type="submit" class="submit-btn">Simpan Data</button>
  </div>
</div>

</form>

<div id="message" style="margin-top:10px; font-weight:bold;">
  <?= $message ?>
</div>


</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const steps = document.querySelectorAll(".form-step");
  const nextBtns = document.querySelectorAll(".next-btn");
  const backBtns = document.querySelectorAll(".back-btn");
  const progress = document.querySelector(".progress");
  const progressSteps = document.querySelectorAll(".progress-step");

  let currentStep = 0;

  // handle "Next" click
  nextBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const currentFormStep = steps[currentStep];
      const inputs = currentFormStep.querySelectorAll("input, select, textarea");

      let allValid = true;

      inputs.forEach(input => {
        if (input.hasAttribute("required") && !input.value.trim()) {
          input.style.borderColor = "red";
          allValid = false;
        } else {
          input.style.borderColor = "#ccc";
        }
      });

      if (!allValid) {
        alert("⚠️ Harap isi semua kolom yang wajib diisi sebelum melanjutkan!");
        return;
      }

      steps[currentStep].classList.remove("active");
      progressSteps[currentStep].classList.remove("active");
      currentStep++;
      steps[currentStep].classList.add("active");
      progressSteps[currentStep].classList.add("active");
      updateProgress();
    });
  });

  // handle "Back" click
  backBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      steps[currentStep].classList.remove("active");
      progressSteps[currentStep].classList.remove("active");
      currentStep--;
      steps[currentStep].classList.add("active");
      progressSteps[currentStep].classList.add("active");
      updateProgress();
    });
  });

  // update progress bar
  function updateProgress() {
    const activeSteps = document.querySelectorAll(".progress-step.active");
    progress.style.width = ((activeSteps.length - 1) / (progressSteps.length - 1)) * 100 + "%";
  }
});
</script>


</body>
</html>
