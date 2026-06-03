<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id   = (int)$_POST['order_id'];
    $new_status = $_POST['order_status'];
    $allowed    = ['Pending', 'Packed', 'On the way', 'Delivered'];

    if (in_array($new_status, $allowed)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET order_status = ? WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($stmt) ? $message = 'Order status updated!' : $message = 'Update failed.';
    }
}

$orders_result = mysqli_query($conn, "
    SELECT o.*, u.full_name, u.email, u.phone_number,
           COUNT(oi.item_id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Admin Orders</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span>✦</span> Essiz Admin</div>
    <nav class="admin-nav">
      <a href="dashboard.php"  class="admin-nav-link">📊 Dashboard</a>
      <a href="products.php"   class="admin-nav-link">💄 Products</a>
      <a href="orders.php"     class="admin-nav-link active">📦 Orders</a>
      <a href="users.php"      class="admin-nav-link">👥 Users</a>
      <a href="../index.php"   class="admin-nav-link">🏠 View Site</a>
      <a href="../logout.php"  class="admin-nav-link admin-logout">🚪 Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-header">
      <div>
        <h1>Manage Orders</h1>
        <p>View and update customer orders</p>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="form-message form-message--success">✓ <?php echo $message; ?></div>
    <?php endif; ?>

    <div class="admin-table-card">
      <div class="admin-table-header">
        <h3>All Orders (<?php echo mysqli_num_rows($orders_result); ?>)</h3>
      </div>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Items</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Delivery Location</th>
              <th>Status</th>
              <th>Date</th>
              <th>Update Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($orders_result) > 0):
              while ($order = mysqli_fetch_assoc($orders_result)):
                $status_class = [
                  'Pending'    => 'status--pending',
                  'Packed'     => 'status--packed',
                  'On the way' => 'status--shipping',
                  'Delivered'  => 'status--delivered',
                ][$order['order_status']] ?? '';
            ?>
            <tr>
              <td><strong>#<?php echo $order['order_id']; ?></strong></td>
              <td>
                <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                <span style="font-size:11px;color:var(--charcoal-light);"><?php echo htmlspecialchars($order['email']); ?></span>
              </td>
              <td><?php echo htmlspecialchars($order['phone_number'] ?? '—'); ?></td>
              <td><?php echo $order['item_count']; ?> items</td>
              <td><strong>KES <?php echo number_format($order['total_amount']); ?></strong></td>
              <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
              <td style="max-width:160px;font-size:12px;"><?php echo htmlspecialchars($order['delivery_location']); ?></td>
              <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $order['order_status']; ?></span></td>
              <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
              <td>
                <form method="POST" action="orders.php" style="display:flex;gap:6px;align-items:center;">
                  <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                  <select name="order_status" style="font-size:12px;padding:4px 8px;border:1px solid var(--border);border-radius:var(--radius-sm);outline:none;cursor:pointer;">
                    <?php foreach(['Pending','Packed','On the way','Delivered'] as $s): ?>
                      <option value="<?php echo $s; ?>" <?php echo $order['order_status'] === $s ? 'selected' : ''; ?>>
                        <?php echo $s; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn-sm">Update</button>
                </form>
              </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
              <td colspan="10" style="text-align:center;color:#888;padding:40px;">No orders yet</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

</body>
</html>