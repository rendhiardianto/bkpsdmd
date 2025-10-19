<?php
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE users SET fullname='$fullname' WHERE id=$user_id";
    if ($conn->query($sql)) {
        $_SESSION['fullname'] = $fullname; // update session
        echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.'); window.location='profile.php';</script>";
    }
}
?>
