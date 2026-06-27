<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6 Admin Export
// BIT3208 Advanced Web Design and Development
// NEW: Export orders and products to CSV
// ============================================================
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

$export_type = $_GET['type'] ?? 'orders';

if ($export_type === 'orders') {
    // Export orders
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="essiz_orders_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID','Customer','Email','Phone','Total (KES)','Payment','Status','Delivery Location','Date']);

    $result = mysqli_query($conn, "
        SELECT o.order_id, u.full_name, u.email, u.phone_number,
               o.total_amount, o.payment_method, o.order_status,
               o.delivery_location, o.created_at
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC
    ");

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            '#' . $row['order_id'],
            $row['full_name'],
            $row['email'],
            $row['phone_number'],
            number_format($row['total_amount']),
            $row['payment_method'],
            $row['order_status'],
            $row['delivery_location'],
            date('d M Y H:i', strtotime($row['created_at']))
        ]);
    }
    fclose($output);
    exit();

} elseif ($export_type === 'products') {
    // Export products
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="essiz_products_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Product ID','Name','Category','Price (KES)','Stock','Skin Type','Rating','Reviews']);

    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY category, name");

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['product_id'],
            $row['name'],
            $row['category'],
            number_format($row['price']),
            $row['stock'],
            $row['skin_type'],
            $row['rating'],
            $row['review_count']
        ]);
    }
    fclose($output);
    exit();

} elseif ($export_type === 'users') {
    // Export users
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="essiz_customers_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['User ID','Full Name','Email','Phone','Skin Type','Budget','Role','Joined']);

    $result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['user_id'],
            $row['full_name'],
            $row['email'],
            $row['phone_number'],
            $row['skin_type'],
            $row['budget'],
            $row['role'],
            date('d M Y', strtotime($row['created_at']))
        ]);
    }
    fclose($output);
    exit();

} else {
    // Export page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Export Data</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span>✦</span> Essiz Admin</div>
    <nav class="admin-nav">
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <a href="dashboard.php" class="admin-nav-link">📊 Dashboard</a>
      <a href="products.php"  class="admin-nav-link">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link">👥 Users</a>
      <a href="reviews.php"   class="admin-nav-link">⭐ Reviews</a>
      <a href="export.php"    class="admin-nav-link active">📥 Export</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-header">
      <div><h1>Export Data</h1><p>Download reports as CSV files</p></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:16px;">

      <div style="background:white;border-radius:var(--radius-lg);padding:32px;text-align:center;box-shadow:var(--shadow);border:1.5px solid var(--border);">
        <div style="font-size:48px;margin-bottom:16px;">📦</div>
        <h3 style="font-family:var(--font-display);font-size:22px;font-weight:400;margin-bottom:8px;">Orders Report</h3>
        <p style="font-size:13px;color:var(--charcoal-light);margin-bottom:20px;">Export all orders with customer details, payment and status</p>
        <a href="export.php?type=orders" class="btn-primary">⬇ Download Orders CSV</a>
      </div>

      <div style="background:white;border-radius:var(--radius-lg);padding:32px;text-align:center;box-shadow:var(--shadow);border:1.5px solid var(--border);">
        <div style="font-size:48px;margin-bottom:16px;">💄</div>
        <h3 style="font-family:var(--font-display);font-size:22px;font-weight:400;margin-bottom:8px;">Products Report</h3>
        <p style="font-size:13px;color:var(--charcoal-light);margin-bottom:20px;">Export all products with prices, stock levels and ratings</p>
        <a href="export.php?type=products" class="btn-primary">⬇ Download Products CSV</a>
      </div>

      <div style="background:white;border-radius:var(--radius-lg);padding:32px;text-align:center;box-shadow:var(--shadow);border:1.5px solid var(--border);">
        <div style="font-size:48px;margin-bottom:16px;">👥</div>
        <h3 style="font-family:var(--font-display);font-size:22px;font-weight:400;margin-bottom:8px;">Customers Report</h3>
        <p style="font-size:13px;color:var(--charcoal-light);margin-bottom:20px;">Export all customer accounts with skin type and budget info</p>
        <a href="export.php?type=users" class="btn-primary">⬇ Download Customers CSV</a>
      </div>

    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>
<?php } ?>