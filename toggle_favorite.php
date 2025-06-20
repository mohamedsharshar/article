<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول.']);
    exit;
}
$user_id = intval($_SESSION['user_id']);
$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
if ($article_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة.']);
    exit;
}
// تحقق هل المقال بالفعل في المفضلة
$stmt = $pdo->prepare('SELECT id FROM favorite_articles WHERE user_id = ? AND article_id = ?');
$stmt->execute([$user_id, $article_id]);
if ($row = $stmt->fetch()) {
    // إزالة من المفضلة
    $pdo->prepare('DELETE FROM favorite_articles WHERE id = ?')->execute([$row['id']]);
    echo json_encode(['success' => true, 'favorited' => false]);
} else {
    // إضافة للمفضلة
    $pdo->prepare('INSERT INTO favorite_articles (user_id, article_id) VALUES (?, ?)')->execute([$user_id, $article_id]);
    echo json_encode(['success' => true, 'favorited' => true]);
}
