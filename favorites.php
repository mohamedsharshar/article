<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT a.* FROM articles a INNER JOIN favorite_articles f ON a.id = f.article_id WHERE f.user_id = ? AND a.content IS NOT NULL AND a.content != '' ORDER BY f.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مقالاتي المفضلة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body.favorites-page {
        background: var(--color-slate-50, #f8fafc);
        min-height: 100vh;
        font-family: 'Cairo', Tahoma, Arial, sans-serif;
        color: var(--color-slate-700, #334155);
        margin: 0;
    }
    .favorites-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2.5rem 0 1.5rem 0;
        max-width: 900px;
        margin: 0 auto;
    }
    .favorites-header h2 {
        font-size: 2.2rem;
        font-family: 'Merriweather', serif;
        font-weight: bold;
        color: var(--color-primary, #3B82F6);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.7em;
    }
    .favorites-header .btn {
        font-size: 1rem;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%);
        color: #fff;
        border: none;
        font-weight: bold;
        box-shadow: 0 2px 8px #3a86ff22;
        transition: background 0.2s, box-shadow 0.2s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5em;
    }
    .favorites-header .btn:hover {
        background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
        box-shadow: 0 4px 16px #4262ed22;
    }
    .favorites-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 2rem;
        max-width: 1000px;
        margin:   auto;
        padding: 0 1rem;
    }
    .favorite-card {
        background: #fff;
        border-radius: 1.2rem;
        box-shadow: 0 4px 24px #33415522;
        padding: 1.5rem 1.2rem 1.2rem 1.2rem;
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 320px;
        transition: box-shadow 0.2s, background 0.2s;
    }
    .favorite-card:hover {
        box-shadow: 0 8px 32px #3B82F655;
        background: #f1f5f9;
    }
    .favorite-card .fav-heart {
        position: absolute;
        top: 18px;
        left: 18px;
        font-size: 1.7rem;
        color: #e63946;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 8px #e6394622;
        padding: 0.2em 0.3em;
        z-index: 2;
        border: none;
        cursor: default;
    }
    .favorite-card .favorite-thumb {
        width: 100%;
        max-height: 180px;
        object-fit: cover;
        border-radius: 0.8rem;
        margin-bottom: 1.1rem;
        box-shadow: 0 2px 12px #33415522;
    }
    .favorite-card h3 {
        font-size: 1.25rem;
        font-family: 'Merriweather', serif;
        font-weight: bold;
        margin: 0 0 0.7rem 0;
        color: var(--color-slate-800, #1E293B);
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .favorite-card .article-excerpt {
        font-size: 1.05rem;
        color: var(--color-slate-600, #475569);
        margin-bottom: 1.2rem;
        margin-top: 0;
        flex: 1;
        line-height: 1.8;
        word-break: break-word;
    }
    .favorite-card .read-more {
        margin-top: auto;
        align-self: flex-end;
        background: var(--color-primary, #3B82F6);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5em 1.2em;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        box-shadow: 0 2px 8px #3B82F622;
        transition: background 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5em;
    }
    .favorite-card .read-more:hover {
        background: #4262ed;
        box-shadow: 0 4px 16px #4262ed22;
    }
    .empty-favorites {
        text-align: center;
        color: #888;
        font-size: 1.2rem;
        margin: 3rem 0 2rem 0;
    }
    @media (max-width: 700px) {
        .favorites-header h2 { font-size: 1.3rem; }
        .favorites-list { gap: 1.2rem; }
        .favorite-card { padding: 1rem 0.5rem; min-height: 220px; }
    }
    html[data-theme="dark"] body.favorites-page {
        background: #0F172A !important;
        color: #fff !important;
    }
    html[data-theme="dark"] .favorite-card {
        background: #1E293B !important;
        color: #fff !important;
        box-shadow: 0 4px 24px #000a;
    }
    html[data-theme="dark"] .favorite-card:hover {
        background: #334155 !important;
    }
    html[data-theme="dark"] .favorite-card h3 {
        color: #fff !important;
    }
    html[data-theme="dark"] .favorite-card .article-excerpt {
        color: #CBD5E1 !important;
    }
    html[data-theme="dark"] .favorites-header h2 {
        color: #60A5FA !important;
    }
    html[data-theme="dark"] .favorites-header .btn {
        background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%) !important;
        color: #fff !important;
    }
    html[data-theme="dark"] .favorite-card .fav-heart {
        background: #1E293B !important;
        color: #e63946 !important;
        box-shadow: 0 2px 8px #e6394622;
    }
    html[data-theme="dark"] .favorite-card .favorite-thumb {
        box-shadow: 0 2px 12px #000a;
    }
    html[data-theme="dark"] .favorite-card .read-more {
        background: #4262ed !important;
        color: #fff !important;
    }
    html[data-theme="dark"] .favorite-card .read-more:hover {
        background: #3a86ff !important;
    }
    </style>
</head>
<body class="favorites-page">
    <div class="favorites-header">
        <h2><i class="fa fa-heart"></i> مقالتي المفضلة</h2>
        <a href="index.php" class="btn"><i class="fa fa-home"></i> العودة للرئيسية</a>
    </div>
    <div class="favorites-list">
        <?php if (empty($favorites)): ?>
            <div class="empty-favorites">لا توجد مقالات مفضلة بعد.</div>
        <?php else: ?>
            <?php foreach ($favorites as $article): ?>
                <div class="favorite-card">
                    <span class="fav-heart">♥</span>
                    <?php if (!empty($article['image'])): ?>
                        <img src="<?= (strpos($article['image'], 'uploads/') === 0 ? '' : 'uploads/articles/') . htmlspecialchars($article['image']) ?>" alt="صورة المقال" class="favorite-thumb">
                    <?php else: ?>
                        <img src="https://source.unsplash.com/400x200/?arabic,writing,article" alt="صورة المقال" class="favorite-thumb">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($article['title']) ?></h3>
                    <p class="article-excerpt">
                        <?= mb_substr(strip_tags($article['content']), 0, 120) . (mb_strlen(strip_tags($article['content'])) > 120 ? '...' : '') ?>
                    </p>
                    <a href="article.php?id=<?= $article['id'] ?>" class="read-more"><i class="fa fa-arrow-left"></i> اقرأ المزيد</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
    // تفعيل الدارك مود تلقائياً حسب تفضيل المستخدم
    (function() {
      let darkPref = localStorage.getItem('darkMode');
      if (darkPref === null) {
        if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          document.documentElement.setAttribute('data-theme', 'dark');
        }
      } else if (darkPref === '1') {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
      }
    })();
    </script>
</body>
</html>
