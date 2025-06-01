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
    </style>
</head>
<body>
  <main class="container" aria-label="قائمة المقالات">
    <header>
      <h1 class="site-title animate__animated animate__fadeInDown" tabindex="0">مقالات</h1>
      <div class="site-desc animate__animated animate__fadeInUp" tabindex="0">منصة مقالات عربية متنوعة وحديثة</div>
      <nav aria-label="روابط رئيسية">
        <a href="index.php" aria-current="page">الرئيسية</a>
        <?php if (isset($_SESSION['username'])): ?>
          <span class="user-nav-info" style="margin:0 0.7rem; color:#2d3a4b; font-weight:bold;">
            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
          </span>
          <a href="profile.php">بروفايلي</a>
          <a href="logout.php">تسجيل الخروج</a>
        <?php else: ?>
          <a href="login.php">تسجيل الدخول</a>
          <a href="register.php">إنشاء حساب</a>
        <?php endif; ?>
      </nav>
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
  </script>
</body>
</html>