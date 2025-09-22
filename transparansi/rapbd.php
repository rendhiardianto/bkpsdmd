<?php
include "../cms/db.php";

// Directly set the value you want:
$tipe_dokumen = 'RAPBD';

// Query only those rows:
$stmt = $conn->prepare("SELECT * FROM transparansi WHERE tipe_dokumen = ?");
$stmt->bind_param("s", $tipe_dokumen);
$stmt->execute();
$result = $stmt->get_result();

?>


<!doctype html>
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
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<link href="../headerFooter.css" rel="stylesheet" type="text/css">
<link href="style.css" rel="stylesheet" type="text/css">

<title>Transparansi - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="../icon/IconWeb.png">
</head>

<body>
	
<div class="topnav" id="mynavBtn">
	<div class="startlogoDD">
		<button onclick="toggleStartMenu()" class="startbtn"><img src="/icon/LogoStart.png"></button>
		<div id="myStart" class="start-content">
			<a href="/cms/index.php" target="_blank"><img src="/icon/cms.png" width="20px"> Login CMS</a>
			<a href="#" target="_blank"><img src="/icon/fingerprint.png" width="20px"> MyPresensi</a>
			<a href="#" target="_blank"><img src="/icon/documents.png" width="20px"> MyDocuments</a>
			<a href="#" target="_blank"><img src="/icon/form.png" width="20px"> MyForm</a>
		</div>
	</div>
	<div class="navLogo">
		<a href="../index.php"><img src="../icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
	</div>
	
	<div class="navRight" >
		<div class="dropdown">
			<button class="dropbtn">PROFIL <i class="fa fa-caret-down"></i></button>
		  <div id="menu1" class="dropdown-content">
			<a href="../profil.html#visiMisi">Visi dan Misi</a>
			<a href="../profil.html#selaPang">Selayang Pandang</a>
			<a href="../profil.html#sejarah">Sejarah</a>
			<a href="../profil.html#strukOrga">Struktur Organisasi</a>
			<a href="../profil.html#maklumat">Maklumat Pelayanan</a>
			<a href="../profil.html#tuPoksi">Tugas Pokok dan Fungsi</a>
		  </div>
		</div>
		
		<div class="dropdown">
			<button class="dropbtn">ARTIKEL <i class="fa fa-caret-down"></i></button>
		  <div id="menu2" class="dropdown-content">
			<a href="../news.php">Berita ASN</a>
			<a href="../blog.php">Blog ASN</a>
		  </div>
		</div>
		
		<a href="../layanan.html">LAYANAN</a>
		
		<div class="dropdown">
			<button class="dropbtn">TRANSPARANSI <i class="fa fa-caret-down"></i></button>
		  <div id="menu3" class="dropdown-content">
			<a href="perbup.php">Perbup</a>
			<a href="renstra.php">Rencana Stategis</a>
			<a href="renja.php">Rencana Kerja</a>
			<a href="iku.php">Indikator Kinerja Utama</a>
			<a href="casscad.php">Casscading</a>
			<a href="perkin.php">Perjanjian Kinerja</a>
			<a href="reaksi.php">Rencana Aksi</a>
			<a href="lapkin.php">Laporan Kinerja</a>
			<a href="sop.php">Standar Operasional Prosedur</a>
			<a href="rapbd.php">RAPBD</a>
			<a href="apbd.php">APBD</a>
			<a href="lppd.php">LPPD</a>
		  </div>
		</div>
		
		<a href="../ppid.html">P.P.I.D.</a>
		
		<div class="dropdown">
			<button class="dropbtn">GALERI <i class="fa fa-caret-down"></i></button>
		  <div id="menu4" class="dropdown-content">
			<a href="../galeri.html#foto">Album Foto</a>
			<a href="../galeri.html#video">Album Video</a>
			<a href="../galeri.html#tempMm">Template Multimedia BKPSDMD</a>
		  </div>
		</div>
		
		<a href="../pengumuman.php">PENGUMUMAN</a>
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------- CONTENT ----------------------------------->
<h1 style="text-align: center; margin-top:30px;">Rancangan Anggaran Pendapatan dan Belanja Daerah (RAPBD)</h1>

<?php while ($row = $result->fetch_assoc()): ?>
<table style="overflow-x:auto; width: 80%;">
	<tr>
		<th style="text-align:center; width: 20%;">Tipe Dokumen</th>
		<th style="text-align:center; width: 50%;">Judul</th>
		<th style="text-align:center;">Nomor</th>
		<th style="text-align:center;">Tahun</th>
		<th style="text-align:center; width: 20%;">Unduh</th>
	</tr>
	<tr>
		<td><?= htmlspecialchars($row['tipe_dokumen']) ?></td>
		<td><?= htmlspecialchars($row['judul']) ?></td>
		<td><?= htmlspecialchars($row['nomor']) ?></td>
		<td><?= htmlspecialchars($row['tahun']) ?></td>
		<td><a href="../cms/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" class="unduh" download><button class="btn"><i class="fa fa-download"></i> Unduh</button></a></td>
	</tr>
	
</table>
<iframe src="../cms/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" width="80%" height="500px"></iframe>
<?php endwhile; ?>

	
<!------------------- FOOTER ----------------------------------->
	
<div class="row">
  <div class="column first">
		<img src="../icon/BKPLogo.png" alt="Logo BKPSDMD">
	  <p style="text-align: center">Copyright © 2025.</p>
	  <p style="text-align: center">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin.</p> 
	  <p style="text-align: center">All Rights Reserved</p>
  </div>
	
  <div class="column second">
		<h3>Butuh Bantuan?</h3>
	  
		<p><a href="https://maps.app.goo.gl/idAZYTHVszUhSGRv8" target="_blank" class="Loc">
			<img src="../icon/sosmed/Loc.png" alt="Logo Loc" width="30px" style="float: left"></a> 
			Jl. Jendral Sudirman, No. 01, Kel. Pematang Kandis, Kec. Bangko, Kab. Merangin, Prov. Jambi - Indonesia | Kode Pos - 37313</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="wa">
			<img src="../icon/sosmed/WA.png" alt="Logo WA" width="30px" style="vertical-align:middle"></a> 
			+62851 5999 7813</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="em">
			<img src="../icon/sosmed/EM.png" alt="Logo Email" width="30px" style="vertical-align:middle"></a> 
			bkd.merangin@gmail.com</p>
  </div>
	
  <div class="column third">
		<h3>Follow Sosial Media Kami!</h3>
		  <a href="https://www.instagram.com/bkpsdmd.merangin/?hl=en" target="_blank" class="ig">
			<img src="../icon/sosmed/IG.png" alt="Logo IG"></a>
	  
		  <a href="https://www.youtube.com/@bkpsdmd.merangin" target="_blank" class="yt">
			<img src="../icon/sosmed/YT.png" alt="Logo YT"></a>
	  
		  <a href="https://www.facebook.com/bkpsdmd.merangin/" target="_blank" class="fb">
			<img src="../icon/sosmed/FB.png" alt="Logo FB"></a>
	  
		  <a href="https://x.com/bkpsdmdmerangin?t=a7RCgFHif89UfeV9aALj8g&s=08" target="_blank" class="x">
			<img src="../icon/sosmed/X.png" alt="Logo X"></a>
	  
		  <a href="https://www.tiktok.com/@bkpsdmd.merangin?_t=ZS-8z3dFdtzgYy&_r=1 " target="_blank" class="tt">
			<img src="../icon/sosmed/TT.png" alt="Logo TT"></a>
  </div>
  <div class="column fourth">
		<h3>Kunjungan Website</h3>
		<p>Hari Ini</p>
		<p>Total</p>
	  
	  	
	  <img src="../icon/BerAkhlak.png" alt="Logo BerAkhlak">
	  
  </div>
</div>

<!--<script> <h3 id="visitor-count">Loading...</h3>
  fetch("counter.php")
    .then(res => res.text())
    .then(count => {
      document.getElementById("visitor-count").innerText = count;
    });
</script>-->
	
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
	
<script src="../JavaScript/script.js"></script>
	
</body>
</html>
