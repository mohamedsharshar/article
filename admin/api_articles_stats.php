<?php
require_once '../db.php';
header('Content-Type: application/json');

// جلب عدد المقالات لكل شهر في آخر 12 شهر
$data = [];
$months = [];
$now = new DateTime();
for ($i = 11; $i >= 0; $i--) {
    $month = clone $now;
    $month->modify("-$i month");
    $label = $month->format('Y-m');
    $months[] = $label;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$label]);
    $data[] = (int)$stmt->fetchColumn();
}
echo json_encode([
    'labels' => $months,
    'data' => $data
]);
