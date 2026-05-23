<?php
// ============================================================
// ESSIZ BEAUTY HUB - Week 1
// BIT3208 - Advanced Web Design and Development
// Week 1 Task: Hello World, Localhost Test, Basic Setup
// ============================================================

echo "<!-- PHP is working! Server-side rendering active -->";
$site_name = "Essiz Beauty Hub";
$student_message = "Hello World! Welcome to " . $site_name;
$week = "Week 1";
$status = "Localhost running successfully ✓";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Essiz Beauty Hub – Week 1</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- ===== HERO SPLASH ===== -->
  <div class="splash-wrapper">

    <!-- Decorative background elements -->
    <div class="bg-circle bg-circle--1"></div>
    <div class="bg-circle bg-circle--2"></div>
    <div class="bg-circle bg-circle--3"></div>
    <div class="grain-overlay"></div>

    <div class="splash-content">

      <!-- Week badge -->
      <div class="week-badge fade-in" style="animation-delay:0.1s">
        🌸 <?php echo $week; ?> — BIT3208
      </div>

      <!-- Brand -->
      <div class="brand fade-in" style="animation-delay:0.3s">
        <div class="brand-icon">✦</div>
        <h1 class="brand-name"><?php echo $site_name; ?></h1>
        <p class="brand-tagline"><em>Intelligent Beauty. Campus Confidence.</em></p>
      </div>

      <!-- Hello World output -->
      <div class="hello-card fade-in" style="animation-delay:0.5s">
        <div class="hello-label">PHP Output</div>
        <p class="hello-text"><?php echo $student_message; ?></p>
      </div>

      <!-- Status checks -->
      <div class="status-grid fade-in" style="animation-delay:0.7s">
        <div class="status-item status--ok">
          <span class="status-dot"></span>
          <span><?php echo $status; ?></span>
        </div>
        <div class="status-item status--ok">
          <span class="status-dot"></span>
          <span>PHP <?php echo phpversion(); ?> active</span>
        </div>
        <div class="status-item" id="db-status">
          <span class="status-dot status-dot--pending"></span>
          <span id="db-status-text">Testing database connection...</span>
        </div>
      </div>

      <!-- DB test result from PHP -->
      <?php
        // Week 1 - Basic Database Connection Test
        $db_host = "localhost:3307";
        $db_user = "root";
        $db_pass = "";
        $db_name = "essizdb_w1";

        $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if ($conn) {
          $db_connected = true;
          $db_message = "Database 'essizdb_w1' connected successfully ✓";
          mysqli_close($conn);
        } else {
          $db_connected = false;
          $db_message = "Database not yet created — run essizdb_w1.sql first";
        }
      ?>
      <script>
        // Update DB status dynamically from PHP result
        const dbOk = <?php echo $db_connected ? 'true' : 'false'; ?>;
        const dbMsg = <?php echo json_encode($db_message); ?>;
        window.addEventListener('DOMContentLoaded', function() {
          const el = document.getElementById('db-status');
          const dot = el.querySelector('.status-dot');
          const txt = document.getElementById('db-status-text');
          txt.textContent = dbMsg;
          if (dbOk) {
            el.classList.add('status--ok');
            dot.classList.remove('status-dot--pending');
          } else {
            el.classList.add('status--warn');
            dot.classList.add('status-dot--warn');
          }
        });
      </script>

      <!-- Feature preview cards -->
      <div class="preview-section fade-in" style="animation-delay:0.9s">
        <p class="preview-label">Coming in the next weeks</p>
        <div class="preview-grid">
          <div class="preview-card">
            <span class="preview-icon">🛍️</span>
            <span>Product Shop</span>
          </div>
          <div class="preview-card">
            <span class="preview-icon">💄</span>
            <span>Beauty Routines</span>
          </div>
          <div class="preview-card">
            <span class="preview-icon">🧴</span>
            <span>Skincare Recs</span>
          </div>
          <div class="preview-card">
            <span class="preview-icon">📦</span>
            <span>Order Tracking</span>
          </div>
          <div class="preview-card">
            <span class="preview-icon">⭐</span>
            <span>Reviews</span>
          </div>
          <div class="preview-card">
            <span class="preview-icon">📊</span>
            <span>Admin Panel</span>
          </div>
        </div>
      </div>

      <!-- Footer note -->
      <div class="footer-note fade-in" style="animation-delay:1.1s">
        <p>BIT3208 Advanced Web Design &amp; Development &nbsp;|&nbsp; Week 1 Deliverable</p>
        <p><?php echo date("l, d F Y"); ?></p>
      </div>

    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>