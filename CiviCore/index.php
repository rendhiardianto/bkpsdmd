<?php
// Prevent back button after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include "db.php";
include "config.php"; // make sure path is correct

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_destroy();
}

$showResend = false; // flag to control button visibility

// ---------------- LOGIN ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $nip = $conn->real_escape_string($_POST['nip']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE nip='$nip'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['verified'] == 0) {
            echo "<script>alert('NIP Anda belum diverifikasi. Klik Resend Verification.');</script>";
            $showResend = true; // show button now
        } 
        elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            if ($user['role'] === 'super_admin') {
                header("Location: dashboard_super_admin.php");
            } else {
                header("Location: dashboard_admin.php");
            }
            exit();
        } else {
            echo "<script>alert('Kata Sandi Anda salah!');</script>";
        }
    }

    else {
        echo "<script>alert('NIP tidak ditemukan! Silahkan daftar.');</script>";
    }
}

// ---------------- RESEND VERIFICATION ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resend'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['verified'] == 1) {
            echo "<script>alert('Akun anda sudah diverfikasi, Silahkan Login.');</script>";
        } else {
            $token = bin2hex(random_bytes(16));
            $conn->query("UPDATE users SET verify_token='$token' WHERE email='$email'");

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bkd.merangin@gmail.com';  // 🔹 replace with your Gmail
                $mail->Password = 'dlmh zkgz awku aokg';   // 🔹 use Gmail App Password (not normal password!)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('yourgmail@gmail.com', 'BKPSDMD Merangin');
                $mail->addAddress($email, $user['fullname']);

                $verifyLink = $baseUrl . "/verify.php?token=" . urlencode($token);
                $mail->isHTML(true);
                $mail->Subject = "Verifikasi Akun CiviCore Anda";
                $mail->Body = "
                Halo #KantiASN, $fullname,<br><br>
                Mohon untuk verifikasi ulang email anda terlebih dahulu, klik tombol di bawah ini:<br><br>

                <a href='$verifyLink' 
                  style='display:inline-block; padding:12px 24px; 
                          background-color:#007bff; color:#ffffff; 
                          text-decoration:none; font-size:16px; 
                          border-radius:5px;'>
                          Verifikasi
                </a>

                <br><br>
                Jika tombol di atas tidak berfungsi, Anda juga bisa klik link ini:<br>
                <a href='$verifyLink'>$verifyLink</a><br><br>

                Best Regards,<br>
                Tim CiviCore BKPSDMD Kab. Merangin
                ";

                $mail->send();
                echo "<script>alert('Email verifikasi sudah terkirim! Periksa kotak masuk email Anda.');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        }
    } else {
        echo "<script>alert('NIP tidak terdaftar pada akun CiviCore. Silahkan daftar akun.');</script>";
    }
}
?>

<!DOCTYPE HTML>
<html hreflang="id">
<head>
<meta charset="UTF-8">
    <!-- Google Analytics (deferred) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-65T4XSDM2Q');
    </script>
    
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google-site-verification" content="e4QWuVl6rDrDmYm3G1gQQf6Mv2wBpXjs6IV0kMv4_cM" />

<title>Masuk ke CiviCore - BKPSDMD Kabupaten Merangin</title>

<link rel="shortcut icon" href="/icon/button/logo2.png">

<link rel="stylesheet" href="/fontFamily.css">
<link href="index.css" rel="stylesheet" type="text/css">
<link href="/headerFooter.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <video autoplay muted loop id="myVideo" width="100%" preload="metadata">
        <source src="/videos/CiviCoreVideo.mp4" type="video/mp4">
    </video>

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
            <a href="/index.php"><img src="/icon/BKPLogo3.png" id="bkpsdmdLogo" alt="Logo BKPSDMD"></a>	
        </div>
    </div>
    
    <div class="flex-container">
        <div class="flex-item-left">
            <h2>THAT HORIZON MIGHT BE CLOSER THAN YOU THINK!</h2>
            <p>Just manage your task from anywhere in the world. Get things done, stay on top of your work, wherever you are.</p>
        </div>
        
        <div class="flex-item-right">
            <form action="" method="POST">
                <div style="text-align: center; font-size:40px;"><b>Login CiviCore</b></div>
                <div class="subtitle" style="text-align: center;">The Core of Civil Service Management</div>
                <br><label>NIP</label>
                <br><input type="nip" placeholder="Masukkan Nomor Induk Pegawai" name="nip" required></p>

                <p><label>Kata Sandi</label>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                    <i class="fa-solid fa-eye toggle-eye" id="togglePassword"></i>
                </div>
                <p><a href="forgot_password/forgot_password.php" style="text-decoration: underline; color:#00b4d8;">Lupa Kata Sandi?</a></p>
                <input type="submit" class="btn btn-success btn-block" name="login" value="Masuk"/>

                <?php if ($showResend): ?>
                    <p><a href="resend_verification.php?email=<?php echo urlencode($email); ?>" class="resend-btn">Kirim ulang verifikasi?</a></p>
                <?php endif; ?>
            </form>
            <p style="margin-top: 30px;">Belum ada akun? daftar <a href="verify_nip.php" style="text-decoration: underline; color: #00b4d8;">di sini</a></p>               
        </div>
        
    </div>

<div id="footer"></div>
<script>
fetch("footer.php")
  .then(response => response.text())
  .then(data => {
    document.getElementById("footer").innerHTML = data;
  });
</script>
<script src="/JavaScript/script.js"></script>

<script>
document.getElementById("togglePassword").addEventListener("click", function() {
    let pwd = document.getElementById("password");
    if (pwd.type === "password") {
        pwd.type = "text";
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
    } else {
        pwd.type = "password";
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
    }
});
</script>


</body>
</html>