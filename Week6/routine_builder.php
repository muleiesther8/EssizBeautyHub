<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Beauty Routine Builder
// BIT3208 Advanced Web Design and Development
// File: routine_builder.php
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

requireLogin();

$user_id   = $_SESSION['user_id'];
$skin_type = $_SESSION['skin_type'] ?? 'All';
$message   = '';
$icons     = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];

// Save routine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_routine'])) {
    $routine_name     = trim($_POST['routine_name'] ?? 'My Routine');
    $routine_type     = $_POST['routine_type'] ?? 'morning';
    $products_selected = implode(',', $_POST['selected_products'] ?? []);
    $notes            = trim($_POST['notes'] ?? '');

    $stmt = mysqli_prepare($conn,
        "INSERT INTO beauty_routines (user_id, routine_name, routine_type, products_selected, notes) VALUES (?,?,?,?,?)"
    );
    mysqli_stmt_bind_param($stmt, "issss", $user_id, $routine_name, $routine_type, $products_selected, $notes);
    mysqli_stmt_execute($stmt) ? $message = 'success' : $message = 'error';
}

// Fetch saved routines
$routines = mysqli_query($conn, "SELECT * FROM beauty_routines WHERE user_id = $user_id ORDER BY created_at DESC");

// Smart product recommendations by skin type and routine type
$routine_type_filter = $_GET['type'] ?? 'morning';

// Morning routine products
$morning_categories = ['Skincare', 'Makeup'];
$night_categories   = ['Skincare', 'Haircare'];
$filter_cats        = $routine_type_filter === 'morning' ? $morning_categories : $night_categories;
$cats_in            = "'" . implode("','", $filter_cats) . "'";

$rec_products = mysqli_query($conn,
    "SELECT * FROM products
     WHERE category IN ($cats_in)
     AND (skin_type = '$skin_type' OR skin_type = 'All')
     ORDER BY rating DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Routine Builder</title>
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
      <li><a href="routine_builder.php" class="active">Routines</a></li>
      <li><a href="cart.php">Cart</a></li>
      <li><a href="dashboard.php">My Account</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>🌸 Beauty Routine Builder</h1>
    <p>Build your personalized <?php echo htmlspecialchars($skin_type); ?> skin routine</p>
  </div>
</div>

<div class="container routine-layout">

  <!-- Builder Panel -->
  <div class="routine-main">

    <?php if ($message === 'success'): ?>
      <div class="form-message form-message--success">✓ Routine saved successfully!</div>
    <?php elseif ($message === 'error'): ?>
      <div class="form-message form-message--error">⚠ Could not save routine. Please try again.</div>
    <?php endif; ?>

    <form method="POST" action="routine_builder.php" id="routineForm">

      <!-- Routine Type Toggle -->
      <div class="routine-toggle">
        <a href="routine_builder.php?type=morning"
           class="routine-toggle-btn <?php echo $routine_type_filter === 'morning' ? 'active' : ''; ?>">
          ☀️ Morning Routine
        </a>
        <a href="routine_builder.php?type=night"
           class="routine-toggle-btn <?php echo $routine_type_filter === 'night' ? 'active' : ''; ?>">
          🌙 Night Routine
        </a>
      </div>
      <input type="hidden" name="routine_type" value="<?php echo $routine_type_filter; ?>">

      <!-- Routine Name -->
      <div class="form-group" style="margin-top:24px;">
        <label>Routine Name</label>
        <div class="input-wrapper">
          <span class="input-icon">✏️</span>
          <input type="text" name="routine_name"
            placeholder="e.g. My Morning Glow Routine"
            value="My <?php echo ucfirst($routine_type_filter); ?> Routine"/>
        </div>
      </div>

      <!-- Product Selection -->
      <h3 style="font-family:var(--font-display);font-size:20px;margin-bottom:16px;">
        Select Products for Your <?php echo ucfirst($routine_type_filter); ?> Routine
      </h3>
      <p style="font-size:13px;color:var(--charcoal-light);margin-bottom:20px;">
        Recommended for <strong><?php echo htmlspecialchars($skin_type); ?></strong> skin
      </p>

      <div class="routine-products-grid">
        <?php
        $step = 1;
        while ($p = mysqli_fetch_assoc($rec_products)):
          $icon  = $icons[$p['category']] ?? '✨';
          $stars = str_repeat('★', round($p['rating'])) . str_repeat('☆', 5 - round($p['rating']));
        ?>
        <label class="routine-product-card">
          <input type="checkbox" name="selected_products[]" value="<?php echo $p['product_id']; ?>">
          <div class="routine-product-inner">
            <div class="routine-step">Step <?php echo $step++; ?></div>
            <div class="routine-product-icon"><?php echo $icon; ?></div>
            <div class="routine-product-info">
              <span class="product-category"><?php echo $p['category']; ?></span>
              <h4><?php echo htmlspecialchars($p['name']); ?></h4>
              <p><?php echo htmlspecialchars($p['description']); ?></p>
              <div class="stars" style="font-size:12px;"><?php echo $stars; ?></div>
              <strong style="color:var(--pink);">KES <?php echo number_format($p['price']); ?></strong>
            </div>
            <div class="routine-check">✓</div>
          </div>
        </label>
        <?php endwhile; ?>
      </div>

      <!-- Notes -->
      <div class="form-group" style="margin-top:24px;">
        <label>Routine Notes (optional)</label>
        <textarea name="notes" placeholder="e.g. Apply SPF last, wait 5 mins between steps..."
          style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:14px;font-family:var(--font-body);outline:none;resize:vertical;min-height:80px;"></textarea>
      </div>

      <div style="display:flex;gap:12px;margin-top:8px;">
        <button type="submit" name="save_routine" class="btn-primary">💾 Save My Routine</button>
        <a href="products.php" class="btn-outline">Browse More Products</a>
      </div>

    </form>
  </div>

  <!-- Saved Routines Sidebar -->
  <aside class="routine-sidebar">
    <h3>My Saved Routines</h3>

    <?php if (mysqli_num_rows($routines) > 0): ?>
      <?php while ($r = mysqli_fetch_assoc($routines)):
        $product_ids = explode(',', $r['products_selected']);
        $product_count = count(array_filter($product_ids));
      ?>
      <div class="saved-routine-card">
        <div class="saved-routine-header">
          <div>
            <h4><?php echo htmlspecialchars($r['routine_name']); ?></h4>
            <span class="routine-type-badge <?php echo $r['routine_type'] === 'morning' ? 'badge--morning' : 'badge--night'; ?>">
              <?php echo $r['routine_type'] === 'morning' ? '☀️ Morning' : '🌙 Night'; ?>
            </span>
          </div>
          <span style="font-size:12px;color:var(--charcoal-light);"><?php echo date('d M', strtotime($r['created_at'])); ?></span>
        </div>
        <p style="font-size:13px;color:var(--charcoal-light);margin-top:8px;">
          <?php echo $product_count; ?> product(s) selected
        </p>
        <?php if ($r['notes']): ?>
          <p style="font-size:12px;color:var(--charcoal-mid);margin-top:6px;font-style:italic;">
            "<?php echo htmlspecialchars(substr($r['notes'], 0, 60)); ?>..."
          </p>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>

    <?php else: ?>
      <div style="text-align:center;padding:32px 16px;">
        <div style="font-size:40px;margin-bottom:12px;">🌸</div>
        <p style="font-size:14px;color:var(--charcoal-light);">No saved routines yet. Build your first one!</p>
      </div>
    <?php endif; ?>

    <!-- Routine Tips -->
    <div class="routine-tips">
      <h4>💡 Routine Tips</h4>
      <?php
      $tips = [
        'Oily'       => ['Use oil-free moisturizer', 'Apply toner after cleanser', 'Never skip SPF'],
        'Dry'        => ['Layer hydrating products', 'Use cream-based moisturizer', 'Apply serum before moisturizer'],
        'Combination'=> ['Use gentle cleanser', 'Target T-zone with toner', 'Light moisturizer works best'],
        'Sensitive'  => ['Patch test new products', 'Fragrance-free is better', 'Less is more'],
        'Normal'     => ['Maintain with basic routine', 'SPF daily is a must', 'Antioxidant serum helps'],
      ];
      $skin_tips = $tips[$skin_type] ?? ['Cleanse, tone, moisturize daily', 'Always wear SPF', 'Stay hydrated'];
      foreach($skin_tips as $tip): ?>
        <div class="routine-tip">✓ <?php echo $tip; ?></div>
      <?php endforeach; ?>
    </div>

  </aside>
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