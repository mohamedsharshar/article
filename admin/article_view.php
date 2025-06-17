<?php
session_start();
require_once '../db.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<h2 style="text-align:center;margin-top:4rem;">المقال غير موجود</h2>';
    exit;
}
$id = intval($_GET['id']);
// جلب المقال مع اسم الناشر الحقيقي
$stmt = $pdo->prepare("SELECT articles.*, categories.name AS category_name, COALESCE(admins.adminname, users.username) AS author_name
FROM articles
LEFT JOIN categories ON articles.category_id = categories.id
LEFT JOIN admins ON articles.admin_id = admins.id
LEFT JOIN users ON articles.user_id = users.id
WHERE articles.id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) {
    echo '<h2 style="text-align:center;margin-top:4rem;">المقال غير موجود</h2>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المقال (لوحة التحكم)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background: #f7f8fa;
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .admin-article-container {
            max-width: 800px;
            margin: 3.5rem auto 2.5rem auto;
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 4px 24px #4262ed14;
            padding: 2.5rem 2rem 2.5rem 2rem;
            position: relative;
        }
        .admin-article-image {
            width: 100%;
            max-height: 340px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px #0002;
            background: #f1f5f9;
            display: block;
        }
        .admin-article-title {
            font-family: 'Cairo', 'Merriweather', serif;
            font-size: 2.1rem;
            font-weight: bold;
            margin-bottom: 0.7rem;
            color: #2563EB;
            letter-spacing: 0.01em;
            line-height: 1.3;
        }
        .admin-article-meta {
            color: #64748b;
            font-size: 1.05rem;
            margin-bottom: 1.2rem;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .admin-article-meta .category-tag {
            margin-right: 0.5rem;
            background: #3B82F6;
            color: #fff;
            padding: 0.35em 1.2em;
            border-radius: 999px;
            font-size: 1em;
            box-shadow: 0 2px 8px #3b82f633;
            z-index: 2;
            font-weight: bold;
            letter-spacing: 0.01em;
        }
        .admin-article-content {
            font-size: 1.15rem;
            line-height: 2.1;
            color: #222;
            margin-bottom: 2rem;
            background: #f8fafc;
            padding: 1.5rem 1.2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 4px #0001;
            word-break: break-word;
            white-space: pre-line;
        }
        .admin-article-back {
            display: inline-block;
            margin-bottom: 1.5rem;
            color: #2563EB;
            font-weight: bold;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.2s;
        }
        .admin-article-back:hover {
            color: #1E293B;
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .admin-article-container {
                padding: 1.2rem 0.5rem;
                margin: 2.2rem 0.2rem 1.5rem 0.2rem;
            }
            .admin-article-title {
                font-size: 1.3rem;
            }
            .admin-article-content {
                font-size: 1.01rem;
                padding: 1rem 0.5rem;
            }
        }
        @media (max-width: 600px) {
            .admin-article-container {
                margin: 1.2rem 0.1rem 1rem 0.1rem;
                padding: 0.7rem 0.1rem;
            }
            .admin-article-title {
                font-size: 1.1rem;
            }
            .admin-article-content {
                font-size: 0.98rem;
                padding: 0.7rem 0.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-article-container">
        <a href="manage_articles.php" class="admin-article-back"><i class="fa fa-arrow-right"></i> العودة للإدارة</a>
        <?php
            $imgSrc = $article['image'] ? '../uploads/articles/' . htmlspecialchars($article['image']) : 'https://source.unsplash.com/800x350/?arabic,writing,' . urlencode($article['category'] ?? 'article');
        ?>
        <div style="position:relative;">
            <img src="<?= $imgSrc ?>" alt="صورة المقال" class="admin-article-image">
            <?php if (!empty($article['category'])): ?>
                <span class="category-tag" style="position:absolute;top:18px;right:18px;"> <?= htmlspecialchars($article['category']) ?> </span>
            <?php endif; ?>
        </div>
        <h1 class="admin-article-title"> <?= htmlspecialchars($article['title']) ?> </h1>
        <div class="admin-article-meta">
            <span><i class="fa fa-calendar-alt"></i> <?= htmlspecialchars(substr($article['created_at'],0,10)) ?></span>
            <span><i class="fa fa-user"></i> <?= htmlspecialchars($article['author_name'] ?: 'مجهول') ?></span>
        </div>
        <div class="admin-article-content">
            <?= nl2br(htmlspecialchars($article['content'])) ?>
        </div>
    </div>
</body>
</html>
