<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Admin Reviews
// BIT3208 Advanced Web Design and Development
// File: admin/reviews.php
// ============================================================
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

$message = '';

// DELETE REVIEW
if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM reviews WHERE review_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        // Recalculate product rating
        $product_id = (int)$_GET['product_id'];
        $avg = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE product_id=$product_id"
        ));
        $new_rating = round($avg['avg'] ?? 0, 1);
        $new_count  = $avg['cnt'] ?? 0;
        mysqli_query($conn, "UPDATE products SET rating=$new_rating, review_count=$new_count WHERE product_id=$product_id");
        $message = 'Review deleted.';
    }
}

// Fetch all reviews
$reviews = mysqli_query($conn, "
    SELECT r.*, u.full_name, p.name as product_name, p.category
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN products p ON r.product_id = p.product_id
    ORDER BY r.created_at DESC
");

// Stats
$total_reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM reviews"))['t'];
$avg_rating    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ROUND(AVG(rating),1) as t FROM reviews"))['t'];
$five_star     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM reviews WHERE rating=5"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Reviews</title>
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
      <a href="products.php"  class="admin-nav-link">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link">👥 Users</a>
      <a href="reviews.php"   class="admin-nav-link active">⭐ Reviews</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-header">
      <div><h1>Manage Reviews</h1><p>View and moderate customer reviews</p></div>
    </div>

    <?php if ($message): ?><div class="form-message form-message--success">✓ <?php echo $message; ?></div><?php endif; ?>

    <!-- Review Stats -->
    <div class="admin-stats" style="grid-template-columns:repeat(3,1fr);">
      <div class="admin-stat-card admin-stat-card--pink">
        <div class="admin-stat-icon">⭐</div>
        <div class="admin-stat-info"><h3><?php echo $total_reviews; ?></h3><p>Total Reviews</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--lavender">
        <div class="admin-stat-icon">📊</div>
        <div class="admin-stat-info"><h3><?php echo $avg_rating; ?>/5</h3><p>Average Rating</p></div>
      </div>
      <div class="admin-stat-card admin-stat-card--green">
        <div class="admin-stat-icon">🌟</div>
        <div class="admin-stat-info"><h3><?php echo $five_star; ?></h3><p>5-Star Reviews</p></div>
      </div>
    </div>

    <!-- Reviews Table -->
    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>All Reviews (<?php echo mysqli_num_rows($reviews); ?>)</h3>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr><th>ID</th><th>Customer</th><th>Product</th><th>Category</th><th>Rating</th><th>Comment</th><th>Date</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($reviews) > 0):
              while ($r = mysqli_fetch_assoc($reviews)):
                $stars = str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']);
            ?>
            <tr>
              <td>#<?php echo $r['review_id']; ?></td>
              <td><?php echo htmlspecialchars($r['full_name']); ?></td>
              <td><strong><?php echo htmlspecialchars($r['product_name']); ?></strong></td>
              <td><?php echo $r['category']; ?></td>
              <td>
                <span style="color:#F5A623;font-size:14px;"><?php echo $stars; ?></span>
                <strong style="margin-left:4px;"><?php echo $r['rating']; ?>/5</strong>
              </td>
              <td style="max-width:200px;font-size:13px;"><?php echo htmlspecialchars($r['comment']); ?></td>
              <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
              <td>
                <a href="reviews.php?delete=<?php echo $r['review_id']; ?>&product_id=<?php echo $r['product_id']; ?>"
                   class="btn-sm btn-sm--delete"
                   onclick="return confirm('Delete this review?')">Delete</a>
              </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="8" style="text-align:center;color:#888;padding:40px;">No reviews yet</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>