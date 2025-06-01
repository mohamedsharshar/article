<?php
session_start();
require_once 'db.php';
// جلب جميع المقالات من قاعدة البيانات
$articles = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مقالات | الصفحة الرئيسية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/index.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
      body, html { font-family: 'Cairo', Tahoma, Arial, sans-serif; background: #f7f8fa; margin: 0; padding: 0; }
      .container { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
      header { display: flex; flex-direction: column; align-items: center; margin-bottom: 2.5rem; }
      .site-title { font-size: 2.3rem; color: #2d3a4b; font-weight: bold; margin-bottom: 0.5rem; letter-spacing: 0.01em; }
      .site-desc { color: #4262ed; font-size: 1.1rem; margin-bottom: 0.5rem; }
      nav { margin-bottom: 1.5rem; }
      nav a { color: #3a86ff; text-decoration: none; font-weight: bold; margin: 0 0.7rem; font-size: 1.08rem; transition: color 0.2s; }
      nav a:hover, nav a:focus { color: #4361ee; text-decoration: underline; }
      .articles-list { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
      @media (max-width: 700px) { .articles-list { grid-template-columns: 1fr; gap: 1.2rem; } }
      .article-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(67,97,238,0.07); padding: 1.5rem 1.2rem; display: flex; flex-direction: column; gap: 0.7rem; position: relative; min-height: 180px; transition: box-shadow 0.2s, transform 0.18s; outline: none; }
      .article-card:focus-within, .article-card:hover { box-shadow: 0 8px 32px 0 #4262ed22, 0 2px 8px #3a86ff22; transform: translateY(-2px) scale(1.03); }
      .article-title { font-size: 1.25rem; color: #2d3a4b; font-weight: bold; margin-bottom: 0.2rem; line-height: 1.4; }
      .article-content { color: #444; font-size: 1.08rem; line-height: 1.7; margin-bottom: 0.5rem; }
      .article-date { color: #888; font-size: 0.98rem; margin-top: auto; text-align: left; }
      .no-articles { text-align: center; color: #888; font-size: 1.2rem; margin-top: 2.5rem; }
      /* زر العودة للأعلى */
      .scroll-top-btn { position: fixed; bottom: 32px; left: 32px; background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%); color: #fff; border: none; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 18px 0 #4262ed22; cursor: pointer; z-index: 100; transition: background 0.2s, box-shadow 0.2s; }
      .scroll-top-btn:focus, .scroll-top-btn:hover { background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%); box-shadow: 0 8px 32px 0 #4262ed33; }
      .main-navbar {
        width: 100vw;
        position: fixed;
        top: 0; right: 0; left: 0;
        background: #fff;
        box-shadow: 0 2px 16px #4262ed0a;
        z-index: 1000;
        border-bottom: 1.5px solid #e3e6f0;
        min-height: 56px;
        display: flex;
        align-items: center;
      }
      .navbar-content {
        max-width: 1100px;
        margin: 0 auto;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2vw;
      }
      .navbar-brand {
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .brand-link {
        color: #4262ed;
        font-size: 1.35rem;
        font-weight: bold;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 7px;
        letter-spacing: 0.01em;
        transition: color 0.18s;
      }
      .brand-link:hover { color: #3a86ff; }
      .navbar-list {
        list-style: none;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin: 0;
        padding: 0;
      }
      .navbar-list > li { position: relative; }
      .navbar-list a {
        color: #3a86ff;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.08rem;
        padding: 8px 18px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: background 0.18s, color 0.18s;
      }
      .navbar-list a[aria-current="page"], .navbar-list a:hover, .navbar-list a:focus {
        background: #e9f0fb;
        color: #4361ee;
      }
      .navbar-user {
        position: relative;
      }
      .user-nav-info {
        color: #2d3a4b;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 8px;
        transition: background 0.18s;
        background: none;
        border: none;
        position: relative;
      }
      .user-nav-info:focus {
        outline: none;
      }
      .navbar-user .user-dropdown {
        display: none;
        position: absolute;
        left: 0; top: 110%;
        background: #fff;
        box-shadow: 0 4px 18px #4262ed22;
        border-radius: 10px;
        min-width: 160px;
        z-index: 10;
        padding: 0.5rem 0;
        list-style: none;
      }
      .navbar-user.open .user-dropdown,
      .navbar-user:focus-within .user-dropdown,
      .user-nav-info:focus + .user-dropdown,
      .user-nav-info:hover + .user-dropdown,
      .user-dropdown:hover {
        display: block;
      }
      .user-dropdown li { width: 100%; }
      .user-dropdown a {
        color: #2d3a4b;
        font-weight: 500;
        font-size: 1.05rem;
        padding: 10px 18px;
        border-radius: 0;
        display: flex;
        align-items: center;
        gap: 7px;
        background: none;
        transition: background 0.18s;
      }
      .user-dropdown a:hover, .user-dropdown a:focus {
        background: #f1f5f9;
        color: #3a86ff;
      }
      @media (max-width: 700px) {
        .navbar-content { flex-direction: column; align-items: stretch; gap: 0.5rem; padding: 0 1vw; }
        .navbar-list { flex-direction: column; gap: 0.2rem; align-items: flex-end; }
        .user-dropdown { left: auto; right: 0; min-width: 140px; }
        .navbar-brand { justify-content: flex-end; }
      }
      .container { margin-top: 0px !important; }
    </style>
</head>
<body>
    <nav class="main-navbar-fixed" aria-label="شريط التنقل الرئيسي">
      <div class="navbar-content">
        <div class="navbar-brand">
          <a href="index.php" class="brand-link"><i class="fa fa-feather"></i> <span>مقالات</span></a>
        </div>
        <ul class="navbar-list">
          <?php if (isset($_SESSION['username'])): ?>
            <li class="navbar-user" id="navbarUser">
              <a href="profile.php" class="user-nav-info" id="userNavInfo" tabindex="0">
                <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
              </a>
              <ul class="user-dropdown" id="userDropdown">
                <li><a href="profile.php"><i class="fa fa-user"></i> بروفايلي</a></li>
                <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> تسجيل الخروج</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li><a href="login.php"><i class="fa fa-sign-in-alt"></i> تسجيل الدخول</a></li>
            <li><a href="register.php"><i class="fa fa-user-plus"></i> إنشاء حساب</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
    <main class="container" aria-label="قائمة المقالات">
      <header style="margin-top: 60px;">
        <h1 class="site-title animate__animated animate__fadeInDown" tabindex="0">مقالات</h1>
        <div class="site-desc animate__animated animate__fadeInUp" tabindex="0">منصة مقالات عربية متنوعة وحديثة</div>
      </header>
      <section class="articles-list animate__animated animate__fadeInUp" aria-live="polite">
        <?php if (count($articles) === 0): ?>
          <div class="no-articles animate__animated animate__fadeIn">لا توجد مقالات بعد.</div>
        <?php else: ?>
          <?php foreach($articles as $article): ?>
            <article class="article-card animate__animated animate__fadeInUp" tabindex="0" aria-label="مقال: <?= htmlspecialchars($article['title']) ?>">
              <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
              <div class="article-content">
                <?= nl2br(htmlspecialchars(mb_substr($article['content'],0,180))) ?><?= mb_strlen($article['content']) > 180 ? '...' : '' ?>
              </div>
              <time class="article-date" datetime="<?= htmlspecialchars($article['created_at']) ?>">
                <i class="fa fa-calendar-alt" aria-hidden="true"></i> <?= date('Y/m/d', strtotime($article['created_at'])) ?>
              </time>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
      <button class="scroll-top-btn" aria-label="العودة للأعلى" onclick="window.scrollTo({top:0,behavior:'smooth'})" style="display:none;"><i class="fa fa-arrow-up"></i></button>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
    <script>
      // زر العودة للأعلى
      const scrollBtn = document.querySelector('.scroll-top-btn');
      window.addEventListener('scroll', () => {
        if(window.scrollY > 300) scrollBtn.style.display = 'flex';
        else scrollBtn.style.display = 'none';
      });
      // user-dropdown: فتح القائمة عند المرور أو التركيز على الزر
      const navbarUser = document.getElementById('navbarUser');
      const userNavInfo = document.getElementById('userNavInfo');
      if(navbarUser && userNavInfo) {
        // لا تحول مباشرة للبروفايل عند الضغط، فقط افتح القائمة
        userNavInfo.addEventListener('click', function(e) {
          e.preventDefault();
          navbarUser.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
          if (!navbarUser.contains(e.target)) {
            navbarUser.classList.remove('open');
          }
        });
      }
    </script>
</body>
</html>