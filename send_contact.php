<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
if (!$name || !$email || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'يرجى تعبئة جميع الحقول بشكل صحيح.']);
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mmshsh05@gmail.com'; 
    $mail->Password = 'glwrlhuryykrlbwh'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($mail->Username, 'موقع مقالات');
    $mail->addAddress('mmshsh05@gmail.com');
    $mail->addReplyTo($email, $name);
    $mail->Subject = 'رسالة جديدة من مقالات';
    $mail->isHTML(true);
    $body = '<div style="background:var(--color-slate-900,#0F172A);padding:32px 0;min-height:100vh;font-family:Inter,system-ui,-apple-system,sans-serif;">
        <div style="max-width:480px;margin:0 auto;background:var(--color-slate-800,#1E293B);border-radius:12px;box-shadow:0 2px 12px #0002;padding:32px 24px;">
            <h2 style="color:var(--color-primary,#3B82F6);text-align:center;margin-bottom:24px;font-family:Inter,system-ui,-apple-system,sans-serif;">رسالة جديدة من نموذج التواصل</h2>
            <table style="width:100%;border-collapse:collapse;font-size:16px;direction:rtl;">
                <tr>
                    <td style="color:var(--color-slate-400,#94A3B8);padding:8px 0;width:90px;font-family:Inter,system-ui,-apple-system,sans-serif;">الاسم:</td>
                    <td style="color:var(--color-white,#fff);padding:8px 0;font-weight:bold;font-family:Inter,system-ui,-apple-system,sans-serif;">' . htmlspecialchars($name) . '</td>
                </tr>
                <tr>
                    <td style="color:var(--color-slate-400,#94A3B8);padding:8px 0;font-family:Inter,system-ui,-apple-system,sans-serif;">البريد:</td>
                    <td style="color:var(--color-white,#fff);padding:8px 0;font-weight:bold;font-family:Inter,system-ui,-apple-system,sans-serif;">' . htmlspecialchars($email) . '</td>
                </tr>
                <tr>
                    <td style="color:var(--color-slate-400,#94A3B8);padding:8px 0;vertical-align:top;font-family:Inter,system-ui,-apple-system,sans-serif;">الرسالة:</td>
                    <td style="color:var(--color-slate-100,#F1F5F9);padding:8px 0;white-space:pre-line;font-family:Inter,system-ui,-apple-system,sans-serif;">' . nl2br(htmlspecialchars($message)) . '</td>
                </tr>
            </table>
            <div style="text-align:center;margin-top:32px;color:var(--color-slate-500,#64748B);font-size:13px;font-family:Inter,system-ui,-apple-system,sans-serif;">موقع مقالات &copy; ' . date('Y') . '</div>
        </div>
    </div>';
    $mail->Body = $body;
    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'تعذر إرسال الرسالة: ' . $mail->ErrorInfo]);
}
