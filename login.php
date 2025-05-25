<?php

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
        <form action="index.php" method="post">
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">دخول</button>
            <p>ليس لديك حساب؟ <a href="register.php">سجّل الآن</a></p>
        </form>
        <?php
        if (isset($_GET['error'])) {
            echo '<p style="color:red;">اسم المستخدم أو كلمة المرور غير صحيحة.</p>';
        }
        ?>
    </div>
</body>
</html>