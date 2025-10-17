<?php
session_start();

// 🔹 Restrict access unless verified
if (!isset($_SESSION['allow_signup']) || $_SESSION['allow_signup'] !== true) {
    header("Location: verify_nip.php"); // adjust path
    exit();
}
include "db.php";
include "config.php"; // make sure path is correct


// capture NIP if coming from verify_nip.php
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

// Load PHPMailer classes manually (no Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- NEW: Preload NIP from verify_nip.php ---
$nipFromGet = $_GET['nip'] ?? '';   // from URL
$fullnameFromDB = '';
$jabatanFromDB = '';
$organisasiFromDB = '';
$organisasiIndukFromDB = '';

if (!empty($nipFromGet)) {
    // ✅ Fetch employee info from data_pegawai_bkd (created by add_user.php)
    $stmt = $conn->prepare("SELECT fullname, jabatan, organisasi, organisasi_induk FROM data_pegawai_bkd WHERE nip=?");
    $stmt->bind_param("s", $nipFromGet);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $fullnameFromDB = $row['fullname'];
        $jabatanFromDB = $row['jabatan'];
        $organisasiFromDB = $row['organisasi'];
        $organisasiIndukFromDB = $row['organisasi_induk'];
    }
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nip = $conn->real_escape_string($_POST['nip']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $profilePic = "default.png"; // default
    if (!empty($_FILES['profile_pic']['name'])) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 1 * 1024 * 1024; // 1MB

    $fileTmp  = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileSize = $_FILES['profile_pic']['size'];
    $fileType = mime_content_type($fileTmp);

    // Validate type
    if (!in_array($fileType, $allowedTypes)) {
        echo "<script>alert('❌ Hanya file JPG atau PNG yang diperbolehkan!'); window.location='signup.php';</script>";
        exit();
    }

    // Rename file
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $safeFullname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fullname);
    $newFileName = $nip . "_" . $safeFullname . "." . $ext;
    $uploadPath = __DIR__ . "/uploads/profile_pics/" . $newFileName;

    // ✅ Check if GD functions exist
    $gdAvailable = function_exists('imagecreatefromjpeg') && function_exists('imagecreatetruecolor');

    if ($gdAvailable) {
        // Create image resource
        if ($fileType == 'image/jpeg') {
            $src = imagecreatefromjpeg($fileTmp);
        } elseif ($fileType == 'image/png') {
            $src = imagecreatefrompng($fileTmp);
        }

        // Resize to 400x600
        $width = imagesx($src);
        $height = imagesy($src);
        $newWidth = 400;
        $newHeight = 600;

        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        if ($fileType == 'image/png') {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
            imagefilledrectangle($tmp, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Crop and resize
        $minSide = min($width, $height);
        $srcX = ($width - $minSide) / 2;
        $srcY = ($height - $minSide) / 2;

        imagecopyresampled($tmp, $src, 0, 0, $srcX, $srcY, $newWidth, $newHeight, $minSide, $minSide);

        // Save compressed image
        if ($fileType == 'image/jpeg') {
            imagejpeg($tmp, $uploadPath, 70);
        } elseif ($fileType == 'image/png') {
            imagepng($tmp, $uploadPath, 7);
        }

        imagedestroy($src);
        imagedestroy($tmp);

        // Reduce quality more if still >1MB
        if (filesize($uploadPath) > $maxSize && $fileType == 'image/jpeg') {
            imagejpeg(imagecreatefromjpeg($uploadPath), $uploadPath, 60);
        }

    } else {
        // 🚩 GD not available → skip resizing and move file directly
        move_uploaded_file($fileTmp, $uploadPath);
    }

    $profilePic = $newFileName;
}

    // check if NIP already exists in users
    $checkNip = $conn->query("SELECT id FROM users WHERE nip='$nip'");
    if ($checkNip->num_rows > 0) {
        echo "<script>alert('NIP sudah terdaftar, silahkan login!'); window.location='index.php';</script>";
        exit();
    }

    // check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    $token = bin2hex(random_bytes(16));
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {
        $token = bin2hex(random_bytes(16));
        $sql = "INSERT INTO users 
        (nip, fullname, jabatan, organisasi, organisasi_induk, email, password, profile_pic, role, verified, verify_token)
        VALUES 
        ('$nip', '$fullname', 
         '" . $conn->real_escape_string($jabatanFromDB ?? '') . "', 
         '" . $conn->real_escape_string($organisasiFromDB ?? '') . "', 
         '" . $conn->real_escape_string($organisasiIndukFromDB ?? '') . "', 
         '$email', '$password', '$profilePic', 'admin', 0, '$token')";

        if ($conn->query($sql)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bkd.merangin@gmail.com';
                $mail->Password = 'dlmh zkgz awku aokg'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('yourgmail@gmail.com', 'BKPSDMD Kab. Merangin');
                $mail->addAddress($email, $fullname);

                
                $verifyLink = $baseUrl . "/verify.php?token=" . urlencode($token);
                $mail->isHTML(true);
                $mail->Subject = "Verifkasi email Anda";
                // 🔹 Build the button as styled <a> tag
                $mail->Body = "
                Halo #KantiASN $fullname,<br><br>
                Mohon untuk verifikasi email anda terlebih dahulu, klik tombol di bawah ini:<br><br>

                <a href='$verifyLink' 
                  style='display:inline-block; padding:12px 24px; 
                          background-color:#007bff; color:#ffffff; 
                          text-decoration:none; font-size:16px; 
                          border-radius:5px;'>
                          Verifikasi
                </a>

                <br><br>
                Jika tombol di atas tidak berfungsi, Anda juga bisa klik link ini:<br><b>
                
                <br><a href='$verifyLink'>$verifyLink</a><br><br>

                Best Regards,<br>
                Tim PUSDATIN BKPSDMD Kab. Merangin
                ";

                $mail->send();
                echo "<script>alert('Cek email akun Gmail #KantiASN untuk verifikasi!'); window.location='index.php'; </script>";
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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-65T4XSDM2Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-65T4XSDM2Q');
    </script>
  <title>Daftar Akun CMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="signup.css" rel="stylesheet">
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
        <h2>Registrasi Ulang Akun CMS</h2>
        <form method="POST" enctype="multipart/form-data">
        <!-- NIP + Fullname prefilled if from verify_nip -->
        <input type="text" name="nip" placeholder="Nomor Induk Pegawai (NIP)" value="<?php echo htmlspecialchars($prefilledNip); ?>" <?php echo $readonlyNip; ?> required>
        <input type="text" name="fullname" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($fullnameFromDB); ?>" <?php echo !empty($fullnameFromDB) ? 'readonly' : ''; ?> required>
        <input type="email" name="email" placeholder="Alamat Email" required>
        <input type="password" name="password" placeholder="Kata Sandi" required>
        <p>Upload Foto Profil<br>
        <input class="input-group" type="file" name="profile_pic" accept="image/*"/><p></p>
        <button type="submit">Daftar</button>
        </form>
    </div>

</div>
</body>
</html>
