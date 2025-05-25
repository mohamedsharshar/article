<?php
// يمكنك إضافة منطق التسجيل هنا لاحقًا
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل حساب جديد</title>
    <link rel="stylesheet" href="./css/register.css">
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
