<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Cart
// BIT3208 Advanced Web Design and Development
// File: cart.php
// ============================================================

require_once 'includes/db_connect.php';
require_once 'includes/session.php';


requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';

// ============================================================
// ADD TO CART
// ============================================================
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];

    // Check if already in cart
    $check = mysqli_prepare($conn, "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check);
    $check_result = mysqli_stmt_get_result($check);
    $existing     = mysqli_fetch_assoc($check_result);

    if ($existing) {
        // Update quantity
        $new_qty = $existing['quantity'] + 1;
        $update  = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE cart_id = ?");
        mysqli_stmt_bind_param($update, "ii", $new_qty, $existing['cart_id']);
        mysqli_stmt_execute($update);
    } else {
        // Insert new
        $insert = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        mysqli_stmt_bind_param($insert, "ii", $user_id, $product_id);
        mysqli_stmt_execute($insert);
    }

    $message = 'Product added to cart!';
}

// ============================================================
// REMOVE FROM CART
// ============================================================
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $delete  = mysqli_prepare($conn, "DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($delete, "ii", $cart_id, $user_id);
    mysqli_stmt_execute($delete);
    header('Location: cart.php');
    exit();
}

// ============================================================
// UPDATE QUANTITY
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $cart_id = (int)$cart_id;
        $qty     = (int)$qty;
        if ($qty > 0) {
            $update = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
            mysqli_stmt_bind_param($update, "iii", $qty, $cart_id, $user_id);
            mysqli_stmt_execute($update);
        } else {
            $delete = mysqli_prepare($conn, "DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
            mysqli_stmt_bind_param($delete, "ii", $cart_id, $user_id);
            mysqli_stmt_execute($delete);
        }
    }
    header('Location: cart.php?updated=1');
    exit();
}

// ============================================================
// FETCH CART ITEMS
// ============================================================
$cart_query = "
    SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.category
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";
$cart_result = mysqli_query($conn, $cart_query);
$cart_items  = [];
$subtotal    = 0;

while ($row = mysqli_fetch_assoc($cart_result)) {
    $row['total'] = $row['price'] * $row['quantity'];
    $subtotal    += $row['total'];
    $cart_items[] = $row;
}

$delivery_fee = $subtotal > 0 ? 200 : 0;
$total        = $subtotal + $delivery_fee;

$icons = ['Skincare'=>'🧴','Makeup'=>'💄','Haircare'=>'💆','Perfumes'=>'🌸','Accessories'=>'🪞'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Cart</title>
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
      <li><a href="cart.php" class="active">Cart <?php if(count($cart_items) > 0): ?><span class="cart-badge"><?php echo count($cart_items); ?></span><?php endif; ?></a></li>
      <li><a href="dashboard.php">My Account</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>🛒 Shopping Cart</h1>
    <p><?php echo count($cart_items); ?> item(s) in your cart</p>
  </div>
</div>

<div class="container cart-layout">

  <?php if (empty($cart_items)): ?>
  <!-- Empty Cart -->
  <div class="empty-cart">
    <div class="empty-icon">🛒</div>
    <h2>Your cart is empty</h2>
    <p>Looks like you haven't added anything yet.</p>
    <a href="products.php" class="btn-primary">Start Shopping</a>
  </div>

  <?php else: ?>

  <!-- Cart Items -->
  <div class="cart-main">
    <?php if (isset($_GET['updated'])): ?>
      <div class="form-message form-message--success">✓ Cart updated successfully!</div>
    <?php endif; ?>

    <form method="POST" action="cart.php">
      <div class="cart-table">
        <div class="cart-table-header">
          <span>Product</span>
          <span>Price</span>
          <span>Quantity</span>
          <span>Total</span>
          <span>Remove</span>
        </div>

        <?php foreach ($cart_items as $item):
          $icon = $icons[$item['category']] ?? '✨';
        ?>
        <div class="cart-row">
          <div class="cart-product">
            <div class="cart-product-icon"><?php echo $icon; ?></div>
            <div class="cart-product-info">
              <h4><?php echo htmlspecialchars($item['name']); ?></h4>
              <span><?php echo htmlspecialchars($item['category']); ?></span>
            </div>
          </div>
          <div class="cart-price">KES <?php echo number_format($item['price']); ?></div>
          <div class="cart-qty">
            <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]"
              value="<?php echo $item['quantity']; ?>" min="0" max="99"
              class="qty-input"/>
          </div>
          <div class="cart-total">KES <?php echo number_format($item['total']); ?></div>
          <div class="cart-remove">
            <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="remove-btn">✕</a>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
      <div class="cart-actions">
        <a href="products.php" class="btn-outline">← Continue Shopping</a>
        <button type="submit" name="update_cart" class="btn-primary">Update Cart</button>
      </div>
    </form>
  </div>

  <!-- Order Summary -->
  <div class="cart-summary">
    <h3>Order Summary</h3>
    <div class="summary-row">
      <span>Subtotal</span>
      <span>KES <?php echo number_format($subtotal); ?></span>
    </div>
    <div class="summary-row">
      <span>Delivery Fee</span>
      <span>KES <?php echo number_format($delivery_fee); ?></span>
    </div>
    <div class="summary-row summary-total">
      <span>Total</span>
      <span>KES <?php echo number_format($total); ?></span>
    </div>
    <a href="checkout.php" class="btn-primary btn-full">Proceed to Checkout →</a>
    <div class="summary-note">🔒 Secure checkout | Mpesa & Cash on Delivery</div>
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
</body>
</html>