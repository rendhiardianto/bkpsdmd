<?php
session_start();
include "db.php"; // adjust path to your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $conn->real_escape_string($_POST['nip']);

    // 1. Check NIP in data_pegawai
    $pegawaiResult = $conn->query("SELECT * FROM data_pegawai_bkd WHERE nip='$nip'");
    if ($pegawaiResult->num_rows == 0) {
        $error = "NIP tidak ditemukan dalam database khusus pegawai BKPSDMD Merangin.";
    } else {
        // 2. Check if already registered
        $userResult = $conn->query("SELECT * FROM users WHERE nip='$nip'");
        if ($userResult->num_rows > 0) {
            $error = "NIP ini sudah terdaftar pada akun CiviCore, silahkan Login.";
        } else {
            // ✅ Set session flag + store verified NIP
            $_SESSION['allow_signup'] = true;
            $_SESSION['verified_nip'] = $nip;
            
            // ✅ Redirect to signup.php with NIP parameter
            header("Location: signup.php?nip=" . urlencode($nip));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi NIP Akun CiviCore</title>
    <link href="verify_nip.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/icon/button/logo2.png">
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="index.php"><img src="../icon/BKPLogo3.png" width="150" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
        </div>
    </div>

    <div class="flex-container">
    <div class="form-box">
        <h2>Verifikasi NIP Sebelum Registrasi</h2>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nip">Masukkan NIP Anda:</label><br>
            <input type="text" name="nip" placeholder="Nomor Induk Pegawai" id="nip" required>
            <button type="submit">Verifikasi</button>
        </form>
    </div>
    </div>

</body>
</html>
