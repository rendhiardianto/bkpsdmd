<?php
include "CiviCore/db.php";

// Update New Pengumuman Badge Logic
// 1. Query all announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

// Check if there is NEW announcement within last 3 days
$newCheck = $conn->query("
    SELECT id 
    FROM announcements 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)
    LIMIT 1
");
$hasNew = $newCheck->num_rows > 0;

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

<link rel="stylesheet" href="/fontFamily.css">
<link href="/headerFooter.css" rel="stylesheet" type="text/css">
<link href="/layanan.css" rel="stylesheet" type="text/css">

<title>Layanan - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="icon/IconWeb.png">
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
		<a href="index.php"><img src="icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
	</div>
	
	<div class="navRight" >
		<div class="dropdown">
			<button class="dropbtn">PROFIL <i class="fa fa-caret-down"></i></button>
		  <div id="menu1" class="dropdown-content">
			<a href="profil.php#visiMisi">Visi dan Misi</a>
			<a href="profil.php#selaPang">Selayang Pandang</a>
			<a href="profil.php#sejarah">Sejarah</a>
			<a href="profil.php#strukOrga">Struktur Organisasi</a>
			<a href="profil.php#maklumat">Maklumat Pelayanan</a>
			<a href="profil.php#tuPoksi">Tugas Pokok dan Fungsi</a>
		  </div>
		</div>
		
		<div class="dropdown">
			<button class="dropbtn">ARTIKEL <i class="fa fa-caret-down"></i></button>
		  <div id="menu2" class="dropdown-content">
			<a href="news.php">Berita ASN</a>
			<a href="blog.php">Blog ASN</a>
		  </div>
		</div>
		
		<a href="layanan.php">LAYANAN</a>
		
		<div class="dropdown">
			<button class="dropbtn">TRANSPARANSI <i class="fa fa-caret-down"></i></button>
		<div id="menu3" class="dropdown-content">
			<a href="transparansi.php?tipe=perbup">Perbup</a>
			<a href="transparansi.php?tipe=renstra">Rencana Strategis</a>
			<a href="transparansi.php?tipe=renja">Rencana Kerja</a>
			<a href="transparansi.php?tipe=iku">Indikator Kinerja Utama</a>
			<a href="transparansi.php?tipe=casscad">Casscading</a>
			<a href="transparansi.php?tipe=perkin">Perjanjian Kinerja</a>
			<a href="transparansi.php?tipe=reaksi">Rencana Aksi</a>
			<a href="transparansi.php?tipe=lapkin">Laporan Kinerja</a>
			<a href="transparansi.php?tipe=sop">Standar Operasional Prosedur</a>
			<a href="transparansi.php?tipe=rapbd">RAPBD</a>
			<a href="transparansi.php?tipe=apbd">APBD</a>
			<a href="transparansi.php?tipe=lppd">LPPD</a>
		</div>
		</div>
		
		<a href="ppid.php">P.P.I.D.</a>
		
		<div class="dropdown">
			<button class="dropbtn">GALERI <i class="fa fa-caret-down"></i></button>
		  <div id="menu4" class="dropdown-content">
			<a href="galeri.php#foto">Album Foto</a>
			<a href="galeri.php#video">Album Video</a>
			<a href="galeri.php#tempMm">Template Multimedia BKPSDMD</a>
		  </div>
		</div>
		
		<a href="pengumuman.php" class="nav-announcement">
			PENGUMUMAN
			<?php if ($hasNew): ?>
				<span class="new-badge">NEW</span>
			<?php endif; ?>
		</a>

		<a href="fungsional.php">POJOK FUNGSIONAL</a>
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------------------------------ CONTENT ----------------------------------->
<div class="content">

	<div class="layananBKD">
		<div class="KPG">
			<h1>Layanan Kepegawaian</h1>
			<div class="flex-container">
				
				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/Surat Syarat Usul JF.pdf" target="_blank">
					<img src="icon/Kepegawaian/fungsional.png" ></a><br>KENAIKAN JABATAN FUNGSIONAL<br></p>
					<button class="ajukanButton" onclick="window.open('layanan/jafung/index.php', '_self')">Ajukan</button>
				</div>
				
				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/PERSYARATAN KENAIKAN PANGKAT 2025.pdf" target="_blank" >
					<img src="icon/Kepegawaian/kp.png" ></a><br>KENAIKAN PANGKAT<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/jabatanPelaksana.PNG" target="_blank" >
					<img src="icon/Kepegawaian/pelaksana.png"></a><br>MUTASI JABATAN PELAKSANA<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/gajiBerkala.PNG" target="_blank" >
					<img src="icon/Kepegawaian/gaji.png"></a><br>GAJI BERKALA<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/pmk.PNG" target="_blank" >
					<img src="icon/Kepegawaian/pmk.png" ></a><br>PENINJAUAN MASA KERJA (PMK)<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/SYARAT PENSIUN.pdf" target="_blank" >
					<img src="icon/Kepegawaian/pensiun.png" ></a><br>PENSIUN<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/mutasi.PNG" target="_blank" >
					<img src="icon/Kepegawaian/mutasi.png" ></a><br>MUTASI<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="/images/LayananKPG/gelarPendidikan.PNG" target="_blank" >
					<img src="icon/Kepegawaian/gelar.png" ></a><br>PEMAKAIAN GELAR PENDIDIKAN<br></p>
				</div>

				<div class="flex-item-main">
					<p><a href="https://asndigital.bkn.go.id/" target="_blank" >
					<img src="icon/Kepegawaian/karpeg.png"></a><br>KARTU PEGAWAI<br></p>
				</div>
				
				<div class="flex-item-main">
					<p><a href="https://asndigital.bkn.go.id/" target="_blank" >
					<img src="icon/Kepegawaian/karsu.png"></a><br>KARTU SUAMI/ISTRI<br></p>
				</div>

			</div> <!--Flex-ContainerClose-->
		</div> <!--KPGClose-->

		<div class="PSDM">
			<h1>Layanan Pengembangan SDM</h1>
			<div class="flex-container">
				<div class="flex-item-main"><p><a href="#" target="_blank" >
					<img src="icon/PSDM/tapera.png" ></a><br>TAPERA<br><sub>Permohonan Pengajuan TAPERA</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="#" target="_blank" >
					<img src="icon/PSDM/cerai.png" ></a><br>IZIN PERCERAIAN<br><sub>Permohonan Pengajuan Izin Cerai</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="https://docs.google.com/forms/d/e/1FAIpQLSfpoV6Utd9lg3KWp-nV3JX29kooy4i-g3liowmISEnUAvUMCg/viewform" target="_blank" >
					<img src="icon/PSDM/tubel.png" ></a><br>TUGAS BELAJAR<br><sub>Permohonan Pengajuan Tubel</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="https://docs.google.com/forms/d/e/1FAIpQLSeP28qWj-p13n_71Uc4OgocsIH5tvFv5AoroTAl4BKuSSfUQw/viewform" target="_blank" >
					<img src="icon/PSDM/satya.png" ></a><br>SATYALANCANA<br><sub>Permohonan Usul Satyalancana</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="#" target="_blank" >
					<img src="icon/PSDM/disiplin.png" ></a><br>HUKUMAN DISIPLIN<br><sub>Permohonan Bebas Hukuman Disiplin</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="#" target="_blank" >
					<img src="icon/PSDM/ujiandinas.png" ></a><br>UJIAN DINAS<br><sub>Informasi Mengenai Ujian Dinas</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="https://bioqu.id/SICUTI" target="_blank" >
					<img src="icon/PSDM/sakit.png"></a><br>CUTI<br><sub>Permohonan Cuti</sub></p>
				</div>

				<div class="flex-item-main"><p><a href="layanan/PSDM/historyDiklat.html">
					<img src="icon/PSDM/diklat.png"></a><br>DIKLAT<br><sub>Informasi Mengenai Diklat</sub></p>
				</div>
			</div>
		</div> <!--PSDClose-->

	</div> <!--layananBKDClose-->

	<div class="layananBKN">
		<h1>Layanan BKN</h1>
		<div class="flex-container">

			<div class="flex-item-main"><p><a href="https://asndigital.bkn.go.id/" target="_blank" class="asndigital">
				<img src="icon/BKN/LogoBKN.png" alt="Logo BKN"></a><br>ASN DIGITAL<br><sub>Portal ASN Digital</sub></p>
			</div>

			<div class="flex-item-main"><p><a href="https://myasn.bkn.go.id/" target="_blank" class="myasn">
				<img src="icon/BKN/MyASN.png" alt="Logo BKN"></a><br>MyASN BKN<br><sub>Sistem Kepegawaian Bagi ASN</sub></p>
			</div>

			<div class="flex-item-main"><p><a href="https://kinerja.bkn.go.id/" target="_blank" class="ekinerja">
				<img src="icon/BKN/E-Kinerja.png" alt="Logo BKN"></a><br>E-KINERJA BKN<br><sub>Sistem Penilaian Kinerja BKN</sub></p>
			</div>

			<div class="flex-item-main"><p><a href="https://siasn-instansi.bkn.go.id/" target="_blank" class="siasn">
				<img src="icon/BKN/SIASN.png" alt="Logo BKN"></a><br>SIASN BKN<br><sub>Sistem Informasi ASN Pemerintah Daerah</sub></p>
			</div>

			<div class="flex-item-main"><p><p><a href="https://perencanaan-siasn.bkn.go.id/" target="_blank" class="hrsiasn">
				<img src="icon/BKN/SIASN.png" alt="Logo BKN"></a><br>HR SIASN<br><sub>Perencanaan Kebutuhan ASN</sub></p>
			</div>

			<div class="flex-item-main"><p><a href="https://imut.bkn.go.id/" target="_blank" class="imutbkn">
				<img src="icon/BKN/IMut.png" alt="Logo BKN"></a><br>I-MUT BKN<br><sub>Integrasi Mutasi ASN</sub></p>
			</div>

			<div class="flex-item-main"><p><a href="https://sscasn.bkn.go.id/" target="_blank" class="imutbkn">
				<img src="icon/BKN/SSCASN.png" alt="Logo BKN"></a><br>SSCASN BKN<br><sub>Sistem Seleksi Calon Aparatur Sipil Negara</sub></p>
			</div>

		</div>
	</div> <!--layananBKNClose-->

</div> <!--contentClose-->
<!------------------- FOOTER ----------------------------------->	
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top"><img src="icon/go_to_top.png"></div>
<script src="JavaScript/back_to_top.js"></script>

<div id="footer"></div>
<script>
fetch("footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->
	
<script src="JavaScript/script.js"></script>

<script>
function openNav() {
  document.getElementById("myNav").style.height = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.height = "0%";
}
</script>
</body>
</html>
