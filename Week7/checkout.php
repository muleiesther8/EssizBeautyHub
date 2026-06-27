<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Checkout
// BIT3208 Advanced Web Design and Development
// File: checkout.php
// ============================================================

require_once 'includes/db_connect.php';
require_once 'includes/session.php';

session_start();
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query  = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.category FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = $user_id";
$cart_result = mysqli_query($conn, $cart_query);
$cart_items  = [];
$subtotal    = 0;

while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['total'] = $row['price'] * $row['quantity'];
    $subtotal    += $row['total'];
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$delivery_fee = 200;
$total        = $subtotal + $delivery_fee;
$error        = '';
$success      = false;

// ============================================================
// PROCESS ORDER
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_location = trim($_POST['delivery_location'] ?? '');
    $payment_method    = trim($_POST['payment_method']    ?? '');
    $mpesa_number      = trim($_POST['mpesa_number']      ?? '');

    if (empty($delivery_location)) {
        $error = 'Please enter your delivery location.';
    } elseif (empty($payment_method)) {
        $error = 'Please select a payment method.';
    } elseif ($payment_method === 'Mpesa' && empty($mpesa_number)) {
        $error = 'Please enter your Mpesa number.';
    } else {

        // Insert order
        $stmt = mysqli_prepare($conn,
            "INSERT INTO orders (user_id, total_amount, order_status, payment_method, delivery_location)
             VALUES (?, ?, 'Pending', ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "idss", $user_id, $total, $payment_method, $delivery_location);

        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn);

            // Insert order items
            foreach ($cart_items as $item) {
                $item_stmt = mysqli_prepare($conn,
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($item_stmt, "iiid",
                    $order_id, $item['product_id'], $item['quantity'], $item['price']
                );
                mysqli_stmt_execute($item_stmt);
            }

            // Clear cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

            $success = true;

        } else {
            $error = 'Order failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Checkout</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="nav-container">
    <a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a>
    <ul class="nav-links">
      <?php if (!empty($_SESSION['profile_photo'])): ?>
  <img src="images/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
       style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--pink);"/>
<?php endif; ?>
      <li>
  <button class="dark-toggle" id="darkToggle" title="Toggle Dark Mode">🌙</button>
</li>
      <li><a href="cart.php">← Back to Cart</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>Checkout</h1>
    <p>Complete your order</p>
  </div>
</div>

<div class="container checkout-layout">

  <?php if ($success): ?>
  <!-- Order Success -->
  <div class="order-success">
    <div class="success-icon">🎉</div>
    <h2>Order Placed Successfully!</h2>
    <p>Thank you for your order, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
    <p>Your order <strong>#<?php echo $order_id; ?></strong> has been received and is being processed.</p>
    <?php if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'Mpesa'): ?>
      <div class="mpesa-simulation">
        <div class="mpesa-icon">📱</div>
        <h3>Mpesa Payment Simulation</h3>
        <p>A payment request of <strong>KES <?php echo number_format($total); ?></strong> has been sent to <strong><?php echo htmlspecialchars($_POST['mpesa_number'] ?? ''); ?></strong></p>
        <p>Enter your Mpesa PIN to complete payment.</p>
        <div class="mpesa-status">✓ Payment request sent</div>
      </div>
    <?php endif; ?>
    <div class="success-actions">
            <a href="orders.php" class="btn-primary">Track My Order</a>
      <a href="products.php" class="btn-outline">Continue Shopping</a>
    </div>
  </div>

  <?php else: ?>

  <!-- Checkout Form -->
  <div class="checkout-main">
    <h3>Delivery Information</h3>

    <?php if ($error): ?>
      <div class="form-message form-message--error">⚠ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="checkout.php" id="checkoutForm">

      <div class="form-group">
        <label>Full Name</label>
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input type="text" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly/>
        </div>
      </div>

      <div class="form-group">
        <label for="delivery_location">Delivery Location *</label>
        <div class="input-wrapper">
          <span class="input-icon">📍</span>
          <input type="text" id="delivery_location" name="delivery_location"
            placeholder="e.g. UoN Main Campus, Hostels Block C, Room 204"/>
        </div>
      </div>

      <div class="form-group">
        <label>Payment Method *</label>
        <div class="payment-options">
          <label class="payment-option" id="mpesaOption">
            <input type="radio" name="payment_method" value="Mpesa" id="mpesaRadio">
            <div class="payment-card">
              <span class="payment-icon">📱</span>
              <div>
                <strong>Mpesa</strong>
                <p>Pay via Mpesa STK Push</p>
              </div>
            </div>
          </label>
          <label class="payment-option">
            <input type="radio" name="payment_method" value="Cash on Delivery">
            <div class="payment-card">
              <span class="payment-icon">💵</span>
              <div>
                <strong>Cash on Delivery</strong>
                <p>Pay when your order arrives</p>
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- Mpesa number field (shown when Mpesa selected) -->
      <div class="form-group" id="mpesaField" style="display:none;">
        <label for="mpesa_number">Mpesa Number *</label>
        <div class="input-wrapper">
          <span class="input-icon">📱</span>
          <input type="tel" id="mpesa_number" name="mpesa_number" placeholder="07XXXXXXXX"/>
        </div>
      </div>

      <button type="submit" class="btn-submit">Place Order — KES <?php echo number_format($total); ?></button>
    </form>
  </div>

  <!-- Order Summary -->
  <div class="cart-summary">
    <h3>Order Summary</h3>
    <?php foreach ($cart_items as $item): ?>
    <div class="summary-item">
      <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
      <span>KES <?php echo number_format($item['total']); ?></span>
    </div>
    <?php endforeach; ?>
    <div class="summary-divider"></div>
    <div class="summary-row"><span>Subtotal</span><span>KES <?php echo number_format($subtotal); ?></span></div>
    <div class="summary-row"><span>Delivery</span><span>KES <?php echo number_format($delivery_fee); ?></span></div>
    <div class="summary-row summary-total"><span>Total</span><span>KES <?php echo number_format($total); ?></span></div>
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

<script src="js/main.js"></script>
<script>
  // Show/hide Mpesa number field
  document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      const mpesaField = document.getElementById('mpesaField');
      mpesaField.style.display = this.value === 'Mpesa' ? 'block' : 'none';
    });
  });
</script>
</body>
</html>