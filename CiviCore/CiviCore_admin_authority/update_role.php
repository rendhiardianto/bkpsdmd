<?php
include "../db.php";
session_start();

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    exit("Unauthorized");
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['role'])) {
        $id   = intval($_POST['id']);
        $role = $conn->real_escape_string($_POST['role']);

        // Only allow valid roles
        if (!in_array($role, ['super_admin', 'admin', 'master', 'verificator_jf', 'verificator_kp', 'verificator_cuti', 'verificator_presensi'])) {
            exit("Invalid role");
        }

        $sql = "UPDATE users SET role='$role' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "Role updated successfully";
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Missing data";
    }
} else {
    echo "Invalid method";
}
