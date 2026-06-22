<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6 Wishlist
// BIT3208 Advanced Web Design and Development
// NEW: Full Wishlist CRUD — Add, View, Remove
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';

// ADD TO WISHLIST
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    header('Location: wishlist.php?added=1'); exit();
}

// REMOVE FROM WISHLIST
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    header('Location: wishlist.php?removed=1'); exit();
}

// CLEAR ENTIRE WISHLIST
if (isset($_GET['clear'])) {
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$user_id");
    header('Location: wishlist.php?cleared=1'); exit();
}

// MOVE TO CART
if (isset($_GET['move_to_cart'])) {
    $product_id = (int)$_GET['move_to_cart'];

    // Add to cart
    $check = mysqli_prepare($conn, "SELECT cart_id, quantity FROM cart WHERE user_id=? AND product_id=?");
    mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

    if ($existing) {
        $new_qty = $existing['quantity'] + 1;
        $upd = mysqli_prepare($conn, "UPDATE cart SET quantity=? WHERE cart_id=?");
        mysqli_stmt_bind_param($upd, "ii", $new_qty, $existing['cart_id']);
        mysqli_stmt_execute($upd);
    } else {
        $ins = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,1)");
        mysqli_stmt_bind_param($ins, "ii", $user_id, $product_id);
        mysqli_stmt_execute($ins);
    }

    // Remove from wishlist
    $del = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    mysqli_stmt_bind_param($del, "ii", $user_id, $product_id);
    mysqli_stmt_execute($del);

    header('Location: cart.php'); exit();
}

// Fetch wishlist items
$wishlist = mysqli_query($conn, "
    SELECT w.wishlist_id, w.created_at, p.*
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = $user_id
    ORDER BY w.created_at DESC
");

$cart_count = getCartCount($conn, $user_id);
$wish_count = mysqli_num_rows($wishlist);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — My Wishlist</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a>
    <ul class="nav-links">
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="wishlist.php" class="active">Wishlist ❤️ <span class="cart-badge"><?php echo $wish_count; ?></span></a></li>
      <li><a href="cart.php">Cart <span class="cart-badge"><?php echo $cart_count; ?></span></a></li>
      <li><a href="dashboard.php">My Account</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>❤️ My Wishlist</h1>
    <p><?php echo $wish_count; ?> saved item(s)</p>
  </div>
</div>

<div class="container" style="padding:40px 24px 80px;">

  <!-- Messages -->
  <?php if (isset($_GET['added'])):   ?><div class="form-message form-message--success">✓ Product added to wishlist!</div><?php endif; ?>
  <?php if (isset($_GET['removed'])): ?><div class="form-message form-message--info">ℹ Product removed from wishlist.</div><?php endif; ?>
  <?php if (isset($_GET['cleared'])): ?><div class="form-message form-message--info">ℹ Wishlist cleared.</div><?php endif; ?>

  <?php if ($wish_count > 0): ?>

  <!-- Wishlist Actions -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <p style="font-size:14px;color:var(--charcoal-light);"><?php echo $wish_count; ?> product(s) in your wishlist</p>
    <a href="wishlist.php?clear=1" class="btn-outline" style="color:var(--danger);border-color:var(--danger);"
       onclick="return confirm('Clear entire wishlist?')">🗑 Clear All</a>
  </div>

  <!-- Wishlist Grid -->
  <div class="products-grid">
    <?php
    mysqli_data_seek($wishlist, 0);
    while ($item = mysqli_fetch_assoc($wishlist)):
      $icon  = getProductIcon($item['category']);
      $stars = getStars($item['rating']);
    ?>
    <div class="product-card">
      <div class="product-image">
        <div class="product-icon"><?php echo $icon; ?></div>
        <!-- Remove from wishlist button -->
        <a href="wishlist.php?remove=<?php echo $item['product_id']; ?>"
           class="wishlist-btn" style="background:var(--pink);color:white;" title="Remove from Wishlist">♥</a>
      </div>
      <div class="product-info">
        <span class="product-category"><?php echo $item['category']; ?></span>
        <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
        <p class="product-desc"><?php echo htmlspecialchars($item['description']); ?></p>
        <div class="product-rating">
          <span class="stars"><?php echo $stars; ?></span>
          <span class="rating-num">(<?php echo $item['rating']; ?>)</span>
        </div>
        <div style="font-size:11px;color:var(--charcoal-light);margin-bottom:10px;">
          Added <?php echo timeAgo($item['created_at']); ?>
        </div>
        <div class="product-footer">
          <span class="product-price">KES <?php echo number_format($item['price']); ?></span>
          <div style="display:flex;gap:6px;">
            <a href="wishlist.php?move_to_cart=<?php echo $item['product_id']; ?>" class="btn-cart">🛒 Add to Cart</a>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <?php else: ?>

  <!-- Empty Wishlist -->
  <div style="text-align:center;padding:80px 24px;">
    <div style="font-size:64px;margin-bottom:16px;">❤️</div>
    <h2 style="font-family:var(--font-display);font-size:28px;margin-bottom:8px;">Your wishlist is empty</h2>
    <p style="color:var(--charcoal-light);margin-bottom:24px;">Browse products and click ♡ to save your favourites!</p>
    <a href="products.php" class="btn-primary">Browse Products</a>
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

<div class="toast" id="toast"></div>
<script src="js/main.js"></script>
</body>
</html>