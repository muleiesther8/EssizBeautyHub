<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 3
// BIT3208 - Advanced Web Design and Development
// File: login.php — Login Page
// ============================================================
$page_title = "Login";
$site_name  = "Essiz Beauty Hub";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $site_name; ?> — <?php echo $page_title; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

<!-- Decorative background -->
<div class="auth-bg">
  <div class="auth-circle auth-circle--1"></div>
  <div class="auth-circle auth-circle--2"></div>
</div>

<!-- Navbar -->
<nav class="navbar navbar--transparent">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">
      <span class="brand-star">✦</span>
      <?php echo $site_name; ?>
    </a>
  </div>
</nav>

<!-- Login Form -->
<div class="auth-wrapper">
  <div class="auth-card fade-in">

    <!-- Header -->
    <div class="auth-header">
      <div class="auth-icon">🌸</div>
      <h1>Welcome Back</h1>
      <p>Login to your Essiz Beauty Hub account</p>
    </div>

    <!-- Form -->
    <form id="loginForm" action="login.php" method="POST" novalidate>

      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-wrapper">
          <span class="input-icon">✉</span>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="your@email.com"
            autocomplete="email"
          />
        </div>
        <span class="error-msg" id="emailError"></span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            autocomplete="current-password"
          />
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

      <button type="submit" class="btn-submit" id="loginBtn">
        Login to My Account
      </button>

      <!-- PHP feedback message -->
      <?php if(isset($_POST['email'])): ?>
        <div class="form-message form-message--info">
          ℹ Week 3: PHP received your form data. Backend processing coming in Week 4!
        </div>
      <?php endif; ?>

    </form>

    <div class="auth-footer">
      <p>Don't have an account? <a href="register.php">Create one free</a></p>
    </div>

  </div>

  <!-- Side panel -->
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