<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT a.* FROM articles a INNER JOIN favorite_articles f ON a.id = f.article_id WHERE f.user_id = ? AND a.deleted = 0 AND a.content IS NOT NULL AND a.content != '' ORDER BY f.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$favorites = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مقالاتي المفضلة</title>
    <link rel="stylesheet" href="css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="favorites-page">
    <div class="container">
        <h2>مقالاتي المفضلة</h2>
        <?php if (empty($favorites)): ?>
            <div class="empty-favorites">لا توجد مقالات مفضلة بعد.</div>
        <?php else: ?>
            <div class="articles-list">
                <?php foreach ($favorites as $article): ?>
                    <div class="article-card">
                        <a href="article.php?id=<?= $article['id'] ?>">
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?= htmlspecialchars($article['image']) ?>" alt="صورة المقال" class="article-thumb">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($article['title']) ?></h3>
                        </a>
                        <p class="article-excerpt">
                            <?= mb_substr(strip_tags($article['content']), 0, 120) . (mb_strlen(strip_tags($article['content'])) > 120 ? '...' : '') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-outline">العودة للرئيسية</a>
    </div>
    <script>
    // دعم الدارك مود
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
    </script>
</body>
</html>
