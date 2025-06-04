<?php
session_start();
require_once 'db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$article->execute([$id]);
$article = $article->fetch(PDO::FETCH_ASSOC);
if (!$article) {
  http_response_code(404);
  echo '<h2 style="text-align:center;margin-top:4rem;">المقال غير موجود</h2>';
  exit;
}
// يمكنك جلب بيانات الكاتب إذا أردت لاحقاً
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($article['title']) ?> | مقالات</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    .article-page-container {
      max-width: 800px;
      margin: 6rem auto 2rem;
      background: #fff;
      border-radius: 1.2rem;
      box-shadow: 0 4px 24px #4262ed14;
      padding: 2.5rem 2rem 2.5rem 2rem;
      position: relative;
    }
    [data-theme="dark"] .article-page-container {
      background: #1E293B !important;
      color: #fff !important;
    }
    .article-page-image {
      width: 100%;
      max-height: 350px;
      object-fit: cover;
      border-radius: 1rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px #0002;
    }
    .article-page-title {
      font-family: 'Merriweather', serif;
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: #2563EB;
    }
    [data-theme="dark"] .article-page-title {
      color: #60A5FA;
    }
    .article-page-meta {
      color: #888;
      font-size: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      align-items: center;
    }
    .article-page-meta .category-tag {
      margin-right: 0.5rem;
    }
    .article-page-content {
      font-size: 1.15rem;
      line-height: 2.1;
      color: #222;
      margin-bottom: 2rem;
      white-space: pre-line;
    }
    [data-theme="dark"] .article-page-content {
      color: #fff;
    }
    .article-page-back {
      display: inline-block;
      margin-bottom: 1.5rem;
      color: #2563EB;
      font-weight: bold;
      text-decoration: none;
      font-size: 1.1rem;
      transition: color 0.2s;
    }
    .article-page-back:hover {
      color: #1E293B;
      text-decoration: underline;
    }
    [data-theme="dark"] .article-page-back:hover {
      color: #fff;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="container">
      <nav class="nav">
        <a href="index.php" class="logo"><i class="fa fa-feather"></i>مقالات</a>
      </nav>
    </div>
  </header>
  <main>
    <div class="article-page-container">
      <a href="index.php" class="article-page-back"><i class="fa fa-arrow-right"></i> العودة للرئيسية</a>
      <?php
        $imgSrc = $article['image'] ? 'uploads/articles/' . htmlspecialchars($article['image']) : 'https://source.unsplash.com/800x350/?arabic,writing,' . urlencode($article['category'] ?? 'article');
      ?>
      <img src="<?= $imgSrc ?>" alt="صورة المقال" class="article-page-image">
      <h1 class="article-page-title"><?= htmlspecialchars($article['title']) ?></h1>
      <div class="article-page-meta">
        <span><i class="fa fa-calendar-alt"></i> <?= htmlspecialchars(substr($article['created_at'],0,10)) ?></span>
        <?php if (!empty($article['category'])): ?>
          <span class="category-tag" style="background:#3B82F6;color:#fff;padding:0.2em 1em;border-radius:999px;font-size:0.95em;"> <?= htmlspecialchars($article['category']) ?> </span>
        <?php endif; ?>
        <span><i class="fa fa-user"></i> <?= htmlspecialchars($article['author'] ?? 'مجهول') ?></span>
      </div>
      <div class="article-page-content">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
      </div>
    </div>
  </main>
</body>
</html>
