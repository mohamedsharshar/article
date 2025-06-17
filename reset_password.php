<?php
require_once 'db.php';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$showForm = false;
$error = '';
$success = '';
if ($token) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $showForm = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
            $password = $_POST['password'];
            if (strlen($password) < 6) {
                $error = 'كلمة المرور يجب أن تكون 6 أحرف أو أكثر';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
                $stmt->execute([$hashed, $user['id']]);
                $success = 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.';
                $showForm = false;
            }
        }
    } else {
        $error = 'الرابط غير صالح أو منتهي الصلاحية.';
    }
} else {
    $error = 'رابط غير صحيح.';
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
    <link rel="stylesheet" href="css/login.css">
    <style>body{direction:rtl;text-align:center;}</style>
</head>
<body>
    <h2>إعادة تعيين كلمة المرور</h2>
    <?php if ($error): ?>
        <div style="color:red;"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color:green;"> <?= htmlspecialchars($success) ?> </div>
        <a href="login.php">تسجيل الدخول</a>
    <?php endif; ?>
    <?php if ($showForm): ?>
        <form method="post">
            <input type="password" name="password" placeholder="كلمة مرور جديدة" required minlength="6"><br><br>
            <button type="submit">تغيير كلمة المرور</button>
        </form>
    <?php endif; ?>
</body>
</html>
