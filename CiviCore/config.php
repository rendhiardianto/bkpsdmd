<?php
// Automatically detect http/https
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Detect current host (localhost or your domain)
$host = $_SERVER['HTTP_HOST'];

// If your project is inside a folder, put it here (example: bkpsdmd-CiviCore/CiviCore)
$projectPath = "/CiviCore"; // change if needed

// Build base URL
$baseUrl = $protocol . "://" . $host . $projectPath;
?>
