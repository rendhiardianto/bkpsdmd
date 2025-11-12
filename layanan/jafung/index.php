<?php
session_start();

include_once __DIR__ . '/../../CiviCore/db.php';
include_once __DIR__ . '/../../CiviCore/datetime_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $conn->real_escape_string($_POST['nip']);

    // 1️⃣ Check if ASN exists
    $pegawaiResult = $conn->query("SELECT jenjang_jabatan, status_pegawai FROM asn_merangin WHERE nip='$nip' LIMIT 1");

    if ($pegawaiResult->num_rows == 0) {
        $error = "NIP ini tidak terdaftar di Lingkup ASN Pemkab Merangin.";
    } else {
        $pegawai = $pegawaiResult->fetch_assoc();
        $jenjang_jabatan = trim($pegawai['jenjang_jabatan']);
        $status  = trim($pegawai['status_pegawai']);

        // 2️⃣ Verify JABATAN & STATUS PEGAWAI
        if (strcasecmp($jenjang_jabatan, 'Fungsional') !== 0 || strcasecmp($status, 'PNS') !== 0) {
            $error = "Hanya ASN dengan Jabatan 'Fungsional' dan Status Pegawai 'PNS' yang dapat mengakses halaman ini.";
        } else {
            // 3️⃣ Check if NIP already has any submission
            $userResult = $conn->query("SELECT id FROM jafung_submissions WHERE nip='$nip' LIMIT 1");

            if ($userResult->num_rows > 0) {
                // ❌ Already has submission — block access
                $error = "NIP ini sedang melakukan Pengajuan Jabatan Fungsional, tidak dapat melakukan pengajuan ulang.";
            } else {
                // ✅ No previous submission — allow upload
                $_SESSION['allow_update'] = true;
                $_SESSION['verified_nip'] = $nip;
                header("Location: upload_form.php?nip=" . urlencode($nip));
                exit();
            }
        }
    }
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
  <title>Welcome Dashboard - Pengajuan Jabatan Fungsional</title>

  <link rel="stylesheet" href="/fontFamily.css">
  <link href="index.css" rel="stylesheet" type="text/css">
  <link href="/headerFooter.css" rel="stylesheet" type="text/css">

  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="../icon/button/logo2.png">
</head>

<body>

<div class="header">
    <div class="logo">
      <a href="../../layanan.html" ><img src="../../icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Pengajuan Jabatan Fungsional</h1>
    </div>
</div>

<div class="liveClock">
  <?php echo renderLiveClock(); ?>
</div>

<div class="content">  

  <div class="leftSide">
    <h2>SELAMAT DATANG</h2>
    <p>Kepada Yth. Bapak/Ibu ASN Di Lingkugan Pemerintah Kabupaten Merangin, 
        <b>sebelum melakukan pengajuan Jabatan Fungsional, harap membaca Pedoman Pengusulan 
        dan Syarat Layanan Jabatan Fungsional</b> berikut;</p>
    <iframe src="../../images/LayananKPG/Surat Syarat Usul JF.pdf"></iframe>
  </div><!--leftSide--> 

  <div class="rightSide">
    <div class="top-right-button">
      Tombol untuk merevisi berkas Anda. <a href="revise_index.php" class="revise-btn">Revisi Berkas</a> 
    </div>
    
    <div class="form-box">
        <h2>Verifikasi NIP</h2>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="nip" placeholder="Masukkan NIP Anda" id="nip" required>
            <button type="submit">Verifikasi</button>
        </form>
    </div>    
  </div><!--rightSide-->
</div><!--content-->

<!------------------- FOOTER ----------------------------------->	
<div id="footer"></div>
<script>
fetch("/footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
</body>
</html>