<?php
include "db.php";

// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND verified=0");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fullname = $user['fullname'];
        $token = $user['verify_token'];
        $lastResend = $user['last_resend'];

        // ✅ Cooldown check (5 minutes)
    if ($lastResend && (time() - strtotime($lastResend)) < 300) {
        $wait = 300 - (time() - strtotime($lastResend));
        echo "<script>alert('Please wait $wait seconds before resending.');window.location='index.php';</script>";
        exit();
    }

        // If user already has a token, reuse it. Otherwise, create a new one.
        if (empty($user['verify_token'])) {
            $token = bin2hex(random_bytes(16));
            $conn->query("UPDATE users SET verify_token='$token' WHERE email='$email'");
        } else {
            $token = $user['verify_token'];
        }

        // Send email with PHPMailer
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
            $mail->Subject = "Resend Verification Email";
            $mail->Body = "Hello $fullname,<br><br>
                          Here is your verification link:<br>
                          <a href='$verifyLink'>$verifyLink</a><br><br><br>
                          Best Regards,<br>Tim PUSDATIN BKPSDMD Kab. Merangin";

            $mail->send();
            // ✅ Update last resend time
            $conn->query("UPDATE users SET last_resend=NOW() WHERE id=" . $user['id']);

            echo "<script>alert('Verification email resent! Check your inbox.'); 
            window.location='index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('No unverified account found with this email.'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Resend Verification</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:400px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, button { width:100%; padding:12px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
    button { background:#e67e22; border:none; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#d35400; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Resend Verification Email</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Resend</button>
    </form>
    <p><a href="index.php">Back to Login</a></p>
  </div>
</body>
</html>
