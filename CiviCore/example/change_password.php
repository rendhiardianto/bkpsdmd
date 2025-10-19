<?php
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // get current password
    $result = $conn->query("SELECT password FROM users WHERE id=$user_id LIMIT 1");
    $row = $result->fetch_assoc();

    if (password_verify($old_password, $row['password'])) {
        $conn->query("UPDATE users SET password='$new_password' WHERE id=$user_id");
        echo "<script>alert('Password changed successfully!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Old password is incorrect!'); window.location='profile.php';</script>";
    }
}
?>  zip_entry_read