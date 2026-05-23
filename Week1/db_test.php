<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 1 Database Test Page
// BIT3208 Advanced Web Design and Development
// File: db_test.php
// Visit: http://localhost/EssizBeautyHub/Week1/db_test.php
// ============================================================

require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DB Test — Essiz Beauty Hub Week 1</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    body { font-family:'DM Sans',sans-serif; background:#FDF6F9; padding:40px 20px; color:#2C2C2C; }
    .container { max-width:700px; margin:0 auto; }
    h1 { font-size:26px; color:#C85B7A; margin-bottom:6px; }
    .subtitle { color:#888; font-size:14px; margin-bottom:30px; }
    .section { background:#fff; border:1px solid #f0d8e4; border-radius:14px; padding:24px; margin-bottom:20px; }
    h2 { font-size:16px; color:#2C2C2C; margin-bottom:14px; border-bottom:1px solid #f5e0e8; padding-bottom:8px; }
    table { width:100%; border-collapse:collapse; font-size:14px; }
    th { background:#FDF0F5; color:#C85B7A; text-align:left; padding:10px 12px; font-weight:500; }
    td { padding:10px 12px; border-bottom:1px solid #f5e0e8; color:#555; }
    tr:last-child td { border-bottom:none; }
    .badge { display:inline-block; padding:3px 10px; border-radius:100px; font-size:12px; }
    .badge--ok { background:#E6F9EE; color:#2D7A4F; }
    .back { display:inline-block; margin-top:20px; color:#C85B7A; text-decoration:none; font-size:14px; }
    .back:hover { text-decoration:underline; }
  </style>
</head>
<body>
<div class="container">

  <h1>✦ Essiz Beauty Hub</h1>
  <p class="subtitle">Week 1 — Database Connection Test | BIT3208</p>

  <!-- Connection status -->
  <div class="section">
    <h2>Connection Status</h2>
    <p>
      <span class="badge badge--ok">✓ Connected</span>
      &nbsp; Successfully connected to database: <strong>essizdb_w1</strong>
    </p>
    <p style="margin-top:10px;font-size:13px;color:#888;">
      Host: localhost:3307 &nbsp;|&nbsp; User: root &nbsp;|&nbsp; Charset: utf8mb4
    </p>
  </div>

  <!-- Users table -->
  <div class="section">
    <h2>Users Table</h2>
    <?php
      $result = mysqli_query($conn, "SELECT user_id, full_name, email, created_at FROM users");
      if (mysqli_num_rows($result) > 0):
    ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['user_id']; ?></td>
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p style="color:#888;font-size:14px;">No users found. Import essizdb_w1.sql first.</p>
    <?php endif; ?>
  </div>

  <!-- Products table -->
  <div class="section">
    <h2>Products Table</h2>
    <?php
      $result2 = mysqli_query($conn, "SELECT product_id, name, category, price, stock FROM products");
      if (mysqli_num_rows($result2) > 0):
    ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Product Name</th>
          <th>Category</th>
          <th>Price (KES)</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result2)): ?>
        <tr>
          <td><?php echo $row['product_id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['category']); ?></td>
          <td>KES <?php echo number_format($row['price'], 2); ?></td>
          <td><?php echo $row['stock']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p style="color:#888;font-size:14px;">No products found. Import essizdb_w1.sql first.</p>
    <?php endif; ?>
  </div>

  <a href="index.php" class="back">← Back to Homepage</a>

</div>
</body>
</html>
<?php mysqli_close($conn); ?>