<?php
require_once '../db.php';
header('Content-Type: application/json; charset=utf-8');

// جلب عدد التعليقات لكل شهر آخر 12 شهر
$labels = [];
$data = [];
$months = [];
$now = new DateTime();
for ($i = 11; $i >= 0; $i--) {
    $month = clone $now;
    $month->modify("-$i months");
    $label = $month->format('Y-m');
    $labels[] = $label;
    $months[$label] = 0;
}

$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt FROM comments WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY ym";
$stmt = $pdo->query($sql);
foreach ($stmt as $row) {
    if (isset($months[$row['ym']])) {
        $months[$row['ym']] = (int)$row['cnt'];
    }
}
foreach ($labels as $l) {
    $data[] = $months[$l];
}
echo json_encode(['labels' => $labels, 'data' => $data], JSON_UNESCAPED_UNICODE);
