<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول لتقييم المقال.']);
    exit;
}
$user_id = intval($_SESSION['user_id']);
$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
// إذا كان الطلب فقط لجلب تقييم المستخدم الحالي
if (isset($_POST['get_user']) && $_POST['get_user'] == '1' && $article_id > 0) {
    $stmt = $pdo->prepare('SELECT rating FROM article_ratings WHERE article_id = ? AND user_id = ?');
    $stmt->execute([$article_id, $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'user_rating' => $row ? intval($row['rating']) : 0
    ]);
    exit;
}
if ($article_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة.']);
    exit;
}
// تحقق إذا كان المستخدم قام بالتقييم مسبقاً
$stmt = $pdo->prepare('SELECT id FROM article_ratings WHERE article_id = ? AND user_id = ?');
$stmt->execute([$article_id, $user_id]);
if ($stmt->fetch()) {
    // تحديث التقييم
    $stmt = $pdo->prepare('UPDATE article_ratings SET rating = ?, created_at = NOW() WHERE article_id = ? AND user_id = ?');
    $stmt->execute([$rating, $article_id, $user_id]);
} else {
    // إضافة تقييم جديد
    $stmt = $pdo->prepare('INSERT INTO article_ratings (article_id, user_id, rating) VALUES (?, ?, ?)');
    $stmt->execute([$article_id, $user_id, $rating]);
}
// حساب المتوسط وعدد التقييمات
$stmt = $pdo->prepare('SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM article_ratings WHERE article_id = ?');
$stmt->execute([$article_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'success' => true,
    'avg_rating' => round($row['avg_rating'], 2),
    'total_ratings' => intval($row['total_ratings'])
]);
