<?php

include "../CiviCore/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $ticket = trim($_POST['ticket_number'] ?? '');

    if ($nip === '' || $ticket === '') {
        $error = "NIP dan Nomor Tiket wajib diisi.";
    } else {
        // 🔍 Cari file berdasarkan NIP & Ticket Number
        $stmt = $conn->prepare("
            SELECT final_doc 
            FROM jafung_submissions 
            WHERE nip = ? AND ticket_number = ? AND status = 'completed'
            LIMIT 1
        ");
        $stmt->bind_param("ss", $nip, $ticket);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && !empty($row['final_doc'])) {
            $filePath = __DIR__ . "/../CiviCore/verification/jafung/uploads/final_docs/" . basename($row['final_doc']);

            if (file_exists($filePath)) {
                // ✅ Prevent auto re-download on refresh by clearing POST data
                header("Cache-Control: no-cache, no-store, must-revalidate");
                header("Pragma: no-cache");
                header("Expires: 0");

                // ✅ Send file
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                $error = "❌ File SK tidak ditemukan di server.";
            }
        } else {
            $error = "❌ Data tidak ditemukan atau SK belum diterbitkan.";
        }
    }
}

// ✅ Optional: clear POST data after file download to stop auto re-download
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // do nothing
}
?>

<!DOCTYPE html>
<html lang="id">
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

<link href="/headerFooter.css" rel="stylesheet" type="text/css">

<link href="download_sk_jafung.css" rel="stylesheet" type="text/css">
<title>Download SK Jabatan Fungsional</title>
<link rel="shortcut icon" href="/icon/IconWeb.png">
</head>
<body>
<div class="header">
    <div class="navbar">
        <a href="index.php" class="btn btn-secondary" style="text-decoration: none; color:white;">&#10094; Kembali</a>
    </div>
    <div class="roleHeader">
        <h1>Dashboard Download SK Jabatan Fungsional</h1>
    </div>
</div>

<div class="container">
    <h2>Download SK Jabatan Fungsional</h2>
    
    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="nip">Nomor Induk Pegawai:</label>
      <input type="text" id="nip" name="nip" placeholder="Masukkan NIP" required>

      <label for="ticket_number">Nomor Tiket:</label>
      <input type="text" id="ticket_number" name="ticket_number" placeholder="Masukkan Nomor Tiket" required>

      <button type="submit">Download SK</button>
    </form>
</div>
<!------------------- FOOTER ----------------------------------->	
<div id="footer"></div>
<script>
fetch("/footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<!------------------- BATAS AKHIR CONTENT ---------------------------------->

</body>
</html>
