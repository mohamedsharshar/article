<?php
session_start();
require_once 'db.php';
// جلب جميع التصنيفات كمصفوفة id=>name
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_KEY_PAIR);

// تحديد التصنيف المختار من الرابط
$selectedCategoryId = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? intval($_GET['category_id']) : null;

// جلب المقالات حسب التصنيف المختار بدون أي JOIN
if ($selectedCategoryId) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE category_id = ? ORDER BY created_at DESC");
    $stmt->execute([$selectedCategoryId]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
// إضافة اسم التصنيف لكل مقال
foreach ($articles as &$article) {
    $article['category_name'] = isset($categories[$article['category_id']]) ? $categories[$article['category_id']] : null;
}
unset($article);
// جلب المقال الأعلى تقييماً بناءً على جدول article_ratings فقط
$topRatedId = $pdo->query("SELECT article_id FROM article_ratings GROUP BY article_id HAVING COUNT(*) > 0 ORDER BY AVG(rating) DESC, COUNT(*) DESC LIMIT 1")->fetchColumn();
$featured = null;
if ($topRatedId) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? LIMIT 1");
    $stmt->execute([$topRatedId]);
    $featured = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($featured) {
        // اسم التصنيف
        $featured['category_name'] = isset($categories[$featured['category_id']]) ? $categories[$featured['category_id']] : null;
        // التقييمات
        $featured['avg_rating'] = $pdo->query("SELECT AVG(rating) FROM article_ratings WHERE article_id = " . intval($featured['id']))->fetchColumn();
        $featured['total_ratings'] = $pdo->query("SELECT COUNT(*) FROM article_ratings WHERE article_id = " . intval($featured['id']))->fetchColumn();
        // اسم الكاتب
        $featured['author_name'] = '';
        if (!empty($featured['user_id'])) {
            $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
            $stmt->execute([$featured['user_id']]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($u) $featured['author_name'] = $u['username'];
        } elseif (!empty($featured['admin_id'])) {
            $stmt = $pdo->prepare('SELECT adminname FROM admins WHERE id = ?');
            $stmt->execute([$featured['admin_id']]);
            $a = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($a) $featured['author_name'] = $a['adminname'];
        }
        if (empty($featured['author_name'])) $featured['author_name'] = 'مجهول';
    }
}

// قواعد (rules) بسيطة: لا تظهر المقالات المحذوفة أو الفارغة العنوان/المحتوى
$articles = array_filter($articles, function($a) {
    return !empty($a['title']) && !empty($a['content']);
});
// Pagination
$perPage = 6;
$totalArticles = count($articles);
$totalPages = ceil($totalArticles / $perPage);
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $perPage;
$articlesPage = array_slice($articles, $start, $perPage);

// جلب المقالات المفضلة للمستخدم الحالي (إن وجد)
$favIds = [];
if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare('SELECT article_id FROM favorite_articles WHERE user_id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $favIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>مقالات | منصة مقالات عصرية</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
:root {
  --color-primary: #3B82F6;
  --color-primary-dark: #2563EB;
  --color-slate-50: #F8FAFC;
  --color-slate-100: #F1F5F9;
  --color-slate-200: #E2E8F0;
  --color-slate-300: #CBD5E1;
  --color-slate-400: #94A3B8;
  --color-slate-500: #64748B;
  --color-slate-600: #475569;
  --color-slate-700: #334155;
  --color-slate-800: #1E293B;
  --color-slate-900: #0F172A;
  --color-white: #FFFFFF;
  --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
  --font-serif: 'Merriweather', Georgia, serif;
}

/* Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: var(--font-sans);
  color: #3B82F6 ;
  background-color: var(--color-slate-50); 
  line-height: 1.5;
}

[data-theme="dark"] body {
  background-color: #0F172A !important;
  color: #fff !important;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

/* Header */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background-color: var(--color-white);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  z-index: 50;
}

[data-theme="dark"] .header {
  background-color: #1E293B !important;
}

.nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 4rem;
}

.logo {
  font-family: var(--font-serif);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-slate-900);
  text-decoration: none;
}

.nav-links {
  display: none;
  background: none !important;
  box-shadow: none !important;
}

@media (min-width: 768px) {
  .nav-links {
    display: flex;
    gap: 2rem;
    background: none !important;
    box-shadow: none !important;
  }
}

.nav-links a {
  color: var(--color-slate-600);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: color 0.2s;
}
.user-dropdown a{
  text-decoration: none !important;
}
.nav-links a:hover,
.nav-links a.active {
  color: var(--color-primary);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-primary {
  background-color: var(--color-primary);
  color: var(--color-white);
}

.btn-primary:hover {
  background-color: var(--color-primary-dark);
}

.btn-outline {
  border: 1px solid var(--color-primary-dark);
  color: #2563EB;
}

.btn-outline:hover {
  background-color: var(--color-slate-100);
}

/* Hero Section */
.hero {
  padding: 8rem 0 4rem;
  text-align: center;
}

.hero h1 {
  font-family: var(--font-serif);
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.125rem;
  max-width: 36rem;
  margin: 0 auto;
}

/* Search Section */
.search-section {
  padding: 2rem 0;
}

.search-form {
  max-width: 32rem;
  margin: 0 auto;
}

.search-input-wrapper {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 0.75rem 3rem;
  border: 1px solid var(--color-slate-200);
  border-radius: 9999px;
  font-size: 1rem;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* تحسين زر البحث */
.search-submit {
  position: absolute;
  left: 2px;
  top: 50%;
  transform: translateY(-50%);
  background: var(--color-primary);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px #3b82f622;
}
.search-submit:hover, .search-submit:focus {
  background: var(--color-primary-dark);
  box-shadow: 0 4px 16px #2563eb33;
  outline: none;
}

/* Categories Section */
.categories-section {
  padding: 1rem 0 2rem;
}

.category-filters {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
}

.category-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 9999px;
  background-color: var(--color-slate-100);
  color: var(--color-slate-700);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
  text-decoration: none !important;
}

.category-btn:hover {
  background-color: var(--color-slate-200);
}

.category-btn.active {
  background-color: var(--color-primary);
  color: var(--color-white);
}

/* Featured Article */
.featured-article {
  padding: 2rem 0;
}

.featured-article h2 {
  font-family: var(--font-serif);
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.featured-card {
  border-radius: 0.75rem;
  overflow: hidden;
  background-color: var(--color-white);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.featured-image {
  position: relative;
  height: 24rem;
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.featured-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  color: var(--color-white);
}

.category-tag {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background-color: var(--color-primary);
  color:#fff !important;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  margin-bottom: 1rem;
  
}

.featured-content h3 {
  font-family: var(--font-serif);
  font-size: 1.875rem;
  margin-bottom: 0.75rem;
}

.article-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
}

.author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.author img {
  width: 2rem;
  height: 2rem;
  border-radius: 9999px;
  object-fit: cover;
}

.meta-info {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
}

/* Articles Grid */
.articles-grid {
  padding: 2rem 0;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  padding: 1.5rem 0;
}

.article-card {
  background-color: var(--color-white);
  border-radius: 0.75rem;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.article-card:hover {
  transform: translateY(-2px);
}

.article-image {
  position: relative;
  height: 12rem;
}

.article-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.article-content {
  padding: 1.5rem;
}

.article-content h3 {
  font-family: var(--font-serif);
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
}

/* Footer */
.footer {
  background-color: var(--color-white);
  border-top: 1px solid var(--color-slate-200);
  padding: 4rem 0 2rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.footer h3 {
  font-family: var(--font-serif);
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.footer h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.footer-about p {
  color: var(--color-slate-600);
  margin-bottom: 1.5rem;
}

.social-links {
  display: flex;
  gap: 1rem;
}

.footer ul {
  list-style: none;
}

.footer ul li {
  margin-bottom: 0.5rem;
}

.footer a {
  color: var(--color-slate-600);
  text-decoration: none;
  transition: color 0.2s;
}

.footer a:hover {
  color: var(--color-primary);
}

.subscribe-form .input-group {
  display: flex;
  margin-top: 1rem;
}

.subscribe-form input {
  flex: 1;
  padding: 0.5rem 1rem;
  border: 1px solid var(--color-slate-200);
  border-radius: 0.375rem 0 0 0.375rem;
  outline: none;
}

.subscribe-form button {
  padding: 0.5rem 1rem;
  background-color: var(--color-primary);
  color: var(--color-white);
  border: none;
  border-radius: 0 0.375rem 0.375rem 0;
  cursor: pointer;
}

.footer-subscribe .input-group {
  display: flex;
  flex-direction: row-reverse;
  align-items: stretch;
  width: 100%;
  margin-top: 1rem;
}
.footer-subscribe input[type="email"] {
  flex: 1;
  padding: 0.6rem 1rem;
  border: 1.5px solid var(--color-slate-200);
  border-radius: 1.5rem 0rem 0rem 1.5rem;
  outline: none;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s, box-shadow 0.2s;
  direction: rtl;
  text-align: right;
}
.footer-subscribe input[type="email"]:focus {
  border-color: var(--color-primary);
  box-shadow: 0 2px 8px #3b82f62a;
}
.footer-subscribe button[type="submit"] {
  padding: 0 1.1rem;
  background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%);
  color: #fff;
  border: none;
  border-radius: 0rem 1.5rem 1.5rem 0rem;
  font-size: 1.2rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.footer-subscribe button[type="submit"]:hover {
  background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
  box-shadow: 0 2px 8px #3b82f62a;
  transform: scale(1.05);
}
@media (max-width: 600px) {
  .footer-subscribe .input-group {
    flex-direction: row-reverse;
    width: 100%;
  }
  .footer-subscribe input[type="email"] {
    font-size: 0.97rem;
    padding: 0.5rem 0.7rem;
    border-radius: 0 1.1rem 1.1rem 0;
  }
  .footer-subscribe button[type="submit"] {
    font-size: 1rem;
    padding: 0 0.7rem;
    border-radius: 1.1rem 0 0 1.1rem;
  }
}
.footer-bottom {
  padding-top: 2rem;
  border-top: 1px solid var(--color-slate-200);
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
  list-style: none;
}

/* Pagination Custom Style */
.pagination-custom {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin: 2rem 0;
}
.pagination-custom .page-link {
  background: linear-gradient(90deg, #60A5FA 0%, #3B82F6 100%);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 0.5em 1.2em;
  font-size: 1rem;
  font-weight: bold;
  box-shadow: 0 2px 8px #60A5FA22;
  transition: background 0.2s, box-shadow 0.2s, color 0.2s;
  text-decoration: none;
  cursor: pointer;
}
.pagination-custom .page-link.active,
.pagination-custom .page-link:hover {
  background: linear-gradient(90deg, #3B82F6 0%, #60A5FA 100%);
  color: #fff;
  box-shadow: 0 4px 16px #3B82F622;
}
@media (max-width: 600px) {
  .pagination-custom .page-link {
    font-size: 0.95rem;
    padding: 0.4em 0.7em;
  }
}

/* Dark Mode */
[data-theme="dark"] {
  --color-primary: #60A5FA;
  --color-primary-dark: #3B82F6;
  --color-slate-50: #0F172A;
  --color-slate-100: #1E293B;
  --color-slate-200: #334155;
  --color-slate-300: #475569;
  --color-slate-400: #64748B;
  --color-slate-500: #94A3B8;
  --color-slate-600: #CBD5E1;
  --color-slate-700: #E2E8F0;
  --color-slate-800: #F1F5F9;
  --color-slate-900: #F8FAFC;
  --color-white: #fff;
}
[data-theme="dark"] body {
  background-color: #0F172A !important;
  color: #fff !important;
}
[data-theme="dark"] .header,
[data-theme="dark"] .footer {
  background-color: #1E293B !important;
  border-color: #334155 !important;
}
[data-theme="dark"] .logo {
  color: #fff !important;
}
[data-theme="dark"] .article-card,
[data-theme="dark"] .featured-card {
  background-color: #1E293B !important;
}
[data-theme="dark"] .btn-outline {
  border-color: #0F172A !important;
  color: #0F172A !important;
}
[data-theme="dark"] .btn-outline:hover {
  background-color: #334155 !important;
}
[data-theme="dark"] .search-input {
  background-color: #1E293B !important;
  border-color: #334155 !important;
  color: #fff !important;
}
[data-theme="dark"] .category-btn {
  background-color: #1E293B !important;
  color: #CBD5E1 !important;
}
[data-theme="dark"] .category-btn:hover {
  background-color: #334155 !important;
}
[data-theme="dark"] .category-tag{
  color:white !important;
}
[data-theme="dark"] .footer a {
  color: #94A3B8 !important;
}
[data-theme="dark"] .footer a:hover {
  color: #60A5FA !important;
}
[data-theme="dark"] a {
  color: #fff !important;
}
[data-theme="dark"] .theme-toggle {
  color: #fff !important;
}
</style>
</head>
<body>
  <header class="header">
    <div class="container">
      <nav class="nav">
        <a href="index.php" class="logo"><i class="fa fa-feather"></i>مقالات</a>
        <div class="nav-links">
          <a href="index.php" class="active">الرئيسية</a>
          <a href="#categories">التصنيفات</a>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="add_article.php">إضافة مقال</a>
          <?php endif; ?>
          <a href="about.php">عن الموقع</a>
          <a href="contact.php">تواصل</a>
          <a href="top_rated_articles.php">الأعلى تقييماً</a>
        </div>
        <div class="nav-actions">
          
          <button class="theme-toggle" aria-label="تبديل الوضع">
            <i class="fa fa-moon"></i>
          </button>
          <span id="user-actions"></span>
          <button class="menu-toggle" aria-label="القائمة" id="menuToggle">
            <i class="fa fa-bars"></i>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container">
        <h1> <i class="fa fa-feather"></i>مقالات</h1>
        <p>اكتشف مقالات ملهمة وحديثة في التقنية، التصميم، والابتكار الرقمي.</p>
      </div>
    </section>

    <section class="search-section">
      <div class="container">
        <form class="search-form">
          <div class="search-input-wrapper">
            <input type="text" placeholder="ابحث عن مقال..." class="search-input">
            <button type="submit" class="search-submit">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </form>
      </div>
    </section>

    <section class="categories-section" id="categories">
      <div class="container">
        <div class="category-filters">
          <a href="index.php" class="category-btn<?= !$selectedCategoryId ? ' active' : '' ?>" data-id="all">الكل</a>
          <?php foreach($categories as $catId => $catName): ?>
            <a href="?category_id=<?= $catId ?>" class="category-btn<?= $selectedCategoryId == $catId ? ' active' : '' ?>" data-id="<?= $catId ?>"> <?= htmlspecialchars($catName) ?> </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="articles-grid">
      <div class="container">
        <h2>
          <?php
            if ($selectedCategoryId && isset($categories[$selectedCategoryId])) {
              echo "مقالات تصنيف: " . htmlspecialchars($categories[$selectedCategoryId]);
            } else {
              echo "أحدث المقالات";
            }
          ?>
        </h2>
        <div class="grid" id="articles-container">
          <?php foreach($articlesPage as $article): ?>
            <article class="article-card" tabindex="0" aria-label="مقال: <?= htmlspecialchars($article['title']) ?>" onclick="window.location.href='article.php?id=<?= $article['id'] ?>'">
              <div class="article-image" style="position:relative;">
                <img src="<?= $article['image'] ? 'uploads/articles/' . htmlspecialchars($article['image']) : 'https://source.unsplash.com/400x200/?arabic,writing,' . urlencode($article['category_name'] ?? 'article') ?>" alt="صورة المقال" loading="lazy">
                <?php if(isset($_SESSION['user_id'])): ?>
                  <button class="fav-btn" data-article-id="<?= $article['id'] ?>" aria-label="أضف للمفضلة" style="position:absolute;top:10px;left:10px;background:none;border:none;cursor:pointer;font-size:1.5rem;z-index:2;">
                    <span class="fav-icon" style="color:<?= in_array($article['id'], $favIds) ? '#e63946' : '#bbb' ?>;font-size:1.5rem;">
                      <?= in_array($article['id'], $favIds) ? '♥' : '♡' ?>
                    </span>
                  </button>
                <?php endif; ?>
              </div>
              <div class="article-content">
                <h3><?= htmlspecialchars($article['title']) ?></h3>
                <p><?= htmlspecialchars(mb_substr($article['content'],0,100)) ?><?= mb_strlen($article['content']) > 100 ? '...' : '' ?></p>
                <div class="meta-info">
                  <span><i class="fa fa-calendar-alt"></i> <?= htmlspecialchars(substr($article['created_at'],0,10)) ?></span>
                  <?php if($article['category_name']): ?><span class="category-tag"> <?= htmlspecialchars($article['category_name']) ?> </span><?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <nav class="pagination-custom">
          <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>" class="page-link">السابق</a>
          <?php endif; ?>
          <?php for($i=1;$i<=$totalPages;$i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i==$page?'active':'' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>" class="page-link">التالي</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>
    </section>

    <section class="featured-article">
      <div class="container">
        <h2>مقال مميز</h2>
        <?php if ($featured): ?>
        <article class="featured-card">
          <div class="featured-image">
            <img src="<?= $featured['image'] ? 'uploads/articles/' . htmlspecialchars($featured['image']) : 'https://source.unsplash.com/900x400/?arabic,writing,' . urlencode($featured['category_name'] ?? 'article') ?>" alt="صورة المقال المميز" loading="lazy">
            <div class="featured-content">
              <?php if ($featured['category_name']): ?><span class="category-tag"><?= htmlspecialchars($featured['category_name']) ?></span><?php endif; ?>
              <h3><?= htmlspecialchars($featured['title']) ?></h3>
              <p><?= htmlspecialchars(mb_substr($featured['content'],0,120)) ?><?= mb_strlen($featured['content']) > 120 ? '...' : '' ?></p>
              <div class="article-meta">
                <div class="author">
                  <i class="fa fa-user"></i>
                  <span><?= htmlspecialchars($featured['author_name'] ?: 'مجهول') ?></span>
                </div>
                <div class="meta-info">
                  <span><i class="fa fa-calendar"></i><?= htmlspecialchars(substr($featured['created_at'],0,10)) ?></span>
                  <span><i class="fa fa-star" style="color:#fbbf24;"></i> <?= number_format($featured['avg_rating'],2) ?> (<?= $featured['total_ratings'] ?> تقييم)</span>
                </div>
              </div>
              <a href="article.php?id=<?= $featured['id'] ?>" class="btn btn-primary" style="margin-top:1rem;">اقرأ المزيد</a>
            </div>
          </div>
        </article>
        <?php else: ?>
        <div style="text-align:center;color:#888;">لا يوجد مقال مميز بعد.</div>
        <?php endif; ?>
      </div>
    </section>

   
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-about">
          <h3>مقالات</h3>
          <p>منصة عصرية لمقالات ملهمة ورؤى في التقنية، التصميم، وأكثر.</p>
          <div class="social-links">
            <a href="#" aria-label="فيسبوك"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="تويتر"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="انستجرام"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="يوتيوب"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="footer-nav">
          <h4>روابط</h4>
          <ul>
            <li><a href="index.php">الرئيسية</a></li>
            <li><a href="#categories">التصنيفات</a></li>
            <li><a href="#about">عن الموقع</a></li>
            <li><a href="#contact">تواصل</a></li>
          </ul>
        </div>
        <div class="footer-categories">
          <h4>تصنيفات</h4>
          <ul>
            <li><a href="#">تقنية</a></li>
            <li><a href="#">تصميم</a></li>
            <li><a href="#">ذكاء اصطناعي</a></li>
            <li><a href="#">تطوير</a></li>
            <li><a href="#">تجربة المستخدم</a></li>
          </ul>
        </div>
        <div class="footer-subscribe">
          <h4>اشترك</h4>
          <p>اشترك في النشرة البريدية لأحدث المقالات والتحديثات.</p>
          <form class="subscribe-form" id="footerSubscribeForm" onsubmit="return false;">
            <div class="input-group">
              <input type="email" id="footerSubscribeEmail" placeholder="بريدك الإلكتروني" required>
              <button type="submit" aria-label="اشترك">
                <i class="fa fa-envelope"></i>
              </button>
            </div>
            <div id="footerSubscribeMsg" style="margin-top:0.7rem;font-size:1rem;"></div>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?php echo date("Y");?> مقالات. جميع الحقوق محفوظة.</p>
        <ul class="footer-links">
          <li><a href="privacy.php">سياسة الخصوصية</a></li>
          <li><a href="terms.php">الشروط</a></li>
          <li><a href="cookies.php">سياسة الكوكيز</a></li>
        </ul>
      </div>
    </div>
  </footer>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
  <script>
    // بيانات المستخدم من PHP
    const username = <?php echo isset($_SESSION['username']) ? '"' . addslashes($_SESSION['username']) . '"' : 'null'; ?>;
    const userActions = document.getElementById('user-actions');
    if (username) {
      userActions.innerHTML = `
        <div class="navbar-user" style="position:relative;display:inline-block;">
          <button class="btn btn-outline user-nav-info" id="userNavInfo" tabindex="0">
            <i class="fa fa-user-circle"></i> ${username}
          </button>
          <ul class="user-dropdown" id="userDropdown">
            <li><a href="profile.php"><i class="fa fa-user"></i> بروفايلي</a></li>
            <li><a href="favorites.php"><i class="fa fa-heart"></i> المفضلة</a></li>
            <li><a href="#" id="logoutLink"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
          </ul>
        </div>
      `;
      // تفعيل القائمة المنسدلة للمستخدم
      const navbarUser = userActions.querySelector('.navbar-user');
      const userNavInfo = document.getElementById('userNavInfo');
      const userDropdown = document.getElementById('userDropdown');
      userNavInfo.addEventListener('click', function(e) {
        e.preventDefault();
        navbarUser.classList.toggle('open');
      });
      document.addEventListener('click', function(e) {
        if (!navbarUser.contains(e.target)) {
          navbarUser.classList.remove('open');
        }
      });
      userNavInfo.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          navbarUser.classList.toggle('open');
        }
      });
      // تفعيل تسجيل الخروج
      const logoutLink = document.getElementById('logoutLink');
      if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
          e.preventDefault();
          window.location.href = 'logout.php';
        });
      }
    } else {
      userActions.innerHTML = `
        <a href="login.php" class="btn btn-outline"><i class="fa fa-sign-in-alt"></i> تسجيل الدخول</a>
        <a href="register.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> إنشاء حساب</a>
      `;
    }

    // منيو بار للموبايل
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.querySelector('.nav-links');
    if (menuToggle && navLinks) {
      menuToggle.addEventListener('click', function() {
        navLinks.classList.toggle('open');
        const icon = menuToggle.querySelector('i');
        if (icon) icon.className = navLinks.classList.contains('open') ? 'fa fa-times' : 'fa fa-bars';
      });
      document.addEventListener('click', function(e) {
        if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
          navLinks.classList.remove('open');
          const icon = menuToggle.querySelector('i');
          if (icon) icon.className = 'fa fa-bars';
        }
      });
      navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          navLinks.classList.remove('open');
          const icon = menuToggle.querySelector('i');
          if (icon) icon.className = 'fa fa-bars';
        });
      });
    }

    // الوضع الليلي (ثابت على الموقع حتى يغيره المستخدم يدوياً)
    const themeToggle = document.querySelector('.theme-toggle');
    function setDarkMode(on) {
      if(on) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('darkMode', '1');
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('darkMode', '0');
      }
    }
    // تفعيل الوضع الليلي دائماً عند التحميل (إلا إذا اختار المستخدم وضع آخر)
    if(themeToggle) {
      if(localStorage.getItem('darkMode') === null) {
        setDarkMode(true);
        themeToggle.innerHTML = '<i class="fa fa-sun"></i>';
      } else if(localStorage.getItem('darkMode') === '1') {
        setDarkMode(true);
        themeToggle.innerHTML = '<i class="fa fa-sun"></i>';
      } else {
        setDarkMode(false);
        themeToggle.innerHTML = '<i class="fa fa-moon"></i>';
      }
      themeToggle.onclick = function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        setDarkMode(!isDark);
        themeToggle.innerHTML = isDark ? '<i class="fa fa-moon"></i>' : '<i class="fa fa-sun"></i>';
      };
    }

    // بيانات المقالات من PHP إلى جافاسكريبت
    const articles = <?php echo json_encode($articles, JSON_UNESCAPED_UNICODE); ?>;
    // تفعيل الفلاتر الديناميكية
    const categoryFilters = document.querySelector('.category-filters');
    let currentCategoryId = null;
    categoryFilters.querySelectorAll('button').forEach(btn => {
      btn.onclick = function() {
        categoryFilters.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCategoryId = btn.dataset.id === 'all' ? null : btn.dataset.id;
        renderArticles();
      };
    });

    // البحث
    const searchInput = document.querySelector('.search-input');
    const searchForm = document.querySelector('.search-form');
    const searchBtn = document.querySelector('.search-submit');
    let currentQuery = '';

    searchForm.onsubmit = e => {
      e.preventDefault();
      currentQuery = searchInput.value.trim();
      renderArticles();
    };
    searchBtn.onclick = function(e) {
      e.preventDefault();
      currentQuery = searchInput.value.trim();
      renderArticles();
    };
    searchInput.addEventListener('input', function() {
      if (searchInput.value.trim() === '') {
        currentQuery = '';
        renderArticles();
      }
    });

    // تفعيل الفلترة عند الضغط على زر تصنيف
    function renderArticles() {
      let filtered = articles;
      if (currentCategoryId) filtered = filtered.filter(a => a.category_id == currentCategoryId);
      if (currentQuery) {
        const q = currentQuery.toLowerCase();
        filtered = filtered.filter(a => (a.title && a.title.toLowerCase().includes(q)) || (a.content && a.content.toLowerCase().includes(q)));
      }
      // إزالة المقال المميز من الشبكة
      const featuredId = articles[0] ? articles[0].id : null;
      filtered = filtered.filter(a => a.id !== featuredId);
      // عرض المقالات
      const grid = document.getElementById('articles-container');
      grid.innerHTML = '';
      if (filtered.length === 0) {
        grid.innerHTML = '<div class="no-articles">لا توجد مقالات مطابقة.</div>';
        return;
      }
      filtered.forEach(article => {
        const imgSrc = article.image ? `uploads/articles/${article.image}` : `https://source.unsplash.com/400x200/?arabic,writing,${encodeURIComponent(article.category_name || 'article')}`;
        const isFav = Array.isArray(window.favIds) && window.favIds.includes(article.id);
        const favBtn = username ? `<button class=\"fav-btn\" data-article-id=\"${article.id}\" aria-label=\"أضف للمفضلة\" style=\"position:absolute;top:10px;left:10px;background:none;border:none;cursor:pointer;font-size:1.5rem;z-index:2;\"><span class="fav-icon" style="color:${isFav ? '#e63946' : '#bbb'};font-size:1.5rem;">${isFav ? '♥' : '♡'}</span></button>` : '';
        const card = document.createElement('article');
        card.className = 'article-card';
        card.tabIndex = 0;
        card.setAttribute('aria-label', 'مقال: ' + article.title);
        card.innerHTML = `
          <div class=\"article-image\">
            <img src=\"${imgSrc}\" alt=\"صورة المقال\" loading=\"lazy\">
            ${favBtn}
          </div>
          <div class=\"article-content\">
            <h3>${article.title}</h3>
            <p>${(article.content || '').substring(0, 100)}${(article.content && article.content.length > 100 ? '...' : '')}</p>
            <div class=\"meta-info\">
              <span><i class=\"fa fa-calendar-alt\"></i> ${article.created_at.split(' ')[0]}</span>
              ${article.category_name ? `<span class=\"category-tag\">${article.category_name}</span>` : ''}
            </div>
          </div>
        `;
        card.onclick = () => window.location.href = `article.php?id=${article.id}`;
        grid.appendChild(card);
      });
      activateFavButtons();
    }

    // تفعيل المقال المميز ديناميكياً
    function renderFeatured() {
      const featured = articles[0];
      if (!featured) return;
      const featuredCard = document.querySelector('.featured-card');
      const imgSrc = featured.image ? `uploads/articles/${featured.image}` : `https://source.unsplash.com/900x400/?arabic,writing,${encodeURIComponent(featured.category || 'article')}`;
      featuredCard.innerHTML = `
        <div class="featured-image">
          <img src="${imgSrc}" alt="صورة المقال المميز" loading="lazy">
          <div class="featured-content">
            ${featured.category ? `<span class="category-tag">${featured.category}</span>` : ''}
            <h3>${featured.title}</h3>
            <p>${(featured.content || '').substring(0, 120)}${(featured.content && featured.content.length > 120 ? '...' : '')}</p>
            <div class="article-meta">
              <div class="author">
                <i class="fa fa-user"></i>
                <span>${featured.author_name || 'مجهول'}</span>
              </div>
              <div class="meta-info">
                <span><i class="fa fa-calendar"></i>${featured.created_at.split(' ')[0]}</span>
                <span><i class="fa fa-clock"></i>قراءة سريعة</span>
              </div>
            </div>
            <a href="article.php?id=${featured.id}" class="btn btn-primary" style="margin-top:1rem;">اقرأ المزيد</a>
          </div>
        </div>
      `;
    }

    // نافذة تفاصيل المقال (بسيطة)
    function showArticleDetails(article) {
      const modal = document.createElement('div');
      modal.style = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:#000a;z-index:9999;display:flex;align-items:center;justify-content:center;';
      const imgSrc = article.image ? `uploads/articles/${article.image}` : `https://source.unsplash.com/600x300/?arabic,writing,${encodeURIComponent(article.category || 'article')}`;
      modal.innerHTML = `
        <div style="background:#fff;max-width:600px;width:90vw;padding:2rem;border-radius:1rem;position:relative;direction:rtl;max-height:90vh;overflow:auto;">
          <button style="position:absolute;top:1rem;left:1rem;font-size:1.5rem;background:none;border:none;cursor:pointer;" aria-label="إغلاق" onclick="this.parentNode.parentNode.remove()"><i class='fa fa-times'></i></button>
          <h2>${article.title}</h2>
          <div style="color:#888;font-size:0.95rem;margin-bottom:1rem;">
            <i class="fa fa-calendar-alt"></i> ${article.created_at.split(' ')[0]}
            ${article.category ? `<span class="category-tag" style="margin-right:1rem;">${article.category}</span>` : ''}
          </div>
          <div style="margin-bottom:1.5rem;">
            <img src="${imgSrc}" alt="صورة المقال" style="width:100%;border-radius:0.5rem;">
          </div>
          <div style="font-size:1.1rem;line-height:2;">${article.content.replace(/\n/g,'<br>')}</div>
        </div>
      `;
      document.body.appendChild(modal);
      modal.onclick = e => { if (e.target === modal) modal.remove(); };
    }

    // الوضع الليلي (ثابت على الموقع حتى يغيره المستخدم يدوياً)
    function setDarkMode(on) {
      if(on) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('darkMode', '1');
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('darkMode', '0');
      }
    }
    // تفعيل الوضع الليلي دائماً عند التحميل (إلا إذا اختار المستخدم وضع آخر)
    if(localStorage.getItem('darkMode') === null) {
      setDarkMode(true);
      document.querySelector('.theme-toggle').innerHTML = '<i class="fa fa-sun"></i>';
    } else if(localStorage.getItem('darkMode') === '1') {
      setDarkMode(true);
      document.querySelector('.theme-toggle').innerHTML = '<i class="fa fa-sun"></i>';
    } else {
      setDarkMode(false);
      document.querySelector('.theme-toggle').innerHTML = '<i class="fa fa-moon"></i>';
    }
    // زر تبديل الوضع
    themeToggle.onclick = function() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      setDarkMode(!isDark);
      themeToggle.innerHTML = isDark ? '<i class="fa fa-moon"></i>' : '<i class="fa fa-sun"></i>';
    };

    // تمرير favIds من PHP إلى جافاسكريبت لاستخدامها في تلوين القلوب
    window.favIds = <?php echo json_encode($favIds); ?>;

    // تفعيل زر المفضلة لجميع المقالات في الصفحة الرئيسية
    function activateFavButtons() {
      document.querySelectorAll('.fav-btn').forEach(function(btn) {
        btn.replaceWith(btn.cloneNode(true));
      });
      document.querySelectorAll('.fav-btn').forEach(function(btn) {
        var icon = btn.querySelector('.fav-icon');
        var articleId = btn.dataset.articleId;
        // عند تحميل الصفحة: إذا المقال في المفضلة يكون القلب أحمر، غير ذلك بدون لون
        if (icon) {
          if (window.favIds && window.favIds.includes(Number(articleId))) {
            icon.textContent = '♥';
            icon.style.color = '#e63946';
            btn.setAttribute('aria-label', 'إزالة من المفضلة');
          } else {
            icon.textContent = '♡';
            icon.style.color = '#bbb';
            btn.setAttribute('aria-label', 'أضف للمفضلة');
          }
        }
        btn.onclick = function(e) {
          e.stopPropagation();
          var icon = this.querySelector('.fav-icon');
          var articleId = this.dataset.articleId;
          var isFav = icon.textContent === '♥';
          if (isFav) {
            icon.textContent = '♡';
            icon.style.color = '#bbb';
            this.setAttribute('aria-label', 'أضف للمفضلة');
          } else {
            icon.textContent = '♥';
            icon.style.color = '#e63946';
            this.setAttribute('aria-label', 'إزالة من المفضلة');
          }
          fetch('toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'article_id=' + articleId
          })
          .then(r=>r.json())
          .then(data => {
            if (window.favIds) {
              if (data.status === 'added') {
                if (!window.favIds.includes(Number(articleId))) window.favIds.push(Number(articleId));
                updateFavIdsStorage();
              } else if (data.status === 'removed') {
                window.favIds = window.favIds.filter(id => id !== Number(articleId));
                updateFavIdsStorage();
                if (window.location.pathname.includes('favorites.php')) {
                  var card = btn.closest('.article-card');
                  if (card) card.remove();
                  if (document.querySelectorAll('.article-card').length === 0) {
                    var grid = document.getElementById('articles-container');
                    if (grid) grid.innerHTML = '<div class="no-articles">لا توجد مقالات مفضلة.</div>';
                  }
                }
              }
            }
          });
        };
      });
    }
    activateFavButtons();

    // مزامنة حالة زر المفضلة بين الصفحة الرئيسية وصفحة المقال
    window.addEventListener('storage', function(e) {
      if (e.key === 'favIdsSync') {
        // إعادة تفعيل القلوب وتحديثها بناءً على window.favIds
        if (window.favIds) {
          try {
            window.favIds = JSON.parse(localStorage.getItem('favIds') || '[]');
          } catch { window.favIds = []; }
        }
        if (typeof activateFavButtons === 'function') activateFavButtons();
      }
    });

    // تحديث localStorage عند تغيير المفضلة
    function updateFavIdsStorage() {
      try {
        localStorage.setItem('favIds', JSON.stringify(window.favIds || []));
        localStorage.setItem('favIdsSync', Date.now()); // trigger event
      } catch {}
    }

    // تمرير سلس عند الضغط على الروابط
const smoothLinks = document.querySelectorAll('a[href^="#about"],a[href^="#contact"]');
smoothLinks.forEach(link => {
  link.addEventListener('click', function(e) {
    const target = document.querySelector(this.getAttribute('href'));
    if(target) {
      e.preventDefault();
      target.scrollIntoView({behavior:'smooth'});
    }
  });
});

document.addEventListener('DOMContentLoaded', function() {
  activateFavButtons();
});
  </script>
</body>
</html>