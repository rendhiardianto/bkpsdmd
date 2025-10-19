<?php
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    exit(json_encode(["status" => "error", "message" => "Unauthorized"]));
}

$action = $_POST['action'] ?? '';

if ($action === "delete") {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM users WHERE id=$id");
    echo json_encode(["status" => "success", "message" => "User deleted"]);
}

if ($action === "edit") {
    $id = (int)$_POST['id'];
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    $sql = "UPDATE users SET fullname='$fullname', email='$email', role='$role' WHERE id=$id";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "User updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }
}