<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 6
// File: includes/functions.php
// Reusable helper functions
// ============================================================

// ============================================================
// Get cart count for logged in user
// ============================================================
function getCartCount($conn, $user_id) {
    $result = mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) as total FROM cart WHERE user_id=$user_id");
    return mysqli_fetch_assoc($result)['total'];
}

// ============================================================
// Get wishlist count for logged in user
// ============================================================
function getWishlistCount($conn, $user_id) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_id=$user_id");
    return mysqli_fetch_assoc($result)['total'];
}

// ============================================================
// Check if product is in wishlist
// ============================================================
function isInWishlist($conn, $user_id, $product_id) {
    $stmt = mysqli_prepare($conn, "SELECT wishlist_id FROM wishlist WHERE user_id=? AND product_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

// ============================================================
// Get product icon by category
// ============================================================
function getProductIcon($category) {
    $icons = [
        'Skincare'    => '🧴',
        'Makeup'      => '💄',
        'Haircare'    => '💆',
        'Perfumes'    => '🌸',
        'Accessories' => '🪞'
    ];
    return $icons[$category] ?? '✨';
}

// ============================================================
// Generate star rating HTML
// ============================================================
function getStars($rating) {
    return str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
}

// ============================================================
// Sanitize input
// ============================================================
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

// ============================================================
// Check login attempts — brute force protection
// ============================================================
function checkLoginAttempts($conn, $email) {
    $stmt = mysqli_prepare($conn, "SELECT login_attempts, locked_until FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$result) return ['locked' => false, 'attempts' => 0];

    // Check if account is locked
    if ($result['locked_until'] && strtotime($result['locked_until']) > time()) {
        $minutes = ceil((strtotime($result['locked_until']) - time()) / 60);
        return ['locked' => true, 'minutes' => $minutes, 'attempts' => $result['login_attempts']];
    }

    return ['locked' => false, 'attempts' => $result['login_attempts']];
}

// ============================================================
// Record failed login attempt
// ============================================================
function recordFailedAttempt($conn, $email) {
    $ip = $_SERVER['REMOTE_ADDR'];

    // Log attempt
    $stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (email, ip_address) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
    mysqli_stmt_execute($stmt);

    // Increment user attempts
    $stmt2 = mysqli_prepare($conn, "UPDATE users SET login_attempts = login_attempts + 1 WHERE email=?");
    mysqli_stmt_bind_param($stmt2, "s", $email);
    mysqli_stmt_execute($stmt2);

    // Check if should lock (after 3 attempts)
    $check = mysqli_prepare($conn, "SELECT login_attempts FROM users WHERE email=?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

    if ($user && $user['login_attempts'] >= 3) {
        // Lock for 15 minutes
        $lock_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $lock = mysqli_prepare($conn, "UPDATE users SET locked_until=? WHERE email=?");
        mysqli_stmt_bind_param($lock, "ss", $lock_until, $email);
        mysqli_stmt_execute($lock);
    }
}

// ============================================================
// Reset login attempts on successful login
// ============================================================
function resetLoginAttempts($conn, $email) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET login_attempts=0, locked_until=NULL WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
}

// ============================================================
// Format price in KES
// ============================================================
function formatPrice($price) {
    return 'KES ' . number_format($price);
}

// ============================================================
// Time ago function
// ============================================================
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60)     return 'Just now';
    if ($diff < 3600)   return floor($diff/60) . ' minutes ago';
    if ($diff < 86400)  return floor($diff/3600) . ' hours ago';
    if ($diff < 604800) return floor($diff/86400) . ' days ago';
    return date('d M Y', $time);
}
?>