<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 1 Database Connection
// BIT3208 Advanced Web Design and Development
// File: includes/db_connect.php
// Include this file in any page that needs DB access:
//   require_once 'includes/db_connect.php';
// ============================================================

$db_host = "localhost:3307";
$db_user = "root";
$db_pass = "";           // Default XAMPP password is empty
$db_name = "essizdb_w1"; // Week 1 database

// Attempt connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("
    <div style='font-family:sans-serif;padding:30px;background:#FFF0F3;border:1px solid #E8869A;border-radius:12px;max-width:500px;margin:40px auto;'>
      <h3 style='color:#C85B7A;'>⚠ Database Connection Failed</h3>
      <p style='color:#555;margin-top:10px;'>Could not connect to <strong>essizdb_w1</strong>.</p>
      <p style='color:#555;margin-top:6px;'>Please make sure:</p>
      <ul style='color:#555;margin-top:6px;padding-left:20px;'>
        <li>XAMPP is running (Apache + MySQL)</li>
        <li>You have imported <strong>essizdb_w1.sql</strong> via phpMyAdmin</li>
      </ul>
      <p style='color:#999;font-size:13px;margin-top:12px;'>Error: " . mysqli_connect_error() . "</p>
    </div>
    ");
}

// Set character encoding
mysqli_set_charset($conn, "utf8mb4");

// Optional: confirm in console (remove in production)
// echo "<!-- DB Connected: essizdb_w1 -->";
?>