<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Admin Products
// BIT3208 Advanced Web Design and Development
// File: admin/products.php
// ============================================================
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

$message = ''; $error = '';

// DELETE
if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt) ? $message = 'Product deleted.' : $error = 'Delete failed.';
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']        ?? '');
    $category    = trim($_POST['category']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $stock       = (int)($_POST['stock']      ?? 0);
    $skin_type   = trim($_POST['skin_type']   ?? 'All');
    $rating      = (float)($_POST['rating']   ?? 0);
    $product_id  = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        $stmt = mysqli_prepare($conn,
            "UPDATE products SET name=?,category=?,description=?,price=?,stock=?,skin_type=?,rating=? WHERE product_id=?"
        );
        mysqli_stmt_bind_param($stmt,"sssdisdi",$name,$category,$description,$price,$stock,$skin_type,$rating,$product_id);
        mysqli_stmt_execute($stmt) ? $message = 'Product updated!' : $error = 'Update failed.';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO products (name,category,description,price,stock,skin_type,rating) VALUES(?,?,?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($stmt,"sssdisi",$name,$category,$description,$price,$stock,$skin_type,$rating);
        mysqli_stmt_execute($stmt) ? $message = 'Product added!' : $error = 'Add failed.';
    }
}

// EDIT MODE
$edit_product = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Products</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span>✦</span> Essiz Admin</div>
    <nav class="admin-nav">
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <a href="dashboard.php" class="admin-nav-link">📊 Dashboard</a>
      <a href="products.php"  class="admin-nav-link active">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link">👥 Users</a>
      <a href="reviews.php"   class="admin-nav-link">⭐ Reviews</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-header">
      <div><h1>Manage Products</h1><p><?php echo mysqli_num_rows($products); ?> products total</p></div>
      <button class="btn-primary" onclick="document.getElementById('productForm').scrollIntoView({behavior:'smooth'})">+ Add Product</button>
    </div>

    <?php if ($message): ?><div class="form-message form-message--success">✓ <?php echo $message; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>

    <!-- Add/Edit Form -->
    <div class="admin-form-card" id="productForm">
      <h3><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
      <form method="POST" action="products.php">
        <?php if ($edit_product): ?>
          <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
        <?php endif; ?>
        <div class="form-row">
          <div class="form-group">
            <label>Product Name</label>
            <div class="input-wrapper">
              <input type="text" name="name" placeholder="e.g. Glow Serum 30ml" required
                value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label>Category</label>
            <div class="input-wrapper">
              <select name="category" required>
                <option value="">Select category</option>
                <?php foreach(['Skincare','Makeup','Haircare','Perfumes','Accessories'] as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php echo ($edit_product['category'] ?? '') === $cat ? 'selected':''; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" placeholder="Product description..." rows="3"
            style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-family:var(--font-body);font-size:14px;outline:none;resize:vertical;"><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Price (KES)</label>
            <div class="input-wrapper">
              <input type="number" name="price" placeholder="850" step="0.01" required
                value="<?php echo $edit_product['price'] ?? ''; ?>"/>
            </div>
          </div>
          <div class="form-group">
            <label>Stock Quantity</label>
            <div class="input-wrapper">
              <input type="number" name="stock" placeholder="25" required
                value="<?php echo $edit_product['stock'] ?? ''; ?>"/>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Skin Type</label>
            <div class="input-wrapper">
              <select name="skin_type">
                <?php foreach(['All','Oily','Dry','Combination','Normal','Sensitive'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo ($edit_product['skin_type'] ?? 'All') === $s ? 'selected':''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Rating (0-5)</label>
            <div class="input-wrapper">
              <input type="number" name="rating" placeholder="4.5" step="0.1" min="0" max="5"
                value="<?php echo $edit_product['rating'] ?? ''; ?>"/>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:12px;">
          <button type="submit" class="btn-primary"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
          <?php if ($edit_product): ?><a href="products.php" class="btn-outline">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Products Table -->
    <div class="admin-table-card">
      <div class="admin-table-header"><h3>All Products (<?php echo mysqli_num_rows($products); ?>)</h3></div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Skin</th><th>Rating</th><th>Reviews</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php
            // Reset pointer
            mysqli_data_seek($products, 0);
            while ($p = mysqli_fetch_assoc($products)): ?>
            <tr>
              <td>#<?php echo $p['product_id']; ?></td>
              <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
              <td><?php echo $p['category']; ?></td>
              <td>KES <?php echo number_format($p['price']); ?></td>
              <td><?php echo $p['stock'] < 10 ? "<span style='color:#E53E3E;font-weight:600;'>{$p['stock']}</span>" : $p['stock']; ?></td>
              <td><?php echo $p['skin_type']; ?></td>
              <td>⭐ <?php echo $p['rating']; ?></td>
              <td><?php echo $p['review_count']; ?></td>
              <td style="display:flex;gap:6px;">
                <a href="products.php?edit=<?php echo $p['product_id']; ?>" class="btn-sm btn-sm--edit">Edit</a>
                <a href="products.php?delete=<?php echo $p['product_id']; ?>" class="btn-sm btn-sm--delete"
                   onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>