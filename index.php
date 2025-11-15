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

$sql = "SELECT id, caption FROM infografis ORDER BY created_at ASC";
$result = $conn->query($sql);

$slides = [];
while ($row = $result->fetch_assoc()) {
    $slides[] = $row;
}
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
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

<link rel="stylesheet" href="/fontFamily.css">
<link href="headerFooter.css" rel="stylesheet" type="text/css">
<link href="index.css" rel="stylesheet" type="text/css">

<title>Beranda - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="/icon/IconWeb.png">
</head>

<body>
<div class="header">
	<video src="videos/HeaderVideo2.mp4" width="100%" autoplay muted loop id="myVideo"></video>
	<!--<img src="images/HomeHeaderSS.jpg" width="100%" alt="Banner Home">-->
</div>
	
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
	
<!------------------- CONTENT ----------------------------------->

<div class="infoGrafis">
    <h2>INFOGRAFIS</h2>

    <div class="slideshow-container">
    <?php foreach ($slides as $index => $row): ?>
        <div class="slides">
            <img src="CiviCore/infoGrafis/uploads/images/<?php echo $row['id']; ?>.png" alt="">
            <div class="caption"><?php echo $row['caption']; ?></div>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Dots -->
    <div class="dots">
    <?php foreach ($slides as $index => $row): ?>
        <span class="dot" onclick="currentSlide(<?php echo $index + 1; ?>)"></span>
    <?php endforeach; ?>
    </div>

    <!-- Thumbnails -->
    <div class="thumbnail-row">
    <?php foreach ($slides as $index => $row): ?>
        <img src="CiviCore/infoGrafis/uploads/images/<?php echo $row['id']; ?>.png"
             onclick="currentSlide(<?php echo $index + 1; ?>)">
    <?php endforeach; ?>
    </div>
</div>

<section id="sambutan-kaban" class="sambutan-wrapper">
	<header>
		<img src="/icon/bkpsdmd_logo_resized.jpg" alt="Logo BKPSDMD">
		<div class="title-sambutan">
			<h2>SEKAPUR SIRIH</h2>
			<p class="subheading">Kepala BKPSDMD Kabupaten Merangin</p>
		</div>
	</header>
	<div class="isiPidato" style="font-style: italic; margin-top: 40px;">
		<p>&#10077;Assalamu'alaikum Warahmatullahi Wabarakaatuh,</p>
		<p>Puji syukur kita panjatkan ke hadirat Allah SWT, karena atas rahmat dan karunia-Nya, Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin dapat meluncurkan Website Resmi BKPSDMD Kabupaten Merangin sebagai salah satu sarana informasi dan pelayanan publik.</p>
		<p>Peluncuran website ini merupakan wujud komitmen kami dalam meningkatkan kualitas layanan kepegawaian serta pengembangan sumber daya aparatur secara lebih transparan, efektif, dan mudah diakses oleh seluruh ASN maupun masyarakat. Melalui platform ini, kami berharap seluruh informasi terkait kepegawaian, pengembangan kompetensi, maupun layanan administrasi dapat tersampaikan dengan lebih cepat, akurat, dan terbuka.</p>
		<p>Website ini juga menjadi langkah nyata dalam mendukung transformasi digital pemerintah daerah, sejalan dengan tuntutan era teknologi informasi yang semakin maju. Kami meyakini, dengan adanya media layanan berbasis digital ini, BKPSDMD Kabupaten Merangin akan mampu memberikan pelayanan yang lebih prima, modern, serta menjawab kebutuhan ASN dan masyarakat dengan lebih baik.</p>
		<p>Akhir kata, kami mengajak seluruh ASN dan masyarakat untuk memanfaatkan website ini sebaik-baiknya, serta memberikan masukan demi peningkatan kualitas layanan BKPSDMD di masa yang akan datang.</p>
		<p>Wassalamu'alaikum Warahmatullahi Wabarakaatuh.&#10078;</p>
	</div>
	<footer class="ttd" style="margin-top: 50px;">
		<p>Merangin, 26 Agustus 2025</p>
		<p>Kepala BKPSDMD Kabupaten Merangin</p>
		<p><strong>H. Ferdi Firdaus Ansori, S.Sos., M.E.</strong></p>
	</footer>
</section>

<div class="shortCut">
	<h1>LAYANAN ASN</h1>

	<div class="flex-container">

		<div class="flex-item-main">
			<a href="layanan/jafung/index.php" target="_blank">
			<img src="icon/Kepegawaian/fungsional.png" ></a>
			<br>KENAIKAN JABATAN FUNGSIONAL
		</div>

		<div class="flex-item-main">
			<a href="/images/LayananKPG/PERSYARATAN KENAIKAN PANGKAT 2025.pdf" target="_blank" >
			<img src="icon/Kepegawaian/kp.png" ></a>
			<br>KENAIKAN PANGKAT
		</div>
		
		<div class="flex-item-main">
			<a href="/images/LayananKPG/gajiBerkala.PNG" target="_blank" >
			<img src="icon/Kepegawaian/gaji.png"></a>
			<br>GAJI BERKALA
		</div>
		
		<div class="flex-item-main">
			<a href="/images/LayananKPG/pmk.PNG" target="_blank" >
			<img src="icon/Kepegawaian/pmk.png" ></a>
			<br>PENINJAUAN MASA KERJA (PMK)
		</div>
		
		<div class="flex-item-main">
			<a href="/images/LayananKPG/SYARAT PENSIUN.pdf" target="_blank" >
			<img src="icon/Kepegawaian/pensiun.png" ></a>
			<br>PENSIUN
		</div>

		<div class="flex-item-main">
			<a href="#" target="_blank" >
			<img src="icon/PSDM/disiplin.png" ></a>
			<br>HUKUMAN DISIPLIN
		</div>
		
		<div class="flex-item-main">
			<a href="https://docs.google.com/forms/d/e/1FAIpQLSfpoV6Utd9lg3KWp-nV3JX29kooy4i-g3liowmISEnUAvUMCg/viewform" target="_blank" >
			<img src="icon/PSDM/tubel.png" ></a>
			<br>TUGAS BELAJAR
		</div>
		
		<div class="flex-item-main">
			<a href="https://bioqu.id/SICUTI" target="_blank" >
			<img src="icon/PSDM/sakit.png"></a>
			<br>CUTI
		</div>
    </div>
</div>

<div class="asn-rekap">
	<h2>STATISTIK</h2>
	<p>Rekapitulasi ASN Kabupaten Merangin</p>

	<div class="chart-rekap">
		<div class="myPlotMain">
			<div class="chart-donat" id="myPlot0"></div>
		</div>
		<div class="chart-donat" id="myPlot1"></div>
		<div class="chart-donat" id="myPlot2"></div>
		<div class="chart-donat" id="myPlot3"></div>

		<div class="chart-bar" id="myPlot100"></div>
		<div class="chart-bar" id="myPlot99"></div>
		<div class="chart-bar" id="myPlot98"></div>
		<div class="chart-bar" id="myPlot97"></div>
		<div class="chart-bar" id="myPlot96"></div>
		<div class="chart-bar" id="myPlot95"></div>
    </div>
</div>

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
<script src="JavaScript/image_slides.js"></script>
<script src="JavaScript/chart_rekap_asn.js"></script>

</body>
</html>
