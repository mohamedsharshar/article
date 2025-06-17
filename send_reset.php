<?php
// معالجة طلب إرسال رابط إعادة تعيين كلمة المرور
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        // إنشاء رمز عشوائي
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 60*30); // 30 دقيقة صلاحية
        // حفظ الرمز في جدول منفصل أو في users (هنا سنستخدم users)
        $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
        $stmt->execute([$token, $expires, $user['id']]);
        // إعداد رابط إعادة التعيين
        $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        // إرسال الإيميل (mail)
        $subject = "إعادة تعيين كلمة المرور";
        $message = "مرحباً {$user['username']},\n\nلقد طلبت إعادة تعيين كلمة المرور. اضغط على الرابط التالي لإعادة تعيين كلمة المرور الخاصة بك:\n$resetLink\n\nإذا لم تطلب ذلك تجاهل هذه الرسالة.";
        $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\nContent-Type: text/plain; charset=utf-8";
        // تفعيل الإرسال الفعلي
        if (mail($email, $subject, $message, $headers)) {
            echo json_encode(['success'=>true, 'message'=>'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.']);
        } else {
            echo json_encode(['success'=>false, 'message'=>'حدث خطأ أثناء إرسال البريد الإلكتروني.']);
        }
        exit;
    } else {
        echo json_encode(['success'=>false, 'message'=>'البريد الإلكتروني غير مسجل']);
        exit;
    }
}
echo json_encode(['success'=>false, 'message'=>'طلب غير صالح']);
