<?php include "db.php"; ?>

<?php
// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nip = $conn->real_escape_string($_POST['nip']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profilePic = null;
    

    if(empty($fullname))
		{
			$errMSG = "Masukkan Nama Depan Anda!";
		}
		else if(empty($email))
		{
			$errMSG = "Masukkan Email Anda!";
		}
		else
		{
			$upload_dir = 'user_images/'; // upload directory
	
			$imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
		
			// valid image extensions
			$valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
		
			// rename uploading image
			$images = rand(1000,1000000).".".$imgExt;
				
			// allow valid image file formats
			if(in_array($imgExt, $valid_extensions))
			{			
				// Check file size
				if($imgSize < 500000)
				{
					move_uploaded_file($tmp_dir,$upload_dir.$images);
				}
				else
				{
					$errMSG = "Maaf, ukuran file foto Anda terlalu besar, maksimal 500KB.";
				}
			}
			else
			{
				$errMSG = "Maaf, hanya file bertipe JPG, JPEG, PNG & GIF saja yang diperbolehkan.";		
			}
    }

    // Validate NIP format (must be 18 digits numeric)
    if (!preg_match('/^[0-9]{18}$/', $nip)) {
    echo "<script>alert('NIP must be exactly 18 digits (numbers only).'); window.location='index.php';</script>";
    exit();
    }
    
    // check if NIP already exists
    $checkNip = $conn->query("SELECT id FROM users WHERE nip='$nip'");
    if ($checkNip->num_rows > 0) {
        echo "<script>alert('This NIP is already registered!'); window.location='index.php';</script>";
        exit();
    }

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    $token = bin2hex(random_bytes(16));
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";}
    else {
        $token = bin2hex(random_bytes(16)); // email verification token
        $sql = "INSERT INTO users (nip, fullname, email, password, profile_pic, role, verified, verification_token)
        VALUES ('$nip', '$fullname', '$email', '$password', '$profilePic', 'user', 0, '$token')";

        if ($conn->query($sql)) {

            // ✅ Send email with PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bkd.merangin@gmail.com';  // 🔹 replace with your Gmail
                $mail->Password = 'dlmh zkgz awku aokg';   // 🔹 use Gmail App Password (not normal password!)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('yourgmail@gmail.com', 'Apa ini');
                $mail->addAddress($email, $fullname);

                // Content
                $verifyLink = "http://localhost/bkpsdmd-cms/cms/example/verify.php?token=$token";
                $mail->isHTML(true);
                $mail->Subject = "Verify your email";
                $mail->Body = "Hello $fullname,<br><br>Please verify your email by clicking this link:<br>
                              <a href='$verifyLink'>$verifyLink</a><br><br><br>
                              Best Regards,<br>Tim PUSDATIN BKPSDMD Kab. Merangin";

                $mail->send();
                echo "<script>alert('Check your Gmail inbox for the verification link!');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sign Up</title>
  <style>
    body { font-family: Arial; background:#f4f6f9; padding:30px; }
    .form-box { max-width:400px; margin:auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
    input, button { width:100%; padding:12px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
    button { background:#3498db; border:none; color:white; font-weight:bold; cursor:pointer; }
    button:hover { background:#2980b9; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Buat Akun CMS</h2>
    <form method="POST">
      <input type="text" name="nip" placeholder="Nomor Induk Pegawai (NIP)" required>
      <input type="text" name="fullname" placeholder="Nama Lengkap" required>
      <input type="email" name="email" placeholder="Alamt Email" required>
      <input type="password" name="password" placeholder="Kata Sandi" required>
      <p>Upload Foto Profil<br>
      <input class="input-group" type="file" name="userPic" accept="image/*"/><p></p>
      <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun CMS? <a href="index.php">Masuk</a></p>
  </div>
</body>
</html>