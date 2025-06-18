<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال بريد إلكتروني صحيح.']);
    exit;
}
// تحقق إذا كان البريد مسجل مسبقاً
$stmt = $pdo->prepare('SELECT COUNT(*) FROM subscriptions WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'أنت مشترك بالفعل بهذا البريد.']);
    exit;
}
// أضف البريد
$stmt = $pdo->prepare('INSERT INTO subscriptions (email, subscribed_at) VALUES (?, NOW())');
if ($stmt->execute([$email])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء الاشتراك.']);
}
