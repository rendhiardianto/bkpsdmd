<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $conn->query("UPDATE users SET reset_token='$token', reset_expiry='$expiry' WHERE email='$email'");

        $reset_link = "http://localhost/bkpsdmd-cms/cms/example/reset_password.php?token=$token";
        
        // Simple mail (works if PHP mail is configured)
        $subject = "Password Reset";
        $message = "Click here to reset your password: $reset_link";
        $headers = "From: no-reply@bkpsdmd.meranginkab.go.id";
        mail($email, $subject, $message, $headers);

        echo "<script>alert('Password reset link sent! Check your email.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('No account found with that email!'); window.location='forgot_password.php';</script>";
    }
}
?>
