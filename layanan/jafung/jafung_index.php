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
            // 3️⃣ Check if NIP already has submission
            $userResult = $conn->query("SELECT id, status FROM jafung_submissions WHERE nip='$nip' ORDER BY created_at DESC LIMIT 1");

            if ($userResult->num_rows > 0) {
                $submission = $userResult->fetch_assoc();

                // ❌ If still processing, prevent new upload
                if (strcasecmp($submission['status'], 'rejected') !== 0 && strcasecmp($submission['status'], 'completed') !== 0) {
                    $error = "NIP ini sedang dalam proses Pengajuan Jabatan Fungsional. 
                    Jika informasi ini tidak benar, silahkan hubungi Tim JAFUNG BKPSDMD Kabupaten Merangin.";
                } else {
                    // ✅ If previous process finished/rejected, allow new upload
                    $_SESSION['allow_update'] = true;
                    $_SESSION['verified_nip'] = $nip;
                    header("Location: upload_document.php?nip=" . urlencode($nip));
                    exit();
                }
            } else {
                // ✅ No previous submission — allow upload
                $_SESSION['allow_update'] = true;
                $_SESSION['verified_nip'] = $nip;
                header("Location: upload_document.php?nip=" . urlencode($nip));
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
  <title>Dashboard - Layanan Jabatan Fungsional</title>
  <link href="jafung_index.css" rel="stylesheet" type="text/css">
  <link href="/headerFooter.css" rel="stylesheet" type="text/css">

  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="../icon/button/logo2.png">
</head>

<body>

<div class="header">
    <div class="logo">
      <a href="/index.php" target="_blank"><img src="/icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
    </div>
    <div class="roleHeader">
      <h1>Welcome Dashboard Jabatan Fungsional</h1>
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
    <iframe src="/images/LayananKPG/Surat Syarat Usul JF.pdf"></iframe>
  </div><!--leftSide--> 

  <div class="rightSide">
    <div class="top-right-button">
      <a href="revise_index.php" class="revise-btn">Revisi Dokumen</a>
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