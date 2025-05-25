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
