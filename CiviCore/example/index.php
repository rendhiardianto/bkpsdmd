<?php
// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include "db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            echo "<script>alert('Your NIP is not verified. Click Resend Verification.');</script>";
            $showResend = true; // show button now
        } 
        elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    }

    else {
        echo "<script>alert('NIP not found! Please register.');</script>";
    }
}

// ---------------- RESEND VERIFICATION ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resend'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['verified'] == 1) {
            echo "<script>alert('Your account is already verified. Please login.');</script>";
        } else {
            $token = bin2hex(random_bytes(16));
            $conn->query("UPDATE users SET verifY_token='$token' WHERE email='$email'");

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bkd.merangin@gmail.com';  // 🔹 replace with your Gmail
                $mail->Password = 'dlmh zkgz awku aokg';   // 🔹 use Gmail App Password (not normal password!)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('yourgmail@gmail.com', 'apa ini');
                $mail->addAddress($email, $user['fullname']);

                $verifyLink = "http://localhost/bkpsdmd-cms/cms/example/verify.php?token=$token";
                $mail->isHTML(true);
                $mail->Subject = "Verify Your Account";
                $mail->Body = "Hello {$user['fullname']},<br><br>
                               Click below to verify your account:<br>
                               <a href='$verifyLink'>$verifyLink</a><br><br><br>
                               Best Regards,<br>Tim PUSDATIN BKPSDMD Kab. Merangin";

                $mail->send();
                echo "<script>alert('Verification email resent! Please check your inbox.');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        }
    } else {
        echo "<script>alert('No account found with that NIP. Please register again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login CMS BKPSDMD Kab. Merangin</title>
    <link rel="shortcut icon" href="images/button/logo2.png">
    <link href="index.css" rel="stylesheet" type="text/css">
</head>

<body>

  <div class="form-box">
    <h2>Masuk</h2>
    <form method="POST">
      <input type="nip" name="nip" placeholder="NIP" required>
      <input type="password" name="password" placeholder="Kata Sandi" required>
      <p><a href="forgot_password.php">Lupa Kata Sandi?</a></p>
      <button type="submit" name="login" class="login-btn">Masuk</button>

    <?php if ($showResend): ?>
    <p><a href="resend_verification.php?email=<?php echo urlencode($email); ?>" class="resend-btn">Kirim ulang verifikasi?</a></p>
    <?php endif; ?>
    </form>
    <p>Belum ada akun? daftar <a href="daftar.php">di sini</a></p>
  </div>
</body>
</html>

