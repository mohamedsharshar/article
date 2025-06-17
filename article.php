<?php
session_start();
require_once 'db.php';
$error = '';
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
// جلب اسم الناشر للمقال (لو فيه user_id أو admin_id)
$authorName = '';
if (!empty($article['user_id'])) {
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->execute([$article['user_id']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u) $authorName = $u['username'];
} elseif (!empty($article['admin_id'])) {
    $stmt = $pdo->prepare('SELECT username FROM admins WHERE id = ?');
    $stmt->execute([$article['admin_id']]);
    $a = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($a) $authorName = $a['username'];
}

// السماح فقط للمستخدم المسجل بإضافة تعليق
$isUserLoggedIn = isset($_SESSION['user_id']);
?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($article['title']) ?> | مقالات</title>
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --color-bg-light: #fff;
      --color-bg-dark: #0F172A;
      --color-surface-light: #f8fafc;
      --color-surface-dark: #334155;
      --color-text-light: #222;
      --color-text-dark: #fff;
      --color-primary: #475569;
      --color-secondary: #64748B;
      --color-accent: #2563eb;
      --color-shadow-light: #33415522;
      --color-shadow-dark: #000a;
    }
    html[data-theme="dark"] body,
    body[data-theme="dark"] {
      background: var(--color-bg-dark) !important;
      color: var(--color-text-dark) !important;
    }
    body {
      background: var(--color-bg-light);
      min-height: 100vh;
      margin: 0;
      font-family: 'Cairo', Tahoma, Arial, sans-serif;
      color: var(--color-text-light);
      transition: background 0.2s, color 0.2s;
    }
    .article-page-container {
      max-width: 800px;
      margin: 4rem auto 0rem auto;
      border-radius: 1.2rem;
      background: var(--color-bg-light);
      box-shadow: 0 4px 24px var(--color-shadow-light);
      padding: 2.5rem 2rem 2.5rem 2rem;
      position: relative;
      color: var(--color-text-light);
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }
    html[data-theme="dark"] .article-page-container,
    body[data-theme="dark"] .article-page-container {
      background: var(--color-surface-dark) !important;
      color: var(--color-text-dark) !important;
      box-shadow: 0 4px 24px var(--color-shadow-dark);
    }
    .article-page-title {
      font-family: 'Merriweather', serif;
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: var(--color-text-light);
      text-shadow: 0 2px 8px var(--color-shadow-light);
      transition: color 0.2s;
    }
    html[data-theme="dark"] .article-page-title,
    body[data-theme="dark"] .article-page-title {
      color: var(--color-text-dark) !important;
      text-shadow: 0 2px 8px var(--color-shadow-dark);
    }
    .article-page-meta {
      color: var(--color-secondary);
      font-size: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      align-items: center;
      transition: color 0.2s;
    }
    html[data-theme="dark"] .article-page-meta,
    body[data-theme="dark"] .article-page-meta {
      color: var(--color-text-dark) !important;
    }
    .article-page-meta .category-tag {
      margin-right: 0.5rem;
      background: var(--color-primary);
      color: #fff;
      padding: 0.35em 1.2em;
      border-radius: 999px;
      font-size: 1em;
      box-shadow: 0 2px 8px var(--color-shadow-light);
      z-index: 2;
      font-weight: bold;
      letter-spacing: 0.01em;
      transition: background 0.2s, color 0.2s;
    }
    html[data-theme="dark"] .article-page-meta .category-tag,
    body[data-theme="dark"] .article-page-meta .category-tag {
      background: var(--color-surface-dark) !important;
      color: var(--color-text-dark) !important;
      box-shadow: 0 2px 8px var(--color-shadow-dark);
    }
    .article-page-content {
      font-size: 1.15rem;
      line-height: 2.1;
      color: var(--color-text-light);
      margin-bottom: 2rem;
      background: var(--color-surface-light);
      padding: 1.5rem 1.2rem;
      border-radius: 1rem;
      box-shadow: 0 1px 4px var(--color-shadow-light);
      word-break: break-word;
      white-space: pre-line;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }
    html[data-theme="dark"] .article-page-content,
    body[data-theme="dark"] .article-page-content {
      background: var(--color-primary) !important;
      color: var(--color-text-dark) !important;
      box-shadow: 0 1px 4px var(--color-shadow-dark);
    }
    .article-page-back {
      display: inline-block;
      margin-bottom: 1.5rem;
      color: var(--color-primary);
      font-weight: bold;
      text-decoration: none;
      font-size: 1.1rem;
      transition: color 0.2s, background 0.2s;
      background: var(--color-surface-light);
      padding: 0.5em 1.2em;
      border-radius: 8px;
      box-shadow: 0 1px 4px var(--color-shadow-light);
    }
    .article-page-back:hover {
      color: #fff;
      background: var(--color-primary);
      text-decoration: none;
    }
    html[data-theme="dark"] .article-page-back,
    body[data-theme="dark"] .article-page-back {
      background: var(--color-surface-dark) !important;
      color: var(--color-text-dark) !important;
      box-shadow: 0 1px 4px var(--color-shadow-dark);
    }
    html[data-theme="dark"] .article-page-back:hover,
    body[data-theme="dark"] .article-page-back:hover {
      background: var(--color-primary) !important;
      color: #fff !important;
    }
    .article-page-image {
      width: 100%;
      max-height: 350px;
      object-fit: cover;
      border-radius: 1rem;
      margin-bottom: 1.2rem;
      box-shadow: 0 2px 12px var(--color-shadow-light);
      transition: box-shadow 0.2s;
    }
    html[data-theme="dark"] .article-page-image,
    body[data-theme="dark"] .article-page-image {
      box-shadow: 0 2px 12px var(--color-shadow-dark);
    }
    .theme-toggle {
      background: none;
      border: none;
      color: var(--color-primary);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.2s;
      margin-right: 0.5rem;
      position: absolute;
      left: 1.2rem;
      top: 1.2rem;
      z-index: 10;
    }
    html[data-theme="dark"] .theme-toggle,
    body[data-theme="dark"] .theme-toggle {
      color: #fff;
    }
    .header {
      background: transparent;
      box-shadow: none;
      padding: 1.2rem 0 0.5rem 0;
    }
    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 1.2rem;
    }
    .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: var(--color-primary);
      text-decoration: none;
      letter-spacing: 0.01em;
      display: flex;
      align-items: center;
      gap: 0.5em;
      transition: color 0.2s;
    }
    .logo:hover {
      color: var(--color-accent);
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
    .article-comments-section {
      margin-top: 2.5rem;
      background: var(--color-surface-light);
      border-radius: 1rem;
      box-shadow: 0 1px 8px var(--color-shadow-light);
      padding: 2rem 1.2rem 1.5rem 1.2rem;
      transition: background 0.2s, box-shadow 0.2s;
    }
    html[data-theme="dark"] .article-comments-section,
    body[data-theme="dark"] .article-comments-section {
      background: var(--color-surface-dark);
      box-shadow: 0 1px 8px var(--color-shadow-dark);
    }
    .comments-title {
      font-size: 1.3rem;
      color: var(--color-primary);
      margin-bottom: 1.2rem;
      display: flex;
      align-items: center;
      gap: 0.5em;
    }
    .comments-list {
      list-style: none;
      padding: 0;
      margin: 0 0 2.2rem 0;
    }
    .comment-item {
      background: #f1f5f9;
      border-radius: 0.7rem;
      margin-bottom: 1.2rem;
      padding: 1rem 1.2rem 0.7rem 1.2rem;
      box-shadow: 0 1px 4px #33415511;
      transition: background 0.2s;
      border-right: 4px solid #2563eb22;
      position: relative;
    }
    .comment-item.admin-comment {
      background: #e0e7ff;
      border-right: 4px solid #2563eb;
    }
    html[data-theme="dark"] .comment-item {
      background: #222e3a;
      color: #fff;
      box-shadow: 0 1px 4px #0005;
      border-right: 4px solid #2563eb33;
    }
    html[data-theme="dark"] .comment-item.admin-comment {
      background: #334155;
      border-right: 4px solid #2563eb;
    }
    .comment-content {
      font-size: 1.08rem;
      margin-bottom: 0.5rem;
      word-break: break-word;
      line-height: 1.8;
    }
    .comment-meta {
      font-size: 0.98rem;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 0.7em;
    }
    html[data-theme="dark"] .comment-meta {
      color: #cbd5e1;
    }
    .no-comments {
      color: #64748b;
      text-align: center;
      margin: 1.5rem 0 2.5rem 0;
      font-size: 1.08rem;
    }
    .add-comment-title {
      font-size: 1.1rem;
      color: var(--color-primary);
      margin-bottom: 0.7rem;
      display: flex;
      align-items: center;
      gap: 0.5em;
    }
    .add-comment-form {
      display: flex;
      flex-direction: column;
      gap: 0.7rem;
      margin-bottom: 0.5rem;
      margin-top: 0.5rem;
    }
    .add-comment-form textarea {
      border-radius: 8px;
      border: 1.5px solid #dbeafe;
      background: #f8fafc;
      font-size: 1.08rem;
      color: #222;
      padding: 12px 10px;
      min-height: 80px;
      max-height: 180px;
      resize: vertical;
      box-shadow: 0 1px 4px #e3e6f0;
      transition: border 0.2s, box-shadow 0.2s;
    }
    .add-comment-form textarea:focus {
      border-color: #4262ed;
      outline: none;
      box-shadow: 0 2px 8px #4262ed22;
    }
    html[data-theme="dark"] .add-comment-form textarea {
      background: #222e3a;
      color: #fff;
      border: 1.5px solid #334155;
      box-shadow: 0 1px 4px #0005;
    }
    .add-comment-btn {
      background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 22px;
      font-size: 1.08rem;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 2px 8px #3a86ff22;
      display: flex;
      align-items: center;
      gap: 7px;
      justify-content: center;
      transition: background 0.2s, box-shadow 0.2s;
    }
    .add-comment-btn:hover {
      background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
      box-shadow: 0 4px 16px #4361ee22;
    }
    /* Unified dark mode toggle logic
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
      var themeToggle = document.getElementById('themeToggleBtn');
      if (!themeToggle) return;
      if(document.documentElement.getAttribute('data-theme') === 'dark') {
        themeToggle.innerHTML = '<i class="fa fa-sun"></i>';
      } else {
        themeToggle.innerHTML = '<i class="fa fa-moon"></i>';
      }
    }
    (function() {
      let darkPref = localStorage.getItem('darkMode');
      if(darkPref === null) {
        setDarkMode(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      } else {
        setDarkMode(darkPref === '1');
      }
      updateThemeIcon();
      var themeToggle = document.getElementById('themeToggleBtn');
      if(themeToggle) {
        themeToggle.onclick = function() {
          const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
          setDarkMode(!isDark);
          updateThemeIcon();
        };
      }
    })(); */
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
  <script>
    // دارك مود موحد لجميع الصفحات
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
      const themeToggle = document.querySelector('.theme-toggle');
      if (!themeToggle) return;
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      themeToggle.innerHTML = isDark ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>';
    }
    document.addEventListener('DOMContentLoaded', function() {
      let darkPref = localStorage.getItem('darkMode');
      if (darkPref === null) {
        setDarkMode(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      } else {
        setDarkMode(darkPref === '1');
      }
      updateThemeIcon();
      var themeToggleBtn = document.querySelector('.theme-toggle');
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
</head>
<body>
  <header class="header">
    <div class="container">
      <button class="theme-toggle" aria-label="تبديل الوضع" type="button"><i class="fa fa-moon"></i></button>
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
        <span><i class="fa fa-user"></i> <?= htmlspecialchars($authorName) ?></span>
      </div>
      <div class="article-page-content">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
      </div>
      <!-- comments section -->
      <div class="article-comments-section">
        <h2 class="comments-title"><i class="fa fa-comments"></i> التعليقات</h2>
        <?php
        // معالجة إضافة تعليق مستخدم
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_comment'])) {
          $user_comment = trim($_POST['user_comment']);
          if ($user_comment && isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare('INSERT INTO comments (article_id, content, is_admin, user_id, created_at) VALUES (?, ?, 0, ?, NOW())');
            $stmt->execute([$article['id'], $user_comment, $_SESSION['user_id']]);
            echo '<meta http-equiv="refresh" content="0">';
            exit;
          }
        }
        // جلب جميع التعليقات لهذا المقال (مستخدمين وأدمن)
        $comments = $pdo->prepare("SELECT content, created_at, is_admin, user_id, admin_id FROM comments WHERE article_id = ? ORDER BY created_at DESC");
        $comments->execute([$article['id']]);
        $comments = $comments->fetchAll(PDO::FETCH_ASSOC);
        if ($comments && count($comments)) {
          echo '<ul class="comments-list">';
          foreach ($comments as $c) {
            $isAdmin = $c['is_admin'] == 1;
            $commentAuthor = $isAdmin ? 'مشرف' : 'مستخدم';
            if ($isAdmin && !empty($c['admin_id'])) {
              $stmt = $pdo->prepare('SELECT adminname FROM admins WHERE id = ?');
              $stmt->execute([$c['admin_id']]);
              $a = $stmt->fetch(PDO::FETCH_ASSOC);
              if ($a) $commentAuthor = $a['adminname'] . ' (مشرف)';
            } elseif (!$isAdmin && !empty($c['user_id'])) {
              $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
              $stmt->execute([$c['user_id']]);
              $u = $stmt->fetch(PDO::FETCH_ASSOC);
              if ($u) $commentAuthor = $u['username'] . ' (مستخدم)';
            }
            echo '<li class="comment-item'.($isAdmin ? ' admin-comment' : '').'">'
              .'<div class="comment-content">'.nl2br(htmlspecialchars($c['content'])).'</div>'
              .'<div class="comment-meta">'
              .'<i class="fa fa-user'.($isAdmin ? '-shield' : '').'"></i> '.htmlspecialchars($commentAuthor)
              .' &nbsp; <i class="fa fa-calendar-alt"></i> '.htmlspecialchars(substr($c['created_at'],0,16)).'</div>'
              .'</li>';
          }
          echo '</ul>';
        } else {
          echo '<div class="no-comments">لا توجد تعليقات بعد.</div>';
        }
        ?>
        <hr style="margin:2.5rem 0 1.5rem 0;opacity:.13;">
        <h3 class="add-comment-title"><i class="fa fa-plus"></i> أضف تعليقك</h3>
        <?php if ($isUserLoggedIn): ?>
        <form class="add-comment-form" method="post" action="" id="addCommentForm">
          <textarea name="user_comment" required placeholder="اكتب تعليقك هنا..." maxlength="500"></textarea>
          <button type="submit" class="add-comment-btn"><i class="fa fa-paper-plane"></i> إرسال</button>
        </form>
        <script>
        document.getElementById('addCommentForm').addEventListener('submit', async function(e) {
          e.preventDefault();
          const form = this;
          const textarea = form.querySelector('textarea[name="user_comment"]');
          const comment = textarea.value.trim();
          if (!comment) return;
          const btn = form.querySelector('button[type="submit"]');
          btn.disabled = true;
          btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جارٍ الإرسال...';
          try {
            const res = await fetch('', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'user_comment=' + encodeURIComponent(comment)
            });
            const html = await res.text();
            // استخراج جزء التعليقات فقط من الصفحة الجديدة
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newCommentsSection = doc.querySelector('.article-comments-section');
            if (newCommentsSection) {
              document.querySelector('.article-comments-section').innerHTML = newCommentsSection.innerHTML;
            }
            textarea.value = '';
          } catch (err) {
            alert('حدث خطأ أثناء إرسال التعليق');
          } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> إرسال';
          }
        });
        </script>
        <?php else: ?>
        <div style="color:#e63946;text-align:center;font-weight:bold;margin:1.5rem 0;">يجب تسجيل الدخول كـ مستخدم لإضافة تعليق.</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
