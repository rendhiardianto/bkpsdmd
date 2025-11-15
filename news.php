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

// Fetch latest news
$result = $conn->query("SELECT * FROM news WHERE visible = 1 ORDER BY created_at DESC LIMIT 10");
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
  <title>Berita - BKPSDMD Kab. Merangin</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  
  <link rel="stylesheet" href="/fontFamily.css">
  <link href="headerFooter.css" rel="stylesheet" type="text/css">
  <link href="news.css" rel="stylesheet" type="text/css">

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

<header>
  <h2>Berita Kepegawaian dan Pengembangan SDM Kab. Merangin</h2>
</header>

<div class="container">
  <!-- News List -->
  <div class="news-list">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="news-item">

        <img src="CiviCore/berita/<?php echo $row['image']; ?>" alt="News">

        <div class="news-content">
          <h2><?php echo $row['title']; ?></h2>
          <p><?php echo substr($row['content'], 0, 120) . "..."; ?></p>
          <!--<a href="CiviCore/berita/news_detail.php?id=<?php echo $row['id']; ?>" target="_blank">Read More</a>-->
		  <a href="berita/news_detail.php?slug=<?php echo urlencode($row['slug']); ?>" target="_blank">Baca selengkapnya</a>

        </div>
      </div>
    <?php endwhile; ?>
  </div>
  
  <!-- Sidebar -->
  <div class="sidebar">
    <h3>Berita Terkini</h3>
    <ul>
      <?php
      $latest = $conn->query("SELECT slug, title FROM news WHERE visible = 1 ORDER BY created_at DESC LIMIT 5");
      while ($n = $latest->fetch_assoc()):
      ?>
        <li><a href="berita/news_detail.php?slug=<?php echo $n['slug']; ?>" target="_blank"><?php echo $n['title']; ?></a></li>
      <?php endwhile; ?>
    </ul>

    <h3>Kategori Berita</h3>
    <ul>
      <?php
      $cats = $conn->query("SELECT DISTINCT category FROM news");
      while ($c = $cats->fetch_assoc()):
      ?>
        <li><a href="news.php?category=<?php echo urlencode($c['category']); ?>"> <?php echo $c['category']; ?></a></li>
      <?php endwhile; ?>
    </ul>
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

</body>
</html>
