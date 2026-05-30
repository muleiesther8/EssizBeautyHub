<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 3
// BIT3208 - Advanced Web Design and Development
// File: register.php — Registration Page
// ============================================================
$page_title = "Register";
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

<div class="auth-bg">
  <div class="auth-circle auth-circle--1"></div>
  <div class="auth-circle auth-circle--2"></div>
</div>

<nav class="navbar navbar--transparent">
  <div class="nav-container">
    <a href="index.php" class="nav-brand">
      <span class="brand-star">✦</span>
      <?php echo $site_name; ?>
    </a>
  </div>
</nav>

<div class="auth-wrapper auth-wrapper--register">
  <div class="auth-card auth-card--wide fade-in">

    <div class="auth-header">
      <div class="auth-icon">✨</div>
      <h1>Create Your Account</h1>
      <p>Join Essiz Beauty Hub — your campus beauty destination</p>
    </div>

    <form id="registerForm" action="register.php" method="POST" novalidate>

      <div class="form-row">
        <!-- Full Name -->
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <div class="input-wrapper">
            <span class="input-icon">👤</span>
            <input
              type="text"
              id="full_name"
              name="full_name"
              placeholder="Jane Wanjiru"
            />
          </div>
          <span class="error-msg" id="nameError"></span>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <span class="input-icon">✉</span>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="jane@example.com"
            />
          </div>
          <span class="error-msg" id="emailError"></span>
        </div>
      </div>

      <div class="form-row">
        <!-- Phone -->
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <div class="input-wrapper">
            <span class="input-icon">📱</span>
            <input
              type="tel"
              id="phone"
              name="phone_number"
              placeholder="07XXXXXXXX"
            />
          </div>
          <span class="error-msg" id="phoneError"></span>
        </div>

        <!-- Skin Type -->
        <div class="form-group">
          <label for="skin_type">Skin Type</label>
          <div class="input-wrapper">
            <span class="input-icon">🧴</span>
            <select id="skin_type" name="skin_type">
              <option value="">Select your skin type</option>
              <option value="oily">Oily Skin</option>
              <option value="dry">Dry Skin</option>
              <option value="combination">Combination Skin</option>
              <option value="normal">Normal Skin</option>
              <option value="sensitive">Sensitive Skin</option>
            </select>
          </div>
          <span class="error-msg" id="skinError"></span>
        </div>
      </div>

      <div class="form-row">
        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Min 8 characters"
            />
            <button type="button" class="toggle-password" id="togglePassword">👁</button>
          </div>
          <span class="error-msg" id="passwordError"></span>
          <!-- Password strength indicator -->
          <div class="password-strength">
            <div class="strength-bar" id="strengthBar"></div>
          </div>
          <span class="strength-text" id="strengthText"></span>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              placeholder="Repeat your password"
            />
          </div>
          <span class="error-msg" id="confirmError"></span>
        </div>
      </div>

      <!-- Terms -->
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" id="terms" name="terms">
          I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
        </label>
        <span class="error-msg" id="termsError"></span>
      </div>

      <button type="submit" class="btn-submit" id="registerBtn">
        Create My Account
      </button>

      <?php if(isset($_POST['full_name'])): ?>
        <div class="form-message form-message--info">
          ℹ Week 3: PHP received your registration data. Full backend processing in Week 4!
        </div>
      <?php endif; ?>

    </form>

    <div class="auth-footer">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

  </div>
</div>

<script src="js/main.js"></script>
</body>
</html>