<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 3
// BIT3208 - Advanced Web Design and Development
// File: index.php — Homepage
// Week 3 Theme: Frontend UI, JavaScript, DOM Manipulation
// ============================================================
$page_title = "Home";
$site_name  = "Essiz Beauty Hub";
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

<!-- ===== NAVBAR ===== -->
<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">
      <span class="brand-star">✦</span>
      <?php echo $site_name; ?>
    </a>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="#">Routines</a></li>
      <li><a href="#">Wishlist</a></li>
      <li><a href="#">Cart <span class="cart-badge" id="cartBadge">0</span></a></li>
      <li><a href="login.php" class="btn-nav">Login</a></li>
    </ul>
  </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-bg">
    <div class="hero-circle hero-circle--1"></div>
    <div class="hero-circle hero-circle--2"></div>
    <div class="hero-circle hero-circle--3"></div>
  </div>
  <div class="hero-content">
    <div class="hero-badge fade-in">🌸 New Arrivals — Campus Collection 2026</div>
    <h1 class="hero-title fade-in">
      Intelligent Beauty.<br>
      <em>Campus Confidence.</em>
    </h1>
    <p class="hero-subtitle fade-in">
      Discover skincare, makeup and beauty essentials curated for university students and young professionals in Kenya.
    </p>
    <div class="hero-actions fade-in">
      <a href="products.php" class="btn-primary">Shop Now</a>
      <a href="#" class="btn-outline">Build My Routine</a>
    </div>
    <div class="hero-stats fade-in">
      <div class="stat"><strong>500+</strong><span>Products</span></div>
      <div class="stat-divider"></div>
      <div class="stat"><strong>2,000+</strong><span>Happy Customers</span></div>
      <div class="stat-divider"></div>
      <div class="stat"><strong>Free</strong><span>Campus Delivery</span></div>
    </div>
  </div>
</section>

<!-- ===== CATEGORIES ===== -->
<section class="section categories-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Shop by Category</h2>
      <p class="section-subtitle">Everything you need for your beauty routine</p>
    </div>
    <div class="categories-grid">
      <?php
        $categories = [
          ['icon'=>'🧴','name'=>'Skincare',    'count'=>'120+ products','color'=>'pink'],
          ['icon'=>'💄','name'=>'Makeup',      'count'=>'85+ products', 'color'=>'lavender'],
          ['icon'=>'💆','name'=>'Haircare',    'count'=>'60+ products', 'color'=>'nude'],
          ['icon'=>'🌸','name'=>'Perfumes',    'count'=>'45+ products', 'color'=>'pink'],
          ['icon'=>'🪞','name'=>'Accessories', 'count'=>'30+ products', 'color'=>'lavender'],
        ];
        foreach($categories as $cat):
      ?>
      <div class="category-card category-card--<?php echo $cat['color']; ?>">
        <div class="category-icon"><?php echo $cat['icon']; ?></div>
        <h3><?php echo $cat['name']; ?></h3>
        <p><?php echo $cat['count']; ?></p>
        <a href="products.php?category=<?php echo urlencode($cat['name']); ?>" class="category-link">Shop →</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<section class="section products-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Featured Products</h2>
      <p class="section-subtitle">Handpicked beauty essentials for you</p>
      <a href="products.php" class="view-all">View All →</a>
    </div>
    <div class="products-grid" id="productsGrid">
      <?php
        $products = [
          ['name'=>'Glow Serum 30ml',       'category'=>'Skincare',    'price'=>850,  'rating'=>4.5,'skin'=>'All Skin Types',  'badge'=>'Bestseller','icon'=>'🧴'],
          ['name'=>'Matte Lipstick — Rose',  'category'=>'Makeup',      'price'=>450,  'rating'=>4.2,'skin'=>'All Skin Types',  'badge'=>'New',       'icon'=>'💄'],
          ['name'=>'Hydrating Moisturizer',  'category'=>'Skincare',    'price'=>1200, 'rating'=>4.8,'skin'=>'Dry Skin',        'badge'=>'Top Rated', 'icon'=>'🫙'],
          ['name'=>'Castor Hair Oil',        'category'=>'Haircare',    'price'=>650,  'rating'=>4.3,'skin'=>'All Hair Types',  'badge'=>'',          'icon'=>'💆'],
          ['name'=>'Floral Perfume 50ml',    'category'=>'Perfumes',    'price'=>2200, 'rating'=>4.6,'skin'=>'All Skin Types',  'badge'=>'Premium',   'icon'=>'🌸'],
          ['name'=>'Makeup Brush Set',       'category'=>'Accessories', 'price'=>980,  'rating'=>4.4,'skin'=>'All Skin Types',  'badge'=>'',          'icon'=>'🪞'],
          ['name'=>'Vitamin C Toner',        'category'=>'Skincare',    'price'=>750,  'rating'=>4.7,'skin'=>'Oily Skin',       'badge'=>'New',       'icon'=>'🧴'],
          ['name'=>'Acne Control Cleanser',  'category'=>'Skincare',    'price'=>550,  'rating'=>4.1,'skin'=>'Oily/Acne Skin', 'badge'=>'',          'icon'=>'🫧'],
        ];
        foreach($products as $index => $product):
          $stars = '';
          for($i=1; $i<=5; $i++){
            $stars .= $i <= round($product['rating']) ? '★' : '☆';
          }
      ?>
      <div class="product-card" data-index="<?php echo $index; ?>">
        <?php if($product['badge']): ?>
          <div class="product-badge"><?php echo $product['badge']; ?></div>
        <?php endif; ?>
        <div class="product-image">
          <div class="product-icon"><?php echo $product['icon']; ?></div>
        </div>
        <div class="product-info">
          <span class="product-category"><?php echo $product['category']; ?></span>
          <h3 class="product-name"><?php echo $product['name']; ?></h3>
          <span class="product-skin"><?php echo $product['skin']; ?></span>
          <div class="product-rating">
            <span class="stars"><?php echo $stars; ?></span>
            <span class="rating-num">(<?php echo $product['rating']; ?>)</span>
          </div>
          <div class="product-footer">
            <span class="product-price">KES <?php echo number_format($product['price']); ?></span>
            <button class="btn-cart" onclick="addToCart(<?php echo $index; ?>, '<?php echo $product['name']; ?>', <?php echo $product['price']; ?>)">
              + Cart
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== SMART RECOMMENDATION BANNER ===== -->
<section class="recommendation-banner">
  <div class="container">
    <div class="rec-content">
      <div class="rec-icon">✨</div>
      <div class="rec-text">
        <h3>Get Personalized Recommendations</h3>
        <p>Tell us your skin type and beauty goals — we'll curate the perfect routine for you.</p>
      </div>
      <a href="register.php" class="btn-primary">Get Started</a>
    </div>
  </div>
</section>

<!-- ===== BUNDLE MODAL ===== -->
<div class="modal-overlay" id="bundleModal">
  <div class="modal-box">
    <button class="modal-close" id="modalClose">✕</button>
    <div class="modal-icon" id="modalIcon"></div>
    <h2 class="modal-title" id="modalTitle"></h2>
    <p class="modal-desc" id="modalDesc"></p>
    <div class="modal-items" id="modalItems"></div>
    <div class="modal-pricing">
      <span class="bundle-original" id="modalOriginal"></span>
      <span class="bundle-price" id="modalPrice"></span>
      <span class="bundle-save" id="modalSave"></span>
    </div>
    <button class="btn-primary btn-full" id="modalCartBtn">Add Bundle to Cart</button>
  </div>
</div>

<!-- ===== BEAUTY BUNDLES ===== -->
<section class="section bundles-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Beauty Bundles</h2>
      <p class="section-subtitle">Save more with our curated bundles</p>
    </div>
    <div class="bundles-grid">
      <div class="bundle-card" onclick="openBundle('campus')">
        <div class="bundle-icon">🎓</div>
        <h3>Campus Essentials</h3>
        <p>Everything you need for campus life</p>
        <div class="bundle-products">Cleanser + Moisturizer + Lip Balm</div>
        <div class="bundle-pricing">
          <span class="bundle-original">KES 2,400</span>
          <span class="bundle-price">KES 1,800</span>
          <span class="bundle-save">Save 25%</span>
        </div>
        <button class="btn-primary" onclick="openBundle('campus')">View Bundle</button>
      </div>
      <div class="bundle-card bundle-card--featured" onclick="openBundle('glow')">
        <div class="bundle-badge">Most Popular</div>
        <div class="bundle-icon">✨</div>
        <h3>Glow Package</h3>
        <p>Complete glow routine for radiant skin</p>
        <div class="bundle-products">Serum + Toner + Moisturizer + SPF</div>
        <div class="bundle-pricing">
          <span class="bundle-original">KES 4,200</span>
          <span class="bundle-price">KES 3,000</span>
          <span class="bundle-save">Save 29%</span>
        </div>
        <button class="btn-primary" onclick="openBundle('glow')">View Bundle</button>
      </div>
      <div class="bundle-card" onclick="openBundle('acne')">
        <div class="bundle-icon">🌿</div>
        <h3>Acne Care Bundle</h3>
        <p>Targeted care for acne-prone skin</p>
        <div class="bundle-products">Cleanser + Toner + Spot Treatment</div>
        <div class="bundle-pricing">
          <span class="bundle-original">KES 3,100</span>
          <span class="bundle-price">KES 2,200</span>
          <span class="bundle-save">Save 29%</span>
        </div>
        <button class="btn-primary" onclick="openBundle('acne')">View Bundle</button>
      </div>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">✦ <?php echo $site_name; ?></div>
        <p>Intelligent Beauty. Campus Confidence.</p>
        <p class="footer-copy">Nairobi, Kenya 🇰🇪</p>
      </div>
      <div class="footer-links">
        <h4>Shop</h4>
        <ul>
          <li><a href="#">Skincare</a></li>
          <li><a href="#">Makeup</a></li>
          <li><a href="#">Haircare</a></li>
          <li><a href="#">Bundles</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Account</h4>
        <ul>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="#">My Orders</a></li>
          <li><a href="#">Wishlist</a></li>
        </ul>
      </div>
      <div class="footer-links">
        <h4>Help</h4>
        <ul>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">Delivery Info</a></li>
          <li><a href="#">Returns</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> <?php echo $site_name; ?> — BIT3208 Advanced Web Design and Development</p>
    </div>
  </div>
</footer>

<!-- Toast notification -->
<div class="toast" id="toast"></div>

<script src="js/main.js"></script>
</body>
</html>