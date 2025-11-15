<?php
include "CiviCore/db.php";

// Main list
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

// Check if there is NEW announcement within last 3 days
$newCheck = $conn->query("
    SELECT id 
    FROM announcements 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)
    LIMIT 1
");
$hasNew = $newCheck->num_rows > 0;

// Sidebar - latest 5
$latest = $conn->query("
    SELECT id, title, created_at 
    FROM announcements 
    ORDER BY created_at DESC 
    LIMIT 5
");

// Filter by year and month
if (isset($_GET["year"]) && isset($_GET["month"])) {
    $year = intval($_GET["year"]);
    $month = intval($_GET["month"]);

    $result = $conn->query("
        SELECT * 
        FROM announcements
        WHERE YEAR(created_at) = $year
        AND MONTH(created_at) = $month
        ORDER BY created_at DESC
    ");
}

// Sidebar - archives (group by year and month)
$archives = $conn->query("
    SELECT 
        YEAR(created_at) AS year,
        MONTH(created_at) AS month,
        COUNT(*) AS total 
    FROM announcements
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at) DESC, MONTH(created_at) DESC
");

$archiveData = [];
while ($a = $archives->fetch_assoc()) {
    $archiveData[$a['year']][] = $a;
}


function indoDate($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $time = strtotime($date);
    $bln = $bulan[intval(date("m", $time))];
    return date("j", $time) . " $bln " . date("Y", $time);
}

function indoDateTime($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $time = strtotime($date);
    $bln = $bulan[intval(date("m", $time))];
    return date("j", $time) . " $bln " . date("Y", $time) . ", " . date("H:i", $time);
}

$indoMonth = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];
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
<!-- Floating Button (Mobile Only) -->
<button class="sidebar-float-btn" onclick="toggleSidebarMobile()">
    ☰ Pengumuman
</button>

<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay" class="sidebar-overlay" onclick="toggleSidebarMobile()"></div>

<div class="content">
  <div class="pengumuman">
		<h1>Pengumuman BKPSDMD Kabupaten Merangin</h1>

        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if ($row['thumbnail']): ?>
              <img src="CiviCore/pengumuman/uploads/thumbnails/<?= $row['thumbnail'] ?>" alt="Thumbnail">
            <?php endif; ?>
            
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p style="font-family: Roboto-Light;">Dipublish oleh: <?= $row['created_by'] ?> | <?= indoDateTime($row['created_at']) ?></p>
            <hr>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <?php if ($row['attachment']): ?>
				    <iframe src="CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" width="100%" height="300px"></iframe>

            <div class="share-box">
              <a class="download" style="float: left;" href="CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" download>
              <i class="fa fa-download"></i> Download</a>

              <?php 
                $shareLink = "https://".$_SERVER['HTTP_HOST']."/pengumuman/detail.php?id=".$row['id'];
              ?>
                <div style="margin-left: auto; display: flex; align-items: center;">
                  Bagikan:
                  <a href="https://wa.me/?text=<?= urlencode($row['title'] . ' | Pengumuman diakses melalui link berikut: ' . $shareLink) ?>" target="_blank" class="share-btn wa">
                    <image src="/icon/Whatsapp2.png" alt="WhatsApp">
                  </a>
                  <button class="share-btn copy" onclick="copyShareLink('<?= $shareLink ?>')">
                    <image src="/icon/copy.png" alt="Copy Link">
                  </button>
                </div><!--share-icon-->
            </div><!--share-box-->
            <?php endif; ?>
        </div><!--card-->
        <?php endwhile; ?> 
    <div id="announcementList"></div>
  </div><!--pengumuman-->

	<aside id="mobileSidebar" class="sidebar">

        <div class="sidebar-box">
            <h3>Pengumuman Terbaru</h3>
            <ul>
                <?php while ($l = $latest->fetch_assoc()): ?>
                    <li>
                        <a href="/pengumuman/detail.php?id=<?= $l['id'] ?>">
                            <?= htmlspecialchars($l['title']) ?>
                        </a> | <small><?= indoDate($l['created_at']) ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="sidebar-box archive-box">
          <h3>Arsip Pengumuman</h3>

          <div class="archive-accordion">
              <?php foreach ($archiveData as $year => $months): ?>
                  <div class="archive-year">
                      <button class="year-btn">
                          <?= $year ?> <span class="arrow">▼</span>
                      </button>

                      <ul class="month-list">
                          <?php foreach ($months as $m): ?>
                              <li>
                                  <a href="pengumuman.php?year=<?= $year ?>&month=<?= $m['month'] ?>">
                                      <?= $indoMonth[$m['month']] ?> (<?= $m['total'] ?>)
                                  </a>
                              </li>
                          <?php endforeach; ?>
                      </ul>
                  </div>
              <?php endforeach; ?>
          </div>
      </div>

    </aside>

</div><!-- CONTENT CLOSE-->
	
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
<script>
function copyShareLink(link) {
    navigator.clipboard.writeText(link).then(() => {
        alert("Link berhasil disalin!");
    });
}
</script>
<script>
function toggleSidebarMobile() {
    const sidebar = document.getElementById("mobileSidebar");
    const overlay = document.getElementById("mobileSidebarOverlay");

    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
}
</script>
<script>
document.querySelectorAll(".year-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        let list = this.nextElementSibling;

        // Toggle open
        if (list.style.maxHeight) {
            list.style.maxHeight = null;
            this.querySelector(".arrow").style.transform = "rotate(0deg)";
        } else {
            list.style.maxHeight = list.scrollHeight + "px";
            this.querySelector(".arrow").style.transform = "rotate(180deg)";
        }
    });
});
</script>

</body>
</html>
