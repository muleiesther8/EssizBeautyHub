<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Forgot Password
// BIT3208 Advanced Web Design and Development
// NEW: Real email reset token system
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/security.php';
require_once 'includes/csrf.php';

redirectIfLoggedIn();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email exists
        $stmt = mysqli_prepare($conn, "SELECT user_id, full_name FROM users WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user) {
            // Generate reset token
            $token      = generateResetToken($conn, $user['user_id']);
            $reset_link = 'http://localhost/EssizBeautyHub/Week7/reset_password.php?token=' . $token;

            // Try to send email via PHPMailer
            $email_sent = false;
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once 'includes/mailer.php';
                $email_sent = sendPasswordResetEmail($email, $user['full_name'], $token);
            }

            if ($email_sent) {
                $message = 'email_sent';
            } else {
                // Demo mode — show link directly
                $message = 'demo';
                $_SESSION['demo_reset_link'] = $reset_link;
                $_SESSION['demo_reset_name'] = $user['full_name'];
            }
        } else {
            // Don't reveal if email exists (security best practice)
            $message = 'email_sent';
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Forgot Password</title>
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

    <?php if ($message === 'email_sent'): ?>
    <!-- Email sent success -->
    <div style="text-align:center;padding:20px 0;">
      <div style="font-size:64px;margin-bottom:16px;">📧</div>
      <h2 style="font-family:var(--font-display);font-size:28px;font-weight:400;margin-bottom:12px;">Check Your Email!</h2>
      <p style="color:var(--charcoal-light);margin-bottom:8px;">If an account exists with that email, we've sent a password reset link.</p>
      <p style="color:var(--charcoal-light);font-size:13px;margin-bottom:24px;">The link expires in 1 hour.</p>
      <a href="login.php" class="btn-primary" style="display:inline-block;">← Back to Login</a>
    </div>

    <?php elseif ($message === 'demo'): ?>
    <!-- Demo mode — show link directly -->
    <div style="text-align:center;padding:20px 0;">
      <div style="font-size:48px;margin-bottom:16px;">🔗</div>
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:12px;">Demo Mode</h2>
      <p style="color:var(--charcoal-light);font-size:13px;margin-bottom:16px;">PHPMailer not configured. In production, this link would be sent to your email. Click below to reset:</p>
      <a href="<?php echo htmlspecialchars($_SESSION['demo_reset_link'] ?? '#'); ?>"
         class="btn-primary" style="display:inline-block;margin-bottom:16px;">
        Reset Password →
      </a>
      <br>
      <a href="login.php" style="color:var(--charcoal-light);font-size:13px;">← Back to Login</a>
    </div>

    <?php else: ?>
    <!-- Form -->
    <div class="auth-header">
      <div class="auth-icon">🔐</div>
      <h1>Forgot Password?</h1>
      <p>Enter your email and we'll send you a reset link</p>
    </div>

    <?php if ($error): ?>
      <div class="form-message form-message--error">⚠ <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="forgot_password.php">
      <?php echo csrfField(); ?>

      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrapper">
          <span class="input-icon">✉</span>
          <input type="email" name="email" placeholder="your@email.com"
            value="<?php echo xssClean($_POST['email'] ?? ''); ?>"/>
        </div>
      </div>

      <button type="submit" class="btn-submit">Send Reset Link</button>
    </form>

    <div class="auth-footer">
      <p>Remember your password? <a href="login.php">Login here</a></p>
    </div>
    <?php endif; ?>

  </div>
</div>

<script src="js/main.js"></script>
</body>
</html>