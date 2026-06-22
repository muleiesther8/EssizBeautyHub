<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Products Page
// BIT3208 Advanced Web Design and Development
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

$icons = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];
$products_result = mysqli_query($conn, "SELECT * FROM products ORDER BY rating DESC");
$total = mysqli_num_rows($products_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Products</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a>
    <button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button>
    <ul class="nav-links" id="navLinks">
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php" class="active">Products</a></li>
      <li><a href="routine_builder.php">Routines</a></li>
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
<div class="page-header">
  <div class="container">
    <h1>All Products</h1>
    <p><?php echo $total; ?> beauty essentials curated for you</p>
  </div>
</div>
<div class="products-layout container">
  <aside class="filters-sidebar" id="filtersSidebar">
    <div class="filters-header">
      <h3>Filters</h3>
      <button class="clear-filters" id="clearFilters">Clear All</button>
    </div>
    <div class="filter-group">
      <label>Search</label>
      <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input type="text" id="searchInput" placeholder="Search products..."/>
      </div>
    </div>
    <div class="filter-group">
      <label>Category</label>
      <div class="filter-options">
        <?php foreach(['Skincare','Makeup','Haircare','Perfumes','Accessories'] as $cat): ?>
        <label class="filter-option"><input type="checkbox" name="category" value="<?php echo $cat; ?>" checked> <?php echo $cat; ?></label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="filter-group">
      <label>Price Range (KES)</label>
      <div class="price-range">
        <input type="range" id="priceRange" min="0" max="5000" value="5000" step="100"/>
        <div class="price-labels"><span>KES 0</span><span id="priceValue">KES 5,000</span></div>
      </div>
    </div>
    <div class="filter-group">
      <label>Skin Type</label>
      <div class="filter-options">
        <?php foreach(['All','Oily','Dry','Sensitive'] as $s): ?>
        <label class="filter-option"><input type="checkbox" name="skin" value="<?php echo $s; ?>" checked> <?php echo $s; ?></label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="filter-group">
      <label>Sort By</label>
      <select id="sortSelect">
        <option value="default">Default</option>
        <option value="price-low">Price: Low to High</option>
        <option value="price-high">Price: High to Low</option>
        <option value="rating">Highest Rated</option>
      </select>
    </div>
  </aside>
  <main class="products-main">
    <div class="products-toolbar">
      <span id="productCount"><?php echo $total; ?> products found</span>
      <button class="filter-toggle-btn" id="filterToggleBtn">⚙ Filters</button>
    </div>
    <div class="products-grid" id="productsGrid">
      <?php while ($p = mysqli_fetch_assoc($products_result)):
        $stars = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5 - round($p['rating']));
        $icon  = $icons[$p['category']] ?? '✨';
      ?>
      <div class="product-card"
           data-category="<?php echo $p['category']; ?>"
           data-price="<?php echo $p['price']; ?>"
           data-skin="<?php echo $p['skin_type']; ?>"
           data-rating="<?php echo $p['rating']; ?>"
           data-name="<?php echo strtolower($p['name']); ?>">
        <div class="product-image">
          <div class="product-icon"><?php echo $icon; ?></div>
          <button class="wishlist-btn">♡</button>
        </div>
        <div class="product-info">
          <span class="product-category"><?php echo $p['category']; ?></span>
          <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
          <p class="product-desc"><?php echo htmlspecialchars($p['description']); ?></p>
          <div class="product-rating">
            <span class="stars"><?php echo $stars; ?></span>
            <span class="rating-num">(<?php echo $p['review_count']; ?>)</span>
          </div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($p['price']); ?></span>
            <div style="display:flex;gap:6px;">
              <a href="product_detail.php?id=<?php echo $p['product_id']; ?>" class="btn-cart" style="background:var(--lavender);">View</a>
              <a href="cart.php?add=<?php echo $p['product_id']; ?>" class="btn-cart">+ Cart</a>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div class="no-results" id="noResults" style="display:none;">
      <div class="no-results-icon">🔍</div>
      <h3>No products found</h3>
      <p>Try adjusting your filters</p>
      <button class="btn-primary" id="resetFilters">Reset Filters</button>
    </div>
  </main>
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