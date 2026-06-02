<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 4 Login
// BIT3208 Advanced Web Design and Development
// File: login.php
// Week 4: Real PHP authentication + sessions
// ============================================================

require_once 'includes/db_connect.php';
require_once 'includes/session.php';

// Redirect if already logged in
redirectIfLoggedIn();

$error   = '';
$success = '';

// Get message from URL if redirected
if (isset($_GET['msg'])) {
    $error = htmlspecialchars($_GET['msg']);
}

if (isset($_GET['registered'])) {
    $success = 'Account created successfully! Please login.';
}

// ============================================================
// PROCESS LOGIN FORM
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {

        // Secure query using prepared statements (SQL injection prevention)
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user) {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {

                // Start session
                session_start();
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();

            } else {
                $error = 'Incorrect password. Please try again.';
            }
        } else {
            $error = 'No account found with that email address.';
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

<div class="auth-bg">
  <div class="auth-circle auth-circle--1"></div>
  <div class="auth-circle auth-circle--2"></div>
</div>

<nav class="navbar navbar--transparent">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">
      <span class="brand-star">✦</span> Essiz Beauty Hub
    </a>
  </div>
</nav>

<div class="auth-wrapper">
  <div class="auth-card fade-in">

    <div class="auth-header">
      <div class="auth-icon">🌸</div>
      <h1>Welcome Back</h1>
      <p>Login to your Essiz Beauty Hub account</p>
    </div>

    <!-- PHP Error/Success Messages -->
    <?php if ($error): ?>
      <div class="form-message form-message--error">⚠ <?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="form-message form-message--success">✓ <?php echo $success; ?></div>
    <?php endif; ?>

    <form id="loginForm" action="login.php" method="POST" novalidate>

      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
          <span class="input-icon">✉</span>
          <input type="email" id="email" name="email"
            placeholder="your@email.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
          />
        </div>
        <span class="error-msg" id="emailError"></span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="password" placeholder="Enter your password"/>
          <button type="button" class="toggle-password" id="togglePassword">👁</button>
        </div>
        <span class="error-msg" id="passwordError"></span>
      </div>

      <div class="form-options">
        <label class="checkbox-label">
          <input type="checkbox" name="remember"> Remember me
        </label>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="btn-submit">Login to My Account</button>

      <!-- Demo credentials hint -->
      <div class="demo-hint">
        <p>🔑 Demo credentials:</p>
        <p><strong>Admin:</strong> admin@essizbeautyhub.com / Admin@1234</p>
        <p><strong>Customer:</strong> janewanjiru254@gmail.com / Pass@1234</p>
      </div>

    </form>

    <div class="auth-footer">
      <p>Don't have an account? <a href="register.php">Create one free</a></p>
    </div>

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
      </div>
    </div>
  </div>

</div>

<script src="js/main.js"></script>
</body>
</html>