<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Product Detail + Reviews
// BIT3208 Advanced Web Design and Development
// File: product_detail.php
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

$product_id = (int)($_GET['id'] ?? 0);
if (!$product_id) { header('Location: products.php'); exit(); }

// Fetch product
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$product) { header('Location: products.php'); exit(); }

$icons = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];
$icon  = $icons[$product['category']] ?? '✨';

// Submit review
$review_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $rating  = (int)$_POST['rating'];
    $comment = trim($_POST['comment'] ?? '');
    $uid     = $_SESSION['user_id'];

    // Check if already reviewed
    $check = mysqli_prepare($conn, "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($check, "ii", $uid, $product_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $review_message = 'already';
    } elseif ($rating >= 1 && $rating <= 5) {
        $ins = mysqli_prepare($conn, "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($ins, "iiis", $uid, $product_id, $rating, $comment);
        if (mysqli_stmt_execute($ins)) {
            // Update product rating
            $avg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE product_id = $product_id"));
            $new_rating = round($avg['avg'], 1);
            $new_count  = $avg['cnt'];
            mysqli_query($conn, "UPDATE products SET rating = $new_rating, review_count = $new_count WHERE product_id = $product_id");
            $product['rating']       = $new_rating;
            $product['review_count'] = $new_count;
            $review_message = 'success';
        }
    }
}

// Fetch reviews
$reviews = mysqli_query($conn, "
    SELECT r.*, u.full_name FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.product_id = $product_id
    ORDER BY r.created_at DESC
");

// Related products
$related = mysqli_query($conn, "
    SELECT * FROM products
    WHERE category = '{$product['category']}' AND product_id != $product_id
    ORDER BY rating DESC LIMIT 4
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — <?php echo htmlspecialchars($product['name']); ?></title>
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
      <?php if (isLoggedIn()): ?>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="dashboard.php">My Account</a></li>
        <li><a href="logout.php" class="btn-nav">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php" class="btn-nav">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Breadcrumb -->
<div style="background:var(--nude);padding:80px 0 16px;">
  <div class="container">
    <p style="font-size:13px;color:var(--charcoal-light);">
      <a href="index.php" style="color:var(--pink);">Home</a> →
      <a href="products.php" style="color:var(--pink);">Products</a> →
      <a href="products.php?category=<?php echo urlencode($product['category']); ?>" style="color:var(--pink);"><?php echo $product['category']; ?></a> →
      <?php echo htmlspecialchars($product['name']); ?>
    </p>
  </div>
</div>

<!-- Product Detail -->
<div class="container" style="padding:40px 24px;">
  <div class="product-detail-layout">

    <!-- Product Image -->
    <div class="product-detail-image">
      <div class="product-detail-icon"><?php echo $icon; ?></div>
      <?php if ($product['stock'] < 10): ?>
        <div class="stock-warning">⚠ Only <?php echo $product['stock']; ?> left in stock!</div>
      <?php endif; ?>
    </div>

    <!-- Product Info -->
    <div class="product-detail-info">
      <span class="product-category"><?php echo $product['category']; ?></span>
      <h1 style="font-family:var(--font-display);font-size:36px;font-weight:400;margin:8px 0;"><?php echo htmlspecialchars($product['name']); ?></h1>

      <div class="product-rating" style="margin-bottom:16px;">
        <span class="stars" style="font-size:20px;"><?php echo str_repeat('★', round($product['rating'])) . str_repeat('☆', 5 - round($product['rating'])); ?></span>
        <span style="font-size:18px;font-weight:600;color:var(--charcoal);margin-left:8px;"><?php echo $product['rating']; ?></span>
        <span style="font-size:14px;color:var(--charcoal-light);">(<?php echo $product['review_count']; ?> reviews)</span>
      </div>

      <div style="font-size:32px;font-weight:700;color:var(--pink);margin-bottom:20px;">
        KES <?php echo number_format($product['price']); ?>
      </div>

      <p style="font-size:15px;color:var(--charcoal-mid);line-height:1.7;margin-bottom:24px;">
        <?php echo htmlspecialchars($product['description']); ?>
      </p>

      <div class="product-detail-meta">
        <div class="meta-item"><span>Skin Type</span><strong><?php echo $product['skin_type']; ?></strong></div>
        <div class="meta-item"><span>Category</span><strong><?php echo $product['category']; ?></strong></div>
        <div class="meta-item"><span>Stock</span><strong><?php echo $product['stock']; ?> units</strong></div>
      </div>

      <div style="display:flex;gap:12px;margin-top:28px;flex-wrap:wrap;">
        <a href="cart.php?add=<?php echo $product['product_id']; ?>" class="btn-primary" style="flex:1;text-align:center;">
          🛒 Add to Cart
        </a>
        <a href="routine_builder.php" class="btn-outline">+ Add to Routine</a>
      </div>
    </div>
  </div>

  <!-- Reviews Section -->
  <div class="reviews-section">
    <h2 style="font-family:var(--font-display);font-size:28px;font-weight:400;margin-bottom:24px;">
      Customer Reviews (<?php echo $product['review_count']; ?>)
    </h2>

    <!-- Write Review -->
    <?php if (isLoggedIn()): ?>
      <?php if ($review_message === 'success'): ?>
        <div class="form-message form-message--success">✓ Review submitted successfully!</div>
      <?php elseif ($review_message === 'already'): ?>
        <div class="form-message form-message--info">ℹ You have already reviewed this product.</div>
      <?php else: ?>
      <div class="review-form-card">
        <h3>Write a Review</h3>
        <form method="POST">
          <div class="star-rating" id="starRating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>">
              <label for="star<?php echo $i; ?>">★</label>
            <?php endfor; ?>
          </div>
          <div class="form-group" style="margin-top:16px;">
            <textarea name="comment" placeholder="Share your experience with this product..."
              style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:14px;font-family:var(--font-body);outline:none;resize:vertical;min-height:100px;"></textarea>
          </div>
          <button type="submit" class="btn-primary">Submit Review</button>
        </form>
      </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="form-message form-message--info">
        <a href="login.php" style="color:var(--pink);">Login</a> to write a review.
      </div>
    <?php endif; ?>

    <!-- Reviews List -->
    <div class="reviews-list">
      <?php if (mysqli_num_rows($reviews) > 0):
        while ($review = mysqli_fetch_assoc($reviews)):
          $r_stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
      ?>
      <div class="review-card">
        <div class="review-header">
          <div class="review-avatar"><?php echo strtoupper(substr($review['full_name'], 0, 1)); ?></div>
          <div>
            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
            <div class="stars" style="font-size:14px;"><?php echo $r_stars; ?></div>
          </div>
          <span style="margin-left:auto;font-size:12px;color:var(--charcoal-light);"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
        </div>
        <p style="font-size:14px;color:var(--charcoal-mid);margin-top:10px;line-height:1.6;">
          <?php echo htmlspecialchars($review['comment']); ?>
        </p>
      </div>
      <?php endwhile; else: ?>
      <p style="color:var(--charcoal-light);text-align:center;padding:40px;">No reviews yet. Be the first to review!</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Related Products -->
  <?php if (mysqli_num_rows($related) > 0): ?>
  <div style="margin-top:60px;">
    <h2 style="font-family:var(--font-display);font-size:28px;font-weight:400;margin-bottom:24px;">Related Products</h2>
    <div class="products-grid">
      <?php while ($rp = mysqli_fetch_assoc($related)):
        $r_icon  = $icons[$rp['category']] ?? '✨';
        $r_stars = str_repeat('★', round($rp['rating'])) . str_repeat('☆', 5 - round($rp['rating']));
      ?>
      <div class="product-card">
        <div class="product-image"><div class="product-icon"><?php echo $r_icon; ?></div></div>
        <div class="product-info">
          <span class="product-category"><?php echo $rp['category']; ?></span>
          <h3 class="product-name"><?php echo htmlspecialchars($rp['name']); ?></h3>
          <div class="product-rating"><span class="stars"><?php echo $r_stars; ?></span></div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($rp['price']); ?></span>
            <a href="product_detail.php?id=<?php echo $rp['product_id']; ?>" class="btn-cart">View</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
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