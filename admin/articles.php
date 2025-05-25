<?php
session_start();
require_once '../db.php';
$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المقالات</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
<main class="main-content">
    <h1 class="dashboard-title animate__animated animate__fadeInDown">إدارة المقالات</h1>
    <button class="add-article-btn"><i class="fa fa-plus"></i> إضافة مقال جديد</button>
    <table class="data-table">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>المحتوى المختصر</th>
                <th>تاريخ النشر</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody id="articlesTable">
            <?php foreach($articles as $article): ?>
            <tr>
                <td><?= htmlspecialchars($article['title']) ?></td>
                <td><?= htmlspecialchars(mb_substr($article['content'],0,50)).'...' ?></td>
                <td><?= htmlspecialchars($article['created_at']) ?></td>
                <td>
                    <button class="action-btn view-btn"><i class="fa fa-eye"></i></button>
                    <button class="action-btn edit-btn"><i class="fa fa-edit"></i></button>
                    <button class="action-btn delete-btn"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- نموذج إضافة مقال جديد (يظهر عند الضغط على الزر) -->
    <div class="add-article-modal" style="display:none;">
        <form action="articles.php" method="post">
            <input type="text" name="title" placeholder="عنوان المقال" required>
            <textarea name="content" rows="4" placeholder="محتوى المقال" required></textarea>
            <button type="submit" name="add_article">إضافة</button>
            <button type="button" class="close-modal">إلغاء</button>
        </form>
    </div>
</main>
<script src="js/articles.js"></script>
</body>
</html>
