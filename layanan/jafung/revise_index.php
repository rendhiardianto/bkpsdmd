<?php
session_start();
include_once __DIR__ . '/../../CiviCore/db.php';
include_once __DIR__ . '/../../CiviCore/datetime_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $conn->real_escape_string($_POST['nip']);

    // 🔍 Check ASN
    $pegawaiResult = $conn->query("SELECT jenjang_jabatan, status_pegawai FROM asn_merangin WHERE nip='$nip' LIMIT 1");

    if ($pegawaiResult->num_rows == 0) {
        $error = "NIP ini tidak terdaftar di Lingkup ASN Pemkab Merangin.";
    } else {
        $pegawai = $pegawaiResult->fetch_assoc();
        $jenjang_jabatan = trim($pegawai['jenjang_jabatan']);
        $status  = trim($pegawai['status_pegawai']);

        if (strcasecmp($jenjang_jabatan, 'Fungsional') !== 0 || strcasecmp($status, 'PNS') !== 0) {
            $error = "Hanya ASN dengan Jabatan 'Fungsional' dan Status Pegawai 'PNS' yang dapat mengakses halaman revisi ini.";
        } else {
            // 🔎 Check latest submission
            $userResult = $conn->query("SELECT id, status FROM jafung_submissions WHERE nip='$nip' ORDER BY created_at DESC LIMIT 1");

            if ($userResult->num_rows > 0) {
                $submission = $userResult->fetch_assoc();

                if (strcasecmp($submission['status'], 'rejected') === 0) {
                    $_SESSION['allow_revise'] = true;
                    $_SESSION['verified_nip'] = $nip;
                    header("Location: revise_document.php?nip=" . urlencode($nip));
                    exit();
                } else {
                    $error = "NIP ini tidak memiliki pengajuan dengan status 'Rejected'.";
                }
            } else {
                $error = "Belum ada pengajuan sebelumnya dengan status 'Rejected'.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi Revisi Dokumen</title>
  <link href="revise_index.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div class="header">
    <div class="logo">
      <a href="index.php"><img src="../../icon/BKPLogo3.png" width="150"></a>
    </div>
    <div class="roleHeader"><h1>Revisi Dokumen Jabatan Fungsional</h1></div>
  </div>

  <div class="content">
    <div class="form-box">
      <h2>Verifikasi NIP untuk Revisi</h2>
      <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
      <?php endif; ?>
      <form method="post">
        <input type="text" name="nip" placeholder="Masukkan NIP Anda" id="nip" required>
        <button type="submit">Verifikasi</button>
      </form>
    </div>
  </div>
<!------------------- FOOTER ----------------------------------->	
<div id="footer"></div>
<script>
fetch("footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
</body>
</html>
