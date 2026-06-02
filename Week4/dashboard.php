<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Customer Dashboard
// BIT3208 Advanced Web Design and Development
// File: dashboard.php
// ============================================================

require_once 'includes/db_connect.php';
require_once 'includes/session.php';

session_start();
requireLogin();

// Get user info
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$skin_type = '';

// Fetch full user data
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
$skin_type = $user['skin_type'] ?? 'Not set';

// Get order count
$orders_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id");
$orders_count  = mysqli_fetch_assoc($orders_result)['total'];

// Get cart count
$cart_result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$cart_count  = mysqli_fetch_assoc($cart_result)['total'] ?? 0;

// Get wishlist count
$wish_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id");
$wish_count  = mysqli_fetch_assoc($wish_result)['total'];

// Smart recommendations based on skin type
$skin_filter = mysqli_real_escape_string($conn, $skin_type);
$rec_query   = "SELECT * FROM products WHERE skin_type = '$skin_filter' OR skin_type = 'All' ORDER BY rating DESC LIMIT 4";
$rec_result  = mysqli_query($conn, $rec_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — My Dashboard</title>
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
      <li><a href="dashboard.php" class="active">My Account</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- Dashboard Header -->
<div class="dashboard-header">
  <div class="container">
    <div class="dashboard-welcome">
      <div class="welcome-avatar">
        <?php echo strtoupper(substr($full_name, 0, 1)); ?>
      </div>
      <div>
        <h1>Welcome back, <?php echo htmlspecialchars(explode(' ', $full_name)[0]); ?>! 🌸</h1>
        <p>Skin type: <strong><?php echo htmlspecialchars($skin_type); ?></strong> &nbsp;|&nbsp; Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
      </div>
    </div>
  </div>
</div>

<div class="container dashboard-body">

  <!-- Stats Cards -->
  <div class="dash-stats">
    <div class="dash-stat-card">
      <div class="dash-stat-icon">📦</div>
      <div class="dash-stat-info">
        <h3><?php echo $orders_count; ?></h3>
        <p>Total Orders</p>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">🛒</div>
      <div class="dash-stat-info">
        <h3><?php echo $cart_count; ?></h3>
        <p>Items in Cart</p>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">❤️</div>
      <div class="dash-stat-info">
        <h3><?php echo $wish_count; ?></h3>
        <p>Wishlist Items</p>
      </div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">🧴</div>
      <div class="dash-stat-info">
        <h3><?php echo htmlspecialchars($skin_type); ?></h3>
        <p>Your Skin Type</p>
      </div>
    </div>
  </div>

  <!-- Smart Recommendations -->
  <div class="dash-section">
    <h2 class="dash-section-title">✨ Recommended for Your <?php echo htmlspecialchars($skin_type); ?> Skin</h2>
    <p class="dash-section-sub">Handpicked products based on your skin type</p>
    <div class="products-grid">
      <?php while ($product = mysqli_fetch_assoc($rec_result)):
        $stars = str_repeat('★', round($product['rating'])) . str_repeat('☆', 5 - round($product['rating']));
        $icons = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];
        $icon  = $icons[$product['category']] ?? '✨';
      ?>
      <div class="product-card">
        <div class="product-image">
          <div class="product-icon"><?php echo $icon; ?></div>
        </div>
        <div class="product-info">
          <span class="product-category"><?php echo $product['category']; ?></span>
          <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
          <div class="product-rating">
            <span class="stars"><?php echo $stars; ?></span>
            <span class="rating-num">(<?php echo $product['rating']; ?>)</span>
          </div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($product['price']); ?></span>
            <a href="cart.php?add=<?php echo $product['product_id']; ?>" class="btn-cart">+ Cart</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="dash-section">
    <h2 class="dash-section-title">Quick Actions</h2>
    <div class="quick-links">
      <a href="products.php" class="quick-link">
        <span>🛍️</span><p>Shop Products</p>
      </a>
      <a href="cart.php" class="quick-link">
        <span>🛒</span><p>View Cart</p>
      </a>
      <a href="orders.php" class="quick-link">
        <span>📦</span><p>My Orders</p>
      </a>
      <a href="#" class="quick-link">
        <span>❤️</span><p>Wishlist</p>
      </a>
      <a href="#" class="quick-link">
        <span>🌸</span><p>My Routine</p>
      </a>
      <a href="#" class="quick-link">
        <span>👤</span><p>Edit Profile</p>
      </a>
    </div>
  </div>

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