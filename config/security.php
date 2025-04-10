<?php
// Prevent direct access to this file
if (!defined('SECURE_ACCESS')) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

// Set secure headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'");

// Session security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate session
function check_session() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /syselectrohogar/");
        exit();
    }
}