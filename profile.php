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
    <link rel="stylesheet" href="css/register.css">
    <style>
      .profile-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 28px 24px 28px; }
      .profile-container h2 { text-align: center; color: #2d3142; margin-bottom: 24px; letter-spacing: 1px; }
      .profile-container label { display: block; margin-bottom: 6px; color: #4f5d75; font-weight: 600; text-align: right; }
      .profile-container input[type="text"], .profile-container input[type="email"], .profile-container input[type="password"] { width: 95%; display: block; margin: 0 0 18px 0; padding: 10px 8px; border: 1px solid #dbeafe; border-radius: 8px; background: #f1f5f9; font-size: 1rem; transition: border 0.2s; text-align: right; }
      .profile-container input:focus { border-color: #3a86ff; outline: none; }
      .profile-container button { width: 100%; padding: 12px; background: linear-gradient(90deg, #3a86ff 0%, #4361ee 100%); color: #fff; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
      .profile-container button:hover { background: linear-gradient(90deg, #4361ee 0%, #3a86ff 100%); }
      .profile-container .success { color: #198754; text-align: center; margin-bottom: 10px; }
      .profile-container .error { color: #e63946; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
  <div class="profile-container">
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
    <a href="index.php" style="display:block;text-align:center;margin-top:18px;color:#3a86ff;text-decoration:none;">&larr; العودة للرئيسية</a>
  </div>
</body>
</html>
