<?php
include "CiviCore/db.php";
$result = $conn->query("SELECT * FROM infografis ORDER BY created_at DESC");

$ids = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
$idList = implode(",", $ids);

$sql = "SELECT id, caption FROM infografis WHERE id IN ($idList)";
$result = $conn->query($sql);

$captions = [];
while ($row = $result->fetch_assoc()) {
    $captions[$row['id']] = $row['caption'];
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
<link rel="shortcut icon" href="icon/IconWeb.png">
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
		<!--<a href="index.php"><img src="icon/LogoStart.png" id="LogoStart" alt="Logo Start Apps"></a>-->
		<a href="index.php"><img src="icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
	</div>
	
	<div class="navRight" >
		
		<div class="dropdown">
			<button class="dropbtn">PROFIL <i class="fa fa-caret-down"></i></button>
		  <div id="menu1" class="dropdown-content">
			<a href="profil.html#visiMisi">Visi dan Misi</a>
			<a href="profil.html#selaPang">Selayang Pandang</a>
			<a href="profil.html#sejarah">Sejarah</a>
			<a href="profil.html#strukOrga">Struktur Organisasi</a>
			<a href="profil.html#maklumat">Maklumat Pelayanan</a>
			<a href="profil.html#tuPoksi">Tugas Pokok dan Fungsi</a>
		  </div>
		</div>
		
		<div class="dropdown">
			<button class="dropbtn">ARTIKEL <i class="fa fa-caret-down"></i></button>
		  <div id="menu2" class="dropdown-content">
			<a href="news.php">Berita ASN</a>
			<a href="blog.php">Blog ASN</a>
		  </div>
		</div>
		
		<a href="layanan.html">LAYANAN</a>
		
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
		
		<a href="ppid.html">P.P.I.D.</a>
		
		<div class="dropdown">
			<button class="dropbtn">GALERI <i class="fa fa-caret-down"></i></button>
		  <div id="menu4" class="dropdown-content">
			<a href="galeri.html#foto">Album Foto</a>
			<a href="galeri.html#video">Album Video</a>
			<a href="galeri.html#tempMm">Template Multimedia BKPSDMD</a>
		  </div>
		</div>
		
		<a href="pengumuman.php">PENGUMUMAN</a>
		<a href="fungsional.php">POJOK FUNGSIONAL</a>
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------- CONTENT ----------------------------------->

<div class="infoGrafis">
	<h2>INFOGRAFIS</h2>

<div class="slideshow-container">
	
  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/1.png" alt="">
    <div class="caption"> <?php echo $captions[1]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/2.png" alt="">
    <div class="caption"><?php echo $captions[2]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/3.png" alt="">
    <div class="caption"><?php echo $captions[3]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/4.png" alt="">
    <div class="caption"><?php echo $captions[4]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/5.png" alt="">
    <div class="caption"><?php echo $captions[5]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/6.png" alt="">
    <div class="caption"><?php echo $captions[6]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/7.png" alt="">
    <div class="caption"><?php echo $captions[7]; ?> </div>
  </div>

  <div class="slides">
    <img src="CiviCore/infoGrafis/uploads/images/8.png" alt="">
    <div class="caption"><?php echo $captions[8]; ?> </div>
  </div>

  <div class="slides">
	<img src="CiviCore/infoGrafis/uploads/images/9.png" alt="">
	<div class="caption"><?php echo $captions[9]; ?> </div>
  </div>

  <div class="slides">
	<img src="CiviCore/infoGrafis/uploads/images/10.png" alt="">
	<div class="caption"><?php echo $captions[10]; ?> </div>
  </div>

  <div class="slides">
	<img src="CiviCore/infoGrafis/uploads/images/11.png" alt="">
	<div class="caption"><?php echo $captions[11]; ?> </div>
  </div>

  <div class="slides">
	<img src="CiviCore/infoGrafis/uploads/images/12.png" alt="">
	<div class="caption"><?php echo $captions[12]; ?> </div>
  </div>

</div>

	<!-- Dots -->
	<div class="dots">
	<span class="dot" onclick="currentSlide(1)"></span>
	<span class="dot" onclick="currentSlide(2)"></span>
	<span class="dot" onclick="currentSlide(3)"></span>
	<span class="dot" onclick="currentSlide(4)"></span>
	<span class="dot" onclick="currentSlide(5)"></span>
	<span class="dot" onclick="currentSlide(6)"></span>
	<span class="dot" onclick="currentSlide(7)"></span>
	<span class="dot" onclick="currentSlide(8)"></span>
	<span class="dot" onclick="currentSlide(9)"></span>
	<span class="dot" onclick="currentSlide(10)"></span>
	<span class="dot" onclick="currentSlide(11)"></span>
	<span class="dot" onclick="currentSlide(12)"></span>
	</div>

	<!-- Thumbnail navigation -->
	<div class="thumbnail-row">
	<img src="CiviCore/infoGrafis/uploads/images/1.png" onclick="currentSlide(1)">
	<img src="CiviCore/infoGrafis/uploads/images/2.png" onclick="currentSlide(2)">
	<img src="CiviCore/infoGrafis/uploads/images/3.png" onclick="currentSlide(3)">
	<img src="CiviCore/infoGrafis/uploads/images/4.png" onclick="currentSlide(4)">
	<img src="CiviCore/infoGrafis/uploads/images/5.png" onclick="currentSlide(5)">
	<img src="CiviCore/infoGrafis/uploads/images/6.png" onclick="currentSlide(6)">
	<img src="CiviCore/infoGrafis/uploads/images/7.png" onclick="currentSlide(7)">
	<img src="CiviCore/infoGrafis/uploads/images/8.png" onclick="currentSlide(8)">
	<img src="CiviCore/infoGrafis/uploads/images/9.png" onclick="currentSlide(9)">
	<img src="CiviCore/infoGrafis/uploads/images/10.png" onclick="currentSlide(10)">
	<img src="CiviCore/infoGrafis/uploads/images/11.png" onclick="currentSlide(11)">
	<img src="CiviCore/infoGrafis/uploads/images/12.png" onclick="currentSlide(12)">
	</div>
</div>

<div class="pidato-kaban">
	<h2> Sekapur Sirih<br><p>Kepala BKPSDMD Kabupaten Merangin</p></h2>
	<img src="images/Foto_Kaban.png" alt="Foto Kaban">
	<div class="isiPidato">	
		<p>&#10077;Assalamu'alaikum Warahmatullahi Wabarakaatuh,</p>
		<p>Puji syukur kita panjatkan ke hadirat Allah SWT, karena atas rahmat dan karunia-Nya, Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin dapat meluncurkan Website Resmi BKPSDMD Kabupaten Merangin sebagai salah satu sarana informasi dan pelayanan publik.</p>
		<p>Peluncuran website ini merupakan wujud komitmen kami dalam meningkatkan kualitas layanan kepegawaian serta pengembangan sumber daya aparatur secara lebih transparan, efektif, dan mudah diakses oleh seluruh ASN maupun masyarakat. Melalui platform ini, kami berharap seluruh informasi terkait kepegawaian, pengembangan kompetensi, maupun layanan administrasi dapat tersampaikan dengan lebih cepat, akurat, dan terbuka.</p>
		<p>Website ini juga menjadi langkah nyata dalam mendukung transformasi digital pemerintah daerah, sejalan dengan tuntutan era teknologi informasi yang semakin maju. Kami meyakini, dengan adanya media layanan berbasis digital ini, BKPSDMD Kabupaten Merangin akan mampu memberikan pelayanan yang lebih prima, modern, serta menjawab kebutuhan ASN dan masyarakat dengan lebih baik.</p>
		<p>Akhir kata, kami mengajak seluruh ASN dan masyarakat untuk memanfaatkan website ini sebaik-baiknya, serta memberikan masukan demi peningkatan kualitas layanan BKPSDMD di masa yang akan datang.</p>
		<p>Wassalamu'alaikum Warahmatullahi Wabarakaatuh.&#10078;</p>
	</div>
	<p>Merangin, 26 Agustus 2025<br>Kepala BKPSDM Kabupaten Merangin<br><b>H. Ferdi Firdaus Ansori, S.Sos., M.E.</b></p>
	
	
</div>
	
<div class="asn-rekap">
	<h2>STATISTIK<br><p>Rekapitulasi ASN Kabupaten Merangin</p></h2>
			
	<div class="chart-rekap">
		<div class="chart-donat" id="myPlot0"></div>
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
<script src="JavaScript/chart_rekap_asn.js"></script>
<script src="JavaScript/image_slides.js"></script>

</body>
</html>
