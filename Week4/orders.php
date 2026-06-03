<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

requireLogin();

$user_id = $_SESSION['user_id'];

$orders_query = "
    SELECT o.order_id, o.total_amount, o.order_status, o.payment_method, o.delivery_location, o.created_at,
           COUNT(oi.item_id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = $user_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
";
$orders_result = mysqli_query($conn, $orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — My Orders</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="cart.php">Cart</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="orders.php" class="active">My Orders</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>📦 My Orders</h1>
    <p>Track and manage your orders</p>
  </div>
</div>

<div class="container orders-layout">

  <?php if (mysqli_num_rows($orders_result) > 0): ?>

    <?php while ($order = mysqli_fetch_assoc($orders_result)):
      $status_class = [
        'Pending'    => 'status--pending',
        'Packed'     => 'status--packed',
        'On the way' => 'status--shipping',
        'Delivered'  => 'status--delivered',
      ][$order['order_status']] ?? '';
      $timeline_steps  = ['Pending', 'Packed', 'On the way', 'Delivered'];
      $current_index   = array_search($order['order_status'], $timeline_steps);
    ?>
    <div class="order-card">
      <div class="order-card-header">
        <div>
          <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
          <div class="order-date"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
          <div class="order-amount">KES <?php echo number_format($order['total_amount']); ?></div>
          <span class="status-badge <?php echo $status_class; ?>"><?php echo $order['order_status']; ?></span>
        </div>
      </div>

      <div class="order-timeline">
        <?php foreach ($timeline_steps as $idx => $step): ?>
          <div class="timeline-step <?php echo $idx <= $current_index ? 'active' : ''; ?> <?php echo $idx < $current_index ? 'completed' : ''; ?>">
            <div class="timeline-dot">
              <?php
                if ($idx < $current_index) echo '✓';
                else echo ($idx === 0 ? '📋' : ($idx === 1 ? '📦' : ($idx === 2 ? '🚚' : '✓')));
              ?>
            </div>
            <div class="timeline-label"><?php echo $step; ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
        <p><strong>Items:</strong> <?php echo $order['item_count']; ?> product(s)</p>
        <p><strong>Delivery Location:</strong> <?php echo htmlspecialchars($order['delivery_location']); ?></p>
        <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
      </div>
    </div>
    <?php endwhile; ?>

  <?php else: ?>
    <div style="text-align:center;padding:80px 24px;">
      <div style="font-size:64px;margin-bottom:16px;">📦</div>
      <h2 style="font-family:var(--font-display);font-size:28px;margin-bottom:8px;">No orders yet</h2>
      <p style="color:var(--charcoal-light);margin-bottom:24px;">Start shopping and your orders will appear here.</p>
      <a href="products.php" class="btn-primary">Start Shopping</a>
    </div>
  <?php endif; ?>

</div>

<footer class="footer">
  <div class="container">
    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> Essiz Beauty Hub — BIT3208 Advanced Web Design and Development</p>
    </div>
  </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>