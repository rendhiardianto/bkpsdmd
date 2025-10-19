<?php

include "db.php";

// Block if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check role
function requireRole($roles = []) {
    if (!isset($_SESSION['role'])) {
        header("Location: index.php");
        exit;
    }

    // Normalize input to array
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!in_array($_SESSION['role'], $roles)) {
        // Role not allowed → redirect to login or dashboard
        header("Location: index.php");
        exit;
    }
}

