<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 5 Register
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';

redirectIfLoggedIn();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name']        ?? '');
    $email            = trim($_POST['email']            ?? '');
    $phone_number     = trim($_POST['phone_number']     ?? '');
    $skin_type        = trim($_POST['skin_type']        ?? '');
    $budget           = trim($_POST['budget']           ?? 'medium');
    $password         = trim($_POST['password']         ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($full_name) || empty($email) || empty($phone_number) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt   = mysqli_prepare($conn, "INSERT INTO users (full_name, email, phone_number, skin_type, budget, password, role) VALUES (?,?,?,?,?,?,'customer')");
mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $email, $phone_number, $skin_type, $budget, $hashed);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: login.php?registered=1'); exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Register</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="auth-bg"><div class="auth-circle auth-circle--1"></div><div class="auth-circle auth-circle--2"></div></div>
<nav class="navbar navbar--transparent">
  <div class="nav-container"><a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a></div>
</nav>
<div class="auth-wrapper auth-wrapper--register">
  <div class="auth-card auth-card--wide fade-in">
    <div class="auth-header">
      <div class="auth-icon">✨</div>
      <h1>Create Your Account</h1>
      <p>Join Essiz Beauty Hub — your campus beauty destination</p>
    </div>
    <?php if ($error): ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>
    <form id="registerForm" action="register.php" method="POST" novalidate>
      <div class="form-row">
        <div class="form-group">
          <label>Full Name</label>
          <div class="input-wrapper"><span class="input-icon">👤</span>
            <input type="text" id="full_name" name="full_name" placeholder="Jane Wanjiru" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"/>
          </div>
          <span class="error-msg" id="nameError"></span>
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrapper"><span class="input-icon">✉</span>
            <input type="email" id="email" name="email" placeholder="jane@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"/>
          </div>
          <span class="error-msg" id="emailError"></span>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Phone Number</label>
          <div class="input-wrapper"><span class="input-icon">📱</span>
            <input type="tel" id="phone_number" name="phone_number" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>"/>
          </div>
          <span class="error-msg" id="phoneError"></span>
        </div>
        <div class="form-group">
          <label>Skin Type</label>
          <div class="input-wrapper"><span class="input-icon">🧴</span>
            <select id="skin_type" name="skin_type">
              <option value="">Select skin type</option>
              <?php foreach(['Oily','Dry','Combination','Normal','Sensitive'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo ($_POST['skin_type'] ?? '') === $s ? 'selected':''; ?>><?php echo $s; ?> Skin</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrapper"><span class="input-icon">🔒</span>
            <input type="password" id="password" name="password" placeholder="Min 8 characters"/>
            <button type="button" class="toggle-password" id="togglePassword">👁</button>
          </div>
          <span class="error-msg" id="passwordError"></span>
          <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
          <span class="strength-text" id="strengthText"></span>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <div class="input-wrapper"><span class="input-icon">🔒</span>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password"/>
          </div>
          <span class="error-msg" id="confirmError"></span>
        </div>
      </div>
      <div class="form-group">
        <label>Beauty Budget</label>
        <div class="budget-options">
          <?php foreach(['low'=>'💰 Budget (Under KES 1,000)','medium'=>'💳 Mid-range (KES 1,000–3,000)','high'=>'💎 Premium (KES 3,000+)'] as $val => $label): ?>
          <label class="budget-option">
            <input type="radio" name="budget" value="<?php echo $val; ?>" <?php echo ($val === 'medium') ? 'checked' : ''; ?>>
            <span><?php echo $label; ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" id="terms" name="terms">
          I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
        </label>
        <span class="error-msg" id="termsError"></span>
      </div>
      <button type="submit" class="btn-submit" id="registerBtn">Create My Account</button>
    </form>
    <div class="auth-footer"><p>Already have an account? <a href="login.php">Login here</a></p></div>
  </div>
</div>
<script src="js/main.js"></script>
</body>
</html>