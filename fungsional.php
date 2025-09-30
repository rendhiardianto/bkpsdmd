<?php
session_start();

include "cms/db.php";
include "cms/auth.php";

// --- Pagination setup ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- Search & Filter ---
$where = "1=1";

// allowed columns to sort by (whitelist)
$allowedSort = ['id','jabatan','total'];

// default sort
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'asc' : 'desc';


if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (jabatan LIKE '%$search%')";
}

// --- Count total users ---
$countResult = $conn->query("SELECT COUNT(*) AS total FROM pojafung WHERE $where");
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// --- Fetch users for the table ---
$result = $conn->query(
  "SELECT id, jabatan, total, link
   FROM pojafung
   WHERE $where
   ORDER BY $sort $order
   LIMIT $offset, $limit"
);


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
<link href="fungsional.css" rel="stylesheet" type="text/css">

<title>POJOK FUNGSIONAL - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="icon/IconWeb.png">
</head>

<body>
	
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

<div class="header">
	<h1 class="title">POJOK JABATAN FUNGSIONAL</h1>
	<h2 class="subtitle">Di Lingkungan Pemkab Merangin</h2>
</div>
<div class="content">
	<h2>FUNGSI DAN TUGAS POKOK JABATAN FUNGSIONAL</h2>
	<p>Jabatan Fungsional adalah jabatan yang menunjukkan tugas, tanggung jawab, wewenang, dan hak seorang PNS yang ditetapkan berdasarkan keahlian atau keterampilan tertentu sesuai dengan peraturan perundang-undangan.</p>		
</div>

<div class="chart-rekap" style="justify-content:center">
	<div class="chart">
		<iframe width="410" height="318" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQShXug2siZVSLzbdzzg_AU4AefMpc9IxBAgRsaKK8bGcyY?em=2&wdAllowInteractivity=False&Item=Chart%202&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
		<iframe width="520" height="318" frameborder="0" scrolling="no" src="https://1drv.ms/x/c/8ef122d5280ec801/IQShXug2siZVSLzbdzzg_AU4AefMpc9IxBAgRsaKK8bGcyY?em=2&wdAllowInteractivity=False&Item=Chart%201&wdDownloadButton=True&wdInConfigurator=True&wdInConfigurator=True"></iframe>
	</div>
</div> <!-- -->

<div class="top-bar">
    <form id="filterForm">
      <input type="text" name="search" id="search" placeholder="Cari berdasarkan jabatan">
      <button type="submit">Cari</button>
    </form>
  </div>

  <div class="tableJafung">

    <table id="userTable" border="1" width="100%" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
      <thead>
    <tr style="background:#3498db; color:white; height:50px;">
      <th>
        <?php
          $p = $_GET; $p['sort']='id'; $p['order'] = ($sort=='id' && $order=='asc') ? 'desc' : 'asc'; $p['page']=1;
          $url = '?'.http_build_query($p);
        ?>
        <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:white;">Nomor <?php if($sort=='id') echo $order=='asc' ? '▲' : '▼'; ?></a>
      </th>

      <th>
        <?php
          $p = $_GET; $p['sort']='jabatan'; $p['order'] = ($sort=='jabatan' && $order=='asc') ? 'desc' : 'asc'; $p['page']=1;
          $url = '?'.http_build_query($p);
        ?>
        <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:white;">Nama Jabatan Fungsional <?php if($sort=='jabatan') echo $order=='asc' ? '▲' : '▼'; ?></a>
      </th>

      <th>
        <?php
          $p = $_GET; $p['sort']='total'; $p['order'] = ($sort=='total' && $order=='asc') ? 'desc' : 'asc'; $p['page']=1;
          $url = '?'.http_build_query($p);
        ?>
        <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:white;">Total Penjabat <?php if($sort=='total') echo $order=='asc' ? '▲' : '▼'; ?></a>
      </th>

      <th>Link Informasi</th>
    </tr>
  </thead>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr style="text-align: left;">
        <td style="text-align:center; width: 10%;"><?php echo $row['id']; ?></td>
        <td style="width: 45%;"><?php echo $row['jabatan']; ?></td>
        <td style="text-align:center; width: 15%;"><?php echo $row['total']; ?></td>

        <td style="text-align:center; width: 30%;">
			<?php if (!empty($row['link'])): ?>
			<a href="<?php echo $row['link']; ?>" target="_blank">
				<?php echo $row['link']; ?>
			</a>
			<?php else: ?>
				<b>Belum tersedia.</b> Jika Anda memiliki informasi mengenai jabatan ini, silahkan <a href="https://wa.me/6285159997813" target="_blank"> beritahu kami.</a>
			<?php endif; ?>
		</td>

      </tr>
      <?php endwhile; ?>
    </table>

  </div>

<div class="pagination">
  <?php if ($page > 1): ?>
    <?php $p = $_GET; $p['page'] = $page-1; $url = '?'.http_build_query($p); ?>
    <a href="<?php echo htmlspecialchars($url); ?>" class="page-link">&#10094; Prev</a>
  <?php endif; ?>

  <?php for ($i=1; $i <= $totalPages; $i++): 
        $p = $_GET; $p['page'] = $i;
        $url = '?'.http_build_query($p);
  ?>
    <a href="<?php echo htmlspecialchars($url); ?>" 
       class="page-link <?php echo ($i==$page) ? 'active' : ''; ?>">
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <?php $p = $_GET; $p['page'] = $page+1; $url = '?'.http_build_query($p); ?>
    <a href="<?php echo htmlspecialchars($url); ?>" class="page-link">Next &#10095;</a>
  <?php endif; ?>
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
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="em">
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
	$(document).ready(function() {
	$('#userTable').DataTable({
		paging: false,      // keep server pagination? set false to keep your existing prev/next links
		ordering: true,
		info: false,
		searching: false    // you already have your own search form
	});
	});
</script>
<script>
  $(document).ready(function() {
    $('#userTable').DataTable({
      // Optional: customize language
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Berikutnya",
          previous: "Sebelumnya"
        }
      },
      // Optional: default order by first column (id)
      order: [[0, "asc"]],
      // Optional: disable sorting for last column (Link)
      columnDefs: [
        { orderable: false, targets: 3 }
      ]
    });
  });
</script>

<script>
// Handle Search form submission	
$(document).ready(function() {
  $('#filterForm').on('submit', function(e) {
    e.preventDefault(); // stop normal submit

    $.get('fungsional.php', $(this).serialize(), function(data) {
      // Extract only the table part from response
      const newTable = $(data).find('#tableContainer').html();
      $('#tableContainer').html(newTable);
    });
  });
});

// Handle pagination clicks
    document.addEventListener("click", function(e) {
      if (e.target.classList.contains("pagination-link")) {
        e.preventDefault();
        const page = e.target.getAttribute("data-page");
        loadUsers(page);
      }
    });
</script>

</body>
</html>
