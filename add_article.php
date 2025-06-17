<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// جلب التصنيفات
$cats = $pdo->query('SELECT id, name FROM categories')->fetchAll(PDO::FETCH_ASSOC);
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $image = '';
    // رفع صورة
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imgName = 'art_' . uniqid() . '.' . $ext;
        $dest = 'uploads/articles/' . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $image = $imgName;
        }
    }
    if ($title && $content && $category_id) {
        // ربط المقال بالمستخدم فقط
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt = $pdo->prepare('INSERT INTO articles (title, content, image, category_id, user_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $content, $image, $category_id, $user_id]);
        $success = 'تم إضافة المقال بنجاح!';
    } else {
        $error = 'يرجى تعبئة جميع الحقول.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة مقال جديد</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        body {
            direction: rtl;
            text-align: center;
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            background: var(--color-slate-50);
            margin: 0;
            min-height: 100vh;
            transition: background 0.2s, color 0.2s;
        }
        .add-article-container {
            max-width: 430px;
            margin: 4rem auto 2.5rem auto;
            background: var(--color-white);
            border-radius: 1.2rem;
            box-shadow: 0 4px 24px #4262ed14;
            padding: 2.5rem 2rem 2.5rem 2rem;
            position: relative;
        }
        .add-article-container h2 {
            color: var(--color-primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .add-article-container input[type="text"],
        .add-article-container textarea,
        .add-article-container select {
            width: 95%;
            display: block;
            margin: 0 auto 18px auto;
            padding: 10px 8px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: #f1f5f9;
            font-size: 1rem;
            transition: border 0.2s, background 0.2s, color 0.2s;
            text-align: right;
            color: #222;
        }
        .add-article-container textarea {
            resize: vertical;
        }
        .add-article-container input[type="file"] {
            margin-bottom: 18px;
        }
        .add-article-container button[type="submit"] {
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7em 2.2em;
            font-size: 1.1em;
            font-family: var(--font-sans);
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 8px #3b82f622;
        }
        .add-article-container button[type="submit"]:hover {
            background: var(--color-primary-dark);
        }
        .add-article-container a {
            color: var(--color-primary);
            text-decoration: none;
            font-size: 1em;
            margin-top: 1.5rem;
            display: inline-block;
        }
        .add-article-container .msg {
            margin-bottom: 1.2rem;
            font-size: 1.1em;
        }
        /* دارك مود */
        :root {
            --color-primary: #3B82F6;
            --color-primary-dark: #2563EB;
            --color-slate-50: #F8FAFC;
            --color-white: #fff;
            --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
            --font-serif: 'Merriweather', Georgia, serif;
        }
        [data-theme="dark"] body {
            background: #0F172A !important;
            color: #fff !important;
        }
        [data-theme="dark"] .add-article-container {
            background: #1E293B !important;
            color: #fff !important;
            box-shadow: 0 4px 24px #0008;
        }
        [data-theme="dark"] .add-article-container input,
        [data-theme="dark"] .add-article-container textarea,
        [data-theme="dark"] .add-article-container select {
            background: #1E293B !important;
            color: #fff !important;
            border: 1px solid #334155;
        }
        [data-theme="dark"] .add-article-container button[type="submit"] {
            background: #2563EB;
        }
        [data-theme="dark"] .add-article-container button[type="submit"]:hover {
            background: #3B82F6;
        }
        [data-theme="dark"] .add-article-container a {
            color: #60A5FA !important;
        }
        .header {
            background: var(--color-white);
            box-shadow: 0 1px 2px #0001;
            padding: 0;
        }
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem 1.5rem 0.5rem 1.5rem;
        }
        .logo {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-primary);
            text-decoration: none;
        }
        .theme-toggle {
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--color-primary);
            cursor: pointer;
        }
    </style>
</head>
<body style="margin:0;">
    <header class="header" style="background:var(--color-white);box-shadow:0 1px 2px #0001;padding:0;">
        <nav class="nav" style="display:flex;align-items:center;justify-content:space-between;max-width:600px;margin:0 auto;padding:1rem 1.5rem 0.5rem 1.5rem;">
            <a href="index.php" class="logo" style="font-size:1.3rem;font-weight:bold;color:var(--color-primary);text-decoration:none;"><i class="fa fa-feather"></i> الرئيسية</a>
            <button class="theme-toggle" aria-label="تبديل الوضع" type="button" style="background:none;border:none;font-size:1.4rem;color:var(--color-primary);cursor:pointer;"><i class="fa fa-moon"></i></button>
        </nav>
    </header>
    <div class="add-article-outer" style="display:flex;justify-content:center;align-items:center;min-height:calc(100vh - 70px);padding-bottom:2rem;">
        <div class="add-article-container" style="margin:0;box-shadow:0 4px 24px #4262ed14;">
            <h2>إضافة مقال جديد</h2>
            <?php if ($error): ?>
                <div class="msg" style="color:red;"> <?= htmlspecialchars($error) ?> </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="msg" style="color:green;"> <?= htmlspecialchars($success) ?> </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="عنوان المقال" required>
                <textarea name="content" placeholder="محتوى المقال" required rows="8"></textarea>
                <select name="category_id" required>
                    <option value="">اختر التصنيف</option>
                    <?php foreach($cats as $cat): ?>
                        <option value="<?= $cat['id'] ?>"> <?= htmlspecialchars($cat['name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="image" accept="image/*">
                <button type="submit">نشر المقال</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
    <script>
    // دارك مود موحد
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
</body>
</html>
