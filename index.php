<?php
session_start();
require_once 'db.php';
// جلب جميع التصنيفات من قاعدة البيانات
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
// جلب جميع المقالات مع التصنيف واسم الناشر (من admins أو users)
$articles = $pdo->query("SELECT articles.*, categories.name AS category_name, COALESCE(admins.adminname, users.username) AS author_name
FROM articles
LEFT JOIN categories ON articles.category_id = categories.id
LEFT JOIN admins ON articles.admin_id = admins.id
LEFT JOIN users ON articles.user_id = users.id
ORDER BY articles.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
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
  background-color: var(--color-slate-50); /* سيتم استبدالها بقيمة ديناميكية حسب الثيم */
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
          <a href="#about">عن الموقع</a>
          <a href="#contact">تواصل</a>
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
          <button class="category-btn active" data-id="all">الكل</button>
          <?php foreach($categories as $cat): ?>
            <button class="category-btn" data-id="<?= $cat['id'] ?>"> <?= htmlspecialchars($cat['name']) ?> </button>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="articles-grid">
      <div class="container">
        <h2>أحدث المقالات</h2>
        <div class="grid" id="articles-container">
          <!-- سيتم تعبئة المقالات عبر جافاسكريبت لاحقاً -->
        </div>
      </div>
    </section>

    <section class="featured-article">
      <div class="container">
        <h2>مقال مميز</h2>
        <article class="featured-card">
          <div class="featured-image">
            <img src="https://images.pexels.com/photos/546819/pexels-photo-546819.jpeg" alt="مستقبل تطوير الويب">
            <div class="featured-content">
              <span class="category-tag">تقنية</span>
              <h3>مستقبل تطوير الويب</h3>
              <p>استكشاف الاتجاهات والتقنيات الناشئة التي تشكل مستقبل تطوير الويب.</p>
              <div class="article-meta">
                <div class="author">
                  <img src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg" alt="أحمد علي">
                  <span>أحمد علي</span>
                </div>
                <div class="meta-info">
                  <span><i class="fa fa-calendar"></i>15 مايو 2025</span>
                  <span><i class="fa fa-clock"></i>6 دقائق قراءة</span>
                </div>
              </div>
            </div>
          </div>
        </article>
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
            <a href="#" aria-label="فيسبوك"><i class="fa fa-facebook"></i></a>
            <a href="#" aria-label="تويتر"><i class="fa fa-twitter"></i></a>
            <a href="#" aria-label="انستجرام"><i class="fa fa-instagram"></i></a>
            <a href="#" aria-label="يوتيوب"><i class="fa fa-youtube"></i></a>
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
          <form class="subscribe-form">
            <div class="input-group">
              <input type="email" placeholder="بريدك الإلكتروني">
              <button type="submit" aria-label="اشترك">
                <i class="fa fa-envelope"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 مقالات. جميع الحقوق محفوظة.</p>
        <ul class="footer-links">
          <li><a href="#">سياسة الخصوصية</a></li>
          <li><a href="#">الشروط</a></li>
          <li><a href="#">سياسة الكوكيز</a></li>
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
        menuToggle.querySelector('i').className = navLinks.classList.contains('open') ? 'fa fa-times' : 'fa fa-bars';
      });
      document.addEventListener('click', function(e) {
        if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
          navLinks.classList.remove('open');
          menuToggle.querySelector('i').className = 'fa fa-bars';
        }
      });
      navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          navLinks.classList.remove('open');
          menuToggle.querySelector('i').className = 'fa fa-bars';
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
    // استخراج التصنيفات الفريدة
    const categories = Array.from(new Set(articles.map(a => a.category).filter(Boolean)));

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
      // تحديث أزرار التصنيفات
      categoryFilters.querySelectorAll('button').forEach(btn => {
        btn.classList.remove('active');
        if ((currentCategoryId === null && btn.dataset.id === 'all') || btn.dataset.id == currentCategoryId) btn.classList.add('active');
      });
      // تصفية المقالات
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
        const card = document.createElement('article');
        card.className = 'article-card';
        card.tabIndex = 0;
        card.setAttribute('aria-label', 'مقال: ' + article.title);
        card.innerHTML = `
          <div class="article-image">
            <img src="${imgSrc}" alt="صورة المقال" loading="lazy">
          </div>
          <div class="article-content">
            <h3>${article.title}</h3>
            <p>${(article.content || '').substring(0, 100)}${(article.content && article.content.length > 100 ? '...' : '')}</p>
            <div class="meta-info">
              <span><i class="fa fa-calendar-alt"></i> ${article.created_at.split(' ')[0]}</span>
              ${article.category_name ? `<span class="category-tag">${article.category_name}</span>` : ''}
            </div>
          </div>
        `;
        card.onclick = () => window.location.href = `article.php?id=${article.id}`;
        grid.appendChild(card);
      });
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

    // تفعيل البحث والفلترة عند التحميل
    renderFeatured();
    renderArticles();
  </script>
</body>
</html>