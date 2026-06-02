<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Admin Dashboard
// BIT3208 Advanced Web Design and Development
// File: admin/dashboard.php
// ============================================================

require_once '../includes/db_connect.php';
require_once '../includes/session.php';


requireAdmin();

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'customer'"))['total'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as total FROM orders"))['total'];

// Recent orders
$orders_result = mysqli_query($conn, "
    SELECT o.order_id, u.full_name, o.total_amount, o.order_status, o.payment_method, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC LIMIT 8
");

// Low stock products
$low_stock = mysqli_query($conn, "SELECT name, stock, category FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">

<!-- Admin Sidebar -->
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <span>✦</span> Essiz Admin
    </div>
    <nav class="admin-nav">
      <a href="dashboard.php" class="admin-nav-link active">📊 Dashboard</a>
      <a href="products.php"  class="admin-nav-link">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link">👥 Users</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Admin Main -->
  <main class="admin-main">

    <!-- Header -->
    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 🌸</p>
      </div>
      <div class="admin-date"><?php echo date('l, d F Y'); ?></div>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats">
      <div class="admin-stat-card admin-stat-card--pink">
        <div class="admin-stat-icon">💄</div>
        <div class="admin-stat-info">
          <h3><?php echo $total_products; ?></h3>
          <p>Total Products</p>
        </div>
      </div>
      <div class="admin-stat-card admin-stat-card--lavender">
        <div class="admin-stat-icon">👥</div>
        <div class="admin-stat-info">
          <h3><?php echo $total_users; ?></h3>
          <p>Total Customers</p>
        </div>
      </div>
      <div class="admin-stat-card admin-stat-card--nude">
        <div class="admin-stat-icon">📦</div>
        <div class="admin-stat-info">
          <h3><?php echo $total_orders; ?></h3>
          <p>Total Orders</p>
        </div>
      </div>
      <div class="admin-stat-card admin-stat-card--green">
        <div class="admin-stat-icon">💰</div>
        <div class="admin-stat-info">
          <h3>KES <?php echo number_format($total_revenue); ?></h3>
          <p>Total Revenue</p>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="admin-charts">
      <div class="admin-chart-card">
        <h3>Sales by Category</h3>
        <canvas id="categoryChart" height="200"></canvas>
      </div>
      <div class="admin-chart-card">
        <h3>Order Status Overview</h3>
        <canvas id="statusChart" height="200"></canvas>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>Recent Orders</h3>
        <a href="orders.php" class="view-all">View All →</a>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($orders_result) > 0):
              while ($order = mysqli_fetch_assoc($orders_result)):
                $status_class = [
                  'Pending'    => 'status--pending',
                  'Packed'     => 'status--packed',
                  'On the way' => 'status--shipping',
                  'Delivered'  => 'status--delivered',
                ][$order['order_status']] ?? '';
            ?>
            <tr>
              <td><strong>#<?php echo $order['order_id']; ?></strong></td>
              <td><?php echo htmlspecialchars($order['full_name']); ?></td>
              <td>KES <?php echo number_format($order['total_amount']); ?></td>
              <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
              <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $order['order_status']; ?></span></td>
              <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
              <td><a href="orders.php?view=<?php echo $order['order_id']; ?>" class="btn-sm">View</a></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align:center;color:#888;padding:30px;">No orders yet</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Low Stock Warning -->
    <?php if (mysqli_num_rows($low_stock) > 0): ?>
    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>⚠ Low Stock Alert</h3>
        <a href="products.php" class="view-all">Manage →</a>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr><th>Product</th><th>Category</th><th>Stock</th></tr>
          </thead>
          <tbody>
            <?php while ($p = mysqli_fetch_assoc($low_stock)): ?>
            <tr>
              <td><?php echo htmlspecialchars($p['name']); ?></td>
              <td><?php echo htmlspecialchars($p['category']); ?></td>
              <td><span style="color:#E53E3E;font-weight:600;"><?php echo $p['stock']; ?> left</span></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </main>
</div>

<script>
// Category Sales Chart
const catCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(catCtx, {
  type: 'doughnut',
  data: {
    labels: ['Skincare', 'Makeup', 'Haircare', 'Perfumes', 'Accessories'],
    datasets: [{
      data: [35, 25, 20, 12, 8],
      backgroundColor: ['#C85B7A','#9B84CC','#C4956A','#5B9EC8','#6BBE8A'],
      borderWidth: 0
    }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Order Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'bar',
  data: {
    labels: ['Pending', 'Packed', 'On the way', 'Delivered'],
    datasets: [{
      label: 'Orders',
      data: [<?php
        $statuses = ['Pending','Packed','On the way','Delivered'];
        $counts   = [];
        foreach ($statuses as $s) {
          $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE order_status = '$s'"));
          $counts[] = $r['c'];
        }
        echo implode(',', $counts);
      ?>],
      backgroundColor: ['#E6A855','#9B84CC','#5B9EC8','#6BBE8A'],
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});
</script>

</body>
</html>