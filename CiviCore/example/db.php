<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "signup_demo";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login with cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $result = $conn->query("SELECT * FROM users WHERE remember_token='$token' LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];
    }
}
?>