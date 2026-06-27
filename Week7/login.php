<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Login
// BIT3208 Advanced Web Design and Development
// NEW: CSRF Protection + 2FA + Session Security
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security.php';

// Set security headers
setSecurityHeaders();
redirectIfLoggedIn();

$error = ''; $success = '';
if (isset($_GET['msg']))        $error   = xssClean($_GET['msg']);
if (isset($_GET['registered'])) $success = 'Account created! Please login.';
if (isset($_GET['reset']))      $success = 'Password reset successfully! Please login.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate CSRF token
    validateCSRF();

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check brute force
        $attempt_check = checkLoginAttempts($conn, $email);

        if ($attempt_check['locked']) {
            $error = "⛔ Account locked. Try again in {$attempt_check['minutes']} minute(s).";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if ($user && password_verify($password, $user['password'])) {

                // Reset login attempts
                resetLoginAttempts($conn, $email);

                // Regenerate session ID (prevents session fixation)
                regenerateSession();

                // Check if 2FA is enabled
                if ($user['two_factor_enabled']) {
                    // Generate and store 2FA code
                $code = generate2FACode();
                store2FACode($conn, $user['user_id'], $code);

                   // Store pending user in session
                $_SESSION['2fa_user_id']   = $user['user_id'];
                $_SESSION['2fa_user_name'] = $user['full_name'];
                $_SESSION['2fa_email']     = $user['email'];

                   // Send 2FA code via email
                require_once 'includes/mailer.php';
                send2FAEmail($user['email'], $user['full_name'], $code);

                header('Location: two_factor.php');
                exit();
                }

                // Normal login — set session
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['skin_type'] = $user['skin_type'];
                $_SESSION['profile_photo']= $user['profile_photo'];
                $_SESSION['last_activity'] = time();

                header($user['role'] === 'admin' ? 'Location: admin/dashboard.php' : 'Location: dashboard.php');
                exit();

            } else {
                if ($user) recordFailedAttempt($conn, $email);
                $remaining = max(0, 3 - (($attempt_check['attempts'] ?? 0) + 1));
                $error = $remaining > 0
                    ? "Invalid email or password. $remaining attempt(s) remaining before lockout."
                    : "Invalid credentials. Account will be locked on next failed attempt.";
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Secure Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="auth-bg"><div class="auth-circle auth-circle--1"></div><div class="auth-circle auth-circle--2"></div></div>
<nav class="navbar navbar--transparent">
  <div class="nav-container"><a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a></div>
</nav>

<div class="auth-wrapper">
  <div class="auth-card fade-in">
    <div class="auth-header">
      <div class="auth-icon">🔐</div>
      <h1>Secure Login</h1>
      <p>Protected with CSRF tokens and brute force detection</p>
    </div>

    <?php if ($error):   ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="form-message form-message--success">✓ <?php echo $success; ?></div><?php endif; ?>

    <form id="loginForm" action="login.php" method="POST" novalidate>
      <!-- CSRF Token — Week 7 Security Feature -->
      <?php echo csrfField(); ?>

      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrapper">
          <span class="input-icon">✉</span>
          <input type="email" id="email" name="email" placeholder="your@email.com"
            value="<?php echo xssClean($_POST['email'] ?? ''); ?>"/>
        </div>
        <span class="error-msg" id="emailError"></span>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="password" placeholder="Enter your password"/>
          <button type="button" class="toggle-password" id="togglePassword">👁</button>
        </div>
        <span class="error-msg" id="passwordError"></span>
      </div>

      <div class="form-options">
        <label class="checkbox-label"><input type="checkbox" name="remember"> Remember me</label>
        <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="btn-submit">Login Securely</button>
    </form>

    <div class="auth-footer"><p>Don't have an account? <a href="register.php">Create one free</a></p></div>
  </div>

  <div class="auth-side fade-in">
  <div class="auth-side-content">
    <h2>Your Beauty Journey Starts Here</h2>
    <p>Join thousands of campus beauties discovering their perfect routine.</p>
    <div class="auth-features">
      <div class="auth-feature">✦ Personalized recommendations</div>
      <div class="auth-feature">✦ Exclusive campus deals</div>
      <div class="auth-feature">✦ Beauty routine builder</div>
      <div class="auth-feature">✦ Track your orders</div>
      <div class="auth-feature">✦ Wishlist your favorites</div>
      <div class="auth-feature">✦ Write product reviews</div>
    </div>
    <div style="margin-top:32px;padding:16px;background:rgba(255,255,255,0.15);border-radius:12px;">
      <p style="font-size:13px;color:rgba(255,255,255,0.9);">
        🌸 Your beauty data is safe and secure with us
      </p>
    </div>
  </div>
</div>

<script src="js/main.js"></script>
</body>
</html>