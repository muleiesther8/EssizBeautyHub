<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Customer Dashboard
// BIT3208 Advanced Web Design and Development
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

requireLogin();

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$skin_type = $_SESSION['skin_type'] ?? 'All';

// Stats
$orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM orders WHERE user_id=$user_id"))['t'];
$cart_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) as t FROM cart WHERE user_id=$user_id"))['t'];
$wish_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM wishlist WHERE user_id=$user_id"))['t'];
$routine_count= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM beauty_routines WHERE user_id=$user_id"))['t'];
$total_spent  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as t FROM orders WHERE user_id=$user_id"))['t'];

// Smart recommendations based on skin type
$skin_filter = mysqli_real_escape_string($conn, $skin_type);
$rec_result  = mysqli_query($conn,
    "SELECT * FROM products WHERE skin_type='$skin_filter' OR skin_type='All' ORDER BY rating DESC LIMIT 4"
);

// Recent orders
$recent_orders = mysqli_query($conn,
    "SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 3"
);

$icons = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];
$user  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
      <?php if (!empty($_SESSION['profile_photo'])): ?>
  <img src="images/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
       style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--pink);"/>
<?php endif; ?>
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="routine_builder.php">Routines</a></li>
      <li><a href="cart.php">Cart</a></li>
      <li><a href="dashboard.php" class="active">My Account</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
      <li><a href="profile.php">👤 Profile</a></li>
    </ul>
  </div>
</nav>

<div class="dashboard-header">
  <div class="container">
    <div class="dashboard-welcome">
      <div class="welcome-avatar">
  <?php if (!empty($_SESSION['profile_photo'])): ?>
    <img src="images/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
         style="width:64px;height:64px;border-radius:50%;object-fit:cover;"/>
  <?php else: ?>
    <?php echo strtoupper(substr($full_name,0,1)); ?>
  <?php endif; ?>
</div>
        <h1>Welcome back, <?php echo htmlspecialchars(explode(' ',$full_name)[0]); ?>! 🌸</h1>
        <p>Skin type: <strong><?php echo htmlspecialchars($skin_type); ?></strong> &nbsp;|&nbsp;
           Budget: <strong><?php echo ucfirst($user['budget'] ?? 'medium'); ?></strong> &nbsp;|&nbsp;
           Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
      </div>
    </div>
  </div>
</div>

<div class="container dashboard-body">

  <!-- Stats -->
  <div class="dash-stats">
    <div class="dash-stat-card">
      <div class="dash-stat-icon">📦</div>
      <div class="dash-stat-info"><h3><?php echo $orders_count; ?></h3><p>Total Orders</p></div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">💰</div>
      <div class="dash-stat-info"><h3>KES <?php echo number_format($total_spent); ?></h3><p>Total Spent</p></div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">🛒</div>
      <div class="dash-stat-info"><h3><?php echo $cart_count; ?></h3><p>Items in Cart</p></div>
    </div>
    <div class="dash-stat-card">
      <div class="dash-stat-icon">🌸</div>
      <div class="dash-stat-info"><h3><?php echo $routine_count; ?></h3><p>My Routines</p></div>
    </div>
  </div>

  <!-- Recent Orders -->
  <?php if ($orders_count > 0): ?>
  <div class="dash-section">
    <h2 class="dash-section-title">Recent Orders</h2>
    <?php while ($o = mysqli_fetch_assoc($recent_orders)):
      $status_class = ['Pending'=>'status--pending','Packed'=>'status--packed','On the way'=>'status--shipping','Delivered'=>'status--delivered'][$o['order_status']] ?? '';
    ?>
    <div style="background:white;border:1.5px solid var(--border);border-radius:var(--radius-md);padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <strong>Order #<?php echo $o['order_id']; ?></strong>
        <span style="font-size:12px;color:var(--charcoal-light);margin-left:12px;"><?php echo date('d M Y', strtotime($o['created_at'])); ?></span>
      </div>
      <span class="status-badge <?php echo $status_class; ?>"><?php echo $o['order_status']; ?></span>
      <strong style="color:var(--pink);">KES <?php echo number_format($o['total_amount']); ?></strong>
      <a href="orders.php" class="btn-sm">Track →</a>
    </div>
    <?php endwhile; ?>
    <a href="orders.php" style="color:var(--pink);font-size:14px;">View all orders →</a>
  </div>
  <?php endif; ?>

  <!-- Smart Recommendations -->
  <div class="dash-section">
    <h2 class="dash-section-title">✨ Recommended for Your <?php echo htmlspecialchars($skin_type); ?> Skin</h2>
    <p class="dash-section-sub">Handpicked products based on your skin type and preferences</p>
    <div class="products-grid">
      <?php while ($p = mysqli_fetch_assoc($rec_result)):
        $stars = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5-round($p['rating']));
        $icon  = $icons[$p['category']] ?? '✨';
      ?>
      <div class="product-card">
        <div class="product-image"><div class="product-icon"><?php echo $icon; ?></div></div>
        <div class="product-info">
          <span class="product-category"><?php echo $p['category']; ?></span>
          <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
          <div class="product-rating">
            <span class="stars"><?php echo $stars; ?></span>
            <span class="rating-num">(<?php echo $p['rating']; ?>)</span>
          </div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($p['price']); ?></span>
            <a href="cart.php?add=<?php echo $p['product_id']; ?>" class="btn-cart">+ Cart</a>
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
      <a href="products.php"       class="quick-link"><span>🛍️</span><p>Shop Products</p></a>
      <a href="cart.php"           class="quick-link"><span>🛒</span><p>View Cart</p></a>
      <a href="orders.php"         class="quick-link"><span>📦</span><p>My Orders</p></a>
      <a href="routine_builder.php"class="quick-link"><span>🌸</span><p>My Routines</p></a>
      <a href="products.php"       class="quick-link"><span>❤️</span><p>Wishlist</p></a>
      <a href="profile.php"        class="quick-link"><span>👤</span><p>Edit Profile</p></a>
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