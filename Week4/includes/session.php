<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Session Management
// BIT3208 Advanced Web Design and Development
// File: includes/session.php
// ============================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// Check if user is logged in
// ============================================================
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// ============================================================
// Check if user is admin
// ============================================================
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// ============================================================
// Require login — redirect if not logged in
// ============================================================
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php?msg=Please login to continue');
        exit();
    }
}

// ============================================================
// Require admin — redirect if not admin
// ============================================================
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header('Location: ../login.php?msg=Admin access required');
        exit();
    }
}

// ============================================================
// Redirect if already logged in
// ============================================================
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit();
    }
}
?>