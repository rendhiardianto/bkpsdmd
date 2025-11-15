<?php
include "../CiviCore/db.php";

// --- FUNGSI TANGGAL INDONESIA ---
function indoDate($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $time = strtotime($date);
    return date("j", $time) . " " . $bulan[date("n", $time)] . " " . date("Y", $time);
}

function indoDateTime($date) {
    return indoDate($date) . ", " . date("H:i", strtotime($date));
}

// --- GET DETAIL ---
if (!isset($_GET['id'])) {
    die("Pengumuman tidak ditemukan.");
}

$id = intval($_GET['id']);
$detail = $conn->query("SELECT * FROM announcements WHERE id = $id");

if ($detail->num_rows === 0) {
    die("Pengumuman tidak ditemukan.");
}

$row = $detail->fetch_assoc();
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
<link href="detail.css" rel="stylesheet" type="text/css">

<title><?= htmlspecialchars($row['title']) ?></title>
<link rel="shortcut icon" href="icon/IconWeb.png">
</head>

<body>
<div class="topnav" id="mynavBtn">
	<div class="navbar">
    <a href="/pengumuman.php" class="btn btn-secondary" 
    style="text-decoration: none; color:white;">&#10094; Kembali</a>
  </div>
  <div class="roleHeader">
    <h1>Detail Pengumuman</h1>
  </div>
</div>

<div class="detail-container">

    <div class="detail-card">
        <?php if ($row['thumbnail']): ?>
            <img src="/CiviCore/pengumuman/uploads/thumbnails/<?= $row['thumbnail'] ?>" alt="Thumbnail">
        <?php endif; ?>

        <h1><?= htmlspecialchars($row['title']) ?></h1>

        <small>
            Dipublish: <?= indoDateTime($row['created_at']) ?>  
            | Oleh: <b><?= htmlspecialchars($row['created_by']) ?></b>
        </small>
        <hr>

        <div class="description">
          <?= nl2br(htmlspecialchars($row['content'])) ?>
        </div>

        <?php if ($row['attachment']): ?>
        <div class="attachment-box">
            <h3>Lampiran:</h3>

            <!-- TAMPILKAN PDF -->
            <iframe 
                src="/CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" 
                width="100%" 
                height="500px">
            </iframe>

            <br><br>
            <a class="download-btn" href="/CiviCore/pengumuman/uploads/files/<?= $row['attachment'] ?>" download>
                <i class="fa fa-download"></i> Download Lampiran
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
