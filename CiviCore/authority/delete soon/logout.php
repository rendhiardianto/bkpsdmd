<?php
session_start();
session_unset();      // remove all session variables
session_destroy();   // destroy the session

// Prevent back button cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: ../index.php");
exit;
?>
