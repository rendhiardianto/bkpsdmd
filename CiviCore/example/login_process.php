<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['fullname'] = $row['fullname'];
    $_SESSION['role'] = $row['role'];

    // Remember me (same as before)
    if (isset($_POST['remember'])) {
        $token = bin2hex(random_bytes(16));
        setcookie("remember_token", $token, time() + (86400 * 30), "/"); 
        $conn->query("UPDATE users SET remember_token='$token' WHERE id=" . $row['id']);
    }

    // Redirect by role
    if ($row['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();

        } else {
            echo "<script>alert('Invalid password!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that NIP!'); window.location='index.php';</script>";
    }
}
?>
