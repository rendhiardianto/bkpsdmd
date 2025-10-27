<?php
session_start();

if (
    !isset($_SESSION['allow_update']) ||
    $_SESSION['allow_update'] !== true ||
    !isset($_SESSION['verified_nip'])
) {
    if (!isset($_SESSION['allow_update'])) {
      echo json_encode(['status' => 'error', 'message' => 'Akses tidak sah.']);
      exit;
    }
}

// Get NIP from session (verified one)
$verified_nip = $_SESSION['verified_nip'];

// Get NIP from URL
$nipFromUrl = $_GET['nip'] ?? '';

// 🧠 Compare session NIP and URL NIP
if ($nipFromUrl !== $verified_nip) {
    // Someone is trying to access another NIP manually
    header("Location: jafung_index.php?error=unauthorized");
    exit();
}

include_once __DIR__ . '/../../CiviCore/db.php';
include_once __DIR__ . '/../../CiviCore/config.php';
include_once __DIR__ . '/../../CiviCore/datetime_helper.php';

$alertMessage = '';
$alertType = ''; // success or error
$redirect = false;

// capture NIP if coming from jafung_index.php
$prefilledNip = "";
$readonlyNip = "";
if (isset($_GET['nip'])) {
    $prefilledNip = $conn->real_escape_string($_GET['nip']);
    $readonlyNip = "readonly"; // make the input read-only
}

// fallback to GET (if needed)
if (empty($prefilledNip) && isset($_GET['nip'])) {
    $prefilledNip = $conn->real_escape_string($_GET['nip']);
    $readonlyNip  = "readonly";
}

// --- NEW: Preload NIP from jafung_index.php ---
$nipFromGet = $_GET['nip'] ?? '';   // from URL

if (!empty($nipFromGet)) {
    // ✅ Fetch employee info from asn_merangin (created by add_asn_merangin.php)
    $stmt = $conn->prepare("SELECT fullname, tempat_lahir, tanggal_lahir, gol_saat_ini, 
    jenis_jabatan, jabatan, tmt_jabatan, pendidikan, organisasi, organisasi_induk FROM asn_merangin WHERE nip=?");
    $stmt->bind_param("s", $nipFromGet);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $fullnameFromDB = $row['fullname'];
        $tempatLahirFromDB = $row['tempat_lahir'];
        $tanggalLahirFromDB = $row['tanggal_lahir'];
        $golSaatIniFromDB = $row['gol_saat_ini'];
        $jenisJabatanFromDB = $row['jenis_jabatan'];
        $jabatanFromDB = $row['jabatan'];
        $tmtJabatanFromDB = $row['tmt_jabatan'];
        $pendidikanFromDB = $row['pendidikan'];
        $organisasiFromDB = $row['organisasi'];
        $organisasiIndukFromDB = $row['organisasi_induk'];
    }
    $stmt->close();
}

// 🧠 Prevent re-submission if NIP already exists
$verified_nip = $_SESSION['verified_nip'] ?? '';

$alert = ''; // container for any alert messages
$alertType = ''; // 'success' or 'error'

if (!empty($verified_nip)) {
    $checkExisting = $conn->prepare("SELECT COUNT(*) FROM jafung_submissions WHERE nip = ?");
    $checkExisting->bind_param("s", $verified_nip);
    $checkExisting->execute();
    $checkExisting->bind_result($existingCount);
    $checkExisting->fetch();
    $checkExisting->close();

    if ($existingCount > 0) {
        $alert = "⚠️ Anda sudah pernah melakukan pengajuan sebelumnya. Anda akan dialihkan ke halaman utama.";
        $alertType = "error";
    }
}

// --- Handle upload if not submitted before ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nip = $conn->real_escape_string($_POST['nip'] ?? '');
    $fullname = $conn->real_escape_string($_POST['fullname'] ?? '');
    $phone = trim($conn->real_escape_string($_POST['phone'] ?? ''));
    $jenis_usulan = $conn->real_escape_string($_POST['jenis_usulan'] ?? '');

    // 🔒 Validation
    if (empty($phone)) {
        $alert = "❌ Nomor HP wajib diisi sebelum mengunggah dokumen.";
        $alertType = "error";
    } elseif (!preg_match('/^[0-9+\-() ]{8,15}$/', $phone)) {
        $alert = "❌ Nomor HP tidak valid. Gunakan hanya angka atau simbol +, -, ().";
        $alertType = "error";
    }

    // --- Upload directory ---
    $uploadDir = __DIR__ . "/uploads/documents/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fields = [
        'surat_usul_opd',
        'sk_cpns',
        'sk_pns',
        'sk_kp_terakhir',
        'sk_jabatan_terakhir',
        'ijazah_dan_transkrip_nilai',
        'ekinerja',
        'pak_awal',
        'pak_terakhir',
        'sertifikat_kompetensi',
        'anjab_abk',
        'rekomendasi_formasi',
        'sk_pemberhentian',
        'syarat_lain'
    ];

    $uploaded_files = [];
    foreach ($fields as $field) {

        if (!empty($_FILES[$field]['name'])) {
            $file = $_FILES[$field];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] === 0 && $ext === 'pdf') {
                $newName = $nip . '_' . $field . '_' . time() . '.pdf';
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $uploaded_files[$field] = $newName;
                }
            }
        }
    }
    if (empty($uploaded_files)) {
        $alert = "❌ Tidak ada dokumen PDF valid yang diunggah.";
        $alertType = "error";
    } else {
        // --- Generate ticket ---
        $ticket_number = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        do {
            $check = $conn->prepare("SELECT COUNT(*) FROM jafung_submissions WHERE ticket_number = ?");
            $check->bind_param("s", $ticket_number);
            $check->execute();
            $check->bind_result($count);
            $check->fetch();
            $check->close();
            if ($count > 0) {
                $ticket_number = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            }
        } while ($count > 0);

        // --- Save submission ---
        $jsonFiles = json_encode($uploaded_files, JSON_UNESCAPED_SLASHES);
        $stmt = $conn->prepare("
            INSERT INTO jafung_submissions 
            (ticket_number, nip, fullname, phone, jenis_usulan, document_paths, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())
        ");
        $stmt->bind_param("ssssss", $ticket_number, $nip, $fullname, $phone, $jenis_usulan, $jsonFiles);
        $stmt->execute();
        $stmt->close();
        unset($_SESSION['allow_update']);
        unset($_SESSION['verified_nip']);
    }
    // return JSON response:
   echo json_encode(['status' => 'success', 'message' => '✅ Dokumen berhasil diunggah!']);
   exit;
}
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
  <title>Dashboard - Layanan Jabatan Fungsional</title>
  <link href="upload_document.css" rel="stylesheet" type="text/css">
  <link href="/headerFooter.css" rel="stylesheet" type="text/css">

  <meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />
  <link rel="shortcut icon" href="../icon/button/logo2.png">
</head>

<body>

<!-- Popup Alert Modal -->
<div id="alertModal" style="
  display:none;
  position:fixed; top:0; left:0; right:0; bottom:0;
  background:rgba(0,0,0,0.5);
  align-items:center; justify-content:center;
  z-index:9999;
">
  <div id="alertBox" style="
    background:#fff; padding:25px 35px; border-radius:10px;
    text-align:center; width:400px;
    box-shadow:0 0 10px rgba(0,0,0,0.3);
  ">
    <p id="alertMessage" style="font-size:16px; margin-bottom:20px;"></p>
    <button onclick="closeAlert()" style="
      background:#007bff; color:white; border:none;
      padding:8px 15px; border-radius:6px; cursor:pointer;
    ">OK</button>
  </div>
</div>

<div class="header">
    <div class="logo">
      <a href="/index.php" target="_blank"><img src="/icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>
    </div>
    <div class="roleHeader">
      <h1>Dashboard Pengajuan Jabatan Fungsional</h1>
    </div>
</div>

<div class="liveClock">
  <?php echo renderLiveClock(); ?>
</div>

<div class="content">

  <div class="info-box">

    <div class="fotoProfil">
      <img src="/icon/button/profil.png">
    </div>

    <table  class="infoTable">
      <tr>
        <td><strong>NIP</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($nipFromGet); ?></td>
      </tr>
      <tr>
        <td><strong>Nama Lengkap</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($fullnameFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Jabatan</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($jabatanFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Organisasi</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($organisasiFromDB); ?></td>
      </tr>
      <tr>
        <td><strong>Organisasi Induk</strong></td>
        <td><strong>:</strong></td>
        <td><?php echo htmlspecialchars($organisasiIndukFromDB); ?></td>
      </tr>
    </table>

    <div class="selecJFtype">
      <h3>Silahkan Pilih Jenis Usulan JF</h3>
      <select name="jenis_usulan" required>
        <option value="">(Pilih Jenis Usulan Jabatan Fungsional)</option>
        <option value="JF4">(JF4) Kenaikan Jenjang JF</option>
        <option value="JF3">(JF3) Pengangkatan Kembali ke dalam JF</option>
        <option value="JF2">(JF2) Perpindahan dari Jabatan Lain ke dalam JF</option>
        <option value="JF1">(JF1) Pengangkatan Pertama dalam JF</option>        
      </select>
    </div>

  </div>
<hr>
  <div class="form-box">

    

  <!-- JF1 -->
  <div class="jf-form" id="JF1" style="display:none;">

    <h2>(JF1) Pengangkatan Pertama dalam Jabatan Fungsional</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="nip" value="<?php echo htmlspecialchars($nipFromGet); ?>">
      <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($fullnameFromDB); ?>">
      <input type="hidden" name="jenis_usulan" value="JF1"> <!-- change per form -->

      <div class="formFile">
        <label>Surat Usul/Pengantar dari OPD <span class="required">*</span></label>
        <input type="file" name="surat_usul_opd" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>SK CPNS <span class="required">*</span></label>
        <input type="file" name="sk_cpns" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>SK PNS <span class="required">*</span></label>
        <input type="file" name="sk_pns" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>SK KP Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_kp_terakhir" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>Ijazah & Transkrip Nilai <span class="required">*</span></label>
        <input type="file" name="ijazah_dan_transkrip_nilai" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>E-Kinerja (1 tahun terakhir) <span class="required">*</span></label>
        <input type="file" name="ekinerja" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>PAK Awal <span class="required">*</span></label>
        <input type="file" name="pak_awal" accept=".pdf" required>
      </div>

      <div class="formFile">
        <label>Syarat lain (jika ada)</label>
        <input type="file" name="syarat_lain" accept=".pdf">
      </div>

      <div class="formFile">
        <label>Nomor WhatsApp Aktif (agar Anda menerima notifikasi pengajuan berkas)<span class="required">*</span></label>
        <input type="text" name="phone" placeholder="Contoh: 081234567890" required>
      </div>

      <p class="note">Kolom dengan tanda <span class="required">*</span> wajib diisi.</p>
      <button type="submit">Submit</button>
    </form>
  </div>

  <!-- JF2 -->
  <div class="jf-form" id="JF2" style="display:none;">

    <h2>(JF2) Perpindahan dari Jabatan Lain ke dalam JF</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="nip" value="<?php echo htmlspecialchars($nipFromGet); ?>">
      <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($fullnameFromDB); ?>">
      <input type="hidden" name="jenis_usulan" value="JF2"> <!-- change per form -->

      <div class="formFile">
        <label>Surat Usul/Pengantar dari OPD <span class="required">*</span></label>
        <input type="file" name="surat_usul_opd" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK CPNS <span class="required">*</span></label>
        <input type="file" name="sk_cpns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK PNS <span class="required">*</span></label>
        <input type="file" name="sk_pns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK KP Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_kp_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK Jabatan Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_jabatan_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Ijazah & Transkrip Nilai <span class="required">*</span></label>
        <input type="file" name="ijazah_dan_transkrip_nilai" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Sertifikat Uji Kompetensi <span class="required">*</span></label>
        <input type="file" name="sertifikat_kompetensi" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>E-Kinerja (2 tahun terakhir) <span class="required">*</span></label>
        <input type="file" name="ekinerja" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>PAK Awal <span class="required">*</span></label>
        <input type="file" name="pak_awal" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Rekomendasi Formasi dari KemenPANRB <span class="required">*</span></label>
        <input type="file" name="rekomendasi_formasi" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Analisis Jabatan & ABK <span class="required">*</span></label>
        <input type="file" name="anjab_abk" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Syarat lain (jika ada)</label>
        <input type="file" name="syarat_lain" accept=".pdf">
      </div>

      <div class="formFile">
        <label>Nomor WhatsApp Aktif (agar Anda menerima notifikasi pengajuan berkas)<span class="required">*</span></label>
        <input type="text" name="phone" placeholder="Contoh: 081234567890" required>
      </div>

      <p class="note">Kolom dengan tanda <span class="required">*</span> wajib diisi.</p>
      <button type="submit">Submit</button>
    </form>
  </div>

  <!-- JF3 -->
  <div class="jf-form" id="JF3" style="display:none;">
    <h2>(JF3) Pengangkatan Kembali ke dalam JF</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="nip" value="<?php echo htmlspecialchars($nipFromGet); ?>">
      <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($fullnameFromDB); ?>">
      <input type="hidden" name="jenis_usulan" value="JF3"> <!-- change per form -->

      <div class="formFile">
        <label>Surat Usul/Pengantar dari OPD <span class="required">*</span></label>
        <input type="file" name="surat_usul_opd" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK CPNS <span class="required">*</span></label>
        <input type="file" name="sk_cpns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK PNS <span class="required">*</span></label>
        <input type="file" name="sk_pns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK KP Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_kp_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK Jabatan Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_jabatan_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Ijazah & Transkrip Nilai <span class="required">*</span></label>
        <input type="file" name="ijazah_dan_transkrip_nilai" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Keputusan Pemberhentian dari JF <span class="required">*</span></label>
        <input type="file" name="sk_pemberhentian" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>E-Kinerja (1 tahun terakhir) <span class="required">*</span></label>
        <input type="file" name="ekinerja" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>PAK Terakhir <span class="required">*</span></label>
        <input type="file" name="pak_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Analisis Jabatan & ABK <span class="required">*</span></label>
        <input type="file" name="anjab_abk" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Nomor WhatsApp Aktif (agar Anda menerima notifikasi pengajuan berkas)<span class="required">*</span></label>
        <input type="text" name="phone" placeholder="Contoh: 081234567890" required>
      </div>
      <div class="formFile">
        <label>Syarat lain (jika ada)</label>
        <input type="file" name="syarat_lain" accept=".pdf">
      </div>
      <p class="note">Kolom dengan tanda <span class="required">*</span> wajib diisi.</p>
      <button type="submit">Submit</button>
    </form>
  </div>

  <!-- JF4 -->
  <div class="jf-form" id="JF4" style="display:none;">
    <h2>(JF4) Kenaikan Jenjang Jabatan Fungsional</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="nip" value="<?php echo htmlspecialchars($nipFromGet); ?>">
      <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($fullnameFromDB); ?>">
      <input type="hidden" name="jenis_usulan" value="JF4"> <!-- change per form -->

      <div class="formFile">
        <label>Surat Usul/Pengantar dari OPD <span class="required">*</span></label>
        <input type="file" name="surat_usul_opd" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK CPNS <span class="required">*</span></label>
        <input type="file" name="sk_cpns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK PNS <span class="required">*</span></label>
        <input type="file" name="sk_pns" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK KP Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_kp_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>SK Jabatan Terakhir <span class="required">*</span></label>
        <input type="file" name="sk_jabatan_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Ijazah & Transkrip Nilai <span class="required">*</span></label>
        <input type="file" name="ijazah_dan_transkrip_nilai" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Sertifikat Uji Kompetensi <span class="required">*</span></label>
        <input type="file" name="sertifikat_kompetensi" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>E-Kinerja (1 tahun terakhir) <span class="required">*</span></label>
        <input type="file" name="ekinerja" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>PAK Terakhir <span class="required">*</span></label>
        <input type="file" name="pak_terakhir" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Analisis Jabatan & ABK <span class="required">*</span></label>
        <input type="file" name="anjab_abk" accept=".pdf" required>
      </div>
      <div class="formFile">
        <label>Nomor WhatsApp Aktif (agar Anda menerima notifikasi pengajuan berkas)<span class="required">*</span></label>
        <input type="text" name="phone" placeholder="Contoh: 081234567890" required>
      </div>
      <div class="formFile">
        <label>Syarat lain (jika ada)</label>
        <input type="file" name="syarat_lain" accept=".pdf">
      </div>
      <p class="note">Kolom dengan tanda <span class="required">*</span> wajib diisi.</p>
      <button type="submit">Submit</button>
    </form>
  </div>

</div>



</div><!--content-->

<!------------------- FOOTER ----------------------------------->
<div class="row">
  <div class="column first">
		<img src="/icon/BKPLogo.png" alt="Logo BKPSDMD">
	  <p style="text-align: center">Copyright © 2025.</p>
	  <p style="text-align: center">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah (BKPSDMD) Kabupaten Merangin.</p> 
	  <p style="text-align: center">All Rights Reserved</p>
  </div>
	
  <div class="column second">
		<h3>Butuh Bantuan?</h3>
	  
		<p><a href="https://maps.app.goo.gl/idAZYTHVszUhSGRv8" target="_blank" class="Loc">
			<img src="/icon/sosmed/Loc.png" alt="Logo Loc" width="30px" style="float: left"></a> 
			Jl. Jendral Sudirman, No. 01, Kel. Pematang Kandis, Kec. Bangko, Kab. Merangin, Prov. Jambi - Indonesia | Kode Pos - 37313</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="wa">
			<img src="/icon/sosmed/WA.png" alt="Logo WA" width="30px" style="vertical-align:middle"></a> 
			+62851 5999 7813</p>
	  
		<p><a href="https://wa.me/6285159997813" target="_blank" class="em">
			<img src="/icon/sosmed/EM.png" alt="Logo Email" width="30px" style="vertical-align:middle"></a> 
			bkd.merangin@gmail.com</p>
  </div>
	
  <div class="column third">
		<h3>Follow Sosial Media Kami!</h3>
		  <a href="https://www.instagram.com/bkpsdmd.merangin/?hl=en" target="_blank" class="ig">
			<img src="/icon/sosmed/IG.png" alt="Logo IG"></a>
	  
		  <a href="https://www.youtube.com/@bkpsdmd.merangin" target="_blank" class="yt">
			<img src="/icon/sosmed/YT.png" alt="Logo YT"></a>
	  
		  <a href="https://www.facebook.com/bkpsdmd.merangin/" target="_blank" class="fb">
			<img src="/icon/sosmed/FB.png" alt="Logo FB"></a>
	  
		  <a href="https://x.com/bkpsdmdmerangin?t=a7RCgFHif89UfeV9aALj8g&s=08" target="_blank" class="x">
			<img src="/icon/sosmed/X.png" alt="Logo X"></a>
	  
		  <a href="https://www.tiktok.com/@bkpsdmd.merangin?_t=ZS-8z3dFdtzgYy&_r=1 " target="_blank" class="tt">
			<img src="/icon/sosmed/TT.png" alt="Logo TT"></a>
  </div>
  <div class="column fourth">
		<h3>Kunjungan Website</h3>
		<p>Hari Ini</p>
		<p>Total</p>
	  
	  	
	  <img src="/icon/BerAkhlak.png" alt="Logo BerAkhlak">
	  
  </div>
</div>
<!------------------- BATAS FOOTER ----------------------------------->
<!-- Alert Popup -->
<div id="alertBox" style="
  display:none;
  position:fixed;
  top:20px;
  left:50%;
  transform:translateX(-50%);
  background:#4CAF50;
  color:#fff;
  padding:12px 20px;
  border-radius:8px;
  box-shadow:0 2px 10px rgba(0,0,0,0.2);
  z-index:9999;
  font-weight:bold;
"></div>

<script>
document.querySelectorAll("form").forEach(form => {
  form.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    
    fetch("../../layanan/jafung/upload_document.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      showPopup(data.message, data.status);
    })
    .catch(() => showPopup("❌ Gagal menghubungi server.", "error"));
  });
});

// 3️⃣ Popup modal alert
function showPopup(message, type = "success") {
  // Create background overlay if it doesn't exist
  let overlay = document.getElementById("popupOverlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.id = "popupOverlay";
    overlay.style.position = "fixed";
    overlay.style.top = "0";
    overlay.style.left = "0";
    overlay.style.width = "100%";
    overlay.style.height = "100%";
    overlay.style.background = "rgba(0, 0, 0, 0.5)";
    overlay.style.display = "flex";
    overlay.style.alignItems = "center";
    overlay.style.justifyContent = "center";
    overlay.style.zIndex = "9999";
    document.body.appendChild(overlay);
  }

  // Create modal box
  overlay.innerHTML = `
    <div id="popupBox" style="
      background: #fff;
      padding: 25px 35px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      text-align: center;
      font-family: 'Segoe UI', sans-serif;
      animation: fadeInScale 0.3s ease;
      max-width: 360px;
    ">
      <div style="font-size: 45px; margin-bottom: 10px;">
        ${type === "error" ? "❌" : "✅"}
      </div>
      <div style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color:#333;">
        ${message}
      </div>
      <button id="popupClose" style="
        background: ${type === "error" ? "#e74c3c" : "#4CAF50"};
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        font-size: 15px;
        cursor: pointer;
      ">OK</button>
    </div>
  `;

  overlay.style.display = "flex";

  // Close popup manually
  document.getElementById("popupClose").onclick = () => {
    overlay.style.display = "none";
  };
}

// 4️⃣ Add simple fade animation
const style = document.createElement('style');
style.textContent = `
@keyframes fadeInScale {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}
`;
document.head.appendChild(style);
</script>

<script>
document.querySelector('.selecJFtype select').addEventListener('change', function() {
  const selected = this.value;
  document.querySelectorAll('.jf-form').forEach(div => {
    div.style.display = 'none';
  });
  if (selected) {
    document.getElementById(selected).style.display = 'block';
  }
});
</script>

</body>
</html>
