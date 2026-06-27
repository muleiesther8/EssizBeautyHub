<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7 Profile Management
// BIT3208 Advanced Web Design and Development
// NEW: Profile photo upload + full profile CRUD
// ============================================================
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security.php';

setSecurityHeaders();
requireLogin();

$user_id = $_SESSION['user_id'];
$message = ''; $error = '';

// ============================================================
// TOGGLE 2FA
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_2fa'])) {
    $new_2fa = (int)$_POST['two_factor_enabled'];
    mysqli_query($conn, "UPDATE users SET two_factor_enabled=$new_2fa WHERE user_id=$user_id");
    header('Location: profile.php?updated=1');
    exit();
}

// ============================================================
// REMOVE PROFILE PHOTO
// ============================================================
if (isset($_GET['remove_photo'])) {
    $stmt_del = mysqli_prepare($conn, "SELECT profile_photo FROM users WHERE user_id=?");
    mysqli_stmt_bind_param($stmt_del, "i", $user_id);
    mysqli_stmt_execute($stmt_del);
    $del_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_del));
    if ($del_user['profile_photo'] && file_exists('images/profiles/' . $del_user['profile_photo'])) {
        unlink('images/profiles/' . $del_user['profile_photo']);
    }
    mysqli_query($conn, "UPDATE users SET profile_photo=NULL WHERE user_id=$user_id");
    $_SESSION['profile_photo'] = null;
    header('Location: profile.php?updated=1');
    exit();
}

// Fetch current user data
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// ============================================================
// UPDATE PROFILE + PHOTO UPLOAD
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    validateCSRF();

    $full_name     = trim($_POST['full_name']    ?? '');
    $phone_number  = trim($_POST['phone_number'] ?? '');
    $skin_type     = trim($_POST['skin_type']    ?? '');
    $budget        = trim($_POST['budget']       ?? 'medium');
    $bio           = trim($_POST['bio']          ?? '');
    $profile_photo = $user['profile_photo'] ?? null;

    // Handle photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['profile_photo'];
        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowed)) {
            $error = 'Only JPG, PNG, GIF and WEBP images are allowed.';
        } elseif ($file['size'] > $max_size) {
            $error = 'Image must be less than 2MB.';
        } else {
            if ($user['profile_photo'] && file_exists('images/profiles/' . $user['profile_photo'])) {
                unlink('images/profiles/' . $user['profile_photo']);
            }
            $ext         = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename    = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = 'images/profiles/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $profile_photo = $filename;
            } else {
                $error = 'Failed to upload image. Please try again.';
            }
        }
    }

    if (empty($error)) {
        if (empty($full_name)) {
            $error = 'Full name is required.';
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE users SET full_name=?, phone_number=?, skin_type=?, budget=?, bio=?, profile_photo=? WHERE user_id=?"
            );
            mysqli_stmt_bind_param($stmt, "ssssssi",
                $full_name, $phone_number, $skin_type, $budget, $bio, $profile_photo, $user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['full_name']     = $full_name;
                $_SESSION['skin_type']     = $skin_type;
                $_SESSION['profile_photo'] = $profile_photo;
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
}

// ============================================================
// CHANGE PASSWORD
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    validateCSRF();

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

$cart_count = getCartCount($conn, $user_id);
$wish_count = getWishlistCount($conn, $user_id);
$csrf_token = generateCSRFToken();

// Profile photo URL
$photo_url = $user['profile_photo']
    ? 'images/profiles/' . $user['profile_photo']
    : null;
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
    <ul class="nav-links" id="navLinks">
      <?php if (!empty($_SESSION['profile_photo'])): ?>
  <img src="images/profiles/<?php echo htmlspecialchars($_SESSION['profile_photo']); ?>"
       style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--pink);"/>
<?php endif; ?>
      <li><button class="dark-toggle" id="darkToggle">🌙</button></li>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="wishlist.php">Wishlist ❤️ <span class="cart-badge"><?php echo $wish_count; ?></span></a></li>
      <li><a href="cart.php">Cart <span class="cart-badge"><?php echo $cart_count; ?></span></a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="profile.php" class="active">Profile</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>👤 My Profile</h1>
    <p>Manage your account information and photo</p>
  </div>
</div>

<div class="container" style="padding:40px 24px 80px;">

  <?php if ($message): ?><div class="form-message form-message--success">✓ <?php echo $message; ?></div><?php endif; ?>
  <?php if ($error):   ?><div class="form-message form-message--error">⚠ <?php echo $error; ?></div><?php endif; ?>
  <?php if (isset($_GET['updated'])): ?><div class="form-message form-message--success">✓ Profile photo removed.</div><?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start;">

    <!-- LEFT: Edit Profile + Photo -->
    <div style="background:white;border-radius:var(--radius-lg);border:1.5px solid var(--border);padding:32px;box-shadow:var(--shadow);">
      <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:24px;">Edit Profile</h2>

      <form method="POST" enctype="multipart/form-data">
        <?php echo csrfField(); ?>

        <!-- Profile Photo Section -->
        <div style="text-align:center;margin-bottom:28px;">

          <!-- Photo Display -->
          <div style="position:relative;display:inline-block;margin-bottom:12px;">
            <?php if ($photo_url): ?>
              <img src="<?php echo htmlspecialchars($photo_url); ?>"
                   alt="Profile Photo"
                   style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--pink);"/>
              <a href="profile.php?remove_photo=1"
                 onclick="return confirm('Remove profile photo?')"
                 style="position:absolute;top:0;right:0;background:var(--danger);color:white;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:12px;text-decoration:none;"
                 title="Remove photo">✕</a>
            <?php else: ?>
              <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--pink),var(--lavender));display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:600;color:white;border:3px solid var(--pink);">
                <?php echo strtoupper(substr($user['full_name'],0,1)); ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Upload Button -->
          <div>
            <label for="profile_photo"
                   style="display:inline-block;padding:8px 20px;background:var(--pink-light);color:var(--pink);border:1.5px solid var(--pink-soft);border-radius:var(--radius-md);cursor:pointer;font-size:13px;font-weight:500;transition:var(--transition);">
              📷 <?php echo $photo_url ? 'Change Photo' : 'Upload Photo'; ?>
            </label>
            <input type="file" id="profile_photo" name="profile_photo"
                   accept="image/jpeg,image/png,image/gif,image/webp"
                   style="display:none;"
                   onchange="previewPhoto(this)"/>
          </div>
          <p style="font-size:11px;color:var(--charcoal-light);margin-top:6px;">JPG, PNG, GIF or WEBP · Max 2MB</p>

          <!-- Preview -->
          <div id="photoPreview" style="display:none;margin-top:12px;">
            <img id="previewImg" src="" alt="Preview"
                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--lavender);"/>
            <p style="font-size:12px;color:var(--lavender);margin-top:4px;">Preview — save to apply</p>
          </div>
        </div>

        <!-- Profile Fields -->
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

        <button type="submit" name="update_profile" class="btn-primary" style="width:100%;">💾 Save Profile</button>
      </form>
    </div>

    <!-- RIGHT: Change Password + Account Info -->
    <div style="display:flex;flex-direction:column;gap:24px;">

      <!-- Change Password -->
      <div style="background:white;border-radius:var(--radius-lg);border:1.5px solid var(--border);padding:32px;box-shadow:var(--shadow);">
        <h2 style="font-family:var(--font-display);font-size:24px;font-weight:400;margin-bottom:24px;">Change Password</h2>
        <form method="POST">
          <?php echo csrfField(); ?>
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
      </div>

      <!-- Account Info -->
      <div style="background:var(--lav-soft);border:1.5px solid var(--lav-mid);border-radius:var(--radius-lg);padding:24px;">
        <h3 style="font-size:16px;font-weight:500;color:var(--lavender);margin-bottom:16px;">🔐 Account Security</h3>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">Member since</span>
            <strong><?php echo date('d M Y', strtotime($user['created_at'])); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">Role</span>
            <strong><?php echo ucfirst($user['role']); ?></strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">Password</span>
            <strong style="color:var(--success);">✓ bcrypt hashed</strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">CSRF Protection</span>
            <strong style="color:var(--success);">✓ Active</strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">Login Attempts</span>
            <strong style="color:<?php echo ($user['login_attempts'] ?? 0) > 0 ? 'var(--warning)' : 'var(--success)'; ?>">
              <?php echo $user['login_attempts'] ?? 0; ?> / 3
            </strong>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px;">
            <span style="color:var(--charcoal-mid);">2FA Status</span>
            <strong style="color:<?php echo $user['two_factor_enabled'] ? 'var(--success)' : 'var(--warning)'; ?>">
              <?php echo $user['two_factor_enabled'] ? '✓ Enabled' : '⚠ Disabled'; ?>
            </strong>
          </div>
        </div>
      </div>

      <!-- Enable 2FA -->
      <div style="background:white;border-radius:var(--radius-lg);border:1.5px solid var(--border);padding:24px;box-shadow:var(--shadow);">
        <h3 style="font-size:16px;font-weight:500;margin-bottom:8px;">🔑 Two-Factor Authentication</h3>
        <p style="font-size:13px;color:var(--charcoal-light);margin-bottom:16px;">
          Add an extra layer of security. A 6-digit code will be required on every login.
        </p>
        <?php
          $is_2fa = $user['two_factor_enabled'] ?? 0;
          $toggle_action = $is_2fa ? 0 : 1;
          $toggle_label  = $is_2fa ? '🔓 Disable 2FA' : '🔐 Enable 2FA';
          $toggle_class  = $is_2fa ? 'btn-outline' : 'btn-primary';
        ?>
        <form method="POST">
          <?php echo csrfField(); ?>
          <input type="hidden" name="two_factor_enabled" value="<?php echo $toggle_action; ?>">
          <button type="submit" name="toggle_2fa" class="<?php echo $toggle_class; ?>" style="width:100%;">
            <?php echo $toggle_label; ?>
          </button>
        </form>
      </div>

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

<div class="toast" id="toast"></div>
<script src="js/main.js"></script>
<script>
// Password strength
const newPass = document.getElementById('new_password');
if (newPass) newPass.addEventListener('input', function() { checkPasswordStrength(this.value); });

// Photo preview before upload
function previewPhoto(input) {
  const preview     = document.getElementById('photoPreview');
  const previewImg  = document.getElementById('previewImg');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImg.src  = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>