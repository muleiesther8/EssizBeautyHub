<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Admin Users
// BIT3208 Advanced Web Design and Development
// File: admin/users.php
// ============================================================
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

$message = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ? AND role = 'customer'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt) ? $message = 'User deleted.' : $message = 'Could not delete user.';
    } else {
        $message = 'You cannot delete your own account.';
    }
}

$users_result = mysqli_query($conn, "
    SELECT u.*,
           COUNT(DISTINCT o.order_id) as order_count,
           COALESCE(SUM(o.total_amount), 0) as total_spent,
           COUNT(DISTINCT r.review_id) as review_count,
           COUNT(DISTINCT br.routine_id) as routine_count
    FROM users u
    LEFT JOIN orders o ON u.user_id = o.user_id
    LEFT JOIN reviews r ON u.user_id = r.user_id
    LEFT JOIN beauty_routines br ON u.user_id = br.user_id
    GROUP BY u.user_id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Users</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span>✦</span> Essiz Admin</div>
    <nav class="admin-nav">
      <a href="dashboard.php" class="admin-nav-link">📊 Dashboard</a>
      <a href="products.php"  class="admin-nav-link">💄 Products</a>
      <a href="orders.php"    class="admin-nav-link">📦 Orders</a>
      <a href="users.php"     class="admin-nav-link active">👥 Users</a>
      <a href="reviews.php"   class="admin-nav-link">⭐ Reviews</a>
      <a href="../index.php"  class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php" class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-header">
      <div><h1>Manage Users</h1><p>View all registered customers and admins</p></div>
    </div>

    <?php if ($message): ?><div class="form-message form-message--success">✓ <?php echo $message; ?></div><?php endif; ?>

    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>All Users (<?php echo mysqli_num_rows($users_result); ?>)</h3>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Skin</th><th>Budget</th><th>Role</th><th>Orders</th><th>Spent</th><th>Reviews</th><th>Routines</th><th>Joined</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
            <tr>
              <td>#<?php echo $user['user_id']; ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="width:32px;height:32px;border-radius:50%;background:var(--pink-light);display:flex;align-items:center;justify-content:center;font-weight:600;color:var(--pink);font-size:13px;flex-shrink:0;">
                    <?php echo strtoupper(substr($user['full_name'],0,1)); ?>
                  </div>
                  <?php echo htmlspecialchars($user['full_name']); ?>
                </div>
              </td>
              <td style="font-size:12px;"><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['phone_number'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($user['skin_type'] ?? '—'); ?></td>
              <td><?php echo ucfirst($user['budget'] ?? '—'); ?></td>
              <td>
                <span style="display:inline-block;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:500;background:<?php echo $user['role']==='admin'?'var(--pink)':'var(--lav-soft)'; ?>;color:<?php echo $user['role']==='admin'?'white':'var(--lavender)'; ?>;">
                  <?php echo ucfirst($user['role']); ?>
                </span>
              </td>
              <td><?php echo $user['order_count']; ?></td>
              <td>KES <?php echo number_format($user['total_spent']); ?></td>
              <td><?php echo $user['review_count']; ?></td>
              <td><?php echo $user['routine_count']; ?></td>
              <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
              <td>
                <?php if ($user['role'] !== 'admin'): ?>
                  <a href="users.php?delete=<?php echo $user['user_id']; ?>" class="btn-sm btn-sm--delete"
                     onclick="return confirm('Delete this user?')">Delete</a>
                <?php else: ?>
                  <span style="font-size:12px;color:var(--charcoal-light);">Protected</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>