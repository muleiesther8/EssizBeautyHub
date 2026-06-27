<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7
// File: includes/security.php
// Advanced Security Functions
// ============================================================

// ============================================================
// 1. XSS PREVENTION — sanitize output
// ============================================================
function xssClean($data) {
    if (is_array($data)) {
        return array_map('xssClean', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// ============================================================
// 2. SQL INJECTION PREVENTION — already using prepared statements
// This function adds extra layer for direct queries
// ============================================================
function sqlClean($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// ============================================================
// 3. SESSION SECURITY — regenerate session ID on login
// Prevents session fixation attacks
// ============================================================
function secureSessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        // Secure session settings
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}

function regenerateSession() {
    session_regenerate_id(true);
}

// ============================================================
// 4. SESSION TIMEOUT — auto logout after inactivity
// ============================================================
function checkSessionTimeout($timeout_minutes = 30) {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > ($timeout_minutes * 60)) {
            session_unset();
            session_destroy();
            header('Location: login.php?msg=Session expired. Please login again.');
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

// ============================================================
// 5. SECURITY HEADERS — prevent clickjacking, XSS etc
// ============================================================
function setSecurityHeaders() {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ============================================================
// 6. GENERATE 2FA CODE
// ============================================================
function generate2FACode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// ============================================================
// 7. STORE 2FA CODE IN DATABASE
// ============================================================
function store2FACode($conn, $user_id, $code) {
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt   = mysqli_prepare($conn,
        "UPDATE users SET two_factor_code=?, two_factor_expiry=? WHERE user_id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssi", $code, $expiry, $user_id);
    return mysqli_stmt_execute($stmt);
}

// ============================================================
// 8. VERIFY 2FA CODE
// ============================================================
function verify2FACode($conn, $user_id, $code) {
    $stmt = mysqli_prepare($conn,
        "SELECT two_factor_code, two_factor_expiry FROM users WHERE user_id=?"
    );
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$result) return false;
    if ($result['two_factor_code'] !== $code) return false;
    if (strtotime($result['two_factor_expiry']) < time()) return false;

    // Clear code after use
    mysqli_query($conn, "UPDATE users SET two_factor_code=NULL, two_factor_expiry=NULL WHERE user_id=$user_id");
    return true;
}

// ============================================================
// 9. GENERATE PASSWORD RESET TOKEN
// ============================================================
function generateResetToken($conn, $user_id) {
    // Delete old tokens
    $del = mysqli_prepare($conn, "DELETE FROM password_reset_tokens WHERE user_id=?");
    mysqli_stmt_bind_param($del, "i", $user_id);
    mysqli_stmt_execute($del);

    // Generate new token
    $token     = bin2hex(random_bytes(32));
    $expires = gmdate('Y-m-d H:i:s', time() + 3600);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?,?,?)"
    );
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $token, $expires);
    mysqli_stmt_execute($stmt);

    return $token;
}

// ============================================================
// 10. VALIDATE RESET TOKEN
// ============================================================
function validateResetToken($conn, $token) {
    $stmt = mysqli_prepare($conn,
        "SELECT prt.*, u.email, u.full_name
         FROM password_reset_tokens prt
         JOIN users u ON prt.user_id = u.user_id
         WHERE prt.token=? AND prt.used=0 AND prt.expires_at > UTC_TIMESTAMP()"
    );
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// ============================================================
// 11. MARK TOKEN AS USED
// ============================================================
function markTokenUsed($conn, $token) {
    $stmt = mysqli_prepare($conn, "UPDATE password_reset_tokens SET used=1 WHERE token=?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
}

// ============================================================
// 12. IP ADDRESS LOGGER
// ============================================================
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}
?>