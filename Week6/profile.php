<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6 Profile Management
// BIT3208 Advanced Web Design and Development
// NEW: Full Profile CRUD — Edit profile + Change password
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = ''; $error = '';

// Fetch current user data
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// ============================================================
// UPDATE PROFILE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name    = trim($_POST['full_name']    ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $skin_type    = trim($_POST['skin_type']    ?? '');
    $budget       = trim($_POST['budget']       ?? 'medium');
    $bio          = trim($_POST['bio']          ?? '');

    if (empty($full_name)) {
        $error = 'Full name is required.';
    } else {
        $stmt = mysqli_prepare($conn,
            "UPDATE users SET full_name=?, phone_number=?, skin_type=?, budget=?, bio=? WHERE user_id=?"
        );
        mysqli_stmt_bind_param($stmt, "sssssi",
            $full_name, $phone_number, $skin_type, $budget, $bio, $user_id
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['full_name'] = $full_name;
            $_SESSION['skin_type'] = $skin_type;
            $message = 'Profile updated successfully!';

            // Refresh user data
            $stmt2 = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id=?");
            mysqli_stmt_bind_param($stmt2, "i", $user_id);
            mysqli_stmt_execute($stmt2);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
        } else {
            $error = 'Update failed. Please try again.';
        }
    }
}

// ============================================================
// CHANGE PASSWORD
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_password     = trim($_POST['new_password']     ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all password fields.';
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new_password) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt   = mysqli_prepare($conn, "UPDATE users SET password=? WHERE user_id=?");
        mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Password changed successfully!';
        } else {
            $error = 'Password change failed. Please try again.';
        }
    }
}

$cart_count    = getCartCount($conn, $user_id);
$wish_count    = getWishlistCount($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — My Profile</title>
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
      <li><a href="wishlist.php">Wishlist ❤️ <?php if($wish_count>0): ?><span class="cart-badge"><?php echo $wish_count; ?></span><?php endif; ?></a></li>
      <li><a href="cart.php">Cart <span class="cart-badge"><?php echo $cart_count; ?></span></a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>👤 My Profile</h1>
    <p>Manage your account information</p>
  </div>
</div>

<div class="container" style="padding:40px 24px 80px;display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start;">

  <!-- Update Profile -->
  <div style="background:white;border-radius:var(--radius-lg);border:1.5px solid var(--border);padding:32px;box-shadow:var(--shadow);">
    <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:24px;">Edit Profile</h2>

    <?php if ($message): ?><div class="form-message form-message--success">✓ <?php echo $message; ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>

    <form method="POST">

      <!-- Profile Avatar -->
      <div style="text-align:center;margin-bottom:24px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--pink),var(--lavender));display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:600;color:white;margin:0 auto 8px;">
          <?php echo strtoupper(substr($user['full_name'],0,1)); ?>
        </div>
        <p style="font-size:13px;color:var(--charcoal-light);">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Full Name</label>
          <div class="input-wrapper"><span class="input-icon">👤</span>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required/>
          </div>
        </div>
        <div class="form-group">
          <label>Email (cannot change)</label>
          <div class="input-wrapper"><span class="input-icon">✉</span>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background:var(--nude);cursor:not-allowed;"/>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Phone Number</label>
          <div class="input-wrapper"><span class="input-icon">📱</span>
            <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" placeholder="07XXXXXXXX"/>
          </div>
        </div>
        <div class="form-group">
          <label>Skin Type</label>
          <div class="input-wrapper"><span class="input-icon">🧴</span>
            <select name="skin_type">
              <?php foreach(['Oily','Dry','Combination','Normal','Sensitive'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo ($user['skin_type'] ?? '') === $s ? 'selected':''; ?>><?php echo $s; ?> Skin</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Beauty Budget</label>
        <div class="input-wrapper"><span class="input-icon">💰</span>
          <select name="budget">
            <?php foreach(['low'=>'Budget (Under KES 1,000)','medium'=>'Mid-range (KES 1,000–3,000)','high'=>'Premium (KES 3,000+)'] as $val=>$label): ?>
            <option value="<?php echo $val; ?>" <?php echo ($user['budget'] ?? 'medium') === $val ? 'selected':''; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Bio</label>
        <textarea name="bio" placeholder="Tell us about your beauty interests..."
          style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:var(--radius-md);font-size:14px;font-family:var(--font-body);outline:none;resize:vertical;min-height:80px;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
      </div>

      <button type="submit" name="update_profile" class="btn-primary" style="width:100%;">💾 Update Profile</button>
    </form>
  </div>

  <!-- Change Password -->
  <div style="background:white;border-radius:var(--radius-lg);border:1.5px solid var(--border);padding:32px;box-shadow:var(--shadow);">
    <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:24px;">Change Password</h2>

    <form method="POST">
      <div class="form-group">
        <label>Current Password</label>
        <div class="input-wrapper"><span class="input-icon">🔒</span>
          <input type="password" name="current_password" placeholder="Enter current password"/>
        </div>
      </div>
      <div class="form-group">
        <label>New Password</label>
        <div class="input-wrapper"><span class="input-icon">🔒</span>
          <input type="password" name="new_password" id="new_password" placeholder="Min 8 characters"/>
        </div>
        <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
        <span class="strength-text" id="strengthText"></span>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <div class="input-wrapper"><span class="input-icon">🔒</span>
          <input type="password" name="confirm_password" placeholder="Repeat new password"/>
        </div>
      </div>
      <button type="submit" name="change_password" class="btn-submit">🔐 Change Password</button>
    </form>

    <!-- Account Info -->
    <div style="margin-top:32px;padding:20px;background:var(--lav-soft);border-radius:var(--radius-md);">
      <h4 style="font-size:14px;font-weight:500;color:var(--lavender);margin-bottom:12px;">🔐 Account Security</h4>
      <p style="font-size:13px;color:var(--charcoal-mid);margin-bottom:6px;">✓ Password is bcrypt hashed</p>
      <p style="font-size:13px;color:var(--charcoal-mid);margin-bottom:6px;">✓ Brute force protection active</p>
      <p style="font-size:13px;color:var(--charcoal-mid);">✓ Session protected pages</p>
    </div>
  </div>

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
// Password strength for new password field
const newPass = document.getElementById('new_password');
if (newPass) {
  newPass.addEventListener('input', function() { checkPasswordStrength(this.value); });
}
</script>
</body>
</html>