<?php
include "../CiviCore/db.php";

// Directly set the value you want:
$tipe_dokumen = 'Rencana Kerja';

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
	<div id="startButton"></div>
	<script>
	fetch("startButton.html")
		.then(response => response.text())
		.then(data => {
			document.getElementById("startButton").innerHTML = data;
		});
	</script>
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
		<a href="../fungsional.php">POJOK FUNGSIONAL</a>
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------- CONTENT ----------------------------------->
<h1 style="text-align: center; margin-top:30px;">Rencana Kerja (RENJA)</h1>

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
		<td><a href="../CiviCore/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" class="unduh" download><button class="btn"><i class="fa fa-download"></i> Unduh</button></a></td>
	</tr>
	
</table>
<iframe src="../CiviCore/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" width="80%" height="500px"></iframe>
<?php endwhile; ?>

	
<!------------------- FOOTER ----------------------------------->	
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top"> <img src="../icon/go_to_top.png"></div>

<div id="footer"></div>
<script>
fetch("footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
	
<script src="../JavaScript/script.js"></script>
	
</body>
</html>
