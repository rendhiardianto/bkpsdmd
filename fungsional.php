<?php
session_start();

include "cms/db.php";

// --- Pagination setup ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;


// --- Search & Filter ---
$where = "1=1";

// allowed columns to sort by (whitelist)
$allowedSort = ['id','jabatan','total','pembina'];

// default sort
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'desc' : 'asc';


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

// --- Count total users ---
$countResult2 = $conn->query("SELECT COUNT(*) AS total FROM instapembinajf WHERE $where");
$totalUsers2 = $countResult2->fetch_assoc()['total'];
$totalPages2 = ceil($totalUsers2 / $limit);

// --- Fetch users for the table ---
$result2 = $conn->query(
  "SELECT id, jabatan, pembina
   FROM instapembinajf
   WHERE $where
   ORDER BY $sort $order"
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
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

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
	<!-- <h2 class="subtitle">Pemerintah Kabupaten Merangin</h2> -->
</div>

<!-- ============================== Tab Button ============================== -->
<div class="content">

  <div class="tab">
    <button class="tablinks" onclick="openCity(event, 'tab1')" id="defaultOpen">Rekapitulasi</button>
    <button class="tablinks" onclick="openCity(event, 'tab2')">Ketentuan Umum</button>
    <button class="tablinks" onclick="openCity(event, 'tab3')">Pengusulan dan Penetapan</button>
    <button class="tablinks" onclick="openCity(event, 'tab4')">Pengangkatan</button>
    <button class="tablinks" onclick="openCity(event, 'tab5')">Penilaian Kinerja</button>
    <button class="tablinks" onclick="openCity(event, 'tab6')">Instansi Pembina</button>
    <button class="tablinks" onclick="openCity(event, 'tab7')">Tabel Daftar Informasi JF</button>
  </div>

  <!-- ============================== Tab content ============================== -->
  <div id="tab1" class="tabcontent">
    <h2 style="text-align: center;">Rekapitulasi Jabatan Fungsional</h2>
    <h3 style="text-align: center;">di Lingkungan Pemerintah Kabupaten Merangin</h3>     
    <div class="chart-rekap">
      <div class="chart1" id="myPlot1"></div>
      <div class="chart2" id="myPlot2"></div>
    </div>
  </div>

  <div id="tab2" class="tabcontent">
    <h2>Kedudukan dan Tanggung Jawab</h2>
    <p style="text-align: justify; line-height: 1.6;">
      Jabatan Fungsional adalah jabatan yang menunjukkan tugas, 
      tanggung jawab, wewenang, dan hak seorang PNS yang ditetapkan 
      berdasarkan keahlian atau keterampilan tertentu sesuai dengan 
      peraturan perundang-undangan.
    </p>
    <p style="text-align: justify; line-height: 1.6;">
      Pejabat Fungsional berkedudukan sebagai pelaksana teknis fungsional 
      pada Instansi Pemerintah dan bertanggung jawab secara langsung 
      kepada Pejabat Pimpinan Tinggi Pratama, Pejabat Administrator, 
      atau Pejabat Pengawas yang memiliki keterkaitan dengan pelaksanaan tugas Jabatan Fungsional.
    </p>

    <h2>Kategori dan Jenjang Jabatan Fungsional (JF)</h2>
    <p><b>JF Keahlian</b></p>
    <ol>
      <li>Ahli Utama</li>
      <p>
        Jenjang JF ahli utama melaksanakan tugas dan fungsi utama yang mensyaratkan 
        kualifikasi professional tingkat tertinggi.
      </p>
      <li>Ahli Madya</li>
      <p>
        Jenjang JF ahli madya melaksanakan tugas dan fungsi utama yang mensyaratkan 
        kualifikasi professional tingkat tinggi.
      </p>
      <li>Ahli Muda</li> 
      <p>
        Jenjang JF ahli muda melaksanakan tugas dan fungsi utama yang mensyaratkan 
        kualifikasi professional tingkat lanjutan.
      </p>       
      <li>Ahli Pertama</li>
      <p>
        Jenjang JF ahli pertama melaksanakan tugas dan fungsi utama yang mensyaratkan 
        kualifikasi profesional tingkat dasar.
      </p>
    </ol>

    <p><b>JF Keterampilan</b></p>
    <ol>
      <li>Penyelia</li>
      <p>
        Jenjang JF penyelia melaksanakan tugas dan fungsi koordinasi dalam JF keterampilan.
      </p>
      <li>Mahir</li>
      <p>
        Jenjang JF mahir melaksanakan tugas dan fungsi utama dalam JF keterampilan.
      </p>
      <li>Terampil</li> 
      <p>
        Jenjang JF terampil melaksanakan tugas dan fungsi yang bersifat lanjutan dalam JF keterampilan.
      </p>       
      <li>Pemula</li>
      <p>
        Jenjang JF pemula melaksanakan tugas dan fungsi yang bersifat dasar dalam JF keterampilan.
      </p>
    </ol>
  </div>

  <div id="tab3" class="tabcontent" style="text-align: justify; line-height: 1.6;">
     <h2>Tata Cara Pengusulan dan Penetapan JF</h2>
    <ol>
      <li><b>Usulan</b></li>
      <p>
        Usulan disampaikan oleh pimpinan Instansi Pemerintah kepada Menteri dengan 
        melampirkan naskah akademik untuk ditelaah/dikaji/dianalisis.
      </p>
      <li><b>Rekomendasi</b></li>
      <p>
        Menteri atau pejabat pimpinan tinggi madya yang membidangi SDM aparatur 
        menerbitkan surat rekomendasi usulan JF.
      </p>
      <li><b>Perumusan Tugas Jabatan dan Uraian Kegiatan</b></li> 
      <p>
        Instansi Pembina bersama Kementerian Pendayagunaan Aparatur Negara dan Reformasi Birokrasi, dan 
        Badan Kepegawaian Negara merumuskan tugas jabatan uraian kegiatan, dan hasil kerja (output).
      </p>       
      <li><b>Uji Beban Kerja dan Norma Waktu</b></li>
      <p>
        Dilaksanakan berdasarkan volume pekerjaan, standar waktu kerja setiap tahun, tingkat kesulitan, dan 
        risiko pekerjaan.
      </p>
      <li><b>Perancangan dan Pengharmonisasian Peraturan Menteri</b></li>
      <p>
        Dilakukan bersama Instansi Pemerintah terkait untuk pengharmonisasian, pembulatan dan pemantapan konsepsi 
        peraturan serta mendapatkan persetujuan tertulis sesuai dengan ketentuan perundang-undangan
      </p>
      <li><b>Paraf Persetujuan Instansi Pembina</b></li>
      <p>
        Rancangan Peraturan Menteri diparaf pada tiap-tiap lembar dan dibubuhi tanda tangan serta nama oleh 
        Pejabat Pimpinan Tinggi Madya instansi pembina JF dan Pejabat Pimpinan Tinggi Madya yang menangani 
        urusan SDM Aparatur Kemenpan RB.
      </p>
      <li><b>Penetapan Peraturan Menteri</b></li>
      <p>
        Menteri menetapkan Peraturan Menteri.
      </p>
      <li><b>Pengundangan dan Penyebarluasan</b></li>
      <p>
        Peraturan Menteri yang telah ditetapkan, dilakukan pengundangan disertai analisa kesesuaian terhadap Pancasila, 
        UUD'45, peraturan perundang-undangan dan dilakukan autentifikasi leh Kemenpan RB. 
        Selanjutnya Kemenpan RB menyampaikan naskah salinan Peraturan Menteri tersebut kepada Instansi Pembina 
        dan Badan Kepegawaian Negara.
      </p>
    </ol>
  </div>

  <div id="tab4" class="tabcontent" style="text-align: justify; line-height: 1.6;">
    <h2>Pengangkatan Jabatan Fungsional</h2>
    <p>Pengangkatan PNS ke dalam JF keahlian dan JF keterampilan dilakukan melalui:</p>
    <ol>
      <li>Pertama</li>
      <li>Perpindahan dari Jabatan lain</li>
      <li>Penyesuaian</li>
      <li>Promosi</li>
    </ol>
    <p>Selain itu pengangkatan ke dalam JF tertentu dapat dilakukan melalui pengangkatan PPPK</p>
    <h2>Tata Cara Pengangkatan JF</h2>
    <ol>
      <li><b>Pengangkatan Pertama</b></li>
      <p>
        • PyB mengusulkan pengangkatan pertama PNS dalam JF kepada PPK untuk: JF ahli pertama; 
        JF ahli muda; JF pemula; dan JF terampil.<br>
        • Pengangkatan pertama dalam JF ditetapkan oleh PPK.
      </p>
      <li><b>Perpindahan dari Jabatan Lain</b></li>
      <p>
        Pengangkatan dalam JF melalui perpindahan Jabatan diusulkan oleh:<br>
        • PPK kepada Presiden bagi PNS yang akan menduduki JF ahli utama (ditetapkan oleh Presiden)<br>
        • PyB kepada PPK bagi PNS yang akan menduduki JF selain JF ahli utama (ditetapkan oleh PPK)
      </p>
      <li><b>Penyesuaian/Inpassing</b></li> 
      <p>
        • Pengangkatan PNS yang akan menduduki JF melalui penyesuaian diusulkan oleh PyB kepada PPK.<br>
        • Pengangkatan PNS dalam JF ini sebagaimana dimaksud ditetapkan oleh PPK.
      </p>       
      <li><b>Promosi</b></li>
      <p>
        Pengangkatan dalam JF melalui promosi diusulkan oleh:<br>
        • PPK kepada Presiden bagi PNS yang akan menduduki JF ahli utama (ditetapkan oleh Presiden)<br>
        • PyB kepada PPK bagi PNS yang akan menduduki JF selain JF ahli utama (ditetapkan oleh PPK)
      </p>
    </ol>
  </div>

  <div id="tab5" class="tabcontent" style="text-align: justify; line-height: 1.6;">

    <h2>Penilaian Kinerja</h2>
    <ul>
      <li>Bertujuan untuk menjamin objektivitas pembinaan yang didasarkan pada sistem prestasi 
        dan sistem karier</li>
      <li>Dilakukan berdasarkan perencanaan kinerja pada tingkat individu dan tingkat unit atau organisasi,
        dengan memperhatikan target, capaian, hasil dan manafaat yang dicapai, serta perilaku PNS</li>
      </li>
      <li>Dilakukan secara objektif, terukur, akuntabel, partisipatif dan transparan</li>
    </ul>

    <h2>Penyusunan SKP JF</h2>
    <p>SKP wajib disusun dan akan dilaksanakan dalam 1 (satu) tahun anggaran berjalan. 
      SKP yang telah disusun harus disetujui dan ditetapkan oleh atasan langsung, yang berisi:</p>
    <ol>
      <li>Kinerja utama disusun dalam bentuk Target Angka Kredit.</li>
      <li>Kinerja tambahan berupa tugas tambahan.</li>

    </ol>
    <h2>Target Angka Kredit</h2>
    <p>Kinerja utama yang berisi butir kegiatan dan diberikan nilai Angka Kredit berdasarkan lampiran
      Peraturan Menteri terkait JF yang diduduki. Ditetapkan secara proporsional berdasarkan jumlah waktu 
      sejak menduduki jabatan pada tahun berjalan, yaitu:
    </p>
    <img src="images/Pojafung/rumusAK.webp" alt="Rumus Target Angka Kredit" style="display: block; margin-left: auto; margin-right: auto; width: 50%;">
    <p>
      Contoh:
      <br>
      Di tanggal 1 April 2022 Pejabat Fungsional Ahli Pertama menduduki jenjang Ahli Muda maka 
      Target Angka Kredit ditetapkan sebagai berikut:
      <br>
      <br>
      𝑇𝑎𝑟𝑔𝑒𝑡 𝐴𝑛𝑔𝑘𝑎 𝐾𝑟𝑒𝑑𝑖𝑡 = (25/12) × 9 = 18,75
    </p>

    <h3><i>Target angka kredit yang harus dicapai untuk masing-masing jenjang JF</i></h3>
    <h4>JF Keahlian</h4>
      <ol>
        <li><b>Ahli Utama</b></li>
        <p>Paling sedikit 50 Angka Kredit atau 20 Angka Kredit jika Pejabat Fungsional memiliki 
          pangkat tertinggi pada jenjang tertinggi.</P>

        <li><b>Ahli Madya</b></li>
        <p>Paling sedikit 37,5 Angka Kredit, atau 30 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi, & 20 Angka Kredit jika Pejabat Fungsional 
          memiliki pangkat tertinggi pada jenjang tertinggi.</P>

        <li><b>Ahli Muda</b></li>
        <p>Paling sedikit 25 Angka Kredit atau 20 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi.</P>

        <li><b>Ahli Pertama</b></li>
        <p>Paling sedikit 12,5 Angka Kredit atau 10 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi.</P>
      </ol>
    <h4>JF Keterampilan</h4>
      <ol>
        <li><b>Penyelia</b></li>
          <p >
            Paling sedikit 25 Angka Kredit atau 20 Angka Kredit jika Pejabat Fungsional memiliki 
            pangkat tertinggi pada jenjang tertinggi.
          </p>

        <li><b>Mahir</b></li>
        <p>Paling sedikit 12,5 Angka Kredit atau 10 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi.
        </p>

        <li><b>Terampil</b></li>
        <p>Paling sedikit 5 Angka Kredit atau 4 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi.
        </p>

        <li><b>Pemula</b></li>
        <p>Paling sedikit 3,75 Angka Kredit atau 3 Angka Kredit jika belum tersedia lowongan 
          kebutuhan jenjang jabatan lebih tinggi.
        </p>
      </ol>
      <br>
      <h2>Penilaian SKP dan Capaian Angka Kredit</h2>
      <h3>Penilaian SKP dan Perilaku Kerja</h3>
      <p>
        Dilakukan oleh pejabat penilai dengan membandingkan realisasi dan target melalui 
        pengumpulan bukti-bukti empiris. Periode pengukuran dilakukan secara periodik setiap bulan, 
        setiap triwulan, setiap semester, dan/atau tahunan.
        <br>
        Penilaian perilaku kerja dapat berdasarkan penilaian rekan kerja setingkat dan/atau bawahan langsung, 
        dan dituangkan dalam dokumen perilaku kerja. Perilaku kerja yang dinilai meliputi aspek:
      </p>
      <ul>
        <li>Orientasi Pelayanan</li>
        <li>Komitmen</li>
        <li>Inisiatif kerja</li>
        <li>Kerjasama</li>
        <li>Kepemimpinan</li>
      </ul>
      <h3>Capaian Angka Kredit</h3>
      <ul>
        <li>Capaian Angka Kredit diperoleh dari hasil penilaian SKP yang ditetapkan dalam bentuk penilaian 
          Angka Kredit oleh Tim Penilai.</li>
        <li>Penilaian capaian angka kredit berdasarkan standar kualitas hasil pekerjaan yang disusun oleh 
          Instansi Pembina JF.</li>
        <li>Capaian Angka Kredit ditetapkan paling tinggi 150% dari target Angka Kredit setiap tahun.</li>
        <li>Bukti fisik dan laporan Hasil Kerja dapat disampaikan kepada Tim Penilai sebagai bahan pertimbangan.</li>
      </ul>
      <br>
      <h2>Pengusulan dan Penetapan Angka Kredit</h2>
      <h3>Pejabat yang mengusulkan Angka Kredit</h3>
      <ul>
        <li>JF Keahlian : paling rendah Pejabat Administrator yang membidangi JF atau kepegawaian.</li>
        <li>JF Keterampilan : paling rendah Pejabat Pengawas yang membidangi JF atau kepegawaian.</li>
      </ul>
      <h3>Pejabat yang menetapkan Angka Kredit</h3>
      <ul>
        <li>Pejabat Pimpinan Tinggi Madya pada Instansi Pembina bagi JF jenjang Ahli Utama</li>
        <li>Pejabat Pimpinan Tinggi Pratama pada instansinya bagi JF jenjang Ahli Pertama sampai dengan 
          Ahli Madya dan JF kategori Keterampilan</li>
      </ul>
  </div>

  <div id="tab6" class="tabcontent" style="text-align: justify; line-height: 1.6;">
    <div class="tab6Left">
      <h2>Instansi Pembina</h2>
      <p>Instansi Pembina berperan sebagai pengelola JF yang bertanggung jawab untuk menjamin terwujudnya 
        standar kualitas dan profesionalitas jabatan.
      </p>
      <h2>Tugas Instansi Pembina JF:</h2>
      <ol>
        <li>Menyusun pedoman formasi JF.</li>
        <li>Menyusun standar kompetensi JF.</li>
        <li>Menyusun petunjuk pelaksanaan dan petunjuk teknis JF.</li>
        <li>Menyusun standar kualitas hasil kerja dan pedoman penilaian kualitas hasil kerja.</li>
        <li>Menyusun pedoman penulisan Karya Tulis/Karya Ilmiah yang bersifat inovatif di bidang tugas.</li>
        <li>Menyusun kurikulum pelatihan JF.</li>
        <li>Menyelenggarakan pelatihan JF.</li>
        <li>Membina penyelenggaraan pelatihan fungsional pada lembaga pelatihan.</li>
        <li>Menyelenggarakan uji kompetensi JF.</li>
        <li>Menganalisis kebutuhan pelatihan fungsional di bidang tugas JF.</li>
        <li>Melakukan sosialisasi JF.</li>
        <li>Mengembangkan sistem informasi JF.</li>
        <li>Memfasilitasi pelaksanaan tugas JF.</li>
        <li>Memfasilitasi pembentukan organisasi profesi JF.</li>
        <li>Memfasilitasi penyusunan dan penetapan kode etik profesi dan kode perilaku JF.</li>
        <li>Melakukan akreditasi pelatihan fungsional dengan mengacu kepada ketentuan yang telah ditetapkan oleh Lembaga Administrasi Negara.</li>
        <li>Melakukan pemantauan dan evaluasi penerapan JF.</li>
        <li>Melakukan koordinasi dengan Instansi Pemerintah dalam rangka pembinaan karier.</li>
      </ol>
    </div>
    <div class="tab6Right">
      <h2 style="text-align: center;">Tabel Instansi Pembina Jabatan Fungsional</h2>
      
      <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Cari nama jabatan atau instansi pembina" title="Type in a name">
      
      <div style="width:100%; height:500px; overflow:auto; border:1px solid #ccc; margin:auto;">
      <table id="userTable" border="1" width="100%" cellspacing="0" cellpadding="8" style="background:#fff; border-collapse:collapse; text-align:center;">
        <thead>
          <tr style="background:#3498db; color:white; height:50px;">
            <th>Nomor</th>
            <th>Jabatan Fungsional</th>
            <th>Instansi Pembina</th>
          </tr>
        </thead>
        <?php while($row = $result2->fetch_assoc()): ?>
          <tr style="text-align:left;">
            <td style="text-align:center;"><?php echo $row['id']; ?></td>
            <td style="width:45%;"><?php echo $row['jabatan']; ?></td>
            <td style="width:45%; text-align:center"><?php echo $row['pembina']; ?></td>
          </tr>
          <?php endwhile; ?>
        </table>
        </div>
    </div>
  </div>

  <div id="tab7" class="tabcontent" style="text-align: justify; line-height: 1.6;">
    <h2 style="text-align: center;">Daftar Tabel Informasi Jabatan Fungsional</h2>

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
            <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:white;">Jabatan Fungsional <?php if($sort=='jabatan') echo $order=='asc' ? '▲' : '▼'; ?></a>
          </th>

          <th>
            <?php
              $p = $_GET; $p['sort']='total'; $p['order'] = ($sort=='total' && $order=='asc') ? 'desc' : 'asc'; $p['page']=1;
              $url = '?'.http_build_query($p);
            ?>
            <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:white;">Total Pejabat <?php if($sort=='total') echo $order=='asc' ? '▲' : '▼'; ?></a>
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
<script src="JavaScript/tab_switching.js"></script>
<script src="JavaScript/chart_fungsional.js"></script>
<script src="JavaScript/searchable_table.js"></script>

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