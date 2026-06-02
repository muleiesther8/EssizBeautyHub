<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Logout
// BIT3208 Advanced Web Design and Development
// File: logout.php
// ============================================================

session_start();
session_unset();
session_destroy();

header('Location: login.php?msg=You have been logged out successfully');
exit();
?>