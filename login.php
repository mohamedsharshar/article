<?php
session_start();
require_once 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if (!$user['is_active']) {
            $error = 'تم تعطيل حسابك من قبل الإدارة.';
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    } else {
        $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
    }
}
?>



<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
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
.login {
  background: var(--color-white);
  border-radius: 1.2rem;
  box-shadow: 0 4px 24px #4262ed14;
  padding: 2.5rem 2rem 2.5rem 2rem;
  max-width: 400px;
  margin: 4rem auto 2.5rem auto;
  position: relative;
  box-sizing: border-box;
}
[data-theme="dark"] .login {
  background: #1E293B !important;
  color: #fff !important;
  box-shadow: 0 4px 24px #0008;
}
.login h2 {
  color: var(--color-primary);
  font-family: var(--font-serif);
  font-size: 2rem;
  margin-bottom: 1.5rem;
}
[data-theme="dark"] .login h2 {
  color: #60A5FA !important;
}
.login button.theme-toggle {
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
[data-theme="dark"] .login button.theme-toggle {
  color: #fff !important;
}
.login input[type="email"], .login input[type="password"] {
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
[data-theme="dark"] .login input[type="email"], [data-theme="dark"] .login input[type="password"] {
  background: #1E293B !important;
  border-color: #334155 !important;
  color: #fff !important;
}
.login input[type="email"]:focus, .login input[type="password"]:focus {
  border-color: #3a86ff;
  outline: none;
}
.login button[type="submit"] {
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
.login button[type="submit"]:hover {
  background: linear-gradient(90deg, #4262ed 0%, #3a86ff 100%);
}
.login a {
  color: var(--color-primary);
  text-decoration: underline;
  transition: color 0.2s;
}
[data-theme="dark"] .login a {
  color: #60A5FA !important;
}
.back-home-btn {
  display: inline-block;
  margin-bottom: 1.2rem;
  color: var(--color-primary);
  background: var(--color-slate-100);
  padding: 7px 18px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  transition: background 0.2s;
}
.back-home-btn:hover {
  background: var(--color-primary);
  color: #fff !important;
}
[data-theme="dark"] .back-home-btn {
  background: #334155 !important;
  color: #fff !important;
}
[data-theme="dark"] .back-home-btn:hover {
  background: #3B82F6 !important;
  color: #fff !important;
}
    </style>
</head>
<body style="direction: rtl; text-align: right;">
    <div class="login">
        <button class="theme-toggle" aria-label="تبديل الوضع" type="button"><i class="fa fa-moon"></i></button>
        <a href="index.php" class="back-home-btn" style="display:inline-block;margin-bottom:1.2rem;color:var(--color-primary);background:var(--color-slate-100);padding:7px 18px;border-radius:8px;text-decoration:none;font-weight:bold;transition:background 0.2s;">
          <i class="fa fa-home"></i> العودة للرئيسية
        </a>
        <h2>تسجيل الدخول</h2>
        <form action="login.php" method="post">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">دخول</button>
            <p>ليس لديك حساب؟ <a href="register.php">سجّل الآن</a></p>
            <p style="margin-top:10px;text-align:center;">
              <a href="#" id="forgotPasswordLink" style="color:var(--color-primary);text-decoration:underline;">هل نسيت كلمة المرور؟</a>
            </p>
        </form>
        <div id="forgotPasswordModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:#0007;z-index:9999;align-items:center;justify-content:center;">
          <div style="background:#fff;padding:2rem 1.5rem;border-radius:1.2rem;max-width:350px;width:90vw;position:relative;box-shadow:0 4px 24px #0005;direction:rtl;">
            <button onclick="document.getElementById('forgotPasswordModal').style.display='none'" style="position:absolute;top:1rem;left:1rem;background:none;border:none;font-size:1.3rem;cursor:pointer;"><i class="fa fa-times"></i></button>
            <h3 style="margin-bottom:1rem;color:var(--color-primary);font-size:1.2rem;">استعادة كلمة المرور</h3>
            <form id="forgotPasswordForm" method="post" autocomplete="off">
              <label for="reset_email">أدخل بريدك الإلكتروني:</label>
              <input type="email" id="reset_email" name="reset_email" required style="width:100%;margin-bottom:1rem;padding:8px 6px;border-radius:7px;border:1px solid #dbeafe;">
              <button type="submit" style="width:100%;background:linear-gradient(90deg,#3a86ff 0%,#4262ed 100%);color:#fff;border:none;border-radius:8px;padding:10px 0;font-size:1.08rem;font-weight:bold;cursor:pointer;">إرسال رابط إعادة التعيين</button>
              <div id="resetMsg" style="margin-top:1rem;font-size:1.01rem;"></div>
            </form>
          </div>
        </div>
        <?php
        if (!empty($error)) {
            echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
        }
        ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
    <script>
    // دارك مود مثل الهوم
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
    document.getElementById('forgotPasswordLink').onclick = function(e) {
      e.preventDefault();
      document.getElementById('forgotPasswordModal').style.display = 'flex';
    };
    document.getElementById('forgotPasswordForm').onsubmit = async function(e) {
      e.preventDefault();
      const email = document.getElementById('reset_email').value.trim();
      const msg = document.getElementById('resetMsg');
      msg.textContent = 'جاري الإرسال...';
      // إرسال الطلب إلى ملف PHP لمعالجة الإيميل
      const res = await fetch('send_reset.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
      });
      const data = await res.json();
      if(data.success) {
        msg.style.color = '#198754';
        msg.textContent = 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني (إذا كان مسجلاً).';
      } else {
        msg.style.color = '#e63946';
        msg.textContent = data.message || 'حدث خطأ، حاول مرة أخرى.';
      }
    };
    </script>
</body>
</html>