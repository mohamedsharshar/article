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
    <link rel="stylesheet" href="./css/login.css">
    <style>
      input#email, input#password {
        width: 95%;
        display: block;
        margin: 0 0 18px 0;
        padding: 10px 8px;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        background: #f1f5f9;
        font-size: 1rem;
        transition: border 0.2s;
        text-align: right;
      }
      input#email:focus, input#password:focus {
        border-color: #3a86ff;
        outline: none;
      }
    </style>
</head>
<body style="direction: rtl; text-align: right;">
    <div class="login">
        <h2>تسجيل الدخول</h2>
        <form action="login.php" method="post">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
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