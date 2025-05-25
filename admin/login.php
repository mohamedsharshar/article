<?php
session_start();
require_once '../db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الأدمن</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        body { direction: rtl; text-align: right; }
    </style>
</head>
<body>
    <div class="login">
        <h2>تسجيل دخول الأدمن</h2>
        <form action="login.php" method="post">
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">دخول</button>
        </form>
        <?php if (isset($error)): ?>
            <p style="color:red; text-align:center;"> <?= $error ?> </p>
        <?php endif; ?>
    </div>
</body>
</html>
