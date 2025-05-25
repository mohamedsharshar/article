<?php
session_start();
require_once '../db.php';
$comments = $pdo->query("SELECT c.*, a.title, IF(is_admin=1,'مشرف','مستخدم') as author_type FROM comments c JOIN articles a ON c.article_id = a.id ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التعليقات</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة التعليقات</h1>
    <table class="data-table">
        <thead>
            <tr>
                <th>التعليق</th>
                <th>المقال</th>
                <th>الكاتب</th>
                <th>تاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="commentsTable">
            <?php foreach($comments as $comment): ?>
            <tr>
                <td><?= htmlspecialchars($comment['content']) ?></td>
                <td><?= htmlspecialchars($comment['title']) ?></td>
                <td><?= $comment['author_type'] ?></td>
                <td><?= htmlspecialchars($comment['created_at']) ?></td>
                <td>
                    <button class="action-btn delete-btn"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<script src="js/comments.js"></script>
</body>
</html>
