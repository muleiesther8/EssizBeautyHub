<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Two Factor Authentication
// BIT3208 Advanced Web Design and Development
// File: two_factor.php
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/security.php';
require_once 'includes/csrf.php';

// Must have pending 2FA
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: login.php');
    exit();
}

$error   = '';
$user_id = $_SESSION['2fa_user_id'];
$name    = $_SESSION['2fa_user_name'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();

    $code = trim($_POST['code'] ?? '');

    if (empty($code)) {
        $error = 'Please enter the verification code.';
    } elseif (!preg_match('/^\d{6}$/', $code)) {
        $error = 'Code must be 6 digits.';
    } else {
        if (verify2FACode($conn, $user_id, $code)) {
            // Fetch full user
            $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id=?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            // Clear 2FA session vars
            unset($_SESSION['2fa_user_id'], $_SESSION['2fa_user_name'], $_SESSION['2fa_email'], $_SESSION['2fa_demo_code']);

            // Set full session
            regenerateSession();
            $_SESSION['user_id']       = $user['user_id'];
            $_SESSION['full_name']     = $user['full_name'];
            $_SESSION['email']         = $user['email'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['skin_type']     = $user['skin_type'];
            $_SESSION['last_activity'] = time();

            header($user['role'] === 'admin' ? 'Location: admin/dashboard.php' : 'Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid or expired code. Please try again.';
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Two Factor Authentication</title>
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
  <div class="auth-card fade-in" style="max-width:480px;">

    <div class="auth-header">
      <div class="auth-icon">🔑</div>
      <h1>Two-Factor Authentication</h1>
      <p>Hi <?php echo htmlspecialchars($name); ?>! Enter your 6-digit verification code.</p>
    </div>

    <?php if ($error): ?>
      <div class="form-message form-message--error">⚠ <?php echo $error; ?></div>
    <?php endif; ?>

    
    <form method="POST" action="two_factor.php">
      <?php echo csrfField(); ?>

      <div class="form-group" style="margin-top:24px;">
        <label style="text-align:center;display:block;">Verification Code</label>
        <input type="text" name="code" maxlength="6" placeholder="000000"
          style="width:100%;padding:16px;text-align:center;font-size:32px;letter-spacing:12px;border:1.5px solid var(--border);border-radius:var(--radius-md);outline:none;font-family:monospace;"
          autofocus/>
      </div>

      <button type="submit" class="btn-submit">Verify Code</button>
    </form>

    <!-- Timer -->
    <div style="text-align:center;margin-top:16px;">
      <p style="font-size:13px;color:var(--charcoal-light);">Code expires in <span id="timer" style="color:var(--pink);font-weight:600;">10:00</span></p>
    </div>

    <div class="auth-footer">
      <p><a href="login.php" style="color:var(--charcoal-light);">← Back to Login</a></p>
    </div>

  </div>
</div>

<script>
// Countdown timer
let seconds = 600;
const timer = document.getElementById('timer');
const countdown = setInterval(function() {
  seconds--;
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  timer.textContent = m + ':' + (s < 10 ? '0' : '') + s;
  if (seconds <= 0) {
    clearInterval(countdown);
    timer.textContent = 'Expired';
    timer.style.color = '#E53E3E';
  }
}, 1000);

// Auto-submit when 6 digits entered
document.querySelector('input[name="code"]').addEventListener('input', function() {
  if (this.value.length === 6) {
    this.closest('form').submit();
  }
});
</script>
<script src="js/main.js"></script>
</body>
</html>