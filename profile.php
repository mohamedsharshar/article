<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
// جلب بيانات المستخدم الحالي
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}
// معالجة التعديل
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['password'];
    // تحقق من عدم تكرار اسم المستخدم أو البريد لمستخدم آخر
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?');
    $stmt->execute([$new_username, $new_email, $user['id']]);
    if ($stmt->fetchColumn() > 0) {
        $error = 'اسم المستخدم أو البريد الإلكتروني مستخدم من قبل.';
    } else {
        if ($new_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$new_username, $new_email, $hashed, $user['id']]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
            $stmt->execute([$new_username, $new_email, $user['id']]);
        }
        $_SESSION['username'] = $new_username;
        $success = true;
        // تحديث بيانات المستخدم بعد التعديل
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروفايلي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
:root {
  --color-primary: #3B82F6;
  --color-primary-dark: #2563EB;
  --color-accent: #60A5FA;
  --color-success: #198754;
  --color-error: #e63946;
  --color-bg-light: #F8FAFC;
  --color-bg-dark: #0F172A;
  --color-container-light: #fff;
  --color-container-dark: #1E293B;
  --color-border: #dbeafe;
  --color-border-dark: #334155;
  --font-sans: 'Inter', 'Cairo', system-ui, sans-serif;
  --font-serif: 'Merriweather', Georgia, serif;
}
html, body {
  height: 100%;
}
body {
  min-height: 100vh;
  margin: 0;
  font-family: var(--font-sans);
  background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
  color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s, color 0.3s;
}
[data-theme="dark"] body {
  background: linear-gradient(120deg, #0F172A 0%, #1E293B 100%) !important;
  color: #fff !important;
}
.profile-outer {
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  background: none;
}
.profile-container {
  background: var(--color-container-light);
  border-radius: 2rem;
  box-shadow: 0 8px 32px rgba(67,97,238,0.13);
  padding: 2.8rem 2.2rem 2.2rem 2.2rem;
  max-width: 550px;
  width: 95vw;
  margin: 0 auto;
  position: relative;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  transition: background 0.3s, color 0.3s, box-shadow 0.3s;
}
[data-theme="dark"] .profile-container {
  background: var(--color-container-dark) !important;
  color: #fff !important;
  box-shadow: 0 8px 32px #0008;
}
.theme-toggle {
  position: absolute;
  left: 1.2rem;
  top: 1.2rem;
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #e0e7ef 60%, #fff 100%);
  border: none;
  border-radius: 50%;
  box-shadow: 0 2px 8px #3b82f61a;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.45rem;
  color: var(--color-primary);
  cursor: pointer;
  transition: background 0.25s, color 0.25s, box-shadow 0.25s, transform 0.18s;
  outline: none;
  z-index: 2;
}
.theme-toggle:active {
  transform: scale(0.93);
}
.theme-toggle:hover {
  background: linear-gradient(135deg, #dbeafe 60%, #e0e7ef 100%);
  color: var(--color-primary-dark);
  box-shadow: 0 4px 16px #3b82f62a;
}
[data-theme="dark"] .theme-toggle {
  background: linear-gradient(135deg, #1E293B 60%, #334155 100%) !important;
  color: #fff !important;
  box-shadow: 0 2px 8px #0008;
}
[data-theme="dark"] .theme-toggle:hover {
  background: linear-gradient(135deg, #334155 60%, #1E293B 100%) !important;
  color: #60A5FA !important;
}
.profile-container h2 {
  text-align: center;
  color: var(--color-primary);
  margin-bottom: 0.7rem;
  letter-spacing: 1px;
  font-family: var(--font-serif);
  font-size: 2.1rem;
  font-weight: 700;
  transition: color 0.2s;
}
[data-theme="dark"] .profile-container h2 {
  color: var(--color-accent) !important;
}
.profile-container label {
  display: block;
  margin-bottom: 0.3rem;
  color: #4f5d75;
  font-weight: 600;
  text-align: right;
  font-size: 1.08rem;
  transition: color 0.2s;
}
[data-theme="dark"] .profile-container label {
  color: #CBD5E1 !important;
}
.profile-container input[type="text"],
.profile-container input[type="email"],
.profile-container input[type="password"] {
  width: 100%;
  display: block;
  margin: 0 auto 1.1rem auto;
  padding: 10px;
  border: 1.5px solid var(--color-border);
  border-radius: 10px;
  background: #f1f5f9;
  font-size: 1.13rem;
  transition: border 0.22s, background 0.22s, color 0.22s, box-shadow 0.22s;
  text-align: right;
  direction: rtl;
  color: #222;
  box-shadow: 0 1px 4px #e3e6f0;
}
[data-theme="dark"] .profile-container input[type="text"],
[data-theme="dark"] .profile-container input[type="email"],
[data-theme="dark"] .profile-container input[type="password"] {
  background: #1E293B !important;
  border-color: var(--color-border-dark) !important;
  color: #fff !important;
  box-shadow: 0 1px 4px #0004;
  text-align: right;
  direction: rtl;
}
.profile-container input:focus {
  border-color: var(--color-primary);
  background: #e0e7ef;
  box-shadow: 0 2px 8px #3b82f62a;
  outline: none;
}
[data-theme="dark"] .profile-container input:focus {
  background: #334155 !important;
  border-color: #60A5FA !important;
}
.profile-container button[type="submit"] {
  width: 100%;
  padding: 15px;
  background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 1.18rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.22s, box-shadow 0.22s, transform 0.15s;
  margin-top: 10px;
  box-shadow: 0 2px 8px #3b82f61a;
}
.profile-container button[type="submit"]:hover {
  background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
  box-shadow: 0 4px 16px #3b82f62a;
  transform: translateY(-2px) scale(1.03);
}
.profile-container .success {
  color: var(--color-success);
  text-align: center;
  margin-bottom: 10px;
  font-size: 1.08rem;
}
.profile-container .error {
  color: var(--color-error);
  text-align: center;
  margin-bottom: 10px;
  font-size: 1.08rem;
}
.profile-container .back-home-link {
  display: block;
  text-align: center;
  margin-top: 1.2rem;
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 600;
  font-size: 1.08rem;
  border-radius: 8px;
  padding: 8px 0;
  transition: background 0.18s, color 0.18s, box-shadow 0.18s;
}
.profile-container .back-home-link:hover {
  color: #fff !important;
}
@media (max-width: 600px) {
  .profile-container {
    padding: 1.5rem 0.7rem 1.2rem 0.7rem;
    max-width: 98vw;
    border-radius: 1.1rem;
  }
  .theme-toggle {
    left: 0.7rem;
    top: 0.7rem;
    width: 38px;
    height: 38px;
    font-size: 1.1rem;
  }
  .profile-container h2 {
    font-size: 1.3rem;
  }
}
    </style>
</head>
<body>
  <div class="profile-outer">
    <div class="profile-container">
      <button class="theme-toggle" aria-label="تبديل الوضع" type="button"><i class="fa fa-moon"></i></button>
      <h2>بروفايلي</h2>
      <?php if ($success): ?>
        <div class="success">تم تحديث بياناتك بنجاح.</div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post" autocomplete="off">
        <label for="username">اسم المستخدم</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <label for="password">كلمة المرور الجديدة (اتركها فارغة إذا لا تريد التغيير)</label>
        <input type="password" id="password" name="password" autocomplete="new-password">
        <button type="submit">حفظ التعديلات</button>
      </form>
      <a href="index.php" class="back-home-link">&larr; العودة للرئيسية</a>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
  <script>
    // دارك مود مثل صفحة اللوجن
    function setDarkMode(on) {
      if(on) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('darkMode', '1');
      } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('darkMode', '0');
      }
    }
    const themeToggle = document.querySelector('.theme-toggle');
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
  </script>
</body>
</html>
