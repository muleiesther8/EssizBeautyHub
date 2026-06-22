<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Admin Dashboard
// BIT3208 Advanced Web Design and Development
// File: admin/dashboard.php
// ============================================================
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

// Stats from DB
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products"))['t'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role='customer'"))['t'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM orders"))['t'];
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as t FROM orders"))['t'];
$total_reviews  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM reviews"))['t'];
$total_routines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM beauty_routines"))['t'];

// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT o.*, u.full_name FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
");

// Low stock
$low_stock = mysqli_query($conn, "SELECT name, stock, category FROM products WHERE stock < 10 ORDER BY stock ASC");

// Sales by category (real data)
$cat_sales = mysqli_query($conn, "
    SELECT p.category, COALESCE(SUM(oi.quantity * oi.unit_price), 0) as revenue
    FROM products p
    LEFT JOIN order_items oi ON p.product_id = oi.product_id
    GROUP BY p.category
");
$cat_labels = []; $cat_data = [];
while ($row = mysqli_fetch_assoc($cat_sales)) {
    $cat_labels[] = $row['category'];
    $cat_data[]   = $row['revenue'];
}

// Order status counts
$status_counts = [];
foreach (['Pending','Packed','On the way','Delivered'] as $s) {
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE order_status='$s'"));
    $status_counts[] = $r['c'];
}

// Monthly revenue (last 6 months)
$monthly = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at,'%b %Y') as month,
           SUM(total_amount) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY created_at ASC
");
$m_labels = []; $m_data = [];
while ($row = mysqli_fetch_assoc($monthly)) {
    $m_labels[] = $row['month'];
    $m_data[]   = $row['revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span>✦</span> Essiz Admin</div>
    <nav class="admin-nav">
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <a href="dashboard.php" class="admin-nav-link active">📊 Dashboard</a>
      <a href="products.php"  class="admin-nav-link">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link">👥 Users</a>
      <a href="reviews.php"   class="admin-nav-link">⭐ Reviews</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 🌸</p>
      </div>
      <div class="admin-date"><?php echo date('l, d F Y'); ?></div>
    </div>

    <!-- Stats -->
    <div class="admin-stats">
      <div class="admin-stat-card admin-stat-card--pink">
        <div class="admin-stat-icon">💄</div>
        <div class="admin-stat-info"><h3><?php echo $total_products; ?></h3><p>Total Products</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--lavender">
        <div class="admin-stat-icon">👥</div>
        <div class="admin-stat-info"><h3><?php echo $total_users; ?></h3><p>Customers</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--nude">
        <div class="admin-stat-icon">📦</div>
        <div class="admin-stat-info"><h3><?php echo $total_orders; ?></h3><p>Total Orders</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--green">
        <div class="admin-stat-icon">💰</div>
        <div class="admin-stat-info"><h3>KES <?php echo number_format($total_revenue); ?></h3><p>Total Revenue</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--pink">
        <div class="admin-stat-icon">⭐</div>
        <div class="admin-stat-info"><h3><?php echo $total_reviews; ?></h3><p>Reviews</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--lavender">
        <div class="admin-stat-icon">🌸</div>
        <div class="admin-stat-info"><h3><?php echo $total_routines; ?></h3><p>Routines Built</p></div>
      </div>
    </div>

    <!-- Charts -->
    <div class="admin-charts">
      <div class="admin-chart-card" style="grid-column:1/-1;">
        <h3>Monthly Revenue (Last 6 Months)</h3>
        <canvas id="revenueChart" height="100"></canvas>
      </div>
    </div>
    <div class="admin-charts">
      <div class="admin-chart-card">
        <h3>Sales by Category</h3>
        <canvas id="categoryChart" height="220"></canvas>
      </div>
      <div class="admin-chart-card">
        <h3>Order Status Overview</h3>
        <canvas id="statusChart" height="220"></canvas>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>All Orders</h3>
        <a href="orders.php" class="view-all">View All →</a>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($recent_orders) > 0):
              while ($o = mysqli_fetch_assoc($recent_orders)):
                $sc = ['Pending'=>'status--pending','Packed'=>'status--packed','On the way'=>'status--shipping','Delivered'=>'status--delivered'][$o['order_status']] ?? '';
            ?>
            <tr>
              <td><strong>#<?php echo $o['order_id']; ?></strong></td>
              <td><?php echo htmlspecialchars($o['full_name']); ?></td>
              <td>KES <?php echo number_format($o['total_amount']); ?></td>
              <td><?php echo $o['payment_method']; ?></td>
              <td><span class="status-badge <?php echo $sc; ?>"><?php echo $o['order_status']; ?></span></td>
              <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" style="text-align:center;color:#888;padding:30px;">No orders yet</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Low Stock -->
    <?php if (mysqli_num_rows($low_stock) > 0): ?>
    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>⚠ Low Stock Alert</h3>
        <a href="products.php" class="view-all">Manage →</a>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead><tr><th>Product</th><th>Category</th><th>Stock</th></tr></thead>
          <tbody>
            <?php while ($p = mysqli_fetch_assoc($low_stock)): ?>
            <tr>
              <td><?php echo htmlspecialchars($p['name']); ?></td>
              <td><?php echo $p['category']; ?></td>
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
// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: <?php echo json_encode($m_labels ?: ['No data']); ?>,
    datasets: [{
      label: 'Revenue (KES)',
      data:  <?php echo json_encode($m_data ?: [0]); ?>,
      borderColor: '#C85B7A',
      backgroundColor: 'rgba(200,91,122,0.1)',
      borderWidth: 2, fill: true, tension: 0.4,
      pointBackgroundColor: '#C85B7A'
    }]
  },
  options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true } } }
});

// Category Chart
new Chart(document.getElementById('categoryChart'), {
  type: 'doughnut',
  data: {
    labels: <?php echo json_encode($cat_labels ?: ['No data']); ?>,
    datasets: [{ data: <?php echo json_encode($cat_data ?: [1]); ?>, backgroundColor: ['#C85B7A','#9B84CC','#C4956A','#5B9EC8','#6BBE8A'], borderWidth:0 }]
  },
  options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
});

// Status Chart
new Chart(document.getElementById('statusChart'), {
  type: 'bar',
  data: {
    labels: ['Pending','Packed','On the way','Delivered'],
    datasets: [{ label:'Orders', data: <?php echo json_encode($status_counts); ?>, backgroundColor:['#E6A855','#9B84CC','#5B9EC8','#6BBE8A'], borderRadius:8 }]
  },
  options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});
</script>
<script src="../js/main.js"></script>
</body>
</html>