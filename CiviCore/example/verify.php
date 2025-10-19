<?php
include "db.php";

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $result = $conn->query("SELECT * FROM users WHERE verify_token='$token' AND verified=0");

    if ($result->num_rows > 0) {
        $conn->query("UPDATE users SET verified=1, verify_token=NULL WHERE verify_token='$token'");
        echo "<h2>Email verified! ✅</h2><p>You can now <a href='index.php'>login</a>.</p>";
    } else {
        echo "<h2>Invalid or expired verification link ❌</h2>";
    }
} else {
    echo "<h2>No token provided</h2>";
}
?>
