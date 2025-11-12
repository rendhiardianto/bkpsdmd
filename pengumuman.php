<?php
include "CiviCore/db.php";
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
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
<link href="pengumuman.css" rel="stylesheet" type="text/css">

<title>Pengumuman - BKPSDMD Kabupaten Merangin</title>
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
		<a href="fungsional.php">POJOK FUNGSIONAL</a>
		<!--<a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fa fa-bars"></i> </a>-->
		<a href="javascript:void(0);" style="font-size:17px;" class="icon" onclick="toggleNav()">&#9776;</a>
	</div>
</div>
	
<!------------------- CONTENT ----------------------------------->

<div class="pengumuman">

    <div class="content">
        <h1>Pengumuman BKPSDMD Kabupaten Merangin</h1>

        <?php while ($row = $result->fetch_assoc()): ?>

        <div class="card">
            <?php if ($row['thumbnail']): ?>
              <img src="CiviCore/pengumuman/uploads/thumbnails/<?= $row['thumbnail'] ?>" alt="Thumbnail">
            <?php endif; ?>
            
            <h2 style="font-family:Raleway-Medium"><?= htmlspecialchars($row['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <?php if ($row['attachment']): ?>
				<iframe src="CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" width="100%" height="400px"></iframe><br>
              <br><a class="download" style="float: left;" href="CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" download>📄 Unduh lampiran</a>
            <?php endif; ?>

            <small style="float: right; font-family:Raleway-Medium">Dipublish oleh: <?= $row['created_by'] ?> | <?php echo date("j F Y, H:i", strtotime($row['created_at'])); ?></small>
        </div>

        <?php endwhile; ?>  
        <div id="announcementList"></div>
        
      </div>

    </div><!-- CONTENT CLOSE-->

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
	
<script src="JavaScript/script.js"></script>

<script>
  function loadPublicAnnouncements(page=1) {
    let search = $("#search").val();
    let filter = $("#filter").val();
    $.post("ajax/ajax_load_public_announcements.php", { page: page, search: search, filter: filter }, function(data){
      $("#announcementList").html(data);
    });
  }

  $("#search, #filter").on("input change", function(){
    loadPublicAnnouncements(1);
  });

  $(document).on("click", ".page-btn", function(){
    let page = $(this).data("page");
    loadPublicAnnouncements(page);
  });

  $(document).ready(function(){ loadPublicAnnouncements(); });
</script>

</body>
</html>
