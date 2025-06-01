<?php
session_start();
require_once 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
    }
}
?>



<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body style="direction: rtl; text-align: right;">
    <div class="login">
        <h2>تسجيل الدخول</h2>
        <form action="login.php" method="post">
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">دخول</button>
            <p>ليس لديك حساب؟ <a href="register.php">سجّل الآن</a></p>
        </form>
        <?php
        if (!empty($error)) {
            echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
        }
        ?>
    </div>
</body>
</html>