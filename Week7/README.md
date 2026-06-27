# Essiz Beauty Hub — Week 7
## BIT3208 Advanced Web Design and Development

---

## Week 7 Theme
**Advanced Security — CSRF, 2FA, XSS Prevention, Secure Password Reset**

---

## New Security Features

### ✅ 1. CSRF Protection (includes/csrf.php)
- Every form has a hidden CSRF token
- Token generated using `bin2hex(random_bytes(32))`
- Verified on every POST request
- Token regenerated after each use
- Returns 403 error if token is invalid

### ✅ 2. XSS Prevention (includes/security.php)
- `xssClean()` function sanitizes all user input
- `htmlspecialchars()` on all output
- `strip_tags()` removes HTML from input
- Security headers set on every page:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-XSS-Protection: 1; mode=block`
  - `X-Content-Type-Options: nosniff`

### ✅ 3. SQL Injection Prevention
- ALL queries use prepared statements with `mysqli_prepare()`
- `mysqli_stmt_bind_param()` for all user input
- Never concatenating user input into SQL strings

### ✅ 4. Session Security
- `session_regenerate_id(true)` on every login
- Session timeout after 30 minutes inactivity
- `session.cookie_httponly` enabled
- `session.use_strict_mode` enabled

### ✅ 5. Two-Factor Authentication (two_factor.php)
- 6-digit code generated after login
- Code stored in database with 10-minute expiry
- Auto-submit form when 6 digits entered
- Countdown timer shows remaining time
- Demo mode shows code on screen
- Production mode sends via email

### ✅ 6. Secure Password Reset (forgot_password.php + reset_password.php)
- Unique cryptographic token: `bin2hex(random_bytes(32))`
- Token stored in `password_reset_tokens` table
- Expires after 1 hour
- Single use — marked as used after reset
- Demo mode shows reset link directly
- Production mode sends via PHPMailer

### ✅ 7. Brute Force Protection (carried from Week 6)
- 3 failed attempts → 15 minute lockout
- Remaining attempts shown to user
- All attempts logged in `login_attempts` table

### ✅ 8. PHPMailer Setup (includes/mailer.php)
- Gmail SMTP configuration
- HTML email templates for reset and 2FA
- Branded Essiz Beauty Hub email design
- Graceful fallback to demo mode if not configured

---

## Security Threats Addressed

| Threat | Solution | Status |
|---|---|---|
| CSRF | Token in every form | ✅ Done |
| XSS | xssClean() + htmlspecialchars() | ✅ Done |
| SQL Injection | Prepared statements | ✅ Done |
| Brute Force | Login attempt limiting | ✅ Done |
| Session Fixation | session_regenerate_id() | ✅ Done |
| Session Hijacking | HTTPOnly cookies | ✅ Done |
| Weak Passwords | bcrypt hashing + strength meter | ✅ Done |
| Account Takeover | 2FA verification | ✅ Done |
| Password Reset | Secure token system | ✅ Done |

---

## New Files This Week
```
Week7/
├── login.php              ← CSRF + 2FA + session regeneration
├── two_factor.php         ← NEW: 2FA verification page
├── forgot_password.php    ← NEW: Token-based reset
├── reset_password.php     ← NEW: Reset via token link
├── includes/
│   ├── csrf.php           ← NEW: CSRF protection
│   ├── security.php       ← NEW: All security functions
│   └── mailer.php         ← NEW: PHPMailer setup
├── database/
│   └── essizdb_w7.sql     ← Added: password_reset_tokens table
└── README.md
```

---

## Demo Credentials
| Role | Email | Password |
|---|---|---|
| Admin | admin@essizbeautyhub.com | password |
| Customer | janewanjiru254@gmail.com | password |

---

## GitHub Commit
**`"Advanced security — CSRF, XSS, 2FA, secure password reset, session security — Week 7"`**