<?php
// ============================================================
// ESSIZ BEAUTY HUB — Week 7
// File: includes/mailer.php
// PHPMailer Email Configuration
// ============================================================

// PHPMailer via CDN/manual include
// Install via Composer: composer require phpmailer/phpmailer
// OR download from: https://github.com/PHPMailer/PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// ============================================================
// EMAIL CONFIGURATION
// Update these with your Gmail credentials
// ============================================================
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'muleiesther84@gmail.com');   // ← Your Gmail
define('MAIL_PASSWORD', 'uuwf elhr icnn ghme');       // ← Gmail App Password
define('MAIL_FROM',     'muleiesther84@gmail.com');
define('MAIL_FROM_NAME','Essiz Beauty Hub');
define('SITE_URL', 'https://unaired-myself-mayflower.ngrok-free.dev/EssizBeautyHub/Week7');

// ============================================================
// SEND EMAIL FUNCTION
// ============================================================
function sendEmail($to_email, $to_name, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        // Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo(MAIL_FROM, MAIL_FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// ============================================================
// SEND PASSWORD RESET EMAIL
// ============================================================
function sendPasswordResetEmail($to_email, $to_name, $token) {
    $reset_link = SITE_URL . '/reset_password.php?token=' . $token;

    $subject = 'Password Reset — Essiz Beauty Hub';

    $body = "
    <div style='font-family:DM Sans,sans-serif;max-width:600px;margin:0 auto;'>
        <div style='background:linear-gradient(135deg,#C85B7A,#9B84CC);padding:32px;text-align:center;border-radius:12px 12px 0 0;'>
            <h1 style='color:white;font-family:Georgia,serif;font-weight:300;margin:0;'>✦ Essiz Beauty Hub</h1>
        </div>
        <div style='background:white;padding:40px;border:1px solid #EDD8E4;'>
            <h2 style='color:#2C2C2C;font-weight:400;'>Password Reset Request</h2>
            <p style='color:#555;line-height:1.6;'>Hi <strong>{$to_name}</strong>,</p>
            <p style='color:#555;line-height:1.6;'>We received a request to reset your password for your Essiz Beauty Hub account.</p>
            <p style='color:#555;line-height:1.6;'>Click the button below to reset your password. This link expires in <strong>1 hour</strong>.</p>
            <div style='text-align:center;margin:32px 0;'>
                <a href='{$reset_link}'
                   style='background:#C85B7A;color:white;padding:14px 32px;border-radius:12px;text-decoration:none;font-weight:500;display:inline-block;'>
                    Reset My Password
                </a>
            </div>
            <p style='color:#888;font-size:13px;'>If you did not request this, please ignore this email. Your password will remain unchanged.</p>
            <p style='color:#888;font-size:12px;'>For security, this link will expire in 1 hour.</p>
        </div>
        <div style='background:#F2E8DC;padding:16px;text-align:center;border-radius:0 0 12px 12px;'>
            <p style='color:#888;font-size:12px;margin:0;'>© " . date('Y') . " Essiz Beauty Hub — Intelligent Beauty. Campus Confidence.</p>
        </div>
    </div>
    ";

    return sendEmail($to_email, $to_name, $subject, $body);
}

// ============================================================
// SEND 2FA CODE EMAIL
// ============================================================
function send2FAEmail($to_email, $to_name, $code) {
    $subject = 'Your Login Verification Code — Essiz Beauty Hub';

    $body = "
    <div style='font-family:DM Sans,sans-serif;max-width:600px;margin:0 auto;'>
        <div style='background:linear-gradient(135deg,#C85B7A,#9B84CC);padding:32px;text-align:center;border-radius:12px 12px 0 0;'>
            <h1 style='color:white;font-family:Georgia,serif;font-weight:300;margin:0;'>✦ Essiz Beauty Hub</h1>
        </div>
        <div style='background:white;padding:40px;border:1px solid #EDD8E4;text-align:center;'>
            <h2 style='color:#2C2C2C;font-weight:400;'>Your Verification Code</h2>
            <p style='color:#555;'>Hi <strong>{$to_name}</strong>, use this code to complete your login:</p>
            <div style='background:#FDF0F5;border:2px solid #C85B7A;border-radius:12px;padding:24px;margin:24px 0;'>
                <h1 style='color:#C85B7A;font-size:48px;letter-spacing:12px;margin:0;'>{$code}</h1>
            </div>
            <p style='color:#888;font-size:13px;'>This code expires in <strong>10 minutes</strong>.</p>
            <p style='color:#888;font-size:13px;'>If you did not attempt to login, please change your password immediately.</p>
        </div>
        <div style='background:#F2E8DC;padding:16px;text-align:center;border-radius:0 0 12px 12px;'>
            <p style='color:#888;font-size:12px;margin:0;'>© " . date('Y') . " Essiz Beauty Hub</p>
        </div>
    </div>
    ";

    return sendEmail($to_email, $to_name, $subject, $body);
}
?>