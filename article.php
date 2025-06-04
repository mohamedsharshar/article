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
    :root {
      --color-slate-500: #64748B;
      --color-slate-600: #475569;
      --color-slate-700: #334155;
      --color-slate-800: #1E293B;
      --color-slate-900: #0F172A;
    }
    body {
      background: var(--color-slate-900);
      min-height: 100vh;
      margin: 0;
      font-family: 'Cairo', Tahoma, Arial, sans-serif;
    }
    .article-page-container {
      max-width: 800px;
      margin: 4rem auto 0rem auto;
      border-radius: 1.2rem;
      background: var(--color-slate-800);
      box-shadow: 0 4px 24px var(--color-slate-700)44;
      padding: 2.5rem 2rem 2.5rem 2rem;
      position: relative;
      color: #fff;
    }
    .article-page-image {
      width: 100%;
      max-height: 350px;
      object-fit: cover;
      border-radius: 1rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px var(--color-slate-900)22;
      background: var(--color-slate-700);
      display: block;
    }
    .article-page-title {
      font-family: 'Merriweather', serif;
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: #fff;
      text-shadow: 0 2px 8px var(--color-slate-900)22;
    }
    .article-page-meta {
      color: #fff;
      font-size: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      align-items: center;
    }
    .article-page-meta .category-tag {
      margin-right: 0.5rem;
      background: var(--color-slate-700);
      color: #fff;
      padding: 0.35em 1.2em;
      border-radius: 999px;
      font-size: 1em;
      box-shadow: 0 2px 8px var(--color-slate-900)33;
      z-index: 2;
      font-weight: bold;
      letter-spacing: 0.01em;
    }
    .article-page-content {
      font-size: 1.15rem;
      line-height: 2.1;
      color: #fff;
      margin-bottom: 2rem;
      background: var(--color-slate-700);
      padding: 1.5rem 1.2rem;
      border-radius: 1rem;
      box-shadow: 0 1px 4px var(--color-slate-900)11;
      word-break: break-word;
      white-space: pre-line;
    }
    .article-page-back {
      display: inline-block;
      margin-bottom: 1.5rem;
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      font-size: 1.1rem;
      transition: color 0.2s, background 0.2s;
      background: var(--color-slate-700);
      padding: 0.5em 1.2em;
      border-radius: 8px;
      box-shadow: 0 1px 4px var(--color-slate-900)11;
    }
    .article-page-back:hover {
      color: #fff;
      background: var(--color-slate-600);
      text-decoration: none;
    }
    @media (max-width: 900px) {
      .article-page-container {
        padding: 1.2rem 0.5rem;
        margin: 2.2rem 0.2rem 1.5rem 0.2rem;
      }
      .article-page-title {
        font-size: 1.3rem;
      }
      .article-page-content {
        font-size: 1.01rem;
        padding: 1rem 0.5rem;
      }
    }
    @media (max-width: 600px) {
      .article-page-container {
        margin: 1.2rem 0.1rem 1rem 0.1rem;
        padding: 0.7rem 0.1rem;
      }
      .article-page-title {
        font-size: 1.1rem;
      }
      .article-page-content {
        font-size: 0.98rem;
        padding: 0.7rem 0.2rem;
      }
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
      <div style="position:relative;">
        <img src="<?= $imgSrc ?>" alt="صورة المقال" class="article-page-image">
        <?php if (!empty($article['category'])): ?>
          <span class="category-tag" style="position:absolute;top:18px;right:18px;"> <?= htmlspecialchars($article['category']) ?> </span>
        <?php endif; ?>
      </div>
      <h1 class="article-page-title"> <?= htmlspecialchars($article['title']) ?> </h1>
      <div class="article-page-meta">
        <span><i class="fa fa-calendar-alt"></i> <?= htmlspecialchars(substr($article['created_at'],0,10)) ?></span>
        <span><i class="fa fa-user"></i> <?= htmlspecialchars($article['author'] ?? 'مجهول') ?></span>
      </div>
      <div class="article-page-content">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
      </div>
    </div>
  </main>
</body>
</html>
