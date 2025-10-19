<?php
// -------------------- Prevent Browser Cache --------------------
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// -------------------- Start and Destroy Session --------------------
session_start();
session_unset();
session_destroy();

// -------------------- Optional: Clear session cookie --------------------
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// -------------------- Redirect to Login --------------------
header("Location: index.php");
exit;
?>
