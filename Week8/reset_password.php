<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Reset Password
// BIT3208 Advanced Web Design and Development
// File: reset_password.php
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/security.php';
require_once 'includes/csrf.php';

$token   = trim($_GET['token'] ?? '');
$message = ''; $error = '';
$valid_token = null;

if (empty($token)) {
    header('Location: forgot_password.php');
    exit();
}

// Validate token
$valid_token = validateResetToken($conn, $token);

if (!$valid_token) {
    $error = 'This reset link is invalid or has expired. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    validateCSRF();

    $new_password     = trim($_POST['new_password']     ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt   = mysqli_prepare($conn,
            "UPDATE users SET password=?, login_attempts=0, locked_until=NULL WHERE user_id=?"
        );
        mysqli_stmt_bind_param($stmt, "si", $hashed, $valid_token['user_id']);

        if (mysqli_stmt_execute($stmt)) {
            markTokenUsed($conn, $token);
            $message = 'success';
        } else {
            $error = 'Password reset failed. Please try again.';
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Reset Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="auth-bg"><div class="auth-circle auth-circle--1"></div><div class="auth-circle auth-circle--2"></div></div>
<nav class="navbar navbar--transparent">
  <div class="nav-container"><a href="index.php" class="nav-brand"><span class="brand-star">✦</span> Essiz Beauty Hub</a></div>
</nav>

<div class="auth-wrapper" style="justify-content:center;">
  <div class="auth-card fade-in">

    <?php if ($message === 'success'): ?>
    <div style="text-align:center;padding:20px 0;">
      <div style="font-size:64px;margin-bottom:16px;">✅</div>
      <h2 style="font-family:var(--font-display);font-size:28px;font-weight:400;margin-bottom:12px;">Password Reset!</h2>
      <p style="color:var(--charcoal-light);margin-bottom:24px;">Your password has been updated successfully.</p>
      <a href="login.php?reset=1" class="btn-primary" style="display:block;text-align:center;">Login with New Password</a>
    </div>

    <?php elseif ($error && !$valid_token): ?>
    <div style="text-align:center;padding:20px 0;">
      <div style="font-size:64px;margin-bottom:16px;">⛔</div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:12px;">Link Expired</h2>
      <p style="color:var(--charcoal-light);margin-bottom:24px;"><?php echo $error; ?></p>
      <a href="forgot_password.php" class="btn-primary" style="display:inline-block;">Request New Link</a>
    </div>

    <?php else: ?>
    <div class="auth-header">
      <div class="auth-icon">🔐</div>
      <h1>Reset Password</h1>
      <p>Hi <?php echo htmlspecialchars($valid_token['full_name']); ?>! Choose your new password.</p>
    </div>

    <?php if ($error): ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>

    <form method="POST">
      <?php echo csrfField(); ?>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

      <div class="form-group">
        <label>New Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="new_password" placeholder="Min 8 characters"/>
          <button type="button" class="toggle-password" id="togglePassword">👁</button>
        </div>
        <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
        <span class="strength-text" id="strengthText"></span>
      </div>

      <div class="form-group">
        <label>Confirm New Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" name="confirm_password" placeholder="Repeat new password"/>
        </div>
      </div>

      <button type="submit" class="btn-submit">Reset My Password</button>
    </form>

    <div class="auth-footer"><p><a href="login.php">← Back to Login</a></p></div>
    <?php endif; ?>

  </div>
</div>
<script src="js/main.js"></script>
</body>
</html>