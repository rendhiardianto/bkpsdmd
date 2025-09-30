<?php
include "cms/db.php";
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

<link href="headerFooter.css" rel="stylesheet" type="text/css">
<link href="index.css" rel="stylesheet" type="text/css">

<title>Beranda - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="icon/IconWeb.png">
</head>

<body>
<div class="header">
	<video src="/videos/HeaderVideo2.mp4" width="100%" autoplay muted loop id="myVideo"></video>
	<!--<img src="images/HomeHeaderSS.jpg" width="100%" alt="Banner Home">-->
</div>
	
<div class="topnav" id="mynavBtn">

	<div class="startlogoDD">
		<button onclick="toggleStartMenu()" class="startbtn"><img src="icon/LogoStart.png"></button>
		<div id="myStart" class="start-content">
			<a href="cms/index.php" target="_blank"><img src="/icon/cms.png" width="20px"> Login CMS</a>
			<a href="#" target="_blank"><img src="/icon/fingerprint.png" width="20px"> MyPresensi</a>
			<a href="#" target="_blank"><img src="/icon/documents.png" width="20px"> MyDocuments</a>
			<a href="#" target="_blank"><img src="/icon/form.png" width="20px"> MyForm</a>
		</div>
	</div>
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
			<a href="transparansi/perbup.php">Perbup</a>
			<a href="transparansi/renstra.php">Rencana Stategis</a>
			<a href="transparansi/renja.php">Rencana Kerja</a>
			<a href="transparansi/iku.php">Indikator Kinerja Utama</a>
			<a href="transparansi/casscad.php">Casscading</a>
			<a href="transparansi/perkin.php">Perjanjian Kinerja</a>
			<a href="transparansi/reaksi.php">Rencana Aksi</a>
			<a href="transparansi/lapkin.php">Laporan Kinerja</a>
			<a href="transparansi/sop.php">Standar Operasional Prosedur</a>
			<a href="transparansi/rapbd.php">RAPBD</a>
			<a href="transparansi/apbd.php">APBD</a>
			<a href="transparansi/lppd.php">LPPD</a>
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
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------- CONTENT ----------------------------------->

<div class="infoGrafis">
	<h2>INFOGRAFIS</h2>

<div class="slideshow-container">
	
  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/1.png" alt="">
    <div class="caption"> <?php echo $captions[1]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/2.png" alt="">
    <div class="caption"><?php echo $captions[2]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/3.png" alt="">
    <div class="caption"><?php echo $captions[3]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/4.png" alt="">
    <div class="caption"><?php echo $captions[4]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/5.png" alt="">
    <div class="caption"><?php echo $captions[5]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/6.png" alt="">
    <div class="caption"><?php echo $captions[6]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/7.png" alt="">
    <div class="caption"><?php echo $captions[7]; ?> </div>
  </div>

  <div class="slides">
    <img src="cms/infoGrafis/uploads/images/8.png" alt="">
    <div class="caption"><?php echo $captions[8]; ?> </div>
  </div>

  <div class="slides">
	<img src="cms/infoGrafis/uploads/images/9.png" alt="">
	<div class="caption"><?php echo $captions[9]; ?> </div>
  </div>

  <div class="slides">
	<img src="cms/infoGrafis/uploads/images/10.png" alt="">
	<div class="caption"><?php echo $captions[10]; ?> </div>
  </div>

  <div class="slides">
	<img src="cms/infoGrafis/uploads/images/11.png" alt="">
	<div class="caption"><?php echo $captions[11]; ?> </div>
  </div>

  <div class="slides">
	<img src="cms/infoGrafis/uploads/images/12.png" alt="">
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
	<img src="cms/infoGrafis/uploads/images/1.png" onclick="currentSlide(1)">
	<img src="cms/infoGrafis/uploads/images/2.png" onclick="currentSlide(2)">
	<img src="cms/infoGrafis/uploads/images/3.png" onclick="currentSlide(3)">
	<img src="cms/infoGrafis/uploads/images/4.png" onclick="currentSlide(4)">
	<img src="cms/infoGrafis/uploads/images/5.png" onclick="currentSlide(5)">
	<img src="cms/infoGrafis/uploads/images/6.png" onclick="currentSlide(6)">
	<img src="cms/infoGrafis/uploads/images/7.png" onclick="currentSlide(7)">
	<img src="cms/infoGrafis/uploads/images/8.png" onclick="currentSlide(8)">
	<img src="cms/infoGrafis/uploads/images/9.png" onclick="currentSlide(9)">
	<img src="cms/infoGrafis/uploads/images/10.png" onclick="currentSlide(10)">
	<img src="cms/infoGrafis/uploads/images/11.png" onclick="currentSlide(11)">
	<img src="cms/infoGrafis/uploads/images/12.png" onclick="currentSlide(12)">
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
	<h2>STATISTIK<br><p>Rekapitulasi ASN Kabupaten Merangin (per Desember 2024)</p></h2>
	<!--style="font-size:2vw;"-->

		<div class="chart-rekap" style="justify-content:center">

			<div class="chart">
			<iframe width="325" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%204&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
			<div class="chart">
			<iframe width="325" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%202&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>

			<div class="chart">
			<iframe width="325" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%201&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>

			<div class="chart">
			<iframe width="535" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%205&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
			<div class="chart">
			<iframe width="535" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%209&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
			<div class="chart">
			<iframe width="327" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%207&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
			<div class="chart">
			<iframe width="458" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%2011&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
			<div class="chart">
			<iframe width="458" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%2010&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>

			<div class="chart">
			<iframe width="326" height="287" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQSO9Lmf51jOT78Wk8csCS7eAQ-61IXDUuLdtoS4tRr7DjU?em=2&wdAllowInteractivity=False&Item=Chart%208&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
			</div>
			
		<!--<canvas class="flex-item-half1" style="max-width:325px"></canvas>
		<canvas class="flex-item-half2" id="myChart2" style="max-width:300px"></canvas>	
		<canvas class="flex-item-half3" id="myChart3" style="max-width:300px"></canvas>
		<canvas class="flex-item-half4" id="myChart4" style="max-width:300px"></canvas>
		
		<canvas class="flex-item-half5" id="myChart5" style="max-width:300px"></canvas>	
		<canvas class="flex-item-half6" id="myChart6" style="max-width:300px"></canvas>	
		<canvas class="flex-item-half7" id="myChart7" style="max-width:300px"></canvas>
		<canvas class="flex-item-half8" id="myChart8" style="max-width:300px"></canvas>
		</div>
		<div class="maps-rekap">
		
		</div>-->
	</div>
</div>
<!------------------- FOOTER ----------------------------------->	
	
<div class="row">
  <div class="column first">
		<img src="icon/BKPLogo.png" alt="Logo BKPSDMD">
	  <p style="text-align: center">Copyright © 2025.</p>
	  <p style="text-align: center">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin.</p> 
	  <p style="text-align: center">All Rights Reserved</p>
  </div>
	
  <div class="column second">
		<h3>Butuh Bantuan?</h3>
	  
		<p><a href="https://maps.app.goo.gl/idAZYTHVszUhSGRv8" target="_blank" class="Loc">
			<img src="icon/sosmed/Loc.png" alt="Logo Loc" width="30px" style="float: left"></a> 
			Jl. Jendral Sudirman, No. 01, Kel. Pematang Kandis, Kec. Bangko, Kab. Merangin, Prov. Jambi - Indonesia | Kode Pos - 37313</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="wa">
			<img src="icon/sosmed/WA.png" alt="Logo WA" width="30px" style="vertical-align:middle"></a> 
			+62851 5999 7813</p>
	  
		<p><a href="bkd.merangin@gmail.com" target="_blank" class="em">
			<img src="icon/sosmed/EM.png" alt="Logo Email" width="30px" style="vertical-align:middle"></a> 
			bkd.merangin@gmail.com</p>
  </div>
	
  <div class="column third">
		<h3>Follow Sosial Media Kami!</h3>
		  <a href="https://www.instagram.com/bkpsdmd.merangin/?hl=en" target="_blank" class="ig"><img src="icon/sosmed/IG.png" alt="Logo IG"></a>
	  
		  <a href="https://www.youtube.com/@bkpsdmd.merangin" target="_blank" class="yt"><img src="icon/sosmed/YT.png" alt="Logo YT"></a>
	  
		  <a href="https://www.facebook.com/bkpsdmd.merangin/" target="_blank" class="fb"><img src="icon/sosmed/FB.png" alt="Logo FB"></a>
	  
		  <a href="https://x.com/bkpsdmdmerangin?t=a7RCgFHif89UfeV9aALj8g&s=08" target="_blank" class="x"><img src="icon/sosmed/X.png" alt="Logo X"></a>
	  
		  <a href="https://www.tiktok.com/@bkpsdmd.merangin?_t=ZS-8z3dFdtzgYy&_r=1 " target="_blank" class="tt"><img src="icon/sosmed/TT.png" alt="Logo TT"></a>
  </div>
  <div class="column fourth">
		<h3>Kunjungan Website</h3>
		<p>Hari Ini</p>
		<p>Total</p>
	  
	  	
	  <img src="icon/BerAkhlak.png" alt="Logo BerAkhlak">
	  
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

<script src="JavaScript/script.js"></script>

<script>
  let slideIndex = 1;
  let slideTimer;

  function showSlides(n) {
    let slides = document.getElementsByClassName("slides");
    let dots = document.getElementsByClassName("dot");
    let thumbs = document.querySelectorAll(".thumbnail-row img");

    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }

    for (let i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }

    for (let i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }

    thumbs.forEach(img => img.classList.remove("active-thumb"));

    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
    thumbs[slideIndex-1].classList.add("active-thumb");
  }

  function plusSlides(n) {
    clearInterval(slideTimer);
    slideIndex += n;
    showSlides(slideIndex);
    autoSlide();
  }

  function currentSlide(n) {
    clearInterval(slideTimer);
    slideIndex = n;
    showSlides(slideIndex);
    autoSlide();
  }

  function autoSlide() {
    slideTimer = setInterval(() => {
      slideIndex++;
      if (slideIndex > document.getElementsByClassName("slides").length) { slideIndex = 1 }
      showSlides(slideIndex);
    }, 4000); // 4 seconds
  }

  // Initialize
  showSlides(slideIndex);
  autoSlide();
</script>

</body>
</html>
