<?php
require_once 'db.php';
header('Content-Type: text/html; charset=utf-8');
// جلب المقالات مع متوسط التقييم وعدد التقييمات
$sql = "SELECT a.*, COALESCE(AVG(r.rating),0) as avg_rating, COUNT(r.id) as total_ratings
        FROM articles a
        LEFT JOIN article_ratings r ON a.id = r.article_id
        GROUP BY a.id
        HAVING total_ratings > 0
        ORDER BY avg_rating DESC, total_ratings DESC, a.created_at DESC
        LIMIT 10";
$articles = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>المقالات الأعلى تقييماً</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    body {
      background: var(--color-slate-50,#f8fafc);
      color: var(--color-slate-900,#222);
      font-family: 'Cairo', Tahoma, Arial, sans-serif;
      margin: 0;
      min-height: 100vh;
      transition: background 0.2s, color 0.2s;
    }
    .top-rated-list {
      max-width: 800px;
      margin: auto;
      background: var(--color-white,#fff);
      border-radius: 1.2rem;
      box-shadow: 0 4px 24px #33415522;
      padding: 4rem;
      transition: background 0.2s, color 0.2s;
    }
    .top-rated-item {
      display: flex;
      align-items: center;
      gap: 1.2rem;
      border-bottom: 1px solid #e2e8f0;
      padding: 1.2rem 0;
    }
    .top-rated-item:last-child { border-bottom: none; }
    .top-rated-title {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--color-primary,#2563eb);
      margin-bottom: 0.3rem;
      text-decoration: none;
      transition: color 0.2s;
    }
    .top-rated-title:hover { color: var(--color-primary-dark,#1d4ed8); }
    .top-rated-meta {
      color: var(--color-slate-600,#64748b);
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }
    .top-rated-stars {
      color: #fbbf24;
      font-size: 1.3rem;
      margin-left: 0.7rem;
      letter-spacing: 1px;
    }
    .top-rated-img {
      width: 70px; height: 70px; border-radius: 12px; object-fit: cover; box-shadow: 0 2px 8px #33415511; border: 1.5px solid #e2e8f0;
      background: #f1f5f9;
    }
    @media (max-width:600px) {
      .top-rated-list { padding: 0.7rem; }
      .top-rated-item { flex-direction: column; align-items: flex-start; gap: 0.7rem; }
      .top-rated-img { width: 100%; height: 40vw; max-width: 100%; }
    }
    [data-theme="dark"] body {
      background: #0F172A !important;
      color: #fff !important;
    }
    [data-theme="dark"] .top-rated-list {
      background: #222e3a !important;
      color: #fff !important;
      box-shadow: 0 4px 24px #0005;
    }
    [data-theme="dark"] .top-rated-title {
      color: #60A5FA !important;
    }
    [data-theme="dark"] .top-rated-title:hover {
      color: #3B82F6 !important;
    }
    [data-theme="dark"] .top-rated-meta {
      color: #CBD5E1 !important;
    }
    [data-theme="dark"] .top-rated-img {
      background: #1E293B;
      border-color: #334155;
      box-shadow: 0 2px 8px #0005;
    }
    a.btn.btn-primary {
      display: inline-block;
      margin-top: 1.5rem;
      padding: 0.7rem 2.2rem;
      border-radius: 1.2rem;
      background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%);
      color: #fff;
      font-size: 1.1rem;
      font-weight: bold;
      text-decoration: none;
      box-shadow: 0 2px 8px #3b82f622;
      transition: background 0.2s, box-shadow 0.2s, color 0.2s;
    }
    a.btn.btn-primary:hover {
      background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
      color: #fff;
      box-shadow: 0 4px 16px #4262ed22;
    }
  </style>
</head>
<body>
  <div class="top-rated-list">
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:0.5rem;">
      <button id="themeToggleBtn" aria-label="تبديل الوضع" style="background:none;border:none;font-size:1.7rem;cursor:pointer;"></button>
    </div>
    <h2 style="text-align:center;margin-bottom:2rem;">المقالات الأعلى تقييماً</h2>
    <?php if (count($articles)): ?>
      <?php foreach($articles as $art): ?>
        <div class="top-rated-item">
          <img class="top-rated-img" src="<?= $art['image'] ? 'uploads/articles/' . htmlspecialchars($art['image']) : 'https://source.unsplash.com/100x100/?arabic,writing,' . urlencode($art['category_name'] ?? 'article') ?>" alt="صورة المقال">
          <div style="flex:1;">
            <a href="article.php?id=<?= $art['id'] ?>" class="top-rated-title"> <?= htmlspecialchars($art['title']) ?> </a>
            <div class="top-rated-meta">
              <span class="top-rated-stars">
                <?= str_repeat('★', round($art['avg_rating'])) . str_repeat('☆', 5-round($art['avg_rating'])) ?>
              </span>
              <?= number_format($art['avg_rating'],2) ?> من 5
              (<?= $art['total_ratings'] ?> تقييم)
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="text-align:center;color:#888;">لا توجد مقالات مقيمة بعد.</div>
    <?php endif; ?>
    <div style="text-align:center;margin-top:2rem;">
      <a href="index.php" class="btn btn-primary">العودة للرئيسية</a>
    </div>
  </div>
  <script>
    // تفعيل الدارك مود تلقائياً حسب تفضيل المستخدم أو النظام
    function setDarkMode(on) {
      if(on) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('darkMode', '1');
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('darkMode', '0');
      }
    }
    function updateThemeIcon() {
      const btn = document.getElementById('themeToggleBtn');
      if (!btn) return;
      btn.innerHTML = document.documentElement.getAttribute('data-theme') === 'dark' ? '<i class="fa fa-sun" style="color:#fff;" ></i>' : '<i class="fa fa-moon"></i>';
    }
    document.addEventListener('DOMContentLoaded', function() {
      let darkPref = localStorage.getItem('darkMode');
      if (darkPref === null) {
        setDarkMode(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      } else {
        setDarkMode(darkPref === '1');
      }
      updateThemeIcon();
      var themeToggleBtn = document.getElementById('themeToggleBtn');
      if(themeToggleBtn) {
        themeToggleBtn.onclick = function() {
          const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
          setDarkMode(!isDark);
          updateThemeIcon();
        };
      }
    });
    window.addEventListener('storage', function(e) {
      if (e.key === 'darkMode') {
        setDarkMode(e.newValue === '1');
        updateThemeIcon();
      }
    });
  </script>
</body>
</html>
