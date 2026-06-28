<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7
// File: includes/csrf.php
// CSRF (Cross-Site Request Forgery) Protection
// ============================================================

// Generate CSRF token
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Output hidden CSRF field for forms
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Validate CSRF on POST requests — die if invalid
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($token)) {
            http_response_code(403);
            die("
            <div style='font-family:sans-serif;padding:40px;text-align:center;'>
                <h2 style='color:#C85B7A;'>⛔ Security Error</h2>
                <p>Invalid or expired security token. Please go back and try again.</p>
                <a href='javascript:history.back()' style='color:#C85B7A;'>← Go Back</a>
            </div>
            ");
        }
        // Regenerate token after use
        unset($_SESSION['csrf_token']);
    }
}
?>