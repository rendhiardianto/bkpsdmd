<?php
session_start();

if (
    !isset($_SESSION['allow_revise']) ||
    $_SESSION['allow_revise'] !== true ||
    !isset($_SESSION['verified_nip'])
) {
    header("Location: revise_index.php");
    exit();
}

$verified_nip = $_SESSION['verified_nip'];
$nipFromUrl = $_GET['nip'] ?? '';

if ($nipFromUrl !== $verified_nip) {
    header("Location: index.php?error=unauthorized");
    exit();
}

include_once __DIR__ . '/../../CiviCore/db.php';
include_once __DIR__ . '/../../CiviCore/config.php';
include_once __DIR__ . '/../../CiviCore/datetime_helper.php';

// --- Fetch ASN info ---
$nipFromGet = $_GET['nip'] ?? '';
if (!empty($nipFromGet)) {
    $stmt = $conn->prepare("SELECT fullname, tempat_lahir, tanggal_lahir, gol_saat_ini, 
        jenis_jabatan, jabatan, tmt_jabatan, pendidikan, organisasi, organisasi_induk 
        FROM asn_merangin WHERE nip=?");
    $stmt->bind_param("s", $nipFromGet);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $fullnameFromDB = $row['fullname'];
        $tempatLahirFromDB = $row['tempat_lahir'];
        $tanggalLahirFromDB = $row['tanggal_lahir'];
        $golSaatIniFromDB = $row['gol_saat_ini'];
        $jenisJabatanFromDB = $row['jenis_jabatan'];
        $jabatanFromDB = $row['jabatan'];
        $tmtJabatanFromDB = $row['tmt_jabatan'];
        $pendidikanFromDB = $row['pendidikan'];
        $organisasiFromDB = $row['organisasi'];
        $organisasiIndukFromDB = $row['organisasi_induk'];
    }
    $stmt->close();
}

// --- Find submission ID ---
$submission_id = $_GET['id'] ?? null;
if (!$submission_id) {
    $stmtAuto = $conn->prepare("SELECT id FROM jafung_submissions WHERE nip = ? ORDER BY id DESC LIMIT 1");
    $stmtAuto->bind_param("s", $verified_nip);
    $stmtAuto->execute();
    $resAuto = $stmtAuto->get_result();
    $rowAuto = $resAuto->fetch_assoc();
    $stmtAuto->close();

    $submission_id = $rowAuto['id'] ?? null;
}

if (!$submission_id) {
    echo "<script>
        alert('❌ Data pengajuan tidak ditemukan untuk NIP ini.\\nSilakan lakukan pengajuan baru.');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// --- Load submission data ---
$stmt = $conn->prepare("SELECT document_paths, status, phone, admin_note FROM jafung_submissions WHERE id = ? AND nip = ?");
$stmt->bind_param("is", $submission_id, $verified_nip);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    die("Submission not found.");
}

$phoneFromDB = $row['phone'] ?? '';
$noteFromDB = $row['admin_note'] ?? '';
$docs = json_decode($row['document_paths'], true) ?? [];
$stmt->close();

// --- Handle file replacement & phone update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedDocs = $docs;
    $newPhoneInput = trim($_POST['phone'] ?? '');
    $newPhone = !empty($newPhoneInput) ? $newPhoneInput : $phoneFromDB;
    $userNote = trim($_POST['user_note'] ?? '');

    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            if (!empty($docs[$key])) {
                $oldFilePath = __DIR__ . "/uploads/documents/" . basename($docs[$key]);
                if (file_exists($oldFilePath)) unlink($oldFilePath);
            }
            
            $uploadDir = __DIR__ . "/uploads/documents/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // ✅ Keep original stored filename (from database)
            if (!empty($docs[$key])) {
                $targetPath = $uploadDir . basename($docs[$key]); 
            } else {
                // fallback if somehow the old name missing
                $targetPath = $uploadDir . basename($file['name']);
            }

            // overwrite old file with new upload
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $updatedDocs[$key] = basename($targetPath);
            }
        }
    }

    $jsonDocs = json_encode($updatedDocs, JSON_UNESCAPED_SLASHES);
    $newStatus = 'revised';

    // ✅ Combine document + phone update in one query
    $updateStmt = $conn->prepare("
        UPDATE jafung_submissions 
        SET document_paths = ?, status = ?, phone = ?, user_note = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $updateStmt->bind_param("ssssi", $jsonDocs, $newStatus, $newPhone, $userNote, $submission_id);

    $updateStmt->execute();
    $updateStmt->close();

    echo "<script>alert('✅ Berkas berhasil direvisi.'); window.location.href='index.php';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
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
  <title>Dashboard - Revisi Dokumen Jabatan Fungsional</title>
  <link href="revise_document.css" rel="stylesheet" type="text/css">
  <link href="/headerFooter.css" rel="stylesheet" type="text/css">

  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="../icon/button/logo2.png">
</head>
<body>

<div class="header">
    <div class="logo">
      <a href="index.php"><img src="/icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Revisi Dokumen Jabatan Fungsional</h1>
    </div>
</div>

<div class="liveClock">
  <?php echo renderLiveClock(); ?>
</div>

<div class="content">

  <div class="info-box">
    
    <div class="fotoProfil">
      <img src="/icon/button/profil.png">
    </div>

    <table  class="infoTable">
      <tr>
        <td><strong>NIP</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($nipFromGet); ?></td>
      </tr>
      <tr>
        <td><strong>Nama Lengkap</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($fullnameFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Golongan</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($golSaatIniFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Jabatan</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($jabatanFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Organisasi</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($organisasiFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Organisasi Induk</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($organisasiIndukFromDB); ?></td>
      </tr>
    </table>

    <div class="inputPhone">
      <h3>No WhatsApp Aktif</h3>
      <input 
        type="text" 
        name="phone" 
        placeholder="Contoh: 081234567890"
        value="<?= htmlspecialchars($phoneFromDB ?? '') ?>"
        required >
        <br><br>
      <div style="color: red"> 
        <h3>Alasan penolakan</h3>
        <?= htmlspecialchars($noteFromDB ?? '') ?>
      </div>
    </div>

  </div>

  <div class="form-box">
    <h2>Revisi Dokumen Pengajuan Jabatan Fungsional</h2>

    <form method="POST" enctype="multipart/form-data">

      <?php foreach ($docs as $key => $file): ?>

        <div class="formFile">
          <p><strong><?= strtoupper(str_replace('_', ' ', $key)); ?></strong></p>

          <?php if (!empty($file)): ?>
            <p>File saat ini: 
              <a href="uploads/documents/<?= htmlspecialchars($file); ?>" target="_blank">
                <?= htmlspecialchars($file); ?>
              </a>
            </p>
          <?php else: ?>
            <p><i>Tidak ada file sebelumnya</i></p>
          <?php endif; ?>

          <label>Upload revisi (jika ingin mengganti):</label><br>
          <input type="file" name="<?= htmlspecialchars($key); ?>" accept=".pdf">
        </div>
      <?php endforeach; ?>

      <div class="formComment">
        <label>Beritahu Kami jika ada Revisi (Opsional)</label>
        <textarea name="user_note" placeholder="Tulis keterangan tambahan mengenai revisi dokumen Anda.">
        </textarea>
      </div>

        <button type="submit" class="submit">Simpan Revisi</button>
    </form>
  </div>
</div>
<!------------------- FOOTER ----------------------------------->
<div class="row">
  <div class="column first">
		<img src="/icon/BKPLogo.png" alt="Logo BKPSDMD">
	  <p style="text-align: center">Copyright © 2025.</p>
	  <p style="text-align: center">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin.</p> 
	  <p style="text-align: center">All Rights Reserved</p>
  </div>
	
  <div class="column second">
		<h3>Butuh Bantuan?</h3>
	  
		<p><a href="https://maps.app.goo.gl/idAZYTHVszUhSGRv8" target="_blank" class="Loc">
			<img src="/icon/sosmed/Loc.png" alt="Logo Loc" width="30px" style="float: left"></a> 
			Jl. Jendral Sudirman, No. 01, Kel. Pematang Kandis, Kec. Bangko, Kab. Merangin, Prov. Jambi - Indonesia | Kode Pos - 37313</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="wa">
			<img src="/icon/sosmed/WA.png" alt="Logo WA" width="30px" style="vertical-align:middle"></a> 
			+62851 5999 7813</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="em">
			<img src="/icon/sosmed/EM.png" alt="Logo Email" width="30px" style="vertical-align:middle"></a> 
			bkd.merangin@gmail.com</p>
  </div>
	
  <div class="column third">
		<h3>Follow Sosial Media Kami!</h3>
		  <a href="https://www.instagram.com/bkpsdmd.merangin/?hl=en" target="_blank" class="ig">
			<img src="/icon/sosmed/IG.png" alt="Logo IG"></a>
	  
		  <a href="https://www.youtube.com/@bkpsdmd.merangin" target="_blank" class="yt">
			<img src="/icon/sosmed/YT.png" alt="Logo YT"></a>
	  
		  <a href="https://www.facebook.com/bkpsdmd.merangin/" target="_blank" class="fb">
			<img src="/icon/sosmed/FB.png" alt="Logo FB"></a>
	  
		  <a href="https://x.com/bkpsdmdmerangin?t=a7RCgFHif89UfeV9aALj8g&s=08" target="_blank" class="x">
			<img src="/icon/sosmed/X.png" alt="Logo X"></a>
	  
		  <a href="https://www.tiktok.com/@bkpsdmd.merangin?_t=ZS-8z3dFdtzgYy&_r=1 " target="_blank" class="tt">
			<img src="/icon/sosmed/TT.png" alt="Logo TT"></a>
  </div>
  <div class="column fourth">
		<h3>Kunjungan Website</h3>
		<p>Hari Ini</p>
		<p>Total</p>
	  
	  	
	  <img src="/icon/BerAkhlak.png" alt="Logo BerAkhlak">
	  
  </div>
</div>
<!------------------- BATAS FOOTER ----------------------------------->
</body>
</html>
