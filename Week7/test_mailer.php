<?php
require_once 'includes/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug  = 2; // Show full debug output
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'muleiesther84@gmail.com';
    $mail->Password   = 'uuwf elhr icnn ghme';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('muleiesther84@gmail.com', 'Essiz Beauty Hub');
    $mail->addAddress('muleiesther84@gmail.com');
    $mail->Subject = 'Test Email';
    $mail->Body    = 'Hello from Essiz Beauty Hub!';

    $mail->send();
    echo '<p style="color:green;">✅ Email sent successfully!</p>';

} catch (Exception $e) {
    echo '<p style="color:red;">❌ Error: ' . $mail->ErrorInfo . '</p>';
}
?>