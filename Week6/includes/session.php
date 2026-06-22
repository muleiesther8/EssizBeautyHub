<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6
// File: includes/session.php
// ============================================================
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function isLoggedIn()  { return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); }
function isAdmin()     { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function requireLogin(){ if (!isLoggedIn()) { header('Location: login.php?msg=Please login to continue'); exit(); } }
function requireAdmin(){ if (!isLoggedIn() || !isAdmin()) { header('Location: login.php?msg=Admin access required'); exit(); } }
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header(isAdmin() ? 'Location: admin/dashboard.php' : 'Location: dashboard.php');
        exit();
    }
}
?>