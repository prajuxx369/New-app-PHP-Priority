<?php
// includes/auth.php - Session management and access control
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

function requireRole($role) {
    if (!isLoggedIn() || getRole() !== $role) {
        header("Location: ../login.php?error=unauthorized");
        exit();
    }
}

function redirectLoggedIn() {
    if (isLoggedIn()) {
        $role = getRole();
        if ($role === 'admin') header("Location: admin/dashboard.php");
        elseif ($role === 'ngo') header("Location: ngo/dashboard.php");
        else header("Location: donor/dashboard.php");
        exit();
    }
}
?>
