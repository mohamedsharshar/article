<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    require_once 'db.php'; 
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        echo "<script>alert('كلمات المرور غير متطابقة.'); window.history.back();</script>";
        exit();
    } 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('اسم المستخدم أو البريد الإلكتروني مستخدم من قبل.'); window.history.back();</script>";
        exit();
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        echo "<script>alert('تم إنشاء الحساب بنجاح!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة مرة أخرى.'); window.history.back();</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <!-- <link rel="stylesheet" href="./css/register.css"> -->
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
body {
  font-family: var(--font-sans);
  color: var(--color-primary);
  background-color: var(--color-slate-50);
  line-height: 1.5;
  min-height: 100vh;
  transition: background 0.2s, color 0.2s;
}
[data-theme="dark"] body {
  background-color: #0F172A !important;
  color: #fff !important;
}
.register-container {
  background: var(--color-white);
  border-radius: 1.2rem;
  box-shadow: 0 4px 24px #4262ed14;
  padding: 2.5rem 2rem 2.5rem 2rem;
  max-width: 400px;
  margin: 4rem auto 2.5rem auto;
  position: relative;
  box-sizing: border-box;
}
[data-theme="dark"] .register-container {
  background: #1E293B !important;
  color: #fff !important;
  box-shadow: 0 4px 24px #0008;
}
.register-container h2 {
  color: var(--color-primary);
  font-family: var(--font-serif);
  font-size: 2rem;
  margin-bottom: 1.5rem;
}
[data-theme="dark"] .register-container h2 {
  color: #60A5FA !important;
}
.register-container .desc {
  color: var(--color-slate-600);
  margin-bottom: 1.2rem;
  font-size: 1.08rem;
}
[data-theme="dark"] .register-container .desc {
  color: #CBD5E1 !important;
}
.register-container button.theme-toggle {
  position: absolute;
  left: 1.2rem;
  top: 1.2rem;
  background: none;
  border: none;
  font-size: 1.3rem;
  color: var(--color-primary);
  cursor: pointer;
  transition: color 0.2s;
}
[data-theme="dark"] .register-container button.theme-toggle {
  color: #fff !important;
}
.register-container input[type="text"],
.register-container input[type="email"],
.register-container input[type="password"] {
  width: 95%;
  display: block;
  margin: 0 0 18px 0;
  padding: 10px 8px;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: #f1f5f9;
  font-size: 1rem;
  transition: border 0.2s, background 0.2s, color 0.2s;
  text-align: right;
  color: #222;
}
[data-theme="dark"] .register-container input[type="text"],
[data-theme="dark"] .register-container input[type="email"],
[data-theme="dark"] .register-container input[type="password"] {
  background: #1E293B !important;
  border-color: #334155 !important;
  color: #fff !important;
}
.register-container input:focus {
  border-color: #3a86ff;
  outline: none;
}
.register-container button[type="submit"] {
  background: linear-gradient(90deg, #3a86ff 0%, #4262ed 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 0;
  font-size: 1.08rem;
  font-weight: bold;
  width: 100%;
  margin-top: 10px;
  cursor: pointer;
  transition: background 0.2s;
}
.register-container button[type="submit"]:hover {
  background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
}
.register-container a.login-link {
  color: var(--color-primary);
  text-decoration: underline;
  transition: color 0.2s;
  display: block;
  margin-top: 1.2rem;
  text-align: center;
}
[data-theme="dark"] .register-container a.login-link {
  color: #60A5FA !important;
}
    </style>
</head>
<body style="direction: rtl; text-align: right;">
    <div class="register-container">
        <button class="theme-toggle" aria-label="تبديل الوضع" type="button"><i class="fa fa-moon"></i></button>
        <h2>إنشاء حساب جديد</h2>
        <div class="desc">سجّل الآن وابدأ في كتابة وقراءة المقالات المميزة!</div>
        <form action="register.php" method="post">
            <label for="username">اسم المستخدم</label>
            <input type="text" id="username" name="username" required>

            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" required>

            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">تأكيد كلمة المرور</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">تسجيل</button>
        </form>
        <a class="login-link" href="login.php">لديك حساب بالفعل؟ تسجيل الدخول</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
    <script>
    // دارك مود مثل login.php
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
