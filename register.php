<?php
// يمكنك إضافة منطق التسجيل هنا لاحقًا
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e2eafc 100%);
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .register-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .register-container h2 {
            text-align: center;
            color: #2d3142;
            margin-bottom: 24px;
            letter-spacing: 1px;
        }
        .register-container label {
            display: block;
            margin-bottom: 6px;
            color: #4f5d75;
            font-weight: 600;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: #f1f5f9;
            font-size: 1rem;
            transition: border 0.2s;
        }
        .register-container input:focus {
            border-color: #3a86ff;
            outline: none;
        }
        .register-container button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .register-container button:hover {
            background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%);
        }
        .register-container .login-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #3a86ff;
            text-decoration: none;
            font-size: 0.98rem;
        }
        .register-container .login-link:hover {
            text-decoration: underline;
        }
        .register-container .desc {
            text-align: center;
            color: #6c757d;
            margin-bottom: 18px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
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
</body>
</html>
