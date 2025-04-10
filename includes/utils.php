<?php
function formatMoney($amount) {
    return number_format($amount, 2, '.', ',');
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function generateSAPNumber() {
    return 'SAP-' . date('Ymd') . '-' . rand(1000, 9999);
}

function checkPermission($requiredRole) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $requiredRole) {
        header('Location: ' . SITE_URL . '/403.php');
        exit();
    }
}