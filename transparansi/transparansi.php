<?php
include "../CiviCore/db.php";

// Define mapping between URL parameter and tipe_dokumen in DB
$tipeMap = [
    'perbup'  => 'Peraturan Bupati',
    'renstra' => 'Rencana Strategis',
    'renja'   => 'Rencana Kerja',
    'iku'     => 'Indikator Kinerja Utama',
    'casscad' => 'Casscading',
    'perkin'  => 'Perjanjian Kinerja',
    'reaksi'  => 'Rencana Aksi',
    'lapkin'  => 'Laporan Kinerja',
    'sop'     => 'Standar Operasional Prosedur',
    'rapbd'   => 'RAPBD',
    'apbd'    => 'APBD',
    'lppd'    => 'LPPD'
];

// Get the tipe parameter from URL
$key = $_GET['tipe'] ?? '';
$tipe_dokumen = $tipeMap[$key] ?? null;

// If no or invalid tipe provided
if (!$tipe_dokumen) {
    die("<h2 style='text-align:center;margin-top:50px;'>Tipe dokumen tidak ditemukan.</h2>");
}

// Query only those rows:
$stmt = $conn->prepare("SELECT * FROM transparansi WHERE tipe_dokumen = ?");
$stmt->bind_param("s", $tipe_dokumen);
$stmt->execute();
$result = $stmt->get_result();
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transparansi - BKPSDMD Kabupaten Merangin</title>
<link rel="shortcut icon" href="../icon/IconWeb.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="../headerFooter.css" rel="stylesheet" type="text/css">
<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="topnav" id="mynavBtn">
    <div id="startButton"></div>
    <script>
    fetch("/startButton.html")
        .then(response => response.text())
        .then(data => {
            document.getElementById("startButton").innerHTML = data;
        });
    </script>
    <div class="navLogo">
        <a href="../index.php"><img src="../icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>    
    </div>
    
    <div class="navRight" >
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
    </div>
</div>

<!------------------- CONTENT ----------------------------------->
<h1 style="text-align: center; margin-top:30px;"><?= htmlspecialchars($tipe_dokumen) ?></h1>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <table style="overflow-x:auto; width: 80%; margin:auto; margin-top:20px;">
            <tr>
                <th style="text-align:center; width: 20%;">Tipe Dokumen</th>
                <th style="text-align:center; width: 50%;">Judul</th>
                <th style="text-align:center;">Nomor</th>
                <th style="text-align:center;">Tahun</th>
                <th style="text-align:center; width: 20%;">Unduh</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($row['tipe_dokumen']) ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['nomor']) ?></td>
                <td><?= htmlspecialchars($row['tahun']) ?></td>
                <td><a href="../CiviCore/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" class="unduh" download>
                    <button class="btn"><i class="fa fa-download"></i> Unduh</button></a>
                </td>
            </tr>
        </table>
        <iframe src="../CiviCore/transparansi/uploads/files/<?= htmlspecialchars($row['attachment']) ?>" width="80%" height="500px" style="display:block; margin:auto; margin-bottom:30px;"></iframe>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">Tidak ada data untuk tipe dokumen ini.</p>
<?php endif; ?>

<!------------------- FOOTER ----------------------------------->
<div class="gotoTop" onclick="topFunction()" id="myBtn" title="Go to top">
    <img src="../icon/go_to_top.png">
</div>

<div id="footer"></div>
<script>
fetch("/footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>

<script src="/JavaScript/script.js"></script>
</body>
</html>
