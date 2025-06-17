<?php
session_start();
require_once '../db.php';

function admin_login($pdo, $email, $password) {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        if (!$admin['is_active']) {
            return [false, 'تم تعطيل حسابك من قبل الإدارة.'];
        }
        if (password_verify($password, $admin['password'])) {
            return [true, $admin];
        } else {
            return [false, 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
        }
    } else {
        return [false, 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    list($login_successful, $admin_or_error) = admin_login($pdo, $email, $password);
    if ($login_successful) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin_or_error['adminname'];
        $_SESSION['admin_id'] = $admin_or_error['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = $admin_or_error;
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
      label {
        text-align: right;
        display: block;
        margin-bottom: 6px;
        color: #4f5d75;
        font-weight: 600;
      }
    </style>
</head>
<body>
    <div class="login">
        <h2>تسجيل دخول الأدمن</h2>
        <form action="login.php" method="post">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
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
