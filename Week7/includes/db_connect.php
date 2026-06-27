<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7
// File: includes/db_connect.php
// ============================================================
$db_host = "localhost:3307";
$db_user = "root";
$db_pass = "";
$db_name = "essizdb_w7";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:30px;background:#FFF0F3;border:1px solid #E8869A;border-radius:12px;max-width:500px;margin:40px auto;'><h3 style='color:#C85B7A;'>⚠ Database Connection Failed</h3><p style='color:#555;margin-top:10px;'>Could not connect to <strong>essizdb_w7</strong>.</p><p style='color:#999;font-size:13px;margin-top:12px;'>Error: " . mysqli_connect_error() . "</p></div>");
}
mysqli_set_charset($conn, "utf8mb4");
?>