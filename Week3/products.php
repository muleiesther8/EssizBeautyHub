<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 3
// BIT3208 - Advanced Web Design and Development
// File: products.php — Products Listing Page
// ============================================================
$page_title = "Products";
$site_name  = "Essiz Beauty Hub";

// Sample products array (will come from DB in Week 5)
$products = [
  ['id'=>1,'name'=>'Glow Serum 30ml',        'category'=>'Skincare',    'price'=>850,  'rating'=>4.5,'skin'=>'All',      'badge'=>'Bestseller','icon'=>'🧴','desc'=>'Brightening serum for radiant skin'],
  ['id'=>2,'name'=>'Matte Lipstick — Rose',   'category'=>'Makeup',      'price'=>450,  'rating'=>4.2,'skin'=>'All',      'badge'=>'New',       'icon'=>'💄','desc'=>'Long lasting matte finish'],
  ['id'=>3,'name'=>'Hydrating Moisturizer',   'category'=>'Skincare',    'price'=>1200, 'rating'=>4.8,'skin'=>'Dry',      'badge'=>'Top Rated', 'icon'=>'🫙','desc'=>'Deep hydration for dry skin'],
  ['id'=>4,'name'=>'Castor Hair Oil',         'category'=>'Haircare',    'price'=>650,  'rating'=>4.3,'skin'=>'All',      'badge'=>'',          'icon'=>'💆','desc'=>'Promotes hair growth and shine'],
  ['id'=>5,'name'=>'Floral Perfume 50ml',     'category'=>'Perfumes',    'price'=>2200, 'rating'=>4.6,'skin'=>'All',      'badge'=>'Premium',   'icon'=>'🌸','desc'=>'Light floral scent for everyday'],
  ['id'=>6,'name'=>'Makeup Brush Set',        'category'=>'Accessories', 'price'=>980,  'rating'=>4.4,'skin'=>'All',      'badge'=>'',          'icon'=>'🪞','desc'=>'Professional 12 piece brush set'],
  ['id'=>7,'name'=>'Vitamin C Toner',         'category'=>'Skincare',    'price'=>750,  'rating'=>4.7,'skin'=>'Oily',     'badge'=>'New',       'icon'=>'🧴','desc'=>'Brightening toner with Vitamin C'],
  ['id'=>8,'name'=>'Acne Control Cleanser',   'category'=>'Skincare',    'price'=>550,  'rating'=>4.1,'skin'=>'Oily',     'badge'=>'',          'icon'=>'🫧','desc'=>'Gentle cleanser for acne-prone skin'],
  ['id'=>9,'name'=>'Nude Lip Gloss',          'category'=>'Makeup',      'price'=>380,  'rating'=>4.0,'skin'=>'All',      'badge'=>'',          'icon'=>'💋','desc'=>'Glossy nude finish lip gloss'],
  ['id'=>10,'name'=>'Argan Hair Serum',       'category'=>'Haircare',    'price'=>890,  'rating'=>4.5,'skin'=>'All',      'badge'=>'Bestseller','icon'=>'✨','desc'=>'Frizz control and shine serum'],
  ['id'=>11,'name'=>'Rose Water Mist',        'category'=>'Skincare',    'price'=>480,  'rating'=>4.3,'skin'=>'Sensitive','badge'=>'',          'icon'=>'🌹','desc'=>'Refreshing hydrating face mist'],
  ['id'=>12,'name'=>'SPF 50 Sunscreen',       'category'=>'Skincare',    'price'=>1100, 'rating'=>4.9,'skin'=>'All',      'badge'=>'Must Have', 'icon'=>'☀️','desc'=>'Lightweight daily sun protection'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $site_name; ?> — <?php echo $page_title; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">
      <span class="brand-star">✦</span>
      <?php echo $site_name; ?>
    </a>
    <button class="nav-toggle" id="navToggle">
      <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php" class="active">Products</a></li>
      <li><a href="#">Routines</a></li>
      <li><a href="#">Wishlist</a></li>
      <li><a href="#">Cart <span class="cart-badge" id="cartBadge">0</span></a></li>
      <li><a href="login.php" class="btn-nav">Login</a></li>
    </ul>
  </div>
</nav>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1>All Products</h1>
    <p>Discover <?php echo count($products); ?>+ beauty essentials curated for you</p>
  </div>
</div>

<!-- Products Layout -->
<div class="products-layout container">

  <!-- Sidebar Filters -->
  <aside class="filters-sidebar" id="filtersSidebar">
    <div class="filters-header">
      <h3>Filters</h3>
      <button class="clear-filters" id="clearFilters">Clear All</button>
    </div>

    <!-- Search -->
    <div class="filter-group">
      <label>Search</label>
      <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input type="text" id="searchInput" placeholder="Search products..."/>
      </div>
    </div>

    <!-- Category -->
    <div class="filter-group">
      <label>Category</label>
      <div class="filter-options">
        <label class="filter-option"><input type="checkbox" name="category" value="Skincare" checked> Skincare</label>
        <label class="filter-option"><input type="checkbox" name="category" value="Makeup" checked> Makeup</label>
        <label class="filter-option"><input type="checkbox" name="category" value="Haircare" checked> Haircare</label>
        <label class="filter-option"><input type="checkbox" name="category" value="Perfumes" checked> Perfumes</label>
        <label class="filter-option"><input type="checkbox" name="category" value="Accessories" checked> Accessories</label>
      </div>
    </div>

    <!-- Price Range -->
    <div class="filter-group">
      <label>Price Range (KES)</label>
      <div class="price-range">
        <input type="range" id="priceRange" min="0" max="5000" value="5000" step="100"/>
        <div class="price-labels">
          <span>KES 0</span>
          <span id="priceValue">KES 5,000</span>
        </div>
      </div>
    </div>

    <!-- Skin Type -->
    <div class="filter-group">
      <label>Skin Type</label>
      <div class="filter-options">
        <label class="filter-option"><input type="checkbox" name="skin" value="All" checked> All Skin Types</label>
        <label class="filter-option"><input type="checkbox" name="skin" value="Oily" checked> Oily Skin</label>
        <label class="filter-option"><input type="checkbox" name="skin" value="Dry" checked> Dry Skin</label>
        <label class="filter-option"><input type="checkbox" name="skin" value="Sensitive" checked> Sensitive Skin</label>
      </div>
    </div>

    <!-- Sort -->
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

  <!-- Products Grid -->
  <main class="products-main">

    <div class="products-toolbar">
      <span id="productCount"><?php echo count($products); ?> products found</span>
      <button class="filter-toggle-btn" id="filterToggleBtn">⚙ Filters</button>
    </div>

    <div class="products-grid" id="productsGrid">
      <?php foreach($products as $product):
        $stars = '';
        for($i=1; $i<=5; $i++){
          $stars .= $i <= round($product['rating']) ? '★' : '☆';
        }
      ?>
      <div class="product-card"
           data-category="<?php echo $product['category']; ?>"
           data-price="<?php echo $product['price']; ?>"
           data-skin="<?php echo $product['skin']; ?>"
           data-rating="<?php echo $product['rating']; ?>"
           data-name="<?php echo strtolower($product['name']); ?>">

        <?php if($product['badge']): ?>
          <div class="product-badge"><?php echo $product['badge']; ?></div>
        <?php endif; ?>

        <div class="product-image">
          <div class="product-icon"><?php echo $product['icon']; ?></div>
          <button class="wishlist-btn" title="Add to Wishlist">♡</button>
        </div>

        <div class="product-info">
          <span class="product-category"><?php echo $product['category']; ?></span>
          <h3 class="product-name"><?php echo $product['name']; ?></h3>
          <p class="product-desc"><?php echo $product['desc']; ?></p>
          <span class="product-skin">For: <?php echo $product['skin']; ?> skin</span>
          <div class="product-rating">
            <span class="stars"><?php echo $stars; ?></span>
            <span class="rating-num">(<?php echo $product['rating']; ?>)</span>
          </div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($product['price']); ?></span>
            <button class="btn-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>', <?php echo $product['price']; ?>)">
              + Cart
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- No results message -->
    <div class="no-results" id="noResults" style="display:none;">
      <div class="no-results-icon">🔍</div>
      <h3>No products found</h3>
      <p>Try adjusting your filters or search term</p>
      <button class="btn-primary" id="resetFilters">Reset Filters</button>
    </div>

  </main>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> <?php echo $site_name; ?> — BIT3208 Advanced Web Design and Development</p>
    </div>
  </div>
</footer>

<div class="toast" id="toast"></div>
<script src="js/main.js"></script>
</body>
</html>